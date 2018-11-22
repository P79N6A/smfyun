<?php defined('SYSPATH') or die('No direct script access.');

//全员分销 api 地址
Class qfx {

    public $Keyword;
    public $cdnurl ;
    public $baseurl ;
    public $methodVersion='3.0.0';
    public $wx;
    public $config;

    //收发消息: $bid、附加处理函数
    public function __construct($Keyword,$wx,$bid,$openid,$userinfo,$qr_user,$Event,$mem){
        Kohana::$log->add('qwt_qfxconfig:$bid:', '111111');
        $this->cdnurl = 'http://'.$_SERVER["HTTP_HOST"].'/qwt/qfx/';
        $this->baseurl = 'http://'.$_SERVER["HTTP_HOST"];
        $this->bid = $bid;
        $this->Keyword = $Keyword;
        $this->wx = $wx;
        $this->config = $config = ORM::factory('qwt_qfxcfg')->getCfg($bid,1);
        // Kohana::$log->add('qwt_qfxconfig:$bid:', print_r($config,true));

        $model_q = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //新用户
        if (!$model_q->id) {
            $model_flag=1;
            Kohana::$log->add("model_flag$bid$openid",$model_flag);
            $model_q->qid = $qr_user->id;
            $model_q->bid = $bid;
            $model_q->values($userinfo);
            if ($userinfo) $model_q->save();
        }else{
            $model_flag=2;
            Kohana::$log->add("model_flag$bid$openid",$model_flag);
            $model_q->subscribe = $userinfo['subscribe'];
            $model_q->subscribe_time = $userinfo['subscribe_time'];
            $model_q->jointime = time();
            $model_q->save();
        }

        //扫码事件 || 扫码关注
        // $scene_id = $bid;
        $EventKey = $wx->getRevEvent()['key'];
        $Ticket = $wx->getRevTicket();
        Kohana::$log->add("qwt_qfx_EventKey", $EventKey);
        Kohana::$log->add("qwt_qfx_Event", $Event);
        Kohana::$log->add("qwt_qfx_getRevSceneId", $wx->getRevSceneId());
        if($EventKey == $bid || $EventKey == 'qfx'.$bid){
            $EventKeyget = 1;
        }
        if($wx->getRevSceneId() == $bid || $wx->getRevSceneId() == 'qfx'.$bid){
            $wxgetRevSceneId = 1;
        }
        if ($userinfo && ($Event == 'SCAN' && $EventKeyget == 1) || ($wxgetRevSceneId == 1)) {
        // if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($wx->getRevSceneId() == $scene_id)) {

            $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
            $fopenidFromQrcode = $fopenid = $fuser->openid;

            //如果 当前用户有效 && 当前用户没有上级 && 当前用户没有生成海报 && 来源二维码有效 && 来源用户 != 当前用户&&当前用户未锁定&&上级未锁定&&当前用户未提交过申请
            Kohana::$log->add("qwt_qfx_qid", $model_q->id);
            Kohana::$log->add("qwt_qfx_fopenid", $fopenid);
            Kohana::$log->add("qwt_qfx_ticket", $model_q->ticket);
            Kohana::$log->add("qwt_qfx_openid", $openid);
            Kohana::$log->add("qwt_qfx_lock1", $model_q->lock);
            Kohana::$log->add("qwt_qfx_lock2", $fuser->lock);
            if ($model_flag==1 &&$model_q->id && !$model_q->fopenid && !$model_q->ticket && $fopenid && $fopenid != $openid&&$fuser->lv==1&&$model_q->lv==0) {
                $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if($subscribe->id&&$subscribe->creattime<=time()-60){
                    Kohana::$log->add("wfb{$bid}SCAN111",print_r($subscribe->openid, true));
                    $has_subscribe=1;
                    Kohana::$log->add("has_subscribe$bid$openid",$has_subscribe);
                }else{
                    $has_subscribe=2;
                    Kohana::$log->add("has_subscribe$bid$openid",$has_subscribe);
                }
                Kohana::$log->add("wfb{$bid}SCAN",print_r(time(), true));

                //先保存关系
                if ($model_q->id > $fuser->id&&$has_subscribe==2) {
                    //处女？
                    $chunv = 1;
                    Kohana::$log->add("qwt_qfx_scan1", 'scan1');
                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->save();

                    //第一个
                    //推荐人 扫码奖励
                    Kohana::$log->add("qwt_qfx_scan", 'scan');
                    // $cksum = md5($fuser->openid.$config['appsecret'].date('Y-m'));
                    // $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($fuser->openid);
                    $url = $this->baseurl.'/smfyun/user_snsapi_base/'.$bid.'/qfx/home';
                    $msg['touser'] = $fopenidFromQrcode;
                    $msg['msgtype'] = 'text';

                    $msg['text']['content'] = "恭喜您推荐了{$model_q->nickname}加入\n\n<a href=\"$url\">点击查看明细</a>";

                    //模板消息
                    if ($config['msg_new_friend'])
                        $this->sendNewMessage($fuser->openid,$model_q->nickname,$url);
                    else
                        $wx->sendCustomMessage($msg);


                    //更新上一级用户的 userinfo
                    $fuserinfo = $wx->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("qwt_qfx:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->subscribe = 0;
                        $fuser->save();
                        // $fuser->values($fuserinfo);
                        // $fuser->save();
                    }

                }
            }

            //已经有上级就直接取来
            if($model_q->fopenid){
                $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
                $fopenid = $fuser->openid;
            }
            $fromuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
            //扫码后默认推送消息
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg2['touser'] = $fopenid;
            $msg2['msgtype'] = 'text';
            //remove emoji
            //让 如果来源二维码就是自己的 话 剔除
            if($fuser->openid!=$model_q->openid){
                $nickname = $fuser->nickname;
            }
            //$nickname = preg_replace('/[\xf0-\xf7].{3}/', '', $fuser->nickname);
            $nickname = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $nickname);

            //判断级数关系
            $name = $config['title1'];
            if($model_q->lv==0){
                $name = $config['title2'];
            }
            // $id = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('lv','=',1)->where('id','<=',$model_q->id)->find_all()->count();
            //自己关注 没上线
            //新加消息
            if ($model_q->id) $msg['text']['content'] = "您是{$config['name']}第 {$model_q->fid} 号{$name}，不用再扫了哦。\n\n";
            Kohana::$log->add("$bid:wdy:weixin_scan88:$openid", print_r($model_flag,true));
            Kohana::$log->add("$bid:wdy:weixin_scan99:$openid", print_r($has_subscribe,true));
            if ($model_flag==2){
                $msg['text']['content'] = "您已经参加过活动了，不用再扫了哦。快去生成海报发起活动吧~";
                $msg2['text']['content'] = "您的朋友".$model_q->nickname."已经参加过活动了，不能再成为您的粉丝了";
                $wx->sendCustomMessage($msg2);
            }
            if ($has_subscribe==1){
                $msg['text']['content'] = "您已经关注过公众号了，不用再扫了哦。快去生成海报发起活动吧~";
                $msg2['text']['content'] = "您的朋友".$model_q->nickname."已经关注过公众号了，不能再成为您的粉丝了";
                $wx->sendCustomMessage($msg2);
            }
            //新加消息
            //已经有上线
            if ($model_q->fopenid && $nickname && $model_q->id) $msg['text']['content'] = "您是「{$nickname}」推荐的{$name}，不用再扫了哦。\n\n";
            //扫的二维码用户 已被取消
            if ($fromuser->id&&$fromuser->lv!=1) $msg['text']['content'] = "对不起您扫的二维码无效或者已过期 \n\n";
            //成功绑定
            if ($chunv) $msg['text']['content'] = "恭喜您经「{$nickname}」推荐成为了{$config['name']}的{$name}！\n\n";
            // if ($model_q->fopenid && $fopenidFromQrcode != $model_q->fopenid && $fuser->nickname) $msg['text']['content'] = "您是{$config['name']}第 {$model_q->id} 号{$config['title1']}，不用再扫了哦。\n\n";

            //自己扫自己的不发消息
            //if ($openid != $fopenid) $wx_result = $wx->sendCustomMessage($msg);

            //扫码后推送网址
            if ($config['qfx_desc']) {
                $msg['msgtype'] = 'text';
                $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                $wx_result = $wx->sendCustomMessage($msg);
            }
        }
        //菜单点击事件
        Kohana::$log->add('qwt_qfxkeyword:', print_r($this->Keyword,true));
        if(strpos($this->Keyword,$config['keyword'])!==false){

            $haibao = 1;
        }
        //Kohana::$log->add('qfxhaibao:', print_r($haibao,true));
        if ($userinfo && $Event == 'CLICK' || $haibao==1) {
            Kohana::$log->add('qwt_qfxhaibao:', 'aaa');
            Kohana::$log->add('qwt_qfxbbb:', $EventKey);
            Kohana::$log->add('qwt_qfxccc:', $config['key_qfxqrcode']);

            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

            //自定义 key
            // if ($EventKey == $config['key_c1_qfx'] && $config['value_c1_qfx']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1_qfx']);
            // }

            // else if ($EventKey == $config['key_c2_qfx'] && $config['value_c2_qfx']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2_qfx']);
            // }

            // else if ($EventKey == $config['key_c3_qfx'] && $config['value_c3_qfx']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3_qfx']);
            // }

            // else if ($EventKey == $config['key_c4_qfx'] && $config['value_c4_qfx']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4_qfx']);
            // }

            //生成海报
            // Kohana::$log->add('qfxbbb:', $EventKey);
            // Kohana::$log->add('qfxccc:', $config['key_qfxqrcode']);
            if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报' ||$haibao==1) {
                //申请审核？
                if(!Model::factory('select_experience')->dopinion($bid,'qfx')){
                    $msg['text']['content'] = '体验海报已用完，需要续费后才能正常使用，谢谢！';
                    Kohana::$log->add("qwt_qfxtiyan:$bid",print_r($msg,true));
                    $result=$wx->sendCustomMessage($msg);
                    Kohana::$log->add("qwt_qfxtiyan:$bid",print_r($result,true));
                    exit();
                }
                Kohana::$log->add('qfxhaibao:', 'bbb');
                if ($model_q->lv==0) {
                    $msg['text']['content'] = "您尚未通过分销审核，暂时不能生成海报，请点击【申请分销】进行申请。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $wx_result = $wx->sendCustomMessage($msg);
                    exit;
                }
                if ($model_q->lv==2) {
                    $msg['text']['content'] = "您的审核已经提交，暂时不能生成海报，请耐心等待审核通过。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $wx_result = $wx->sendCustomMessage($msg);
                    exit;
                }
                if ($model_q->lv==3) {
                    $msg['text']['content'] = "您已经被取消了分销商资格或审核未通过，暂时不能生成海报，请联系管理员。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $wx_result = $wx->sendCustomMessage($msg);
                    exit;
                }

                //有 ticket 并且没有超过 7 天 就不用重新生成
                $ticket_lifetime = 3600*24*7;
                //自定义过期时间
                if ($config['qfx_ticket_lifetime']) $ticket_lifetime = 3600*24*$config['qfx_ticket_lifetime'];

                $qrcode_type = 0;

                //if (!$model_q->lastupdate) $model_q->lastupdate = time();

                $config['text_qfxsend'] = str_replace('{ID}', $model_q->id, $config['text_qfxsend']);
                $config['text_qfxsend'] = str_replace('\n', "\n", $config['text_qfxsend']);

                // Kohana::$log->add('weixin_qfx:tpl_lifetime', time() - $model_q->lastupdate);

                if ( ($result['ticket'] = $model_q->ticket) && (time() - $model_q->lastupdate < $ticket_lifetime) ) {

                    //pass
                    //海报过期时间文案
                    $config['text_qfxsend'] = str_replace('{TIME}', date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime), $config['text_qfxsend']);
                    $msg['text']['content'] = $config['text_qfxsend'];

                    //更新用户信息
                    //$model_q->values($userinfo);
                    //$model_q->save();

                } else {

                    $time = time();

                    //永久二维码 只能有一个
                    // if ($model_q->id == 1) {
                        // $ticket_lifetime = 3600*24*365*3;
                        // $qrcode_type = 1;
                        // $time = time() + $ticket_lifetime;
                    // }

                    $result = $wx->getQRCode('qfx'.$bid, $qrcode_type, $ticket_lifetime);
                    $model_q->lastupdate = $time;

                    //海报过期时间文案
                    $config['text_qfxsend'] = str_replace('{TIME}', date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime), $config['text_qfxsend']);
                    $msg['text']['content'] = $config['text_qfxsend'];

                    //生成海报并保存
                    $model_q->values($userinfo);
                    $model_q->bid = $bid;
                    $model_q->ticket = $result['ticket'];
                    $model_q->save();

                    $newticket = true;
                }

                $wx_result = $wx->sendCustomMessage($msg);

                $md5 = md5($result['ticket'].time().rand(1,100000));

                //图片合成
                //模板
                $imgtpl = DOCROOT."qwt/qfx/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('qwt_qfxcfg')->where('bid', '=', $bid)->where('key', '=', 'qwtqfxtpl')->find();
                Kohana::$log->add("qwt_qfx:$bid:send:aaa",'aaa');
                if (!$tpl->pic) {
                    Kohana::$log->add("qwt_qfx:$bid:msg1:",print_r($msg,true));
                    $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                    $wx_result = $wx->sendCustomMessage($msg);
                    exit;
                }
                Kohana::$log->add("qwt_qfx:$bid:send:bbb",'bbb');
                if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);
                if (filesize($imgtpl) == 0) @unlink($imgtpl);

                if (!file_exists($imgtpl)) {
                    @mkdir(dirname($imgtpl));
                    @file_put_contents($imgtpl, $tpl->pic);
                }
                Kohana::$log->add("qwt_qfx:$bid:send:ccc",'ccc');
                //默认头像
                $tplhead = ORM::factory('qwt_qfxcfg')->where('bid', '=', $bid)->where('key', '=', 'qwtqfxtplhead')->find()->pic;
                $default_head_file = DOCROOT."qwt/qfx/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'qwt_qfx:qfxtpl:'.$openid.':'.$tpl->lastupdate;
                $uploadresult['media_id'] = $mem->get($tpl_key);
                Kohana::$log->add("qwt_qfx:$bid:send:ddd",'ddd');
                if ($uploadresult['media_id'] && !$newticket) {
                    //pass
                    // Kohana::$log->add('weixin_qfx:tpl_key', $tpl_key);
                    // Kohana::$log->add('weixin_qfx:media_id_cache', print_r($uploadresult, true));
                } else {

                    //获取参数二维码
                    $qrurl = $wx->getQRUrl($result['ticket']);
                    Kohana::$log->add("qwt_qfx:$bid:geturl:", '111');
                    $localfile = "{$tmpdir}$md5.jpg";
                    $remote_qrcode = curls($qrurl);
                    if (!$remote_qrcode) $remote_qrcode = curls($qrurl);
                    if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);

                    //获取头像
                    $headfile = "{$tmpdir}$md5.head.jpg";

                    //IP 获取
                    //http://182.254.104.16/mmopen/ajNVdqHZLLB1WVibay1icL4QZ4VWrLZriblYa9yBu7hia3AAERIvI4ysT3MhwoKpCbgC1WF7mBuHxhRHLhRbI7scUg/0
                    //http://wx.qlogo.cn/mmopen/ajNVdqHZLLAwad4e2M5lW5vNg6iaMSIkeNnt3oNfw84BWrg657rfeoLSico8eyyOV8mLXuSsx723UJntfZJLu4vA/132
                    $remote_head_url = $model_q->headimgurl;
                    $remote_head = curls($remote_head_url);

                    // if (!$remote_head) {
                    //     $remote_head_url = str_replace('/0', '/132', $model_q->headimgurl);
                    //     $remote_head = curls($remote_head_url);
                    // }

                    // //retry... 96px
                    // if (!$remote_head) {
                    //     $remote_head_url = str_replace('/132', '/96', $remote_head_url);
                    //     $remote_head = curls($remote_head_url);
                    // }

                    //获取失败用默认头像
                    // if (!$remote_head && $default_head) $remote_head = $default_head;
                    //获取失败用默认头像
                    if (!$remote_head && $default_head_file) {
                        $remote_head = file_get_contents($default_head_file);
                        Kohana::$log->add("qwt_qfx:$bid:head4:", $remote_head);
                    }

                    //写入临时头像文件
                    if ($remote_head) file_put_contents($headfile, $remote_head);

                    if (!$remote_head || !$remote_qrcode) {
                        $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                        $wx_result = $wx->sendCustomMessage($msg);
                        $model_q->ticket = '';
                        $model_q->save();
                        Kohana::$log->add("qwt_qfx:$bid:file:remote_head_url get ERROR!", $remote_head_url);

                        @unlink($headfile);
                        @unlink($localfile);
                        exit;
                    }

                    //合成
                    $dest = imagecreatefromjpeg($imgtpl);
                    $src_qrcode = imagecreatefromjpeg($localfile);
                    $src_head = imagecreatefromjpeg($headfile);

                    if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
                    if($src_head) imagecopyresampled($dest, $src_head, $config['px_head_left'], $config['px_head_top'], 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

                    $newfile = "{$tmpdir}$md5.new.jpg";
                    imagejpeg($dest, $newfile);
                    if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
                    if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);
                    Kohana::$log->add("qwt_qfx:$bid:newfile:", print_r($newfile,true));
                    if (file_exists($newfile)) {
                        $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                        Kohana::$log->add("qwt_qfx:$bid:upload:", print_r($uploadresult,true));
                        if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) {
                            Kohana::$log->add("qwt_qfx:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                            if ($wx->errCode == 45009) {
                                $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                $wx_result = $wx->sendCustomMessage($msg);
                                exit;
                            }
                        }

                    } else {
                        $tmp = getimagesize($imgtpl);

                        Kohana::$log->add("qwt_qfx:$bid:newfile $newfile gen ERROR!");
                        Kohana::$log->add("qwt_qfx:$bid:imgtplfile:$imgtpl", file_exists($imgtpl));
                        Kohana::$log->add("qwt_qfx:$bid:imgtplfileinfo", print_r($tmp, true));

                        Kohana::$log->add("qwt_qfx:$bid:qrcodefile:$localfile", file_exists($localfile));
                        Kohana::$log->add("qwt_qfx:$bid:headfile:$headfile", file_exists($headfile));
                    }

                    unlink($localfile);
                    unlink($headfile);
                    unlink($newfile);

                    //Cache
                    if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                }
                Kohana::$log->add("qwt_qfx:$bid:msg1:",print_r($msg,true));
                $msg['touser'] = $openid;
                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                Kohana::$log->add("qwt_qfx:$bid:msg2:",print_r($msg,true));
                unset($msg['text']);
                Kohana::$log->add("qwt_qfx:$bid:openid出口: $openid");
                $wx_result = $wx->sendCustomMessage($msg);
            }

            // //资产明细
            // else if ($EventKey == $config['key_qfxscore'] || $EventKey == '资产查询') {
            //     if ($model_q->lv==0) {
            //         $msg['text']['content'] = '请先点击菜单【申请分销】完成申请后方可查看';
            //         $wx->sendCustomMessage($msg);
            //         exit;
            //     }
            //     if ($model_q->lv==2) {
            //         $msg['text']['content'] = "您的审核已经提交，暂时不能生成海报，请耐心等待审核通过。\n\n";
            //         $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
            //         $wx_result = $wx->sendCustomMessage($msg);
            //         exit;
            //     }
            //     if ($model_q->lv==3) {
            //         $msg['text']['content'] = "您已经被取消了分销商资格或审核未通过，暂时不能生成海报，请联系管理员。 \n\n";
            //         $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
            //         $wx_result = $wx->sendCustomMessage($msg);
            //         exit;
            //     }
            //     $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
            //     $msg['msgtype'] = 'news';

            //     $news_pic_file = 'score.jpg';
            //     $news_pic = $this->cdnurl.$news_pic_file;

            //     $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['title5'].'明细';
            //     $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['title5'] .'为 '. $model_q->score .'，点击查看明细...';
            //     // $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '点击查看明细...';
            //     $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
            //     $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;
            // }

            // else if ($EventKey) {
            //     $msg['msgtype'] = 'text';
            //     $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「全员分销」商户后台完成配置：'.$EventKey;
            //     Kohana::$log->add('qfxddd:', $txtReply);
            // }
            Kohana::$log->add("qwt_qfx:$bid:openid总出口: $openid");
            //检查 Auth 是否过期
            if ($wx_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("qwt_qfx:$bid:we_result", print_r($wx_result, true));
                //$wx->resetAuth();
            }
        }
        if($txtReply){
            $this->txtReply = $txtReply;
        }
        //默认文字回复
        // if ($txtReply) {
        //     Kohana::$log->add('qfxeee:', $txtReply);
        //     $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
        //     $txtresult = sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $txtReply);
        //     ob_flush();
        //     Kohana::$log->add("weixin_qfx:$bid:openid文字回复: $openid");
        //     echo $txtresult;
        // }

        // //默认图文回复
        // if ($newsReply) {
        //     Kohana::$log->add("weixin_qfx:$bid:openid图文回复: $openid");
        //     echo $wx->news($newsReply)->reply(array(), true);
        // }

    }
    public function end(){
        return $this->txtReply;
    }
    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！')
    {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔'.$this->config['title5'].'！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $title;

        $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#FF0000';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');

        $tplmsg['data']['keyword4']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword4']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';

        // Kohana::$log->add("weixin_qfx:$bid:tplmsg", print_r($tplmsg, true));
        return $this->wx->sendTemplateMessage($tplmsg);
    }
    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendNewMessage($openid, $nickname,$url, $remark='干的漂亮，请继续加油哦！')
    {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_new_friend'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您有新的好友【'.$nickname.'】加入！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        // $tplmsg['data']['keyword1']['value'] = $title;

        // $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2);
        // $tplmsg['data']['keyword2']['color'] = '#FF0000';

        // $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');

        // $tplmsg['data']['keyword4']['value'] = ''.number_format($total, 2);
        // $tplmsg['data']['keyword4']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';

        // Kohana::$log->add("weixin_qfx:$bid:tplmsg", print_r($tplmsg, true));
        return $this->wx->sendTemplateMessage($tplmsg);
    }

}
