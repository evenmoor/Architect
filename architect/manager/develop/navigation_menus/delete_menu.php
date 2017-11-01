<? if(validatePermissions('system', 11)){ ?>
<?
if(mysql_query('DELETE FROM tbl_navigation_menus
			   	WHERE navigation_menu_ID = "'.clean($_GET['m']).'"
				LIMIT 1')){
		
	//clean up affected tables
	mysql_query('OPTIMIZE TABLE tbl_navigation_menus');
	mysql_query('OPTIMIZE TABLE tbl_navigation_menu_items ');
		
	//redirect to edit menu screen
	$menu_dashboard_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/navigation_menus/dashboard/';
	systemLog('menu deleted id# '.clean($_GET['m']).'.');
	header("Location: ".$menu_dashboard_path);
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