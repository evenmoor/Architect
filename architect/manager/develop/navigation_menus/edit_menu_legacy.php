<? if(validatePermissions('system', 11)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'edit':
				//build target
				$target = '';
				
				if($_POST['target_type'] == 'document'){
					$target = $_POST['target_doc'];
				}else{
					$target = $_POST['target_man'];
				}
			
				//update database element
				mysql_query('UPDATE tbl_navigation_menu_items
							SET navigation_menu_item_position = "'.clean($_POST['position']).'",
							navigation_menu_item_link_target = "'.clean($target).'",
							navigation_menu_item_class = "'.clean($_POST['class']).'",
							navigation_menu_item_name_en = "'.clean($_POST['name']).'",
							navigation_menu_item_title_en = "'.clean($_POST['title']).'"
							WHERE navigation_menu_item_ID="'.clean($_POST['niid']).'"
							LIMIT 1');
				
				systemLog('menu item updated id# '.clean($_POST['niid']).'.');
			break;//end edit
			
			case 'add':
				//build target
				$target = '';
				
				if($_POST['target_type'] == 'document'){
					$target = $_POST['target_doc'];
				}else{
					$target = $_POST['target_man'];
				}
			
				//build FK
				$parent_FK = 'NULL';
				if($_POST['niid'] != 0){
					$parent_FK = '"'.clean($_POST['niid']).'"';
				}
			
				//add item to database
				mysql_query('INSERT INTO tbl_navigation_menu_items(navigation_menu_item_ID,
																   navigation_menu_item_navigation_menu_FK,
																   navigation_menu_item_parent_menu_item_FK,
																   navigation_menu_item_position,
																   navigation_menu_item_link_target,
																   navigation_menu_item_class,
																   navigation_menu_item_name_en,
																   navigation_menu_item_title_en)
																VALUES(NULL,
																   "'.clean($_POST['mid']).'",
																   '.$parent_FK.',
																   "'.clean($_POST['position']).'",
																   "'.clean($target).'",
																   "'.clean($_POST['class']).'",
																   "'.clean($_POST['name']).'",
																   "'.clean($_POST['title']).'")');
				systemLog('menu item added id# '.clean(mysql_insert_id()).'.');
			break;//end add
			
			case 'delete':
				//remove item from database
				mysql_query('DELETE FROM tbl_navigation_menu_items 
									WHERE navigation_menu_item_ID = "'.clean($_POST['niid']).'"
									LIMIT 1');
				
				systemLog('menu item removed id# '.clean($_POST['niid']).'.');
				
				//clean up affected table
				mysql_query('OPTIMIZE TABLE tbl_navigation_menu_items');
			break;//end delete
			
			case 'save':
				//update database element
				mysql_query('UPDATE tbl_navigation_menus
							 SET navigation_menu_name = "'.clean($_POST['name']).'"
							 WHERE navigation_menu_ID = "'.clean($_POST['mid']).'"
							 LIMIT 1');
				
				systemLog('menu updated id# '.clean($_POST['mid']).'.');
			break;//end save
		}
	}
?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<?
	$menu = mysql_fetch_assoc(mysql_query('SELECT * FROM tbl_navigation_menus WHERE navigation_menu_ID="'.clean($_GET['m']).'" LIMIT 1'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Menu (<? echo $menu['navigation_menu_name']; ?>) | <? echo $site_settings['name']; ?></title>
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
	//add the add item links to the end of each list
	$('ul.menu_list').append('<li><a href="" class="add_item">(+)</a></li>');
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
	
	//add a menu item
	$('a.add_item').click(function(e){
		e.preventDefault();
		//get value of parent node
		var node = $(this).closest('ul.menu_list');
		node = node.attr('id');
		
		var position = $(this).position();
		var offset = $(this).outerHeight(true);
		
		var page_location = '<? echo constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/navigation_menus/add_menu_item/'; ?>';
		$.ajax(page_location, function(data) {
			$('#modify_fields').html('<p>Loading...</p>');
		})
		.done(function(data){
			$('#modify_fields').html(data);
			refreshHelpLinks();
		});
		
		//rewrite modify menu form
		$('#modify_menu [name="niid"]').val(node);
		$('#modify_menu [name="action"]').val("add");
		$('#modify_menu [type="submit"]').val("Add");
		$('#modify_menu h2').html("Add Item");
		
		//reposition menu
		$('#modify_menu').css({	"left"	:	position.left,
							  	"top"	:	(position.top + offset)});
		
		//reset button 
		$('#modify_menu [name="delete"]').fadeOut(0);
		
		//fade menu in
		$('#modify_menu').fadeIn();
	});
	
	//edit a menu item
	$('a.edit_item').click(function(e){
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
		$('#modify_menu [name="niid"]').val(node);
		$('#modify_menu [name="action"]').val("edit");
		$('#modify_menu [type="submit"]').val("Save");
		$('#modify_menu h2').html("Edit Item");
		
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
        	<h1><? echo $menu['template_name']; ?></h1>
        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
            	<h2>Menu Properties</h2>
            	<p>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_name' class='help_link'>?</a><br />
                <input type='text' name='name' value='<? echo $menu['navigation_menu_name']; ?>'/></p>
                <input type='hidden' name='mid' value='<? echo $_GET['m']; ?>' />
                <input type='hidden' name='action' value='save' />
                <p><input type='submit' value='Save' /></p>
            </form>
            
            <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='modify_menu'>
            	<h2></h2>
                <div id='modify_fields'>
                </div>
            	<input type='hidden' name='mid' value='<? echo $_GET['m']; ?>' />
                <input type='hidden' name='niid' value='' />
                <input type='hidden' name='action' value='' />
                <p><input type='submit' value='Add' /> <input type='button' name='delete' value='Delete' /> <input type='button' name='cancel' value='Cancel'/></p>
            </form>
            <h2>Menu Items</h2>
			<?
                function generate_menu($menu, $parent_element){
					if($parent_element != 0){
						$parent_string = '= "'.clean($parent_element).'"';
					}else{
						$parent_string = 'IS NULL';
					}
					
                    $elements_of_parent = mysql_query('SELECT navigation_menu_item_ID,
													  			navigation_menu_item_name_'.getCurrentLanguage().',
																navigation_menu_item_class
															FROM tbl_navigation_menu_items
															WHERE navigation_menu_item_navigation_menu_FK = "'.clean($menu).'"
															AND navigation_menu_item_parent_menu_item_FK '.$parent_string.'
															ORDER BY navigation_menu_item_position DESC, navigation_menu_item_name_'.getCurrentLanguage().'
															');
					
					echo '<ul class="menu_list" id="'.$parent_element.'">';
						while($element = mysql_fetch_assoc($elements_of_parent)){
							$class = '';
							if($element['navigation_menu_item_class']){
								$class = '('.$element['navigation_menu_item_class'].')';
							}
							echo '<li><a href="'.constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/develop/navigation_menus/edit_menu_item/?iid='.$element['navigation_menu_item_ID'].'" id="'.$element['navigation_menu_item_ID'].'" class="edit_item">'.$element['navigation_menu_item_name_'.getCurrentLanguage()].' '.$class.'</a>';
								generate_menu($menu, $element['navigation_menu_item_ID']);
							echo '</li>';
						}
					echo '</ul>';
                }
                
                generate_menu($_GET['m'], 0);
            ?>
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