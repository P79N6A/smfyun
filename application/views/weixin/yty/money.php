<?php
$lv_name=ORM::factory('yty_qrcode')->where('id','=',$user2['id'])->find()->agent->skus->name;
?>
    <link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
    <style type="text/css">
        #ranking_title{
            display: block;
            background: #2CCE2A;
            width: 20%;
            color: white;
            /* padding: 10px; */
            border-radius: 10px;
            margin-left: 40%;
            padding-bottom: 10px;
            padding-top: 10px;
            margin-top: 10px;
        }
        #nranking_title{
            display: block;
            background: #f2f2f2;
            width: 20%;
            color: white;
            /* padding: 10px; */
            border-radius: 10px;
            margin: auto;
            padding-bottom: 10px;
            padding: 10px 10px;
            margin-top: 10px;
        }
        .mco{
            color:red;
        }
    </style>
    <div class="block"  style="margin-top: 0px;">
        <div class="name-card name-card-directseller clearfix" style="padding:20px">
            <a class="thumb"><img src="<?=$user2['headimgurl']?>"></a>
            <div class="detail">
                <p class="font-size-16" style="color:#000"><?=$user2['nickname']?></p>
                <p class="font-size-14"><?=$lv_name?></p>
                 <p class="font-size-14">累计已提现：<?=$result['money_paid']?$result['money_paid']:0?>元</p>
            </div>
        </div>
    </div>
    <div class="block" >
        <div class="ui two border-bottom overview">
            <div class="item" >
                <div class="value ellipsis">
                <span class="font-size-10 c-black">可提现:<span class='mco'><?=number_format($result['money_now'],2)?>元</span></span>
                <?php
                if($result['money_flag']):
                $now = time();
                $rand = rand(99999999,9999999999);
                $cksum = md5($openid.$config['appsecret'].$now.$rand);
                ?>
                 <span id="ranking_title"> <a style="color:white;" href="/yty/money/1/<?=$cksum?>?time=<?=$now?>&amp;rand=<?=$rand?>">提现</span>
                <?php else:?>
                <span id="nranking_title"> <a style="color:;">提现</span>
                <?php endif;?>
            </div>
            </div>
            <div class="item">
                
                <div class="value">
                    <span class="font-size-10 c-black">待结算:<span class='mco'><a href="/yty/score/1"><?=number_format($result['money_nopaid'], 2)?></a></span>元</span>
                </div> 
                
            </div>
        </div>
</div>
    <div class="block block-list">
        <a href="/yty/score" class="block-item clearfix arrow"><p  class="pull-left">收支明细</p></a>
        <a href="/yty/score/3" class="block-item clearfix arrow"><p class="pull-left">提现记录</p></a>
    </div>
    <br><br><br><br><br><br><br>
<?php if($result['alert']):?>
    <script>
        alert('<?=$result['alert']?>');
        <?php if($result['ok']):?>location.href="/yty/score/3";<?php endif?>
    </script>
<?php endif?>
    <footer class="page-footer fixed-footer">
        <ul style="height: 0px;">
            <li>
                <a href="/yty/home">
                    <img src="/yty/images/footer002.png"/>
                    <p style="font-size: 14px;line-height:14px;">个人中心</p>
                </a>
            </li>

            <li >
                <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
                    <img src="/yty/images/footer004.png"/>
                    <p style="font-size: 14px;line-height:14px;">推荐商品</p>
                </a>
            </li>
        </ul>
    </footer>
