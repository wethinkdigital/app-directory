<?php
	
add_action("wp_ajax_directory_search_user", "directory_search_user");
add_action("wp_ajax_nopriv_directory_search_user", "directory_search_user"); 

function directory_search_user($params = null){
		
	if($_REQUEST) foreach($_REQUEST as $k=>$v) $params[$k] = $v;
	
	
	if($params['encrypted']){
		global $dircore;
		parse_str($dircore->decrypt($params['encrypted']),$safeparams);
		$params = array_merge($params,$safeparams);
	}
	

			
	$role = $params['role'];
	global $$role, $user, $usermeta;
	if(!$$role) die('Role "'.$role.'" does not exist');
	$vars = $$role->getVarNames();
	
	$params = $$role->prepVars($params);


	$order = $params['order'] ? $params['order'] : 'DESC';


	// set up basic query args
	$query_args = array(	'role' => $role,
							'orderby' => 'date',
							'order' => $order,
							'posts_per_page' => -1,
							'meta_query' => array()
							); 
	


	
	// add ordering if requested
	if($params['orderby']){
		$query_args['meta_key']	= $params['orderby'];
		$query_args['orderby'] = 'meta_value';
	}
	
	// remove unexpected search variables
	if($params){
		$clean_params = array();
		foreach($params as $k=>$v){
			if(in_array($k, $vars) && $v != ''){
				// (below) supports individual search values presented as array but not multiple values within a search field
				$clean_params[$k] = is_array($v) ? $v[0] : $v; 
			}
		}
	}

	// check which params have multichoice answers
	if($clean_params){
		$mc_params = array();
		foreach($clean_params as $k=>$v){
			$q = $$role->getQuestion($k);
			if(is_array($q['value'])){
				$mc_params[] = $k;
			}
		}
	}		

	// set meta query for each valid search param
	foreach($clean_params as $k=>$v){
		
			$q = $$role->getQuestion($k);
			
			if($q['taxonomy']){
				
				$query_args['tax_query'][] = array( 'taxonomy' => $q['taxonomy'],
													'field' => 'slug',
													'terms' => $v
													);
				
			} else {
				
		
					
	
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
	}
	

	
	//echo '<pre>'; print_r($user); echo '</pre>';
	
	// wpdb keyword search
	//
	// Currently searches all non system meta fields
	// next version: loop through $job->getVars to build get_col query for specified fields only
	

	if($params['keywords'] && $params['keywords'] != ''){
		global $wpdb;
		$keywords = sanitize_text_field( $params['keywords'] );
		$post_ids_meta = $wpdb->get_col( " SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key NOT LIKE '\_%' AND meta_value LIKE '%".mysql_real_escape_string($keywords)."%'" );
		$query_args['include'] = $post_ids_meta;
	}
	
	//die(print_r($query_args));
	//echo '<pre>'; print_r($params); echo '</pre>';
	//echo '<pre>'; print_r($query_args); echo '</pre>';

	
	// run WP query
	$result = new WP_User_Query($query_args);
	//print_r($result);
	
	//setup posts array
	$users = array();
	
	// push meta values into post object
	for($i=0; $i<count($result->results); $i++){
		$cleanmeta = array();
		$thisuser = $result->results[$i];
		//echo $thisuser->ID.', ';
		$meta = get_user_meta( $thisuser->ID );
		foreach($meta as $k=>$v){
			
// 			$foundkeys = array();
			$q = $$role->getQuestion($k);
			
			if($q['fieldtype'] == 'date') {
				if($v[0]){
					$v[0] = formatDate($v[0],$q);
					$datearr = explode(' ', $v[0]);
					$cleanmeta[$k.'_day'] = $datearr[0];
					$cleanmeta[$k.'_month'] = $datearr[1];
					$cleanmeta[$k.'_year'] = $datearr[2];
				}
			}
			



			// converts salary string to currency format
			// needs better interception of strings already formatted
			// Causes explosion on testing server
/*
			if($k == 'salary_details' && $v[0]){
				$currcode = unserialize($meta['salary_currency'][0]);
				$currsym = $$role->getCurrencySymbol('en_GB',$currcode[0]);
				$salval = preg_replace("/[^0-9.]/", "", $v[0]);
				$v[0] = $$currsym.number_format(floatval($salval),0,'.',',');
			}
*/
			
			$cleanmeta[$k] = unserialize($v[0]) ? unserialize($v[0]) : $v[0];
		}
		
		
		
		$thisuser->meta = $cleanmeta;
		

		
		

		
		// update returned in search count
		if($params['inc_search_count'] == 'true'){
			$search_count = get_user_meta($thispost->ID,'search_count',true);
			//echo 'found post '.$thispost->ID.' with post count '.$search_count;
			if($search_count != ''){
				update_user_meta($thisuser->ID,'search_count',intval($search_count)+1);
			} else {
				update_user_meta($thisuser->ID,'search_count','1');
			}
		}
		
		$users[] = $thisuser;


	}

	$result->foundusers = $users;
	
	//print_r($users);
	//echo count($users);

	
	// add new users array back into returned object
// 		echo '<pre>'; print_r($result); echo '</pre>';
	
	//if(count($result->posts) == 0) $result['query'] = $query_args;
	
	// choose return path (AJAX or HTTP)
	if($_POST){
		if($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			echo json_encode($users);
		} else {
			header('Location: '.strtok($_SERVER["HTTP_REFERER"],'?').'?'.http_build_query($clean_params, '', '&amp;'));
		}		
	} else {
		return $result;
	}
	
	die();
	
}



wp_register_script( 'directory_search_user', WP_PLUGIN_URL.'/ibe-directory/js/directory_search_user.js', array('jquery') );
wp_localize_script( 'directory_search_user', 'directory_search_user', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
wp_enqueue_script( 'directory_search_user' );
