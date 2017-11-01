<? if(validatePermissions('system', 12)){ ?>
<?

$single_template = $group_template = $additional_fields = 'NULL';

if($_POST['template-single'] != 0){
	$single_template = '"'.clean($_POST['template-single']).'"';
}

if($_POST['template-group'] != 0){
	$group_template = '"'.clean($_POST['template-group']).'"';
}

if($_POST['additional_fields'] != 0){
	$additional_fields = '"'.clean($_POST['additional_fields']).'"';
}

if(mysql_query('INSERT INTO tbl_document_groups(document_group_ID,
										   document_group_template_FK,
										   document_group_single_item_template_FK,
										   document_group_additional_field_group_FK,
										   document_group_permissions_FK,
										   document_group_name)
									VALUES(NULL,
										   '.$group_template.',
										   '.$single_template.',
										   '.$additional_fields.',
										   NULL,
										   "'.clean($_POST['name']).'")')){
		
	//redirect to edit group screen
	$group_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/document_groups/edit_group/?g='.mysql_insert_id();
	systemLog('document group added id# '.clean(mysql_insert_id()).'.');
	header("Location: ".$group_path);
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