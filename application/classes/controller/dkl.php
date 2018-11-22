<?php defined('SYSPATH') or die('No direct script access.');

class Controller_dkl extends Controller_Base {
    public $template = 'weixin/dkl/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $tel;
    public $uid;
    public $client;
    public $scorename;
    public $access_token;
    public $methodVersion = '3.0.0';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        $_SESSION =& Session::instance()->as_array();
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'cookie2') return;
        if (Request::instance()->action == 'index_oauth') return;
        if (Request::instance()->action == 'verification') return;
        if (Request::instance()->action == 'clerk_apply') return;
        if (Request::instance()->action == 'veri_login') return;


        if (!$_GET['openid']) {
            if (!$_SESSION['dkl']['bid']) die('页面已过期。请重新点击相应菜单');
            if (!$_SESSION['dkl']['openid']) die('Access Deined..请重新点击相应菜单');
        }
        $biz = ORM::factory('dkl_login')->where('id','=',$_SESSION['dkl']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime) < time()) die('您的账号已过期');
        $this->config = $_SESSION['dkl']['config'];
        $this->openid = $_SESSION['dkl']['openid'];
        $this->bid = $_SESSION['dkl']['bid'];
        $this->uid = $_SESSION['dkl']['uid'];
        $this->access_token = $_SESSION['dkl']['access_token'];
        $sname = ORM::factory('dkl_cfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        if ($_GET['debug']) print_r($_SESSION['dkl']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['dkla']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }

    //入口
    public function action_index($bid) {
        $config = ORM::factory('dkl_cfg')->getCfg($bid);
        $this->access_token=ORM::factory('dkl_login')->where('id', '=', $bid)->find()->access_token;
        //echo '$access_token'.$this->access_token."<br>";

        if (!$_GET['openid']) $_SESSION['dkl'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            Kohana::$log->add('openid',print_r($openid,true));
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m-d'))) {
                $_SESSION['dkl'] = NULL;
                die('Access Deined!请重新点击相应菜单');
            }
            $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $userobj->ip = Request::$client_ip;
            $userobj->save();

            $_SESSION['dkl']['config'] = $config;
            $_SESSION['dkl']['openid'] = $openid;
            $_SESSION['dkl']['bid'] = $bid;
            $_SESSION['dkl']['uid'] = $userobj->id;
            $_SESSION['dkl']['access_token'] = $this->access_token;
            if ($bid == 2) {
                // print_r($_SESSION);exit;
            }

            Request::instance()->redirect('/dkl/'.$_GET['url']);
        }
    }
    //Oauth 入口
    public function action_index_oauth($bid, $url='verification') {
       $_SESSION =& Session::instance()->as_array();
       $config = ORM::factory('dkl_cfg')->getCfg($bid,1);
        Request::instance()->redirect('/dkl/'.$url);
    }
    public function action_verification($bid){
        $this->template = 'tpl/blank';
        self::before();
        if (Request::instance()->action != 'veri_login' && (!$_SESSION['dkl']['tel']||!$_SESSION['dkl']['bid'])) {
            header('location:/dkl/clerk_apply/'.$bid);
            exit;
        }
        $_SESSION['dkla']['bid']=$bid;
        $bid = $this->bid = $_SESSION['dkl']['bid'];
        $tel= $this->tel=$_SESSION['dkl']['tel'];
        $veri=ORM::factory('dkl_veri')->where('bid','=',$bid)->where('tel','=',$tel)->find();
        if($veri->flag!=1){
            header('location:/dkl/clerk_apply/'.$bid);
            exit;
        }
        $_SESSION['dkl']['tel']=$this->tel;
        $_SESSION['dkl']['bid']=$this->bid;
        $orders = ORM::factory('dkl_order')->where('bid', '=', $bid);
        $orders= $orders->reset(FALSE);
        $s='ssss';
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
        }
        $orders = $orders->where('tel', 'like', $s);
        $result['countall'] = $countall = $orders->count_all();
        $order2s = ORM::factory('dkl_order')->where('bid', '=', $bid)->where('vid','=',$veri->id)->where('status','=',1);
        $order2s= $order2s->reset(FALSE);
        $result['sort']='createdtime';
        if ($result['sort']){
            $orders = $orders->order_by($result['sort'], 'DESC');
            $order2s = $order2s->order_by('tag_time', 'DESC');
        }
        $result['orders'] = $orders->find_all();
        $result['order2s'] = $order2s->find_all();
        //核销动作
        if ($_POST['tel']) {
            $veri2=ORM::factory('dkl_order')->where('tel','=',$_POST['tel'])->where('status','=','0')->find_all();
            $num=ORM::factory('dkl_order')->where('tel','=',$_POST['tel'])->where('status','=','0')->count_all();
            $array = array();
            foreach ($veri2 as $k => $v) {
                // echo $k;
                $id=$v->id;
                $tel=$v->tel;
                $name=$v->qid;
                $good=$v->name;
                $time=date('m-d H:i',$v->createdtime);
                $arr=array('id'=>$id,'tel'=>$tel,'name'=>$name,'good'=>$good,'time'=>$time);
                $array[$k] = $arr;
            }
            $res = array('res'=>$array,'num'=>$num);
            echo json_encode($res);
            exit();
        }
        //hexiao
        if ($_POST['veri']) {
            $id = $_POST['veri'];
            $order_edit = ORM::factory('dkl_order')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($order_edit->id) {
                if($order_edit->item->type==4){
                    $tempname=ORM::factory("dkl_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("dkl_item")->where("id","=",$order_edit->iid)->find()->value1;
                    $openid = ORM::factory("dkl_qrcode")->where("id","=",$order_edit->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                    if($hbresult['result_code']=='SUCCESS'){
                        $order_edit->status=1;
                        $order_edit->vid=$veri->id;
                        $order_edit->tag_time=time();
                        $order_edit->save();
                    }
                }else{
                    $order_edit->status=1;
                    $order_edit->vid=$veri->id;
                    $order_edit->tag_time=time();
                    $order_edit->save();
                }
            }
            $res = array('status'=>'SUCCESS');
            echo json_encode($res);
            exit();
        }
        //hexiaojilu
        if ($_POST['rtel']) {
            $veri1=ORM::factory('dkl_order')->where('tel','=',$_POST['rtel'])->where('status','=','1')->where('vid','=',$veri->id)->find_all();
            $num=ORM::factory('dkl_order')->where('tel','=',$_POST['rtel'])->where('status','=','1')->where('vid','=',$veri->id)->count_all();
            $array = array();
            foreach ($veri1 as $k => $v) {
                // echo $k;
                $tel=$v->tel;
                $name=$v->qid;
                $good=$v->name;
                $time=date('m-d H:i',$v->createdtime);
                $ttime=date('m-d H:i',$v->tag_time);
                $arr=array('tel'=>$tel,'name'=>$name,'good'=>$good,'time'=>$time,'ttime'=>$ttime);
                $array[$k] = $arr;
            }
            $res = array('res'=>$array,'num'=>$num);
            echo json_encode($res);
            exit();
        }
        $view = "weixin/dkl/verification";
        $this->template->content = View::factory($view)->bind('bid',$bid)->bind('result', $result);
    }
    public function action_clerk_apply($bid){
        $this->template = 'tpl/blank';
        self::before();
        $_SESSION['dkl']['bid']=$bid;
        $view = "weixin/dkl/clerk_apply";
        if (!$_POST['form']['tel']) {
            $result['content']=1;
        }else{
            if (!preg_match("/^1[34578]\d{9}$/", $_POST['form']['tel'])) {
                $result['content']=4;
            }else{
                $telephone=$_POST['form']['tel'];
                $veri=ORM::factory('dkl_veri')->where('bid','=',$bid)->where('tel','=',$telephone)->find();
                if ($veri->id&&$veri->flag==1){
                    $result['content']=2;
                    $_SESSION['dkl']['tel']=$telephone;
                    Request::instance()->redirect('dkl/verification/'.$bid);
                }else{
                    if ($veri->id&&$veri->flag!=1) {
                        //登录成功
                        $result['content']=3;
                        $result['err']='您没有核销员资格';

                    }else{
                        $result['content']=3;
                        $result['err']="查不到手机号为{$telephone}的核销员!";
                    }
                }
            }
        }
        $this->template->content = View::factory($view)->bind('result',$result);
    }
    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/dkl/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;



        //计算排名
        $user = ORM::factory('dkl_qrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;


        if(isset($_POST['rank'])){//今日排名
            $user = ORM::factory('dkl_qrcode', $this->uid)->as_array();

            $ranktoday = "dkl:ranktoday:{$this->bid}:{$this->openid}:$top";
            //$mem->delete($ranktoday);
            $result['rank'] = $mem->get($ranktoday);

            $topday = "dkl:toptoday:{$this->bid}:$top";
            //$mem->delete($topday);
            $users = $mem->get($topday);
            if (!$users) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT dkl_qrcodes.nickname,dkl_qrcodes.headimgurl,dkl_scores.qid,sum(dkl_scores.score) as score from dkl_qrcodes , dkl_scores where dkl_qrcodes.id=dkl_scores.qid and dkl_scores.bid=582 and from_unixtime(dkl_scores.lastupdate,'%Y-%m-%d')= '$today' group by dkl_scores.qid order by score desc limit $top");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users[] = $userobj;
                    // $sql = DB::query(Database::SELECT,"SELECT nickname,headimgurl FROM dkl_qrcodes where `id`=$userobj->qid");
                    // $qr = $sql->execute()->as_array();
                    // $users['nickname'] = $qr['nickname'];
                    // $users['headimgurl'] = $qr['headimgurl'];
                }
                $mem->set($topday, $users, 600);
            }
            if (!$result['rank']) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT dkl_qrcodes.nickname,dkl_qrcodes.headimgurl,dkl_scores.qid,sum(dkl_scores.score) as score from dkl_qrcodes , dkl_scores where dkl_qrcodes.id=dkl_scores.qid and dkl_scores.bid=582 and from_unixtime(dkl_scores.lastupdate,'%Y-%m-%d')= '$today' group by dkl_scores.qid order by score desc ");
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
            $rankkey = "dkl:rank3:{$this->bid}:{$this->openid}:$top";
            $result['rank'] = $mem->get($rankkey);
            if (!$result['rank']) {//全部排名
            $result['rank'] = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
            }

            $topkey = "dkl:top3:{$this->bid}:$top";
            $users = $mem->get($topkey);
            if (!$users) {
                $usersobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
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
        $view = "weixin/dkl/scores";

        $this->template->title = '我的'. $this->scorename;
        $this->template->content = View::factory($view)->bind('scores', $scores)->bind('scorename', $this->scorename);

        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('dkl_qrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/dkl/items";

        $obj = ORM::factory('dkl_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('dkl_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('dkl_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('dkl_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit)->bind('scorename',$this->scorename);
        // $key = "dkl:items:{$this->bid}";
        // $items = $mem->get($key);
        // if (!$items) {
        //     $obj = ORM::factory('dkl_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        //     foreach($obj as $i) $items[] = $i->as_array();
        //     $mem->set($key, $items, 600);
        // }
    }

    public function action_item($iid){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/dkl/xiangqing";
        $item = ORM::factory('dkl_item')->where('bid', '=', $this->bid)->where('id', '=', $iid)->find();
        $day_limit = ORM::factory('dkl_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('dkl_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('dkl_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $user2 = ORM::factory('dkl_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->as_array();
        // $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('item', $item)->bind('dlimit',$dlimit)->bind('user2',$user2);
    }
    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/dkl/neworder";
        $config = $this->config;
        $bid = $this->bid;
        $this->access_token=ORM::factory('dkl_login')->where('id', '=', $bid)->find()->access_token;
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $this->client=$client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }

        $item = ORM::factory('dkl_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/dkl/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->score} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('dkl_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('dkl_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('dkl_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            if ($userobj->lock == 0 && $count2 >= $this->config['risk_level1'] & $count3 <= $this->config['risk_level2']) {
                $userobj->lock = 1;
                $userobj->save();

                if ($userobj->lock == 1) die('您的账号存在刷分现象，已被锁定。如果您确认是系统误判断，请联系客服解决。');
            }
        }

        $this->template->title = $item->name;
        if($_POST['data'] && Security::check($_POST['csrf']) !== true) die('不合法');
        //实物
        if ($_POST['data']['tel']) {
            $order = ORM::factory('dkl_order');
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
                $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);
                if($this->config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/dkl/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
        //微信卡劵
        if ($_POST['data']['type']==1 ) {
            $order = ORM::factory('dkl_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;
            $order->url = '/dkl/ticket/'.$item->value1;
            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();
                //扣积分
                $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);
                if($this->config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/dkl/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
        //有赞优惠券优惠码
        if ($_POST['data']['type']==2) {
            $order = ORM::factory('dkl_order');
            $order->values($_POST['data']);
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;
            $method = 'youzan.ump.coupon.take';
            $params = [
                'coupon_group_id'=>$item->value1,
                'weixin_openid'=>$userobj->openid,
            ];
            $results = $this->client->post($method, $this->methodVersion, $params, $files);
            //成功
            if ($results['response']) {
                //减库存
                $order->save();
                $item->stock--;
                $item->save();
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $we = new Wechat($config);

                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的有赞优惠券优惠码已下发，请在会员中心查看！';
                $we->sendCustomMessage($msg);
                //扣积分
                $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);
                if($this->config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/dkl/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }else{
                echo $results['error_response']['code'].$results['error_response']['msg'];
                exit;
            }
        }
        //特权商品
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('dkl_order');
            $order->values($_POST['data']);
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;
            $order->url = $item->url;
            $method = 'youzan.users.weixin.follower.tags.add';
            $params = [
                'tags'=> $item->value1,
                'weixin_openid'=>$userobj->openid,
            ];
            $results=$this->client->post($method, $this->methodVersion, $params, $files);
            //成功
            if ($results['response']) {
                //减库存
                $order->save();
                $item->stock--;
                $item->save();
                //扣积分
                $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);
                if($this->config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/dkl/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }else{
                echo $results['error_response']['code'].$results['error_response']['msg'];
                exit;
            }
        }
        //微信红包
        if ($_POST['data']['type']==4&&Security::check($_POST['csrf'])==1) {

            $order = ORM::factory('dkl_order');
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
                //扣积分
               $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

               $userobj->scores->scoreOut($userobj, 4, $order->score);
               if($this->config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
               $goal_url = '/dkl/orders';
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
                $tempname=ORM::factory("dkl_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("dkl_item")->where("id","=",$iid)->find()->value1;
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
                   $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                   $userobj->scores->scoreOut($userobj, 4, $order->score);
                   if($this->config['switch']==1){
                        $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                    }
                   $goal_url = '/dkl/orders';
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
            $order = ORM::factory('dkl_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item


            //gift
            //$wx['appid'] = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appid')->find()->value;
            //$wx['appsecret'] = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appsecert')->find()->value;
            $oid = ORM::factory('dkl_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->value1; //? iid
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
                    var_dump($result1s);
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
                       $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                       $userobj->scores->scoreOut($userobj, 4, $order->score);
                       if($this->config['switch']==1){
                            $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                        }
                        Request::instance()->redirect($result1s["response"]["receive_address"]);
                    }else{
                        echo $result1s['error_response']['code'].$result1s['error_response']['msg'];
                        exit;
                    }

                }
            }
            //exit();

        }
        //有赞会员卡
        if ($_POST['data']['type']==6 ) {
            $order = ORM::factory('dkl_order');
            $order->values($_POST['data']);
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;
            $order->url = $item->value1;
            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();
                //扣积分
                $userobj = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);
                if($this->config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                }
                $goal_url = '/dkl/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }else{
                echo $results['error_response']['code'].$results['error_response']['msg'];
                exit;
            }
        }
        //自动填写旧地址
        $old_order = ORM::factory('dkl_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location(){
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=7NZBZ-NIF3F-DBIJG-JQZUW-LLTDE-DEBBA';
          $ch = curl_init(); // 初始化一个 cURL 对象
          curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
          curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
          $res = curl_exec($ch); // 运行cURL，请求网页
          curl_close($ch); // 关闭一个curl会话
          $json_obj = json_decode($res, true);
          Kohana::$log->add('dkl:location', print_r($res, true));
          Kohana::$log->add('dkl:location', print_r($json_obj, true));
          //$nation = $json_obj['result']['address_component']['nation'];
          $province = $json_obj['result']['address_component']['province'];
          $city = $json_obj['result']['address_component']['city'];
          $disrict = $json_obj['result']['address_component']['district'];
          //$street = $json_obj['result']['address_component']['street'];
          $content = $province.$city.$disrict;
          echo $content;
          $area = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/dkl/check_location";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $count = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('dkl_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $we->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('callback_url', $callback_url)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
    }
    public function action_check_post() {//海报购买界面      不可删
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.pay.qrcode.create';
        $params = [
            'qr_name' =>'支付即可生成海报',
            'qr_price' => $this->config['needpay'],
            'qr_type' => 'QR_TYPE_DYNAMIC',
        ];
        $test=$client->post($method, $this->methodVersion, $params, $files);

        $postuser = ORM::factory('dkl_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $postuser->needpost = $test['response']['qr_id'];
        $postuser->save();
        Request::instance()->redirect($test['response']['qr_url']);
    }
    public function action_test(){
        //会员卡 name card_alias
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->access_token='1b2d41881aa83f56bfb80da6e0ae3372';
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.scrm.card.list';
        $params = [
        ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        $yzmembers=$results['response']['items'];
        echo '<pre>';
        var_dump($yzmembers);
        echo '</pre>';
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.users.weixin.follower.get';
        $params = [
           'weixin_openid'=>'oDt2QjpBI3pJLoWTMrdAukgDbQX8',
           'fields'=>'user_id',
        ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        $user_id = $results['response']['user']['user_id'];
        echo $user_id.'<br>';
        $method = 'youzan.scrm.customer.card.grant';
        $params = [
            'card_alias'=> '3nwlbppnii81xy',
            'fans_id'=>$user_id,
            'fans_type'=>1,
            'mobile' => '',
        ];
        $results=$client->post($method, $this->methodVersion, $params, $files);
        echo '<pre>';
        var_dump($results);
        echo '</pre>';
        exit();



    }
    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/dkl/ticket";
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

    //已兑换表单
    public function action_orders() {
        $view = "weixin/dkl/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('dkl_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }
    public function action_xiangqing(){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/dkl/xiangqing";
        $obj = ORM::factory('dkl_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('dkl_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('dkl_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('dkl_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
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
        $table = "dkl_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

     //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
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
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
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
    private function rsync($bid,$openid,$access_token,$chscore){
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($access_token){
            $client = new YZTokenClient($access_token);
        }else{
            die('请在后台一键授权给有赞');
        }
        $qrcode=ORM::factory('dkl_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        Kohana::$log->add("dklbid", print_r($bid,true));
        Kohana::$log->add("dklopenid", print_r($openid,true));
        $result=$client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add("score", print_r($qrcode->score,true));
        $fans_id = $result['response']['user']['user_id'];
        if($qrcode->yz_score==0){
            $method = 'youzan.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $a=$client->post($method, $this->methodVersion, $params, $files);
            Kohana::$log->add("result", print_r($a,true));
            $qrcode->yz_score=1;
            $qrcode->save();
            $qrcode=ORM::factory('dkl_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{
            Kohana::$log->add("dklerror", 'aa');
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
        Kohana::$log->add("dklpoint", print_r($point,true));
        Kohana::$log->add("dklscore", print_r($qrcode->score,true));
        if($point&&$point!=$qrcode->score){
            $score_change=$point-$qrcode->score;
            Kohana::$log->add("score_change", print_r($score_change,true));
            $qrcode->scores->scoreIn($qrcode,5,$score_change);
        }
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."dkl/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dkl/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."dkl/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('dkl_cfg')->where('bid', '=', $bid)->where('key', '=', 'dkl_file_cert')->find();
        $file_key = ORM::factory('dkl_cfg')->where('bid', '=', $bid)->where('key', '=', 'dkl_file_key')->find();
        $file_rootca = ORM::factory('dkl_cfg')->where('bid', '=', $bid)->where('key', '=', 'dkl_file_rootca')->find();

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

        if (!file_exists(rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
        }

        // Kohana::$log->add("weixin_dkl:$bid:curl_post_ssl:cert_file", $cert_file);

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
