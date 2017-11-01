<? if(validatePermissions('system', 15)){ ?>
<?
	if($_GET['g'] != 1 && $_GET['g'] != 4){
		if(mysql_query('DELETE FROM tbl_user_groups
					   WHERE user_group_ID = "'.clean($_GET['g']).'"
					   LIMIT 1')){
			
			//delete their permissions
			mysql_query('DELETE FROM tbl_site_permissions
					   WHERE site_permission_type_FK = "1"
						AND site_permission_value = "'.clean($_GET['g']).'"');
			
			//clean up affected tables
			mysql_query('OPTIMIZE TABLE tbl_user_groups');
			mysql_query('OPTIMIZE TABLE tbl_site_permissions');
			
			//redirect to user permissions screen
			$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/dashboard/';
			
			systemLog('User group removed: id# '.clean($_GET['g']).'.');
			
			header("Location: ".$document_path);
		}else{//database delete failed
			?><h1>DB delete error.</h1><?
			echo mysql_error();
		}
	}else{
		?><h1>Error</h1><?
		?><p>Cannot Delete Protected Group</p><?
	}
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>