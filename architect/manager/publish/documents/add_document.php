<? if(validatePermissions('system', 7)){ ?>
<?
	$template = $group = 'NULL';
	
	if($_POST['template'] != 0){
		$template = '"'.clean($_POST['template']).'"';
	}
	
	if($_POST['group'] != 0){
		$group = '"'.clean($_POST['group']).'"';
	}

	if(mysql_query('INSERT INTO tbl_documents(document_ID,
											   document_group_FK,
											   document_template_FK,
											   document_created,
											   document_name,
											   document_title_en,
											   document_content_en)
										VALUES(NULL,
											   '.$group.',
											   '.$template.',
											   "'.date('Y-m-d H-i-s').'",
											   "'.clean($_POST['name']).'",
											   "'.clean($_POST['title']).'",
											   "'.clean($_POST['content']).'")')){
			
		//redirect to edit document screen
		$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/publish/documents/edit_document/?d='.mysql_insert_id();
		systemLog('Document added: id# '.mysql_insert_id().' | '.clean($_POST['name']).'.');
		header("Location: ".$document_path);
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