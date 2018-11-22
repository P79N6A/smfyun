<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>订单详情</title>
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
	</style>
</head>
<body>
	<div class="aui-container">
		<div class="aui-page">
			<div class="header header-color">
				<div class="header-background"></div>
				<div class="toolbar statusbar-padding">
					<div class="header-title">
						<div class="title"><?=$order->item->name?></div>
					</div>
				</div>
			</div>
			<div class="aui-text-top aui-l-content aui-l-content-clear">
				<div class="devider b-line"></div>
                <div class="fade-main aui-menu-list aui-menu-list-clear">
                    <ul>
                        <div class="devider b-line"></div>
                        <li class="b-line">
                            <a class="link-input" data-name="name">
                                <h3>收货人</h3>
                                <span id="text-name" style="right:16px;"><?=$order->name?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>联系电话</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->tel?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3 style="min-width:80px;">收货地址</h3>
                                <span id="text-tel" style="right:16px;position:inherit;"><?=$order->address?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>付款时间</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->pay_time==0?date('Y-m-d H:i:s',$order->lastupdate):date('Y-m-d H:i:s',$order->pay_time)?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="addr">
                                <h3 style="color:#0cc0cc;"><?=$order->state==1?'已发货，请注意签收':'请耐心等待管理员发货'?></h3>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>快递单位</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->shiptype?$order->shiptype:'无'?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>快递单号</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->shipcode?$order->shipcode:'无'?></span>
                            </a>
                        </li>
                    </ul>
                </div>
			</div>
		</div>
	</div>
</body>
    <script src="../../sqb/js/jquery.min.js"></script>
    <script src="../../sqb/js/jquery.scs.min.js"></script>
    <script src="../../sqb/js/CNAddrArr.min.js"></script>
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
	</script>
</html>
