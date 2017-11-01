<ul>
	<? if(validatePermissions('system', 15)){ ?>
    	<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/dashboard/'>Dashboard</a></li>
        <li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/seo_settings/'>SEO Settings</a></li>
        <li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/site_settings/'>Site Settings</a></li>
        <li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/logs/dashboard/'>System Log</a></li>
        <li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/update/'>Update</a></li>
        <li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/users/dashboard/'>Users and Permissions</a></li>
    <? } ?>
</ul>