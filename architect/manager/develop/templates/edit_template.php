<? if(validatePermissions('system', 10)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'delete_styles':
			case 'delete_scripts':
			case 'delete_images':
			
				//connect to ftp
				$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
				//log into connection
				$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
				
				if($login_check){//if can login to ftp
					//base template directory
					$base_directory = mysql_fetch_assoc(mysql_query('SELECT template_location 
												  FROM tbl_templates
												  WHERE template_ID = "'.clean($_POST['tid']).'"
												  LIMIT 1'));
					
					//base location
					$base_directory = $base_directory['template_location'];
					$base_directory = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $base_directory, 1);
					
					$base_directory = explode('/', $base_directory);
					
					//extract relevant file path information
					$base_directory = constant("FTP_ROOT").'templates/'.$base_directory[count($base_directory) - 2].'/';
				
					//path to files
					$file_path = "";

					//modify file path
					switch($_POST['action']){
						case 'delete_images':
							$file_path = $base_directory.'images/';
						break;
						case 'delete_scripts':
							$file_path = $base_directory.'scripts/';
						break;
						case 'delete_styles':
							$file_path = $base_directory.'styles/';
						break;
					}//end file path modification
					
					foreach($_POST['files'] as $file){
						$file_location = $file_path.$file;
						if(ftp_delete($ftp_connection, $file_location)){//perform the actual delete
							systemLog('Supporting file deleted - '.clean($file_location).'.');
						}else{
							echo "<h1>Error: unable to delete ".$file_location."</h1>";
							systemLog('Supporting file delete failed - '.clean($file_location).'.');
						}
					}//loop to parse deletes
				}//end FTP login
			break;//end delete
			
			case 'save':
				//build style list
				$style_list = '';
				
				if(isset($_POST['style_names'])){//check to see if custom styles have been submitted
					$style_names = $_POST['style_names'];
					$style_elements = $_POST['style_elements'];
					$style_styles = $_POST['style_styles'];

					for($style_counter = 0; $style_counter < count($style_names); $style_counter++){
						$style_list .= $style_names[$style_counter].'|'.$style_elements[$style_counter].'|'.$style_styles[$style_counter].'||';
					}
				}
			
				//update database elements
				mysql_query('UPDATE tbl_templates
							 SET template_permission_FK = "'.clean($_POST['permissions']).'",
							 template_additional_field_group_FK = "'.clean($_POST['additional_fields']).'",
							 template_name = "'.clean($_POST['name']).'",
							 template_custom_styles = "'.clean($style_list).'"
							 WHERE template_ID = "'.clean($_POST['tid']).'"
							 LIMIT 1');
				//update actual template file
				
				$file_location = mysql_fetch_assoc(mysql_query('SELECT template_location 
											  FROM tbl_templates
										      WHERE template_ID = "'.clean($_POST['tid']).'"
											  LIMIT 1'));
				
				//base location
				$file_location = $file_location['template_location'];
				//clean up location by removing install path
				//$file_location = str_replace(constant("ARCH_INSTALL_PATH"), "", $file_location);
				$file_location = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $file_location, 1);
				
				//append path to front end
				$file_location = constant("ARCH_FRONT_END_PATH").$file_location;
				
				//write new contents to template
				file_put_contents($file_location, $_POST['template']);
			break;//end save
			
			case 'upload':
				//connect to ftp
				$ftp_connection = ftp_connect(constant("FTP_LOCATION")) or die("Could not connect to FTP server.");
				//log into connection
				$login_check = ftp_login($ftp_connection, constant("FTP_USERNAME"), constant("FTP_PASSWORD"));
				
				if($login_check){//if can login to ftp
					//count files for loop
					$file_counter = 0;
					//base template directory
					$base_directory = mysql_fetch_assoc(mysql_query('SELECT template_location 
												  FROM tbl_templates
												  WHERE template_ID = "'.clean($_POST['tid']).'"
												  LIMIT 1'));
					
					//base location
					$base_directory = $base_directory['template_location'];
					//clean up location by removing install path
					//$base_directory = str_replace(constant("ARCH_INSTALL_PATH"), "", $base_directory);
					$base_directory = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $base_directory, 1);
					
					$base_directory = explode('/', $base_directory);
					$final_base_directory = '';
					
					//extract relevant file path information
					$base_directory = constant("FTP_ROOT").'templates/'.$base_directory[count($base_directory) - 2].'/';
					
					foreach($_FILES['files']['name'] as $filename){
						$type_directory = '';
						switch($_FILES['files']['type'][$file_counter]){//filter files into appropriate directories
							case 'text/css'://CSS files
								$type_directory = 'styles/';
							break;
							
							case 'text/javascript'://Javascript files 
							case 'text/x-c'://(I have no idea why php identifys some javascript files as text/x-c)
								$type_directory = 'scripts/';
							break;
							
							default://Everything else is assumed to be an image
								$type_directory = 'images/';
							break;
						}
						
						//build final upload directory
						$upload_directory = $base_directory.$type_directory.$filename;
						
						//open the temporary file for upload
						$temp_file = fopen($_FILES['files']['tmp_name'][$file_counter], 'r');
						
						if(ftp_fput($ftp_connection, $upload_directory, $temp_file, FTP_BINARY)){
							//print "<p>File uploaded: ".$upload_directory."</p>";
						}else{
							//print "<p>File upload <strong>failed</strong>: ".$upload_directory."</p>";
						}
						$file_counter++;
					}//end upload loop
				}//end ftp login check
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	$template = mysql_fetch_assoc(mysql_query('SELECT * FROM tbl_templates WHERE template_ID="'.clean($_GET['t']).'" LIMIT 1'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Template (<? echo $template['template_name']; ?>) | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#upload_form').slideUp(0);
		
		$('#upload').click(function(e){
			e.preventDefault();
			$(this).slideUp(0);
			$('#upload_form').slideDown(0);
		});
		
		$('a.remove').click(function(e){
			e.preventDefault();
			$(this).parent().parent().remove();
		});
		
		$('#add_cutom_style').submit(function(e){
			e.preventDefault();
			var name = $('#custom_style_name').val();
			var element = $('#custom_style_element').val();
			var style = $('#custom_style_style').val();
			
			var style_table = '<tr>';
			style_table += '<td>'+name+'</td>';
			style_table += '<td>'+element+'</td>';
			style_table += '<td>'+style+'</td>';
			
			style_table += '<td>* <a href="" class="remove">Remove</a>';
			style_table += '<input type="hidden" name="style_names[]" value="'+name+'"/>';
			style_table += '<input type="hidden" name="style_elements[]" value="'+element+'"/>';
			style_table += '<input type="hidden" name="style_styles[]" value="'+style+'"/>';
			style_table += '</td>';
			
			style_table += '</tr>';
			
			$('#custom_styles_list').append(style_table);
			
			$('#custom_styles_list a.remove').last().click(function(e){
				e.preventDefault();
				$(this).parent().parent().remove();
			});
			
			$('#custom_style_name').val('');
			$('#custom_style_element').val('');
			$('#custom_style_style').val('');
		});
	});
</script>
<script>
	//hot keys
	$(document).bind('keydown', 'ctrl+s', function(e){
		e.preventDefault();
	   $('#template_form').submit();
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
        	<h1><? echo $template['template_name']; ?></h1>
        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='template_form'>
            	<h2>Template Properties</h2>
            	<p>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=template_name' class='help_link'>?</a></sup><br />
                <input type='text' name='name' value='<? echo $template['template_name']; ?>'/></p>
                <p>Template:<br />
                <?
					//base location
					$template_location = $template['template_location'];
					//clean up location by removing install path
					//$template_location = str_replace(constant("ARCH_INSTALL_PATH"), "", $template_location);
					$template_location = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $template_location, 1);
					//append path to front end
					$template_location = constant("ARCH_FRONT_END_PATH").$template_location;
					$template_content = file_get_contents($template_location); //<-- replaced include so that php tags will not parse
				?>
                <textarea name='template' class='html_area ui_code_highlight'><? echo htmlspecialchars($template_content); ?></textarea></p>
                <p>Permissions:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=permissions_enforcement' class='help_link'>?</a></sup> <br />
                <select name='permissions'>
                	<option value='0'>None</option>
                </select></p>
                <p>Additional Fields Group:<br />
                <select name='additional_fields'>
                	<option value='0'>None</option>
                    <? 
						$field_groups = mysql_query('SELECT additional_field_group_ID,
														additional_field_group_name
													FROM tbl_additional_field_groups
													ORDER BY additional_field_group_name');
						
						while($group = mysql_fetch_assoc($field_groups)){
							$selected = '';
							if($template['template_additional_field_group_FK'] == $group['additional_field_group_ID']){
								$selected = 'selected="selected"';
							}
							?><option value='<? echo $group['additional_field_group_ID']; ?>' <? echo $selected; ?>><? echo $group['additional_field_group_name']; ?></option><?
						}
					?>
                </select></p>
                <input type='hidden' name='tid' value='<? echo $_GET['t']; ?>' />
                
                <h3>Custom CSS<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.3&topic=custom_css_styles' class='help_link'>?</a></sup></h3>
                <table class='styled_table' id='custom_styles_list'>
                <tr>
                    <th>Name</th>
                    <th>Element</th>
                    <th>Styles</th>
                    <th>Pending<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.3&topic=custom_css_pending_status' class='help_link'>?</a></sup></th>
                </tr>
                <?
					$current_custom_styles = $template['template_custom_styles'];
					$current_custom_styles = explode('||', $current_custom_styles);
					foreach($current_custom_styles as $style){
						$style_values = explode('|', $style);
						if(trim($style_values[0]) != ''){
						?>
							<tr>
                            	<td><? echo $style_values[0]; ?></td>
                                <td><? echo $style_values[1]; ?></td>
                                <td><? echo $style_values[2]; ?></td>
                                <td>
                                	<a href='' class='remove'>Remove</a>
                                	<input type="hidden" name="style_names[]" value="<? echo $style_values[0]; ?>"/>
                                    <input type="hidden" name="style_elements[]" value="<? echo $style_values[1]; ?>"/>
                                    <input type="hidden" name="style_styles[]" value="<? echo $style_values[2]; ?>"/>
                                </td>
                            </tr>
						<?
						}
					}
				?>
                </table>
                
                <input type='hidden' name='action' value='save' />
                <p><input type='submit' value='Save (ctrl+s)' /></p>
            </form>
            
            <h2>Add Custom Style</h2>
            <form action='' method='get' id='add_cutom_style'>
            	<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.3&topic=custom_css_name' class='help_link'>?</a></sup></label><br /><input type='text' id='custom_style_name' /></p>
                <p><label>Element:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.3&topic=custom_css_element' class='help_link'>?</a></sup></label><br /><input type='text' id='custom_style_element' /></p>
                <p><label>Style:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.3&topic=custom_css_styles_styles' class='help_link'>?</a></sup></label><br /><input type='text' id='custom_style_style' /></p>
            	<p><input type='submit' value='Add' /></p>
            </form>
            
            <h2>Supporting Files</h2>
            <p><a href='' id='upload'>Upload</a></p>
            <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='upload_form' enctype='multipart/form-data'>
            	<p>Upload file(s):<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=multiple_file_upload' class='help_link'>?</a></sup> <input type='file' name='files[]' multiple="" /></p>
                <input type='hidden' name='tid' value='<? echo $_GET['t']; ?>' />
                <input type='hidden' name='action' value='upload' />
                <p><input type='submit' value='Upload' /></p>
            </form>
            <div class='tri_column'>
            	<h3>Images:</h3>
                <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='upload_form' enctype='multipart/form-data'>
                    <p>
                    <?
                        //base location
                        $base_directory = $template['template_location'];
                        //clean up location by removing install path
                        $base_directory = preg_replace('['.constant("ARCH_INSTALL_PATH").']', '', $base_directory, 1);
                        
                        $base_directory = explode('/', $base_directory);
                        
                        //extract relevant file path information
                        $image_directory = constant("ARCH_FRONT_END_PATH").'templates/'.$base_directory[count($base_directory) - 2].'/images';
                        $script_directory = constant("ARCH_FRONT_END_PATH").'templates/'.$base_directory[count($base_directory) - 2].'/scripts';
                        $style_directory = constant("ARCH_FRONT_END_PATH").'templates/'.$base_directory[count($base_directory) - 2].'/styles';
                        
                        //array to hold files
                        $file_array = array();
                        
                        //make sure the directory can be opened
                        if ($directory_handler = opendir($image_directory)) {
                            /* This is the correct way to loop over the directory. <-- http://php.net/manual/en/function.readdir.php */
                            // parse directory
                            while(false !== ($file_ref = readdir($directory_handler))){
                                if($file_ref != '..' && $file_ref != '.'){//filter out directory addresses
                                    array_push($file_array, $file_ref);
                                }
                            }
                            
                            //close directory
                            closedir($directory_handler);
                        }
                        
                        //sort files alphabetically 
                        asort($file_array);
                        
                        foreach($file_array as $file_name){
                            echo '<label><input type="checkbox" name="files[]" value="'.$file_name.'"/>'.$file_name.'</label><br/>';
                        }
                    ?>
                    </p>
                    <input type='hidden' name='tid' value='<? echo $_GET['t']; ?>' />
                    <input type='hidden' name='action' value='delete_images' />
                    <p><input type='submit' value='Delete Checked Images' /></p>
            	</form>
            </div>
            <div class='tri_column'>
            	<h3>Scripts:</h3>
                <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='upload_form' enctype='multipart/form-data'>
                    <p>
                    <?
                        //array to hold files
                        $file_array = array();
                        
                        //make sure the directory can be opened
                        if ($directory_handler = opendir($script_directory)) {
                            // parse directory
                            while(false !== ($file_ref = readdir($directory_handler))){
                                if($file_ref != '..' && $file_ref != '.'){//filter out directory addresses
                                    array_push($file_array, $file_ref);
                                }
                            }
                            
                            //close directory
                            closedir($directory_handler);
                        }
                        
                        //sort files alphabetically 
                        asort($file_array);
                        
                        foreach($file_array as $file_name){
                            echo $file_name.'<br/>';
                        }
                    ?>
                    </p>
                	<input type='hidden' name='tid' value='<? echo $_GET['t']; ?>' />
                    <input type='hidden' name='action' value='delete_scripts' />
                    <p><input type='submit' value='Delete Checked Scripts' /></p>
            	</form>
            </div>
            <div class='tri_column'>
            	<h3>Styles:</h3>
                <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='upload_form' enctype='multipart/form-data'>
                    <p>
                    <?
                        //array to hold files
                        $file_array = array();
                        
                        //make sure the directory can be opened
                        if ($directory_handler = opendir($style_directory)) {
                            // parse directory
                            while(false !== ($file_ref = readdir($directory_handler))){
                                if($file_ref != '..' && $file_ref != '.'){//filter out directory addresses
                                    array_push($file_array, $file_ref);
                                }
                            }
                            
                            //close directory
                            closedir($directory_handler);
                        }
                        
                        //sort files alphabetically 
                        asort($file_array);
                        
                        foreach($file_array as $file_name){
                            echo $file_name.'<br/>';
                        }
                    ?>
                	</p>
                	<input type='hidden' name='tid' value='<? echo $_GET['t']; ?>' />
                    <input type='hidden' name='action' value='delete_styles' />
                    <p><input type='submit' value='Delete Checked Styles' /></p>
            	</form>
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