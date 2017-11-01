<? if(validatePermissions('system', 14)){ ?>
<?
if(mysql_query('INSERT INTO tbl_additional_field_groups(additional_field_group_ID,
										   additional_field_group_permissions_FK,
										   additional_field_group_name)
									VALUES(NULL,
										   NULL,
										   "'.clean($_POST['name']).'")')){
		
	//redirect to edit menu screen
	$add_group_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/additional_fields/edit_field_group/?g='.mysql_insert_id();
	systemLog('additional field group added id# '.clean(mysql_insert_id()).'.');
	header("Location: ".$add_group_path);
}else{//database add failed
	?><h1>DB add error.</h1><?
	echo mysql_error();
}
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>