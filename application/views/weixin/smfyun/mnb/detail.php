<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

		<title></title>

		<link type="text/css" rel="stylesheet" href="../../qwt/mnb/demo.css" />
		<link type="text/css" rel="stylesheet" href="../../qwt/mnb/jquery.mmenu.all.css" />

		<!-- for the one page layout -->
		<style type="text/css">
			section
			{
				border-top: 1px solid #ccc;
				padding: 150px 0 200px;
			}
			section:first-child
			{
				border-top: none;
				padding-top: 0;
			}
		</style>

		<!-- for the fixed header -->
		<style type="text/css">
			.header,
			.footer
			{
				position: fixed;
				left: 0;
				right: 0;
			}
			.header
			{
				top: 0;
			}
			.footer
			{
				bottom: 0;
			}
			@media (min-width: 800px) {
				.header a
				{
					display: none;
				}
			}
			section img{
				width: 100% !important;
				max-width: 100% !important;
			}
		</style>

		<script type="text/javascript" src="http://code.jquery.com/jquery-3.2.1.js"></script>
		<script type="text/javascript" src="http://cdn.jsdelivr.net/hammerjs/2.0.8/hammer.min.js"></script>

		<script type="text/javascript" src="../../qwt/mnb/jquery.mmenu.all.js"></script>
		<script type="text/javascript">
			$(function() {
				$('nav#menu').mmenu({
					drag 		: true,
					pageScroll 	: {
						scroll 		: true,
						update		: true
					},
					// extensions: {
					// 	'all': ['pagedim-white'],
					// 	'(min-width: 400px)' : ['pagedim-black']
					// },
					sidebar 	: {
						expanded 	: 800
					},
					navbar:{
						title:'问题分类'
					}
				});
			});
		</script>
	</head>
	<body style="background-color:#fff;">
		<div id="page" style="padding:65px 15px 15px 15px">
			<div class="header Fixed" style="background-color:#f1eff6;color:#6b6a6f;font-size:12px;height:45px;font-weight:400;">
				<a href="/qwtmnb/onepage"><span></span></a>
				问题详情
			</div>
			<div class="content" id="content" style="text-align:inherit;padding:0;">
				<section id="intro" style="background-color: rgb(255, 255, 255);color: rgb(51, 51, 51);font-family: arial;font-size: 16px;line-height:25.6px;text-align: justify;">
				<div style="margin-bottom: 10px;line-height: 1.4;font-weight: 400;color:#000;font-size: 24px;"><?=$faq->title?></div>
					<p style="font-size:17px;color:#8c8c8c;line-height:20px;margin-bottom:28px;"><?=date('Y-m-d H:i:s',$faq->createtime)?><span style="float:right"><?=$faq->type->name?></span></p>
					<?=$faq->comment?>
				</section>
			</div>
			<!-- <div class="footer Fixed" style="background-color:#ff901d;">
				Fixed footer :-)
			</div> -->
		</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
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
	</body>
</html>
