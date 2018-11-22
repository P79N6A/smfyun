<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>待付款的订单</title>
	<link rel="stylesheet" type="text/css" href="../../sqb/css/ui.css">
	<link href="../../sqb/favicon.ico" type="image/x-icon" rel="icon">
	<link href="../../sqb/iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <link href="../../sqb/css/layout.min.css" rel="stylesheet" />
    <link href="../../sqb/css/scs.min.css" rel="stylesheet" />
	<style type="text/css">
	.avator{
		height: 30px;
		position: absolute;
		right: 45px;
		border-radius: 15px;
	}
	#myAddrs{
    color: #80a049;
    padding-right: 30px;
	}
 textarea{
  width: 100%;
  height: 200px;
 }
.add{
    background-color: #fff;
    position: relative;
    height: 60px;
    border-bottom: 1px solid #f4f4f4;
}
.addimg{
    float: left;
    /*width: 40px;*/
    height: 100%;
    padding: 10px;
}
.addtext{
    line-height: 60px;
    color: #666666;
    font-size: 14px;
    float: left;
}
.goin{
    float: right;
    /*width: 20px;*/
    height: 100%;
    padding: 20px;
}
.edit{
    background-color: #fff;
    position: relative;
    border-bottom: 1px solid #f4f4f4;
}
.position{
    width: 9%;
    /*height: 15px;*/
    padding-left: 3%;
    padding-right: 2%;
    padding-top: 15px;
    display: inline-block;
    float: left;
}
.address{
    display: inline-block;
    width: 84%;
    padding: 10px 0 10px 0;
}
.basic{
    font-size: 14px;
    color: #333;
}
.phone{
    float: right;
}
.location{
    margin-top: 10px;
    font-size: 12px;
    color: #999;
}
.goin2{
    float: right;
    width: 6%;
    /*height: 10px;*/
    padding-left: 1%;
    padding-right: 2%;
    padding-top: 30px;
}
  .footer{
   position: fixed;
   bottom: 0;
   width: 100%;
   height: 5em;
   text-align: center;
   font-size: 12px;
   background-color: #fff;
  }
  .menu-icon{
   float: left;
   width: 5em;
   /*height: 2em;*/
   color: #949494;
   padding: 3em 0 0 0;
   line-height: 2em;
   border-right: 1px solid #e1e1e1;
   text-decoration: none;
  }
  .menu-button{
   float: left;
   width: calc(100% - 7.5em - 3px);
   height: 2.5em;
   line-height: 2.5em;
   /*background-color: #999;*/
   background-color: #ff6501;
   color: #fff;
   font-weight: bold;
   padding-bottom: 3em;
   font-size: 200%;
   text-decoration: none;
  }
  .menu-shop{
    background: url(../../qwt/kjb/shop.png) no-repeat 1.5em .5em;
    background-size: 2em 2em;
  }
  .menu-mine{
    background: url(../../qwt/kjb/user.png) no-repeat 1.5em .5em;
    background-size: 2em 2em;
  }
  .menu-call{
    background: url(../../qwt/kjb/phone.png) no-repeat 1.5em .5em;
    background-size: 2em 2em;
  }
  .grey{
    background-color: gray;
  }
	</style>
</head>
<body>
	<div class="aui-container" style="padding-bottom:4em;">
		<div class="aui-page">
			<div class="header header-color">
				<div class="header-background"></div>
				<div class="toolbar statusbar-padding">
					<div class="header-title">
						<div class="title"><?=$item->name?></div>
					</div>
				</div>
			</div>
			<div class="aui-text-top aui-l-content aui-l-content-clear">
				<div class="devider b-line"></div>
                <div class="fade-main aui-menu-list aui-menu-list-clear">
                    <ul>
                        <div class="devider b-line"></div>
                        <li class="b-line">
<form method="post" id="newForm" style="width:100%;">
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
            <input id="name"  type="hidden" name="buy[name]" >
            <input id="tel" type="hidden" name="buy[tel]" >
            <input id="money" type="hidden" name="buy[pay_money]" >
            <input id="prov" class='prov' type="hidden" name="s_province" >
            <input id="city" class='city' type="hidden" name="s_city" >
            <input id="dist" class='dist' type="hidden" name="s_dist">
            <input id="address"  type="hidden" name="buy[address]">
    </form>
                        </li>
                        <li class="b-line" style="display:block;">
                            <img src="/qwtkjb/images/item/<?=$item->id?>.jpg" style="width:100%">
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3 style="min-width:80px;">商品名称</h3>
                                <span id="text-tel" style="right:16px;"><?=$item->name?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="addr">
                                <h3 style="color:#0cc0cc;">原价</h3>
                                <span id="text-tel" style="right:16px;">￥<?=$item->old_price/100?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>现价</h3>
                                <span id="text-tel" style="right:16px;">￥<?=$event->now_price/100?></span>
                            </a>
                        </li>
                    </ul>
                </div>
			</div>
		</div>
	</div>
<div class='footer'>
 <a class="menu-icon menu-shop" href="/qwtkjb/list">其他商品</a><a href="/qwtkjb/myitem" class="menu-icon menu-mine">我的</a><a class="menu-icon menu-call" href="tel://<?=$shop['tel']?>">联系商家</a><?php if ($event->now_price==0):?><a class="menu-button menu-main share grey button-get">立刻领取</a><?php else:?><a class="menu-button menu-main share grey button-buy">立刻付款</a><?php endif?>
</div>
</body>
    <script src="../../sqb/js/jquery.min.js"></script>
    <script src="../../sqb/js/jquery.scs.min.js"></script>
    <script src="../../sqb/js/CNAddrArr.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<script type="text/javascript">
    var status = 0;
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
                    $('.button-buy').removeClass('grey');
                    $('.button-get').removeClass('grey');
                    status=1;
                }
            }
        });
    });
$('.button-get').click(function(){
    if (status==0) {
        console.log('没填地址');
        return false;
    };
  $.ajax({
      url: '/qwtkjb/checkout/<?=$order->id?>',
      type: 'post',
      dataType: 'json',
      data: {freeget: 1,
            data:{
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
        window.location.href = "/qwtkjb/myorder";
      }
  })
  .fail(function(res) {
      // console.log(JSON.stringify(res));
      console.log("error");
      return false;
  })
  .always(function() {
      console.log("complete");
      return false;
  });

})
$('.button-buy').click(function(){
    if (status==0) {
        console.log('没填地址');
        return false;
    };
  $.ajax({
      url: '/qwtkjb/checkout/<?=$order->id?>',
      type: 'post',
      dataType: 'json',
      data: {wxpay: 1,
            data:{
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
                  // window.location.href = "/qwtkjb/order/"+res.oid;
                  window.location.href = "/qwtkjb/myorder";
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
	</script>
</html>
