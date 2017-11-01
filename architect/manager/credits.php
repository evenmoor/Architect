<? if(validatePermissions('system', 1)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Credits | Architect</title>
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
        
        <div class='content'>
        	<h1>Credits</h1>
            <p>"We are like dwarfs sitting on the shoulders of giants.<br /> We see more, and things that are more distant, than they did,<br /> not because our sight is superior or because we are taller than they,<br /> but because they raise us up, and by their great stature add to ours."<br /> - John of Salisbury <em>Metalogicon</em> 1159</p>
            
            <p>Architect was built using several open source technologies. We would like to thank the following open source projects, without their work Architect would never have been possible.</p>
            	<ul>
                	<li><a href='http://php.net/'>PHP</a></li>
                    <li><a href='http://www.mysql.com/'>MySQL</a></li>
                	<li><a href='http://jquery.com/'>jQuery</a></li>
                    <li><a href='http://ckeditor.com/'>CKEditor</a></li>
                    <li><a href='http://codemirror.net/'>CodeMirror</a></li>
                </ul>
                
             <p>Architect would also not have been possible without without the help of our QA team: Jacob, Tanya, and Jeremy.</p>
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