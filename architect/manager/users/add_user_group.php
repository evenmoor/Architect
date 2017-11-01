<? if(validatePermissions('system', 15)){ ?>
<?
	if(mysql_query('INSERT INTO tbl_user_groups(user_group_ID, 
												user_group_name,
												user_group_description)
										VALUES(NULL,
											"'.clean($_POST['name']).'",
											"'.clean($_POST['description']).'")')){
		
		//redirect to user permissions screen
		$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/dashboard/';
		
		systemLog('User group added: id# '.clean(mysql_insert_id()).'.');
		
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