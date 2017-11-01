<? if(validatePermissions('system', 15)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Site Update | <? echo $site_settings['name']; ?></title>
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
        		<h1>Site Update</h1>
            </div><!--/heading-->
            
            <div class='single_column'>
            	<? 
					$status = 'Architect is up to date!'; 
					$architect_information_feed = file_get_contents('https://webapps.irapture.com/Architect/ver.xml');
					$architect_version_number = xmlElementSet("current_version", $architect_information_feed, true);
					if($architect_version_number[0] > $site_settings['version']){
						$status = 'Architect needs an update.'; 
					}
				?>
                <h2>Current Status: <? echo $status; ?></h2>
              <p>Updates for Architect can be obtained at <a href='https://webapps.irapture.com/Architect/'>https://webapps.irapture.com/Architect/</a></p>
                <?
                    $update_path = constant("ARCH_FRONT_END_PATH").'update/';
                        if(opendir($update_path)){
                            ?><h3>Update Data Found</h3><?
                            ?><p><a href='<? echo constant("ARCH_INSTALL_PATH").'update/'; ?>'>Run update ></a></p><?
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