<?
/*
	Architect Basic Site Configuration Version 1.1
	
	Development by: Joshua Moor
	Last Modified: 03/27/13
	
	This configuration file is to provide 2 things
	1) Barebones system variables allowing initial load.
	2) Any configuration options that a user cannot provide in the back end like additional plugins.
	
	Contents
		Change Log
		Variables
			Site Variables
				ARCH_BACK_END_PATH - Path to the Architect's back end. 
				ARCH_FRONT_END_PATH - Path to the Architect's front end.
				ARCH_INSTALL_PATH - Path to the Architect's front end intstall 
				expanded_modules - an array of expanded modules that have been added to the base system

	Change Log:
	03/27/13 | V 1.1 | expanded_modules added
	02/27/13 | V 1.0 | ARCH_INSTALL_PATH, ARCH_FRONT_END_PATH, ARCH_BACK_END_PATH added
*/

//----------------------------------------------- Variables -----------------------------------------------//

//---------------------------- Site Variables ----------------------------//
define("ARCH_BACK_END_PATH", "/var/www/vhosts/melindamotivates.com/httpdocs/architect/"); //This absolute path to the directory that Architect's system files are in.
define("ARCH_FRONT_END_PATH", "/var/www/vhosts/melindamotivates.com/httpdocs/"); //This absolute path to the directory that Architect's public files are in.
define("ARCH_INSTALL_PATH", "/"); //This is the public directory that Architect was installed to.

$expansion_modules = array('blog');
?>