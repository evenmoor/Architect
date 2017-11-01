<ul>
	<? if(validatePermissions('system', 1)){ ?>
		<li <? if(count($page_path) < 3){ echo 'class="active"'; }?>><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/'>Home</a></li>
    <? } ?>
    
    <? if(validatePermissions('system', 5)){ ?>
    	<li <? if($page_path[1] == 'publish'){ echo 'class="active"'; }?>><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/publish/dashboard/'>Publish</a></li>
    <? } ?>
    
	<? if(validatePermissions('system', 9)){ ?>
    	<li <? if($page_path[1] == 'develop'){ echo 'class="active"'; }?>><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/develop/dashboard/'>Develop</a></li>
    <? } ?>
    
    <?
		if(validatePermissions('system', 1)){
			foreach($expansion_modules as $module){
				$active = '';
				if($page_path[1] == 'expanded' && $page_path[2] == $module){
					$active = 'class="active"';
				}
				include(constant("ARCH_BACK_END_PATH").'modules/'.$module.'/module_config.php');//include default config for module
				?><li <? echo $active; ?>><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/expanded/<? echo $module;?>/dashboard/'><? echo $module_name;?></a></li><?
			}
		}
	?>
    
    <? if(validatePermissions('system', 15)){ ?>
    	<li <? if($page_path[1] == 'admin' || $page_path[1] == 'users'){ echo 'class="active"'; }?>><a href='<? echo constant("ARCH_INSTALL_PATH"); ?><? echo constant("ARCH_HANDLER_MANAGE"); ?>/admin/dashboard/'>Admin</a></li>
    <? } ?>
</ul>