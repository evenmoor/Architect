<? if(validatePermissions('system', 11)){ ?>
<p>Name:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_item_name' class='help_link'>?</a></sup><br /><input type='text' name='name'/></p>
<p>Title:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_item_title' class='help_link'>?</a></sup><br /><input type='text' name='title'/></p>
<p>Position:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_item_position' class='help_link'>?</a></sup><br /><input type='text' name='position'/></p>
<p>Class:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=menu_item_class' class='help_link'>?</a></sup><br /><input type='text' name='class' /></p>
<p>Target:<br />
<input type='radio' name='target_type' value='document' checked="checked" />Document:<br />
<select name='target_doc'>
	<option value=''>Select One</option>
	<?
		$documents = mysql_query('SELECT document_ID,
								 document_name
								 FROM tbl_documents 
								 ORDER BY document_name');
		
		while($document = mysql_fetch_assoc($documents)){
			?><option value='arch_doc:<? echo $document['document_ID']; ?>'><? echo $document['document_name']; ?></option><?
		}
	?>
</select><br />
<input type='radio' name='target_type' value='manual'/>Manual:<br />
<input type='text' name='target_man'/></p>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>

