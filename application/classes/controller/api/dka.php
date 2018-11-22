<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_dka extends Controller_API {

    var $token = 'dakabao';

    var $FromUserName;
    var $Keyword;
    var $methodVersion='3.0.0';
    var $baseurl = 'http://jfb.smfyun.com/dka/';
    var $cdnurl = 'http://cdn.jfb.smfyun.com/dka/';
    var $scorename;
    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "dka";
        if (!is_numeric($bid)) $bid = ORM::factory('dka_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('dka_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by bole");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0){
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/dka/';

        set_time_limit(15);
        Database::$default = "dka";
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $mem = Cache::instance('memcache');

        $debug_bid = 1350;
        if(isset($_GET['bname'])){
            $bname = $_GET['bname'];
        }
        if(isset($_POST['tplcheck'])){
            $bname = $_POST['tplcheck'];
            echo 'bname:'.$bname;
        }
        //username->bid
        //if (!is_numeric($bid))

        $biz = ORM::factory('dka_login')->where('user', '=', $bname)->find();

        $bid = $biz->id;

        $config = ORM::factory('dka_cfg')->getCfg($bid);

        if ($debug) print_r($config);

        if (!$config['appid'] || !$config['appsecret'] || !$config['scene_id']) die('Not Config!');
        //if ($bid == $debug_bid) Kohana::$log->add('weixin2:config', print_r($config, true));
        if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
            $txtReply = '您的打卡宝插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';
            $die=1;
        }

        $we = new Wechat($config);
        $we->getRev();

        //DEBUG by bole
        // Kohana::$log->add('$GLOBALS', print_r($GLOBALS, true));
        // if ($bid == 2) Kohana::$log->add('$IPS', print_r($we->getServerIp(), true));

        if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:getRevData", print_r($we->getRevData(), true));
        if(substr($we->checkAuth(), 0,5)=='error'){
            $txtReply = $we->checkAuth();
        }
        if(substr($we->checkAuth(), 0,11)=='error:40013'){
            $txtReply = '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写';
        }
        if(substr($we->checkAuth(), 0,11)=='error:40014'){
            $txtReply = '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口';
        }
        if(substr($we->checkAuth(), 0,11)=='error:40164'){
            $txtReply = '请查看说明书将指定IP地址添加到微信后台白名单';
        }
        if(substr($we->checkAuth(), 0,11)=='error:40125'){
            $txtReply = 'appsecret填写不正确，请检查';
        }
        if (!$we->checkAuth()) {
            $txtReply = 'appid 和 appsecret 配置不正确，请检查';
            if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:checkAuth", 'appid 和 appsecret 配置不正确，请检查');
        }
        $sname = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
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
            $msg['text']['content'] = '您的打卡宝插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';
            $we->sendCustomMessage($msg);
            exit;
        }
        if ($bid == $debug_bid) {
            // Kohana::$log->add("weixin2:$bid:WE", $we->errCode.':'.$we->errMsg.':token:'.$we->access_token);
            Kohana::$log->add("weixin2:$bid:userinfo", var_export($userinfo, true));
        }

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

            if (!$txtReply) $txtReply = '十分抱歉，活动今天参与人数已经达到微信规定的上限。积分仍然保留，活动恢复后会以微信通知的方式通知您继续参加或兑换积分哦。奖品已经加量，请大家谅解。';
        }

        //关注事件
        $Event = $we->getRevEvent()['event'];
        $EventKey = $we->getRevEvent()['key'];
        $Ticket = $we->getRevTicket();
        //获取地理位置事件
        // $Locationx = $we->getRevGeo()['x'];
        // $Locationy = $we->getRevGeo()['y'];
        //当前微信用户
    if(isset($_POST['tplcheck'])){//发布模板消息
        $ops = ORM::factory('dka_qrcode')->where('bid','=',$bid)->where('dka_join','!=',0)->find_all()->as_array();//找出此商家下的 加入了打卡的用户
        $tplid = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','tplid')->find()->value;
        foreach ($ops as $op) {
            $cksum = md5($op->openid.$config['appsecret'].date('Y-m-d'));
            $tplmsg['touser'] = $op->openid;
            $tplmsg['template_id'] = $tplid;
            $tplmsg['url'] = $this->baseurl.'index/'. $bid.'?url=dka&cksum='. $cksum .'&openid='. base64_encode($op->openid);
            // echo $tplmsg['url'].'<br>';

            $tplmsg['data']['first']['value'] = '今天的打卡时间:'.date('Y-m-d H:i');
            $tplmsg['data']['first']['color'] = '#999999';

            $tplmsg['data']['work']['value'] = '亲，快来打卡哦~';
            $tplmsg['data']['work']['color'] = '#999999';

            $tplmsg['data']['remark']['value'] = '连续坚持打卡会有额外的积分奖励，兑换超值奖品~';
            $tplmsg['data']['remark']['color'] = '#999999';
            $we->sendTemplateMessage($tplmsg);
            Kohana::$log->add('dka:tplcheckopenid:{$bid}', $op->openid);//
        }
        die();
    }
        if(isset($_GET['bname'])){
            $openid = $_GET['openid'];

        }
        $model_q = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //扫码事件 || 扫码关注
        $scene_id = $config['scene_id'];
        if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($we->getRevSceneId() == $scene_id)) {

            //新用户
            if (!$model_q->id) {
                $model_q->bid = $bid;
                $model_q->values($userinfo);
                //$model_q->ip = Request::$client_ip;
                if ($userinfo) $model_q->save();
            }

            //根据 Ticket 获取二维码来源用户
            //小俞 -> 伯乐 oVOgUs0cGsvun_FM8-ywVpCd8AHk -> 珂心如意 oVOgUs9valnS-IN-NZzOmcxuAGuw -> 念念不忘 oVOgUs0vuImayOiVuD1Uf7SATZG8
            //珂心如意
            $fuser = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//王旭文
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;//王旭文

            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户
            if ($model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid &&$ffopenid!=$openid) {

                //首次关注积分
                if (ORM::factory('dka_score')->where('qid', '=', $model_q->id)->where('type', '=', 1)->count_all() == 0) {
                     if ($config['goal00'] > 0) $model_q->scores->scoreIn($model_q, 1, $config['goal00']);
                }

                //先保存关系
                if ($model_q->id > $fuser->id) {
                    //处女？
                    $chunv = 1;

                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->save();


                    //推荐人 积分增加处理
                    if ($config['goal01'] > 0&&$fuser->lock!=1) $goal01_result = $model_q->scores->scoreIn($fuser, 2, $config['goal01'], $model_q->id);

                    //积分话术
                    $config['text_goal'] .= "您当前". $this->scorename ."为：{$fuser->score}";

                    $tpl = $config['text_goal'];
                    $msg['touser'] = $fopenidFromQrcode;
                    $msg['msgtype'] = 'text';
                    //$msg['text']['content'] = sprintf($tpl, $userinfo['nickname']);
                    $msg['text']['content'] = "您的朋友「{$userinfo['nickname']}」接受了您的邀请，一起开始打卡计划吧";
                    $we->sendCustomMessage($msg);
                    //if ($goal01_result) $we->sendCustomMessage($msg);
                    if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result_fuser", var_export($we_result, true).$we->errCode.':'.$we->errMsg);

                    //更新上一级用户的 userinfo
                    $fuserinfo = $we->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->values($fuserinfo);
                        $fuser->save();
                    }

                    //风险判断
                    if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0 && $fuser->lock == 0) {
                        //直接用户
                        $count2 = ORM::factory('dka_qrcode', $fuser->id)->scores->where('type', '=', 2)->count_all();
                        //用是否生成海报判断真实下线
                        $count3 = ORM::factory('dka_qrcode')->where('bid', '=', $fuser->bid)->where('fopenid', '=', $fuser->openid)->where('ticket', '<>', '')->count_all();

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
                    $ffuser = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                    if ($ffuser->fopenid) {
                        //返利给 $ffuser 的上线 $ffuser->fopenid;
                        $fffuser = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
                        if ($fffuser && $config['goal02'] > 0&&$fffuser->lock!=1) $goal02_result = ORM::factory('dka_score')->scoreIn($fffuser, 3, $config['goal02'], $model_q->id);
                        $config['text_goal2'] .= "您当前". $this->scorename ."为：{$fffuser->score}";

                        $nickname = $ffuser->nickname;
                        $tpl = $config['text_goal2'];
                        $msg['touser'] = $ffuser->fopenid;
                        "您的朋友「{$userinfo['nickname']}」接受了您的邀请，一起开始打卡计划吧";
                        //$msg['text']['content'] = sprintf($tpl, $nickname);
                        $msg['text']['content'] = "您的朋友「{$nickname}」邀请了新的小伙伴，一起开始打卡计划吧";
                        $we->sendCustomMessage($msg);

                    }
                }
            }

            //已经有上级就直接取来
            else {
                $fuser = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
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
            if ($model_q->id && $model_q->fopenid && $fopenid)$msg['text']['content'] = "亲，你已经是「{$nickname}」的支持者了，不能再扫了哦。";
            $name = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','name')->find()->value;
            if ($chunv) $msg['text']['content'] = "恭喜你，接受「{$nickname}」的邀请，点击菜单「我要打卡」，{$name}打卡计划等你来参与哦，赢取积分兑换超值奖品~";
            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己的不发消息
            $name = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','name')->find()->value;
            if ($openid != $fopenid) $we_result = $we->sendCustomMessage($msg);
            //扫码后推送网址
            if ($config['text_follow_url']) {
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = $name.'早起计划说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow2.png';
                $we_result = $we->sendCustomMessage($msg);
            }
        }
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
        //     $street = $json_obj['result']['address_component']['street'];//街道
        //     $msg['text']['content'] = '$nation.$province.$city.$district.$street';//$nation.$province.$city.$district.$street
        //     $we_result = $we->sendCustomMessage($msg);
        // }
        //菜单点击事件
        if(isset($_GET['bname'])){
            $Event = 'CLICK';
            //$EventKey = $config['key_qrcode'];
            $EventKey = '生成海报';
            $openid = $_GET['openid'];
            $userinfo = $we->getUserInfo($openid);
            $flag = 1;
        }
        if ($userinfo && $Event == 'CLICK' || $chunv2) {

            if (!$model_q->id) {
                $model_q->bid = $bid;
                $model_q->values($userinfo);
                //$model_q->ip = Request::$client_ip;
                if ($userinfo) $model_q->save();
            }
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $cksum = md5($model_q->openid.$config['appsecret'].date('Y-m-d'));
            //$pos = strpos($mystring, $findme); $findme是你要查找的字符，如果找到返回True，否则返回false
            $count = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $position = 0;
            $u_location = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
            for ($i=1; $i <=$count ; $i++) {
                $pro[$i] = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                $city[$i] = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                $dis[$i] = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
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

            $status = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
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
            else if ($EventKey == $config['key_c5'] && $config['value_c5']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c5']);
                // $we_result = $we->sendCustomMessage($msg);
            }
            //我要打卡
            else if ($EventKey == $config['key_dka']) {
                $url = $this->baseurl.'index/'. $bid .'?url=dka&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                $msg['msgtype'] = 'news';

                $news_pic_file = 'news_top.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."dka/$news_pic_file")) $news_pic_file = 'news_dka.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '我要打卡';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

            }
            //我的收益
            // else if ($EventKey == $config['key_fxbscore'] || $EventKey == '我的收益') {
            //     if (!$model_q->id2) {
            //         $msg['text']['content'] = '请先点击我要打卡，生成邀请卡，成为'.$config['title1'] ;
            //         $we->sendCustomMessage($msg);
            //         exit;
            //     }
            //     $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
            //     // $tplmsg['touser'] = $model_q->openid;
            //     // $tplmsg['template_id'] = $config['msg_scan1_tpl'];
            //     // $tplmsg['url'] = $url;

            //     $money_all = number_format($model_q->details->select(array('SUM("cash")', 'total_score'))->where('cash', '>', 0)->find()->total_score, 2);
            //     // $txtReply = "@{$model_q->nickname}\n\n{$config['name']}第 {$model_q->id2} 号{$config['title1']}\n总收益：￥$money_all\n当前收益：￥{$model_q->score} \n\n<a href=\"$url\">点击查看明细</a>";
            //     $msg['touser'] = $model_q->openid;
            //     $msg['msgtype'] = 'text';
            //     $msg['text']['content'] = "@".$model_q->nickname."\n\n".$config['name']."第".$model_q->id2." 号".$config['title1']."\n总收益：￥".$money_all."\n当前收益：￥".$model_q->cash."\n\n".'<a href="'.$url.'">点击查看明细</a>';

            //     // $we->sendCustomMessage($msg);
            // }
            //生成海报
            else if ($EventKey == $config['key_qrcode'] || $chunv || $EventKey == '生成海报') {
                $ticket_lifetime = 3600*24*7;
                if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];
                if (($model_q->id2!=0)&&($result['ticket'] = $model_q->ticket) && (time() - $model_q->lastupdate < $ticket_lifetime) ) {
                    // 非新用户&&该ticket存在并且直接赋值给$result['ticket']&&未过期
                    //有 ticket 并且没有超过 7 天 就不用重新生成
                    //Kohana::$log->add('weixin:ticket1', print_r($result['ticket'], true));//
                    $msg['text']['content'] = $config['text_send'];
                } else {
                    $ticket_lifetime = 3600*24*7;
                    //自定义过期时间
                    if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];
                    $result = $we->getQRCode($config['scene_id'], 0, $ticket_lifetime);
                    //Kohana::$log->add('weixin:result', print_r($result, true));//
                    $msg['text']['content'] = $config['text_send'];
                    //生成海报并保存
                    $model_q->values($userinfo);
                    $model_q->bid = $bid;
                    $model_q->ticket = $result['ticket'];
                    // $msg['touser'] = $model_q->openid;
                    // $msg['msgtype'] = 'text';
                    // $msg['text']['content'] = 'ticket:'.$result['ticket'];

                    // $we->sendCustomMessage($msg);
                    // exit;
                    $model_q->lastupdate = time();
                    $model_q->save();

                    $newticket = true;
                }

                // 3 条客服消息限制，这里不发了
                //$we_result = $we->sendCustomMessage($msg);

                $md5 = md5($result['ticket'].time().rand(1,100000));

                //图片合成
                //模板
                $imgtpl = DOCROOT."dka/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
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
                $tplhead = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                $default_head_file = DOCROOT."dka/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'dka:tpl:'.$openid.':'.$tpl->lastupdate;
                $uploadresult['media_id'] = $mem->get($tpl_key);

                if ($bid == $debug_bid) $newticket = true;

                // if ($uploadresult['media_id'] && !$newticket) {
                //     //pass
                //     // Kohana::$log->add('weixin2:tpl_key', $tpl_key);
                //     // Kohana::$log->add('weixin2:media_id_cache', print_r($uploadresult, true));
                // } else {

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
                    if (!$remote_head && $default_head_file) {
                        $remote_head = file_get_contents($default_head_file);
                        Kohana::$log->add("dka:$bid:head4:", $remote_head);
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
                    $config['px_qrcode_left'] = 170;
                    $config['px_qrcode_top'] = 450;
                    $config['px_qrcode_width'] = 300;

                    if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
                    if($src_head) imagecopyresampled($dest, $src_head, 90, 315, 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

                    $newfile = "{$tmpdir}$md5.new.jpg";
                    imagejpeg($dest, $newfile);
                    if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
                    if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);

                    $qid = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->id;
                    $today = date('y-m-d',time());
                    function countday($userconday,$bid,$qid){
                        $frontday = date("Y-m-d",strtotime('-'.$userconday.'day'));
                        $continue = ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$frontday)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date;
                        if($continue){
                            $userconday++;
                            return countday($userconday,$bid,$qid);
                        }else{
                            return $userconday;
                        }
                    }
                    function hex2rgb($hexColor) {
                        $color = str_replace('#', '', $hexColor);
                        if (strlen($color) > 3) {
                            $rgb = array(
                                'r' => hexdec(substr($color, 0, 2)),
                                'g' => hexdec(substr($color, 2, 2)),
                                'b' => hexdec(substr($color, 4, 2))
                            );
                        } else {
                            $color = $hexColor;
                            $r = substr($color, 0, 1) . substr($color, 0, 1);
                            $g = substr($color, 1, 1) . substr($color, 1, 1);
                            $b = substr($color, 2, 1) . substr($color, 2, 1);
                            $rgb = array(
                                'r' => hexdec($r),
                                'g' => hexdec($g),
                                'b' => hexdec($b)
                                );
                        }
                        return $rgb;
                    }
                    if(ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$today)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date){
                        $flag = 1;
                    }else{
                        $flag = 0;
                    }//判断今日签到
                    $countday = countday(1,$bid,$qid)-1+$flag;//计算连续打卡天数
                    $num = ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->count_all();
                    //$score=ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->score;
                    $add = ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=' , $qid)->where('date','=',date('Y-m-d',time()))->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->score;
                    //第一次合成
                    $im = imagecreatefromjpeg($newfile);//选择图片

                    $newfile =  "{$tmpdir}$md5.new.jpg";//缓存地址文件
                    $text = "$num";//文字

                    $font = DOCROOT."dka/dist/msyh.ttf";//选择字体


                    //$size = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_size')->find()->value;
                    $size =52;
                    //$top = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_top')->find()->value;
                    $top =360;
                    //$left = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_left')->find()->value;
                    $left =425;
                    //$color = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_color')->find()->value;//选择颜色
                    $color = "#EC6941";
                    $rgb = hex2rgb($color);

                    $color = @imagecolorallocate($im, $rgb['r'],$rgb['g'],$rgb['b']);//选择颜色

                    imagettftext($im, $size, 0, $left, $top, $color, $font, $text);//将文字和图片合成

                    imagejpeg($im, $newfile,100);//缓存到文件


                    //第二次合成
                    $im = imagecreatefromjpeg($newfile);//选择图片

                    $newfile =  "{$tmpdir}$md5.new.jpg";//缓存地址文件
                    $text = "$countday";//总天数

                    $font = DOCROOT."dka/dist/msyh.ttf";//选择字体


                    //$size = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_size')->find()->value;
                    $size =32;
                    //$top = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_top')->find()->value;
                    $top = 405;
                    //$left = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_left')->find()->value;
                    $left =322;
                    //$color = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_color')->find()->value;//选择颜色
                    $color = "#86CC00";
                    $rgb = hex2rgb($color);

                    $color = @imagecolorallocate($im, $rgb['r'],$rgb['g'],$rgb['b']);//选择颜色

                    imagettftext($im, $size, 0, $left, $top, $color, $font, $text);//将文字和图片合成

                    imagejpeg($im, $newfile,100);//缓存到文件


                    //第三次合成
                    $im = imagecreatefromjpeg($newfile);//选择图片

                    $newfile =  "{$tmpdir}$md5.new.jpg";//缓存地址文件
                    if(!$add) $add=0;
                    $text = "$add";//连续打卡奖励积分
                    $font = DOCROOT."dka/dist/msyh.ttf";//选择字体


                    //$size = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_size')->find()->value;
                    $size =32;
                    //$top = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_top')->find()->value;
                    $top =405;
                    //$left = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_left')->find()->value;
                    $left =460;
                    //$color = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'font_color')->find()->value;//选择颜色
                    $color = "#86CC00";
                    $rgb = hex2rgb($color);

                    $color = @imagecolorallocate($im, $rgb['r'],$rgb['g'],$rgb['b']);//选择颜色

                    imagettftext($im, $size, 0, $left, $top, $color, $font, $text);//将文字和图片合成

                    imagejpeg($im, $newfile,100);//缓存到文件



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
                //}

                //海报发送前提醒消息
                $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「生成海报」菜单重新获取哦！';
                if ($bid == 64) $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「我要参加」菜单重新获取哦！';
                $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;

                $we_result = $we->sendCustomMessage($msg);

                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                unset($msg['text']);

                if(!$model_q->id2){
                    $fx_count = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('ticket', '<>', '')->count_all();
                    $model_q->id2 = $fx_count;
                    if ($model_q->id) $model_q->save();
                }

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
                if (!file_exists(DOCROOT."dka/$news_pic_file")) $news_pic_file = 'news_score.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '. $model_q->score .'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                // $we_result = $we->sendCustomMessage($msg);
            }

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
                if (!file_exists(DOCROOT."dka/$news_pic_file")) $news_pic_file = 'news_order.jpg';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $config['score'].'兑换';
                if ($bid == 64) $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = '奖品兑换';

                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $config['score'] .'为 '. $model_q->score .'，点击查看可兑换的产品...';
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
                if (!file_exists(DOCROOT."dka/$news_pic_file")) $news_pic_file = 'news_top.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'排行榜';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                //$we_result = $we->sendCustomMessage($msg);
            }

            else if ($EventKey) {
                $msg['msgtype'] = 'text';
                $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「打卡宝」商户后台完成配置：'.$EventKey;

                //用户少的时候才回复 debug
                // if (ORM::factory('dka_qrcode')->where('bid', '=', $bid)->count_all() < 10)
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
                $replyfront = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
                $replyend = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
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

    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！')
    {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔收益！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $title;

        $tplmsg['data']['keyword2']['value'] = '￥'.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#FF0000';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');

        $tplmsg['data']['keyword4']['value'] = '￥'.number_format($total, 2);
        $tplmsg['data']['keyword4']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';

        // Kohana::$log->add("weixin_fxb:$bid:tplmsg", print_r($tplmsg, true));
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
