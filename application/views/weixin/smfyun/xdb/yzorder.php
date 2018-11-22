<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>兑换券进度</title>
<link href="../qwt/xdb/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="../qwt/xdb/css/home.css" rel="stylesheet" type="text/css"/>
<link href="http://at.alicdn.com/t/font_l6a0fwucxvzehfr.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.tb960x90 {display:none!important;display:none}
.hint{
	color: #999;
	text-align: center;
	padding: 1em;
}
</style>
</head>
<body>
<!--top-->
<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/index">
<div class="top_c">
	<p class="titi">
		<< 返回主页
	</p>
</div>
</a>
<!--头部-->
<!-- <div class="pos">
	<div class="container">
		<div class="row titll">
			<a href="#">
			<div class="col-xs-2" style="color:#246fc0;border-bottom:1px solid #246fc0;">
				BTC
			</div>
			</a>
			<a href="#">
			<div class="col-xs-2">
				ETH
			</div>
			</a>
		</div>
	</div>
</div> -->
<!--列表-->
<div class="ding_d" style="margin-top:4em;">
<div class="hint">进程中订单（<?=count($orders)?>尚未完成 / <?=$orderscount?>已完成）<br>只有已完成（确认收货后）的订单会计算到进度中</div>
<?php foreach ($orders as $k => $v):?>
	<div class="on_d">
		<div class="sp_pr">
			<img src="<?=$v->qrcode->headimgurl?>">
			<div class="text_p">
				<p>
					<?=$v->title?> <!-- <span>银行转账</span> -->
				</p>
				<span class="yue"><?=$v->qrcode->nickname?></span>
				<span class="yue"><?=$v->update_time?></span>
			</div>
			<div class="button">
				<p>
					<span>￥<?=$v->total_fee?></span>
				</p>
				<?php if ($v->status=='TRADE_SUCCESS'):?>
				<a class="liji" style="width:auto;border-color:#00c451;color:#00c451">已完成</a>
			<?php elseif ($v->status=='WAIT_BUYER_CONFIRM_GOODS'):?>
				<a class="liji" style="width:auto;border-color:#c7af03;color:#c7af03">待确认</a>
			<?php else:?>
				<a class="liji" style="width:auto;border-color:orange;color:orange">已付款</a>
			<?php endif?>
			</div>
		</div>
	</div>
<?php endforeach?>
<div class="hint">已结算订单（<?=count($order_done)?>）</div>
<?php foreach ($order_done as $k => $v):?>
	<div class="on_d">
		<div class="sp_pr">
			<img src="<?=$v->qrcode->headimgurl?>">
			<div class="text_p">
				<p>
					<?=$v->title?> <!-- <span>银行转账</span> -->
				</p>
				<span class="yue"><?=$v->qrcode->nickname?></span>
				<span class="yue"><?=$v->update_time?></span>
			</div>
			<div class="button">
				<p>
					<span>￥<?=$v->total_fee?></span>
				</p>
			</div>
		</div>
	</div>
<?php endforeach?>
</div>
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
		</div>
	</div>
</div>
</body>
</html>
