<?php defined('SYSPATH') or die('No direct script access.');

//鱼汤煲 api 地址
Class Controller_Api_ytb extends Controller_API {

    var $token = 'yutangbao2016';

    var $FromUserName;
    var $Keyword;
    var $access_token;
    var $baseurl;
    var $cdnurl = 'http://cdn.jfb.smfyun.com/ytb/';
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

        Database::$default = "ytb";
        if (!is_numeric($bid)) $bid = ORM::factory('ytb_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('ytb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@鱼汤煲 by 神码浮云");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0)
    {
        $postStr = file_get_contents("php://input");
        Kohana::$log->add("ytbpostStr", print_r($postStr, true));

        Database::$default = "ytb";
        $this->baseurl = 'http://'.$_SERVER["HTTP_HOST"].'/ytb/';
        set_time_limit(15);
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        Kohana::$log->add('11111','1111111');
        $mem = Cache::instance('memcache');

        $debug_bid = 100000;

        //username->bid
        //if (!is_numeric($bid))
        Kohana::$log->add('bnameme',print_r($bname,true));
        $biz = ORM::factory('ytb_login')->where('user', '=', $bname)->find();
        $bid = $biz->id;
        Kohana::$log->add('bid',print_r($bid,true));
        $this->config = $config = ORM::factory('ytb_cfg')->getCfg($bid);
        $this->access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;

        if ($debug) {
            echo $bid;
            print_r($config);
        }

        Kohana::$log->add('access_token',print_r($this->access_token,true));
        Kohana::$log->add('appid',print_r($config['appid'],true));
        Kohana::$log->add('appsecret',print_r($config['appsecret'],true));
        Kohana::$log->add('scene_id',print_r($config['scene_id'],true));
        if (!$this->access_token || !$config['appid'] || !$config['appsecret'] ) die('Not Config!');
        Kohana::$log->add('333','33333');
        if ($bid == $debug_bid) Kohana::$log->add('weixin_ytb:config', print_r($config, true));
        if ($biz->expiretime && strtotime($biz->expiretime) < time()) $txtReply = '您的鱼塘宝插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';

        $this->we = $we = new Wechat($config);
        $we->getRev();

        //DEBUG by bole
        // Kohana::$log->add('$GLOBALS', print_r($GLOBALS, true));
        // if ($bid == 2) Kohana::$log->add('$IPS', print_r($we->getServerIp(), true));

        if ($bid == $debug_bid) Kohana::$log->add("weixin_ytb:$bid:getRevData", print_r($we->getRevData(), true));
        if(substr($we->checkAuth(), 0,5)=='error'){
            $txtReply = $we->checkAuth();
        }
        if (!$we->checkAuth()) {
            $txtReply = 'appid 和 appsecret 配置不正确，请检查';
            if ($bid == $debug_bid) Kohana::$log->add("weixin_ytb:$bid:checkAuth", 'appid 和 appsecret 配置不正确，请检查');
        }

        $fromUsername = $we->getRevFrom();
        $toUsername = $we->getRevTo();
        $this->Keyword = $we->getRevContent();
        Kohana::$log->add('keyword:', $this->Keyword);
        $openid = $this->FromUserName = $fromUsername;
        $userinfo = $we->getUserInfo($openid);

        if ($bid == $debug_bid) {
            Kohana::$log->add("weixin_ytb:$bid:userinfo", var_export($userinfo, true));
        }

        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("weixin_ytb:$bid:WE", $we->errCode.':'.$we->errMsg);

            if ($we->errCode != 45009) {
                $key = "weixin_ytb:$bid:resetAuth";
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

        //当前微信用户
        $model_q = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //免 oauth 登录网址
        $cksum = md5($model_q->openid.$config['appsecret'].date('Y-m'));
        $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);

        //新用户
        if (!$model_q->id||!$model_q->nickname) {
            $newone = 1;
            $model_q->bid = $bid;
            $model_q->values($userinfo);
            if ($userinfo) $model_q->save();
        }
        //发放优惠券   点过链接的人
        if($model_q->fopenid&&$newone==1){
            $fopenid = $model_q->fopenid;
            $fuser = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            $num = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->where('subscribe','=',1)->count_all();
            $text = $fuser->nickname.'，恭喜您激活了一个新的支持者：'.$model_q->nickname;
            if($config['coupontpl']){
                $this->sendtplcoupon($fopenid,$config,$text,$we);
            }else{
                $msg['touser'] = $fopenid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $text;
                $we->sendCustomMessage($msg);
            }
            if($fuser->fopenid){//上上线存在
                $ffuser = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fuser->fopenid)->find();
                $text = $ffuser->nickname.'，恭喜您的好友'.$fuser->nickname.'激活了一个新的支持者：'.$model_q->nickname;
                if($config['coupontpl']){
                    $this->sendtplcoupon($ffuser->fopenid,$config,$text,$we);
                }else{
                    $msg['touser'] = $ffuser->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    $we->sendCustomMessage($msg);
                }
            }
            if(($num-$fuser->used) >= $config['rw_num']){
                //发优惠券
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
                $client = new YZTokenClient($access_token);

                $method = 'youzan.ump.coupon.take';
                $params = [
                    'coupon_group_id'=>$config['rw_value'],
                    'weixin_openid'=>$fopenid,
                 ];
                $results = $client->post($method, $this->methodVersion, $params, $files);
                // Kohana::$log->add("weixin_ytb:$bid:coupon", print_r($results, true));
                if($results['response']){
                    $text = '恭喜您，分享人数已达8人，30元优惠券已经精准砸向您的会员中心。欲查看请点击进入“并不贵－我的订单－－我的优惠劵”，在购买的时候可以自动使用哦~';
                    $fuser->used = $fuser->used+$config['rw_num'];
                    $fuser->save();
                }else{
                    $text = $results['error_response']['code'].':'.$results['error_response']['msg'];
                }
                if($config['coupontpl']){
                    $result = $this->sendtplcoupon($fopenid,$config,$text,$we);
                    // Kohana::$log->add("weixin_ytb:$bid:tpl", print_r($result, true));
                }else{
                    $msg['touser'] = $fopenid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    $result = $we->sendCustomMessage($msg);
                    // Kohana::$log->add("weixin_ytb:$bid:cus", print_r($result, true));
                }
            }
        }
        $scene_id = $config['scene_id'];
        if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($we->getRevSceneId() == $scene_id)) {
            Kohana::$log->add('ytb', $Event);
            $fuser = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;


            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户&&当前用户未锁定&&上级未锁定
            if ($model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid && $ffopenid!=$openid){

                //先保存关系
                if ($model_q->id > $fuser->id) {
                    //处女？
                    $chunv = 1;

                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->lv = 1;
                    $model_q->save();
                    $num = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->where('subscribe','=',1)->count_all();
                    if(($num-$fuser->used) >= $config['rw_num']){
                        //发优惠券
                        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                        $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
                        $client = new YZTokenClient($access_token);
                        $method = 'youzan.ump.coupon.take';
                        $params = [
                            'coupon_group_id'=>$config['rw_value'],
                            'weixin_openid'=>$fopenid,
                         ];
                        $results = $client->post($method, $this->methodVersion, $params, $files);
                        // Kohana::$log->add("weixin_ytb:$bid:coupon", print_r($results, true));
                        if($results['response']){
                            $text = '恭喜您，分享人数已达8人，30元优惠券已经精准砸向您的会员中心。欲查看请点击进入“并不贵－我的订单－－我的优惠劵”，在购买的时候可以自动使用哦~';
                            $fuser->used = $fuser->used+$config['rw_num'];
                            $fuser->save();
                        }else{
                            $text = $results['error_response']['code'].':'.$results['error_response']['msg'];
                        }
                        if($config['coupontpl']){
                            $result = $this->sendtplcoupon($fopenid,$config,$text,$we);
                            // Kohana::$log->add("weixin_ytb:$bid:tpl", print_r($result, true));
                        }else{
                            $msg['touser'] = $fopenid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $text;
                            $result = $we->sendCustomMessage($msg);
                            // Kohana::$log->add("weixin_ytb:$bid:cus", print_r($result, true));
                        }
                    }
                    //判断是否升级，发送升级文案
                    $all_score=$fuser->all_score;
                    $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->count_all();
                    $lv=$fuser->lv;
                    if($lv==1){
                        if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }elseif ($all_score>=$config['pool_score1']&&$last_num>=$config['pool_num1']){
                            $fuser->lv=2;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['second'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_er1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_er2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }elseif($lv==2){
                        if ($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }
                    //推荐人 积分增加处理   上一级用户
                    $fuser = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                    //积分话术
                    $config['text_goal'] .= "您当前可用". $config['score_name'] ."为：{$fuser->score}";

                    $tpl = $config['text_goal'];
                    $msg['touser'] = $fopenidFromQrcode;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = sprintf($tpl, $userinfo['nickname']);
                    $we->sendCustomMessage($msg);
                    // if ($goal_result) $we->sendCustomMessage($msg);
                    // if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result_fuser", var_export($we_result, true).$we->errCode.':'.$we->errMsg);

                    //更新上一级用户的 userinfo
                    // $fuserinfo = $we->getUserInfo($fuser->openid);
                    // if ($fuserinfo['subscribe'] == 0) {
                    //     if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:FuserInfo", print_r($fuserinfo, true));
                    //     $fuser->values($fuserinfo);
                    //     $fuser->save();
                    // }

                    //风险判断

                    //上一级推荐人 积分增加处理
                    //珂心如意
                    $ffuser = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                    if ($ffuser->fopenid) {
                        //返利给 $ffuser 的上线 $ffuser->fopenid;
                        $fffuser = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                        if ($fffuser && $config['goal2'] > 0) {

                            $fffuser = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                            // $goal2_result = ORM::factory('ytb_score')->scoreIn($fffuser, 3, $config['goal2'], $model_q->id);
                        }
                        $fffuser = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                        $config['text_goal2'] .= "您当前可用". $config['score_name'] ."为：{$fffuser->score}";
                        $nickname = $ffuser->nickname;
                        $tpl = $config['text_goal2'];
                        $msg['touser'] = $ffuser->fopenid;
                        $msg['text']['content'] = sprintf($tpl, $nickname);

                        $we->sendCustomMessage($msg);
                    }
                }
            }

            //已经有上级就直接取来
            else {
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
            //读取数据库： $replySet = ORM::factory('ytb_cfg')->where('bid', '=', $bid)->where('key', '=', 'text_goal3')->find()->value;
            $replySet = $config['text_goal3'];
            $replySet2 = $config['text_goal4'];
            if(!$replySet){
                $replySet="恭喜您成为了「%s」的支持者";
            }
            if(!$replySet2){
                $replySet2="您已经是「%s」的支持者了，不用再扫了哦。";
            }
            if ($model_q->id && $model_q->fopenid && $fopenid){
                $fnickname = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find()->nickname;
                $msg['text']['content'] = sprintf($replySet2,$fnickname);
            }
            if ($chunv) $msg['text']['content'] = sprintf($replySet,$nickname);

            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己无上线的不发消息

            if ($model_q->fopenid) $we_result = $we->sendCustomMessage($msg);
            //扫码后推送网址
            if ($config['text_follow_url']) {
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '鱼塘规则';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'yumiao.jpg';
                $we_result = $we->sendCustomMessage($msg);
            }
        }
        //菜单点击事件
        if ($userinfo && $Event == 'CLICK' ) {

            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

            //自定义 key
            if ($EventKey == $config['key_c1_ytb'] && $config['value_c1_ytb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1_ytb']);
            }

            else if ($EventKey == $config['key_c2_ytb'] && $config['value_c2_ytb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2_ytb']);
            }

            else if ($EventKey == $config['key_c3_ytb'] && $config['value_c3_ytb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3_ytb']);
            }

            else if ($EventKey == $config['key_c4_ytb'] && $config['value_c4_ytb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4_ytb']);
            }

            //资产明细
            else if ($EventKey == $config['key_ytbscore'] || $EventKey == '我的'.$config['score_name']) {
                $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'scores2.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '我的'.$config['score_name'];
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的可用'. $config['score_name'] .'为 '. $model_q->score .'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;
            }
            //积分商城
            else if ($EventKey == $config['key_ytbitem'] || $EventKey == $config['score_name'].'兑换') {
                $url = $this->baseurl.'index/'. $bid .'?url=items&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'new_order.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score_name'].'兑换';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的可用'. $config['score_name'] .'为 '. $model_q->score .'，点击进入...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;
            }
            //生成海报
            else if ($EventKey == $config['key_ytbqrcode'] || $chunv || $EventKey == '生成海报') {

                $ticket_lifetime = 3600*24*7;
                //自定义过期时间      不可删

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
                    // if ($model_q->lock == 100) {
                    //     $ticket_lifetime = 3600*24*365*3;
                    //     $qrcode_type = 1;
                    //     $time = time() + $ticket_lifetime;
                    // }

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
                $imgtpl = DOCROOT."ytb/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('ytb_cfg')->where('bid', '=', $bid)->where('key', '=', 'ytbtpl')->find();
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
                $tplhead = ORM::factory('ytb_cfg')->where('bid', '=', $bid)->where('key', '=', 'ytbtplhead')->find();
                $default_head_file = DOCROOT."ytb/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'ytb:tpl:'.$openid.':'.$tpl->lastupdate;
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
                    // if (!$remote_head && $default_head) $remote_head = $default_head;
                    //获取失败用默认头像
                    if (!$remote_head && $default_head_file) {
                        $remote_head = file_get_contents($default_head_file);
                        Kohana::$log->add("ytb:$bid:head4:", $remote_head);
                    }

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
            else if ($EventKey) {
                $msg['msgtype'] = 'text';
                $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到商户后台完成配置：'.$EventKey;
            }

            //检查 Auth 是否过期
            if ($we_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("weixin_ytb:$bid:we_result", print_r($we_result, true));
                //$we->resetAuth();
            }
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
    public function sendtext($openid,$keyword){
        if($this->config['coupontpl']){
            $result=$this->sendTemplateMessage1($openid,$this->config['coupontpl'],'','',$keyword);
        }else{
            $result=$this->sendCustomMessage1($openid,$keyword);
        }
        return $result;
    }
    public function sendCustomMessage1($openid,$km_text){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $km_text;
        $result=$this->we->sendCustomMessage($msg);
        return $result;
    }
    public function sendTemplateMessage1($openid,$mgtpl,$keyword,$keyword1,$keyword2){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $mgtpl;
        $tplmsg['url']=$url;
        $tplmsg['data']['first']['value']=urlencode($keyword);
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = urlencode($keyword1);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['remark']['value'] = urlencode($keyword2);
        $tplmsg['data']['remark']['color'] = '#FF0000';
        $result=$this->we->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        return $result;
    }
    private function sendtplcoupon($openid,$config,$text,$we) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $config['coupontpl'];

        $tplmsg['data']['keyword1']['value'] = '有赞优惠卷';
        $tplmsg['data']['keyword1']['color'] = '#999999';

        $tplmsg['data']['remark']['value'] = $text;
        $tplmsg['data']['remark']['color'] = '#999999';
        return $we->sendTemplateMessage($tplmsg);
    }
    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！')
    {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔'.$this->config['score_name'].'！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $title;

        $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#FF0000';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');

        $tplmsg['data']['keyword4']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword4']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';

        // Kohana::$log->add("weixin_ytb:$bid:tplmsg", print_r($tplmsg, true));
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

        // Kohana::$log->add("weixin_ytb:$bid:tplmsg", print_r($tplmsg, true));
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
