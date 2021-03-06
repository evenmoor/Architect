<?
	systemLog('User logged out: Session Expiration.');
	session_unset();
	session_destroy();
	
	$rpath = "";
	if(isset($_GET['rpath'])){
		$rpath = "?rpath=".$_GET['rpath'];
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Expire Session | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>

<body>
	<div id='page'>
        <div class='content'>
            <h1>Expired Session</h1>
            <p>Your session has expired and you have been logged out. Please <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/login/<? echo $rpath; ?>'>log in</a> again to continue.</p>
    	</div><!--/content-->
    </div><!--/page-->
</body>
</html>