<?php
	
/*-----------------------------------------------
	
Application name and email (used in notify function et al)

-------------------------------------------------*/

define('APPLICATION', 'Allivion');
define('APPLICATION_EMAIL', 'noreply@allivion.com');
define('NOTIFY_EMAIL', 'notifications@allivion.com');
define('SYSADMIN_EMAIL', 'david@dlrobins.co.uk');


/*-----------------------------------------------
	
define urls for pages using specific templates

-------------------------------------------------*/
	
define('DIRECTORY_LOGINPATH', '/');
define('DIRECTORY_SYSADMIN', '/sysadmin-dashboard');
define('DIRECTORY_RECADMIN', '/recruiter-dashboard');
define('DIRECTORY_ADVADMIN', '/advertiser-dashboard');
define('DIRECTORY_CANDADMIN', '/candidate-dashboard');
define('DIRECTORY_CREATEUSERPATH', '/user/create');
define('DIRECTORY_UPDATEUSERPATH', '/user/update');
define('POSTTITLEFIELD', 'job_title');


/*-----------------------------------------------
	
Encryption keys

-------------------------------------------------*/

if(function_exists("mcrypt_encrypt")) {
define('IV_SIZE', mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
}
define('CRYPTKEY','NFOLOOHWZ0DOAL1VH4K6W7CG');


/*-----------------------------------------------
	
Default file upload path root

-------------------------------------------------*/
	
define('DIRECTORY_UPLOADPATH', '/wp-content/uploads/files/');


