<? if(validatePermissions('system', 10)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Template Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#add_template_form').slideUp(0);
		
		$('#toggle_template_form').click(function(e){
			e.preventDefault();
			$('#add_template_form').slideToggle();
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
        		<h1>Template Dashboard</h1>
            </div>
            
            <div class='single_column'>
            	<h2>Templates</h2>
                <p><strong><a href='' id='toggle_template_form'>Add Template:</a></strong></p>
                <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/add_template/' id='add_template_form'>
                	<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=template_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                    <p><input type='submit' value='Add Template' /></p>
                </form>
                <?
					$templates = mysql_query('SELECT template_ID,
											 			template_name
													FROM tbl_templates
													ORDER BY template_name');
					
					$counter = 0;
					while($template = mysql_fetch_assoc($templates)){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						?><div class='table_row <? echo $odd; ?>'>
                            <span class='table_column centered' style='width:15%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/edit_template/?t=<? echo $template['template_ID']; ?>'>Edit</a></span>
                            <span class='table_column' style='width:85%;'><? echo $template['template_name']; ?> || <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/delete_template/?t=<? echo $template['template_ID']; ?>' class='confirm'><em>(delete)</em></a></span>
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