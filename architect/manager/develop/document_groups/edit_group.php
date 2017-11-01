<? if(validatePermissions('system', 12)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'save':
				$single_template = $group_template = $additional_fields = 'NULL';

				if($_POST['template-single'] != 0){
					$single_template = '"'.clean($_POST['template-single']).'"';
				}
				
				if($_POST['template-group'] != 0){
					$group_template = '"'.clean($_POST['template-group']).'"';
				}
				
				if($_POST['additional_fields'] != 0){
					$additional_fields = '"'.clean($_POST['additional_fields']).'"';
				}
				
				mysql_query('UPDATE tbl_document_groups
							SET document_group_template_FK='.$group_template.',
							document_group_single_item_template_FK='.$single_template.',
							document_group_additional_field_group_FK='.$additional_fields.',
							document_group_name="'.clean($_POST['name']).'"
							WHERE document_group_ID="'.clean($_POST['gid']).'"
							LIMIT 1');
				
				systemLog('document group updated id# '.clean($_POST['gid']).'.');
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	$group = mysql_fetch_assoc(mysql_query('SELECT * 
										   FROM tbl_document_groups 
										   WHERE document_group_ID="'.clean($_GET['g']).'" 
										   LIMIT 1'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Document Group (<? echo $group['document_group_name']; ?>) | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
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
        	<h1><? echo $group['document_group_name']; ?></h1>
            <?
				$group_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group['document_group_name'].'/';
			?>
            <p class='right'><a class='button' href='<? echo $group_path; ?>'>view document group</a></p>
            <?
				$templates = mysql_query('SELECT template_ID,
										 template_name
										 FROM tbl_templates
										 ORDER BY template_name');
				
				$additional_fields = mysql_query('SELECT additional_field_group_ID,
												 additional_field_group_name
												 FROM tbl_additional_field_groups
												 ORDER BY additional_field_group_name');
			?>
            
        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
            	<h2>Document Group Properties</h2>
            	<p>Name:<br />
                <input type='text' name='name' value='<? echo $group['document_group_name']; ?>'/></p>
                <p><label>Group Template:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=group_template' class='help_link'>?</a> <br />
                <select name='template-group'>
                    <option value='0'>Select One</option>
                    <?
                        while($template = mysql_fetch_assoc($templates)){
							$selected = '';
							if($template['template_ID'] == $group['document_group_template_FK']){
								$selected = 'selected="selected"';
							}
                            ?><option value='<? echo $template['template_ID']; ?>' <? echo $selected; ?>><? echo $template['template_name']; ?></option><?
                        }
                    ?>
                </select></label></p>
                <p><label>Single Template:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=group_single_template' class='help_link'>?</a><br />
                <select name='template-single'>
                    <option value='0'>Select One</option>
                    <?
                        mysql_data_seek($templates, 0);
                        while($template = mysql_fetch_assoc($templates)){
							$selected = '';
							if($template['template_ID'] == $group['document_group_single_item_template_FK']){
								$selected = 'selected="selected"';
							}
                            ?><option value='<? echo $template['template_ID']; ?>' <? echo $selected; ?>><? echo $template['template_name']; ?></option><?
                        }
                    ?>
                </select></label></p>
                <p><label>Additional Fields:<br />
                <select name='additional_fields'>
                    <option value='0'>None</option>
                    <?
                        while($field = mysql_fetch_assoc($additional_fields)){
							$selected = '';
							if($field['additional_field_group_ID'] == $group['document_group_additional_field_group_FK']){
								$selected = 'selected="selected"';
							}
                            ?><option value='<? echo $field['additional_field_group_ID']; ?>' <? echo $selected; ?>><? echo $field['additional_field_group_name']; ?></option><?
                        }
                    ?>
                </select></label>
                <input type='hidden' name='gid' value='<? echo $_GET['g']; ?>' />
                <input type='hidden' name='action' value='save' />
                <p><input type='submit' value='Save' /></p>
            </form>
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