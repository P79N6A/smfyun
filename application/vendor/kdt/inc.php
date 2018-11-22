<?php
require 'KdtApiClient.php';
$client = new KdtApiClient('3053bb9ab356dd0826', '4cc0251535d74b86c5f5aad645194198');

//Weixin 公众号配置
$config['appid'] = 'wxf3e29c3a2838100e';
$config['appsecret'] = 'd60b755f9362a16120d203c7cba34c26';

//按 sku 取数量、颜色、材质
function _WeixinDetailFromSku($sku) {
    //sku=2&size=xl&thick=1&color=3&num=6
    //plan=21&sku=19&num=1&size=s&color=3

    parse_str($sku, $var);
    if (!$var['plan']) return false;

    $color = ORM::factory('color')->where('sum', '=', $var['color'])->find()->id;
    if (!$color) $color = 0;

    $size_id = 2; //M

    if ($var['size']=='s') $size_id = 1; //S
    if ($var['size']=='l') $size_id = 5; //L
    if ($var['size']=='xl') $size_id = 3; //XL

    if ($var['size']=='xxl') $size_id = 4;
    if ($var['size']=='2xl') $size_id = 4;

    if ($var['size']=='3xl') $size_id = 6;
    if ($var['size']=='xxxl') $size_id = 6;

    $result['plan_id'] = $var['plan'];
    $result['sku_id'] = $var['sku'] ? $var['sku'] : 1;

    //$result['thick'] = $var['thick'] >= 3 ?  : 0;
    $result['thick'] = (int)$var['thick'];

    $result['color_id'] = $color;
    $result['size_id'] = $size_id;

    $result['shipnum'] = $var['num'];
    $result['shipcount'] = $var['ct'] ? $var['ct'] : 1;
    $result['shipcyc'] = $var['cyc'] ? $var['cyc'] : 360;

    return $result;
}
