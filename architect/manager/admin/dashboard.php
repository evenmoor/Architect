<? if(validatePermissions('system', 15)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administrative Dashboard | <? echo $site_settings['name']; ?></title>
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
        		<h1>Administrative Dashboard</h1>
            </div>
            
            <div class='dual_column'>
                <h2>Recent Log Entries</h2>
                <?
					$log_entries = mysql_query('SELECT system_log_entry,
											 			system_log_timestamp
													FROM tbl_system_log
													ORDER BY system_log_entry_ID DESC
													LIMIT 10');
					
					$counter = 0;
					while($entry = mysql_fetch_assoc($log_entries)){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						?><div class='table_row <? echo $odd; ?>'>
                        	<?
								$timestamp = strtotime($entry['system_log_timestamp']);
								$timestamp = date('m/d/y g:ia', $timestamp);
							?>
                        	<span class='table_column' style='width:100%;'><strong><? echo $timestamp; ?></strong> | <em><? echo $entry['system_log_entry']; ?></em></span>
                        </div><?
						$counter++;
					}
					
				?>
            </div>
                        
            <div class='dual_column'>
            	<h2>Active Modules</h2>
                <div class='table_row'>
                    <span class='table_column' style='width:100%;'>Templates</span>
                </div>
                <div class='table_row odd'>
                    <span class='table_column' style='width:100%;'>Document Groups</span>
                </div>
                <?
					$counter = 0;
					foreach($expansion_modules as $module){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						include(constant("ARCH_BACK_END_PATH").'modules/'.$module.'/module_config.php');//include default config for module
						?><div class='table_row <? echo $odd; ?>'>
                        	<span class='table_column' style='width:100%;'><? echo $module_name;?></span>
                        </div><?
						
						$counter++;
					}
				?>
            </div>
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