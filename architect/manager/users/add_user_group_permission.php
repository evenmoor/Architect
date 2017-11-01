<? if(validatePermissions('system', 15)){ ?>
<?
	if(mysql_query('INSERT INTO tbl_site_permissions(site_permission_ID, 
												site_permission_type_FK,
												site_permission_entity_FK,
												site_permission_value)
										VALUES(NULL, 
												"1",
												"'.clean($_POST['permission']).'",
												"'.clean($_POST['group']).'")')){
		
		//redirect to user permissions screen
		$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/dashboard/';
		
		systemLog('User group permission added: id# '.clean(mysql_insert_id()).'.');
		
		header("Location: ".$document_path);
	}else{//database delete failed
		?><h1>DB add error.</h1><?
		echo mysql_error();
	}
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>