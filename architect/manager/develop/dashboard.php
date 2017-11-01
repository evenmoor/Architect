<? if(validatePermissions('system', 9)){ ?>
<?
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
	$folder_list = generateFolderList($ftp_connection, constant("FTP_ROOT").'media'.'/repository');
	
	//close ftp connection
	ftp_close($ftp_connection);
	
	asort($folder_list);
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Development Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#add_template_form, #add_block_form, #add_menu_form, #add_additional_fields_form, #add_group_form, #upload_media_form, #add_form_form').slideUp(0);
		
		$('#toggle_additional_field_groups_form').click(function(e){
			e.preventDefault();
			$('#add_additional_fields_form').slideToggle();
		});
		
		$('#toggle_form_form').click(function(e){
			e.preventDefault();
			$('#add_form_form').slideToggle();
		});
		
		$('#toggle_template_form').click(function(e){
			e.preventDefault();
			$('#add_template_form').slideToggle();
		});
		
		$('#toggle_block_form').click(function(e){
			e.preventDefault();
			$('#add_block_form').slideToggle();
		});
		
		$('#toggle_menu_form').click(function(e){
			e.preventDefault();
			$('#add_menu_form').slideToggle();
		});
		
		$('#toggle_group_form').click(function(e){
			e.preventDefault();
			$('#add_group_form').slideToggle();
		});
		
		$('#toggle_media_form').click(function(e){
			e.preventDefault();
			$('#upload_media_form').slideToggle();
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
        		<h1>Development Dashboard</h1>
            </div>
            
            <? if(validatePermissions('system', 10)){ ?>
                <div class='quad_column'>
                    <h2>Templates</h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/dashboard/'>View All Templates ></a></em></p>
                    <p><strong><a href='' id='toggle_template_form'>Add Template:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/add_template/' id='add_template_form'>
                        <p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=template_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                        <p><input type='submit' value='Add Template' /></p>
                    </form>
                    <h3>Recent Templates:</h3>
                    <?
                        $templates = mysql_query('SELECT template_ID,
                                                            template_name
                                                        FROM tbl_templates
                                                        ORDER BY template_ID DESC
                                                        LIMIT 10');
                        
                        $counter = 0;
                        while($template = mysql_fetch_assoc($templates)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <span class='table_column' style='width:60%;'><? echo $template['template_name']; ?></span>
                                <span class='table_column centered' style='width:40%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/edit_template/?t=<? echo $template['template_ID']; ?>'>Edit</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/templates-->
            <? }// end template permission check ?>
            
            <? if(validatePermissions('system', 11)){ ?>
                <div class='quad_column'>
                    <h2>Menus</h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/dashboard/'>View All Menus ></a></em></p>
                    <p><strong><a href='' id='toggle_menu_form'>Add Menu:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/add_menu/' id='add_menu_form'>
                        <p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                        <p><input type='submit' value='Add Menu' /></p>
                    </form>
                    <h3>Recent Menus:</h3>
                    <?
                        $menus = mysql_query('SELECT navigation_menu_ID,
                                                        navigation_menu_name
                                                    FROM tbl_navigation_menus 
                                                    ORDER BY navigation_menu_ID DESC
                                                    LIMIT 10');
                        
                        $counter = 0;
                        while($menu = mysql_fetch_assoc($menus)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <span class='table_column' style='width:60%;'><? echo $menu['navigation_menu_name']; ?></span>
                                <span class='table_column centered' style='width:40%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/edit_menu/?m=<? echo $menu['navigation_menu_ID']; ?>'>Edit</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/Navigation Menus-->
            <? }//end menu permission check ?>
            
            <? if(validatePermissions('system', 13)){ ?>
                <div class='quad_column'>
                    <h2>Blocks</h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/blocks/dashboard/'>View All Blocks ></a></em></p>
                    <p><strong><a href='' id='toggle_block_form'>Add Block:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/blocks/add_block/' id='add_block_form'>
                        <p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=block_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                        <p><input type='submit' value='Add Block' /></p>
                    </form>
                    <h3>Recent Blocks:</h3>
                    <?
                        $blocks = mysql_query('SELECT block_ID,
                                                        block_name
                                                    FROM tbl_blocks 
                                                    ORDER BY block_ID DESC
                                                    LIMIT 10');
                        
                        $counter = 0;
                        while($block = mysql_fetch_assoc($blocks)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <span class='table_column' style='width:60%;'><? echo $block['block_name']; ?></span>
                                <span class='table_column centered' style='width:40%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/blocks/edit_block/?b=<? echo $block['block_ID']; ?>'>Edit</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/Blocks-->
            <? }// end block permission check ?>
            
            <? if(validatePermissions('system', 16)){ ?>
                <div class='quad_column'>
                    <h2>Forms</h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/forms/dashboard/'>View All Forms ></a></em></p>
                    <p><strong><a href='' id='toggle_form_form'>Add Form:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/forms/add_form/' id='add_form_form'>
                        <p><label>Name: <input type='text' name='name' /></label></p>
                        <p><input type='submit' value='Add Form' /></p>
                    </form>
                    <h3>Recent Forms:</h3>
                    <?
                        $forms = mysql_query('SELECT form_ID,
                                                        form_name
                                                    FROM tbl_forms 
                                                    ORDER BY form_ID DESC
                                                    LIMIT 10');
                        
                        $counter = 0;
                        while($form = mysql_fetch_assoc($forms)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <span class='table_column' style='width:60%;'><? echo $form['form_name']; ?></span>
                                <span class='table_column centered' style='width:40%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/forms/edit_form/?f=<? echo $form['form_ID']; ?>'>Edit</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/Blocks-->
            <? }// end block permission check ?>

			<? if(validatePermissions('system', 14)){ ?>
                <div class='quad_column left_column'>
                    <h2>Additional Field Groups</h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/dashboard/'>View All Additional Field Groups ></a></em></p>
                    <p><strong><a href='' id='toggle_additional_field_groups_form'>Add Additional Field Group:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/add_field_group/' id='add_additional_fields_form'>
                        <p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_groups_name' class='help_link'>?</a></sup> <input type='text' name='name' /></label></p>
                        <p><input type='submit' value='Add Additional Field Group' /></p>
                    </form>
                    <h3>Recent Additional Field Groups:</h3>
                    <?
                        $groups = mysql_query('SELECT additional_field_group_ID,
                                                        additional_field_group_name
                                                    FROM tbl_additional_field_groups 
                                                    ORDER BY additional_field_group_ID DESC
                                                    LIMIT 10');
                        
                        $counter = 0;
                        while($group = mysql_fetch_assoc($groups)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <span class='table_column' style='width:60%;'><? echo $group['additional_field_group_name']; ?></span>
                                <span class='table_column centered' style='width:40%;'><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/edit_field_group/?g=<? echo $group['additional_field_group_ID']; ?>'>Edit</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/Field Groups-->
            <? }//end additional field group check ?>
            
            <? if(validatePermissions('system', 12)){ ?>
                <div class='quad_column'>
                    <h2>Document Groups</h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/document_groups/dashboard/'>View All Document Groups ></a></em></p>
                    <p><strong><a href='' id='toggle_group_form'>Add Document Group:</a></strong></p>
                    <?
                        $templates = mysql_query('SELECT template_ID,
                                                 template_name
                                                 FROM tbl_templates
                                                 ORDER BY template_name');
                        
                        $additional_fields = mysql_query('SELECT additional_field_group_ID,
                                                         additional_field_group_name
                                                         FROM tbl_additional_field_groups
                                                         ORDER BY additional_field_group_name');
                    ?>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/document_groups/add_group/' id='add_group_form'>
                        <p><label>Name:<br />
                        <input type='text' name='name' /></label></p>
                        <p><label>Group Template:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=group_template' class='help_link'>?</a></sup><br />
                        <select name='template-group'>
                            <option value='0'>Select One</option>
                            <?
                                while($template = mysql_fetch_assoc($templates)){
                                    ?><option value='<? echo $template['template_ID']; ?>'><? echo $template['template_name']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        <p><label>Single Template:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=group_single_template' class='help_link'>?</a></sup><br />
                        <select name='template-single'>
                            <option value='0'>Select One</option>
                            <?
                                mysql_data_seek($templates, 0);
                                while($template = mysql_fetch_assoc($templates)){
                                    ?><option value='<? echo $template['template_ID']; ?>'><? echo $template['template_name']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        <p><label>Additional Fields:<br />
                        <select name='additional_fields'>
                            <option value='0'>None</option>
                            <?
                                while($field = mysql_fetch_assoc($additional_fields)){
                                    ?><option value='<? echo $field['additional_field_group_ID']; ?>'><? echo $field['additional_field_group_name']; ?></option><?
                                }
                            ?>
                        </select></label>
                        <p><input type='submit' value='Add Group' /></p>
                    </form>
                    
                    <h3>Recent Document Groups:</h3>
                    <?
                        $groups = mysql_query('SELECT document_group_ID,
                                                        document_group_name
                                                    FROM  tbl_document_groups 
                                                    ORDER BY document_group_ID DESC
                                                    LIMIT 10');
                        
                        $counter = 0;
                        while($group = mysql_fetch_assoc($groups)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <span class='table_column' style='width:60%;'><? echo $group['document_group_name']; ?></span>
                                <span class='table_column centered' style='width:40%;'><a href='<?  echo  constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group['document_group_name'].'/' ?>'>View</a> | <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/document_groups/edit_group/?g=<? echo $group['document_group_ID']; ?>'>Edit</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/Document Groups-->
            <? }// end document group permission check ?>
            
            <? if(validatePermissions('system', 6)){ ?>
                <div class='quad_column'>
                    <h2>General File Repository<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.4&topic=Repository' class='help_link'>?</a></sup></h2>
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/dashboard/?p=/repository/'>View Files In Repository ></a></em></p>
                    <p><strong><a href='' id='toggle_media_form'>Upload Media:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/upload_media/' id='upload_media_form' enctype='multipart/form-data'>
                        <p>File(s)<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=multiple_file_upload' class='help_link'>?</a></sup>: <input type='file' name='files[]' multiple="" /></p>
                        <p>To Repository Folder: <select name='target'>
                            <option value='/repository/'>/repository/</option>
                            <? 
                                foreach($folder_list as $folder){ 
                                    ?><option value='<? echo $folder; ?>/'><? echo $folder; ?>/</option><?
                                }
                            ?>
                        </select></p>
                        <p><input type='submit' value='Upload Media' /></p>
                    </form>
                </div><!--/Repository-->
            <? }// end media permission check ?>
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