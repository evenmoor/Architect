<? if(validatePermissions('system', 16)){ ?>
<?
	if(mysql_query('DELETE FROM tbl_forms
					   WHERE form_ID = "'.clean($_GET['f']).'"
					   LIMIT 1')){
		
		//clean up affected tables
		mysql_query('OPTIMIZE TABLE tbl_forms');
		mysql_query('OPTIMIZE TABLE tbl_form_elements');
		mysql_query('OPTIMIZE TABLE tbl_form_scripts');
		mysql_query('OPTIMIZE TABLE tbl_form_values');
		
		//redirect to forms dashboard
		$dashboard_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/forms/dashboard/';
		
		systemLog('Form delted id# '.clean($_GET['f']).'.');
		
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