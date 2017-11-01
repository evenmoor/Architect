<? if(validatePermissions('system', 13)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'save':
				//update database elements
				mysql_query('UPDATE tbl_blocks
							 SET block_name = "'.clean($_POST['name']).'"
							 WHERE block_ID = "'.clean($_POST['bid']).'"
							 LIMIT 1');
				
				//update actual block file
				$file_location = mysql_fetch_assoc(mysql_query('SELECT block_code_location 
											  FROM tbl_blocks
										      WHERE block_ID = "'.clean($_POST['bid']).'"
											  LIMIT 1'));
				
				//base location
				$file_location = $file_location['block_code_location'];
				//clean up location by removing install path
				//$file_location = str_replace(constant("ARCH_INSTALL_PATH"), "", $file_location);
				$file_location = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $file_location, 1);
				//append path to front end
				$file_location = constant("ARCH_FRONT_END_PATH").$file_location;
				
				//write new contents to template
				file_put_contents($file_location, $_POST['block']);
				
				systemLog('block updated id# '.clean($_POST['bid']).'.');
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	$block = mysql_fetch_assoc(mysql_query('SELECT * FROM tbl_blocks WHERE block_ID="'.clean($_GET['b']).'" LIMIT 1'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Block (<? echo $block['block_name']; ?>) | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script>
	//hot keys
	$(document).bind('keydown', 'ctrl+s', function(e){
		e.preventDefault();
	   $('#block_form').submit();
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
        	<h1><? echo $block['block_name']; ?></h1>
        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='block_form'>
            	<h2>Block Properties</h2>
            	<p>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=block_name' class='help_link'>?</a></sup><br />
                <input type='text' name='name' value='<? echo $block['block_name']; ?>'/></p>
                <p>Block:<br />
                <?
					//base location
					$block_location = $block['block_code_location'];
					//clean up location by removing install path
					//$block_location = str_replace(constant("ARCH_INSTALL_PATH"), "", $block_location);
					$block_location = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $block_location, 1);
					//append path to front end
					$block_location = constant("ARCH_FRONT_END_PATH").$block_location;
					$block_content = file_get_contents($block_location); //<-- replaced include so that php tags will not parse
				?>
                <textarea name='block' class='html_area ui_code_highlight'><? echo htmlspecialchars($block_content); ?></textarea></p>
                <input type='hidden' name='bid' value='<? echo $_GET['b']; ?>' />
                <input type='hidden' name='action' value='save' />
                <p><input type='submit' value='Save (ctrl+s)' /></p>
            </form>
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