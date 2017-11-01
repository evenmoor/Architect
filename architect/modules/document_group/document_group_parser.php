<?

/*
	Architect Dcoument Group Parser Version 1.1
	- Extends tag_parser.php
	
	Development by: Joshua Moor
	Last Modified: 09/02/13
	
	Variables:
		document_group - group of documents to process
		document_group_limit - number of items to display
		document_group_sort - sort method string for the documents 
	
	Functions:
		replaceBlockDocumentElements - replace block style document tags with content
		replaceInlineDocumentElements - replace inline document tags with content
		replaceOther - replaces document tags 
		documentGroupParser - constructor

	Change Log:
		08/27/14 | 1.1 | replaceBlockDocumentElements and replaceInlineDocumentElements modified to allow inclusion of architect elements
		09/02/13 | 1.0 | Initial Parser Created: document_group, document_group_limit, document_group_sort, replaceBlockDocumentElements, replaceInlineDocumentElements, replaceOther, documentGroupParser
*/

class documentGroupParser extends tagParser{
	
	//group of documents to process
	var $document_group;
	
	//number of items to display
	var $document_group_limit = '';
	
	//sort method string for the documents 
	var $document_group_sort = '';
	
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
			//array to hold elements in the block
			$element_array = array();
			
			//string to match document elements against
			$document_elements_string = "/{arch:[a-zA-Z0-9\s]*\/}/";
			
			//fill array
			preg_match_all($document_elements_string, $block, $element_array);
			
			$documents_in_group = mysql_query('SELECT document_ID,
											  	document_title_'.getCurrentLanguage().',
												document_content_'.getCurrentLanguage().'
												FROM tbl_documents 
												WHERE document_group_FK = "'.$this->document_group.'"
												'.$this->document_group_sort.'
												'.$this->document_group_limit.'');
			
			$parsed_documents_content = '';
			
			while($document = mysql_fetch_assoc($documents_in_group)){//start document loop
				//start block content and fill it with all of the content in the block
				$block_content = $block;
			
				//parse element array
				foreach($element_array[0] as $element){
					//strip name out of the element string
					$element_name = explode(':', $element);
					$element_name = explode('/', $element_name[1]);
					//final element name
					$element_name = $element_name[0];
					
					if(in_array($element_name, $this->document_default_fields)){//if it is a default element
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
														AND additional_field_value_document_FK = "'.clean($document['document_ID']).'"
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
					
					//grab content for element out of the database and replace it in the block
					$block_content = preg_replace("{".$element."}", $element_content, $block_content, 1);
				}
				
				//clean out document start and end tags
				$block_content = str_replace("{arch:document}", "", $block_content);
				$block_content = str_replace("{/arch:document}", "", $block_content);
				
				$parsed_documents_content .= $block_content;
			}//end document loop
			
			//preform actual replacement
			$content = preg_replace("{".$block."}", $parsed_documents_content, $content, 1);
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
			
			$documents_in_group = mysql_query('SELECT document_ID,
											  	document_title_'.getCurrentLanguage().',
												document_content_'.getCurrentLanguage().'
												FROM tbl_documents 
												WHERE document_group_FK = "'.$this->document_group.'"
												'.$this->document_group_sort.'
												'.$this->document_group_limit.'');
			
			$parsed_documents_content = '';
			
			while($document = mysql_fetch_assoc($documents_in_group)){//start document loop
				if(in_array($element_name, $this->document_default_fields)){//if it is a default element
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
													AND additional_field_value_document_FK = "'.clean($document['document_ID']).'"
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
				
				$parsed_documents_content .= $element_content;
			}//end inline document parsing loop
			
			//preform actual replacement
			$content = preg_replace("{".$element."}", $parsed_documents_content, $content, 1);
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
		
		//return parsed content
		return $content;
	}//end replaceOther
	
	// -- documentGroupParser -- 
	// class constructor
	// requires template file from which to pull layout
	// requires boolean indicating whether or not the template is a file or content
	// requires a group of documents to render
	// optional boolean indicating whether or not to automatically render tags. defaults to yes
	// optional boolean indicating whether or not to display the content after rendering. defaults to no
	// optional array of additional parameters for the parser
	// returns nothing
	public function documentGroupParser($template, $template_is_content, $document_group, $render_content  = true, $display_content = false, $parameter_array = array()){
		//distill group id from group name
		$group_id = mysql_query('SELECT document_group_ID
					FROM tbl_document_groups
					WHERE document_group_name="'.clean($document_group).'"
					LIMIT 1');
		
		if(mysql_num_rows($group_id) > 0){//check for valid group
			//distill unique id number from group name
			$group_id = mysql_fetch_assoc($group_id);
			$group_id = $group_id['document_group_ID'];
			$this->document_group = $group_id;
			
			//if a limit has been defined incorporate it
			if(isset($parameter_array['limit'])){
				$this->document_group_limit = "LIMIT ".$parameter_array['limit'];
			}
			
			//if an order has been defined incorporate it
			if(isset($parameter_array['order'])){
				//choose ordering method
				switch($parameter_array['order']){
					//age of document newest to oldest
					case "ageDESC":
					case "newest":
						$this->document_group_sort = "ORDER BY document_created DESC";
					break;
					
					//age of document oldest to newest
					case "ageASC":
					case "oldest":
						$this->document_group_sort = "ORDER BY document_created ASC";
					break;
					
					//last update newest to oldest
					case "updateDESC":
						$this->document_group_sort = "ORDER BY document_modified DESC";
					break;
					
					//last update oldest to newest
					case "updateASC":
						$this->document_group_sort = "ORDER BY document_modified ASC";
					break;
					
					//name z to a
					case "alphabeticalDESC":
						$this->document_group_sort = "ORDER BY document_name DESC";
					break;
					
					//name a to z
					case "alphabeticalASC":
						$this->document_group_sort = "ORDER BY document_name ASC";
					break;
					
					//unhandled sort order
					default:
					break;
				}
			}
			
			//call default tag parser
			parent::tagParser($template, $template_is_content, $render_content, $display_content);
		}else{//invalid group
			?><p>Error: Invalid group</p><?
		}
	}
}

?>