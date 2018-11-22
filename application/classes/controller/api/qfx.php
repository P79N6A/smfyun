<?php defined('SYSPATH') or die('No direct script access.');

//全员分销 api 地址
Class Controller_Api_qfx extends Controller_API {

    var $token = 'quanfenxiao2016';

    var $FromUserName;
    var $Keyword;
    var $access_token;
    var $baseurl;
    var $cdnurl = 'http://cdn.jfb.smfyun.com/qfx/';
    var $methodVersion='3.0.0';
    var $we;
    var $config;

    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "qfx";
        if (!is_numeric($bid)) $bid = ORM::factory('qfx_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@全员分销 by 1nnovator");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0)
    {
        Database::$default = "qfx";
        $this->baseurl = 'http://'.$_SERVER["HTTP_HOST"].'/qfx/';
        set_time_limit(15);
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        Kohana::$log->add('11111','1111111');
        $mem = Cache::instance('memcache');

        $debug_bid = 100000;

        //username->bid
        //if (!is_numeric($bid))
        Kohana::$log->add('bnameme',print_r($bname,true));
        $biz = ORM::factory('qfx_login')->where('user', '=', $bname)->find();
        $bid = $biz->id;
        Kohana::$log->add('bid',print_r($bid,true));
        $this->config = $config = ORM::factory('qfx_cfg')->getCfg($bid);
        $this->access_token=ORM::factory('qfx_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;

        if ($debug) {
            echo $bid;
            print_r($config);
        }

        Kohana::$log->add('access_token',print_r($this->access_token,true));
        Kohana::$log->add('appid',print_r($config['appid'],true));
        Kohana::$log->add('appsecret',print_r($config['appsecret'],true));
        Kohana::$log->add('scene_id',print_r($config['scene_id_qfx'],true));
        if (!$this->access_token || !$config['appid'] || !$config['appsecret'] || !$config['scene_id_qfx']) die('Not Config!');
        Kohana::$log->add('333','33333');
        if ($bid == $debug_bid) Kohana::$log->add('weixin_qfx:config', print_r($config, true));
        if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
            $txtReply = '您的全分销插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';
            $die =1;
        }

        $this->we = $we = new Wechat($config);
        $we->getRev();

        //DEBUG by bole
        // Kohana::$log->add('$GLOBALS', print_r($GLOBALS, true));
        // if ($bid == 2) Kohana::$log->add('$IPS', print_r($we->getServerIp(), true));

        if ($bid == $debug_bid) Kohana::$log->add("weixin_qfx:$bid:getRevData", print_r($we->getRevData(), true));
        if(substr($we->checkAuth(), 0,5)=='error'){
            $txtReply = $we->checkAuth();
        }
        if (!$we->checkAuth()) {
            $txtReply = 'appid 和 appsecret 配置不正确，请检查';
            if ($bid == $debug_bid) Kohana::$log->add("weixin_qfx:$bid:checkAuth", 'appid 和 appsecret 配置不正确，请检查');
        }

        $fromUsername = $we->getRevFrom();
        $toUsername = $we->getRevTo();
        $this->Keyword = $we->getRevContent();
        Kohana::$log->add('qfxfromUsername:', $fromUsername);
        Kohana::$log->add('qfxtoUsername:', $toUsername);
        Kohana::$log->add('qfxkeyword:', $this->Keyword);
        $openid = $this->FromUserName = $fromUsername;
        Kohana::$log->add("weixin_qfx:$bid:openid入口: $openid");
        $userinfo = $we->getUserInfo($openid);
        if($die == 1){
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = '您的全分销插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';
            $we->sendCustomMessage($msg);
            exit;
        }
        if ($bid == $debug_bid) {
            Kohana::$log->add("weixin_qfx:$bid:userinfo", var_export($userinfo, true));
        }

        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("weixin_qfx:$bid:WE", $we->errCode.':'.$we->errMsg);

            if ($we->errCode != 45009) {
                $key = "weixin_qfx:$bid:resetAuth";
                $count = (int)$mem->get($key);
                $mem->set($key, ++$count, 0);
                Kohana::$log->add($key, $count);
                $we->resetAuth();
            }

            $txtReply = '十分抱歉，活动今天参与人数已经达到微信规定的上限，麻烦您稍候再试，请谅解。';
        }

        //关注事件
        $Event = $we->getRevEvent()['event'];
        $EventKey = $we->getRevEvent()['key'];

        $Ticket = $we->getRevTicket();
        Kohana::$log->add('qfxevent:', print_r($we->getRevEvent(),true));
        // Kohana::$log->add('eventkey:', $this->Keyword);
        // Kohana::$log->add('ticket:', $this->Keyword);
        //当前微信用户
        $model_q = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        //取消关注 改变关注状态
        // $childs = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->find_all();
        // foreach ($childs as $k => $v) {
        //     $chuserinfo=array();
        //     $chuserinfo = $we->getUserInfo($v->openid);
        //     if($chuserinfo['subscribe']==0){
        //         $v->subscribe = 0;
        //         $v->save();
        //     }
        // }


        //免 oauth 登录网址
        $cksum = md5($model_q->openid.$config['appsecret'].date('Y-m'));
        $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);

        //新用户
        if (!$model_q->id) {
            $model_q->bid = $bid;
            $model_q->values($userinfo);
            if ($userinfo) $model_q->save();
        }

        //扫码事件 || 扫码关注
        $scene_id = $config['scene_id_qfx'];

        if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($we->getRevSceneId() == $scene_id)) {

            $fuser = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
            $fopenidFromQrcode = $fopenid = $fuser->openid;

            //如果 当前用户有效 && 当前用户没有上级 && 当前用户没有生成海报 && 来源二维码有效 && 来源用户 != 当前用户&&当前用户未锁定&&上级未锁定&&当前用户未提交过申请
            Kohana::$log->add("qid", $model_q->id);
            Kohana::$log->add("fopenid", $fopenid);
            Kohana::$log->add("ticket", $model_q->ticket);
            Kohana::$log->add("openid", $openid);
            Kohana::$log->add("lock1", $model_q->lock);
            Kohana::$log->add("lock2", $fuser->lock);
            if ($model_q->id && !$model_q->fopenid && !$model_q->ticket && $fopenid && $fopenid != $openid&&$fuser->lv==1&&$model_q->lv==0) {

                //先保存关系
                if ($model_q->id > $fuser->id) {
                    //处女？
                    $chunv = 1;
                    Kohana::$log->add("scan1", 'scan1');
                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->save();

                    //第一个
                    //推荐人 扫码奖励
                    Kohana::$log->add("scan", 'scan');
                    $cksum = md5($fuser->openid.$config['appsecret'].date('Y-m'));
                    $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($fuser->openid);

                    $msg['touser'] = $fopenidFromQrcode;
                    $msg['msgtype'] = 'text';

                    $msg['text']['content'] = "恭喜您推荐了{$model_q->nickname}加入\n\n<a href=\"$url\">点击查看明细</a>";

                    //模板消息
                    if ($config['msg_new_friend'])
                        $this->sendNewMessage($fuser->openid,$model_q->nickname,$url);
                    else
                        $we->sendCustomMessage($msg);


                    //更新上一级用户的 userinfo
                    $fuserinfo = $we->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("weixin_qfx:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->values($fuserinfo);
                        $fuser->save();
                    }

                }
            }

            //已经有上级就直接取来
            if($model_q->fopenid){
                $fuser = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
                $fopenid = $fuser->openid;
            }
            $fromuser = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
            //扫码后默认推送消息
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

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
            if ($model_q->id) $msg['text']['content'] = "您是{$config['name']}第 {$model_q->fid} 号{$name}，不用再扫了哦。\n\n";
            //已经有上线
            if ($model_q->fopenid && $nickname && $model_q->id) $msg['text']['content'] = "您是「{$nickname}」推荐的{$name}，不用再扫了哦。\n\n";
            //扫的二维码用户 已被取消
            if ($fromuser->id&&$fromuser->lv!=1) $msg['text']['content'] = "对不起您扫的二维码无效或者已过期 \n\n";
            //成功绑定
            if ($chunv) $msg['text']['content'] = "恭喜您经「{$nickname}」推荐成为了{$config['name']}的{$name}！\n\n";
            // if ($model_q->fopenid && $fopenidFromQrcode != $model_q->fopenid && $fuser->nickname) $msg['text']['content'] = "您是{$config['name']}第 {$model_q->id} 号{$config['title1']}，不用再扫了哦。\n\n";

            //自己扫自己的不发消息
            //if ($openid != $fopenid) $we_result = $we->sendCustomMessage($msg);

            //扫码后推送网址
            if ($config['qfx_desc']) {
                $msg['msgtype'] = 'text';
                $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                $we_result = $we->sendCustomMessage($msg);
            }
        }
        //菜单点击事件
        Kohana::$log->add('qfxkeyword:', print_r($this->Keyword,true));
        if(strpos($this->Keyword,$config['keyword'])!==false){

            $haibao = 1;
        }
        //Kohana::$log->add('qfxhaibao:', print_r($haibao,true));
        if ($userinfo && $Event == 'CLICK' || $haibao==1) {
            Kohana::$log->add('qfxhaibao:', 'aaa');
            Kohana::$log->add('qfxbbb:', $EventKey);
            Kohana::$log->add('qfxccc:', $config['key_qfxqrcode']);

            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

            //自定义 key
            if ($EventKey == $config['key_c1_qfx'] && $config['value_c1_qfx']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1_qfx']);
            }

            else if ($EventKey == $config['key_c2_qfx'] && $config['value_c2_qfx']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2_qfx']);
            }

            else if ($EventKey == $config['key_c3_qfx'] && $config['value_c3_qfx']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3_qfx']);
            }

            else if ($EventKey == $config['key_c4_qfx'] && $config['value_c4_qfx']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4_qfx']);
            }

            //生成海报
            // Kohana::$log->add('qfxbbb:', $EventKey);
            // Kohana::$log->add('qfxccc:', $config['key_qfxqrcode']);
            else if ($EventKey == $config['key_qfxqrcode'] || $chunv || $EventKey == '生成海报' ||$haibao==1) {
                //申请审核？
                Kohana::$log->add('qfxhaibao:', 'bbb');
                if ($model_q->lv==0) {
                    $msg['text']['content'] = "您尚未通过分销审核，暂时不能生成海报，请点击【申请分销】进行申请。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $we_result = $we->sendCustomMessage($msg);
                    exit;
                }
                if ($model_q->lv==2) {
                    $msg['text']['content'] = "您的审核已经提交，暂时不能生成海报，请耐心等待审核通过。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $we_result = $we->sendCustomMessage($msg);
                    exit;
                }
                if ($model_q->lv==3) {
                    $msg['text']['content'] = "您已经被取消了分销商资格或审核未通过，暂时不能生成海报，请联系管理员。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $we_result = $we->sendCustomMessage($msg);
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

                    $result = $we->getQRCode($config['scene_id_qfx'], $qrcode_type, $ticket_lifetime);
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

                $we_result = $we->sendCustomMessage($msg);

                $md5 = md5($result['ticket'].time().rand(1,100000));

                //图片合成
                //模板
                $imgtpl = DOCROOT."qfx/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('qfx_cfg')->where('bid', '=', $bid)->where('key', '=', 'qfxtpl')->find();
                if (!$tpl->pic) {
                    $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                    $we_result = $we->sendCustomMessage($msg);
                    exit;
                }

                if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);
                if (filesize($imgtpl) == 0) @unlink($imgtpl);

                if (!file_exists($imgtpl)) {
                    @mkdir(dirname($imgtpl));
                    @file_put_contents($imgtpl, $tpl->pic);
                }

                //默认头像
                $tplhead = ORM::factory('qfx_cfg')->where('bid', '=', $bid)->where('key', '=', 'qfxtplhead')->find()->pic;
                $default_head_file = DOCROOT."qfx/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'qfx:qfxtpl:'.$openid.':'.$tpl->lastupdate;
                $uploadresult['media_id'] = $mem->get($tpl_key);

                if ($uploadresult['media_id'] && !$newticket) {
                    //pass
                    // Kohana::$log->add('weixin_qfx:tpl_key', $tpl_key);
                    // Kohana::$log->add('weixin_qfx:media_id_cache', print_r($uploadresult, true));
                } else {

                    //获取参数二维码
                    $qrurl = $we->getQRUrl($result['ticket']);
                    $localfile = "{$tmpdir}$md5.jpg";
                    $remote_qrcode = curls($qrurl);
                    if (!$remote_qrcode) $remote_qrcode = curls($qrurl);
                    if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);

                    //获取头像
                    $headfile = "{$tmpdir}$md5.head.jpg";

                    //IP 获取
                    //http://182.254.104.16/mmopen/ajNVdqHZLLB1WVibay1icL4QZ4VWrLZriblYa9yBu7hia3AAERIvI4ysT3MhwoKpCbgC1WF7mBuHxhRHLhRbI7scUg/0
                    //http://wx.qlogo.cn/mmopen/ajNVdqHZLLAwad4e2M5lW5vNg6iaMSIkeNnt3oNfw84BWrg657rfeoLSico8eyyOV8mLXuSsx723UJntfZJLu4vA/132
                    $remote_head_url = str_replace('wx.qlogo.cn', '182.254.104.16', $model_q->headimgurl);
                    $remote_head = curls($remote_head_url);

                    if (!$remote_head) {
                        $remote_head_url = str_replace('/0', '/132', $model_q->headimgurl);
                        $remote_head = curls($remote_head_url);
                    }

                    //retry... 96px
                    if (!$remote_head) {
                        $remote_head_url = str_replace('/132', '/96', $remote_head_url);
                        $remote_head = curls($remote_head_url);
                    }

                    //获取失败用默认头像
                    // if (!$remote_head && $default_head) $remote_head = $default_head;
                    //获取失败用默认头像
                    if (!$remote_head && $default_head_file) {
                        $remote_head = file_get_contents($default_head_file);
                        Kohana::$log->add("qfx:$bid:head4:", $remote_head);
                    }

                    //写入临时头像文件
                    if ($remote_head) file_put_contents($headfile, $remote_head);

                    if (!$remote_head || !$remote_qrcode) {
                        $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                        $we_result = $we->sendCustomMessage($msg);
                        Kohana::$log->add("weixin_qfx:$bid:file:remote_head_url get ERROR!", $remote_head_url);

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

                    if (file_exists($newfile)) {
                        $uploadresult = $we->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) $uploadresult = $we->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) {
                            Kohana::$log->add("weixin_qfx:$bid:$newfile upload ERROR!", $we->errCode.':'.$we->errMsg);
                            if ($we->errCode == 45009) {
                                $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                $we_result = $we->sendCustomMessage($msg);
                                exit;
                            }
                        }

                    } else {
                        $tmp = getimagesize($imgtpl);

                        Kohana::$log->add("weixin_qfx:$bid:newfile $newfile gen ERROR!");
                        Kohana::$log->add("weixin_qfx:$bid:imgtplfile:$imgtpl", file_exists($imgtpl));
                        Kohana::$log->add("weixin_qfx:$bid:imgtplfileinfo", print_r($tmp, true));

                        Kohana::$log->add("weixin_qfx:$bid:qrcodefile:$localfile", file_exists($localfile));
                        Kohana::$log->add("weixin_qfx:$bid:headfile:$headfile", file_exists($headfile));
                    }

                    unlink($localfile);
                    unlink($headfile);
                    unlink($newfile);

                    //Cache
                    if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                }


                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                Kohana::$log->add("weixin_qfx:$bid:openid出口: $openid");
                $we_result = $we->sendCustomMessage($msg);
            }

            //资产明细
            else if ($EventKey == $config['key_qfxscore'] || $EventKey == '资产查询') {
                if ($model_q->lv==0) {
                    $msg['text']['content'] = '请先点击菜单【申请分销】完成申请后方可查看';
                    $we->sendCustomMessage($msg);
                    exit;
                }
                if ($model_q->lv==2) {
                    $msg['text']['content'] = "您的审核已经提交，暂时不能生成海报，请耐心等待审核通过。\n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $we_result = $we->sendCustomMessage($msg);
                    exit;
                }
                if ($model_q->lv==3) {
                    $msg['text']['content'] = "您已经被取消了分销商资格或审核未通过，暂时不能生成海报，请联系管理员。 \n\n";
                    $msg['text']['content'] .= str_replace('\n', "\n", $config['qfx_desc']);
                    $we_result = $we->sendCustomMessage($msg);
                    exit;
                }
                $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'score.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['title5'].'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['title5'] .'为 '. $model_q->score .'，点击查看明细...';
                // $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                $we->sendCustomMessage($msg);
                exit;
            }

            else if ($EventKey) {
                $msg['msgtype'] = 'text';
                $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「全员分销」商户后台完成配置：'.$EventKey;
                Kohana::$log->add('qfxddd:', $txtReply);
            }
            Kohana::$log->add("weixin_qfx:$bid:openid总出口: $openid");
            //检查 Auth 是否过期
            if ($we_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("weixin_qfx:$bid:we_result", print_r($we_result, true));
                //$we->resetAuth();
            }
        }

        //默认文字回复
        if ($txtReply) {
            Kohana::$log->add('qfxeee:', $txtReply);
            $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
            $txtresult = sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $txtReply);
            ob_flush();
            Kohana::$log->add("weixin_qfx:$bid:openid文字回复: $openid");
            echo $txtresult;
        }

        //默认图文回复
        if ($newsReply) {
            Kohana::$log->add("weixin_qfx:$bid:openid图文回复: $openid");
            ob_flush();
            $newsresult = $we->news($newsReply)->reply(array(), true);
            Kohana::$log->add("weixin_qfx:$bid:openid图文回复: ",$newsresult);
            echo $newsresult;
        }

        exit;
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
        return $this->we->sendTemplateMessage($tplmsg);
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
        return $this->we->sendTemplateMessage($tplmsg);
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
