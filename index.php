<?
/*
	Architect Version 1.6.1
	
	This page processess all Architect page requests. It should NOT be modified unless you know what you are doing.
	
	Development by: Joshua Moor
	Last Modified: 09/02/14

	Change Log:
	09/02/14 | 1.6.1 | document privacy supported added to document view
	08/27/14 | 1.6 | sitemap.xml rendering support added
	08/26/14 | 1.5 | robots.txt rendering support added
	08/26/13 | 1.4 | edit_mode variable listener added for responsive content editor
	08/22/13 | 1.3 | id-less page link support added
	-------- | 1.2 | support for plugins added
	03/26/13 | 1.1 | named session added
	03/14/13 | 1.0 | login enforcement for manager added
	03/13/13 | 1.0 | home page added.
	03/09/13 | 1.0 | default timezone setting added.
	03/08/13 | 1.0 | site settings added.
*/
	session_name("CMS-A");

	//start a session
	session_start();

	//load basic site configuration
	require('utility/basic_site_configuration.php');
	
	//load site functions
	require(constant("ARCH_BACK_END_PATH").'utility/system_functions.php');
	//load advanced configuration
	require(constant("ARCH_BACK_END_PATH").'utility/site_configuration.php');
	//open database connection
	require(constant("ARCH_BACK_END_PATH").'utility/database_connect.php');
	
	//settings to be used on subsequent pages
	$site_settings = mysql_fetch_assoc(mysql_query('SELECT site_name,
															site_version,
															site_status,
															site_development_alternate_path,
															site_development_override,
															site_languages,
															site_timezone
														FROM tbl_site_settings
														WHERE site_ID = "1"
														LIMIT 1'));
	
	$site_settings = array('name' => $site_settings['site_name'],
							'version' => $site_settings['site_version'],
							'status' => $site_settings['site_status'],
							'development_alternate_path' => $site_settings['site_development_alternate_path'],
							'development_override' => $site_settings['site_development_override'],
							'languages' => $site_settings['site_languages'],
							'timezone' => $site_settings['site_timezone']);
	
	//build initial development_redirection_override
	if(!isset($_SESSION['development_redirect_override'])){
		$_SESSION['development_redirect_override'] = false;
	}
	
	//check for passed development redirection override
	if(isset($_GET['override']) && $_GET['override'] == $site_settings['development_override']){
		$_SESSION['development_redirect_override'] = true;
	}
	
	//set timezone
	date_default_timezone_set($site_settings['timezone']);
	
	//pathing variable used to determine which handler to load.
	$page_path = $_SERVER['REQUEST_URI'];
	
	//strip out variables
	$page_variables = explode("?", $page_path);
	
	if(count($page_variables) > 1){
		$page_path = $page_variables[0];
		$page_variables = $page_variables[1];
	}else{
		$page_path = $page_variables[0];
		$page_variables = '';//empty unneeded variables
	}
	
	//clean up pathing variable by removing CMS Install Location leaving just the relavent information
	//$page_path = str_replace(constant("ARCH_INSTALL_PATH"), "", $page_path); <-- replaced due to root installation errors
	$page_path =  preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $page_path, 1);
	
	//clean up path string by removing url encoding
	$page_path = urldecode($page_path);
	//create page path array
	$page_path = explode("/", $page_path);
	
	switch($page_path[0]){//choose handler
		//display specific pages
		
		//robots.txt
		case "robots.txt":
			//collect information for robots.txt file
			$robots = mysql_fetch_assoc(mysql_query('SELECT site_robots
														FROM tbl_site_settings
														WHERE site_ID = "1"
														LIMIT 1'));
			//force text file header
			header("Content-Type:text/plain");
			//output file
			echo $robots['site_robots'];
		break;//end robots
		
		//sitemap.xml
		case "sitemap.xml":
			$sitemap = mysql_fetch_assoc(mysql_query('SELECT site_map
														FROM tbl_site_settings
														WHERE site_ID = "1"
														LIMIT 1'));
			print $sitemap['site_map'];
		break;//end sitemap
		
		//display user screens
		case constant("ARCH_HANDLER_USER"):
			//adjusted path for manager screen
			$adjusted_path = '';
			
			//parse page_path to grab all elements of the manager path
			for($path_counter = 1; $path_counter < count($page_path); $path_counter++){
				if($path_counter > 1 && $page_path[$path_counter]!= ''){//add slash for any directories
					$adjusted_path .= '/';
				}
				$adjusted_path .= $page_path[$path_counter];
			}
			
			//catch empty path and redirect to landing page
			if($adjusted_path == ''){
				$adjusted_path = 'login';
			}
			
			//apply file extention to final element
			$adjusted_path .= '.php';
			
			//for security purposes limit it to the user path
			
			//append back end file path and directory name
			$adjusted_path = constant("ARCH_BACK_END_PATH").'users/'.$adjusted_path;
			
			//load page
			//echo '<p>Adjusted Path: '.$adjusted_path.'</p>';
			include($adjusted_path);
		break;
	
		//display a group of related documents
		case constant("ARCH_HANDLER_GROUP"):
			if($site_settings['status'] == "ONLINE" || $site_settings['status'] == "DEVELOPMENT" && $_SESSION['development_redirect_override']){//make sure the site is online or in development mode with a valid override
				$display_type = "GROUP";
				
				//name of the document group to display
				$group_name = $page_path[1];
				
				//check to see if we are displaying one specific item in the group
				if(count($page_path) > 2 && $page_path[2] != ''){
					$group_item = $page_path[2];
					$display_type = "SINGLE";
				}
				
				//handle display types
				switch($display_type){
					//groups are parsed differently than normal pages
					case "GROUP":
						$group_details = mysql_fetch_assoc(mysql_query('SELECT document_group_template_FK
																			FROM tbl_document_groups  
																			WHERE document_group_name="'.clean($group_name).'"
																			LIMIT 1'));
						
						//grab template data
						$template = mysql_fetch_assoc(mysql_query('SELECT template_location
																	FROM tbl_templates 
																	WHERE template_ID="'.clean($group_details['document_group_template_FK']).'"
																	LIMIT 1'));
						//distill path location from object
						$template = $template['template_location'];
						
						//clean out install path
						//$template = str_replace(constant("ARCH_INSTALL_PATH"), "", $template);
						$template = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $template, 1);
						
						//load template into parser and then render and display template
						//new document_group_parser($template, $group_name, true);
						//load parser
						require_once(constant("ARCH_BACK_END_PATH").'modules/tag_parser.php');
						require_once(constant("ARCH_BACK_END_PATH").'modules/document_group/document_group_parser.php');
						new documentGroupParser($template, false, $group_name, true, true);
					break;//end group
					
					//single elements are handled as normal pages, the only difference is that the template comes from the group rather than the document.
					case "SINGLE":
						if(constant("ARCH_DOCUMENT_PATH_ID")){
							//default structure denotes a - seperating a page's id number from its name
							$page_details = explode('-', $group_item);
	
							//grab document data
							$document = mysql_fetch_assoc(mysql_query('SELECT *
																		FROM tbl_documents
																		WHERE document_ID="'.clean($page_details[0]).'"
																		LIMIT 1'));
						}else{
							//grab document data
							$document = mysql_fetch_assoc(mysql_query('SELECT *
																		FROM tbl_documents
																		WHERE document_name="'.clean($group_item).'"
																		LIMIT 1'));
						}
						
						//allow document specific template to overwrite group template
						$template_ID = $document['document_template_FK'];
						
						//if no document specific template has been specified load the group single item template
						if($template_ID == '' || $template_ID == 0){
							$group_template = mysql_fetch_assoc(mysql_query('SELECT document_group_single_item_template_FK
																			FROM tbl_document_groups  
																			WHERE document_group_ID="'.clean($document['document_group_FK']).'"
																			LIMIT 1'));
							$template_ID = $group_template['document_group_single_item_template_FK'];
						}
						
						//grab template data
						$template = mysql_fetch_assoc(mysql_query('SELECT template_location
																	FROM tbl_templates 
																	WHERE template_ID="'.clean($template_ID).'"
																	LIMIT 1'));
						//distill path location from object
						$template = $template['template_location'];
						
						
						//clean out install path
						$template = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $template, 1);
						
						//edit mode variable
						$edit_mode = false;
						if(isset($_GET['edit_mode']) && $_GET['edit_mode'] == 'true'){
							$edit_mode = true;
						}
						
						//load parser
						require_once(constant("ARCH_BACK_END_PATH").'modules/tag_parser.php');
						require_once(constant("ARCH_BACK_END_PATH").'modules/documents/document_parser.php');
						new documentParser($template, false, $document, true, true, $edit_mode);
					break;//end single
				}
			}else{//site status is not online
				display_non_online_pages($site_settings);
			}
		break;
	
		//display a static page based on it's id
		case constant("ARCH_HANDLER_PAGE"):
			if($site_settings['status'] == "ONLINE" || $site_settings['status'] == "DEVELOPMENT" && $_SESSION['development_redirect_override']){//make sure the site is online or in development mode with a valid override
				//distill the details about the page from the url path
				$page_details = $page_path[1];
				//default structure denotes a - seperating a page's id number from its name
				$page_details = explode('-', $page_details);
				
				if(constant("ARCH_DOCUMENT_PATH_ID")){//check to see if ids are in page paths
					//grab document data
					$document = mysql_fetch_assoc(mysql_query('SELECT *
															FROM tbl_documents
															WHERE document_ID="'.clean($page_details[0]).'"
															LIMIT 1'));
				}else{
					//grab document data
					$document = mysql_fetch_assoc(mysql_query('SELECT *
															FROM tbl_documents
															WHERE document_name="'.clean($page_path[1]).'"
															LIMIT 1'));
				}
				
				if($document['document_status_FK'] == 2 && validatePermissions('document', $document['document_privacy_list']) || isLoggedIn() && validatePermissions('system', 7)){//make sure the document is available..
					//grab template data
					$template = mysql_fetch_assoc(mysql_query('SELECT template_location
																FROM tbl_templates 
																WHERE template_ID="'.clean($document['document_template_FK']).'"
																LIMIT 1'));
					//distill path location from object
					$template = $template['template_location'];
					//clean out install path
					//$template = str_replace(constant("ARCH_INSTALL_PATH"), "", $template);
					$template = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $template, 1);
					
					//edit mode variable
					$edit_mode = false;
					if(isset($_GET['edit_mode']) && $_GET['edit_mode'] == 'true'){
						$edit_mode = true;
					}
					
					//load parser
					//require(constant("ARCH_BACK_END_PATH").'modules/template/template_parser.php');
					//load template into parser and then render and display template
					//new template_parser($template, $document, true, $edit_mode);
					require_once(constant("ARCH_BACK_END_PATH").'modules/tag_parser.php');
					require_once(constant("ARCH_BACK_END_PATH").'modules/documents/document_parser.php');
					new documentParser($template, false, $document, true, true, $edit_mode);
				}elseif($document['document_status_FK'] == 2 && !validatePermissions('document', $document['document_privacy_list'])){
					require(constant("ARCH_BACK_END_PATH").'status_pages/invalid_permissions.php');
				}elseif($document['document_status_FK'] != 2){//document is not online
					include(constant("ARCH_BACK_END_PATH").'status_pages/unavailable_page.php');
				}
			}else{//site status is not online
				display_non_online_pages($site_settings);
			}
		break;
		
		//handle management pages
		case constant("ARCH_HANDLER_MANAGE"):
			if(isLoggedIn()){//check to see if the user is logged in
				//adjusted path for manager screen
				$adjusted_path = '';
				
				if($page_path[1] == 'expanded' && in_array($page_path[2], $expansion_modules)){//check expanded modules for requested module 
					for($path_counter = 2; $path_counter < count($page_path); $path_counter++){
						if($path_counter > 1 && $page_path[$path_counter]!= ''){//add slash for any directories
							$adjusted_path .= '/';
						}
						
						$adjusted_path .= $page_path[$path_counter];
					}
					
					//apply file extention to final element
					$adjusted_path .= '.php';
					
					//append back end file path and directory name
					$adjusted_path = constant("ARCH_BACK_END_PATH").'modules/'.$adjusted_path;
				}else{
					//parse page_path to grab all elements of the manager path
					for($path_counter = 1; $path_counter < count($page_path); $path_counter++){
						if($path_counter > 1 && $page_path[$path_counter]!= ''){//add slash for any directories
							$adjusted_path .= '/';
						}
						$adjusted_path .= $page_path[$path_counter];
					}
					
					//catch empty path and redirect to landing page
					if($adjusted_path == ''){
						$adjusted_path = 'index';
					}
					
					//apply file extention to final element
					$adjusted_path .= '.php';
					
					//for security purposes limit include to the manager path
					
					//append back end file path and directory name
					$adjusted_path = constant("ARCH_BACK_END_PATH").'manager/'.$adjusted_path;
				}
				
				//load page
				include($adjusted_path);
			}else{//no user logged in
				header('Location: '.constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_USER").'/login/?rpath='.$_SERVER['REQUEST_URI']);
			}
		break;
		
		//missing, expanded, invalid, unsupported, or poorly formed handler
		default:
			//check for an expanded handler
			$expended_handler = getModuleHandler($expansion_modules, $page_path[0]);
			if($expended_handler != 'NONE'){//check for expanded handler
				$module_path =  constant("ARCH_BACK_END_PATH").'/modules/'.$expended_handler.'/index.php';
				include($module_path);
			}else{//not an expanded handler
				if($site_settings['status'] == "ONLINE" || $site_settings['status'] == "DEVELOPMENT" && $_SESSION['development_redirect_override']){//make sure the site is online or in development mode with a valid override
					$home_page = mysql_query('SELECT document_ID
												FROM tbl_documents
												WHERE document_is_home_page = "1"
												LIMIT 1');
					
					if(mysql_num_rows($home_page) > 0){
						$home_page = mysql_fetch_assoc($home_page);
						//grab document data
						$document = mysql_fetch_assoc(mysql_query('SELECT *
																	FROM tbl_documents
																	WHERE document_ID="'.clean($home_page['document_ID']).'"
																	LIMIT 1'));
						
						//grab template data
						$template = mysql_fetch_assoc(mysql_query('SELECT template_location
																	FROM tbl_templates 
																	WHERE template_ID="'.clean($document['document_template_FK']).'"
																	LIMIT 1'));
						//distill path location from object
						$template = $template['template_location'];
						//clean out install path
						//$template = str_replace(constant("ARCH_INSTALL_PATH"), "", $template);
						$template = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $template, 1);
						
						//load parser
						require_once(constant("ARCH_BACK_END_PATH").'modules/tag_parser.php');
						require_once(constant("ARCH_BACK_END_PATH").'modules/documents/document_parser.php');
						new documentParser($template, false, $document, true, true, false);
					}else{
						?><h1>Sorry, there is nothing to display...</h1><?
					}
				}else{//site status is not online
					display_non_online_pages($site_settings);
				}
			}
		break;
	}
	
	//close database connection
	require(constant("ARCH_BACK_END_PATH").'utility/database_disconnect.php');
	
	function display_non_online_pages($site_settings){
		switch($site_settings['status']){
			case 'DEVELOPMENT':
				if(trim($site_settings['development_alternate_path']) != ''){//make sure the redirect path isn't blank
					header('Location: '.$site_settings['development_alternate_path']);
				}else{//redirect to offline status page
					include(constant("ARCH_BACK_END_PATH").'status_pages/offline.php');
				}
			break;
			case 'OFFLINE':
				include(constant("ARCH_BACK_END_PATH").'status_pages/offline.php');
			break;
			case 'UPDATING':
				include(constant("ARCH_BACK_END_PATH").'status_pages/updating.php');
			break;
			case 'MAINTENANCE':
				include(constant("ARCH_BACK_END_PATH").'status_pages/maintenance.php');
			break;
			default:
				include(constant("ARCH_BACK_END_PATH").'status_pages/misconfigured.php');
			break;
		}
	}
?>