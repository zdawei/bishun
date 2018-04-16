<?php
	require_once('../getActiveUrl.php');
	$url = get_one_active_url();
	if($url[0] == 0){echo json_encode( ['iserr' => 'true', 'errinfo' => 'Api failure: getretex-'.$url[1]] );return;}
	$url = $url[1];
	
	$ZI = $_POST["zi"];
	$NO = $_POST["no"];
	$XY = $_POST["xy"];
	header('Content-type: text/plain');

	session_start();
	//$content = file_get_contents("http://".$url."/getretex?zi=".$ZI."&no=".$NO."&xy=".$XY);
	$resdata = [];
	if(curl_http("http://".$url."/getretex?id=".session_id()."&zi=".$ZI."&no=".$NO."&xy=".$XY, $resdata)){
		echo $resdata;return;
	}else{
		echo json_encode( ['iserr' => 'true', 'errinfo' => 'Api failure: getretex-'.$url] );return;
	}
?>