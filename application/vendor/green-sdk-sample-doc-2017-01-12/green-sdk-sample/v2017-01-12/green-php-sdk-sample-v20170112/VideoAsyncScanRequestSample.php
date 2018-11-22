<?php
/**
 * Created by PhpStorm.
 * User: hyliu
 * Date: 2017/4/21
 * Time: 10:04
 */

include_once 'aliyuncs/aliyun-php-sdk-core/Config.php';
use Green\Request\V20170112 as Green;

date_default_timezone_set("PRC");

$ak = parse_ini_file("aliyun.ak.ini");
//请替换成你自己的accessKeyId、accessKeySecret
$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", $ak["accessKeyId"], $ak["accessKeySecret"]); // TODO
DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "Green", "green.cn-hangzhou.aliyuncs.com");
$client = new DefaultAcsClient($iClientProfile);

$request = new Green\VideoAsyncScanRequest();
$request->setMethod("POST");
$request->setAcceptFormat("JSON");

$frame1 = array(
    "offset" => 0,
    "url" => "http://pic12.nipic.com/20110221/6727421_210944911000_2.jpg"
);

$frame2 = array(
    "offset" => 5,
    "url" => "http://pic12.nipic.com/20110221/6727421_210944911000_3.jpg"
);


$frame3 = array(
    "offset" => 10,
    "url" => "http://pic12.nipic.com/20110221/6727421_210944911000_4.jpg"
);

$task1 = array(
    "dataId" => uniqid(),
    "interval" => 5,
    "length" => 3600,
    "url" => "http://cloud.video.taobao.com/play/u/228898015/p/1/e/1/t/1/***.swf",
    "frames" => array($frame1, $frame2, $frame3)
);
$request->setContent(json_encode(array("tasks" => array($task1),
    "scenes" => array("porn"))));

try {
    $response = $client->getAcsResponse($request);
    print_r($response);
} catch (Exception $e) {
    print_r($e);
}
