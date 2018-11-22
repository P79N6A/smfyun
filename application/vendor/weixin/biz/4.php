<?php
 //红包金额，单位：分
$money = rand(100, 101);

 //商户名，不能超过 5 个字
$config['name'] = '禾葡兰';

$config['appid'] = 'wx6006c5ab8336f863';
$config['appsecret'] = 'b527e6b521dc2d72ae0fc4f8c4953348';

$config['partnerid'] = '1229665102';
$config['partnerkey'] = 'skjfdoasiut36546341ggfddgsdagf12';

//最多发多少个红包
$config['max'] = 100;

//关注链接
$config['follow_url'] = 'http://mp.weixin.qq.com/s?__biz=MzA4NTA1MDIxNw==&mid=203949122&idx=1&sn=4f4ca6bca02199fdd42cc12bc27945f9';

//头图
$config['img_top'] = '/_img/weixin/hongbao/top4.jpg';

//头图链接，不加链接为空
$config['img_top_link'] = '';
?>