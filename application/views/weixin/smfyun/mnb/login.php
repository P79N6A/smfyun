<!--Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
<title>登录</title>
<!-- Custom Theme files -->
<link href="../../qwt/mnb/style.css" rel="stylesheet" type="text/css" media="all"/>
<!-- Custom Theme files -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="登录" />
<!--Google Fonts-->
<!--<link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
--><!--Google Fonts-->
<style type="text/css">
	.warning{
    color: #900a0a;
    padding: 10px 20px;
    width: 50%;
    margin: 0 auto 20px auto;
    background-color: rgba(255, 151, 101, 0.85);
	}
 input::-webkit-input-placeholder{
	   color: #fff;
	}
	.onchecking{
    color: #0a9087;
    padding: 10px 20px;
    width: 50%;
    margin: 0 auto 20px auto;
    background-color: rgba(85, 255, 65, 0.85);
	}
</style>
</head>
<body>
<!--header start here-->
<div class="login-form" style="text-align:center;">
			<div style="height:130px;">
			</div>
				<?php if ($result['error']):?>
				<div class="warning"><?=$result['error']?></div>
			<?php endif?>
				<?php if ($result['ok']):?>
				<div class="onchecking"><?=$result['ok']?></div>
			<?php endif?>
			<div class="login-top" <?=$result['ok']?'style="display:none"':''?>>
			<form method="post">
				<div class="login-ic" style="background:rgba(0,0,0,.32);">
					<i ></i>
					<input class="forleftline" type="text" name="tel" value="" placeholder="手机号"/>
					<div class="clear"> </div>
				</div>
				<div class="login-ic" style="background:rgba(0,0,0,.32);">
					<i class="icon"></i>
					<input class="forleftline" type="password" name="passwd" value="" placeholder="密码">
					<div class="clear"> </div>
				</div>
				<div class="login-ic" style="background:rgba(0,0,0,.32);">
					<i class="icon2"></i>
					<input class="forleftline" type="text" name="name" placeholder="姓名">
					<div class="clear"> </div>
				</div>
				<div class="login-ic" style="background:rgba(0,0,0,.32);">
					<i class="icon3"></i>
					<input class="forleftline" type="text" name="pcode" placeholder="授权码">
					<div class="clear"> </div>
				</div>

				<div class="log-bwn">
					<button type="submit" class="loginbutton">Login</button>
				</div>
				</form>
			</div>
			<!-- <p class="copy">© 2016 xxxxxxxxxxx</p> -->
</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<!--header start here-->
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
