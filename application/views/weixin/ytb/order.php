<?php
// print_r($order);
$score = ORM::factory('ytb_score')->where('qid', '=', $user2['id'])->where('tid', '=', $order->id)->find();
if (!$score->id) die('订单无效');
// print_r($score->as_array());


//订单状态
$status['WAIT_SELLER_SEND_GOODS'] = '等待卖家发货';
$status['WAIT_BUYER_CONFIRM_GOODS'] = '已发货';
$status['TRADE_BUYER_SIGNED'] = '已签收';
$status['TRADE_CLOSED'] = '已退款';

$css['WAIT_SELLER_SEND_GOODS'] = 'c-orange';
$css['TRADE_CLOSED'] = 'c-gray-dark';
$css['TRADE_BUYER_SIGNED'] = 'c-green';
?>

<ul class="block block-list c-gray-dark">

    <li class="block-item">奖励<span class="pull-right font-size-18 c-green"><?=$score->score?><?=$config['score_name']?></span></li>
    <li class="block-item">
        <p class="line-height-30 font-size-14">商品名称<span class="pull-right"><?=$order->title?></span></p>
        <p class="line-height-30 font-size-14">订&nbsp;&nbsp;单&nbsp;&nbsp;号<span class="pull-right"><?=$order->tid?></span></p>
        <p class="line-height-30 font-size-14">订单状态<span class="pull-right"><?=$status[$order->status]?></span></p>
        <p class="line-height-30 font-size-14">下单时间<span class="pull-right"><?=date('y-m-d H:i:s',$order->pay_time)?></span></p>
        <!-- <p class="line-height-30 font-size-14">交易方式<span class="pull-right">微信安全支付－自有</span></p> -->
        <p class="line-height-30 font-size-14">订单金额<span class="pull-right">￥<?=$order->payment?></span></p>
        <p class="line-height-30 font-size-14">积分比例<span class="pull-right"><?=ceil(($score->score/$order->payment)*100)?>%</span></p>
        <p class="line-height-30 font-size-14">买&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;家<span class="pull-right"><?=$order->qrcode->nickname?></span></p>
    </li>
</ul>
