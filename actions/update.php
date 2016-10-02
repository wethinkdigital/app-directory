<?php
	
add_action("wp_ajax_directory_update", "directory_update");

function directory_update(){
	
	global $user, $dircore;

	// Nonce check	
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'directory_update_nonce')) {
      exit('You are not authorised to take this action');
	} 
	
	// Was post ID provided
	if(!$_REQUEST['post_id']) exit('No post ID was supplied');
	
	
	// get relevant object
	$type = $_REQUEST['type'];
	global $$type;
	

	$varnames = $$type->getVarNames();
	
	foreach($varnames as $var){
		$q = $$type->getQuestion($var);
		if($q['taxonomy']){
			if($_REQUEST[$var]) foreach($_REQUEST[$var] as $v){
				$term = get_term_by('slug',$v,$q['taxonomy']);
				$terms[] = $term->term_id;
			}
			wp_set_object_terms($_REQUEST['post_id'],$terms,$q['taxonomy']);
		} else {
			if(is_array($q['value']) && !is_array($_REQUEST[$var])){
				$_REQUEST[$var] = array($_REQUEST[$var]);
			}
			if($q['fieldtype'] == 'date') $_REQUEST[$var] = strtotime($_REQUEST[$var]);
			update_post_meta($_REQUEST['post_id'],$var,$_REQUEST[$var]);
		}
		$result[$var] = $_REQUEST[$var];
	}
	
	// Check if files were submitted with the form
	// NB If handed by AJAX FormData, $_FILES still exists but array contains no file data
	$uploads = $$type->uploadFiles($_REQUEST['post_id']);
	if($uploads){
		foreach($uploads as $upload){
			// need to ensure these are mutually exclusive
			if($upload['attachment_id']) {
				update_post_meta($_REQUEST['post_id'],$upload['varname'],array($upload['attachment_id']));
				update_post_meta($_REQUEST['post_id'],$upload['varname'].'_label',$upload['original_filename']);
			} else  {
				update_post_meta($_REQUEST['post_id'],$upload['varname'],$upload['filepath'].$upload['filename']);
				update_post_meta($_REQUEST['post_id'],$upload['varname'].'_label',$upload['original_filename']);
			}
		}
	}
	
	// Send notification of item creation to supplied email
	if($_REQUEST['notify']){
		$dircore->notify($_REQUEST);
		$result['notifyuser'] = $_REQUEST['notify'];
	}
	

	if($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		echo json_encode($result);
	} else {
		if($_REQUEST['redirect']){
			header('Location: '.$_REQUEST['redirect']);
		} else {
			header('Location: '.$_SERVER['HTTP_REFERER']);
		}
	}
	
	die();
	
}



wp_register_script( 'directory_update', WP_PLUGIN_URL.'/ibe-directory/js/directory_update.js', array('jquery') );
wp_localize_script( 'directory_update', 'directory_update', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
wp_enqueue_script( 'directory_update' );