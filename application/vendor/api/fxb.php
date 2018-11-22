<?php defined('SYSPATH') or die('No direct script access.');
require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
    $smfy=new SmfyQwt();
    $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtfxb/';
    $this->cdnurl = 'http://cdn.jfb.smfyun.com/qwt/fxb/';
    $config = ORM::factory('qwt_fxbcfg')->getCfg($bid,1);
    $sname = ORM::factory('qwt_fxbcfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
    $yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
    $sid = ORM::factory('qwt_login')->where('id','=',$bid)->find()->shopid;
    if($sname){
        $this->scorename = $sname;
    }else{
        $this->scorename = '积分';
    }
//关注事件
        $Event = $wx->getRevEvent()['event'];
        $EventKey = $wx->getRevEvent()['key'];
        $Ticket = $wx->getRevTicket();

        //当前微信用户
        $model_q = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        //免 oauth 登录网址
        $cksum = md5($model_q->openid.$config['appsecret'].date('Y-m'));
        $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);

        //新用户
        if (!$model_q->id||!$model_q->nickname){
            $model_flag=1;
            Kohana::$log->add("model_flag$bid$openid",$model_flag);
            $newone=1;
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
        if($model_q->fopenid&&$newone==1){
            $fopenid = $model_q->fopenid;
            $fuser = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            $nickname=$fuser->nickname;
            $name = $config['title1'];
            if($model_q->fopenid){
              $fuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find();
              $name = $config['title1'];
              if($fuser2->fopenid){
                $ffuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$fuser2->fopenid)->find();
                $name = $config['title2'];
                if($ffuser2->fopenid&&$config['kaiguan_needpay']==1){
                  $fffuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$ffuser2->fopenid)->find();
                  $name = $config['titlen3'];
                }
              }
            }
            $model_q->shscores->scoreIn($model_q,0,$config['money_init']);
            if($config['switch']==1){
                $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_init'],$sid);
                // $this->rsync($bid,$model_q->openid,$access_token,$config['money_init']);
            }
            $text="恭喜您经「{$nickname}」推荐成为了{$config['name']}的{$name}！获得了".number_format($config['money_init'],2)."{$config['scorename']}的奖励\n\n";
            $msg['touser'] = $model_q->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $text;
            $result = $wx->sendCustomMessage($msg);
            $fuser->shscores->scoreIn($fuser,8,$config['money_scan1'],$model_q->id);
            if($config['switch']==1){
                $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_scan1'],$sid);
                // $this->rsync($bid,$fuser->openid,$access_token,$config['money_scan1']);
            }
            $text = "您推荐了{$model_q->nickname}加入，获得". number_format($config['money_scan1'],2) ."{$config['scorename']}奖励。您当前{$config['scorename']}为：".number_format($fuser->shscore,2);
            $msg['touser'] = $fopenid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $text;
            $result = $wx->sendCustomMessage($msg);
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
                $ffuser = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find();
                $ffuser->shscores->scoreIn($ffuser,9,$config['money_scan2'],$model_q->id);
                if($config['switch']==1){
                    $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_scan2'],$sid);
                    // $this->rsync($bid,$ffuser->openid,$access_token,$config['money_scan2']);
                }
                $text="您的好友{$fuser->nickname}推荐了一位新的支持者，您获得". number_format($config['money_scan2'],2) ."{$config['scorename']}奖励。您当前{$config['scorename']}为：".number_format($ffuser->shscore,2);
                $msg['touser'] = $ffuser->openid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $text;
                $result = $wx->sendCustomMessage($msg);
            }
        }
        //扫码事件 || 扫码关注
        $scene_id = $bid;
        Kohana::$log->add("qwt_fxb_EventKey", print_r($userinfo,true));
        Kohana::$log->add("qwt_fxb_EventKey", $EventKey);
        Kohana::$log->add("qwt_fxb_Event", $Event);
        Kohana::$log->add("qwt_fxb_getRevSceneId", $wx->getRevSceneId());
        if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($wx->getRevSceneId() == $scene_id)) {

            //根据 Ticket 获取二维码来源用户
            //小俞 -> 伯乐 oVOgUs0cGsvun_FM8-ywVpCd8AHk -> 珂心如意 oVOgUs9valnS-IN-NZzOmcxuAGuw -> 念念不忘 oVOgUs0vuImayOiVuD1Uf7SATZG8
            //珂心如意
            $fuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
            $fopenidFromQrcode = $fopenid = $fuser->openid;
            if($model_q->lock==1||$fuser->lock==1){
                exit;
            }
            //如果 当前用户有效 && 当前用户没有上级 && 当前用户没有生成海报 && 来源二维码有效 && 来源用户 != 当前用户&&当前用户未锁定&&上级未锁定
            if ($model_flag==1 &&$model_q->id && !$model_q->fopenid && !$model_q->ticket && $fopenid && $fopenid != $openid&&$model_q->lock!=1&&$fuser->lock!=1) {
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

                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->save();
                    //风险判断
                    if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0) {
                        //直接用户
                        $count2 = ORM::factory('qwt_fxbqrcode', $fuser->id)->shscores->where('type', '=', 1)->count_all();
                        //用是否生成海报判断真实下线
                        $count3 = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $fuser->bid)->where('fopenid', '=', $fuser->openid)->where('ticket', '<>', '')->count_all();
                        //锁定用户
                        Kohana::$log->add("risk", $fuser->lock);
                        if ($fuser->lock == 0 && $count2 >= $config['risk_level1'] & $count3 <= $config['risk_level2']) {
                            $fuser->lock = 1;
                            $fuser->save();

                            //发消息通知上级
                            $msg['touser'] = $fopenidFromQrcode;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $config['text_risk'];
                            $we_result = $wx->sendCustomMessage($msg);
                            exit;
                        }
                        Kohana::$log->add("risk", print_r($count2, true));
                        Kohana::$log->add("risk", print_r($count3, true));
                        Kohana::$log->add("risk", $config['risk_level1']);
                        Kohana::$log->add("risk", $config['risk_level2']);

                    }
                    if (ORM::factory('qwt_fxbshscore')->where('qid', '=', $model_q->id)->where('type', '=', 0)->count_all() == 0) {
                        if ($config['money_init'] > 0) {

                            $model_q = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                            $goal_result0 = $model_q->shscores->scoreIn($model_q, 0, $config['money_init']);
                            if($config['switch']==1){
                                $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_init'],$sid);
                                // $this->rsync($bid,$model_q->openid,$access_token,$config['money_init']);
                            }
                        }
                    }

                    //第一个
                    //推荐人 扫码奖励
                    if ($config['money_scan1'] > 0) {

                        $fuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
                        $goal_result = $model_q->shscores->scoreIn($fuser, 1, $config['money_scan1'], $model_q->id);
                        if($config['switch']==1){
                            $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_scan1'],$sid);
                            // $this->rsync($bid,$fuser->openid,$access_token,$config['money_scan1']);
                        }
                    }

                    $msg['touser'] = $fopenidFromQrcode;
                    $msg['msgtype'] = 'text';

                    $msg['text']['content'] = "您推荐了{$model_q->nickname}加入，获得". number_format($config['money_scan1'],2) ."{$config['scorename']}奖励。您当前{$config['scorename']}为：".number_format($fuser->shscore,2);

                    $cksum = md5($fuser->openid.$config['appsecret'].date('Y-m'));
                    $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($fuser->openid);

                    if ($goal_result) {
                        //模板消息
                        // if ($config['msg_score_tpl'])
                        //     $this->sendScoreMessage($fuser->openid, '「'. $model_q->nickname .'」扫码奖励', $config['money_scan1']/100, $fuser->shscore, $url);
                        // else
                            $wx->sendCustomMessage($msg);
                    }

                    //更新上一级用户的 userinfo
                    $fuserinfo = $wx->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("qwtfxb:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->subscribe = 0;
                        $fuser->save();
                        // $fuser->values($fuserinfo);
                        // $fuser->save();
                    }


                    //上一级推荐人 积分增加处理
                    //珂心如意

                    if ($config['money_scan2'] > 0) {
                        $ffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                        if ($ffuser->fopenid) {
                            //返利给 $ffuser 的上线 $ffuser->fopenid;
                            $fffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
                            if ($fffuser && $config['money_scan2'] > 0) {
                                $fffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
                                $goal2_result = ORM::factory('qwt_fxbshscore')->scoreIn($fffuser, 2, $config['money_scan2'], $model_q->id);
                                if($config['switch']==1){
                                    $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_scan2'],$sid);
                                    // $this->rsync($bid,$fffuser->openid,$access_token,$config['money_scan2']);
                                }
                            }
                            // $config['text_goal2'] .= "您当前". $config['score'] ."为：{$fffuser->score}";
                            //第二个

                            $nickname = $ffuser->nickname;
                            $tpl = $config['text_goal2'];
                            $msg['touser'] = $ffuser->fopenid;
                            $msg['text']['content'] = "您的好友{$ffuser->nickname}推荐了一位新的支持者，您获得". number_format($config['money_scan2'],2) ."{$config['scorename']}奖励。您当前{$config['scorename']}为：".number_format($fffuser->shscore,2);

                            $cksum = md5($fffuser->openid.$config['appsecret'].date('Y-m'));
                            $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($fffuser->openid);

                            if ($goal2_result) {
                                //模板消息
                                // if ($config['msg_score_tpl'])
                                //     $this->sendScoreMessage($fffuser->openid, '间接好友扫码奖励', $config['money_scan2']/100, $fffuser->shscore, $url);
                                // else
                                    $wx->sendCustomMessage($msg);
                            }
                            if ($config['kaiguan_score']==1&&$config['money_scan3']>0) {//如果三级开关打开 并且奖励大于0
                                if($fffuser->fopenid){
                                    $ffffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fffuser->fopenid)->find();
                                    if ($ffffuser && $config['money_scan3'] > 0) {

                                        $ffffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fffuser->fopenid)->find();
                                        $goal3_result = ORM::factory('qwt_fxbshscore')->scoreIn($ffffuser, 3, $config['money_scan3'], $model_q->id);
                                        if($config['switch']==1){
                                            $result=$smfy->fxbrsync($bid,$model_q->openid,$yzaccess_token,$config['money_scan3'],$sid);
                                            // $this->rsync($bid,$ffffuser->openid,$access_token,$config['money_scan3']);
                                        }
                                    }
                                    $nickname = $fffuser->nickname;
                                    $tpl = $config['text_goal2'];
                                    $msg['touser'] = $fffuser->fopenid;

                                    $msg['text']['content'] = "您的好友推荐了一位好友加入，您获得". number_format($config['money_scan3'],2) ."{$config['scorename']}奖励。您当前{$config['scorename']}为：{$ffffuser->shscore}";

                                    $cksum = md5($ffffuser->openid.$config['appsecret'].date('Y-m'));
                                    $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($ffffuser->openid);

                                    if ($goal3_result) {
                                        //模板消息
                                        // if ($config['msg_score_tpl'])
                                        //     $this->sendScoreMessage($ffffuser->openid, '间接好友扫码奖励', $config['money_scan3']/100, $ffffuser->shscore, $url);
                                        // else
                                            $wx->sendCustomMessage($msg);
                                    }
                                }
                            }
                            // if ($goal2_result) $we_result = $wx->sendCustomMessage($msg);
                        }
                      }

                }
            }

            //已经有上级就直接取来
            else {
                $fuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
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

            //判断级数关系
            $name = $config['title1'];
            if($model_q->fopenid){
              $fuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find();
              $name = $config['title1'];
              if($fuser2->fopenid){
                $ffuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$fuser2->fopenid)->find();
                $name = $config['title2'];
                if($ffuser2->fopenid&&$config['kaiguan_needpay']==1){
                  $fffuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$ffuser2->fopenid)->find();
                  $name = $config['titlen3'];
                }
              }
            }
            $id = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('id','<=',$model_q->id)->find_all()->count();
            if ($model_q->id) $msg['text']['content'] = "您是{$config['name']}第 {$id} 号{$name}，不用再扫了哦。\n\n";
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
            if ($nickname && $model_q->fopenid) $msg['text']['content'] = "您是「{$nickname}」推荐的{$name}，不用再扫了哦。\n\n";
            if ($chunv) $msg['text']['content'] = "恭喜您经「{$nickname}」推荐成为了{$config['name']}的{$name}！获得了".number_format($config['money_init'],2)."{$config['scorename']}的奖励\n\n";
            // if ($model_q->fopenid && $fopenidFromQrcode != $model_q->fopenid && $fuser->nickname) $msg['text']['content'] = "您是{$config['name']}第 {$model_q->id} 号{$config['title1']}，不用再扫了哦。\n\n";

            //自己扫自己的不发消息
            //if ($openid != $fopenid) $we_result = $wx->sendCustomMessage($msg);

            //扫码后推送网址
            if ($config['qwt_fxbdesc']) {
                $msg['msgtype'] = 'text';
                $msg['text']['content'] .= str_replace('\n', "\n", $config['qwt_fxbdesc']);
                $we_result = $wx->sendCustomMessage($msg);
            }
        }
        Kohana::$log->add('2222','22222');
        //菜单点击事件
        if ($userinfo && $Event == 'CLICK' || $chunv2) {

            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

            //自定义 key
            if ($EventKey == $config['key_c1_fxb'] && $config['value_c1_fxb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c1_fxb']);
            }

            else if ($EventKey == $config['key_c2_fxb'] && $config['value_c2_fxb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c2_fxb']);
            }

            else if ($EventKey == $config['key_c3_fxb'] && $config['value_c3_fxb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c3_fxb']);
            }

            else if ($EventKey == $config['key_c4_fxb'] && $config['value_c4_fxb']) {
                $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $config['value_c4_fxb']);
            }

            //生成海报
            else if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报') {
                if(Model::factory('select_experience')->dopinion($bid,'fxb')){
                    //统计分销数量
                    if (!$model_q->id2) {
                        $fx_count = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('ticket', '<>', '')->count_all();
                        $model_q->id2 = $fx_count+1;
                        if ($model_q->id) $model_q->save();
                    }

                    //没有购买过不能生成海报判断？
                    if ($config['haibao_needpay']) {
                        if (ORM::factory('qwt_fxbtrade')->where('bid', '=', $bid)->where('qid', '=', $model_q->id)->count_all() == 0) {
                            $msg['text']['content'] = "您尚未购买过本店商品，不能生成海报。\n\n";
                            $msg['text']['content'] .= str_replace('\n', "\n", $config['qwt_fxbdesc']);
                            $we_result = $wx->sendCustomMessage($msg);
                            exit;
                        }
                    }

                    //有 ticket 并且没有超过 7 天 就不用重新生成
                    $ticket_lifetime = 3600*24*7;
                    //自定义过期时间
                    if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

                    $qrcode_type = 0;

                    //if (!$model_q->lastupdate) $model_q->lastupdate = time();

                    $config['text_fxbsend'] = str_replace('{ID}', $model_q->id2, $config['text_fxbsend']);
                    $config['text_fxbsend'] = str_replace('\n', "\n", $config['text_fxbsend']);

                    // Kohana::$log->add('qwtfxb:tpl_lifetime', time() - $model_q->lastupdate);

                    if ( ($result['ticket'] = $model_q->ticket) && (time() - $model_q->lastupdate < $ticket_lifetime) ) {

                        //pass
                        //海报过期时间文案
                        $config['text_fxbsend'] = str_replace('{TIME}', date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime), $config['text_fxbsend']);
                        $msg['text']['content'] = $config['text_fxbsend'];

                        //更新用户信息
                        //$model_q->values($userinfo);
                        //$model_q->save();

                    } else {

                        $time = time();

                        //永久二维码 只能有一个
                        if ($model_q->id == 1) {
                            $ticket_lifetime = 3600*24*365*3;
                            $qrcode_type = 1;
                            $time = time() + $ticket_lifetime;
                        }

                        $result = $wx->getQRCode($bid, $qrcode_type, $ticket_lifetime);
                        $model_q->lastupdate = $time;

                        //海报过期时间文案
                        $config['text_fxbsend'] = str_replace('{TIME}', date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime), $config['text_fxbsend']);
                        $msg['text']['content'] = $config['text_fxbsend'];

                        //生成海报并保存
                        $model_q->values($userinfo);
                        $model_q->bid = $bid;
                        $model_q->ticket = $result['ticket'];
                        $model_q->save();

                        $newticket = true;
                    }

                    $we_result = $wx->sendCustomMessage($msg);

                    $md5 = md5($result['ticket'].time().rand(1,100000));

                    //图片合成
                    //模板
                    $imgtpl = DOCROOT."qwt/fxb/tmp/tpl.$bid.jpg";
                    $tmpdir = '/dev/shm/';

                    //判断模板文件是否需要从数据库更新
                    $tpl = ORM::factory('qwt_fxbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                    if (!$tpl->pic) {
                        $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                        $we_result = $wx->sendCustomMessage($msg);
                        exit;
                    }

                    if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);
                    if (filesize($imgtpl) == 0) @unlink($imgtpl);

                    if (!file_exists($imgtpl)) {
                        @mkdir(dirname($imgtpl));
                        @file_put_contents($imgtpl, $tpl->pic);
                    }

                    //默认头像
                    $tplhead = ORM::factory('qwt_fxbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->pic;
                    $default_head_file = DOCROOT."qwt/fxb/tmp/head.$bid.jpg";

                    if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                    if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                    //有海报缓存直接发送
                    $tpl_key = 'qwtwfb:tpl'.$openid.':'.$tpl->lastupdate;
                    $uploadresult['media_id'] = $mem->get($tpl_key);

                    if ($uploadresult['media_id'] && !$newticket) {
                        //pass
                        // Kohana::$log->add('qwtfxb:tpl_key', $tpl_key);
                        // Kohana::$log->add('qwtfxb:media_id_cache', print_r($uploadresult, true));
                    } else {

                        //获取参数二维码
                        $qrurl = $wx->getQRUrl($result['ticket']);
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
                            Kohana::$log->add("qwtfxb:$bid:head4:", $remote_head);
                        }

                        //写入临时头像文件
                        if ($remote_head) file_put_contents($headfile, $remote_head);

                        if (!$remote_head || !$remote_qrcode) {
                            $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                            $we_result = $wx->sendCustomMessage($msg);
                            $model_q->ticket = '';
                            $model_q->save();
                            Kohana::$log->add("qwtfxb:$bid:file:remote_head_url get ERROR!", $remote_head_url);

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
                            $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                            if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                            if (!$uploadresult['media_id']) {
                                Kohana::$log->add("qwtfxb:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                                if ($wx->errCode == 45009) {
                                    $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                    $we_result = $wx->sendCustomMessage($msg);
                                    exit;
                                }
                            }

                        } else {
                            $tmp = getimagesize($imgtpl);

                            Kohana::$log->add("qwtfxb:$bid:newfile $newfile gen ERROR!");
                            Kohana::$log->add("qwtfxb:$bid:imgtplfile:$imgtpl", file_exists($imgtpl));
                            Kohana::$log->add("qwtfxb:$bid:imgtplfileinfo", print_r($tmp, true));

                            Kohana::$log->add("qwtfxb:$bid:qrcodefile:$localfile", file_exists($localfile));
                            Kohana::$log->add("qwtfxb:$bid:headfile:$headfile", file_exists($headfile));
                        }

                        unlink($localfile);
                        unlink($headfile);
                        unlink($newfile);

                        //Cache
                        if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                    }

                    //首次生成海报收益
                    // if (ORM::factory('fxb_score')->where('qid', '=', $model_q->id)->where('type', '=', 0)->count_all() == 0) {
                    //     if ($config['money_init'] > 0) $model_q->shscores->scoreIn($model_q, 0, $config['money_init']);
                    // }

                    $msg['msgtype'] = 'image';
                    $msg['image']['media_id'] = $uploadresult['media_id'];

                    $we_result = $wx->sendCustomMessage($msg);
                }else{
                    $msg['touser'] = $openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '体验海报已用完，需要续费后才能正常使用，谢谢！';
                    $wx->sendCustomMessage($msg);
                    exit();
                }
            }

            //收益明细
            else if ($EventKey == $config['key_fxbscore'] || $EventKey == '积分查询') {
                //判断级数关系
                $name = $config['title1'];
                if($model_q->fopenid){
                  $fuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find();
                  $name = $config['title1'];
                  if($fuser2->fopenid){
                    $ffuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$fuser2->fopenid)->find();
                    $name = $config['title2'];
                    if($ffuser2->fopenid&&$config['kaiguan_needpay']==1){
                      $fffuser2 = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('openid','=',$ffuser2->fopenid)->find();
                      $name = $config['titlen3'];
                    }
                  }
                }
                if (!$model_q->openid) {
                    $msg['text']['content'] = '请先点击生成海报，成为'.$name ;
                    $wx->sendCustomMessage($msg);
                    exit;
                }

                $tplmsg['touser'] = $model_q->openid;
                $tplmsg['template_id'] = $config['msg_scan1_tpl'];
                $tplmsg['url'] = $url;

                // $title3=$config['title3'];
                $title5=$config['title5'];

                $money_all = number_format($model_q->scores->select(array('SUM("score")', 'total_score'))->where('score', '>', 0)->find()->total_score, 2);
                // $txtReply = "@{$model_q->nickname}\n\n{$config['name']}第 {$model_q->id2} 号{$name}\n{$title3}：￥$money_all\n{$title4}：￥{$model_q->score} \n\n<a href=\"$url\">点击查看明细</a>";

                if(empty($result['aaa'])){
                    $result['aaa']=" ";
                }
                if(empty($title5)){
                    $title5=" ";
                }
                $id = ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('id','<=',$model_q->id)->find_all()->count();
                $txtReply = "@{$model_q->nickname}\n\n{$config['name']}第 {$id} 号{$name}\n总{$config['title5']}：$money_all \n当前可转出{$config['title5']}：{$model_q->score}\n当前可用{$config['scorename']}：".number_format($model_q->shscore,2)."\n\n<a href=\"$url\">点击查看明细</a>";
            }

            // else if ($EventKey) {
            //     $msg['msgtype'] = 'text';
            //     $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「订单宝」商户后台完成配置：'.$EventKey;
            // }

            //检查 Auth 是否过期
            if ($we_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("qwtfxb:$bid:we_result", print_r($we_result, true));
                //$wx->resetAuth();
            }
        }
