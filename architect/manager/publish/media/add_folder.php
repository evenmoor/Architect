<? if(validatePermissions('system', 6)){ ?>
<?
	$relative_path = $_POST['base'].$_POST['name'];
	$folder_path = constant("FTP_ROOT").'media'.$relative_path;
	
	//connect to ftp
	$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
	//log into connection
	$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
	
	if($login_check){//if can login to ftp
		if(ftp_mkdir($ftp_connection, $folder_path)){//build directory
			//redirect to media screen
			$media_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/publish/media/dashboard/?p='.$relative_path;
			systemLog('media folder added - '.clean($relative_path).'.');
			header("Location: ".$media_path);
		}else{//build failed
			?>
                <h1>FTP Error</h1>
                <p>Unable to create folder.</p>
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