<?php
$name = $config['title1'];
if($user2['fopenid']){
  $fuser2 = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$user2['fopenid'])->find();
  $name = $config['title1'];
  if($fuser2->fopenid){
    $ffuser2 = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$fuser2->fopenid)->find();
    $name = $config['title2'];
    if($ffuser2->fopenid&&$config['kaiguan_needpay']==1){
      $fffuser2 = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffuser2->fopenid)->find();
      $name = $config['titlen3'];
    }
  }
}

//获取推荐用户
if ($user2['fopenid']) $fuser[] = ORM::factory('fxb_qrcode')->where('openid', '=', $user2['fopenid'])->find()->nickname . ' 推荐';
//是否火种用户
if ($user2['id2']) $fuser[] = "{$user2['id2']} 号{$name}";

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

<a href="/qwtfxb/money">
    <div class="settlement-center">
                <?php
                    if(empty($result['aaa'])){
                        $result['aaa']="元";
                    }
                ?>        
                <!-- <span class="title font-size-14"><?=$result['aaa'] ?>（元）</span> -->
                <span class="title font-size-14" style="color:#000;">总收益（<?=$result['aaa']?>）</span> 
                <p class="profit-sum"><span><?=number_format($result['money'], 2)?></span></p>
                <p class="useful-money font-size-14" style="color:#000;">（包含未结算收益）</p>
     
    </div>
  </a>
    <!-- 修改 -->
    <div class="block">
        <a href="/qwtfxb/orders">
            <div class="ui border-bottom overview" style="width:50%;float: left;">
                <div class="item"  style="border-right: 1px solid #ccc;">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['trades']?></span>
                        <span class="corner">笔</span>
                    </div>
                    <div class="label ellipsis">推广订单</div>
                </div>
            </div>
        </a>

        <a href="/qwtfxb/orders" style="">
            <div class="ui border-bottom overview" style="width:50%;float: right;">
                     <!-- <div class="ui border-bottom overview"> -->
                            <div class="item">
                                <div class="value ellipsis">
                                    <span class="font-size-28 c-green"><?=number_format($result['paid'], 2)?></span>
                                    <span class="corner">元</span>
                                </div>
                                <div class="label ellipsis">累计付款金额</div>
                            </div>
                        <!-- </div> -->
            </div>
        </a>


        <a href="/qwtfxb/customer/month">
            <div class="ui border-bottom overview" style="width:50%;float: left;">
                <div class="item" style="border-right: 1px solid #ccc;">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['follows_month']?></span>
                    </div>
                    <div class="label ellipsis">本月新增客户</div>
                </div>
            </div>
        </a>


        <a href="/qwtfxb/customer" style="">
            <div class="ui border-bottom overview" style="width:50%;float: right;">
                    <!-- <div class="ui border-bottom overview"> -->
                        <div class="item">
                            <div class="value ellipsis">
                                <span class="font-size-28 c-green"><?=(int)$user2['follows']?></span>
                            </div>
                            <div class="label ellipsis">累计客户</div>
                        </div>
                    <!-- </div> -->
            </div>
        </a>
    </div>
    <!-- 修改完 -->


    <div class="block block-list block-list-actions" style="margin: 20px 0 -1px 0;">
        <a class="block-item clearfix arrow" href="/qwtfxb/score"><p class="c-black font-size-14">收支明细</p></a>
        <a class="block-item clearfix arrow" href="/qwtfxb/top"><p class="c-black font-size-14">业绩排行</p></a>
        <?php if($config['fxb_url']):?><a class="block-item clearfix arrow" href="<?=$config['fxb_url']?>"><p class="c-black font-size-14">常见问题解答</p></a><?php endif?>
    </div>


    <div class="block" style="padding:20px;line-height:150%">
        <h1 class="font-size-18 c-green">提现说明</h1>
        <?=nl2br($config['fxb_money_desc'])?>
    </div>

    <div class="action-container" style="margin:20px 0"><a href="/qwtfxb/money" class="js-spread btn btn-block btn-green">我要提现</a></div>
