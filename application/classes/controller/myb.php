<?php defined('SYSPATH') or die('No direct script access.');

//分销宝前台
//TODO $Id$
/*
*/

class Controller_Myb extends Controller_Base {
    public $template = 'weixin/myb/tpl/fftpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://dd.smfyun.com/myb/';
    var $we;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();

        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'cron') return;
        $_SESSION =& Session::instance()->as_array();
        $biz = ORM::factory('myb_login')->where('id','=',$_SESSION['myb']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime) < time()) die('您的账号已过期');
        $this->config = $_SESSION['myb']['config'];
        $this->openid = $_SESSION['myb']['openid'];
        $this->bid = $_SESSION['myb']['bid']?$_SESSION['myb']['bid']:$_SESSION['myba']['bid'];
        $this->uid = $_SESSION['myb']['uid'];
        $this->access_token = $_SESSION['myb']['access_token'];
        if ($_GET['debug']) print_r($_SESSION['myb']);
        //只能通过微信打开
        // if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['myba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        $user = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

         $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$this->openid'")->execute()->as_array();
         $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
         $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；

          for($i=0;$firstchild[$i];$i++)
          {
            $tempid[$i]=$firstchild[$i]['openid'];
          }


         if($this->config['kaiguan_needpay']==1)
         {
              $tempdata = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->find_all()->as_array();
              for($i=0;$tempdata[$i];$i++)
              {
                    $sort=$tempdata[$i]->as_array();
                    $tempiid[$i]=$sort['openid'];
              }
           }

        $customer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid','IN',$tempiid)->or_where('fopenid', '=', $this->openid)->order_by('paid', 'DESC');
        $user['follows'] =$customer->count_all();


        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();

        $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
        for($i=0;$firstchild[$i];$i++)
        {
            $tempid[$i]=$firstchild[$i]['openid'];
        }

        if($this->config['kaiguan_needpay']==1)
         {
              $tempdata = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->find_all()->as_array();
              for($i=0;$tempdata[$i];$i++)
              {
                    $sort=$tempdata[$i]->as_array();
                    $tempiid[$i]=$sort['openid'];
              }
        }

        $user['follows_month']=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid','IN',$tempiid)->or_where('fopenid', '=', $this->openid)->where('jointime','>=',$month)->count_all();
        $user['trades'] = ORM::factory('myb_score')->where('qid', '=', $user['id'])->where('type', 'IN', array(2,3))->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    // public function action_test(){// 静默授权
    //     $bid=1;
    //     $config=ORM::factory('myb_cfg')->getCfg($bid,1);
    //     require Kohana::find_file('vendor', 'weixin/wechat.class');
    //     $we = new Wechat($config);
    //     $ticket_lifetime = 3600*24*7;
    //     $time = time();
    //     $qrcode_type = 0;
    //     $result = $we->getQRCode($bid, $qrcode_type, $ticket_lifetime);
    //     echo "<pre>";
    //     var_dump($result);
    //     echo "</pre>";
    //     $qrurl = $we->getQRUrl($result['ticket']);
    //     echo "<pre>";
    //     var_dump($qrurl);
    //     echo "</pre>";
    //     exit;
    // }
    public function action_storefuop($bid){// 静默授权
        $bid=2;
        $config=ORM::factory('myb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/myb/index/'.$bid;
        $we = new Wechat($config);
        $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
        header("Location:$auth_url");
        exit;
    }
    //入口
    public function action_index($bid) {
        //只能通过微信打开
        $config=ORM::factory('myb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        $token = $we->getOauthAccessToken();
        $openid=$token['openid'];
        $userinfo=$we->getUserInfo($openid);
        // $userobj=ORM::factory('myb_qrcode')
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['myba']['bid']) return $this->action_msg('请通过微信打开！', 'warn');

        // $this->access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
        if (!$openid){
            $_SESSION['myb'] = NULL;
        }else{
            $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $userobj->values($userinfo);
            $userobj->bid = $bid;
            $userobj->ip = Request::$client_ip;
            $userobj->save();
            $_SESSION['myb']['config'] = $config;
            $_SESSION['myb']['openid'] = $openid;
            $_SESSION['myb']['bid'] = $bid;
            $_SESSION['myb']['uid'] = $userobj->id;
            Request::instance()->redirect('/myb/home');
        }
    }
    //Oauth 入口
    public function action_index_oauth($bid, $url='home') {
        $config = ORM::factory('myb_cfg')->getCfg($bid);

        //没有 Oauth 授权过才需要
        if ($config && !$_SESSION['myb']['openid2']) {

            require Kohana::find_file('vendor', 'weixin/wechat.class');
            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

            $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
            if (!$_GET['callback']) $callback_url .= "{$split}callback=1";

            $we = new Wechat(array('appid'=>$config['appid'], 'appsecret'=>$config['appsecret']));

            if (!$_GET['callback']) {
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            } else {
                $token = $we->getOauthAccessToken();
                $userinfo = $we->getOauthUserinfo($token['access_token'], $token['openid']);
                $openid = $userinfo['openid'];
            }

            if (!$openid) $_SESSION['myb'] = NULL;

            if ($openid) {

            }
        }


    }

    //默认页面
    public function action_home($openid='') {
        $view = "weixin/myb/home";
        // die('系统维护中...');
        $bid = $this->bid;

        if (!$this->uid&&$openid=='') {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }
        if($this->uid){
            $userobj = ORM::factory('myb_qrcode', $this->uid);
        }else{
            $userobj = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }

        $this->config = $_SESSION['myb']['config'] = ORM::factory('myb_cfg')->getCfg($bid,1);
        $this->openid = $_SESSION['myb']['openid'] = $userobj->openid;
        $this->bid = $_SESSION['myb']['bid'] = $this->bid;
        $this->uid = $_SESSION['myb']['uid'] = $userobj->id;
        //新用户关注收益
        // if ($this->config['money_init'] > 0 && ORM::factory('myb_score')->where('qid', '=', $this->uid)->where('type', '=', 6)->count_all() == 0) {
        //     $userobj->scores->scoreIn($userobj, 6, $this->config['money_init']/100);
        // }
        //可用积分
        $userobj->shscore = $result['shscore'] = $userobj->shscores->select(array('SUM("score")', 'total_score'))->find()->total_score;
        //已兑换积分
        $result['useshscore'] = abs($userobj->shscores->select(array('SUM("score")', 'total_score'))->where('score', '<', 0)->find()->total_score);
        //可转出金额
        $result['money_now'] = $userobj->scores->select(array('SUM("score")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
        //当前收益
        $result['score'] = $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
        //预计收益
        $userobj->money = $result['money'] = $userobj->scores->select(array('SUM("score")', 'total_score'))->where('score', '>', 0)->find()->total_score;
        //累计付款金额
        $userobj->paid = $result['paid'] = $userobj->scores->select(array('SUM("money")', 'money_paid'))->where('type', 'IN', array(2,3))->find()->money_paid;
         $result['aaa']=$this->config['title5'];
        if ($userobj->id) $userobj->save();
        $result['bname'] = ORM::factory('myb_login')->where('id','=',$bid)->find()->user;
        $result['openid'] = $userobj->openid;
        $this->template->title = '我的奖励';
        $this->template->content = View::factory($view)->bind('result', $result);
    }
    //结算信息设置
    public function action_account_set(){
        if(!$this->bid) die('页面已过期，请重试');
        $view = "weixin/myb/account_set";
        $this->template = 'tpl/blank';
        self::before();
        $user = ORM::factory('myb_qrcode')->where('bid','=',$this->bid)->where('id','=',$this->uid)->find();
        if ($_POST['form']) {
            $user->name=$_POST['form']['name'];
            $user->tel=$_POST['form']['tel'];
            $user->alipay_name=$_POST['form']['zfb'];
            $user->save();
            Request::instance()->redirect('/myb/home/'.$user->openid);
        }
        $this->template->content = View::factory($view)->bind('user',$user);
    }
    //兑换商城
    public function action_items() {
        $view = "weixin/myb/items";
        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('myb_qrcode', $this->uid);
        //总积分
        $userobj->shscore = $result['shscore'] = $userobj->shscores->select(array('SUM("score")', 'total_score'))->find()->total_score;
        if ($userobj->id) $userobj->save();
        $items = ORM::factory('myb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        $this->template->title = '兑换商城';
        $this->template->content = View::factory($view)->bind('userobj', $userobj)->bind('result', $result)->bind('items',$items);
    }
    //商品详情
    public function action_item($iid){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/myb/xiangqing";
        $item = ORM::factory('myb_item')->where('bid', '=', $this->bid)->where('id', '=', $iid)->find();
        $day_limit = ORM::factory('myb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('myb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('myb_shscore')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $user2 = ORM::factory('myb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->as_array();
        // $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('item', $item)->bind('dlimit',$dlimit)->bind('user2',$user2);
    }
    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/myb/ticket";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);

        $jsapi = $we->getJsSign($callback_url);
        $ticket = $we->getJsCardTicket();
        $sign = $we->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory($view)
                ->bind('cardId', $cardId)
                ->bind('jsapi', $jsapi)
                ->bind('ticket', $ticket)
                ->bind('sign', $sign);
    }
    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/myb/neworder";
        $config = $this->config;
        $bid = $this->bid;
        $this->access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }

        $item = ORM::factory('myb_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/myb/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->shscore) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->shscore} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('myb_shorder')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('myb_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('myb_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            //用是否生成海报判断下线数量 （生成海报不一定有积分） 下面计算的是 下线中 没有生成海报的数量
            //$count3 = ORM::factory('myb_qrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
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
        if($_POST['data'] && Security::check($_POST['csrf']) !== true) die('不合法');

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url&&$_POST['data']['type']!=5) &&Security::check($_POST['csrf'])==1) {
            $order = ORM::factory('myb_shorder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;

            //虚拟产品
            if ($item->url) {

                $order->status = 1;

                $url = substr($item->url , 0 , 4);
                if ($url == 'http'){
                    $order->url = $item->url;
                } else {
                    $order->url = '/myb/ticket/'.$item->url;
                }

            } else {
                //省份 城市
                $order->city = $_POST['s_province'].' '.$_POST['s_city'].' '.$_POST['s_dist'];
            }

            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();

                //扣积分
                $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                Kohana::$log->add("openid", print_r($userobj->openid, true));

                $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->shscores->scoreOut($userobj, 4, $order->score);
                if($config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/myb/shorders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //话费流量
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('myb_shorder');
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
                $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->shscores->scoreOut($userobj, 4, $order->score);
                if($config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/myb/shorders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //微信红包
        if ($_POST['data']['type']==4&&Security::check($_POST['csrf'])==1) {

            $order = ORM::factory('myb_shorder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;

            if($this->config['hb_check']==1){
               $order->status = 0;
               $order->save();
                //减库存
               $item->stock--;
               $item->save();
               $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

               $userobj->scores->scoreOut($userobj, 4, $order->score);
               $goal_url = '/myb/orders';
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $we = new Wechat($config);

                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的红包需要审核，审核通过后会自动下发，请耐心等待';
                $we->sendCustomMessage($msg);
                Request::instance()->redirect($goal_url);
                exit;
            }
                //发红包
                $tempname=ORM::factory("myb_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("myb_item")->where("id","=",$iid)->find()->price;
                $tempmoney=$tempmoney*100;

                //读取 用户 请求红包
                $mem = Cache::instance('memcache');
                $cache = $mem->get($this->openid.Request::$client_ip);
                if($cache) die('请勿重复刷红包');

                $hbresult = $this->hongbao($this->config, $this->openid, '', $tempname, $tempmoney);
                if($hbresult['result_code']=='SUCCESS')
                {
                    //成功
                   $order->save();

                    //减库存
                   $item->stock--;
                   $item->save();
                    //扣积分
                   $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                    $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                   $userobj->shscores->scoreOut($userobj, 4, $order->score);
                   if($config['switch']==1){
                        $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                    }
                   $goal_url = '/myb/shorders';
                   if ($order->url) $goal_url = $order->url;

                    //成功后跳转
                    Request::instance()->redirect($goal_url);

                }else{
                    echo $hbresult['return_msg'];
                    exit();
                }

        }

        //赠品
        if ($_POST['data']['type']==5){
            $order = ORM::factory('myb_shorder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item


            //gift
            //$wx['appid'] = ORM::factory('myb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appid')->find()->value;
            //$wx['appsecret'] = ORM::factory('myb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appsecert')->find()->value;
            $oid = ORM::factory('myb_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            $client = new YZTokenClient($this->access_token);

            // echo '赠品列表:<br><br><br>';
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [

            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            //Kohana::$log->add('weixin:giftresult:$this->bid', print_r($results, true));//写入日志，可以删除
            for($i=0;$results['response']['presents'][$i];$i++){
                $res = $results['response']['presents'][$i];
                $present_id=$res['present_id'];
                //echo 'present_id:'.$present_id.'<br>';
                if($present_id==$oid){//找到指定赠品
                    //根据openid获取userid
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                       'weixin_openid'=>$this->openid,
                       'fields'=>'user_id',
                    ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);
                    $user_id = $results['response']['user']['user_id'];
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'youzan.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $result1s = $client->post($method, $this->methodVersion, $params, $files);
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
                       $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                       $userobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                       $userobj->shscores->scoreOut($userobj, 4, $order->score);
                       if($config['switch']==1){
                            $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                        }
                       //echo "<script>alert('领取成功，请回到公众号主页查看！')</script>";
                        Request::instance()->redirect($result1s["response"]["receive_address"]);
                       //echo $results["response"]["receive_address"];
                       // exit;
                    }else{
                        echo "您已经兑换过该赠品，每个人只能领一次哦～";
                        exit;
                    }

                }
            }

        }

        //自动填写旧地址
        $old_order = ORM::factory('myb_shorder')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    //转出
    public function action_money($out=0, $cksum='') {
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $view = "weixin/myb/money";
        $userobj = ORM::factory('myb_qrcode', $this->uid);

        $title5=$this->config['title5'];
        $result['aaa']=$this->config['title5'];

        //可转出金额
        $result['money_now'] = $userobj->scores->select(array('SUM("score")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
        //已结算金额
        $result['money_paid'] = abs($userobj->scores->select(array('SUM("score")', 'money_paid'))->where('paydate', '<', time())->where('type', '=', 4)->find()->money_paid);
        //待结算金额
        $result['money_nopaid'] = $userobj->scores->select(array('SUM("score")', 'money_nopaid'))->where('paydate', '>=', time())->where('type', 'IN', array(1,2,3))->find()->money_nopaid;

        //判断转出条件
        $result['money_flag'] = false;
        $result['money_out'] = $this->config['money_out'];

        if($title5=='收益'){
            $title5="元";
        }


        if ($result['money_now'] >= $this->config['money_out']/100) {
            //判断成功购买金额
            $money_buy = $userobj->trades->select(array('SUM("money")', 'money_buy'))->where('status', '=', 'TRADE_BUYER_SIGNED')->find()->money_buy;

            if ($money_buy >= $this->config['money_out_buy']/100) {
                $result['money_flag'] = true;
            } else {
                $result['money_out_msg'] = '您需要成功消费满'. number_format($this->config['money_out_buy']/100, 2) .$title5.' 才能转出。';
            }
        } else {
            $result['money_out_msg'] = '满'. number_format($this->config['money_out']/100, 2) .$title5.'即可转出。';
        }

        //转出
        //只能提取整数
        $MONEY = floor($result['money_now']);
        $md5 = md5($this->openid.$this->config['appsecret'].$_GET['time'].$_GET['rand']);
        // echo "cks:$cksum<br />md5:$md5";
        if ( ($cksum == $md5) && (time() - $_GET['time'] < 600) ) $cksum_flag = true;

        if ($out == 1 && $cksum_flag == true && ($MONEY >= $this->config['money_out']/100) ) {
            if (!$this->config['partnerid'] || !$this->config['partnerkey']) die('ERRROR: Partnerid 和 Partnerkey 未配置，不能自动转出，请联系管理员！');

            $this->we = $we = new Wechat($this->config);
            $result_m = $this->sendMoney($userobj, $MONEY*100);

            if ($result_m['result_code'] == 'SUCCESS') {
                $userobj->scores->scoreOut($userobj, 4, $MONEY);

                $cksum = md5($userobj->openid.$this->config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $this->bid .'?url=score/3&cksum='. $cksum .'&openid='. base64_encode($userobj->openid);

                //发消息通知
                $fmsg = "申请转出{$MONEY} 元成功！请到微信钱包中查收。";
                if ($this->config['msg_money_tpl']) {
                    $this->sendMoneyMessage($userobj->openid, '转出成功', -$MONEY, $userobj->score, $url);
                } else {
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $userobj->openid;
                    $msg['text']['content'] = $fmsg;
                    $we->sendCustomMessage($msg);
                }

                $result['ok']++;
                $result['alert'] = '转出成功!';
                return $this->action_msg("转出成功，请到微信钱包中查收。", 'suc');

            } else {
                // print_r($result);exit;
                Kohana::$log->add("weixin_myb:$bid:money", print_r($result, true));
                $result['alert'] = '转出失败：'.$result_m['return_msg'];
            }
        }

        $this->template->title = '结算中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //积分排行榜
    public function action_top2() {
        $mem = Cache::instance('memcache');
        $view = "weixin/myb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '业绩排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('myb_qrcode', $this->uid)->as_array();

        $rankkey = "myb:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "myb:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //收益明细
    public function action_score($type=0) {
        $view = "weixin/myb/scores";
        $userobj = ORM::factory('myb_qrcode', $this->uid);

        $title = array('收支明细', '待结算', '已结算', '转出记录');

        $this->template->title = $title[$type];
        $this->template->content = View::factory($view)->bind('scores', $scores);

        $scores = $userobj->scores;

        if ($type == 1) $scores = $scores->where('type', 'IN', array(1,2,3))->where('paydate', '>', time());
        if ($type == 2) $scores = $scores->where('type', 'IN', array(1,2,3))->where('paydate', '<=', time());
        if ($type == 3) $scores = $scores->where('type', '=', 4);

        $scores = $scores->order_by('lastupdate', 'DESC')->limit(500)->find_all();
    }
    //积分明细
    public function action_shscore($type=0) {
        $view = "weixin/myb/shscore";
        $userobj = ORM::factory('myb_qrcode', $this->uid);

        $this->template->title = '积分明细';
        $this->template->content = View::factory($view)->bind('scores', $scores);

        $shscores = $userobj->shscores;

        $scores = $shscores->order_by('lastupdate', 'DESC')->limit(500)->find_all();
    }
    //积分明细
    public function action_shorders($type=0) {
        $view = "weixin/myb/shorders";
        $userobj = ORM::factory('myb_qrcode', $this->uid);
        $this->template->title = '我的奖励';
        $this->template->content = View::factory($view)->bind('orders', $orders)->bind('userobj', $userobj);
        $orders = ORM::factory('myb_shorder')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }

    //订单明细
    public function action_orders() {
        $view = "weixin/myb/orders";
        $userobj = ORM::factory('myb_qrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->scores->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }

    public function action_order($tid) {
        $view = "weixin/myb/order";

        $order = ORM::factory('myb_trade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }

    //排行榜
    public function action_top() {
        $view = 'weixin/myb/top';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('myb_qrcode', $this->uid);
        $top = $this->config['rank_myb'] ? $this->config['rank_myb'] : 10;

        $result['rank'] = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('id2', '>', 0)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('paid', '>', $user->paid)->count_all()+1;

        $usersobj = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('paid', 'DESC')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }

    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/myb/customer';
        $this->template->title = '累计客户';
        $this->template->content = View::factory($view)
        ->bind('config',$this->config)
        ->bind('mycustomers',$totlecustomer)//绑定所有用户（1,2,3）级
        ->bind('result', $result)
        ->bind('totlenum',$totlenum)
        ->bind('page',$pages)
        ->bind('pagenum',$page)
        ->bind('newadd',$newadd);
        //$this->template->content = View::factory($view)->bind('result', $result);

        $user = ORM::factory('myb_qrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$user->openid' and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$user->openid'")->execute()->as_array();


           $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
           $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；

           for($i=0;$firstchild[$i];$i++)
           {
            $tempid[$i]=$firstchild[$i]['openid'];
           }

           if($this->config['kaiguan_needpay']==1)
           {
              $tempdata = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->find_all()->as_array();
              for($i=0;$tempdata[$i];$i++)
              {
                    $sort=$tempdata[$i]->as_array();
                    $tempiid[$i]=$sort['openid'];
              }
           }




           if($newadd=='month')
           {
            $customer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', 'IN',$tempiid)->or_where('fopenid', '=', $user->openid)->where('jointime','>=',$month);
           }
           else
             $customer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->or_where('fopenid', 'IN',$tempiid);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/myb/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->or_where('fopenid', 'IN', $tempiid)->where('jointime','>=',$month)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->or_where('fopenid', 'IN', $tempiid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();


    }

    // public function action_newadd() {//本月新增
    //     $view = 'weixin/myb/newadd';

    //     $this->template->title = '累计客户';
    //     $this->template->content = View::factory($view)
    //     ->bind('mycustomers',$totlecustomer)
    //     ->bind('result', $result)
    //     ->bind('totlenum',$totlenum)
    //     ->bind('page',$pages)
    //     ->bind('pagenum',$page);
    //     //$this->template->content = View::factory($view)->bind('result', $result);

    //     $user = ORM::factory('myb_qrcode', $this->uid);

    //     $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$user->openid'")->execute()->as_array();
    //       $tempid=array();
    //         if($firstchild[0]['openid']==null)
    //         {
    //           $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
    //         }
    //         else
    //         {
    //           for($i=0;$firstchild[$i];$i++)
    //           {
    //             $tempid[$i]=$firstchild[$i]['openid'];
    //           }
    //         }
    //       $totlecustomer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid);
    //       $totlenum=$totlecustomer->count_all();
    //     //  $totlecustomer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->find_all();

    //          //分页
    //         $page = max($_GET['page'], 1);
    //         $offset = (500 * ($page - 1));

    //         $pages = Pagination::factory(array(
    //             'total_items'   => $totlenum,
    //             'items_per_page'=>500,
    //         ))->render('weixin/myb/admin/pages');
    //      $totlecustomer=ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
    // }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "myb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    //提示页面
    public function action_msg($msg, $type='suc') {
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/myb/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }
    // public function action_storefuop($bid){// 静默授权
    //     $config=ORM::factory('myb_cfg')->getCfg($bid,1);
    //     require Kohana::find_file('vendor', 'weixin/wechat.class');
    //     $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/myb/getopenid/'.$bid;
    //     $we = new Wechat($config);
    //     $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
    //     header("Location:$auth_url");
    //     exit;
    // }
    // public function action_getopenid($bid){//通过code获取openid
    //     $config=ORM::factory('myb_cfg')->getCfg($bid,1);
    //     require Kohana::find_file('vendor', 'weixin/wechat.class');
    //     $we = new Wechat($config);
    //     $token = $we->getOauthAccessToken();
    //     $openid=$token['openid'];
    //     echo $openid.'<br>';
    //     require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
    //     $access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
    //     $client = new YZTokenClient($access_token);
    //     $method = 'youzan.users.weixin.follower.get';
    //     $params = [
    //         'weixin_openid'=>$openid,
    //      ];
    //     $results = $client->post($method, $this->methodVersion, $params, $files);
    //     echo '<pre>';
    //     var_dump($results);
    //     echo '</pre>';
    //     $user = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
    //     if($results['response']['user']['sex']=='m'){
    //         $sex=1;//男
    //     }else if($results['response']['user']['sex']=='f'){
    //         $sex=2;//女
    //     }else{
    //         $sex=0;//人妖
    //     }
    //     $user->openid = $openid;
    //     $user->nickname = $results['response']['user']['nick'];
    //     if($user->subscribe!=1){//一旦关注为1 就不允许撤销
    //         $user->subscribe = $results['response']['user']['is_follow'];
    //     }
    //     $user->sex = $sex;
    //     $user->bid = $bid;
    //     $user->headimgurl = $results['response']['user']['avatar'];
    //     $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
    //     $user->save();

    //     $_SESSION['myb']['config'] = $config;
    //     $_SESSION['myb']['openid'] = $openid;
    //     $_SESSION['myb']['bid'] = $bid;
    //     $_SESSION['myb']['uid'] = $user->id;
    //     $_SESSION['myb']['access_token'] =$this->access_token;
    //     Request::instance()->redirect('/myb/commends/'.$openid.'/'.$bid);
    // }
    public function action_commends($mopenid,$bid){//奖品list分享页面
        $fopenid = $mopenid;
        $config=ORM::factory('myb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/myb/commends/'.$mopenid.'/'.$bid;
        $we = new Wechat($config);

        //$userobj = ORM::factory('myb_qrcode', $this->uid);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/myb/tpl/tpl2';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            }
            $user=ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $user->bid=$bid;
            $user->openid=$openid;
            $user->save();
            // echo $fopenid.'<br>';
            // echo $openid.'<br>';
            //exit;
            $user = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid){
                // echo '有上线';
                $status = 1;
                $result['title'] = $fuser->nickname.'的推荐商品';
            }else{
                $result['title'] = $fuser->nickname.'的推荐商品';
                // echo $user->id.'<br>';
                // echo $fuser->id.'<br>';
                if($fopenid != $openid&&$fuser->id < $user->id){//上线id大于本人id
                    $user->fopenid = $fopenid;
                    $user->save();
                    $status = 1;
                    //关系绑定之后 发送消息通知
                    $text = $fuser->nickname.'，恭喜你增加了一个新的支持者!';
                    if($config['coupontpl']){
                        $this->sendtplcoupon($fopenid,$config,$text,$we);
                    }else{
                        $msg['touser'] = $fopenid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        $we->sendCustomMessage($msg);
                    }
                }
            }
            $view = "weixin/myb/commendsother";//别人直接是url进，不需要加密
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                $result['title'] = $user->nickname.'的推荐商品';
                $view = "weixin/myb/commends";//自己进 跳入shareopenid 需要加密url
            }
            $this->template->title = $fuser->nickname.'推荐商品';
            $result['commends'] = ORM::factory('myb_setgood')->where('bid','=',$bid)->where('status','=',1)->find_all();
            $result['openid'] = $user->openid;

            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }
    }
    public function action_shareopenid($mopenid,$gid,$bid){//商品分享页面 自己可以打开 别人也可以打开
        $fopenid = base64_decode($mopenid);
        $config=ORM::factory('myb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/myb/shareopenid/'.$mopenid.'/'.$gid.'/'.$bid;
        $we = new Wechat($config);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/myb/tpl/tpl';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            }
            $user = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid){
                // echo '有上线';
                $status = 1;
            }else{
                $status = 1;
                if($fopenid != $openid&&$fuser->id < $user->id){
                    require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                    $access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
                    $client = new YZTokenClient($access_token);
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                        'weixin_openid'=>$openid,
                     ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);

                    if($results['response']['user']['sex']=='m'){
                        $sex=1;//男
                    }else if($results['response']['user']['sex']=='f'){
                        $sex=2;//女
                    }else{
                        $sex=0;//人妖
                    }
                    $user->nickname = $results['response']['user']['nick'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $sex;
                    $user->headimgurl = $results['response']['user']['avatar'];
                    $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->bid = $bid;
                    $user->openid = $openid;
                    $user->fopenid = $fopenid;
                    $user->save();
                    //关系绑定之后 发送消息通知
                    $text = $fuser->nickname.'，恭喜你增加了一个新的支持者!';
                    if($config['coupontpl']){
                        $this->sendtplcoupon($fopenid,$config,$text,$we);
                    }else{
                        $msg['touser'] = $fopenid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        $we->sendCustomMessage($msg);
                    }
                }
            }
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                // $result['content'] = '快将该商品推荐给你的好友吧';
            }
            $commend = ORM::factory('myb_setgood')->where('bid','=',$bid)->where('id','=',$gid)->find();
            $view = "weixin/myb/share";
            $this->template->title = $fuser->nickname.':'.$commend->title;
            $this->template->content = View::factory($view)->bind('status', $status)->bind('commend', $commend)->bind('result', $result);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }
    }
    public function action_test(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('result11', print_r($result11, true));
        $msg=$result11['msg'];
        $client_id='01d4e8941f3a8c7322';
        $client_secret='165b7748358bc9e71fb7035ee93a5758';
        $sign_string = $client_id."".$msg."".$client_secret;
        $sign = md5($sign_string);
        if($sign != $result11['sign']){
            exit();
        }else{
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $type=$result11['type'];
        $kdt_id =$result11['kdt_id'];
        if($type=='TRADE_ORDER_STATE'){
            $status=$result11['status'];
            Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $bid = ORM::factory('myb_login')->where('shopid','=',$kdt_id)->find()->id;
            $this->bid=$bid;
            $this->config = $config = ORM::factory('myb_cfg')->getCfg($bid);
            $expiretime=ORM::factory('myb_login')->where('id', '=', $bid)->find()->expiretime;
            // if(strtotime($expiretime) < time()) die ('插件已过期');
            $this->we = $we = new Wechat($config);
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $this->access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
            if($this->access_token){
                $this->client =$client= new YZTokenClient($this->access_token);
            }else{
                Kohana::$log->add("myb:$bid:bname", print_r('有赞参数未填', true));
            }
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                $posttid=urldecode($msg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("myb:$bid", print_r($jsona, true));
                $tid=$jsona['tid'];
                $method = 'youzan.trade.get';
                $params = [
                    'with_childs'=>true,
                    'tid'=>$tid,
                ];
                $result = $client->post($method, $this->methodVersion, $params, $files);
                $trade=$result['response']['trade'];
                if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                    $this->tradeImport($trade, $bid, $client, $we, $config);
                } else {
                    $this->tradeImport($trade, $bid, $client, $we, $config);
                }
            }
        }

    }
    private function tradeImport($trade, $bid, $client, $we, $config) {
        // print_r($trade);exit;
        $tid = $trade['tid'];
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS','TRADE_CLOSED');
        // echo $trade['status']."<br>";
        // echo "<pre>";
        // var_dump($okstatus);
        // echo "</pre>";
        if (!in_array($trade['status'], $okstatus)) {
            echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($trade['status'], true));
        $myb_trade = ORM::factory('myb_trade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($myb_trade->id) {

            //更新订单状态
            if ($myb_trade->status != $trade['status']) {
                $myb_trade->status = $trade['status'];
                $myb_trade->save();
                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($trade['status'] == 'TRADE_CLOSED') ORM::factory('myb_score')->where('tid', '=', $myb_trade->id)->delete_all();
            if ($trade['refund_state'] != 'NO_REFUND') ORM::factory('myb_score')->where('tid', '=', $myb_trade->id)->delete_all();

            //echo "$tid pass.\n";
            return;
        }
        Kohana::$log->add('11111', '111111');
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['type'], true));
        if ($trade['type'] != 'FIXED') return;

        //男人袜不参与火种用户的商品
        if ($bid == 2) {
            foreach ($trade['orders'] as $od) {
                if ($od['item_id'] == 222975865 || $od['item_id'] == 226597275 || $od['item_id'] == 215414338 || $od['item_id'] == 237725901 || $od['item_id'] == 249641512) {
                    //echo "$tid noMoney pass.\n"; //恰型、太阳镜、套套、活动6C
                    $trade['payment'] -= $od['payment'];
                }
            }
        }
        Kohana::$log->add('payment', print_r($trade['payment'], true));
        //付款金额为 0
        if ($trade['payment'] <= 0) return;
        Kohana::$log->add('8888', '8888');
        Kohana::$log->add('fans_id', print_r($trade['fans_info']['fans_id'],true));
        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'fans_id'=>$trade['fans_info']['fans_id'],
        ];
        $result = $client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        //$userinfo = $this->youzanid2OpenID($trade['weixin_user_id'], $client);
        // print_r($userinfo);

        //只处理有下线的订单
        $myb_qrcode = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($myb_qrcode->id, true));
        if (!$myb_qrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }

        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['pay_time']);

        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $myb_qrcode->subscribe : $myb_qrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            //echo "$tid Time pass.\n";
            return;
        }

        $trade['qid'] = $myb_qrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        //计算返利金额
        Kohana::$log->add('8888', '8888');
        //某些特殊情况订单改价问题
         $ordersumpayment = 0;
         $trade['adjust_fee']['pay_change'];//订单改价
         $trade['adjust_fee']['post_change'];//邮费改价
         foreach ($trade['orders'] as $order) {
            $ordersumpayment = $ordersumpayment+$order['payment'];//计算出 各个商品花费价格
         }
        $money  = $trade['money'] = $trade['payment'] - $trade['post_fee'];//实付金额-改价后的邮费
        // echo 'postfee'.$trade['post_fee'].'<br>';
        // echo 'postch'.$trade['adjust_fee']['post_change'].'<br>';
        // var_dump($moeny);
        Kohana::$log->add('money', print_r($moeny,true));
        $average=$money/($money+$trade['discount_fee']);//权重
        // echo 'average';
        // var_dump($average);
        Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid){//有一级
            $rank=1;
            $ffuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            if($ffuser->fopenid){//有二级
                $rank=2;
                $fffuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
                if($fffuser->openid){//有三级
                    $rank=3;
                }
            }
        }
             $money0=$money1=$money2=$money3=0;
             // echo 'tradeorders';
             // var_dump($trade['orders']);
             Kohana::$log->add('trade[orders]', print_r($trade['orders'],true));
             foreach ($trade['orders'] as $order) {
                $tempmoney=($order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment))*$average;
                Kohana::$log->add('tempmoney', print_r($tempmoney,true));
                Kohana::$log->add('orderpayment', print_r($orderpayment,true));
                Kohana::$log->add('ordersumpayment', print_r($ordersumpayment,true));
                // echo 'tempmoney';
                // var_dump($tempmoney);
                $goodid=$order['item_id'];
                $goodidcof=ORM::factory('myb_setgood')->where('goodid','=',$goodid)->find();
                if($goodidcof->id)//用户单独配置了
                {
                    $money0=$money0+$tempmoney*$goodidcof->money0/100;
                    if($rank>=1) $money1=$money1+$tempmoney*$goodidcof->money1/100;
                    if($rank>=2) $money2=$money2+$tempmoney*$goodidcof->money2/100;
                    if($rank>=3&&$config['kaiguan_needpay']==1) $money3=$money3+$tempmoney*$goodidcof->money3/100;
                }
                else//没有配置就默认的数据
                {
                    $money0 =$money0+$tempmoney * $config['money0'] / 100; //自购
                    if($rank>=1) $money1 =$money1+$tempmoney * $config['money1'] / 100; //一级
                    if($rank>=2) $money2 =$money2+$tempmoney * $config['money2'] / 100; //二级
                    if($rank>=3&&$config['kaiguan_needpay']==1) $money3 =$money3+$tempmoney * $config['money3'] / 100; //三级
                }

             }

            $money0 = $trade['money0'] = number_format($money0, 2, '.', ''); //自购
            $money1 = $trade['money1'] = number_format($money1, 2, '.', ''); //一级
            $money2 = $trade['money2'] = number_format($money2, 2, '.', ''); //二级
            if($config['kaiguan_needpay']==1) $money3 = $trade['money3'] = number_format($money3, 2, '.', ''); //三级


        $myb_trade->values($trade);
        $myb_trade->save();
        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['item_id'];
            $num=$order['num'];
            $price=$order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment);
            $myb_order=ORM::factory('myb_order')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$myb_order->id)//跳过已导入的order
            {
                $myb_order->bid=$bid;
                $myb_order->tid=$tid;
                $myb_order->goodid=$goodid;
                $myb_order->title=$title;
                $myb_order->num=$num;
                $myb_order->price=$price;
                $myb_order->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('myb_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('myb_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('myb_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money0', print_r($money0, true));
        //自购返利
        if ($money0 > 0) {
            //echo "$tid money0:$money0 \n";
            Kohana::$log->add('money0', print_r($money0, true));
            $myb_qrcode->scores->scoreIn($myb_qrcode, 1, $money0, 0, $myb_trade->id);

            //发消息
            Kohana::$log->add('openid', print_r($myb_qrcode->openid, true));
            $msg['touser'] = $myb_qrcode->openid;
            $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
            Kohana::$log->add('cksum', print_r($cksum, true));
            $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

            $msg['text']['content'] = "恭喜您完成一笔订单！\n\n实付金额：$money {$title5}\n系统返利：$money0 {$title5}\n\n<a href=\"$url\">查看我的{$title5}明细</a>";
            Kohana::$log->add('msg_score_tpl', print_r($config['msg_score_tpl'],true));
            if ($config['msg_score_tpl']){
                $we_result = $this->sendScoreMessage($msg['touser'], '购买返利', $money0, $myb_qrcode->score, $url);
                Kohana::$log->add('ScoreMessage', print_r($we_result, true));
            }
            else{
                $we_result = $we->sendCustomMessage($msg);
                Kohana::$log->add('CustomMessage', print_r($we_result, true));
            }
        }

        //订单上线返利
        if ($money1 > 0) {
            $fuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $myb_qrcode->fopenid)->find();
            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                $fuser->scores->scoreIn($fuser, 2, $money1, $myb_qrcode->id, $myb_trade->id);

                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

                $msg['text']['content'] = "您推荐的{$config['title1']}「{$myb_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money1 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->score, $url);
                else
                    $we_result = $we->sendCustomMessage($msg);
            }
        }

        //订单上上线返利
        if ($money2 > 0 && $fuser->fopenid) {
            $ffuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            if ($ffuser->id) {
                //echo "$tid money2:$money2 \n";
                $ffuser->scores->scoreIn($ffuser, 3, $money2, $fuser->id, $myb_trade->id);

                //发消息
                $msg['touser'] = $ffuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

                $msg['text']['content'] = "您推荐的{$config['title2']}「{$myb_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money2 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友的好友购买返利', $money2, $ffuser->score, $url);
                else
                    $we_result = $we->sendCustomMessage($msg);
            }
        }
        //订单上上上线返利  三级返利
        if ($money3 > 0 && $ffuser->fopenid&&$config['kaiguan_needpay']==1) {
            $fffuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
            if ($fffuser->id) {
                //echo "$tid money3:$money3 \n";
                $fffuser->scores->scoreIn($fffuser, 3, $money3, $fuser->id, $myb_trade->id);

                //发消息
                $msg['touser'] = $fffuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

                $msg['text']['content'] = "您推荐的{$config['titlen3']}「{$myb_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money3 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友的好友的好友购买返利', $money3, $fffuser->score, $url);
                else
                    $we_result = $we->sendCustomMessage($msg);

            }
        }
        //TODO:更多级别返利

        //echo "$tid done.\n";
        flush();ob_flush();
    }

    // private function youzanid2OpenID($fansid, $client) {
    //     $method = 'youzan.users.weixin.follower.get';
    //     $params = array('user_id' => $fansid,);

    //     $result = $client->post($this->access_token,$method, $params);
    //     $user = $result['response']['user'];
    //     return $user;
    // }
    private function sendtplcoupon($openid,$config,$text,$we) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $config['coupontpl'];

        $tplmsg['data']['keyword1']['value'] = '有赞优惠卷';
        $tplmsg['data']['keyword1']['color'] = '#999999';

        $tplmsg['data']['remark']['value'] = $text;
        $tplmsg['data']['remark']['color'] = '#999999';
        return $we->sendTemplateMessage($tplmsg);
    }
    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔'.$this->config['title5'].'！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $title;

        $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#FF0000';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');

        $tplmsg['data']['keyword4']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword4']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_myb:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_myb:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
    }

    //账户余额通知模板：openid、类型、收益、总金额、网址
    private function sendMoneyMessage($openid, $title, $money, $total, $url) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_money_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = $title;
        $tplmsg['data']['first']['color'] = '#06bf04';

        $tplmsg['data']['keyword1']['value'] = ''.number_format($money, 2);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = '时间：'.date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';

        // Kohana::$log->add("weixin_myb:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
    }
    private function hongbao($config, $openid, $we='', $bid=1, $money)
    {
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $we = new Wechat($config);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //支付商户号
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
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
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
    //企业付款：https://pay.weixin.qq.com/wiki/doc/api/mch_pay.php?chapter=14_2
    private function sendMoney($userobj, $money) {
        $config = $this->config;
        $openid = $userobj->openid;

        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $this->we = $we = new Wechat($config);
        }

        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["mch_appid"] = $config['appid'];
        $data["mchid"] = $config['partnerid']; //商户号
        $data["nonce_str"] = $this->we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.$config['title5'].'转出';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->we->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_myb:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_myb:hongbaoresult', print_r($result, true));
        return $result;
    }
    private function rsync($bid,$openid,$access_token,$chscore){
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($access_token){
            $client = new YZTokenClient($access_token);
        }else{
            die('请在后台一键授权给有赞');
        }
        $qrcode=ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        Kohana::$log->add("mybbid", print_r($bid,true));
        Kohana::$log->add("mybopenid", print_r($openid,true));
        $result=$client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add("score", print_r($qrcode->shscore,true));
        $fans_id = $result['response']['user']['user_id'];
        if($qrcode->yz_score==0){
            $method = 'youzan.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->shscore,
            ];
            $a=$client->post($method, $this->methodVersion, $params, $files);
            Kohana::$log->add("result", print_r($a,true));
            $qrcode->yz_score=1;
            $qrcode->save();
            $qrcode=ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{
            Kohana::$log->add("myberror", 'aa');
            if($chscore>=0){
                $method = 'youzan.crm.customer.points.increase';
                $params =[
                'fans_id' => $fans_id,
                'points' => $chscore,
                ];
                $a=$client->post($method, $this->methodVersion, $params, $files);
            }else{
                $method = 'youzan.crm.customer.points.decrease';
                $params =[
                'fans_id' => $fans_id,
                'points' => -$chscore,
                ];
                $a=$client->post($method, $this->methodVersion, $params, $files);
            }
        }
        $method = 'youzan.crm.fans.points.get';
        $params =[
        'fans_id' => $fans_id,
        ];
        $results=$client->post($method, $this->methodVersion, $params, $files);

        $point = $results['response']['point'];
        Kohana::$log->add("mybpoint", print_r($point,true));
        Kohana::$log->add("mybscore", print_r($qrcode->shscore,true));
        if($point&&$point!=$qrcode->shscore){
            $score_change=$point-$qrcode->shscore;
            Kohana::$log->add("score_change", print_r($score_change,true));
            $qrcode->shscores->scoreIn($qrcode,6,$score_change);
        }
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."myb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."myb/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'myb_file_cert')->find();
        $file_key = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'myb_file_key')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        // Kohana::$log->add("weixin_myb:$bid:curl_post_ssl:cert_file", $cert_file);

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
