<?php
//打卡宝默认文案
$config['dka']['cdn'] = 'http://cdn.jfb.smfyun.com';
$config['dka']['score'] = '积分';
$config['dka']['score2'] = '积分'; //兑换页面显示单位

$config['dka']['text_send']     = '正在为您生成海报，大约需要等待6秒钟，请稍等片刻。'; //发送海报
$config['dka']['text_send2']    = '正在为您发送海报，请稍候。';

$config['dka']['text_goal']     = '您的朋友「%s」成为了您的支持者！您已获得了相应的积分奖励，请注意查收。';
$config['dka']['text_goal2']    = '您的朋友「%s」又获得了一个新的支持者！';
$config['dka']['text_risk']    = '您的账号存在安全风险，暂时无法兑换奖品。';

$config['dka']['goal0'] = 10; //关注奖励
$config['dka']['goal'] = 10; //直接推荐
$config['dka']['goal2'] = 2; //间接推荐

$config['dka']['rank'] = 10; //排行榜

$config['dka']['px_qrcode_left']    = 170;
$config['dka']['px_qrcode_top']     = 450;
$config['dka']['px_qrcode_width']   = 300;
$config['dka']['px_qrcode_height']  = 300;

$config['dka']['px_head_left']    = 270;
$config['dka']['px_head_top']     = 162;
$config['dka']['px_head_width']   = 100;
$config['dka']['px_head_height']  = 100;

$config['dka']['ticket_lifetime']  = 7; //海报有效期（最长7天）

$config['dka']['copyright'] = '打卡宝@神码浮云';

//分销宝默认文案
$config['dka']['text_fxbsend']    = '您的海报将于 {TIME} 失效，过期后请点击「生成海报」菜单重新获取哦。正在为您发送海报，请稍候...'; //发送海报

$config['dka']['fxb'] = '分销';
$config['dka']['title1'] = '品牌大使'; //一级
$config['dka']['title2'] = '粉丝'; //二级

$config['dka']['money_out'] = 100; //提现门槛.分
$config['dka']['money_out_buy'] = 1990; //提现门槛.消费金额.分

$config['dka']['money_init'] = 10; //关注奖励分
$config['dka']['money_scan1'] = 1; //一级扫码奖励分
$config['dka']['money_scan2'] = 0; //二级扫码奖励分

$config['dka']['money0'] = 5; //本级佣金比例 %
$config['dka']['money1'] = 10; //一级佣金比例 %
$config['dka']['money2'] = 5; //二级佣金比例 %

$config['dka']['order_from'] = 10; //订单金额参与分销的门槛(元)
$config['dka']['order_day'] = 10; //交易后可结算佣金天数

$conifg['dka']['haibao_needpay'] = 0; //判断是否有交易才允许生成海报？
$config['dka']['fxb_ticket_lifetime']  = 7; //海报有效期（最长7天）

//活动说明
$config['dka']['fxb_desc'] = '男人袜招募健康大使了，即刻起购买任意一款产品（最低19.9元）即可成为健康大使，拥有专属海报，分享还能赚钱哦~<a href="http://nanrenwa.youzan.com/">【马上购买】</a>已在您账户存放了0.88元，满1元即可提现哦~';

//提现说明
$config['dka']['fxb_money_desc'] = <<<EOF
1. 须满足 { ❶佣金满1元 ❷在微商城购买过任意产品并且成功交易 } 两个条件方可提现；
2. 可提现结算周期为"T+10"，即指交易当天后的第 10 个交易日（节假日、周末不计在内）；
3. 您可自主提现，24 小时内转到微信零钱；
4. 男人袜拥有本活动最终解释权。
EOF;

//收益模板消息：收益通知->OPENTM206596805
$config['dka']['msg_score_tpl'] = '';

//分销宝默认文案
$config['dka']['text_fxbsend']    = '您的海报将于 {TIME} 失效，过期后请点击「生成海报」菜单重新获取哦。正在为您发送海报，请稍候...'; //发送海报

$config['dka']['fxb'] = '分销';
$config['dka']['title1'] = '品牌大使'; //一级
$config['dka']['title2'] = '粉丝'; //二级

$config['dka']['money_out'] = 100; //提现门槛.分
$config['dka']['money_out_buy'] = 1990; //提现门槛.消费金额.分

$config['dka']['money_init'] = 10; //关注奖励分
$config['dka']['money_scan1'] = 1; //一级扫码奖励分
$config['dka']['money_scan2'] = 0; //二级扫码奖励分

$config['dka']['money0'] = 5; //本级佣金比例 %
$config['dka']['money1'] = 10; //一级佣金比例 %
$config['dka']['money2'] = 5; //二级佣金比例 %

$config['dka']['order_from'] = 10; //订单金额参与分销的门槛(元)
$config['dka']['order_day'] = 10; //交易后可结算佣金天数

$conifg['dka']['haibao_needpay'] = 0; //判断是否有交易才允许生成海报？
$config['dka']['fxb_ticket_lifetime']  = 7; //海报有效期（最长7天）

//活动说明
$config['dka']['fxb_desc'] = '男人袜招募健康大使了，即刻起购买任意一款产品（最低19.9元）即可成为健康大使，拥有专属海报，分享还能赚钱哦~<a href="http://nanrenwa.youzan.com/">【马上购买】</a>已在您账户存放了0.88元，满1元即可提现哦~';

//提现说明
$config['dka']['fxb_money_desc'] = <<<EOF
1. 须满足 { ❶佣金满1元 ❷在微商城购买过任意产品并且成功交易 } 两个条件方可提现；
2. 可提现结算周期为"T+10"，即指交易当天后的第 10 个交易日（节假日、周末不计在内）；
3. 您可自主提现，24 小时内转到微信零钱；
4. 男人袜拥有本活动最终解释权。
EOF;

//收益模板消息：收益通知->OPENTM206596805
$config['dka']['msg_score_tpl'] = '';
