<?php

///////////////////////////////////////////
//
// Define user role
// no spaces - letters, hyphen or underscore only
//
///////////////////////////////////////////
	
$role = 'recruiter_admin'; 


///////////////////////////////////////////
//
// Define item labelling (WordPress admin)
//
///////////////////////////////////////////

$label = 'Recruiter admin';


///////////////////////////////////////////
//
// Define user type admin root
//
///////////////////////////////////////////

$adminroot = '/recruiter-dashboard';


///////////////////////////////////////////
//
// Define questions / values for item type
// To add a question, add a new array to the end of $vars
//
// 	array(
//		'name' => 'question_2',
//		'label' => 'How question 2 is labelled?',
//		'instructions' => 'Further instructions on how to complete this question'
//		'placeholder' => 'field suggestion for question 2',
//		'fieldtype' => 'check', (can be 'text', 'textarea', 'richtext', 'dropdown', 'check', 'radio', 'password', 'email', 'file')
//		'value' => array(
//			'Option 1' 		=> '1',	
//			'Option 2' 		=> '2',	
//			'Option 3' 		=> '3'	
//		),
//		'group' => 'package',
//		'required' => 'publish', (can be 'save', 'publish'. Fields set to publish will also be required for save)
//		'extra_class' => 'myclass'
//	),
//
///////////////////////////////////////////



$vars = array(
	
	array(
		'name' => 'first_name',
		'label' => 'First name',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'basics',
		'required' => 'save',
		'keyword' => 'true'
	),

	array(
		'name' => 'last_name',
		'label' => 'Last name',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'basics'
	),
	
	array(
		'name' => 'user_email',
		'label' => 'Email',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'basics',
		'required' => 'save',
		'keyword' => 'true'
	),

	array(
		'name' => 'user_pass',
		'label' => 'Password',
		'placeholder' => '',
		'fieldtype' => 'password',
		'group' => 'basics',
		'required' => 'save',
		'keyword' => 'true'
	),

	array(
		'name' => 'confirm_user_pass',
		'label' => 'Confirm password',
		'placeholder' => '',
		'fieldtype' => 'password',
		'group' => 'basics',
		'required' => 'save',
		'keyword' => 'true'
	),
	
	array(
		'name' => 'recruiter_name',
		'label' => 'Organisation Name',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'profile',
		'required' => 'save',
		'keyword' => 'true'
	),

	array(
		'name' => 'recruiter_sector',
		'label' => 'Sector',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Private'	=> array('slug' => 'private'),	
			'Public/Charity/University/HE'	=> array('slug' => 'public'),	
		),
		'group' => 'sysadmin'
	),


	array(
		'name' => 'logo',
		'label' => 'Logo',
		'placeholder' => '',
		'fieldtype' => 'image',
		'multiple' => 'false',
		'group' => 'profile'
	),

	array(
		'name' => 'brand_header',
		'label' => 'Brand header',
		'instructions' => 'Recommended size 960px x 150px',
		'placeholder' => '',
		'fieldtype' => 'image',
		'multiple' => 'false',
		'group' => 'profile'
	),
	
	array(
		'name' => 'boilerplate',
		'label' => 'Succinct profile / Boilerplate',
		'placeholder' => '',
		'fieldtype' => 'richtext',
		'group' => 'profile',
		'limit' => '600',
		'tags_allowed' => 'p,br,strong,em'
	),
	
	array(
		'name' => 'video',
		'label' => 'Promo video',
		'placeholder' => 'Your YouTube video ID',
		'fieldtype' => 'text',
		'group' => 'profile'
	),

	array(
		'name' => 'contact_phone',
		'label' => 'Contact phone no',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'profile'
	),

	array(
		'name' => 'job_title',
		'label' => 'Job title',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'profile'
	),
	
	array(
		'name' => 'department',
		'label' => 'Department',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'profile'
	),
	array(
		'name' => 'default_app_email',
		'label' => 'Default application email',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'profile'
	),
	array(
		'name' => 'website',
		'label' => 'Main website address',
		'placeholder' => '',
		'fieldtype' => 'link',
		'group' => 'profile'
	),
	array(
		'name' => 'contactpage',
		'label' => 'Your website contact page',
		'placeholder' => '',
		'fieldtype' => 'link',
		'group' => 'profile'
	),
	array(
		'name' => 'jobspage',
		'label' => 'Your website jobs page',
		'placeholder' => '',
		'fieldtype' => 'link',
		'group' => 'profile'
	),
	array(
		'name' => 'main_address',
		'label' => 'Main address',
		'placeholder' => '',
		'fieldtype' => 'textarea',
		'group' => 'profile'
	),
	array(
		'name' => 'invoice_address',
		'label' => 'Invoice address',
		'placeholder' => '',
		'fieldtype' => 'textarea',
		'group' => 'profile'
	),
	array(
		'name' => 'subscriber',
		'label' => 'Subscriber',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Standard'	=> array('slug' => 'standard'),	
			'Annual'	=> array('slug' => 'annual'),	
		),
		'group' => 'sysadmin'
	),
);