<?php
	
add_action("wp_ajax_directory_create_user", "directory_create_user");
add_action("wp_ajax_nopriv_directory_create_user", "directory_create_user");

function directory_create_user(){

	global $dircore;
			
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'directory_create_user_nonce')) {
      exit('You are not authorised to take this action');
	}
	
	$valid = array('user_pass','user_login','user_nicename','user_url','user_email','display_name','nickname','first_name','last_name','description','rich_editing','user_registered','role','jabber','aim','yim','redirect','readterms','recruiter_name');
	
	foreach($_REQUEST as $k=>$v){
		if(in_array($k, $valid)){
			$params[$k] = $_REQUEST[$k];
		}
	}
	
	$error = array();
	
	if($params['first_name'] == '') $error[] = 'First name missing';
	if($params['user_email'] == '') $error[] = 'Email missing';
	if($params['user_pass'] == '') $error[] = 'Password missing';
	if($params['user_pass'] != $_REQUEST['confirm_user_pass']) $error[] = 'Passwords do not match';
	if($params['user_email'] != $_REQUEST['confirm_user_email']) $error[] = 'Email addresses do not match';
	if($params['readterms'] == '') $error[] = 'Confirm you have read the terms and conditions';
	
	

	if(count($error) == 0){ // Data supplied is good, no errors
		$params['user_login'] = strtolower($params['first_name']).'_'.strtolower($params['last_name']);
		$newuserID = wp_insert_user($params);
		if(!is_wp_error($newuserID)){
			if($_REQUEST['group_id']) update_user_meta($newuserID,'group_id',$_REQUEST['group_id']);
			if($_REQUEST['recruiter_sector']) update_user_meta($newuserID,'recruiter_sector',$_REQUEST['recruiter_sector']);
			if($_REQUEST['recruiter_name']) update_user_meta($newuserID,'recruiter_name',$_REQUEST['recruiter_name']);

			if($_REQUEST['autologin'] == 'true'){
			    wp_set_current_user($id); // set the current wp user
			    wp_set_auth_cookie($id); // start the cookie for the current registered user
			}
			
			// Send notification of user creation to supplied email
			if($_REQUEST['notify']){
				$dircore->notify($_REQUEST);
			}

			if($_REQUEST['redirect']){
				header('Location: '.$_REQUEST['redirect']);
			} else {
				header('Location: '.$_SERVER['HTTP_REFERER'].'?u='.$newuserID);
			}
		} else {
			// insert user has errored, pass back to the registration form with details
		}
	} else { // Errors with data supplied
		session_start();
		$_SESSION['errors'] = $error;
		$_SESSION['userdata'] = $params;
		header('Location: '.$_REQUEST['submitfail']);
	}
	
	
/////////////////////////////////////////////
//
// Form submitted by AJAX or HTTP
//
/////////////////////////////////////////////
	
	
	// $params['subtype'] = ($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? 'AJAX' : 'HTTP';
	// $params['referer'] = $_SERVER['HTTP_REFERER'];
	// $dircore->formAfter($params);
	
	
	
	// // Form submitted by AJAX

	// if($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	// 	$result['result'] = 'success';
	// 	if($params['success_message']){
	// 		$result['message'] = $params['success_message'];
	// 	}

	// 	if($params['redirect']){
	// 		$result['redirect'] = $params['redirect'].'?i='.$newitemID.'&u='.$result['post_author'];
	// 	}
	// 	echo json_encode($result);
		
	
	// // Form submitted by HTTP
	// } else {
	// 	if($params['redirect']){
	// 		header('Location: '.$_REQUEST['redirect']);
	// 	} else {
	// 		header('Location: '.$_SERVER['HTTP_REFERER'].'?u='.$newuserID);
	// 	}
	// }

	
	
/////////////////////////////////////////////
//
// End AJAX or HTTP
//
/////////////////////////////////////////////
	


	
}


//add_action( 'init', 'directory_create_user_enqueue' );

// function directory_create_user_enqueue() {
//    wp_register_script( 'directory_create_user', WP_PLUGIN_URL.'/ibe-directory/js/directory_create_user.js', array('jquery') );
//    wp_localize_script( 'directory_create_user', 'directory_create_user', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
//    wp_enqueue_script( 'directory_create_user' );
// }