<?php
	
add_action("wp_ajax_nopriv_directory_login", "directory_login");
add_action("wp_ajax_directory_login", "directory_login");

function directory_login(){
		
	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'directory_login_nonce')) {
      exit('You are not authorised to take this action');
	} 

    $info = array();
    $info['user_password'] = $_REQUEST['password'];
    $info['remember'] = true;
    
	// allow login in by email address
    if(filter_var($_REQUEST['username'], FILTER_VALIDATE_EMAIL)) {
	    $user = get_user_by('email', $_REQUEST['username']);
	    if($user) $info['user_login'] = $user->user_login;
	} else {
    	$info['user_login'] = $_REQUEST['username'];
	}

    $login = wp_signon( $info, false );
    if (!is_wp_error($login) ){
		$login = (array) $login;
	    if($_REQUEST['redirect']) $login['redirect'] = $_REQUEST['redirect'];
        echo json_encode($login);
    } else {
        echo json_encode(array('loggedin'=>false, 'message'=>'<span class="error">Username or password not recognised</span>'));
    }

	die();
	
}



wp_register_script( 'directory_login', WP_PLUGIN_URL.'/ibe-directory/js/directory_login.js', array('jquery') );
wp_localize_script( 'directory_login', 'directory_login', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
wp_enqueue_script( 'directory_login' );