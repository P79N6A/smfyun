<?php defined('SYSPATH') or die('No direct script access.');
$keyword = $this->Keyword;
$rule = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->where('keyword','=',$keyword)->find();
if ($rule->id) {
    $msg['touser'] = $openid;
    $msg['msgtype'] = 'miniprogrampage';
    $msg['miniprogrampage']['title'] = $rule->msg->title;
    $msg['miniprogrampage']['appid'] = $rule->msg->appid;
    if ($rule->msg->path) {
        $msg['miniprogrampage']['pagepath'] = $rule->msg->path;
    }
    $msg['miniprogrampage']['thumb_media_id'] = $rule->msg->media_id;
    $wx_result = $wx->sendCustomMessage($msg);
    Kohana::$log->add('zdf:keyword:miniprogram:msg:'.$bid,print_r($msg,true));
    Kohana::$log->add('zdf:keyword:miniprogram:'.$bid,print_r($wx_result,true));
    if ($rule->text) {
        $msg['touser'] = $openid;
        $msg['msgtype'] = 'text';
        $msg['text']['content'] = '@'.$userinfo['nickname'].','.$rule->text;
        $wx_result = $wx->sendCustomMessage($msg);
        Kohana::$log->add('zdf:keyword:text:'.$bid,print_r($wx_result,true));
    }
}
exit;
