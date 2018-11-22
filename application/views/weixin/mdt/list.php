<!-- 首页 -->

<div class="m_baoliao w">
<div class="baoliao_title"><span>最新上架</span><em><a href="#"><img src="/mdt/images/iconfont-shuaxin.png"></a></em></div>
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

            <div class="bl_mall large orange awesome">马上抢</div>
            <div class="colockbox" id="colockbox<?=$item->id?>"> <span class="day">00</span> <span class="hour">00</span> <span class="minute">00</span> <span class="second">00</span></div>

        </div>
    </div>
</div>

</a>

<script>$(function(){countDown("<?=date('Y/m/d H:i:s', $item->endtime)?>","#colockbox<?=$item->id?>");});</script>
<?php endforeach?>


</div>
<!-- <div class="bl_more"><a href="#">加载更多</a></div> -->
</div>
