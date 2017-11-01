<? if(validatePermissions('system', 11)){ ?>
<? 
	$menu_item = mysql_fetch_assoc(mysql_query('SELECT navigation_menu_item_position,
											   			navigation_menu_item_link_target,
														navigation_menu_item_class,
														navigation_menu_item_name_'.getCurrentLanguage().',
														navigation_menu_item_title_'.getCurrentLanguage().'
													FROM tbl_navigation_menu_items 
													WHERE navigation_menu_item_ID="'.clean($_GET['iid']).'" 
													LIMIT 1'));
?>
<p>Name:<br />
<input type='text' name='name' value='<? echo $menu_item['navigation_menu_item_name_'.getCurrentLanguage()]; ?>'/></p>
<p>Title:<br />
<input type='text' name='title' value='<? echo $menu_item['navigation_menu_item_title_'.getCurrentLanguage()]; ?>'/></p>
<p>Position:<br />
<input type='text' name='position' value='<? echo $menu_item['navigation_menu_item_position']; ?>'/></p>
<p>Class:<br />
<input type='text' name='class' value='<? echo $menu_item['navigation_menu_item_class']; ?>'/></p>
<p>Target:<br />
<? 
	$document_checked = $manual_checked = $nav_id = $manual_link = '';
	$type_check = explode('arch_doc:', $menu_item['navigation_menu_item_link_target']);
	if(count($type_check) > 1){
		$document_checked = 'checked="checked"';
		$nav_id = $type_check[1];
	}else{
		$manual_checked = 'checked="checked"';
		$manual_link = $menu_item['navigation_menu_item_link_target'];
	}
?>
<input type='radio' name='target_type' value='document' <? echo $document_checked; ?>/>Document:<br />
<select name='target_doc'>
	<option value=''>Select One</option>
	<?
		$documents = mysql_query('SELECT document_ID,
								 document_name
								 FROM tbl_documents 
								 ORDER BY document_name');
		
		while($document = mysql_fetch_assoc($documents)){
			$selected = '';
			
			if($document['document_ID'] == $nav_id){
				$selected = 'selected="selected"';
			}
			?><option value='arch_doc:<? echo $document['document_ID']; ?>' <? echo $selected; ?>><? echo $document['document_name']; ?></option><?
		}
	?>
</select><br />
<input type='radio' name='target_type' value='manual' <? echo $manual_checked; ?>/>Manual:<br />
<input type='text' name='target_man' value='<? echo $manual_link; ?>'/></p>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>