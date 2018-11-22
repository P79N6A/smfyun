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
    <title>兑换商城</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/ui.css">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/style.css">
    <link rel="stylesheet" type="text/css" href="../fxb/prize/css/didi.css">
    <link rel="stylesheet" type="text/css" href="../fxb/prize/css/index.css">
    <link rel="stylesheet" type="text/css" href="../fxb/prize/css/swiper.css">
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
                  <?=number_format($result['shscore'],0)?>
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
    <div class="view js-page-view">
        <section id="mods">
         <div class="mod" style="background:#eff0f4">
            <div class="header">
                <span class="txt" style='font-size:18px;'><a href="/fxb/shorders">查看已兑换奖品</a></span>
            </div>
            <div class="bd clearfix">
            <?php foreach ($items as $item):?>
                <div class="goods js-click">
                <a href="/fxb/item/<?=$item->id?>">
                    <div class="media" style="padding-top:0px">
                        <img style="width:100%,height:100%" src="http://<?=$_SERVER['HTTP_HOST']?>/fxb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>0.jpg" alt="<?$item->name?>">
                    </div>
                    <div class="info">
                        <!-- <span class="type">
                            <?=convert($item->type)?>
                        </span> -->
                        <span class="name">
                            <?=$item->name?>
                        </span>
                        <span class="price">
                            <em><?=$item->score?></em><?=$config['scorename']?>
                        </span>
                    </div>
                    </a>
                </div>
            <?php endforeach?>
            </div>
        </div>
    </section>
    </div>
</body></html>
