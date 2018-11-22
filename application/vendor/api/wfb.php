<?php defined('SYSPATH') or die('No direct script access.');

    $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtwfb/';
    $this->cdnurl = 'http://cdn.jfb.smfyun.com/qwt/wfb/';
    $config = ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
    $sname = ORM::factory('qwt_wfbcfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
    $yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
    if($sname){
        $this->scorename = $sname;
    }else{
        $this->scorename = '积分';
    }
    // $userinfo = $wx->getUserInfo($openid);
    $EventKey = $wx->getRevEvent()['key'];
    $Ticket = $wx->getRevTicket();
        //当前微信用户
        $model_q = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        //获取地理位置事件
        // if($config['status']==1&&!$model_q->area&&$model_q->id){
        //     $location = $wx->getRevEventGeo();
        //     $locationx = $location['x'];
        //     $locationy = $location['y'];
        //     Kohana::$log->add("qwt:wfb:$bid:locationa", print_r($location,true));
        //     Kohana::$log->add("qwt:wfb:$bid:locationx", $location['x']);
        //     Kohana::$log->add("qwt:wfb:$bid:locationy", $location['y']);
        //     $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $locationx. ',' . $locationy . '&key=WR5BZ-C4JWR-QC3WR-WRLZX-VOF35-P3BQO';
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
        //     $u_location = $province.$city.$disrict;
        //     $model_q->area = $u_location;
        //     $model_q->save();
        // }
        //扫码事件 || 扫码关注
        // $scene_id = $bid;
        Kohana::$log->add("qwtwfb:EventKey", $EventKey);
        Kohana::$log->add("qwtwfb:scene_id", $scene_id);
        Kohana::$log->add("qwtwfb:wxget_scene_id", $wx->getRevSceneId());
        if($EventKey == $bid || $EventKey == 'wfb'.$bid){
            $EventKeyget = 1;
        }
        if($wx->getRevSceneId() == $bid || $wx->getRevSceneId() == 'wfb'.$bid){
            $wxgetRevSceneId = 1;
        }
        // if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($wx->getRevSceneId() == $scene_id)) {
        if ($userinfo && ($Event == 'SCAN' && $EventKeyget == 1) || ($wxgetRevSceneId == 1)) {
            //新用户
            if (!$model_q->id) {
                // Kohana::$log->add("qwtwfb:$bid:model_q", 'model_q');
                $model_flag=1;
                Kohana::$log->add("model_flag$bid$openid",$model_flag);
                $model_q->bid = $bid;
                $model_q->qid = $qr_user->id;
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
            $fuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;

            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户&&当前用户未锁定&&上级未锁定
            if ($model_flag==1 && $model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid && $ffopenid!=$openid&&$model_q->lock!=1&&$fuser->lock!=1) {
                $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if($subscribe->id&&$subscribe->creattime<=time()-60){
                    Kohana::$log->add("wfb{$bid}SCAN111",print_r($subscribe->openid, true));
                    $has_subscribe=1;
                    $model_p = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                    $model_p->old=1;
                    $model_p->save();
                    Kohana::$log->add("has_subscribe$bid$openid",$has_subscribe);
                }else{
                    $model_p = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                    $model_p->old=0;
                    $model_p->save();
                    $has_subscribe=2;
                    Kohana::$log->add("has_subscribe$bid$openid",$has_subscribe);
                }
                Kohana::$log->add("wfb{$bid}SCAN",print_r(time(), true));
                // 扫码后判断用户地理位置
                $count = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
                $position = 0;
                $u_location = $qr_user->area;
                for ($i=1; $i <=$count ; $i++) {
                    $pro[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                    $city[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                    $dis[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                    $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                    Kohana::$log->add("qwtwfb:location1:$bid:$openid:", $p_location[$i]);
                    Kohana::$log->add("qwtwfb:location2:$bid:$openid:", $u_location);
                    $pos[$i] = @strpos($u_location, $p_location[$i]);
                    if ($pos[$i]!==false) {
                        $position++;
                    }
                }
                $status = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
                if($has_subscribe==2){
                    if(($position >0 && $status=='1')||$status=='0'||!$status){
                        $joinarea=1;
                    }else{
                        $joinarea=2;
                        if($u_location){
                            $model_q->joinarea=2;
                            $model_q->save();
                            $msg['touser'] = $model_q->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $config['reply'];
                            $wx->sendCustomMessage($msg);
                            $msg2['touser'] = $fopenid;
                            $msg2['msgtype'] = 'text';
                            $msg2['text']['content'] = "不好意思，您的朋友{$model_q->nickname}不在本次活动的参与地区，不要灰心哦，请继续关注我们的公众号，有更多惊喜等着你呢！";
                            $wx->sendCustomMessage($msg2);
                        }else{
                            $model_q->joinarea=1;
                            $model_q->save();
                            $msg['touser'] = $model_q->openid;
                            $msg['msgtype'] = 'text';
                            $url = 'http://'. $_SERVER['HTTP_HOST'] .'/smfyun/check_location/'.$bid.'/'.$openid.'/wfb';
                            $replyhref = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyhref')->find()->value;
                            $msg['text']['content'] = '<a href="'.$url.'">'.$replyhref.'</a>';
                            $wx->sendCustomMessage($msg);
                        }
                    }
                }
                //首次关注积分
                if (ORM::factory('qwt_wfbscore')->where('qid', '=', $model_q->id)->where('type', '=', 1)->count_all() == 0 &&$has_subscribe==2&&$joinarea==1) {
                    if ($config['goal0'] > 0) {

                        $model_q = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                        $sid=$model_q->scores->scoreIn($model_q, 1, $config['goal0']);
                        if($config['switch']==1){
                            require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                            $smfy=new SmfyQwt();
                            $result=$smfy->wfbrsync($bid,$model_q->openid,$yzaccess_token,$config['goal0'],$sid,'首次关注');
                        }
                    }
                }
                //先保存关系
                if ($model_q->id > $fuser->id &&$has_subscribe==2) {
                    //处女？
                    $chunv = 1;
                    if($joinarea==1||$model_q->joinarea==1){
                       $model_q->fopenid = $fopenidFromQrcode;
                    }
                    $model_q->save();

                    //男人袜女性积分减半
                    // if ($bid == 2) {
                    //     if ($userinfo['sex'] != 1) $config['goal'] = $config['goal']/2;
                    // }
                    if($joinarea==1){
                        //推荐人 积分增加处理   上一级用户
                        if ($config['goal'] > 0&&$fuser->lock!=1) {
                            $fuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                            $goal_result = $model_q->scores->scoreIn($fuser, 2, $config['goal'], $model_q->id);
                            if($config['switch']==1){
                                require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                                $smfy=new SmfyQwt();
                                $result=$smfy->wfbrsync($bid,$fuser->openid,$yzaccess_token,$config['goal'],$goal_result,'上级加积分');
                            }
                        }
                        $fuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();
                        Kohana::$log->add("wfbscore$user->id",$fuser->score);
                        //积分话术
                        $config['text_goal'] .= "您当前". $this->scorename ."为：{$fuser->score}";

                        $tpl = $config['text_goal'];
                        $msg['touser'] = $fopenidFromQrcode;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = sprintf($tpl, $userinfo['nickname']);

                        if ($goal_result) $wx->sendCustomMessage($msg);
                        if ($bid == $debug_bid) Kohana::$log->add("qwtwfb:$bid:we_result_fuser", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);

                        //更新上一级用户的 userinfo
                        $fuserinfo = $wx->getUserInfo($fuser->openid);
                        if ($fuserinfo['subscribe'] == 0) {
                            if ($bid == $debug_bid) Kohana::$log->add("qwtwfb:$bid:FuserInfo", print_r($fuserinfo, true));
                            $fuser->subscribe = 0;
                            $fuser->save();
                            // $fuser->values($fuserinfo);
                            // $fuser->save();
                        }

                        //风险判断
                        if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0) {
                            //直接用户
                            $count2 = ORM::factory('qwt_wfbqrcode', $fuser->id)->scores->where('type', '=', 2)->count_all();
                            //用是否生成海报判断真实下线
                            $count3 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($fuser->bid))->where('bid', '=', $fuser->bid)->where('fopenid', '=', $fuser->openid)->where('ticket', '<>', '')->count_all();
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
                                $we_result = $wx->sendCustomMessage($msg);
                            }
                        }

                        //上一级推荐人 积分增加处理
                        //珂心如意
                        $ffuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                        if ($ffuser->fopenid) {
                            //返利给 $ffuser 的上线 $ffuser->fopenid;
                            $fffuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                            if ($fffuser && $config['goal2'] > 0&&$fffuser->lock!=1) {


                                $fffuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();//上上级
                                $goal2_result = ORM::factory('qwt_wfbscore')->scoreIn($fffuser, 3, $config['goal2'], $model_q->id);
                                if($config['switch']==1){
                                    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                                    $smfy=new SmfyQwt();
                                    $result=$smfy->wfbrsync($bid,$fffuser->openid,$yzaccess_token,$config['goal2'],$goal2_result,'上上级加积分');
                                }

                            }
                            Kohana::$log->add("wfbscore$fffuser->id",$fffuser->score);
                            $config['text_goal2'] .= "您当前". $this->scorename ."为：{$fffuser->score}";

                            $nickname = $ffuser->nickname;
                            $tpl = $config['text_goal2'];
                            $msg['touser'] = $ffuser->fopenid;
                            $msg['text']['content'] = sprintf($tpl, $nickname);

                            if ($goal2_result) $we_result = $wx->sendCustomMessage($msg);
                        }
                    }
                }
            }

            //已经有上级就直接取来
            else {
                //$fuser = ORM::factory('qwt_wfbqrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
                $fopenid = $fuser->openid;
            }

            //扫码后默认推送消息
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg2['touser'] = $fopenid;
            $msg2['msgtype'] = 'text';
            //remove emoji
            $nickname = $fuser->nickname;
            //$nickname = preg_replace('/[\xf0-\xf7].{3}/', '', $fuser->nickname);
            $nickname = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $nickname);



            // if ($model_q->fopenid && $fopenidFromQrcode != $model_q->fopenid && $fuser->nickname) $msg['text']['content'] = "亲，你已经是「{$nickname}」的支持者了，不能再扫了哦。";
            //如果 当前用户有效 && 当前用户有上级 && 来源二维码有效
            //自定义回复文本消息
            //读取数据库： $replySet = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'text_goal3')->find()->value;
            $replySet = $config['text_goal3'];
            $replySet2 = $config['text_goal4'];
            if(!$replySet){
                $replySet="恭喜您成为了「%s」的支持者";
            }
            if(!$replySet2){
                $replySet2="您已经是「%s」的支持者了，不用再扫了哦。";
            }
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
            if ($model_q->id &&$model_q->fopenid && $fopenid){
                $fnickname = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find()->nickname;
                //$msg['text']['content'] = sprintf($replySet2,$fnickname);
                $msg['text']['content'] = str_replace('%s', $fnickname, $replySet2);
            }
            if ($chunv) $msg['text']['content'] = str_replace('%s', $fnickname, $replySet);

            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己无上线的不发消息

            //if ($model_q->fopenid) $we_result = $wx->sendCustomMessage($msg);
            if($msg&& $model_q->joinarea==0){
               $we_result = $wx->sendCustomMessage($msg);
            }
            //扫码后推送网址
            if ($config['text_follow_url']&&$fuser->lock!=1) {
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '活动说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                $we_result = $wx->sendCustomMessage($msg);
            }
        }
        // Kohana::$log->add("111", 'wfb');
        if(strpos($this->Keyword,$config['keyword'])!==false){
            $haibao = 2;
        }
        // Kohana::$log->add("112", 'wfb');

        //菜单点击事件
        if ($userinfo && $Event == 'CLICK' || $chunv2||$haibao==2) {
            // Kohana::$log->add("113", 'wfb');
            if (!$model_q->id) {
                $model_q->bid = $bid;
                $model_q->qid = $qr_user->id;
                $model_q->values($userinfo);
                //$model_q->ip = Request::$client_ip;
                if ($userinfo) $model_q->save();
            }
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $cksum = md5($model_q->openid.date('Y-m-d'));
            //$pos = strpos($mystring, $findme); $findme是你要查找的字符，如果找到返回True，否则返回false
            $count = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $position = 0;
            // $u_location = ORM::factory('qwt_wfbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
            $u_location = $qr_user->area;
            // Kohana::$log->add('wfb:bid', $bid);
            // Kohana::$log->add('wfb:openid', $openid);
            // Kohana::$log->add('wfb:u_location', print_r($u_location,true));
            for ($i=1; $i <=$count ; $i++) {
                $pro[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                $city[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                $dis[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                Kohana::$log->add("qwtwfb:location1:$bid:$openid:", $p_location[$i]);
                Kohana::$log->add("qwtwfb:location2:$bid:$openid:", $u_location);
                $pos[$i] = @strpos($u_location, $p_location[$i]);
                if ($pos[$i]!==false) {
                    $position++;
                }
            }
            // $pos=implode(glue,$pos);
            // $msg['text']['content'] = $pos.$p_location[1].$p_location[2].$p_location[3].$position.$count;
            // $wx->sendCustomMessage($msg);
            // exit;

            $status = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
            if(Model::factory('select_experience')->dopinion($bid,'wfb')){
                if(($position >0 && $status=='1')||$status=='0'||!$status){
                    $isvalue = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key','=','value_'.substr($EventKey,-2))->find()->value;
                    if($isvalue&&substr($iskey, 0,4)!='http'){
                        $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $isvalue);
                    }
                    // Kohana::$log->add("114", 'wfb');
                    //生成海报
                    else if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报'||$haibao==2) {
                        // Kohana::$log->add("115", 'wfb');
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
                            if ($model_q->lock == 100) {
                                $ticket_lifetime = 3600*24*365*3;
                                $qrcode_type = 1;
                                $time = time() + $ticket_lifetime;
                            }

                            $result = $wx->getQRCode('wfb'.$bid, $qrcode_type, $ticket_lifetime);
                            $model_q->lastupdate = $time;

                            $msg['text']['content'] = $config['text_send'];

                            //生成海报并保存
                            $model_q->values($userinfo);
                            $model_q->bid = $bid;
                            $model_q->ticket = $result['ticket'];
                            if(!$result['ticket']){
                                Kohana::$log->add("qwtwfb:$bid:noticket", print_r($result,true));
                            }
                            $model_q->save();

                            $newticket = true;
                        }

                        // 3 条客服消息限制，这里不发了
                        //$we_result = $wx->sendCustomMessage($msg);

                        $md5 = md5($result['ticket'].time().rand(1,100000));

                        //图片合成
                        //模板
                        $imgtpl = DOCROOT."qwt/wfb/tmp/tpl.$bid.jpg";
                        $tmpdir = '/dev/shm/';

                        //判断模板文件是否需要从数据库更新
                        $tpl = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if (!$tpl->pic) {
                            $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                            $we_result = $wx->sendCustomMessage($msg);
                            exit;
                        }

                        if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);

                        if (!file_exists($imgtpl)) {
                            @mkdir(dirname($imgtpl));
                            @file_put_contents($imgtpl, $tpl->pic);
                        }

                        //默认头像
                        $tplhead = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        $default_head_file = DOCROOT."qwt/wfb/tmp/head.$bid.jpg";

                        if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                        if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                        //有海报缓存直接发送
                        $tpl_key = 'qwtwfb:tpl:'.$openid.':'.$tpl->lastupdate;
                        $uploadresult['media_id'] = $mem->get($tpl_key);

                        if ($bid == $debug_bid) $newticket = true;

                        if ($uploadresult['media_id'] && !$newticket) {
                            //pass
                            // Kohana::$log->add('qwtwfb:tpl_key', $tpl_key);
                            // Kohana::$log->add('qwtwfb:media_id_cache', print_r($uploadresult, true));
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
                            $remote_head_url = $qr_user->headimgurl;
                            // if($openid == 'oDhh_1Lne-uvSuWZcxVuvWTYeyqU'){
                            //     $remote_head_url = "http://third182.254.104.16/mmopen/04LrpEqAf4ulWRMj0K2YfcJ5OXbqj6EkRl4XXI2ibJqdAn41wtq3QMLxY7TLiaZle6pcgIuc8IiaB982XPAWgAiaOnU5KQjiaobTr/132";
                            // }
                            $remote_head = curls($remote_head_url);
                            // Kohana::$log->add("qwtwfb:$bid:file:remote_head_url1", print_r($remote_head));
                            // if (!$remote_head) {
                            //     $remote_head_url = str_replace('/0', '/132', $qr_user->headimgurl);
                            //     $remote_head = curls($remote_head_url);
                            // }

                            // //retry... 96px
                            // if (!$remote_head) {
                            //     $remote_head_url = str_replace('/132', '/96', $remote_head_url);
                            //     $remote_head = curls($remote_head_url);
                            // }

                            //获取失败用默认头像
                            if (!$remote_head && $default_head_file) $remote_head = file_get_contents($default_head_file);
                            // Kohana::$log->add("qwtwfb:$bid:file:remote_head_url2", print_r($remote_head));
                            //写入临时头像文件
                            if ($remote_head) file_put_contents($headfile, $remote_head);

                            if (!$remote_head || !$remote_qrcode) {
                                $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                                $we_result = $wx->sendCustomMessage($msg);
                                $model_q->ticket = '';
                                $model_q->save();
                                Kohana::$log->add("qwtwfb:$bid:file:remote_head_url get ERROR!", $remote_head_url);
                                Kohana::$log->add("qwtwfb:$bid:file:remote_qr_url get ERROR!", print_r($result,true));
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
                                    Kohana::$log->add("qwtwfb:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                                    if ($wx->errCode == 45009) {
                                        $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                        $we_result = $wx->sendCustomMessage($msg);
                                        exit;
                                    }
                                } else {
                                    //上传成功 pass
                                    if ($bid == $debug_bid) Kohana::$log->add("qwtwfb:$bid:$newfile upload OK!", print_r($uploadresult, true));
                                }

                            } else {
                                Kohana::$log->add("qwtwfb:$bid:newfile $newfile gen ERROR!");
                                Kohana::$log->add("qwtwfb:$bid:imgtplfile", file_exists($imgtpl));
                                Kohana::$log->add("qwtwfb:$bid:qrcodefile", file_exists($localfile));
                                Kohana::$log->add("qwtwfb:$bid:headfile", file_exists($headfile));
                            }

                            unlink($localfile);
                            unlink($headfile);
                            unlink($newfile);

                            //Cache
                            if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                        }


                        //海报发送前提醒消息
                        $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请重新获取海报！';
                        if ($bid == 64) $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请重新获取海报！';
                        $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;
                        echo '';
                        $we_result = $wx->sendCustomMessage($msg);

                        $msg['msgtype'] = 'image';
                        $msg['image']['media_id'] = $uploadresult['media_id'];
                        unset($msg['text']);

                        $we_result = $wx->sendCustomMessage($msg);

                        if ($bid == $debug_bid) Kohana::$log->add("qwtwfb:$bid:img_msg", var_export($msg, true));
                        if ($bid == $debug_bid) Kohana::$log->add("qwtwfb:$bid:we_result_img", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);
                        exit;
                    }
                    //检查 Auth 是否过期
                    if ($we_result === false) {
                        if ($bid == $debug_bid) Kohana::$log->add("qwtwfb:$bid:we_result", print_r($we_result, true));
                        //$wx->resetAuth();
                    }
                    //$msg['text']['content'] = '符合要求'.$u_location.$p_location;

                }else{
                    //$msg['text']['content'] = '不符合要求'.$u_location.$p_location;
                    // $url = $this->baseurl.'index/'. $bid .'?url=check_location&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                    $url = 'http://'. $_SERVER['HTTP_HOST'] .'/smfyun/check_location/'.$bid.'/'.$openid.'/wfb';
                    $replyhref = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyhref')->find()->value;
                    $msg['text']['content'] = '<a href="'.$url.'">'.$replyhref.'</a>';
                }
            }else{
                $msg['text']['content'] = '体验海报已用完，需要续费后才能正常使用，谢谢！';
            }
            $wx->sendCustomMessage($msg);
            //点击菜单先检测是否有地理位置
            //1 有地理位置且包含数据库中地理位置字符串 按照原计划执行
            //2 无地理位置或者地理位置不符合 发消息跳转重新获取地理位置链接
            //自定义 key
            exit;
        }
