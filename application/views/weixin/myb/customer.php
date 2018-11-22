<!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="/wdy/bootstrap/css/bootstrap.min.css">


<?php
$ranktitle = "共 ".$totlenum."位客户";
if($newadd=='month')
$ranktitle = "本月新增 ".$totlenum."位客户";
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
?>

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
        padding: 30px 15px;
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

    .cur section{color: #fff;
    }
     a {text-decoration:none}
     a:link,a:visited,a:active,a:hover {text-decoration:none}
</style>

<div class="block" >
    <div class="name-card name-card-directseller clearfix" style="padding:20px">
        <a class="thumb"><img style="background-image:url('<?=$user2['headimgurl']?>')"></a>
        <div class="detail">
            <p class="font-size-16" style="color:#000"><?=$user2['nickname']?></p>
            <p class="font-size-14"><?=$fuser?><?=$user2['id2']?> 号<?=$name?></p>
        </div>
    </div>
</div>

<div class="block" >
   <?//if($newadd!='month'):?>
    <a href="/fxb/customer/month">
            <div class="ui border-bottom overview" style="width:50%;float: left;">
                <div class="item" style="border-right: 1px solid #ccc;">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['follows_month']?></span>
                    </div>
                    <div class="label ellipsis">本月新增客户</div>
                </div>
            </div>
        </a>
      <?//else:?>
        <a href="/fxb/customer">
            <div class="ui border-bottom overview">
                <div class="item">
                    <div class="value ellipsis">
                        <span class="font-size-28 c-green"><?=(int)$user2['follows']?></span>
                    </div>
                    <div class="label ellipsis">累计客户</div>
                </div>
            </div>
        </a>
      <?//endif;?>
       <!-- <a href="/fxb/orders">
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
                    <div style="text-align:center">没有记录</div>
                </p>
            </a>
        </div>
        <br><br><br><br><br><br><br><br><br><br>
        <?else:?>
        <section id="ranking_list">
        <section class="box block-item">

                  <section class="col_1 cola">排名</section>
                  <!-- <section class="col_2"><img src="<?=$user->headimgurl?>" /></section> -->
                  <section class="col_1 cola">类型</section>
                  <section class="col_1 cola">昵称</section>

                  <section class="col_1 cola">销售额</section>
                  <section class="col_1 cola">下线</section>
                  <section class="col_1 cola">二级</section>
                  <?php if($config['kaiguan_needpay']==1):?>
                    <section class="col_1 cola">三级</section>
                  <?endif?>
        </section>
        <?php
        foreach ($mycustomers as $user):
        $rank++;
        $ischild=ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$user->openid)->find();

        if($ischild->fopenid==$openid)
        {
          $kind='下线';
        }
        else
        {
           $ischildd=ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$ischild->fopenid)->find();
           if($ischildd->fopenid==$openid)
            $kind='二级';
           else
            $kind='三级';

        }


        $count2 = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', '=', $user->openid)->count_all();

        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM fxb_qrcodes WHERE fopenid='$user->openid' and bid='$bid'")->execute()->as_array();

         $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
         $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
         for($i=0;$firstchild[$i];$i++)
          {
            $tempid[$i]=$firstchild[$i]['openid'];
          }

         if($config['kaiguan_needpay']==1)
         {
              $tempdata = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid)->find_all()->as_array();
              for($i=0;$tempdata[$i];$i++)
              {
                    $sort=$tempdata[$i]->as_array();
                    $tempiid[$i]=$sort['openid'];
              }
          }

        $count4 = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempiid)->count_all();
        $count3 = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid)->count_all();
        ?>
                <section class="box block-item">

                  <section class="col_1"><?=$rank+($pagenum-1)*500?></section>
                  <!-- <section class="col_2"><img src="<?=$user->headimgurl?>" /></section> -->
                  <!-- <section class="col_1"><?//=($ischild)?'下线':'二级'?></section> -->
                  <section class="col_1"><?=$kind?></section>
                  <section class="col_1"><?=$user->nickname?></section>

                  <section class="col_1">&yen;<?=$user->paid?></section>
                  <section class="col_1"><?=$count2?></section>
                  <section class="col_1"><?=$count3?></section>
                  <?php if($config['kaiguan_needpay']==1):?>
                    <section class="col_1"><?=$count4?></section>
                  <?endif?>
                </section>
        <?php endforeach?>

        </section>
        <? endif;?>
    </section>
</div>
 <div class="box-footer clearfix">
                <?=$page?>
  </div>
