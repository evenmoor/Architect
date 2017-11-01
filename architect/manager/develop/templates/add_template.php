<? if(validatePermissions('system', 10)){ ?>
<?
	//template name
	$template_name = $_POST['name'];
	$template_name = trim($template_name);
	if($template_name == ""){
		$template_name = "blank";
	}
	
	//build unique template directory
	$unique_directory_name = uniqid($template_name.'_');
	//path from ftp root
	$directory_path = constant("FTP_ROOT").'templates/'.$unique_directory_name;
	
	//path to new template
	$template_ftp_path = $directory_path.'/'.$template_name.'.php';
	//html path to template
	$template_html_path = constant("ARCH_INSTALL_PATH").'templates/'.$unique_directory_name.'/'.$template_name.'.php';
	
	//array of directories to be created
	$directory_path_array = array($directory_path,
								$directory_path.'/styles',
								$directory_path.'/scripts',
								$directory_path.'/images');
	
	
	//connect to ftp
	$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
	//log into connection
	$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
	
	//boolean monitoring whether or not the directories were created
	$build_failed = false;
	
	if($login_check){//if can login to ftp
		for($loop_counter = 0; $loop_counter < count($directory_path_array); $loop_counter++){//build directories in array
			if(ftp_mkdir($ftp_connection, $directory_path_array[$loop_counter])){//create directory
				
			}else{//directory create failed
				?><p>Failed to create directory: <? echo $directory_path_array[$loop_counter]; ?>.</p><?
				$build_failed = true;
			}
		}

		if(!$build_failed){//if the directory was built upload default files
			//name of default template
			$default_template = constant("ARCH_BACK_END_PATH")."manager/develop/default_builds/default_template.html";
			//load template
			$file_pointer = fopen($default_template , 'r');
			if(ftp_fput($ftp_connection, $template_ftp_path, $file_pointer, FTP_BINARY)){//upload template
				if(ftp_chmod($ftp_connection, 0777, $template_ftp_path)){//set permissions
				}else{//permission set failed
					?><p>Template permission set failed.</p><?
				}
			}else{//upload failed
				?><p>Default template upload failed.</p><?
			}
		}

		//close ftp connection
		ftp_close($ftp_connection);
		
		if(!$build_failed){//template directory built and ready
			//add template to database
			if(mysql_query('INSERT INTO tbl_templates(template_ID,
												   template_permission_FK,
												   template_additional_field_group_FK,
												   template_name,
												   template_location)
											VALUES(NULL,
												   NULL,
												   NULL,
												   "'.clean($template_name).'",
												   "'.clean($template_html_path).'")')){
				
				//redirect to edit template screen
				$template_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/templates/edit_template/?t='.mysql_insert_id();
				systemLog('template added id# '.clean(mysql_insert_id()).'.');
				header("Location: ".$template_path);
			}else{//database add failed
				?><h1>DB add error.</h1><?
				echo mysql_error();
			}
		}else{//build failed
			?><h1>Directory build failed.</h1><?
			?><p>Path: <? echo $directory_path; ?></p><?
		}
	}else{//connection to ftp failed
		?><h1>FTP connection failed.</h1><?
	}
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>