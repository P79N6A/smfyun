<style>
.left2 {
    overflow: hidden;
    width: 100%;
}
</style>

            <div class="header">
                <span class="txt" style='font-size:18px;background:#fff'><a>兑换明细</a></span>
            </div>
<ul class="cd-gallery">

<?php foreach ($orders as $order):?>

        <li>
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">
                    <li class="selected"><img src="<?=$config['cdn']?>/dkl/images/item/<?=$order->item->id?>.v<?=$order->item->lastupdate?>.jpg" alt="<?=$order->item->name?>"></li>
                </ul>
            </div> <!-- .cd-single-item -->

                <div class="order-info">
                    <ul>
                    <li class="left2"><b>奖品名称：</b><?=$order->item->name?></li>

                    <li class="left"><b>奖品价格：</b><?=$order->item->price?>元</li>
                    <li class="right"><b>消费<?=$config['scorename']?>：</b><?=$order->score?><?=$config['scorename']?></li>
                    <li class="left"><b>兑换时间：</b><?=date('Y-m-d H:i', $order->createdtime)?></li>
                    <li class="right"><b>订单状态：</b><?=$order->status ? '已核销' : '未核销'?></li>

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
