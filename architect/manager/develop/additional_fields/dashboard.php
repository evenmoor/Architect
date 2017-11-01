<? if(validatePermissions('system', 14)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Additional Field Groups Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#add_additional_fields_form').slideUp(0);
		
		$('#toggle_additional_field_groups_form').click(function(e){
			e.preventDefault();
			$('#add_additional_fields_form').slideToggle();
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
        		<h1>Additional Field Groups Dashboard</h1>
            </div>
            
            <div class='single_column'>
            	<h2>Additional Field Groups</h2>
                <p><strong><a href='' id='toggle_additional_field_groups_form'>Add Additional Field Group:</a></strong></p>
                <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/add_field_group/' id='add_additional_fields_form'>
                	<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_groups_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                    <p><input type='submit' value='Add Additional Field Group' /></p>
                </form>
                <?
					$groups = mysql_query('SELECT additional_field_group_ID,
													additional_field_group_name
												FROM tbl_additional_field_groups 
												ORDER BY additional_field_group_ID DESC
												LIMIT 10');
					
					$counter = 0;
					while($group = mysql_fetch_assoc($groups)){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						?><div class='table_row <? echo $odd; ?>'>
                        	<span class='table_column centered' style='width:15%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/edit_field_group/?g=<? echo $group['additional_field_group_ID']; ?>'>Edit</a></span>
                        	<span class='table_column' style='width:85%;'><? echo $group['additional_field_group_name']; ?> || <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/delete_field_group/?g=<? echo $group['additional_field_group_ID']; ?>' class='confirm'>(delete)</a></span>
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