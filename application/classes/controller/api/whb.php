<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_whb extends Controller_API {

    var $token = 'zhibo';

    var $FromUserName;
    var $Keyword;
    var $appId = 'wxd0b3a6ff48335255';
    var $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    var $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    var $baseurl = 'http://jfb.smfyun.com/whb/';
    var $cdnurl = 'http://cdn.jfb.smfyun.com/whb/';
    var $cdnurl2 = 'http://jfb.dev.smfyun.com/whb/';
    var $scorename;
    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($appid='')
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "whb";
        // if (!is_numeric($appid)) $bid = ORM::factory('whb_login')->where('appid', '=', $appid)->find()->id;
        // $config = ORM::factory('whb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by bole");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($appid='', $debug=0)
    {
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/whb/';
        // Kohana::$log->add('$whb','aaaaaaaaaa');
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('$wzb', print_r($postStr, true));
        // Kohana::$log->add('$whb', print_r($appid, true));
        set_time_limit(15);
        Database::$default = "whb";
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');

        $mem = Cache::instance('memcache');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $appid;//商户appid

        $debug_bid = 64;

        //username->bid
        //if (!is_numeric($bid))
        $biz = ORM::factory('wzb_login')->where('appid', '=', $appid)->find();
        $bid = $biz->id;
        // Kohana::$log->add('$whbbid', print_r($bid, true));
        // $config = ORM::factory('whb_cfg')->getCfg($bid,1);

        if ($debug) print_r($config);

        //if (!$config['appid'] || !$config['appsecret'] ) die('Not Config!');
        //if ($bid == $debug_bid) Kohana::$log->add('weixin2:config', print_r($config, true));
        if ($biz->expiretime && strtotime($biz->expiretime) < time()) $txtReply = '您的账号已过期！';

        if(!$bid) {
            Kohana::$log->add('wzbbid:', 'api'.$appid);//写入日志，可以删除
        }
        $wx = new Wxoauth($bid,'wzb',$this->appId,$options);

        // Kohana::$log->add("whb", print_r($options,true));
        $end = $wx->getRev();
        // Kohana::$log->add("whb", print_r($end,true));
        //DEBUG by bole
        // Kohana::$log->add('$GLOBALS', print_r($GLOBALS, true));
        // if ($bid == 2) Kohana::$log->add('$IPS', print_r($wx->getServerIp(), true));

        if ($bid == $debug_bid) Kohana::$log->add("wzb:$bid:getRevData", print_r($wx->getRevData(), true));


        $fromUsername = $wx->getRevFrom();
        $toUsername = $wx->getRevTo();

        $this->Keyword = $wx->getRevContent();
        $openid = $this->FromUserName = $fromUsername;
        $userinfo = $wx->getUserInfo($openid);

        if ($bid == $debug_bid) {
            // Kohana::$log->add("weixin2:$bid:WE", $wx->errCode.':'.$wx->errMsg.':token:'.$wx->access_token);
            Kohana::$log->add("weixin2:$bid:userinfo", var_export($userinfo, true));
        }

        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("weixin2:$bid:WE", $wx->errCode.':'.$wx->errMsg);

            if ($wx->errCode != 45009) {
                $mem = Cache::instance('memcache');
                $cachename1 ='wzb.access_token'.$bid;
                $ctoken = $mem->delete($cachename1);
            }

            if (!$txtReply) $txtReply = '抱歉哦，消息一不小心走丢了，麻烦再次点击下，谢谢谅解～';
        }

        //关注事件
        $Event = $wx->getRevEvent()['event'];
        $EventKey = $wx->getRevEvent()['key'];

        if($toUsername == 'gh_3c884a361561'){
            if ($Event) $txtReply = $Event.'from_callback';
            if ($this->Keyword == 'TESTCOMPONENT_MSG_TYPE_TEXT') $txtReply = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';

            if(strpos($this->Keyword, 'QUERY_AUTH_CODE') !== false) {
                // echo $wx->text('')->reply(array(),true);
                $auth_code = str_replace('QUERY_AUTH_CODE:', '', $this->Keyword);
                $msg['touser'] = $openid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $auth_code.'_from_api';
                $result = $wx->sendCustomMessage($msg);
                Kohana::$log->add("whb:$bid:sendCustomMessage", print_r($result,true).print_r($msg, true));
            }
        }

        // $Ticket = $wx->getRevTicket();
        // //当前微信用户
        // $model_q = ORM::factory('whb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        // //扫码事件 || 扫码关注
        // $in_sceneid = ORM::factory('whb_qr')->where('bid', '=', $bid)->where('sceneid', '=', $wx->getRevSceneId())->find();
        // Kohana::$log->add("whb:$bid:getRevSceneId", $wx->getRevSceneId());
        // Kohana::$log->add("whb:$bid:sceneid", $in_sceneid->sceneid);
        // if ($userinfo && ($Event == 'SCAN' && $EventKey == $in_sceneid->sceneid) || ($wx->getRevSceneId() == $in_sceneid->sceneid)) {
        //     //新用户
        //     if (!$model_q->id) {
        //         $model_q->bid = $bid;
        //         $model_q->from_qr = $in_sceneid->id;
        //         $model_q->values($userinfo);
        //         //$model_q->ip = Request::$client_ip;
        //         if ($userinfo) $model_q->save();
        //         $chunv = 1;
        //     }
        //     //扫码后默认推送消息
        //     $msg['touser'] = $openid;
        //     $msg['msgtype'] = 'text';

        //     //remove emoji
        //     $nickname = $fuser->nickname;
        //     //$nickname = preg_replace('/[\xf0-\xf7].{3}/', '', $fuser->nickname);
        //     $nickname = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $nickname);
        //     if($in_sceneid->starttime>time()){
        //         // $txtReply = '不好意思，活动于'.date('Y-m-d H:i:s',$in_sceneid->$starttime).'正式开始哦~';
        //         $status = 2;
        //         $msg['text']['content'] = '不好意思，活动于'.date('Y-m-d H:i:s',$in_sceneid->starttime).'正式开始哦~';
        //         $wx->sendCustomMessage($msg);
        //     }
        //     if($in_sceneid->endtime<time()){
        //         // $txtReply = '不好意思，活动已经于'.date('Y-m-d H:i:s',$in_sceneid->$endtime).'结束了哦~';
        //         $status = 2;
        //         $msg['text']['content'] = '不好意思，活动已经于'.date('Y-m-d H:i:s',$in_sceneid->endtime).'结束了哦~';
        //         $wx->sendCustomMessage($msg);
        //     }

        //     //扫码后推送网址
        //     if ($chunv == 1 && $status!=2) {
        //         $cksum = $cksum = md5($model_q->openid.date('Y-m-d'));
        //         $url = $this->baseurl.'index/'. $bid .'?url=hongbao&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
        //         $msg['msgtype'] = 'news';
        //         $msg['news']['articles'][0]['title'] = '点击领取红包';
        //         $msg['news']['articles'][0]['url'] = $url;
        //         $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
        //         $we_result = $wx->sendCustomMessage($msg);
        //     }
        // }
        // //菜单点击事件
        // if ($userinfo && $Event == 'CLICK' || $chunv2) {

        //     if (!$model_q->id) {
        //         $model_q->bid = $bid;
        //         $model_q->values($userinfo);
        //         //$model_q->ip = Request::$client_ip;
        //         if ($userinfo) $model_q->save();
        //     }
        //     $msg['touser'] = $openid;
        //     $msg['msgtype'] = 'text';
        //     $cksum = md5($model_q->openid.date('Y-m-d'));
        //     //$pos = strpos($mystring, $findme); $findme是你要查找的字符，如果找到返回True，否则返回false
        //     $count = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
        //     $position = 0;
        //     $u_location = ORM::factory('whb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
        //     Kohana::$log->add('whbuposition', $bid);
        //     Kohana::$log->add('whbuposition', $openid);
        //     Kohana::$log->add('whbuposition', print_r($u_location,true));
        //     for ($i=1; $i <=$count ; $i++) {
        //         $pro[$i] = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
        //         $city[$i] = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
        //         $dis[$i] = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
        //         $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        //         Kohana::$log->add('whbuposition',$p_location[$i]);
        //         $pos[$i] = strpos($u_location, $p_location[$i]);
        //         if ($pos[$i]!==false) {
        //             $position++;
        //         }
        //     }
        //     // $pos=implode(glue,$pos);
        //     // $msg['text']['content'] = $pos.$p_location[1].$p_location[2].$p_location[3].$position.$count;
        //     // $wx->sendCustomMessage($msg);
        //     // exit;
        //     Kohana::$log->add('whbuposition',$position);
        //     $status = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
        //  if(($position >0 && $status=='1')||$status=='0'||!$status){

        //     $isvalue = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key','=','value_'.substr($EventKey,-2))->find()->value;
        //     if($isvalue&&substr($iskey, 0,4)!='http'){
        //         $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $isvalue);
        //     }

        //     //生成海报
        //     else if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报') {

        //         $ticket_lifetime = 3600*24*7;
        //         //自定义过期时间      不可删


        //         if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

        //         $qrcode_type = 0;

        //         if ( ($result['ticket'] = $model_q->ticket) && (time() - $model_q->lastupdate < $ticket_lifetime) ) {

        //             //pass
        //             $msg['text']['content'] = $config['text_send'];

        //             //更新用户信息
        //             //$model_q->values($userinfo);
        //             //$model_q->save();

        //         } else {

        //             $time = time();

        //             //永久二维码
        //             if ($model_q->lock == 100) {
        //                 $ticket_lifetime = 3600*24*365*3;
        //                 $qrcode_type = 1;
        //                 $time = time() + $ticket_lifetime;
        //             }

        //             $result = $wx->getQRCode($bid, $qrcode_type, $ticket_lifetime);
        //             $model_q->lastupdate = $time;

        //             $msg['text']['content'] = $config['text_send'];

        //             //生成海报并保存
        //             $model_q->values($userinfo);
        //             $model_q->bid = $bid;
        //             $model_q->ticket = $result['ticket'];
        //             $model_q->save();

        //             $newticket = true;
        //         }

        //         // 3 条客服消息限制，这里不发了
        //         //$we_result = $wx->sendCustomMessage($msg);

        //         $md5 = md5($result['ticket'].time().rand(1,100000));

        //         //图片合成
        //         //模板
        //         $imgtpl = DOCROOT."whb/tmp/tpl.$bid.jpg";
        //         $tmpdir = '/dev/shm/';

        //         //判断模板文件是否需要从数据库更新
        //         $tpl = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
        //         if (!$tpl->pic) {
        //             $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
        //             $we_result = $wx->sendCustomMessage($msg);
        //             exit;
        //         }

        //         if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);

        //         if (!file_exists($imgtpl)) {
        //             @mkdir(dirname($imgtpl));
        //             @file_put_contents($imgtpl, $tpl->pic);
        //         }

        //         //默认头像
        //         $tplhead = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
        //         $default_head_file = DOCROOT."whb/tmp/head.$bid.jpg";

        //         if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
        //         if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

        //         //有海报缓存直接发送
        //         $tpl_key = 'whb:tpl:'.$openid.':'.$tpl->lastupdate;
        //         $uploadresult['media_id'] = $mem->get($tpl_key);

        //         if ($bid == $debug_bid) $newticket = true;

        //         if ($uploadresult['media_id'] && !$newticket) {
        //             //pass
        //             // Kohana::$log->add('weixin2:tpl_key', $tpl_key);
        //             // Kohana::$log->add('weixin2:media_id_cache', print_r($uploadresult, true));
        //         } else {

        //             //获取参数二维码
        //             $qrurl = $wx->getQRUrl($result['ticket']);
        //             $localfile = "{$tmpdir}$md5.jpg";
        //             $remote_qrcode = curls($qrurl);
        //             if (!$remote_qrcode) $remote_qrcode = curls($qrurl);
        //             if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);

        //             //获取头像
        //             $headfile = "{$tmpdir}$md5.head.jpg";

        //             //IP 获取
        //             //http://182.254.104.16/mmopen/ajNVdqHZLLB1WVibay1icL4QZ4VWrLZriblYa9yBu7hia3AAERIvI4ysT3MhwoKpCbgC1WF7mBuHxhRHLhRbI7scUg/0
        //             //http://wx.qlogo.cn/mmopen/ajNVdqHZLLAwad4e2M5lW5vNg6iaMSIkeNnt3oNfw84BWrg657rfeoLSico8eyyOV8mLXuSsx723UJntfZJLu4vA/132
        //             $remote_head_url = str_replace('wx.qlogo.cn', '182.254.104.16', $model_q->headimgurl);
        //             $remote_head = curls($remote_head_url);

        //             if (!$remote_head) {
        //                 $remote_head_url = str_replace('/0', '/132', $model_q->headimgurl);
        //                 $remote_head = curls($remote_head_url);
        //             }

        //             //retry... 96px
        //             if (!$remote_head) {
        //                 $remote_head_url = str_replace('/132', '/96', $remote_head_url);
        //                 $remote_head = curls($remote_head_url);
        //             }

        //             //获取失败用默认头像
        //             if (!$remote_head && $default_head) $remote_head = $default_head;

        //             //写入临时头像文件
        //             if ($remote_head) file_put_contents($headfile, $remote_head);

        //             if (!$remote_head || !$remote_qrcode) {
        //                 $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
        //                 $we_result = $wx->sendCustomMessage($msg);
        //                 Kohana::$log->add("weixin2:$bid:file:remote_head_url get ERROR!", $remote_head_url);

        //                 @unlink($headfile);
        //                 @unlink($localfile);
        //                 exit;
        //             }

        //             //合成
        //             $dest = imagecreatefromjpeg($imgtpl);
        //             $src_qrcode = imagecreatefromjpeg($localfile);
        //             $src_head = imagecreatefromjpeg($headfile);

        //             if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
        //             if($src_head) imagecopyresampled($dest, $src_head, $config['px_head_left'], $config['px_head_top'], 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

        //             $newfile = "{$tmpdir}$md5.new.jpg";
        //             imagejpeg($dest, $newfile);
        //             if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
        //             if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);


        //             if (file_exists($newfile)) {
        //                 $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
        //                 if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
        //                 if (!$uploadresult['media_id']) {
        //                     Kohana::$log->add("weixin2:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
        //                     if ($wx->errCode == 45009) {
        //                         $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
        //                         $we_result = $wx->sendCustomMessage($msg);
        //                         exit;
        //                     }
        //                 } else {
        //                     //上传成功 pass
        //                     if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:$newfile upload OK!", print_r($uploadresult, true));
        //                 }

        //             } else {
        //                 Kohana::$log->add("weixin2:$bid:newfile $newfile gen ERROR!");
        //                 Kohana::$log->add("weixin2:$bid:imgtplfile", file_exists($imgtpl));
        //                 Kohana::$log->add("weixin2:$bid:qrcodefile", file_exists($localfile));
        //                 Kohana::$log->add("weixin2:$bid:headfile", file_exists($headfile));
        //             }

        //             unlink($localfile);
        //             unlink($headfile);
        //             unlink($newfile);

        //             //Cache
        //             if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
        //         }


        //         //海报发送前提醒消息
        //         $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「生成海报」菜单重新获取哦！';
        //         if ($bid == 64) $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「我要参加」菜单重新获取哦！';
        //         $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;
        //         echo '';
        //         $we_result = $wx->sendCustomMessage($msg);

        //         $msg['msgtype'] = 'image';
        //         $msg['image']['media_id'] = $uploadresult['media_id'];
        //         unset($msg['text']);

        //         $we_result = $wx->sendCustomMessage($msg);

        //         if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:img_msg", var_export($msg, true));
        //         if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result_img", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);
        //         exit;
        //     }

        //     //积分明细
        //     else if ($EventKey == 'score' || $EventKey == '积分查询') {
        //         if (!$model_q->openid) {
        //             $msg['text']['content'] = '请先点击生成海报';
        //             $wx->sendCustomMessage($msg);
        //             exit;
        //         }
        //         $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
        //         $msg['msgtype'] = 'news';

        //         $news_pic_file = 'news_score.'. $bid .'.jpg';
        //         if (!file_exists(DOCROOT."whb/$news_pic_file")) $news_pic_file = 'news_score.png';
        //         $news_pic = $this->cdnurl.$news_pic_file;

        //         $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'明细';
        //         $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看明细...';
        //         $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
        //         $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

        //         // $we_result = $wx->sendCustomMessage($msg);
        //     }

        //     /*//积分明细
        //     else if ($EventKey == $config['key_score'] || $EventKey == '积分查询') {
        //         if (!$model_q->openid) {
        //             $msg['text']['content'] = '请先点击生成海报';
        //             $wx->sendCustomMessage($msg);
        //             exit;
        //         }
        //         $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
        //         $msg['msgtype'] = 'news';

        //         $news_pic_file = 'imgtpl/'.$bid.'/score1.jpg';
        //         // if (!file_exists(DOCROOT."whb/$news_pic_file")) $news_pic_file = 'imgtpl/'.'$bid'.'/score1.jpg';
        //         $news_pic = $this->cdnurl2.$news_pic_file;

        //         $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'明细';
        //         $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看明细...';
        //         $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
        //         $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

        //         // $we_result = $wx->sendCustomMessage($msg);
        //     }*/



        //     //兑换商城
        //     else if ($EventKey == 'item' || $EventKey == '积分兑换') {
        //         if (!$model_q->openid) {
        //             $msg['text']['content'] = '请先点击生成海报';
        //             $wx->sendCustomMessage($msg);
        //             exit;
        //         }

        //         $url = $this->baseurl.'index/'. $bid .'?url=items&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
        //         $msg['msgtype'] = 'news';

        //         $news_pic_file = 'news_order.'. $bid .'.jpg';
        //         if (!file_exists(DOCROOT."whb/$news_pic_file")) $news_pic_file = 'news_order.jpg';
        //         $news_pic = $this->cdnurl.$news_pic_file;

        //         /*$news_pic_file = 'imgtpl/'.$bid.'/score2.jpg';
        //         // if (!file_exists(DOCROOT."whb/$news_pic_file")) $news_pic_file = 'imgtpl/'.'$bid'.'/score1.jpg';
        //         $news_pic = $this->cdnurl2.$news_pic_file;*/

        //         $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'兑换';
        //         if ($bid == 64) $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '奖品兑换';

        //         $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看可兑换的产品...';
        //         $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
        //         $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

        //         //$we_result = $wx->sendCustomMessage($msg);
        //     }

        //     //排行榜
        //     else if ($EventKey == 'top' || $EventKey == '积分排行') {
        //         if (!$model_q->openid) {
        //             $msg['text']['content'] = '请先点击生成海报';
        //             $wx->sendCustomMessage($msg);
        //             exit;
        //         }

        //         $url = $this->baseurl.'index/'. $bid .'?url=top&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
        //         $msg['msgtype'] = 'news';

        //         $news_pic_file = 'news_top.'. $bid .'.jpg';
        //         if (!file_exists(DOCROOT."whb/$news_pic_file")) $news_pic_file = 'news_top.png';
        //         $news_pic = $this->cdnurl.$news_pic_file;

        //         /*$news_pic_file = 'imgtpl/'.$bid.'/score3.jpg';
        //         // if (!file_exists(DOCROOT."whb/$news_pic_file")) $news_pic_file = 'imgtpl/'.'$bid'.'/score1.jpg';
        //         $news_pic = $this->cdnurl2.$news_pic_file;*/

        //         $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'排行榜';
        //         $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
        //         $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

        //         //$we_result = $wx->sendCustomMessage($msg);
        //     }

        //     else if ($EventKey) {
        //         $msg['msgtype'] = 'text';
        //         $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「积分宝」商户后台完成配置：'.$EventKey;

        //         //用户少的时候才回复 debug
        //         // if (ORM::factory('whb_qrcode')->where('bid', '=', $bid)->count_all() < 10)
        //         //$we_result = $wx->sendCustomMessage($msg);
        //     }

        //     //检查 Auth 是否过期
        //     if ($we_result === false) {
        //         if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result", print_r($we_result, true));
        //         //$wx->resetAuth();
        //     }
        //     //$msg['text']['content'] = '符合要求'.$u_location.$p_location;

        //   }
        //     else{
        //         //$msg['text']['content'] = '不符合要求'.$u_location.$p_location;
        //         $url = $this->baseurl.'index/'. $bid .'?url=check_location&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
        //         $replyfront = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
        //         $replyend = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
        //         $msg['text']['content'] = $replyfront.'<a href="'.$url.'">点击查看是否在活动范围内</a>'.$replyend;
        //         // $msg['text']['content'] = $config['reply'];
        //     }
        //     $wx->sendCustomMessage($msg);
        //     //点击菜单先检测是否有地理位置
        //     //1 有地理位置且包含数据库中地理位置字符串 按照原计划执行
        //     //2 无地理位置或者地理位置不符合 发消息跳转重新获取地理位置链接
        //     //自定义 key
        //     exit;
        // }

        //默认文字回复
        //$txtReply = 'success';
        if ($txtReply) {

            $result = $wx->text($txtReply)->reply(array(),true);
            echo $result;
        }

        //默认图文回复
        if ($newsReply) {
            echo $wx->news($newsReply)->reply(array(),true);
        }

        exit;
    }

    //检查签名
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        } else {
            return false;
        }
    }

}
