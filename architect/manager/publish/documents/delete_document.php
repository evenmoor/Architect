<? if(validatePermissions('system', 7)){ ?>
<?
	if(mysql_query('DELETE FROM tbl_documents
				   WHERE document_ID = "'.clean($_GET['d']).'"
				   LIMIT 1')){
		//clean up affected table
		mysql_query('OPTIMIZE TABLE tbl_documents');
		
		//redirect to edit document screen
		$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/publish/documents/dashboard/';
		
		systemLog('Document removed: id# '.clean($_GET['d']).'.');
		
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