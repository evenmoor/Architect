<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Account Confirmation | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>

<body>
	<div id='page'>
        <div class='content'>
        <h1>Account Confirmation</h1>
        <?
            if(isset($_GET['ucid']) && $_GET['ucid'] != ''){
                $confirmationString = generateRandomString(50, false, true); //generate new string to protect account
                mysql_query('UPDATE tbl_users SET user_user_status_FK = "2", 
							user_confirmation_string="'.$confirmationString.'" 
							WHERE user_confirmation_string="'.clean($_GET['ucid']).'" 
							LIMIT 1');
                systemLog($_GET['ucid'].' account confirmed.');
                
                if(mysql_affected_rows() > 0){
                    ?><p>Your account has been confirmed. You can now <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/login/'>log in</a>.</p><?
                }else{
                    ?><p>I'm sorry, but I am unable to confirm your account. Please contact us.</p><?
                }
            }
        ?>
        </div><!--/content-->
    </div><!--/page-->
</body>
</html>