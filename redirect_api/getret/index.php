<?php
	require_once('../getActiveUrl.php');
	$url = get_one_active_url();
	if($url == ''){return 'error';}
	
	$ZI = $_GET["zi"];
	$NO = $_GET["no"];
	$XY = $_GET["xy"];
	header('Access-Control-Allow-Origin:*');  #张忠伟添加 调试
	header('Access-Control-Allow-Methods:POST'); #张忠伟添加 调试
	header('Access-Control-Allow-Headers:x-requested-with,content-type'); #张忠伟添加 调试
	header('Content-type: text/plain');
	
	$content = file_get_contents("http://".$url."/getret?zi=".$ZI."&no=".$NO."&xy=".$XY);
	echo $content;
?>