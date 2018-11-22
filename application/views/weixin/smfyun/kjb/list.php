<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

    <title></title>
    <!-- for the one page layout -->
    <link rel="stylesheet" type="text/css" href="../qwt/kjb/list.css">
    <style type="text/css">
    ul{
    position: fixed;
    margin-top: calc(12.6em + 6px);
    height: calc(100% - 19.6em - 6px);
    overflow: scroll;
    padding: 1em 0;
    }
    h3{
      background-color: #fff;
      position: fixed;
      top: 0;
      width: 100%;
      height: 1em;
      z-index: 1;
      padding-top: .5em;
      margin-top: 0;
    }
    .menu-bar{
      position: fixed;
      bottom: 0;
      background-color: #fff;
      width: 100%;
      height: 5em;
      border-top: 1px solid #dedede;
    }
    .menu{
      width: calc(33% - 1px);
      border-left: 1px solid #dedede;
      text-align: center;
      display: inline-block;
      height: 2em;
      padding-top: 3em;
      text-decoration: none;
      color: #666;
    }
  .menu-shop{
    background: url(../qwt/kjb/shop.png) no-repeat 50% .5em;
    background-size: 2em 2em;
    border-left: 0;
  }
  .menu-event{
    background: url(../qwt/kjb/user.png) no-repeat 50% .5em;
    background-size: 2em 2em;
  }
  .menu-order{
    background: url(../qwt/kjb/list.png) no-repeat 50% .5em;
    background-size: 2em 2em;
  }
  .menu-active{
    color: red;
  }
  .top-bar{
    position: fixed;
    top: 0;
    width: 100;
    width: 100%;
    background-color: #fff;
    border-bottom: 1px solid #dedede;
    text-align: center;
  }
  .top-bar img{
    width: 4em;
    border-radius: 2em;
    border: 1px solid #dedede;
    margin-top: 1em;
  }
  .shopname{
    color: #666;
    padding: .2em;
    border-bottom: 1px solid #dedede;
  }
  .two-button{
    border-bottom: 1px solid #dedede;
  }
  .button-left{
    width: calc(50% - 1px);
    display: inline-block;
    border-right: 1px solid #dedede;
    text-decoration: none;
    color: #999;
    padding: 3em 0 .2em;
    background: url(../qwt/kjb/shop.png) no-repeat 50% .5em;
    background-size: 2em 2em;
  }
  .button-right{
    width: 50%;
    display: inline-block;
    /* border-right: 1px solid #dedede; */
    text-decoration: none;
    color: #999;
    padding: 3em 0 .2em;
    background: url(../qwt/kjb/phone.png) no-repeat 50% .5em;
    background-size: 2em 2em;
  }
  .main-title{
    padding: .5em;
    color: #fff;
    background-color: red;
  }
  .left-time{
    color: #ff595c;
    position: absolute;
    bottom: 1em;
    right: 8em;
  }
    </style>

    <script type="text/javascript" src="http://code.jquery.com/jquery-3.2.1.js"></script>
  </head>
  <body>
    <div class="top-bar">
      <img src="<?=$shop['img']?>">
      <div class="shopname"><?=$shop['name']?></div>
      <div class="two-button">
        <a class="button-left" href="<?=$shop['url']?>">访问店铺</a><a class="button-right" href="tel://<?=$shop['tel']?>">联系客服</a>
      </div>
      <div class="main-title"><?=$result['type']?></div>
    </div>
    <ul>
      <?php if ($result['type']=='我发起的砍价'):?>
        <?php if ($event):?>
          <?php foreach ($event as $k => $v):?>
            <?php if ($v->item->endtime>time()):?>
              <?php $order[$k] = ORM::factory('qwt_kjborder')->where('bid','=',$bid)->where('eid','=',$v->id)->find();?>
                <?php if (!$order[$k]->id):?>
              <li>
                <img src="/qwtkjb/images/item/<?=$v->item->id?>.jpg">
                <div class="item-name"><?=$v->item->name?></div>
                <div class="old-price">原价￥<?=number_format($v->item->old_price/100,2)?></div>
                <div class="min-price">最低价￥<?=number_format($v->item->price/100,2)?></div>
                <div class="min-price">我的价格￥<?=number_format($v->now_price/100,2)?></div>
                  <?php
                    $lefttime = $v->item->endtime - time();
                    $day = intval(floor($lefttime/86400));
                    $lefttime = $lefttime%86400;
                    $hour = intval(floor($lefttime/3600));
                    $lefttime = $lefttime%3600;
                    $minute = intval(floor($lefttime/60));
                  ?>
                  <div class="left-time">剩余<?=$day?>天<?=$hour?>小时<?=$minute?>分</div>
                <a class="button-kan" href="/qwtkjb/kanpage/<?=$v->id?>"><?=$result['action']?></a>
              </li>
            <?php endif?>
            <?php endif?>
          <?php endforeach?>
        <?php endif?>
      <?php else:?>
        <?php if ($result['type']=='我的订单'):?>
          <?php if ($order):?>
            <?php foreach ($order as $k => $v):?>
              <?php if($v->order_state==1||$v->pay_money==0):?>
              <li>
                <img src="/qwtkjb/images/item/<?=$v->item->id?>.jpg">
                <div class="item-name"><?=$v->item->name?></div>
                <div class="old-price">原价￥<?=number_format($v->item->old_price/100,2)?></div>
                <div class="min-price">付款￥<?=number_format($v->pay_money/100,2)?></div>
                <div class="min-price">付款时间：<?=$v->pay_time==0?date('Y-m-h H:i:s',$v->lastupdate):date('Y-m-h H:i:s',$v->pay_time)?></div>
                <div class="min-price">订单状态：<?=$v->state==1?'已发货':'未发货'?></div>
                <a class="button-kan" href="/qwtkjb/order/<?=$v->id?>"><?=$result['action']?></a>
              </li>
              <?php else:?>
              <li>
                <img src="/qwtkjb/images/item/<?=$v->item->id?>.jpg">
                <div class="item-name"><?=$v->item->name?></div>
                <div class="old-price">原价￥<?=number_format($v->item->old_price/100,2)?></div>
                <div class="min-price">未付款</div>
                <a class="button-kan" href="/qwtkjb/checkout/<?=$v->id?>">继续付款</a>
              </li>
              <?php endif?>
            <?php endforeach?>
          <?php endif?>
        <?php else:?>
          <?php if ($item_now):?>
            <?php foreach ($item_now as $k => $v):?>
              <li>
                <img src="/qwtkjb/images/item/<?=$v->id?>.jpg">
                <div class="item-name"><?=$v->name?></div>
                <div class="old-price">原价￥<?=number_format($v->old_price/100,2)?></div>
                <div class="min-price">最低价￥<?=number_format($v->price/100,2)?></div>
                <?php
                  $lefttime = $v->endtime - time();
                  $day = intval(floor($lefttime/86400));
                  $lefttime = $lefttime%86400;
                  $hour = intval(floor($lefttime/3600));
                  $lefttime = $lefttime%3600;
                  $minute = intval(floor($lefttime/60));
                ?>
                <div class="left-time">剩余<?=$day?>天<?=$hour?>小时<?=$minute?>分</div>
                <a class="button-kan" href="/qwtkjb/itempage/<?=$v->id?>"><?=$result['action']?></a>
              </li>
            <?php endforeach?>
          <?php endif?>
        <?php endif?>
      <?php endif?>
    </ul>
    <div class="menu-bar">
      <a class="menu menu-shop <?=$result['type']=='砍价商品列表'?'menu-active':''?>" href="/qwtkjb/list">商品列表</a><a class="menu menu-event <?=$result['type']=='我发起的砍价'?'menu-active':''?>" href="/qwtkjb/myitem">我发起的砍价</a><a class="menu menu-order <?=$result['type']=='我的订单'?'menu-active':''?>" href="/qwtkjb/myorder">我的订单</a>
    </div>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script type="text/javascript">
  wx.config({
    debug: 0,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: '<?php echo $jsapi["timestamp"];?>',
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'hideMenuItems'
      ]
  });
  wx.ready(function () {
    wx.checkJsApi({
      jsApiList: [
          'checkJsApi',
        'hideMenuItems'
      ],
      success: function (res) {
        console.log(res);
      }
    });
    wx.hideMenuItems({
            menuList: [
                        'menuItem:share:appMessage',
                        'menuItem:share:timeline',
            'menuItem:copyUrl',
            "menuItem:editTag",
            "menuItem:delete",
            "menuItem:originPage",
            "menuItem:readMode",
            "menuItem:openWithQQBrowser",
            "menuItem:openWithSafari",
            "menuItem:share:email",
            "menuItem:share:brand",
            "menuItem:share:qq",
            "menuItem:share:weiboApp",
            "menuItem:favorite",
            "menuItem:share:facebook",
            "menuItem:share:QZone"
            ],
            success: function (res) {
              console.log(res);
            },
            fail: function (res) {
              console.log(res);
            }
        });
   })
$(document).ready(function(){
  var topheight = $('.top-bar').height();
  $('ul').css('margin-top',topheight+'px');
})
    </script>
  </body>
</html>
