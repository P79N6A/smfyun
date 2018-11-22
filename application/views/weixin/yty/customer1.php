<!-- Bootstrap 3.3.5 -->
    <!-- <link rel="stylesheet" href="/wdy/bootstrap/css/bootstrap.min.css"> -->


<?php
$ranktitle = "共 ".$totlenum."位经销商";
if($newadd=='month')
$ranktitle = "本月新增 ".$totlenum."位经销商";
$name = $config['title1'];
if($user2['sid']){
  $name = ORM::factory('yty_sku')->where('bid','=',$bid)->where('id','=',$user2['sid'])->find()->name;
}
// $id = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('lv','=',1)->where('id','<=',$user2['id'])->find_all()->count();
$id =$user2['fid'];
?>

    <link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>

<style>
    #rankpage{
      position: relative;
      width: 100%;
      /*max-width: 1170px;*/
      font-sirze: 15px;
      margin-top:40px;
        }
    #ranking {
        /*width: 90%;*/
        left: 5%;
        top: 10%;
        background-color: #fff;
        border-radius: 5px;
        /*padding: 30px 15px;*/
    }
    #ranking_list {
        display: block;
        width: 100%;
        /*height: 100%;*/
        overflow: hidden;
        overflow-y:auto;
    }

    #ranking_list::-webkit-scrollbar-track {
     -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
     background-color: #F5F5F5;
     border-radius: 10px;
    }
     #ranking_list::-webkit-scrollbar {
     width: 5px;
     background-color: #F5F5F5;
    }
     #ranking_list::-webkit-scrollbar-thumb {
     border-radius: 10px;
     background-image: -webkit-gradient(linear,  left bottom,  left top,  color-stop(0.44, rgb(122,153,217)),  color-stop(0.72, rgb(73,125,189)),  color-stop(0.86, rgb(28,58,148)));
    }

    #ranking_title, #play_game, #return_back {
        display: block;
        height: 40px;
        line-height: 40px;
        text-align: center;
        color: #fff;
        font-size: 18px;
        border-radius: 4px;
        position: absolute;
    }

    #ranking_title {
        width: 50%;
        left: 25%;
        top: -21px;
        background-color: #06bf04;
    }

    .box {
        width: 100%;
        /*background-color: #eee;*/
        height: 50px;
        line-height: 50px;
        display: -webkit-box;
        display: -moz-box;
        display: box;
        /*border-radius: 10px;*/
        /*margin:5px auto 5px;*/
    }
    .cur{background-color: #dd5964;}
    .cola {
        color:#06bf04;
        background-repeat:no-repeat; background-position:center center;
    }
    .col_1{
        -webkit-box-flex: 1;
        -moz-box-flex: 1;
        box-flex: 1;
        padding: 5px 0;
        width: 40px;
        text-align:center;
    }
    .col_4 {
        width:20%;
        text-align:center;
        color:#06bf04;
        font-size:14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        /*overflow: hidden;*/
    }
    .cur section{color: #fff;
    }
     a {text-decoration:none}
     a:link,a:visited,a:active,a:hover {text-decoration:none}
</style>
<?php
$lv_name=ORM::factory('yty_qrcode')->where('id','=',$user2['id'])->find()->agent->skus->name;
?>
<div class="block" style="margin-top:0px;">
    <div class="name-card name-card-directseller clearfix" style="padding:20px">
        <a class="thumb"><img src="<?=$user2['headimgurl']?>"></a>
        <div class="detail">
            <p class="font-size-16" style="color:#000"><?=$user2['nickname']?></p>
            <p class="font-size-14"><?=$lv_name?></p>
        </div>
    </div>
</div>

<div class="block" >
   <?//if($newadd!='month'):?>
    <a href="/yty/customer1/month">
            <div class="ui border-bottom overview" style="width:50%;float: left;">
                <div class="item" style="border-right: 1px solid #ccc;">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['follows_month']?></span>
                    </div>
                    <div class="label ellipsis">本月新增经销商</div>
                </div>
            </div>
        </a>
      <?//else:?>
        <a href="/yty/customer1">
            <div class="ui border-bottom overview">
                <div class="item">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['follows']?></span>
                    </div>
                    <div class="label ellipsis">累计经销商</div>
                </div>
            </div>
        </a>
      <?//endif;?>
       <!-- <a href="/yty/orders">
            <div class="ui border-bottom overview">
                <div class="item"  style="border-right: 1px solid #ccc;">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['trades']?></span>
                        <span class="corner">笔</span>
                    </div>
                    <div class="label ellipsis">推广订单</div>
                </div>
            </div>
        </a> -->
</div>

<div id="rankpage">
    <section id="ranking">
        <span id="ranking_title"><?=$ranktitle?></span>

        <?php
        if (count($mycustomers) == 0):
        ?>
        <div class="js-list">
            <a class="block-item">
                <p class="line-height-30">
                    <div style="text-align:center;margin-top:30px;">没有记录</div>
                </p>
            </a>
        </div>
        <br><br><br><br><br><br><br><br><br><br>
        <?else:?>
        <section id="ranking_list">
        <section class="box block-item">

                  <section class="col_4">排名</section>
                  <section class="col_4">头像</section>
                  <section class="col_4">昵称</section>
                  <section class="col_4">进货额</section>
                  <section class="col_4">总收益</section>
        </section>
        <?php
        foreach ($mycustomers as $user):
        $rank++;
        $count1 = $user->trades->select(array('SUM("payment")', 'payment'))->where_open()->or_where('status', '=', 'WAIT_SELLER_SEND_GOODS')->or_where('status', '=', 'WAIT_BUYER_CONFIRM_GOODS')->or_where('status', '=', 'TRADE_BUYER_SIGNED')->where_close()->find()->payment;
        $count2 = $user->trades->select(array('SUM("money1")', 'money1'))->where_open()->or_where('status', '=', 'WAIT_SELLER_SEND_GOODS')->or_where('status', '=', 'WAIT_BUYER_CONFIRM_GOODS')->or_where('status', '=', 'TRADE_BUYER_SIGNED')->where_close()->find()->money1;
        ?>
                <section class="box block-item">

                  <section class="col_4"><?=$rank+($pagenum-1)*500?></section>
                  <section class="col_4"><img style="width:32px"src="<?=$user->headimgurl?>" /></section>
                  <section class="col_4"><?=$user->nickname?></section>
                  <section class="col_4"><?=number_format($user->agent->stock,2)?></section>
                  <section class="col_4"><?=number_format($user->money,2)?></section>
                </section>
        <?php endforeach?>

        </section>
        <? endif;?>
    </section>
</div>
 <div class="box-footer clearfix">
                <?=$page?>
  </div>
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
