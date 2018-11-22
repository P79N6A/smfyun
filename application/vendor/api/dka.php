<?php defined('SYSPATH') or die('No direct script access.');
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtdka/';
        $this->cdnurl = 'http://cdn.jfb.smfyun.com/qwt/dka/';

        $config = ORM::factory('qwt_dkacfg')->getCfg($bid,1);

        $sname = ORM::factory('qwt_dkacfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }

        if ($bid == $debug_bid) {
            // Kohana::$log->add("qwtdka:$bid:WE", $wx->errCode.':'.$wx->errMsg.':token:'.$wx->access_token);
            Kohana::$log->add("qwtdka:$bid:userinfo", var_export($userinfo, true));
        }

        //关注事件
        $EventKey = $wx->getRevEvent()['key'];
        $Ticket = $wx->getRevTicket();

        if(isset($_GET['bname'])){
            $openid = $_GET['openid'];

        }
        $model_q = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //扫码事件 || 扫码关注
        // $scene_id = $bid;
        Kohana::$log->add("qwtdka:$bid:scene_id", $scene_id);
        Kohana::$log->add("qwtdka:$bid:get_scene_id", print_r($wx->getRevSceneId(),true));
        Kohana::$log->add("qwtdka:$bid:getRevEvent", print_r($wx->getRevEvent(),true));
        if($EventKey == $bid || $EventKey == 'dka'.$bid){
            $EventKeyget = 1;
        }
        if($wx->getRevSceneId() == $bid || $wx->getRevSceneId() == 'dka'.$bid){
            $wxgetRevSceneId = 1;
        }
        if ($userinfo && ($Event == 'SCAN' && $EventKeyget == 1) || ($wxgetRevSceneId == 1)) {
        // if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($wx->getRevSceneId() == $scene_id)) {

            //新用户
            if (!$model_q->id) {
                $model_flag=1;
                Kohana::$log->add("model_flag$bid$openid",$model_flag);
                $model_q->bid = $bid;
                $model_q->values($userinfo);
                //$model_q->ip = Request::$client_ip;
                if ($userinfo) $model_q->save();
            }else{
                $model_flag=2;
                Kohana::$log->add("model_flag$bid$openid",$model_flag);
                $model_q->subscribe = $userinfo['subscribe'];
                $model_q->subscribe_time = $userinfo['subscribe_time'];
                $model_q->jointime = time();
                $model_q->save();
            }

            //根据 Ticket 获取二维码来源用户
            //小俞 -> 伯乐 oVOgUs0cGsvun_FM8-ywVpCd8AHk -> 珂心如意 oVOgUs9valnS-IN-NZzOmcxuAGuw -> 念念不忘 oVOgUs0vuImayOiVuD1Uf7SATZG8
            //珂心如意
            $fuser = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//王旭文
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;//王旭文

            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户
            if ($model_flag==1 &&$model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid &&$ffopenid!=$openid) {
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
                //首次关注积分
                if (ORM::factory('qwt_dkascore')->where('qid', '=', $model_q->id)->where('type', '=', 1)->count_all() == 0&&$has_subscribe==2) {
                     if ($config['goal00'] > 0) $model_q->scores->scoreIn($model_q, 1, $config['goal00']);
                }

                //先保存关系
                if ($model_q->id > $fuser->id&&$has_subscribe==2) {
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
                    $wx->sendCustomMessage($msg);
                    //if ($goal01_result) $wx->sendCustomMessage($msg);
                    if ($bid == $debug_bid) Kohana::$log->add("qwtdka:$bid:we_result_fuser", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);

                    //更新上一级用户的 userinfo
                    $fuserinfo = $wx->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("qwtdka:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->subscribe = 0;
                        $fuser->save();
                        // $fuser->values($fuserinfo);
                        // $fuser->save();
                    }

                    //风险判断
                    if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0 && $fuser->lock == 0) {
                        //直接用户
                        $count2 = ORM::factory('qwt_dkaqrcode', $fuser->id)->scores->where('type', '=', 2)->count_all();
                        //用是否生成海报判断真实下线
                        $count3 = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $fuser->bid)->where('fopenid', '=', $fuser->openid)->where('ticket', '<>', '')->count_all();

                        if ($fuser->lock == 0 && $count2 >= $config['risk_level1'] & $count3 <= $config['risk_level2']) {
                            $fuser->lock = 1;
                            $fuser->save();

                            //发消息通知上级
                            $msg['touser'] = $fopenidFromQrcode;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $config['text_risk'];
                            $we_result = $wx->sendCustomMessage($msg);
                        }
                    }

                    //上一级推荐人 积分增加处理
                    //珂心如意
                    $ffuser = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                    if ($ffuser->fopenid) {
                        //返利给 $ffuser 的上线 $ffuser->fopenid;
                        $fffuser = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
                        if ($fffuser && $config['goal02'] > 0&&$fffuser->lock!=1) $goal02_result = ORM::factory('qwt_dkascore')->scoreIn($fffuser, 3, $config['goal02'], $model_q->id);
                        $config['text_goal2'] .= "您当前". $this->scorename ."为：{$fffuser->score}";

                        $nickname = $ffuser->nickname;
                        $tpl = $config['text_goal2'];
                        $msg['touser'] = $ffuser->fopenid;
                        "您的朋友「{$userinfo['nickname']}」接受了您的邀请，一起开始打卡计划吧";
                        //$msg['text']['content'] = sprintf($tpl, $nickname);
                        $msg['text']['content'] = "您的朋友「{$nickname}」邀请了新的小伙伴，一起开始打卡计划吧";
                        $wx->sendCustomMessage($msg);

                    }
                }
            }

            //已经有上级就直接取来
            else {
                $fuser = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
                $fopenid = $fuser->openid;
            }

            //扫码后默认推送消息
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg2['touser'] = $fopenidFromQrcode;
            $msg2['msgtype'] = 'text';
            //remove emoji
            $nickname = $fuser->nickname;
            //$nickname = preg_replace('/[\xf0-\xf7].{3}/', '', $fuser->nickname);
            $nickname = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $nickname);



            // if ($model_q->fopenid && $fopenidFromQrcode != $model_q->fopenid && $fuser->nickname) $msg['text']['content'] = "亲，你已经是「{$nickname}」的支持者了，不能再扫了哦。";
            //如果 当前用户有效 && 当前用户有上级 && 来源二维码有效
            //新加消息
            Kohana::$log->add("$bid:wdy:weixin_scan88:$openid", print_r($model_flag,true));
            Kohana::$log->add("$bid:wdy:weixin_scan99:$openid", print_r($has_subscribe,true));
            if ($model_flag==2&&$model_q->lock!=1&&$fuser->lock!=1){
                $msg['text']['content'] = "您已经参加过活动了，不用再扫了哦。快去生成海报发起活动吧~";
                if($fopenid && $fopenid != $openid && $ffopenid!=$openid){
                    $msg2['text']['content'] = "您的朋友".$model_q->nickname."已经参加过活动了，不能再成为您的粉丝了";
                    $wx->sendCustomMessage($msg2);
                }
            }
            if ($has_subscribe==1&&$model_q->lock!=1&&$fuser->lock!=1){
                $msg['text']['content'] = "您已经关注过公众号了，不用再扫了哦。快去生成海报发起活动吧~";
                if($fopenid && $fopenid != $openid && $ffopenid!=$openid){
                    $msg2['text']['content'] = "您的朋友".$model_q->nickname."已经关注过公众号了，不能再成为您的粉丝了";
                    $wx->sendCustomMessage($msg2);
                }
            }
            //新加消息
            if ($model_q->id && $model_q->fopenid && $fopenid)$msg['text']['content'] = "亲，你已经是「{$nickname}」的支持者了，不能再扫了哦。";
            $name = ORM::factory('qwt_dkacfg')->where('bid','=',$bid)->where('key','=','name')->find()->value;
            if ($chunv) $msg['text']['content'] = "恭喜你，接受「{$nickname}」的邀请，点击菜单「我要打卡」，{$name}打卡计划等你来参与哦，赢取积分兑换超值奖品~";
            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己的不发消息
            $name = ORM::factory('qwt_dkacfg')->where('bid','=',$bid)->where('key','=','name')->find()->value;
            if ($openid != $fopenid) $we_result = $wx->sendCustomMessage($msg);
            //扫码后推送网址
            if ($config['text_follow_url']) {
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = $name.'早起计划说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow2.png';
                $we_result = $wx->sendCustomMessage($msg);
            }
        }
