<?php
$config['qwt']['token'] = 'smfyun';
$config['qwt']['day'] = 3;
$config['qwt']['hbnum'] = 50;
$config['qwt']['wzbnum'] = 1;
$config['qwt']['wfbhaibao'] = 100;//100
$config['qwt']['wdbhaibao'] = 100;
$config['qwt']['rwbhaibao'] = 100;
$config['qwt']['rwdhaibao'] = 100;
$config['qwt']['dkahaibao'] = 100;
$config['qwt']['fxbhaibao'] = 100;
$config['qwt']['qfxhaibao'] = 100;
$config['qwt']['glcount'] = 100;//100
$config['qwt']['dldnum'] = 20;//20
$config['qwt']['kminum'] = 50;//50
$config['qwt']['menu_cfg'][0]['name'] = '积分宝服务号生成海报';
$config['qwt']['menu_cfg'][0]['iid'] = 3;
$config['qwt']['menu_cfg'][0]['type'] = 'qrcode';

$config['qwt']['menu_cfg'][1]['name'] = '积分宝服务号兑换中心';
$config['qwt']['menu_cfg'][1]['iid'] = 3;
$config['qwt']['menu_cfg'][1]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/wfb/items';
$config['qwt']['menu_cfg'][1]['type'] = 'url';

$config['qwt']['menu_cfg'][2]['name'] = '积分宝服务号积分明细';
$config['qwt']['menu_cfg'][2]['iid'] = 3;
$config['qwt']['menu_cfg'][2]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/wfb/score';
$config['qwt']['menu_cfg'][2]['type'] = 'url';

$config['qwt']['menu_cfg'][3]['name'] = '任务宝服务号版生成海报';
$config['qwt']['menu_cfg'][3]['iid'] = 4;
$config['qwt']['menu_cfg'][3]['type'] = 'qrcode';

$config['qwt']['menu_cfg'][4]['name'] = '代理哆代理申请';
$config['qwt']['menu_cfg'][4]['iid'] = 9;
$config['qwt']['menu_cfg'][4]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/dld/form';
$config['qwt']['menu_cfg'][4]['type'] = 'url';

$config['qwt']['menu_cfg'][5]['name'] = '代理哆个人中心';
$config['qwt']['menu_cfg'][5]['iid'] = 9;
$config['qwt']['menu_cfg'][5]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/dld/memberpage';
$config['qwt']['menu_cfg'][5]['type'] = 'url';

// $config['qwt']['menu_cfg'][6]['name'] = '代理哆成为一级代理商的链接';
// $config['qwt']['menu_cfg'][6]['iid'] = 9;
// $config['qwt']['menu_cfg'][6]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/dld/code';
// $config['qwt']['menu_cfg'][6]['type'] = 'url';

$config['qwt']['menu_cfg'][6]['name'] = '全员分销生成海报';
$config['qwt']['menu_cfg'][6]['iid'] = 7;
$config['qwt']['menu_cfg'][6]['type'] = 'qrcode';

$config['qwt']['menu_cfg'][7]['name'] = '全员分销资产查询';
$config['qwt']['menu_cfg'][7]['iid'] = 7;
$config['qwt']['menu_cfg'][7]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/qfx/home';
$config['qwt']['menu_cfg'][7]['type'] = 'url';

$config['qwt']['menu_cfg'][8]['name'] = '全员分销申请分销';
$config['qwt']['menu_cfg'][8]['iid'] = 7;
$config['qwt']['menu_cfg'][8]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/qfx/form';
$config['qwt']['menu_cfg'][8]['type'] = 'url';

$config['qwt']['menu_cfg'][9]['name'] = '订单宝我的收益';
$config['qwt']['menu_cfg'][9]['iid'] = 8;
$config['qwt']['menu_cfg'][9]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/fxb/home';
$config['qwt']['menu_cfg'][9]['type'] = 'url';

$config['qwt']['menu_cfg'][10]['name'] = '订单宝生成海报';
$config['qwt']['menu_cfg'][10]['iid'] = 8;
$config['qwt']['menu_cfg'][10]['type'] = 'qrcode';

$config['qwt']['menu_cfg'][11]['name'] = '神码云直播链接';
$config['qwt']['menu_cfg'][11]['iid'] = 11;
$config['qwt']['menu_cfg'][11]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_userinfo/'.'%s'.'/wzb/live';
$config['qwt']['menu_cfg'][11]['type'] = 'url';

$config['qwt']['menu_cfg'][12]['name'] = '打卡宝我要打卡';
$config['qwt']['menu_cfg'][12]['iid'] = 13;
$config['qwt']['menu_cfg'][12]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/dka/dka';
$config['qwt']['menu_cfg'][12]['type'] = 'url';

$config['qwt']['menu_cfg'][13]['name'] = '打卡宝积分兑换';
$config['qwt']['menu_cfg'][13]['iid'] = 13;
$config['qwt']['menu_cfg'][13]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_base/'.'%s'.'/dka/items';
$config['qwt']['menu_cfg'][13]['type'] = 'url';

$config['qwt']['menu_cfg'][14]['name'] = '积分宝订阅号生成海报';
$config['qwt']['menu_cfg'][14]['iid'] = 2;
$config['qwt']['menu_cfg'][14]['type'] = 'qrcode';

$config['qwt']['menu_cfg'][15]['name'] = '积分宝订阅号积分兑换';
$config['qwt']['menu_cfg'][15]['iid'] = 2;
$config['qwt']['menu_cfg'][15]['type'] = 'item';

$config['qwt']['menu_cfg'][16]['name'] = '任务宝订阅号生成海报';
$config['qwt']['menu_cfg'][16]['iid'] = 25;
$config['qwt']['menu_cfg'][16]['type'] = 'qrcode';
