<? if(validatePermissions('system', 16)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	//check for passed actions
	if(isset($_POST['action'])){
		switch($_POST['action']){
			case "add_element_value":
				mysql_query('INSERT INTO tbl_form_values(form_value_ID,
														form_value_form_element_FK,
														form_value_display_value,
														form_value,
														form_value_order)
													VALUES(NULL,
														"'.clean($_POST['id']).'",
														"'.clean($_POST['form_value_display_value']).'",
														"'.clean($_POST['form_value']).'",
														NULL)');
				
				$value_id = mysql_insert_id();
				
				systemLog('Element value id# '.$value_id.' added to form id# '.clean($_POST['form']).'.');
			break;//add element value
			
			case "update_element_value":
			break;//update element value
			
			case "remove_element_value":
				mysql_query('DELETE FROM tbl_form_values
									WHERE form_value_ID="'.clean($_POST['id']).'"
									LIMIT 1');
				
				systemLog('Element value id# '.$_POST['id'].' removed from form id# '.clean($_POST['form']).'.');
			break;//remove element value
			
			case "update_element":
				switch($_POST['type']){
					case 6://email addresses
					case 7://url
					case 8://number
					case 9://tel
					case 10://dates
					case 1://normal text elements and similar elements
						$required = 0;
						if($_POST['form_element_is_required']){
							$required = 1;
						}
						
						$pattern = $_POST['form_element_pattern'];
						
						switch($pattern){
							case "Basic Date: MM/DD/YYYY":
								$pattern = '(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d';
							break;
							case "Advanced Date: MM/DD/YYYY":
								$pattern = '(?:(?:0[1-9]|1[0-2])[\/\\-. ]?(?:0[1-9]|[12][0-9])|(?:(?:0[13-9]|1[0-2])[\/\\-. ]?30)|(?:(?:0[13578]|1[02])[\/\\-. ]?31))[\/\\-. ]?(?:19|20)[0-9]{2}';
							break;
							case "Advanced Date: WC3 Datetime":
								$pattern = '/([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/';
							break;
							case "Postal Code - America: XXXXX or XXXXX-XXXX":
								$pattern = '(\d{5}([\-]\d{4})?)';
							break;
							case "Phone Number - America: XXX-XXX-XXXX":
								$pattern = '\d{3}[\-]\d{3}[\-]\d{4}';
							break;
							case "Domain: domain.ext":
								$pattern = '^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$';
							break;
							case "Number whole or decimal: XXXXXXX or XXXX.XXXXXX":
								$pattern = '[-+]?[0-9]*[.,]?[0-9]+';
							break;
							case "Price: X.XX":
								$pattern = '\d+(\.\d{2})?';
							break;
						}
					
						mysql_query('UPDATE tbl_form_elements
										SET form_element_name="'.clean($_POST['form_element_name']).'",
											form_element_label="'.clean($_POST['form_element_label']).'",
											form_element_content="'.clean($_POST['form_element_content']).'",
											form_element_pattern="'.clean($pattern).'",
											form_element_is_required="'.$required.'"
										WHERE form_element_ID="'.clean($_POST['id']).'"
										LIMIT 1');
					break;//text and similar elements
					
					case 4://checkboxes
					case 3://radio buttons
					case 5://select box
					case 2://textareas
						mysql_query('UPDATE tbl_form_elements
										SET form_element_name="'.clean($_POST['form_element_name']).'",
											form_element_label="'.clean($_POST['form_element_label']).'",
											form_element_is_required="'.$required.'"
										WHERE form_element_ID="'.clean($_POST['id']).'"
										LIMIT 1');
					break;
					
					case 11://html elements
						mysql_query('UPDATE tbl_form_elements
										SET form_element_content="'.clean($_POST['form_element_content']).'"
										WHERE form_element_ID="'.clean($_POST['id']).'"
										LIMIT 1');
					break;
				}
				
				systemLog('Element id# '.$_POST['id'].' updated.');
			break;//update element
			
			case "add_element":
				mysql_query('INSERT INTO tbl_form_elements(form_element_ID,
															form_element_form_FK,
															form_element_form_page,
															form_element_type_FK,
															form_element_is_required,
															form_element_order)
														VALUES(NULL,
															"'.clean($_POST['form']).'",
															"'.clean($_POST['page']).'",
															"'.clean($_POST['type']).'",
															0,
															"'.clean($_POST['last_position']).'")');
				
				$element_id = mysql_insert_id();
				
				systemLog('Element id# '.$element_id.' added to form id# '.clean($_POST['form']).'.');
			break;//add element
			
			case "remove_element":
				mysql_query('DELETE FROM tbl_form_elements
									WHERE form_element_ID="'.clean($_POST['id']).'"
									LIMIT 1');
				
				systemLog('Element id# '.$_POST['id'].' removed.');
				
				mysql_query('OPTIMIZE TABLE tbl_form_elements');
			break;//remove element
			
			case "move_down":
			case "move_up":
				mysql_query('UPDATE tbl_form_elements
										SET form_element_order="'.clean($_POST['new_position']).'"
										WHERE form_element_ID="'.clean($_POST['id']).'"
										LIMIT 1');
										
				systemLog('Element id# '.$_POST['id'].' moved.');
			break;//end movement
			
			case "update_properties":
				mysql_query('UPDATE tbl_forms
								SET form_name = "'.clean($_POST['name']).'",
									form_destination = "'.clean($_POST['delivery']).'",
									form_captcha_enabled = "'.clean($_POST['captcha']).'"
								WHERE form_ID = "'.clean($_POST['form']).'"
								LIMIT 1');
								
				systemLog('Form updated id# '.clean($_POST['form']).'.');
			break;//update form properties
			
			case "add_page":
				mysql_query('UPDATE tbl_forms
								SET form_total_pages = form_total_pages + 1
								WHERE form_ID = "'.clean($_POST['form']).'"
								LIMIT 1');
								
				systemLog('Page added to form id# '.clean($_POST['form']).'.');
			break;
			
			case "add_script":
				mysql_query('INSERT INTO tbl_form_scripts(form_script_ID,
															form_script_form_FK,
															form_script_location)
														VALUES(NULL,
															"'.$_POST['form'].'",
															"'.$_POST['script_location'].'")');
				
				$script_id = mysql_insert_id();
				
				systemLog('Script id# '.$script_id.' added to form id# '.clean($_POST['form']).'.');
			break;//add script
			
			case "remove_scripts":
				$scripts = $_POST['scripts'];
				
				foreach($scripts as $script){
					mysql_query('DELETE FROM tbl_form_scripts
									WHERE form_script_ID="'.clean($script).'"
									LIMIT 1');
					
					systemLog('Script id# '.$script.' removed from form id# '.clean($_POST['form']).'.');
				}
			break;//remove scripts
		}
	}

	$form = mysql_fetch_assoc(mysql_query('SELECT * FROM tbl_forms WHERE form_ID="'.clean($_GET['f']).'" LIMIT 1'));
	
	$available_elements = mysql_query('SELECT *
										FROM tbl_form_element_types
										ORDER BY form_element_type');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Form (<? echo $form['form_name']; ?>) | Form | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<style type='text/css'>
	ul.unmarked{list-style:none;}
	.dual_column{margin-bottom:10px; margin-right:1%; width:72%; background:#eee;}
		.dual_column input[type="text"], .dual_column textarea{width:75%;}
	
	.form_page_header{background:#3a74b5; margin-top:5px; float:left; clear:both; width:100%;
		border-radius:5px;
		transition:all .5s;}
	.form_page_header:first{margin-top:0;}
	.form_page_header.active{text-align:center;
		border-radius:5px 5px 0 0;}
	.form_page_header a{color:#ffffff; padding:0 1%; width:98%;}
	
	.form_page_header a:hover{color:#cccccc;}
	.form_page{float:left; clear:both; width:100%; background:#3a74b5;
		border-radius:0 0 5px 5px;}
	
	.move_up_form, .move_up_form p, .move_up_form input, .move_down_form, .move_down_form p, .move_down_form input, .remove_element_form, .remove_element_form p, .remove_element_form input{display:inline; padding:0; margin:0;}
	.move_up_form, .move_down_form{position:absolute; right:1em; top:5px;}
	.move_up_form{right:8.5em;}
	
	.remove_element_form{position:absolute; right:1em; bottom:5px;}
	
	.add_element_form{float:left; clear:both; width:50%; margin:5px 22.5%; background:#cae1fb; padding:5px 2.5%;
		border-radius:5px;}
	
	.form_element{float:left; clear:both; width:96%; margin:5px 1%; border:1px solid #000; position:relative; left:-1px; padding:5px 1%; background:#fff;
		border-radius:5px;}
	.form_element:nth-child(odd){background:#ddd;}
</style>
<script>
	$(function(){
		$('.form_page').slideUp(0);
		
		//handle field previews
		$('a.preview_toggle').click(function(e){
			e.preventDefault();
			$(this).parent().next('.field_preview').slideToggle();
			if($(this).html() == "Preview -"){
				$(this).html("Preview +");
			}else{
				$(this).html("Preview -");
			}
		});
		
		$('.form_page_header a').click(function(e){
			e.preventDefault();
			
			//toggle the active state
			$(this).parent().toggleClass('active');
			//toggle the form page
			$(this).parent().next('.form_page').slideToggle();
		});
		
		$('.form_page_header a').first().click();
	});
</script>
</head>

<body>
	<div id='page'>
    	<div id='header'>
        	<div id='user_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/user_navigation.php'); ?>
            </div><!--/user_navigation-->
            <div id='primary_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/primary_navigation.php'); ?>
            </div><!--/primary_navigation-->
            <img src='<? echo constant("ARCH_INSTALL_PATH"); ?>themes/base/images/arch_title.png' alt='Architect' id='title'/>
        </div><!--/header-->
        
        <div id='sub_navigation'>
        	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/sub_navigation.php'); ?>
        </div><!--/sub_navigation-->
        
        <div class='content'>
        	<div class='single_column'>
        		<h1>Edit Form: <? echo $form['form_name']; ?></h1>
            </div>

            <div class='dual_column'>
            	<datalist id="patterns">
                	<!--
                    	Patterns courtesy of http://html5pattern.com/
                    -->
                    <option value="Basic Date: MM/DD/YYYY">
                    <option value="Advanced Date: MM/DD/YYYY">
                    <option value="Advanced Date: WC3 Datetime">
                    <option value="Postal Code - America: XXXXX or XXXXX-XXXX">
                    <option value="Phone Number - America: XXX-XXX-XXXX">
                    <option value="Domain: domain.ext">
                    <option value="Number whole or decimal: XXXXXXX or XXXX.XXXXXX">
                    <option value="Price: X.XX">
                </datalist>
            
                <?
                    for($loop_counter = 1; $loop_counter <= $form['form_total_pages']; $loop_counter++){
                        ?>
                            <h3 class='form_page_header'><a href='' id='page_header_<? echo $loop_counter; ?>'>Page <? echo $loop_counter; ?></a></h3>
                            <div class='form_page' id='page_<? echo $loop_counter; ?>'>
                                <?
                                    $last_position = 50;
                                
                                    $elements_on_page = mysql_query('SELECT *
                                                                        FROM tbl_form_elements
                                                                        WHERE form_element_form_FK = "'.clean($form['form_ID']).'"
                                                                            AND form_element_form_page = "'.clean($loop_counter).'"
                                                                        ORDER BY form_element_order');
                                                                        
                                    while($element = mysql_fetch_assoc($elements_on_page)){
                                        ?>
                                            <div class='form_element'>
                                                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='remove_element_form'>
                                                    <input type='hidden' name='action' value='remove_element'/>
                                                    <input type='hidden' name='id' value='<? echo $element['form_element_ID']; ?>'/>
                                                    <p><input type='submit' value='Remove Field'/></p>
                                                </form>
                                                
                                                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='move_up_form'>
                                                    <input type='hidden' name='action' value='move_up'/>
                                                    <input type='hidden' name='new_position' value='<? echo ($element['form_element_order'] - 1); ?>'/>
                                                    <input type='hidden' name='id' value='<? echo $element['form_element_ID']; ?>'/>
                                                    <p><input type='submit' value='Move Up'/></p>
                                                </form>
                                            
                                                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='move_down_form'>
                                                    <input type='hidden' name='action' value='move_down'/>
                                                    <input type='hidden' name='new_position' value='<? echo ($element['form_element_order'] + 1); ?>'/>
                                                    <input type='hidden' name='id' value='<? echo $element['form_element_ID']; ?>'/>
                                                    <p><input type='submit' value='Move Down'/></p>
                                                </form>
                                            
                                                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='update_field_form'>
                                                    <?
                                                        switch($element['form_element_type_FK']){
															case 4://checkboxes
															case 3://radio buttons
																?>
                                                                	<p>Name:<br/>
                                                                    <input type='text' name='form_element_name' value='<? echo $element['form_element_name']; ?>'/></p>
																	<p>Label:<br/>
                                                                    <input type='text' name='form_element_label' value='<? echo $element['form_element_label']; ?>'/></p>
                                                                    <?
																		$checked = '';
																		if($element['form_element_is_required'] == 1){
																			$checked = 'checked="checked"';
																		}
																	?>
                                                                    <p><input type='checkbox' name='form_element_is_required' <? echo $checked; ?>/> Element is required.</p>
                                                                <?
															break;//check boxes and radio buttons
															
															case 5://select box
																?>
                                                                	<p>Name:<br/>
                                                                    <input type='text' name='form_element_name' value='<? echo $element['form_element_name']; ?>'/></p>
																	<p>Label:<br/>
                                                                    <input type='text' name='form_element_label' value='<? echo $element['form_element_label']; ?>'/></p>
                                                                    <?
																		$checked = '';
																		if($element['form_element_is_required'] == 1){
																			$checked = 'checked="checked"';
																		}
																	?>
                                                                    <p><input type='checkbox' name='form_element_is_required' <? echo $checked; ?>/> Element is required.</p>
                                                                <?
															break;//select boxes
															
															case 2://textareas
																?>
                                                                	<p>Name:<br/>
                                                                    <input type='text' name='form_element_name' value='<? echo $element['form_element_name']; ?>'/></p>
																	<p>Label:<br/>
                                                                    <input type='text' name='form_element_label' value='<? echo $element['form_element_label']; ?>'/></p>
                                                                    <?
																		$checked = '';
																		if($element['form_element_is_required'] == 1){
																			$checked = 'checked="checked"';
																		}
																	?>
                                                                    <p><input type='checkbox' name='form_element_is_required' <? echo $checked; ?>/> Element is required.</p>
                                                                    
                                                                    <p><a href='' class='preview_toggle'>Preview -</a></p>
                                                                    <p class='field_preview'>
																	<? if($element['form_element_label'] != ""){ ?>
                                                                        <label><? echo $element['form_element_label']; ?>
                                                                        <? if($element['form_element_is_required'] == 1){
                                                                            ?>*<?
                                                                        }?>
                                                                        <br/>
                                                                    <? } ?>
                                                                        <textarea></textarea>
                                                                    
                                                                    <? if($element['form_element_label'] != ""){ ?>
                                                                        </label>
                                                                    <? } ?>
                                                                    </p>
                                                                <?
															break;//textarea
															
															case 6://email addresses
															case 7://url
															case 8://number
															case 9://tel
															case 10://dates
															case 1://normal text elements and similar elements
																?>
                                                                	<p>Name:<br/>
                                                                    <input type='text' name='form_element_name' value='<? echo $element['form_element_name']; ?>'/></p>
																	<p>Label:<br/>
                                                                    <input type='text' name='form_element_label' value='<? echo $element['form_element_label']; ?>'/></p>
                                                                    <p>Placeholder:<br/>
                                                                    <input type='text' name='form_element_content' value='<? echo $element['form_element_content']; ?>'/></p>
                                                                    
                                                                    <p>Validation Pattern:<br/>
                                                                    <input type='text' name='form_element_pattern' list='patterns' value='<? echo $element['form_element_pattern']; ?>'/></p>
                                                                    <?
																		$checked = '';
																		if($element['form_element_is_required'] == 1){
																			$checked = 'checked="checked"';
																		}
																	?>
                                                                    <p><input type='checkbox' name='form_element_is_required' <? echo $checked; ?>/> Element is required.</p>
                                                                    <p><a href='' class='preview_toggle'>Preview -</a></p>
																<?
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
																?>
                                                                <p class='field_preview'>
                                                                <? if($element['form_element_label'] != ""){ ?>
                                                                	<label><? echo $element['form_element_label']; ?>
                                                                    <? if($element['form_element_is_required'] == 1){
																		?>*<?
																	}?>
                                                                    <br/>
                                                                <? } ?>
                                                                    <input type='<? echo $type; ?>' <? if($element['form_element_pattern'] != ""){ ?> pattern="<? echo $element['form_element_pattern']; ?>" <? } ?> <? if($element['form_element_content'] != ""){ ?> placeholder="<? echo $element['form_element_content']; ?>"  <? } ?> />
                                                                
                                                                <? if($element['form_element_label'] != ""){ ?>
                                                                	</label>
																<? } ?>
                                                                </p>
															<?
															break;//end text like elements 
															
                                                            case 11://html elements
                                                                ?><p>Content:</p><?
																?><textarea name='form_element_content' style='width:100%; height:400px;' class='ui_wysiwyg' >
                                                                    <? echo htmlspecialchars($element['form_element_content']); ?>
                                                                </textarea><?
                                                            break;//end html element
                                                        }
                                                    ?>
                                                    
                                                    <input type='hidden' name='action' value='update_element'/>
                                                    <input type='hidden' name='type' value='<? echo $element['form_element_type_FK']; ?>'/>
                                                    <input type='hidden' name='id' value='<? echo $element['form_element_ID']; ?>'/>
                                                    <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                                                    <input type='hidden' name='page' value='<? echo $loop_counter; ?>'/>
                                                    <p><input type='submit' class='update_button' value='Update Field'/></p>
                                                </form>
                                                
                                                <?
													switch($element['form_element_type_FK']){
															case 4://checkboxes
															case 3://radio buttons
															case 5://select box	
																?><h3>Current Field Values</h3><?
																
																$element_values = mysql_query('SELECT form_value_ID,
																										form_value_display_value,
																										form_value
																									FROM tbl_form_values
																									WHERE form_value_form_element_FK = "'.$element['form_element_ID'].'"
																									ORDER BY form_value_order');
																				
																?><table><?
																	?><tr>
                                                                    	<th>Action</th>
                                                                    	<th>Display Value</th>
                                                                        <th>Submission Value</th>
																	</tr><?
																	
																	while($value = mysql_fetch_assoc($element_values)){
																		?>
																		<tr>
                                                                        	<td>
                                                                            <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='remove_element_value_form'>
                                                                            	<input type='hidden' name='action' value='remove_element_value'/>
                                                                                <input type='hidden' name='id' value='<? echo $value['form_value_ID']; ?>'/>
                                                                                <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                                                                                <input type='submit' class='update_button' value='Remove'/>
                                                                            </form>
                                                                            </td>
                                                                            <td><? echo $value['form_value_display_value']; ?></td>
                                                                            <td><? echo $value['form_value']; ?></td>
                                                                        </tr>
																		<?
																	}
																	
																	?>
																		<form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='add_element_value_form'>
                                                                        	<input type='hidden' name='action' value='add_element_value'/>
                                                                            <input type='hidden' name='id' value='<? echo $element['form_element_ID']; ?>'/>
                                                                            <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                                                                        	<tr>
                                                                            	<td><input type='submit' class='update_button' value='Add'/></td>
                                                                                <td><input type='text' name='form_value_display_value'/></td>
                                                                                <td><input type='text' name='form_value'/></td>
                                                                            </tr>
																		</form>
																	<?
																?></table><?
																
																switch($element['form_element_type_FK']){
																	case 3://radio buttons
																	case 4://checkboxes	
																		?><p><a href='' class='preview_toggle'>Preview -</a></p>
																		<p class='field_preview'>
																		<? if($element['form_element_label'] != ""){ ?>
																			<label><? echo $element['form_element_label']; ?>
                                                                            </label>
																			<? if($element['form_element_is_required'] == 1){
																				?>*<?
																			}?>
																			<br/>
																		<? } ?>
																			
																		<?
                                                                            $element_values = mysql_query('SELECT form_value_display_value,
                                                                                                                    form_value
                                                                                                                FROM tbl_form_values
                                                                                                                WHERE form_value_form_element_FK = "'.$element['form_element_ID'].'"
                                                                                                                ORDER BY form_value_order');
                                                                            $first_value = true;                                    
                                                                            while($value = mysql_fetch_assoc($element_values)){
																				if($first_value){
																					$first_value = false;
																				}else{
																					?><br/><?
																				}
																				if($element['form_element_type_FK'] == 4){
																					?><label><input type='checkbox' value='<? echo $value['form_value']; ?>'/> <? echo $value['form_value_display_value']; ?></label><?
																				}else{
                                                                                	?><label><input type='radio' value='<? echo $value['form_value']; ?>'/> <? echo $value['form_value_display_value']; ?></label><?
																				}
                                                                            }
                                                                        ?>
																		
																		</p><?
																	break;
																	
																	case 5://select box	
																		?><p><a href='' class='preview_toggle'>Preview -</a></p>
																		<p class='field_preview'>
																		<? if($element['form_element_label'] != ""){ ?>
																			<label><? echo $element['form_element_label']; ?>
																			<? if($element['form_element_is_required'] == 1){
																				?>*<?
																			}?>
																			<br/>
																		<? } ?>
																			
																			<select>
																				<?
																					$element_values = mysql_query('SELECT form_value_display_value,
																															form_value
																														FROM tbl_form_values
																														WHERE form_value_form_element_FK = "'.$element['form_element_ID'].'"
																														ORDER BY form_value_order');
																														
																					while($value = mysql_fetch_assoc($element_values)){
																						?><option value='<? echo $value['form_value']; ?>'><? echo $value['form_value_display_value']; ?></option><?
																					}
																				?>
																			</select>
																		
																		<? if($element['form_element_label'] != ""){ ?>
																			</label>
																		<? } ?>
																		</p><?
																	break;//checkboxes
																}
															break;
													}
												?>
                                            </div>
                                        <?
                                    }
                                ?>
                            
                                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='add_element_form'>
                                	<h2>Add Element</h2>
                                    <p>Type:<br/><select name='type'>
                                        <? 
                                        
                                            mysql_data_seek($available_elements, 0);
                                            while($element = mysql_fetch_assoc($available_elements)){
                                                ?><option value='<? echo $element['form_element_type_ID']; ?>'><? echo $element['form_element_type']; ?></option><?
                                            }
                                        ?>
                                    </select></p>
                                    
                                    <input type='hidden' name='action' value='add_element'/>
                                    <input type='hidden' name='last_position' value='<? echo $last_position + 1; ?>'/>
                                    <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                                    <input type='hidden' name='page' value='<? echo $loop_counter; ?>'/>
                                    <p><input type='submit' value='Add'/></p>
                                </form>
                            </div><!--/form_page-->
                        <?
                    }
                ?>
                
                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>' class='add_page'>
                    <input type='hidden' name='action' value='add_page'/>
                    <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                    <p><input type='submit' value='Add New Page'/></p>
                </form>
            </div><!--/dual_column-->
            
            <div class='quad_column'>
                <h2>Form Properties</h2>
                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>'>
                    <p>Name:<br /><input type='text' name='name' value='<? echo $form['form_name']; ?>'/></p>
                    <p>Deliver to:<br/><input type='text' name='delivery' value='<? echo $form['form_destination']; ?>'/></p>
                    <?
                        $yes_checked = $no_checked = "";
                        if($form['form_captcha_enabled'] == 1){
                            $yes_checked = 'selected="selected"';
                        }else{
                            $no_checked = 'selected="selected"';
                        }
                    ?>
                    <p>CAPTCHA enabled:<br/><select name='captcha'>
                        <option value='0' <? echo $no_checked; ?>>No</option>
                        <option value='1' <? echo $yes_checked; ?>>Yes</option>
                    </select></p>
                    <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                    <input type='hidden' name='action' value='update_properties'/>
                    <p><input type='submit' value='Save'/></p>
                </form>
                
                <h2>Supporting Scripts</h2>
                <?
                    $supporting_scripts = mysql_query('SELECT form_script_ID,
                                                                form_script_location
                                                            FROM tbl_form_scripts
                                                            WHERE form_script_form_FK = "'.clean($form['form_ID']).'"');
                ?>
                <? if(mysql_num_rows($supporting_scripts) > 0) { ?>
                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>'>
                    <ul class='unmarked'>
                        <? while($script = mysql_fetch_assoc($supporting_scripts)){ ?>
                            <li><label><input type='checkbox' name='scripts[]' value='<? echo $script['form_script_ID']; ?>'/> <? echo $script['form_script_location']; ?></label></li>
                        <? } ?>
                    </ul>
                    
                    <input type='hidden' name='action' value='remove_scripts'/>
                    <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                    <p><input type='submit' value='Deleted Checked Files'/></p>
                </form>
                <? } ?>
                
                <form method='post' action='<? echo $_SERVER['REQUEST_URI']; ?>'>
                    <p>Script Location: <input type='text' name='script_location'/></p>
                    
                    <input type='hidden' name='action' value='add_script'/>
                    <input type='hidden' name='form' value='<? echo $form['form_ID']; ?>'/>
                    <p><input type='submit' value='Add Script'/></p>
                </form>
                
                <h2>Test Form</h2>
                <p>Test the form <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/forms/test_form/?f=<? echo $form['form_ID']; ?>'>here</a>.</p>
        	</div><!--/quad_column-->
        </div><!--/content-->
        
        <div id='footer'>
        	<div id='version'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/version.php'); ?>
            </div><!--/version-->
            
            <div id='footer_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer_navigation.php'); ?>
            </div><!--/footer_navigaiton-->
        </div><!--/footer-->
    </div><!--/page-->
</body>
</html>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer.php'); ?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>