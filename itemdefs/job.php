<?php

///////////////////////////////////////////
//
// Define item type
// no spaces - letters, hyphen or underscore only
//
///////////////////////////////////////////
	
$type = 'job'; 


///////////////////////////////////////////
//
// Define item labelling (WordPress admin)
//
///////////////////////////////////////////

$label = 'jobs';
$single_label ='job';

///////////////////////////////////////////
//
// Item type is hierarchical
//
///////////////////////////////////////////

$hierarchical = false;


///////////////////////////////////////////
//
// Item type expires after (days)
// Will no longer appear in search results if older than $expires
//
///////////////////////////////////////////

$expires = array('publish_from' => 90);





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
//		'fieldtype' => 'check', (can be 'text', 'date', 'textarea', 'richtext', 'dropdown', 'check', 'radio', 'password', 'email', 'file')
//		'value' => array(
//			'Option 1' 		=> array('slug' => '1'),	
//			'Option 2' 		=> array('slug' => '2'),	
//			'Option 3' 		=> array('slug' => '3')	
//		),
//		'datedisplay' => 'relative' (can be 'relative' for '3 days ago' or use PHP date function syntax eg 'jS M Y' for '12th Jan 2015'. Only affects field of type 'date')
//		'group' => 'package',
//		'required' => 'job_status:published', (can be 'save', 'publish'. Fields set to publish will also be required for save)
//		'extra_class' => 'myclass'
//	),
//
///////////////////////////////////////////

//$sector_vals = &$sector->getTerms();


$vars = array(
	
	array(
		'name' => 'ad_type',
		'label' => 'Ad type',
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Standard' 	=> array('slug' => 'standard'),	
			'Premium'	=> array('slug' => 'premium'),	
			'Sponsored' => array('slug' => 'sponsored'),		
		),
		'group' => 'publishing',
	),
	array(
		'name' => 'job_status',
		'label' => 'Status',
		
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Draft'		=> array('slug' => 'draft'),	
			'Published'	=> array('slug' => 'published'),	
			'Archived' 	=> array('slug' => 'archived')
		),
		'group' => 'publishing'
	),
	array(
		'name' => 'publish_from',
		'label' => 'Publish from',
		'fieldtype' => 'date',
		'datedisplay' => 'j M Y',
		'group' => 'publishing',
		'class' => 'fromtoday'
	),
	array(
		'name' => 'closing_date',
		'label' => 'Closing date',
		'fieldtype' => 'date',
		'datedisplay' => 'j M Y',
		'group' => 'publishing',
		'required' => 'job_status:published',
	),
	
	array(
		'name' => 'job_title',
		'label' => 'Job title',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'headline',
		'required' => 'save',
		'keyword' => 'true'
	),

	array(
		'name' => 'job_ref',
		'label' => 'Job reference',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'headline',
		'required' => 'save',
	),
	
	array(
		'name' => 'role_func',
		'label' => 'Role function',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Academic, Research, Teaching'		=> array('slug' => 'acadamic'),	
			'PhD'								=> array('slug' => 'phd'),	
			'Professional & Managerial' 		=> array('slug' => 'professional'),
			'Clerical & Administrative' 		=> array('slug' => 'clerical'),
			'Technical'					 		=> array('slug' => 'technical'),
			'Craft & Manual' 					=> array('slug' => 'craftmanual'),
			'Masters'					 		=> array('slug' => 'masters')
		),
		'addblank' => true,
		'group' => 'headline',
		'keyword' => 'true',
		'force' => 'phd@industry:phd',
	),

	array(
		'name' => 'job_level',
		'label' => 'Job level',
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Senior' 	=> array('slug' => 'senior'),	
			'Intermediate'	=> array('slug' => 'intermediate'),	
			'Entry' => array('slug' => 'entry'),		
		),		
		'group' => 'headline',
		'addblank' => true,
		'keyword' => 'true',
		'required' => 'job_status:published',
	),

	array(
		'name' => 'department',
		'label' => 'Department/Faculty',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'headline',
		'keyword' => 'true',
		'required' => 'job_status:published',
	),
	
		array(
		'name' => 'qualification',
		'label' => 'Qualification level',
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'group' => 'headline',
		'value' => array(
			'Integrated Doctorate/Masters' 	=> array('slug' => 'integrated_doctors_masters'),	
			'International Doctorate'	=> array('slug' => 'international_doctorate'),	
			'PhD' => array('slug' => 'phd'),		
			'Professional Doctorate' => array('slug' => 'professional_doctorate'),		
			'Masters' => array('slug' => 'masters'),		
		),		
		'keyword' => 'true',
		'dependency' => 'role_func:phd',
	),
	

	
	array(
		'name' => 'salary_range',
		'label' => 'Salary range',
		'placeholder' => '',
		'fieldtype' => 'check',
		'value' => array(
			'Not specified'	=> array('slug' => 'not_specified'),
			'£0 - £9999' 		=> array('slug' => '0-9999'),	
			'£10,000 - £14,999' => array('slug' => '10000-14999'),	
			'£15,000 - £19,999' => array('slug' => '15000-19999'),	
			'£20,000 - £29,999' => array('slug' => '20000-29999'),	
			'£30,000 - £39,999' => array('slug' => '30000-39999'),	
			'£40,000 - £49,999' => array('slug' => '40000-49999'),	
			'£50,000 - £69,999' => array('slug' => '50000-69999'),	
			'£70,000 - £99,999' => array('slug' => '70000-99999'),	
			'£100,000+' 		=> array('slug' => '100000'),	
		),
		'group' => 'package',
		'required' => 'job_status:published'
	),

	// array(
	// 	'name' => 'salary_currency',
	// 	'label' => 'Salary currency',
	// 	'placeholder' => '',
	// 	'fieldtype' => 'dropdown',
	// 	'value' => array(
	// 		'British pounds'	=> array('slug' => 'GBP'),	
	// 		'Euro' 				=> array('slug' => 'EUR'),	
	// 		'US Dollars' 		=> array('slug' => 'USD')	
	// 	),
	// 	'group' => 'package'
	// ),
	
	array(
		'name' => 'salary_details',
		'label' => 'Salary details',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'package',
		'required' => 'job_status:published',
		'dependency' => 'salary_range:!not_specified',
),

	array(
		'name' => 'studentship_funding',
		'label' => 'Studentship funding',
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'group' => 'package',
		'value' => array(
			'All types of Funding' 	=> array('slug' => 'all'),	
			'EU Students'	=> array('slug' => 'eu'),	
			'International Students' => array('slug' => 'international'),		
			'Self-Funded Students' => array('slug' => 'self_funded'),		
			'UK Students' => array('slug' => 'uk'),		
			'Other' => array('slug' => 'other'),		
		),		
		'addblank' => true,
		'keyword' => 'true',
		'dependency' => 'industry:masters,industry:phd',
	),
	

	
	array(
		'name' => 'hours',
		'label' => 'Hours',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Full time'			=> array('slug' => 'fulltime'),	
			'Part time' 		=> array('slug' => 'parttime'),	
			'Variable hours' 	=> array('slug' => 'variablehours')	
		),
		'group' => 'package'
	),
	
	array(
		'name' => 'contract',
		'label' => 'Contract',
		'placeholder' => '',
		'fieldtype' => 'check',
		'value' => array(
			'Permanent' 		=> array('slug' => 'permanent'),	
			'Fixed Term'		=> array('slug' => 'fixedterm'),	
			'Job share' 		=> array('slug' => 'jobshare')
		),
		'group' => 'package'
	),
	
	array(
		'name' => 'industry',
		'label' => 'Industry',
		'placeholder' => '',
		'fieldtype' => 'check',
		'value' => $sector->taxTreeRecursive(),
		'taxonomy' => 'sector',
		'group' => 'industry_location',
		'keyword' => 'true',
		'select_parent' => 'false',
		'required' => 'job_status:published',
		'force' => 'phd@role_func:phd',

	),

	array(
		'name' => 'region',
		'label' => 'Region',
		'placeholder' => '',
		'fieldtype' => 'check',
		'value' => array(
			'London'				=> array('slug' => 'london'),	
			'Midlands of England'	=> array('slug' => 'midlandsengland'),	
			'Northern England'		=> array('slug' => 'northengland'),	
			'Northern Ireland'		=> array('slug' => 'northernireland'),	
			'Republic of Ireland'	=> array('slug' => 'republicireland'),	
			'Scotland'				=> array('slug' => 'scotland'),	
			'South East England'	=> array('slug' => 'southeastengland'),	
			'South West England'	=> array('slug' => 'southwestengland'),	
			'Wales'					=> array('slug' => 'wales'),	
			'North, South & Central America' => array('slug' => 'americas'),	
			'Europe'				=> array('slug' => 'europe'),	
			'Asia & Middle East'	=> array('slug' => 'asia'),	
			'Australasia'			=> array('slug' => 'australasia'),	
			'Africa'				=> array('slug' => 'africa'),	
		),
		'group' => 'industry_location',
		'required' => 'job_status:published'
	),

	array(
		'name' => 'location',
		'label' => 'Location',
		'placeholder' => '',
		'fieldtype' => 'text',
		'group' => 'industry_location',
		'required' => 'job_status:published'
	),



	array(
		'name' => 'full_description_limited',
		'label' => 'Advert summary',
		'placeholder' => '',
		'fieldtype' => 'richtext',
		'group' => 'details',
		'limit' => '600',
		'tags_allowed' => 'p,br,strong,em'
	),
	
	array(
		'name' => 'full_description',
		'label' => 'Advert summary',
		'placeholder' => '',
		'fieldtype' => 'richtext',
		'group' => 'details',
	),

	array(
		'name' => 'doc_upload',
		'label' => 'Document upload',
		'placeholder' => '',
		'fieldtype' => 'file',
		'group' => 'details',
		'extra_class' => 'widelabel'
	),

	array(
		'name' => 'doc_download_label',
		'label' => 'Document download label',
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Job description' 		=> array('slug' => 'jobdescription'),	
			'Research brief'		=> array('slug' => 'researchbrief'),	
			'Further information document' 		=> array('slug' => 'furtherinfo')
		),
		'group' => 'details'
	),



/*
	array(
		'name' => 'extra_info',
		'label' => 'Extra information',
		'instructions' => 'If you\'d like candidates to provide extra information, please detail below what you\'d like them to provide',
		'placeholder' => '',
		'fieldtype' => 'textarea',
		'group' => 'extra'
	),
*/

	array(
		'name' => 'application_method',
		'label' => 'Application method',
		'placeholder' => '',
		'fieldtype' => 'dropdown',
		'value' => array(
			'Send an email'		=> array('slug' =>'email'),	
			'Link to website'	=> array('slug' =>'website'),	
			'Application form'	=> array('slug' =>'form')
		),
		'group' => 'extra'
	),

	array(
		'name' => 'application_website',
		'label' => 'Application website',
		'placeholder' => '',
		'fieldtype' => 'text',
		'dependency' => 'application_method:website',
		'group' => 'extra'
	),


	array(
		'name' => 'application_email',
		'label' => 'Email applications to',
		'placeholder' => '',
		'fieldtype' => 'text',
		'dependency' => 'application_method:email',
		'group' => 'extra'
	),

	array(
		'name' => 'promote',
		'label' => 'Promote for',
		'fieldtype' => 'dropdown',
		'addblank' => true,
		'value' => $sector->taxTreeRecursive(),
		'dependency' => 'ad_type:sponsored',
		'group' => 'admin'
	),
	array(
		'name' => 'promote_from',
		'label' => 'Promote from',
		'fieldtype' => 'date',
		'datedisplay' => 'j M Y',
		'dependency' => 'ad_type:sponsored',
		'group' => 'admin'	
	),
	array(
		'name' => 'promote_to',
		'label' => 'Promote to',
		'fieldtype' => 'date',
		'datedisplay' => 'j M Y',
		'dependency' => 'ad_type:sponsored',
		'group' => 'admin'	
	),
	array(
		'name' => 'promote_enabled',
		'label' => 'Promotion enabled',
		'fieldtype' => 'check',
		'value' => array(
			'Enabled' 		=> array('slug' => 'enabled') // labelled as tick
		),
		'group' => 'sysadmin'
	),
	array(
		'name' => 'ad_banner',
		'label' => 'Ad banner',
		'placeholder' => '',
		'fieldtype' => 'file',
		'group' => 'admin',
		'extra_class' => 'widelabel'
	),

	array(
		'name' => 'group_id',
		'fieldtype' => 'hidden',
		'group' => 'admin'
	),
	array(
		'name' => 'search_count',
		'label' => 'Search count',
		'fieldtype' => 'hidden',
		'group' => 'admin'
	)


);