<?php
	require_once('../getActiveUrl.php');
	$url = get_one_active_url();
	if($url[0] == 0){echo json_encode( ['iserr' => 'true', 'errinfo' => 'Api failure: getinfo-'.$url[1]] );return;}
	$url = $url[1];

	$ZI = $_POST["zi"];
	header('Access-Control-Allow-Origin:*');  #张忠伟添加 调试
	header('Access-Control-Allow-Methods:POST'); #张忠伟添加 调试
	header('Access-Control-Allow-Headers:x-requested-with,content-type'); #张忠伟添加 调试
	header('Content-type: text/plain');
	
	$resdata = [];
	if(curl_http("http://".$url."/getinfo?&zi=".$ZI, $resdata)){
		echo $resdata;return;
	}else{
		echo json_encode( ['iserr' => 'true', 'errinfo' => 'Api failure: getinfo-'.$url] );return;
	}
?>