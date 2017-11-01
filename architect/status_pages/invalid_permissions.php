<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Invalid Permissions | <? echo $site_settings['name']; ?></title>
</head>

<body>
	<? if(isLoggedIn()){ ?>
		<h1>Error: Invalid Permissions</h1>
    	<p>Sorry, but you don't have permission to access this page.</p>
    <? }else{ ?>
    	<h1>Error: Invalid Permissions</h1>
    	<p>Please log in to access this page.</p>
        <form action='<? echo constant("ARCH_INSTALL_PATH")."user/login/?rpath=".$_SERVER['REQUEST_URI']; ?>' method='post' class='private_login_form'>
        	<p><label>Username:<br/><input type='text' name='loginUsername'/></label></p>
            <p><label>Password:<br/><input type='password' name='loginPassword'/></label></p>
            <p><input type='submit' value='Log In'/></p>
            <input type='hidden' name='logInSubmitted' value='true'/>
            <input type='hidden' name='rpath' value='<? echo $_SERVER['REQUEST_URI']; ?>'/>
        </form>
	<? } ?>
</body>
</html>