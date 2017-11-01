<? if(validatePermissions('system', 15)){ ?>
<?
	//update
	mysql_query('UPDATE tbl_site_settings
				SET site_robots="'.clean($_POST['robots']).'",
					site_map="'.clean($_POST['sitemap']).'" 
				WHERE site_ID="1"
				LIMIT 1');
	//log
	systemLog('Site SEO Settings Updated');
	
	//return
	$admin_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/admin/seo_settings/';
	header('Location: '.$admin_path);
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>