<!--UI Elements-->
<link href='<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/redmond/jquery-ui-1.10.2.custom.min.css' rel='stylesheet' media='screen'/>

<script type='text/javascript'>
	<?	//javascript constants	?>
	var CKEDITOR_BROWSE_PATH = '<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/media_browser/';
	var CKEDITOR_BROWSE_UPLOAD_PATH = '<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/media_browser_upload/';
	var TIMEOUT_REFRESH_PATH = '<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/extend_session/';
	var TIMEOUT_EXPIRATION_PATH = '<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_USER"); ?>/expire_session/?rpath=<? echo $_SERVER['REQUEST_URI']; ?>';
	var HELP_RESOLUTION_PATH = '<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/help/kb_call/';
	
	<? //optional javascript values ?>
	<? if(isset($template_custom_styles) && $template_custom_styles != ''){ //check for style override ?>
		<?
			$style_string = '[';//start style string
			$current_custom_styles = explode('||', $template_custom_styles);//extract styles
			foreach($current_custom_styles as $style){
				$style_values = explode('|', $style);//extract style values
				if(trim($style_values[0]) != ''){//check for blank style name
					if($style_string != '['){//check to see if we need to append a comma
						$style_string .= ', ';
					}
					$style_string .= '{ name: "'.$style_values[0].'", element: "'.$style_values[1].'", attributes: {style:"'.$style_values[2].'"} }';//build CKEditor style array
				}
			}
			$style_string .= ']';
		?>
		var ckEditor_custom_styles = <? echo $style_string; ?>;
	<? }else{ ?>
		var ckEditor_custom_styles = 'default';
	<? } ?>
</script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>

<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/jquery.hotkeys-0.7.9.min.js"></script>

<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/ckeditor/ckeditor.js"></script>
<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/ckeditor/adapters/jquery.js"></script>
<!--<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/timepicker.js"></script> this requires jquery ui-->

<!--CodeMirror-->
<? if(!isset($legacy) || $legacy == false){ ?>
<link href='<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/lib/codemirror.css' rel='stylesheet' media='screen' />
<link href='<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/theme/neat.css' rel='stylesheet' media='screen' />
<script src='<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/lib/codemirror.js'></script>
<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/mode/xml/xml.js"></script>
<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/mode/javascript/javascript.js"></script>
<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/mode/css/css.js"></script>
<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/mode/clike/clike.js"></script>
<script src='<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/codeMirror/mode/php/php.js'></script>
<? } ?>

<script type='text/javascript'>
	$(document).ready(function(){
		code_field_id = 0;
		$('.ui_code_highlight').each(function(index) {
			$(this).attr('id', 'code-' + code_field_id);
			CodeMirror.fromTextArea(document.getElementById('code-' + code_field_id), {
					lineNumbers: true,
					matchBrackets: true,
					mode: "application/x-httpd-php",
					indentUnit: 3,
					indentWithTabs: false,
					smartIndent: false,
					enterMode: "keep",
					tabMode: "shift"
				}
			);
			code_field_id++;
		});
	});
</script>

<!--/custom config-->
<script type='text/javascript'>
	var timeout_config = {	duration 	: 	<? echo $_SESSION['user_session_timeout']; ?>,
							warning		:	1};
</script>

<script src="<? echo constant("ARCH_INSTALL_PATH"); ?>scripts/ui_elements.js"></script>
<?
	//test for user overrides
	//css overrides
	if($_SESSION['font'] != "DEFAULT" && trim($_SESSION['font']) != ""){
		?>
			<style>
				body{font-family: <? echo $_SESSION['font']; ?>;}
			</style>
		<?
	}
	if($_SESSION['font-size'] != "DEFAULT" && trim($_SESSION['font-size']) != ""){
		?>
			<style>
				body{font-size: <? echo $_SESSION['font-size']; ?>px;}
			</style>
		<?
	}
?>