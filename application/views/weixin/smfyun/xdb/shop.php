<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>兑换中心</title>
<link href="../qwt/xdb/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="../qwt/xdb/css/home.css" rel="stylesheet" type="text/css"/>
<link href="http://at.alicdn.com/t/font_6yxmrwgmg7kl0udi.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.tb960x90 {display:none!important;display:none}
/*.biao {height: 3.414em;line-height: 3.414em;}*/
.biao span{width:8%;display: -webkit-inline-box;}
.biao span img{width:100%;}
.one{
	display: inline-block;
	/*width: calc(50% - 10px);*/
	/*width: 50%;*/
	width: 100%;
	font-size: 12px;
	padding: 5px 5px;
	border-bottom: none;
	height: calc(8em + 10px);
	position: relative;
	border-bottom: 1px solid #efefef;
}
.one img{
	/*width: 100%;*/
	max-width: 8em;
	max-height: 8em;
	display: inline-block;
	float: left;
}
.biao{
	display: inline-block;
	font-weight: bold;
	font-size: 1.2em;
	height: 2em;
	line-height: 2em;
	/*margin-top: .4em;*/
	/*margin-left: 1em;*/
	position: absolute;
	left: 7.5em;
	top: 5px;
}
.fubiao{
	color: #999;
	font-size: 1em;
	height: 1.6em;
	line-height: 1.6em;
	margin-bottom: 0;
}
.price{
	display: inline-block;
	position: absolute;
	color: #666;
	font-size: .9em;
	height: 1.3em;
	line-height: 1.3em;
	margin-bottom: 0;
	left: 10em;
	bottom: 5px;
}
.button-buy{
	position: absolute;
	right: .5em;
	font-size: 14px;
	bottom: calc(50% - .5em - 3px);
	/*float: right;*/
	display: inline-block;
	color: #fff;
	font-weight: bold;
	background-color: #f91848;
	padding: 5px;
	/*border: 1px solid orange;*/
	/*border-radius: 5px;*/
}
.bggray{
	background-color: #999;
}
</style>
</head>
<body>
<!--top-->
<div class="top_c">
	<p class="titi">
		兑换中心
	</p>
</div>
<!--头部-->
<!-- <div class="tou">
	<div class="sp_pr" style="border:none;">
		<img src="../qwt/xdb/images/gdsgf4.jpg">
		<div class="text_p">
			<p>
				Join
			</p>
			<span class="yue">兑换券 174 | 兑换商品 99 </span>
		</div>
	</div>
</div> -->
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
<div class="lie_b" style="margin-top:4em;margin-bottom:5em;">
	<div class="container" style="padding-left:0;padding-right:0;font-size:0">
	<?php foreach ($items as $k => $v):?>
		<div class="one">
			<img src="../qwtxdb/images/item/<?=$v->id?>"/>
			<div class="biao">
				<?=$v->name?>
			</div>
			<div class="price">
				<span style="color:orange;"><?=$v->score?></span> 兑换券
			</div>
			<?php if ($v->score>$user->score):?>
			<div class="button-buy bggray">
				兑换券不足
			</div>
			<?php else:?>
			<a class="button-buy" href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/checkout/<?=$v->id?>">
				立即兑换
			</a>
		<?php endif?>
		</div>
	<?php endforeach?>
	</div>
</div><!--
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
			<div class="col-xs-3">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/shop" class="dao">
				<i class="iconfont icon-shouye-copy-copy-copy" style="color:#246fc0;"></i>
				<span class="nav_ti" style="color:#246fc0;">兑换中心</span>
				</a>
			</div>
			<div class="col-xs-3">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/order" class="dao">
				<i class="iconfont icon-zhangdan"></i>
				<span class="nav_ti">兑换记录</span>
				</a>
			</div>
			<div class="col-xs-3">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/qrcode" class="dao">
				<i class="iconfont icon-qianbao-"></i>
				<span class="nav_ti">我的海报</span>
				</a>
			</div>
			<div class="col-xs-3">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/index" class="dao">
				<i class="iconfont icon-information"></i>
				<span class="nav_ti">个人中心</span>
				</a>
			</div>
		</div>
	</div>
</div>
<script src="../qwt/xdb/js/jquery.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/index.js" type="text/javascript"></script>
</body>
</html>
