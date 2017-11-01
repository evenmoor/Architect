<? if(validatePermissions('system', 6)){ ?>
<?

	
	//connect to ftp
	$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
	//log into connection
	$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
	
	if($login_check){//if can login to ftp
		//base media directory -- limits the delete action to media folder
		$base_directory = constant("FTP_ROOT").'media';
		
		$file = $_POST['file'];
			
		//build final upload directory
		$file_location = $base_directory.$file;
		
		//make sure a file is specified
		if(trim($file) != ''){
			if(ftp_delete($ftp_connection, $file_location)){//perform the actual delete
				systemLog('media removed - '.clean($file_location).'.');
				$file_elements = explode("/", $file);
				
				//build redirect path
				$redirect_path = '';
				for($counter = 0; $counter < count($file_elements) - 1; $counter++){
					$redirect_path.= $file_elements[$counter].'/';
				}
				
				//echo $redirect_path;
				
				$media_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/publish/media/dashboard/?p='.$redirect_path;
				header("Location: ".$media_path);
			}else{
				systemLog('media delete failed - '.clean($file_location).'.');
				?>
					<h1>Error</h1>
                    <p>Media delete failed: <? echo $file_location; ?></p>
				<?
			}
		}else{
			?>
            	<h1>Error</h1>
                <p>No file specified</p>
            <?
		}
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