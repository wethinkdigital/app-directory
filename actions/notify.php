<?php
	
add_action("wp_ajax_directory_notify", "directory_notify");
add_action("wp_ajax_nopriv_directory_notify", "directory_notify");

function directory_notify(){
		global $dircore;
	
	if($_REQUEST['encrypted']){
		parse_str($dircore->decrypt($_REQUEST['encrypted']),$safeparams);
		$_REQUEST = array_merge($_REQUEST,$safeparams);
	}

	//echo '<pre>'; print_r($_REQUEST); echo '</pre>';

	$dircore->notify($_REQUEST);

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

wp_register_script( 'directory_notify', WP_PLUGIN_URL.'/ibe-directory/js/directory_notify.js', array('jquery') );
wp_localize_script( 'directory_notify', 'directory_notify', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
wp_enqueue_script( 'directory_notify' );