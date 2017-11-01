<? if(validatePermissions('system', 13)){ ?>
<?
	$block_info = mysql_fetch_assoc(mysql_query('SELECT block_ID,
														block_code_location 
													FROM tbl_blocks
													WHERE block_ID="'.clean($_GET['b']).'"'));
	
	//set break directory into components
	$block_directory = explode('/', $block_info['block_code_location']); 
	//strip out unique portion of url
	$block_directory = $block_directory[(count($block_directory) - 2)];
	//append FTP path and blocks directory
	$block_directory = constant("FTP_ROOT").'blocks/'.$block_directory.'/';
	
	//connect to ftp
	$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
	//log into connection
	$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
	
	if($login_check){//if we can login to ftp
		
		//recursive function to delete directory and its children
		function removeFTPDirectory($connection, $directory){
			//return value
			$directory_removed = false;
			
			//get a list of elements in the directory
			$directory_elements = ftp_nlist($connection, $directory);

			//parse directory elements
			foreach($directory_elements as $element){
				//filter names out of the path
				$element_components = explode('/', $element);
				$file_extention_check = explode('.', $element_components[(count($element_components) - 1)]);

				if(count($file_extention_check) > 1){//if the item has an extention it is a file delete it
					ftp_delete($connection, $element);
				}else{//if the item has no extention it is a directory pass it back into the function
					removeFTPDirectory($connection, $element);
					//remove the now empty directory itself
					ftp_rmdir($connection, $element);
				}
			}
			
			$directory_removed = ftp_rmdir($connection, $directory);
			
			return $directory_removed;
		}
	
		if(removeFTPDirectory($ftp_connection, $block_directory)){
			//close ftp connection
			ftp_close($ftp_connection);
			
			if(mysql_query('DELETE FROM tbl_blocks
						   WHERE block_ID = "'.clean($block_info['block_ID']).'"
						   LIMIT 1')){
				
				//clean up affected tables
				mysql_query('OPTIMIZE TABLE tbl_blocks');
				
				//redirect to block dashboard
				$dashboard_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/blocks/dashboard/';
				
				systemLog('Block delted id# '.clean($block_info['block_ID']).'.');
				
				header("Location: ".$dashboard_path);
			}else{
				?><h1>Database update failed: <? echo mysql_error(); ?></h1><?
			}
		}else{
			?><h1>Directory removal failed: <? echo $block_directory; ?></h1><?
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