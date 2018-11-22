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
    
    <link rel="stylesheet" type="text/css" href="../dist/css/detial.css">


</head>

  <body class="page-detail" data-page="detail" ontouchstart="">
    <div class="view js-page-view" id="page-view-detail">
        <div class="detail-header" id="detail-header">

              <div style="width: 320px;"> <img src="http://cdn.jfb.smfyun.com/dyb/images/item/<?=$xqitem->id?>.v<?=$xqitem->lastupdate?>0.jpg" alt="<?=$xqitem->name?>">

          </div>

           <div class="goods-info">
            <span class="type">手气大比拼</span>
            <span class="name"><?=$xqitem->name?></span>
             </div>
           </div>
           <div id="chose-price" class="chose-price" style="display:none">  <div class="price-row js-price-select state-selected" data-type="0">
            <b class="coins">300</b><?=$scorename?>
            <span class="check mall-radio checked">
            </span>
          </div>
           </div>
           <div id="pay-bar" class="pay-bar"> 单价：
            <span class="pay-total js-total-price">
             <b class="coins"><?=$xqitem->score?></b><?=$scorename?>
           </span>
           <?php if ($dlimit==1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">已经达到兑换上限</a>
                <?php elseif($xqitem->stock <= 0):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">已换完</a>
                <?php elseif ($user2['score'] < $xqitem->score):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">您的<?=$scorename?>不够</a>
                <?php elseif ($xqitem->endtime && strtotime($xqitem->endtime) < time()):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">已截止</a>
                <?php elseif ($xqitem->limit > 0 && $limit >= $xqitem->limit):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">您已经兑换过该奖品</a>
                <?php elseif ($user2['lock'] == 1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">您的账号已被锁定</a>
                <?php else:?>
                <a href="/dyb/neworder/<?=$xqitem->id?>" class="pay-btn">立即兑换</a>
                <?php endif?>

            </div>
             <div class="desc-main" id="desc-main">
              <div class="rich-txt">
                <div><b><u>商品简介：</u></b>
     </div>
                <div><?=$xqitem->desc?>
        <br><br>
        </div>
                <!-- <div><b><u>使用范围：</u></b></div><div>全国</div> -->
                <!-- <div class="important-tip"> <span class="hd">重要说明</span> <div class="bd"> 商品兑换流程请仔细参照商品详情页的“兑换流程”、“注意事项”与“使用时间”，除商品本身不能正常兑换外，商品一经兑换，一律不退还。（如商品过期、兑换流程操作失误、仅限新用户兑换） </div> </div> --> </div>




</body>
