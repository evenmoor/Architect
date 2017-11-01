<ul>
	<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/'>Home</a></li>
    <? if(count($page_path) > 2){ ?>
    	<? if($page_path[1] == 'develop'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/dashboard/'>Develop</a></li> 
        <? }elseif($page_path[1] == 'license'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/license/'>Licence</a></li> 
        <? }elseif($page_path[1] == 'credits'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/credits/'>Credits</a></li> 
        <? }elseif($page_path[1] == 'help'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/help/help_topics/'>Help Topics</a></li> 
        <? }elseif($page_path[1] == 'publish'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/dashboard/'>Publish</a></li> 
        <? }elseif($page_path[1] == 'admin' || $page_path[1] == 'users' || $page_path[1] == 'logs'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/dashboard/'>Admin</a></li> 
        <? } ?>
	<? } ?>
    
    <? if(count($page_path) > 3){ ?>
    	<? if($page_path[2] == 'templates'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/templates/dashboard/'>Templates</a></li> 
        <? }elseif($page_path[2] == 'blocks'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/blocks/dashboard/'>Blocks</a></li> 
        <? }elseif($page_path[2] == 'navigation_menus'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/navigation_menus/dashboard/'>Navigation Menus</a></li> 
        <? }elseif($page_path[2] == 'documents'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/documents/dashboard/'>Documents</a></li> 
        <? }elseif($page_path[2] == 'additional_fields'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/additional_fields/dashboard/'>Additional Fields</a></li> 
        <? }elseif($page_path[2] == 'media'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/media/dashboard/'>Media</a></li> 
        <? }elseif($page_path[2] == 'document_groups'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/document_groups/dashboard/'>Document Groups</a></li> 
        <? }elseif($page_path[2] == 'forms'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/forms/dashboard/'>Forms</a></li> 
        <? } ?>
	<? } ?>
    
    <? if(count($page_path) > 4){ ?>
    	<? if($page_path[3] == 'edit_template'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Template</a></li> 
        <? }else if($page_path[3] == 'edit_block'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Block</a></li> 
        <? }else if($page_path[3] == 'edit_menu'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Menu</a></li> 
        <? }else if($page_path[3] == 'edit_document'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Document</a></li> 
        <? }else if($page_path[3] == 'edit_document_legacy'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Document (Legacy)</a></li> 
        <? }else if($page_path[3] == 'edit_field_group'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Additional Fields Group</a></li> 
        <? }else if($page_path[3] == 'edit_group'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Document Group</a></li> 
        <? }else if($page_path[3] == 'edit_form'){ ?>
        	<li>></li>
    		<li><a href='<? echo $_SERVER['REQUEST_URI']; ?>'>Edit Form</a></li> 
        <? }else if($page_path[3] == 'test_form'){ ?>
        	<li>></li>
    		<li><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/forms/edit_form/?f=<? echo $form['form_ID']; ?>'>Edit Form</a></li> 
        <? } ?>
	<? } ?>
</ul>