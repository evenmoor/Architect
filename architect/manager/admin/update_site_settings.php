<? if(validatePermissions('system', 15)){ ?>
<?
	mysql_query('UPDATE tbl_site_settings
				SET site_name="'.clean($_POST['name']).'",
					site_status="'.clean($_POST['status']).'",
					site_development_alternate_path = "'.clean($_POST['alternate_development_path']).'",
					site_development_override = "'.clean($_POST['development_override']).'",
					site_timezone="'.clean($_POST['timezone']).'",
					site_custom_login_css_override="'.clean($_POST['stylesheet_override']).'",
					site_custom_login_preform_override="'.clean($_POST['pre-form_override']).'",
					site_custom_login_postform_override="'.clean($_POST['post-form_override']).'"
				WHERE site_ID="1"
				LIMIT 1');
	
	$admin_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/admin/site_settings/';
	
	header('Location: '.$admin_path);
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>