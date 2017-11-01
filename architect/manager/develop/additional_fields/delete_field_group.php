<? if(validatePermissions('system', 14)){ ?>
<?
if(mysql_query('DELETE FROM tbl_additional_field_groups
			   	WHERE additional_field_group_ID = "'.clean($_GET['g']).'"
				LIMIT 1')){
	
	systemLog('additional field group deleted id# '.clean($_GET['g']).'.');
	
	//clean up affected tables
	mysql_query('OPTIMIZE TABLE tbl_additional_field_groups');
	mysql_query('OPTIMIZE TABLE tbl_additional_fields');
		
	//redirect to edit menu screen
	$group_dashboard_path = constant("ARCH_INSTALL_PATH"). constant("ARCH_HANDLER_MANAGE").'/develop/additional_fields/dashboard/';
	header("Location: ".$group_dashboard_path);
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