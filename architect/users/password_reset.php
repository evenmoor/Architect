<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Password Reset | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>

<body>
	<div id='page'>
        <div class='content'>
            <h1>Password Reset</h1>
            <?
                if(isset($_GET['ucid']) && $_GET['ucid'] != '' || isset($_POST['ucid']) && $_POST['ucid'] != ''){
                    $generate_form = true;
                    $password_error = '';
                    
                    if(isset($_POST['ucid'])){
                        if($_POST['password'] != $_POST['confirmPassword']){
                            $password_error = '<p class="error">Passwords do not match.</p>';
                        }else{
                            switch(evaluatePassword(clean($_POST['password']))){
                                case "0":
                                    //password is fine
                                    $generate_form = false;
                                break;
                                case "1":
                                    $password_error = '<p class="error">Password is too short.</p>';
                                break;
                                case "2":
                                    $password_error = '<p class="error">Password requires more upper case letters.</p>';
                                break;
                                case "3":
                                    $password_error = '<p class="error">Password requires more lower case letters.</p>';
                                break;
                                case "4":
                                    $password_error = '<p class="error">Password requires more numbers.</p>';
                                break;
                                case "5":
                                    $password_error = '<p class="error">Password requires more special characters.</p>';
                                break;
                                default:
                                    $password_error = '<p class="error">Unknown password error.</p>';
                            }
                        }
                    }
                    
                    if($generate_form){
                        $ucid = '';
                        $check_passed = false;
                        
                        if(isset($_GET['ucid'])){
                            $ucid = $_GET['ucid'];
                        }elseif(isset($_POST['ucid'])){
                            $ucid = $_POST['ucid'];
                        }
                        
                        if(mysql_num_rows(mysql_query('SELECT user_ID FROM tbl_users WHERE user_confirmation_string="'.clean($ucid).'" LIMIT 1')) == 1){$check_passed = true;}
                        systemLog($ucid.' password changed.');
                                                
                        if($check_passed){
                            ?>
                                <form method='post' action='<? echo $_SERVER["REQUEST_URI"]; ?>'>
                                    <h2>Choose a new password:</h2>
                                    <? if($password_error != ''){ echo $password_error; } ?>
                                    <p><label>New Password:</label> <input type='password' name='password'/></p>
                                    <p><label>Confirm New Password:</label> <input type='password' class='styledField'/></p>
                                    <input type='hidden' name='ucid' value='<? echo $ucid; ?>'>
                                    <p><input type='submit' value='Save Password'/></p>
                                </form>
                            <?
                        }else{
                            ?>
                                <h2>Error</h2>
                                <p>Invalid password reset request, please request a <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/password_recovery/'>password reset</a>.</p>
                            <?
                        }
                    }else{
                        $confirmation_string = generateRandomString(50, false, true); //generate new string to protect account
                        $generate_salt = generateRandomString(25);
                        $password_hash_1 = $_POST['password'].$generate_salt;
                        $password_hash_2 = $generate_salt.$_POST['password'];
                        $password_hash_1 = hash('sha512', $password_hash_1, false);
                        $password_hash_2 = hash('sha512', $password_hash_2, false);
                        
                        $confirmation_string = generateRandomString(50, false, true);
                        if(mysql_query('UPDATE tbl_users 
                                       SET user_salt="'.$generate_salt.'", 
                                       user_confirmation_string="'.$confirmation_string.'", 
                                       user_password_hash_1="'.$password_hash_1.'", 
                                       user_password_hash_2="'.$password_hash_2.'" 
                                       WHERE user_confirmation_string="'.clean($_POST['ucid']).'" 
                                       LIMIT 1')){
                            ?><p>Your password has been changed. You may now <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/login/'>log in</a>.<?
                        }else{
                            ?><p>The password change failed, please contact us.<?
                        }
                    }
                }
            ?>
    	</div><!--/content-->
    </div><!--/page-->
</body>
</html>