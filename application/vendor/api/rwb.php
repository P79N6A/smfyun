<?php defined('SYSPATH') or die('No direct script access.');
Class rwb {
    public $Keyword;
    public $cdnurl ;
    public $baseurl ;
    public $methodVersion='3.0.0';
    public $wx;
    public $config;
    public $bid;
    public $client;
    public $yzaccess_token;
    public function __construct($Keyword,$wx,$bid,$openid,$userinfo,$qr_user,$Event,$mem){
        Kohana::$log->add("qwtrwb{$bid}", '进api啦');
        Kohana::$log->add('1', print_r($Keyword,true));
        //Kohana::$log->add('2', print_r($wx,true));
        Kohana::$log->add('3', print_r($bid,true));
        Kohana::$log->add('4', print_r($openid,true));
        // Kohana::$log->add('5', print_r($userinfo,true));
        // Kohana::$log->add('6', print_r($qr_user,true));
        Kohana::$log->add('7', print_r($Event,true));
        // Kohana::$log->add('8', print_r($mem,true));
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtrwb/';
        $this->cdnurl = 'http://cdn.jfb.smfyun.com/qwt/rwb/';
        $this->bid = $bid;
        $this->Keyword = $Keyword;
        $this->wx = $wx;
        $this->config = $config = ORM::factory('qwt_rwbcfg')->getCfg($bid,1);
        $mem = Cache::instance('memcache');
        $sname = ORM::factory('qwt_rwbcfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '人气值';
        }
        $userinfo = $wx->getUserInfo($openid);
        if ($userinfo == false) {
            Kohana::$log->add("qwtrwb:$bid:wx", $wx->errCode.':'.$wx->errMsg);

            if ($wx->errCode != 45009) {
                $mem = Cache::instance('memcache');
                $cachename1 ='qwt.access_token'.$bid;
                $ctoken = $mem->delete($cachename1);
            }

            if (!$txtReply) $txtReply = '抱歉哦，消息一不小心走丢了，麻烦再次点击下，谢谢谅解～';
        }
        $EventKey = $wx->getRevEvent()['key'];
        $Ticket = $wx->getRevTicket();
        //当前微信用户
        $model_q = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        //获取地理位置事件
        //扫码事件 || 扫码关注
        // $scene_id = $bid;
        if($EventKey == $bid || $EventKey == 'rwb'.$bid){
            $EventKeyget = 1;
        }
        if($wx->getRevSceneId() == $bid || $wx->getRevSceneId() == 'rwb'.$bid){
            $wxgetRevSceneId = 1;
        }
        if ($userinfo && ($Event == 'SCAN' && $EventKeyget == 1) || ($wxgetRevSceneId == 1)) {
            //新用户
            if (!$model_q->id) {
                // Kohana::$log->add("qwtrwb:$bid:model_q", 'model_q');
                $model_flag=1;
                Kohana::$log->add("model_flag$bid$openid",$model_flag);
                $model_q->bid = $bid;
                $model_q->qid= $qr_user->id;
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
            //珂心如意
            $fuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;

            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户&&当前用户未锁定&&上级未锁定
            if ($model_flag==1&&$model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid && $ffopenid!=$openid&&$model_q->lock!=1&&$fuser->lock!=1) {
                //首次关注积分
                $model_q = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                $task =ORM::factory('qwt_rwbtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
                $tid=ORM::factory('qwt_rwbtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find()->id;
                $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if($subscribe->id&&$subscribe->creattime<=time()-60){
                    Kohana::$log->add("rwb{$bid}SCAN111",print_r($subscribe->openid, true));
                    $has_subscribe=1;
                    $model_p = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                    $model_p->old=1;
                    $model_p->save();
                    Kohana::$log->add("has_subscribe$bid$openid",$has_subscribe);
                }else{
                    if($task->id){
                        $record=ORM::factory('qwt_rwbrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$model_q->id)->find();

                        if(!$record->id){
                            $record->bid=$bid;
                            $record->tid=$task->id;
                            $record->qid=$model_q->id;
                            $record->fqid=$fuser->id;
                            $record->save();
                        }
                        $record2=ORM::factory('qwt_rwbrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$fuser->id)->find();
                        Kohana::$log->add("record2", print_r($record2->id,true));
                        if(!$record2->id){
                            $record2->bid=$bid;
                            $record2->tid=$task->id;
                            $record2->qid=$fuser->id;
                            $record2->save();
                        }

                    }
                    $last_num=ORM::factory('qwt_rwbrecord')->where('bid','=',$bid)->where('fqid','=',$fuser->id)->where('tid','=',$tid)->count_all();
                    $model_p = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                    $model_p->old=0;
                    $model_p->save();
                    $has_subscribe=2;
                    Kohana::$log->add("has_subscribe$bid$openid",$has_subscribe);
                }
                Kohana::$log->add("rwb{$bid}SCAN",print_r(time(), true));
                // 扫码后判断用户地理位置
                $count = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
                $position = 0;
                $u_location = $qr_user->area;
                for ($i=1; $i <=$count ; $i++) {
                    $pro[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                    $city[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                    $dis[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                    $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                    Kohana::$log->add("qwtrwblocation1:$bid:$openid:", $p_location[$i]);
                    Kohana::$log->add("qwtrwblocation2:$bid:$openid:", $u_location);
                    $pos[$i] = @strpos($u_location, $p_location[$i]);
                    if ($pos[$i]!==false) {
                        $position++;
                    }
                }
                $status = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
                if($has_subscribe==2){
                    Kohana::$log->add("qwtrwblocationlocation",$u_location);
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
                            $url = 'http://'. $_SERVER['HTTP_HOST'] .'/smfyun/check_location/'.$bid.'/'.$openid.'/rwb';
                            $replyhref = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyhref')->find()->value;
                            $msg['text']['content'] = '<a href="'.$url.'">'.$replyhref.'</a>';
                            $wx->sendCustomMessage($msg);
                        }
                    }
                    Kohana::$log->add("qwtrwblocationjoinarea",$joinarea);
                }
                //先保存关系
                if ($model_q->id&&$has_subscribe==2) {
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
                    //推荐人 积分增加处理   上一级用户
                    if($joinarea==1){
                        $fuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                        $mgtpl=$config['mgtpl'];
                        Kohana::$log->add("mgtpl", print_r($mgtpl, true));
                        if($tid){
                            $num =ORM::factory('qwt_rwbrecord')->where('bid','=',$bid)->where('tid','=',$tid)->where('fqid','=',$fuser->id)->count_all();
                            $sku_all=ORM::factory('qwt_rwbsku')->where('bid','=',$bid)->where('tid','=',$tid)->count_all();
                            $skus = ORM::factory('qwt_rwbsku')->where('bid','=',$bid)->where('tid','=',$tid)->order_by('num', 'ASC')->find_all();
                            $sql = DB::query(Database::SELECT,"SELECT * from qwt_rwbskus where `bid` = $bid and `tid` = $tid");
                            $sku_nests =$sql->execute()->as_array();
                            Kohana::$log->add("sku_nests", print_r($sku_nests, true));
                            $flag=3;
                            $alltime=0;
                            $finish=0;
                            $sku_nest=0;
                            foreach ($skus as $sku) {
                                $flag=3;
                                $alltime++;
                                $sku_stock=$sku->stock;
                                $sku_num=$sku->num;
                                $text=$sku->text;
                                 Kohana::$log->add("111", '1111');
                                $item_name=$sku->item->km_content;
                                Kohana::$log->add("item_name", print_r($item_name, true));
                                if($alltime!=$sku_all){
                                    $sku_nest = $sku_nests[$alltime]['num'];
                                    $item_next=ORM::factory('qwt_rwbitem')->where('id','=',$sku_nests[$alltime]['iid'])->find()->km_content;
                                    Kohana::$log->add("item_next", print_r($item_next, true));
                                }
                                Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                                Kohana::$log->add("sku_all", print_r($sku_all, true));
                                Kohana::$log->add("alltime", print_r($alltime, true));
                                $ordernum=ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                                Kohana::$log->add("num", print_r($num, true));
                                Kohana::$log->add("sku_num", print_r($sku_num, true));
                                if($num>=$sku->num){
                                    $flag=1;
                                    $order=ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('tid','=',$tid)->where('kid','=',$sku->id)->where('qid','=',$fuser->id)->find();
                                    if(!$order->id){
                                        $flag=1;
                                        $item=ORM::factory('qwt_rwbitem')->where('id','=',$sku->iid)->find();
                                        $ordernum=ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                                        //将库存存入memcache
                                        $m = new Memcached();
                                        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
                                        $keyname="qrwb_ordernum:{$bid}:{$sku->id}";
                                        do {
                                            $onum = $m->get($keyname, null, $cas);
                                            if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                                                $m->add($keyname, $ordernum);
                                            } else {
                                                $m->cas($cas, $keyname, $ordernum);
                                            }
                                        } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                                        do {
                                            $ordernum = $m->get($keyname, null, $cas1);
                                            $ordernum+=1;
                                            $m->cas($cas1, $keyname, $ordernum);
                                        } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                                        if($ordernum<=$sku->stock){
                                            Kohana::$log->add("qwt_rwb:$bid:stock", print_r($sku->stock, true));
                                            if($alltime==$sku_all){
                                                $finish=1;
                                            }else{
                                                $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                                $ordernum_next=ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('kid','=',$sku_nests[$alltime]['id'])->where('state','=',1)->count_all();
                                                $stock_next=$sku_nests[$alltime]['stock']-$ordernum_next;
                                                Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                                            }
                                            if($item->key=='hongbao'){
                                                $this->sendHongbao($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }elseif ($item->key=='coupon') {
                                                Kohana::$log->add("qwt_rwb:$bid:coupon", 'coupon');
                                                $this->sendCoupon($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }elseif ($item->key=='yhm') {
                                                $this->sendYhm($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }elseif ($item->key=='yzcoupon') {
                                                Kohana::$log->add("qwt_rwb:$bid:yzcoupon", 'yzcoupon');
                                                $this->sendYzcoupon($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }elseif ($item->key=='gift') {
                                                $this->sendGift($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }elseif ($item->key=='kmi') {
                                                $this->sendKmi($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }elseif ($item->key=='shiwu') {
                                                $this->sendShiwu($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                                break;
                                            }
                                        }else{
                                            $order=ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('iid','=',$item->id)->where('qid','=',$fuser->id)->where('tid','=',$tid)->where('kid','=',$sku->id)->find();
                                            if(!$order->id){
                                                $order->bid=$bid;
                                                $order->tid=$tid;
                                                $order->qid=$fuser->id;
                                                $order->iid=$item->id;
                                                $order->kid=$sku->id;
                                                $order->status=1;
                                                $order->name=$fuser->nickname;
                                                $order->task_name=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find()->name;
                                                $order->item_name=$item->km_content;
                                                $order->state=0;
                                                $order->log='库存不足';
                                                $order->save();
                                            }
                                            if($sku_nest!=0){
                                                $text_goal=$config['text_goal'];
                                                $text_goals=sprintf($text_goal,$model_q->nickname);
                                                $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                                $keyword=$text_goals.',您还需要'.$sku_nest.'个支持者就可以获得'.$item_next;
                                                $keyword1=$task->name;
                                                $keyword2="本级奖品已被领完，继续加油，么么哒。";
                                                $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                                                break;
                                            }else{
                                                $text_goal2=$config['text_goal2'];
                                                $text_goal2s=sprintf($text_goal2,$task->name);
                                                $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                                $keyword=$model_q->nickname.'成为了你的支持者'.$text_goal2s;
                                                $keyword1=$task->name;
                                                $keyword2='本级奖品已被领完,继续加油，么么哒。';
                                                $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                                                break;
                                            }
                                        }
                                    }
                                }else{
                                    break;
                                }
                            }
                        }else{
                            $keyword=$model_q->nickname.'成为了你的支持者';
                            $keyword1=$task->name;
                            $keyword2='暂时没有有效的任务哦，请继续关注我们的任务信息，么么哒。';
                            $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                        }
                        if(!$num){
                            $num=0;
                        }
                        if($flag==3){
                            $text_goal=$config['text_goal'];
                            $text_goals=sprintf($text_goal,$model_q->nickname);
                            $need_num=$sku_num-$last_num;
                            $left_num=$sku_stock-$ordernum;
                            $keyword=$text_goals.'您还需要'.$need_num.'个支持者就可以获得'.$item_name;
                            $keyword1=$task->name;
                            //$keyword2="\\n你试试";
                            $keyword2="任务目标:{$sku_num}\\n已经完成:{$last_num}\\n还需人数:{$need_num}\\n{$item_name}剩余数量:{$left_num}";
                            //$openid=$fuser->openid;
                            Kohana::$log->add("openid", print_r($fuser->openid, true));
                            Kohana::$log->add("mgtpl", print_r($mgtpl, true));
                            Kohana::$log->add("keyword", print_r($keyword, true));
                            Kohana::$log->add("keyword1", print_r($keyword1, true));
                            Kohana::$log->add("keyword2", print_r($keyword2, true));
                            $lll=$this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                            Kohana::$log->add("lll$bid", print_r($lll, true));
                        }
                        if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:we_result_fuser", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);
                        //更新上一级用户的 userinfo
                        $fuserinfo = $wx->getUserInfo($fuser->openid);
                        if ($fuserinfo['subscribe'] == 0) {
                            if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:FuserInfo", print_r($fuserinfo, true));
                            $fuser->subscribe = 0;
                            $fuser->save();
                            // $fuser->values($fuserinfo);
                            // $fuser->save();
                        }
                        //上一级推荐人 积分增加处理
                        //珂心如意
                        $ffuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();
                    }
                }
            }
            //已经有上级就直接取来
            else {
                //$fuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
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
            //读取数据库： $replySet = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'text_goal3')->find()->value;
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
            Kohana::$log->add("$bid:rwb:weixin_scan1:$openid", print_r($msg,true));
            if ($has_subscribe==1&&$model_q->lock!=1&&$fuser->lock!=1){
                $msg['text']['content'] = "您已经关注过公众号了，不用再扫了哦。快去生成海报发起活动吧~";
                if($fopenid && $fopenid != $openid && $ffopenid!=$openid){
                    $msg2['text']['content'] = "您的朋友".$model_q->nickname."已经关注过公众号了，不能再成为您的粉丝了";
                    $wx->sendCustomMessage($msg2);
                }
            }
            Kohana::$log->add("$bid:rwb:weixin_scan2:$openid", print_r($msg,true));
            if ($model_q->id && $model_q->fopenid && $fopenid){
                $fnickname = ORM::factory('qwt_rwbqrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find()->nickname;
                //$msg['text']['content'] = sprintf($replySet2,$fnickname);
                $msg['text']['content'] = str_replace('%s', $fnickname, $replySet2);
            }
            Kohana::$log->add("$bid:rwb:weixin_scan3:$openid", print_r($msg,true));
            if ($chunv) $msg['text']['content'] = str_replace('%s', $fnickname, $replySet);

            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己无上线的不发消息

            //if ($model_q->fopenid) $we_result = $wx->sendCustomMessage($msg);
            Kohana::$log->add("$bid:rwb:weixin_scan4:$openid", print_r($msg,true));
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
        Kohana::$log->add("3333", '进入rwb');
        if(strpos($this->Keyword,$config['keyword'])!==false){
            $haibao = 2;
        }
        //菜单点击事件
        if ($userinfo && $Event == 'CLICK' &&$EventKey == 'qrcode'|| $chunv2||$haibao==2) {
            Kohana::$log->add("222", '进入rwb');
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
            $count = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $position = 0;
            // $u_location = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
            $u_location = $qr_user->area;
            // Kohana::$log->add('rwb:bid', $bid);
            // Kohana::$log->add('rwb:openid', $openid);
            // Kohana::$log->add('rwb:u_location', print_r($u_location,true));
            for ($i=1; $i <=$count ; $i++) {
                $pro[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                $city[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                $dis[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
                $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
                Kohana::$log->add("qwtrwb:", $p_location[$i]);
                Kohana::$log->add("qwtrwb:", $u_location);
                $pos[$i] = @strpos($u_location, $p_location[$i]);
                if ($pos[$i]!==false) {
                    $position++;
                }
            }
            $status = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
            if(($position >0 && $status=='1')||$status=='0'||!$status){
                $isvalue = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key','=','value_'.substr($EventKey,-2))->find()->value;
                if($isvalue&&substr($iskey, 0,4)!='http'){
                    $txtReply = $msg['text']['content'] = str_replace('\n', "\n", $isvalue);
            }
            //生成海报
            //Kohana::$log->add("qwtrwbhaibao", print_r($haibao,true));
            else if ($EventKey == 'qrcode' || $chunv || $EventKey == '生成海报'||$haibao==2) {
                Kohana::$log->add("111111", '进入rwb');
                if(!Model::factory('select_experience')->dopinion($bid,'rwb')){
                    $msg['text']['content'] = "体验海报已用完，需要续费后才能正常使用，谢谢！";
                    $wx->sendCustomMessage($msg);
                    die();
                }
                $task1 =ORM::factory('qwt_rwbtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
                if($task1->id){
                    $aaaa='有有效任务';
                }else{
                    $task2 =ORM::factory('qwt_rwbtask')->where('bid','=',$bid)->where('endtime','>',time())->find();
                    if($task2->id&&$task2->begintime!=$task2->endtime){
                        $msg['text']['content'] = "活动尚未开始";
                        $wx->sendCustomMessage($msg);
                    }elseif(ORM::factory('qwt_rwbtask')->where('bid','=',$bid)->find()->id){
                        $msg['text']['content'] = "活动已经结束";
                        $wx->sendCustomMessage($msg);
                    }else{
                        $msg['text']['content'] = "活动尚未开始";
                        $wx->sendCustomMessage($msg);
                    }
                    die();
                }
                $ticket_lifetime = 3600*24*7;
                Kohana::$log->add("23", '进入rwb');
                //自定义过期时间      不可删
                if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

                $qrcode_type = 0;
                Kohana::$log->add("33", '进入rwb');
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

                    $result = $wx->getQRCode('rwb'.$bid, $qrcode_type, $ticket_lifetime);
                    $model_q->lastupdate = $time;

                    $msg['text']['content'] = $config['text_send'];

                    //生成海报并保存
                    $model_q->values($userinfo);
                    $model_q->bid = $bid;
                    $model_q->ticket = $result['ticket'];
                    $model_q->save();

                    $newticket = true;
                }
                Kohana::$log->add("312", '进入rwb');
                // 3 条客服消息限制，这里不发了
                //$we_result = $wx->sendCustomMessage($msg);

                $md5 = md5($result['ticket'].time().rand(1,100000));

                //图片合成
                //模板
                $imgtpl = DOCROOT."qwt/rwb/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                if (!$tpl->pic) {
                    $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                    $we_result = $wx->sendCustomMessage($msg);
                    exit;
                }
                Kohana::$log->add("312", '进入rwb');
                if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);

                if (!file_exists($imgtpl)) {
                    @mkdir(dirname($imgtpl));
                    @file_put_contents($imgtpl, $tpl->pic);
                }
                Kohana::$log->add("23", '进入rwb');
                //默认头像
                $tplhead = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                $default_head_file = DOCROOT."qwt/rwb/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                Kohana::$log->add("23", '进入rwb');
                //有海报缓存直接发送
                $tpl_key = 'qwtrwb:tpl:'.$openid.':'.$tpl->lastupdate;
                $uploadresult['media_id'] = $mem->get($tpl_key);
                Kohana::$log->add('mem', print_r($mem,true));
                if ($bid == $debug_bid) $newticket = true;

                if ($uploadresult['media_id'] && !$newticket) {
                    Kohana::$log->add("2322", '进入rwb');
                    //pass
                    // Kohana::$log->add('qwtrwb:tpl_key', $tpl_key);
                    // Kohana::$log->add('qwtrwb:media_id_cache', print_r($uploadresult, true));
                } else {
                    Kohana::$log->add("ssd11", '进入rwb');
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
                    $remote_head = curls($remote_head_url);

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
                    if (!$remote_head && $default_head) $remote_head = file_get_contents($default_head_file);
                    //写入临时头像文件
                    if ($remote_head) file_put_contents($headfile, $remote_head);

                    if (!$remote_head || !$remote_qrcode) {
                        $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                        $we_result = $wx->sendCustomMessage($msg);
                        $model_q->ticket = '';
                        $model_q->save();
                        Kohana::$log->add("qwtrwb:$bid:file:remote_head_url get ERROR!", $remote_head_url);

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
                            Kohana::$log->add("qwtrwb:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                            if ($wx->errCode == 45009) {
                                $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                $we_result = $wx->sendCustomMessage($msg);
                                exit;
                            }
                        } else {
                            //上传成功 pass
                            if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:$newfile upload OK!", print_r($uploadresult, true));
                        }

                    } else {
                        Kohana::$log->add("qwtrwb:$bid:newfile $newfile gen ERROR!");
                        Kohana::$log->add("qwtrwb:$openid:$bid:imgtplfile", file_exists($imgtpl));
                        Kohana::$log->add("qwtrwb:$openid:$bid:qrcodefile", file_exists($localfile));
                        Kohana::$log->add("qwtrwb:$openid:$bid:headfile", file_exists($headfile));
                        Kohana::$log->add("qwtrwb:$openid:$bid:file:remote_head_url2", print_r($remote_head));
                    }

                    unlink($localfile);
                    unlink($headfile);
                    unlink($newfile);

                    //Cache
                    if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                }
                Kohana::$log->add("222111", '进入rwb');
                //海报发送前提醒消息
                $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请重新获取海报！';
                if ($bid == 64) $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请重新获取海报！';
                //$msg['text']['content'] = str_replace('\n', "\n", $config['text_send']). "\n\n" .$txtReply2;
                $msg['text']['content'] =  $config['text_send']. "\n\n" .$txtReply2;
                $we_result = $wx->sendCustomMessage($msg);
                Kohana::$log->add("333111", '进入rwb');
                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                unset($msg['text']);

                $we_result = $wx->sendCustomMessage($msg);
                Kohana::$log->add("313121", '进入rwb');
                if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:img_msg", var_export($msg, true));
                if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:we_result_img", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);
                exit;
            }
            // else if ($EventKey) {
            //     $msg['msgtype'] = 'text';
            //     $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「任务宝」商户后台完成配置：'.$EventKey;

            // }
            //检查 Auth 是否过期
            if ($we_result === false) {
                if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:we_result", print_r($we_result, true));
                //$wx->resetAuth();
            }
            //$msg['text']['content'] = '符合要求'.$u_location.$p_location;

          }
            else{
                // $url = 'http://'. $_SERVER['HTTP_HOST'].'/smfyun/check_location/'. $bid .'/'.$openid.'/rwb';
                //$msg['text']['content'] = '不符合要求'.$u_location.$p_location;
                //$url = $this->baseurl.'index/'. $bid .'?url=check_location&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                // $replyfront = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
                // $replyend = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
                //$msg['text']['content'] = $replyfront.'<a href="'.$url.'">点击查看是否在活动范围内</a>'.$replyend;
                // $msg['text']['content'] = $config['reply'];
                $url = 'http://'. $_SERVER['HTTP_HOST'] .'/smfyun/check_location/'.$bid.'/'.$openid.'/rwb';
                $replyhref = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'replyhref')->find()->value;
                $msg['text']['content'] = '<a href="'.$url.'">'.$replyhref.'</a>';
            }
            $wx->sendCustomMessage($msg);
            exit;
        }
        if($txtReply){
            $this->txtReply = $txtReply;
        }

    }
    public function sendShiwu($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        $bid = $this->bid;
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        // $value=$items->value;
        // $hello = explode('&',$value);
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        Kohana::$log->add("tag", print_r($hello[0],true));
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        Kohana::$log->add("qwt_rwbresult", print_r($aa,true));
        if($finish==1){
            $url=$_SERVER['HTTP_HOST'].'/qwtrwb/shiwu/1?bid='.$bid.'&qid='.$qid.'&kid='.$kid.'&iid='.$iid.'&tid='.$tid;
            $text_goal2=$this->config['text_goal2'];
            $text_goal2s=sprintf($text_goal2,$task_name);
            $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
            $keyword1=$task_name;
            $keyword2="您的全部任务已完成\\n{$text}";
            $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }else{
            $url=$_SERVER['HTTP_HOST'].'/qwtrwb/shiwu/1?bid='.$bid.'&qid='.$qid.'&kid='.$kid.'&iid='.$iid.'&tid='.$tid;
            $text_goal=$this->config['text_goal'];
            $text_goals=sprintf($text_goal,$nickname);
            $keyword=$text_goals."您的当前任务已完成,恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
            $keyword1=$task_name;
            $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
            $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }
        Kohana::$log->add("qwt_rwbtpl_{$this->bid}", print_r($result,true));
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->status=0;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        if($result['errmsg']=='ok'){
            $order->state=1;
            $order->save();
        }else{
            $order->state=0;
            $order->log=$result['errmsg'];
            $order->save();
            $this->sendCustomMessage($openid,$result['errmsg']);

        }
        Kohana::$log->add("发特权", '1111');
    }
    public function sendHongbao($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        $bid = $this->bid;
        Kohana::$log->add("发红包iid", print_r($iid,true));
        Kohana::$log->add("发红包qid", print_r($qid,true));
        Kohana::$log->add("发红包tid", print_r($tid,true));
        Kohana::$log->add("发红包kid", print_r($kid,true));
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        Kohana::$log->add("bid", print_r($this->bid,true));
        $shop = ORM::factory("qwt_login")->where('id','=',$this->bid)->find();
        $tempname = $shop->weixin_name;
        Kohana::$log->add("tempname", print_r($tempname,true));
        $this->config['appid'] = $shop->appid;
        $this->config['name'] = $tempname;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        $result = $this->hongbao1($this->config, $openid, '', $tempname, $value);
        Kohana::$log->add("result", print_r($result,true));
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->status=1;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        if($result['result_code']=='SUCCESS'){
            $order->state=1;
            $order->save();
            if($finish==1){
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $keyword=$nickname.'成为了你的支持者，'. $text_goal2s.'恭喜您获得奖品'.$item_name;
                $keyword1=$task_name;
                $keyword2=$text;
                $this->sendTemplateMessage($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成,恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                $this->sendTemplateMessage($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }
        }else{
            $order->state=0;
            $order->log= $result['return_msg'];
            $order->save();
            $this->sendCustomMessage($openid,$result['return_msg']);
        }
    }
    public function sendKmi($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        // if (!$this->we) {
        //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
        //     require_once Kohana::find_file('vendor', 'weixin/inc');
        //     //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
        //     $this->we = new Wechat($config);
        // }
        $bid = $this->bid;
        if(!$this->yzaccess_token){
            $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
            $yzaccess_token=$this->yzaccess_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->yzaccess_token);
        }
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $value=$items->value;
        $hello = explode('&',$value);
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        Kohana::$log->add("tag", print_r($hello[0],true));
        $method = 'youzan.users.weixin.follower.tags.add';
        $params = [
            'tags'=> $hello[0],
            'weixin_openid'=>$openid,
        ];
        $aa=$this->client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add("qwt_rwbresult", print_r($aa,true));
        if($finish==1){
                $url=$hello[1];
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
                $keyword1=$task_name;
                $keyword2="您的全部任务已完成\\n{$text}";
                $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }else{
                $url=$hello[1];
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成,恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }
        Kohana::$log->add("qwt_rwbtpl_{$this->bid}", print_r($result,true));
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->status=1;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        if($result['errmsg']=='ok'){
            $order->state=1;
            $order->save();
        }else{
            $order->state=0;
            $order->log=$result['errmsg'];
            $order->save();
            $this->sendCustomMessage($openid,$result['errmsg']);

        }
        Kohana::$log->add("发特权", '1111');
    }
    public function sendCoupon($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        Kohana::$log->add("qwt_rwb_coupon_tplmsg:{$this->bid}", $mgtpl);
        // if (!$this->we) {
        //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
        //     require_once Kohana::find_file('vendor', 'weixin/inc');
        //     //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
        //     $this->we = new Wechat($config);
        // }
        $bid = $this->bid;
        // if(!$this->yzaccess_token){
        //     $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        //     $yzaccess_token=$this->yzaccess_token;
        // }
        // if(!$this->client){
        //     require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        //     $this->client = new YZTokenClient($this->yzaccess_token);
        // }
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        if($finish==1){
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $url = 'http://'.$_SERVER ['HTTP_HOST'].'/qwtrwb/ticket/'.$value.'/'.$this->bid;
                $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
                $keyword1=$task_name;
                $keyword2="您的全部任务已完成\\n{$text}";
                $result = $this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $url = 'http://'.$_SERVER ['HTTP_HOST'].'/qwtrwb/ticket/'.$value.'/'.$this->bid;
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                $result = $this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->status=1;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        if($result['errmsg']=='ok'){
            $order->state=1;
            $order->save();
        }else{
            $order->state=0;
            $order->log=$result['errmsg'];
            $order->save();
            $this->sendCustomMessage($openid,$result['errmsg']);
        }
        Kohana::$log->add("微信卡劵", '1111');
    }
    public function sendYzcoupon($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        // if (!$this->we) {
        //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
        //     require_once Kohana::find_file('vendor', 'weixin/inc');
        //     //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
        //     $this->we = new Wechat($config);
        // }
        $bid = $this->bid;
        if(!$this->yzaccess_token){
            $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
            $yzaccess_token=$this->yzaccess_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->yzaccess_token);
        }
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        $method = 'youzan.ump.coupon.take';
        $params = [
            'coupon_group_id'=>$value,
            'weixin_openid'=>$openid,
         ];
        $results = $this->client->post($method, $this->methodVersion, $params, $files);
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->status=1;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        if($results['response']){
            $order->state=1;
            $order->save();
            if($finish==1){
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
                $keyword1=$task_name;
                $keyword2="您的全部任务已完成\\n{$text}";
                $this->sendTemplateMessage($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n{$item_next}剩余数量:{$stock_next}\\n{$text}";
                $this->sendTemplateMessage($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }
        }else if($results['error_response']){
            $order->state=0;
            $order->log=$results['error_response']['code'].$results['error_response']['msg'];
            $order->save();
            $this->sendCustomMessage($openid,$results['error_response']['code'].$results['error_response']['msg']);
        }
        Kohana::$log->add("有赞优惠劵",'1111' );
    }
    public function sendYhm($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        Kohana::$log->add("km", '进入卡密了');
        $bid = $this->bid;
        $mgtpl=$this->config['mgtpl'];
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $item_text=$items->km_text;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->status=1;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        Kohana::$log->add("bid", print_r($this->bid,true));
        Kohana::$log->add("value", print_r($items->value,true));
        $count=ORM::factory('qwt_rwbkm')->where('bid','=',$this->bid)->where('live','=',1)->where('starttime','=',$items->value)->count_all();
         Kohana::$log->add("count", printf($count,true));
        if($count!=0){
            $kmikm=ORM::factory('qwt_rwbkm')->where('bid','=',$this->bid)->where('live','=',1)->where('starttime','=',$items->value)->find();
            $url='http://'.$_SERVER['HTTP_HOST'].'/qwtrwb/kmpass/'.$kmikm->id.'/'.$iid;
            Kohana::$log->add("$this->bid:url", print_r($url,true));
            $password1=$kmikm->password1;
            $password2=$kmikm->password2;
            $password3=$kmikm->password3;
            $msgs=$item_text;
            $id =$kmikm->id;
            $msgs = str_replace("「%a」",$password1,$msgs);
            $password = $password1;
            if($password2){
                $msgs = str_replace("「%b」",$password2,$msgs);
                $password = $password.','.$password2;
                if($password3){
                    $msgs = str_replace("「%c」",$password3,$msgs);
                    $password = $password.','.$password3;
                }
            }
            if($finish==1){
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name.','.$msgs;
                $keyword1=$task_name;
                $keyword2="您的全部任务已完成\\n{$text}";
                $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name.','.$msgs."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n{$item_next}剩余数量:{$stock_next}\\n{$text}";
                $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
            }
            if($result['errmsg']=='ok'){
                $order->state=1;
                $order->save();
                $km=ORM::factory('qwt_rwbkm')->where('id','=',$id)->find();
                $km->live=0;
                $km->save();
            }else{
                $order->state=0;
                $order->log=$result['errmsg'];
                $order->save();
                $this->sendCustomMessage($openid,$result['errmsg']);
            }
        }else{
            $order->state=0;
            $order->log='卡密库存不足';
            $order->save();
            $keyword='卡密库存不足';
            $keyword1=$task_name;
            $this->sendTemplateMessage($openid,$mgtpl,'',$keyword,$keyword1,'');
        }
    }
    public function sendGift($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        $bid = $this->bid;
        // if (!$this->we) {
        //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
        //     require_once Kohana::find_file('vendor', 'weixin/inc');
        //     //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
        //     $this->we = new Wechat($config);
        // }
         if(!$this->yzaccess_token){
            $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
            $yzaccess_token=$this->yzaccess_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->yzaccess_token);
        }
        $items=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwbqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwbtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwbsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        $method = 'youzan.ump.presents.ongoing.all';
        $params = [
        ];
        $results = $this->client->post($method, $this->methodVersion, $params, $files);
        for($h=0;$results['response']['presents'][$h];$h++){
            $res = $results['response']['presents'][$h];
            $present_id=$res['present_id'];
            Kohana::$log->add("present_id",print_r( $present_id,true));
            if($present_id==$value){//找到指定赠品
                $method = 'youzan.users.weixin.follower.get';
                $params = [
                    'weixin_openid'=>$openid,
                    'fields'=>'user_id',
                 ];
                $results = $this->client->post($method, $this->methodVersion, $params, $files);
                $user_id = $results['response']['user']['user_id'];
                Kohana::$log->add("gift_id",print_r( $value,true));
                $method = 'youzan.ump.present.give';
                $params = [
                    'activity_id'=>$value,
                    'fans_id'=>$user_id,
                 ];
                $results = $this->client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add("results",print_r( $results,true));

                $order=ORM::factory('qwt_rwborder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
                if(!$order->id){
                    $order->bid=$this->bid;
                    $order->tid=$tid;
                    $order->qid=$qid;
                    $order->iid=$iid;
                    $order->kid=$kid;
                    $order->status=1;
                    $order->name=$nickname1;
                    $order->task_name=$task_name;
                    $order->item_name=$item_name;
                }
                if($results['response']){
                    $order->state=1;
                    $order->save();
                    if($finish==1){
                        $text_goal2=$this->config['text_goal2'];
                        $text_goal2s=sprintf($text_goal2,$task_name);
                        $url = $results['response']['receive_address'];
                        $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
                        $keyword1=$task_name;
                        $keyword2="您的全部任务已完成\\n{$text}";
                        $this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
                    }else{
                        $text_goal=$this->config['text_goal'];
                        $text_goals=sprintf($text_goal,$nickname);
                        $url = $results['response']['receive_address'];
                        $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                        $keyword1=$task_name;
                        $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                        $this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
                     }
                }else if($results['error_response']){
                    $order->state=0;
                    $log=$results['error_response']['code'].$results['error_response']['msg'];
                    $order->log=$log;
                    $order->save();
                    $this->sendCustomMessage($openid,$log);
                }
            }
        }
        Kohana::$log->add("有赞赠品",'11111' );
    }
    private function checkSignature(){
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
    public function end(){
        return $this->txtReply;
    }
    public function sendCustomMessage($openid,$km_text){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $km_text;
        $result=$this->wx->sendCustomMessage($msg);
        return $result;
    }
    public function sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2){
        Kohana::$log->add("qwt_rwb_lll", 'lll');
        $keyword=str_replace(' ','',$keyword);
        $keyword=str_replace('"','',$keyword);
        $keyword1=str_replace(' ','',$keyword1);
        $keyword1=str_replace('"','',$keyword1);
        $keyword2=str_replace(' ','',$keyword2);
        $keyword2=str_replace('"','',$keyword2);
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $mgtpl;
        $tplmsg['url']=$url;
        $tplmsg['data']['first']['value']=urlencode($keyword);
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = urlencode($keyword1);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        // $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:m');
        // $tplmsg['data']['keyword3']['color'] = '#FF0000';
        $tplmsg['data']['remark']['value'] = urlencode($keyword2);
        $tplmsg['data']['remark']['color'] = '#FF0000';
        Kohana::$log->add("qwt_rwb_tplmsg",print_r($tplmsg,true));
        Kohana::$log->add("qwt_rwb_tplmsg1",print_r(json_encode($tplmsg),true));
        Kohana::$log->add("qwt_rwb_tplmsg2",print_r(urldecode(json_encode($tplmsg)),true));
        $result=$this->wx->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        Kohana::$log->add("qwt_rwb_tplmsg3",print_r($result,true));
        if($result['errmsg']!='ok'){
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $result['errmsg'];
            $this->wx->sendCustomMessage($msg);
        }
        return $result;
    }
    private function hongbao1($config, $openid, $wx='', $bid=1, $money){
        //记录 用户 请求红包
        Kohana::$log->add("qwt_rwb_进发红包了",print_r($openid,true));
        Kohana::$log->add("qwt_rwb_进发红包了",print_r($bid,true));
        Kohana::$log->add("qwt_rwb_进发红包了",print_r($money,true));
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);
        Kohana::$log->add("qwt_rwb_mch_id",print_r($config['mchid'],true));
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        Kohana::$log->add("qwt_rwb_mch",print_r($mch_billno,true));

        $data["nonce_str"] = $this->wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $config['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        // $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        // $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        Kohana::$log->add("qwt_rwb_data1",print_r($data,true));

        $data["sign"] = strtoupper(md5($this->wx->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        Kohana::$log->add("qwt_rwb_data",print_r($data['sign'],true));
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $this->wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 30, array(), $bid);//支付安全验证函数（核心函数）
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        return $result;//hash数组
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        Kohana::$log->add("qwt_rwb_进curl",'111');
        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        $rootca_file=DOCROOT."qwt/tmp/$bid/rootca.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();
        $file_rootca = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_rootca')->find();

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

        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
        }

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
            return false;
        }

    }
}


