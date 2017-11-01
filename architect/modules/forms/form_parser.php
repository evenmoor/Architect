<?
/*
	Form Builder 1.0
	
	Development by: Joshua Moor
	Last Modified: 9/12/14
	
	Variables:
		build_state - (PUBLIC) the current build state of the object
		form_id - ID number of the form in the database
		form_page - current page of the form
		total_pages - total pages in the form
		form_name - name of the form in the database
		process_form - boolean indicating whether or not to process the current page of the form
		form_variables - variables passed by browser from the form
		session_variables - session passed in which to store form variables
	
	Functions:
		build - (PUBLIC) build the html and javascript for the form
		display - calls internal functions in order to display the form in its current state
		process - process the current page and progress the form to the next page
		submit - submit the completed form to the destination email address
		
		form - (PUBLIC) the form constructor

	Change Log:
		09/10/13 | 1.0 | Initial Class Created: build_state, form_id, form_page, total_pages, form_name, process_form, form_variables, session_variables, display, process, build, submit, form 

*/

	class Form{
		//public property describing the build state of the form
		//contains SUCCESS if built and an error otherwise
		public $build_state;
		
		//database id of the form
		private $form_id;
		//current display page of the form
		private $form_page;
		//total pages in form
		private $total_pages;
		//name of the form
		private $form_name;
		//boolean controlling processing of form
		private $process_form;
		
		//Variable containers
		//array of variables passed to the form
		private $form_variables;
		//arry of variables stored in the session
		private $session_variables;
		
		//-------------- Build --------------//
		//function to handle building the form's HTML
		//requires: nothing
		//returns: nothing
		private function build(){
			$form_content = '';
				
			//build form header
			$form_content = '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
			
				//check to see if there is an error from processing
				if($this->session_variables["Error"] != "NONE"){
					$form_content .= "<p class='form_error'>Error: ".$this->session_variables["Error"]."</p>";
				}
				
				//grab elements
				$elements = mysql_query('SELECT form_element_ID,
												form_element_type_FK,
												form_element_name,
												form_element_content,
												form_element_label,
												form_element_pattern,
												form_element_is_required
											FROM tbl_form_elements
											WHERE form_element_form_FK = "'.clean($this->form_id).'"
												AND form_element_form_page = "'.clean($this->form_page).'"
											ORDER BY form_element_order');
				
				//grab scripts
				$form_scripts = mysql_query('SELECT form_script_location
												FROM tbl_form_scripts
												WHERE form_script_form_FK = "'.clean($this->form_id).'"');
											
				//counter to ensure unique field ids
				$element_id = 1;
				
				//booleans indicating presence of elements which might need javascript fallbacks
				$required_elements_present = false;
				$placeholders_present = false;
				$patterns_present = false;
				
				//build form
				while($element = mysql_fetch_assoc($elements)){
					switch($element['form_element_type_FK']){
						case 5://select box
							//label
							$label = false;
							if(trim($element['form_element_label']) != ""){
								$label = true;
							}
							
							//start the element
							$form_content .= '<p>';
							//apply label
							if($label){
								$form_content .= '<label for="'.$this->form_name."_".$element_id .'">'.$element['form_element_label'];
								if($element['form_element_is_required'] == 1){
									$form_content .='*';
								}
								$form_content .= '<br/>';
							}
							
							$form_content .= '<select name="'.$element['form_element_name'].'" id="'.$this->form_name."_".$element_id .'">';
							
							$values = mysql_query('SELECT form_value_display_value,
															form_value
														FROM tbl_form_values
														WHERE form_value_form_element_FK = "'.clean($element['form_element_ID']).'"
														ORDER BY form_value_order');
														
							while($value = mysql_fetch_assoc($values)){
								$form_content .= '<option value="'.$value['form_value'].'">'.$value['form_value_display_value'].'</option>';
							}
							
							//end the element
							$form_content .= '</select></label></p>';
						break; 
						
						case 4://check boxes
						case 3://radio buttons and similar elements
							//label
							$label = false;
							if(trim($element['form_element_label']) != ""){
								$label = true;
							}
						
							//start the element
							$form_content .= '<p>';
							//apply label
							if($label){
								$form_content .= '<label>'.$element['form_element_label'];
								if($element['form_element_is_required'] == 1){
									$form_content .='*';
								}
								$form_content .= '</label><br/>';
							}
							
							$first_value = true;
							$multi_value = false;
							
							$values = mysql_query('SELECT form_value_display_value,
															form_value
														FROM tbl_form_values
														WHERE form_value_form_element_FK = "'.clean($element['form_element_ID']).'"
														ORDER BY form_value_order');
														
							if(mysql_num_rows($values) > 1){
								$multi_value = true;
							}
							
							while($value = mysql_fetch_assoc($values)){
								$required = '';
								if($element['form_element_is_required'] == 1){
									$required = 'required';
								}
								
								$checked = '';
								switch($element['form_element_type_FK']){
									case 3://radio buttons
										if(isset($this->form_variables[$element['form_element_name']]) || isset($this->session_variables[$element['form_element_name']])){
											//form variables over write session variables as they represent the most recently entered data
											if(isset($this->form_variables[$element['form_element_name']]) && isset($this->session_variables[$element['form_element_name']])){
												if($this->form_variables[$element['form_element_name']] == $value['form_value']){//if current selection is this element
													$checked = 'checked';
												}
											}else{//only 1 value present
												if(isset($this->form_variables[$element['form_element_name']])){
													if($this->form_variables[$element['form_element_name']] == $value['form_value']){
														$checked = 'checked';
													}
												}else{
													if($this->session_variables[$element['form_element_name']] == $value['form_value']){
														$checked = 'checked';
													}
												}
											}
										}
									
										if($first_value){
											$first_value = false;
											
											$form_content .= '<label><input type="radio" name="'.$element['form_element_name'].'" value="'.$value['form_value'].'" '.$required.' '.$checked.'/> '.$value['form_value_display_value'].'</label>';
										}else{
											$form_content .= '<br/><label><input type="radio" name="'.$element['form_element_name'].'" value="'.$value['form_value'].'" '.$checked.'/> '.$value['form_value_display_value'].'</label>';
										}
									break;//end radio buttons
									
									case 4://checkboxes
										$array = '';
										if($multi_value){//multiple values
											$array = '[]';
											//support for checkbox group validation is not yet built into browsers so it needs to be patched in
											if($required == 'required'){
												$required = 'class="patch-checkbox-group-validation"';
											}
											
											if(isset($this->form_variables[$element['form_element_name']]) || isset($this->session_variables[$element['form_element_name']])){
												//form variables over write session variables as they represent the most recently entered data
												if(isset($this->form_variables[$element['form_element_name']]) && isset($this->session_variables[$element['form_element_name']])){
													if(in_array($value['form_value'], $this->form_variables[$element['form_element_name']])){//if current selection is the value array
														$checked = 'checked';
													}
												}else{//only 1 value present
													if(isset($this->form_variables[$element['form_element_name']])){
														if(in_array($value['form_value'], $this->form_variables[$element['form_element_name']])){
															$checked = 'checked';
														}
													}else{
														if(in_array($value['form_value'], $this->session_variables[$element['form_element_name']])){
															$checked = 'checked';
														}
													}
												}
											}
										}else{//single value
											if(isset($this->form_variables[$element['form_element_name']]) || isset($this->session_variables[$element['form_element_name']])){
												//form variables over write session variables as they represent the most recently entered data
												if(isset($this->form_variables[$element['form_element_name']]) && isset($this->session_variables[$element['form_element_name']])){
													if($this->form_variables[$element['form_element_name']] == $value['form_value']){//if current selection is this element
														$checked = 'checked';
													}
												}else{//only 1 value present
													if(isset($this->form_variables[$element['form_element_name']])){
														if($this->form_variables[$element['form_element_name']] == $value['form_value']){
															$checked = 'checked';
														}
													}else{
														if($this->session_variables[$element['form_element_name']] == $value['form_value']){
															$checked = 'checked';
														}
													}
												}
											}
										}//end single value
									
										if($first_value){
											$first_value = false;
											$form_content .= '<label><input type="checkbox" name="'.$element['form_element_name'].''.$array.'" value="'.$value['form_value'].'" '.$required.' '.$checked.'/> '.$value['form_value_display_value'].'</label>';
										}else{
											$form_content .= '<br/><label><input type="checkbox" name="'.$element['form_element_name'].''.$array.'" value="'.$value['form_value'].'" '.$required.' '.$checked.'/> '.$value['form_value_display_value'].'</label>';
										}
									break;//end checkboxes
								}
							}
							
							//finish elment
							$form_content .= '</p>';
						break;//end radio buttons and similar elements
						
						case 2://textareas
							//label
							$label = false;
							if(trim($element['form_element_label']) != ""){
								$label = true;
							}
						
							//start the element
							$form_content .= '<p>';
							//apply label
							if($label){
								$form_content .= '<label for="'.$this->form_name."_".$element_id .'">'.$element['form_element_label'];
								if($element['form_element_is_required'] == 1){
									$form_content .='*';
								}
								$form_content .= '<br/>';
							}
							
							//check for required
							$required = '';
							if($element['form_element_is_required'] == 1){
								$required = 'required';
								$required_elements_present = true;
							}
							
							//check for previously entered value
							$value = '';
							if(isset($this->form_variables[$element['form_element_name']]) || isset($this->session_variables[$element['form_element_name']])){
								//form variables over write session variables as they represent the most recently entered data
								if(isset($this->form_variables[$element['form_element_name']]) && isset($this->session_variables[$element['form_element_name']])){
									$value = $this->form_variables[$element['form_element_name']];
								}else{//only 1 value present
									if(isset($this->form_variables[$element['form_element_name']])){
										$value = $this->form_variables[$element['form_element_name']];
									}else{
										$value = $this->session_variables[$element['form_element_name']];
									}
								}
							}
							
							//build actual element
							$form_content .= '<textarea name="'.$element['form_element_name'].'" id="'.$this->form_name."_".$element_id .'" '.$required.'>'.$value.'</textarea>';
							//finish label
							if($label){
								$form_content .= '</label>';
							}
							$form_content .= '</p>';
						break;//end textarea
						
						case 6://email addresses
						case 7://url
						case 8://number
						case 9://tel
						case 10://dates
						case 1://normal text elements and similar elements
							//input type
							$type = 'text';
							switch($element['form_element_type_FK']){//type selection for modified text elements
								case 6:
									$type = 'email';
								break;
								case 7:
									$type = 'url';
								break;
								case 8:
									$type = 'number';
								break;
								case 9:
									$type = 'tel';
								break;
								case 10:
									$type = 'date';
								break;
							}
							
							//label
							$label = false;
							if(trim($element['form_element_label']) != ""){
								$label = true;
							}
							
							//placeholder
							$placeholder = '';
							if(trim($element['form_element_content']) != ""){
								$placeholder = 'placeholder="'.$element['form_element_content'].'"';
								$placeholders_present = true;
							}
							
							//regular expression pattern
							$pattern = '';
							if(trim($element['form_element_pattern']) != ""){
								$pattern = 'pattern="'.$element['form_element_pattern'].'"';
								$patterns_present = true;
							}

							//start the element
							$form_content .= '<p>';
							//apply label
							if($label){
								$form_content .= '<label for="'.$this->form_name."_".$element_id .'">'.$element['form_element_label'];
								if($element['form_element_is_required'] == 1){
									$form_content .='*';
								}
								$form_content .= '<br/>';
							}
							
							//check for required
							$required = '';
							if($element['form_element_is_required'] == 1){
								$required = 'required';
								$required_elements_present = true;
							}
							
							//check for previously entered value
							$value = '';
							if(isset($this->form_variables[$element['form_element_name']]) || isset($this->session_variables[$element['form_element_name']])){
								//form variables over write session variables as they represent the most recently entered data
								if(isset($this->form_variables[$element['form_element_name']]) && isset($this->session_variables[$element['form_element_name']])){
									$value = 'value="'.$this->form_variables[$element['form_element_name']].'"';
								}else{//only 1 value present
									if(isset($this->form_variables[$element['form_element_name']])){
										$value = 'value="'.$this->form_variables[$element['form_element_name']].'"';
									}else{
										$value = 'value="'.$this->session_variables[$element['form_element_name']].'"';
									}
								}
							}
							
							//build actual element
							$form_content .= '<input type="'.$type.'" name="'.$element['form_element_name'].'" id="'.$this->form_name."_".$element_id .'" '.$placeholder.' '.$pattern.' '.$required.' '.$value.'/>';
							//finish label
							if($label){
								$form_content .= '</label>';
							}
							$form_content .= '</p>';
						break;//end text element
						
						case 11://html code
							$form_content .= $element['form_element_content'];
						break;
					}
					
					$element_id++;
				}
			
			//build form footer
				//if this is the last page
				if($this->form_page == $this->total_pages){
					//grab form details
					$form_details = mysql_query('SELECT form_captcha_enabled
													FROM tbl_forms
													WHERE form_ID = "'.$this->form_id.'"
													LIMIT 1');
					
					$form_details = mysql_fetch_assoc($form_details);
					
					//check for captcha
					if($form_details['form_captcha_enabled'] == 1){
						//build out different captcha methods at a future date...
						
						//if a captcha value has not been defined
						if(!isset($this->session_variables['CAPTCHA'])){
							$this->session_variables['CAPTCHA'] = rand(1, 100);
						}
						
						$element_id++;
						
						$form_content .= '<p><label for="'.$this->form_name."_".$element_id.'">Please enter '.$this->session_variables['CAPTCHA'].':*<br/><input type="text" name="cc" id="'.$this->form_name."_".$element_id.'"/><label></p>';
					}
				}
				
				//if there are required elements
				if($required_elements_present){
					$form_content .= '<p>* indicates a required field.</p>';
				}
				
				$form_content .= '<input type="hidden" name="form" value="'.$this->form_name.'"/>';
				$form_content .= '<input type="hidden" name="page" value="'.$this->form_page.'"/>';
				$form_content .= '<p><input type="submit" value="Continue"/></p>';
			$form_content .= '</form>';
			
		
			
			$form_content .= '<script>';
				$form_content .= 'var custom_script_paths = Array(';
					//build array of custom scripts
					$first_script = true;
					while($script = mysql_fetch_assoc($form_scripts)){
						if($first_script){
							$first_script = false;
						}else{
							$form_content .= ',';
						}
						
						$form_content .= '"'.constant("ARCH_INSTALL_PATH").''.$script['form_script_location'].'"';
					}
				$form_content .= ');';
				$form_content .= 'var form_script_path = "'.constant("ARCH_INSTALL_PATH").'scripts/form.js";';
				$form_content .= 'var modernizr_path = "'.constant("ARCH_INSTALL_PATH").'scripts/form_modernizr.js";';
			$form_content .= '</script>';
				
			$form_content .= '<script src="'.constant("ARCH_INSTALL_PATH").'scripts/form_loader.js"></script>';
			
			echo $form_content;
		}
		
		//-------------- Display --------------//
		//function to handle the displaying of a form
		//requires: nothing
		//returns: the session variables modified by the form processing
		public function display(){
			$this->process();
			
			if($this->form_page <= $this->total_pages){
				$this->build();
			}else{
				$this->submit();
			}
			
			//return the session variables so they can be passed back to the form later
			return $this->session_variables;
		}
		
		//-------------- Process --------------//
		//function to handle processing form fields
		//requires: nothing
		//returns: nothing
		private function process(){
			//clear error state
			$this->session_variables["Error"] = "NONE";
			
			if($this->process_form){
				//grab processable elements on page
				$elements = mysql_query('SELECT form_element_ID,
												form_element_type_FK,
												form_element_name,
												form_element_label,
												form_element_pattern,
												form_element_is_required
											FROM tbl_form_elements
											WHERE form_element_form_FK = "'.clean($this->form_id).'"
												AND form_element_form_page = "'.clean($this->form_page).'"
												AND form_element_type_FK != 11
											ORDER BY form_element_order');
				
				//Process elements							
				while($element = mysql_fetch_assoc($elements)){
					$element_passes = true;
					$error_text = "";
					
					switch($element['form_element_type_FK']){
						case 5://select box
							if($element['form_element_is_required'] == 1){//if the element is required
								if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
									$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
								}else{//flag error for blank element
									$element_passes = false;
									$error_text = "Please make a choice from <em>".$element['form_element_label']."</em>";
								}
							}else{//element is not required
								if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
									$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
								}
							}
						break;
						
						case 4://checkboxes
							$multi_value = false;
							
							$values = mysql_query('SELECT form_value
														FROM tbl_form_values
														WHERE form_value_form_element_FK = "'.clean($element['form_element_ID']).'"');
														
							if(mysql_num_rows($values) > 1){
								$multi_value = true;
							}
							
							if($multi_value){//multiple values
								if($element['form_element_is_required'] == 1){//if the element is required
									if(count($this->form_variables[$element['form_element_name']]) > 0){//element is not blank store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}else{//flag error for blank element
										$element_passes = false;
										$error_text = "Please make at least one choice from <em>".$element['form_element_label']."</em>";
									}
								}else{//element is not required
									if(count($this->form_variables[$element['form_element_name']]) > 0){//element is not blank store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}
								}
							}else{//single value
								if($element['form_element_is_required'] == 1){//if the element is required
									if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}else{//flag error for blank element
										$element_passes = false;
										$error_text = "Please make a choice from <em>".$element['form_element_label']."</em>";
									}
								}else{//element is not required
									if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}
								}
							}//end singe value
						break;//end checkboxes
						
						case 3://radio buttons
							if($element['form_element_is_required'] == 1){//if the element is required
								if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
									$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
								}else{//flag error for blank element
									$element_passes = false;
									$error_text = "Please make a choice from <em>".$element['form_element_label']."</em>";
								}
							}else{//element is not required
								if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
									$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
								}
							}
						break;//end radio buttons
						
						case 2://textareas
							if($element['form_element_is_required'] == 1){//if the element is required
								if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
									$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
								}else{//flag error for blank element
									$element_passes = false;
									$error_text = "Please fill in <em>".$element['form_element_label']."</em>";
								}
							}else{//element is not required
								if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
									$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
								}
							}
						break;
						
						case 6://email addresses
						case 7://url
						case 8://number
						case 9://tel
						case 10://dates
						case 1://normal text elements and similar elements
							if(trim($element['form_element_pattern'] != "")){//if a pattern has been supplied
								if($element['form_element_is_required'] == 1){//if the element is required
									if(validateText($this->form_variables[$element['form_element_name']], "/".$element['form_element_pattern']."/", false)){//element is correctly formatted - store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}else{//element is incorrectly formatted or blank - flag it
										$element_passes = false;
										if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank
											$error_text = "Please correct the formatting of <em>".$element['form_element_label']."</em>";
										}else{//element is blank
											$error_text = "Please fill in <em>".$element['form_element_label']."</em>";
										}
									}
								}else{//element is not required
									if(validateText($this->form_variables[$element['form_element_name']], "/".$element['form_element_pattern']."/", true)){//element is correctly formatted or blank
										if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
											$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
										}
									}else{//element is not correctly formatted
										$element_passes = false;
										$error_text = "Please correct the formatting of <em>".$element['form_element_label']."</em>";
									}
								}
							}else{//if no pattern has been supplied
								if($element['form_element_is_required'] == 1){//if the element is required
									if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}else{//flag error for blank element
										$element_passes = false;
										$error_text = "Please fill in <em>".$element['form_element_label']."</em>";
									}
								}else{//element is not a required element
									if(trim($this->form_variables[$element['form_element_name']]) != ""){//element is not blank store it
										$this->session_variables[$element['form_element_name']] = $this->form_variables[$element['form_element_name']];
									}
								}//end not required
							}//end no pattern
						break;//end text element
					}//end of element type switch
					
					if(!$element_passes){//if the element failed processing
						if($this->session_variables["Error"] === "NONE"){//build initial error text
							$this->session_variables["Error"] = $error_text;
						}else{//append new error text
							$this->session_variables["Error"] .= "<br/>".$error_text;
						}
					}
				}//end of elements loop
				
				//if this is the last page check for captcha
				if($this->form_page == $this->total_pages){
					if(isset($this->session_variables['CAPTCHA'])){
						if($this->form_variables['cc'] != $this->session_variables['CAPTCHA']){
							if($this->session_variables["Error"] === "NONE"){//build initial error text
								$this->session_variables["Error"] = "Please enter ".$this->session_variables['CAPTCHA'].".";
							}else{//append new error text
								$this->session_variables["Error"] .= "<br/>Please enter ".$this->session_variables['CAPTCHA'].".";
							}
						}
					}
				}
				
				//if there are no errors on the page go to the next page
				if($this->session_variables["Error"] === "NONE"){
					$this->form_page++;
				}
			}
		}
		
		//-------------- Submit --------------//
		//function to handle submission of the form to its final desination
		//requires: nothing
		//returns: nothing
		private function submit(){
			$status_message = "";
			
			//Grab all form elements for formatting and sending. Ignore HTML elements
			$form_elements = mysql_query('SELECT form_element_ID,
												form_element_type_FK,
												form_element_form_page,
												form_element_name,
												form_element_label
											FROM tbl_form_elements
											WHERE form_element_form_FK="'.$this->form_id.'"
												AND form_element_type_FK != 11
											ORDER BY form_element_form_page,
												form_element_order');
			
			//Grab the form details
			$form_details = mysql_query('SELECT form_name,
												form_destination
											FROM tbl_forms
											WHERE form_ID="'.$this->form_id.'"
											LIMIT 1');
			$form_details = mysql_fetch_assoc($form_details);
			
			$semi_rand = md5(time());
			$mime_inner_boundary = "==Inner_Boundary_$semi_rand";
			//$mine_outer_boundary = "==Outer_Boundary_$semi_rand";
			$mime_boundary_header = chr(34).$mime_inner_boundary.chr(34);
			
			$to = clean($form_details['form_destination']);//primary recipiant
			$headers = "From: ".constant('EMAIL_NO_REPLY')."\r\n"; //from address is required
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: multipart/alternative;\n";
			$headers .= "	    boundary=".$mime_boundary_header;
			$subject = $form_details['form_name']." : Submission"; //subject line of the email
	
			$plain_text_message = "";
			$html_message = "";
			$current_page = 0;
			
			//build message
			while($element = mysql_fetch_assoc($form_elements)){
				$single_value = true;
				
				switch($element['form_element_type_FK']){
					case 4://checkboxes
						$values = mysql_query('SELECT form_value
													FROM tbl_form_values
													WHERE form_value_form_element_FK = "'.clean($element['form_element_ID']).'"');
													
						if(mysql_num_rows($values) > 1){
							$single_value = false;
						}
					break;
				}
				
				//check for new page
				if($element['form_element_form_page'] != $current_page){
					$plain_text_message .= "\r\n";
					$plain_text_message .= "------------------------------------------------------------"."\r\n";
					$plain_text_message .= "Page ".$element['form_element_form_page']."\r\n";
					$plain_text_message .= "------------------------------------------------------------"."\r\n";
					$plain_text_message .= "\r\n";
					
					$html_message .= "<h2>Page ".$element['form_element_form_page']."</h2>";
					
					$current_page = $element['form_element_form_page'];
				}
				
				if(isset($this->session_variables[$element['form_element_name']])){//ensure the field is defined
					if($single_value){
						//append field to the message with its label
						$plain_text_message .= $element['form_element_label']." ".$this->session_variables[$element['form_element_name']]."\r\n";
						$html_message .= "<p><strong>".$element['form_element_label']."</strong> ".$this->session_variables[$element['form_element_name']]."</p>";
					}else{//multi-value
						switch($element['form_element_type_FK']){
							case 4:
								$plain_text_message .= $element['form_element_label']." ";
								$html_message .= "<p><strong>".$element['form_element_label']."</strong> ";
							
								$values = $this->session_variables[$element['form_element_name']];
								$first_value = true;
								foreach($values as $value){
									if($first_value){
										$first_value = false;
									}else{
										$plain_text_message .= "\r\n";
										$html_message .= "<br/>";
									}
									
									$plain_text_message .= $value;
									$html_message .= $value;
								}
								
								$plain_text_message .= "\r\n";
								$html_message .= "</p>";
							break;//end checkboxes
						}
					}//end multi-value
				}//definition check
			}//end build message
			
			
			$message = "--$mime_inner_boundary
Content-Type: text/plain; charset=us-ascii
Content-Transfer-Encoding: 7bit

$plain_text_message

--$mime_inner_boundary
Content-Type: text/html; charset=us-ascii
Content-Transfer-Encoding: 7bit

$html_message

--$mime_inner_boundary--";
			
			//send the message
			if(mail($to, $subject, $message, $headers)){
				$status_message .= "<h1>Thank You</h1>";
				$status_message .= "<p>The form has been successfully submitted.</p>";
			}else{
				$status_message .= "<h1>Error</h1>";
				$status_message .= "<p>There was an error submitting the form. Please try again.</p>";
			}
			
			echo $status_message;
		}
		
		//-------------- Constructor --------------//
		//Builds a new Form object
		//requires: a string containing the database name of the form, an int specifying the current page of the form, the post variables submitted to the form, a session array containing the variables submitted on each page
		//optional: boolean indicating whether or not the page needs to be processed, defaults to true
		//returns: a boolean indicating whether the tested string(s) passed the regex comparison or not, for arrays a single failure will fail the whole set
		public function Form($form_name, $form_page, $form_variables, $session_variables, $process = true){
			$this->build_state = "SUCCESS";
			
			$this->process_form = $process;
			
			//checks for a numeric page number
			if(is_numeric($form_page)){
				$this->form_page = $form_page;
			}else{
				$this->build_state = "Build Failure: Invalid Page Number";
			}
			
			//ensure the build is valid before querying the database
			if($this->build_state === "SUCCESS"){
				//grab details from the database
				$form_details = mysql_query('SELECT form_ID,
													form_name,
													form_destination,
													form_captcha_enabled
												FROM tbl_forms
												WHERE form_name="'.clean($form_name).'"
												LIMIT 1');
				//ensure the form is in the database
				if(mysql_num_rows($form_details) > 0){
					$form_details = mysql_fetch_assoc($form_details);
					
					//find highest page which has an element
					$element_page = mysql_query('SELECT form_element_form_page
												FROM tbl_form_elements
												WHERE form_element_form_FK = "'.clean($form_details['form_ID']).'"
												ORDER BY form_element_form_page DESC
												LIMIT 1');
					$element_page = mysql_fetch_assoc($element_page);
					
					//set remaining object variables
					$this->form_id = $form_details['form_ID'];
					$this->form_name = $form_details['form_name'];	
					$this->form_variables = $form_variables;
					$this->session_variables = $session_variables;
					$this->total_pages = $element_page['form_element_form_page'];
				}else{
					$this->build_state = "Build Failure: Invalid Form Selection";
				}
			}
			
			
		}
	}
?>