<?php

///////////////////////////////////////////
//
// Define user role
// no spaces - letters, hyphen or underscore only
//
///////////////////////////////////////////
	
$role = 'candidate'; 


///////////////////////////////////////////
//
// Define item labelling (WordPress admin)
//
///////////////////////////////////////////

$label = 'Candidate';


///////////////////////////////////////////
//
// Define user type admin root
//
///////////////////////////////////////////

$adminroot = '/candidate-dashboard';


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
		'name' => 'cv_upload',
		'label' => 'Upload CV',
		'placeholder' => '',
		'fieldtype' => 'file',
		'multiple' => 'false',
		'group' => 'profile'
	),

	
	array(
		'name' => 'personal_summary',
		'label' => 'Personal summary',
		'instructions' => 'Please do not include any contact information in the fields below. This is the information that recruiters will be able to search against to find candidates.',
		'placeholder' => '',
		'fieldtype' => 'textarea',
		'group' => 'profile'
	),

	array(
		'name' => 'education',
		'label' => 'Education',
		'placeholder' => '',
		'fieldtype' => 'textarea',
		'group' => 'profile'
	),

	array(
		'name' => 'career_history',
		'label' => 'Career history',
		'placeholder' => '',
		'multiple' => 'true',
		'fieldtype' => 'textarea',
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
		'name' => 'current_job_title',
		'label' => 'Current job title',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'profile'
	),
	array(
		'name' => 'profile_status',
		'label' => 'Status',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Actively jobseeking, show me in search results'	=> array('slug' => 'active'),	
			'Don\'t show me in search results'	=> array('slug' => 'passive'),	
		),
		'group' => 'profile'
	),
	array(
		'name' => 'industry',
		'label' => 'Industry',
		'placeholder' => '',
		'fieldtype' => 'check',
		'value' => $sector->taxTreeRecursive(),
		'keyword' => 'true',
		'select_parent' => 'false',

	),
);