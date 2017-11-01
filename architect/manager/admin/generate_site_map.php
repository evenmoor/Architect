<? if(validatePermissions('system', 15)){ ?>
<?
	//start map
	$site_map_content = "<?xml version='1.0' encoding='UTF-8'?> \r\n";
	$site_map_content .= "\r\n";
	$site_map_content .= "<urlset xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' \r\n xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd' \r\n xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'> \r\n";

	//Navigation elements
	$nav_items = mysql_query('SELECT navigation_menu_item_link_target
								FROM tbl_navigation_menu_items
								WHERE navigation_menu_item_navigation_menu_FK = "'.clean($_POST['nav']).'"
								ORDER BY navigation_menu_item_position DESC');
	
	while($item = mysql_fetch_assoc($nav_items)){
		$internal_link_check = explode(":", $item['navigation_menu_item_link_target']);
		
		//handler for various architect link types
		switch($internal_link_check[0]){
			case "arch_doc":
				$document = mysql_fetch_assoc(mysql_query('SELECT document_ID,
																document_modified,
																document_name
															FROM tbl_documents
															WHERE document_ID="'.clean($internal_link_check[1]).'"
															LIMIT 1'));						
				
				if(constant("ARCH_DOCUMENT_PATH_ID")){//check to see if ids are in page paths
					$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$document['document_ID'].'-'.$document['document_name'].'/';
				}else{
					$document_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_PAGE").'/'.$document['document_name'].'/';
				}
				
				if(isset($_SERVER['HTTPS'])){//handle https connections
					$document_path = "https://".$_SERVER['SERVER_NAME'].$document_path;
				}else{//handle other connections
					$document_path = "http://".$_SERVER['SERVER_NAME'].$document_path;
				}
			
				$site_map_content .= "\r\n";
				
				$site_map_content .= "<url>";
				$site_map_content .= "\r\n";
					$site_map_content .= "<loc>".$document_path."</loc>";
					$site_map_content .= "\r\n";
					$site_map_content .= "<lastmod>".date("Y-m-d", strtotime($document['document_modified']))."</lastmod>";
					$site_map_content .= "\r\n";
					$site_map_content .= "<changefreq>monthly</changefreq>";
					$site_map_content .= "\r\n";
					$site_map_content .= "<priority>0.5</priority>";
				$site_map_content .= "\r\n";
				$site_map_content .= "</url>";
				
				$site_map_content .= "\r\n";
			break;
		}
	}

	//finish map
	$site_map_content .= "\r\n";
	$site_map_content .= "</urlset>";

	//update map
	mysql_query('UPDATE tbl_site_settings
				SET site_map="'.clean($site_map_content).'" 
				WHERE site_ID="1"
				LIMIT 1');
	//log			
	systemLog('Site Map generated based on: '.$_POST['nav'].'');
	
	//return
	$admin_path = constant("ARCH_INSTALL_PATH").constant("ARCH_HANDLER_MANAGE").'/admin/seo_settings/';
	header('Location: '.$admin_path);
?>
<? 
}else{
	require(constant("ARCH_BACK_END_PATH").'users/invalid_permissions.php');
} 
?>