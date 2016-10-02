<?php
	
add_action("wp_ajax_directory_update_user", "directory_update_user");
add_action("wp_ajax_nopriv_directory_update_user", "directory_update_user");

function directory_update_user(){
	
	
	/* PSEUDO
		
		Verify nonce
		
		Translate $_REQUEST into $params
		
		Set the ID of the user we're updating (either current user or user ID supplied as encrypted string)
		
		Set which usertype object is used to complete the update (to get correct user vars to match against)
		
		Collect all user (not meta) info into $update_user, validating against array of wp_user fields
		
		If updating a recruiter (not recruiter_admin), build:
			- user_login
			- user_nicename
			- user_displayname
			and verify password and confirm_password match (if supplied); create $error[] if not
			
		Collect all usermeta into $update_usermeta, validating against expected vars from user definition
		
		If a logo was provided, upload then retrieve the attachment ID and append to $update_usermeta
		
		Update user
		
		Update usermeta
		
	*/
	

	// Nonce check
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'directory_update_user_nonce')) {
      exit('You are not authorised to take this action');
	}
	

	// set params
	if($_REQUEST) foreach($_REQUEST as $k=>$v) { $params[$k] = $v; }




	// see if (encrypted) user ID has been supplied, if not use logged in user's ID
	

	// supplied role object exists
	$role = $_REQUEST['role'];
	global $$role, $user, $usermeta;
	if(!$$role) die('Role "'.$role.'" does not exist');
	
	
	// set up core user data (stored in wp_users)
	$valid = array('user_pass','user_login','user_nicename','user_url','user_email','display_name','nickname','first_name','last_name','description','rich_editing','user_registered','jabber','aim','yim');
		
	foreach(array_filter($params) as $k=>$v){
		if(in_array($k, $valid)){
			$update_user[$k] = $v;
		}
	}
	
	
	// assemble / validate data for update user
	if($params['role'] == 'recruiter'){
		if($update_user['user_pass'] != '' && $update_user['user_pass'] != $params['confirm_user_pass']) $error[] = 'Passwords do not match';
		$update_user['user_login'] = $update_user['user_nicename'] = strtolower($update_user['first_name']).'_'.strtolower($update_user['last_name']);
		$update_user['display_name'] = $update_user['first_name'].' '.$update_user['last_name'];	
		unset($update_usermeta['confirm_user_pass']);
	}

	// set up user meta data
	$varnames = $$role->getVarNames();
	$validmeta = array_diff($varnames,$valid); // why?
	
	foreach(array_filter($params) as $k=>$v){ 
		if(in_array($k, $validmeta)){
			
			// format urls with leading "http://"
			if($q = $$role->getQuestion($k)){
				if($q['fieldtype'] == 'link'){
					$v = $$role->makeLink($v);
				}
			}
			
			
			$update_usermeta[$k] = $v;
		}
	}	
		
	

	
	// Set ID of user we're updating
	if($params['ID']) die('Unsecured ID provided, please use encrypted field function');

	if($params['encrypted']){
		global $dircore;
		parse_str($dircore->decrypt($params['encrypted']),$safeparams);
		$params = array_merge($params,$safeparams);
	}

	$update_user['ID'] = $params['ID'] ? $params['ID'] : $user->ID; 


// echo '<pre>'; print_r($update_user); echo '</pre>';
	
// echo '<pre>'; print_r($params); echo '</pre>';

/*
	echo '<pre>Item var names '; print_r($varnames); echo '</pre>';
	echo '<pre>Valid meta fields '; print_r($validmeta); echo '</pre>';
	echo '<pre>User '; print_r(array_filter($update_user)); echo '</pre>';
	echo '<pre>Usermeta '; print_r($update_usermeta); echo '</pre>';
*/

	//die();
	
	



	
	// If no errors, update user and usermeta
	if(!$error){
		wp_update_user($update_user);
		if($update_usermeta){
			foreach($update_usermeta as $k=>$v){
				update_user_meta($update_user['ID'],$k,$v);
			}
		}
		
		// Check if files were submitted with the form
		// NB If handed by AJAX FormData, $_FILES still exists but array contains no file data
		$uploads = $$role->uploadFiles();

// echo '<pre>'; print_r($uploads); echo '</pre>';

		if($uploads){
			foreach($uploads as $upload){
				if($upload['attachment_id']) {
					update_user_meta($update_user['ID'],$upload['varname'],array($upload['attachment_id']));
					update_user_meta($update_user['ID'],$upload['varname'].'_label',$upload['original_filename']);
				} else  {
					update_user_meta($update_user['ID'],$upload['varname'],$upload['filepath'].$upload['filename']);
					update_user_meta($update_user['ID'],$upload['varname'].'_label',$upload['original_filename']);
				}
			}
		}

	
	// errors found, return to form with error details	
	} else {
		session_start();
		$_SESSION['errors'] = $error;
		$_SESSION['userdata'] = $update_user;
		$_SESSION['usermeta'] = $update_usermeta;
		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}
	
	//Form submitted by AJAX
	if($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$result['result'] = 'success';
		if($params['success_message']){
			$result['message'] = $params['success_message'];
		}

		if($params['redirect']){
			$result['redirect'] = $params['redirect'];
		}
		echo json_encode($result);
		die();
		
	
	// Form submitted by HTTP
	} else {
		if($params['redirect']){
			header('Location: '.$params['redirect']);
		} else {
			header('Location: '.$_SERVER['HTTP_REFERER']);
		}
	}
	
	

	
}


wp_register_script( 'directory_update_user', WP_PLUGIN_URL.'/ibe-directory/js/directory_update_user.js', array('jquery') );
wp_localize_script( 'directory_update_user', 'directory_update_user', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
wp_enqueue_script( 'directory_update_user' );