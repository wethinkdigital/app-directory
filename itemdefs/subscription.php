<?php

///////////////////////////////////////////
//
// Define item type
// no spaces - letters, hyphen or underscore only
//
///////////////////////////////////////////
	
$type = 'subscription'; 


///////////////////////////////////////////
//
// Define item labelling (WordPress admin)
//
///////////////////////////////////////////

$label = 'subscriptions';
$single_label ='subscription';

///////////////////////////////////////////
//
// Item type is hierarchical
//
///////////////////////////////////////////

$hierarchical = false;


///////////////////////////////////////////
//
// Item type expires after (days)
// Will no longer be valid if older than $expires
//
///////////////////////////////////////////

$expires = array('subscription_date' => 90);

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
		'name' => 'subscriber_email',
		'label' => 'Email address',
		'placeholder' => '',
		'fieldtype' => 'email',
		'group' => 'headline',
	),
	array(
		'name' => 'subscription_date',
		'label' => 'Subscription date',
		'placeholder' => '',
		'fieldtype' => 'hidden',
		'group' => 'headline'
	),
	array(
		'name' => 'subscription_type',
		'label' => 'Subscription type',
		'placeholder' => '',
		'fieldtype' => 'hidden',
		'group' => 'headline'
	),
	array(
		'name' => 'industry',
		'label' => 'Industry',
		'placeholder' => '',
		'fieldtype' => 'hidden',
		'value' => $sector->taxTreeRecursive(),
		'taxonomy' => 'sector',
	),	
	array(
		'name' => 'item_type',
		'label' => 'Item type',
		'placeholder' => '',
		'fieldtype' => 'hidden',
		'group' => 'headline'
	),
	array(
		'name' => 'status',
		'label' => 'Status',
		'placeholder' => '',
		'fieldtype' => 'hidden',
		'group' => 'headline'
	),
);