<? if(validatePermissions('system', 14)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'edit':
				//update element
				mysql_query('UPDATE tbl_additional_fields
							SET additional_field_name = "'.clean($_POST['name']).'",
							additional_field_is_required = "'.clean($_POST['required']).'"
							WHERE additional_field_ID = "'.clean($_POST['fid']).'"
							LIMIT 1');
				systemLog('additional field updated id# '.clean($_POST['fid']).'.');
			break;//end edit
			
			case 'add':
				//add element
				mysql_query('INSERT INTO tbl_additional_fields(additional_field_ID,
															   additional_field_additional_field_group_FK,
															   additional_field_name,
															   additional_field_is_required)
															VALUES(NULL,
															   "'.clean($_POST['gid']).'",
															   "'.clean($_POST['name']).'",
															   "'.clean($_POST['required']).'")');
				systemLog('additional field added id# '.clean(mysql_insert_id()).'.');
			break;//end add
			
			case 'delete':
				//remove item from database
				mysql_query('DELETE FROM tbl_additional_fields 
									WHERE additional_field_ID = "'.clean($_POST['fid']).'"
									LIMIT 1');
				
				systemLog('additional field deleted id# '.clean($_POST['fid']).'.');
				
				//clean up affected table
				mysql_query('OPTIMIZE TABLE tbl_additional_fields');
			break;//end delete
			
			case 'save':
				//update database element
				mysql_query('UPDATE tbl_additional_field_groups
							 SET additional_field_group_name = "'.clean($_POST['name']).'"
							 WHERE additional_field_group_ID = "'.clean($_POST['gid']).'"
							 LIMIT 1');
				
				systemLog('additional field group updated id# '.clean($_POST['gid']).'.');
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	$group = mysql_fetch_assoc(mysql_query('SELECT * FROM tbl_additional_field_groups WHERE additional_field_group_ID="'.clean($_GET['g']).'" LIMIT 1'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Field Group (<? echo $group['additional_field_group_name']; ?>) | Manage Architect</title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
<script>
	function refreshHelpLinks(){
		$('#modify_fields a.help_link').click(function(e){
			e.preventDefault();
		});
		
		$('#modify_fields a.help_link').mouseenter(function(e){
			var help_url = $(this).attr('href');
			var link_position = $(this).position();
			var vertical_offset = $(this).height();
			var horizontal_offset = $(this).width();
			
			$('#help_link_div').html("Loading...");
			$('#help_link_div').css({'top'	:	(e.pageY + horizontal_offset),
									'left'	:	(e.pageX + vertical_offset)});
			
			$('#help_link_div').fadeIn(help_fade_speed);
			
			$.ajax({
				url: help_url
			}).done(function(content) {
				$('#help_link_div').html(content);
			});
		});
		
		$('#modify_fields a.help_link').mouseleave(function(){
			$('#help_link_div').fadeOut(help_fade_speed);
		});
	}

	$(function(){
		$('#add_field_form').slideUp(0);
		
		$('#toggle_add_field_form').click(function(e){
			e.preventDefault();
			$('#add_field_form').slideToggle();
		});
		
		//hide modify form
		$('#modify_menu').fadeOut(0);
		$('#modify_menu [name="delete"]').fadeOut(0);
		
		//handles cancel
		$('#modify_menu [name="cancel"]').click(function(){
			$('#modify_menu').fadeOut();
		});
		
		$('#modify_menu [name="delete"]').click(function(){
			$('#modify_menu [name="delete"]').fadeOut(0);
			$('#modify_fields').html('');
			
			//rewrite modify menu form
			$('#modify_menu [name="action"]').val("delete");
			$('#modify_menu [type="submit"]').val("Confirm");
			$('#modify_menu h2').html("Confirm Deletion");											 
		});
		
		$('ul.fields a').click(function(e){
			e.preventDefault();
			//get value of this node
			var node = $(this).attr('id');
			
			var position = $(this).position();
			var offset = $(this).outerHeight(true);
	
			var page_location = $(this).attr('href');
			$.ajax(page_location, function(data) {
				$('#modify_fields').html('<p>Loading...</p>');
			})
			.done(function(data){
				$('#modify_fields').html(data);
				refreshHelpLinks();
			});
			//rewrite modify menu form
			$('#modify_menu [name="fid"]').val(node);
			$('#modify_menu [name="action"]').val("edit");
			$('#modify_menu [type="submit"]').val("Save");
			$('#modify_menu h2').html("Edit Field");
			
			//reposition menu
			$('#modify_menu').css({	"left"	:	position.left,
									"top"	:	(position.top + offset)});
			
			//reset button 
			$('#modify_menu [name="delete"]').fadeIn(0);
			
			//fade menu in
			$('#modify_menu').fadeIn();
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
        	<h1><? echo $group['additional_field_group_name']; ?></h1>
        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
            	<h2>Field Group Properties</h2>
            	<p>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_groups_name' class='help_link'>?</a></sup><br />
                <input type='text' name='name' value='<? echo $group['additional_field_group_name']; ?>'/></p>
                <input type='hidden' name='gid' value='<? echo $_GET['g']; ?>' />
                <input type='hidden' name='action' value='save' />
                <p><input type='submit' value='Save' /></p>
            </form>
            
            <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='modify_menu'>
            	<h2></h2>
                <div id='modify_fields'>
                </div>
                <input type='hidden' name='fid' value='' />
                <input type='hidden' name='action' value='' />
                <p><input type='submit' value='Add' /> <input type='button' name='delete' value='Delete' /> <input type='button' name='cancel' value='Cancel'/></p>
            </form>
            
            <h2>Fields</h2>
            <? 
				$fields = mysql_query('SELECT *
									  FROM tbl_additional_fields
									  WHERE additional_field_additional_field_group_FK = "'.$group['additional_field_group_ID'].'"
									  ORDER BY additional_field_name');
			?>
            <ul class='fields'>
            	<?
					while($field = mysql_fetch_assoc($fields)){
						$required = '';
						if($field['additional_field_is_required'] == '1'){
							$required = '*';
						}
						
						?><li><a id='<? echo $field['additional_field_ID']; ?>' href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/edit_field/?fid=<? echo $field['additional_field_ID']; ?>'><? echo $field['additional_field_name']; ?><? echo $required; ?></a></li><?
					}
				?>
            </ul>
            <p>*Required Field</p>
            
            <h3><a href='' id='toggle_add_field_form'>Add Field</a></h3>
            <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='add_field_form'>
            	<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_name' class='help_link'>?</a></sup></label><br />
                <input type='text' name='name'/></p>
                <p><label>Field is required:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_is_required' class='help_link'>?</a></sup><br />
                <select name='required'>
                <option value='0'>No</option>
                <option value='1'>Yes</option>
                </select></label></p>
                <input type='hidden' name='gid' value='<? echo $_GET['g']; ?>' />
                <input type='hidden' name='action' value='add' />
                <p><input type='submit' value='Add Field' /></p>
            </form>
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