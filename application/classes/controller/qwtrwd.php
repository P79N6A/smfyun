<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtrwd extends Controller_Base {
    public $template = 'weixin/smfyun/rwd/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $wx;
    public $uid;
    public $token = 'weidingbao';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    //public $access_token;
    public $cdnurl = 'http://cdn.jfb.smfyun.com/qwt/rwd/';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "qwt";
        parent::before();

        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'qrscan') return;
        if (Request::instance()->action == 'shiwu') return;
        if (Request::instance()->action == 'kmpass') return;

        $_SESSION =& Session::instance()->as_array();

        if (!$_GET['openid']) {
            if (!$_SESSION['qwtrwd']['bid']) {
                // Kohana::$log->add("session不存在:bid", $_SESSION['qwtrwd']['bid']);
                // Kohana::$log->add("session不存在:openid", $_SESSION['qwtrwd']['openid']);
                die('请重新点击验证或者点击菜单进入本页面哦~');
            }
            if (!$_SESSION['qwtrwd']['openid']) die('Access Deined..请重新点击相应菜单');
        }

        $this->config = $_SESSION['qwtrwd']['config'];
        $this->openid = $_SESSION['qwtrwd']['openid'];
        $this->bid = $_SESSION['qwtrwd']['bid'];
        $this->uid = $_SESSION['qwtrwd']['uid'];
        //$this->access_token = $_SESSION['qwtrwd']['access_token'];


        //积分同步 回调
        if ($_GET['debug']) print_r($_SESSION['qwtrwd']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }
    public function action_kmpass($id,$iid){
        $this->template = 'tpl/blank';
        self::before();
        $km_text =ORM::factory('qwt_rwditem')->where('id','=',$iid)->find()->km_text;
        $password1 = ORM::factory('qwt_rwdkm')->where('id','=',$id)->find()->password1;
        $password2 = ORM::factory('qwt_rwdkm')->where('id','=',$id)->find()->password2;
        $password3 = ORM::factory('qwt_rwdkm')->where('id','=',$id)->find()->password3;
        $km_text = str_replace("「%a」",$password1,$km_text);
        $password = $password1;
        if($password2){
            $km_text = str_replace("「%b」",$password2,$km_text);
            $password = $password.','.$password2;
            if($password3){
                $km_text = str_replace("「%c」",$password3,$km_text);
                $password = $password.','.$password3;
            }
        }
        // echo $km_text;
        $view = "weixin/rwb/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('km_text', $km_text);
    }

    public function action_shiwu($e='order'){
        $this->template = 'tpl/blank';
        self::before();
        $bid=$_GET['bid'];
        $qid=$_GET['qid'];
        $tid=$_GET['tid'];
        $kid=$_GET['kid'];
        $iid=$_GET['iid'];
        $item1=ORM::factory('qwt_rwditem')->where('id','=',$iid)->find();
        $order=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->where('iid','=',$iid)->find();
        $item['need_money']=($item1->need_money)/100;
        if(!$order->id) die('您的url有误');
        if($order->id&&$_POST){
            $receive_name=$_POST['data']['name'];
            $tel=$_POST['data']['tel'];
            $address=$_POST['s_province'].$_POST['s_city'].$_POST['s_dist'].$_POST['data']['address'];
            $order->receive_name=$receive_name;
            $order->tel=$tel;
            $order->address=$address;
            $order->pay_money=$item['need_money'];
            $order->save();
        }
        if($order->order_state>0||($order->pay_money==0&&$order->tel)){
            $result['status']=1;
            $neirong='';
            if($order->status==0){
                $result['neirong']='请耐心等待管理员发货';
            }else{
                $result['neirong']='您的奖品已发货，请注意查收';
            }
        }else{
            $result['status']=0;
        }
        $item['pic']='http://'.$_SERVER['HTTP_HOST'].'/qwtrwd/images/item/'.$item1->id.'v'.$item1->lastupdate.'.jpg';
        $item['km_content']=$item1->km_content;
        $item['id']=$item1->id;
        // var_dump($item);
        // exit();
        $view = "weixin/smfyun/rwd/gerenxinxi";

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
        //$order=
        $this->template->content = View::factory($view)
            ->bind('jsapi',$jsapi)
            ->bind('item',$item)
            ->bind('bid',$bid)
            ->bind('qid',$qid)
            ->bind('result', $result)
            ->bind('order', $order);
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->where('openid','!=','')->where('fuopenid','!=','')->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }

    //入口
    public function action_index($bid) {
        $config = ORM::factory('qwt_rwdcfg')->getCfg($bid,1);
        //$this->access_token=ORM::factory('qwt_rwdlogin')->where('id', '=', $bid)->find()->access_token;
        if (!$_GET['openid']) $_SESSION['qwtrwd'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);

            if ($_GET['cksum'] != md5($openid.date('Y-m-d'))) {
                $_SESSION['qwtrwd'] = NULL;
                die('Access Deined!请重新点击相应菜单');
            }
            $userobj = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            if($userobj->id){
                $userobj->ip = Request::$client_ip;
                $userobj->save();
            }

            $_SESSION['qwtrwd']['config'] = $config;
            $_SESSION['qwtrwd']['openid'] = $openid;
            $_SESSION['qwtrwd']['bid'] = $bid;
            $_SESSION['qwtrwd']['uid'] = $userobj->id;
            //$_SESSION['qwtrwd']['access_token'] = $this->access_token;
            // Kohana::$log->add("session:index:$bid:openid", $_SESSION['qwtrwd']['openid']);
            // Kohana::$log->add("session:index:$bid:bid", $_SESSION['qwtrwd']['bid']);
            if ($bid == 1) {
                // print_r($_SESSION);exit;
            }
            Request::instance()->redirect('/qwtrwd/'.$_GET['url']);
        }
    }
    // 获取服务号openid和进行跳转  生成海报的图文
    public function action_storefuop(){
        $bid = $this->bid;
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $ticket_lifetime = 3600*24*7;
        if($this->config['ticket_lifetime']) $ticket_lifetime=time()+3600*24*$this->config['ticket_lifetime'];
        $storekey = base64_encode($this->openid.'|'.$this->bid.'|'.$ticket_lifetime);
        $appid="wxd3a678cfeb03e3a3";//神码浮云技术部的
        $appsecret="661fb2647a804e14ded1f65fad682695";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        if(!$bid) Kohana::$log->add('qwtrwdbid:', 'storefuop');//写入日志，可以删除
        $this->wx=$wx = new Wxoauth($bid,'rwd',$this->appId,$options);
        //$we = new Wechat(array('appid'=>$appid, 'appsecret'=>$appsecret));
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';
        if (!$_GET['callback']) {
                $auth_url = $wx->getOauthRedirect($appid,$callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            } else {
                $token = $wx->getOauthAccessToken($appid,$appsecret);//依托服务号(appid appsecret)的 openid
                $fuopenid = $token['openid'];

                $_SESSION['qwtrwd']['config'] = $this->config;
                $_SESSION['qwtrwd']['openid'] = $this->openid;
                $_SESSION['qwtrwd']['bid'] = $this->bid;
                // Kohana::$log->add("session:$this->bid:openid", $_SESSION['qwtrwd']['openid']);
                // Kohana::$log->add("session:$this->bid:bid", $_SESSION['qwtrwd']['bid']);
                // $_SESSION['qwtrwd']['uid'] = $userobj->id;
            }
        $this->template = 'tpl/blank';
        self::before();
        $url = 'http://'.$_SERVER["HTTP_HOST"].'/smfyun/qwt_rwd/'.$bid;
        //echo 'openid2'.explode('|',base64_decode($storekey))[0];
        //echo 'bid'.explode('|',base64_decode($storekey))[1];
        $bid = explode('|',base64_decode($storekey))[1];
        $openid2 = explode('|',base64_decode($storekey))[0];
        $config = $this->config;
        //$we = new Wechat($config);

        if(!$fuopenid) die('消息不小心走丢啦，请重新点击菜单验证');
        $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();//根据服务号来查
        if($user2->fopenid&&!$user2->openid){//绑定 自己扫了码但是 未绑定 就点击生成海报
            $user2->fuopenid = $fuopenid;
            $userinfo=$wx->getUserInfo($openid2);//根据订阅号openid获取用户信息
            $user2->openid=$openid2;
            $user2->bid=$bid;
            $user2->ticket=base64_decode($storekey);
            $user2->nickname=$userinfo['nickname'];
            $user2->headimgurl=$userinfo['headimgurl'];
            $user2->subscribe=$userinfo['subscribe'];
            $user2->sex=$userinfo['sex'];
            $user2->subscribe_time=$userinfo['subscribe_time'];
            $user2->save();
            //验证改用户之前是否关注公众号
            $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid2)->find();
            $scan=ORM::factory('qwt_rwdscan')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();
            Kohana::$log->add("rwd{$bid}SCAN1",print_r($fuopenid, true));
            Kohana::$log->add("rwd{$bid}SCAN11",print_r($scan->id, true));
            if($subscribe->id&&$scan->id&&$subscribe->creattime<=$scan->time-60){
                Kohana::$log->add("wfb{$bid}SCAN111",print_r($subscribe->openid, true));//老用户
                $has_subscribe=1;
                $model_p = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                $model_p->old=1;
                $model_p->save();
                Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
            }else{//新用户
                $model_p = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                $model_p->old=0;
                $model_p->save();
                $has_subscribe=2;
                Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
            }
            Kohana::$log->add("wfb{$bid}SCAN",print_r(time(), true));
            //上级用户
            $fuser = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
            //给上级用户发消息  绑定成功或者失败
            $model_q = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
            $task =ORM::factory('qwt_rwdtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
            if($task->id&&$has_subscribe==2){
                $record=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$model_q->id)->find();

                if(!$record->id){
                    $record->bid=$bid;
                    $record->tid=$task->id;
                    $record->qid=$model_q->id;
                    $record->fqid=$fuser->id;
                    $record->save();
                }
                $record2=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$fuser->id)->find();
                Kohana::$log->add("record2", print_r($record2->id,true));
                if(!$record2->id){
                    $record2->bid=$bid;
                    $record2->tid=$task->id;
                    $record2->qid=$fuser->id;
                    $record2->save();
                }

            }
            $tid=ORM::factory('qwt_rwdtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find()->id;
            $last_num=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('fqid','=',$fuser->id)->where('tid','=',$tid)->count_all();

            if($has_subscribe==2){
                if($tid){
                    $num =ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$tid)->where('fqid','=',$fuser->id)->count_all();
                    $sku_all=ORM::factory('qwt_rwdsku')->where('bid','=',$bid)->where('tid','=',$tid)->count_all();
                    $skus = ORM::factory('qwt_rwdsku')->where('bid','=',$bid)->where('tid','=',$tid)->order_by('num', 'ASC')->find_all();
                    $sql = DB::query(Database::SELECT,"SELECT * from qwt_rwdskus where `bid` = $bid and `tid` = $tid");
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
                            $item_next=ORM::factory('qwt_rwditem')->where('id','=',$sku_nests[$alltime]['iid'])->find()->km_content;
                            Kohana::$log->add("item_next", print_r($item_next, true));
                        }
                        Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                        Kohana::$log->add("sku_all", print_r($sku_all, true));
                        Kohana::$log->add("alltime", print_r($alltime, true));
                        $ordernum=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                        Kohana::$log->add("num", print_r($num, true));
                        Kohana::$log->add("sku_num", print_r($sku_num, true));
                        if($num>=$sku->num){
                            $flag=1;
                            $order=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('tid','=',$tid)->where('kid','=',$sku->id)->where('qid','=',$fuser->id)->find();
                            if(!$order->id){
                                $flag=1;
                                $item=ORM::factory('qwt_rwditem')->where('id','=',$sku->iid)->find();
                                $ordernum=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                                //将库存存入memcache
                                $m = new Memcached();
                                $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
                                $keyname="qrwd_ordernum:{$bid}:{$sku->id}";
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
                                    Kohana::$log->add("qwt_rwd:$bid:stock", print_r($sku->stock, true));
                                    if($alltime==$sku_all){
                                        $finish=1;
                                    }else{
                                        $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                        $ordernum_next=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('kid','=',$sku_nests[$alltime]['id'])->where('state','=',1)->count_all();
                                        $stock_next=$sku_nests[$alltime]['stock']-$ordernum_next;
                                        Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                                    }
                                    if ($item->key=='yhm') {
                                        $this->sendKmi($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                        break;
                                    }elseif ($item->key=='shiwu') {
                                        $this->sendShiwu($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                        break;
                                    }
                                }else{
                                    $order=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('iid','=',$item->id)->where('qid','=',$fuser->id)->where('tid','=',$tid)->where('kid','=',$sku->id)->find();
                                    if(!$order->id){
                                        $order->bid=$bid;
                                        $order->tid=$tid;
                                        $order->qid=$fuser->id;
                                        $order->iid=$item->id;
                                        $order->kid=$sku->id;
                                        $order->status=1;
                                        $order->name=$fuser->nickname;
                                        $order->task_name=ORM::factory('qwt_rwdtask')->where('id','=',$tid)->find()->name;
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
                                        $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
                                        break;
                                    }else{
                                        $text_goal2=$config['text_goal2'];
                                        $text_goal2s=sprintf($text_goal2,$task->name);
                                        $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                        $keyword=$model_q->nickname.'成为了你的支持者'.$text_goal2s;
                                        $keyword1=$task->name;
                                        $keyword2='本级奖品已被领完,继续加油，么么哒。';
                                        $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
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
                    $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
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
                    $keyword1='任务名称:'.$task->name;
                    //$keyword2="\\n你试试";
                    $keyword2="任务目标:{$sku_num}\n\n已经完成:{$last_num}\n\n还需人数:{$need_num}\n\n{$item_name}剩余数量:{$left_num}";
                    //$openid=$fuser->openid;
                    Kohana::$log->add("openid", print_r($fuser->openid, true));
                    Kohana::$log->add("mgtpl", print_r($mgtpl, true));
                    Kohana::$log->add("keyword", print_r($keyword, true));
                    Kohana::$log->add("keyword1", print_r($keyword1, true));
                    Kohana::$log->add("keyword2", print_r($keyword2, true));
                    $lll=$this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
                    Kohana::$log->add("lll$bid", print_r($lll, true));
                }
                // $fuser->scores->scoreIn($fuser, 2, $config['goal']);
                // $msg['touser'] = $fuser->openid;//fuser 上级用户
                // $msg['msgtype'] = 'text';
                // $msg['text']['content'] = sprintf($config['text_goal'],$user2->nickname).'您的当前'.$config['score'].'为：';
                // $wx->sendCustomMessage($msg);

            }else{
                $msg['touser'] = $fuser->openid;//fuser 上级用户
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '您的朋友'.$user2->nickname.'已经关注过公众号了，不能再成为您的支持者了';
                $wx->sendCustomMessage($msg);
            }
            //给自己发消息  绑定成功或者失败
            $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();//根据服务号来查
            if($has_subscribe==2){
                // $user2->scores->scoreIn($user2, 1, $config['goal0']);
                $msg['touser'] = $user2->openid;//user2 当前用户
                $msg['msgtype'] = 'text';
                //$msg['text']['content'] = sprintf($config['text_goal3'],$fuser->nickname);
                $msg['text']['content'] = str_replace('%s', $fuser->nickname, $config['text_goal3']);
                $wx->sendCustomMessage($msg);
                $bindcon = '恭喜您成为'.$fuser->nickname.'的支持者~，';
            }else{
                $msg['touser'] = $user2->openid;//user2 当前用户
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '您已经关注过公众号了，不能再成为'.$fuser->nickname.'的支持者了哦。';
                $wx->sendCustomMessage($msg);

                $bindcon='您已经关注过公众号了，不能再成为'.$fuser->nickname.'的支持者了哦。';
            }
            // $fuuser2->delete();
            if($config['text_follow_url']){
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '活动说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$user2->openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                $we_result = $wx->sendCustomMessage($msg);
            }
        }else if(!$user2->fuopenid){//自己关注
            $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();//根据订阅号来查
            $userinfo=$wx->getUserInfo($openid2);//根据订阅号openid获取用户信息
            $user2->fuopenid = $fuopenid;
            $user2->openid = $openid2;
            $user2->bid = $bid;
            $user2->ticket=base64_decode($storekey);
            $user2->nickname=$userinfo['nickname'];
            $user2->headimgurl=$userinfo['headimgurl'];
            $user2->subscribe=$userinfo['subscribe'];
            $user2->sex=$userinfo['sex'];
            $user2->subscribe_time=$userinfo['subscribe_time'];
            $user2->save();
            if($config['text_follow_url']){
                $msg['touser'] = $user2->openid;//user2 当前用户
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '活动说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$user2->openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                $wx->sendCustomMessage($msg);
            }
        }else if($user2->openid){//多次点击验证海报
            //$bindcon = '点击【生成海报】 快让更多小伙伴 加入吧!';
        }
        $buser = ORM::factory('qwt_login')->where('id', '=', $bid)->find();
        $post_data = array(
          'openid' =>$openid2
        );
        // $url = $url.'/'.$buser->appid;
        //Kohana::$log->add('rwd:$bid', print_r($post_data, true));//写入日志，可以删除
        $res = $this->request_post($url, $post_data);
        $bindcon = $bindcon.'<br>海报已生成，请返回对话框查收~<br>';
        $view = "weixin/smfyun/rwd/storefuop";
        $title = '生成海报';
        $this->template->content = View::factory($view)->bind('subhref', $config['subhref'])->bind('over', $over)->bind('bindcon', $bindcon)->bind('title', $title);
    }
    //扫码进入
    public function action_qrscan($ticket=1){
        $this->template = 'tpl/blank';
        self::before();

        //require_once Kohana::find_file("vendor/kdt/lib","KdtRedirectApiClient");
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $appid="wxd3a678cfeb03e3a3";//神码浮云技术部的
        $appsecret="661fb2647a804e14ded1f65fad682695";

        $openid1 = explode('|',$ticket)[0];//上级订阅号openid

        $bid = explode('|',$ticket)[1];

        $login = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if(!$bid) Kohana::$log->add('qwtrwdbid:', 'qrscan');//写入日志，可以删除
        $wx = new Wxoauth($bid,'rwd',$this->appId,$options);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';
        if (!$_GET['callback']) {
            $auth_url = $wx->getOauthRedirect($appid,$callback_url, '', 'snsapi_base');
            Kohana::$log->add('qwtrwd:callback:bid:'.$bid, $auth_url);
            header("Location:$auth_url");exit;
        } else {
            $token = $wx->getOauthAccessToken($appid,$appsecret);
        }
        $fuopenid2 = $token['openid'];//当前用户服务号openid
        $scan=ORM::factory('qwt_rwdscan')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid2)->find();
        if(!$scan->id&&$fuopenid2){
            $scan->bid=$bid;
            $scan->fuopenid=$fuopenid2;
            $scan->time=time();
            $scan->save();
        }
        $config = ORM::factory('qwt_rwdcfg')->getCfg($bid,1);
        //$we = new Wechat($config);

        $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid2)->find();//根据服务号查
        $user1 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('openid','=',$openid1)->find();//根据订阅号查上线
        //风险判断
        if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0) {
            //直接用户
            $count2 = ORM::factory('qwt_rwdqrcode', $user1->id)->scores->where('type', '=', 2)->count_all();
            //用是否生成海报判断真实下线
            $count3 = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('fopenid', '=', $user1->openid)->where('ticket', '<>', '')->count_all();
            if ($user1->lock == 0 && $count2 >= $config['risk_level1'] & $count3 <= $config['risk_level2']) {
                $user1->lock = 1;
                $user1->save();
                //发消息通知上级
                $msg['touser'] = $openid1;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $config['text_risk'];
                $we_result = $wx->sendCustomMessage($msg);
            }
        }
        $expiretime = explode('|',$ticket)[2];
        if($expiretime<time())  $over=1;
        // echo $user1->lock.'<br>';
        // echo $over.'<br>';
        // exit();
        if($user1->lock!=1&&$over!=1){//二维码没过期并且未锁定
            if($user2->openid){//如果自己是老用户 也就是 openid存在
                if($user2->fopenid){//如果自己有上级
                    $fuser = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
                    $bindcon= '您已经是'.$fuser->nickname.'的支持者了，不用再扫了哦~';
                }else if($user2->openid==$openid1){//自己扫自己
                    $bindcon= '不能自己扫自己哟';
                }else if($user2->openid==$user1->fopenid){
                    $bindcon= $user1->nickname.'已经是您的支持者了哟';
                }else{//没有上线
                    $qr_img = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/images/'.$bid.'/wx_qr_img';
                    $bindcon= '亲，请直接进入'.$login->weixin_name.'公众号点击菜单生成海报参与哦~';
                }
            }else{//openid不存在
                if($user2->fuopenid&&$user2->fopenid){//多次扫码但是自己不绑定
                    $fuser = ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
                    $bindcon= '只差一步就能获得奖励啦，快点我~';
                    $href=1;
                }else{//未扫过  只预先保存关系 不存多余东西
                    $user2->fuopenid = $fuopenid2;
                    $user2->fopenid = $openid1;
                    $user2->bid = $bid;
                    $user2->save();
                    $bindcon='点我参与本活动';
                    $href=1;
                }
            }
        }
        if($user1->lock==1){
            $bindcon='您扫的用户：'.$user1->nickname.'已经被锁定了，您不能成为他的支持者！';
        }
        $view = "weixin/smfyun/rwd/sub";
        $title = $config['name'];
        $this->template->content = View::factory($view)->bind('subhref', $config['subhref'])->bind('over', $over)->bind('href', $href)->bind('bindcon', $bindcon)->bind('title', $title)->bind('qr_img', $qr_img);
    }
    //图文获取  点击验证图文
    public function action_qrcheck($openid2=1){
        $this->template = 'tpl/blank';
        self::before();
        $bid = $this->bid;
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $appid="wxd3a678cfeb03e3a3";//神码浮云技术部的
        $appsecret="661fb2647a804e14ded1f65fad682695";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if(!$bid) Kohana::$log->add('qwtrwdbid:', 'qrcheck');//写入日志，可以删除
        $this->wx=$wx = new Wxoauth($bid,'rwd',$this->appId,$options);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';
        if (!$_GET['callback']) {
                $auth_url = $wx->getOauthRedirect($appid,$callback_url, '', 'snsapi_base');
                Kohana::$log->add("qwt$rwdtoken", print_r($auth_url,true));
                header("Location:$auth_url");exit;
            } else {
                $token = $wx->getOauthAccessToken($appid,$appsecret);
                Kohana::$log->add("qwt$rwdtoken", print_r($token,true));
                $_SESSION['qwtrwd']['config'] = $this->config;
                $_SESSION['qwtrwd']['openid'] = $this->openid;
                $_SESSION['qwtrwd']['bid'] = $this->bid;
                // Kohana::$log->add("session:qrcheck:$this->bid:openid", $_SESSION['qwtrwd']['openid']);
                // Kohana::$log->add("session:qrcheck:$this->bid:bid", $_SESSION['qwtrwd']['bid']);
            }
        $openid2 = $this->openid;//当前用户订阅号openid
        $fuopenid2 = $token['openid'];//当前用户服务号openid

        $config = $this->config;
        //$we = new Wechat($config);

        $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$this->bid)->where('fuopenid','=',$fuopenid2)->find();

        if(!$user2->id){//自己关注 自己点击 是没有id的
            $bindcon='请直接点击菜单生成海报参与本活动哦~';
            $userinfo=$wx->getUserInfo($openid2);//根据订阅号openid获取用户信息
            $user2->openid=$openid2;
            $user2->fuopenid=$fuopenid2;
            $user2->bid=$this->bid;
            $user2->nickname=$userinfo['nickname'];
            $user2->headimgurl=$userinfo['headimgurl'];
            $user2->subscribe=$userinfo['subscribe'];
            $user2->sex=$userinfo['sex'];
            $user2->subscribe_time=$userinfo['subscribe_time'];
            $user2->save();
            if($config['text_follow_url']){
                $msg['touser'] = $user2->openid;//user2 当前用户
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '活动说明';
                $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$user2->openid;
                $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                $wx->sendCustomMessage($msg);
            }
        }else{//数据库有值
            if(!$user2->openid){//新用户
                $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$this->bid)->where('fuopenid','=',$fuopenid2)->find();
                $userinfo=$wx->getUserInfo($openid2);//根据订阅号openid获取用户信息
                $user2->openid=$openid2;
                $user2->nickname=$userinfo['nickname'];
                $user2->headimgurl=$userinfo['headimgurl'];
                $user2->subscribe=$userinfo['subscribe'];
                $user2->sex=$userinfo['sex'];
                $user2->subscribe_time=$userinfo['subscribe_time'];
                $user2->save();
                //验证改用户之前是否关注公众号
                $subscribe=ORM::factory('qwt_wfbsubscribe')->where('bid','=',$bid)->where('openid','=',$openid2)->find();
                Kohana::$log->add("rwd{$bid}SCAN1",print_r($fuopenid2, true));
                $scan=ORM::factory('qwt_rwdscan')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid2)->find();
                Kohana::$log->add("rwd{$bid}SCAN11",print_r($scan->id, true));
                if($subscribe->id&&$scan->id&&$subscribe->creattime<=$scan->time-60){
                    Kohana::$log->add("wfb{$bid}SCAN111",print_r($subscribe->openid, true));
                    $has_subscribe=1;
                    $model_p = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                    $model_p->old=1;
                    $model_p->save();
                    Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
                }else{
                    $model_p = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                    $model_p->old=0;
                    $model_p->save();
                    $has_subscribe=2;
                    Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
                }
                Kohana::$log->add("wfb{$bid}SCAN",print_r(time(), true));
                //验证改用户之前是否关注公众号
                $fuser = ORM::factory('qwt_rwdqrcode')->where('bid','=',$this->bid)->where('openid','=',$user2->fopenid)->find();
                //给上级用户发消息  绑定成功或者失败
                $model_q = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                $task =ORM::factory('qwt_rwdtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find();
                if($task->id&&$has_subscribe==2){
                    $record=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$model_q->id)->find();

                    if(!$record->id){
                        $record->bid=$bid;
                        $record->tid=$task->id;
                        $record->qid=$model_q->id;
                        $record->fqid=$fuser->id;
                        $record->save();
                    }
                    $record2=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('qid','=',$fuser->id)->find();
                    Kohana::$log->add("record2", print_r($record2->id,true));
                    if(!$record2->id){
                        $record2->bid=$bid;
                        $record2->tid=$task->id;
                        $record2->qid=$fuser->id;
                        $record2->save();
                    }

                }
                $tid=ORM::factory('qwt_rwdtask')->where('bid','=',$bid)->where('begintime','<',time())->where('endtime','>',time())->find()->id;
                $last_num=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('fqid','=',$fuser->id)->where('tid','=',$tid)->count_all();
                Kohana::$log->add("qwtrwdd", print_r('a', true));
                if($has_subscribe==2){
                    // $fuser->scores->scoreIn($fuser, 2, $config['goal']);
                    if($tid){
                        $num =ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$tid)->where('fqid','=',$fuser->id)->count_all();
                        $sku_all=ORM::factory('qwt_rwdsku')->where('bid','=',$bid)->where('tid','=',$tid)->count_all();
                        $skus = ORM::factory('qwt_rwdsku')->where('bid','=',$bid)->where('tid','=',$tid)->order_by('num', 'ASC')->find_all();
                        $sql = DB::query(Database::SELECT,"SELECT * from qwt_rwdskus where `bid` = $bid and `tid` = $tid");
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
                                $item_next=ORM::factory('qwt_rwditem')->where('id','=',$sku_nests[$alltime]['iid'])->find()->km_content;
                                Kohana::$log->add("item_next", print_r($item_next, true));
                            }
                            Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                            Kohana::$log->add("sku_all", print_r($sku_all, true));
                            Kohana::$log->add("alltime", print_r($alltime, true));
                            $ordernum=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                            Kohana::$log->add("num", print_r($num, true));
                            Kohana::$log->add("sku_num", print_r($sku_num, true));
                            if($num>=$sku->num){
                                $flag=1;
                                $order=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('tid','=',$tid)->where('kid','=',$sku->id)->where('qid','=',$fuser->id)->find();
                                if(!$order->id){
                                    $flag=1;
                                    $item=ORM::factory('qwt_rwditem')->where('id','=',$sku->iid)->find();
                                    $ordernum=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                                    //将库存存入memcache
                                    $m = new Memcached();
                                    $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
                                    $keyname="qrwd_ordernum:{$bid}:{$sku->id}";
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
                                        Kohana::$log->add("qwt_rwd:$bid:stock", print_r($sku->stock, true));
                                        if($alltime==$sku_all){
                                            $finish=1;
                                        }else{
                                            $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                            $ordernum_next=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('kid','=',$sku_nests[$alltime]['id'])->where('state','=',1)->count_all();
                                            $stock_next=$sku_nests[$alltime]['stock']-$ordernum_next;
                                            Kohana::$log->add("sku_nest", print_r($sku_nest, true));
                                        }
                                        if ($item->key=='yhm') {
                                            $this->sendKmi($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }elseif ($item->key=='shiwu') {
                                            $this->sendShiwu($item->id,$fuser->id,$tid,$sku->id,$finish,$sku_nest,$model_q->nickname,$item_next,$stock_next);
                                            break;
                                        }
                                    }else{
                                        $order=ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('iid','=',$item->id)->where('qid','=',$fuser->id)->where('tid','=',$tid)->where('kid','=',$sku->id)->find();
                                        if(!$order->id){
                                            $order->bid=$bid;
                                            $order->tid=$tid;
                                            $order->qid=$fuser->id;
                                            $order->iid=$item->id;
                                            $order->kid=$sku->id;
                                            $order->status=1;
                                            $order->name=$fuser->nickname;
                                            $order->task_name=ORM::factory('qwt_rwdtask')->where('id','=',$tid)->find()->name;
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
                                            $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
                                            break;
                                        }else{
                                            $text_goal2=$config['text_goal2'];
                                            $text_goal2s=sprintf($text_goal2,$task->name);
                                            $sku_nest=$sku_nests[$alltime]['num']-$sku->num;
                                            $keyword=$model_q->nickname.'成为了你的支持者'.$text_goal2s;
                                            $keyword1=$task->name;
                                            $keyword2='本级奖品已被领完,继续加油，么么哒。';
                                            $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
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
                        $this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
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
                        $keyword1='任务名称:'.$task->name;
                        //$keyword2="\\n你试试";
                        $keyword2="任务目标:{$sku_num}\n\n已经完成:{$last_num}\n\n还需人数:{$need_num}\n\n{$item_name}剩余数量:{$left_num}";
                        //$openid=$fuser->openid;
                        Kohana::$log->add("openid", print_r($fuser->openid, true));
                        Kohana::$log->add("mgtpl", print_r($mgtpl, true));
                        Kohana::$log->add("keyword", print_r($keyword, true));
                        Kohana::$log->add("keyword1", print_r($keyword1, true));
                        Kohana::$log->add("keyword2", print_r($keyword2, true));
                        $lll=$this->sendTemplateMessage($fuser->openid,$mgtpl,'',$keyword,$keyword1,$keyword2,'');
                        Kohana::$log->add("lll$bid", print_r($lll, true));
                    }
                    // $msg['touser'] = $fuser->openid;//fuser 上级用户
                    // $msg['msgtype'] = 'text';
                    // $msg['text']['content'] = sprintf($config['text_goal'],$user2->nickname).'您的当前'.$config['score'].'为：';
                    // $wx->sendCustomMessage($msg);
                }else{
                    $msg['touser'] = $fuser->openid;//fuser 上级用户
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '您的朋友'.$user2->nickname.'已经关注过公众号了，不能再成为您的支持者了';
                    $wx->sendCustomMessage($msg);
                }
                $user2 = ORM::factory('qwt_rwdqrcode')->where('bid','=',$this->bid)->where('fuopenid','=',$fuopenid2)->find();
                //给自己发消息  绑定成功或者失败
                if($has_subscribe==2){
                    // $user2->scores->scoreIn($user2, 1, $config['goal0']);
                    $msg['touser'] = $user2->openid;//user2 当前用户
                    $msg['msgtype'] = 'text';
                    //$msg['text']['content'] = sprintf($config['text_goal3'],$fuser->nickname);
                    $msg['text']['content'] = str_replace('%s', $fuser->nickname, $config['text_goal3']);
                    $wx->sendCustomMessage($msg);

                    $bindcon='恭喜您成为'.$fuser->nickname.'的支持者';
                }else{
                    $msg['touser'] = $user2->openid;//user2 当前用户
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '您已经关注过公众号了，不能再成为'.$fuser->nickname.'的支持者了哦。';
                    $wx->sendCustomMessage($msg);

                    $bindcon='您已经关注过公众号了，不能再成为'.$fuser->nickname.'的支持者了哦。';
                }
                if($config['text_follow_url']){
                    $msg['msgtype'] = 'news';
                    $msg['news']['articles'][0]['title'] = '活动说明';
                    $msg['news']['articles'][0]['url'] = $config['text_follow_url'].'?openid='.$user2->openid;
                    $msg['news']['articles'][0]['picurl'] = $this->cdnurl.'news_follow.png';
                    $we_result = $wx->sendCustomMessage($msg);
                }
            }else{//老用户
                if(!$user2->fopenid){//不存在上线
                    $bindcon='快让更多小伙伴加入吧';
                }else{//有上线
                    $fuser = ORM::factory('qwt_rwdqrcode')->where('bid','=',$this->bid)->where('openid','=',$user2->fopenid)->find();
                    $bindcon='您已经是'.$fuser->nickname.'的支持者了，不用再点了哦~';
                }
            }
        }

        $view = "weixin/smfyun/rwd/qrcheck";
        $this->template->content = View::factory($view)->bind('over', $over)->bind('bindcon', $bindcon)->bind('config', $config);
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location($openid2=1){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/rwd/check_location";
        //$wx['appid'] = $this->config['appid'];
        //$wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        //$we = new Wechat($wx);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        if(!$this->bid) Kohana::$log->add('qwtrwdbid:', 'localtion');//写入日志，可以删除
        $wx = new Wxoauth($this->bid,$options);
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=LBQBZ-6J63V-M5PPN-U65U7-7DVG5-RWFST';
          $ch = curl_init(); // 初始化一个 cURL 对象
          curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
          curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
          $res = curl_exec($ch); // 运行cURL，请求网页
          curl_close($ch); // 关闭一个curl会话
          $json_obj = json_decode($res, true);
          //$nation = $json_obj['result']['address_component']['nation'];
          $province = $json_obj['result']['address_component']['province'];
          $city = $json_obj['result']['address_component']['city'];
          $disrict = $json_obj['result']['address_component']['district'];
          //$street = $json_obj['result']['address_component']['street'];
          $content = $province.$city.$disrict;
          echo $content;
          $area = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        $count = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        $info = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('qwt_rwdcfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
                //->bind('fuopenid', $fuopenid2);
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_rwd$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }
    public function sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2,$keyword3){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $keyword."\n\n".$keyword2;
        if($keyword1){
            $msg['text']['content'] = $keyword."\n\n".$keyword1."\n\n".$keyword2;
        }
        if(!$keyword2){
            $msg['text']['content'] = $keyword."\n\n".$keyword1;
        }
        if($url){
            $text1=$keyword3?$keyword3:'点击查看明细';
            $msg['text']['content']=$msg['text']['content']."\n\n".'<a href="'.$url.'">'.$text1.'</a>';
        }
        $result=$this->wx->sendCustomMessage($msg);
        return $result;
        // Kohana::$log->add("qwt_rwd_lll", 'lll');
        // $keyword=str_replace(' ','',$keyword);
        // $keyword=str_replace('"','',$keyword);
        // $keyword1=str_replace(' ','',$keyword1);
        // $keyword1=str_replace('"','',$keyword1);
        // $keyword2=str_replace(' ','',$keyword2);
        // $keyword2=str_replace('"','',$keyword2);
        // $tplmsg['touser'] = $openid;
        // $tplmsg['template_id'] = $mgtpl;
        // $tplmsg['url']=$url;
        // $tplmsg['data']['first']['value']=urlencode($keyword);
        // $tplmsg['data']['first']['color'] = '#FF0000';
        // $tplmsg['data']['keyword1']['value'] = urlencode($keyword1);
        // $tplmsg['data']['keyword1']['color'] = '#FF0000';
        // // $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:m');
        // // $tplmsg['data']['keyword3']['color'] = '#FF0000';
        // $tplmsg['data']['remark']['value'] = urlencode($keyword2);
        // $tplmsg['data']['remark']['color'] = '#FF0000';
        // Kohana::$log->add("qwt_rwd_tplmsg",print_r($tplmsg,true));
        // Kohana::$log->add("qwt_rwd_tplmsg1",print_r(json_encode($tplmsg),true));
        // Kohana::$log->add("qwt_rwd_tplmsg2",print_r(urldecode(json_encode($tplmsg)),true));
        // $result=$this->wx->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        // Kohana::$log->add("qwt_rwd_tplmsg3",print_r($result,true));
        // if($result['errmsg']!='ok'){
        //     $msg['touser'] = $openid;
        //     $msg['msgtype'] = 'text';
        //     $msg['text']['content'] = $result['errmsg'];
        //     $this->wx->sendCustomMessage($msg);
        // }
        // return $result;
    }
    public function sendCustomMessage($openid,$km_text){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $km_text;
        $result=$this->wx->sendCustomMessage($msg);
        return $result;
    }
    public function sendKmi($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        Kohana::$log->add("km", '进入卡密了');
        $bid = $this->bid;
        $mgtpl=$this->config['mgtpl'];
        $items=ORM::factory('qwt_rwditem')->where('id','=',$iid)->find();
        $value=$items->value;
        $item_name=$items->km_content;
        $item_text=$items->km_text;
        $qrcodes=ORM::factory('qwt_rwdqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwdtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwdsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        $order=ORM::factory('qwt_rwdorder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
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
        $count=ORM::factory('qwt_rwdkm')->where('bid','=',$this->bid)->where('live','=',1)->where('starttime','=',$items->value)->count_all();
         Kohana::$log->add("count", printf($count,true));
        if($count!=0){
            $kmikm=ORM::factory('qwt_rwdkm')->where('bid','=',$this->bid)->where('live','=',1)->where('starttime','=',$items->value)->find();
            $url='http://'.$_SERVER['HTTP_HOST'].'/qwtrwd/kmpass/'.$kmikm->id.'/'.$iid;
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
                $keyword1='任务名称:'.$task_name;
                $keyword2="您的全部任务已完成";
                $keyword3=$text;
                $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2,$keyword3);
            }else{
                $text_goal=$this->config['text_goal'];
                $text_goals=sprintf($text_goal,$nickname);
                $keyword=$text_goals."您的当前任务已完成，恭喜您获得奖品".$item_name.','.$msgs."\n\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
                $keyword1='任务名称:'.$task_name;
                $keyword2="任务目标:{$sku_nest}\n\n{$item_next}剩余数量:{$stock_next}";
                $keyword3=$text;
                $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2,$keyword3);
            }
            if($result['errmsg']=='ok'){
                $order->state=1;
                $order->save();
                $km=ORM::factory('qwt_rwdkm')->where('id','=',$id)->find();
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
            $this->sendTemplateMessage($openid,$mgtpl,'',$keyword,$keyword1,'','');
        }
    }
    public function sendShiwu($iid,$qid,$tid,$kid,$finish,$sku_nest,$nickname,$item_next,$stock_next){
        $mgtpl=$this->config['mgtpl'];
        $bid = $this->bid;
        $items=ORM::factory('qwt_rwditem')->where('id','=',$iid)->find();
        // $value=$items->value;
        // $hello = explode('&',$value);
        $item_name=$items->km_content;
        $qrcodes=ORM::factory('qwt_rwdqrcode')->where('id','=',$qid)->find();
        $openid=$qrcodes->openid;
        $nickname1=$qrcodes->nickname;
        $tasks=ORM::factory('qwt_rwdtask')->where('id','=',$tid)->find();
        $task_name=$tasks->name;
        $skus=ORM::factory('qwt_rwdsku')->where('id','=',$kid)->find();
        $text=$skus->text;
        Kohana::$log->add("tag", print_r($hello[0],true));
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $keyname=$bid.':'.$iid.':'.$qid.':'.$tid.':'.$kid;
        $m->add($keyname,$openid,5);
        if($m->getResultCode() != Memcached::RES_SUCCESS) return;
        Kohana::$log->add("qwt_rwdresult", print_r($aa,true));
        if($finish==1){
            $url=$_SERVER['HTTP_HOST'].'/qwtrwd/shiwu/1?bid='.$bid.'&qid='.$qid.'&kid='.$kid.'&iid='.$iid.'&tid='.$tid;
            $text_goal2=$this->config['text_goal2'];
            $text_goal2s=sprintf($text_goal2,$task_name);
            $keyword=$nickname.'成为了你的支持者，'.$text_goal2s.'恭喜您获得奖品'.$item_name;
            $keyword1='任务名称:'.$task_name;
            $keyword2="您的全部任务已完成";
            $keyword3=$text;
            $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2,$keyword3);
        }else{
            $url=$_SERVER['HTTP_HOST'].'/qwtrwd/shiwu/1?bid='.$bid.'&qid='.$qid.'&kid='.$kid.'&iid='.$iid.'&tid='.$tid;
            $text_goal=$this->config['text_goal'];
            $text_goals=sprintf($text_goal,$nickname);
            $keyword=$text_goals."您的当前任务已完成,恭喜您获得奖品".$item_name."\n\n您还需要".$sku_nest."个支持者就可以获得".$item_next;
            $keyword1='任务名称:'.$task_name;
            $keyword2="任务目标:{$sku_nest}\n\n奖品名称:{$item_next}\n\n剩余数量:{$stock_next}";
            $keyword3=$text;
            $result=$this->sendTemplateMessage($openid,$mgtpl,$url,$keyword,$keyword1,$keyword2,$keyword3);
        }
        Kohana::$log->add("qwt_rwdtpl_{$this->bid}", print_r($result,true));
        $order=ORM::factory('qwt_rwdorder')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->find();
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
}

