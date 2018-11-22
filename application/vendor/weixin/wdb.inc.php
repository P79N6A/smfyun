<?php
$money = 100;


$config['img_top'] = '/hongbao/top.jpg';
$config['max'] = 100;

//关注后自动回复 - 用于关注后红包
// $config['wdb']['reply'] = '亲爱的你终于来啦~欢迎关注COLORMAD魅缤纷，我家的甲油会呼吸哟~~<a href="http://wap.koudaitong.com/v2/showcase/coupon/fetch?alias=gs48iz68">领取优惠券戳这里》》》</a>回复你的生日如7月5日即回复“0705”，测一下你的2015色彩运势和转运搭配吧~~~';
$config['wdb']['qrcode'] = 'gQHv7zoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0owTXpxOXZsWHFzbmJkdGY4MjhqAAIEVb8SVQMEAAAAAA=='; //红包二维码 Ticket

//口令红包触发关键字
$config['wdb']['keyword'] = '海报';

//口令红包文案
$config['wdb']['kouling'] = '输入口令领取现金红包：%s';
$config['wdb']['not'] = '红包口令不正确';

//领到红包的概率: 100 为 100%、1 为 1% 有红包，领不到红包的时候随机回复 $config['wdb']['success2'][];
$config['wdb']['rate'] = 50;
$config['wdb']['success2'][] = '您领到了优惠券1 <a href="http://www.nanrenwa.com/">点击这里领取</a>';

//通用红包文案
$config['wdb']['success'] = "领取成功"; //领取成功
$config['wdb']['got'] = "您领过了"; //重复领取
$config['wdb']['limit'] = "红包发完了"; //发完了
$config['wdb']['timelimit'] = "0-8 点人家要休息，不发红包哦"; //时间不对

//语音红包配置
$config['yuyin']['NOMATCH'] = "死鬼，伦家都听不清，「%s」是神马？？为什么要这么羞涩胆怯呢？"; //未匹配回复
//语音红包触发关键字
$config['yuyin']['哈哈'] = 'MONEY'; //触发红包
//语音红包其它关键字触发回复
$config['yuyin']['我爱你'] = "呦，这么大声儿，是要和伦家私定终身么？陈老板已经给我准备好了嫁妆送给你，快快戳这里领取：<a href=\"http://kdt.im/N8yQCK0K\">http://kdt.im/N8yQCK0K</a> \r能领到多大礼，就看你有多爱我啦~嚒嚒[亲亲]";
$config['yuyin']['你好'] = '你妹';


//微代言默认文案
$config['wdb']['cdn'] = 'http://cdn.jfb.smfyun.com';
$config['wdb']['score'] = '积分';
$config['wdb']['score2'] = '积分'; //兑换页面显示单位

$config['wdb']['text_send']     = '正在为您生成海报，大约需要等待6秒钟，请稍等片刻。'; //发送海报
$config['wdb']['text_send2']    = '正在为您发送海报，请稍候。';

$config['wdb']['text_goal']     = '您的朋友「%s」成为了您的支持者！您已获得了相应的积分奖励，请注意查收。';
$config['wdb']['text_goal2']    = '您的朋友「%s」又获得了一个新的支持者！';
$config['wdb']['text_goal3']    = '恭喜您成为了「%s」的支持者~';
$config['wdb']['text_goal4']    = '您已经是「%s」的支持者了，点击生成海报让更多小伙伴加入吧';
$config['wdb']['text_risk']    = '您的账号存在安全风险，暂时无法兑换奖品。';

$config['wdb']['goal0'] = 10; //关注奖励
$config['wdb']['goal'] = 10; //直接推荐
$config['wdb']['goal2'] = 2; //间接推荐

$config['wdb']['rank'] = 10; //排行榜

$config['wdb']['px_qrcode_left']    = 170;
$config['wdb']['px_qrcode_top']     = 450;
$config['wdb']['px_qrcode_width']   = 300;
$config['wdb']['px_qrcode_height']  = 300;

$config['wdb']['px_head_left']    = 270;
$config['wdb']['px_head_top']     = 162;
$config['wdb']['px_head_width']   = 100;
$config['wdb']['px_head_height']  = 100;

$config['wdb']['ticket_lifetime']  = 7; //海报有效期（最长7天）

$config['wdb']['copyright'] = '积分宝@神码浮云';

//微订宝默认文案
$config['wdb']['text_fxbsend']    = '您的海报将于 {TIME} 失效，过期后请点击「生成海报」菜单重新获取哦。正在为您发送海报，请稍候...'; //发送海报

$config['wdb']['fxb'] = '分销';
$config['wdb']['title1'] = '品牌大使'; //一级
$config['wdb']['title2'] = '粉丝'; //二级

$config['wdb']['money_out'] = 100; //提现门槛.分
$config['wdb']['money_out_buy'] = 1990; //提现门槛.消费金额.分

$config['wdb']['money_init'] = 10; //关注奖励分
$config['wdb']['money_scan1'] = 1; //一级扫码奖励分
$config['wdb']['money_scan2'] = 0; //二级扫码奖励分

$config['wdb']['money0'] = 5; //本级佣金比例 %
$config['wdb']['money1'] = 10; //一级佣金比例 %
$config['wdb']['money2'] = 5; //二级佣金比例 %

$config['wdb']['order_from'] = 10; //订单金额参与分销的门槛(元)
$config['wdb']['order_day'] = 10; //交易后可结算佣金天数

$conifg['wdb']['haibao_needpay'] = 0; //判断是否有交易才允许生成海报？
$config['wdb']['fxb_ticket_lifetime']  = 7; //海报有效期（最长7天）

$config['wdb']['needtext']  = '小主还未获取生成海报的权利哟';//用户未满足生成海报条件提示文案
//活动说明
$config['wdb']['fxb_desc'] = '男人袜招募健康大使了，即刻起购买任意一款产品（最低19.9元）即可成为健康大使，拥有专属海报，分享还能赚钱哦~<a href="http://nanrenwa.youzan.com/">【马上购买】</a>已在您账户存放了0.88元，满1元即可提现哦~';

//提现说明
$config['wdb']['fxb_money_desc'] = <<<EOF
1. 须满足 { ❶佣金满1元 ❷在微商城购买过任意产品并且成功交易 } 两个条件方可提现；
2. 可提现结算周期为"T+10"，即指交易当天后的第 10 个交易日（节假日、周末不计在内）；
3. 您可自主提现，24 小时内转到微信零钱；
4. 男人袜拥有本活动最终解释权。
EOF;

//收益模板消息：收益通知->OPENTM206596805
$config['wdb']['msg_score_tpl'] = '';

//微订宝默认文案
$config['wdb']['text_fxbsend']    = '您的海报将于 {TIME} 失效，过期后请点击「生成海报」菜单重新获取哦。正在为您发送海报，请稍候...'; //发送海报
$config['wdb']['key_menu'] = '免费领';
// $config['wdb']['key_qrcode'] = '生成海报';
// $config['wdb']['key_score'] = '积分查询';
// $config['wdb']['key_item'] = '积分兑换';
// $config['wdb']['key_top'] = '积分排行';

$config['wdb']['fxb'] = '分销';
$config['wdb']['title1'] = '品牌大使'; //一级
$config['wdb']['title2'] = '粉丝'; //二级

$config['wdb']['money_out'] = 100; //提现门槛.分
$config['wdb']['money_out_buy'] = 1990; //提现门槛.消费金额.分

$config['wdb']['money_init'] = 10; //关注奖励分
$config['wdb']['money_scan1'] = 1; //一级扫码奖励分
$config['wdb']['money_scan2'] = 0; //二级扫码奖励分

$config['wdb']['money0'] = 5; //本级佣金比例 %
$config['wdb']['money1'] = 10; //一级佣金比例 %
$config['wdb']['money2'] = 5; //二级佣金比例 %

$config['wdb']['order_from'] = 10; //订单金额参与分销的门槛(元)
$config['wdb']['order_day'] = 10; //交易后可结算佣金天数

$conifg['wdb']['haibao_needpay'] = 0; //判断是否有交易才允许生成海报？
$config['wdb']['fxb_ticket_lifetime']  = 7; //海报有效期（最长7天）

//活动说明
$config['wdb']['fxb_desc'] = '男人袜招募健康大使了，即刻起购买任意一款产品（最低19.9元）即可成为健康大使，拥有专属海报，分享还能赚钱哦~<a href="http://nanrenwa.youzan.com/">【马上购买】</a>已在您账户存放了0.88元，满1元即可提现哦~';

//提现说明
$config['wdb']['fxb_money_desc'] = <<<EOF
1. 须满足 { ❶佣金满1元 ❷在微商城购买过任意产品并且成功交易 } 两个条件方可提现；
2. 可提现结算周期为"T+10"，即指交易当天后的第 10 个交易日（节假日、周末不计在内）；
3. 您可自主提现，24 小时内转到微信零钱；
4. 男人袜拥有本活动最终解释权。
EOF;

//收益模板消息：收益通知->OPENTM206596805
$config['wdb']['msg_score_tpl'] = '';
