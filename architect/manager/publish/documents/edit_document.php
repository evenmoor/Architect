<? if(validatePermissions('system', 7)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'save':
				//parse all post fields to see if there are additional fields
				foreach($_POST as $field => $value){
					${$field} = $value;
					
					//if the field isn't part of the standard document fields
					if($field != 'name' && $field != 'home' && $field != 'template' && $field != 'title' && $field != 'content' && $field != 'did' && $field != 'action'){
						//distill field id from additional field name
						$field_id = str_replace('additional_field-', '', $field);
						
						//check for exsisting value
						$value_check = mysql_query('SELECT additional_field_value_ID
												   FROM tbl_additional_field_values
												   WHERE additional_field_value_additional_field_FK="'.clean($field_id).'"
												   AND additional_field_value_document_FK="'.clean($_POST['did']).'"
												   LIMIT 1');
						
						if(mysql_num_rows($value_check) > 0){//if there is a value
							$value_check = mysql_fetch_assoc($value_check);
							mysql_query('UPDATE tbl_additional_field_values
										SET additional_field_value_en = "'.clean($value).'"
										WHERE additional_field_value_ID = "'.clean($value_check['additional_field_value_ID']).'"
										LIMIT 1');
						}else{//if there is no value
							mysql_query('INSERT INTO tbl_additional_field_values(additional_field_value_ID,
																				 additional_field_value_additional_field_FK,
																				 additional_field_value_document_FK,
																				  additional_field_value_en)
																			VALUES(NULL,
																			 "'.clean($field_id).'",
																			 "'.clean($_POST['did']).'",
																			 "'.clean($value).'")');
						}//end value update
					}//end field check
				}//end additional fields
			
				//check to see if this document will be the new home page
				if($_POST['home'] == 1){
					//clear out any current home pages that are not the current page
					mysql_query('UPDATE tbl_documents
								SET document_is_home_page = "0"
								WHERE document_is_home_page = "1"
								AND NOT document_ID = "'.clean($_POST['did']).'"');
				}
			
				$template = $group = 'NULL';
	
				if($_POST['template'] != 0){
					$template = '"'.clean($_POST['template']).'"';
				}
				
				if($_POST['group'] != 0){
					$group = '"'.clean($_POST['group']).'"';
				}
			
				//update database elements
				mysql_query('UPDATE tbl_documents
							 SET document_group_FK = '.$group.',
							 document_template_FK = '.$template.',
							 document_status_FK = "'.clean($_POST['status']).'",
							 document_is_home_page = "'.clean($_POST['home']).'",
							 document_name = "'.clean($_POST['name']).'",
							 document_title_en = "'.clean($_POST['document_title_en']).'",
							 document_content_en = "'.clean($_POST['document_content_en']).'"
							 WHERE document_ID = "'.clean($_POST['did']).'"
							 LIMIT 1');
				
				systemLog('Document updated: id# '.clean($_POST['did']).' | '.clean($_POST['name']).'.');
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	$document_contains_php = false; //boolean indicating whether or not PHP is in the document

	$document = mysql_fetch_assoc(mysql_query('SELECT * 
											  FROM tbl_documents 
											  WHERE document_ID="'.clean($_GET['d']).'" 
											  LIMIT 1'));
	
	$template_details = mysql_fetch_assoc(mysql_query('SELECT template_additional_field_group_FK,
													  template_custom_styles
													  FROM tbl_templates
													  WHERE template_ID = "'.clean($document['document_template_FK']).'"
													  LIMIT 1'));
	
	$template_custom_styles = $template_details['template_custom_styles'];
	
	$group_details = mysql_fetch_assoc(mysql_query('SELECT document_group_name,
												    document_group_additional_field_group_FK
												   	FROM tbl_document_groups 
													WHERE document_group_ID ="'.clean($document['document_group_FK']).'"
													LIMIT 1'));
	
	$additional_fields = mysql_query('SELECT additional_field_ID,
									 			additional_field_name,
												additional_field_is_required
											FROM tbl_additional_fields
											WHERE additional_field_additional_field_group_FK = "'.clean($template_details['template_additional_field_group_FK']).'"
											OR additional_field_additional_field_group_FK = "'.clean($group_details['document_group_additional_field_group_FK']).'"');
	
	//array to hold php elements in the content area
	$php_elements_array;
	
	//string to match content against
	$php_match_string = "/<\?(.*?)\?>/s";
	
	//fill array with php elements
	preg_match_all($php_match_string, $document['document_content_en'], $php_elements_array);
	
	//check for PHP in content area
	if(count($php_elements_array[1]) > 0){
		$document_contains_php = true;
	}
	
	if($document_contains_php){//switch over to legacy editor for documents with PHP in the content area
		header("Location: ".constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE")."/publish/documents/edit_document_legacy/?d=".$_GET['d']."&alert=redirect_php");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Document (<? echo $document['document_name']; ?>) | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script>
	//hot keys
	$(document).bind('keydown', 'ctrl+s', function(e){
		e.preventDefault();
	   $('#save').click();
	});
	
	$(function(){
		$('#custom_dimensions').slideUp(0);
		$('#properties').slideUp(0);
		
		$('#save').click(function(){
			//clear out previous fields
			$('#document_fields').html("");
			var element_counter = 0;
			//parse fields
			$("#responsive_wysiwyg").contents().find(".arch-content_element").each(function(){
				var element_name = $(this).attr('id');
				var element_content;
				
				//extract text from inline style elements and all paragraph tags from other elements
				if($(this).hasClass('arch-inline_element')){
					element_content = $(this).text();
				}else{
					element_content = $(this).html();
				}
				
				//add field to form
				$('#document_fields').append("<textarea name='"+element_name+"' id='field-"+element_counter+"'></textarea>");
				$('#field-'+element_counter).html(element_content);
				element_counter++;
			});
			
			//perform actual save
			$('#properties').submit();
		});
		
		$('#dimensions').change(function(){
			if($(this).val() == 'custom'){
				$('#custom_dimensions').slideDown();
			}else{
				$('#custom_dimensions').slideUp();
				var wrapper_width = $('#responsive_wrapper').width();
				var dimension_setting = $('#dimensions').val();
				if(dimension_setting == 'default'){//handle default settings
					$('#responsive_wysiwyg').css({
						'width'			:	'80%',
						'height'		:	'800px',
						'margin-left'	:	'10%'
					});
				}else{//set dimensions
					var new_dimensions = dimension_setting.split('|');
					
					var margin_offset;
					//calculate margin_offset
					margin_offset = (wrapper_width - new_dimensions[0]) / 2;
					if(margin_offset < 0){
						margin_offset = 0;
					}
					
					//resize iframe
					$('#responsive_wysiwyg').css({
						'width'			:	new_dimensions[0]+'px',
						'height'		:	new_dimensions[1]+'px',
						'margin-left'	:	margin_offset+'px'
					});
				}
			}
		}).keypress(function() { $(this).change(); });
		
		$('#properties_toggle').click(function(e){
			e.preventDefault();
			$('#properties').slideToggle();
		});
		
		$('#dimension_form').submit(function(e){
			e.preventDefault();
			
			var dimension_setting = $('#dimensions').val();
			
			if(dimension_setting == 'custom'){//handle custom settings
				var wrapper_width = $('#responsive_wrapper').width();
				var new_width = $('#custom_width').val();
				var new_height = $('#custom_height').val();
				
				var margin_offset;
				//calculate margin_offset
				margin_offset = (wrapper_width - new_width) / 2;
				if(margin_offset < 0){
					margin_offset = 0;
				}
				
				//resize iframe
				$('#responsive_wysiwyg').css({
					'width'			:	new_width+'px',
					'height'		:	new_height+'px',
					'margin-left'	:	margin_offset+'px'
				});
			}
		});
	});
</script>
<style>
	#responsive_wysiwyg, #responsive_wrapper{display:block; width:100%; height:800px; border:none; overflow:auto; position:relative;}
	#responsive_wysiwyg{width:80%; margin-left:10%;}
	#responsive_controls{float:left; clear:both; position:relative; width:100%;}
		.controls{float:left; clear:none; width:30%; margin:0 1%; padding:5px .5%; background:#ccc;}
		.controls.right{text-align:left;}
		.controls.center{text-align:center;}
</style>
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
        	<span id='field_name' class='data_field'><? echo $document['document_name']; ?></span>
        	<div id='responsive_controls'>
            	<div class='controls left'>
                	<form id='dimension_form'>
                    	<p><label for='dimensions'>Dimensions:</label> <select id='dimensions'>
                        	<option value='default'>Default</option>
                            <option value='custom'>Custom</option>
                        	<option disabled="disabled">---Landscape---</option>
                            <option value='1920|1080'>1920 x 1080</option>
                            <option value='1600|1200'>1600 x 1200</option>
                            <option value='1280|1024'>1280 x 1024</option>
                            <option value='1280|800'>1280 x 800</option>
                            <option value='1024|600'>1024 x 600</option>
                            <option value='960|640'>960 x 640</option>
                            <option value='640|480'>640 x 480</option>
                            <option value='480|320'>480 x 320</option>
                            <option value='320|240'>320 x 240</option>
                            <option disabled="disabled">---Portait---</option>
                            <option value='600|1024'>600 x 1024</option>
                            <option value='640|960'>640 x 960</option>
                            <option value='480|640'>480 x 640</option>
                            <option value='320|480'>320 x 480</option>
                        </select></p>
                        <p id='custom_dimensions'><label for='custom_width'>Width:</label> <input type='text' id='custom_width'/><br />
                        <label for='custom_height'>Height:</label> <input type='text' id='custom_height' /><br/>
                        <input type='submit' value='Update' /></p>
                    </form>
                </div><!--/left controls-->
                
                <div class='controls center'>
                	<p><input type='button' value='Save (ctrl+s)' id='save'/></p>
                </div><!--/center controls-->
                
                <div class='controls right'>
                	<p style='text-align:right;'><input type='button' id='properties_toggle' value='Document Properties'/></p>
                	<form id='properties' action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
                    	<p><label for='name'>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_name' class='help_link'>?</a></sup>
                        <br /><input type='text' id='name' name='name' value=''/></label></p>
                
                        <p><label for='status'>Status:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_status' class='help_link'>?</a></sup>
                        <br /><select name='status' id='status'>
                            <? 
                                $document_statuses = mysql_query('SELECT document_status_ID,
                                                                    document_status_en
                                                                FROM tbl_document_statuses 
                                                                ORDER BY document_status_en'); 
                            
                                while($status = mysql_fetch_assoc($document_statuses)){
                                    $selected = '';
                                    if($status['document_status_ID'] == $document['document_status_FK']){
                                        $selected = 'selected="selected"';
                                    }
                                    ?><option value='<? echo $status['document_status_ID']; ?>' <? echo $selected; ?>><? echo $status['document_status_en']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        
                        <p><label for='home'>Site Home Page?<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_set_home_page' class='help_link'>?</a></sup><br />
                         <select name='home' id='home'>
                            <?
                                $yes_selected = $no_selected = '';
                                if($document['document_is_home_page'] == 1){
                                    $yes_selected = 'selected="selected"';
                                }else{
                                    $no_selected = 'selected="selected"';
                                }
                            ?>
                            <option value='0' <? echo $no_selected; ?>>No</option>
                            <option value='1' <? echo $yes_selected; ?>>Yes</option>
                        </select></label></p>
                        <p><label for='group'>Group:<br />
                        <select name='group' id='group'>
                                                    <option value='0'>None</option>
                            <? 
                                $document_groups = mysql_query('SELECT document_group_ID,
                                                                    document_group_name
                                                                FROM tbl_document_groups
                                                                ORDER BY document_group_name'); 
                            
                                while($group = mysql_fetch_assoc($document_groups)){
                                    $selected = '';
                                    if($group['document_group_ID'] == $document['document_group_FK']){
                                        $selected = 'selected="selected"';
                                    }
                                    ?><option value='<? echo $group['document_group_ID']; ?>' <? echo $selected; ?>><? echo $group['document_group_name']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        <p><label for='template'>Template:<br />
                        	<select name='template' id='template'>
                            <option value=''>None</option>
                            <?
                                $templates = mysql_query('SELECT template_ID,
                                                                template_name
                                                            FROM tbl_templates
                                                            ORDER BY template_name');
                                while($template = mysql_fetch_assoc($templates)){
                                    $selected = '';
                                    if($template['template_ID'] == $document['document_template_FK']){
                                        $selected = 'selected="selected"';
                                    }
                                    ?><option value='<? echo $template['template_ID']; ?>' <? echo $selected; ?>><? echo $template['template_name']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        <input type='hidden' name='did' value='<? echo $document['document_ID']; ?>' />
                		<input type='hidden' name='action' value='save' />
                     	<div id='document_fields'>
                        </div><!--/document_fields-->
                    </form>
                </div><!--/right controls-->
            </div><!--/responsive_controls-->
            <?
				$group_name = $document_path = "";//the final path to load
				$group_document = false;//boolean controlling whether or not the document is part of a group
				
				//check for valid group code
				if(!is_null($document['document_group_FK']) && $document['document_group_FK'] != 0){
					$group_name = mysql_query('SELECT document_group_name
												FROM tbl_document_groups
												WHERE document_group_ID="'.clean($document['document_group_FK']).'"
												LIMIT 1'); 
					
					if(mysql_num_rows($group_name) == 1){//ensure the group still exsists
						$group_document = true;
						$group_name = mysql_fetch_assoc($group_name);
						$group_name = $group_name['document_group_name'];
					}
				}
				
				//switch to build document path							
				if($group_document){
					$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP")."/".$group_name."/".$document['document_ID']."-".$document['document_name']."/?edit_mode=true";
				}else{
					if(constant("ARCH_DOCUMENT_PATH_ID")){//handle page paths with ids
						$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE")."/".$document['document_ID']."-".$document['document_name']."/?edit_mode=true";
					}else{//handle page paths stripped of ids
						$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE")."/".$document['document_name']."/?edit_mode=true";
					}
				}
			?>
        	<div id='responsive_wrapper' class='transparent'>
                <iframe id='responsive_wysiwyg' src='<? echo $document_path; ?>' contenteditable="true">
                </iframe><!--/responsive_wysiwyg-->
            </div><!--/responsive_wrapper-->
            <p><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/edit_document_legacy/?d=<? echo $_GET['d']; ?>'>Legacy Editor</a></p>
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