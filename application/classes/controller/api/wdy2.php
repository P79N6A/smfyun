<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_Wdy extends Controller_API {

    var $token = 'jifenbao2015';

    var $FromUserName;
    var $Keyword;
    var $access_token;
    var $baseurl = 'http://jfb.smfyun.com/wdy/';
    var $cdnurl = 'http://cdn.jfb.smfyun.com/wdy/';
    var $cdnurl2 = 'http://jfb.dev.smfyun.com/wdy/';
    var $scorename;
    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "wdy";
        if (!is_numeric($bid)) $bid = ORM::factory('wdy_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('wdy_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by bole");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0)
    {
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/wdy/';
        Kohana::$log->add("strbname:", $bname);

        set_time_limit(15);
        Database::$default = "wdy";
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $mem = Cache::instance('memcache');


        $debug_bid = 1350;

        //username->bid
        //if (!is_numeric($bid))
        $biz = ORM::factory('wdy_login')->where('user', '=', $bname)->find();
        $bid = $biz->id;
        $config = ORM::factory('wdy_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;
        if ($debug) print_r($config);

        if (!$config['appid'] || !$config['appsecret'] || !$config['scene_id']) die('Not Config!');
        //if ($bid == $debug_bid) Kohana::$log->add('weixin2:config', print_r($config, true));
        if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
            $die = 1;
            $txtReply = '您的账号已过期！';
        }
        $we = new Wechat($config);
        $we->getRev();

        //DEBUG by bole
        // Kohana::$log->add('$GLOBALS', print_r($GLOBALS, true));
        // if ($bid == 2) Kohana::$log->add('$IPS', print_r($we->getServerIp(), true));

        if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:getRevData", print_r($we->getRevData(), true));

        if (!$we->checkAuth()) {
            $txtReply = 'appid 和 appsecret 配置不正确，请检查';
            if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:checkAuth", 'appid 和 appsecret 配置不正确，请检查');
        }
        $sname = ORM::factory('wdy_cfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $fromUsername = $we->getRevFrom();
        $toUsername = $we->getRevTo();
        $this->Keyword = $we->getRevContent();
        $openid = $this->FromUserName = $fromUsername;
        $userinfo = $we->getUserInfo($openid);

        if($die == 1){
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = '您的账号已过期！';
            $we->sendCustomMessage($msg);
            exit;
        }
        //取消关注扣积分
        if($config['btnn']==1){
            $child1=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->find_all();
            Kohana::$log->add("weixin2:aaaa", 'aaaa');
            foreach ($child1 as $a) {
                $chuserinfo=array();
                $chuserinfo = $we->getUserInfo($a->openid);
                Kohana::$log->add("weixin2:bbbb", 'bbbb');
                if ($chuserinfo['subscribe'] === 0) {
                    //Kohana::$log->add("weixin2:cccc", 'cccc');
                    $qg=ORM::factory('wdy_qgscore')->where('bid','=',$bid)->where('openid','=',$a->openid)->find()->openid;
                    $userobj0=ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $a->openid)->find();
                    $userobj0->subscribe=0;
                    $userobj0->save();
                   // Kohana::$log->add("weixin2:aaaa",print_r($qg,true));
                    if(!$qg){
                        Kohana::$log->add("weixin2:ddddd", 'ddddd');
                        $qg=ORM::factory('wdy_qgscore');
                        $qg->bid=$bid;
                        $qg->openid=$a->openid;
                        $qg->save();
                        $userobj0 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $a->openid)->find();
                        $userobj0->scores->scoreOut($userobj0, 9,$config['goal0']);
                        if($config['switch']==1){
                            $this->rsync($bid,$userobj0->openid,$access_token,-$config['goal0']);
                        }
                        $userobj1 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                        $userobj1->scores->scoreOut($userobj1, 10,$config['goal']);
                        if($config['switch']==1){
                            $this->rsync($bid,$userobj1->openid,$access_token,-$config['goal']);
                        }
                        $fopenid1 =ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->fopenid;
                        $userobj3 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenid1)->find()->id;
                        if($userobj3){
                            $userobj2 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenid1)->find();
                            $userobj2->scores->scoreOut($userobj2, 10,$config['goal2']);
                            if($config['switch']==1){
                                $this->rsync($bid,$userobj2->openid,$access_token,-$config['goal2']);
                            }
                        }
                    }
                }
            }
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM wdy_qrcodes Where `bid` = $bid and `fopenid`='$openid'")->execute()->as_array();
            $tempid=array();
            if($firstchild[0]['openid']==null)
            {
              $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
            }
            else
            {
              for($i=0;$firstchild[$i];$i++)
              {
                $tempid[$i]=$firstchild[$i]['openid'];
              }
            }
            $child2 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('fopenid','IN',$tempid)->find_all();
            foreach ($child2 as $b) {
                $chuserinfo=array();
                $chuserinfo = $we->getUserInfo($b->openid);
                if ($chuserinfo['subscribe'] === 0) {
                    $qg=ORM::factory('wdy_qgscore')->where('bid','=',$bid)->where('openid','=',$b->openid)->find()->openid;
                    $userobj0=ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $b->openid)->find();
                    $userobj0->subscribe=0;
                    $userobj0->save();
                    if(!$qg){
                        $qg=ORM::factory('wdy_qgscore');
                        $qg->bid=$bid;
                        $qg->openid=$b->openid;
                        $qg->save();
                        $userobj0 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $b->openid)->find();
                        $userobj0->scores->scoreOut($userobj0, 9,$config['goal0']);
                        if($config['switch']==1){
                            $this->rsync($bid,$userobj0->openid,$access_token,-$config['goal0']);
                        }
                        $userobj1 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                        $userobj1->scores->scoreOut($userobj1, 10,$config['goal2']);
                        if($config['switch']==1){
                            $this->rsync($bid,$userobj1->openid,$access_token,-$config['goal2']);
                        }
                        $fopenid1 =ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $b->openid)->find()->fopenid;
                        $userobj2 = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenid1)->find();
                        $userobj2->scores->scoreOut($userobj2, 10,$config['goal']);
                        if($config['switch']==1){
                            $this->rsync($bid,$userobj2->openid,$access_token,-$config['goal']);
                        }
                    }
                }
            }
        }
        //end
        Kohana::$log->add("weixin2:1111", '11111');

        if ($bid == $debug_bid) {
            // Kohana::$log->add("weixin2:$bid:WE", $we->errCode.':'.$we->errMsg.':token:'.$we->access_token);
            Kohana::$log->add("weixin2:$bid:userinfo", var_export($userinfo, true));
        }
        Kohana::$log->add("weixin2:22222", '22222');

        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("weixin2:$bid:WE", $we->errCode.':'.$we->errMsg);

            if ($we->errCode != 45009) {
                $key = "weixin2:$bid:resetAuth";
                $count = (int)$mem->get($key);
                $mem->set($key, ++$count, 0);
                Kohana::$log->add($key, $count);
                $we->resetAuth();
            }

            if (!$txtReply) $txtReply = '抱歉哦，消息一不小心走丢了，麻烦再次点击下，谢谢谅解～';
        }
        Kohana::$log->add("weixin2:3333", '33333');

        //关注事件
        $Event = $we->getRevEvent()['event'];
        $EventKey = $we->getRevEvent()['key'];
        $Ticket = $we->getRevTicket();
        Kohana::$log->add("weixin2:$bid:bname", print_r($Event, true));
        Kohana::$log->add("weixin2:$bid:bname", print_r($EventKey, true));
        Kohana::$log->add("weixin2:$bid:bname", print_r($Ticket, true));
        //获取地理位置事件
        // $Locationx = $we->getRevGeo()['x'];
        // $Locationy = $we->getRevGeo()['y'];
        //当前微信用户
        $model_q = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        if($access_token){
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            $client = new KdtApiOauthClient();
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        Kohana::$log->add("weixin2:$bid", '44444');
        Kohana::$log->add("wdy:$bid:scan0:$openid", print_r($Event.$EventKey,true));
        //扫码事件 || 扫码关注
        $scene_id = $config['scene_id'];
        if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($we->getRevSceneId() == $scene_id)) {
            //新用户
            Kohana::$log->add("wdy:$bid:scan:$openid", print_r($Event.$EventKey,true));

            if (!$model_q->id) {
                //Kohana::$log->add("weixin2:$bid:model_q", 'model_q');
                $model_q->bid = $bid;
                $model_q->values($userinfo);
                //$model_q->ip = Request::$client_ip;
                if ($userinfo) $model_q->save();
            }

            //根据 Ticket 获取二维码来源用户
            //小俞 -> 伯乐 oVOgUs0cGsvun_FM8-ywVpCd8AHk -> 珂心如意 oVOgUs9valnS-IN-NZzOmcxuAGuw -> 念念不忘 oVOgUs0vuImayOiVuD1Uf7SATZG8
            //珂心如意
            $fuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;

            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户&&当前用户未锁定&&上级未锁定
            if ($model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid && $ffopenid!=$openid&&$model_q->lock!=1&&$fuser->lock!=1) {

                //首次关注积分
                if (ORM::factory('wdy_score')->where('qid', '=', $model_q->id)->where('type', '=', 1)->count_all() == 0) {
                    if ($config['goal0'] > 0) {
                        $model_q = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                        $model_q->scores->scoreIn($model_q, 1, $config['goal0']);
                        if($config['switch']==1){
                            $this->rsync($bid,$model_q->openid,$access_token,$config['goal0']);
                        }
                    }
                }

                //先保存关系
                if ($model_q->id > $fuser->id) {
                    //处女？
                    $chunv = 1;

                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->save();

                    //男人袜女性积分减半
                    if ($bid == 2) {
                        if ($userinfo['sex'] != 1) $config['goal'] = $config['goal']/2;
                    }

                    //推荐人 积分增加处理   上一级用户
                    $fuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                    if ($config['goal'] > 0&&$fuser->lock!=1) {

                        $fuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                        $goal_result = $model_q->scores->scoreIn($fuser, 2, $config['goal'], $model_q->id);
                        if($config['switch']==1){
                            $this->rsync($bid,$fuser->openid,$access_token,$config['goal']);
                        }
                    }
                     $fuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                    //积分话术
                    $config['text_goal'] .= "您当前". $this->scorename ."为：{$fuser->score}";

                    $tpl = $config['text_goal'];
                    $msg['touser'] = $fopenidFromQrcode;
                    $msg['msgtype'] = 'text';
                    $content=sprintf($tpl, $userinfo['nickname']);
                    // $a=strpos($content,'<a');
                    // $b=strpos($content,'/a>');
                    // $c=substr($content,$a,$b-$a+3);
                    // $c =str_replace('"', '\"',$c);
                    $content =str_replace('\n', "\n",$content);
                    //$content =str_replace($c, "$c",$content);
                    $msg['text']['content'] = $content;
                    if ($goal_result) $we->sendCustomMessage($msg);
                    if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result_fuser", var_export($we_result, true).$we->errCode.':'.$we->errMsg);

                    //更新上一级用户的 userinfo
                    $fuserinfo = $we->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->values($fuserinfo);
                        $fuser->save();
                    }

                    //风险判断
                    if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0) {
                        //直接用户
                        $count2 = ORM::factory('wdy_qrcode', $fuser->id)->scores->where('type', '=', 2)->count_all();
                        //用是否生成海报判断真实下线
                        $count3 = ORM::factory('wdy_qrcode')->where('bid', '=', $fuser->bid)->where('fopenid', '=', $fuser->openid)->where('ticket', '<>', '')->count_all();
                        // //解除锁定
                        // if ($fuser->lock == 1 && $count3 > $config['risk_level2']) {
                        //     $fuser->lock = 0;
                        //     $fuser->save();
                        // }
                        //锁定用户
                        if ($fuser->lock == 0 && $count2 >= $config['risk_level1'] & $count3 <= $config['risk_level2']) {
                            $fuser->lock = 1;
                            $fuser->save();

                            //发消息通知上级
                            $msg['touser'] = $fopenidFromQrcode;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $config['text_risk'];
                            $we_result = $we->sendCustomMessage($msg);
                        }
                    }

                    //上一级推荐人 积分增加处理
                    //珂心如意
                    $ffuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                    if ($ffuser->fopenid) {
                        //返利给 $ffuser 的上线 $ffuser->fopenid;
                        $fffuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                        if ($fffuser && $config['goal2'] > 0&&$fffuser->lock!=1) {
                            $fffuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                            $goal2_result = ORM::factory('wdy_score')->scoreIn($fffuser, 3, $config['goal2'], $model_q->id);
                            if($config['switch']==1){
                                $this->rsync($bid,$fffuser->openid,$access_token,$config['goal2']);
                            }
                        }
                        $fffuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                        $config['text_goal2'] .= "您当前". $this->scorename ."为：{$fffuser->score}";
                        $nickname = $ffuser->nickname;
                        $tpl = $config['text_goal2'];
                        $msg['touser'] = $ffuser->fopenid;
                        $content=sprintf($tpl, $userinfo['nickname']);
                        // $a=strpos($content,'<a');
                        // $b=strpos($content,'/a>');
                        // $c=substr($content,$a,$b-$a+3);
                        // $c =str_replace('"', '\"',$c);
                        $content =str_replace('\n', "\n",$content);
                        //$content =str_replace($c, "$c",$content);
                        $msg['text']['content'] = $content;

                        if ($goal2_result) $we_result = $we->sendCustomMessage($msg);
                    }
                }
            }

            //已经有上级就直接取来
            else {
                //$fuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
                $fopenid = $fuser->openid;
            }

            //扫码后默认推送消息
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

            //remove emoji
            $nickname = $fuser->nickname;
            //$nickname = preg_replace('/[\xf0-\xf7].{3}/', '', $fuser->nickname);
            $nickname = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $nickname);



            // if ($model_q->fopenid && $fopenidFromQrcode != $model_q->fopenid && $fuser->nickname) $msg['text']['content'] = "亲，你已经是「{$nickname}」的支持者了，不能再扫了哦。";
            //如果 当前用户有效 && 当前用户有上级 && 来源二维码有效
            //自定义回复文本消息
            //读取数据库： $replySet = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'text_goal3')->find()->value;
            $replySet = $config['text_goal3'];
            $replySet2 = $config['text_goal4'];
            if(!$replySet){
                $replySet="恭喜您成为了「%s」的支持者";
            }
            if(!$replySet2){
                $replySet2="您已经是「%s」的支持者了，不用再扫了哦。";
            }
            if ($model_q->id && $model_q->fopenid && $fopenid){
                $fnickname = ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find()->nickname;
                $content=sprintf($replySet2,$fnickname);
                // $a=strpos($content,'<a');
                // $b=strpos($content,'/a>');
                // $c=substr($content,$a,$b-$a+3);
                // $c =str_replace('"', '\"',$c);
                $content =str_replace('\n', "\n",$content);
                //$content =str_replace($c, "$c",$content);
                $msg['text']['content'] = $content;
            }
            if ($chunv) {
                $content=sprintf($replySet,$nickname);
                // $a=strpos($content,'<a');
                // $b=strpos($content,'/a>');
                // $c=substr($content,$a,$b-$a+3);
                // $c =str_replace('"', '\"',$c);
                $content =str_replace('\n', "\n",$content);
                //$content =str_replace($c, "$c",$content);
                $msg['text']['content'] = $content;
            };

            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己无上线的不发消息
            Kohana::$log->add("wdy:$bid:sendCustomMessage", '111');
            if ($model_q->fopenid) $we_result = $we->sendCustomMessage($msg);
            Kohana::$log->add("wdy:$bid:sendCustomMessage", var_dump($we_result));
            //扫码后推送网址
            if ($config['text_follow_url']&&$fuser->lock!=1) {
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '活动说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                $we_result = $we->sendCustomMessage($msg);
                Kohana::$log->add("wdy:$bid:sendCustomMessage", var_dump($we_result));
            }
        }
        $model_q = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        //微信用户上传地理位置信息
        // if ($locationx && $locationy){
        //     $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $locationx. ',' . $locationy . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
        //     $ch = curl_init(); // 初始化一个 cURL 对象
        //     curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
        //     curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置curl参数，要求结果保存到字符串中还是输出到屏幕上
        //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        //     $res = curl_exec($ch); // 运行cURL，请求网页
        //     curl_close($ch); // 关闭一个curl会话
        //     $json_obj = json_decode($res, true);
        //     $nation = $json_obj['result']['address_component']['nation'];//国家
        //     $province = $json_obj['result']['address_component']['province'];//省份
        //     $city = $json_obj['result']['address_component']['city'];//城市
        //     $disrict = $json_obj['result']['address_component']['district'];//区
        //     $contenteet = $json_obj['result']['address_component']['street'];//街道
        //     $msg['text']['content'] = '$nation.$province.$city.$district.$contenteet';//$nation.$province.$city.$district.$contenteet
        //     $we_result = $we->sendCustomMessage($msg);
        // }
        //菜单点击事件
        Kohana::$log->add("weixin2:$bid", '1123');

        if ($userinfo && $Event == 'CLICK' || $chunv2) {

            if (!$model_q->id) {
                $model_q->bid = $bid;
                $model_q->values($userinfo);
                //$model_q->ip = Request::$client_ip;
                if ($userinfo) $model_q->save();
            }
            Kohana::$log->add("weixin2:$bid", '5555');
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $cksum = md5($model_q->openid.$config['appsecret'].date('Y-m-d'));
            //$pos = strpos($mystring, $findme); $findme是你要查找的字符，如果找到返回True，否则返回false
            $count = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $position = 0;
            $u_location = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
            for ($i=1; $i <=$count ; $i++) {
                $pro[$i] = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                $city[$i] = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                $dis[$i] = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                $pos[$i] = strpos($u_location, $p_location[$i]);
                if ($pos[$i]!==false) {
                    $position++;
                }
            }
            // $pos=implode(glue,$pos);
            // $msg['text']['content'] = $pos.$p_location[1].$p_location[2].$p_location[3].$position.$count;
            // $we->sendCustomMessage($msg);
            // exit;

            $status = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
         if(($position >0 && $status=='1')||$status=='0'||!$status){

            if ($EventKey == $config['key_c1'] && $config['value_c1']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1']);
                // $we_result = $we->sendCustomMessage($msg);
            }

            else if ($EventKey == $config['key_c2'] && $config['value_c2']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2']);
                // $we_result = $we->sendCustomMessage($msg);
            }

            else if ($EventKey == $config['key_c3'] && $config['value_c3']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3']);
                // $we_result = $we->sendCustomMessage($msg);
            }

            else if ($EventKey == $config['key_c4'] && $config['value_c4']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4']);
                // $we_result = $we->sendCustomMessage($msg);
            }

            //生成海报
            else if ($EventKey == $config['key_qrcode'] || $chunv || $EventKey == '生成海报') {

                $ticket_lifetime = 3600*24*7;
                //自定义过期时间      不可删

                //第二种 方案      不可删
                if ($config['needpay']&&$access_token) {
                    require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
                    $qr_id = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->needpost;
                    if($qr_id!=0&&$qr_id!=1){
                        //Kohana::$log->add('qr_id:', print_r($qr_id, true));//写入日志，可以删除
                        $client = new KdtApiOauthClient();
                        $method1 = 'kdt.trades.qr.get';
                        $params = [
                            'qr_id'=>$qr_id,
                            'status'=>'TRADE_RECEIVED',
                        ];
                        $results=$client->post($access_token,$method1,$params);
                        if($results['response']['qr_trades']) {
                            $qr = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                            $qr->needpost=1;
                            $qr->save();
                        }
                        //Kohana::$log->add('s1:', print_r($results, true));//写入日志，可以删除
                    }
                    $needpost = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->needpost;
                    if($needpost==1) {
                    }else{
                        $url = $this->baseurl.'index/'. $bid .'?url=check_post&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                        $msg['text']['content'] = $config['needtext'].'<a href="'.$url.'">点击购买</a>';
                        $we_result = $we->sendCustomMessage($msg);
                        exit;
                    }
                }
                if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

                $qrcode_type = 0;

                if ( ($result['ticket'] = $model_q->ticket) && (time() - $model_q->lastupdate < $ticket_lifetime) ) {

                    //pass
                    $msg['text']['content'] = $config['text_send'];

                    //更新用户信息
                    //$model_q->values($userinfo);
                    //$model_q->save();

                } else {

                    $time = time();

                    //永久二维码
                    if ($model_q->lock == 100) {
                        $ticket_lifetime = 3600*24*365*3;
                        $qrcode_type = 1;
                        $time = time() + $ticket_lifetime;
                    }

                    $result = $we->getQRCode($config['scene_id'], $qrcode_type, $ticket_lifetime);
                    $model_q->lastupdate = $time;

                    $msg['text']['content'] = $config['text_send'];

                    //生成海报并保存
                    $model_q->values($userinfo);
                    $model_q->bid = $bid;
                    $model_q->ticket = $result['ticket'];
                    $model_q->save();

                    $newticket = true;
                }

                // 3 条客服消息限制，这里不发了
                //$we_result = $we->sendCustomMessage($msg);

                $md5 = md5($result['ticket'].time().rand(1,100000));

                //图片合成
                //模板
                $imgtpl = DOCROOT."wdy/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
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
                $tplhead = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                $default_head_file = DOCROOT."wdy/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'wdy:tpl:'.$openid.':'.$tpl->lastupdate;
                $uploadresult['media_id'] = $mem->get($tpl_key);

                if ($bid == $debug_bid) $newticket = true;

                if ($uploadresult['media_id'] && !$newticket) {
                    //pass
                    // Kohana::$log->add('weixin2:tpl_key', $tpl_key);
                    // Kohana::$log->add('weixin2:media_id_cache', print_r($uploadresult, true));
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

                    //Cache
                    if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                }


                //海报发送前提醒消息
                $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「生成海报」菜单重新获取哦！';
                if ($bid == 64) $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「我要参加」菜单重新获取哦！';
                $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;

                $we_result = $we->sendCustomMessage($msg);

                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                unset($msg['text']);

                $we_result = $we->sendCustomMessage($msg);

                if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:img_msg", var_export($msg, true));
                if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result_img", var_export($we_result, true).$we->errCode.':'.$we->errMsg);
                exit;
            }

            //积分明细
            else if ($EventKey == $config['key_score'] || $EventKey == '积分查询') {
                if (!$model_q->openid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }
                $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_score.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."wdy/$news_pic_file")) $news_pic_file = 'news_score.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                // $we_result = $we->sendCustomMessage($msg);
            }

            /*//积分明细
            else if ($EventKey == $config['key_score'] || $EventKey == '积分查询') {
                if (!$model_q->openid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }
                $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'imgtpl/'.$bid.'/score1.jpg';
                // if (!file_exists(DOCROOT."wdy/$news_pic_file")) $news_pic_file = 'imgtpl/'.'$bid'.'/score1.jpg';
                $news_pic = $this->cdnurl2.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                // $we_result = $we->sendCustomMessage($msg);
            }*/



            //兑换商城
            else if ($EventKey == $config['key_item'] || $EventKey == '积分兑换') {
                if (!$model_q->openid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }

                $url = $this->baseurl.'index/'. $bid .'?url=items&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_order.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."wdy/$news_pic_file")) $news_pic_file = 'news_order.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                /*$news_pic_file = 'imgtpl/'.$bid.'/score2.jpg';
                // if (!file_exists(DOCROOT."wdy/$news_pic_file")) $news_pic_file = 'imgtpl/'.'$bid'.'/score1.jpg';
                $news_pic = $this->cdnurl2.$news_pic_file;*/

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'兑换';
                if ($bid == 64) $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '奖品兑换';

                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看可兑换的产品...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                //$we_result = $we->sendCustomMessage($msg);
            }

            //排行榜
            else if ($EventKey == $config['key_top'] || $EventKey == '积分排行') {
                if (!$model_q->openid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }

                $url = $this->baseurl.'index/'. $bid .'?url=top&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_top.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."wdy/$news_pic_file")) $news_pic_file = 'news_top.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                /*$news_pic_file = 'imgtpl/'.$bid.'/score3.jpg';
                // if (!file_exists(DOCROOT."wdy/$news_pic_file")) $news_pic_file = 'imgtpl/'.'$bid'.'/score1.jpg';
                $news_pic = $this->cdnurl2.$news_pic_file;*/

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'排行榜';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                //$we_result = $we->sendCustomMessage($msg);
            }

            else if ($EventKey) {
                $msg['msgtype'] = 'text';
                $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「积分宝」商户后台完成配置：'.$EventKey;

                //用户少的时候才回复 debug
                // if (ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->count_all() < 10)
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
                $url = $this->baseurl.'index/'. $bid .'?url=check_location&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $replyfront = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
                $replyend = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
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
            Kohana::$log->add("wdytxtresult", print_r($txtresult, true));
            echo $txtresult;
        }

        //默认图文回复
        if ($newsReply) {
            echo $we->news($newsReply)->reply(array(), true);
        }

        exit;
    }
    private function rsync($bid,$openid,$access_token,$chscore){
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        if($access_token){
            $client = new KdtApiOauthClient();
        }else{
            die('请在后台一键授权给有赞');
        }

        $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'kdt.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        $result=$client->post($access_token,$method,$params);
        $fans_id= $result['response']['user']['user_id'];
        if($qrcode->yz_score==0){
            $method = 'kdt.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $a=$client->post($access_token,$method,$params);

            $qrcode->yz_score=1;
            $qrcode->save();
            $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{

            if($chscore>=0){
                $method = 'kdt.crm.customer.points.increase';
                $params =[
                'fans_id' => $fans_id,
                'points' => $chscore,
                ];
                $a=$client->post($access_token,$method,$params);
            }else{
                $method = 'kdt.crm.customer.points.decrease';
                $params =[
                'fans_id' => $fans_id,
                'points' => -$chscore,
                ];
                $a=$client->post($access_token,$method,$params);
            }
        }
        $method = 'kdt.crm.fans.points.get';
        $params =[
        'fans_id' => $fans_id,
        ];
        $results=$client->post($access_token,$method,$params);
        $point = $results['response']['point'];
        if($point&&$point!=$qrcode->score){
            $score_change=$point-$qrcode->score;
            $qrcode->scores->scoreIn($qrcode,5,$score_change);
        }
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
