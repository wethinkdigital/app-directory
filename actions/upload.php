<?php
	
add_action("wp_ajax_directory_upload", "directory_upload");
add_action("wp_ajax_nopriv_directory_upload", "directory_upload");

function directory_upload(){
		
	// Nonce check
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'directory_upload_nonce')) {
      exit('You are not authorised to take this action');
	}
	
	// check required data has been supplied
	if(!$_REQUEST['uploadname']) die('Upload name not supplied');

	if ($_FILES) {

		$attach_id = media_handle_upload('upload',$user->ID);
		
		if($_REQUEST['attachto'] == 'user'){
		
			$images = get_user_meta($user->ID, $_REQUEST['uploadname'], true);
			if(!is_array($images) || empty($images)) { $images = array(); }
		     	$images[] = strval($attach_id);
			
			update_user_meta($user->ID, $_REQUEST['uploadname'], $images);
	
		} else {
			
			$images = get_post_meta($_REQUEST['post_id'], $_REQUEST['uploadname'], true);
			if(!is_array($images) || empty($images)) { $images = array(); }
		     	$images[] = strval($attach_id);
			
			update_post_meta($_REQUEST['post_id'], $_REQUEST['uploadname'], $images);

		}
		unset($_FILES);
	
		  
	}
	
	
		
}

// AJAX disabled as form redirects after submission
// add_action( 'init', 'directory_update_enqueue' );

/*
function directory_upload_enqueue() {
   wp_register_script( 'directory_upload', WP_PLUGIN_URL.'/ibe-directory/js/directory_upload.js', array('jquery') );
   wp_localize_script( 'directory_upload', 'directory_upload', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
   wp_enqueue_script( 'directory_upload' );
}
*/