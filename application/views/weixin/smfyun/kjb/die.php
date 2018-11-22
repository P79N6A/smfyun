<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>出错了</title>
  <style type="text/css">
  body{
    margin: 0;
    padding: 0;
    background-color: #efefef;
  }
  .background{
    margin-top: 12px;
    padding: 20px 16px;
    background-color: #fff;
    border-radius: 2px;
    border: 1px solid #d2d2d2;
  }
  .bg{
    width: 100%;
  }
  .text{
    padding: 0 3em;
    text-align: center;
    color: #666;
  }
  .foot a{
    text-decoration: none;
    color: #8a8afd;
  }
  .foot{
        text-align: center;
    font-size: 12px;
    color: #999;
    position: fixed;
    width: 100%;
    bottom: 20px;
  }
  </style>
</head>
<body>
<div class="lingqu">
  <div class="background">
    <img class="bg" src="../qwt/yyhb/sorry.png">
    <div class="text">未找到该订单或您不是订单发起人</div>
  </div>
  <div class="foot"><a href="http://yingyong.smfyun.com">神码浮云</a>提供技术支持</div>
</div>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
    <script type="text/javascript">
  wx.config({
    debug: 0,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: '<?php echo $jsapi["timestamp"];?>',
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'hideMenuItems'
      ]
  });
  wx.ready(function () {
    wx.checkJsApi({
      jsApiList: [
          'checkJsApi',
        'hideMenuItems'
      ],
      success: function (res) {
        console.log(res);
      }
    });
    wx.hideMenuItems({
            menuList: [
                        'menuItem:share:appMessage',
                        'menuItem:share:timeline',
            'menuItem:copyUrl',
            "menuItem:editTag",
            "menuItem:delete",
            "menuItem:originPage",
            "menuItem:readMode",
            "menuItem:openWithQQBrowser",
            "menuItem:openWithSafari",
            "menuItem:share:email",
            "menuItem:share:brand",
            "menuItem:share:qq",
            "menuItem:share:weiboApp",
            "menuItem:favorite",
            "menuItem:share:facebook",
            "menuItem:share:QZone"
            ],
            success: function (res) {
              console.log(res);
            },
            fail: function (res) {
              console.log(res);
            }
        });
   })
    </script>
</body>
</html>


