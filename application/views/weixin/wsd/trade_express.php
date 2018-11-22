<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="ch" data-dpr="1" style="font-size: 12px;">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
		<title>物流信息</title>
		<style>
			body{font-size: 12px;margin: 0;background-color: #f1f2f6;}
			ul li{list-style: none;}
			.track-rcol{width: 900px; border: 1px solid #eee;}
			.ordernumber{padding: 20px; padding-left: 5px; position: relative;background-color: #fff;margin-top: 20px;border-top:1px solid #e4e5e9;border-bottom: 1px solid #e4e5e9;
    line-height: 18px;
    padding-left: 65px;
    color: #999;}
			.track-list{padding: 20px; padding-left: 5px; position: relative;background-color: #fff;margin-top: 20px;border-top:1px solid #e4e5e9;border-bottom: 1px solid #e4e5e9}
			.track-list li{position: relative; padding: 9px 0 0 25px; line-height: 18px; border-left: 1px solid #d9d9d9; color: #999;}
			.track-list li.first{color: red; padding-top: 0; border-left-color: #fff;}
			.track-list li .node-icon{position: absolute; left: -6px; top: 50%; width: 11px; height: 11px; background: url(/wsd/img/order-icons.png)  -21px -72px no-repeat;}
			.track-list li.first .node-icon{background-position:0 -72px;}
			.track-list li .time{margin-right: 20px; position: relative; top: 4px; display: inline-block; vertical-align: middle;}
			.track-list li .txt{max-width: 600px; position: relative; top: 4px; display: inline-block; vertical-align: middle;}
			.track-list li.first .time{margin-right: 20px; }
			.track-list li.first .txt{max-width: 600px; }
		</style>
	</head>
	<body>
			<div class="ordernumber"><span>订单编号：<?=$tid?></span><br><span>快递公司：<?=$result['response']['express_id']?$result['response']['express_id']:'无'?></span><br><span>物流单号：<?=$result['response']['nu']?$result['response']['nu']:'无'?></span></div>
			<div class="track-list">
				<ul style="padding-left:40px;">
				<?php if($res[0]):?>
					<?php foreach ($res as $k => $v):?>
						<li class="<?=$k==0?'first':''?>">
							<i class="node-icon"></i>
							<span class="time"><?=$v->time?></span>
							<span class="txt"><?=$v->context?></span>
						</li>
					<?php endforeach?>
				<?php else:?>
						暂无物流信息
				<?php endif?>
				</ul>
			</div>
	</body>
</html>
