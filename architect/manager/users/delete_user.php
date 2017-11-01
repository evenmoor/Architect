<? if(validatePermissions('system', 15)){ ?>
<?
	if(mysql_query('DELETE FROM tbl_users
						   WHERE user_ID = "'.clean($_GET['u']).'"
						   LIMIT 1')){
		//clean up affected tables
		mysql_query('OPTIMIZE TABLE tbl_users');
		//redirect to block dashboard
		
		systemLog('User deleted id# '.clean($_GET['u']).'.');
		
		$dashboard_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/dashboard/';
		header("Location: ".$dashboard_path);
	}else{
		?><h1>Database update failed: <? echo mysql_error(); ?></h1><?
	}
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>