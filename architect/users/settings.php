<?
	$password_error = $username_error = $email_error = '';
	
	if(isset($_POST['action']) && isLoggedIn()){
		switch($_POST['action']){
			//update personal architect settings
			case 'update_architect_settings':
				$font_size = $font_family = '';
				if($_POST['font_type'] != "DEFAULT"){
					$font_family = $_POST['custom_font'];
				}else{
					$font_family = $_POST['font_type'];
				}
				
				if($_POST['font_size'] != "DEFAULT"){
					$font_size = $_POST['custom_size'];
				}else{
					$font_size = $_POST['font_size'];
				}
				
				//update table
				mysql_query('UPDATE tbl_users
							SET user_font_family = "'.clean($font_family).'",
							user_font_size = "'.clean($font_size).'",
							user_session_timeout = "'.clean($_POST['timeout']).'",
							user_tutorials_enabled = "'.clean($_POST['tutorials']).'"
							WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
							LIMIT 1');
				
				//update session variables
				$_SESSION['font'] = $font_family;
				$_SESSION['font-size'] = $font_size;
				$_SESSION['user_session_timeout'] = $_POST['timeout'];
				$_SESSION['tutorial_mode'] = $_POST['tutorials'];
			break;//end update personal architect settings
			
			//update account settings
			case 'update_account_settings':
				//update table
				mysql_query('UPDATE tbl_user_data
							SET user_data_first_name="'.clean($_POST['first']).'",
							user_data_middle_name="'.clean($_POST['middle']).'",
							user_data_last_name="'.clean($_POST['last']).'"
							WHERE user_data_user_FK = "'.clean($_SESSION['user_ID']).'"
							LIMIT 1');
			break;//end update account settings
			
			//change username
			case 'change_username':
				if(trim($_POST['username']) != ''){
					$username_check = mysql_query('SELECT user_username
														FROM tbl_users
														WHERE user_username="'.clean($_POST['username']).'"
														LIMIT 1');
														
					if(mysql_num_rows($username_check) > 0){
						$username_error = '<p class="error">Username is unavailable.</p>';
					}else{
						$current_salt = mysql_fetch_assoc(mysql_query('SELECT user_salt 
																		 FROM tbl_users
																		 WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
																		 LIMIT 1'));
						
						$password_hash_1 = $_POST['current_password'].$current_salt['user_salt'];
						$password_hash_1 = hash('sha512', $password_hash_1, false);
				
						$password_hash_2 = $current_salt['user_salt'].$_POST['current_password'];
						$password_hash_2 = hash('sha512', $password_hash_2, false);
						
						$login_result = mysql_query('SELECT user_ID
													FROM tbl_users 
													WHERE user_ID = "'.clean($_SESSION['user_ID']).'" 
													AND user_password_hash_1 = "'.$password_hash_1.'" 
													AND user_password_hash_2 = "'.$password_hash_2.'"');
						
						if(mysql_num_rows($login_result) == 1){//current password checks out
							if(mysql_query('UPDATE tbl_users
											   SET user_username="'.clean($_POST['username']).'"
										   WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
										   LIMIT 1')){//username set
								$_SESSION['username'] = $_POST['username'];
							}else{
								$username_error = '<p class="error">Failed to set username.</p>';
							}
						}else{
							$username_error = '<p class="error">Invalid password.</p>';
						}
					}
				}else{
					$username_error = '<p class="error">Username must not be blank.</p>';
				}
			break;
			
			//change email address
			case 'change_email_address':
				if(trim($_POST['email']) != ''){
					$email_check = mysql_query('SELECT user_email_address
														FROM tbl_users
														WHERE user_email_address="'.clean($_POST['email']).'"
														LIMIT 1');
														
					if(mysql_num_rows($email_check) > 0){
						$email_error = '<p class="error">Email address is unavailable.</p>';
					}else{
						$current_salt = mysql_fetch_assoc(mysql_query('SELECT user_salt 
																		 FROM tbl_users
																		 WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
																		 LIMIT 1'));
						
						$password_hash_1 = $_POST['current_password'].$current_salt['user_salt'];
						$password_hash_1 = hash('sha512', $password_hash_1, false);
				
						$password_hash_2 = $current_salt['user_salt'].$_POST['current_password'];
						$password_hash_2 = hash('sha512', $password_hash_2, false);
						
						$login_result = mysql_query('SELECT user_ID
													FROM tbl_users 
													WHERE user_ID = "'.clean($_SESSION['user_ID']).'" 
													AND user_password_hash_1 = "'.$password_hash_1.'" 
													AND user_password_hash_2 = "'.$password_hash_2.'"');
						
						if(mysql_num_rows($login_result) == 1){//current password checks out
							if(mysql_query('UPDATE tbl_users
											   SET user_email_address="'.clean($_POST['email']).'"
										   WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
										   LIMIT 1')){//email set
							}else{
								$email_error = '<p class="error">Failed to set email address.</p>';
							}
						}else{
							$email_error = '<p class="error">Invalid password.</p>';
						}
					}
				}else{
					$email_error = '<p class="error">Email address must not be blank.</p>';
				}
			break;
			
			//change password
			case 'change_password':
				if($_POST['new_password'] != $_POST['confirm_new_password']){
					$password_error = '<p class="error">Passwords do not match.</p>';
				}else{
					switch(evaluatePassword($_POST['new_password'])){
						case "0":
							//password is fine
							$current_salt = mysql_fetch_assoc(mysql_query('SELECT user_salt 
																			 FROM tbl_users
																			 WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
																			 LIMIT 1'));
							
							$password_hash_1 = $_POST['current_password'].$current_salt['user_salt'];
							$password_hash_1 = hash('sha512', $password_hash_1, false);
					
							$password_hash_2 = $current_salt['user_salt'].$_POST['current_password'];
							$password_hash_2 = hash('sha512', $password_hash_2, false);
							
							$login_result = mysql_query('SELECT user_ID
														FROM tbl_users 
														WHERE user_ID = "'.clean($_SESSION['user_ID']).'" 
														AND user_password_hash_1 = "'.$password_hash_1.'" 
														AND user_password_hash_2 = "'.$password_hash_2.'"');
							
							if(mysql_num_rows($login_result) == 1){//current password checks out
								$password_salt = generateRandomString(25);
								$password_hash_1 = $_POST['new_password'].$password_salt;
								$password_hash_2 = $password_salt.$_POST['new_password'];
								$password_hash_1 = hash('sha512', $password_hash_1, false);
								$password_hash_2 = hash('sha512', $password_hash_2, false);
								
								if(mysql_query('UPDATE tbl_users
											   SET user_salt="'.clean($password_salt).'",
											   user_password_hash_1="'.clean($password_hash_1).'",
											   user_password_hash_2="'.clean($password_hash_2).'"
											   WHERE user_ID = "'.clean($_SESSION['user_ID']).'"
											   LIMIT 1')){
									//password set
								}else{
									$password_error = '<p class="error">Failed to set password.</p>';
								}
							}else{//current password failure
								$password_error = '<p class="error">Current password invalid.</p>';
							}
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
							$password_error = '<p class="error">Password requires more numbers. '.$_POST['new_password'].'</p>';
						break;
						case "5":
							$input_error = true;
							$password_error = '<p class="error">Password requires more special characters.</p>';
						break;
						default:
							$password_error = '<p class="error">Unknown password error.</p>';
					}
				}
			break;
		}
	}
?>
<?
	if(isLoggedIn()){
		$user_settings = mysql_fetch_assoc(mysql_query('SELECT tbl_users.user_username, 
																 tbl_users.user_email_address, 
																 tbl_users.user_font_family,
																 tbl_users.user_font_size,
																 tbl_users.user_session_timeout,
																 tbl_users.user_tutorials_enabled,
																 tbl_user_data.user_data_first_name,
																 tbl_user_data.user_data_middle_name,
																 tbl_user_data.user_data_last_name
															 FROM tbl_user_data
															 	INNER JOIN tbl_users ON tbl_user_data.user_data_user_FK = tbl_users.user_ID
															 WHERE user_data_user_FK = "'.clean($_SESSION['user_ID']).'"
															 LIMIT 1'));
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>User Settings | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>

<body>
	<? if(isLoggedIn()){ ?>
        <div id='page'>
            <div class='content'>
                <p class='right'><a class='button' href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/'>Back to Manager</a></p>
                <div class='tri_column'>
                    <h1>Account Settings</h1>
                    <form method="post" action="<? echo $_SERVER["REQUEST_URI"]; ?>">
                        <p><label>First Name:</label><br /> <input type='text' name='first' value="<? echo $user_settings['user_data_first_name']; ?>"/></p>
                        <p><label>Middle Name:</label><br /> <input type='text' name='middle' value="<? echo $user_settings['user_data_middle_name']; ?>"/></p>
                        <p><label>Last Name:</label><br /> <input type='text' name='last' value="<? echo $user_settings['user_data_last_name']; ?>"/></p>
                        <input type='hidden' name='action' value='update_account_settings' />
                        <p><input type='submit' value='Save' /></p>
                    </form>
                </div><!--/tri_column-->
                
                <div class='tri_column'>
                    <h1>Personal Architect Settings </h1>
                    <form method="post" action="<? echo $_SERVER["REQUEST_URI"]; ?>">
                        <p><label>Font:</label><sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=user_settings_font' class='help_link'>?</a></sup><br /> 
                        <?
                            $default = $custom = '';
                            if($user_settings['user_font_family'] == "DEFAULT"){
                                $default = 'checked="checked"';
                            }else{
                                $custom = 'checked="checked"';
                            }
                        ?>
                        <input type='radio' name='font_type' value='DEFAULT' <? echo $default; ?>/>Default<br />
                        <?
                            $valid_fonts = array(array("Arial, Helvetica, sans-serif", "Arial"),
                                                       array("Helvetica, sans-serif", "Helvetica"),
                                                       array("Verdana, Geneva, sans-serif", "Verdana"),
                                                       array("Georgia, Times, serif", "Georgia"),
                                                       array("Tahoma, Geneva, sans-serif", "Tahoma"),
                                                       array("Times New Roman, Times, serif", "Times"));
                        ?>
                        <input type='radio' name='font_type' value='CUSTOM' <? echo $custom; ?>/><select name='custom_font'>
                                    <option value='DEFAULT'>Select One</option>
                            <?
                                foreach($valid_fonts as $font){
                                    $selected = '';
                                    if($font[0] == $user_settings['user_font_family']){
                                        $selected = 'selected="selected"';
                                    }
                                    ?><option value='<? echo $font[0]; ?>' <? echo $selected; ?>><? echo $font[1]; ?></option><?
                                }
                            ?>
                        </select></p>
                        
                        <?
                            $default = $custom = '';
                            if($user_settings['user_font_size'] == "DEFAULT"){
                                $default = 'checked="checked"';
                            }else{
                                $custom = 'checked="checked"';
                            }
                        ?>
                        
                        <p><label>Font Size:</label><sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=user_settings_font_size' class='help_link'>?</a></sup><br />
                        <input type='radio' name='font_size' value='DEFAULT' <? echo $default; ?>/>Default<br />
                        <input type='radio' name='font_size' value='CUSTOM' <? echo $custom; ?>/><input type='text' name='custom_size' value="<? if($user_settings['user_font_size'] != "DEFAULT"){echo $user_settings['user_font_size'];}?>"/>px</p>
                        
                        <p><label>Tutorials:</label><sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.6&topic=user_settings_tutorials' class='help_link'>?</a></sup><br/>
                        <?
							$enabled_checked = $disabled_checked = '';
							
							if($user_settings['user_tutorials_enabled'] == 1){
								$enabled_checked = 'checked="checked"';
							}else{
								$disabled_checked = 'checked="checked"';
							}
						?>
                        <input type='radio' name='tutorials' value='1' <? echo $enabled_checked; ?>/>Enabled<br/>
                        <input type='radio' name='tutorials' value='0' <? echo $disabled_checked; ?>/>Disabled</p>
                        
                        <p><label>Session Timeout:</label><sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=user_settings_session_timeout' class='help_link'>?</a></sup><br />
                        <input type='text' name='timeout' value='<? echo $user_settings['user_session_timeout']; ?>'/>min</p>
                        <input type='hidden' name='action' value='update_architect_settings' />
                        <p><input type='submit' value='Save' /></p>
                    </form>
                </div><!--/tri_column-->
                
                <div class='tri_column'>
                    <h1>Password Change</h1>
                    <?
						if($password_error != ''){
							echo $password_error;
						}
					?>
                    <form method="post" action="<? echo $_SERVER["REQUEST_URI"]; ?>">
                        <p><label>Current Password:</label><br /> 
                        <input type='password' name='current_password' /></p>
                        <p><label>New Password:</label><br /> 
                        <input type='password' name='new_password' /></p>
                        <p><label>Confirm New Password:</label><br /> 
                        <input type='password' name='confirm_new_password' /></p>
                        <input type='hidden' name='action' value='change_password' />
                        <p><input type='submit' value='Change Password' /></p>
                    </form>
                    
                    <h1>Username Change</h1>
                    
                    <?
						if($username_error != ''){
							echo $username_error;
						}
					?>
                    <form method="post" action="<? echo $_SERVER["REQUEST_URI"]; ?>">
                        <p><label>Current Password:</label><br /> 
                        <input type='password' name='current_password' /></p>
                        <p><label>Username:</label><br /> 
                        <input type='text' name='username' value='<? echo $user_settings['user_username']; ?>'/></p>
                        <input type='hidden' name='action' value='change_username' />
                        <p><input type='submit' value='Change Username' /></p>
                    </form>
                    
                    <h1>Email Address Change</h1>
                    
                    <?
						if($email_error != ''){
							echo $email_error;
						}
					?>
                    <form method="post" action="<? echo $_SERVER["REQUEST_URI"]; ?>">
                        <p><label>Current Password:</label><br /> 
                        <input type='password' name='current_password' /></p>
                        <p><label>Email Address:</label><br /> 
                        <input type='text' name='email' value='<? echo $user_settings['user_email_address']; ?>'/></p>
                        <input type='hidden' name='action' value='change_email_address' />
                        <p><input type='submit' value='Change Email Address' /></p>
                    </form>
                </div><!--/tri_column-->
            </div><!--/content-->
        </div><!--/page-->
    <? }//end if logged in ?>
</body>
</html>