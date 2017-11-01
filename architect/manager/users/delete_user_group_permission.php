<? if(validatePermissions('system', 15)){ ?>
<?
	if(mysql_query('DELETE FROM tbl_site_permissions 
				   WHERE site_permission_ID = "'.clean($_GET['p']).'"
				   LIMIT 1')){
		//clean up affected table
		mysql_query('OPTIMIZE TABLE tbl_site_permissions');
		
		//redirect to user permissions screen
		$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/dashboard/';
		
		systemLog('User group permission removed: id# '.clean($_GET['g']).'.');
		
		header("Location: ".$document_path);
	}else{//database delete failed
		?><h1>DB delete error.</h1><?
		echo mysql_error();
	}
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>