<?

/*
	Architect Tag Parser 1.3
	- Introducted to replace template_parser 1.1
	
	Development by: Joshua Moor
	Last Modified: 9/12/14
	
	Variables:
		content - the content that is being parsed and replaced
		content_rendered - boolean indicating whether or not the content has been rendered
		document_default_fields - content fields available in default documents
		file_path - string containing the file path of the file being parsed
	
	Functions:
		buildMenuString - recursive function that will build a menu string
		getContent - gets the content of the tag parser
		isRendered - checks the current status rendered/unrendered of the content
		navigationElementIsActive - function that determines whether or not to mark a nav item as active
		renderContent - renders the current content of the object
		replaceBlocks - replaces block tags with their associated content.
		replaceForms - replaces form tags with their actual forms.
		replaceNavigation - replaces navigation tags with fully fleshed out navigation menus.
		replaceOther - replaces other tags. Empty in the basic tag parser, but is designed to be overloaded by classes inheriting from this base class (documents, blogs, etc.)
		replacePaths - replaces path tags with their actual paths
		replacePrivate - checks to see if the user has valid permissions for a given section of a document
		replaceRemote - replaces remote tags with something in another document/group
		replaceTags - control function to call other replacers.
		tagParser - constructor

	Change Log:
		08/28/14 | 1.3 | replacePrivate added
		08/27/14 | 1.2 | replaceForms added (implemented on 9/12/14)
		11/06/13 | 1.1 | navigationElementIsActive tweaked to fix error 29
		09/02/13 | 1.0 | Initial Parser Created: content, content_rendered, document_default_fields, file_path, buildMenuString, getContent, isRendered, navigationElementIsActive, renderContent, replaceBlocks, replaceNavigatoin, replaceOther, replacePaths, replaceRemote, replaceTags, tagParser

*/

class tagParser{
	//content that is being parsed and replaced.
	var $content;
	//boolean indicating whether or not the content has been rendered.
	var $content_rendered;
	//content fields available in default documents
	var $document_default_fields = array("title", "content");
	//string containing the file path of the file being parsed
	var $file_path;
	
	// -- buildMenuString -- //
	//recursive function that will build a menu string
	//requires an id of the menu to generate
	//optional id of parent node
	//returns a string containing the menu
	public function buildMenuString($menu, $parent_element = 0){
		//string to hold menu
		$menu_string = '';
		
		//set parent element string
		if($parent_element != 0){//set search string for sub elements
			$parent_string = '= "'.clean($parent_element).'"';
		}else{//change search string for top level navigation elements (null parent)
			$parent_string = 'IS NULL';
		}
		
		//navigation elements in menu of parent
		$elements_of_parent = mysql_query('SELECT navigation_menu_item_ID,
										  			navigation_menu_item_link_target,
													navigation_menu_item_class,
													navigation_menu_item_name_'.getCurrentLanguage().',
													navigation_menu_item_title_'.getCurrentLanguage().'
												FROM tbl_navigation_menu_items
												WHERE navigation_menu_item_navigation_menu_FK = "'.clean($menu).'"
												AND navigation_menu_item_parent_menu_item_FK '.$parent_string.'
												ORDER BY navigation_menu_item_position DESC, navigation_menu_item_name_'.getCurrentLanguage().'
												');
		
		//check for elements to display
		if(mysql_num_rows($elements_of_parent) > 0){
			$menu_string .= '<ul>';
				//parse elements
				while($element = mysql_fetch_assoc($elements_of_parent)){
					$classes = '';
					//add custom classes
					$classes = $element['navigation_menu_item_class'];
					
					$link_target = $element['navigation_menu_item_link_target'];
					$type_check = explode('arch_doc:', $link_target);
					
					//if the link is to an internal document rewrite the link
					if(count($type_check) > 1){
						$target_document = mysql_fetch_assoc(mysql_query('SELECT document_ID,
																		 document_name
																		 FROM tbl_documents
																		 WHERE document_ID = "'.clean($type_check[1]).'"
																		 LIMIT 1'));
						if(constant("ARCH_DOCUMENT_PATH_ID")){//check to see if ids are in page paths
							$link_target = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$target_document['document_ID'].'-'.$target_document['document_name'].'/';
						}else{
							$link_target = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$target_document['document_name'].'/';
						}
					}

					if($this->navigationElementIsActive($element['navigation_menu_item_link_target'], $menu)){//check to see if the link needs to be marked active
						if($classes != ''){
							$classes.= ' ';
						}
						$classes.= 'active';
					}
					
						//build link
						//if there is a link
						if($link_target != ''){
							$menu_string .= '<li class="'.$classes.'"><a href="'.$link_target.'" title="'.$element['navigation_menu_item_title_'.getCurrentLanguage()].'">'.$element['navigation_menu_item_name_'.getCurrentLanguage()].'</a>';
						}else{
							$menu_string .= '<li class="'.$classes.'">'.$element['navigation_menu_item_name_'.getCurrentLanguage()].'';
						}
						//build sub navigation
						$menu_string .= $this->buildMenuString($menu, $element['navigation_menu_item_ID']);
					$menu_string .= '</li>';
				}
			$menu_string .= '</ul>';
		}
		
		//return menu
		return $menu_string;
	}//end buildMenuString
	
	// -- getContent --
	// gets the content of the tag parser
	// requires nothing
	// returns a string containing the content
	public function getContent(){
		return $this->content;
	}//end getContent
	
	// -- isRendered --
	// checks the current status rendered/unrendered of the content
	// requires nothing
	// returns a boolean indicating status
	public function isRendered(){
		return $this->content_rendered;
	}//end isRendered
	
	// -- navigationElementIsActive -- //
	//function that determines whether or not to mark a nav item as active
	//requires a string describing the current target of the link
	//returns a boolean indicating  whether or not an element should be marked active
	public function navigationElementIsActive($target){
		return false;
	}
	
	// -- renderContent --
	// renders the current content of the object
	// requires nothing
	// returns nothing
	public function renderContent(){
		echo $this->content;
	}//end renderContent
	
	// -- replaceBlocks -- 
	// replaces block tags with their associated content
	// requires content to parse for block tags
	// returns parsed content
	public function replaceBlocks($content){
		//regular expression indicating what a block looks like
		$block_expression = "/\{arch:block:(.*)\/\}/";
		
		//array to hold blocks that need to be replaced
		$block_array = array();
		
		//fill the array
		preg_match_all($block_expression, $content, $block_array);
		
		//parse block array
		foreach($block_array[0] as $block){
			//strip name out of the block string
			$block_name = explode(':', $block);
			$block_name = explode('/', $block_name[2]);
			//final block name
			$block_name = $block_name[0];
			
			//grab content for block out of the database
			$block_content = mysql_fetch_assoc(mysql_query('SELECT block_code_location
															FROM tbl_blocks
															WHERE block_name="'.$block_name.'"
															LIMIT 1'));

			$block_content = $block_content['block_code_location'];
			//clean out install path
			$block_content = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $block_content, 1);
			//final block path
			$block_content = constant("ARCH_FRONT_END_PATH").$block_content;
			
			//read content out of the block
			ob_start();
			include($block_content);
			$block_content = ob_get_contents();
			ob_end_clean();
			
			//sets up a recursive call rendering any architect tags that are in the block
			$block_content = $this->replaceTags($block_content);
			
			//preform actual replacement
			$content = preg_replace("{".$block."}", $block_content, $content, 1);
		}//end block parsing
		
		//return parsed content
		return $content;
	}//end replaceBlocks
	
	// -- replaceForms -- 
	// replaces form tags with their forms based on stage
	// requires content to parse for form tags
	// returns parsed content
	public function replaceForms($content){
		//regular expression indicating what a form looks like
		$form_expression = "/\{arch:form:(.*)\/\}/";
		
		//array to hold forms that need to be replaced
		$form_array = array();
		
		//fill the array
		preg_match_all($form_expression, $content, $form_array);
		
		//parse block array
		foreach($form_array[0] as $form){
			//strip name out of the form string
			$form_name = explode(':', $form);
			$form_name = explode('/', $form_name[2]);
			//final form name
			$form_name = $form_name[0];
			
			require_once(constant("ARCH_BACK_END_PATH").'modules/forms/form_parser.php');
			
			//initial variables
			$page = 1;//current page of the form
			$process = false;
		
			//if a page has been submitted
			if(isset($_POST['form'])){
				if($_POST['form'] == $form_name){//ensure the form is the right one
					$process = true;
					$page = $_POST['page'];//set the page
				}
			}else{//initial form page
				//build variable array
				$_SESSION['form_variables'][$form_name] = array();
				
				//default error state
				$_SESSION['form_variables'][$form_name]["Error"] = "NONE";
			}
		
			//create the form
			$display_form = new Form($form_name, $page, $_POST, $_SESSION['form_variables'][$form_name], $process);
			
			//on successful build display form
			if($display_form->build_state === "SUCCESS"){
				//display the form
				//Note: currently this is wrapped in an ob start. future versions will replace this with two function calls: one to build the HTML and one to pass back the value in order to eliminate the ob call.
				ob_start();
					$_SESSION['form_variables'][$form_name] = $display_form->display();
					$form_content = ob_get_contents();
				ob_end_clean();
			}else{//otherwise display error
				$form_content = "<p class='error'>".$display_form->build_state."</p>";
			}
			
			//preform actual replacement
			$content = preg_replace("{".$form."}", $form_content, $content, 1);
		}//end form processing
		
		//return parsed content
		return $content;
	}//end replaceForms
	
	// -- replaceNavigation -- 
	// replaces navigation tags with fully fleshed out navigation menus.
	// requires content to parse
	// returns parsed content
	public function replaceNavigation($content){
		//regular expression indicating what a navigation menu looks like
		$navigation_expression = "/\{arch:navigation:(.*)\/\}/";
		
		//array to hold blocks that need to be replaced
		$navigation_array = array();
		
		//fill the array
		preg_match_all($navigation_expression, $content, $navigation_array);
		
		//parse navigation menu array
		foreach($navigation_array[0] as $menu){
			//strip name out of the menu string
			$menu_name = explode(':', $menu);
			$menu_name = explode('/', $menu_name[2]);
			//final menu name
			$menu_name = $menu_name[0];
			
			//grab content for menu out of the database
			$menu_content = mysql_fetch_assoc(mysql_query('SELECT navigation_menu_ID
															FROM tbl_navigation_menus
															WHERE navigation_menu_name="'.$menu_name.'"
															LIMIT 1'));
			
			$menu_content = $menu_content['navigation_menu_ID'];
			
			$menu_content = $this->buildMenuString($menu_content, 0);

			//preform actual replacement
			$content = preg_replace("{".$menu."}", $menu_content, $content, 1);
		}//end navigation parsing
		
		
		//return parsed content
		return $content;
	}//end replaceNavigation
	
	// -- replaceOther -- 
	// replaces other tags. Empty in the basic tag parser, but is designed to be overloaded by classes inheriting from this base class (documents, blogs, etc.)
	// requires content to parse
	// returns pased content
	public function replaceOther($content){
		/*
		
			Left Empty on purpose!
		
		*/
		//return parsed content
		return $content;
	}//end replaceOther
	
	// -- replacePaths -- 
	// replaces path tags with rendered paths
	// requires content to parse
	// returns parsed content
	public function replacePaths($content){
		//regular expression indicating what a path element looks like
		$paths_expression = "/\{arch:path:(.*)\/\}/";
		
		//array to hold blocks that need to be replaced
		$paths_array = array();
		
		//fill the array
		preg_match_all($paths_expression, $content, $paths_array);
		
		
		//explode path value
		$file_path = explode('/', $this->file_path);
		
		//base path for path variables
		$base_path = '';
		//build pase path
		for($loop_counter = 0; $loop_counter < count($file_path) - 1; $loop_counter++){
			$base_path .= $file_path[$loop_counter].'/';
		}
		
		//append install path
		$base_path = constant('ARCH_INSTALL_PATH').$base_path;
		
		//parse path array
		foreach($paths_array[0] as $path){
			//strip name out of the menu string
			$path_name = explode(':', $path);
			$path_name = explode('/', $path_name[2]);
			//final menu name
			$path_name = $path_name[0];
			
			//set base path
			$path_content = $base_path;
			
			//append folder to base path value
			switch($path_name){
				case 'templateRoot':
					$path_content .= '';
				break;
				case 'templateImages':
					$path_content .= 'images/';
				break;
				case 'templateStyles':
					$path_content .= 'styles/';
				break;
				case 'templateScripts':
					$path_content .= 'scripts/';
				break;
				case 'repository':
					$path_content = constant('ARCH_INSTALL_PATH').'media/repository/';
				break;
			}
			

			//preform actual replacement
			$content = preg_replace("{".$path."}", $path_content, $content, 1);
		}//end path parsing
		
		//return parsed content
		return $content;
	}//end replacePaths
	
	// -- replacePrivate -- 
	// removes private tags if the user's permission levels aren't high enough
	// requires content to parse
	// returns parsed content
	public function replacePrivate($content){
		//regular expression indiicating what a document block looks like
		$private_block_string = "/\{arch:private(?:.*?)\}(.*?)\{\/arch:private\}/s";
		
		//array to hold blocks that need to be replaced
		$private_array = array();
		
		//fill the array
		preg_match_all($private_block_string, $content, $private_array);
		
		//parse document block array
		foreach($private_array[0] as $block){
			//array to hold parameters
			$element_parameters = array();
			
			//string to match private parameters against
			$private_parameters_string = "/{arch:private(?:.*?)}/";
			
			//fill array with parameters to parse
			preg_match_all($private_parameters_string, $block, $element_parameters);
			$block_parameters = $element_parameters[0];
			
			//build block parameters array
			$block_parameters = str_replace("arch:private ", "", $block_parameters[0]);
			$block_parameters = explode(" ", $block_parameters);
			//preg_match_all("/\s/", $block_parameters, $block_parameters);
			//print_r($block_parameters);
			//echo "<h1>$block_parameters</h1>";
			//preg_match_all("", $block_parameters, $block_parameters);
			
			//parameters for the block
			//controls which user groups can view the block
			$view = '';
			//boolean controlling whether or not to show a login form on failure to validate
			$refer = false;
			
			//array to hold final computed values with spaces while I find a regular expression to replace the explode on line 371
			$final_values = array();
			
			$current_element = -1;
			
			//loop to deal with space filled parameters pending regular expression 
			foreach($block_parameters as $parameter){
				$new_param_check = explode("=", $parameter);
				
				if(count($new_param_check) > 1){
					$current_element++;
				}
				$final_values[$current_element] .= $parameter." ";
			}
			
			//print_r($final_values);
			
			//parse private parameters
			foreach($final_values as $parameter){
				$parameter = str_replace('{', '', $parameter);
				$parameter = str_replace('}', '', $parameter);
				$parameter_values = explode("=", $parameter);
				
				switch($parameter_values[0]){
					case "view":
						$value = str_replace('"', '', $parameter_values[1]);
						$view = trim($value);
					break;
					case "refer":
						$value = str_replace('"', '', $parameter_values[1]);
						//check true/false value
						if(trim(strtoupper($value)) == "TRUE"){
							$refer = true;
						}else{
							$refer = false;
						}
					break;
				}
			}//end parameter parse
			
			if(isLoggedIn() && validatePermissions("document", $view)){//if the user is qalified to see the block
				//rebuild the whole block
				$private_content = $block;
				
				//clean out private start and end tags
				$private_content = str_replace($element_parameters[0][0], "", $private_content);
				$private_content = str_replace("{/arch:private}", "", $private_content);
			}else{//if the user isn't qualified to see the block
				if($refer && !isLoggedIn()){//refer to login page
					$private_content = "<form action='".constant("ARCH_INSTALL_PATH")."user/login/?rpath=".$_SERVER['REQUEST_URI']."' method='post' class='private_login_form'>";
						$private_content .= "<p>Please log in to see this content.</p>";
						$private_content .= "<p><label>Username:<br/><input type='text' name='loginUsername'/></label></p>";
						$private_content .= "<p><label>Password:<br/><input type='password' name='loginPassword'/></label></p>";
						$private_content .= "<p><input type='submit' value='Log In'/></p>";
						$private_content .= "<input type='hidden' name='logInSubmitted' value='true'/>";
                      	$private_content .= "<input type='hidden' name='rpath' value='".$_SERVER['REQUEST_URI']."'/>";
					$private_content .= "</form>";
				}else{//no referral needed
					//empty the block
					$private_content = "";
				}
			}
			
			//preform actual replacement
			$content = preg_replace("{".$block."}", $private_content, $content, 1);
		}
		
		
		//return parsed content
		return $content;
	}//end replacePaths
	
	// -- replaceRemote -- 
	// replaces remote tags with something in another document/group
	// requires content to parse
	// returns parsed content
	public function replaceRemote($content){
		//regular expression indicating what a remote element looks like
		$remote_elements_expression = "/\{arch:remote(?:.*?)\}(.*?)\{\/arch:remote\}/s";
		
		//array to hold blocks that need to be replaced
		$remote_elements_array = array();
		
		//fill the array
		preg_match_all($remote_elements_expression, $content, $remote_elements_array);
		
		//parse navigation menu array
		foreach($remote_elements_array[0] as $remote){
			//echo "<h1>Remote Element Found!!!</h1>";
			
			//regular expression identifying the remote block header
			$remote_header_expression = "/\{arch:remote(?:.*?)\}/";
			//grab header
			preg_match($remote_header_expression, $remote, $remote_parameters);
			$remote_parameters = $remote_parameters[0];
			
			//clean out architect text 
			$remote_parameters = str_replace("{arch:remote", "", $remote_parameters);
			$remote_parameters = str_replace("}", "", $remote_parameters);
			$remote_parameters = trim($remote_parameters);
			
			//create parameter array 
			$remote_parameters = explode(" ", $remote_parameters);
			//print_r($remote_parameters);
			
			
			$parameters = array();
			foreach($remote_parameters as $parameter){
				//grab out element pairs
				$elements = explode("=", $parameter);
				
				//clear out quote marks
				$elements[1] = str_replace('"', '', $elements[1]);
				$elements[1] = str_replace("'", '', $elements[1]);
				
				$parameters[$elements[0]] = $elements[1];
			}
			
			//print_r($parameters);
			//handle remote content by type
			$remote_content = '';
			
			
			
			switch($parameters["type"]){
				case "document":
					//replace remote tags with document tags to prevent infinate loop
					$template = preg_replace($remote_header_expression, "{arch:document}", $remote);
					$template = str_replace("{/arch:remote}", "{/arch:document}", $template);
				
					//load parser if it hasn't already been
					require_once(constant("ARCH_BACK_END_PATH").'modules/documents/document_parser.php');
					
					//grab document information
					$document = mysql_fetch_assoc(mysql_query('SELECT *
															FROM tbl_documents
															WHERE document_ID="'.clean($parameters["id"]).'"
															LIMIT 1'));
					//invoke parser
					$remote_document = new documentParser($template, true, $document, true, false, false);
					//extract content 
					$remote_content = $remote_document->getContent();
				break;
				
				case "group":
					//replace remote tags with document tags to prevent infinate loop
					$template = preg_replace($remote_header_expression, "{arch:document}", $remote);
					$template = str_replace("{/arch:remote}", "{/arch:document}", $template);
				
					//load parser if it hasn't already been
					require_once(constant("ARCH_BACK_END_PATH").'modules/document_group/document_group_parser.php');
					
					//array to hold group template parameters
					$group_parameters = array();
					
					//if a limit parameter was provided in remote block
					if(isset($parameters['limit'])){
						$group_parameters['limit'] = $parameters['limit'];
					}
					
					//if an order parameter was proviced in remote block
					if(isset($parameters['order'])){
						$group_parameters['order'] = $parameters['order'];
					}
					
					//invoke parser
					$remote_group = new documentGroupParser($template, true, $parameters["id"], true, false, $group_parameters);

					//extract content
					$remote_content = $remote_group->getContent();
				break;
				
				default:
					//replace remote tags with expanded tags to prevent infinate loop and enable the use to the template in various modules
					$template = preg_replace($remote_header_expression, "{arch:expanded}", $remote);
					$template = str_replace("{/arch:remote}", "{/arch:expanded}", $template);
				
					//load basic config file
					global $expansion_modules; //grab the global expansion modules from the basic configs

					if(in_array($parameters["type"], $expansion_modules)){//check expanded modules list to make sure the target module is installed
						//load the module's config
						require(constant("ARCH_BACK_END_PATH").'modules/'.$parameters["type"].'/module_config.php');
						
						if(isset($remote_support) && $remote_support == true){//check to make sure the module supports the remote tag
							//build path for to the module
							$module_path =  constant("ARCH_BACK_END_PATH").'/modules/'.$parameters["type"].'/remote_content.php';
							//add parameters
							
							//read content out of the module
							ob_start();
							include($module_path);
							$remote_content = ob_get_contents();
							ob_end_clean();
						}else{//module without remote support
							$remote_content = "Error: this module does not support remote calls.";
						}
					}else{//unsupported module
						$remote_content = "Error: unsupported module.";
					}
				break;
			}
			
			//preform actual replacement
			$content = preg_replace("{".$remote."}", $remote_content, $content, 1);
		}//end remote element parsing

		//return parsed content
		return $content;
	}//end replaceRemote
	
	// -- replaceTags --
	// control function to invoke other replacers
	// requires content to parse
	// returns parsed content
	public function replaceTags($content){
		//blocks are replaced first as they might create additional replacements in later code
		$content = $this->replaceBlocks($content);
		//private areas are parsed second to save additional parsing if sections are eliminated by permissions
		$content = $this->replacePrivate($content);
		$content = $this->replaceForms($content);
		$content = $this->replaceNavigation($content);
		$content = $this->replacePaths($content);
		$content = $this->replaceRemote($content);
		//other tags are replaced last because the base class may lose control during this process.
		$content = $this->replaceOther($content);
		return $content;
	}//end replaceTags
	
	// -- tagParser -- 
	// class constructor
	// requires file from which to pull content
	// boolean indicating whether or not the file passed is the content or a path to the file
	// optional boolean indicating whether or not to automatically render tags. defaults to yes
	// optional boolean indicating whether or not to display the content after rendering. defaults to no
	// returns nothing
	public function tagParser($file, $file_is_content = false, $render_content  = true, $display_content = false){
		$this->content_rendered = false;
		
		if($file_is_content){//if the file is content and ready to be used
			$this->content = $file;
			$this->file_path = "";
		}else{//if the file is a path to be opened
			//open the template and grab out its content
			$file_handler = fopen($file, 'r') or die('Failed to load file: '.$file.'.<br/>');
			
			//if the file has content
			if($file_handler != false){
				//read content out of template file with rendered php. If you want php to be limited per template we could add and if branch here
				ob_start();
				include_once($file);
				$file_content = ob_get_contents();
				ob_end_clean();
				//fill object's content
				$this->content = $file_content;
				$this->file_path = $file;
			}
			
			//close file
			fclose($file_handler);
		}
		
		//check to see if we need to render this content
		if($render_content){
			$this->content = $this->replaceTags($this->content);
			
			//by this point content should be rendered
			$this->content_rendered = true;
		}
		
		//check to see if we need to display this content
		if($display_content){
			$this->renderContent();
		}	
	}//end tagParser
}
?>