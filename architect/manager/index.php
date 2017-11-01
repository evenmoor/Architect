<? if(validatePermissions('system', 1)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Architect Manager | <? echo $site_settings['name']; ?></title>
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
        
        <div class='content'>
            <div class='dual_column'>
            	<ul class='warning_list'>
                    <? 
						$home_page_check = mysql_query('SELECT document_ID FROM tbl_documents WHERE document_is_home_page ="1" LIMIT 1'); 
						if(mysql_num_rows($home_page_check) < 1){//no home page defined
							?><li>No Home Page Selected</li><?
						}
						
						if($site_settings['status'] != 'ONLINE'){
							?><li>Site Is Not Online</li><?
						}
						
						$install_path = constant("ARCH_FRONT_END_PATH").'install/';
						
						if(opendir($install_path)){
							?><li>Install Directory Is Still Present</li><?
						}
						
						$update_path = constant("ARCH_FRONT_END_PATH").'update/';
						if(opendir($update_path)){
							?><li>Update Directory Is Present.</li><?
						}
						
						$architect_information_feed = file_get_contents('https://webapps.irapture.com/Architect/ver.xml');
						$architect_version_number = xmlElementSet("current_version", $architect_information_feed, true);
						if($architect_version_number[0] > $site_settings['version']){
							?><li>An Update is available. Please contact <a href='http://www.irapture.com/'>iRapture.com</a> to request an upgrade.</li><?
						}
					?>
                </ul>
                
                <noscript>
                	<div class='whoah'>
                        <h2>HEY! Wait a second....</h2>
                        <p><img src='<? echo constant("ARCH_INSTALL_PATH"); ?>themes/base/images/arch_whoops.png' alt='Oops!'/>It looks like you have JavaScript disabled. Although I understand that you may not want to leave JavaScript enabled for your standard internet browsing, Architect uses it heavily for a lot of the interaction here in the backend. I won't stop you from continuing on, but I also won't guarantee that you will be able to use every screen back here (at least not conveniently).</p>
                    </div>
                </noscript>
                
            	<h1>Welcome</h1>
                
                <h2>Architect News</h2>
                <? $architect_feed_content = file_get_contents('http://webapps.irapture.com/Architect/feed.php'); ?>
                <? echo $architect_feed_content; ?>
            </div>
            
            <div class='dual_column'>
                <table class='styled_table' style='width:80%; margin:0 0 10px 10%;'>
                	<tr>
                    	<th colspan="2">Site Information</th>
                    </tr>
                	<tr>
                    	<th class='right'>Name:</th>
                        <td><? echo $site_settings['name']; ?></td>
                    </tr>
                    <tr>
                    	<th class='right'>Status:</th>
                        <td><? echo $site_settings['status']; ?></td>
                    </tr>
                    <tr>
                    	<th class='right'>Timezone:</th>
                        <td><? echo $site_settings['timezone']; ?></td>
                    </tr>
                </table>
                <table class='styled_table' style='width:80%; margin:0 0 10px 10%;'>
                	<tr>
                    	<th colspan="2">Platform Information</th>
                    </tr>
                    <tr>
                    	<th class='right'>Architect Version:</th>
                        <td><? echo $site_settings['version']; ?></td>
                    </tr>
                    <tr>
                    	<th class='right'>PHP Version:</th>
                        <td><? echo phpversion(); ?></td>
                    </tr>
                    <tr>
                    	<th class='right'>MySQL Version:</th>
                        <td><? echo mysql_get_server_info(); ?></td>
                    </tr>
                </table>
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