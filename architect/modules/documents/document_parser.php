<?

/*
	Architect Dcoument Group Parser Version 1.2
	- Extends tag_parser.php
	
	Development by: Joshua Moor
	Last Modified: 11/06/13
	
	Variables:
		document - elements of the current document
		edit_mode - boolean controlling whether or not to render document in edit mode
	
	Functions:
		navigationElementIsActive - function that determines whether or not to mark a nav item as active
		replaceBlockDocumentElements - replace block style document tags with content
		replaceInlineDocumentElements - replace inline style document tags with content
		replaceOther - replaces document tags 
		documentParser - constructor

	Change Log:
		08/27/14 | 1.2 | replaceBlockDocumentElements and replaceInlineDocumentElements modified to allow inclusion of architect elements
		11/06/13 | 1.1 | navigationElementIsActive tweaked to fix error 29
		09/02/13 | 1.0 | Initial Parser Created: document, edit_mode, navigationElementIsActive, replaceBlockDocumentElements, replaceInlineDocumentElements, replaceOther, documentParser
*/

class documentParser extends tagParser{
	// elements of the current document
	var $document;

	// boolean controlling whether or not to render document in edit mode
	var $edit_mode;
	
	// -- navigationElementIsActive -- //
	//function that determines whether or not to mark a nav item as active
	//requires a string describing the current target of the link and an integer constituting a menu id
	//returns a boolean indicating  whether or not an element should be marked active
	public function navigationElementIsActive($target, $menu){
		$type_check = explode('arch_doc:', $target);
		$active = false;
		
		if($type_check[1] == $this->document['document_ID']){
			$active = true;
		}
		
		//loop to check for active child
		$check_id = $type_check[1];
		$search_id = mysql_fetch_assoc(mysql_query('SELECT navigation_menu_item_ID 
															FROM tbl_navigation_menu_items
															WHERE navigation_menu_item_link_target = "arch_doc:'.$this->document['document_ID'].'"
															AND navigation_menu_item_navigation_menu_FK = "'.clean($menu).'"
															LIMIT 1'));
		
		$search_id = $search_id['navigation_menu_item_ID'];
		$search = true;
		while($search){
			$search_result = mysql_fetch_assoc(mysql_query('SELECT navigation_menu_item_link_target,
														   navigation_menu_item_parent_menu_item_FK
														   FROM tbl_navigation_menu_items
														   WHERE navigation_menu_item_ID="'.$search_id.'"
														   LIMIT 1'));
			
			if($search_result['navigation_menu_item_link_target'] == "arch_doc:".$check_id){//found link
				$active = true;
				$search = false;
			}elseif($search_result['navigation_menu_item_parent_menu_item_FK'] == '' || $search_result['navigation_menu_item_parent_menu_item_FK'] == 0){//no parent
				$search = false;
			}else{//found parent to search
				$search_id = $search_result['navigation_menu_item_parent_menu_item_FK'];
			}
		}
		
		return $active;
	}
	
	// -- replaceBlockDocumentElements -- 
	// replaces block style document tags
	// requires content to parse
	// returns parsed content
	public function replaceBlockDocumentElements($content){
		//regular expression indiicating what a document block looks like
		$document_block_string = "/\{arch:document\}(.*?)\{\/arch:document\}/s";
		
		//array to hold blocks that need to be replaced
		$document_array = array();
		
		//fill the array
		preg_match_all($document_block_string, $content, $document_array);
		
		//parse document block array
		foreach($document_array[0] as $block){
			//start block content and fill it with all of the content in the block
			$block_content = $block;

			//array to hold elements in the block
			$element_array = array();
			
			//string to match document elements against
			$document_elements_string = "/{arch:[a-zA-Z0-9\s]*\/}/";
			
			//fill array
			preg_match_all($document_elements_string, $block, $element_array);
			
			//parse element array
			foreach($element_array[0] as $element){
				//strip name out of the element string
				$element_name = explode(':', $element);
				$element_name = explode('/', $element_name[1]);
				//final element name
				$element_name = $element_name[0];
				
				//inline editing variables
				$element_editing_type = "";
				
				if(in_array($element_name, $this->document_default_fields)){//if it is a default element
					if($element_name == "title"){//switch to inline definition to remove extra tags generated by ckeditor
						$element_editing_type = " arch-inline_element";
					}
				
					//document element name
					$element_name = 'document_'.$element_name.'_'.getCurrentLanguage();
					
					//grab tag parser
					require_once(constant("ARCH_BACK_END_PATH").'modules/tag_parser.php');

					//variable to hold content with rendered tags filled with content
					$tag_rendered_content = $this->document[$element_name];
					//build handler and pass it the content to be rendered
					$tag_rendered_content_handler = new tagParser($tag_rendered_content, true, true, false);
					
					//retrieve the rendered content
					$tag_rendered_content = $tag_rendered_content_handler->getContent();
		
					ob_start();
					//evaluate string as php append closing and open php tags to comply with expected php eval format
					//http://php.net/eval
                    eval("?>".$tag_rendered_content."<?");
					$evaluated_content = ob_get_clean();
					
					$element_content = $evaluated_content;
				}else{//if it is not a default element
					$field_id = mysql_query('SELECT additional_field_ID 
											FROM tbl_additional_fields
											WHERE additional_field_name = "'.clean($element_name).'"
											LIMIT 1');
					
					if(mysql_num_rows($field_id) > 0){//if the field exsists
						$field_id = mysql_fetch_assoc($field_id);
						$field_id = $field_id['additional_field_ID'];
						
						$field_value = mysql_query('SELECT additional_field_value_'.getCurrentLanguage().'
													FROM tbl_additional_field_values
													WHERE additional_field_value_additional_field_FK = "'.clean($field_id).'"
													AND additional_field_value_document_FK = "'.clean($this->document['document_ID']).'"
													LIMIT 1');
						
						if(mysql_num_rows($field_value) > 0){//if the field has value
							$field_value = mysql_fetch_assoc($field_value);
							$field_value = $field_value['additional_field_value_'.getCurrentLanguage()];

							$element_content = $field_value;
						}else{//the field has no value
							$element_content = '';
						}
					}else{//field dosn't exsist
						$element_content = '';
					}
				}//end non default element

				if($this->edit_mode == true){//check for editing mode
					if(trim($element_content) == ''){//check for empty elements in edit mode
						$element_content = $element;
					}
					$element_content = '<div class="arch-content_element'.$element_editing_type.'" id="'.$element_name.'" style="display:inline;" contenteditable="true">'.$element_content.'</div>';
				}
				
				//grab content for element out of the database and replace it in the block
				$block_content = preg_replace("{".$element."}", $element_content, $block_content, 1);
				//echo $block_content;
			}
			
			//clean out document start and end tags
			$block_content = str_replace("{arch:document}", "", $block_content);
			$block_content = str_replace("{/arch:document}", "", $block_content);
			
			//preform actual replacement
			$content = preg_replace("{".$block."}", $block_content, $content, 1);
		}//end document block parsing
		
		return $content;
	}// end replaceBlockDocumentElements
	
	// -- replaceInlineDocumentElements -- 
	// replaces inline style document tags
	// requires content to parse
	// returns parsed content
	public function replaceInlineDocumentElements($content){
		//regular expression indicating what a document inline element looks like
		$inline_document_expression = "/\{arch:document:(.*)\/\}/";
		
		//array to hold blocks that need to be replaced
		$document_array = array();
		
		//fill the array
		preg_match_all($inline_document_expression, $content, $document_array);
		
		//parse inline document elements array
		foreach($document_array[0] as $element){
			//strip name out of the block string
			$element_name = explode(':', $element);
			$element_name = explode('/', $element_name[2]);
			//final block name
			$element_name = $element_name[0];
			
			//inline editing variables
			$element_editing_type = "";
			
			if(in_array($element_name, $this->document_default_fields)){//if it is a default element
				if($element_name == "title"){//switch to inline definition to remove extra tags generated by ckeditor
					$element_editing_type = " arch-inline_element";
				}
			
				//document element name
				$element_name = 'document_'.$element_name.'_'.getCurrentLanguage();
			
				//grab tag parser
				require_once(constant("ARCH_BACK_END_PATH").'modules/tag_parser.php');

				//variable to hold content with rendered tags filled with content
				$tag_rendered_content = $this->document[$element_name];
				//build handler and pass it the content to be rendered
				$tag_rendered_content_handler = new tagParser($tag_rendered_content, true, true, false);
				
				//retrieve the rendered content
				$tag_rendered_content = $tag_rendered_content_handler->getContent();
	
				ob_start();
				//evaluate string as php append closing and open php tags to comply with expected php eval format
				//http://php.net/eval
				eval("?>".$tag_rendered_content."<?");
				$evaluated_content = ob_get_contents();
				ob_end_clean();
				
				$element_content = $evaluated_content;
			}else{//if it is not a default element
				$field_id = mysql_query('SELECT additional_field_ID 
										FROM tbl_additional_fields
										WHERE additional_field_name = "'.clean($element_name).'"
										LIMIT 1');
				
				if(mysql_num_rows($field_id) > 0){//if the field exsists
					$field_id = mysql_fetch_assoc($field_id);
					$field_id = $field_id['additional_field_ID'];
					
					$field_value = mysql_query('SELECT additional_field_value_'.getCurrentLanguage().'
												FROM tbl_additional_field_values
												WHERE additional_field_value_additional_field_FK = "'.clean($field_id).'"
												AND additional_field_value_document_FK = "'.clean($this->document['document_ID']).'"
												LIMIT 1');
					
					if(mysql_num_rows($field_value) > 0){//if the field has value
						$field_value = mysql_fetch_assoc($field_value);
						$field_value = $field_value['additional_field_value_'.getCurrentLanguage()];
						
						$element_content = $field_value;
					}else{//the field has no value
						$element_content = '';
					}
				}else{//field dosn't exsist
					$element_content = '';
				}
			}//end non default element
			
			if($this->edit_mode == true){//check for editing mode
				if(trim($element_content) == ''){//check for empty elements in edit mode
					$element_content = $element;
				}
				$element_content = '<div class="arch-content_element'.$element_editing_type.'" id="'.$element_name.'" style="display:inline;" contenteditable="true">'.$element_content.'</div>';
			}
			
			//preform actual replacement
			$content = preg_replace("{".$element."}", $element_content, $content, 1);
		}//end inline document parsing
		
		//return parsed content
		return $content;
	}// end replaceInlineDocumentElements
	
	// -- replaceOther -- 
	// replaces document tags
	// requires content to parse
	// returns parsed content
	public function replaceOther($content){
		$content = $this->replaceInlineDocumentElements($content);
		$content = $this->replaceBlockDocumentElements($content);
		
		//if the document is going to be edited add supporting scripts
		if($this->edit_mode == true){
			$editing_scripts .= '<script type="text/javascript">';

			$editing_scripts .= 'var CKEDITOR_BROWSE_PATH = "'.constant("ARCH_INSTALL_PATH").''.constant("ARCH_HANDLER_MANAGE").'/publish/media/media_browser/";';
			$editing_scripts .= 'var CKEDITOR_BROWSE_UPLOAD_PATH = "'.constant("ARCH_INSTALL_PATH").''.constant("ARCH_HANDLER_MANAGE").'/publish/media/media_browser_upload/";';

			$editing_scripts .= 'var ckEditor_custom_styles = "default";';
			$editing_scripts .= '</script>';
			$editing_scripts .= '<script src="'.constant("ARCH_INSTALL_PATH").'scripts/ckeditor/ckeditor.js"></script>';
			
			$content = str_replace('</head>', $editing_scripts.'</head>', $content);
		}
		
		//return parsed content
		return $content;
	}//end replaceOther
	
	// -- documentParser -- 
	// class constructor
	// requires template file from which to pull layout
	// requires boolean indicating whether or not the template is a file or content
	// requires mysql_assoc formatted array of document content
	// optional boolean indicating whether or not to automatically render tags. defaults to yes
	// optional boolean indicating whether or not to display the content after rendering. defaults to no
	// returns nothing
	public function documentParser($template, $template_is_content, $document_values, $render_content  = true, $display_content = false, $edit_mode = false){
		$this->document = $document_values;
		$this->edit_mode = $edit_mode;
		
		//call default tag parser
		parent::tagParser($template, $template_is_content, $render_content, $display_content);
	}
}

?>