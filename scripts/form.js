// HTML5 input support patches
// Development by Joshua Moor of iRapture.com
// Last Modified: 9/10/2014
// Required: Modernizr, jQuery

frmjq(function(){
	//check for placeholder support and patch it in if needed
	if(!Modernizr.input.placeholder){
		$('input[placeholder]').each(function(){
			//identify forms which may need additional support
			var field_form = frmjq(this).closest('form');
			if(!field_form.hasClass('patch-support')){
				field_form.addClass('patch-support');
			}
			
			//build initial placeholders
			frmjq(this).val(frmjq(this).attr('placeholder'));
			//identify fields which need placeholder support
			frmjq(this).addClass('patch-placeholder');
		});
	}
	
	//remove placeholder text on focus
	frmjq('input.patch-placeholder').focus(function(){
		var current_value = frmjq(this).val();
		var current_placeholder = frmjq(this).attr('placeholder');
		
		if(current_value == current_placeholder){
			frmjq(this).val("");
		}
	});
	
	//replace placeholder text on lose focus
	frmjq('input.patch-placeholder').blur(function(){
		var current_value = frmjq(this).val();
		var current_placeholder = frmjq(this).attr('placeholder');
		
		if(current_value == ""){
			frmjq(this).val(current_placeholder);
		}
	});
	
	//check for required field support and patch it in if needed
	if(!Modernizr.input.required){
		frmjq('input[required], textarea[required]').each(function(){
			//identify forms which may need additional support
			var field_form = frmjq(this).closest('form');
			if(!field_form.hasClass('patch-support')){
				field_form.addClass('patch-support');
			}
			
			frmjq(this).addClass('patch-required');
		});
	}
	
	//check for pattern support and patch it in if needed
	if(!Modernizr.input.pattern){
		//alert('pattern support needs to be patched in');
		frmjq('input[pattern]').each(function(){
			//identify forms which may need additional support
			var field_form = frmjq(this).closest('form');
			if(!field_form.hasClass('patch-support')){
				field_form.addClass('patch-support');
			}
			
			frmjq(this).addClass('patch-pattern');
		});
	}
	
	//replace placeholder text on lose focus
	frmjq('input.patch-pattern').blur(function(){
		//grab current match pattern
		var match_pattern = frmjq(this).attr('pattern');
		
		//check the match
		if(frmjq(this).val().search(match_pattern) ==-1){//match failed
			frmjq(this).closest('form').find('.patch_error_message').html("Warning: This field is improperly formatted.");
		}
	});
	
	//add an error message field for patched support form
	frmjq('form.patch-support').prepend('<p class="patch_error_message"></p>');
	
	//function to handle submission of patched support form
	frmjq('form.patch-support').submit(function(){
		//build return value
		var return_value = true;
		var error_message = "";
		
		//cycle through each field
		frmjq(this).find('input, textarea').each(function(){

			//placeholder fields
			if(frmjq(this).hasClass('patch-placeholder')){
				var current_value = frmjq(this).val();
				var current_placeholder = frmjq(this).attr('placeholder');
				
				//if the placeholder is still the value of the field, clear it out
				if(current_value == current_placeholder){
					frmjq(this).val("");
				}
			}//end placeholder fields
			
			//required fields
			if(frmjq(this).hasClass('patch-required')){
				//current value for text type elements.....
				if(frmjq(this).is('input[type=radio]') || frmjq(this).is('input[type=checkbox]')){
					if(frmjq('input[name='+frmjq(this).attr('name')+']:checked').length < 1){//look for checked values with the element's name
						return_value = false;
						if(error_message != ''){
							error_message += "<br/>";
						}
						if(frmjq(this).is('input[type=radio]')){
							error_message += "A required radio button has no selection.";
						}else{
							error_message += "A required checkbox has no selection.";
						}
					}
				}else{
					var current_value = frmjq.trim(frmjq(this).val());
					
					//if the value of a required field is empty, the form cannot be submitted
					if(current_value == ""){
						return_value = false;
						if(error_message != ''){
							error_message += "<br/>";
						}
						error_message += "A required field is blank.";
					}
				}
			}//end required fields
			
			//pattern fields
			if(frmjq(this).hasClass('patch-pattern')){
				//grab current match pattern
				var match_pattern = frmjq(this).attr('pattern');
				
				//check the match
				if(frmjq(this).val().search(match_pattern) ==-1){//match failed
					return_value = false;
					if(error_message != ''){
						error_message += "<br/>";
					}
					error_message += "A field's input is not properly formatted.";
				}
			}//end required fields
		});
		
		if(!return_value){
			frmjq(this).find('.patch_error_message').html(error_message);
		}
		
		//return the final return value
		return return_value;
	});
});