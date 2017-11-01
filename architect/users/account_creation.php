<?
	$account_created = false;
	$input_error = false;
	$first_name_error = $last_name_error = $email_error = $username_error = $password_error = false;
	
	if(isset($_POST['createAccount']) && $_POST['createAccount'] == 'true'){
		if($_POST['password'] != $_POST['passwordConfirm']){
			$input_error = true;
			$password_error = '<p class="error">Passwords do not match.</p>';
		}else{
			switch(evaluatePassword($_POST['password'])){
				case "0":
					//password is fine
				break;
				case "1":
					$input_error = true;
					$password_error = '<p class="error">Password is too short.</p>';
				break;
				case "2":
					$input_error = true;
					$password_error = '<p class="error">Password requires more upper case letters.</p>';
				break;
				case "3":
					$input_error = true;
					$password_error = '<p class="error">Password requires more lower case letters.</p>';
				break;
				case "4":
					$input_error = true;
					$password_error = '<p class="error">Password requires more numbers.</p>';
				break;
				case "5":
					$input_error = true;
					$password_error = '<p class="error">Password requires more special characters.</p>';
				break;
				default:
					$input_error = true;
					$password_error = '<p class="error">Unknown password error.</p>';
			}
		}
		
		$username_count = mysql_query('SELECT COUNT(*) FROM tbl_users WHERE user_username = "'.$_POST['username'].'"');
		$username_count = mysql_fetch_assoc($username_count);
		if($username_count['COUNT(*)'][0] > 0){
			$input_error = true;
			$username_error = '<p class="error">This username has been taken. Please choose another.</p>';
		}else{
			if(strlen($_POST['username']) < 8){
				$input_error = true;
				$username_error  = '<p class="error">Please choose a username at least 8 characters long.</p>';
			}
		}
		
		$email_address_count = mysql_query('SELECT COUNT(*) FROM tbl_users WHERE user_email_address="'.$_POST['email'].'"');
		$email_address_count = mysql_fetch_assoc($email_address_count);
		if($email_address_count['COUNT(*)'][0] > 0){
			$input_error = true;
			$email_error = '<p class="error">This email address has already been registered with this site.</p>';
		}else{
			if(!validateEmailAddress($_POST['email'])){
				$input_error = true;
				$email_error  = '<p class="error">Please enter a valid email address.</p>';
			}
		}
		
		if(strlen(trim($_POST['firstName'])) < 1){
			$input_error = true;
			$first_name_error = '<p class="error">Please enter your first name.</p>';
		}
		
		if(strlen(trim($_POST['lastName'])) < 1){
			$input_error = true;
			$last_name_error = '<p class="error">Please enter your last name.</p>';
		}
		
		if(!$input_error){
			$password_salt = generateRandomString(25);
			$password_hash_1 = $_POST['password'].$password_salt;
			$password_hash_2 = $password_salt.$_POST['password'];
			$password_hash_1 = hash('sha512', $password_hash_1, false);
			$password_hash_2 = hash('sha512', $password_hash_2, false);
			
			$confirmationString = generateRandomString(50, false, true);
			
			if(mysql_query('INSERT INTO tbl_users(user_ID,
												 user_user_status_FK,
												 user_user_group_FK,
												 user_username, 
												 user_salt, 
												 user_password_hash_1, 
												 user_password_hash_2, 
												 user_confirmation_string, 
												 user_email_address) 
						   					VALUES(NULL,
												   "1",
												   "4",
												   "'.clean($_POST['username']).'", 
													"'.$password_salt.'", 
													"'.$password_hash_1.'", 
													"'.$password_hash_2.'",
													"'.$confirmationString.'", 
													"'.clean($_POST['email']).'")')){
				
					$user_id = mysql_insert_id();
					
					//insert user data into appropriate table
					mysql_query('INSERT INTO tbl_user_data(user_data_ID,
														   user_data_user_FK,
														   user_data_first_name,
														   user_data_middle_name,
														   user_data_last_name)
														VALUES(NULL,
														   "'.clean($user_id).'",
														   "'.clean($_POST['firstName']).'",
														   "",
														   "'.clean($_POST['lastName']).'")');
					
					echo mysql_error();
				
				$account_created = true;
				systemLog($_POST['username'].' account created.');
				
				$to = clean($_POST['email']);//primary recipiant
				$headers = "From: ".constant('EMAIL_NO_REPLY')."\r\n"; //from address is required
				$subject = 'Account Confirmation: '.$site_settings['name']; //subject line of the email
			
				$message="Welcome to ".$site_settings['name']."
	
Your account has been created using this email address. Please use the link below to confirm this as a valid email address to associate with your account.
	
http://".$_SERVER['SERVER_NAME']."".constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_USER")."/account_confirmation/?ucid=".$confirmationString."";

				mail($to, $subject, $message, $headers);
			}
		}
	}
?>

<?
	function generateForm($first_name_error, $last_name_error, $email_error, $username_error, $password_error){
		?>
		<form method='post' action='<? echo $_SERVER["REQUEST_URI"]; ?>'>
			<h2>Create Your Account</h2>
            <? if($first_name_error!=false){echo $first_name_error;}?>
			<p><label>First Name:</label> <br> <input type='text' name='firstName' value="<? if(isset($_POST['firstName'])){echo $_POST['firstName'];} ?>"/></p>
			<? if($last_name_error!=false){echo $last_name_error;}?>
            <p><label>Last Name:</label> <br> <input type='text' name='lastName' value="<? if(isset($_POST['firstName'])){echo $_POST['lastName'];} ?>"/></p>
            <? if($email_error!=false){echo $email_error;}?>
            <p><label>Email Address:</label> <br/> <input type='text' name='email' value="<? if(isset($_POST['firstName'])){echo $_POST['email'];} ?>"/></p>
			<? if($username_error!=false){echo $username_error;}?>
            <p><label>Desired Username:</label> <br/> <input type='text' name='username' value="<? if(isset($_POST['firstName'])){echo $_POST['username'];} ?>"/></p>
            <? if($password_error!=false){echo $password_error;}?>
			<p><label>Password:</label> <br/> <input type='password' name='password' value="<? if(isset($_POST['firstName'])){echo $_POST['password'];} ?>"/></p>
			<p><label>Confirm Password:</label> <br/> <input type='password' name='passwordConfirm' value="<? if(isset($_POST['firstName'])){echo $_POST['passwordConfirm'];} ?>"/></p>
			<input type='hidden' name='createAccount' value='true'/>
			<p><input type='submit' value='Create Account'/></p>
		</form>
		<?
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Account Creation | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
</head>
	
<body>
	<div id='page'>
        <div class='content'>
        <h1>Account Creation</h1>
        <?
            if(!$account_created){
                generateForm($first_name_error, $last_name_error, $email_error, $username_error, $password_error);
            }else{
                ?><p>Your account has been created.</p><p>You will recieve a confirmation email shortly.</p><?
            }
        ?>
        </div><!--/content-->
    </div><!--/page-->
</body>
</html>