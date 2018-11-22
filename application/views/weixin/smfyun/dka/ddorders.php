<div class="js-result-list">
  <div class="js-list">

    <?php
    if (count($trades) == 0) {
    ?>

    <div class="js-list">
        <a class="block-item">
            <p class="line-height-30">
                <div style="text-align:center">没有记录</div>
            </p>
        </a>
    </div>

    <?php
    }

    //订单状态
    $status['WAIT_SELLER_SEND_GOODS'] = '等待卖家发货';
    $status['WAIT_BUYER_CONFIRM_GOODS'] = '已发货';
    $status['TRADE_BUYER_SIGNED'] = '订单完成';
    $status['TRADE_CLOSED'] = '已退款';
    $status['TRADE_CLOSED_BY_USER'] = '已取消';
    $status['WAIT_BUYER_PAY'] = '待付款';

    $css['TRADE_CLOSED'] = 'c-gray-dark';
    $css['TRADE_CLOSED_BY_USER'] = 'c-gray-dark';
    $css['TRADE_BUYER_SIGNED'] = 'c-green';
    $css['WAIT_BUYER_PAY'] = 'c-gray-dark';

    foreach ($trades as $score):
        $trade = $score->trade;
        if (!$trade->tid) {
            $score->delete();
            continue;
        }

        // echo $trade->status;
    ?>

    <div class="block block-order animated js-order-item">

        <div class="header">
         <span class="font-size-12"><?=$trade->tid?></span><span class="c-orange pull-right <?=$css[$trade->status]?>"><?=$status[$trade->status]?></span>
        </div>

        <hr class="margin-0 left-10">

        <div class="block block-list border-top-0 border-bottom-0 score_list font-size-12">
            <div class="block-item border-none name-card name-card-3col score-name-card clearfix">
                <a href="/qwtdka/ddorder/<?=$trade->id?>" class="thumb"><img src="<?=$trade->pic_thumb_path?>"></a>
                <div class="detail"><a href="/qwtdka/ddorder/<?=$trade->id?>"><h3 class="ellipsis"><?=$trade->title?></h3></a></div>

                <div class="right-col">
                    <div class="order-state">￥<?=$trade->total_fee?></div>
                    <div class="order-state c-gray-dark">x<?=$trade->num?></div>
                </div>
            </div>
        </div>

        <div class="block block-list border-top-0 border-bottom-0" style="padding: 0 10px;margin: 10px 0;">
            <div class="pull-left font-size-12">佣金：<span class="c-gray-dark">(订单总价:￥<?=$trade->money?>)</span></div>
            <div class="pull-right font-size-12"><span class="c-orange">￥<?=$score->cash?></span></div>
        </div>

        <hr class="margin-0 left-10">

        <div class="bottom">
            <div class="pull-left font-size-12"><span class="c-gray-dark"><?=$trade->pay_time?></span></div>
            <div class="pull-right font-size-12"><span class="c-gray-dark">买家：<?=$trade->qrcode->nickname?></span></div>
        </div>
    </div>

    <?php endforeach;?>

  </div>
</div>
