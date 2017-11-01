<? if(validatePermissions('system', 15)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Site Settings | <? echo $site_settings['name']; ?></title>
<link href="<? echo constant("ARCH_INSTALL_PATH"); ?>themes<? echo constant("ARCH_SYSTEM_THEME_PATH"); ?>" rel="stylesheet" type="text/css" media="all" />
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/scripts.php'); ?>
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
        
        <div id='section_navigation'>
        	<? require(constant("ARCH_BACK_END_PATH").'manager/includes/admin_section_navigation.php'); ?>
        </div><!--/section_navigation-->
        
        <div class='content with_section_nav'>
        	<div class='single_column'>
        		<h1>Site Settings</h1>
            </div><!--/heading-->
                <form action='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/update_site_settings/' method='post'>
                <div class='single_column'>
                <p><input type='submit' value='Save' /></p>
                </div><!--/single column-->
                	<div class='tri_column'>
                        <h2>General Settings</h2>
                        <p><label>Site Name:</label><br /><input type='text' name='name' value='<? echo $site_settings['name']; ?>'/></p>
                        <p><label>Site Status:</label><br />
                            <select name='status'>
                                <?
                                    $status_list = array("ONLINE", "OFFLINE", "UPDATING", "MAINTENANCE", "DEVELOPMENT");
                                    asort($status_list);
                                    foreach($status_list as $status){
                                        $selected = '';
                                        if($site_settings['status'] == $status){
                                            $selected = 'selected="selected"';
                                        }
                                        ?><option value='<? echo $status; ?>' <? echo $selected; ?>><? echo $status; ?></option><?
                                    }
                                ?>
                            </select>
                        </p>
                        <p><label>Site Timezone:</label><br />
                            <select name='timezone'>
                                <?
                                    $timezone_list = array("Europe/Andorra","Asia/Dubai","Asia/Kabul","America/Antigua","America/Anguilla","Europe/Tirane","Asia/Yerevan","Africa/Luanda","Antarctica/McMurdo","Antarctica/South_Pole","Antarctica/Rothera","Antarctica/Palmer","Antarctica/Mawson","Antarctica/Davis","Antarctica/Casey","Antarctica/Vostok","Antarctica/DumontDUrville","Antarctica/Syowa","Antarctica/Macquarie","America/Argentina/Buenos_Aires","America/Argentina/Cordoba","America/Argentina/Salta","America/Argentina/Jujuy","America/Argentina/Tucuman","America/Argentina/Catamarca","America/Argentina/La_Rioja","America/Argentina/San_Juan","America/Argentina/Mendoza","America/Argentina/San_Luis","America/Argentina/Rio_Gallegos","America/Argentina/Ushuaia","Pacific/Pago_Pago","Europe/Vienna","Australia/Lord_Howe","Australia/Hobart","Australia/Currie","Australia/Melbourne","Australia/Sydney","Australia/Broken_Hill","Australia/Brisbane","Australia/Lindeman","Australia/Adelaide","Australia/Darwin","Australia/Perth","Australia/Eucla","America/Aruba","Europe/Mariehamn","Asia/Baku","Europe/Sarajevo","America/Barbados","Asia/Dhaka","Europe/Brussels","Africa/Ouagadougou","Europe/Sofia","Asia/Bahrain","Africa/Bujumbura","Africa/Porto-Novo","America/St_Barthelemy","Atlantic/Bermuda","Asia/Brunei","America/La_Paz","America/Kralendijk","America/Noronha","America/Belem","America/Fortaleza","America/Recife","America/Araguaina","America/Maceio","America/Bahia","America/Sao_Paulo","America/Campo_Grande","America/Cuiaba","America/Santarem","America/Porto_Velho","America/Boa_Vista","America/Manaus","America/Eirunepe","America/Rio_Branco","America/Nassau","Asia/Thimphu","Africa/Gaborone","Europe/Minsk","America/Belize","America/St_Johns","America/Halifax","America/Glace_Bay","America/Moncton","America/Goose_Bay","America/Blanc-Sablon","America/Montreal","America/Toronto","America/Nipigon","America/Thunder_Bay","America/Iqaluit","America/Pangnirtung","America/Resolute","America/Atikokan","America/Rankin_Inlet","America/Winnipeg","America/Rainy_River","America/Regina","America/Swift_Current","America/Edmonton","America/Cambridge_Bay","America/Yellowknife","America/Inuvik","America/Creston","America/Dawson_Creek","America/Vancouver","America/Whitehorse","America/Dawson","Indian/Cocos","Africa/Kinshasa","Africa/Lubumbashi","Africa/Bangui","Africa/Brazzaville","Europe/Zurich","Africa/Abidjan","Pacific/Rarotonga","America/Santiago","Pacific/Easter","Africa/Douala","Asia/Shanghai","Asia/Harbin","Asia/Chongqing","Asia/Urumqi","Asia/Kashgar","America/Bogota","America/Costa_Rica","America/Havana","Atlantic/Cape_Verde","America/Curacao","Indian/Christmas","Asia/Nicosia","Europe/Prague","Europe/Berlin","Europe/Busingen","Africa/Djibouti","Europe/Copenhagen","America/Dominica","America/Santo_Domingo","Africa/Algiers","America/Guayaquil","Pacific/Galapagos","Europe/Tallinn","Africa/Cairo","Africa/El_Aaiun","Africa/Asmara","Europe/Madrid","Africa/Ceuta","Atlantic/Canary","Africa/Addis_Ababa","Europe/Helsinki","Pacific/Fiji","Atlantic/Stanley","Pacific/Chuuk","Pacific/Pohnpei","Pacific/Kosrae","Atlantic/Faroe","Europe/Paris","Africa/Libreville","Europe/London","America/Grenada","Asia/Tbilisi","America/Cayenne","Europe/Guernsey","Africa/Accra","Europe/Gibraltar","America/Godthab","America/Danmarkshavn","America/Scoresbysund","America/Thule","Africa/Banjul","Africa/Conakry","America/Guadeloupe","Africa/Malabo","Europe/Athens","Atlantic/South_Georgia","America/Guatemala","Pacific/Guam","Africa/Bissau","America/Guyana","Asia/Hong_Kong","America/Tegucigalpa","Europe/Zagreb","America/Port-au-Prince","Europe/Budapest","Asia/Jakarta","Asia/Pontianak","Asia/Makassar","Asia/Jayapura","Europe/Dublin","Asia/Jerusalem","Europe/Isle_of_Man","Asia/Kolkata","Indian/Chagos","Asia/Baghdad","Asia/Tehran","Atlantic/Reykjavik","Europe/Rome","Europe/Jersey","America/Jamaica","Asia/Amman","Asia/Tokyo","Africa/Nairobi","Asia/Bishkek","Asia/Phnom_Penh","Pacific/Tarawa","Pacific/Enderbury","Pacific/Kiritimati","Indian/Comoro","America/St_Kitts","Asia/Pyongyang","Asia/Seoul","Asia/Kuwait","America/Cayman","Asia/Almaty","Asia/Qyzylorda","Asia/Aqtobe","Asia/Aqtau","Asia/Oral","Asia/Vientiane","Asia/Beirut","America/St_Lucia","Europe/Vaduz","Asia/Colombo","Africa/Monrovia","Africa/Maseru","Europe/Vilnius","Europe/Luxembourg","Europe/Riga","Africa/Tripoli","Africa/Casablanca","Europe/Monaco","Europe/Chisinau","Europe/Podgorica","America/Marigot","Indian/Antananarivo","Pacific/Majuro","Pacific/Kwajalein","Europe/Skopje","Africa/Bamako","Asia/Rangoon","Asia/Ulaanbaatar","Asia/Hovd","Asia/Choibalsan","Asia/Macau","Pacific/Saipan","America/Martinique","Africa/Nouakchott","America/Montserrat","Europe/Malta","Indian/Mauritius","Indian/Maldives","Africa/Blantyre","America/Mexico_City","America/Cancun","America/Merida","America/Monterrey","America/Matamoros","America/Mazatlan","America/Chihuahua","America/Ojinaga","America/Hermosillo","America/Tijuana","America/Santa_Isabel","America/Bahia_Banderas","Asia/Kuala_Lumpur","Asia/Kuching","Africa/Maputo","Africa/Windhoek","Pacific/Noumea","Africa/Niamey","Pacific/Norfolk","Africa/Lagos","America/Managua","Europe/Amsterdam","Europe/Oslo","Asia/Kathmandu","Pacific/Nauru","Pacific/Niue","Pacific/Auckland","Pacific/Chatham","Asia/Muscat","America/Panama","America/Lima","Pacific/Tahiti","Pacific/Marquesas","Pacific/Gambier","Pacific/Port_Moresby","Asia/Manila","Asia/Karachi","Europe/Warsaw","America/Miquelon","Pacific/Pitcairn","America/Puerto_Rico","Asia/Gaza","Asia/Hebron","Europe/Lisbon","Atlantic/Madeira","Atlantic/Azores","Pacific/Palau","America/Asuncion","Asia/Qatar","Indian/Reunion","Europe/Bucharest","Europe/Belgrade","Europe/Kaliningrad","Europe/Moscow","Europe/Volgograd","Europe/Samara","Asia/Yekaterinburg","Asia/Omsk","Asia/Novosibirsk","Asia/Novokuznetsk","Asia/Krasnoyarsk","Asia/Irkutsk","Asia/Yakutsk","Asia/Khandyga","Asia/Vladivostok","Asia/Sakhalin","Asia/Ust-Nera","Asia/Magadan","Asia/Kamchatka","Asia/Anadyr","Africa/Kigali","Asia/Riyadh","Pacific/Guadalcanal","Indian/Mahe","Africa/Khartoum","Europe/Stockholm","Asia/Singapore","Atlantic/St_Helena","Europe/Ljubljana","Arctic/Longyearbyen","Europe/Bratislava","Africa/Freetown","Europe/San_Marino","Africa/Dakar","Africa/Mogadishu","America/Paramaribo","Africa/Juba","Africa/Sao_Tome","America/El_Salvador","America/Lower_Princes","Asia/Damascus","Africa/Mbabane","America/Grand_Turk","Africa/Ndjamena","Indian/Kerguelen","Africa/Lome","Asia/Bangkok","Asia/Dushanbe","Pacific/Fakaofo","Asia/Dili","Asia/Ashgabat","Africa/Tunis","Pacific/Tongatapu","Europe/Istanbul","America/Port_of_Spain","Pacific/Funafuti","Asia/Taipei","Africa/Dar_es_Salaam","Europe/Kiev","Europe/Uzhgorod","Europe/Zaporozhye","Europe/Simferopol","Africa/Kampala","Pacific/Johnston","Pacific/Midway","Pacific/Wake","America/New_York","America/Detroit","America/Kentucky/Louisville","America/Kentucky/Monticello","America/Indiana/Indianapolis","America/Indiana/Vincennes","America/Indiana/Winamac","America/Indiana/Marengo","America/Indiana/Petersburg","America/Indiana/Vevay","America/Chicago","America/Indiana/Tell_City","America/Indiana/Knox","America/Menominee","America/North_Dakota/Center","America/North_Dakota/New_Salem","America/North_Dakota/Beulah","America/Denver","America/Boise","America/Shiprock","America/Phoenix","America/Los_Angeles","America/Anchorage","America/Juneau","America/Sitka","America/Yakutat","America/Nome","America/Adak","America/Metlakatla","Pacific/Honolulu","America/Montevideo","Asia/Samarkand","Asia/Tashkent","Europe/Vatican","America/St_Vincent","America/Caracas","America/Tortola","America/St_Thomas","Asia/Ho_Chi_Minh","Pacific/Efate","Pacific/Wallis","Pacific/Apia","Asia/Aden","Indian/Mayotte","Africa/Johannesburg","Africa/Lusaka","Africa/Harare");
                                    asort($timezone_list);
                                    foreach($timezone_list as $timezone){
                                        $selected = '';
                                        if($site_settings['timezone'] == $timezone){
                                            $selected = 'selected="selected"';
                                        }
                                        ?><option value='<? echo $timezone; ?>' <? echo $selected; ?>><? echo $timezone; ?></option><?
                                    }
                                ?>
                            </select>
                        </p>
                    </div><!--/general settings-->
                    
                    <div class='tri_column'>
                        <h2>Development Settings<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=admin_development_settings' class='help_link'>?</a></sup></h2>
                        <p><label>Alternate Path:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=admin_development_settings_alternate_path' class='help_link'>?</a></sup></label><br /><input type='text' name='alternate_development_path' value='<? echo $site_settings['development_alternate_path']; ?>'/></p>
                        <p><label>Path Override:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=admin_development_settings_alternate_path_override' class='help_link'>?</a></sup></label><br /><input type='text' name='development_override' value='<? echo $site_settings['development_override']; ?>'/></p>
                        <p>Preview Link:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.2&topic=admin_development_settings_preview_link' class='help_link'>?</a></sup><br /> <? echo $_SERVER['HTTP_HOST']; ?><? echo constant("ARCH_INSTALL_PATH"); ?>?override=<? echo $site_settings['development_override']; ?></p>
                    </div><!--/development settings-->
                    
                    <div class='tri_column'>
                        <h2>Site Customization Settings</h2>
                        <h3>Login Customization</h3>
                        <?
							$additional_settings = mysql_query('SELECT site_custom_login_css_override,
																	site_custom_login_preform_override,
																	site_custom_login_postform_override
																FROM tbl_site_settings
																WHERE site_ID="1"
																LIMIT 1');
							$additional_settings = mysql_fetch_assoc($additional_settings);
						?>
                        <p><label>Stylesheet Override:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.6&topic=admin_login_customization_css_override' class='help_link'>?</a></sup><br />
                        <input type='text' name='stylesheet_override' value='<? echo $additional_settings['site_custom_login_css_override']; ?>'/></label></p>
                        <p><label>Pre-form Content Override:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.6&topic=admin_login_customization_pre-form_content_override' class='help_link'>?</a></sup></label><br/>
                        <textarea name='pre-form_override'><? echo $additional_settings['site_custom_login_preform_override']; ?></textarea></p>
                        <p><label>Post-form Content Override:<sup><a href='https://webapps.irapture.com/KB/?platform=Architect&version=1.6&topic=admin_login_customization_post-form_content_override' class='help_link'>?</a></sup></label><br/>
                        <textarea name='post-form_override'><? echo $additional_settings['site_custom_login_postform_override']; ?></textarea></p>
                    </div><!--/publishing settings-->
                    
                    <div class='single_column'>
                		<p><input type='submit' value='Save' /></p>
                	</div><!--/single column-->
                    
                    <input type='hidden' name='action' value='save_site_settings' />
                </form>
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