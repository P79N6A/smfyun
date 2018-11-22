<?php
$uri = Request::instance()->current()->uri();
if ($uri == 'mdt/miaosha/1')
    $li2 = 'current';
else
    $li1 = 'current';
?>

<div class="m_baoliao w">

    <div class="ui-tab">
        <ul class="ui-tab-nav">
        <li class="<?=$li1?>"><a href="/mdt/miaosha">进行中</a></li>
        <li class="<?=$li2?>"><a href="/mdt/miaosha/1">已结束</a></li>
      </ul>
    </div>

<div class="baoliao_list">

<?php foreach($items as $item):?>
<a href="/mdt/click/<?=$item->id?>?<?=time()?>">
<div class="baoliao_content">
    <div class="bl_img"><img src="http://cdn.jfb.smfyun.com/mdt/images/item/<?=$item->id?>.v<?=$item->lastupdate?>.jpg" alt="<?=$item->name?>"></div>

    <div class="bl_right">
        <div class="bl_title"><?=$item->name?></div>

        <div class="bl_tag">
          <div class="bl_left">
                <div class="bl_price">￥<?=$item->price?></div>
                <div class="bl_oprice">原价￥<?=$item->price2?></div>
            </div>

            <?php if(!$li2):?>
            <div class="bl_mall large orange awesome">马上抢</div>
            <div class="colockbox" id="colockbox<?=$item->id?>"> <span class="day">00</span> <span class="hour">00</span> <span class="minute">00</span> <span class="second">00</span></div>
            <?php endif?>

        </div>
    </div>

</div>
</a>
<script>$(function(){countDown("<?=date('Y/m/d H:i:s', $item->endtime)?>","#colockbox<?=$item->id?>");});</script>
<?php endforeach?>


</div>
<!-- <div class="bl_more"><a href="#">加载更多</a></div> -->
</div>
