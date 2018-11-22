<?php defined('SYSPATH') or die('No direct script access.');

    $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtwdb/';
    $this->cdnurl = 'http://cdn.jfb.smfyun.com/qwt/wdb/';
    $config = ORM::factory('qwt_wdbcfg')->getCfg($bid,1);
    $sname = ORM::factory('qwt_wdbcfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
    if($sname){
        $this->scorename = $sname;
    }else{
        $this->scorename = '积分';
    }

    $userinfo = $wx->getUserInfo($openid);
        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("qwtwdb:$bid:WE", $wx->errCode.':'.$wx->errMsg);

            if ($wx->errCode != 45009) {
                // $key = "weixin2:$bid:resetAuth";
                // $count = (int)$mem->get($key);
                // $mem->set($key, ++$count, 0);
                // Kohana::$log->add($key, $count);
                // $wx->resetAuth();
                $mem = Cache::instance('memcache');
                $cachename1 ='qwt.access_token'.$bid;
                $ctoken = $mem->delete($cachename1);
            }

            if (!$txtReply) $txtReply = '抱歉哦，消息一不小心走丢了，麻烦再次点击下，谢谢谅解～';
        }

        $EventKey = $wx->getRevEvent()['key'];
        $Ticket = $wx->getRevTicket();

        //获取地理位置事件
        // $Locationx = $wx->getRevGeo()['x'];
        // $Locationy = $wx->getRevGeo()['y'];
        //当前微信用户
        if(isset($_POST['poster'])){
            $openid = $_POST['openid'];
            Kohana::$log->add("qwtwdbopenid:", $openid);
        }
        $model_q = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //自己关注
        if($userinfo &&$Event =='subscribe'){
            //Kohana::$log->add("openid:$bid:验证url", $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid));
            $cksum = md5($openid.date('Y-m-d'));
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'news';
            $msg['news']['articles'][0]['title'] = '点击验证获得'.$config['score'];
            $msg['news']['articles'][0]['url'] = $this->baseurl.'index/'. $bid .'?url=qrcheck&cksum='. $cksum .'&openid='. base64_encode($openid);
            $msg['news']['articles'][0]['description'] = '特别注意：扫码关注的粉丝必须点击验证后才能获得'.$config['score'].'奖励；直接关注的粉丝可忽略；';
            $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'subscribe.png';
            $wx_result = $wx->sendCustomMessage($msg);
        }
        if(isset($_POST['poster'])){
            $Event = 'CLICK';
            //$EventKey = $config['key_qrcode'];
            $EventKey = '生成海报';
            $openid = $_POST['openid'];
            $userinfo = $wx->getUserInfo($openid);
            //Kohana::$log->add("qwtwdb:$bid:userinfo", print_r($userinfo, true));
        }
        if(strpos($this->Keyword,$config['keyword'])!==false){
            $haibao = 2;
        }
        //菜单点击事件
        if ($userinfo && $Event == 'CLICK' || $chunv2||$haibao==2) {
            // if (!$model_q->id) {
            //     $model_q->bid = $bid;
            //     $model_q->values($userinfo);
            //     //$model_q->ip = Request::$client_ip;
            //     if ($userinfo) $model_q->save();
            // }
            Kohana::$log->add("qwtwdb:", '1111');
            $status = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
            $userid = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->id;
            // if(!$userid){
            //     $msg['touser'] = $openid;
            //     $msg['msgtype'] = 'text';
            //     $msg['text']['content'] = '请点击验证';
            //     $wx->sendCustomMessage($msg);
            //     exit;
            // }
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $cksum = md5($openid.date('Y-m-d'));
            //$pos = strpos($mystring, $findme); $findme是你要查找的字符，如果找到返回True，否则返回false
            if($userid&&$status==1){//用户存在 并且地理位置打开了
                $count = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
                $position = 0;
                $u_location = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
                for ($i=1; $i <=$count ; $i++) {
                    $pro[$i] = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                    $city[$i] = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                    $dis[$i] = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                    $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                    $pos[$i] = strpos($u_location, $p_location[$i]);
                    if ($pos[$i]!==false) {
                        $position++;
                    }
                }
            }

         if(($position >0 && $status=='1')||$status=='0'||!$status||!$userid){
            $isvalue = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key','=','value_'.substr($EventKey,-2))->find()->value;
            if($isvalue&&substr($iskey, 0,4)!='http'){
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $isvalue);
            }
            // if ($EventKey == $config['key_c1'] && $config['value_c1']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1']);
            //     // $wx_result = $wx->sendCustomMessage($msg);
            // }

            // else if ($EventKey == $config['key_c2'] && $config['value_c2']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2']);
            //     // $wx_result = $wx->sendCustomMessage($msg);
            // }

            // else if ($EventKey == $config['key_c3'] && $config['value_c3']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3']);
            //     // $wx_result = $wx->sendCustomMessage($msg);
            // }

            // else if ($EventKey == $config['key_c4'] && $config['value_c4']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4']);
            //     // $wx_result = $wx->sendCustomMessage($msg);
            // }
            //生成海报
            else if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报'||$haibao==2) {
                if(Model::factory('select_experience')->dopinion($bid,'wdb')){
                    Kohana::$log->add("qwtwdb:$bid", '2222');
                    $model_q = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                    $ticket_lifetime = 3600*24*7;
                    if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

                    if ( ($result['ticket'] = $model_q->ticket) &&  (time()<explode('|',$model_q->ticket)[2]) ) {
                        Kohana::$log->add("weixin2:$bid:2", $model_q->ticket);
                        $msg['text']['content'] = $config['text_send'];

                    } else {
                        Kohana::$log->add("weixin2:$bid:3", $model_q->ticket);
                        $time = time()+$ticket_lifetime;

                        $result['ticket'] = $model_q->openid.'|'.$bid.'|'.$time;// ticket直接用openid和bid加密
                        $model_q->lastupdate = time();

                        $msg['text']['content'] = $config['text_send'];

                        //生成海报并保存
                        // $model_q->values($userinfo);
                        // $model_q->bid = $bid;
                        // $model_q->ticket = $result['ticket'];
                        // $model_q->save();
                        $fuopenid = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->fuopenid;
                        //Kohana::$log->add("openid:$bid:url", $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid));
                        if(!$fuopenid){
                            $cksum = md5($openid.date('Y-m-d'));
                            $msg['touser'] = $openid;
                            $msg['msgtype'] = 'news';
                            $msg['news']['articles'][0]['title'] = '点击验证生成海报';
                            $msg['news']['articles'][0]['url'] = $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid);
                            $msg['news']['articles'][0]['description'] = '特别注意：点击验证后才能生成海报';
                            $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'subscribe.png';
                            $wx_result = $wx->sendCustomMessage($msg);
                            exit;
                        }
                    }

                    $md5 = md5($result['ticket'].time().rand(1,100000));
                    $model_q->ticket = $result['ticket'];
                    $model_q->save();
                    //图片合成
                    //模板
                    $imgtpl = DOCROOT."qwt/wdb/tmp/tpl.$bid.jpg";
                    $tmpdir = '/dev/shm/';

                    //判断模板文件是否需要从数据库更新
                    $tpl = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                    if (!$tpl->pic) {
                        $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                        $wx_result = $wx->sendCustomMessage($msg);
                        exit;
                    }

                    if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);
                    //exit;
                    if (!file_exists($imgtpl)) {
                        @mkdir(dirname($imgtpl));
                        @file_put_contents($imgtpl, $tpl->pic);
                    }

                    //默认头像
                    $tplhead = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                    $default_head_file = DOCROOT."qwt/wdb/tmp/head.$bid.jpg";

                    if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                    if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);


                        //获取二维码
                        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
                        $qrurl =  'http://'.$_SERVER["HTTP_HOST"].'/qwtwdb/qrscan/'.$result['ticket'];
                        $localfile = "{$tmpdir}$md5.jpg";
                        QRcode::png($qrurl,$localfile,'L','6','2');
                        $remote_qrcode = $localfile;
                        //if (!$remote_qrcode) $remote_qrcode = QRcode::png($qrurl, false, 'L', '4');
                        //if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);
                        //获取头像

                        $headfile = "{$tmpdir}$md5.head.jpg";

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
                        if (!$remote_head && $default_head) $remote_head = file_get_contents($default_head_file);

                        //写入临时头像文件
                        if ($remote_head) file_put_contents($headfile, $remote_head);

                        if (!$remote_head || !$remote_qrcode) {
                            $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                            $wx_result = $wx->sendCustomMessage($msg);
                            $model_q->ticket = '';
                            $model_q->save();
                            Kohana::$log->add("weixin2:$bid:file:remote_head_url get ERROR!", $remote_head_url);

                            @unlink($headfile);
                            @unlink($localfile);
                            exit;
                        }

                        //合成
                        $dest = imagecreatefromjpeg($imgtpl);
                        $src_qrcode = imagecreatefrompng($localfile);
                        $src_head = imagecreatefromjpeg($headfile);

                        if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
                        if($src_head) imagecopyresampled($dest, $src_head, $config['px_head_left'], $config['px_head_top'], 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

                        $newfile = "{$tmpdir}$md5.new.jpg";
                        imagejpeg($dest, $newfile);
                        if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
                        if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);


                        if (file_exists($newfile)) {
                            $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                            if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                            if (!$uploadresult['media_id']) {
                                Kohana::$log->add("weixin2:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                                if ($wx->errCode == 45009) {
                                    $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                    $wx_result = $wx->sendCustomMessage($msg);
                                    exit;
                                }
                            } else {
                                //上传成功 pass
                                if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:$newfile upload OK!", print_r($uploadresult, true));
                            }

                        } else {
                            Kohana::$log->add("weixin2:$bid:newfile $newfile gen ERROR!");
                            Kohana::$log->add("weixin2:$bid:imgtplfile", file_exists($imgtpl));
                            Kohana::$log->add("weixin2:$bid:qrcodefile", file_exists($localfile));
                            Kohana::$log->add("weixin2:$bid:headfile", file_exists($headfile));
                        }

                        unlink($localfile);
                        unlink($headfile);
                        unlink($newfile);
                    //海报发送前提醒消息


                    $txtReply2 = '海报有效期到 '. date('Y-m-d H:i', explode('|',$model_q->ticket)[2]) .' 过期后请重新获取海报！';

                    $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;
                    //exit;
                    // if ($txtReply2) {
                    //     Kohana::$log->add('$wdb', 'wwwww');
                    //     $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
                    //     Kohana::$log->add('$wdb001', 'aaa');
                    //     $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
                    //     $result2 = sprintf($textTpl, $fromUsername, $toUsername, $timeStamp, 'text',$txtReply2);
                    //     Kohana::$log->add('$wdb00', print_r($result2, true));
                    //     $encryptMsg = '';
                    //     $errCode = $pc->encryptMsg($result2, $timeStamp, $nonce, $encryptMsg);
                    //     if ($errCode == 0) {
                    //         if ($bid == 1)Kohana::$log->add('$wdb', print_r($encryptMsg, true));
                    //         Kohana::$log->add('$wdb11', print_r($encryptMsg, true));
                    //     } else {
                    //         Kohana::$log->add('$wdb22', print_r($errCode, true));
                    //     }
                    //     ob_flush()
                    //     echo $encryptMsg;
                    //     exit;
                    // }
                    //echo $wx->text($txtReply2)->reply(array(), true);
                    //Kohana::$log->add('$wdb11', print_r($result, true));
                    Kohana::$log->add("msg:$bid:wdb", print_r($msg,true));
                    $wx_result = $wx->sendCustomMessage($msg);

                    $msg['msgtype'] = 'image';
                    $msg['image']['media_id'] = $uploadresult['media_id'];
                    unset($msg['text']);
                    $wx_result = $wx->sendCustomMessage($msg);
                    Kohana::$log->add("msg:$bid:tplimg", print_r($wx_result,true));

                    exit;
                }else{
                    $msg['touser'] = $openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '体验海报已用完，需要续费后才能正常使用，谢谢！';
                    $wx->sendCustomMessage($msg);
                    exit();
                }
            }

            //积分明细
            else if ($EventKey == 'score' || $EventKey == '积分查询') {
                if (!$model_q->fuopenid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $wx->sendCustomMessage($msg);
                    exit;
                }
                $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_score.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."qwt/wdb/$news_pic_file")) $news_pic_file = 'news_score.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['score'] .'为 '. $model_q->score .'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                // $wx_result = $wx->sendCustomMessage($msg);
            }

            //兑换商城
            else if ($EventKey == 'item' || $EventKey == '积分兑换') {
                if (!$model_q->fuopenid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $wx->sendCustomMessage($msg);
                    exit;
                }

                $url = $this->baseurl.'index/'. $bid .'?url=items&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_order.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."qwt/wdb/$news_pic_file")) $news_pic_file = 'news_order.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'兑换';
                if ($bid == 64) $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '奖品兑换';

                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['score'] .'为 '. $model_q->score .'，点击查看可兑换的产品...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;
                //Kohana::$log->add("weixin2:$bid:积分兑换", print_r($news_pic, true));
                //$wx_result = $wx->sendCustomMessage($msg);
            }

            //排行榜
            else if ($EventKey == 'top' || $EventKey == '积分排行') {
                if (!$model_q->fuopenid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $wx->sendCustomMessage($msg);
                    exit;
                }

                $url = $this->baseurl.'index/'. $bid .'?url=top&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_top.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."qwt/wdb/$news_pic_file")) $news_pic_file = 'news_top.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'排行榜';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;
                Kohana::$log->add('$wdb:jfph', print_r($newsReply, true));

                //$wx_result = $wx->sendCustomMessage($msg);
            }

            else if ($EventKey) {
                // $msg['msgtype'] = 'text';
                // $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「积分宝」商户后台完成配置：'.$EventKey;

                //用户少的时候才回复 debug
                // if (ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->count_all() < 10)
                //$wx_result = $wx->sendCustomMessage($msg);
            }

            //检查 Auth 是否过期
            if ($wx_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result", print_r($wx_result, true));
                //$wx->resetAuth();
            }
            //$msg['text']['content'] = '符合要求'.$u_location.$p_location;

          }
            else{
                //$msg['text']['content'] = '不符合要求'.$u_location.$p_location;
                $url = $this->baseurl.'index/'. $bid .'?url=check_location&cksum='. $cksum .'&openid='. base64_encode($openid);
                $replyfront = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
                $replyend = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
                $msg['text']['content'] = $replyfront.'<a href="'.$url.'">点击查看是否在活动范围内</a>'.$replyend;
            }
            $wx->sendCustomMessage($msg);
            //点击菜单先检测是否有地理位置
            //1 有地理位置且包含数据库中地理位置字符串 按照原计划执行
            //2 无地理位置或者地理位置不符合 发消息跳转重新获取地理位置链接
            //自定义 key
            exit;
        }

        //$txtReply2='默认文字回复';
        // if ($txtReply) {
        //     echo $result=$wx->text($txtReply)->reply(array(), true);
        // }


        // //默认图文回复
        // if ($newsReply) {
        //     Kohana::$log->add('$wdb:tuwen', 'aaaa');
        //     echo $result=$wx->news($newsReply)->reply(array(), true);
        //     Kohana::$log->add('$wdb:tuwen', print_r($result, true));
        // }

        // exit;


    //检查签名

    //$txtReply = '进入订阅号';
