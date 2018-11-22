    <div class="block head-info">
        <p class="rmb_logo"></p>
        <p class="useful-money font-size-14">可提现金额（只能提现整数金额）</p>
        <p class="profit-sum font-size-28">￥<span><?=number_format($result['money_now'], 2)?></span></p>
    </div>

    <div class="action-container">
        <?php
        //满足提现最小金额 & 有购买过
        if($result['money_flag']):
            $now = time();
            $rand = rand(99999999,9999999999);
            $cksum = md5($openid.$config['appsecret'].$now.$rand);
        ?>
            <a href="/qwtdka/money/1/<?=$cksum?>?time=<?=$now?>&amp;rand=<?=$rand?>" class="btn btn-block btn-green disabled2">我要提现</a>
        <?php else:?>
            <a class="btn btn-block btn-green disabled">提现</a>
        <?php endif?>

        <p style="color:red" class="font-size-14"><?=$result['money_out_msg']?></p>
    </div>

    <div class="block block-list">
        <a href="/qwtdka/ddscore/2" class="block-item clearfix arrow"><p class="pull-left">已结算金额</p><span>￥<?=number_format($result['money_paid'], 2)?></span></a>
        <a href="/qwtdka/ddscore/1" class="block-item clearfix arrow"><p class="pull-left">待结算金额</p><span>￥<?=number_format($result['money_nopaid'], 2)?></span></a>
    </div>

    <div class="block block-list">
        <a href="/qwtdka/ddscore" class="block-item clearfix arrow"><p class="pull-left">收支明细</p></a>
        <a href="/qwtdka/ddscore/3" class="block-item clearfix arrow"><p class="pull-left">提现记录</p></a>
    </div>


<?php if($result['alert']):?>
    <script>
        alert('<?=$result['alert']?>');
        <?php if($result['ok']):?>location.href="/qwtdka/ddscore/3";<?php endif?>
    </script>
<?php endif?>
