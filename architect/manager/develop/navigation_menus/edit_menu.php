<? if(validatePermissions('system', 11)){ ?>
<?
	if(isset($_POST['action'])){//check for pending actions
		switch($_POST['action']){
			case 'save':
				//update database element
				mysql_query('UPDATE tbl_navigation_menus
							 SET navigation_menu_name = "'.clean($_POST['name']).'"
							 WHERE navigation_menu_ID = "'.clean($_POST['mid']).'"
							 LIMIT 1');
				
				systemLog('menu updated id# '.clean($_POST['mid']).'.');
				
				//clear out old menu items
				mysql_query('DELETE FROM tbl_navigation_menu_items 
				WHERE navigation_menu_item_navigation_menu_FK="'.clean($_POST['mid']).'"');
				
				//parse elements
				$position_counter = 100000;
				
				//
				$insert_keys = array();
				
				foreach($_POST['elements'] as $elements){
					//echo '<h1>Element</h1>';
					//echo '<h1>ID: '.$elements['id'].'</h1>';
					//echo '<h1>Name: '.$elements['name'].'</h1>';
					//echo '<h1>Parent: '.$elements['parent'].'</h1>';
					//echo '<h1>'.$elements['class'].'</h1>';
					//echo '<h1>'.$elements['title'].'</h1>';
					//echo '<h1>'.$elements['target'].'</h1>';
					$parent_id = 'NULL';
					if($elements['parent'] != 'null' && $elements['parent'] != 'Null' && $elements['parent'] != 'NULL' && $elements['parent'] != ''){
						$parent_id = '"'.$insert_keys[$elements['parent']].'"';
						//echo '<h1>Parent Insert: '.$parent_id.'</h1>';
					}
					
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
																'.$parent_id.',
																"'.$position_counter.'",
																"'.clean($elements['target']).'",
																"'.clean($elements['class']).'",
																"'.clean($elements['name']).'",
																"'.clean($elements['title']).'")');
					
					//echo mysql_error();
					
					$insert_keys[$elements['id']] = mysql_insert_id();
					
					//echo '<h1>Insert ID: '.$insert_keys[$elements['id']].'</h1>';
					
					$position_counter--;
				}
				
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

<style type='text/css'>
	.content ul.menu{disply:block; position:relative; border:1px solid #666; list-style:none; width:40%; margin:0; padding:0; background:#fff; 
	border-radius:5px;}
	.content ul.menu ul{width:100%; left:25%; margin:5px 0;}
	.content ul.menu li{display:block; position:relative; border:1px solid #ccc; margin:2px; padding:1px;
	border-radius:5px;}
	.content ul.menu li:nth-child(odd){background:rgba(204, 204, 204,.25);}
	.content ul.menu a{display:inline-block; padding:2px;}
	.content ul.menu a.add{width:100%;}
	.content ul.menu a.remove{display:inline-block; padding:2px; width:10%; background:rgba(255, 0, 0, .5); text-align:center; position:absolute; right:1px; top:1px;
	border-radius:5px;}
	.content ul.menu a.expand{display:inline-block; padding:2px; width:10%; background:rgba(0, 255, 0, .5); text-align:center; position:absolute; right:12%; top:1px;
	border-radius:5px;}
</style>

<script type='text/javascript'>
	function sortable_nav(){
		//clear custom links
		$('ul.menu li.add').remove();
		$('a.remove').remove();
		
		$id_counter = 0;
		//append needed custom links
		$('ul.menu li').each(function(){
			$(this).append('<a href="" class="remove">X</a>');
			$(this).append('<a href="" class="expand">+</a>');
			$id_counter++;
		});
		
		$('ul.menu').append('<li class="add"><a href="" class="add">Add</a></li>');
		
		//invoke sorting
		$('.menu').sortable({
			items: "li:not(.add)",
			connectWith: ".menu"
		});
	}
	
	$(function(){
		//handle data spans
		$('span.data').each(function(){
			var holder = $(this).parent().find('span').first();
			if($(this).hasClass('class')){
				holder.data("class", $(this).html());
			}
			
			if($(this).hasClass('link_type')){
				holder.data("link_type", $(this).html());
			}
			
			if($(this).hasClass('link_title')){
				holder.data("link_title", $(this).html());
			}
			
			if($(this).hasClass('link_target')){
				holder.data("link_target", $(this).html());
			}
			
			$(this).remove();
		});
		
		sortable_nav();
		
		$('#save').click(function(e){
			e.preventDefault();
			
			var element_string = "";
			//parse menus looking for list items
			var element_counter = 0;
			$('ul.menu li').each(function(){
				if(!$(this).hasClass('add')){//filter out add links
					var data_container = $(this).find('span').first();
					data_container.data("id", element_counter);
					element_counter++;
				}
			});
			
			//var last_parent = "NONE";
			$('ul.menu li').each(function(){
				if(!$(this).hasClass('add')){//filter out add links
					var data_container = $(this).find('span').first();
				
					var name = "";
					var encoded_value = $(this).find('span a').html();
					encoded_value = encoded_value.replace(/"/g, "&quot;");
					name = encoded_value;
					
					var title = "";
					encoded_value = data_container.data("link_title");
					encoded_value = encoded_value.replace(/"/g, "&quot;");
					title = encoded_value;
					
					var id = data_container.data("id");//$(this).attr('id');
					
					parent = $(this).parent('ul.menu').parent('li').find('span').first().data("id");
					/*
					var encoded_value = $(this).html();
					encoded_value = encoded_value.replace(/"/g, "&quot;");
					*/
					element_string += "<input type='hidden' name='elements["+id+"][parent]' value='"+parent+"'/>";
					element_string += "<input type='hidden' name='elements["+id+"][id]' value='"+id+"'/>";
					element_string += "<input type='hidden' name='elements["+id+"][target]' value='"+data_container.data("link_target")+"'/>";
					element_string += "<input type='hidden' name='elements["+id+"][class]' value='"+data_container.data("class")+"'/>";
					element_string += '<input type="hidden" name="elements['+id+'][title]" value="'+title+'"/>';
					element_string += '<input type="hidden" name="elements['+id+'][name]" value="'+name+'"/>';
				}
			});
			
			$('#element_list').html(element_string);
			$('#save_form').submit();
		});

		$('ul.menu').delegate('a.add', 'click', function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).parent().parent().append("<li><span class='form_area'><a href=''>New Element</a></span></li>");
			sortable_nav();
		});
		
		$('ul.menu').delegate('a.remove', 'click', function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).parent().remove();
		});
		
		$('ul.menu').delegate('a.expand', 'click', function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).parent().append("<ul class='menu'></ul>");
			sortable_nav();
		});
		
		$('ul.menu').delegate('form.update_item', 'submit', function(e){
			e.preventDefault();
			e.stopPropagation();
			
			var parent = $(this).parent();
	
			parent.html("<a href=''>"+$(this).find("input[name='text']").val()+"</a>");
			parent.data("class", $(this).find("input[name='class']").val());
			var type = "text";
			if($(this).find("input[name='link_type']:checked").val() == "link"){
				type = "link";
			}
			if(type == "link" && $(this).find("input[name='target_type']:checked").val() == "manual"){
				type = "manual";
			}
			
			parent.data("link_type", type);
			parent.data("link_title", $(this).find("input[name='title']").val());
			if(type == "link"){
				parent.data("link_target", $(this).find("select[name='document_list']").val());
			}else if(type == "manual"){
				parent.data("link_target", $(this).find("input[name='manual_target']").val());
			}
			
			sortable_nav();
		});
		
		$('ul.menu').delegate('input[name="link_type"]', 'change', function(e){
			e.stopPropagation();
			if($(this).val() == 'link'){
				$(this).parent().find('.link_details').slideDown(0);
			}else{
				$(this).parent().find('.link_details').slideUp(0);
			}
		});
		
		$('ul.menu').delegate('li a', 'click', function(e){
			e.preventDefault();
			e.stopPropagation();
			if(!$(this).hasClass('remove') && !$(this).hasClass('expand') && !$(this).hasClass('add')){//make sure it is an editable link
				var target_element = $(this).parent();
				var form_text = "<form class='update_item'>";//start form
				
				var encoded_value = $(this).html();
				encoded_value = encoded_value.replace(/"/g, "&quot;");
				
				form_text += 'text: <input type="text" value="'+encoded_value+'" name="text"/>';//text field
				
				var class_name = '';
				if(target_element.data("class")){
					class_name = target_element.data("class");
				}
				
				form_text += "<br/>class: <input type='text' value='"+class_name+"' name='class'/>";//class
				
				if(target_element.data("link_type") == 'text'){//element type
					form_text += "<br/>type: <input type='radio' class='link_type' name='link_type' value='link'/>Link <input type='radio' class='link_type' name='link_type' value='text' checked='checked'/>Text";
				}else{
					form_text += "<br/>type: <input type='radio' class='link_type' name='link_type' value='link' checked='checked'/>Link <input type='radio' class='link_type' name='link_type' value='text'/>Text";
				}
				
				if(target_element.data("link_type") == 'text'){//element type
					form_text += "<div class='link_details' style='display:none'>";//collapseing field
				}else{
					form_text += "<div class='link_details'>";//collapseing field
				}
				
				var title_name = '';
				if(target_element.data("link_title")){
					encoded_value =target_element.data("link_title");
					encoded_value = encoded_value.replace(/"/g, "&quot;");
					title_name = encoded_value;
				}
				
				form_text += '<br/>title: <input type="text" value="'+title_name+'" name="title"/>';//title
				
				
				form_text += "<br/>target:";//target
				if(target_element.data("link_type") == 'link'){
					form_text += "<br/><input type='radio' name='target_type' value='link' checked='checked'/>Document: <select name='document_list'>";
				}else{
					form_text += "<br/><input type='radio' name='target_type' value='link'/>Document: <select name='document_list'>";
				}
				
				form_text += "<option value=''></option>";
				
				<?
					$documents = mysql_query('SELECT document_ID,
								 document_name
								 FROM tbl_documents 
								 ORDER BY document_name');
		
					while($document = mysql_fetch_assoc($documents)){
						?>
							if(target_element.data("link_target") == 'arch_doc:<? echo $document['document_ID']; ?>'){
								form_text += "<option value='arch_doc:<? echo $document['document_ID']; ?>' selected='selected'><? echo $document['document_name']; ?></option>";
							}else{
								form_text += "<option value='arch_doc:<? echo $document['document_ID']; ?>'><? echo $document['document_name']; ?></option>";
							}
						<?
					}
				?>
				form_text += "</select>";//target
				if(target_element.data("link_type") == 'manual'){//element type
					form_text += "<br/><input type='radio' name='target_type' value='manual' checked='checked'/>Custom: <input type='text' name='manual_target' value='"+target_element.data("link_target")+"'/>";//target
				}else{
					form_text += "<br/><input type='radio' name='target_type' value='manual'/>Custom: <input type='text' name='manual_target' />";//target
				}
				
				form_text += "</div>";

				form_text += "<br/><input type='submit' value='Update'/></form>";//end form
				
				target_element.html(form_text);
				sortable_nav();
			}
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
        	<h1><? echo $menu['navigation_menu_name']; ?></h1>
        	<form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post' id='save_form'>
            	<h2>Menu Properties</h2>
            	<p>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_name' class='help_link'>?</a><br />
                <input type='text' name='name' value='<? echo $menu['navigation_menu_name']; ?>'/></p>
                <input type='hidden' name='mid' value='<? echo $_GET['m']; ?>' />
                <input type='hidden' name='action' value='save' />
                <div id='element_list'>
                </div>
                <p><input type='submit' value='Save' id="save"/></p>
            </form>
            
            <?
                function generate_menu($menu, $parent_element){
					if($parent_element != 0){
						$parent_string = '= "'.clean($parent_element).'"';
					}else{
						$parent_string = 'IS NULL';
					}
					
                    $elements_of_parent = mysql_query('SELECT *
															FROM tbl_navigation_menu_items
															WHERE navigation_menu_item_navigation_menu_FK = "'.clean($menu).'"
															AND navigation_menu_item_parent_menu_item_FK '.$parent_string.'
															ORDER BY navigation_menu_item_position DESC, navigation_menu_item_name_'.getCurrentLanguage().'
															');
					
					echo '<ul class="menu">';
						while($element = mysql_fetch_assoc($elements_of_parent)){
							$type = "text";
							$target = $element['navigation_menu_item_link_target'];
							
							if($target != ''){
								$target = explode(':', $target);
								
								if(count($target) > 1){
									if($target[0] == "arch_doc"){
										$type = "link";
									}else{
										$type = "manual";
									}
								}else{
									$type = "manual";
								}
							}
							
							$target = $element['navigation_menu_item_link_target'];
							
							echo '<li><span><a href="">'.$element['navigation_menu_item_name_'.getCurrentLanguage()].'</a></span>
							<span class="data class">'.$element['navigation_menu_item_class'].'</span>
							<span class="data link_title">'.$element['navigation_menu_item_title_'.getCurrentLanguage()].'</span>
							<span class="data link_type">'.$type.'</span>
							<span class="data link_target">'.$target.'</span>';
						
								generate_menu($menu, $element['navigation_menu_item_ID']);
								
							echo '</li>';
						}
					echo '</ul>';
                }
                
                generate_menu($_GET['m'], 0);
            ?>
            
            <!--<ul class='menu'>
            
            </ul>-->
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