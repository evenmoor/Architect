<?
/*
	Architect System Functions Version 1.2.2
	
	Development by: Joshua Moor
	Last Modified: 08/29/14
	
	This file provides general functions for the site
	
	Contents
		Change Log
		Functions
			Database Functions
				clean - Sanitizes a string for database insertion.
			Email Functions
				validateEmailAddress - Validates an email address
			Miscellaneous Functions
				generateRandomString - Generates a random string
				getCurrentLanguage - Returns current site language
				setCurrentLanguage - Allows site language to be changed
				validateText - Validates a string or collections of strings against a regex string
				xmlElementSet - Returns the contents of an element in an XML string
			Password Functions
				evaluatePassword - Evaluates the complexity of a password
			System Functions
				getModuleHandler - Get the handler for a custom module
				systemLog - Makes an entry in the logs
			User Functions
				isLoggedIn - Checks to see whether or not someone is logged in
				validatePermissions - Determines whether or not a user has access to a given screen or section

	Change Log:
	09/08/14 | V 1.3 | validateText added
	08/28/14 | V 1.2.2 | validatePermissions updated to address private documents and tags
	08/27/13 | V 1.2.1 | validatePermissions updated
	04/12/13 | V 1.2 | xmlElementSet added
	03/27/13 | V 1.1 | getModuleHandler added
	02/27/13 | V 1.0 | clean, validateEmailAddress, generateRandomString, getCurrentLanguage, setCurrentLanguage, evaluatePassword, systemLog, isLoggedIn, validatePermissions added
*/

//----------------------------------------------- Functions -----------------------------------------------//

//---------------------------- Database Functions ----------------------------//
//-------------- Clean --------------//
//sanitizes a string for database entry
//accepts a string
//returns a sanitized string
function clean($string_to_clean){
	$clean_string = mysql_real_escape_string($string_to_clean);
	return $clean_string;
}

//---------------------------- Email Functions ----------------------------//
//-------------- Validate Email Address --------------//
//validates an email address. 
//Based on Dogulas Lovell's email validation code from Linux Journal Issue 158 June 2007
//accepts a string
//returns a boolean indicating whether or not the email address is valid
function validateEmailAddress($email_address){
	$is_valid = true;
	$at_index = strrpos($email_address, "@");
	if(is_bool($at_index) && !$at_index){
		$is_valid = false;
	}else{
		$domain = substr($email_address, $at_index+1); //domain is after the @ symbol
		$domain_length = strlen($domain);
		$local = substr($email_address, 0, $at_index); //local is the portion in front of the @ symbol
		$local_length = strlen($local);
		if($local_length < 1 || $local_length > 64){ //valid locals can only be bewteen 1 an 64 characters long as per RFC 2821 4.5.3.1
			$is_valid = false;
		}else if($domain_length < 1 || $domain_length > 255){ // valid domains can only be between 1 and 255 characters long as per RFC 2821 4.5.3.1
			$is_valid = false;
		}else if($local[0] == '.' || $local[$local_length-1] == '.'){ //valid locals cannot begin or end with a . as per RFC 2822 3.2.4
			$is_valid = false;
		}else if(preg_match('/\\.\\./', $local)){ //valid locals cannot have two consecutive dots as per RFC 2822 3.2.4
			$is_valid = false;
		}else if(!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){//check domain for disallowed characters
			$is_valid = false;
		}else if(preg_match('/\\.\\./', $domain)){//check domain for consecutive dots
			$is_valid = false;
		}else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))){ //remove even back slashes from local portion and then check for disallowed characters
			 if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))){//remove even back slashes from local portion and check to make sure special characters are escaped properly
				$is_valid = false;
			 }
		}
		
		//if($is_valid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){ //check dns for domains
			//$is_valid = false;
		//}
	}	
	return $is_valid;
}

//---------------------------- Miscellaneous Functions ----------------------------//
//-------------- Generate Random String --------------//
//generates a random string
//requires: accepts an integer determining the length of salt to be generated 
//optional: a boolean indicating whether or not special characters are allowed, and a boolean indicating whether or not to use uniqid on the final string
//returns: a salt string
function generateRandomString($string_length, $special_characters = true, $force_unique = false){
	//characters strings the salt will be generated from. Add or remove characters from the string characters strings to modify valid chatacters.
	if($special_characters){//if the string can have special charaters
		$string_characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()=+-_/?:;<>'; 
	}else{//no special characters
		$string_characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	}
	
	$salt_string = '';
	
	for($string_counter = 0; $string_counter < intval($string_length); $string_counter++){//loop adding one character to random string on each iteraction
		$salt_string .= substr($string_characters, rand(0, strlen($string_characters)), 1); //add new character to string
	}
	
	if($force_unique){
		return uniqid($salt_string); //return final string with a unique id
	}else{
		return $salt_string; //return final string
	}
}

//-------------- Get Current Language --------------//
//returns the current site language, if no language is currently set or sessions are disabled it will return a default language.
//requires: nothing
//returns: a string containing current language
function getCurrentLanguage(){
	$default_language = 'en';
	
	if(isset($_SESSION['language'])){
		return $_SESSION['language'];
	}else{
		return $default_language;
	}
}

//-------------- Set Current Language --------------//
//sets the current site language
//requires: an active session and a string determining language to use
//returns: nothing
function setCurrentLanguage($language){
	$_SESSION['language'] = $language;
}

//-------------- Validate Text --------------//
//validates a string of text, or array set of strings based upon a given regular expression
//requires: accepts a string or array of strings to be validated and a regular expression to validate them against
//optional: a boolean indicating whether or not the field can be blank, this defaults to false
//returns: a boolean indicating whether the tested string(s) passed the regex comparison or not, for arrays a single failure will fail the whole set
// Development by Matt Manis
function validateText($test, $regex, $allow_blank = false){
	$result = false;
	if(is_array($test)){
		foreach($test as $value){
			if(preg_match($regex, $value) || ($allow_blank == true && trim($value) == "")){ $result = true; }
			else{ $result = false; break; }
		}
	}
	else{
		if(preg_match($regex, $test) || ($allow_blank == true && trim($test) == "")){ $result = true; }
	}
	return $result;
}

//-------------- XML Element Set --------------//
//Returns the contents of a tag set in an XML string
//based on (reference needed)
//requires: two strings, elementName (the tag contents are being extracted from), xml (the xml data to extract the tag from)
//optional: boolean, contentOnly a boolean indicating if the XML structure should be preserved
//returns the contents of elementName
function xmlElementSet($elementName, $xml, $contentOnly = false) {
	if ($xml == false) {
		return false;
	}
	$found = preg_match_all('#<'.$elementName.'(?:\s+[^>]+)?>' .
			'(.*?)</'.$elementName.'>#s',
			$xml, $matches, PREG_PATTERN_ORDER);
	if ($found != false) {
		if ($contentOnly) {
			return $matches[1];  //ignore the enlosing tags
		} else {
			return $matches[0];  //return the full pattern match
		}
	}
	// No match found: return false.
	return false;
}

//---------------------------- Password Functions ----------------------------//
//-------------- Evaluate Password --------------//
//evaluates a proposed password, based on length, upper and lower case letters, numbers, special characters, dictionary words, and previous passwords used.
//acceptable minimums can be set in the function
//accepts a string to evaluate
//returns an integer which corresponds to the following values:
//0 = acceptable
//1 = too short
//2 = not enough upper casse characters
//3 = not enough lower case characters
//4 = not enough numbers
//5 = not enough special characters
//6 = too similar to dictionary word
//7 = too similar to previous password

//Notes:
//Dictionary and previous password comparision not currently implemented
//booleans controlling enforcement added, but not implemented
function evaluatePassword($password_to_evaluate, $enforce_length = true, $enforce_case_count = true, $enforce_number_count = true, $enforce_special_character_count = true){
	$return_value = 0;
	$minimum_length = 8;
	$minimum_lower_case_characters = 1;
	$minimum_upper_case_characters = 0;
	$minimum_numbers = 1;
	$minimum_special_characters = 0;
	
	if(strlen($password_to_evaluate) >= $minimum_length){//check length
		$matches; //array to hold regular expression matches
		preg_match_all('/[a-z]/', $password_to_evaluate, $matches); //check for lowercase characters
		if(count($matches[0]) >= $minimum_lower_case_characters){
			preg_match_all('/[A-Z]/', $password_to_evaluate, $matches); //check for uppercase characters
			if(count($matches[0]) >= $minimum_upper_case_characters){
				preg_match_all('/[0-9]/', $password_to_evaluate, $matches); //check for numbers
				if(count($matches[0]) >= $minimum_numbers){
					preg_match_all('/[^a-zA-Z0-9]/', $password_to_evaluate, $matches); //check for special characters
					if(count($matches[0]) >= $minimum_special_characters){
						return $return_value;
					}else{
						$return_value = 5;
						return $return_value;
					}
				}else{
					$return_value = 4;
					return $return_value;
				}
			}else{
				$return_value = 2;
				return $return_value;
			}
		}else{
			$return_value = 3;
			return $return_value;
		}	
	}else{
		$return_value = 1;
		return $return_value;
	}
}

//---------------------------- System Functions ----------------------------//
//-------------- Get Module Handler --------------//
//Checks to see if a module is loaded in the system
//Accepts an array of enabled modules, a string which is compared to a list of enabled modules
//Returns a string indicating whether or not the module is enabled, it returns NONE if no handler is enabled
function getModuleHandler($module_list, $module_to_check){
	$return_value = "NONE";
	
	foreach($module_list as $module){
		include(constant("ARCH_BACK_END_PATH").'modules/'.$module.'/module_config.php');//include default config for module
		if($module_handler == $module_to_check){//compare handler
			$return_value = $module_handler;//ser return value
			break;
		}
	}
	
	return ($return_value);
}

//-------------- System Log --------------//
//Logs an action in the system log and on php's error log. This allows system logging in two different places. Writing to the php error log allows viewing and log management through plesk.
//Accepts a string log_entry which is the message that will be logged
//Returns a boolean indicating whether or not the message was logged
//Requires an active database connection
function systemLog($log_entry){
	$user_type = 'user';
	$user = clean($_SESSION['user_ID']);
	$now = clean(date('Y-m-d H:i:s'));
	$log = clean($log_entry);
	
	if(mysql_query('INSERT INTO tbl_system_log(system_log_entry_id, 
												system_log_entry, 
												system_log_timestamp) 
				   							VALUES(NULL,
												   "'.$user_type.': '.$user.' -- '.$log.'",
												   "'.$now.'")')){//write to database
		error_log('+++ Architect System : '.$user_type.' userID - '.$user.' | '.$now.' |  '.$log.' +++', 0);//write to php log
		return true;
	}else{
		return false;
	}
}

//---------------------------- User Functions ----------------------------//
//-------------- Is Logged In --------------//
//tests to see if a user is logged in
//accepts nothing
//returns a boolean indicating whether or not the user is logged in
function isLoggedIn(){
	if(isset($_SESSION['user_ID']) && $_SESSION['user_ID'] != 0){	
		return true;
	}else{
		return false;
	}
}


//-------------- Validate Permissions --------------//
//validates a user's permission to use the given screen
//accepts a string defining permission type valid values["system"], and an entity id number
//returns a boolean indicating whether not the permissions were validated. If they failed to validate, a log entry is created.
//requires an open database connection

//example function call validatePermissions("system", 11);

//Note currently system is the only valid permission type, but I have it as a variable so we can expand it to include pages and templates at a later date
function validatePermissions($permission_type, $entity_id){
	//grab status from database
	$user_status = mysql_fetch_assoc(mysql_query('SELECT user_user_status_FK
													FROM tbl_users
													WHERE user_ID="'.clean($_SESSION['user_ID']).'"
													LIMIT 1'));
													
	//if user has been banned stop now
	if($user_status['user_user_status_FK'] == 3){
		systemLog(''.$_SERVER['REMOTE_ADDR'].' banned user attempted access to a secure page.');
		return false;
	}else{
		//grab group type from the database
		$user_group = mysql_fetch_assoc(mysql_query('SELECT user_user_group_FK
														FROM tbl_users
														WHERE user_ID="'.clean($_SESSION['user_ID']).'"
														LIMIT 1'));
		//if the user is an admin grant them access and stop now
		if($user_group['user_user_group_FK'] == 1){
			return true;
		}else{//otherwise check for specific permission on the entity
			switch($permission_type){
				//system permissions
				case 'system':
					$permission_check = mysql_query('SELECT site_permission_ID
														FROM tbl_site_permissions
														WHERE site_permission_type_FK = "1"
															AND site_permission_entity_FK = "'.clean($entity_id).'"
															AND site_permission_value = "'.clean($user_group['user_user_group_FK']).'"
														OR site_permission_type_FK = "2"
															AND site_permission_entity_FK = "'.clean($entity_id).'"
															AND site_permission_value = "'.clean($_SESSION['user_ID']).'"
														LIMIT 1');

					if(mysql_num_rows($permission_check) > 0){//if valid permissions have been found
						return true;
					}else{//no permissions
						systemLog(''.$_SERVER['REMOTE_ADDR'].'  attempted access to a page without proper permission.');
						return false;
					}
				break;
				
				//permissions for specific documents or sections of a document
				case 'document':
					//return value
					$return_value = false;
					
					//get user group
					$user_group = mysql_query('SELECT tbl_user_groups.user_group_name
														FROM tbl_users
															INNER JOIN tbl_user_groups ON tbl_users.user_user_group_FK = tbl_user_groups.user_group_ID
														WHERE user_ID="'.clean($_SESSION['user_ID']).'"
														LIMIT 1');
					$user_group = mysql_fetch_assoc($user_group);
					$user_group = $user_group['user_group_name'];
					
					$allowed_groups = explode(":", $entity_id);
					
					//parse allowed groups
					foreach($allowed_groups as $group){
						if($group === $user_group){//if the allowed group is the same as the user's group
							$return_value = true;
							break;
						}
					}
					
					//condition to handle "everyone" mode
					if(trim($entity_id) == ""){
						$return_value = true;
					}
					
					return $return_value;
				break;
				
				//handles unhandled permission types
				default:
					systemLog(''.$_SERVER['REMOTE_ADDR'].'  user attempted access to a secure page with an unhandled permission type.');
					return false;
				break;
			}
		}
	}
}

?>