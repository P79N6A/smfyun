<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtkjb extends Controller_Base {
    public $template = 'weixin/sqb/tpl/blank';
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public $access_token;
    public $methodVersion = '3.0.0';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'smfyun';
    public function before() {
        Database::$default = "qwt";
        parent::before();
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'kmpass') return;
        if (Request::instance()->action == 'ticket') return;
        if (Request::instance()->action == 'die') return;
        $_SESSION =& Session::instance()->as_array();
        if (!$_SESSION['qwtkjb']['bid']) die('页面已过期。请重新点击相应菜单');
        $this->config = $_SESSION['qwtkjb']['config'];
        $this->openid = $_SESSION['qwtkjb']['openid'];
        $this->bid = $_SESSION['qwtkjb']['bid'];
        $this->uid = $_SESSION['qwtkjb']['uid'];

        $app = 'kjb';
        $check = Model::factory('select_experience')->fenzai($bid,$app);
        if ($check==false){
            $eventnum = ORM::factory('qwt_kjbevent')->where('bid','=',$bid)->count_all();
            // $cutnum = ORM::factory('qwt_kjbcut')->where('bid','=',$bid)->count_all();
            $leftnum = 50 - $eventnum - $cutnum;
            if (!$leftnum > 0) {
                Request::instance()->redirect('/qwtkjb/die2');
            }
        }
    }
    public function after() {
        parent::after();
    }
    public function action_list(){
        $time = time();
        $result['action'] = '立刻开砍';
        $result['type'] = '砍价商品列表';
        $item_now = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('status','=',0)->where('begintime','<',$time)->where('endtime','>',$time)->find_all();

        $shop['img'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->headimg;
        $shop['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $shop['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','tel')->find()->value;
        $shop['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','url')->find()->value;

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/list')
            ->bind('jsapi',$jsapi)
            ->bind('result',$result)
            ->bind('item_long',$item_long)
            ->bind('shop',$shop)
            ->bind('item_now',$item_now);
    }
    public function action_myitem(){
        $bid = $this->bid;
        $result['action'] = '立刻查看';
        $result['type'] = '我发起的砍价';
        $event = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('qid','=',$this->uid)->find_all();
        $shop['img'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->headimg;
        $shop['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $shop['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','tel')->find()->value;
        $shop['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','url')->find()->value;

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/list')
            ->bind('jsapi',$jsapi)
            ->bind('bid',$bid)
            ->bind('result',$result)
            ->bind('shop',$shop)
            ->bind('event',$event);
    }
    public function action_myorder(){
        $qid = $this->uid;
        $bid = $this->bid;
        $result['action'] = '查看详情';
        $result['type'] = '我的订单';
        $order = ORM::factory('qwt_kjborder')->where('bid','=',$bid)->where('qid','=',$qid)->find_all();
        $shop['img'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->headimg;
        $shop['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $shop['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','tel')->find()->value;
        $shop['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','url')->find()->value;

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/list')
            ->bind('jsapi',$jsapi)
            ->bind('result',$result)
            ->bind('shop',$shop)
            ->bind('order',$order);
    }
    public function action_order($oid){
        $qid = $this->uid;
        $bid = $this->bid;
        $order = ORM::factory('qwt_kjborder')->where('bid','=',$bid)->where('qid','=',$qid)->where('id','=',$oid)->find();
        if (!$order->id) {
            Request::instance()->redirect('/qwtkjb/die');
        }
        // $order = ORM::factory('qwt_kjborder')->where('bid','=',$bid)->where('id','=',$oid)->find();

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/gerenxinxi')
            ->bind('jsapi',$jsapi)
            ->bind('order',$order);
    }
    public function action_die3($type){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        if ($type==1) {
            $result['text']='未找到商品';
        }
        if ($type==2) {
            $result['text']='商品库存不足';
        }
        if ($type==3) {
            $result['text']='来晚一步，被抢空了';
        }

        $this->template->content = View::factory('weixin/smfyun/kjb/die2')
            ->bind('result',$result)
            ->bind('jsapi',$jsapi);
    }
    public function action_die2(){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/die2')
            ->bind('jsapi',$jsapi);
    }
    public function action_die(){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/die')
            ->bind('jsapi',$jsapi);
    }
    public function action_itempage($iid){
        $qid = $this->uid;

        $item = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('id','=',$iid)->find();
        if (!$item->id){
            die('商品不存在');
        }else{
            $item->pv = $item->pv+1;
            $item->save();
            // $result['join_num'] = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->count_all();
        }

        $check = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->find();
        if ($check->id) {
            Request::instance()->redirect('/qwtkjb/kanpage/'.$check->id);
        };
        $result['type'] = 'item';
        $result['sells_num'] = ORM::factory('qwt_kjborder')->where('bid','=',$this->bid)->where('order_state','=',1)->where('iid','=',$iid)->count_all();
        if ($item->endtime==0) {
            $result['time'] = 'forever';
        }else{
            $result['endtime'] = $item->endtime;
            $result['nowtime'] = time();
            $lefttime = $item->endtime - time();
            $result['day'] = intval(floor($lefttime/86400));
            $lefttime = $lefttime%86400;
            $result['hour'] = intval(floor($lefttime/3600));
            $lefttime = $lefttime%3600;
            $result['minute'] = intval(floor($lefttime/60));
            $result['second'] = $lefttime%60;
        }
        $shop['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $shop['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','tel')->find()->value;
        $shop['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','url')->find()->value;

        //pvall浏览量总
        // $pvall = 0;
        // $events = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->find_all();
        // if ($events){
        //     foreach ($events as $m => $n) {
        //       $pvall = $pvall + $n->PV;
        //     }
        // }
        // $pvall=ORM::factory('qwt_kjbevent')->select(array('SUM("PV")', 'pvall'))->where('bid','=',$this->bid)->where('iid','=',$item->id)->find()->pvall;
        //cutcount砍价次数总
        // $cutevent[] = 0;
        // $itemevent = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->find_all();
        // foreach ($itemevent as $m => $n) {
        //     $cutevent[] = $n->id;
        // }
        // $cutcount = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','IN',$cutevent)->count_all();
        // $bid=$this->bid;
        // $iid=$item->id;
        // $cutarray=DB::query(Database::SELECT,"SELECT COUNT(id) as cutcount from qwt_kjbcuts where eid IN (SELECT id from qwt_kjbevents where bid = $bid and iid= $iid)")->execute()->as_array();
        // $cutcount=$cutarray[0]['cutcount'];
        $this->template->content = View::factory('weixin/smfyun/kjb/kanpage')
            ->bind('shop',$shop)
            ->bind('result',$result)
            ->bind('pvall',$pvall)
            ->bind('cutcount',$cutcount)
            ->bind('item',$item);
    }
    public function action_buildkan($iid){
        $qid = $this->uid;

        $check = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('qid','=',$qid)->find();
        if ($check->id) {
            Request::instance()->redirect('/qwtkjb/kanpage/'.$check->id);
        }else{
            $item = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('id','=',$iid)->find();

            //设了要关注但是没关注的情况
            if ($item->need_sub == 2) {
                $subqid = ORM::factory('qwt_kjbqrcode')->where('openid','=',$_SESSION['qwtkjb']['openid'])->where('bid','=',$this->bid)->find()->qid;
                $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('id','=',$subqid)->find();
                if ($qr_user->subscribe == 0) {
                    $shopheadimgurl = "http://".$_SERVER['HTTP_HOST']."/qwta/images/".$this->bid."/wx_qr_img";

                    $need_subscribe = ORM::factory('qwt_kjbqrcode')->where('bid','=',$this->bid)->where('id','=',$qid)->find();
                    $need_subscribe->need_subscribe = 2;
                    $need_subscribe->pushurl = "http://".$_SERVER['HTTP_HOST']."/smfyun/user_snsapi_userinfo/".$this->bid."/kjb/buildkan?iid=".$iid;
                    $need_subscribe->save();

                    require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
                    $options['token'] = $this->token;
                    $options['encodingaeskey'] = $this->encodingAesKey;
                    $options['appid'] = $biz->appid;

                    $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
                    if ($_GET['url']) $callback_url = urldecode($_GET['url']);
                    $wx = new Wxoauth($this->bid,$options);
                    $jsapi = $wx->getJsSign($callback_url);

                    $this->template->content = View::factory('weixin/smfyun/kjb/need_sub')
                        ->bind('jsapi',$jsapi)
                        ->bind('shopheadimgurl',$shopheadimgurl);
                }else{
                    $event = ORM::factory('qwt_kjbevent');
                    $event->bid = $this->bid;
                    $event->qid = $qid;
                    $event->iid = $iid;
                    $event->now_price = $item->old_price;
                    $event->save();
                    $item->eventcount = $item->eventcount + 1;
                    $item->save();
                    Request::instance()->redirect('/qwtkjb/kanpage/'.$event->id);
                }
            }else{
                $event = ORM::factory('qwt_kjbevent');
                $event->bid = $this->bid;
                $event->qid = $qid;
                $event->iid = $iid;
                $event->now_price = $item->old_price;
                $event->save();
                $item->eventcount = $item->eventcount + 1;
                $item->save();
                Request::instance()->redirect('/qwtkjb/kanpage/'.$event->id);
            }

        }
    }
    //关注页面
    public function action_need_sub($eid){
        $shopheadimgurl = "http://".$_SERVER['HTTP_HOST']."/qwta/images/".$this->bid."/wx_qr_img";
        $qid = $this->uid;
        $user = ORM::factory('qwt_kjbqrcode')->where('bid','=',$this->bid)->where('id','=',$qid)->find();
        $user->need_subscribe = 1;
        $user->pushurl = "http://".$_SERVER['HTTP_HOST']."/smfyun/user_snsapi_userinfo/".$this->bid."/kjb/kanpage?eid=".$eid;
        $user->save();

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/need_sub')
            ->bind('jsapi',$jsapi)
            ->bind('shopheadimgurl',$shopheadimgurl);
    }
    //订单创建
    public function action_createorder($eid){
        $bid = $this->bid;
        $qid = $this->uid;
        $event = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('id','=',$eid)->find();
        if (!$event->id) {
            Request::instance()->redirect('/qwtkjb/die');
        }
        if (!$qid==$event->qid) {
            Request::instance()->redirect('/qwtkjb/die');
        }
        $item = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('id','=',$event->iid)->find();
        $order = ORM::factory('qwt_kjborder')->where('bid','=',$this->bid)->where('qid','=',$qid)->where('eid','=',$eid)->find();
        if ($order->id) {
            Request::instance()->redirect('/qwtkjb/checkout/'.$order->id);
        }

        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $stock = $item->stock;
        $keyname="qwtkjb_itemnum:{$bid}:{$item->id}";
        do {
            $item_num = $m->get($keyname, null, $cas);
            if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                $m->add($keyname, $stock);
            } else {
                $m->cas($cas, $keyname, $stock);
            }
        } while ($m->getResultCode() != Memcached::RES_SUCCESS);
        //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");
        //通过memcache队列判断库存
        do {
            $item_num = $m->get($keyname, null, $cas1);
            $item_num-=1;
            $m->cas($cas1, $keyname, $item_num);
        } while ($m->getResultCode() != Memcached::RES_SUCCESS);
        if(!$item->id) {
            $result['error'] = '未找到商品';
            Request::instance()->redirect('/qwtkjb/die3/1');
        }elseif($item->stock <= 0){
            $result['error'] = '商品库存不足';
            Request::instance()->redirect('/qwtkjb/die3/2');
        }elseif($item_num<0){
            $result['error'] = '商品库存不足啦';
            Request::instance()->redirect('/qwtkjb/die3/3');
        }else{
            // $m->set('qwt_wfb:{$this->bid}:{$this->qid}:{$item->id}', 1, time() + 300);//300s有效期 5min
            $order = ORM::factory('qwt_kjborder');
            // $order->values($_POST['data']);
            // exit();
            $order->eid = $eid;
            $order->item_name = $item->name;
            $order->iid = $event->iid;
            $order->bid = $this->bid;
            $order->pay_money = $event->now_price;
            $order->qid = $event->qid;
            $order->save();

            //库存-1
            $item->stock--;
            $item->save();
            Request::instance()->redirect('/qwtkjb/checkout/'.$order->id);
        }

    }

    //付款页面
    public function action_checkout($oid){
        $qid = $this->uid;
        $bid = $this->bid;
        $order = ORM::factory('qwt_kjborder')->where('bid','=',$this->bid)->where('id','=',$oid)->find();
        if (!$order->id) {
            Request::instance()->redirect('/qwtkjb/die');
        }
        if (!$order->qid=$qid) {
            Request::instance()->redirect('/qwtkjb/die');
        }
        $event = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('id','=',$order->eid)->find();
        $item = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('id','=',$event->iid)->find();
        if ($item->status == 3)die('活动已被终止');
        if (time()<$item->begintime)die('活动未开始');
        if ($item->endtime<time()&&$item->endtime>0)die('活动已经结束');

        if ($_POST['wxpay']) {
            $order->values($_POST['data']);
            $order->pay_money=$event->now_price;
            $order->pay_time=time();
            $order->save();
            $config = ORM::factory('qwt_cfg')->getCfg($this->bid);
            require_once Kohana::find_file('vendor/wx_pay', 'WeixinPay');
            $biz = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            $appid = $biz->appid;
            $openid = $this->openid;
            $mch_id = $config['mchid'];
            $key = $config['apikey'];
            $out_trade_no = $mch_id. time();
            $total_fee = floor($event->now_price);
            $body = $item->name.'费用';
            $attach = base64_encode('qwt_kjborder:'.$order->id.':order_state:kjb:'.$wait_order->id);//表名 oid  字段状态 wait_order->id
            $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/notify_url';
            Kohana::$log->add("qwtkjb:$bid:$oid:money", $total_fee);
            $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$attach,$notify_url);
            $result=$weixinpay->pay();
            $result['oid'] = $order->id;
            echo json_encode($result);
            exit;
        }
        if ($_POST['freeget']) {
            if (!$event->now_price==0) {
                $result['error']='未正常支付，请重试';
            }else{
                $order->values($_POST['data']);
                $order->order_state=1;
                $order->pay_time=time();
                $order->save();
                $result['oid'] = $order->id;
                echo json_encode($result);
                exit;
            }
        }

        $shop['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $shop['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','tel')->find()->value;
        $shop['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','url')->find()->value;
        $shop['title'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','title')->find()->value;
        $user = ORM::factory('qwt_kjbqrcode')->where('id','=',$this->uid)->find();
        $fuser = ORM::factory('qwt_kjbqrcode')->where('id','=',$event->qid)->find();
        $biz = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/fukuan')
            ->bind('join',$join)
            ->bind('cut',$cut)
            ->bind('user',$user)
            ->bind('fuser',$fuser)
            ->bind('shop',$shop)
            ->bind('event',$event)
            ->bind('result',$result)
            ->bind('jsapi',$jsapi)
            ->bind('order',$order)
            ->bind('item',$item);
    }

    public function action_kanpage($eid){
        $qid = $this->uid;
        $event = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('id','=',$eid)->find();
        $item = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('id','=',$event->iid)->find();

        if (!$event->id){
            die('活动不存在');
        }else{
            $event->PV = $event->PV + 1;
            $event->save();
            $event = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('id','=',$eid)->find();
        }
        if (!$item->id){
            die('商品不存在');
        }else{
            $item->pv = $item->pv + 1;
            $item->save();
            $item = ORM::factory('qwt_kjbitem')->where('bid','=',$this->bid)->where('id','=',$event->iid)->find();
            // $result['join_num'] = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->count_all();
        }
        if ($item->status == 3)die('活动已被终止');
        if (time()<$item->begintime)die('活动未开始');
        if ($item->endtime<time()&&$item->endtime>0)die('活动已经结束');

        $result['sells_num'] = ORM::factory('qwt_kjborder')->where('order_state','=',1)->where('bid','=',$this->bid)->where('iid','=',$event->iid)->count_all();
        if ($item->endtime==0) {
            $result['time'] = 'forever';
        }else{
            $result['endtime'] = $item->endtime;
            $result['nowtime'] = time();
            $lefttime = $item->endtime - time();
            $result['day'] = intval(floor($lefttime/86400));
            $lefttime = $lefttime%86400;
            $result['hour'] = intval(floor($lefttime/3600));
            $lefttime = $lefttime%3600;
            $result['minute'] = intval(floor($lefttime/60));
            $result['second'] = $lefttime%60;
        }
        $shop['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $shop['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','tel')->find()->value;
        $shop['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','url')->find()->value;
        $shop['title'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$this->bid)->where('key','=','title')->find()->value;
        $user = ORM::factory('qwt_kjbqrcode')->where('id','=',$this->uid)->find();
        $fuser = ORM::factory('qwt_kjbqrcode')->where('id','=',$event->qid)->find();
        $biz = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();

        if($_POST['wxpay']==1){ //唤起微信所需要支付签名
            $m = new Memcached();
            $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
            $stock = $item->stock;
            $keyname="qwtkjb_itemnum:{$bid}:{$item->id}";
            do {
                $item_num = $m->get($keyname, null, $cas);
                if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                    $m->add($keyname, $stock);
                } else {
                    $m->cas($cas, $keyname, $stock);
                }
            } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");
            //通过memcache队列判断库存
            do {
                $item_num = $m->get($keyname, null, $cas1);
                $item_num-=1;
                $m->cas($cas1, $keyname, $item_num);
            } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            if(!$item->id) {
                $result['error'] = '未找到商品';
            }elseif($item->stock <= 0){
                $result['error'] = '商品库存不足';
            }elseif($item_num<0){
                $result['error'] = '商品库存不足啦';
            }else{
                // $m->set('qwt_wfb:{$this->bid}:{$this->qid}:{$item->id}', 1, time() + 300);//300s有效期 5min
                $order = ORM::factory('qwt_kjborder');
                $order->values($_POST['data']);
                // exit();
                $order->eid = $eid;
                $order->item_name = $item->name;
                $order->iid = $event->iid;
                $order->bid = $this->bid;
                $order->pay_money = $event->now_price;
                $order->qid = $fuser->id;
                $order->save();

                //库存-1
                $item->stock--;
                $item->save();
                //创建新订单
                $wait_order = ORM::factory('qwt_waitorder');
                $wait_order->bid = $this->bid;
                $wait_order->qid = $fuser->id;
                $wait_order->iid = $item->id;
                $wait_order->item_name = 'qwt_kjbitem';
                $wait_order->save();

                $config = ORM::factory('qwt_cfg')->getCfg($this->bid);
                require_once Kohana::find_file('vendor/wx_pay', 'WeixinPay');
                $biz = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                $appid = $biz->appid;
                $openid = $this->openid;
                $mch_id = $config['mchid'];
                $key = $config['apikey'];
                $out_trade_no = $mch_id. time();
                $total_fee = floor($event->now_price);
                $body = $item->name.'费用';
                $attach = base64_encode('qwt_kjborder:'.$order->id.':order_state:kjb:'.$wait_order->id);//表名 oid  字段状态 wait_order->id
                $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/notify_url';
                $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$attach,$notify_url);
                $result=$weixinpay->pay();
                $result['oid'] = $order->id;



                // $config = ORM::factory('qwt_cfg')->getCfg($this->bid);
                // require_once Kohana::find_file('vendor/wx_pay', 'WeixinPay');

                // $appid = $biz->appid;
                // $openid = $this->openid;
                // $mch_id = $config['mchid'];
                // $key = $config['apikey'];
                // $out_trade_no = $mch_id. time();
                // $total_fee = floor($event->now_price/100);
                // $body = $item->name.'费用';
                // $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
                // $result = $weixinpay->pay();
                // //库存-1
                // $item->stock--;
                // $item->save();
                // //创建新订单
                // $wait_order = ORM::factory('qwt_waitorder');
                // $wait_order->bid = $this->bid;
                // $wait_order->qid = $fuser->id;
                // $wait_order->iid = $item->id;
                // $wait_order->item_name = 'qwt_kjbitem';
                // $wait_order->save();
                // $result['wait_id'] = $wait_order->id;
                // $result['pay_money'] = $total_fee;
            }
            echo json_encode($result);
            exit;
        }
        if($_POST['buy']){ //name tel address   $_POST['buy']['name']

            //0.有库存没？
            // if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能购买");

            // $m = new Memcached();
            // $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
            // $stock = $item->stock;
            // $keyname="qwtkjb_itemnum:{$bid}:{$item->id}";
            //   //将库存存入memcache
            // do {
            //     $item_num = $m->get($keyname, null, $cas);
            //     if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
            //         $m->add($keyname, $stock);
            //     } else {
            //         $m->cas($cas, $keyname, $stock);
            //     }
            // } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            // //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");
            // //通过memcache队列判断库存
            // do {
            //     $item_num = $m->get($keyname, null, $cas1);
            //     $item_num-=1;
            //     $m->cas($cas1, $keyname, $item_num);
            // } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            // if($item_num<0){
            //     $item_num+=1;
            //     die("该奖品库存为 {$item_num}，暂时不能购买，请稍后再试！");
            // }
            $order = ORM::factory('qwt_kjborder');
            $order->values($_POST['buy']);
            $order->bid = $this->bid;
            $order->iid = $item->id;
            $order->eid = $event->id;
            $order->item_name = $item->name;
            $order->qid = $fuser->id;
            // $order->score = $item->score;
            $order->pay_time = time();
            $order->save();

            $event->state=1;
            $event->save();
        }
        if($_POST['cut']==1){//砍价
            if($fuser->id == $user->id){
                $qknife = ORM::factory('qwt_kjbknife')->where('bid','=',$this->bid)->where('iid','=',$item->id)->where('qid','=',$qid)->where('endtime','>',time())->find();
                if (!$qknife->id) {
                    $result['error'] = '不能自己给自己砍价！';
                    $result['state'] = 0;
                    echo json_encode($result);
                    exit;
                }
            }
            if (!$_POST['self']) {
                $has_cut_event = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->find_all();
                foreach ($has_cut_event as $key => $value) {
                    $has_cut_eventarr[$key] = $value->id;
                }
                $has_cut_obj = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','IN',$has_cut_eventarr)->find_all();
                foreach ($has_cut_obj as $k => $v) {
                    $has_cut_arr[$k] = $v->qid;
                }
                if(@in_array($user->id, $has_cut_arr)){
                    $result['error'] = '该商品您已经帮好友砍过一次了，不能再砍了~';
                    $result['state'] = 0;
                    echo json_encode($result);
                    exit;
                }
            }
            $has_cut_num = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','=',$eid)->count_all();
            $lessnum = $item->cut_num - $has_cut_num;//剩余砍价次数
            $has_cut = DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_kjbcuts where bid=$this->bid and eid=$eid")->execute()->as_array();
            $lessmoney = $item->old_price - $item->price - $has_cut[0]['money'];//剩余金额

            Kohana::$log->add("qwtkjbprice:$bid:1", $item->old_price);
            Kohana::$log->add("qwtkjbprice:$bid:2", $item->price);
            Kohana::$log->add("qwtkjbprice:$bid:3", $has_cut[0]['money']);
            $m = new Memcached();
            $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
            $keyname="qwtkjb_cutnum:{$bid}:{$has_cut_num}";

            do {
                $item_num = $m->get($keyname, null, $cas);
                if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                    $m->add($keyname, $lessnum);
                } else {
                    $m->cas($cas, $keyname, $lessnum);
                }
            } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            //通过memcache队列判断库存
            do {
                $item_num = $m->get($keyname, null, $cas1);
                $item_num-=1;
                $m->cas($cas1, $keyname, $item_num);
            } while ($m->getResultCode() != Memcached::RES_SUCCESS);

            if($item_num>=0&&$event->now_price > $item->price){
                if($item_num==0){
                    $cut_money = $lessmoney;
                }else{
                    $averge = round($lessmoney/$lessnum);
                    $cut_money = rand($averge/2,1.5*$averge);
                }
                $cut_money_format = number_format($cut_money/100,2);
                $cut = ORM::factory('qwt_kjbcut');
                $cut->bid = $this->bid;
                $cut->eid = $event->id;
                $cut->qid = $qid;
                $cut->money = $cut_money;
                $cut->save();
                $event->now_price=$event->now_price - $cut_money;
                $event->save();
                $item->cutcount = $item->cutcount + 1;
                $item->save();
                $result['content'] = '恭喜您，帮'.$fuser->nickname.'砍了'.$cut_money_format.'元';
                $knife = ORM::factory('qwt_kjbknife')->where('bid','=',$this->bid)->where('iid','=',$item->id)->where('qid','=',$qid)->find();
                if (!$knife->id) {
                    $knife = ORM::factory('qwt_kjbknife');
                    $knife->bid = $this->bid;
                    $knife->iid = $item->id;
                    $knife->qid = $qid;
                    $knife->endtime = time()+3*3600;
                    $knife->save();
                    $result['content'] = $result['content'].'<br>送你一把宝刀，3小时内有效<br>发起此商品的砍价时，可以自己帮自己砍一刀';
                }
                $result['state'] = 1;
            }else{
                $result['1'] = $event->now_price;
                $result['2'] = $item->price;
                $result['3'] = $item_num;
                $result['4'] = $lessnum;
                $result['5'] = $item->cut_num;
                $result['6'] = $has_cut_num;
                $result['error'] = '已经砍到最低价了哦，赶快购买吧！';
                $result['state'] = 0;
            }
            echo json_encode($result);
            exit;
        }

        //砍价排行榜
        $cut_count = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','=',$eid)->order_by('money','DESC')->count_all();

        $cut = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','=',$eid)->order_by('money','DESC')->limit(10)->find_all();

        //砍掉排行榜
        $join = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->order_by('now_price','ASC')->limit(10)->find_all();

        //判断是否同一人 不同的人的话是否砍过 同一个的话是否买过
        if ($this->uid == $fuser->id) {
            //是一个人
            $result['self']=1;
            $buy = ORM::factory('qwt_kjborder')->where('bid','=',$this->bid)->where('eid','=',$event->id)->where('qid','=',$this->uid)->find();
            if ($buy->id) {
                if ($buy->order_state == 1) {
                    //买下并付款了
                    $result['payed'] = 1;
                }else{
                    //买了还没付款,给地址
                    $result['buyed'] = 1;
                    $result['oid'] = $buy->id;
                }
            }else{
                $ifknife = ORM::factory('qwt_kjbknife')->where('bid','=',$this->bid)->where('iid','=',$item->id)->where('qid','=',$this->uid)->where('endtime','>',time())->find();
                if ($ifknife->id) {
                    $ifselfcut = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','=',$event->id)->where('qid','=',$this->uid)->find();
                    if (!$ifselfcut->id) {
                        //有刀且没砍才显示
                        $result['knife'] = 1;
                    }
                }
            }
        }else{
            if ($item->cut_sub == 2) {
                $subqid2 = ORM::factory('qwt_kjbqrcode')->where('openid','=',$_SESSION['qwtkjb']['openid'])->where('bid','=',$this->bid)->find()->qid;
                $qr_user2 = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('id','=',$subqid2)->find();
                if ($qr_user2->subscribe == 0) {
                    $result['subtocut'] = 1;
                }
            }
            $buy = ORM::factory('qwt_kjborder')->where('bid','=',$this->bid)->where('eid','=',$eid)->find();
            if ($buy->id) {
                //显示已经被买
                $result['buyed'] = 1;
            }else{
                $cutted = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','=',$eid)->where('qid','=',$this->uid)->find();
                if ($cutted->id) {
                    $result['cutted'] = 1;
                }
            }
        }

        //pvall浏览量总
        //   $pvall = 0;
        //   $events = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->find_all();
        //   if ($events){
        //     foreach ($events as $m => $n) {
        //       $pvall = $pvall + $n->PV;
        //     }
        // }

        // $pvall=ORM::factory('qwt_kjbevent')->select(array('SUM("PV")', 'pvall'))->where('bid','=',$this->bid)->where('iid','=',$item->id)->find()->pvall;

        //cutcount砍价次数总
        // $cutevent[] = 0;
        // $itemevent = ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('iid','=',$item->id)->find_all();
        // foreach ($itemevent as $m => $n) {
        //     $cutevent[] = $n->id;
        // }
        // $cutcount = ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('eid','IN',$cutevent)->count_all();

        // $bid=$this->bid;
        // $iid=$item->id;
        // $cutarray=DB::query(Database::SELECT,"SELECT COUNT(id) as cutcount from qwt_kjbcuts where eid IN (SELECT id from qwt_kjbevents where bid = $bid and iid= $iid)")->execute()->as_array();
        // $cutcount=$cutarray[0]['cutcount'];

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory('weixin/smfyun/kjb/kanpage')
            ->bind('join',$join)
            ->bind('cut',$cut)
            ->bind('cut_count',$cut_count)
            ->bind('cutted',$cutted)
            ->bind('user',$user)
            ->bind('fuser',$fuser)
            ->bind('cutcount',$cutcount)
            ->bind('shop',$shop)
            ->bind('pvall',$pvall)
            ->bind('event',$event)
            ->bind('result',$result)
            ->bind('jsapi',$jsapi)
            ->bind('item',$item);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_kjb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
