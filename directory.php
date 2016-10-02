<?php 
	
/* 
Plugin Name: app Directory 
Description: 
Version: 1.0 
Author: David Robins 
*/

require_once('config.php');
require_once('directory_coreclass.php');
require_once('directory_itemclass.php');
require_once('directory_userclass.php');
require_once('directory_taxclass.php');
require_once('directory_subscriptionclass.php');
require_once('user_functions.php');
require_once('item_functions.php');
require_once('dev_functions.php');
require_once('jsapi.php');
require_once('systemplate.php');
require_once('cpt_template.php');


global $wp_rewrite;
$dircore = new directoryCore();

function directoryInit(){

	foreach(glob(__DIR__ . '/taxdefs/*.php') as $filename)
	{
	    include($filename);
	    global $$type;
	    $$type = new taxdef($type,$label,$single_label,$items,$terms);
	    
	}
	
	foreach(glob(__DIR__ . '/itemdefs/*.php') as $filename)
	{
	    //$sectors = &$sector->getTerms();
	    include($filename);
	    global $$type;
	    $$type = new itemdef($type,$label,$single_label,$hierarchical);
	    $$type->vars = $vars;
	    $$type->setExpire($expires);
	    $$type->tax = &$sector->getTerms();
	    
	    // this happens before wordpress init so can't access custom taxonomies 
	    // if we put setvars inside itemdef, we're repeating code
	    
	    // Passing booleans into functions erratic / bad idea
	    // Find a better way to control this
	    // Hierarchical set to TRUE for all items in the interim (26/08/15)
	    
	    // Could be improved. Depends on variables of one file superceding the last
	    // Unset between each file? Or get file contets as an object?
	}
	
	foreach(glob(__DIR__ . '/actions/*.php') as $filename)
	{
	    include($filename);
	}
	
	foreach(glob(__DIR__ . '/userdefs/*.php') as $filename)
	{
	    include($filename);
	    global $$role;
	    $$role = new userdef($role,$label);
	    $$role->setVars($vars);
	    $$role->setAdminRoot($adminroot);
	}

}

add_action( 'init', 'directoryInit' );


function directoryEnqueue() {

	wp_register_script( 'directory_core', WP_PLUGIN_URL.'/ibe-directory/js/directory_core.js', array('jquery') );
	wp_enqueue_script( 'directory_core' );

	wp_register_script( 'angular', WP_PLUGIN_URL.'/ibe-directory/js/angular.min.js', array('jquery') );
	wp_enqueue_script( 'angular' );

	wp_register_script( 'directory_app', WP_PLUGIN_URL.'/ibe-directory/js/directory_app.js', array('jquery') );
	wp_enqueue_script( 'directory_app' );

	wp_register_script( 'serialize_object', WP_PLUGIN_URL.'/ibe-directory/js/jquery.serialize-object.js', array('jquery') );
	wp_enqueue_script( 'serialize_object' );
	
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

}

add_action( 'init', 'directoryEnqueue' );

function directoryGetUser(){
	global $user, $usermeta, $wp_roles;
	$user = $usermeta = null;
	if(is_user_logged_in()) {
		$user = wp_get_current_user();
		$user_custom = get_user_meta($user->ID);
		foreach($user_custom as $k=>$v){
			$usermeta[$k] = count($v) == 1 ? $v[0] : $v;
		}
		if(!$usermeta['group_id']) $usermeta['group_id'] = $user->ID;
	}
}

add_action( 'init', 'directoryGetUser' );




function directory_deactivate(){
	foreach(glob(__DIR__ . '/userdefs/*.php') as $filename)
	{
	    remove_role( strtok($filename, '.') );
	}

}

register_deactivation_hook( __FILE__, 'directory_deactivate' );