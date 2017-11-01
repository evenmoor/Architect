<? if(validatePermissions('system', 16)){ ?>
<?
	if(mysql_query('INSERT INTO tbl_forms(form_ID,
											form_name)
									VALUES(NULL,
										"'.clean($_POST['name']).'")')){
		$form_id = mysql_insert_id();
				
		systemLog('Form added id# '.clean($form_id).'.');
		
		//redirect to edit form screen
		$form_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/forms/edit_form/?f='.$form_id;
		header("Location: ".$form_path);
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