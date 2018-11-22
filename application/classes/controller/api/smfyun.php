<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_smfyun extends Controller_API {
    var $FromUserName;
    var $Keyword;
    var $bid;
    var $baseurl;
    var $wx;
    var $token = 'smfyun';
    var $appId = 'wx4d981fffa8e917e7';
    var $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    var $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public function action_get($appid='')
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "qwt";
        if (!is_numeric($appid)) $bid = ORM::factory('qwt_login')->where('appid', '=', $appid)->find()->id;
        //$config = ORM::factory('wfb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@码上 by 1nnovator");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($appid='', $debug=0)
    {
        Database::$default = "qwt";
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('smfyun_appid', print_r($appid, true));
        Kohana::$log->add('smfyun', print_r($postStr, true));
        set_time_limit(15);
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $mem = Cache::instance('memcache');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $appid;//商户appid


        $debug_bid = 1;
        $biz = ORM::factory('qwt_login')->where('appid','=',$appid)->find();

        $wx = new Wxoauth($biz->id,$options);
        $this->wx = $wx;
        $bid = $biz->id;
        $wx->getRev();

        $cfg = ORM::factory('qwt_cfg')->getCfg(0,1);

        //微信全网验证
        $fromUsername = $wx->getRevFrom();
        $toUsername = $wx->getRevTo();
        $this->Keyword = $wx->getRevContent();
        Kohana::$log->add('qwtsmfyun', print_r($this->Keyword, true));
        $openid = $this->FromUserName = $fromUsername;
        //存入用户数据
        $userinfo = $wx->getUserInfo($openid);
        Kohana::$log->add("$openid", print_r($userinfo, true));
        $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if($userinfo['subscribe_time']){
            $subscribe->bid=$bid;
            $subscribe->creattime=$userinfo['subscribe_time'];
            $subscribe->openid=$openid;
            $subscribe->save();
        }
        if ($userinfo == false) {
            // die('UserInfo get error!');
            Kohana::$log->add("qwt_userinfo:$bid:wx", $wx->errCode.':'.$wx->errMsg);

            if ($wx->errCode != 45009) {
                $mem = Cache::instance('memcache');
                $cachename1 ='qwt.access_token'.$bid;
                $ctoken = $mem->delete($cachename1);
            }
            //一般情况是商户授权了两个同样的appid
            if (!$txtReply) $txtReply = $wx->errCode.':'.$wx->errMsg.'：抱歉哦，消息一不小心走丢了，麻烦再次操作下，谢谢谅解！！！';
            $result = $wx->text($txtReply)->reply(array(),true);
            echo $result;
            exit;
        }
        if(!$openid) Kohana::$log->add("qwt_smfyun_click_openid:$bid", print_r($userinfo,true).'openid未获取到');
        $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        //名字替换
        $userinfo['nickname'] = str_replace("'", "_", $userinfo['nickname']);
        if(!$qr_user->id){
            $qr_user->bid = $bid;
            $qr_user->values($userinfo);
        }else{//更新头像  跑路的不更新
            if($userinfo['nickname']&&$userinfo['headimgurl']){
                $qr_user->subscribe_time = $userinfo['subscribe_time'];
                $qr_user->jointime = time();
                $qr_user->nickname = $userinfo['nickname'];
                $qr_user->headimgurl = $userinfo['headimgurl'];
            }
            $qr_user->subscribe = $userinfo['subscribe'];
        }
        $qr_user->save();
        $nickname = $userinfo['nickname'];
        Kohana::$log->add('qwt_event:bid'.$bid.':openid:'.$openid, print_r($wx->getRevEvent(),true));
        $Event = $wx->getRevEvent()['event'];

        $EventKey = $wx->getRevEvent()['key'];
        if($toUsername == 'gh_3c884a361561'){
            Kohana::$log->add('wx_test_event1:$bid', print_r($wx->getRevEvent(),true));
            Kohana::$log->add('wx_test_event:$bid', print_r($Event,true));
            Kohana::$log->add('wx_test_keyword:$bid', print_r($this->Keyword,true));
            if ($Event) $txtReply = $Event.'from_callback';
            if ($this->Keyword == 'TESTCOMPONENT_MSG_TYPE_TEXT') $txtReply = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';

            if(strpos($this->Keyword, 'QUERY_AUTH_CODE') !== false) {
                // echo $wx->text('')->reply(array(),true);
                $auth_code = str_replace('QUERY_AUTH_CODE:', '', $this->Keyword);
                $msg['touser'] = $openid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $auth_code.'_from_api';
                $result = $wx->sendCustomMessage($msg);
                Kohana::$log->add("ws_test_message:$bid", print_r($result,true).print_r($msg, true));
            }
            Kohana::$log->add('wx_test_textreply:$bid', print_r($txtReply,true));
            //默认文字回复
            if ($txtReply) {
                $result = $wx->text($txtReply)->reply(array(),true);
                Kohana::$log->add('wx_test_textreply_result:$bid', print_r($result,true));
                echo $result;
            }
            exit;
        }

        $datatype = $wx->getRevData()['MsgType'];
        if(isset($_POST['poster'])){
            $datatype = 'event';
        }
        $buys = ORM::factory('qwt_buy')->where('bid', '=', $biz->id)->where('switch', '=', 1)->where('status', '=', 1)->where('expiretime','>',time())->find_all();//找出已购买未过期的 所有插件 开关switch
        if($Event=='unsubscribe'){
            Kohana::$log->add('qwt_event_unsubscribe:bid'.$bid.':openid:'.$openid, print_r($wx->getRevEvent(),true));
            foreach ($buys as $buy) {
                switch ($buy->item->alias) {
                    case 'wfb':
                        $wfbcfg=ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
                        if($wfbcfg['btnn']==1){
                            require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                            $smfy=new SmfyQwt();
                            $result=$smfy->wfbunfollow($bid,$openid);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        if($Event=='user_get_card'){
            Kohana::$log->add('qwt_event_user_get_card:bid'.$bid.':openid:'.$openid, print_r($wx->getRevEvent(),true));
            foreach ($buys as $buy) {
                switch ($buy->item->alias) {
                    case 'hby':
                        $cardid = $wx->getRevData()['Cardid'];
                        $weixin = ORM::factory('qwt_hbyweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('couponid','=',$cardid)->find();
                        $weixin->status = 'COUPON GET';
                        $weixin->save();
                        break;
                    default:
                        break;
                }
            }
        }
        if($Event=='subscribe'){
            Kohana::$log->add('qwtywm1_event_subscribe:bid'.$bid.':openid:'.$openid, print_r($wx->getRevEvent(),true));
            $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $subscribe->bid=$bid;
            $subscribe->creattime=time();
            $subscribe->openid=$openid;
            $subscribe->save();
            foreach ($buys as $buy) {
                switch ($buy->item->alias) {
                    case 'hby':
                        Kohana::$log->add('qwt_event_subscribe:bid'.$bid.':openid:'.$openid, 1);
                        $users = ORM::factory('qwt_hbyweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('ct','=',3)->find_all();
                        $config = ORM::factory('qwt_hbycfg')->getCfg($bid,1);
                        // $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyorders where bid=$bid and state = 1 ")->execute()->as_array();
                        // $result['all'] = number_format($money1[0]['money'],2);
                        $hby_buy = ORM::factory('qwt_login')->where('id','=',$bid)->find();
                        $result['all'] = $hby_buy->hby_money;
                        Kohana::$log->add('qwt_event_subscribe:bid'.$bid.':openid:'.$openid, 2);
                        foreach ($users as $key => $value) {
                            $lid = ORM::factory('qwt_hbylogin')->where('id','=',$value->from_lid)->find();//对应门店
                            $rid = ORM::factory('qwt_hbyrule')->where('id','=',$lid->rid)->find();
                            $rconfig = ORM::factory('qwt_hbyrcfg')->getCfg($value->from_lid);//门店config
                            $rules = $rid->as_array();
                            $new_arr = array_merge($rconfig,$rules);
                            $config = array_merge($config,$new_arr);
                            if($config['issub']==1){
                                if($value->couponid){//发卡券
                                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwthby/api_ticket/'.$value->couponid.'/'.$bid;
                                    $msg['touser'] = $openid;
                                    $msg['msgtype'] = 'text';
                                    $msg['text']['content'] = "<a href=\"".$url."\">点击领取微信卡券</a>";
                                    $result = $wx->sendCustomMessage($msg);
                                    Kohana::$log->add('qwt_event_hby_ticket:bid'.$bid.':openid:'.$openid, print_r($result,true));
                                    if($result['errcode']==0){
                                        $value->ct = 1;
                                        $value->rule_name = $config['name'];
                                        $value->couponid = $config['couponid'];
                                        $value->couponname = $config['couponname'];
                                        $value->status = 'COUPON GET';
                                    }else{
                                        $value->ct = 2;
                                        $value->error = $result['errcode'].$result['errmsg'];
                                    }
                                }else{//发送红包
                                    Kohana::$log->add('qwt_event_hby_hongbao:bid'.$bid.':openid:'.$value->id, 3);
                                    $user = ORM::factory('qwt_hbyqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
                                    $money = rand($config['moneyMin'],$config['money']);
                                    if($result['all']>=$money/100){
                                        $hbresult = $this->hongbao($config,$user->myopenid,$bid,$money,$wx);
                                        if($hbresult['result_code'] == 'SUCCESS'){//下发成功
                                            $value->ct = 1;
                                            $value->money = $money;
                                            $value->rule_name = $config['name'];
                                            $value->mch_billno=$hbresult['mch_billno'];

                                            $hby_buy->hby_money = number_format($hby_buy->hby_money-$value->money/100,2);
                                            $buser = ORM::factory('qwt_hbyorder');
                                            $buser->money = -number_format($value->money/100,2,'.','');
                                            $buser->bid = $value->bid;
                                            $buser->wxid = $value->id;
                                            $buser->left = $hby_buy->hby_money;
                                            $buser->state = 1;
                                            $hby_buy->save();
                                            $buser->save();
                                            // $result['content'] = '红包下发成功';
                                        }else{
                                            $value->ct = 2;
                                            $value->money = $money;
                                            $value->error = $hbresult['err_code'].$hbresult['return_msg'];
                                            // $result['content'] = $weixin->error;
                                        }
                                    }else{
                                        $value->ct = 2;
                                        $value->money = $money;
                                        $value->error = '账户余额不足，请前往后台充值';
                                    }
                                    if($value->errpr) $txtReply = $value->error;
                                }
                                $value->sendtime = time();
                                $value->nickname = $userinfo['nickname'];
                                $value->headimgurl = $userinfo['headimgurl'];
                                $value->save();
                            }
                        }
                        break;
                    case 'ywm':
                        Kohana::$log->add('qwtywm2_event_subscribe:bid'.$bid.':openid:'.$openid, 1);
                        $users = ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('ct','=',3)->find_all();
                        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid,1);
                        Kohana::$log->add('qwtywm33'.$bid.':openid:'.$openid,print_r($config,true));
                        if($config['ifattention']==1){
                            Kohana::$log->add('qwtywm3_event_subscribe:bid'.$bid.':openid:'.$openid, 2);
                            foreach ($users as $key => $value) {
                                $ywm_buy = ORM::factory('qwt_login')->where('id','=',$bid)->find();
                                $result['all'] = $ywm_buy->ywm_money;
                                Kohana::$log->add('qwt_event_subscribe:bid'.$bid.':openid:'.$openid, 2);
                                Kohana::$log->add('qwtywm4_event_subscribe:bid'.$bid.':openid:'.$value->id, 3);
                                $user = ORM::factory('qwt_ywmqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                                $money = rand($config['leastmoney'],$config['mostmoney']);
                                // $money1=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmorders where bid=$bid and state = 1 ")->execute()->as_array();
                                // $result['all'] = number_format($money1[0]['money'],2);

                                $money2=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmweixins where bid=$bid and ct = 1 ")->execute()->as_array();
                                $result['used'] = number_format($money2[0]['money']/100,2);
                                Kohana::$log->add('qwtywm5:bid'.$bid.':openid:'.$value->id, $result['all']);
                                Kohana::$log->add('qwtywm6:bid'.$bid.':openid:'.$value->id, $result['used']);
                                if($result['all']<$money/100){
                                    $value->ct = 2;
                                    $value->money = $money;
                                    $value->error ='余额不足，请充值';
                                }else{
                                    $hbresult = $this->hongbao($config,$user->myopenid,$bid,$money,$wx);
                                    Kohana::$log->add('qwtywm6:bid'.$bid.':openid:'.$value->id, print_r($hbresult,true));
                                    if($hbresult['result_code'] == 'SUCCESS'){//下发成功
                                        // $value->ct = 1;
                                        // $value->money  = $money;
                                        // $result['content'] = '红包下发成功';
                                        $value->ct = 1;
                                        $value->money = $money;
                                        $value->mch_billno=$hbresult['mch_billno'];

                                        $ywm_buy->ywm_money = number_format($ywm_buy->ywm_money-$value->money/100,2);
                                        $buser = ORM::factory('qwt_ywmorder');
                                        $buser->money = -number_format($value->money/100,2,'.','');
                                        $buser->bid = $value->bid;
                                        $buser->wxid = $value->id;
                                        $buser->left = $ywm_buy->ywm_money;
                                        $buser->state = 1;
                                        $ywm_buy->save();
                                        $buser->save();
                                    }else{
                                        $value->ct = 2;
                                        $value->money = $money;
                                        $value->error = $hbresult['err_code'].$hbresult['return_msg'];
                                        // $result['content'] = $weixin->error;
                                    }
                                }
                                $value->sendtime = time();
                                $value->nickname = $userinfo['nickname'];
                                $value->headimgurl = $userinfo['headimgurl'];
                                $value->save();
                            }
                        }
                        break;
                    case 'zdf':
                        $follow = ORM::factory('qwt_zdffollow')->where('bid','=',$bid)->find();
                        if ($follow->switch==1) {
                            $msg['touser'] = $openid;
                            $msg['msgtype'] = 'miniprogrampage';
                            $msg['miniprogrampage']['title'] = $follow->msg->title;
                            $msg['miniprogrampage']['appid'] = $follow->msg->appid;
                            if ($follow->msg->path) {
                                $msg['miniprogrampage']['pagepath'] = $follow->msg->path;
                            }
                            $msg['miniprogrampage']['thumb_media_id'] = $follow->msg->media_id;
                            $wx_result = $wx->sendCustomMessage($msg);
                            Kohana::$log->add('zdf:follow:miniprogram:msg:'.$bid,print_r($msg,true));
                            Kohana::$log->add('zdf:follow:miniprogram:'.$bid,print_r($wx_result,true));
                            if ($follow->text) {
                                $msg['touser'] = $openid;
                                $msg['msgtype'] = 'text';
                                $msg['text']['content'] = '@'.$userinfo['nickname'].','.$follow->text;
                                $wx_result = $wx->sendCustomMessage($msg);
                                Kohana::$log->add('zdf:follow:text:'.$bid,print_r($wx_result,true));
                            }
                        }
                        break;
                    case 'yyhb':
                        $task = ORM::factory('qwt_yyhbtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
                        if ($task->state == 1) {
                            $yyhbuser = ORM::factory('qwt_yyhbqrcode')->where('openid','=',$openid)->where('bid','=',$bid)->find();
                            if ($yyhbuser->need_subscribe==1) {
                                $url = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_userinfo/'.$bid.'/yyhb/yyhb';
                                $msg['touser'] = $openid;
                                $msg['msgtype'] = 'text';
                                $msg['text']['content'] = "<a href=\"".$url."\">点击参加口令红包活动</a>";
                                $result = $wx->sendCustomMessage($msg);
                                Kohana::$log->add('qwt_event_yyhb_subscribe:bid'.$bid.':openid:'.$openid, print_r($result,true));
                                $yyhbuser->subscribe=1;
                                $yyhbuser->need_subscribe=0;
                                $yyhbuser->save();
                            }
                        }
                        break;
                    case 'kjb':
                        $kjbuser = ORM::factory('qwt_kjbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                        if (!$kjbuser->need_subscribe==0) {
                            $url = $kjbuser->pushurl;
                            $msg['touser'] = $openid;
                            $msg['msgtype'] = 'text';
                            if ($kjbuser->need_subscribe==1) {
                                $msg['text']['content'] = "<a href=\"".$url."\">点击此处继续帮ta砍价</a>";
                            }else{
                                $msg['text']['content'] = "<a href=\"".$url."\">点击此处继续发起砍价活动</a>";
                            }
                            $result = $wx->sendCustomMessage($msg);
                            Kohana::$log->add('qwt_event_kjb_subscribe:bid'.$bid.':openid:'.$openid, print_r($result,true));
                            $kjbuser->subscribe=1;
                            $kjbuser->need_subscribe=0;
                            $kjbuser->save();
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        if ($Event == 'TEMPLATESENDJOBFINISH') {
            Kohana::$log->add($datatype.':'.$bid.':回复空串_模板消息推送回复', print_r($result,true));
            ob_clean();
            ob_flush();
            echo '';
            exit;
        }
        if($Event == 'LOCATION'){
            $gendar = $userinfo['sex'];
            $xxb = ORM::factory('qwt_buy')->where('bid', '=', $biz->id)->where('switch', '=', 1)->where('status', '=', 1)->where('iid','=',12)->where('expiretime','>',time())->find();//查看xxb是否合法
            Kohana::$log->add('xxb', $biz->id);
            Kohana::$log->add('xxb', $xxb->id);
            if($xxb->id){
                $max_send = ORM::factory('qwt_xxbcfg')->where('bid','=',$bid)->where('key','=','max_send')->find()->value;
                Kohana::$log->add('xxb:max_send', $max_send);
                $xxb_user = ORM::factory('qwt_xxbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if(!$xxb_user->id){
                    $xxb_user->bid = $bid;
                    $xxb_user->qid = $qr_user->id;
                    $xxb_user->values($userinfo);
                }
                $xxb_user->save();
                $sends_num = ORM::factory('qwt_xxbscore')->where('bid','=',$bid)->where('qid','=',$xxb_user->id)->where('lastupdate','>',strtotime(date("Y-m-d"),time()))->count_all();
                Kohana::$log->add('xxb:has_send', $sends_num);
                if($max_send>$sends_num){
                    Kohana::$log->add('qwt_eventlocation:bid'.$bid.':openid:'.$openid, print_r($wx->getRevEventGeo(),true));
                    $locationx = $wx->getRevEventGeo()['x'];
                    $locationy = $wx->getRevEventGeo()['y'];
                    if ($locationx && $locationy){
                        if($cfg['map_qq']==1){
                            $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $locationx. ',' . $locationy . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
                            $ch = curl_init(); // 初始化一个 cURL 对象
                            curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
                            curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置curl参数，要求结果保存到字符串中还是输出到屏幕上
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                            $res = curl_exec($ch); // 运行cURL，请求网页
                            curl_close($ch); // 关闭一个curl会话
                            $json_obj = json_decode($res, true);
                            $pro = $json_obj['result']['address_component']['province'];//省份
                            $city = $json_obj['result']['address_component']['city'];//城市
                            $dis = $json_obj['result']['address_component']['district'];//区
                            $qr_user->area = $pro.$city.$dis;
                            $qr_user->save();
                            if($pro == $city){
                                $city = $dis;
                                $dis = '';
                            }
                        }else{
                            $host = "http://jisujwddz.market.alicloudapi.com";
                            $path = "/geoconvert/coord2addr";
                            $method = "GET";
                            $appcode = "5ee84130544445bf875bbb1d3a017a71";
                            $headers = array();
                            array_push($headers, "Authorization:APPCODE " . $appcode);
                            $querys = "lat=".$locationx."&lng=".$locationy."&type=baidu";
                            $bodys = "";
                            $url = $host . $path . "?" . $querys;

                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($curl, CURLOPT_FAILONERROR, false);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_HEADER, false);
                            if (1 == strpos("$".$host, "https://"))
                            {
                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                            }
                            $res = curl_exec($curl);
                            curl_close($curl); // 关闭一个curl会话
                            $json_obj = json_decode($res, true);
                            $pro = $json_obj['result']['province'];//省份
                            $city = $json_obj['result']['city'];//城市
                            $dis = $json_obj['result']['district'];//区
                            $qr_user->area = $pro.$city.$dis;
                            $qr_user->save();
                            if($pro == $city){
                                $city = $dis;
                                $dis = '';
                            }
                        }
                        // $contenteet = $json_obj['result']['address_component']['street'];//街道
                    }
                }
                $lists = ORM::factory('qwt_xxblist')->where('exist','=',1)->where('bid','=',$bid)->find_all();
                foreach ($lists as $key => $value) {
                    $rules[$key]['sex'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','sex')->find()->value;
                    $rules[$key]['pro'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','pro')->find()->value;
                    $rules[$key]['city'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','city')->find()->value;
                    $rules[$key]['dis'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','dis')->find()->value;
                    $rules[$key]['rid'] = $value->id;
                }
                Kohana::$log->add('xxb:pro0', print_r($pro,true));
                Kohana::$log->add('xxb:city0', print_r($city,true));
                Kohana::$log->add('xxb:dis0', print_r($dis,true));
                foreach ($rules as $k => $v) {
                    if ($gendar==$v['sex']||$v['sex']==3) {
                        Kohana::$log->add('xxb:sex', print_r($gendar,true));
                        if ($pro==$v['pro']||$v['pro']=='全部省'||$v['pro']==''||$v['pro']==null) {
                            Kohana::$log->add('xxb:pro', print_r($pro,true));
                            if ($city==$v['city']||$v['city']=='全部市'||$v['city']==''||$v['city']==null) {
                                Kohana::$log->add('xxb:city', print_r($city,true));
                                if ($dis==$v['dis']||$v['dis']=='全部区'||$v['dis']==''||$v['dis']==null) {
                                    Kohana::$log->add('xxb:dis', print_r($dis,true));
                                    $rid = $v['rid'];
                                    Kohana::$log->add('xxb:rid', print_r($rid,true));
                                    break;
                                }
                            }
                        }
                    }
                }
                if($rid&&$max_send>$sends_num){//满足规则
                    $iid = ORM::factory('qwt_xxblist')->where('exist','=',1)->where('bid','=',$bid)->where('id','=',$rid)->find()->iid;
                    $item = ORM::factory('qwt_xxbitem')->where('bid','=',$bid)->where('id','=',$iid)->find();
                    if ($item->type == 0) {
                        $msgs = ORM::factory('qwt_xxbmsg')->where('bid', '=', $biz->id)->where('iid','=',$iid)->find_all();
                        Kohana::$log->add('xxb', print_r($msgs,true));
                        $newsReply = array();
                        foreach ($msgs as $k => $v) {
                            Kohana::$log->add('xxbbbb', $k);
                            $newsReply[$k]['Title'] = $xxb_user->nickname.' '.$v->title;
                            $newsReply[$k]['PicUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwtxxba/images/msg/'.$v->id.'?'.time().'.jpg';
                            $newsReply[$k]['Url'] = $v->url;
                        }
                        $has_send = 1;
                        Kohana::$log->add($datatype.':'.$bid.':xxb', print_r($newsReply,true));
                    }else{
                        $msg['touser'] = $openid;
                        $msg['msgtype'] = 'miniprogrampage';
                        $msg['miniprogrampage']['title'] = $item->title;
                        $msg['miniprogrampage']['appid'] = $item->appid;
                        if ($item->path) {
                            $msg['miniprogrampage']['pagepath'] = $item->path;
                        }
                        $msg['miniprogrampage']['thumb_media_id'] = $item->media_id;
                        $wx_result = $wx->sendCustomMessage($msg);
                        Kohana::$log->add('xxb:miniprogram:msg:'.$bid,print_r($msg,true));
                        Kohana::$log->add('xxb:miniprogram:'.$bid,print_r($wx_result,true));
                        if($wx_result['errcode'] == 0){//客服接口下发成功
                            $has_send = 1;
                        }
                    }
                    if($has_send == 1){
                        $xxb_score = ORM::factory('qwt_xxbscore');
                        $xxb_score->bid = $bid;
                        $xxb_score->iid = $iid;
                        $xxb_score->qid = $xxb_user->id;
                        $xxb_score->save();
                    }
                }
                // $newsReply = array(
                //             "0"=>array(
                //               'Title'=>'msg title',
                //               'PicUrl'=>'http://www.domain.com/1.jpg',
                //               'Url'=>'http://www.baidu.com'
                //             ),
                //             "1"=>array(
                //               'Title'=>'msg title2',
                //               'PicUrl'=>'http://www.domain.com/1.jpg',
                //               'Url'=>'http://www.smfyun.com'
                //             )
                //           );
            }else{//不存在也要
                //只有 某些应用会用到 rwb wfb wdb
                $product_arr = ['wfb','rwb','wdb','rwd'];
                foreach ($buys as $v) {
                    if(in_array($v->item->alias,$product_arr)){
                        $use_location = 1;
                    }
                }
                $locationx = $wx->getRevEventGeo()['x'];
                $locationy = $wx->getRevEventGeo()['y'];
                Kohana::$log->add('qwt_areax:', $locationx);
                Kohana::$log->add('qwt_areay:', $locationy);
                if ($locationx && $locationy && !$qr_user->area && $use_location==1){//只查询area不存在的
                    if($cfg['map_qq']==1){
                        $host = "http://jisujwddz.market.alicloudapi.com";
                        $path = "/geoconvert/coord2addr";
                        $method = "GET";
                        $appcode = "5ee84130544445bf875bbb1d3a017a71";
                        $headers = array();
                        array_push($headers, "Authorization:APPCODE " . $appcode);
                        $querys = "lat=".$locationx."&lng=".$locationy."&type=baidu";
                        $bodys = "";
                        $url = $host . $path . "?" . $querys;

                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_FAILONERROR, false);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_HEADER, false);
                        if (1 == strpos("$".$host, "https://"))
                        {
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        }
                        $res = curl_exec($curl);
                        curl_close($curl); // 关闭一个curl会话
                        $json_obj = json_decode($res, true);
                        $pro = $json_obj['result']['province'];//省份
                        $city = $json_obj['result']['city'];//城市
                        $dis = $json_obj['result']['district'];//区
                    }else{
                        $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $locationx. ',' . $locationy . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
                        $ch = curl_init(); // 初始化一个 cURL 对象
                        curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
                        curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置curl参数，要求结果保存到字符串中还是输出到屏幕上
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        $res = curl_exec($ch); // 运行cURL，请求网页
                        curl_close($ch); // 关闭一个curl会话
                        $json_obj = json_decode($res, true);
                        $pro = $json_obj['result']['address_component']['province'];//省份
                        $city = $json_obj['result']['address_component']['city'];//城市
                        $dis = $json_obj['result']['address_component']['district'];//区
                    }
                    $qr_user->area = $pro.$city.$dis;
                    $qr_user->save();
                    Kohana::$log->add('qwt_area:', $qr_user->area);
                    // $contenteet = $json_obj['result']['address_component']['street'];//街道
                }
            }
            Kohana::$log->add("qwtwfbarea$bid",'111');
            // foreach ($buys as $v) {
            //     Kohana::$log->add("qwtwfbarea$bid",$v->item->alias);
            //     if($v->item->alias=='wfb'){
            //         $wfbqrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
            //         Kohana::$log->add("qwtwfbarea$bid",$wfbqrcode->joinarea);
            //         if($wfbqrcode->joinarea==1){
            //             Kohana::$log->add('qwtwfbarea',$wfbqrcode->openid);
            //             require_once Kohana::find_file('vendor', 'area/wfb');
            //             $wfbarea = new wfb($bid,$wx,$openid,$wfbqrcode);
            //             $txtReply = $wfbarea->end();
            //         }
            //     }
            //     if($v->item->alias=='rwb'){
            //         $rwbqrcode=ORM::factory('qwt_rwbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
            //         Kohana::$log->add("qwtrwbarea$bid",$rwbqrcode->joinarea);
            //         if($rwbqrcode->joinarea==1){
            //             Kohana::$log->add('qwtrwbarea',$rwbqrcode->openid);
            //             require_once Kohana::find_file('vendor', 'area/rwb');
            //             $rwbarea = new rwb($bid,$wx,$openid,$rwbqrcode);
            //             $txtReply = $rwbarea->end();
            //             Kohana::$log->add('qwtrwbtext',$txtReply);
            //             $result = $wx->text($txtReply)->reply(array(),true);
            //             Kohana::$log->add($datatype.':'.$bid.':$textresult', print_r($result,true));
            //             ob_clean();
            //             ob_flush();
            //             echo $result;
            //             exit;
            //         }
            //     }
            // }
        }
        if($datatype=='text'){
            foreach ($buys as $k => $v) {
                $has_buy[$k] = $v->iid;
            }
            if(preg_match('/^\d{10}$/', $this->Keyword)){//红包
                if(in_array(1, $has_buy)){
                    Kohana::$log->add('$type', '进入红包');
                    require_once Kohana::find_file('vendor', 'api/hbb');
                    $qwt_hbb = new hbb($this->Keyword,$wx,$openid,$appid,$biz,$nickname);
                    $txtReply = $qwt_hbb->end();
                }else{
                    // $txtReply = '对不起，您的未购买【口令红包】产品或已关闭开关。';
                }
            }else{//盖楼
                if(in_array(6, $has_buy)){
                    Kohana::$log->add('$type', '进入盖楼');
                    require_once Kohana::find_file('vendor', 'api/gl');
                    $qwt_gl = new gl($this->Keyword,$wx,$bid,$openid,$userinfo,$biz,$appid);
                    $txtReplygl = $qwt_gl->end();
                }
                if(in_array(4, $has_buy)){
                    //$menu = ORM::factory('qwt_menu')->where('bid','=',$bid)->where('k_key','=',$EventKey)->find();
                    Kohana::$log->add("sssdadtype", '进入rwb');
                    require_once Kohana::find_file('vendor', 'api/rwb');
                    $qwt_rwb=new rwb($this->Keyword,$wx,$bid,$openid,$userinfo,$qr_user,$Event,$mem);
                    $txtReplyrwb = $qwt_rwb->end();
                }
                if(in_array(7, $has_buy)){
                    Kohana::$log->add("sssdadtype", '进入qfx');
                    require_once Kohana::find_file('vendor', 'api/qfx');
                    $qwt_qfx = new qfx($this->Keyword,$wx,$bid,$openid,$userinfo,$qr_user,$Event,$mem);
                    $txtReplyqfx = $qwt_qfx->end();
                }
                if(in_array(2, $has_buy)){
                    Kohana::$log->add("sssdadtype", '进入wdb');
                    require_once Kohana::find_file('vendor', 'api/wdb');
                }
                if(in_array(25, $has_buy)){
                    Kohana::$log->add("sssdadtype", '进入rwd');
                    require_once Kohana::find_file('vendor', 'api/rwd');
                }
                if(in_array(3, $has_buy)){
                    Kohana::$log->add("sssdadtype", '进入wfb');
                    require_once Kohana::find_file('vendor', 'api/wfb');
                }
                if(in_array(20, $has_buy)){
                    Kohana::$log->add("sssdadtype", '进入zdf');
                    require_once Kohana::find_file('vendor', 'api/zdf');
                }
            }
        }
        if($datatype=='event'){//点击菜单或扫码
            //关键字在表里面搜
            $menu = ORM::factory('qwt_menu')->where('bid','=',$bid)->where('type','=','_text')->where('k_key','=',$EventKey)->find();
            if($menu->keyword&&$EventKey){
                $txtReply = $menu->keyword;
            }else{
                foreach ($buys as $v) {
                    Kohana::$log->add('smfyun_iid', print_r($v->iid, true));
                    switch ($v->iid) {
                        case 2:
                        require_once Kohana::find_file('vendor', 'api/wdb');
                            break;
                        case 3:
                        require_once Kohana::find_file('vendor', 'api/wfb');
                            break;
                        case 4:
                        Kohana::$log->add("ddasrwb", '进入rwb');
                        require_once Kohana::find_file('vendor', 'api/rwb');
                        $qwt_rwb=new rwb($this->Keyword,$wx,$bid,$openid,$userinfo,$qr_user,$Event,$mem);
                        $txtReply = $qwt_rwb->end();
                            break;
                        case 7:
                        require_once Kohana::find_file('vendor', 'api/qfx');
                        $qwt_qfx = new qfx($this->Keyword,$wx,$bid,$openid,$userinfo,$qr_user,$Event,$mem);
                        $txtReply = $qwt_qfx->end();
                            break;
                        case 8:
                        require_once Kohana::find_file('vendor', 'api/fxb');
                            break;
                        case 13:
                        require_once Kohana::find_file('vendor', 'api/dka');
                            break;
                        case 25:
                        require_once Kohana::find_file('vendor', 'api/rwd');
                            break;
                        case 27:
                        require_once Kohana::find_file('vendor', 'api/xdb');
                            break;
                    }
                }
            }
        }
        if(ORM::factory('qwt_buy')->where('bid', '=', $biz->id)->where('status', '=', 1)->where('expiretime','>=',time())->count_all()==0){
            // $txtReply = '对不起，您尚未购买先关产品或已过期。';
        }
        Kohana::$log->add($datatype.':'.$bid.'$txtReply1', $txtReply);
        Kohana::$log->add($datatype.':'.$bid.'$newsReply1', $newsReply);
        ob_clean();
        ob_flush();
        //默认文字回复
        if ($txtReplygl) {
            $result = $wx->text($txtReplygl)->reply(array(),true);
            Kohana::$log->add($datatype.':'.$bid.':$textresultgl', print_r($result,true));
            ob_clean();
            ob_flush();
            echo $result;
            exit;
        }
        if ($txtReplyqfx) {
            $result = $wx->text($txtReplyqfx)->reply(array(),true);
            Kohana::$log->add($datatype.':'.$bid.':$textresultqfx', print_r($result,true));
            ob_clean();
            ob_flush();
            echo $result;
            exit;
        }
        if ($txtReplyrwb) {
            $result = $wx->text($txtReplyrwb)->reply(array(),true);
            Kohana::$log->add($datatype.':'.$bid.':$textresultrwb', print_r($result,true));
            ob_clean();
            ob_flush();
            echo $result;
            exit;
        }
        if ($txtReply) {
            $result = $wx->text($txtReply)->reply(array(),true);
            Kohana::$log->add($datatype.':'.$bid.':$textresult', print_r($result,true));
            ob_clean();
            ob_flush();
            echo $result;
            exit;
        }

        //默认图文回复
        if ($newsReply) {
            $result = $wx->news($newsReply)->reply(array(),true);
            Kohana::$log->add($datatype.':'.$bid.':$newsresult', print_r($result,true));
            ob_clean();
            ob_flush();
            echo $result;
        }
        if($cfg['test']==1){
            if(!$newsReply&&!$txtReply){
                // $txtReply = '';
                // $result = $wx->text($txtReply)->reply(array(),true);
                Kohana::$log->add($datatype.':'.$bid.':回复空串', print_r($result,true));
                ob_clean();
                ob_flush();
                echo "";
                exit;
            }
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
     //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
    private function hongbao($config, $openid, $bid=1, $money, $wx){
        $Appid = 'wx31d7e1641cdeaf00';//武汉惠生活  代发   红包
        $config['mchid'] = 1275904301;
        $config['apikey'] = 'r1IPFhzbD14cO4gRsJXC2fas9WexVadF';

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
        $data["wxappid"] = $Appid;

        $data["re_openid"] = $openid;
        $data["total_amount"] = $money;
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "[{$config['logoname']}]送红包"; //活动名称
        //$data["nick_name"] = $config['name']; //提供方名称
        $data["send_name"] = $config['logoname']; //红包发送者名称
        $data["wishing"] = $config['logoname'].'恭喜发财！'; //红包祝福
        $data["remark"] = '运气太好啦！'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        Kohana::$log->add('$qwt_hby',print_r($data, true));
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));
        Kohana::$log->add('$qwt_hby',print_r($data, true));

        $postXml = $wx->xml_encode($data);
        Kohana::$log->add('hbbpostXml:',print_r($postXml, true));

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        // Kohana::$log->add('weixin:hongbao:fail:'.$config['name'], print_r($data, true));
        // Kohana::$log->add('weixin:hongbaopartnerkey:fail:'.$config['name'], $config['partnerkey']);
        if ($bid == 6) Kohana::$log->add('qwt_hbb:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);
        Kohana::$log->add('$qwt_hby_resultXml:',print_r($resultXml,true));

        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        Kohana::$log->add('$qwt_hby_response:',print_r($response,true));
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['mch_billno']=$mch_billno;
        Kohana::$log->add('$qwt_hby:',print_r($result, true) );
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/hby/cert/cert.pem";
        $key_file = DOCROOT."qwt/hby/cert/key.pem";
        //$rootca_file=DOCROOT."hby/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_hbycfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_cert')->find();
        $file_key = ORM::factory('qwt_hbycfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_key')->find();
        //$file_rootca = ORM::factory('qwt_hbycfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        // if (!file_exists(rootca_file)) {
        //     @mkdir(dirname($rootca_file));
        //     @file_put_contents($rootca_file, $file_rootca->pic);
        // }

        // Kohana::$log->add("weixin_fxb:$bid:curl_post_ssl:cert_file", $cert_file);

        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_file);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $key_file);

        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $rootca_file);// CA根证书（用来验证的网站证书是否是CA颁布）

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);

        $data = curl_exec($ch);

        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo curl_error($ch);
            curl_close($ch);
            Kohana::$log->add("qwt:hb_result:hby:ssl:".$bid, print_r($error,true));
            return false;
        }

    }
}
