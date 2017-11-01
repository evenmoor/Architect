<? if(validatePermissions('system', 15)){ ?>
<?
	if(isset($_POST['action'])){
		switch($_POST['action']){
			case 'update':
				$userList=$_POST['users'];
							
				while(list ($key,$val) = @each ($userList)) { 
					$new_status = $_POST['status-'.$val];
					$new_group = $_POST['group-'.$val];
					mysql_query("UPDATE tbl_users 
					SET user_user_status_FK ='".clean($new_status)."', 
					user_user_group_FK='".clean($new_group)."' 
					WHERE user_ID = ".$val." 
					LIMIT 1");
					systemLog('User: '.$val.' updated | status '.$newStatus.'| type '.$newType.'');
				}
			break;
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Users and Permissions | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script>
	$(function(){
		$('#add_user_group_form').slideUp(0);
		
		$('#add_user_group').click(function(e){
			e.preventDefault();
			$('#add_user_group_form').slideToggle();
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
        
         <div id='section_navigation'>
        	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/admin_section_navigation.php'); ?>
        </div><!--/section_navigation-->
        
        <div class='content with_section_nav'>
        	<div class='single_column'>
        		<h1>User and Permissions Dashboard</h1>
            </div>
            
            <div class='single_column'>
            	<h2>User Groups</h2>
                
                <div class='table_row'>
                    <span class='table_column' style='width:25%;'><strong>Group Name</strong></span>
                    <span class='table_column' style='width:35%;'><strong>Description</strong></span>
                    <span class='table_column' style='width:30%;'><strong>Permissions List</strong></span>
                    <span class='table_column' style='width:10%;'></span>
                </div>
                
                <?
					$counter = 1;
					
					$user_groups = mysql_query('SELECT user_group_ID,
														user_group_name,
														user_group_description
													FROM tbl_user_groups
													ORDER BY user_group_name');
					
					$permission_entities = mysql_query('SELECT site_permission_entity_id,
															site_permission_entity
														FROM tbl_site_permission_entities
														ORDER BY site_permission_entity');
										
					while($group = mysql_fetch_assoc($user_groups)){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						?>
							<div class='table_row <? echo $odd; ?>'>
								<span class='table_column' style='width:25%;'><? echo $group['user_group_name']; ?></span>
                                <span class='table_column' style='width:35%;'><? echo $group['user_group_description']; ?></span>
                                <?
									$permission_list = '&nbsp;';
									if($group['user_group_ID'] == 1){//admins have complete control so no need to look them up
										$permission_list = 'All Screens and Content';
									}else{
										//array holding current permissions
										$current_permission_keys = array();
										
										//find permissions for the group
										$group_permissions = mysql_query('SELECT site_permission_ID,
																				tbl_site_permission_entities.site_permission_entity_id,
																				 tbl_site_permission_entities.site_permission_entity
																			FROM tbl_site_permissions 
																			LEFT JOIN tbl_site_permission_entities ON tbl_site_permissions.site_permission_entity_FK = tbl_site_permission_entities.site_permission_entity_id
																			WHERE site_permission_type_FK = "1"
																				AND site_permission_value = "'.clean($group['user_group_ID']).'"
																			ORDER BY site_permission_entity');
										if(mysql_num_rows($group_permissions) > 0){//if permissions were found
											$permission_list = '<ul>';
											while($permission = mysql_fetch_assoc($group_permissions)){//build permission list
												array_push($current_permission_keys, $permission['site_permission_entity_id']);
												$permission_list .= '<li>'.$permission['site_permission_entity'].' - <a href="'.constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/delete_user_group_permission/?p='.$permission['site_permission_ID'].'"  class="confirm">Delete</a></li>';
											}
											$permission_list .= '</ul>';
										}
										
										//add list of permissions that can be added
										$permission_list .= '<form action="'.constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/users/add_user_group_permission/" method="post"><p><select name="permission"><option value="">None</option>';
										mysql_data_seek($permission_entities, 0);
										while($entity = mysql_fetch_assoc($permission_entities)){
											if(!in_array($entity['site_permission_entity_id'], $current_permission_keys)){//if the current group doesn't already have access make it an option
												$permission_list .= '<option value="'.$entity['site_permission_entity_id'].'">'.$entity['site_permission_entity'].'</option>';
											}
										}
										$permission_list .= '</select><input type="hidden" name="group" value="'.$group['user_group_ID'].'"/><input type="submit" value="Add"/></p></form>';
									}
								?>
                                <span class='table_column' style='width:30%;'><? echo $permission_list; ?></span>
                                <span class='table_column' style='width:10%;'>
                                <? if($group['user_group_ID'] != 1 && $group['user_group_ID'] != 4){ //admin group can not be removed ?>
                                	<a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/users/delete_user_group/?g=<? echo $group['user_group_ID']; ?>'  class='confirm'>Delete Group</a>
                                <? } ?>
                                </span>
							</div>
						<?
						
						$counter++;
					}
				?>
                <div class='single_column'>
                <h3><a href='' id='add_user_group'>Add User Group</a></h3>
                <form method='post' action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/users/add_user_group/' id='add_user_group_form'>
                	<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.5&topic=user_group_name' class='help_link'>?</a></sup><br /><input type='text' name='name' /></label></p>
                    <p><label>Description:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.5&topic=user_group_description' class='help_link'>?</a></sup><br /><input type='text' name='description' /></label></p>
                    <p><input type='submit' value='Add User Group' /></p>
                </form>
                </div>
            </div>
            
            <div class='single_column'>
            	<h2>Users</h2>
                <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
					<?
                        $users = mysql_query('SELECT tbl_users.user_ID,
													tbl_users.user_user_status_FK,
													tbl_users.user_user_group_FK,
													tbl_users.user_username,
													tbl_user_data.user_data_first_name,
													tbl_user_data.user_data_middle_name,
													tbl_user_data.user_data_last_name
                                                FROM tbl_users
													INNER JOIN tbl_user_data ON tbl_users.user_ID = tbl_user_data.user_data_user_FK
                                                ORDER BY user_username');
												
						$user_statuses = mysql_query('SELECT user_status_ID,
														user_status
														FROM tbl_user_statuses
														ORDER BY user_status');
														
						$user_groups = mysql_query('SELECT user_group_ID,
														user_group_name
														FROM tbl_user_groups
														ORDER BY user_group_name');
						
                        $counter = 0;					
                        while($user = mysql_fetch_assoc($users)){
                            $odd = '';
                            if($counter  % 2 != 0){
                                $odd = 'odd';
                            }
                            
                            ?>
                                <div class='table_row <? echo $odd; ?>'>
                                    <span class='table_column centered' style='width:5%;'><input type='checkbox' name='users[]' value='<? echo $user['user_ID']; ?>' /></span>
                                    <span class='table_column centered' style='width:30%;'><? echo $user['user_username']; ?> (<? echo $user['user_data_last_name']; ?>, <? echo $user['user_data_first_name']; ?><? if(trim($user['user_data_middle_name']) != ""){echo " ".$user['user_data_middle_name'];} ?>) </span>
    								<span class='table_column centered' style='width:20%;'><select name='status-<? echo $user['user_ID']; ?>' style='min-width:1px; width:100%;'>
                                    	<?
											mysql_data_seek($user_statuses, 0);
											while($status = mysql_fetch_assoc($user_statuses)){
												$selected = '';
												if($status['user_status_ID'] == $user['user_user_status_FK']){
													$selected = 'selected="selected"';
												}
												?><option value='<? echo $status['user_status_ID']; ?>' <? echo $selected; ?>><? echo $status['user_status']; ?></option><?
											}
										?>
                                    </select></span>
                                    <span class='table_column centered' style='width:35%;'><select name='group-<? echo $user['user_ID']; ?>' style='min-width:1px; width:100%;'>
                                    	<?
											mysql_data_seek($user_groups, 0);
											while($group = mysql_fetch_assoc($user_groups)){
												$selected = '';
												if($group['user_group_ID'] == $user['user_user_group_FK']){
													$selected = 'selected="selected"';
												}
												?><option value='<? echo $group['user_group_ID']; ?>' <? echo $selected; ?>><? echo $group['user_group_name']; ?></option><?
											}
										?>
                                    </select></span>
                                    <span class='table_column centered' style='width:10%;'>
                                    	<? if($user['user_ID'] != $_SESSION['user_ID']){ ?>
                                    		<a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/users/delete_user/?u=<? echo $user['user_ID']; ?>' class='confirm'>Delete</a>
                                        <? } ?>
                                    </span>
                                </div>
                            <?
                            
                            $counter++;
                        }
                    ?>
                    <input type='hidden' name='action' value='update' />
                    
                    <div class='single_column'>
               		  <p><input type='submit' value='Update Selected Users' /></p>
                  </div>
                </form>
            </div><!--/users-->
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