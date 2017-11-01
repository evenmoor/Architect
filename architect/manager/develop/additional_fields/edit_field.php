<? if(validatePermissions('system', 14)){ ?>
<? 
	$field = mysql_fetch_assoc(mysql_query('SELECT additional_field_name,
											   		additional_field_is_required
												FROM tbl_additional_fields 
												WHERE additional_field_ID="'.clean($_GET['fid']).'" 
												LIMIT 1'));
?>
<p><label>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_name' class='help_link'>?</a></sup> </label><br />
<input type='text' name='name' value='<? echo $field['additional_field_name']; ?>'/></p>
<?
	$yes_selected = $no_selected = '';
	if($field['additional_field_is_required'] == 1){
		$yes_selected = 'selected="selected"';
	}else{
		$no_selected = 'selected="selected"';
	}
?>
<p><label>Field is required:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=additional_field_is_required' class='help_link'>?</a></sup><br />
<select name='required'>
<option value='0' <? echo $no_selected;?>>No</option>
<option value='1' <? echo $yes_selected;?>>Yes</option>
</select></label></p>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>