<? if(validatePermissions('system', 7)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			//update privacy
			case 'alter_access':
				$groups = $_POST['privacy'];
				$privacy_string = "";
				foreach($groups as $group){
					if($privacy_string != ""){
						$privacy_string.=":";
					}
					
					$privacy_string.=$group;
					
					//update privacy elements
					mysql_query('UPDATE tbl_documents
								 SET document_privacy_list = "'.clean($privacy_string).'"
								 WHERE document_ID = "'.clean($_POST['did']).'"
								 LIMIT 1');
					
					systemLog('Document privacy updated: id# '.clean($_POST['did']).' | '.clean($_POST['name']).'.');
				}
			break;
			
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
							 document_title_en = "'.clean($_POST['title']).'",
							 document_content_en = "'.clean($_POST['content']).'"
							 WHERE document_ID = "'.clean($_POST['did']).'"
							 LIMIT 1');
				
				systemLog('Document updated: id# '.clean($_POST['did']).' | '.clean($_POST['name']).'.');
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
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
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Document (<? echo $document['document_name']; ?>) | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? $legacy = true; ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script>
	//hot keys
	$(document).bind('keydown', 'ctrl+s', function(e){
		e.preventDefault();
	   $('#document_form').submit();
	});
	
	$(function(){
		$('#change_privacy_form').slideUp(0);
		
		$('#change_privacy').click(function(e){
			e.preventDefault();
			$('#change_privacy_form').slideToggle();
		});
	});
</script>
<style type='text/css'>
	.dual_column{margin-bottom:10px; margin-right:1%; width:72%; background:#eee;}
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
        	<? 
				if(isset($_GET['alert'])){
					switch($_GET['alert']){
						case "redirect_php":
							?><h6 class='warning'>Warning: This page contains PHP code. Please use the legacy editor to prevent loss of code.</h6><?
						break;
					}
				}
			?>
            
        	
            <?
				$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$document['document_ID'].'-'.$document['document_name'].'/';
				
				if($group_details['document_group_name']){
					$group_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group_details['document_group_name'].'/';
					$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group_details['document_group_name'].'/'.$document['document_ID'].'-'.$document['document_name'].'/';
				}
			?>
            
            <p class='right'><a class='button' href='<? echo $document_path; ?>'>view document</a></p>
            <? if($group_details['document_group_name']){ ?>
            <p class='right'><a class='button' href='<? echo $group_path; ?>'>view document group</a></p>
            <? } ?>
            
            <h1><? echo $document['document_name']; ?></h1>
            
            <span id='field_name' class='data_field'><? echo $document['document_name']; ?></span>
            <span id='field_document_title' class='data_field'><? echo $document['document_title_en']; ?></span>

        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='document_form'>
            	<div class='dual_column'>
                    <h2>Document Content</h2>
                    <p><label>Title:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_title' class='help_link'>?</a></sup><br /><input type='text' id='document_title' name='title' value=''/></label></p>
                    <!--  -->
                    <p><label>Content:</label><br/><textarea id='content' name='content' style='width:100%; height:400px;' class='ui_wysiwyg' >
                        <? echo htmlspecialchars($document['document_content_en']); ?>
                    
                    </textarea></p>
                    <div id='document_additional_fields'>
                        <?
                            while($field = mysql_fetch_assoc($additional_fields)){//parse fields
                                $required = $value = '';
                                if($field['additional_field_is_required'] == "1"){//handle required fields
                                    $required = '*';
                                }
                                
                                $value_check = mysql_query('SELECT additional_field_value_en
                                                           FROM tbl_additional_field_values
                                                           WHERE additional_field_value_additional_field_FK = "'.clean($field['additional_field_ID']).'"
                                                           AND additional_field_value_document_FK = "'.clean($document['document_ID']).'"
                                                           LIMIT 1');
                                
                                if(mysql_num_rows($value_check) > 0){//if the field has a value show it.
                                    $value = mysql_fetch_assoc($value_check);
                                    $value = $value['additional_field_value_en'];
                                }
                                
                                ?><p><label><? echo $field['additional_field_name']; ?><? echo $required; ?>:<br /><textarea name='additional_field-<? echo $field['additional_field_ID']; ?>' ><? echo $value; ?></textarea></label></p><?
                            }
                        ?>
                        <p>* indicates a required field.</p>
                    </div>
                    <input type='hidden' name='did' value='<? echo $document['document_ID']; ?>' />
                    <input type='hidden' name='action' value='save' />
                    <p><input type='submit' value='Save (ctrl+s)' /></p>
                </div><!--/dual_column-->
                
                <div class='quad_column'>
                	<h2>Document Properties</h2>
                    <p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_name' class='help_link'>?</a></sup><br /><input type='text' id='name' name='name'/></label></p>
                    
                    <p><label>Status:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_status' class='help_link'>?</a></sup><br /><select name='status'>
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
                    
                    <p><label>Site Home Page?<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_set_home_page' class='help_link'>?</a></sup><br /> <select name='home'>
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
                    <p><label>Group:<br /><select name='group'>
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
                    <p><label>Template:<br /><select name='template'>
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
                    
                    </form>
                    
                    <h2>Document Privacy:</h2>
                    	<? if(trim($document['document_privacy_list']) == ""){ ?>
                    		<p>Everyone may access this document.</p>
                        <? }else{ ?>
                        	<p>Access is limited to*: 
                        	<?
								$privacy_list = explode(":", $document['document_privacy_list']);
								sort($privacy_list);
								$first_group = true;
								foreach($privacy_list as $group){
									if(!$first_group){
										echo ", ";
									}
									
									echo $group;
									$first_group = false;
								}
							?>
                            </p>
                            <p><em>* Note: Site Administrator have access to all areas of the site even if not explicitly allowed here.</em></p>
                        <? } ?>
                        
                        <h3><a href='' id='change_privacy'>Alter Access</a></h3>
                        <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='change_privacy_form'>
							<?
								$groups = mysql_query('SELECT user_group_name
														FROM tbl_user_groups
														ORDER BY user_group_name');
								
								$counter = 1;
								
								while($group = mysql_fetch_assoc($groups)){
									$odd = '';
									if($counter  % 2 != 0){
										$odd = 'odd';
									}
									
									$checked = '';
									if(in_array($group['user_group_name'], $privacy_list)){
										$checked = 'checked="checked"';
									}
																		
									?><div class='table_row <? echo $odd; ?>'>
                                    	<label><span class='table_column' style='width:10%;'><input type='checkbox' value='<? echo $group['user_group_name']; ?>' name='privacy[]' <? echo $checked; ?>/></span>
                                        <span class='table_column' style='width:90%;'><? echo $group['user_group_name']; ?></span></label>
                                    </div><?
									
									$counter++;
                                    
								}
							?>
                            
                        	<input type='hidden' name='did' value='<? echo $document['document_ID']; ?>' />
                            <input type='hidden' name='action' value='alter_access' />
                            <p><input type='submit' value='Limit Access to Selected Groups' /></p>
                        </form>
                    </ul>
                    
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