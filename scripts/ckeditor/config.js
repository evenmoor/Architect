/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.uiColor = '#cccccc'; //background color for the UI
	
	config.filebrowserBrowseUrl = CKEDITOR_BROWSE_PATH;
    config.filebrowserUploadUrl = CKEDITOR_BROWSE_UPLOAD_PATH;
	
	config.allowedContent = true;
	
	config.protectedSource.push( /<\?[\s\S]*?\?>/g );   // ignore PHP code
	
	config.entities_additional = '#36'; //convert $ signs normal content to html entities
	
	config.stylesSet = ckEditor_custom_styles; //load any specified styles
	
	config.toolbar = [
					  ['Bold', 'Italic', 'Underline', 'Strike',  'Subscript', 'Superscript', 'RemoveFormat', '-', 'Font', 'FontSize', '-', 'TextColor', 'BGColor'], 
					  
					  ['NumberedList', 'BulletedList'],
					  
					  ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
					  
					  ['Styles', 'Format', ''],
					  
					  ['Link', 'Unlink', 'Anchor'], 
					  
					  ['Image', 'Table', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'],
					  
					  ['Scayt'],
					  
					  ['Maximize', '-', 'ShowBlocks', '-', 'Source']
	];//define custom toolbar
};
