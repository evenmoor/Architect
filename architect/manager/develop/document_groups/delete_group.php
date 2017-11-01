<? if(validatePermissions('system', 12)){ ?>
<?
if(mysql_query('DELETE FROM tbl_document_groups
			   	WHERE document_group_ID = "'.clean($_GET['g']).'"
				LIMIT 1')){
		
	//clean up affected tables
	mysql_query('OPTIMIZE TABLE tbl_document_groups');
		
	//redirect to edit menu screen
	$groups_dashboard_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/document_groups/dashboard/';
	systemLog('document group deleted id# '.clean($_GET['g']).'.');
	header("Location: ".$groups_dashboard_path);
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