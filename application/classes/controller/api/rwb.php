<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_Rwb extends Controller_API {
    var $token = 'renwubao2015';
    var $FromUserName;
    var $Keyword;
    var $config;
    var $bid;
    var $access_token;
    var $baseurl = 'http://jfb.smfyun.com/rwb/';
    var $cdnurl = 'http://cdn.jfb.smfyun.com/rwb/';
    var $cdnurl2 = 'http://jfb.dev.smfyun.com/rwb/';
    var $scorename;
    var $we;
    var $client;
    var $methodVersion='3.0.0';
    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "rwb";
        if (!is_numeric($bid)) $bid = ORM::factory('rwb_login')->where('user', '=', $bid)->find()->id;
        $this->config=$config = ORM::factory('rwb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by bole");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0)
    {
        $this->baseurl = 'http://'. $_SERVER['HTTP_HOST'] .'/rwb/';
        set_time_limit(15);
        Database::$default = "rwb";
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $mem = Cache::instance('memcache');
        $debug_bid = 1350;
        $biz = ORM::factory('rwb_login')->where('user', '=', $bname)->find();
        $this->bid = $bid=$biz->id;
        $this->config=$config = ORM::factory('rwb_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;
        if ($debug) print_r($config);
        if (!$config['appid'] || !$config['appsecret'] || !$config['scene_id']) die('Not Config!');
        //if ($bid == $debug_bid) Kohana::$log->add('weixin2:config', print_r($config, true));
        if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
            $txtReply = '您的任务宝插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';
            $die =1;
        }

        $this->we=$we = new Wechat($config);
        $we->getRev();
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
        $sname = ORM::factory('rwb_cfg')->where('bid','=',$bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '人气值';
        }
        $fromUsername = $we->getRevFrom();
        $toUsername = $we->getRevTo();
        $this->Keyword = $we->getRevContent();
        $openid = $this->FromUserName = $fromUsername;
        $userinfo = $we->getUserInfo($openid);
        Kohana::$log->add("rwb:keyword:$bname", print_r($this->Keyword,true));
        Kohana::$log->add("rwb:fromUsername:$bname", print_r($fromUsername,true));
        Kohana::$log->add("rwb:toUsername:$bname", print_r($toUsername,true));
        if($die == 1){
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = '您的任务宝插件已过期！在有赞后台-概况-微信-插件中心，关闭对应的插件即可取消本提示；如需继续使用，请联系第三方续费。';
            $we->sendCustomMessage($msg);
            exit;
        }
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
        Kohana::$log->add("rwb:Eventall:$bname", print_r($we->getRevEvent(),true));
        Kohana::$log->add("rwb:Event:$bname", print_r($Event,true));
        Kohana::$log->add("rwb:Ticket:$bname", print_r($Ticket,true));
        Kohana::$log->add("rwb:EventKey:$bname", print_r($EventKey,true));
        $model_q = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        if($access_token){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        Kohana::$log->add("weixin2:44444", '44444');

        //扫码事件 || 扫码关注
        $scene_id = $config['scene_id'];

        if ($userinfo && ($Event == 'SCAN' && $EventKey == $scene_id) || ($we->getRevSceneId() == $scene_id)) {
            //新用户
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

            $fuser = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
            if($fuser->fopenid){
                $ffopenid=$fuser->fopenid;
            }//互扫bug
            $fopenidFromQrcode = $fopenid = $fuser->openid;
            //如果 当前用户有效 && 当前用户没有上级 && 来源二维码有效 && 来源用户 != 当前用户&&上上级!=当前用户&&当前用户未锁定&&上级未锁定
            if ($model_q->id && !$model_q->fopenid && $fopenid && $fopenid != $openid && $ffopenid!=$openid&&$model_q->lock!=1&&$fuser->lock!=1) {
                //首次关注积分
                $model_q = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                //任务宝
                $task =ORM::factory('rwb_task')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
                Kohana::$log->add($bid."rwbtid", print_r($task->id,true));
                if($task->id){
                    $record=ORM::factory('rwb_record')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$model_q->id)->find();

                    if(!$record->fqid){
                        $record->bid=$bid;
                        $record->tid=$task->id;
                        $record->qid=$model_q->id;
                        $record->fqid=$fuser->id;
                        $record->save();
                    }
                    $record2=ORM::factory('rwb_record')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$fuser->id)->find();
                    Kohana::$log->add("record2", print_r($record2->id,true));
                    if(!$record2->id){
                        $record2->bid=$bid;
                        $record2->tid=$task->id;
                        $record2->qid=$fuser->id;
                        $record2->save();
                    }

                }
                $tid = $task->id;
                $last_num=ORM::factory('rwb_record')->where('bid','=',$bid)->where('fqid','=',$fuser->id)->where('tid','=',$tid)->count_all();
                //$model_q->scores->scoreIn($model_q, 1, $config['goal0']);
                //先保存关系
                if ($model_q->id) {
                    //处女？
                    $chunv = 1;

                    $model_q->fopenid = $fopenidFromQrcode;
                    $model_q->save();


                    $fuser = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('ticket', '=', $Ticket)->find();//上一级
                    Kohana::$log->add($bid."rwbtid2", print_r($tid,true));
                    //分发奖品
                    $mgtpl=$this->config['mgtpl'];
                    if($tid){
                        $num =ORM::factory('rwb_record')->where('bid','=',$bid)->where('tid','=',$tid)->where('fqid','=',$fuser->id)->count_all();
                        $sku_all=ORM::factory('rwb_sku')->where('bid','=',$bid)->where('tid','=',$tid)->count_all();
                        $skus = ORM::factory('rwb_sku')->where('bid','=',$bid)->where('tid','=',$tid)->order_by('num', 'ASC')->find_all();
                        $sql = DB::query(Database::SELECT,"SELECT * from rwb_skus where `bid` = $bid and `tid` = $tid");
                        $sku_nests =$sql->execute()->as_array();
                        Kohana::$log->add($bid."sku_nests", print_r($sku_nests, true));
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
                            Kohana::$log->add($bid."item_name", print_r($item_name, true));
                            if($alltime!=$sku_all){
                                $sku_nest = $sku_nests[$alltime]['num'];
                                $item_next=ORM::factory('rwb_item')->where('id','=',$sku_nests[$alltime]['iid'])->find()->km_content;
                                Kohana::$log->add("item_next", print_r($item_next, true));
                            }
                            Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                            Kohana::$log->add("sku_all", print_r($sku_all, true));
                            Kohana::$log->add($bid."alltime", print_r($alltime, true));
                            $ordernum=ORM::factory('rwb_order')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                            Kohana::$log->add($bid."num", print_r($num, true));
                            Kohana::$log->add($bid."sku_num", print_r($sku_num, true));
                            if($num>=$sku->num){
                                $flag=1;
                                $order=ORM::factory('rwb_order')->where('bid','=',$bid)->where('tid','=',$tid)->where('kid','=',$sku->id)->where('qid','=',$fuser->id)->find();
                                if(!$order->id){
                                    $flag=1;
                                    $item=ORM::factory('rwb_item')->where('id','=',$sku->iid)->find();
                                    $ordernum=ORM::factory('rwb_order')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                                    if($ordernum<$sku->stock){
                                        if($alltime==$sku_all){
                                            $finish=1;
                                        }else{
                                            $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                            $ordernum_next=ORM::factory('rwb_order')->where('bid','=',$bid)->where('kid','=',$sku_nests[$alltime]['id'])->where('state','=',1)->count_all();
                                            $stock_next=$sku_nests[$alltime]['stock']-$ordernum_next;
                                            Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                                        }
                                        if($item->key=='hongbao'){
                                            $this->sendHongbao($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }elseif ($item->key=='gift') {
                                            $this->sendGift($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }elseif ($item->key=='coupon') {
                                            $this->sendCoupon($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }elseif ($item->key=='kmi') {
                                            $this->sendKmi($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }elseif ($item->key=='yzcoupon') {
                                            $this->sendYzcoupon($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }elseif ($item->key=='yhm') {
                                            $this->sendYhm($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }
                                    }else{
                                        $order=ORM::factory('rwb_order')->where('bid','=',$bid)->where('iid','=',$item->id)->where('qid','=',$fuser->id)->where('tid','=',$tid)->where('kid','=',$sku->id)->find();
                                        if(!$order->id){
                                            $order->bid=$bid;
                                            $order->tid=$tid;
                                            $order->qid=$fuser->id;
                                            $order->iid=$item->id;
                                            $order->kid=$sku->id;
                                            $order->name=$fuser->nickname;
                                            $order->task_name=ORM::factory('rwb_task')->where('id','=',$tid)->find()->name;
                                            $order->item_name=$item->km_content;
                                            $order->state=0;
                                            $order->log='库存不足';
                                            $order->save();
                                        }
                                        if($sku_nest!=0){
                                            $text_goal=$this->config['text_goal'];
                                            $text_goals=sprintf($text_goal,$model_q->nickname);
                                            $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                            $keyword=$text_goals."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                                            $keyword1=$task->name;
                                            $keyword2="本级奖品已被领完，继续加油，么么哒。";
                                            $this->sendTemplateMessage1($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                                            break;
                                        }else{
                                            $text_goal2=$this->config['text_goal2'];
                                            $text_goal2s=sprintf($text_goal2,$task->name);
                                            $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                            $keyword=$model_q->nickname.'成为了你的支持者'.$text_goal2s;
                                            $keyword1=$task->name;
                                            $keyword2='本级奖品已被领完,继续加油，么么哒。';
                                            $this->sendTemplateMessage1($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
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
                        $this->sendTemplateMessage1($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                    }
                    //积分话术
                    if(!$num){
                        $num=0;
                    }
                    Kohana::$log->add($bid, '1111');
                    Kohana::$log->add($bid."flag", print_r($flag, true));
                    if($flag==3){
                        $text_goal=$this->config['text_goal'];
                        $text_goals=sprintf($text_goal,$model_q->nickname);
                        $need_num=$sku_num-$last_num;
                        $left_num=$sku_stock-$ordernum;
                        $keyword=$text_goals."\\n您还需要".$need_num."个支持者就可以获得".$item_name;
                        $keyword1=$task->name;
                        //$keyword2="\\n你试试";
                        $keyword2="任务目标:{$sku_num}人气值\\n已经完成:{$last_num}人气值\\n还需人气值:{$need_num}个\\n奖品名称:{$item_name}\\n剩余数量:{$left_num}";
                        //$openid=$fuser->openid;
                        Kohana::$log->add("openid", print_r($fuser->openid, true));
                        Kohana::$log->add($bid."mgtpl", print_r($mgtpl, true));
                        Kohana::$log->add($bid."keyword2", print_r($keyword2, true));
                        $lll=$this->sendTemplateMessage1($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
                        Kohana::$log->add($bid."rwbkeyword2", print_r($lll, true));
                    }
                    if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:we_result_fuser", var_export($we_result, true).$we->errCode.':'.$we->errMsg);

                    //更新上一级用户的 userinfo
                    $fuserinfo = $we->getUserInfo($fuser->openid);
                    if ($fuserinfo['subscribe'] == 0) {
                        if ($bid == $debug_bid) Kohana::$log->add("weixin2:$bid:FuserInfo", print_r($fuserinfo, true));
                        $fuser->values($fuserinfo);
                        $fuser->save();
                    }

                    //上一级推荐人 积分增加处理
                    //珂心如意
                    $ffuser = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenidFromQrcode)->find();

                }
            }

            //已经有上级就直接取来
            else {
                //$fuser = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $model_q->fopenid)->find();
                $fopenid = $fuser->openid;
            }

            //扫码后默认推送消息
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';

            //remove emoji
            $nickname = $fuser->nickname;
            //$nickname = preg_replace('/[\xf0-\xf7].{3}/', '', $fuser->nickname);
            $nickname = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $nickname);

            $replySet = $config['text_goal3'];
            $replySet2 = $config['text_goal4'];
            if(!$replySet){
                $replySet="恭喜您成为了「%s」的支持者";
            }
            if(!$replySet2){
                $replySet2="您已经是「%s」的支持者了，不用再扫了哦。";
            }
            if ($model_q->id && $model_q->fopenid && $fopenid){
                $fnickname = ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$model_q->fopenid)->find()->nickname;
                $msg['text']['content'] = str_replace('\n', "\n",sprintf($replySet2,$fnickname));
            }
            if ($chunv) $msg['text']['content'] = str_replace('\n', "\n",sprintf($replySet,$nickname));

            // 2016-1-7修改扫描自己上线不提示bug 上面注释为先前版本 by 1nnovator
            //自己扫自己无上线的不发消息

            if ($model_q->fopenid) $we_result = $we->sendCustomMessage($msg);
            //扫码后推送网址
            if ($config['text_follow_url']&&$fuser->lock!=1) {
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '活动说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                $we_result = $we->sendCustomMessage($msg);
            }
        }
        $model_q = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        //菜单点击事件
        Kohana::$log->add("weixin2:5555", '5555');
        Kohana::$log->add("keyword", $this->Keyword);
        if(strpos($this->Keyword,$config['keyword'])!==false){
            $haibao = 1;
        }
        if ($userinfo && $Event == 'CLICK' || $haibao==1) {

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
            $count = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $position = 0;
            $u_location = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->area;
            for ($i=1; $i <=$count ; $i++) {
                $pro[$i] = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
                $city[$i] = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
                $dis[$i] = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
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

            $status = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'status')->find()->value;
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
            else if ($EventKey == $config['key_qrcode'] || $chunv || $EventKey == '生成海报'||$haibao==1) {
                $task1 =ORM::factory('rwb_task')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
                if($task1->id){
                    $aaaa='有有效任务';
                }else{
                    $task2 =ORM::factory('rwb_task')->where('bid','=',$bid)->where('endtime','>',time())->find();
                    if($task2->id&&$task2->begintime!=$task2->endtime){
                        $msg['text']['content'] = "活动尚未开始";
                        $we->sendCustomMessage($msg);
                    }elseif(ORM::factory('rwb_task')->where('bid','=',$bid)->find()->id){
                        $msg['text']['content'] = "活动已经结束";
                        $we->sendCustomMessage($msg);
                    }else{
                        $msg['text']['content'] = "活动尚未开始";
                        $we->sendCustomMessage($msg);
                    }
                    die();
                }
                $ticket_lifetime = 3600*24*7;
                //自定义过期时间      不可删

                //第二种 方案      不可删
                if ($config['needpay']&&$access_token) {
                    require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                    $qr_id = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->needpost;
                    if($qr_id!=0&&$qr_id!=1){
                        //Kohana::$log->add('qr_id:', print_r($qr_id, true));//写入日志，可以删除
                        $this->client = new YZTokenClient($this->access_token);
                        $method1 = 'youzan.trades.qr.get';
                        $params = [
                            'qr_id'=>$qr_id,
                            'status'=>'TRADE_RECEIVED',
                        ];
                        $results=$this->client->post($method, $this->methodVersion, $params, $files);
                        if($results['response']['qr_trades']) {
                            $qr = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                            $qr->needpost=1;
                            $qr->save();
                        }
                        //Kohana::$log->add('s1:', print_r($results, true));//写入日志，可以删除
                    }
                    $needpost = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->needpost;
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
                    $msg['text']['content'] = str_replace('\n', "\n", $config['text_send']);

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
                    Kohana::$log->add("rwb:$bid:scene_id:$model_q->openid", $config['scene_id']);
                    $model_q->lastupdate = $time;

                    $msg['text']['content'] = str_replace('\n', "\n", $config['text_send']);

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
                $imgtpl = DOCROOT."rwb/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';

                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
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
                $tplhead = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                $default_head_file = DOCROOT."rwb/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'rwb:tpl:'.$openid.':'.$tpl->lastupdate;
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
                        Kohana::$log->add("rwb:$bid:head4:", $remote_head);
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
                $msg['text']['content'] = str_replace('\n', "\n", $config['text_send']). "\n\n" .$txtReply2;

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
            else if ($EventKey == $config['key_score'] || $EventKey == '任务完成情况') {
                if (!$model_q->openid) {
                    $msg['text']['content'] = '请先点击生成海报';
                    $we->sendCustomMessage($msg);
                    exit;
                }
                $tid=ORM::factory('rwb_task')->where('bid','=',$bid)->where('endtime','>',time())->find()->id;
                $num1 =ORM::factory('rwb_record')->where('bid','=',$bid)->where('tid','=',$tid)->where('fqid','=',$model_q->id)->count_all();
                Kohana::$log->add("rwb:score", '11111');
                $url = $this->baseurl.'index/'. $bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($model_q->openid);
                Kohana::$log->add("rwb:url", print_r($url,true));
                $msg['msgtype'] = 'news';
                $news_pic_file = 'news_score.'. $bid .'.jpg';
                if (!file_exists(DOCROOT."rwb/$news_pic_file")) $news_pic_file = 'news_score.png';
                $news_pic = $this->cdnurl.$news_pic_file;

                $newsReply[0]['Title'] = $msg['news']['articles'][0]['title'] = $this->scorename.'明细';
                $newsReply[0]['Description'] = $msg['news']['articles'][0]['description'] = '您的'. $this->scorename .'为 '.$num1.'，点击查看明细...';
                $newsReply[0]['Url'] = $msg['news']['articles'][0]['url'] = $url;
                $newsReply[0]['PicUrl'] = $msg['news']['articles'][0]['picurl'] = $news_pic;

                // $we_result = $we->sendCustomMessage($msg);
            }
            else if ($EventKey) {
                $msg['msgtype'] = 'text';
                $txtReply = $msg['text']['content'] = '请将下面的 KEY 填写到「任务宝」商户后台完成配置：'.$EventKey;

                //用户少的时候才回复 debug
                // if (ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->count_all() < 10)
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
                $replyfront = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyfront')->find()->value;
                $replyend = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'replyend')->find()->value;
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
    public function sendHongbao($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        Kohana::$log->add("发红包iid", print_r($iid,true));
        Kohana::$log->add("发红包qid", print_r($qid,true));
        Kohana::$log->add("发红包tid", print_r($tid,true));
        Kohana::$log->add("发红包kid", print_r($kid,true));
        $items=ORM::factory('rwb_item')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('rwb_qrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('rwb_task')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('rwb_sku')->where('id','=',$kid)->find();
        $text=$skus->text;
        Kohana::$log->add("bid", print_r($this->bid,true));
        $tempname=ORM::factory("rwb_login")->where('id','=',$this->bid)->find()->user;
        Kohana::$log->add("tempname", print_r($tempname,true));
        $result = $this->hongbao($this->config, $openid, '', $tempname, $value);
        Kohana::$log->add("result", print_r($result,true));
        $order=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
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
                $this->sendTemplateMessage1($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成,恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                $this->sendTemplateMessage1($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }
        }else{
            $order->state=0;
            $order->log= $result['return_msg'];
            $order->save();
            $this->sendCustomMessage1($openid,$result['return_msg']);
        }
    }
    public function sendKmi($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
            $this->we = new Wechat($config);
        }
        if(!$this->access_token){
            $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
            $access_token=$this->access_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->access_token);
        }

        $items=ORM::factory('rwb_item')->where('id','=',$iid)->find();
        $value=$items->value;
        $hello = explode('&',$value);
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('rwb_qrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('rwb_task')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('rwb_sku')->where('id','=',$kid)->find();
        $text=$skus->text;
        Kohana::$log->add("tag", print_r($hello[0],true));
        $method = 'youzan.users.weixin.follower.tags.add';
        $params = [
            'tags'=> $hello[0],
            'weixin_openid'=>$openid,
        ];
        $aa=$this->client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add("rwb_result", print_r($aa,true));
        if($finish==1){
                $url=$hello[1];
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
                $keyword1=$task_name;
                $keyword2="您的全部任务已完成\\n{$text}";
                $result=$this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }else{
                $url=$hello[1];
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成,恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                $result=$this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }
        Kohana::$log->add("rwb_tpl_{$this->bid}", print_r($result,true));
        $order=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
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
            $this->sendCustomMessage1($openid,$result['errmsg']);

        }
        Kohana::$log->add("发特权", '1111');
    }
    public function sendCoupon($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
            $this->we = new Wechat($config);
        }
         if(!$this->access_token){
            $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
            $access_token=$this->access_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->access_token);
        }

        $items=ORM::factory('rwb_item')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('rwb_qrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('rwb_task')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('rwb_sku')->where('id','=',$kid)->find();
        $text=$skus->text;
        if($finish==1){
                $text_goal2=$this->config['text_goal2'];
                $text_goal2s=sprintf($text_goal2,$task_name);
                $url = $_SERVER ['HTTP_HOST'].'/rwb/ticket/'.$value.'/'.$this->bid;
                $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
                $keyword1=$task_name;
                $keyword2="您的全部任务已完成\\n{$text}";
                $this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $url = $_SERVER ['HTTP_HOST'].'/rwb/ticket/'.$value.'/'.$this->bid;
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                $this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
        }
        $order=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
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
            $this->sendCustomMessage1($openid,$result['errmsg']);
        }
        Kohana::$log->add("微信卡劵", '1111');
    }
    public function sendYzcoupon($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
            $this->we = new Wechat($config);
        }
        if(!$this->access_token){
            $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
            $access_token=$this->access_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->access_token);
        }
        $items=ORM::factory('rwb_item')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('rwb_qrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('rwb_task')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('rwb_sku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $method = 'youzan.ump.coupon.take';
        $params = [
            'coupon_group_id'=>$value,
            'weixin_openid'=>$openid,
         ];
        $results = $this->client->post($method, $this->methodVersion, $params, $files);
        $order=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
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
                $this->sendTemplateMessage1($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n{$item_next}剩余数量:{$stock_next}\\n{$text}";
                $this->sendTemplateMessage1($openid,$mgtpl,'',$keyword,$keyword1,$keyword2);
            }
        }else if($results['error_response']){
            $order->state=0;
            $order->log=$results['error_response']['code'].$results['error_response']['msg'];
            $order->save();
            $this->sendCustomMessage1($openid,$results['error_response']['code'].$results['error_response']['msg']);
        }
        Kohana::$log->add("有赞优惠劵",'1111' );
    }
    public function sendYhm($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        Kohana::$log->add("km", '进入卡密了');
        $mgtpl=$this->config['mgtpl'];
        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
            $this->we = new Wechat($config);
        }
        $items=ORM::factory('rwb_item')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $item_text=$items->km_text;
        $qrcodes=ORM::factory('rwb_qrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('rwb_task')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('rwb_sku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $order=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
        if(!$order->id){
            $order->bid=$this->bid;
            $order->tid=$tid;
            $order->qid=$qid;
            $order->iid=$iid;
            $order->kid=$kid;
            $order->name=$nickname1;
            $order->task_name=$task_name;
            $order->item_name=$item_name;
        }
        Kohana::$log->add("bid", print_r($this->bid,true));
        Kohana::$log->add("value", print_r($items->value,true));
        $count=ORM::factory('rwb_km')->where('bid','=',$this->bid)->where('live','=',1)->where('starttime','=',$items->value)->count_all();
         Kohana::$log->add("count", printf($count,true));
        if($count!=0){
            $kmikm=ORM::factory('rwb_km')->where('bid','=',$this->bid)->where('live','=',1)->where('starttime','=',$items->value)->find();
            $url='http://'.$_SERVER['HTTP_HOST'].'/rwba/kmpass/'.$kmikm->id.'/'.$iid;
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
                $result=$this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name.','.$msgs."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1=$task_name;
                $keyword2="任务目标:{$sku_nest}\\n{$item_next}剩余数量:{$stock_next}\\n{$text}";
                $result=$this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
            }
            if($result['errmsg']=='ok'){
                $order->state=1;
                $order->save();
                $km=ORM::factory('rwb_km')->where('id','=',$id)->find();
                $km->live=0;
                $km->save();
            }else{
                $order->state=0;
                $order->log=$result['errmsg'];
                $order->save();
                $this->sendCustomMessage1($openid,$result['errmsg']);
            }
        }else{
            $order->state=0;
            $order->log='卡密库存不足';
            $order->save();
            $keyword='卡密库存不足';
            $keyword1=$task_name;
            $this->sendTemplateMessage1($openid,$mgtpl,'',$keyword,$keyword1,'');
        }
    }
    public function sendGift($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
            $this->we = new Wechat($config);
        }
         if(!$this->access_token){
            $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
            $access_token=$this->access_token;
        }
        if(!$this->client){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->client = new YZTokenClient($this->access_token);
        }

        $items=ORM::factory('rwb_item')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('rwb_qrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('rwb_task')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('rwb_sku')->where('id','=',$kid)->find();
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

                $order=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
                if(!$order->id){
                    $order->bid=$this->bid;
                    $order->tid=$tid;
                    $order->qid=$qid;
                    $order->iid=$iid;
                    $order->kid=$kid;
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
                        $this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
                    }else{
                        $text_goal=$this->config['text_goal'];
                        $text_goals=sprintf($text_goal,$nickname);
                        $url = $results['response']['receive_address'];
                        $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name."\\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                        $keyword1=$task_name;
                        $keyword2="任务目标:{$sku_nest}\\n奖品名称:{$item_next}\\n剩余数量:{$stock_next}\\n{$text}";
                        $this->sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2);
                     }
                }else if($results['error_response']){
                    $order->state=0;
                    $log=$results['error_response']['code'].$results['error_response']['msg'];
                    $order->log=$log;
                    $order->save();
                    $this->sendCustomMessage1($openid,$log);
                }
            }
        }
        Kohana::$log->add("有赞赠品",'11111' );
    }
    private function hongbao($config, $openid, $we='', $bid=1, $money){
        //记录 用户 请求红包
        Kohana::$log->add("进发红包了",print_r($openid,true));
        Kohana::$log->add("进发红包了",print_r($bid,true));
        Kohana::$log->add("进发红包了",print_r($money,true));
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件
            $this->we = new Wechat($config);
        }
         Kohana::$log->add("mch_id",print_r($config['mchid'],true));
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
         Kohana::$log->add("mch",print_r($mch_billno,true));

        $data["nonce_str"] = $this->we->generateNonceStr(32);//随机字符串
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
        Kohana::$log->add("data1",print_r($data,true));

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        Kohana::$log->add("data",print_r($data['sign'],true));
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $this->we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);//支付安全验证函数（核心函数）
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
        Kohana::$log->add("进curl",'111');
        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."rwb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."rwb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."rwb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'rwb_file_cert')->find();
        $file_key = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'rwb_file_key')->find();
        $file_rootca = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'rwb_file_rootca')->find();

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
    //检查签名
    public function sendCustomMessage1($openid,$km_text){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $km_text;
        $result=$this->we->sendCustomMessage($msg);
        return $result;
    }
    public function sendTemplateMessage1($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2){
        $keyword=str_replace(' ','',$keyword);
        //$keyword=str_replace('\\','',$keyword);
        $keyword1=str_replace(' ','',$keyword1);
        //$keyword1=str_replace('\\','',$keyword1);
        $keyword2=str_replace(' ','',$keyword2);
        //$keyword2=str_replace('\\','',$keyword2);
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $mgtpl;
        $tplmsg['url']=urlencode($url);
        $tplmsg['data']['first']['value']=urlencode(str_replace('"','',$keyword));
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = urlencode(str_replace('"','',$keyword1));
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['remark']['value'] = urlencode(str_replace('"','',$keyword2));
        $tplmsg['data']['remark']['color'] = '#FF0000';
        Kohana::$log->add("$this->bid:tplmsg", print_r($tplmsg,true));
        Kohana::$log->add("$this->bid:tplmsg1", print_r(urldecode(json_encode($tplmsg)),true));
        $result=$this->we->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        Kohana::$log->add("$this->bid:tplmsg2",print_r($result,true));
        if($result['errmsg']!='ok'){
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $result['errmsg'];
            $this->we->sendCustomMessage($msg);
        }
        return $result;
    }
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
