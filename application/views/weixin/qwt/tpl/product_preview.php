<!-- 详情页开始-->
    <link rel="icon" type="image/png" href="/qwt/assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="/qwt/assets/i/app-icon72x72@2x.png">
    <link rel="stylesheet" href="/qwt/assets/css/sweetalert.css">
   <link  rel="stylesheet" href="/qwt/css/page_login26f6ea.css">
    <script src="/qwt/assets/js/echarts.min.js"></script>
    <script src="/qwt/assets/js/sweetalert.min.js"></script>
    <script src="/qwt/assets/js/jquery.min.js"></script>
    <script src="/qwt/assets/js/iscroll.js"></script>
  <link href="/qwt/css/xiangqing.css" rel="stylesheet">
<section>

  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
<!-- ▲ Main container -->

        <!-- page heading end-->
        <!--body wrapper start-->
<link href="/qwt/css/bootstrap.min.css" rel="stylesheet">
<link href="/qwt/css/index_1.css" rel="stylesheet">
<link href="/qwt/css/product_1.css" rel="stylesheet">
<style type="text/css">
.banner{
    margin-top: 60px;
    width: 100%;
    min-width: 1060px;
    /* height: 115px; */
    height: 340px;
    /*margin-top: -7px;*/
    background: url(/qwt/images/banner1.png) no-repeat center 0 #fff;
  }
.old-pricebox{
    display: block;
    line-height: 20px;
    vertical-align: middle;
    color: #999;
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 15px;
}
.sellcount{
    font-size: 15px;
}
.old-price{
    font-size: 20px;
    color: #f60;
    font-weight: bold;
    text-decoration: line-through;
  }
.count-num{
    display: block;
    line-height: 20px;
    vertical-align: middle;
    color: #999;
    font-size: 15px;
    margin-bottom: 15px;
    font-weight: bold;

}
.product_intro img{
  max-width:300px;
  display: inline-block;
}
            .kf{
                position:fixed;
                top:90px;
                right:38px;
            }
            @media only screen and (max-width: 500px){
                .kf{
                    right: 0px;
                }
                .htext{
                    font-size: 22px;
                }
            }
            .mcontent{
                padding:10px;
                text-align: center;
            }
            .message_box{
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                display: -webkit-box;
                -webkit-box-pack: center;
                -webkit-box-align: center;
                z-index: 1002;}
            .message_box.mask{
                background-color: rgba(0, 0, 0, .7);
                -webkit-animation: fadeIn .6s ease;
            }
            .message_box > div{
                padding-top: 25px;
                height:auto;


                top:150px;
                width: 500px;

                background-color: #ffffff;
                border-radius: 7px;
                font-size: 16px;
                -webkit-animation: fadeIn1 .6s ease;
            }
            .message_box .btns{
                border-radius: 0 0 7px 7px;
                display: -webkit-box;
                line-height: 40px;
                border-top:1px solid rgb(203, 203, 203);
                background-color:#00a65a;
                text-align:center;
                padding-top:10px;
                padding-bottom:10px;
            }
            .message_box .ok{
                color:white;
                background-color:#008749;
                width:100px;
                margin-left:200px;
                border-radius:10px;
                display:inline-block;
            }
</style>
     <div class="head_box">
      <div class="inner wrp">
       <h1 class="logo" style="padding-top:0px;">
        <a href="../qwta/login" title="神码浮云营销插件平台" style="width:auto;height:60px;display:inline-block;position:relative;overflow:visible;"><img style="height:100%;" src="/qwt/images/logo.png"><!-- <img style="
    position: absolute;
    top: 30px;
    left: 180px;" src="../qwt/images/image.png"> --></a>
    <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwta/login" style="display:inline-block">返回主页</a>
       </h1>
      </div>
     </div>
     <div class="banner"></div>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
<div class="wrapper">
     <div class="row">
        <div class="col-sm-12">
            <section class="panel">
            <header class="panel-heading">
            <img style='width:222px;display:inline-block;float:left;' src="<?=$result['product']->picurl?>">
             <div class="app-board-intro" style="width:78%;display:inline-block;padding-left:20px">
                   <h2 class="title" style="margin-left:0px;"><?=$result['product']->name?></h2>
                   <p class="intro"><?=$result['product']->abstract?></p>
                   <?php if ($result['product']->id==21):?>
        <a style="position:inherit;margin-bottom:50px;" class="kf btn btn-success" type="button">立即咨询</a>
    <?php else:?>
                <div class="old-pricebox">
                    原价：<span class="old-price">￥<?=$oldprice?></span>
                </div>
                <div class="original-price" style="font-weight: bold;">
                    补贴价：<span class="normal-price" style="font-weight:bold;">￥<?=$shprice?></span>
                </div>
                <div class="count-num">
                    售出数量：<span class="sellcount"><?=$count?></span>
                </div>
        <div class="activity-date clearfix js-activity-date" style="margin-bottom:10px">
            <span class="pull-left">应用规格：</span>
            <?php foreach ($result['sku'] as $key => $v):?>
                <a class="pull-left js-select-click <?=$key==0?'selected':'';?>" data-sku="<?=$v->id?>" data-price="<?=$v->price?>" data-tryout="<?=$v->tryout?>" data-oldprice="<?=$v->old_price?>">
                    <span><?=$v->name?></span>
                </a>
            <?php endforeach?>
           <!-- <a href="#myModal" data-toggle='modal'><img src="http://<?=$_SERVER["HTTP_HOST"]?>/qwta/buy/1" /></a> -->
        </div>
        <a data-toggle='modal' class="buy btn btn-success" type="button">立即开通</a>
            <p class="product_confirm">
            <div style="line-height:20px;margin-top:20px;">支付：<img style="margin-left:10px;margin-right:5px" src="/qwt/images/wx_pay.png">微信支付</div>
            </p>
        <?php endif?>

            </div>
            </header>
<style type="text/css">
    .buy{
        margin-left: 50px;
    }
    .product_intro{
        margin-top: 12px;
    }
</style>
<section class="product_intro" style="background-color:#fff;">
    <div class="tab-content">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a aria-controls="intro" role="tab" data-toggle="tab" data-name="intro">应用介绍</a></li>
            <li role="presentation"><a aria-controls="comment" role="tab" data-toggle="tab" data-name="comment">案例用户</a></li>
            <li role="presentation"><a aria-controls="updatelogs" role="tab" data-toggle="tab" data-name="updatelogs">更新日志</a></li>
        </ul>
        <div role="tabpanel" class="tab-pane active" id="intro">
                <?=$result['product']->interduce?>
        </div>
        <div role="tabpanel" class="tab-pane" id="comment">
            <?=$result['product']->example?>
        </div>
        <div role="tabpanel" class="tab-pane" id="updatelogs">
            <?=$result['product']->updatelogs?>
        </div>
    </div>
</section>

    </section>
        </div>
    </div>
</div>
</div>
</div>
</section>
        <span class="bottomqr"><img class="kf" src="/qwt/images/kf.png"></span>
        <!--详情页结束 -->
                            <!-- modal -->
<script type="text/javascript">
            $(document).on('click','.kf',function(){
                console.log(2);
                $('body').append('<div class=\'message_box mask\'>'+
                                          '<div>'+
                                          '<div class=\'mcontent\'>'+
                                           '<div style=\'margin-top:5px;text-align:left;\'>1.工作时间：周一到周五9:30-18:30；<br>2.客服电话：15926434187；<br>3.客服微信：扫码添加微信，方便快速沟通问题（优先回复）。</div>'+
                                           '<img style=\'width:200px\' src=\'/qwt/images/qr.jpg\'>'+
                                          '</div>'+
                                          '<div class=\'btns\'><span  class=\'ok\'>确定</span></div>'+
                                          '</div>'+
                                          '</div>');
            })

                $(document).on('click', '.ok', function() {
                               $(".message_box").fadeOut();
                          });
          $(".example_logo>div").hover(function() {
            var no = $(".example_logo>div").index(this);
            $('.example').hide();
            $('.example').eq(no).show();
          });
    $('.buy').click(function(){
      swal({
        title: "请先登录",
        text: "点击确定登录后即可购买应用",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: false
      },
      function(){
        window.location.href = "/qwta/login?from=product/<?=$iid?>";
      });
    })

    $('.js-select-click').click(function() {
        $('.js-select-click').removeClass('selected');
        $(this).addClass('selected');
        $('.normal-price').html('￥'+$('.selected').data('price'));
        $('.old-price').html('￥'+$('.selected').data('oldprice'));
        if ($('.selected').data('tryout')==1){
            $('.buy').text('立即开通');
        }else{
            $('.buy').text('立即订购');
        }
    });
    $('.product_intro .tab-content .nav-tabs li a').click(function() {
      $('.product_intro .tab-content .nav-tabs li').removeClass('active');
      $(this).parent().addClass('active');
      var a=$(this).data('name');
      $('.tab-pane').removeClass('active');
      $('#'+a).addClass('active')
    });
</script>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
