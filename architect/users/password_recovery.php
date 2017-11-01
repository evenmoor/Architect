<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Password Recovery | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>

<body>
	<div id='page'>
        <div class='content'>
            <h1>Password Recovery</h1>
            <? 
                $message_sent = false;
                if(isset($_POST['email']) && isset($_POST['firstName'])){
                    $confirmation_string = generateRandomString(50, false, true); //generate new string to protect account
                    $user = mysql_fetch_assoc(mysql_query('SELECT user_ID, 
                                                          user_email_address 
                                                          FROM tbl_user_data 
                                                          LEFT JOIN tbl_users ON  tbl_user_data.user_data_user_FK = tbl_users.user_ID
                                                          WHERE user_email_address ="'.clean($_POST['email']).'" 
                                                          AND user_data_first_name="'.clean($_POST['firstName']).'" 
                                                          LIMIT 1'));
                    
                    if($user['user_ID'] != 0){
                        if(mysql_query('UPDATE tbl_users 
                                       SET user_confirmation_string="'.$confirmation_string.'" 
                                       WHERE user_ID="'.$user['userID'].'" 
                                       LIMIT 1')){
                            
                            systemLog($user['user_ID'].' password reset requested.');
                            
                            $to = clean($user['user_email_address']);//primary recipiant
                            $headers = "From: ".constant('EMAIL_NO_REPLY')."\r\n"; //from address is required
                            $subject = 'Password Recovery: '.$site_settings['name']; //subject line of the email
                    
                            $message="A password reset has been requested for the account assoicated with this email address. Please use the link below to reset your password.
        
        http://".$_SERVER['SERVER_NAME']."".constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_USER")."/password_reset/?ucid=".$confirmation_string."";
                            if(mail($to, $subject, $message, $headers)){
                                $message_sent = true;
                            }
                        }
                    }
                }
                
                if($message_sent){
                    ?>
                        <p>A password reset message has been sent to your email account.</p>
                    <?
                }else{
              ?>
                  <form class='loginForm' action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
                    <p><label>First Name:</label> <br/> <input type='text' name='firstName'/></p>
                    <p><label>Email Address:</label> <br/> <input type='text' name='email'/></p>
                    <p><input type='submit' value='Recover Password'/></p>
                  </form>
              <? } ?>
    	</div><!--/content-->
    </div><!--/page-->
</body>
</html>