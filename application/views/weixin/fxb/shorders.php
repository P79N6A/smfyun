<?php
function convert($type){
    switch ($type) {
        case 0:
        echo '虚拟奖品';
            break;
        case 1:
        echo '微信卡券';
            break;
        case 2:
        echo '实物奖品';
            break;
        case 3:
        echo '话费流量';
            break;
        case 4:
        echo '微信红包';
            break;
        case 5:
        echo '有赞赠品';
            break;
        default:
            # code...
            break;
    }
}
?>
<!doctype html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1.0 user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <title>我的奖励</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/ui.css">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/style.css">
  <style>
.text_menu
{
  font-size: 15px;
  color: #607fa6;
  opacity: 0.6;
}
  </style>
</head>
<body class="page-index" data-page="index" ontouchstart="">
<div class="wrp">
      <!-- UserInfo -->
      <div id="js_main" class="profile_hd">
        <div class="inner">
          <img class="profile_logo" src="<?=$userobj->headimgurl?>">
          <div class="profile_name">
          <?=$userobj->nickname?>
          </div>
          <div class="data_overview">
            <ul class="overview_list" style="width:100%">
              <li class="overview_item">
              <a href='/fxb/shscore'>
                <p class="desc r_line">
                  可用<?=$config['scorename']?>
                </p>
                <p class="number">
                  <?=abs($userobj->shscore)?>
                </p>
                </a>
              </li>
              <li class="overview_item" >
              <a href='/fxb/home'>
                <p class="desc r_line"style="font-size: 22px;top: 14px;">
                  我的奖励
                </p>
                <!-- <p class="number">
                  0
                </p> -->
              </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
</div>
    <style>
.left2 {
    overflow: hidden;
    width: 100%;
}
</style>

<ul class="cd-gallery">

<?php foreach ($orders as $order):?>

        <li>
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">
                    <li class="selected"><img src="http://<?=$_SERVER['HTTP_HOST']?>/fxb/images/item/<?=$order->item->id?>.v<?=$order->item->lastupdate?>.jpg" alt="<?=$order->item->name?>"></li>
                </ul>
            </div> <!-- .cd-single-item -->

                <div class="order-info">
                    <ul>
                    <li class="left2"><b>奖品名称：</b><?=$order->item->name?></li>

                    <li class="left"><b>奖品价格：</b><?=$order->item->price?>元</li>
                    <li class="right"><b>消费<?=$config['scorename']?>：</b><?=$order->score?><?=$config['scorename']?></li>
                    <li class="left"><b>兑换时间：</b><?=date('Y-m-d H:i', $order->createdtime)?></li>
                    <li class="right"><b>订单状态：</b><?=$order->status ? '已发货' : '未发货'?></li>

                    <?php if (!$order->url):?>
                        <li class="left2"><b>收货信息：</b><?=$order->city.' '.$order->address?></li>
                        <!--以下信息为实物，并且已处理时显示，未处理或非实物不显示-->
                        <?php if($order->shipcode&&$order->type==null):?>
                            <li class="left"><b>快递单号：</b><?=$order->shipcode?></li>
                            <li class="right"><b>快递类型：</b><?=$order->shiptype?></li>
                        <?php endif?>
                    <?php endif?>

                    </ul>
                </div>

                <!-- 如果为虚拟物品出现以下按钮跳转链接-->
                <div class="cd-customization">

                    <?php if ($order->url):?>
                    <!-- <form action="<?=$order->url?>" method="get"> -->
                        <button onclick="javascript:location.href='<?=$order->url?>'" type="submit" class="go-use">立即使用</button>
                    <!-- </form> -->
                    <?php endif?>

                </div> <!-- .cd-customization -->

        </li>

<?php endforeach?>

</ul>

</body></html>
