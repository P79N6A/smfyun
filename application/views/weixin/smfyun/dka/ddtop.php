<?php
$ranktitle = "第 {$result['rank']} 名";
?>

<style>
    #rankpage{
      position: relative;
      width: 100%;
      /*max-width: 1170px;*/
      font-size: 14px;
      font-size: 0.875rem;
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
    .col_1 {
        width: 50px;
        text-align: center;
        font-size:20px;
        color:#06bf04;
        background-repeat:no-repeat; background-position:center center; background-size:40px 40px;
    }

    .col_1[title="1"]{ color:#fff; background-image:url("/wdy/front/images/r1.png"); line-height:38px;}
    .col_1[title="2"]{ color:#fff; background-image:url("/wdy/front/images/r2.png"); line-height:38px;}
    .col_1[title="3"]{ color:#fff;  background-image:url("/wdy/front/images/r3.png");line-height:38px;}
    .col_2 {
        -webkit-box-flex: 1;
        -moz-box-flex: 1;
        box-flex: 1;
        padding: 5px 0;
        text-align:center;
    }
    .col_2 img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .col_3 {
        width:35%;
        /*color:#f9820d;*/
        font-size:14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        text-align:left;
    }
    .col_4 {
        width:25%;
        text-align:center;
        color:#06bf04;
        font-size:14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .cur section{color: #fff;}
</style>

<div class="block" >
    <div class="name-card name-card-directseller clearfix" style="padding:20px">
        <a class="thumb"><img style="background-image:url('<?=$user2['headimgurl']?>')"></a>
        <div class="detail">
            <p class="font-size-16" style="color:#000"><?=$user2['nickname']?></p>
            <p class="font-size-14"><?=$fuser?><?=$user2['id2']?> 号<?=$config['title1']?></p>
        </div>
    </div>
</div>

<div class="block" >
    <a href="/qwtdka/ddorders">
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
                    <span class="font-size-28 c-green"><?=number_format($user2['paid'], 2)?></span>
                    <span class="corner">元</span>
                </div>
                <div class="label ellipsis">累计付款金额</div>
            </div>
        </div>
    </a>
</div>

<div id="rankpage">
    <section id="ranking">
        <span id="ranking_title"><?=$ranktitle?></span>

        <section id="ranking_list">

        <?php
        foreach ($users as $user):
        $rank++;
        ?>
                <section class="box block-item">
                  <section class="col_1"<?=$rank<=3 ? ' title="1"' : ''?>><?=$rank?></section>
                  <section class="col_2"><img src="<?=$user['headimgurl']?>" /></section>
                  <section class="col_3"><?=$user['nickname']?></section>
                  <section class="col_4">&yen;<?=$user['paid']?></section>
                </section>
        <?php endforeach?>

        </section>
    </section>
</div>
