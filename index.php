<?php
/*
require_once('library/jssdk.php');
$jssdk = new JSSDK("wx1d7077b1e7f684ca", "a4a2a36bb7c53d056a9ec5b628c2e846") or die;
$signPackage = $jssdk->getSignPackage() or die;
*/
	session_start();
	session_regenerate_id(true);
?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>手写汉字，纠正错误笔顺</title>
    <link rel="stylesheet" type="text/css" href="./css/semantic.css" />
	<link rel="shortcut icon" type="image/x-icon" href="http://nlplab.blcu.edu.cn/img/shortpic.png" />
    <script>    
	  //$URL = 'nlplab.blcu.edu.cn';
	  $URL = '202.112.195.172/bisun';
      $CONFIG = {};
      $CONFIG['getlist'] = "http://"+$URL+"/redirect_api/getlist/";
      $CONFIG['getinfo'] = "http://"+$URL+"/redirect_api/getinfo/";
      $CONFIG['getret'] = "http://"+$URL+"/redirect_api/getret/";
      $CONFIG['getscore'] = "http://"+$URL+"/redirect_api/getscore/";
      $CONFIG['getretex'] = "http://"+$URL+"/redirect_api/getretex/";
    </script>
  </head>
  <body>
    <!--<audio src="./audio/global.wav" autoplay="autoplay" loop="loop"></audio>-->
    <div id='writing'> 
        <v-begin  :is_show="isShow" @cust="custShowFunc"> </v-begin>
        <!--擦，这里的自定义事件中间不能大写。。。。。-->
        <v-selec :is_show="isShow" @cust="custShowFunc"> </v-selec>
        <v-canvas :is_show="isShow" @cust="custShowFunc"> </v-canvas>
        <v-result :is_show="isShow" @cust="custShowFunc"> </v-result>
        <!--<v-button :is_show="isShow" @cust="custShowFunc"> </v-button>-->
    </div>
    <script type="text/javascript" src="./js/build/bundle.min.js" ></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<!--script>
var wx_content_package = {
    'title' : '颠覆三观！纠正你的汉字笔顺',
    'imgUrl': 'http://nlplab.blcu.edu.cn:80/image/hanzi-logo.jpg',
    'link'  : 'http://nlplab.blcu.edu.cn:80/',
    'desc'  : '看看语文老师有哪些笔顺错误，记得不要写连笔字',
};

wx.config({
    //debug: true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
        //'checkJsApi',
        'showOptionMenu',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
    ]
  });
  
  wx.ready(function () {
      wx.showOptionMenu();

      wx.onMenuShareTimeline({
        title   :   wx_content_package.title,
        link    :   wx_content_package.link,
        imgUrl  :   wx_content_package.imgUrl,
        trigger: function (res) {},
        success: function (res) {},
        cancel: function (res) {},
        fail: function (res) {}
      });
	  
      wx.onMenuShareAppMessage({
        desc    :   wx_content_package.desc,
        title   :   wx_content_package.title,
        link    :   wx_content_package.link,
        imgUrl  :   wx_content_package.imgUrl,
          trigger: function (res) {},
          success: function (res) {},
          cancel: function (res) {},
          fail: function (res) {}
      });
  });
  wx.error(function (res) {
    alert(res.errMsg);
  });
</script-->
  </body>
</html>