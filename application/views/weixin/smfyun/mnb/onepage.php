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
			.onep{
				padding: 12px 15px 13px 0;
				margin-left: 15px;
				border-bottom: 1px solid #efefef;
				text-align: left;
			}
			.mod-list .list-btn {
    font-size: 12px;
    height: 24px;
    display: block;
    text-align: center;
    color: #B3B3B3;
}
.list-btn .ico-turn {
    position: relative;
    display: block;
    width: 7px;
    height: 24px;
}
.fg-arrow:before {
    position: absolute;
    top: 50%;
    right: 2px;
    margin-top: -5px;
    display: inline-block;
    width: 7px;
    height: 7px;
    content: ".";
    font-size: 0;
    border-top: 2px solid #dadada;
    border-right: 2px solid #dadada;
    -webkit-transform: rotate(45deg);
    transform: rotate(45deg);
}
.searchinput{
display: inline-block;
width: 70%;
    line-height: 25px;
    padding: 5px;
    border: 0;
    border-radius: 5px 0 0 5px;
}
.searchbutton{
	display: inline-block;
	width: 20%;
    line-height: 25px;
    padding: 5px;
    border: 0;
    border-radius: 0 5px 5px 0;
    background-color: #1a79ff;
    color: #fff;
    font-weight: bold;
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
	<body style="background-color:#f1eff6;">
		<div id="page" style="padding:45px 0 0 0;">
			<div class="header Fixed" style="background-color:#f1eff6;color:#6b6a6f;font-size:12px;height:45px;font-weight:400;padding:0 10px;">
				<!-- <a href="#menu"><span></span></a> -->
				<form method="post" style="line-height:35px;padding:5px 0;"><input class="searchinput" name="keyword" type="text" placeholder="搜索" value="<?=$result['keyword']?>"><button type="submit" class="searchbutton">搜索</button></form>

			</div>
			<div class="content" id="content" style="padding:0;">
				<section style="padding:0;background-color:#fff;<?=$result['keyword']?'':'display:none'?>">
				<?php if ($faqs[0]->id):?>
					<?php foreach ($faqs as $k => $v):?>
						<a style="text-decoration:none;font-size:16px;font-weight:400;color:#000;" href="/qwtmnb/detail/<?=$v->id?>"><div class="onep"><p style="text-align:left;padding:0;line-height:25px;margin:0;display:inline-block;width:100%;"><?=$v->title?><i class="list-btn" href="/qwtmnb/detail/<?=$v->id?>"><span class="fg-arrow ico-turn" style="float:right;display:inline-block;"></span></i></p></div></a>
				<?php endforeach?>
			<?php else:?>
				<div class="onep"><p style="text-align:left;padding:0;line-height:25px;margin:0;display:inline-block;width:100%;font-size:16px;font-weight:400;color:#000;">未找到相关问题</p></div>
			<?php endif?>
				</section>
			<!--
			<?php foreach ($usertype as $k => $v):?>
				<section id="<?=$v->id?>" style="padding:0;background-color:#fff;<?=$k==$first?'':'display:none'?>">
					<?php
					$userfaq = ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('tid','=',$v->id)->find_all();
					if ($userfaq[0]->id):?>
					<?php foreach ($userfaq as $m => $n):?>
					<div class="onep"><p style="text-align:left;padding:0;line-height:25px;margin:0;display:inline-block;width:100%;"><a style="text-decoration:none;font-size:16px;font-weight:400;color:#000;" href="/qwtmnb/detail/<?=$n->id?>"><?=$n->title?></a><a class="list-btn" href="/qwtmnb/detail/<?=$n->id?>"><span class="fg-arrow ico-turn" style="float:right;display:inline-block;"></span></a></p></div>
				<?php endforeach?>
			<?php else:?>
				<div class="onep"><p style="text-align:left;padding:0;line-height:25px;margin:0;display:inline-block;width:100%;font-size:16px;font-weight:400;color:#000;">此分类暂无问题</p></div>
			<?php endif?>
				</section>
			<?php endforeach?> -->
			</div>
			<!-- <div class="footer Fixed" style="background-color:#ff901d;">
				Fixed footer :-)
			</div> -->
			<!-- <nav id="menu">
				<ul>
			<?php foreach ($usertype as $k => $v):?>
					<li><a class="linkto" data-name="<?=$v->name?>" data-id="<?=$v->id?>" href="#<?=$v->id?>"><?=$v->name?></a></li>
				<?php endforeach?>
				</ul>
			</nav> -->
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
		$('.linkto').click(function(){
			var id = $(this).data('id');
			var name = $(this).data('name');
			$('.typetitle').text(name);
			$('section').hide();
			$('#'+id).show();
		})
		</script>
	</body>
</html>
