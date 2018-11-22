<?php
$money = 100;
$config['name'] = '男人袜';
$config['img_top'] = '/hongbao/top.jpg';
$config['max'] = 100;
$config['yty']['rank'] = 10; //排行榜
$config['yty']['copyright'] = '云天佑@神码浮云';
$config['yty']['scorename'] = '资产';//积分名称自定义
$config['yty']['title5'] = '收益';//收益名称自定义
$config['yty']['yty'] = '分销';
$config['yty']['title1'] = '品牌大使'; //一级
$config['yty']['title2'] = '粉丝'; //二级
$config['yty']['titlen3'] = '粉丝'; //三级
$config['yty']['title3'] = '总收益';
$config['yty']['title4'] = '当前收益';
$config['yty']['title6'] = '';
$config['yty']['title7'] = '元';
$config['yty']['team_title'] = '您的朋友「%s」邀请您加入云商~';
$config['yty']['team_desc'] = '赶快来加入云商吧！';
$config['yty']['title8'] = '金额';
$config['yty']['list_text']='来自云天佑的好物推荐，快来看看哦~';
$config['yty']['list_text']='http://';
//活动说明
$config['yty']['yty_desc'] = '店铺双十一活动正在进行中，全场包邮 \n\n<a href="http://nanrenwa.youzan.com">【戳这里购买】</a>';
$config['yty']['money_out'] = 100; //提现门槛.分
$config['yty']['money_out_buy'] = 1990; //提现门槛.消费金额.分

$config['yty']['money0'] = 1; //本级佣金比例 %
$config['yty']['money1'] = 1; //一级佣金比例 %

$config['yty']['order_from'] = 10; //订单金额参与分销的门槛(元)
$config['yty']['order_day'] = 10; //交易后可结算佣金天数

$conifg['yty']['haibao_needpay'] = 0; //判断是否有交易才允许生成海报？
$config['yty']['yty_ticket_lifetime']  = 7; //海报有效期（最长7天）
//提现说明
$config['yty']['yty_money_desc'] = <<<EOF
1. 须满足 { ❶佣金满1元 ❷在微商城购买过任意产品并且成功交易 } 两个条件方可提现；
2. 可提现结算周期为"T+10"，即指交易当天后的第 10 个交易日（节假日、周末不计在内）；
3. 您可自主提现，24 小时内转到微信零钱；
4. 本公司拥有本活动最终解释权。
EOF;
//收益模板消息：收益通知->OPENTM206596805
$config['yty']['msg_score_tpl'] = '';
