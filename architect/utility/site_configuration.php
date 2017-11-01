<?
/*
	Architect Site Configuration Version 1.5
	
	Development by: Joshua Moor
	Last Modified: 08/22/13
	
	This configuration file provides the bulk of the Architect's configuration
	
	Contents
		Change Log
		Variables
			Architect Variables
				ARCH_DOCUMENT_PATH_ID - Boolean to control the presence of document ids in the site path
				ARCH_HANDLER_GROUP - Handler name for the group section
				ARCH_HANDLER_MANAGE - Handler name for the management section
				ARCH_HANDLER_PAGE - Handler name for the page section
				ARCH_HANDLER_USER - Handler name for the user section
				ARCH_LOGIN_ATTEMPTS_ALLOWED - Number of bad attempts to allow before locking login form
				ARCH_LOGIN_LOCKOUT_TIME - Number of minutes to lock out the login form after the login form
				ARCH_SYSTEM_THEME_PATH - Absolute path to system theme in the themes folder
			Database Variables
				ARCH_DB_LOCATION - Location of the Architect's database.
				ARCH_DB_NAME - Architect's database name.
				ARCH_DB_PASSWORD - Password used to access Architect's database.
				ARCH_DB_USERNAME - Username used to access Architect's database.
			Email Variables
				EMAIL_NO_REPLY - Email address to use when sending emails
			FTP Variables
				FTP_LOCATION - Location of the FTP host.
				FTP_ROOT - root folder of the ftp server.
				FTP_PASSWORD - Password used for FTP.
				FTP_USERNAME - Username used for FTP.
				
	Change Log:
	08/22/13 | V 1.5 | ARCH_DOCUMENT_PATH_ID, ARCH_SYSTEM_THEME_PATH added
	07/02/13 | V 1.4 | ARCH_LOGIN_ATTEMPTS_ALLOWED, ARCH_LOGIN_LOCKOUT_TIME added
	05/12/13 | V 1.3 | ARCH_HANDLER_GROUP, ARCH_HANDLER_MANAGE, ARCH_HANDLER_PAGE, ARCH_HANDLER_USER added
	03/15/13 | V 1.2 | EMAIL_NO_REPLY added
	03/06/13 | V 1.1 | FTP_ROOT, FTP_LOCATION, FTP_USERNAME, FTP_PASSWORD added
	02/27/13 | V 1.0 | ARCH_DB_LOCATION, ARCH_DB_NAME, ARCH_DB_PASSWORD, ARCH_DB_USERNAME added
*/

//----------------------------------------------- Variables -----------------------------------------------//

//---------------------------- Architect Variables ----------------------------//
define("ARCH_DOCUMENT_PATH_ID", true);
define("ARCH_HANDLER_GROUP", "group");
define("ARCH_HANDLER_MANAGE", "manage");
define("ARCH_HANDLER_PAGE", "page");
define("ARCH_HANDLER_USER", "user");
define("ARCH_LOGIN_ATTEMPTS_ALLOWED", 3);
define("ARCH_LOGIN_LOCKOUT_TIME", 5);
define("ARCH_SYSTEM_THEME_PATH", "/cool_blue/styles/cool_blue.css");

//---------------------------- Database Variables ----------------------------//
define("ARCH_DB_LOCATION", "localhost");
define("ARCH_DB_NAME", "");
define("ARCH_DB_PASSWORD", "");
define("ARCH_DB_USERNAME", "");

//---------------------------- Email Variables ----------------------------//
define("EMAIL_NO_REPLY", "noreply@site.com");

//---------------------------- FTP Variables ----------------------------//
define("FTP_LOCATION", "localhost");
define("FTP_ROOT", "");
define("FTP_PASSWORD", "");
define("FTP_USERNAME", "");
?>