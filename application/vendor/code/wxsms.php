<?php
class WXSMS{
 const account='dh54161';
 const password='fdA9%L44';
 const sign='【男人袜】';
 const url='http://wt.3tong.net/json/sms/';
 //发送短信
 public static function Send($phones,$content){
  $array=array(
   'account'=>self::account,
   'password'=>md5(self::password),
   'phones'=>$phones,
   'content'=>$content
  );
  $url=self::url.'Submit';
  $value=json_encode($array);
  $result=self::Post($url, $value);
  return json_decode($result,true);
  // var_dump($result);
 }

 //查看短信上行，查看短信的具体内容
 public static function Deliver(){
  $array=array(
    'account'=>self::account,
    'password'=>md5(self::password),
  );
  $value=json_encode($array);
  $url=self::url.'Deliver';
  $result=self::Post($url, $value);
  print_r(json_decode($result)['delivers']);
 }

 //查询余额
 public static function Balance(){
  $array=array(
    'account'=>self::account,
    'password'=>md5(self::password),
  );
  $value=json_encode($array);
  $url=self::url.'Balance';
  $result=self::Post($url, $value);
  print_r(json_decode($result)['smsBalance']);
 }

 //查询短信状态报告
 public static function Report(){
  $array=array(
   'account'=>self::account,
   'password'=>md5(self::password),
  );
  $value=json_encode($array);
  $url=self::url.'Report';
  $result=self::Post($url, $value);
  print_r(json_decode($result)['reports']);
 }

 //查询黑名单
 public static function BlackList($phones){
  $array=array(
    'account'=>self::account,
    'password'=>md5(self::password),
    'phones'=>$phones
  );
  $value=json_encode($array);
  $url=self::url.'BlackListCheck';
  $result=self::Post($url, $value);
  var_dump($result);
 }

 //检测敏感词
 public static function KeyWord($content){
  $array=array(
    'account'=>self::account,
    'password'=>md5(self::password),
    'content'=>$content
  );
  $value=json_encode($array);
  $url=self::url.'KeyWordCheck';
  $result=self::Post($url, $value);
  var_dump($result);
 }

 private static function Post($url,$value){
  $curl=curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $value);
  $result=curl_exec($curl);
  curl_close($curl);
  return $result;
 }
}
