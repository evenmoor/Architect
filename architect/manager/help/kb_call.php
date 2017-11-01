<?
	$platform = $_POST['platform'];
	$version = $_POST['version'];
	$topic = $_POST['topic'];
	
	//$help_details = file_get_contents('http://webapps.irapture.com/KB/?platform='.$platform.'&version='.$version.'&topic='.$topic.'');
	//echo $help_details;
	
	// create curl resource
	$ch = curl_init();

	// set url
	curl_setopt($ch, CURLOPT_URL, 'http://webapps.irapture.com/KB/?platform='.$platform.'&version='.$version.'&topic='.$topic);

	//return the transfer as a string
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// $output contains the output string
	$output = curl_exec($ch);

	// close curl resource to free up system resources
	curl_close($ch); 
	
	echo $output;
?>
