// Form jQuery Detection and Loading Document
// Designed to allow the scripts nesscessary for the form UI scripts to be loaded
/*
	Thanks to the following helpful posters
	Jason at Stack Overflow - http://stackoverflow.com/questions/8586446/dynamically-load-external-javascript-file-and-wait-for-it-to-load-without-usi
	ajpiano at the jQuery Forums - http://forum.jquery.com/topic/multiple-versions-of-jquery-on-the-same-page
*/

//header to attach scripts
var page_header = document.getElementsByTagName('head')[0];

//define jQuery handle -- defaults to only one version of jQuery mode
if(window.jQuery){ 
	var frmjq = $;
}

//array of allowable versions
var supported_versions = Array("1.11.1");

//form script location
//var form_script_path = "/test/form.js";

//fall back version of jQuery to load from Google
var fall_back_version_number = "1.11.1";
var google_path = '//ajax.googleapis.com/ajax/libs/jquery/'+fall_back_version_number+'/jquery.min.js';

//check to see if jQuery has been loaded
if(typeof jQuery == 'undefined'){//if it hasn't been loaded
	//load jQuery
	loadScriptFile(google_path, function(){
		//define the functon call
		frmjq = $; 
		//append form scripts
		loadScriptFile(modernizr_path, function(){
			loadScriptFile(form_script_path, function(){
				loadCustomScripts();
			});	
		});
	});
}else{//if it has been loaded
	if(supported_versions.indexOf($.fn.jquery) == -1){//check for unsupported version
		//load supported jQuery
		loadScriptFile(google_path, function(){ 
			//return control of $ to other jQuery version and assign handler to supported version
			frmjq = jQuery.noConflict(true);
			//append form scripts
			
			loadScriptFile(modernizr_path, function(){
				loadScriptFile(form_script_path, function(){
					loadCustomScripts();	
				});	
			});
		});
	}else{//supported version found
		//append form scripts
		loadScriptFile(modernizr_path, function(){
			loadScriptFile(form_script_path, function(){
				loadCustomScripts();
			});	
		});
	}
}

//load specified custom scripts
function loadCustomScripts(){
	for(var loop_counter = 0; loop_counter < custom_script_paths.length; loop_counter++){ 
		loadScriptFile(custom_script_paths[loop_counter], function(){});
	}
}

//function to load a script file with a call back. Thanks Jason!
function loadScriptFile(src, callback){
    var sf = document.createElement('script');
    sf.src = src;
    sf.async = true;
    sf.onreadystatechange = sf.onload = function(){
        var state = sf.readyState;
        if (!callback.done && (!state || /loaded|complete/.test(state))) {
            callback.done = true;
            callback();
        }
    };
    page_header.appendChild(sf);
}

