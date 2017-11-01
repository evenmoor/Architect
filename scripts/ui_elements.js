/*
	Architect User Interface Elements Version 1.4
	
	Development by: Joshua Moor
	Last Modified: 09/02/14
	
	This file provides basic configuration for various user interface elements
	Requires: jQuery, CKEditor, jQuery CK Adapter, jQuery UI, timeout_config
*/

//help box variables
var help_fade_speed = 500;

//timeout variables
var session_expire_timeout;
var session_warning_timeout;
var expiration_in_minutes;
var warning_in_minutes;

$(function(){ 
	/*//hide tutorial transcripts
	$('.tutorial .transcript').slideUp(0);
	//add listeners on the display transcript toggle
	$('.tutorial a.display_transcript').click(function(e){
		e.preventDefault();
		$(this).parent().next('.transcript').slideToggle();
		if($(this).html() == "(Show Transcript)"){
			$(this).html("(Hide Transcript)");
		}else{
			$(this).html("(Show Transcript)");
		}
	});*/
	
	//prep tutorials
	$('.tutorial').each(function(){
		var video_check = $(this).children('iframe.video');
		if(video_check.length > 0){
			$(this).children('.transcript').slideUp(0);
		}else{
			$(this).find('a.display_transcript').remove();
		}
	});
	
	$('.tutorial a.display_transcript').click(function(e){
		e.preventDefault();
		$(this).parent().next('.transcript').slideToggle();
		if($(this).html() == "(Show Transcript)"){
			$(this).html("(Hide Transcript)");
		}else{
			$(this).html("(Show Transcript)");
		}
	});

	$('body').append("<div id='help_link_div'></div>");//append help div
	$('#help_link_div').fadeOut(0);//prep help links
	$('body').append("<div id='timeout_dialog' title='Timeout Warning'><p><strong>Warning</strong>: Your session is about to time out.</p></div>");//append timeout div
	
	//display confirmation before allowing continuation
	$('a.confirm').click(function(e){
		if(!confirm('Are you sure you want to do this?')){
			e.preventDefault();
		}
	});
	
	$('.ui_wysiwyg').ckeditor();//add wysiwyg
	
	$('.ui_date_picker').datepicker({	changeMonth	:	true,
										changeYear	:	true});//add date picker
	
	/* Help Link Handlers */
	$('#help_link_div').click(function(){
		$(this).fadeOut(help_fade_speed);
	});
	
	$('a.help_link').click(function(e){
		e.preventDefault();
	});
	
	$('a.help_link').mouseenter(function(e){
		var help_url = $(this).attr('href');
		var link_position = $(this).position();
		var vertical_offset = $(this).height();
		var horizontal_offset = $(this).width();
		
		$('#help_link_div').html("Loading...");
		$('#help_link_div').css({'top'	:	(e.pageY + horizontal_offset),
								'left'	:	(e.pageX + vertical_offset)});
		
		$('#help_link_div').fadeIn(help_fade_speed);
		
		var help_vars = help_url.split("?");
		help_vars = help_vars[1].split("&");
		
		var platform = help_vars[0].split("=");
		platform = platform[1];
		
		var version = help_vars[1].split("=");
		version = version[1];
		
		var topic = help_vars[2].split("=");
		topic = topic[1];
		
		$.ajax({
			type	:	"POST",
			data	:	'platform='+platform+'&version='+version+'&topic='+topic+'',
			url		:	HELP_RESOLUTION_PATH
		}).done(function(content) {
			$('#help_link_div').html(content);
		});
	});
	
	$('a.help_link').mouseleave(function(){
		$('#help_link_div').fadeOut(help_fade_speed);
	});
	
	/* Time Out Handlers*/
	$('#timeout_dialog').dialog({
		autoOpen	:	false,
		resizeable	:	false,
		modal		:	true,
		minHeight	:	0,
		buttons:	{
						"Extend Session": function() {
							$.ajax({url:TIMEOUT_REFRESH_PATH, success:function(result){extend_session();}});
							$(this).dialog("close");
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
	});
	
	expiration_in_minutes = timeout_config['duration'] * 1000 * 60;
	
	/* Make sure an expiration needs to be set */
	if(expiration_in_minutes > 0){
		session_expire_timeout = setTimeout(expire_session, expiration_in_minutes);
		
		warning_in_minutes = expiration_in_minutes - (timeout_config['warning'] * 1000 * 60);
		session_warning_timeout = setTimeout(warn_session, warning_in_minutes);
	}
	
	//allow user to extend their session
	function extend_session(){
		clearTimeout(session_expire_timeout);
		clearTimeout(session_warning_timeout);
		session_expire_timeout = setTimeout(expire_session, expiration_in_minutes);
		session_warning_timeout = setTimeout(warn_session, warning_in_minutes);
	}
	
	//expire the session
	function expire_session(){
		window.location.href=TIMEOUT_EXPIRATION_PATH;
	}
	
	//warn about expiring session
	function warn_session(){
		$('#timeout_dialog').dialog('open');
	}

	//handles hidden data_fields
	//allows layout breaking elements to be supported
	$('.data_field').each(function(){
		//grab id of the field
		var id = $(this).attr('id');
		//distill id of target input element
		id = id.replace('field_', '');
		var target_element = $('#'+id);
		//fill the element with the removed input
		if(target_element.is('input') || target_element.is('textarea')){
			var content = $(this).html();
			//fix php tags...
			//this syntax hasn't been pushed out yet....
			content = content.replace(/<!--\?/g, '<?');
			content = content.replace(/\?-->/g, '?>');
			target_element.val(content);
		}else{
			alert('Error unsupported field type: '+target_element.attr('id'));
		}
	});
});