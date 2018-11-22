<?php defined('SYSPATH') or die('No direct script access.');
Class wfb {
    public $methodVersion='3.0.0';
    public $wx;
    public $config;
    public $bid;
    public $txtReply;
    public $scorename;
    public function __construct($bid,$wx,$openid,$qrcode){
        Kohana::$log->add('qwtwfbarea1',$bid);
        Kohana::$log->add('qwtwfbarea1',$openid);
        $sname = ORM::factory('qwt_wfbcfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        // 扫码后判断用户地理位置
        $yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $config=ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
        $count = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
        $position = 0;
        $qr_user=ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
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
        $wfbqrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $wfbqrcode->joinarea=0;
        $wfbqrcode->save();
        if(($position >0 && $status=='1')||$status=='0'||!$status){
            $model_q=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
            if (ORM::factory('qwt_wfbscore')->where('qid', '=', $model_q->id)->where('type', '=', 1)->count_all() == 0) {
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
            $fuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $wfbqrcode->fopenid)->find();
            $fopenidFromQrcode = $fopenid = $fuser->openid;
            if ($model_q->id > $fuser->id ) {
                //处女？
                $chunv = 1;
                //推荐人 积分增加处理   上一级用户
                if ($config['goal'] > 0&&$fuser->lock!=1) {
                    $goal_result = $model_q->scores->scoreIn($fuser, 2, $config['goal'], $model_q->id);
                    if($config['switch']==1){
                        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                        $smfy=new SmfyQwt();
                        $result=$smfy->wfbrsync($bid,$fuser->openid,$yzaccess_token,$config['goal'],$goal_result,'上级加积分');
                    }
                }
                //积分话术
                $config['text_goal'] .= "您当前". $this->scorename ."为：{$fuser->score}";
                $tpl = $config['text_goal'];
                $msg['touser'] = $fopenidFromQrcode;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = sprintf($tpl, $model_q->nickname);
                if ($goal_result) $wx->sendCustomMessage($msg);
                //更新上一级用户的 userinfo
                $fuserinfo = $wx->getUserInfo($fuser->openid);
                if ($fuserinfo['subscribe'] == 0) {
                    $fuser->subscribe = 0;
                    $fuser->save();
                }
                //风险判断
                if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0) {
                    //直接用户
                    $count2 = ORM::factory('qwt_wfbqrcode', $fuser->id)->scores->where('type', '=', 2)->count_all();
                    //用是否生成海报判断真实下线
                    $count3 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($fuser->bid))->where('bid', '=', $fuser->bid)->where('fopenid', '=', $fuser->openid)->where('ticket', '<>', '')->count_all();
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
            $replySet = $config['text_goal3'];
            if(!$replySet){
              $replySet="恭喜您成为了「%s」的支持者";  
            }
            $this->txtReply = sprintf($replySet,$fuser->nickname);
            // if($msg){
            //    $we_result = $wx->sendCustomMessage($msg);
            // }
        }else{
            //$fuser=ORM::factory('qwt_wfbqrcode')->where('bid','=',$bid)->where('openid','=',$wfbqrcode->fopenid)->find();
            $msg2['touser'] = $wfbqrcode->fopenid;
            $msg2['msgtype'] = 'text';
            $msg2['text']['content'] = "不好意思，您的朋友{$wfbqrcode->nickname}不在本次活动的参与地区，不要灰心哦，请继续关注我们的公众号，有更多惊喜等着你呢！";
            $wx->sendCustomMessage($msg2);
            $wfbqrcode->fopenid='';
            $wfbqrcode->save();
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $config['reply'];
            $wx->sendCustomMessage($msg);
        }
    }
    public function end(){
        return $this->txtReply;
    }
}
