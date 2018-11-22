<?php
$money = rand(100,101);

$config['appid'] = 'wxf3e29c3a2838100e';
$config['appsecret'] = 'd60b755f9362a16120d203c7cba34c26';

$config['partnerid'] = '1217729301';
$config['partnerkey'] = '9f48c9b6b2dfd7191a0e23e6f177c57f';

$config['youzan_appid'] = '3053bb9ab356dd0826';
$config['youzan_appsecret'] = '4cc0251535d74b86c5f5aad645194198';

$config['name'] = '男人袜';
$config['img_top'] = '/hongbao/top.jpg';
$config['max'] = 100000;

//口令红包
$config['hb']['keyword'] = '红包包.bak';

$config['hb']['hack'] = '傻瓜，输错啦~ 再仔细看看，保持姿势，再来一遍，你还有 %s 次输入机会哦~';
$config['hb']['payed'] = '啊哦~客官，你的人品码已经被其他人兑换过了呢！下次收到红包卡的时候，再来试试，先 <a href="http://nanrenwa.youzan.com/">去店里看看</a> 妹子高清无码套图吧~';
$config['hb']['success2'][] = '亲爱的，你与红包擦肩而过了呢~ 别哭，人家送张优惠券给你可好？接，biu~ <a href="http://wap.koudaitong.com/v2/showcase/coupon/fetch?alias=k9ynvf91">点击领取</a>';
$config['hb']['success_msg'] = '哇，您捡到了一块肥皂，快接，biu~ <a href="http://wap.koudaitong.com/v2/showcase/coupon/fetch?alias=k9ynvf91">点击领取</a>'; //成功触发红包的附加回复

//语音红包配置
$config['hb']['success'] = '哇喔~好棒！看来你人品不错哦，为什么不<a href="http://nanrenwa.youzan.com/">去店里看看</a>，把红包用掉呢？';
$config['hb']['got'] = '死鬼，你已经领过啦！快去呼唤你的小伙伴一起过来领吧~ 为什么不<a href="http://nanrenwa.youzan.com/">去店里看看</a>，把红包用掉呢？';
$config['hb']['limit'] = "呵呵哒，男人简直就如洪水猛兽，红包一下子就领光了，晚一点再来试啦！乖~";
$config['hb']['timelimit'] = '哎呀，0:00~8:00点，妹子们都在睡觉呢，天亮了再来兑换红包哦！亲亲~';

$config['hb']['rate'] = 100;

//红包分裂
$config['hb']['split'] = 2; //2级裂变
$config['hb']['split_count'] = 5; //红包分裂数量
$config['hb']['split_txt'] = '您获得了一个新口令，发给朋友让 TA 关注微信：nanrenwaa 回复 %s 就可以领红包！';
$config['hb']['splits_txt'] = "【免费抢红包】唔，领到小飘飘的红包，你就是我的人了呢。当然哦，还有一大波红包送你的小伙伴儿：\n%s\n快把这些红包转给朋友啦~ 关注微信：nanrenwaa 回复上列红包数字就可以一起来领咯，还能顺便免费体验男人袜呢：http://kdt.im/N86vcVZ9m";

//竞猜红包
$config['hb']['split_guess'] = 0; //是否竞猜红包?
$config['hb']['split_guess_txt'] = "【男人袜·恭喜获得给好友发红包特权】\n「%s」看到这串数字了吗？让好友猜最后 1 位密码，猜对就可领到！\n注：①共 10 位数，空白数字为0~9，只有 3 次机会哦\n②在男人袜微信公众号（nanrenwaa），即可发密码领红包\n快喊小伙伴一起来领，你来发，我买单！";
