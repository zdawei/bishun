<?php
	require_once('../getActiveUrl.php');
	$url = get_one_active_url();
	if($url[0] == 0){echo json_encode( ['iserr' => 'true', 'errinfo' => 'Api failure: getlist-'.$url[1] ] );return;}
	$url = $url[1];
	
	$ID = $_POST["id"];
	header('Content-type: text/plain');
	
	$resdata = [];
	session_start();
	session_regenerate_id(true);
	if(curl_http("http://".$url."/getlist?count=6&id=".$ID, $resdata)){
		echo $resdata; return;
	}else{
		echo json_encode( ['iserr' => 'true', 'errinfo' => 'Api failure: getlist-'.$url] );return;
	}
?>