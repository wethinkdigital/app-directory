<?php
	
add_action("wp_ajax_directory_search", "directory_search");
add_action("wp_ajax_nopriv_directory_search", "directory_search"); 

function directory_search($params = null){


	//echo 'searching';

	$postdata = file_get_contents("php://input");
   $request = (array) json_decode($postdata);

		
	if($request) foreach($request as $k=>$v) $params[$k] = $v;
	
	
	
	if($params['encrypted']){
		global $dircore;
		parse_str($dircore->decrypt($params['encrypted']),$safeparams);
		$params = array_merge($params,$safeparams);
	}
	
	//echo '<pre>'; print_r($params); echo '</pre>';

			
	$type = post_type_exists($params['type']) ? $params['type'] : 'post';
	global $$type;
	$vars = $params['type'] == 'post' ? array() : $$type->getVarNames();
	
	
	$expire = $params['expire'] ? $params['expire'] : null;

	//echo '<pre>'; print_r($vars); echo '</pre>';

	
	// Cleans all params, removing anything unexpected
	$params = $$type->prepVars($params);
	
/*
				if($k == 'expire' && is_array($v)){
					$preppedVars[$k] = $v;
				}
*/

	//echo '<pre>'; print_r($params); echo '</pre>';

	$order = $params['order'] ? $params['order'] : 'DESC';

	$ppp = $params['paging'] ? $params['paging'] : -1;

	if($params['paging']){
		if($params['pg'] && $params['pg'] > 1){
			$offset = (intval($params['pg']) - 1) * $params['paging'];
		} else {
			$offset = 0;
		}
	}


	// set up basic query args
	$query_args = array(	'post_type' => $type,
							'orderby' => 'date',
							'order' => $order,
							'posts_per_page' => $ppp,
							'offset' => $offset 
							);

	//echo '<pre>'; print_r($query_args); echo '</pre>';


	$query_args['meta_query'] = $params['meta_query'] ? $params['meta_query'] : array();
	
	if($params['author']) $query_args['author'] = $params['author'];

	

	// Exclude posts from suspended users
	$suspended_args = array('role' => 'suspended');
	$result = new WP_User_Query($suspended_args);

	for($i=0; $i<count($result->results); $i++){
		$suspended_users[] = $result->results[$i]->ID;
	}

	if(isset($suspended_users) && is_array($suspended_users)){
		$query_args['meta_query'][] = array('key' => 'group_id', 'value' => $suspended_users, 'compare' => 'NOT IN');
		$query_args['author__not_in'] = $suspended_users;
	}
	


	// Get the default expiration period for the item, unless it's provided in the initial params
	if(!$params['expire'] || !is_array($params['expire'])){
		$params['expire'] = $$type->getExpire() != '' ? $$type->getExpire() : null;
	}

	if($params['expire']){
		foreach($params['expire'] as $k=>$v){
			$expires_if_older = strtotime('now') - (intval($v)*24*60*60);
			$query_args['meta_query'][] = array('key' => $k, 'value' => $expires_if_older, 'compare' => '>');
		}
	}
	
	//echo '<pre>Queryargs'; print_r($query_args); echo '</pre>';

	
	// add ordering if requested
	if($params['orderby']){
		$query_args['meta_key']	= $params['orderby'];
		$query_args['orderby'] = 'meta_value';
	}

/*
	echo '<pre>Params'; print_r($params); echo '</pre>';
	echo '<pre>Vars'; print_r($vars); echo '</pre>';
*/


	
	// remove unexpected search variables
	if($params){
		$clean_params = array();
		foreach($params as $k=>$v){
			if(in_array($k, $vars) && $v != '' || $k == 'post__in'){
				// (below) supports individual search values presented as array but not multiple values within a search field
				$clean_params[$k] = is_array($v) ? $v[0] : $v; 
			}
		}
	}


	// check which params have multichoice answers
	if($clean_params){
		$mc_params = array();
		foreach($clean_params as $k=>$v){
			$q = $$type->getQuestion($k);
			if($q && is_array($q['value'])){
				$mc_params[] = $k;
			}
		}
	}		

	//echo '<pre>Clean Params'; print_r($clean_params); echo '</pre>';


	// set meta query for each valid search param
	foreach($clean_params as $k=>$v){
		
			$q = $$type->getQuestion($k);

			if($q){
			
				if($q['taxonomy']){

					foreach(explode(',',$v) as $vterm){

						if(strstr($vterm, '!')){
							$vterm = preg_replace('@!@','',$vterm);
							$operator = 'NOT IN';
						} else {
							$operator = 'IN';
						}
							
							
						
						$query_args['tax_query'][] = array(
							'taxonomy' => $q['taxonomy'],
							'field' => 'slug',
							'terms' => $vterm,
							'operator' => $operator
						);

					}

				} else {
					
					//echo $k; print_r($v);
		
					if(strstr($v, '!')){
						$v = preg_replace('@!@','',$v);
						if(in_array($k, $mc_params)){
							$compare = 'NOT LIKE';
							$v = '"'.$v.'"';
						} else {
							$compare = '!=';
						}
						
					} else if(strstr($v, '<')) {
						$v = preg_replace('@<@','',$v);
						$compare = '<';
					
					} else if(strstr($v, '>')) {
						$v = preg_replace('@>@','',$v);
						$compare = '>';					
						
					} else  {
						if(in_array($k, $mc_params)){
							$compare = 'LIKE';
							$v = '"'.$v.'"';
						} else {
							$compare = '=';
						}
					}
				
					$query_args['meta_query'][] = array(
						'key' => $k,
						'value' => $v,
						'compare' => $compare,
						'type' => $fieldtype	
					);
				
				}
			} else if(strstr($k, '__')){
				$query_args[$k] = explode(',',$v);
			}
	}
	

	
	
	// wpdb keyword search
	//
	// Currently searches all non system meta fields
	// next version: loop through $job->getVars to build get_col query for specified fields only
	

	// if($params['keywords'] && $params['keywords'] != ''){
	// 	global $wpdb;
	// 	$keywords = sanitize_text_field( $params['keywords'] );
	// 	$post_ids_meta = $wpdb->get_col( " SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key NOT LIKE '\_%' AND meta_value LIKE '%".mysql_real_escape_string($keywords)."%'" );
	// 	$query_args['post__in'] = $post_ids_meta != '' ? $post_ids_meta : 0;
	// }
	

	//echo '<pre>'; print_r($query_args); echo '</pre>';


	// run WP query
	$result = new WP_Query($query_args);
	
	//echo '<pre>'; print_r($result); echo '</pre>';
	
	//setup posts array
	$posts = array();
	
	// push meta values into post object
	for($i=0; $i<count($result->posts); $i++){
		$cleanmeta = array();
		$thispost = $result->posts[$i];
		$meta = get_post_custom( $thispost->ID );
		foreach($meta as $k=>$v){
			
			$foundkeys = array();
			$q = $$type->getQuestion($k);
			
			if($q['fieldtype'] == 'date') {
				if($v[0]){
					$v[0] = formatDate($v[0],$q);
					$datearr = explode(' ', $v[0]);
					$cleanmeta[$k.'_day'] = $datearr[0];
					$cleanmeta[$k.'_month'] = $datearr[1];
					$cleanmeta[$k.'_year'] = $datearr[2];
				}
			}

			if($q['fieldtype'] == 'image'){
				$cleanmeta[$k.'_images'] = wp_get_attachment_image_src(get_intermediate_image_sizes());
			}
		
			
			$cleanmeta[$k] = unserialize($v[0]) ? unserialize($v[0]) : $v[0];
		}
		$thispost->meta = $cleanmeta;
		
		//push author meta into post object
		$cleanauthormeta = array();
		$authormeta = get_user_meta($thispost->post_author);
		if($authormeta) foreach($authormeta as $k=>$v){
			$cleanauthormeta[$k] = unserialize($v[0]) ? unserialize($v[0]) : $v[0];
			$q = $$type->getQuestion($k);
			if($q['fieldtype'] == 'image'){
				foreach($cleanauthormeta[$k] as $img){
					$src = wp_get_attachment_image_src($img,'full');
					$cleanauthormeta[$k.'_image'][] = '<img src="'.$src[0].'" />';
				}
			}
		}
		$thispost->authormeta = $cleanauthormeta;
		
		//push group meta into post object
		
		//$groupid = $thispost->meta['group_id'] ? $thispost->meta['group_id'] : $thispost->post_author;
		$groupmeta = get_user_meta($thispost->meta['group_id']);
		
		if(!$role){
			$querieduser = new WP_User($thispost->meta['group_id']);
			$role = $querieduser->roles[0];
			global $$role;
		}

		$cleangroupmeta = array();
		if($groupmeta) foreach($groupmeta as $k=>$v){
			$cleangroupmeta[$k] = unserialize($v[0]) ? unserialize($v[0]) : $v[0];


			$q = $$role->getQuestion($k);
			if($q['fieldtype'] == 'image'){
				if(is_array($cleangroupmeta[$k])){
					foreach($cleangroupmeta[$k] as $img){
						$src = wp_get_attachment_image_src($img,'recruiter_icon_small');
						$cleangroupmeta[$k.'_image'][] = $src[0];
					}
				}
			}


		}
		$thispost->groupmeta = $cleangroupmeta;
		
		//push all taxonomy terms into post object
		foreach(get_taxonomies() as $tax){
			$terms = wp_get_post_terms($thispost->ID,$tax);
			if(count($terms > 0)){
				$terms_arr = array();
				foreach($terms as $term){ $terms_arr[] = (array)$term; }
				$cleanterms[$tax] = $terms_arr;
			}
		}
		$thispost->terms = $cleanterms;
		
		// update returned in search count
		if($params['inc_search_count'] == 'true'){
			$search_count = get_post_meta($thispost->ID,'search_count',true);
			//echo 'found post '.$thispost->ID.' with post count '.$search_count;
			if($search_count != ''){
				update_post_meta($thispost->ID,'search_count',intval($search_count)+1);
			} else {
				update_post_meta($thispost->ID,'search_count','1');
			}
		}
		
		// build posts array with promoted items first
		// this smacks of business logic...
		
		if($thispost->meta['promote'][0] == $params['industry'] && $thispost->meta['promote_enabled'][0] != '' && $thispost->meta['ad_type'][0] == 'sponsored'){
			array_unshift($posts, $thispost);	
		} else {
			$posts[] = $thispost;
		}


	}
	
	// add new posts array back into returned object
	$result->posts = $posts;


	if($ppp > -1){
		$result->paging = ceil(count($posts)/$ppp);
	}

	
	//if(count($result->posts) == 0) $result['query'] = $query_args;

	if($params['angular']) {

		echo json_encode($result);
		die();
	}
	
	//choose return path (AJAX or HTTP)
	if($_POST){
		if($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			echo json_encode($result);
		} else {
			header('Location: '.strtok($_SERVER["HTTP_REFERER"],'?').'?'.http_build_query($clean_params, '', '&amp;'));
		}		
	} else {
		return $result;
	}

	
	die();
	
}



// wp_register_script( 'directory_search', WP_PLUGIN_URL.'/ibe-directory/js/directory_search.js', array('jquery') );
// wp_localize_script( 'directory_search', 'directory_search', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
// wp_enqueue_script( 'directory_search' );
