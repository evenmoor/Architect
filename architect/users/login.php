<?
	$logged_in = false;
	$login_error = false;
	
	if(isLoggedIn()){
		$logged_in = true;
	}
	
	$return_path = '';
	
	//listen for return path overrides
	if(isset($_GET['rpath']) || isset($_POST['rpath'])){
		if(isset($_GET['rpath']) && $_GET['rpath'] != ''){
			$return_path = $_GET['rpath'];
		}elseif(isset($_POST['rpath']) && $_POST['rpath'] != ''){
			$return_path = $_POST['rpath'];
		}
	}
	
	if(trim($return_path) == ''){//test for empty return path
		$return_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/';
	}
	
	if($return_path != ''){//check for a redirect path and force it on primary domain
		if(isset($_SERVER['HTTPS'])){//handle https connections
			$return_path = "https://".$_SERVER['SERVER_NAME'].$return_path;
		}else{//handle other connections
			$return_path = "http://".$_SERVER['SERVER_NAME'].$return_path;
		}
	}
	
	if(isset($_POST['logInSubmitted'])){
		$user_salt_data = mysql_query('SELECT user_salt FROM tbl_users WHERE user_username = "'.clean($_POST['loginUsername']).'"');
		$salt = mysql_fetch_assoc($user_salt_data);
		$logMessage = '';
		$logged_in = false;
		
		$password_hash_1 = $_POST['loginPassword'].$salt['user_salt'];
		$password_hash_1 = hash('sha512', $password_hash_1, false);

		$password_hash_2 = $salt['user_salt'].$_POST['loginPassword'];
		$password_hash_2 = hash('sha512', $password_hash_2, false);
		
		$login_result = mysql_query('SELECT user_ID,
									user_font_family,
									user_font_size,
									user_session_timeout,
									user_tutorials_enabled
									FROM tbl_users 
									WHERE user_username = "'.clean($_POST['loginUsername']).'" 
									AND user_password_hash_1 = "'.$password_hash_1.'" 
									AND user_password_hash_2 = "'.$password_hash_2.'"');
		if(mysql_num_rows($login_result) == 1){//there should only ever be one row that matches 
			$user_info = mysql_fetch_assoc($login_result);
			$_SESSION['user_ID'] = $user_info['user_ID'];
			$_SESSION['font'] = $user_info['user_font_family'];
			$_SESSION['font-size'] = $user_info['user_font_size'];
			$_SESSION['user_session_timeout'] = $user_info['user_session_timeout'];
			$_SESSION['tutorial_mode'] = $user_info['user_tutorials_enabled'];
			$_SESSION['username'] = $_POST['loginUsername'];
			$_SESSION['login_in_attempts'] = 0;
			$logged_in = true;
			systemLog('User logged in at '.$_SERVER['REMOTE_ADDR'].'.');
			header('Location: '.$return_path);
		}else{
			systemLog($_POST['username'].' invalid login attempted from '.clean($_SERVER['REMOTE_ADDR']).' using '.clean($_POST['loginUsername']).'.');
			$login_error = true;
			if(isset($_SESSION['login_in_attempts'])){
				$_SESSION['login_in_attempts']++;
			}else{
				$_SESSION['login_in_attempts'] = 1;
			}
			
			if($_SESSION['login_in_attempts'] > constant("ARCH_LOGIN_ATTEMPTS_ALLOWED")){
				$_SESSION['login_lockout'] = mktime(date("H"), date("i") + constant("ARCH_LOGIN_LOCKOUT_TIME"), date("s"), date("m"), date("d"), date("Y"));
				$_SESSION['login_in_attempts'] = 0;
			}
		}
	}
	
	$custom_login_settings = mysql_query('SELECT site_custom_login_css_override,
												site_custom_login_preform_override,
												site_custom_login_postform_override
											FROM tbl_site_settings
											WHERE site_ID="1"
											LIMIT 1');
	$custom_login_settings = mysql_fetch_assoc($custom_login_settings);
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Log In | <? echo $site_settings['name']; ?></title>
<? if(trim($custom_login_settings['site_custom_login_css_override']) != ""){ ?>
	<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>media/repository/<? echo $custom_login_settings['site_custom_login_css_override']; ?>" rel="stylesheet" type="text/css" media="all" />
<? }else{ ?>
	<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? } ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>
    
<body>
	<? if(trim($custom_login_settings['site_custom_login_preform_override']) != ""){ ?>
    	<? echo $custom_login_settings['site_custom_login_preform_override']; ?>
    <? }else{ ?>
        <div id='page'>
            <div class='content'>
                <h1>Log In</h1>
   	<? } ?>
            <? $now = mktime(); ?>
			<? if(!$logged_in && !isset($_SESSION['login_lockout']) || !$logged_in && isset($_SESSION['login_lockout']) && $_SESSION['login_lockout'] < $now){ ?>
                <form method='post' action='<? echo $_SERVER["REQUEST_URI"]; ?>'>
                	<? 
						$attempt_count = ''; 
						if(isset($_SESSION['login_in_attempts']) && $_SESSION['login_in_attempts'] > 0){
							$attempt_count = '<br>--Attempts ('.$_SESSION['login_in_attempts'].')--'; 
						}
					?>
                      <h2>Please log in</h2>
                      <? if($login_error){?><p class="error">Error: There was a problem with your<br/> username/password combination.<br/> Please try again. <? echo $attempt_count; ?></p> <? } ?>
                      <p><label>Username:</label> <br/> <input type='text' name='loginUsername' value='<? if(isset($_POST['loginUsername'])){echo $_POST['loginUsername'];}?>'/></p>
                      <p><label>Password:</label> <br/> <input type='password' name='loginPassword' /></p>
                      <input type='hidden' name='logInSubmitted' value='true' />
                      <input type='hidden' name='rpath' value='<? echo $return_path; ?>'/>
                      <p><input type='submit' value='Log In'/> </p>
                </form>
                <p><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/password_recovery/'>Forgot your password?</a><br />
                <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/account_creation/'>Create Account</a></span></p>
            <? }elseif( !$logged_in && isset($_SESSION['login_lockout']) && $_SESSION['login_lockout'] > $now){ ?>
            	<p>You have attempted too many log ins. Please wait <? echo constant("ARCH_LOGIN_LOCKOUT_TIME"); ?> minutes and <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/login/'>try again</a>.</p>
                <p>You may also attempt <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/password_recovery/'>password recovery</a> if you need!</p>
            <? }else{ ?>
                <p>Hello <? echo $_SESSION['username']; ?>,<br/> You are currently logged in. Would you like to <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/logout/'>log out</a> or visit the <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/'>dashboard</a>?</p>
            <? } ?>
    
    <? if(trim($custom_login_settings['site_custom_login_postform_override']) != ""){ ?>
    	<? echo $custom_login_settings['site_custom_login_postform_override']; ?>
    <? }else{ ?>
            </div><!--/content-->
        </div><!--/page-->
   	<? } ?>
</body>
</html>