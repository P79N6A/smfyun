<!-- 详情页开始-->
  <link href="/qwt/css/xiangqing.css" rel="stylesheet">
  <style type="text/css">

.old-pricebox{
    display: block;
    line-height: 20px;
    vertical-align: middle;
    color: #999;
    font-size: 15px;
    margin-bottom: 15px;
    font-weight: bold;
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
  max-width:50%;
  display: inline-block;
}
.textline{
    text-decoration: line-through;
}
.discount-price{
    font-size: 20px;
    color: #f60;
}
.zhekou-price{
    display: block;
    line-height: 20px;
    vertical-align: middle;
    color: #999;
    font-size: 15px;
    margin-bottom: 15px;
}
.discount-num{
    margin-left: 10px;
    padding: 5px;
    border-radius: 5px;
    background-color: red;
    color: #fff;
}
.sellcount{
    font-size: 15px;
}
.swaltable{
    text-align: center;
    border-radius: 5px;
    border: 1px solid green;
}
.swaltable thead tr th{
    text-align: center;
    padding: 5px 10px;
    font-weight: bold;
    white-space: nowrap;
    border-left: 1px solid #efefef;
}
.swaltable thead tr th:first-child{
    border-left:0;
}
.swaltable tbody tr td{
    padding: 5px 10px;
    border-top: 1px solid #efefef;
    /*white-space: nowrap;*/
    border-left: 1px solid #efefef;
}
.swaltable tbody tr td:first-child{
    border-left:0;
}
</style>
<section>

  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
      <div class="tpl-content-page-title">
                购买应用
        </div>
      <ol class="am-breadcrumb">
        <li><a class="am-icon-home am-active">购买应用</a></li>
      </ol>

<!-- ▲ Main container -->

        <!-- page heading end-->
        <!--body wrapper start-->
<link href="/qwt/css/bootstrap.min.css" rel="stylesheet">
<link href="/qwt/css/index_1.css" rel="stylesheet">
<link href="/qwt/css/product_1.css" rel="stylesheet">
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
                    补贴价：<span class="normal-price " style="font-weight: bold;">￥<?=$shprice?></span>
                    <!-- 价格区间 -->
                </div>
                <?php
                $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$bid)->find();
                $flogin=ORM::factory('qwt_login')->where('id','=',$qrcode_edit->fubid)->find();
                if($qrcode_edit->flag==1||$flogin->flag==1){
                    if($qrcode_edit->flag==1){
                        $bid1=$qrcode_edit->id;
                    }else{
                        $bid1=$flogin->id;
                    }
                    $cfg=ORM::factory('qwt_cfg')->getCfg($bid1,1);
                }
                ?>
                <div class="zhekou-price" style="font-weight: bold;">
                    专属邀请折扣价：<span class="discount-price" style="font-weight: bold;">￥<?=$zkprice.'.00'?></span><span class="discount-num">专属折扣<?=($cfg['discount']/10)?>折</span>
                </div>
                <div class="count-num">
                    售出数量：<span class="sellcount"><?=$count?></span>
                </div>
        <div class="activity-date clearfix js-activity-date" style="margin-bottom:10px">
            <span class="pull-left">应用规格：</span>
            <?php
            $tryout = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$result['product']->id)->where('status','=',1)->find();
            if (!$tryout->id):?>
                <a class="pull-left js-select-click" data-sku="<?=$result['tryout']->id?>" data-price="0.00" data-zkprice="0.00" data-oldprice="0.00" data-tryout="<?=$result['tryout']->tryout?>">
                    <span><?=$result['tryout']->name?></span>
                </a>
        <?php endif?>
            <?php foreach ($result['sku'] as $key => $v):
            // $bid=$v->bid;
            $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$bid)->find();
            $flogin=ORM::factory('qwt_login')->where('id','=',$qrcode_edit->fubid)->find();
            $zkprice='0.00';
            if($qrcode_edit->flag==1||$flogin->flag==1){
                if($qrcode_edit->flag==1){
                    $bid1=$qrcode_edit->id;
                }else{
                    $bid1=$flogin->id;
                }
                $cfg=ORM::factory('qwt_cfg')->getCfg($bid1,1);
                $dailisku=ORM::factory('qwt_dlsku')->where('bid','=',$bid1)->where('sid','=',$v->id)->where('state','=',1)->find();
                if($cfg['ifdiscount']==1&&$dailisku->id){
                    $zkprice=ceil($v->price*$cfg['discount']/100).'.00';
                }
            }
            ?>
                <a class="pull-left js-select-click <?=$key==0?'selected':'';?>" data-sku="<?=$v->id?>" data-price="<?=$v->price?>" data-zkprice="<?=$zkprice?>" data-oldprice="<?=$v->old_price?>" data-tryout="<?=$v->tryout?>">
                    <span><?=$v->name?></span>
                </a>
            <?php endforeach?>
           <!-- <a href="#myModal" data-toggle='modal'><img src="http://<?=$_SERVER["HTTP_HOST"]?>/qwta/buy/1" /></a> -->
        </div>
        <a data-toggle='modal' class="buy btn btn-success" type="button">立即订购</a>
            <?php
            $tryout = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$result['product']->id)->find();
            if (!$tryout->id):?>
        <a class="tryout btn btn-success" type="button" style="display:none">立即开通</a>
        <?php endif?>
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
<section class="product_intro">
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
$(document).ready(function(){
    check();
})
document.querySelector('.buy').onclick = function(){
        window.sku_id = $('.selected').data('sku');
        var timestamp = Date.parse(new Date());
        timestamp = timestamp / 1000;
        window.orderid = 'E'+sku_id+timestamp;
        // $.ajax({
        //   url: '/qwta/createorder/'+orderid+'/'+sku_id,
        //   type: 'post',
        //   dataType: 'text',
        //   success: function (res){
        //     console.log(res);
        //   }
        // });

        $.ajax({
          url: '/qwta/buy/'+sku_id+'/'+orderid,
          type: 'post',
          dataType: 'json',
          success: function (res){
            window.imgsrc = res.imgurl;
            window.qrid = res.qrid;
            console.log(res.imgurl);
            console.log(res.qrid);
            swal({
                title: "付款方式",
                text: "微信扫码付款",
                imageUrl: window.imgsrc,
                imageSize: "200x200",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "我已付款",
                cancelButtonText: "取消付款",
                closeOnConfirm: false,
                closeOnCancel: true,
                html: true,
                  },
                function(isConfirm){
                  if (isConfirm) {
                        $.ajax({
                          url: '/qwta/notify/'+orderid+'/'+sku_id+'/'+window.qrid,
                          type: 'post',
                          dataType: 'text',
                          success: function (res){
                    swal("等待", "查询中，请稍等", "info");
                            if(res=='支付成功,请前往【插件中心】查看'){
                    swal("成功", "支付成功,请前往【插件中心】查看.", "success");
                                window.location.href='http://<?=$_SERVER["HTTP_HOST"]?>/qwta/order/';
                            }else{
                    swal("失败", "支付失败，请重试", "error");
                            }
                          }
                        });
                  }
              })
          }
        });

};
    $('.js-select-click').click(function() {
        $('.js-select-click').removeClass('selected');
        $(this).addClass('selected');
        $('.normal-price').html('￥'+$('.selected').data('price'));
        $('.old-price').html('￥'+$('.selected').data('oldprice'));
        $('.discount-price').html('￥'+$('.selected').data('zkprice'));
        check();
        var type = $(this).data('tryout');
        if (type==1) {
            $('.tryout').show();
            $('.buy').hide();
        }else{
            $('.tryout').hide();
            $('.buy').show();
        }
    });
    $('.tryout').click(function(){
        window.sku_id = $('.selected').data('sku');
        var timestamp = Date.parse(new Date());
        timestamp = timestamp / 1000;
        window.orderid = 'E'+sku_id+timestamp;
        window.qrid = 'tryout';
        swal({
            title: "开通试用的信息",
            text: "<table class='swaltable'><thead><tr><th>应用规格</th><th>价格</th><th>使用期限</th><th>功能区别</th></tr></thead><tbody><tr><td><?=$result['product']->name?>试用版</td><td>免费</td><td>3天</td><td><?=$result['tryout']->testab?>功能与正式版一致</td></tr><tr><td><?=$result['product']->name?>正式版</td><td><?=$result['tryout']->fullsku?></td><td>按应用规格</td><td><?=$result['tryout']->fullab?></td></tr></tbody></table>",
            // imageUrl: window.imgsrc,
            // imageSize: "200x200",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认开通",
            cancelButtonText: "取消",
            closeOnConfirm: false,
            closeOnCancel: true,
            html: true,
        },
        function(isConfirm){
              if (isConfirm) {
                    $.ajax({
                      url: '/qwta/notify/'+orderid+'/'+sku_id+'/'+window.qrid,
                      type: 'post',
                      dataType: 'text',
                      success: function (res){
                swal("等待", "查询中，请稍等", "info");
                        if(res=='开通成功'){
            swal({
                title: "成功",
                text: "开通成功,请前往【应用中心】查看并配置",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "立即前往",
                closeOnConfirm: false,
                  },
                function(isConfirm){
                  if (isConfirm) {
                            window.location.href='http://<?=$_SERVER["HTTP_HOST"]?>/qwta/switch/';
                  }
              })
                        }else{
                swal("失败", res, "error");
                        }
                      }
                    });
              }
        })
    })
    $('.product_intro .tab-content .nav-tabs li a').click(function() {
      $('.product_intro .tab-content .nav-tabs li').removeClass('active');
      $(this).parent().addClass('active');
      var a=$(this).data('name');
      $('.tab-pane').removeClass('active');
      $('#'+a).addClass('active')
    });
function check(){
    var a = $('.discount-price').text();
    console.log(a);
    if (a == '￥0.00') {
        $('.zhekou-price').hide();
        $('.normal-price').removeClass('textline');
    }else{
        $('.zhekou-price').show();
        $('.normal-price').addClass('textline');
    };
}
</script>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
