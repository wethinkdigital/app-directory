<?php

///////////////////////////////////////////
//
// Define taxonomy type
// no spaces - letters, hyphen or underscore only
//
///////////////////////////////////////////
	
$type = 'sector'; 


///////////////////////////////////////////
//
// Define taxonomy labelling (WordPress admin)
//
///////////////////////////////////////////

$label = 'sectors';
$single_label ='sector';


///////////////////////////////////////////
//
// Which item types will this taxonomy apply to
//
///////////////////////////////////////////

$items = array('job');


///////////////////////////////////////////
//
// Define terms to be registered when taxonomy is created
//
///////////////////////////////////////////

$terms = array(
			'Academic' => array(
				'Agriculture, Food and Veterinary' => 'agri-food-vet',
				'Architecture, Building and Planning' => 'architecture-building-planning',
				'Biological Sciences' => 'bioscience',
				'Business and Management Studies' => 'business-management',
				'Computer Science' => 'computer-science',
				'Creative Arts and Design' => 'creative-art-design',
				'Economics' => 'economics',
				'Education Studies (inc. TEFL)' => 'education-studies',
				'Engineering and Technology' => 'engineering-tech',
				'Health and Medical' => 'health-medical',
				'Historical and Philosophical Studies' => 'history-philo',
				'Information Management and Librarianship' => 'library-IM',
				'Languages, Literature and Culture' => 'lang-lit-culture',
				'Law' => 'law',
				'Mathematics and Statistics' => 'maths-stat',
				'Media and Communications' => 'media-comms',
				'Physical and Environmental Sciences' => 'phys-env-science',
				'Politics and Government' => 'politics',
				'Psychology' => 'psychology',
				'Social Sciences and Social Care' => 'social-care',
				'Sport and Leisure' => 'sport-leisure',
			),
			'Professional' => array(
				'Administrative' => 'admin',
				'Finance' => 'finance',
				'Fundraising and Alumni' => 'fundraising',
				'Hospitality, Retail, Conferences and Events' => 'hospitality-events',
				'Human Resources' => 'hr',
				'International Activities' => 'international',
				'IT' => 'it',
				'Library Services and Information Management' => 'libraryserv-info',
				'PR, Marketing, Sales and Communication' => 'pr-comms',
				'Property and Maintenance' => 'prop-maintenance',
				'Senior Management' => 'snr-management',
				'Sport and Leisure' => 'sport-leisure',
				'Student Services' => 'student-services',
				'Other' => 'other'
			),
			'Studentships' => array(
				'Masters' => 'masters',
				'PhD' => 'phd'
			)
		);


