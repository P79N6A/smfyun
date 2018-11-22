<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>邀请好友</title>
<link href="../qwt/xdb/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="../qwt/xdb/css/home.css" rel="stylesheet" type="text/css"/>
<link href="http://at.alicdn.com/t/font_6yxmrwgmg7kl0udi.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.tb960x90 {display:none!important;display:none}
.biao {height: 3.414em;line-height: 3.414em;}
.biao span{width:8%;display: -webkit-inline-box;}
.biao span img{width:100%;}
.tou img{
	width: 100%;
}
.tou{
	background-color: #ececec;
}
.hint{
	color: #999;
	text-align: center;
	padding: 1em;
}
.tou span{display: inline;}
.tou p{font-size: 12px;text-align: left;color:#666;}
.title{
	text-align: center;
	border-bottom: 2px solid red
}
</style>
</head>
<body>
<!--top-->
<!-- <div class="top_c">
	<p class="titi">
		我的海报
	</p>
</div> -->
<!--头部-->
<div class="tou" style="margin-bottom:3.857em;margin-top:0;padding-top:0">
		<img class="qrimg" src="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/shareticket/<?=$bid?>/<?=$user->openid?>">
<div class="hint">
		如您的二维码过期，请<a class="update">点击更新</a> / 下拉查看活动说明
</div>
<div class="title">活动说明</div>
<div class="hint">
		<?=$rule?>
</div>
</div>
<!-- <div class="tou2" style="">
	<a href="#">
	<div class="col-xs-6">
		<img src="images/top.png" height="30px"/> 我购买的
	</div>
	</a>
	<a href="#">
	<div class="col-xs-6">
		<img src="images/bottom.png" height="30px"/> 我出售的
	</div>
	</a>
</div> -->
<!--列表-->
<!-- <div class="lie_b">
	<div class="container">
		<div class="one">
			<a href="r#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico1.png"/></span>我的广告<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one" style="border-bottom:none;">
			<a href="#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico2.png"/></span>受信任的<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
	</div>
</div> -->
<!-- <div class="lie_b">
	<div class="container">
		<div class="one">
			<a href="#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico3.png"/></span>礼品中心<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one">
			<a href="r#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico1.png"/></span>兑换记录<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one" style="border-bottom:none;">
			<a href="#">
			<p class="biao">
				邀请好友们扫二维码，好友们每完成（确认收货）<?=$target?>笔订单，您获得一张兑换券
			</p>
			</a>
		</div>
	</div>
</div> -->
<!--
<div class="lie_b" style=" margin-bottom: 5.571em;">
	<div class="container">
		<div class="one">
			<a href="#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico5.png"/></span>设置<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one">
			<a href="#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico6.png"/></span>建议反馈<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one">
			<a href="#">
			<p class="biao">
				<span class="iconfont "><img src="images/uico7.png"/></span>关于我们<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one">
			<a href="#">
			<p class="biao">
				<span class="iconfont"><img src="images/uico8.png"/></span>客服中心<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one" style="border-bottom:none;">
			<a href="#">
			<p class="biao">
				<span class="iconfont "><img src="images/uico9.png"/></span>退出<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
	</div>
</div> -->
<!--footer-->
<div class="footer">
	<div class="container">
		<div class="row">
			<div class="col-xs-4">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/index" class="dao">
				<i class="iconfont icon-shouye-copy-copy-copy"></i>
				<span class="nav_ti">兑换中心</span>
				</a>
			</div>
			<div class="col-xs-4">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/order" class="dao">
				<i class="iconfont icon-zhangdan"></i>
				<span class="nav_ti">兑换记录</span>
				</a>
			</div>
			<div class="col-xs-4">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/qrcode" class="dao">
				<i class="iconfont icon-qianbao-" style="color:#246fc0;"></i>
				<span class="nav_ti" style="color:#246fc0;">邀请好友</span>
				</a>
			</div>
		</div>
	</div>
</div>
<script src="../qwt/xdb/js/jquery.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/index.js" type="text/javascript"></script>
<script type="text/javascript">
	$('.update').click(function(){
		var a = parseInt(Math.random()*100);
		$('.qrimg').attr("src","http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/shareticket/<?=$bid?>/<?=$user->openid?>?refresh=1&num="+a);
	})
</script>
</body>
</html>
