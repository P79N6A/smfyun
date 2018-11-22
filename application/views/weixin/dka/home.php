<?php
//获取推荐用户
if ($user2['fopenid']) $fuser[] = ORM::factory('dka_qrcode')->where('openid', '=', $user2['fopenid'])->find()->nickname . ' 推荐';
//是否火种用户
if ($user2['id2']) $fuser[] = "{$user2['id2']} 号{$config['title1']}";

if ($fuser) $fuser = join(' / ', $fuser);
?>

    <div class="block" >
        <div class="name-card name-card-directseller clearfix" style="padding:20px">
            <a class="thumb"><img style="background-image:url('<?=$user2['headimgurl']?>')"></a>
            <div class="detail">
                <p class="font-size-16" style="color:#000"><?=$user2['nickname']?></p>
                <p class="font-size-14"><?=$fuser?></p>
            </div>
        </div>
    </div>

    <div>
        <a href="/dka/money">
            <div class="settlement-center">
                <span class="title font-size-14">总收益（元）</span>
                <p class="profit-sum"><span><?=number_format($result['money'], 2)?></span></p>
                <p class="useful-money font-size-14">（包含未结算收益）</p>
            </div>
        </a>
    </div>

    <div class="block" >
        <a href="/dka/ddorders">
            <div class="ui two border-bottom overview">
                <div class="item">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['trades']?></span>
                        <span class="corner">笔</span>
                    </div>
                    <div class="label ellipsis">推广订单</div>
                </div>
                <div class="item">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=number_format($result['paid'], 2)?></span>
                        <span class="corner">元</span>
                    </div>
                    <div class="label ellipsis">累计付款金额</div>
                </div>
            </div>
        </a>

        <a style="cursor: default;" href="/dka/ddtop">
            <div class="ui two overview">
                <div class="item">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$result['follows_month']?></span>
                    </div>
                    <div class="label ellipsis">本月新增客户</div>
                </div>
                <div class="item">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['follows']?></span>
                    </div>
                    <div class="label ellipsis">累计客户</div>
                </div>
            </div>
        </a>
    </div>


    <div class="block block-list block-list-actions" style="margin: 20px 0 -1px 0;">
        <a class="block-item clearfix arrow" href="/dka/ddscore"><p class="c-black font-size-14">收支明细</p></a>
        <a class="block-item clearfix arrow" href="/dka/ddtop"><p class="c-black font-size-14">业绩排行</p></a>
        <?php if($config['dka_url']):?><a class="block-item clearfix arrow" href="<?=$config['dka_url']?>"><p class="c-black font-size-14">常见问题解答</p></a><?php endif?>
    </div>


    <div class="block" style="padding:20px;line-height:150%">
        <h1 class="font-size-18 c-green">提现说明</h1>
        <?=nl2br($config['dka_money_desc'])?>
    </div>

    <div class="action-container" style="margin:20px 0"><a href="/dka/money" class="js-spread btn btn-block btn-green">我要提现</a></div>
