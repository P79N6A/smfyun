<?php
function convert($type){
    switch ($type) {
        case 0:
        echo '实物奖品';
            break;
        case 1:
        echo '微信卡券';
            break;
        case 2:
        echo '虚拟奖品';
            break;
        case 3:
        echo '话费流量';
            break;
        case 4:
        echo '微信红包';
            break;
        case 5:
        echo '有赞优惠券';
            break;
        case 6:
        echo '有赞赠品';
            break;
        default:
            # code...
            break;
    }
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1.0 user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="full-screen" content="yes">
    <meta name="x5-full-screen" content="true">
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <title>商品详情</title>

    <link rel="stylesheet" type="text/css" href="http://<?=$_SERVER['HTTP_HOST']?>/qwt/css/detial.css">


</head>

  <body class="page-detail" data-page="detail" ontouchstart="">
    <div class="view js-page-view" id="page-view-detail">
        <div class="detail-header" id="detail-header">

              <div style="width: auto;"> <img src="<?=$config['cdn']?>/qwtwfb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>.jpg" alt="<?=$item->name?>">

          </div>

           <div class="goods-info">
            <!-- <span class="type"><?=convert($item->type)?></span> -->
            <span class="name"><?=$item->name?> / 价值￥<?=$item->price?></span>
             </div>
           </div>
           <div id="chose-price" class="chose-price" style="display:none">  <div class="price-row js-price-select state-selected" data-type="0">
            <span class="check mall-radio checked">
            </span>
          </div>
           </div>
           <div id="pay-bar" class="pay-bar">
            <span class="pay-total js-total-price">
             <b class="coins"><?=$item->score?><?=$scorename?><?=$item->need_money>0?'+'.($item->need_money/100).'元':''?></b>（剩余：<?=$item->stock?>件）
           </span>
           <?php if ($dlimit==1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled" style="width:35%">已经达到兑换上限</a>
                <?php elseif($item->stock <= 0):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled" style="width:35%">已换完</a>
                <?php elseif ($user2['score'] < $item->score):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled" style="width:35%">您的<?=$scorename?>不够</a>
                <?php elseif ($item->endtime && strtotime($item->endtime) < time()):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled" style="width:35%">已截止</a>
                <?php elseif ($item->limit > 0 && $limit >= $item->limit):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled" style="width:35%">您已经兑换过该奖品</a>
                <?php elseif ($user2['lock'] == 1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled" style="width:35%">您的账号已被锁定</a>
                <?php else:?>
                <a href="/qwtwfb/neworder/<?=$item->id?>" class="pay-btn" style="width:35%">立即兑换</a>
                <?php endif?>

            </div>
             <div class="desc-main" id="desc-main">
              <div class="rich-txt">
                <div><b><u>商品简介：</u></b>
     </div>
                <div><?=$item->desc?>
        <br><br>
        </div>
                <!-- <div><b><u>使用范围：</u></b></div><div>全国</div> -->
                <!-- <div class="important-tip"> <span class="hd">重要说明</span> <div class="bd"> 商品兑换流程请仔细参照商品详情页的“兑换流程”、“注意事项”与“使用时间”，除商品本身不能正常兑换外，商品一经兑换，一律不退还。（如商品过期、兑换流程操作失误、仅限新用户兑换） </div> </div> --> </div>




</body>
