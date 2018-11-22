<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'appid' => 'wxf3e29c3a2838100e',
	'appsecret' => 'd60b755f9362a16120d203c7cba34c26',

	'partnerid' => '1217729301',
	'partnerkey' => '9f48c9b6b2dfd7191a0e23e6f177c57f',

	'name' => '男人袜',
);






$q['绑定'] = '<a href="http://www.nanrenwa.com/weixin/bind/{FromUserName}">点我进行绑定操作</a>';
$q['下单'] = '<a href="http://www.nanrenwa.com/weixin/cod/{FromUserName}">点我快速下单</a>';

$q['菜单,帮助,M'] = '回复关键字使用自助服务
1.【绑定】绑定您的男人袜帐号
2.【快递】查询您最新订单的状态
3.【男人妹】呼叫妹子一对一服务

如果这里解决不了您的问题，欢迎随时来电骚扰 4006822806
更多服务还在开发中……';

$q['Hello2BizUser'] = '客官，您终于来了。。让人家等的好辛苦。。。';
$q['subscribe'] = '客官，您终于来了。。让人家等的好辛苦。。。';

//$q['活动'] = '【微信送福利】回复 1~100 之间的数字即可参与，允许精确到 1 位小数，每人只有一次机会。截止到 28 日晚 21 点，最接近所有数字平均数的亲，会获得男人袜加厚款 3 双。PS. 转发活动微博，可以增加获奖机率，信不信由你！';
$q['活动'] = '活动结束了哈。。下次有了我再通知亲。。';

$q['伯乐,亢焜,avenger'] = '麻麻最喜欢的人之一。。';
$q['春艳'] = '陈老板最喜欢的人之一。。';

$q['语音'] = '我还听不懂语音哦，@男人妹 正在努力中！你可以在微博上找我聊 @男人袜';
$q['/::D'] = '傻笑什么啊？一双@男人袜 塞到你嘴里！';
$q['偷笑,@P'] = '有什么好笑的？我说错了么？主人～～～有人欺负我！';
$q['/::B'][] = '傻样。。。';
$q['/::B'][] = '/::B /::B /::B';

//$q['快递'] = '男人袜合作的快递有中通、韵达、申通，快递不到的会发邮政小包，有指定快递请留言哈。快递一般要 2~4 天送达，邮政要慢一点了。。你懂的。。';
$q['客服,400'] = '男人袜微博和微信客服白天都在哈，如果不在的话可以打电话：4006822806，是免长途费的，不许骚扰客服妹子哦。';
$q['怎么卖,多少钱'] = '体验装 3 双 29 元，包年是 108 元，全部是包邮的。更详细的可以访问男人袜官网了解一下哈：http://nanrenwa.com';
$q['陈老板'][] = '我是老板娘，有事？';
$q['陈老板'][] = '陈老板只对美女有兴趣。。';
$q['内裤'] = '陈老板说了，世界末日前一定会上线的。。。';
$q['袜子,男袜,男人袜'] = '亲。妹子手工缝制的袜纸+陈老板狂野的签名+形似小尺子的12cm书签+充满情趣的2颗弹珠，男人袜四件套。客官，你要嘛？PS：男人袜官网www.nanrenwa.com，买袜子送妹子。童话里都是骗人的。。';
$q['贵了'] = '是你太穷了。。。亲';
$q['男人妹,男淫妹'][] = '亲。。我随时都在哈 /::$';
$q['男人妹,男淫妹'][] = '么么。。';
$q['机器,自动回复'][] = '有时候是真人了。。人家总要休息的嘛。。/::$';
$q['机器,自动回复'][] = '你见过会卖萌的机器人嘛。。/::$';
$q['微博'][] = '@男人袜 期待你的关注哦。。';

$q['BUY2'] = <<< EOF
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[news]]></MsgType>
	<ArticleCount>2</ArticleCount>
	<Articles>
		<item>
		<Title><![CDATA[标题一]]></Title>
		<Description><![CDATA[<h1>标题</h1><h2>副标题</h2><p>内容</p><address>结尾</address>中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国]]></Description>
		<PicUrl><![CDATA[http://www.nanrenwa.com/_img/10k.jpg]]></PicUrl>
		<Url><![CDATA[http://www.nanrenwa.com]]></Url>
		</item>

		<item>
		<Title><![CDATA[标题一]]></Title>
		<Description><![CDATA[<h1>标题</h1><h2>副标题</h2><p>内容</p><address>结尾</address>中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国中华人民共和国]]></Description>
		<PicUrl><![CDATA[http://www.nanrenwa.com/_img/10k.jpg]]></PicUrl>
		<Url><![CDATA[http://www.nanrenwa.com]]></Url>
		</item>
	<FuncFlag>0</FuncFlag>
</xml>
EOF;

return $q;

