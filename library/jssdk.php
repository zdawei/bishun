<?php
class JSSDK {
  private $appId;
  private $appSecret;
  private $dir;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
    $this->dir = 'cache/';
	$this->url = 'http://nlplab.blcu.edu.cn/';
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url = $this->url;
	
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
        "appId"     => $this->appId,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string
    );
    return $signPackage;
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket($type = '') {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $ticket_file = $type == '' ?  $this->appId.'_jsapi_ticket.php' : $this->appId.'_card_ticket.php';
    
    $file_exists = file_exists($this->dir . $ticket_file);
    $data = json_decode($this->get_php_file($ticket_file));
    if (!$file_exists || ($data->expire_time - 20 ) < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      //https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=wx_card
      if($type == 'card'){
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$accessToken&type=wx_card";
      }else{
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      }
      // echo $url;
      $res = json_decode($this->httpGet($url));
      //var_dump($res);exit;
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = abs(time() + 7000);
        $data->jsapi_ticket = $ticket;
        $this->set_php_file($ticket_file, json_encode($data));
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  public function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file($this->appId."_access_token.php"));
    if (!file_exists($this->dir . $this->appId.'_access_token.php') || $data->expire_time < time()) {
      //var_dump($this);
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      //var_dump($res);
      $access_token = $res->access_token;
      $expire_time = $res->expires_in;
      if ($access_token) {
        $data->expire_time = abs(time() + 100);
        $data->access_token = $access_token;
        $this->set_php_file($this->appId."_access_token.php", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    //print_r(curl_error($curl));
    curl_close($curl);

    return $res;
  }

  /*******************************************************
   *      微信卡券：JSAPI 卡券Package - 基础参数没有附带任何值 - 再生产环境中需要根据实际情况进行修改
   *******************************************************/
  public function wxCardPackage($cardId = '', $open_id= ""){
    $timestamp = time();
    $api_ticket = $this->getJsApiTicket('card');
    $no_cestr = $this->wxNonceStr();
    $cardId = $cardId;
    // time , code no_scr , ticket , cardid
    $code = $no_cestr . '_' . (time() + 1000);
    $arrays = array($api_ticket, $timestamp, $no_cestr,$cardId, $open_id);
    sort($arrays , SORT_STRING);

    $string = sha1(implode($arrays));
    $resultArray['str'] = implode($arrays);
    $resultArray['card_id'] = $cardId;
    $resultArray['nonce_str'] = $no_cestr;
    $resultArray['code'] = $code;
    $resultArray['ticket'] = $api_ticket;
    $resultArray['openid'] = $open_id;
    $resultArray['timestamp'] = $timestamp;
    $resultArray['signature'] = $string;

    return $resultArray;
  }

  /*****************************************************
   *      生成随机字符串 - 最长为32位字符串
   *****************************************************/
  public function wxNonceStr($length = 16, $type = FALSE) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    if($type == TRUE){
      return strtoupper(md5(time() . $str));
    }
    else {
      return $str;
    }
  }

  /*******************************************************
   *  微信卡券：获取卡券列表
   *******************************************************/
  public function wxCardListPackage($cardType = "" , $cardId = ""){
    //$api_ticket = $this->wxVerifyJsApiTicket();
    $resultArray = array();
    $timestamp = time();
    $nonceStr = $this->wxNonceStr();
    //$strings =
    $arrays = array($this->appId,$this->appSecret,$timestamp,$nonceStr);
    sort($arrays , SORT_STRING);
    $string = sha1(implode($arrays));

    $resultArray['app_id'] = $this->appId;
    $resultArray['card_sign'] = $string;
    $resultArray['time_stamp'] = $timestamp;
    $resultArray['nonce_str'] = $nonceStr;
    $resultArray['card_type'] = $cardType;
    $resultArray['card_id'] = $cardId;
    return $resultArray;
  }

  /**
   * 卡的详情
   * @param type $filename
   * @return type
   */
  public function getCardInfo($cardId){
    $data = array(
        'card_id' => $cardId,
    );
    $access_token = $this->getAccessToken();
    $result = $this->http_post('https://api.weixin.qq.com/card/get?access_token=' . $access_token, json_encode($data));
    //file_put_contents(APPPATH . 'cache/error.txt',var_export($result, true));
    if ($result) {
      $json = json_decode($result, true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg  = $json['errmsg'];
        return false;
      }
      return $json;
    }
    return false;
  }

  /**
   * POST 请求
   * @param string $url
   * @param array $param
   * @param boolean $post_file 是否文件上传
   * @return string content
   */
  protected function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
      $strPOST = $param;
    } else {
      $aPOST = array();
      foreach($param as $key=>$val){
        $aPOST[] = $key."=".urlencode($val);
      }
      $strPOST =  join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    //echo 112312313;
    //echo "3asfasdfasdfadsf" .$sContent;
    //print_r($sContent);
    //print_r(curl_error($oCurl));
    $aStatus = curl_getinfo($oCurl);
    //print_r($aStatus);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
      return $sContent;
    }else{
      return false;
    }
  }



  private function get_php_file($filename) {
    return trim(file_get_contents($this->dir . $filename));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($this->dir . $filename, "w");
    fwrite($fp, $content);
    fclose($fp);
  }
}
