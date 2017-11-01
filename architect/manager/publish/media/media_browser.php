<? if(validatePermissions('system', 6)){ ?>
<?
	//Media Browser Plugin for CKEditor Version 1.0
	//Tested with CKEditor Version 4.1
	//Last Modified 03/22/13
	
	$folder_path = '/';
	if(isset($_GET['p'])){
		$folder_path = $_GET['p'];
	}
	
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
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script>
	//from the CKEditor API
	//http://docs.ckeditor.com/#!/guide/dev_file_browser_api
	// Helper function to get parameters from the query string.
	function getUrlParam( paramName ) {
		var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
		var match = window.location.search.match(reParam) ;
		return ( match && match.length > 1 ) ? match[ 1 ] : null ;
	}


	$(function(){
		$('a.file_link').click(function(e){
			e.preventDefault();
			//grab window function
			var function_number = getUrlParam( 'CKEditorFuncNum' );
			//build file path from constant
			var file_path = $(this).attr('href');
			
			//pass values back to CKEditor listener
			window.opener.CKEDITOR.tools.callFunction(function_number, file_path);
			//close the window
			window.close();
			//console.log('Editor:'+function_number+' Path:'+file_path);
		});
	});
</script>
</head>

<body>
	<div id='page'>        
        <div class='content'>
        	<div class='single_column'>
        		<h1>Media Browser: <? echo $folder_path; ?></h1>
                <?
						$previous_path = '/';
						$previous_path_elements = explode('/', $folder_path);
						for($loop_counter = 0; $loop_counter < count($previous_path_elements) - 1; $loop_counter++){
							if($previous_path != '/'){
								$previous_path .= '/';
							}
							$previous_path .= $previous_path_elements[$loop_counter];
						}
						
						$back_path = $_SERVER['REQUEST_URI'];
						//trim off exsisting pathing values
						$back_path = explode('&p=', $back_path);
						$back_path = $back_path[0];
						
						$additional_folder_path = $back_path;
						
						//add new pathing
						$back_path.= '&p='.$previous_path;
				?>
            </div>
            
            <div class='single_column'>
            	<div class='dual_column' style='width:23%;'>
                	<h2>Folder List</h2>
                    <p>
                    	<? if($back_path != $_SERVER['REQUEST_URI']){ ?>
                    		<a href='<? echo $back_path; ?>'>/\ Up One Level /\</a><br />
                        <? } ?>
                    <? 
						foreach($folder_list as $folder){
							?><a href='<? echo $additional_folder_path; ?>&p=<? echo $folder[0]; ?>' class='folder'><? echo $folder[1]; ?>/</a><br /><?
						}
					?>
                    </p>
                </div>
                <div class='dual_column' style='width:73%;'>
                	<h2>File List</h2>
                    <?
						if(count($file_list) > 0){
							?><p><?
							foreach($file_list as $file){
								?><a href='<? echo constant("ARCH_INSTALL_PATH").'media'.$file[0]; ?>' class='file_link file'><? echo $file[1]; ?></a><br /><?
							}
							?></p><?
						}else{
							?><p>Empty<p><?
						}
					?>
                </div>
            </div>
        </div><!--/content-->
    </div><!--/page-->
</body>
</html>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/footer.php'); ?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>