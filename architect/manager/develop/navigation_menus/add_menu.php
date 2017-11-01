<? if(validatePermissions('system', 11)){ ?>
<?
if(mysql_query('INSERT INTO  tbl_navigation_menus(navigation_menu_ID,
										   navigation_menu_permissions_FK,
										   navigation_menu_name)
									VALUES(NULL,
										   NULL,
										   "'.clean($_POST['name']).'")')){
		
	//redirect to edit menu screen
	$menu_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/navigation_menus/edit_menu/?m='.mysql_insert_id();
	systemLog('menu added id# '.clean(mysql_insert_id()).'.');
	header("Location: ".$menu_path);
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