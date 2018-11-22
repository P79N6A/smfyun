<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="ch" data-dpr="1" style="font-size: 12px;">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
  <title>订单明细</title>
  <style type="text/css">
  body{
    margin: 0;
    font-family: "Bentonsans";
    src: "BentonSansCond-Bold_0.otf";
    /*text-shadow: 1px 1px 1px #efefef;*/
    background-color: #f3f4f6;
    cursor: pointer;
  }
  div{
    cursor: pointer;
  }

  .tab-bar{
    height: 40px;
    background-color: #f8f8f8;
    position: fixed;
    top: 0;
    width: 100%;
    border-bottom: 1px solid #e9e9e9;
  }
  .tab-body{
    margin-top: 81px;
  }
  ul{
    padding: 0;
    height: 100%;
    margin: 0;
  }
  ul li{
    list-style-type: none;
    display: inline-block;
    width: 20%;
    text-align: center;
    height: 38px;
  }
  .active{
    border-bottom: 2px solid #d13504;
  }
  .active a{
    color: #d13504;
  }
  ul li a{
    padding-top: 13px;
    text-decoration: none;
    display: inline-block;
    color: #5c5c5c;
    font-size: 13px;
    width: 100%;
    height: 26px;
  }
  .orders{
    background-color: #fff;
    border-top: 1px solid #e9e9e9;
    border-bottom: 1ox solid #e9e9e9;
    margin-bottom: 15px;
    border-bottom: 1px solid #e9e9e9;
  }
  .content-head{
    display: inline-block;
    width: 100%;
    font-family: PingFangSC-Regular, sans-serif;
    padding: 3px 0;
    border-bottom: 1px solid #e6e6e6;
    height: 36px;
  }
  .head-top{
    display: inline-block;
    width: 100%;
    color: #3a3a3a;
    font-weight: normal;
    font-size: 12px;
    height: 12px;
    line-height: 12px;
    padding: 3px 0;
  }
  .phonenumber{
    display: inline-block;
    float: left;
    padding-left: 15px;
  }
  .phonenumber img{
    height: 12px;
    padding-right: 5px;
  }
  .status{
    display: inline-block;
    float: right;
    padding-right: 15px;
  }
  .head-bottom{
    display: inline-block;
    width: 100%;
    color: #b5b5b5;
    font-weight: normal;
    font-size: 12px;
    height: 12px;
    line-height: 12px;
    padding: 3px 0;
  }
  .datetime{
    display: inline-block;
    float: left;
    padding-left: 15px;
  }
  .orderid{
    display: inline-block;
    float: right;
    padding-right: 15px;
  }
  .content-body{
    display: inline-block;
    max-height: 201px;
    overflow: scroll;
    border-bottom: 1px solid #e9e9e9;
    width: 100%
  }
  .goods-box{
    display: inline-block;
    width: 100%;
    height: 100px;
    border-bottom: 1px solid #e9e9e9;
  }
  .img-box{
    display: inline-block;
    float: left;
    height: 100px;
    width: 110px;
    text-align: center;
    vertical-align: middle;
  }
  .img-box img{
    display: inline-block;
    padding: 10px 15px 10px 15px;
    max-width: 80px;
    max-height: 80px;
  }
  .price{
    margin-top: 10px;
    height: 20px;
    line-height: 20px;
    font-size: 12px;
    color: #aaa;
    text-align: right;
    padding-right: 15px;
  }
  .name{
    height: 20px;
    line-height: 20px;
    font-size: 12px;
    color: #404040;
  }
  .num{
    height: 20px;
    line-height: 20px;
    font-weight: 12px;
    color: #aaa;
    text-align: right;
    padding-right: 15px;
  }
  .type{
    height: 20px;
    line-height: 20px;
    font-size: 12px;
    color: #9a9a9a;
  }
  .content-lg{
    display: inline-block;
    width: 100%;
    padding: 7px 0;
  }
  .lgleft{
    display: inline-block;
    float: left;
    padding-left: 15px;
  }
  .lgright{
    display: inline-block;
    float: right;
    padding-right: 15px;
    color: #d93300;
  }
  .sumdesc{
    color: #484848;
  }
  .sumnum{
    font-weight: bold;
    color: #3e3e3e;
  }
  .sumsmall{
    color: #9e9e9e;
  }
  .menuicon{
    width: 15%;
    display: inline-block;
    text-align: center;
    height: 100%;
  }
  .menuicon img{
    height: 20px;
    margin-top: 10px;
  }
  .search-box{
    width: 70%;
    display: inline-block;
    position: absolute;
    top: 0;
    left: 15%;
    bottom: 0;
    right: 15%;
    padding: 10px 0;
  }
  .search-button{
    width: 15%;
    display: inline-block;
    text-align: center;
    float: right;
    position: absolute;
    top: 0;
    right: 0;
    padding: 0;
    border: 0;
    height: 100%;
    margin: 0;
    background: bottom;
    vertical-align: middle;
  }
  .search-button img{
    height: 20px;
  }
  input{

    position: absolute;
    /* left: 10px; */
    /* right: 10px; */
    width: 100%;
    border-radius: 5px;
    border: 0;
    /* height: 100%; */
    height: 20px;
    text-align: center;
  }
  .second-tab{
    top: 41px;
  }
  </style>
</head>
<?php
$arr = array();
$arr['WAIT_SELLER_SEND_GOODS'] = '待发货';
$arr['WAIT_BUYER_CONFIRM_GOODS'] = '待收货';
$arr['TRADE_BUYER_SIGNED'] = '已完成';
$arr['TRADE_CLOSED'] = '已退款';
$arr['TRADE_CLOSED_BY_USER'] = '已取消';
?>
<body>
<div class="tab-bar">
  <div class="menuicon">
    <img src="/dld/img/category.png">
  </div><div class="search-box">
    <input type="text" name='s' class='inputstr' placeholder="收货人，手机号搜索">
  </div><button class="search-button">
      <img src="/dld/img/search.png">
    </button>
</div>
<div class="tab-bar second-tab">
  <ul>
    <li class="active"><a data-type="all" href="#">全部</a></li><li><a data-type="unpay" href="#">待发货</a></li><li><a data-type="unsend" href="#">待收货</a></li><li><a data-type="done" href="#">已完成</a></li><li><a data-type="payback" href="#">已退款</a></li>
  </ul>
</div>
<div class="tab-body list-all">
  <?php foreach ($orders as $k => $v):?>
    <div class="orders order_<?=$k?>">
    <div class="content-head">
      <div class="head-top">
        <div class="phonenumber"><img src="/dld/img/lisense.png">收货人：<?=$v->receiver_name?>(手机号：<?=$v->receiver_mobile?>)</div>
        <div class="status"><?=$arr[$v->status]?></div>
      </div>
      <div class="head-bottom">
        <div class="datetime"><?=$v->pay_time?></div>
        <div class="orderid"><?=$v->tid?></div>
      </div>
    </div>
    <div class="content-body">
      <div class="goods-box">
        <div class="img-box">
          <img src="<?=$v->pic_thumb_path?>">
        </div>
          <div class="price">
            ￥<?=$v->total_fee?>
          </div>
          <div class="name">
            <?=$v->title?><br>
            (此处仅显示订单概况，不显示订单购买详情)
          </div>
          <div class="num">
            <!-- <?=$v->num?> -->
          </div>
          <div class="type">
            <!-- 口味：香辣孜然味；规格：120g -->
          </div>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">共<?=$v->num?>件，实付款：</span>
        <span class="sumnum">￥<?=$v->payment?></span>
        <span class="sumsmall">（含运费￥<?=$v->post_fee?>）</span>
      </div>
      <div class="lgright">
        利润：￥<?=$v->money1?>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">收货地址：<?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></span>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">买家备注：<?=strlen($v->buyer_message)==0?'无':$v->buyer_message?></span>
      </div>
    </div>
  </div>
  <?php endforeach?>
</div>
<div class="tab-body list-unpay" style="display:none">
<?php foreach ($orders as $k => $v):?>
    <?php if($v->status=='WAIT_SELLER_SEND_GOODS'):?>
      <div class="orders">
    <div class="content-head">
      <div class="head-top">
        <div class="phonenumber"><img src="/dld/img/lisense.png">收货人：<?=$v->receiver_name?>(手机号：<?=$v->receiver_mobile?>)</div>
        <div class="status"><?=$arr[$v->status]?></div>
      </div>
      <div class="head-bottom">
        <div class="datetime"><?=$v->pay_time?></div>
        <div class="orderid"><?=$v->tid?></div>
      </div>
    </div>
    <div class="content-body">
      <div class="goods-box">
        <div class="img-box">
          <img src="<?=$v->pic_thumb_path?>">
        </div>
          <div class="price">
            ￥<?=$v->total_fee?>
          </div>
          <div class="name">
            <?=$v->title?><br>
            (此处仅显示订单概况，不显示订单购买详情)
          </div>
          <div class="num">
            <!-- <?=$v->num?> -->
          </div>
          <div class="type">
            <!-- 口味：香辣孜然味；规格：120g -->
          </div>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">共<?=$v->num?>件，实付款：</span>
        <span class="sumnum">￥<?=$v->payment?></span>
        <span class="sumsmall">（含运费￥<?=$v->post_fee?>）</span>
      </div>
      <div class="lgright">
        利润：￥<?=$v->money1?>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">收货地址：<?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></span>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">买家备注：<?=strlen($v->buyer_message)==0?'无':$v->buyer_message?></span>
      </div>
    </div>
  </div>
    <?php endif?>
  <?php endforeach?>
</div>
<div class="tab-body list-unsend" style="display:none">
<?php foreach ($orders as $k => $v):?>
    <?php if($v->status=='WAIT_BUYER_CONFIRM_GOODS'):?>
      <div class="orders">
    <div class="content-head">
      <div class="head-top">
        <div class="phonenumber"><img src="/dld/img/lisense.png">收货人：<?=$v->receiver_name?>(手机号：<?=$v->receiver_mobile?>)</div>
        <div class="status"><?=$arr[$v->status]?></div>
      </div>
      <div class="head-bottom">
        <div class="datetime"><?=$v->pay_time?></div>
        <div class="orderid"><?=$v->tid?></div>
      </div>
    </div>
    <div class="content-body">
      <div class="goods-box">
        <div class="img-box">
          <img src="<?=$v->pic_thumb_path?>">
        </div>
          <div class="price">
            ￥<?=$v->total_fee?>
          </div>
          <div class="name">
            <?=$v->title?><br>
            (此处仅显示订单概况，不显示订单购买详情)
          </div>
          <div class="num">
            <!-- <?=$v->num?> -->
          </div>
          <div class="type">
            <!-- 口味：香辣孜然味；规格：120g -->
          </div>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">共<?=$v->num?>件，实付款：</span>
        <span class="sumnum">￥<?=$v->payment?></span>
        <span class="sumsmall">（含运费￥<?=$v->post_fee?>）</span>
      </div>
      <div class="lgright">
        利润：￥<?=$v->money1?>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">收货地址：<?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></span>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">买家备注：<?=strlen($v->buyer_message)==0?'无':$v->buyer_message?></span>
      </div>
    </div>
  </div>
    <?php endif?>
  <?php endforeach?>
</div>
<div class="tab-body list-done" style="display:none">
<?php foreach ($orders as $k => $v):?>
    <?php if($v->status=='TRADE_BUYER_SIGNED'):?>
      <div class="orders">
    <div class="content-head">
      <div class="head-top">
        <div class="phonenumber"><img src="/dld/img/lisense.png">收货人：<?=$v->receiver_name?>(手机号：<?=$v->receiver_mobile?>)</div>
        <div class="status"><?=$arr[$v->status]?></div>
      </div>
      <div class="head-bottom">
        <div class="datetime"><?=$v->pay_time?></div>
        <div class="orderid"><?=$v->tid?></div>
      </div>
    </div>
    <div class="content-body">
      <div class="goods-box">
        <div class="img-box">
          <img src="<?=$v->pic_thumb_path?>">
        </div>
          <div class="price">
            ￥<?=$v->total_fee?>
          </div>
          <div class="name">
            <?=$v->title?><br>
            (此处仅显示订单概况，不显示订单购买详情)
          </div>
          <div class="num">
            <!-- <?=$v->num?> -->
          </div>
          <div class="type">
            <!-- 口味：香辣孜然味；规格：120g -->
          </div>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">共<?=$v->num?>件，实付款：</span>
        <span class="sumnum">￥<?=$v->payment?></span>
        <span class="sumsmall">（含运费￥<?=$v->post_fee?>）</span>
      </div>
      <div class="lgright">
        利润：￥<?=$v->money1?>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">收货地址：<?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></span>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">买家备注：<?=strlen($v->buyer_message)==0?'无':$v->buyer_message?></span>
      </div>
    </div>
  </div>
    <?php endif?>
  <?php endforeach?>
</div>
<div class="tab-body list-payback" style="display:none">
<?php foreach ($orders as $k => $v):?>
    <?php if($v->status=='TRADE_CLOSED'):?>
      <div class="orders">
    <div class="content-head">
      <div class="head-top">
        <div class="phonenumber"><img src="/dld/img/lisense.png">收货人：<?=$v->receiver_name?>(手机号：<?=$v->receiver_mobile?>)</div>
        <div class="status"><?=$arr[$v->status]?></div>
      </div>
      <div class="head-bottom">
        <div class="datetime"><?=$v->pay_time?></div>
        <div class="orderid"><?=$v->tid?></div>
      </div>
    </div>
    <div class="content-body">
      <div class="goods-box">
        <div class="img-box">
          <img src="<?=$v->pic_thumb_path?>">
        </div>
          <div class="price">
            ￥<?=$v->total_fee?>
          </div>
          <div class="name">
            <?=$v->title?><br>
            (此处仅显示订单概况，不显示订单购买详情)
          </div>
          <div class="num">
            <!-- <?=$v->num?> -->
          </div>
          <div class="type">
            <!-- 口味：香辣孜然味；规格：120g -->
          </div>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">共<?=$v->num?>件，实付款：</span>
        <span class="sumnum">￥<?=$v->payment?></span>
        <span class="sumsmall">（含运费￥<?=$v->post_fee?>）</span>
      </div>
      <div class="lgright">
        利润：￥<?=$v->money1?>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">收货地址：<?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></span>
      </div>
    </div>
    <div class="content-lg">
      <div class="lgleft">
        <span class="sumdesc">买家备注：<?=strlen($v->buyer_message)==0?'无':$v->buyer_message?></span>
      </div>
    </div>
  </div>
    <?php endif?>
  <?php endforeach?>
</div>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
      var arr = Array();
  <?php foreach ($orders as $k => $v):?>
      arr[<?=$k?>] = Array();
      arr[<?=$k?>]['receiver_mobile'] = '<?=$v->receiver_mobile?>'
      arr[<?=$k?>]['receiver_name'] = '<?=$v->receiver_name?>'
  <?php endforeach?>
  $('.search-button').click(function() {
    var str = $('.inputstr').val();
    if(str!=''){
      $('.tab-bar ul li').removeClass('active');
      $('.tab-bar ul li').eq(0).addClass('active');
      $('.tab-body').css({
        display: 'none'
      });
      $('.list-all').css({
        display: 'block'
      });
      $('.list-all > .orders').css({
        display: 'none'
      });
      $.each(arr, function(index, val) {
        console.log(val['receiver_mobile'].indexOf(str));
        console.log(val['receiver_name'].indexOf(str));
        if(val['receiver_mobile'].indexOf(str)>=0||val['receiver_name'].indexOf(str)>=0){
          console.log(index);
          $('.order_'+index).css({
            display: 'block'
          });
         }
      });
    }
  });
  $(".inputstr").on("input propertychange",function(){
     if($(this).val()==""){
        $('.list-all > .orders').css({
          display: 'block'
        });
     }
  })
</script>
<script type="text/javascript">
  $('.tab-bar ul li a').click(function(){
    $('.tab-bar ul li').removeClass('active');
    $(this).parent().addClass('active');
    var a = $(this).data('type');
    $('.tab-body').hide();
    $('.list-'+a).show();
  })
  var a = 0;

  $('.menuicon').click(function(){
    $('.second-tab').toggle();
    if (a==0) {
      $(".tab-body").css("margin-top","40px");
      a=1;
    }else{
      $(".tab-body").css("margin-top","81px");
      a=0;
    }
  });
</script>
</body>

</html>
