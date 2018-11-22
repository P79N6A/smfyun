<?php
    function covent($flag){
        switch ($flag) {
            case 1:
                echo "通过";
                break;
            case 2:
                echo "未通过";
                break;
            default:
                echo "审核中";
                break;
        }
    }
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
    a{
        color: white;
    }
    .block-item{
      padding:3px;
    }
    #ranking_title {
        width: 50%;
        left: 25%;
        top: -30px;
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
        width:20%;
        text-align:center;
        color:#06bf04;
        font-size:14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .cur section{color: #fff;}
</style>

<div class="block" style="margin-top:0px;">
    <div class="name-card name-card-directseller clearfix" style="padding:20px">
        <a class="thumb"><img src="<?=$user['headimgurl']?>"></a>
        <div class="detail">
            <p class="font-size-16" style="color:#000"><?=$user2['nickname']?></p>
        </div>
    </div>
</div>
<div class="block" >

        <div class="ui two border-bottom overview">
            <div class="item" >
            <a href="/yty/stock">
                <div class="value ellipsis">
                    <span class="font-size-28 c-green"><?=(int)$result['num1']?></span>
                    <span class="corner">个</span>
                </div>
                <div class="label ellipsis">我的进货申请</div>
            </a>
            </div>
            <div class="item">
            <a href="/yty/stock?stock=1">
                <div class="value ellipsis">
                    <span class="font-size-28 c-green"><?=(int)$result['num2']?></span>
                    <span class="corner">个</span>
                </div>
                <div class="label ellipsis">下级经销商进货申请</div>
            </a>
            </div>
        </div>
</div>

<div id="rankpage">
    <section id="ranking">
        <?php if($result['status']==1):?>
        <a href="/yty/mstock?"><span id="ranking_title"> 我要进货</span></a>
        <?php endif;?>
        <section id="ranking_list">
             <section class="box block-item">
                <section class="col_4">头像</section>
                <section class="col_4">昵称</section>
                <section class="col_4">受理经销商</section>
                <section class="col_4">金额</section>
                <?php if($result['status']==1):?>
                <section class="col_4">状态</section>
                <?php endif;?>
                <?php if($result['status']==2):?>
                <section class="col_4">操作</section>
                <?php endif;?>
            </section>
            <?php
            foreach ($stocks as $stock):
            ?>
            <!-- <form method="post" onsubmit="return check()"> -->
            <section class="box block-item">
                <section class="col_2"><img src="<?=$stock->qrcode->headimgurl?>" /></section>
                <section class="col_4"><?=$stock->qrcode->nickname?></section>
                <section class="col_4"><?=$stock->fqrcode->nickname?></section>
                <section class="col_4"><?=$stock->money?></section>
                <?php if($result['status']==1):?>
                <section class="col_4"><?=covent($stock->flag)?></section>
                <?php endif;?>
                <?php if($result['status']==2):?>
                    <?php if($stock->flag==0):?>
                    <section class="col_4"><button class="btn btn-danger shenhe" data-id='<?=$stock->id?>'>审核</button></section>
                     <?php endif?>
                     <?php if($stock->flag==1):?>
                    <section class="col_4">已处理</section>
                    <?php endif?>
                <?php endif;?>
            </section>
            <!-- </form> -->
             <?php endforeach?>
         </section>
    </section>
</div>
 <div class="box-footer clearfix">
                <?=$page?>
  </div>
    <footer class="page-footer fixed-footer">
        <ul style="height: 0px">
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
<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
 <title>alert</title>
 <link rel="stylesheet" type="text/css" href="/yty/css/sweetalert.css">
</head>
<style type="text/css">
/* .btn{
  display: none;
 }*/
</style>
<body>
</body>
<script type="text/javascript" src="/yty/js/sweetalert.min.js"></script>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
$('.shenhe').click(function(){
    console.log($(this).data('id'));
    var that = this;
    sweetAlert({
      title: "确定?",
      text: "审核一旦通过就不可撤回!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "确定",
      closeOnConfirm: false
    }, function(){
    var data = {
        id:$(that).data('id')
    }
    $.ajax({
      url: '/yty/stock',
      type: 'post',
      dataType: 'json',
      data: {data: data},
    })
    .done(function(res) {
      if(res.flag=='success'){
        swal('成功',res.echo,'success');
      }else{
        swal('失败',res.echo,'error');
      }
      swal({
        title: "提示",
        text: res.echo,
        type: res.flag,
        closeOnConfirm: false,
        }, function() {
            window.location.reload();
        });
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
      // swal("Deleted!",
      // "Your imaginary file has been deleted.",
      // "success");
    });
})

</script>
</html>
<link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
