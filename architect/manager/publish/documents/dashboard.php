<? if(validatePermissions('system', 7)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documents Dashboard | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script type='text/javascript'>
	$(function(){
		$('#add_document_form').slideUp(0);
		
		if($('#search_param').val() == ''){
			$('#search_form').slideUp(0);
		}
		
		$('#toggle_document_form').click(function(e){
			e.preventDefault();
			$('#add_document_form').slideToggle();
		});
		
		$('#toggle_search_form').click(function(e){
			e.preventDefault();
			$('#search_form').slideToggle();
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
        		<h1>Documents Dashboard</h1>
            </div>
            
            <div class='single_column'>
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
                    <div id='document_additional_fields'>
                    </div>
                    <p><input type='submit' value='Add Document' /></p>
                </form>
                
                <p><strong><a href='' id='toggle_search_form'>Search Documents:</a></strong></p>
                <form action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/dashboard/' method='post' id='search_form'>
                	<p><label for='search_param'>Search:</label><br />
                    <input type='text' id='search_param' name='search' value='<? if(isset($_REQUEST['search'])){  echo $_REQUEST['search']; } ?>'/></p>
                    <p><label for='search_target'>Target:</label><br />
                    <input type='radio' value='document_name' name='search_target' <? if(isset($_REQUEST['search_target']) && $_REQUEST['search_target'] == 'document_name'){ ?>checked="checked"<? } ?> />Name<br />
                    <input type='radio' value='document_body' name='search_target' <? if(isset($_REQUEST['search_target']) && $_REQUEST['search_target'] == 'document_body'){ ?>checked="checked"<? } ?> />Content</p>
                    <p><input type='submit' class='ui_button' value='Search' /></p>
                    <? if(isset($_REQUEST['search_target']) && isset($_REQUEST['search']) && trim($_REQUEST['search']) != ''){ ?>
                    <p><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/dashboard/'>(Clear Search Parameters)</a></p>
                    <? } ?>
                </form>
                <?
					//default filter string
					$filter = $link_search_parameters = '';
					if(isset($_REQUEST['search_target']) && isset($_REQUEST['search']) && trim($_REQUEST['search']) != ''){
						$target_field = 'document_name';
						if($_REQUEST['search_target'] == 'document_body'){
							$target_field = 'document_content_en';
						}
						$filter = 'WHERE '.$target_field.' LIKE "%'.clean($_REQUEST['search']).'%"';
						
						$link_search_parameters = '&search_target='.$_REQUEST['search_target'].'&search='.$_REQUEST['search'];
					}
				
					//default database sort string
					$sort = 'ORDER BY document_name ASC';
					
					//default sort strings
					$name_sort = 'name';
					$updated_sort = 'updated';
					$created_sort = 'created';
					
					//listen for passed sorts and adjust
					if(isset($_GET['sort'])){
						switch($_GET['sort']){
							case 'name':
								$sort = 'ORDER BY document_name ASC';
								$name_sort = 'name-desc';
							break;
							case 'name-desc':
								$sort = 'ORDER BY document_name DESC';
								$name_sort = 'name';
							break;
							case 'updated':
								$sort = 'ORDER BY document_modified ASC';
								$updated_sort = 'updated-desc';
							break;
							case 'updated-desc':
								$sort = 'ORDER BY document_modified DESC';
								$updated_sort = 'updated';
							break;
							case 'created':
								$sort = 'ORDER BY document_created ASC';
								$created_sort = 'created-desc';
							break;
							case 'created-desc':
								$sort = 'ORDER BY document_created DESC';
								$created_sort = 'created';
							break;
						}
					}
					
					$documents = mysql_query('SELECT document_ID,
											 			document_group_FK,
														document_created,
											 			document_is_home_page,
											 			document_name,
														document_modified
													FROM tbl_documents
													'.$filter.'
													'.$sort.'');
					$counter = 1;
					?>
                    <div class='table_row'>
                    	<span class='table_column centered' style='width:40%'><strong><a href='?sort=<? echo $name_sort.$link_search_parameters; ?>'>Name</a></strong></span>
                        <span class='table_column centered' style='width:20%'><strong><a href='?sort=<? echo $updated_sort.$link_search_parameters; ?>'>Updated</a></strong></span>
                        <span class='table_column centered' style='width:20%'><strong><a href='?sort=<? echo $created_sort.$link_search_parameters; ?>'>Created</a></strong></span>
                    	<span class='table_column centered' style='width:20%'><strong>Controls</strong></span>
					</div>
					<?
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
									if(constant("ARCH_DOCUMENT_PATH_ID")){//check to see if ids are in page paths
										$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group_details['document_group_name'].'/'.$document['document_ID'].'-'.$document['document_name'].'/';
									}else{
										$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_GROUP").'/'.$group_details['document_group_name'].'/'.$document['document_name'].'/';
									}
								}
								
								$created = strtotime($document['document_created']);
								$modified = strtotime($document['document_modified']);
							?>
                            <span class='table_column centered' style='width:40%'><? echo $document['document_name']; ?> <? echo $home; ?></span>
                            <span class='table_column centered' style='width:20%'><? echo date('M d Y g:ia', $modified);?></span>
                            <span class='table_column centered' style='width:20%'><? echo date('M d Y g:ia', $created);?></span>
                            <span class='table_column centered' style='width:20%'>
                            	<a href='<? echo $document_path; ?>'>View</a> | 
                            	<a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/edit_document/?d=<? echo $document['document_ID']; ?>'>Edit</a> | 
                                <a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/delete_document/?d=<? echo $document['document_ID']; ?>'  class='confirm'>Delete</a>
                            </span>
                        </div><?
						$counter++;
					}
					
				?>
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