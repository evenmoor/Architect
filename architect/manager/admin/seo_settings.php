<? if(validatePermissions('system', 15)){ ?>
<?

	$seo_settings = mysql_fetch_assoc(mysql_query('SELECT site_robots,
															site_map
														FROM tbl_site_settings
														WHERE site_ID = "1"
														LIMIT 1'));

?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SEO Settings | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
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
        
        <div id='section_navigation'>
        	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/admin_section_navigation.php'); ?>
        </div><!--/section_navigation-->
        
        <div class='content with_section_nav'>
        	<div class='single_column'>
        		<h1>SEO Settings</h1>
            </div><!--/heading-->
            
            <div class='single_column'>
            	<h2>Automatically Generate Site Map</h2>
            	<form action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/generate_site_map/' method='post'>
                	<p>Navigation Menu:<br/><select name='nav'>
                    	<option value=''>Choose One</option>
                        <?
							$menus = mysql_query('SELECT navigation_menu_ID,
													navigation_menu_name
												FROM tbl_navigation_menus 
												ORDER BY navigation_menu_name');
							
							while($menu = mysql_fetch_assoc($menus)){
								?><option value='<? echo $menu['navigation_menu_ID']; ?>'><? echo $menu['navigation_menu_name']; ?></option><?
							}
						?>
                    </select></p>
                	<p><input type='submit' value='Generate Site Map'/></p>
                </form>
                
                <h2>Manually Update Settings</h2>
                <form action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/update_seo_settings/' method='post'>
                	<p><label>Robots.txt:<br/>
                    <textarea name='robots'><? echo $seo_settings['site_robots']; ?></textarea></label></p>
                    
                    <p><label>Site Map:<br/>
                    <textarea name='sitemap'><? echo $seo_settings['site_map']; ?></textarea></label></p>
                	<p><input type='submit' value='Save'/></p>
                </form>
            </div><!--/SEO settings-->
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