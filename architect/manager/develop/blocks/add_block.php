<? if(validatePermissions('system', 13)){ ?>
<?
	//build unique block directory
	$unique_directory_name = uniqid($_POST['name'].'_');
	//path from ftp root
	$directory_path = constant("FTP_ROOT").'blocks/'.$unique_directory_name;
	//path to new block
	$block_ftp_path = $directory_path.'/'.$_POST['name'].'.php';
	//html path to block
	$block_html_path = constant("ARCH_INSTALL_PATH").'blocks/'.$unique_directory_name.'/'.$_POST['name'].'.php';
	
	//array of directories to be created
	$directory_path_array = array($directory_path);
	
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
			//name of default block
			$default_block = constant("ARCH_BACK_END_PATH")."manager/develop/default_builds/default_block.html";
			//load block
			$file_pointer = fopen($default_block , 'r');
			if(ftp_fput($ftp_connection, $block_ftp_path, $file_pointer, FTP_BINARY)){//upload template
				if(ftp_chmod($ftp_connection, 0777, $block_ftp_path)){//set permissions
				}else{//permission set failed
					?><p>Block permission set failed.</p><?
				}
			}else{//upload failed
				?><p>Default block upload failed.</p><?
			}
		}

		//close ftp connection
		ftp_close($ftp_connection);
		
		if(!$build_failed){//block directory built and ready
			//add block to database
			if(mysql_query('INSERT INTO tbl_blocks(block_ID,
												   block_name,
												   block_code_location)
											VALUES(NULL,
												   "'.clean($_POST['name']).'",
												   "'.clean($block_html_path).'")')){
				
				//redirect to edit block screen
				$block_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/blocks/edit_block/?b='.mysql_insert_id();
				header("Location: ".$block_path);
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