<style>
.top {
    text-align: center;
    font-size: 18px;
    margin: 10px;
}

button.disabled {
    background-color: #999 !important;
    border:1px solid #999 !important;
}
</style>

<div class="top">
    <a class="btn btn-danger" href="/wfb/orders">查看我已经兑换的产品</a>
</div>

<!-- TableList -->
<ul class="cd-gallery">


<?php
// var_dump($config);
if(is_array($items)){

}else{
    echo '<center><div>请先添加奖品</div><center>';
    exit;
}
foreach ($items as $item):
    $item = (object)$item;
    //判断限购
    $limit = ORM::factory('wfb_order')->where('qid', '=', $user2['id'])->where('iid', '=', $item->id)->count_all();
?>

        <li>
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">
                    <li class="selected"><img src="http://cdn.jfb.smfyun.com/wfb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>0.jpg" alt="<?=$item->name?>"></li>
                </ul>
            </div> <!-- .cd-single-item -->
            <div class="cd-item-info">
                <b><a href="/wfb/neworder/<?=$item->id?>"><?=$item->name?></a> (还有<?=$item->stock?>件)</b>
                <em>价值 <?=$item->price?> 元 / <?=$item->score?> <?=$config['score2']?></em>
            </div> <!-- cd-item-info -->

            <?php if ($item->desc):?>
                <div class="product-info"><?=$item->desc?></div>
            <?php endif?>

            <div class="cd-customization">

                <?php if ($dlimit==1):?>
                    <button type="submit" class="add-to-cart disabled" disabled="">已达到兑换上限</button>
                <?php elseif($item->stock <= 0):?>
                    <button type="submit" class="add-to-cart disabled" disabled="">已换完</button>
                <?php elseif ($user2['score'] < $item->score):?>
                    <button type="submit" class="add-to-cart disabled" disabled="">您的<?=$config['score']?>不够</button>
                <?php elseif ($item->endtime && strtotime($item->endtime) < time()):?>
                    <button type="submit" class="add-to-cart disabled" disabled="">已截止</button>
                <?php elseif ($item->limit > 0 && $limit >= $item->limit):?>
                    <button type="submit" class="add-to-cart disabled" disabled="">您已经兑换过该产品</button>
                <?php elseif ($user2['lock'] == 1):?>
                    <button type="submit" class="add-to-cart disabled" disabled="">您的账号已被锁定</button>
                <?php else:?>
                    <form action="/wfb/neworder/<?=$item->id?>"><button type="submit" class="add-to-cart">立即兑换</button></form>
                <?php endif?>

            </div> <!-- .cd-customization -->
        </li>
<?php endforeach?>

</ul>
