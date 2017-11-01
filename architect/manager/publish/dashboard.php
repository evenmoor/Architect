<? if(validatePermissions('system', 5)){ ?>
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
<title>Publication Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#add_document_form, #upload_media_form').slideUp(0);
		
		$('#toggle_document_form').click(function(e){
			e.preventDefault();
			$('#add_document_form').slideToggle();
		});
		
		$('#toggle_media_form').click(function(e){
			e.preventDefault();
			$('#upload_media_form').slideToggle();
		});
	});
</script>
<style type='text/css'>
	.dual_column{width:47%; margin:0 .5% 10px .5%; background:#eee;}
</style>
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
        		<h1>Publication Dashboard</h1>
            </div>
            <? if(validatePermissions('system', 7)){ ?>
                <div class='dual_column'>
                    <h2>Documents</h2>
                    
                    <? if($_SESSION['tutorial_mode'] == 1){ ?>
                    	<div class='tutorial'>
                        	<h2>Tutorial</h2>
                        	<p><a href='' class='display_transcript'>(Show Transcript)</a></p>
                        	<div class='transcript'>
                            	<p>Documents are the individual pages available on the site. Documents may be part of a group of related documents or single entities.</p>
                                <p>In order to add a new document:</p>
                                <ol>
                                	<li>Click <em>Add Document</em> link.</li>
                                    <li>Enter the document's name in the <em>Name</em> field.</li>
                                    <li>Choose either a document <em>Group</em> or a <em>Template</em> from the available dropdown fields. (You can specify both, but it is not nesscessary.)</li>
                                    <li>Enter the document's title in the <em>Title</em> field.</li>
                                    <li>Write the document's content into the <em>Content</em> field.</li>
                                    <li>Click <em>Add Document</em> button.</li>
                                </ol>
                            </div><!--/transcript-->
                            <p class='disable_tutorial_note'>These tutorials can be disabled in the <em><? echo $_SESSION['username']; ?>'s Settings</em> link.</p>
						</div><!--/tutorial-->
					<? } ?>
                    
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/dashboard/'>View All Documents ></a></em></p>
                    <p><strong><a href='' id='toggle_document_form'>Add Document:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/add_document/' id='add_document_form'>
                        <p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_name' class='help_link'>?</a></sup><br /><input type='text' name='name' /></label></p>
                        <p><label>Group:<br /><select name='group'>
                            <option value='0'>None</option>
                            <? 
                                $document_groups = mysql_query('SELECT document_group_ID,
                                                                    document_group_name
                                                                FROM tbl_document_groups
                                                                ORDER BY document_group_name'); 
                            
                                while($group = mysql_fetch_assoc($document_groups)){
                                    ?><option value='<? echo $group['document_group_ID']; ?>'><? echo $group['document_group_name']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        <p><label>Template:<br /><select name='template'>
                            <option value='0'>None</option>
                            <?
                                $templates = mysql_query('SELECT template_ID,
                                                                template_name
                                                            FROM tbl_templates
                                                            ORDER BY template_name');
                                while($template = mysql_fetch_assoc($templates)){
                                    ?><option value='<? echo $template['template_ID']; ?>'><? echo $template['template_name']; ?></option><?
                                }
                            ?>
                        </select></label></p>
                        <p><label>Title:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=document_title' class='help_link'>?</a></sup><br /><input type='text' name='title' /></label></p>
                        <p><label>Content:</label><br /> <textarea name='content' class='ui_wysiwyg'></textarea></p>
                        <p><input type='submit' value='Add Document' /></p>
                    </form>
                    <h3>Recent Documents</h3>
                    <?
                        $documents = mysql_query('SELECT document_ID,
                                                            document_group_FK,
                                                            document_is_home_page,
                                                            document_name
                                                        FROM tbl_documents
                                                        ORDER BY document_ID DESC
                                                        LIMIT 10');
                        
                        $counter = 0;
                        while($document = mysql_fetch_assoc($documents)){
                            $home = '';
                            if($document['document_is_home_page'] == 1){
                                $home = '(Home Page)';
                            }
                            
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?><div class='table_row <? echo $odd; ?>'>
                                <?
                                    $group_details = mysql_fetch_assoc(mysql_query('SELECT document_group_name,
                                                        document_group_additional_field_group_FK
                                                        FROM tbl_document_groups 
                                                        WHERE document_group_ID ="'.clean($document['document_group_FK']).'"
                                                        LIMIT 1'));
                                
                                    if(constant("ARCH_DOCUMENT_PATH_ID")){//check to see if ids are in page paths
                                        $document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$document['document_ID'].'-'.$document['document_name'].'/';
                                    }else{
                                        $document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$document['document_name'].'/';
                                    }
                                    
                                    if($group_details['document_group_name']){
                                        $document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group_details['document_group_name'].'/'.$document['document_ID'].'-'.$document['document_name'].'/';
                                    }
                                ?>
                            
                                <span class='table_column' style='width:60%;'><? echo $document['document_name']; ?> <? echo $home; ?></span>
                                <span class='table_column centered' style='width:40%;'>
                                <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/edit_document/?d=<? echo $document['document_ID']; ?>'>Edit</a> 
                                | <a href='<? echo $document_path; ?>'>View</a></span>
                            </div><?
                            $counter++;
                        }
                        
                    ?>
                </div><!--/documents column-->
            <? }//end document permission check ?>
            
            <? if(validatePermissions('system', 6)){ ?>
                <div class='dual_column'>
                    <h2>Media</h2>
                    
                    <? if($_SESSION['tutorial_mode'] == 1){ ?>
                    <div class='tutorial'>
                        <h2>Tutorial</h2>
                        <p><a href='' class='display_transcript'>(Show Transcript)</a></p>
                        <div class='transcript'>
                        	<p>Media are elements (images, videos, audio files, etc.) uploaded to the site which can be used on documents. A media file can be of any type but not folders and can be uploaded in groups on modern browsers.</p>
                            <p>To upload a media file to the site:</p>
                            <ol>
                            	<li>Click <em>Upload Media</em> link.</li>
                                <li>Click the button in the <em>Files</em> field.</li>
                                <li>Browse to the file on your computer.</li>
                                <li>If you are uploading more than one file hold down the ctrl key to select multiple files.</li>
                                <li>Click the <em>Open</em> button.</li>
                                <li>Choose a folder from the <em>To Folder:</em> field to which the files will be uploaded.</li>
                                <li>Click the <em>Upload Media</em> button.</li>
                            </ol>
                        </div><!--/transcript-->
                        <p class='disable_tutorial_note'>These tutorials can be disabled in the <em><? echo $_SESSION['username']; ?>'s Settings</em> link.</p>
                    </div><!--/tutorial-->
                    <? } ?>
                    
                    <p><em><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/dashboard/'>View All Media ></a></em></p>
                    <p><strong><a href='' id='toggle_media_form'>Upload Media:</a></strong></p>
                    <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/upload_media/' id='upload_media_form' enctype='multipart/form-data'>
                        <p>File(s)<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=multiple_file_upload' class='help_link'>?</a></sup>: <input type='file' name='files[]' multiple="" /></p>
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
                </div><!-- media column -->
            
            <? }//end media permission check ?>
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