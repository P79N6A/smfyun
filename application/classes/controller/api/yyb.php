<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_wdb extends Controller_API {

    var $token = 'weidingbao';

    var $FromUserName;
    var $Keyword;

    var $baseurl = 'http://wdb.smfyun.com/wdb/';
    var $cdnurl = 'http://cdn.jfb.smfyun.com/wdb/';

    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "wdb";
        if (!is_numeric($bid)) $bid = ORM::factory('wdb_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('wdb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by bole");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0)
    {
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/wdb/';
        Kohana::$log->add("wdb:", $_POST['poster']);
        //exit;
        set_time_limit(15);
        Database::$default = "wdb";
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $mem = Cache::instance('memcache');

        $debug_bid = 1350;
        if(isset($_POST['poster'])){
            $bname = $_POST['poster'];
            Kohana::$log->add("wdb:", print_r($bname, true));
        }
        //username->bid
        //if (!is_numeric($bid))
        $biz = ORM::factory('wdb_login')->where('user', '=', $bname)->find();
        $bid = $biz->id;
        $config = ORM::factory('wdb_cfg')->getCfg($bid);

        if ($debug) print_r($config);

        if (!$config['appid'] || !$config['appsecret']) die('Not Config!');
        //if ($bid == $debug_bid) Kohana::$log->add('weixin2:config', print_r($config, true));
        if ($biz->expiretime && strtotime($biz->expiretime) < time()) $txtReply = '您的预约宝插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';

        $we = new Wechat($config);
        $we->getRev();

        //DEBUG by bole
        // Kohana::$log->add('$GLOBALS', print_r($GLOBALS, true));
        // if ($bid == 2) Kohana::$log->add('$IPS', print_r($we->getServerIp(), true));

        if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:getRevData", print_r($we->getRevData(), true));
        if(substr($we->checkAuth(), 0,5)=='error'){
            $txtReply = $we->checkAuth();
        }
        if (!$we->checkAuth()) {
            $txtReply = 'appid 和 appsecret 配置不正确，请检查';
            if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:checkAuth", 'appid 和 appsecret 配置不正确，请检查');
        }
        $fromUsername = $we->getRevFrom();
        $toUsername = $we->getRevTo();
        $this->Keyword = $we->getRevContent();
        $openid = $this->FromUserName = $fromUsername;
        Kohana::$log->add("wdb:$bid:fromusername", print_r($fromUsername, true));
        $userinfo = $we->getUserInfo($openid);
        if ($bid == $debug_bid) {
            // Kohana::$log->add("weixin2:$bid:WE", $we->errCode.':'.$we->errMsg.':token:'.$we->access_token);
            Kohana::$log->add("wdb:$bid:userinfo", var_export($userinfo, true));
        }

        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("wdb:$bid:WE", $we->errCode.':'.$we->errMsg);

            if ($we->errCode != 45009) {
                $key = "weixin2:$bid:resetAuth";
                $count = (int)$mem->get($key);
                $mem->set($key, ++$count, 0);
                Kohana::$log->add($key, $count);
                $we->resetAuth();
            }

            if (!$txtReply) $txtReply = '抱歉哦，消息一不小心走丢了，麻烦再次点击下，谢谢谅解～';
        }

        //关注事件
        $Event = $we->getRevEvent()['event'];
        $EventKey = $we->getRevEvent()['key'];
        $Ticket = $we->getRevTicket();
        //获取地理位置事件
        // $Locationx = $we->getRevGeo()['x'];
        // $Locationy = $we->getRevGeo()['y'];
        //当前微信用户
        if(isset($_POST['poster'])){
            $openid = $_POST['openid'];

        }
        $model_q = ORM::factory('wdb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //自己关注
        if($userinfo &&$Event =='subscribe'){
            Kohana::$log->add("openid:$bid:验证url", $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid));
            $cksum = md5($openid.$config['appsecret'].date('Y-m-d'));
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'news';
            $msg['news']['articles'][0]['title'] = '点击验证获得'.$config['score'];
            $msg['news']['articles'][0]['url'] = $this->baseurl.'index/'. $bid .'?url=qrcheck&cksum='. $cksum .'&openid='. base64_encode($openid);
            $msg['news']['articles'][0]['description'] = '特别注意：扫码关注的粉丝必须点击验证后才能获得'.$config['score'].'奖励；直接关注的粉丝可忽略；';
            $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'subscribe.png';
            $we_result = $we->sendCustomMessage($msg);
        }
        if(isset($_POST['poster'])){
            $Event = 'CLICK';
            //$EventKey = $config['key_qrcode'];
            $EventKey = '生成海报';
            $openid = $_POST['openid'];
            $userinfo = $we->getUserInfo($openid);
            //Kohana::$log->add("wdb:$bid:userinfo", print_r($userinfo, true));
        }
        //菜单点击事件
        if ($userinfo && $Event == 'CLICK' || $chunv2) {
            // if (!$model_q->id) {
            //     $model_q->bid = $bid;
            //     $model_q->values($userinfo);
            //     //$model_q->ip = Request::$client_ip;
            //     if ($userinfo) $model_q->save();
            // }
            $status = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
            $userid = ORM::factory('wdb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->id;
            // if(!$userid){
            //     $msg['touser'] = $openid;
            //     $msg['msgtype'] = 'text';
            //     $msg['text']['content'] = '请点击验证';
            //     $we->sendCustomMessage($msg);
            //     exit;
            // }
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $cksum = md5($openid.$config['appsecret'].date('Y-m-d'));
            //$pos = strpos($mystring, $findme); $findme是你要查找的字符，如果找到返回True，否则返回false
            if($userid&&$status==1){//用户存在 并且地理位置打开了
                $count = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
                $position = 0;
                $u_location = ORM::factory('wdb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
                for ($i=1; $i <=$count ; $i++) {
                    $pro[$i] = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                    $city[$i] = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                    $dis[$i] = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                    $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                    $pos[$i] = strpos($u_location, $p_location[$i]);
                    if ($pos[$i]!==false) {
                        $position++;
                    }
                }
            }

         if(($position >0 && $status=='1')||$status=='0'||!$status||!$userid){
            $isvalue = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key','=','value_'.substr($EventKey,-2))->find()->value;
            if($isvalue&&substr($iskey, 0,4)!='http'){
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $isvalue);
            }
            // if ($EventKey == $config['key_c1'] && $config['value_c1']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1']);
            //     // $we_result = $we->sendCustomMessage($msg);
            // }

            // else if ($EventKey == $config['key_c2'] && $config['value_c2']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2']);
            //     // $we_result = $we->sendCustomMessage($msg);
            // }

            // else if ($EventKey == $config['key_c3'] && $config['value_c3']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3']);
            //     // $we_result = $we->sendCustomMessage($msg);
            // }

            // else if ($EventKey == $config['key_c4'] && $config['value_c4']) {
            //     $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4']);
            //     // $we_result = $we->sendCustomMessage($msg);
            // }
            //生成海报
            else if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报') {
                $model_q = ORM::factory('wdb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                $ticket_lifetime = 3600*24*7;
                if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

                if ( ($result['ticket'] = $model_q->ticket) &&  (time()<explode('|',$model_q->ticket)[2]) ) {
                    //Kohana::$log->add("weixin2:$bid:2", $model_q->ticket);
                    $msg['text']['content'] = $config['text_send'];

                } else {
                    // Kohana::$log->add("weixin2:$bid:3", $model_q->ticket);
                    $time = time()+$ticket_lifetime;

                    $result['ticket'] = $model_q->openid.'|'.$bid.'|'.$time;// ticket直接用openid和bid加密
                    $model_q->lastupdate = time();

                    $msg['text']['content'] = $config['text_send'];

                    //生成海报并保存
                    // $model_q->values($userinfo);
                    // $model_q->bid = $bid;
                    // $model_q->ticket = $result['ticket'];
                    // $model_q->save();
                    $fuopenid = ORM::factory('wdb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->fuopenid;
                    Kohana::$log->add("openid:$bid:url", $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid));
                    if(!$fuopenid){
                        $cksum = md5($openid.$config['appsecret'].date('Y-m-d'));
                        $msg['touser'] = $openid;
                        $msg['msgtype'] = 'news';
                        $msg['news']['articles'][0]['title'] = '点击验证生成海报';
                        $msg['news']['articles'][0]['url'] = $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid);
                        $msg['news']['articles'][0]['description'] = '特别注意：点击验证后才能生成海报';
                        $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'subscribe.png';
                        $we_result = $we->sendCustomMessage($msg);
                        exit;
                    }
                }

                $md5 = md5($result['ticket'].time().rand(1,100000));
                $model_q->ticket = $result['ticket'];
                $model_q->save();
                //图片合成
                //模板
                $imgtpl = DOCROOT."wdb/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                if (!$tpl->pic) {
                    $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                    $we_result = $we->sendCustomMessage($msg);
                    exit;
                }

                if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);

                if (!file_exists($imgtpl)) {
                    @mkdir(dirname($imgtpl));
                    @file_put_contents($imgtpl, $tpl->pic);
                }

                //默认头像
                $tplhead = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                $default_head_file = DOCROOT."wdb/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);


                    //获取二维码
                    require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
                    $qrurl =  'http://'.$_SERVER["HTTP_HOST"].'/wdb/qrscan/'.$result['ticket'];
                    $localfile = "{$tmpdir}$md5.jpg";
                    QRcode::png($qrurl,$localfile,'L','6','2');
                    $remote_qrcode = $localfile;
                    //if (!$remote_qrcode) $remote_qrcode = QRcode::png($qrurl, false, 'L', '4');
                    //if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);
                    //获取头像
                    $headfile = "{$tmpdir}$md5.head.jpg";

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
                    if (!$remote_head && $default_head) $remote_head = $default_head;

                    //写入临时头像文件
                    if ($remote_head) file_put_contents($headfile, $remote_head);

                    if (!$remote_head || !$remote_qrcode) {
                        $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                        $we_result = $we->sendCustomMessage($msg);
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
                        $uploadresult = $we->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) $uploadresult = $we->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) {
                            Kohana::$log->add("weixin2:$bid:$newfile upload ERROR!", $we->errCode.':'.$we->errMsg);
                            if ($we->errCode == 45009) {
                                $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                $we_result = $we->sendCustomMessage($msg);
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
                $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', explode('|',$model_q->ticket)[2]) .' 过期后请点击「生成海报」菜单重新获取哦！';
                $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;

                $we_result = $we->sendCustomMessage($msg);

                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                unset($msg['text']);

                $we_result = $we->sendCustomMessage($msg);
                //Kohana::$log->add("msg:$bid:tplimg", print_r($we_result,true));

                exit;
            }

            //积分明细
            else if ($EventKey == 'score' || $EventKey == '积分查询') {
                if (!$model_q->fuopenid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }
                $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_score.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."wdb/$news_pic_file")) $news_pic_file = 'news_score.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['score'] .'为 '. $model_q->score .'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                // $we_result = $we->sendCustomMessage($msg);
            }

            //兑换商城
            else if ($EventKey == 'item' || $EventKey == '积分兑换') {
                if (!$model_q->fuopenid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }

                $url = $this->baseurl.'index/'. $bid .'?url=items&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_order.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."wdb/$news_pic_file")) $news_pic_file = 'news_order.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'兑换';
                if ($bid == 64) $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '奖品兑换';

                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['score'] .'为 '. $model_q->score .'，点击查看可兑换的产品...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;
                Kohana::$log->add("weixin2:$bid:积分兑换", print_r($news_pic, true));
                //$we_result = $we->sendCustomMessage($msg);
            }

            //排行榜
            else if ($EventKey == 'top' || $EventKey == '积分排行') {
                if (!$model_q->fuopenid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }

                $url = $this->baseurl.'index/'. $bid .'?url=top&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_top.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."wdb/$news_pic_file")) $news_pic_file = 'news_top.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'排行榜';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                //$we_result = $we->sendCustomMessage($msg);
            }

            else if ($EventKey) {
                $msg['msgtype'] = 'text';
                $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「积分宝」商户后台完成配置：'.$EventKey;

                //用户少的时候才回复 debug
                // if (ORM::factory('wdb_qrcode')->where('bid', '=', $bid)->count_all() < 10)
                //$we_result = $we->sendCustomMessage($msg);
            }

            //检查 Auth 是否过期
            if ($we_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result", print_r($we_result, true));
                //$we->resetAuth();
            }
            //$msg['text']['content'] = '符合要求'.$u_location.$p_location;

          }
            else{
                //$msg['text']['content'] = '不符合要求'.$u_location.$p_location;
                $url = $this->baseurl.'index/'. $bid .'?url=check_location&cksum='. $cksum .'&openid='. base64_encode($openid);
                $replyfront = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
                $replyend = ORM::factory('wdb_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
                $msg['text']['content'] = $replyfront.'<a href="'.$url.'">点击查看是否在活动范围内</a>'.$replyend;
            }
            $we->sendCustomMessage($msg);
            //点击菜单先检测是否有地理位置
            //1 有地理位置且包含数据库中地理位置字符串 按照原计划执行
            //2 无地理位置或者地理位置不符合 发消息跳转重新获取地理位置链接
            //自定义 key
            exit;
        }

        //默认文字回复
        if ($txtReply) {
            $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
            $txtresult = sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $txtReply);
            echo $txtresult;
        }

        //默认图文回复
        if ($newsReply) {
            echo $we->news($newsReply)->reply(array(), true);
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
