<? if(validatePermissions('system', 11)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Navigation Menu Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#add_navigation_form').slideUp(0);
		
		$('#toggle_navigation_form').click(function(e){
			e.preventDefault();
			$('#add_navigation_form').slideToggle();
		});
	});
</script>
</head>

<body>
	<div id='page'>
    	<div id='header'>
        	<div id='user_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/user_navigation.php'); ?>
            </div><!--/user_navigation-->
            <div id='primary_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/primary_navigation.php'); ?>
            </div><!--/primary_navigation-->
            <img src='<? echo constant("ARCH_INSTALL_PATH"); ?>themes/base/images/arch_title.png' alt='Architect' id='title'/>
        </div><!--/header-->
        
        <div id='sub_navigation'>
        	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/sub_navigation.php'); ?>
        </div><!--/sub_navigation-->
        
        <div class='content'>
        	<div class='single_column'>
        		<h1>Navigation Menu Dashboard</h1>
            </div>
            
            <div class='single_column'>
            	<h2>Menus</h2>
                <p><strong><a href='' id='toggle_navigation_form'>Add Menu:</a></strong></p>
                <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/add_menu/' id='add_navigation_form'>
                	<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                    <p><input type='submit' value='Add Menu' /></p>
                </form>
                <?
					$menus = mysql_query('SELECT navigation_menu_ID,
													navigation_menu_name
												FROM tbl_navigation_menus 
												ORDER BY navigation_menu_name');
					
					$counter = 0;
					while($menu = mysql_fetch_assoc($menus)){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						?><div class='table_row <? echo $odd; ?>'>
                            <span class='table_column centered' style='width:15%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/edit_menu/?m=<? echo $menu['navigation_menu_ID']; ?>'>Edit</a></span>
                            <span class='table_column' style='width:85%;'><? echo $menu['navigation_menu_name']; ?> || <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/delete_menu/?m=<? echo $menu['navigation_menu_ID']; ?>' class='confirm'>(delete)</a></span>
                        </div><?
						$counter++;
					}
					
				?>
            </div><!--/templates-->
        </div><!--/content-->
        
        <div id='footer'>
        	<div id='version'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/version.php'); ?>
            </div><!--/version-->
            
            <div id='footer_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer_navigation.php'); ?>
            </div><!--/footer_navigaiton-->
        </div><!--/footer-->
    </div><!--/page-->
</body>
</html>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer.php'); ?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>