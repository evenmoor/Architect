<? if(validatePermissions('system', 6)){ ?>
<?
	$folder_path = '/';
	if(isset($_GET['p'])){
		$folder_path = $_GET['p'];
	}
	
	//recursive function to list all folders in directory
	function generateFolderList($connection, $directory){
		$folder_list = array();
		
		//get a list of elements in the directory
		$directory_elements = ftp_nlist($connection, $directory);

		//parse directory elements
		foreach($directory_elements as $element){
			//filter names out of the path
			$element_components = explode('/', $element);
			$file_extention_check = explode('.', $element_components[(count($element_components) - 1)]);

			if(count($file_extention_check) > 1){//if the item has an extention it is a file
			}else{//if the item has no extention it is a directory pass it back into the function
				//add the directory itself
				array_push($folder_list, str_replace(constant("FTP_ROOT").'media', "", $element));
				
				$folders = generateFolderList($connection, $element);
				if(count($folders) > 0){
					foreach($folders as $folder){
						array_push($folder_list, str_replace(constant("FTP_ROOT").'media', "", $folder));
					}
					//array_push($folder_list, $folders);
				}
			}
		}
		
		return $folder_list;
	}
	
	//connect to ftp
	$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
	//log into connection
	$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
	
	//list of all folders in media directory
	$folder_list = generateFolderList($ftp_connection, constant("FTP_ROOT").'media');
	
	//close ftp connection
	ftp_close($ftp_connection);
	
	asort($folder_list);
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#upload_media_form, #add_folder_form').slideUp(0);
		
		$('#toggle_media_form').click(function(e){
			e.preventDefault();
			$('#upload_media_form').slideToggle();
		});
		
		$('#toggle_add_folder_form').click(function(e){
			e.preventDefault();
			$('#add_folder_form').slideToggle();
		});
	});
</script>
</head>

<body>
	<div id='page'>
    	<div id='header'>
        	<div id='user_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/user_navigation.php'); ?>
            </div><!--/user_navigation-->
            <div id='primary_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/primary_navigation.php'); ?>
            </div><!--/primary_navigation-->
            <img src='<? echo constant("ARCH_INSTALL_PATH"); ?>themes/base/images/arch_title.png' alt='Architect' id='title'/>
        </div><!--/header-->
        
        <div id='sub_navigation'>
        	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/sub_navigation.php'); ?>
        </div><!--/sub_navigation-->
        
        <div class='content'>
        	<div class='single_column'>
        		<h1>Media Dashboard</h1>
            </div>
            
            <div class='single_column'>
            	<p><strong><a href='' id='toggle_media_form'>Upload Media:</a></strong></p>
                <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/upload_media/' id='upload_media_form' enctype='multipart/form-data'>
                	<p>File(s)<a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=multiple_file_upload' class='help_link'>?</a></sup>: <input type='file' name='files[]' multiple="" /></p>
                    <p>To Folder: <select name='target'>
                    	<option value='/'>/</option>
                        <? 
							foreach($folder_list as $folder){ 
								?><option value='<? echo $folder; ?>/'><? echo $folder; ?>/</option><?
							}
						?>
                    </select></p>
                    <p><input type='submit' value='Upload Media' /></p>
                </form>
                
                <p><strong><a href='' id='toggle_add_folder_form'>Add Folder:</a></strong></p>
                <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/add_folder/' id='add_folder_form'>
                	<p>Folder Name: <input type='text' name='name' /></p>
                    <input type='hidden' name='base' value='<? if($folder_path != '/'){echo $folder_path;} ?>/' />
                    <p><input type='submit' value='Add Folder' /></p>
                </form>
                
                <h2>Folder: <? echo $folder_path; ?></h2>
                <?
					if($folder_path != '/'){
						$previous_path = '/';
						$previous_path_elements = explode('/', $folder_path);
						for($loop_counter = 0; $loop_counter < count($previous_path_elements) - 1; $loop_counter++){
							if($previous_path != '/'){
								$previous_path .= '/';
							}
							$previous_path .= $previous_path_elements[$loop_counter];
						}
                		?><p><a href='?p=<? echo $previous_path; ?>'>< Back</a></p><?
					}
				?>
                <p>
                <?
					//connect to ftp
					$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
					//log into connection
					$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
					
					$absolute_path = constant("FTP_ROOT").'media'.$folder_path;
					
					//list of the folder contents
					$folder_contents = ftp_nlist($ftp_connection, $absolute_path);
					sort($folder_contents);
					
					//close ftp connection
					ftp_close($ftp_connection);
					
					$folder_list = array();
					$file_list = array();
					
					//parse directory elements
					foreach($folder_contents as $element){
						//filter names out of the path
						$element_components = explode('/', $element);
						$file_extention_check = explode('.', $element_components[(count($element_components) - 1)]);
						$relative_path = str_replace(constant("FTP_ROOT").'media', "", $element);
						
						$element_array = array($relative_path, $element_components[(count($element_components) - 1)]);
		
						if(count($file_extention_check) > 1){//if the item has an extention it is a file delete it
							array_push($file_list, $element_array);
						}else{
							array_push($folder_list, $element_array);
						}
					}
					
					asort($folder_list);
					asort($file_list);
					
					?>
                    	<div class='quad_column'>
							<h3>Folder List:</h3>
					<?
					
					$counter = 0;
					foreach($folder_list as $folder){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						$counter++;
						
						?><div class='table_row <? echo $odd; ?>'><?
							?><a href='?p=<? echo $folder[0]; ?>' class='folder'><? echo $folder[1]; ?>/</a><br /><?
						?></div><?
					}
					?> 
                    	</div>
                        <div class='dual_column'> 
							<h3>File List:</h3>
					<?
					
					$counter = 0;
					foreach($file_list as $file){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						$counter++;
						
						?><div class='table_row <? echo $odd; ?>'><?
							$delete_form = "<form action='".constant("ARCH_INSTALL_PATH")."".constant("ARCH_HANDLER_MANAGE")."/publish/media/delete_media/' method='post' style='display:inline;'><input type='submit' value='Delete'/><input type='hidden' name='file' value='".$file[0]."'/></form>";
							$move_form = "<form action='' method='post'><input type='submit' value='Move'/></form>";
							?><a href='<? echo constant("ARCH_INSTALL_PATH").'media'.$file[0]; ?>' class='file'><? echo $file[1]; ?></a> <? echo $delete_form;
						?></div><?
					}
					?> </div> <?
				?>
                </p>
            </div>
        </div><!--/content-->
        
        <div id='footer'>
        	<div id='version'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/version.php'); ?>
            </div><!--/version-->
            
            <div id='footer_navigation'>
            	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer_navigation.php'); ?>
            </div><!--/footer_navigaiton-->
        </div><!--/footer-->
    </div><!--/page-->
</body>
</html>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer.php'); ?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>