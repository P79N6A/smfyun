<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>兑换记录</title>
<link href="../qwt/xdb/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="../qwt/xdb/css/home.css" rel="stylesheet" type="text/css"/>
<link href="http://at.alicdn.com/t/font_l6a0fwucxvzehfr.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.tb960x90 {display:none!important;display:none}
</style>
</head>
<body>
<!--top-->
<!-- <div class="top_c">
	<p class="titi">
		兑换记录
	</p>
</div> -->
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
<div class="ding_d" style="margin-top:0;">
<?php foreach ($orders as $k => $v):?>
	<?php if ($v->item->type==0&&$v->order_state==0&&$v->need_money!=0):?>
	<?php else:?>
	<div class="on_d">
		<div class="sp_pr">
			<img src="../qwtxdb/images/item/<?=$v->iid?>">
			<div class="text_p">
				<p>
					<?=$v->item->name?> <!-- <span>银行转账</span> -->
				</p>
				<span class="yue"><span style="color:#fff">|</span><?=$v->city?> <?php if ($v->item->type==0):?><small style='color:orange'><?=$v->status==1 ? '已发货' : '未发货'?></small><?php endif?><?php if ($v->item->type==3):?><small style='color:orange'><?=$v->status==1 ? '已办理' : '未办理'?></small><?php endif?></span>
				<span class="yue"><?=date('Y-m-d h:i:s',$v->createdtime)?></span>
			</div>
			<div class="button">
				<p>
					<!-- <span>46460.92 CNY</span> -->
				</p>
				<?php if ($v->url):?>
				<a href="..<?=$v->url?>" class="liji">领取</a>
			<?php endif?>
			<?php if ($v->item->type==0&&$v->status==1):?>
				<a href="../qwtxdb/order_detail/<?=$v->id?>" class="liji">详情</a>
			<?php endif?>
			</div>
		</div>
	</div>
<?php endif?>
<?php endforeach?>
</div>
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
				<i class="iconfont icon-zhangdan" style="color:#246fc0;"></i>
				<span class="nav_ti" style="color:#246fc0;">兑换记录</span>
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
