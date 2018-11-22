<?php defined('SYSPATH') or die('No direct script access.');
Class rwb {
    public $methodVersion='3.0.0';
    public $wx;
    public $config;
    public $bid;
    public $txtReply;
    public $yzaccess_token;
    public $client;
    public $scorename;
    public function __construct($bid,$wx,$openid,$qrcode){
        Kohana::$log->add('qwtrwbarea1',$bid);
        Kohana::$log->add('qwtrwbarea1',$openid);
        $sname = ORM::factory('qwt_rwbcfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        // 扫码后判断用户地理位置
        $this->wx=$wx;
        $this->config=$config=ORM::factory('qwt_rwbcfg')->getCfg($bid,1);
        $this->bid=$bid;
        $model_q=$qrcode;
        $yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $config=ORM::factory('qwt_rwbcfg')->getCfg($bid,1);
        $count = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
        $position = 0;
        $qr_user=ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $u_location = $qr_user->area;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
            Kohana::$log->add("qwtrwb:location1:$bid:$openid:", $p_location[$i]);
            Kohana::$log->add("qwtrwb:location2:$bid:$openid:", $u_location);
            $pos[$i] = @strpos($u_location, $p_location[$i]);
            if ($pos[$i]!==false) {
                $position++;
            }
        }
        $status = ORM::factory('qwt_rwbcfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
        $rwbqrcode=ORM::factory('qwt_rwbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $rwbqrcode->joinarea=0;
        $rwbqrcode->save();
        if(($position >0 && $status=='1')||$status=='0'||!$status){
            $fuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=',$rwbqrcode->fopenid)->find();//上一级
            $mgtpl=$config['mgtpl'];
            Kohana::$log->add("mgtpl", print_r($mgtpl, true));
            $tid=ORM::factory('qwt_rwbtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find()->id;
            $last_num=ORM::factory('qwt_rwbrecord')->where('bid','=',$bid)->where('fqid','=',$fuser->id)->where('tid','=',$tid)->count_all();
            if($tid){
                Kohana::$log->add("fuid", print_r($fuser->id, true));
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
                $keyword2='暂时没有有效的任务哦，请继续关系我们的任务信息，么么哒。';
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
            $fuserinfo = $wx->getUserInfo($fuser->openid);
            if ($fuserinfo['subscribe'] == 0) {
                if ($bid == $debug_bid) Kohana::$log->add("qwtrwb:$bid:FuserInfo", print_r($fuserinfo, true));
                $fuser->subscribe = 0;
                $fuser->save();
            }
            $ffuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find(); 
            $replySet = $config['text_goal3'];
            if(!$replySet){
                $replySet="恭喜您成为了「%s」的支持者";
            }
            $this->txtReply = sprintf($replySet,$fuser->nickname);
            Kohana::$log->add('qwtrwbtext2',$this->txtReply);   
        }else{
            $msg2['touser'] = $rwbqrcode->fopenid;
            $msg2['msgtype'] = 'text';
            $msg2['text']['content'] = "不好意思，您的朋友{$rwbqrcode->nickname}不在本次活动的参与地区，不要灰心哦，请继续关注我们的公众号，有更多惊喜等着你呢！";
            $wx->sendCustomMessage($msg2);
            $rwbqrcode->fopenid='';
            $rwbqrcode->save();
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $config['reply'];
            $wx->sendCustomMessage($msg);
        }
    }
    public function sendShiwu($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
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
        // $method = 'youzan.users.weixin.follower.tags.add';
        // $params = [
        //     'tags'=> $hello[0],
        //     'weixin_openid'=>$openid,
        // ];
        // $aa=$this->client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add("qwt_rwbresult", print_r($aa,true));
        if($finish==1){
            $url=$_SERVER['HTTP_HOST'].'/qwtrwb/shiwu?bid='.$bid.'&qid='.$qid.'&kid='.$kid.'&iid='.$iid.'&tid='.$tid;
            $text_goal2=$this->config['text_goal2'];
            $text_goal2s=sprintf($text_goal2,$task_name);
            $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
            $keyword1=$task_name;
            $keyword2="您的全部任务已完成\\n{$text}";
            $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }else{
            $url=$_SERVER['HTTP_HOST'].'/qwtrwb/shiwu?bid='.$bid.'&qid='.$qid.'&kid='.$kid.'&iid='.$iid.'&tid='.$tid;
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
        Kohana::$log->add('qwtrwbtext1',$this->txtReply);
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
