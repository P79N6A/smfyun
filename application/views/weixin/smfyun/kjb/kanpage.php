<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

    <title><?=$shop['title']?></title>
    <!-- for the one page layout -->
    <link rel="stylesheet" type="text/css" href="/qwt/kjb/kanpage.css">
    <style type="text/css">
    .itemimg{
      background-image: url(/qwtkjb/images/item/<?=$item->id?>.jpg);
    }
    .buynow{
      box-shadow: 2px 2px 2px 0 rgba(0,0,0,1);
      border: 2px solid #efefef;
      border-radius: 1em;
    }
    .shadowfade{
      text-align: center;
      color: #fff;
      padding-top: 50%;
      font-size: 18px;
    }
    .imgbox{
      width: calc(100% - 4em);
      padding: 2em;
      position: relative;
    }
    .imgbox img{
      width: 100%;
    }
    .hint-text{
      color: #562801;
      position: absolute;
      top: 55%;
      width: 40%;
      left: 30%;
    }
    .hintbuynow{
      position: absolute;
      width: 32%;
      height: 11%;
      left: 50%;
      top: 72%;
    }
    .hintcancel{
      position: absolute;
      width: 32%;
      height: 11%;
      left: 18%;
      top: 72%;
    }
    </style>

    <script type="text/javascript" src="http://code.jquery.com/jquery-3.2.1.js"></script>
  </head>
  <body>
<!-- <a href="http://www.baidu.com/" class="report">投诉</a> -->
<div class="shadow shadowfade">请稍等……</div>
<div class="shadow shadowshare">
  <img src="/qwt/kjb/fenxiang.png" class="close shareimg">
</div>
<div class="shadow shadowcut">
  <a href="/qwtkjb/kanpage/<?=$event->id?>">
    <img style="width:24em" class="cutimg" src="/qwt/kjb/cutimg.png">
    <div class="cuttext"></div>
    <!-- <div class="cutbutton">点击继续</div> -->
  </a>
</div>
<div class="shadow shadowform">
</div>
<div class="shadow shadowhint">
  <div class="imgbox">
    <img src="/qwt/kjb/kanhint.png">
    <div class="hint-text">您将以￥<?=$event->now_price/100?>买下，点击确认购买后不能再让好友帮你砍了哦~</div>
    <a class="hintbuynow" href="/qwtkjb/createorder/<?=$event->id?>"></a>
    <a class="hintcancel"></a>
  </div>
</div>
<div class='footer'>
 <a class="menu-icon menu-shop" href="/qwtkjb/list">其他商品</a><a href="/qwtkjb/myitem" class="menu-icon menu-mine">我的</a><a class="menu-icon menu-call" href="tel://<?=$shop['tel']?>">联系商家</a><?php if ($result['type']=='item'):?><a class="menu-button menu-main" href="/qwtkjb/buildkan/<?=$item->id?>">我要报名</a><?php else:?><a class="menu-button menu-main share">找人帮砍</a><?php endif?>
</div>
<div class="container">
  <!-- 尚未完成 分享次数 <span class='font-red'>3</span>次分享 -->
  <div class="top"><span class='font-red'><?=$item->pv?></span>次浏览<span class='font-red'><?=$item->eventcount?></span>人报名<span class='font-red'><?=$item->cutcount?></span>人帮砍</div>
  <div class="itemimg"></div>
  <div class="mainbox">
    <div class="titlebar">
      <div class="title-left">
        <div style="float: left;">
          <div class="icon-yuan">￥<?=$item->price/100?></div>
          <div class="dijia">底价</div>
        </div>
        <!-- <div class="min-price"><?=$item->price/100?></div> -->
      </div>
      <div class="title-content">
        <div class="old-price">原价￥<?=$item->old_price/100?></div>
        <div class="sell-num">已售<?=$result['sells_num']?>份，剩余<?=$item->stock?>份</div>
      </div>
      <div class="mask1"></div>
      <div class="mask2"></div>
    </div>
    <?php if (!$result['time']=='forever'):?>
    <div class="title-right">
      <div class="day-left">距结束仅剩<span class="day-text"><?=$result['day']?></span>天</div>
      <div class="time-left">
        <label class="time-hour"><?=$result['hour']?></label>时<label class="time-minute"><?=$result['minute']?></label>分<label class="time-second"><?=$result['second']?></label>秒
      </div>
    </div>
    <?php endif?>
<?php if (!$result['type']=='item'):?>
    <div class="content-center">
      <img class="user-avator" src="<?=$fuser->headimgurl?>">
      <ul style="margin-top:1em;">
        <li class="content-price">￥<span style="font-size:3em;font-weight:bold;"><?=$event->now_price/100?> </span> 现价</li>
        <?php if (($item->cut_num - $cut_count)==0):?>
          <li class="content-text">已砍至最低价</li>
        <?php else:?>
        <li class="content-text"><span class="font-orange"><?php if(!$result['self']==1):?><?=$fuser->nickname?><?php endif?></span>距离活动最低价还差<span class="font-orange"><?=($event->now_price - $item->price)/100?>元</span>，还可以砍<span class="font-orange"><?=$item->cut_num - $cut_count?></span>刀<?php if(!$result['self']==1):?>，快帮Ta一把<?php endif?></li>
      <?php endif?>
      </ul>
    </div>
    <div class="button-box">
      <?php if ($item->stock==0):?>
        <a href="/qwtkjb/list" class="button-main back-gray">已售完，看看其他商品</a>
      <?php else:?>
        <?php if ($result['self']==1):?>
          <?php if ($result['payed']==1):?>
            <a href="/qwtkjb/list" class="button-main back-gray">已买到，看看其他商品</a>
          <?php elseif($result['buyed']==1):?>
            <a href="/qwtkjb/checkout/<?=$result['oid']?>" class="button-main buynow">尚未付款，立即付款</a>
          <?php else:?>
            <?php if($event->now_price - $item->price ==0):?>
              <a href="/qwtkjb/createorder/<?=$event->id?>" class="button-main buynow">已到最低价，立刻买下</a>
            <?php else:?>
              <a class="button-main buynow buyhint">立刻此价买下</a>
              <?php if ($result['knife']==1):?>
              <a data-knife="1" class="cutnow button-main back-yellow">使用宝刀帮自己砍一刀</a>
              <?php endif?>
            <?php endif?>
          <?php endif?>
        <?php else:?>
          <?php if ($result['subtocut']==1):?>
            <a class="button-main" href="/qwtkjb/need_sub/<?=$event->id?>">关注我们，帮ta砍价</a>
          <?php else:?>
            <?php if ($result['buyed']==1):?>
              <a class="button-main" href="/qwtkjb/buildkan/<?=$item->id?>">ta已买下，我也要参加</a>
            <?php else:?>
              <?php if($event->now_price - $item->price ==0):?>
                <a class="button-main" href="/qwtkjb/buildkan/<?=$item->id?>">已到最低价，我也要参加</a>
              <?php else:?>
                <?php if ($result['cutted']==1):?>
                  <a class="button-main" href="/qwtkjb/buildkan/<?=$item->id?>">已帮砍，我也要参加</a>
                <?php else:?>
                  <a class="button-main cutnow">帮Ta砍一刀</a>
                <?php endif?>
              <?php endif?>
            <?php endif?>
          <?php endif?>
        <?php endif?>
      <?php endif?>
    </div>
    <!-- <div class="paybox" style="display:none">
<form method="post" id="newForm">
            <div class="edit" style="display:none">
                <image class="position" src="/qwt/wfb/position.png"></image>
                <div class="address">
                    <div class="basic">
                        <text class="name">收货人：</text>
                        <text class="phone">电话：</text>
                    </div>
                    <div class="location">收货地址：</div>
                </div>
                <image class="goin2" src="/qwt/wfb/goin.png"></image>
            </div>
            <div class="add">
                <image class="addimg" src="/qwt/wfb/add.png"></image>
                <div class="addtext">点击添加地址</div>
                <image class="goin" src="/qwt/wfb/goin.png"></image>
            </div>
            <div class="button-buy">立即付款</div>
            <input id="name"  type="hidden" name="buy[name]" >
            <input id="tel" type="hidden" name="buy[tel]" >
            <input id="money" type="hidden" name="buy[pay_money]" >
            <input id="prov" class='prov' type="hidden" name="s_province" >
            <input id="city" class='city' type="hidden" name="s_city" >
            <input id="dist" class='dist' type="hidden" name="s_dist">
            <input id="address"  type="hidden" name="buy[address]">
    </form>
    </div> -->
  <?php endif?>
  </div>
  <div class="main-title font-orange font-bold"><?=$item->title?></div>
  <div class="main-name font-bold"><?=$item->subtitle?></div>
  <ul class="main-list">
   <!--  <li class="main-li">
      <div class="list-title">&nbsp;&nbsp;商家信息</div>
      <div class="line"></div>
      <ul class="list-detail">
        <li>商家名称：<?=$shop['name']?></li>
        <li>商家地址：<?=$shop['url']?></li>
        <li>服务电话：<a href="tel://<?=$shop['tel']?>" style="color: #000;"><?=$shop['tel']?></a></li>
      </ul>
    </li> -->
    <li class="main-li">
      <div class="list-title">&nbsp;&nbsp;商品详情</div>
      <div class="line"></div>
      <div class="event-detail">
        <?=$item->desc?>
      </div>
    </li>
    <li class="main-li">
      <div class="list-title">&nbsp;&nbsp;砍价规则</div>
      <div class="line"></div>
      <div class="event-detail">
        <?=$item->rule?>
      </div>
    </li>
    <li class="main-li" style="padding-bottom: 0;">
      <div class="list-title">&nbsp;&nbsp;砍价风云榜</div>
      <div class="line"></div>
      <div class="rank-bar">
        <a class="active" id="button-rank-kan">帮砍榜</a><a id="button-rank-event" style="border-radius: 0 4px 4px 0;">砍价榜</a>
      </div>
      <ul class="rank-list rank-kan">
        <?php if ($cut):?>
          <?php foreach ($cut as $k => $v):?>
            <li>
              <img class="rank-avator" src="<?=$v->qrcode->headimgurl?>">
              <div class="rank-name"><?=$v->qrcode->nickname?></div>
              <div class="rank-money"><?=$v->money/100?>元</div>
            </li>
          <?php endforeach?>
        <?php endif?>
        <li style="border:0">
          <div class="rank-nomore">暂无更多</div>
        </li>
      </ul>
      <ul class="rank-list rank-event" style="display:none;">
        <?php if ($join):?>
          <?php foreach ($join as $k => $v):?>
            <li>
              <img class="rank-avator" src="<?=$v->qrcode->headimgurl?>">
              <div class="rank-name"><?=$v->qrcode->nickname?></div>
              <div class="rank-money"><?=($item->old_price - $v->now_price)/100?>元</div>
            </li>
          <?php endforeach?>
        <?php endif?>
        <li style="border:0">
          <div class="rank-nomore">暂无更多</div>
        </li>
      </ul>
    </li>
  </ul>
  <div class="copyright">神码浮云提供技术支持</div>
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
      'chooseWXPay',
      'onMenuShareTimeline',
      'onMenuShareAppMessage'
      ]
  });
  wx.ready(function () {
    wx.checkJsApi({
      jsApiList: [
          'checkJsApi',
          'chooseWXPay',
          'onMenuShareTimeline',
          'onMenuShareAppMessage'
      ],
      success: function (res) {
        console.log(res);
      }
    });
    wx.onMenuShareTimeline({//朋友圈分享
      // title: "<?=$item->title?>", // 分享标题
      title: "帮我砍一刀", // 分享标题
      link: "http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_userinfo/<?=$event->bid?>/kjb/kanpage?eid=<?=$event->id?>", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwtkjb/images/item/<?=$item->id?>?time=<?=time()?>",
        success: function () {
        // 用户确认分享后执行的回调函数
        // alert('分享成功');
      // $.ajax({
      //   url: '/qwtkjb/share',
      //   type: 'post',
      //   dataType: 'json',
      //   data: {share: <?=$event->id?>},
      // })
      // .done(function(res) {
      //   console.log(res);
      // })
      // .fail(function() {
      //   console.log("error");
      // })
      // .always(function() {
      //   console.log("complete");
      // });
        },
        cancel: function () {
         alert('用户取消');
        // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({//分享给朋友
      // title: "<?=$item->title?>", // 分享标题
      title: "帮我砍一刀", // 分享标题
      desc: "距离最低价还差<?=($event->now_price - $item->price)/100?>元，还可以砍<?=$item->cut_num - $cut_count?>刀，快帮我一把", // 分享描述
      link: "http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_userinfo/<?=$event->bid?>/kjb/kanpage?eid=<?=$event->id?>", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwtkjb/images/item/<?=$item->id?>?time=<?=time()?>", // 分享图标
      success: function () {
      // 用户确认分享后执行的回调函数
      // alert('分享成功');
      // $.ajax({
      //   url: '/qwtkjb/share',
      //   type: 'post',
      //   dataType: 'json',
      //   data: {share: <?=$event->id?>},
      // })
      // .done(function(res) {
      //   console.log(res);
      // })
      // .fail(function() {
      //   console.log("error");
      // })
      // .always(function() {
      //   console.log("complete");
      // });

      },
      cancel: function () {
       alert('用户取消');
      // 用户取消分享后执行的回调函数
      }
      });
   })
      $('#button-rank-kan').click(function(){
        $('.rank-kan').show();
        $('.rank-event').hide();
        $('#button-rank-kan').addClass('active');
        $('#button-rank-event').removeClass('active');
      })
      $('#button-rank-event').click(function(){
        $('.rank-kan').hide();
        $('.rank-event').show();
        $('#button-rank-kan').removeClass('active');
        $('#button-rank-event').addClass('active');
      })
      $('.share').click(function(){
        $('.shadowshare').show();
      })
      $('.close').click(function(){
        $('.shadow').hide();
      })
      $(".cutnow").click(function(){
        var zikan = $(this).data('knife');
        $('.shadowfade').show();
        if (zikan==1) {
          $.ajax({
              url: '/qwtkjb/kanpage/<?=$event->id?>',
              type: 'post',
              dataType: 'json',
              data: {
                cut: 1,
                self: 1
              },
          })
          .done(function(res) {
        $('.shadowfade').hide();
              console.log("success");
              if (res.state == 1) {
                $('.cuttext').html(res.content);
                $('.shadowcut').show();
              }else{
                $('.cuttext').html(res.error);
                $('.shadowcut').show();
              }
          })
          .fail(function() {
              console.log("error");
          })
          .always(function() {
              console.log("complete");
          });
        }else{
          $.ajax({
              url: '/qwtkjb/kanpage/<?=$event->id?>',
              type: 'post',
              dataType: 'json',
              data: {cut: 1},
          })
          .done(function(res) {
        $('.shadowfade').hide();
              console.log("success");
              if (res.state == 1) {
                $('.cuttext').html(res.content);
                $('.shadowcut').show();
              }else{
                $('.cuttext').html(res.error);
                $('.shadowcut').show();
              }
          })
          .fail(function() {
              console.log("error");
          })
          .always(function() {
              console.log("complete");
          });
        }
      })
      $('.buynow').click(function(){
        $('.paybox').show();
      })

    $('.add,.edit').click(function() {
        wx.openAddress({
            success: function (res) {
                $('#name').val(res.userName);
                $('#tel').val(res.telNumber);
                $('.prov').val(res.provinceName);
                $('.city').val(res.cityName);
                $('.dist').val(res.countryName);
                $('#address').val(res.provinceName+res.cityName+res.countryName+res.detailInfo);
                var userName = res.userName; // 收货人姓名
                // var postalCode = res.postalCode; // 邮编
                var provinceName = res.provinceName; // 国标收货地址第一级地址（省）
                var cityName = res.cityName; // 国标收货地址第二级地址（市）
                var countryName = res.countryName; // 国标收货地址第三级地址（国家）
                var detailInfo = res.detailInfo; // 详细收货地址信息
                // var nationalCode = res.nationalCode; // 收货地址国家码
                var telNumber = res.telNumber; // 收货人手机号码
                if(userName&&provinceName&&cityName&&detailInfo&&telNumber){
                    $('.name').text('收货人：'+userName);
                    $('.phone').text('电话：'+telNumber);
                    $('.location').text('地址：'+provinceName+cityName+countryName+detailInfo);
                    $('.add').css({
                        'display': 'none'
                    });
                    $('.edit').css({
                        'display': 'block'
                    });
                    $('.button-buy').show();
                }
            }
        });
    });
$('.button-buy').click(function(){
  $.ajax({
      url: '/qwtkjb/kanpage/<?=$event->id?>',
      type: 'post',
      dataType: 'json',
      data: {wxpay: 1,
            data:{
                pay_money:$('#money').val(),
                name:$('#name').val(),
                tel:$('#tel').val(),
                address:$('#address').val(),
            }
      },
  })
  .done(function(res) {
      console.log(res);
      console.log("success");
      if(res.error){
          alert(res.error);
          return;
      }else{
        // $('#money').val(res.pay_money)
          wx.chooseWXPay({
              timestamp: res.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
              nonceStr: res.nonceStr, // 支付签名随机串，不长于 32 位
              package: res.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
              signType: res.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
              paySign: res.paySign, // 支付签名
              success: function (res2) {
                  // 支付成功后的回调函数
                  // a = 1
                  // $.ajax({
                  //     url: '/qwta/wait_order',
                  //     type: 'post',
                  //     dataType: 'json',
                  //     data: {wait_id: res.wait_id},
                  // })
                  // .done(function() {
                  //     console.log("success");

                  //     var form = document.getElementById("newForm");
                  //     form.submit();
                  // })
                  // .fail(function() {
                  //     console.log("error");
                  // })
                  // .always(function() {
                  //     console.log("complete");
                  // });
                  //购买之后跳转页面
                  window.location.href = "/qwtkjb/order/"+res.oid;
              }
          });
      }
  })
  .fail(function(res) {
      console.log(JSON.stringify(res));
      console.log("error");
      return false;
  })
  .always(function() {
      console.log("complete");
      return false;
  });
})
<?php if (!$result['time']=='forever'):?>
window.onload = function(){
   var endTime = <?=$result['endtime']?>; // 最终时间
    var second = endTime - <?=$result['nowtime']?>;
   setInterval(clock,1000); // 开启定时器
   function clock(){
    // 用将来的时间毫秒 - 现在的毫秒 / 1000 得到的 还剩下的秒 可能处不断 取整
    // console.log(second);
     // 一小时 3600 秒
    if (second >= 0) {
    // second / 3600 一共的小时数 /24 天数
    var d = parseInt(second / 3600 / 24); //天数
    var leftsecond = second - 24 * 3600 * d;
    //console.log(d);
    var h = parseInt(leftsecond / 3600) // 小时
    leftsecond = leftsecond - 3600 * h;
    // console.log(h);
    var m = parseInt(leftsecond / 60);
    leftsecond = leftsecond - 60 * m;
    //console.log(m);
    var s = parseInt(leftsecond); // 当前的秒
    console.log(s);
    /* if(d<10)
    {
     d = "0" + d;
    }*/
    h<10 ? h="0"+h : h;
    m<10 ? m="0"+m : m;
    s<10 ? s="0"+s : s;
    $('.day-text').text(d);
    $('.time-hour').text(h);
    $('.time-minute').text(m);
    $('.time-second').text(s);
    second = second - 1;

    };

   }
  }
  <?php endif?>
  $('.buyhint').click(function(){
    $('.shadowhint').show();
  })

  $('.hintcancel').click(function(){
    $('.shadowhint').hide();
  })
    </script>
  </body>
</html>
