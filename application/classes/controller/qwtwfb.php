<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtwfb extends Controller_Base {
    public $template = 'weixin/smfyun/wfb/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'smfyun';
    public $scorename;
    public $methodVersion='3.0.0';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "qwt";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'index') return;
        if (Request::instance()->action == 'test') return;
        // if (!$_GET['openid']) {
        //     if (!$_SESSION['qwtwfb']['bid']) die('页面已过期。请重新点击相应菜单');
        //     if (!$_SESSION['qwtwfb']['openid']) die('Access Deined..请重新点击相应菜单');
        // }
        if (!$_SESSION['qwtwfb']['bid']) die('页面已过期。请重新点击相应菜单');
        $this->config = $_SESSION['qwtwfb']['config'];
        $this->openid = $_SESSION['qwtwfb']['openid'];
        $this->bid = $_SESSION['qwtwfb']['bid'];
        $this->uid = $_SESSION['qwtwfb']['uid'];


        $sname = ORM::factory('qwt_wfbcfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        if ($_GET['debug']) print_r($_SESSION['wfb']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->where('joinarea','=',0)->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);
        View::bind_global('scorename', $this->scorename);
        $this->template->user = $user;
        parent::after();
    }

    //入口
    public function action_index($bid) {
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid);


        if (!$_GET['openid']) $_SESSION['qwtwfb'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);

            if ($_GET['cksum'] != md5($openid.date('Y-m-d'))) {
                $_SESSION['qwtwfb'] = NULL;
                die('Access Deined!请重新点击相应菜单');
            }
            $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $userobj->ip = Request::$client_ip;
            $userobj->save();

            $_SESSION['qwtwfb']['config'] = $config;
            $_SESSION['qwtwfb']['openid'] = $openid;
            $_SESSION['qwtwfb']['bid'] = $bid;
            $_SESSION['qwtwfb']['uid'] = $userobj->id;

            if ($bid == 2) {
                // print_r($_SESSION);exit;
            }

            Request::instance()->redirect('/qwtwfb/'.$_GET['url']);
        }
    }

    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/smfyun/wfb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;



        //计算排名
        $user = ORM::factory('qwt_wfbqrcode', $this->uid)->as_array();


        if(isset($_POST['rank'])){//今日排名
            $user = ORM::factory('qwt_wfbqrcode', $this->uid)->as_array();

            $ranktoday = "qwtwfb:ranktoday:{$this->bid}:{$this->openid}:$top";
            //$mem->delete($ranktoday);
            $result['rank'] = $mem->get($ranktoday);

            $topday = "qwtwfb:toptoday:{$this->bid}:$top";
            //$mem->delete($topday);
            $users = $mem->get($topday);
            if (!$users) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT qwt_wfbqrcodes.nickname,qwt_wfbqrcodes.headimgurl,qwt_wfbscores.qid,sum(qwt_wfbscores.score) as score from qwt_wfbqrcodes , qwt_wfbscores where qwt_wfbqrcodes.id=qwt_wfbscores.qid and qwt_wfbscores.bid=582 and from_unixtime(qwt_wfbscores.lastupdate,'%Y-%m-%d')= '$today' group by qwt_wfbscores.qid order by score desc limit $top");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users[] = $userobj;
                    // $sql = DB::query(Database::SELECT,"SELECT nickname,headimgurl FROM qwt_wfbqrcodes where `id`=$userobj->qid");
                    // $qr = $sql->execute()->as_array();
                    // $users['nickname'] = $qr['nickname'];
                    // $users['headimgurl'] = $qr['headimgurl'];
                }
                $mem->set($topday, $users, 600);
            }
            if (!$result['rank']) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT qwt_wfbqrcodes.nickname,qwt_wfbqrcodes.headimgurl,qwt_wfbscores.qid,sum(qwt_wfbscores.score) as score from qwt_wfbqrcodes , qwt_wfbscores where qwt_wfbqrcodes.id=qwt_wfbscores.qid and qwt_wfbscores.bid=582 and from_unixtime(qwt_wfbscores.lastupdate,'%Y-%m-%d')= '$today' group by qwt_wfbscores.qid order by score desc ");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users2[] = $userobj;
                    if($userobj['qid']==$this->uid){
                        $rank = $k+1;
                    }
                };
                $result['rank']=$rank;
                $mem->set($ranktoday, $result['rank'], 600);
            }
        }else{
            $rankkey = "qwtwfb:rank3:{$this->bid}:{$this->openid}:$top";
            $result['rank'] = $mem->get($rankkey);
            if (!$result['rank']) {//全部排名
            $result['rank'] = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
            }

            $topkey = "qwtwfb:top3:{$this->bid}:$top";
            $users = $mem->get($topkey);
            if (!$users) {
                $usersobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
                foreach ($usersobj as $userobj) {
                    $users[] = $userobj->as_array();
                }
                $mem->set($topkey, $users, 600);
            }
        }
        $this->template->title = $this->scorename.'排行榜';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);
    }

    //我的积分
    public function action_score() {
        $view = "weixin/smfyun/wfb/scores";

        $this->template->title = '我的'. $this->scorename;
        $this->template->content = View::factory($view)->bind('scores', $scores)->bind('scorename', $this->scorename);

        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('qwt_wfbqrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/smfyun/wfb/items";

        $obj = ORM::factory('qwt_wfbitem')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('qwt_wfbcfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('qwt_wfbscore')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit);
        // $key = "wfb:items:{$this->bid}";
        // $items = $mem->get($key);
        // if (!$items) {
        //     $obj = ORM::factory('qwt_wfbitem')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        //     foreach($obj as $i) $items[] = $i->as_array();
        //     $mem->set($key, $items, 600);
        // }
    }
    //详情页
    public function action_item($iid) {
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/smfyun/wfb/xiangqing";
        $item = ORM::factory('qwt_wfbitem')->where('bid', '=', $this->bid)->where('id', '=', $iid)->find();
        $day_limit = ORM::factory('qwt_wfbcfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('qwt_wfbscore')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $user2 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->as_array();
        // $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('item', $item)->bind('dlimit',$dlimit)->bind('user2',$user2);
    }
    public function action_wxpay(){
        if($_POST['data']){
            $item = ORM::factory('qwt_wfbitem')->where('id','=',$_POST['data']['iid'])->find();
            $m = new Memcached();
            $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
            $stock=$item->stock;
            $keyname="qwfb_itemnum:{$bid}:{$item->id}";
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
                $result['error'] = '未找到奖品';
            }elseif($item->stock <= 0){
                $result['error'] = '奖品库存不足';
            }elseif($item_num<0){
                $result['error'] = '奖品库存不足啦';
            }else{
                $user = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
                // $m->set('qwt_wfb:{$this->bid}:{$this->qid}:{$item->id}', 1, time() + 300);//300s有效期 5min
                $order = ORM::factory('qwt_wfborder');
                $order->values($_POST['data']);
                // exit();
                $order->bid = $this->bid;
                $order->need_money = $item->need_money;
                $order->qid = $user->id;
                $order->score = $item->score;
                $order->save();

                //库存-1
                $item->stock--;
                $item->save();
                //创建新订单
                $wait_order = ORM::factory('qwt_waitorder');
                $wait_order->bid = $this->bid;
                $wait_order->qid = $user->id;
                $wait_order->iid = $item->id;
                $wait_order->item_name = 'qwt_wfbitem';
                $wait_order->save();
                $result['wait_id'] = $wait_order->id;

                $config = ORM::factory('qwt_cfg')->getCfg($this->bid);
                require_once Kohana::find_file('vendor/wx_pay', 'WeixinPay');
                $biz = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                $appid = $biz->appid;
                $openid = $this->openid;
                $mch_id = $config['mchid'];
                $key = $config['apikey'];
                $out_trade_no = $mch_id. time();
                $total_fee = floor($item->need_money);
                $body = $item->name.'费用';
                $attach = base64_encode('qwt_wfborder:'.$order->id.':order_state:wfb:'.$wait_order->id);//表名 oid  字段状态 wait_order->id
                $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/notify_url';
                $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$attach,$notify_url);
                $result=$weixinpay->pay();

            }
            echo json_encode($result);
        }
        exit;
    }
    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/smfyun/wfb/neworder";
        $config = $this->config;
        $bid = $this->bid;
        $config=ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $yzaccess_token=$admin->yzaccess_token;
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($admin->yzaccess_token){
            $client = new YZTokenClient($admin->yzaccess_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $item = ORM::factory('qwt_wfbitem', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/qwtwfb/items');

        $this->template->content = View::factory($view)->bind('item', $item)->bind('jsapi', $jsapi)->bind('bid', $bid);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $stock=$item->stock;
        $keyname="qwfb_itemnum:{$bid}:{$item->id}";
          //将库存存入memcache
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
        if($item_num<0){
            $item_num+=1;
            die("该奖品库存为 {$item_num}，暂时不能兑换，请稍后再试！");
        }
        //1.积分够不
        $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->score} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('qwt_wfborder')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->and_where_open()->where('need_money','=',0)->or_where_open()->where('need_money','>',0)->and_where('order_state','=','1')->or_where_close()->and_where_close()->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('qwt_wfbqrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('qwt_wfbqrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            //用是否生成海报判断下线数量 （生成海报不一定有积分） 下面计算的是 下线中 没有生成海报的数量
            //$count3 = ORM::factory('qwt_wfbqrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
            // echo "2:$count2, 3:$count3";
            // if ($fuser->lock == 1 && $count3 > $config['risk_level2']) {
            //     $fuser->lock = 0;
            //     $fuser->save();
            // }
            if ($userobj->lock == 0 && $count2 >= $this->config['risk_level1'] & $count3 <= $this->config['risk_level2']) {
                $userobj->lock = 1;
                $userobj->save();

                if ($userobj->lock == 1) die('您的账号存在刷分现象，已被锁定。如果您确认是系统误判断，请联系客服解决。');
            }
        }

        $this->template->title = $item->name;
        // if($_POST){
        //     echo "<pre>";
        //     var_dump($_POST);
        //     echo "</pre>";
        //     echo $item->url."<br>";

        // }

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url&&$_POST['data']['type']!=5&&$_POST['data']['type']!=6)&&Security::check($_POST['csrf'])==1) {
            $order = ORM::factory('qwt_wfborder');
            $order->values($_POST['data']);
            // exit();
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->need_money = $item->need_money;
            $order->qid = $userobj->id;
            $order->score = $item->score;

            //虚拟产品
            if ($item->url) {

                $order->status = 1;

                $url = substr($item->url , 0 , 4);
                if ($url == 'http'){
                    $order->url = $item->url;
                } else {
                    $order->url = '/qwtwfb/ticket/'.$item->url;
                }

            } else {
                //省份 城市
                $order->city = $_POST['s_province'].' '.$_POST['s_city'].' '.$_POST['s_dist'];
            }

            //成功
            if ($order->save()) {
                //减库存
                if($order->item->need_money>0){
                    //之前在wxpay里面已经减少了库存
                }else{
                    $item->stock--;
                    $item->save();
                }


                //扣积分

                $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $sid=$userobj->scores->scoreOut($userobj, 4, $order->score);
                if($config['switch']==1){
                    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                    $smfy=new SmfyQwt();
                    $result=$smfy->wfbrsync($bid,$userobj->openid,$yzaccess_token,-$order->score,$sid,'兑换奖品1');
                }
                $goal_url = '/qwtwfb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
        //话费流量
        if ($_POST['data']['type']==3&&Security::check($_POST['csrf'])==1) {
            $order = ORM::factory('qwt_wfborder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();
                //扣积分
                $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $sid=$userobj->scores->scoreOut($userobj, 4, $order->score);
                if($config['switch']==1){
                    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                    $smfy=new SmfyQwt();
                    $result=$smfy->wfbrsync($bid,$userobj->openid,$yzaccess_token,-$order->score,$sid,'兑换奖品2');
                }
                $goal_url = '/qwtwfb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
        //微信红包
        if ($_POST['data']['type']==4&&Security::check($_POST['csrf'])==1) {

            $order = ORM::factory('qwt_wfborder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;
            if($config['hb_check']==1){
               $order->status = 0;
               $order->save();
                //减库存
               $item->stock--;
               $item->save();
                //扣积分
               $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

               $sid=$userobj->scores->scoreOut($userobj, 4, $order->score);
               if($config['switch']==1){
                    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                    $smfy=new SmfyQwt();
                    $result=$smfy->wfbrsync($bid,$userobj->openid,$yzaccess_token,-$order->score,$sid,'兑换奖品3');
                }
                $goal_url = '/qwtwfb/orders';
                require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
                $options['token'] = $this->token;
                $options['encodingaeskey'] = $this->encodingAesKey;
                $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
                $wx = new Wxoauth($bid,$options);
                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的红包需要审核，审核通过后会自动下发，请耐心等待';
                $wx->sendCustomMessage($msg);
                Request::instance()->redirect($goal_url);
                exit;
            }
                //发红包
                $tempname=ORM::factory("qwt_login")->where("id","=",$this->bid)->find()->name;
                $tempmoney=ORM::factory("qwt_wfbitem")->where("id","=",$iid)->find()->price;
                $tempmoney=$tempmoney*100;
                $hbresult = $this->hongbao($this->config, $this->openid, '', $tempname, $tempmoney);
                if($hbresult['result_code']=='SUCCESS')
                {
                    //成功
                   $order->save();

                    //减库存
                   $item->stock--;
                   $item->save();
                    //扣积分

                   $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                   $sid=$userobj->scores->scoreOut($userobj, 4, $order->score);
                   if($config['switch']==1){
                        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                        $smfy=new SmfyQwt();
                        $result=$smfy->wfbrsync($bid,$userobj->openid,$yzaccess_token,-$order->score,$sid,'兑换奖品4');
                    }
                   $goal_url = '/qwtwfb/orders';
                   if ($order->url) $goal_url = $order->url;

                    //成功后跳转
                    Request::instance()->redirect($goal_url);

                }else{
                    echo $hbresult['return_msg'];
                    exit();
                }

        }
        //赠品
        if ($_POST['data']['type']==6&&Security::check($_POST['csrf'])==1){
            $order = ORM::factory('qwt_wfborder');
            $order->values($_POST['data']);
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item
            $oid = ORM::factory('qwt_wfbitem')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            // $client = new YZTokenClient($this->access_token);
            //echo $oid.'<br>';
            // echo '赠品列表:<br><br><br>';
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [

            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            for($i=0;$results['response']['presents'][$i];$i++){
                $res = $results['response']['presents'][$i];
                $present_id=$res['present_id'];
                //echo 'present_id:'.$present_id.'<br>';
                // echo $this->openid."<br>";
                // echo $present_id."<br>";
                if($present_id==$oid){//找到指定赠品
                    //根据openid获取userid
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                       'weixin_openid'=>$this->openid,
                       'fields'=>'user_id',
                    ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);
                    $user_id = $results['response']['user']['user_id'];
                    var_dump($results);
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'youzan.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $result1s = $client->post($method, $this->methodVersion, $params, $files);
                    // echo '<pre>';
                    var_dump($result1s);
                    // echo  '</pre>';
                    // exit();
                    Kohana::$log->add('weixin:oid', print_r($oid, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:fans_id', print_r($user_id, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:gift', print_r($result1s, true));//写入日志，可以删除
                    if($result1s['response']['is_success']==true){
                        $order->status = 1;
                        $order->save();

                        //减库存
                       $item->stock--;
                       $item->save();
                        //扣积分
                       $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                       $sid=$userobj->scores->scoreOut($userobj, 4, $order->score);
                       if($config['switch']==1){
                            require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                            $smfy=new SmfyQwt();
                            $result=$smfy->wfbrsync($bid,$userobj->openid,$yzaccess_token,-$order->score,$sid,'兑换奖品5');
                        }
                       // if($this->config['switch']==1){
                       //      $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score,$sid);
                       //  }
                       //echo "<script>alert('领取成功，请回到公众号主页查看！')</script>";
                        Request::instance()->redirect($result1s["response"]["receive_address"]);
                       //echo $results["response"]["receive_address"];
                       // exit;
                    }else{
                        //var_dump()
                        echo $result1s['error_response']['code'].$result1s['error_response']['msg'];
                        //echo "您已经兑换过该赠品，每个人只能领一次哦～";
                        exit;
                    }

                }
            }

        }
        //优惠券
        if ($_POST['data']['type']==5&&Security::check($_POST['csrf'])==1) {
            $order = ORM::factory('qwt_wfborder');
            $order->values($_POST['data']);
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item
            $oid = ORM::factory('qwt_wfbitem')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            $method = 'youzan.ump.coupon.take';
            $params = [
                'coupon_group_id'=>$oid,
                'weixin_openid'=>$userobj->openid,
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            //成功
            if ($results['response']) {
                //减库存
                $order->status = 1;
                $order->save();
                $item->stock--;
                $item->save();
                // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                // $wx = new Wechat($config);
                require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
                $options['token'] = $this->token;
                $options['encodingaeskey'] = $this->encodingAesKey;
                $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
                $wx = new Wxoauth($bid,$options);
                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的有赞优惠券优惠码已下发，请在会员中心查看！';
                $wx->sendCustomMessage($msg);
                //扣积分
                $userobj = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $sid=$userobj->scores->scoreOut($userobj, 4, $order->score);
                if($config['switch']==1){
                    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                    $smfy=new SmfyQwt();
                    $result=$smfy->wfbrsync($bid,$userobj->openid,$yzaccess_token,-$order->score,$sid,'兑换奖品6');
                }
                // if($this->config['switch']==1){
                //     $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                // }
                $goal_url = '/qwtwfb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }else{
                echo $results['error_response']['code'].$results['error_response']['msg'];
                exit;
            }
        }

        //自动填写旧地址
        $old_order = ORM::factory('qwt_wfborder')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location(){
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=WR5BZ-C4JWR-QC3WR-WRLZX-VOF35-P3BQO';
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
          $area = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwtwfbbid:', 'location');//写入日志，可以删除

        $wx = new Wxoauth($this->bid,$options);

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/wfb/check_location";

        $count = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('qwt_wfbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
    }
    // public function action_check_post() {//海报购买界面      不可删
    //     require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
    //     $client = new KdtApiOauthClient();
    //     $method = 'kdt.pay.qrcode.createQrCode';
    //     $params = [
    //         'qr_name' =>'支付即可生成海报',
    //         'qr_price' => $this->config['needpay'],
    //         'qr_type' => 'QR_TYPE_DYNAMIC',
    //     ];
    //     $test=$client->post($this->access_token,$method, $params);

    //     $postuser = ORM::factory('qwt_wfbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
    //     $postuser->needpost = $test['response']['qr_id'];
    //     $postuser->save();
    //     Request::instance()->redirect($test['response']['qr_url']);
    // }

    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/wfb/ticket";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwtwfbbid:', 'ticket');//写入日志，可以删除
        $wx = new Wxoauth($this->bid,$options);

        $jsapi = $wx->getJsSign($callback_url);
        $ticket = $wx->getJsCardTicket();
        $sign = $wx->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory($view)
                ->bind('cardId', $cardId)
                ->bind('jsapi', $jsapi)
                ->bind('ticket', $ticket)
                ->bind('sign', $sign);
    }

    //已兑换表单
    public function action_orders() {
        $view = "weixin/smfyun/wfb/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('qwt_wfborder')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->and_where_open()->where('need_money','=',0)->or_where_open()->where('need_money','>',0)->and_where('order_state','=','1')->or_where_close()->and_where_close()->find_all();
    }
    public function action_xiangqing(){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/smfyun/wfb/xiangqing";
        $obj = ORM::factory('qwt_wfbitem')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('qwt_wfbcfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('qwt_wfbscore')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        // $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('xiangqing', $items)->bind('dlimit',$dlimit);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_wfb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

     //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
      private function hongbao($config, $openid, $wx='', $bid=1, $money)
    {
        if (!$wx) {
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/qwt.inc');
            //require_once Kohana::find_file('vendor', "weixin/smfyun/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('qwtwfbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,$options);
        }
        $config['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->name;
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $options['appid'];//三方appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        //$data["min_value"] = $money; //最小金额
        //$data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        //$data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name'].""; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写

        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
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

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."wfb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();
        //$file_rootca = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wfbfile_rootca')->find();

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

        // if (!file_exists(rootca_file)) {
        //     @mkdir(dirname($rootca_file));
        //     @file_put_contents($rootca_file, $file_rootca->pic);
        // }

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
