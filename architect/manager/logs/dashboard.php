<? if(validatePermissions('system', 15)){ ?>
<? require(constant("ARCH_BACK_END_PATH").'manager/includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>System Log | <? echo $site_settings['name']; ?></title>
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
        		<h1>System Log</h1>
            </div>
            
            <div class='single_column'>
            	<h2>Entries</h2>
                <form action='<? echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
                	<?
						$first_entry = $last_entry = $entry_count = '';
						
						$first_entry = mysql_query('SELECT system_log_timestamp
													FROM tbl_system_log
													ORDER BY system_log_entry_ID ASC
													LIMIT 1');
						$first_entry = mysql_fetch_assoc($first_entry);
						
						$first_entry = strtotime($first_entry['system_log_timestamp']);
						$first_entry = date('m/d/Y', $first_entry);
						
						$last_entry = mysql_query('SELECT system_log_timestamp
													FROM tbl_system_log
													ORDER BY system_log_entry_ID DESC
													LIMIT 1');
						$last_entry = mysql_fetch_assoc($last_entry);
						
						$last_entry = strtotime($last_entry['system_log_timestamp']);
						$last_entry = date('m/d/Y', $last_entry);
					
						$entry_count = mysql_query('SELECT COUNT(*) FROM tbl_system_log');
						$entry_count = mysql_fetch_assoc($entry_count);
						$entry_count = $entry_count['COUNT(*)'];
					
                		$filter_start = $filter_end = $filter = $filter_limit = '';
						$limit = '10';
						
						if(isset($_POST['filter_limit']) && trim($_POST['filter_limit']) != ''){
							$filter_limit = $_POST['filter_limit'];
							$limit = $filter_limit;
						}
						
						if(isset($_POST['filter_start']) && trim($_POST['filter_start']) != ''){
							$filter_start = $_POST['filter_start'];
							$timestamp = strtotime($_POST['filter_start']);
							$timestamp = date('Y-m-d H:i:s', $timestamp);
							$filter .= 'WHERE system_log_timestamp >= "'.$timestamp.'"';
						}
						
						if(isset($_POST['filter_end']) && trim($_POST['filter_end']) != ''){
							$filter_end = $_POST['filter_end'];
							$timestamp = strtotime($_POST['filter_end']);
							$timestamp = date('Y-m-d H:i:s', $timestamp);
							if($filter == ''){
								$filter .= 'WHERE ';
							}else{
								$filter .= 'AND ';
							}
							
							$filter .= 'system_log_timestamp <= "'.$timestamp.'"';
						}
					?>
                	<p><label>Date Range:</label> <input type='text' name='filter_start' value='<? echo $filter_start; ?>' class='ui_date_picker' placeholder='first: <? echo $first_entry; ?>' style='width:100px;'/> - <input type='text' name='filter_end' value='<? echo $filter_end; ?>' style='width:100px;' class='ui_date_picker' placeholder='last: <? echo $last_entry; ?>'/></p>
                    <p><label>Limit:</label> <input name='filter_limit' value='<? echo $filter_limit; ?>' placeholder='total: <? echo $entry_count; ?>'/></p>
                	<p><input type='submit' value='Filter Entries' /></p>
                </form>
				<?
					$limit;
					
					$log_entries = mysql_query('SELECT system_log_entry,
														system_log_timestamp
													FROM tbl_system_log
													 '.$filter.' 
													ORDER BY system_log_timestamp ASC
													LIMIT '.$limit.'');
					
					echo mysql_error();
					
					$counter = 0;
					while($entry = mysql_fetch_assoc($log_entries)){
						$odd = '';
						if($counter  % 2 != 0){
							$odd = 'odd';
						}
						
						?><div class='table_row <? echo $odd; ?>'>
							<?
								$timestamp = strtotime($entry['system_log_timestamp']);
								$timestamp = date('m/d/y g:ia', $timestamp);
							?>
							<span class='table_column' style='width:100%;'>
                                <span class='table_column' style='min-width:10%; margin-right:1%;'>
                                    <strong><? echo $timestamp; ?></strong>
                                </span>
                                <span class='table_column' style='max-width:89%;'>
                                    <em><? echo $entry['system_log_entry']; ?></em>
                                </span>
                            </span>
						</div><?
						$counter++;
					}
				?>
            </div><!--/users-->
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