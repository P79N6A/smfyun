<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>个人中心</title>
<link href="../qwt/xdb/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="../qwt/xdb/css/home.css" rel="stylesheet" type="text/css"/>
<link href="http://at.alicdn.com/t/font_6yxmrwgmg7kl0udi.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.tb960x90 {display:none!important;display:none}
.biao {height: 3.414em;line-height: 3.414em;}
.biao span{width:8%;display: -webkit-inline-box;}
.biao span img{width:100%;}
.yue a{
	display: -webkit-inline-box;
	color: #fff;
	text-decoration: none;
}
.tou2{
    background-image: none;
}

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
.middle{
	display: inline-block;
	position: absolute;
	color: #999;
	left: 9em;
	bottom: calc(50% - 0.5em);
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
	bottom: 10px;
}
.old-price{
	position: absolute;
	top: 5px;
	right: .5em;
	line-height: 2em;
	height: 2em;
}
.button-buy{
	position: absolute;
	right: .5em;
	font-size: 14px;
	/*bottom: calc(50% - .5em - 3px);*/
	bottom: 10px;
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
<!-- <div class="top_c">
	<p class="titi">
		个人中心
	</p>
</div> -->
<!--头部-->
<div class="tou" style="margin-top:0">
	<div class="sp_pr" style="border:none;">
		<img src="<?=$user->headimgurl?>">
		<div class="text_p">
			<p>
				<?=$user->nickname?>
			</p>
			<span class="yue"><a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/shop">兑换券 <?=$user->score?></a> | <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/order">兑换商品 <?=$ordercount?> <?=$limit>0?'/ '.$limit:''?></a> </span>
		</div>
	</div>
</div>
<div class="tou2" style="">
	<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/yzorder">
	<div class="col-xs-12">
		 下一枚兑换券推荐进度： <?=$targetcount?> / <?=$target?> <span style="color:#43e0ff"> >></span>
	</div>
	</a>
	<!-- <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/shop">
	<div class="col-xs-6">
		<img src="../qwt/xdb/images/bottom.png" height="30px"/> 邀请进度
	</div>
	</a> -->
</div>
<!--列表-->

<div class="lie_b" style="margin-top:1em;margin-bottom:5em;">
	<div class="container" style="padding-left:0;padding-right:0;font-size:0">
	<?php foreach ($items as $k => $v):?>
		<div class="one">
			<img src="../qwtxdb/images/item/<?=$v->id?>"/>
			<div class="biao">
				<?=$v->name?>
			</div>
			<div class="middle">
			还剩<?=$v->stock?>件<?php
			if ($v->limit>0) {
				if ($v->type==0&&$v->need_money>0) {
					$count = ORM::factory('qwt_xdbchange')->where('bid','=',$bid)->where('iid','=',$v->id)->where('qid','=',$user->id)->where('order_state','=',1)->count_all();
					}else{
						$count = ORM::factory('qwt_xdbchange')->where('bid','=',$bid)->where('iid','=',$v->id)->where('qid','=',$user->id)->count_all();
						}
						$left = $v->limit - $count;
						if ($left>0) {
						echo "/您还可兑换".$left."次";
						}else{
							echo "您不能再兑换该商品了";
						}
						}?>
						</div>
			<div class="old-price">原价<span style="color:orange">￥<?=$v->price?></span></div>
			<div class="price">
				<span style="color:orange;font-weight:bold;font-size:1.8em"><?=$v->score?></span> 兑换券
			</div>
			<?php if ($v->limit>0&&$left==0):?>
			<div class="button-buy bggray">
				不能再兑换
			</div>
			<?php elseif ($limit>0&&$ordercount>=$limit):?>
			<div class="button-buy bggray">
				不能再兑换
			</div>
			<?php elseif ($v->score>$user->score):?>
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
</div>
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
</div> --><!--
<div class="lie_b">
	<div class="container">
		<div class="one">
			<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/shop">
			<p class="biao">
				<span class="iconfont"><img src="../qwt/xdb/images/uico3.png"/></span>礼品中心<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one">
			<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/order">
			<p class="biao">
				<span class="iconfont"><img src="../qwt/xdb/images/uico1.png"/></span>兑换记录<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one" style="border-bottom:none;">
			<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/qrcode">
			<p class="biao">
				<span class="iconfont"><img src="../qwt/xdb/images/uico4.png"/></span>邀请好友<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
	</div>
</div> --><!--
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
				<i class="iconfont icon-shouye-copy-copy-copy" style="color:#246fc0;"></i>
				<span class="nav_ti" style="color:#246fc0;">兑换中心</span>
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
				<i class="iconfont icon-qianbao-"></i>
				<span class="nav_ti">邀请好友</span>
				</a>
			</div>
			<!-- <div class="col-xs-3">
				<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/index" class="dao">
				<i class="iconfont icon-information" style="color:#246fc0;"></i>
				<span class="nav_ti" style="color:#246fc0;">个人中心</span>
				</a>
			</div> -->
		</div>
	</div>
</div>
<script src="../qwt/xdb/js/jquery.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/index.js" type="text/javascript"></script>
</body>
</html>
