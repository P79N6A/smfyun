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
function auth($auth,$config){
    $content = '仅限';
    $len = strlen($auth);

    $content = strpos($auth,'a')===false?$content.'':$content.$config['pool_name'].',';
    $content = strpos($auth,'b')===false?$content.'':$content.$config['third'].',';
    $content = strpos($auth,'c')===false?$content.'':$content.$config['second'].',';
    $content = strpos($auth,'d')===false?$content.'':$content.$config['first'];

    $content = $content.'兑换哦';
    if($len==0) $content = '';
    return $content;
}
$hasbuy = ORM::factory('ytb_trade')->where('qid','=',$userobj->id)->where('status','=','TRADE_BUYER_SIGNED')->count_all();
?>
<!doctype html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1.0 user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <title>兑换商城</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/ui.css">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/style.css">
    <link rel="stylesheet" type="text/css" href="../ytb/prize/css/didi.css">
    <link rel="stylesheet" type="text/css" href="../ytb/prize/css/index.css">
    <link rel="stylesheet" type="text/css" href="../ytb/prize/css/swiper.css">
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
              <a href='#'>
                <p class="desc r_line">
                  可用<?=$config['score_name']?>
                </p>
                <p class="number">
                  <?=number_format($result['score'],2)?>
                </p>
                </a>
              </li>
              <li class="overview_item" style="line-height: 60px;">
              <a href='/ytb/shorders'>
                <p class="desc r_line">
                  已兑换奖品
                </p>
                <!-- <p class="number">
                  0
                </p> -->
              </a>
              </li>
              <li class="overview_item" >
              <a href='/ytb/home'>
                <p class="desc r_line"style="top: 20px;">
                  我的<?=$config['score_name']?>
                </p>
                <!-- <p class="number">
                  0
                </p> -->
              </a>
              </li>
            </ul>
          </div>
        <?php if($hasbuy==0):?>
          <div style="position: relative;bottom: 20px;font-size: 13px;color: red;">请先购买并完成一笔订单哦</div>
        <?php endif?>
        </div>
      </div>
</div>
    <div class="view js-page-view">
        <section id="mods">
         <div class="mod" style="background:#eff0f4">
            <!-- <div class="header">
                <span class="txt" style='font-size:18px;'><a href="/ytb/shorders">查看已兑换奖品</a></span>
            </div> -->
            <div class="bd clearfix">
            <?php foreach ($items as $item):?>
              <?php if(!$item->endtime || strtotime($item->endtime) >= time()):?>
                <div class="goods js-click">
                <a href="/ytb/item/<?=$item->id?>">
                    <div class="media" style="padding-top:0px">
                        <img style="width:100%,height:100%" src="http://<?=$_SERVER['HTTP_HOST']?>/ytb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>0.jpg" alt="<?$item->name?>">
                    </div>
                    <div class="info">
                        <!-- <span class="type">
                            <?=convert($item->type)?>
                        </span> -->
                        <span class="name">
                            <?=$item->name?>
                        </span>
                        <span class="price">
                            <em><?=$item->score?></em><?=$config['score_name']?>
                        </span>
                        <span class="price">
                            <em><?=auth($item->auth,$config)?></em>
                        </span>
                    </div>
                    </a>
                </div>
              <?php endif?>
            <?php endforeach?>
            </div>
        </div>
    </section>
    </div>
</body></html>
