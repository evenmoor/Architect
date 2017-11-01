<? if(validatePermissions('system', 6)){ ?>
<?

	
	//connect to ftp
	$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
	//log into connection
	$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
	
	if($login_check){//if can login to ftp
		//count files for loop
		$file_counter = 0;
		//base media directory
		$relative_path = $_POST['target'];
		$base_directory = constant("FTP_ROOT").'media'.$relative_path;
		
		foreach($_FILES['files']['name'] as $filename){
			//open the temporary file for upload
			$temp_file = fopen($_FILES['files']['tmp_name'][$file_counter], 'r');
			
			//build final upload directory
			$upload_directory = $base_directory.$filename;
			
			if(ftp_fput($ftp_connection, $upload_directory, $temp_file, FTP_BINARY)){
				//print "<p>File uploaded: ".$upload_directory."</p>";
				$media_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/publish/media/dashboard/?p='.$relative_path;
				systemLog('media uploaded - '.clean($media_path).'.');
			}else{
				print "<p>File upload <strong>failed</strong>: ".$upload_directory."</p>";
			}
			$file_counter++;
		}//end upload loop
		
		header("Location: ".$media_path);
	}else{//connection failed
		?>
        	<h1>FTP Error</h1>
            <p>Unable to connect to FTP server.</p>
		<?
	}
	
	ftp_close($ftp_connection);
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>