<?php defined('SYSPATH') or die('No direct script access.');

class Controller_whb extends Controller_Base {
    public $template = 'weixin/whb/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $appId = 'wxd0b3a6ff48335255';
    public $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'weihongbao';
    public $scorename;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "whb";
        parent::before();

        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'cookie2') return;

        if (Request::instance()->action == 'component_access_token') return;
        if (Request::instance()->action == 'dcomponent_access_token') return;
        if (Request::instance()->action == 'access_token') return;
        if (Request::instance()->action == 'daccess_token') return;
        if (Request::instance()->action == 'sendcustommsg') return;
        if (Request::instance()->action == 'userinfo') return;
        $_SESSION =& Session::instance()->as_array();

        if (!$_GET['openid']) {
            if (!$_SESSION['whb']['bid']) die('页面已过期。请重新点击相应菜单');
            if (!$_SESSION['whb']['openid']) die('Access Deined..请重新点击相应菜单');
        }

        $this->config = $_SESSION['whb']['config'];
        $this->openid = $_SESSION['whb']['openid'];
        $this->bid = $_SESSION['whb']['bid'];
        $this->uid = $_SESSION['whb']['uid'];

        if ($_GET['debug']) print_r($_SESSION['whb']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        // $user['follows'] = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    public function action_userinfo($openid){ //用户信息
        $this->template = 'tpl/blank';
        self::before();
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth(1,'whb',$this->appId);
        //var_dump($wx->getUserInfo($openid));
        $userinfo = $wx->getUserInfo($openid);
        echo $userinfo['nickname'];
        echo $userinfo['headimgurl'];
        exit;
    }
    public function action_sendcustommsg($openid){// 客服接口
        $this->template = 'tpl/blank';
        self::before();
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth(1,'whb',$this->appId);
        $msg['touser'] = $openid;
        $msg['msgtype'] = 'text';
        $msg['text']['content'] = '1dwdwdadawdwa';
        var_dump($wx->sendCustomMessage($msg));
        exit;
    }
    public function action_dcomponent_access_token($appid){// 删除三方平台 ctoken
        $this->template = 'tpl/blank';
        self::before();
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$appid;
        $ctoken = $mem->delete($cachename1);
        var_dump($ctoken);
        $cachename2 ='expiretime'.$appid;
        $ctime = $mem->delete($cachename2);
        var_dump($ctime);
        exit;
    }
    public function action_component_access_token($appid){//读取 三方平台 ctoken
        $this->template = 'tpl/blank';
        self::before();
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$appid;
        $ctoken = $mem->get($cachename1);
        var_dump($ctoken);
        $cachename2 ='expiretime'.$appid;
        $ctime = $mem->get($cachename2);
        echo date('y-m-d H:i:s',$ctime);
        exit;
    }
    public function action_access_token($bid){//读取商户 token
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth($bid,'whb',$this->appId);
        $access_token = $wx->get_accesstoken();
        if($access_token){
            echo 'accesstoken:'.$access_token;
        }else{
            echo 'access_token已过期或不存在';
        }
        exit;
    }
     public function action_daccess_token($bid){//删除商户 token
        $this->template = 'tpl/blank';
        self::before();
        $mem = Cache::instance('memcache');
        $cachename1 ='whb.access_token'.$bid;
        $ctoken = $mem->delete($cachename1);
        var_dump($ctoken);
        exit;
    }
    //入口
    public function action_index($bid) {
        $config = ORM::factory('whb_cfg')->getCfg($bid);


        if (!$_GET['openid']) $_SESSION['whb'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);

            if ($_GET['cksum'] != md5($openid.date('Y-m-d'))) {
                $_SESSION['whb'] = NULL;
                die('Access Deined!请重新点击相应菜单');
            }
            $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $userobj->ip = Request::$client_ip;
            $userobj->save();

            $_SESSION['whb']['config'] = $config;
            $_SESSION['whb']['openid'] = $openid;
            $_SESSION['whb']['bid'] = $bid;
            $_SESSION['whb']['uid'] = $userobj->id;

            if ($bid == 2) {
                // print_r($_SESSION);exit;
            }

            Request::instance()->redirect('/whb/'.$_GET['url']);
        }
    }
    public function action_hongbao(){
        $this->template = 'tpl/blank';
        self::before();
        $user = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('id', '=', $this->uid)->find();
        if($user->from_qr){
            $scene_id = ORM::factory('whb_qr')->where('bid', '=', $this->bid)->where('id', '=', $user->from_qr)->find();
            if($scene_id->starttime>time()){
                $txtReply = '不好意思，活动于'.date('Y-m-d H:i:s',$scene_id->starttime).'正式开始哦~';
            }
            if($scene_id->endtime<time()){
                $txtReply = '不好意思，活动已经于'.date('Y-m-d H:i:s',$scene_id->endtime).'结束了哦~';
            }
        }
        $mem = Cache::instance('memcache');
        if($_GET['code']==true){
            require_once Kohana::find_file('vendor', 'code/wxsms');
            $sms = new WXSMS;
            $str = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
            $key = $this->bid.':'.$_POST['tel'];
            //验证一分钟之内ip不能重复发送
            $die_key = $_SERVER['REMOTE_ADDR'].':'.$this->bid;
            if($mem->get($die_key)){
                echo '一分钟内不能重复发送哦';
                exit;
            }
            if($_POST['tel']!=''){
                if($user->tel&&$user->tel!=$_POST['tel']){
                    echo '您已经和手机号：'.$user->tel.'绑定了并领取了红包哦！';
                    exit;
                }
                $tel = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->where('status','=',1)->find();
                if($tel->id){
                    echo '对不起您已经领取过红包了哦';
                }else{
                    if($mem->get($key)){
                        $str = $mem->get($key);//10分钟内点击 重复发送同一验证码
                    }else{
                        $mem->set($key, $str, 600);//10分钟外点击 发送新验证码
                    }
                    $content = '您好，您本次的验证码为：'.$str.',10分钟内有效！';
                    $result = $sms->Send($_POST['tel'],$content);
                    // var_dump($result);
                    if($result["desc"]==="提交成功"){
                        $user->msgid = $result['msgid'];
                        $user->status = 2;//表示短信成功发送
                        $user->save();
                        $mem->set($die_key, time()+60, 60);//1分钟
                        echo '验证码发送成功，请查收！';
                    }else{
                        echo $result['desc'];
                    }
                }
            }else{
                echo '手机号不能为空哦！';
            }
            exit;
        }
        if($_POST['hb']){
            if($_POST['hb']['tel'] && $_POST['hb']['code']){
                $mem = Cache::instance('memcache');
                $key = $this->bid.':'.$_POST['hb']['tel'];
                $str = $mem->get($key);
                if($str == $_POST['hb']['code']){
                    $user_tel = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('tel', '=', $_POST['hb']['tel'])->find();
                    if($user_tel->status!=1){//可以发送
                        $qr = ORM::factory('whb_qr')->where('bid','=',$this->bid)->where('id','=',$user->from_qr)->find();
                        if($qr->stock>0){
                            $tempmoney = rand($qr->minprice,$qr->maxprice);
                            $tempname=ORM::factory("whb_login")->where("id","=",$this->bid)->find()->user;
                            $end = $this->hongbao($this->config, $this->openid, '', $tempname, $tempmoney);
                            if($end['return_msg']==="发放成功"){
                                $user->status = 1;
                                $user->hbmoney = $tempmoney;
                                $user->tel = $_POST['hb']['tel'];
                                $user->save();

                                $qr->stock--;
                                $qr->save();

                                $order=ORM::factory('whb_order');
                                $order->bid = $this->bid;
                                $order->qid = $user->id;
                                $order->money = $tempmoney;
                                $order->tid = $end['mch_billno'];
                                $order->save();

                                $mem->delete($key);//删除验证码缓存
                                $result['error'] = '红包已经发送成功了，请查收哦！';
                            }
                            // var_dump($end);
                        }else{
                            $result['error'] = '红包库存不够了哦';
                        }
                    }else{
                        $result['error'] = '红包已经发送过了哦';
                    }
                }else{
                    $result['error'] = '验证码不正确哦';
                }
            }else{
                $result['error'] = '请填写完整哦';
            }
        }
        $die_key = $_SERVER['REMOTE_ADDR'].':'.$this->bid;
        if($mem->get($die_key)&&$mem->get($die_key)-time()>0){
            $lefttime = $mem->get($die_key) - time();
        }
        $view = "weixin/whb/hongbao";
        $user = ORM::factory('whb_qrcode', $this->uid)->find();
        $this->template->title = '领取红包';
        $this->template->content = View::factory($view)->bind('user', $user)->bind('result', $result)->bind('lefttime', $lefttime)->bind('txtReply', $txtReply);
    }
    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/whb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;



        //计算排名
        $user = ORM::factory('whb_qrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;


        if(isset($_POST['rank'])){//今日排名
            $user = ORM::factory('whb_qrcode', $this->uid)->as_array();

            $ranktoday = "whb:ranktoday:{$this->bid}:{$this->openid}:$top";
            //$mem->delete($ranktoday);
            $result['rank'] = $mem->get($ranktoday);

            $topday = "whb:toptoday:{$this->bid}:$top";
            //$mem->delete($topday);
            $users = $mem->get($topday);
            if (!$users) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT whb_qrcodes.nickname,whb_qrcodes.headimgurl,whb_scores.qid,sum(whb_scores.score) as score from whb_qrcodes , whb_scores where whb_qrcodes.id=whb_scores.qid and whb_scores.bid=582 and from_unixtime(whb_scores.lastupdate,'%Y-%m-%d')= '$today' group by whb_scores.qid order by score desc limit $top");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users[] = $userobj;
                    // $sql = DB::query(Database::SELECT,"SELECT nickname,headimgurl FROM whb_qrcodes where `id`=$userobj->qid");
                    // $qr = $sql->execute()->as_array();
                    // $users['nickname'] = $qr['nickname'];
                    // $users['headimgurl'] = $qr['headimgurl'];
                }
                $mem->set($topday, $users, 600);
            }
            if (!$result['rank']) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT whb_qrcodes.nickname,whb_qrcodes.headimgurl,whb_scores.qid,sum(whb_scores.score) as score from whb_qrcodes , whb_scores where whb_qrcodes.id=whb_scores.qid and whb_scores.bid=582 and from_unixtime(whb_scores.lastupdate,'%Y-%m-%d')= '$today' group by whb_scores.qid order by score desc ");
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
            $rankkey = "whb:rank3:{$this->bid}:{$this->openid}:$top";
            $result['rank'] = $mem->get($rankkey);
            if (!$result['rank']) {//全部排名
            $result['rank'] = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
            }

            $topkey = "whb:top3:{$this->bid}:$top";
            $users = $mem->get($topkey);
            if (!$users) {
                $usersobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
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
        $view = "weixin/whb/scores";

        $this->template->title = '我的'. $this->scorename;
        $this->template->content = View::factory($view)->bind('scores', $scores)->bind('scorename', $this->scorename);

        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('whb_qrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/whb/items";

        $obj = ORM::factory('whb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('whb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('whb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('whb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit);
        // $key = "whb:items:{$this->bid}";
        // $items = $mem->get($key);
        // if (!$items) {
        //     $obj = ORM::factory('whb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        //     foreach($obj as $i) $items[] = $i->as_array();
        //     $mem->set($key, $items, 600);
        // }
    }

    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/whb/neworder";
        $config = $this->config;
        $bid = $this->bid;

        $item = ORM::factory('whb_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/whb/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->score} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('whb_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('whb_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('whb_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            //用是否生成海报判断下线数量 （生成海报不一定有积分） 下面计算的是 下线中 没有生成海报的数量
            //$count3 = ORM::factory('whb_qrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
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

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url&&$_POST['data']['type']!=5) ) {
            $order = ORM::factory('whb_order');
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
                    $order->url = '/whb/ticket/'.$item->url;
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

                $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/whb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //话费流量
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('whb_order');
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

                $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/whb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //微信红包
        if ($_POST['data']['type']==4) {

            $order = ORM::factory('whb_order');
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
               $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

               $userobj->scores->scoreOut($userobj, 4, $order->score);
               $goal_url = '/whb/orders';
               require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');//配置文件

                $options['token'] = $this->token;
                $options['encodingaeskey'] = $this->encodingAesKey;
                $options['appid'] = ORM::factory('whb_login')->where('id','=',$this->bid)->find()->appid;
                $wx = new Wxoauth($this->bid,'whb',$this->appId,$options);
                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的红包需要审核，审核通过后会自动下发，请耐心等待';
                $wx->sendCustomMessage($msg);
                Request::instance()->redirect($goal_url);
                exit;
            }

                //发红包
                $tempname=ORM::factory("whb_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("whb_item")->where("id","=",$iid)->find()->price;
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

                   $userobj = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                   $userobj->scores->scoreOut($userobj, 4, $order->score);
                   $goal_url = '/whb/orders';
                   if ($order->url) $goal_url = $order->url;

                    //成功后跳转
                    Request::instance()->redirect($goal_url);

                }else{
                    echo $hbresult['return_msg'];
                    exit();
                }

        }


        //自动填写旧地址
        $old_order = ORM::factory('whb_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location(){
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=NLRBZ-N3RRX-WQM4J-TEUAI-KFQVZ-CQBQY';
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
          $area = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('whb_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('whbbid:', 'location');//写入日志，可以删除

        $wx = new Wxoauth($this->bid,'whb',$this->appId,$options);

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/whb/check_location";

        $count = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('whb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('callback_url', $callback_url)
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

    //     $postuser = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
    //     $postuser->needpost = $test['response']['qr_id'];
    //     $postuser->save();
    //     Request::instance()->redirect($test['response']['qr_url']);
    // }

    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/whb/ticket";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('whb_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('whbbid:', 'ticket');//写入日志，可以删除
        $wx = new Wxoauth($this->bid,'whb',$this->appId,$options);

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
        $view = "weixin/whb/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('whb_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }
    public function action_xiangqing(){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/whb/xiangqing";
        $obj = ORM::factory('whb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('whb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('whb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('whb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
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
        $table = "whb_$type";

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
            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('whb_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('whbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,'whb',$this->appId,$options);
        }

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
        $result['mch_billno']=$mch_billno;
        return $result;//hash数组
    }


    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."whb/tmp/$bid/cert.pem";
        $key_file = DOCROOT."whb/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."whb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_cert')->find();
        $file_key = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_key')->find();
        //$file_rootca = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_rootca')->find();

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
