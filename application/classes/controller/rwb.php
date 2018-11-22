<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rwb extends Controller_Base {
    public $template = 'weixin/rwb/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public $access_token;
    public $methodVersion = '3.0.0';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "rwb";
        parent::before();

        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'cookie2') return;
        if (Request::instance()->action == 'ticket') return;
        $_SESSION =& Session::instance()->as_array();

        if (!$_GET['openid']) {
            if (!$_SESSION['rwb']['bid']) die('页面已过期。请重新点击相应菜单');
            if (!$_SESSION['rwb']['openid']) die('Access Deined..请重新点击相应菜单');
        }
        $biz = ORM::factory('rwb_login')->where('id','=',$_SESSION['rwb']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime)+86400 < time()) die('您的账号已过期');
        $this->config = $_SESSION['rwb']['config'];
        $this->openid = $_SESSION['rwb']['openid'];
        $this->bid = $_SESSION['rwb']['bid'];
        $this->uid = $_SESSION['rwb']['uid'];
        $this->access_token = $_SESSION['rwb']['access_token'];
        if($this->config['switch']==1){
            require_once Kohana::find_file("vendor/kdt","KdtApiOauthClient");
            $client = new KdtApiOauthClient();
            $openid =$this->openid;
            $bid=$this->bid;
            $config=$this->config;
            $weixin_openid=$openid;
            $method='kdt.users.weixin.follower.get';
            $params=[
                'weixin_openid'=>$openid,
            ];
            $results=$client->post($this->access_token,$method,$params);
            $user_id = $results['response']['user']['user_id'];
            $method = 'kdt.crm.fans.points.get';
            $params =[
            'fans_id' => $user_id,
            ];
            $results=$client->post($this->access_token,$method,$params);
            $point = $results['response']['point'];
            if(isset($point)){
                //echo "积分:".$point.'<br>';
                $yzscore = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$openid)->find()->yz_score;
                //echo 'yzscore:'.$yzscore.'<br>';
                $score = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$openid)->find()->score;
                //echo 'score:'.$score.'<br>';
                $score_sum=($point*$config['switch'])+$score;
                //echo 'score_sum:'.$score_sum."<br>";
                $score_details=ORM::factory('rwb_score');
                $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$openid)->find();
                //当表中有赞积分为零，拉取到的有赞积分不为零。1，第一次同步。2，积分消耗完了，然后重新获取到了积分。
                if($yzscore==0){
                    $method = 'kdt.crm.fans.points.payin.get';
                    $params =[
                    'fans_id' => $user_id,
                    'kdt_id' => $kdt_id,
                    'points' => floor($score/$config['switch']),
                    ];
                    $a=$client->post($this->access_token,$method,$params);
                    if($a['response']['is_success']=='true'||$score==0){
                        if($point!=0){
                            $score_details->bid=$bid;
                            $score_details->qid=$scores->id;
                            $score_details->type=5;
                            $score_details->score=$point*$config['switch'];
                            $score_details->save();
                        }
                        $scores->score=$score_sum;
                        $scores->yz_score = floor($score_sum/$config['switch']);
                        $scores->save();
                    }
                }
                //当表中有赞积分不为零，肯定不是第一次同步
                if($yzscore!=0&&$point!=$yzscore){
                    $score_details->bid=$bid;
                    $score_details->qid=$scores->id;
                    if($point>$yzscore){
                        $score_details->type=5;
                    }else{
                        $score_details->type=6;
                    }
                    $score_details->score=($point-$yzscore)*$config['switch'];
                    $score_details->save();
                    $scores->score=$point*$config['switch'];
                    $scores->yz_score=$point;
                    $scores->save();
                }
                if($yzscore!=0&&$point==$yzscore&&$score!=$yzscore*$config['switch']){
                    if($score>$yzscore*$config['switch']){
                        $method = 'kdt.crm.fans.points.payin.get';
                        $params =[
                        'fans_id' => $user_id,
                        'kdt_id' => $kdt_id,
                        'points' => floor($score/$config['switch']-$yzscore),
                        ];
                    }else{
                        $method = 'kdt.crm.fans.points.payout.get';
                        $params =[
                        'fans_id' => $user_id,
                        'kdt_id' => $kdt_id,
                        'points' => floor($yzscore-$score/$config['switch']),
                        ];
                    }
                    $a=$client->post($this->access_token,$method,$params);
                    if($a['response']['is_success']=='true'){
                        $scores->yz_score=floor($score/$config['switch']);
                        $scores->save();

                    }
                }
            }
        }
        $sname = ORM::factory('rwb_cfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        if ($_GET['debug']) print_r($_SESSION['rwb']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }

    //入口
    public function action_index($bid) {
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        //echo '$access_token'.$this->access_token."<br>";

        if (!$_GET['openid']) $_SESSION['rwb'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);

            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m-d'))) {
                $_SESSION['rwb'] = NULL;
                die('Access Deined!请重新点击相应菜单');
            }
            $userobj = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $userobj->ip = Request::$client_ip;
            $userobj->save();

            $_SESSION['rwb']['config'] = $config;
            $_SESSION['rwb']['openid'] = $openid;
            $_SESSION['rwb']['bid'] = $bid;
            $_SESSION['rwb']['uid'] = $userobj->id;
            $_SESSION['rwb']['access_token'] = $this->access_token;
            if ($bid == 2) {
                // print_r($_SESSION);exit;
            }

            Request::instance()->redirect('/rwb/'.$_GET['url']);
        }
    }

    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/rwb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;



        //计算排名
        $user = ORM::factory('rwb_qrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;


        if(isset($_POST['rank'])){//今日排名
            $user = ORM::factory('rwb_qrcode', $this->uid)->as_array();

            $ranktoday = "rwb:ranktoday:{$this->bid}:{$this->openid}:$top";
            //$mem->delete($ranktoday);
            $result['rank'] = $mem->get($ranktoday);

            $topday = "rwb:toptoday:{$this->bid}:$top";
            //$mem->delete($topday);
            $users = $mem->get($topday);
            if (!$users) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT rwb_qrcodes.nickname,rwb_qrcodes.headimgurl,rwb_scores.qid,sum(rwb_scores.score) as score from rwb_qrcodes , rwb_scores where rwb_qrcodes.id=rwb_scores.qid and rwb_scores.bid=582 and from_unixtime(rwb_scores.lastupdate,'%Y-%m-%d')= '$today' group by rwb_scores.qid order by score desc limit $top");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users[] = $userobj;
                    // $sql = DB::query(Database::SELECT,"SELECT nickname,headimgurl FROM rwb_qrcodes where `id`=$userobj->qid");
                    // $qr = $sql->execute()->as_array();
                    // $users['nickname'] = $qr['nickname'];
                    // $users['headimgurl'] = $qr['headimgurl'];
                }
                $mem->set($topday, $users, 600);
            }
            if (!$result['rank']) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT rwb_qrcodes.nickname,rwb_qrcodes.headimgurl,rwb_scores.qid,sum(rwb_scores.score) as score from rwb_qrcodes , rwb_scores where rwb_qrcodes.id=rwb_scores.qid and rwb_scores.bid=582 and from_unixtime(rwb_scores.lastupdate,'%Y-%m-%d')= '$today' group by rwb_scores.qid order by score desc ");
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
            $rankkey = "rwb:rank3:{$this->bid}:{$this->openid}:$top";
            $result['rank'] = $mem->get($rankkey);
            if (!$result['rank']) {//全部排名
            $result['rank'] = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
            }

            $topkey = "rwb:top3:{$this->bid}:$top";
            $users = $mem->get($topkey);
            if (!$users) {
                $usersobj = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
                foreach ($usersobj as $userobj) {
                    $users[] = $userobj->as_array();
                }
                $mem->set($topkey, $users, 600);
            }
        }
        $this->template->title = $this->scorename.'排行榜';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);
    }

    //所有活动
    public function action_score() {
        $view = "weixin/rwb/scores";

        $this->template->title = '活动概况';
        $this->template->content = View::factory($view)->bind('tasks', $tasks);

        $tasks = ORM::factory('rwb_task')->where('bid','=',$this->bid)->find_all();
    }
    //单个活动
    public function action_task($tid) {
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/rwb/task";

        $user = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('rwb_record')->where('bid', '=', $this->bid)->where('tid', '=', $tid)->where('fqid', '=', $user['id'])->count_all();
        $prizes = ORM::factory('rwb_order')->where('bid', '=', $this->bid)->where('tid', '=', $tid)->where('qid', '=', $user['id'])->find_all();
        $title = '活动详情';
        $this->template->content = View::factory($view)->bind('tasks', $tasks)->bind('user', $user)->bind('prizes', $prizes)->bind('title', $title);
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/rwb/items";

        $obj = ORM::factory('rwb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('rwb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('rwb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('rwb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit)->bind('scorename',$this->scorename);
        // $key = "rwb:items:{$this->bid}";
        // $items = $mem->get($key);
        // if (!$items) {
        //     $obj = ORM::factory('rwb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        //     foreach($obj as $i) $items[] = $i->as_array();
        //     $mem->set($key, $items, 600);
        // }
    }

    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/rwb/neworder";
        $config = $this->config;
        $bid = $this->bid;
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        if($this->access_token){
            $client = new KdtApiOauthClient();
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }

        $item = ORM::factory('rwb_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/rwb/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->score} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('rwb_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('rwb_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('rwb_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            //用是否生成海报判断下线数量 （生成海报不一定有积分） 下面计算的是 下线中 没有生成海报的数量
            //$count3 = ORM::factory('rwb_qrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
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
            $order = ORM::factory('rwb_order');
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
                    $order->url = '/rwb/ticket/'.$item->url;
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
                if($config['switch']==1){
                    $method ='kdt.shop.basic.get';
                    $params =[
                    ];
                    $result =$client->post($this->access_token,$method,$params);
                    $kdt_id = $result['response']['sid'];
                    $method = 'kdt.users.weixin.follower.get';
                    $params =[
                    'weixin_openid'=>$this->openid,
                    ];
                    $result=$client->post($this->access_token,$method,$params);
                    //Kohana::$log->add("yz1", print_r($result, true));
                    $user_id = $result['response']['user']['user_id'];
                    $method = 'kdt.crm.fans.points.get';
                    $params =[
                    'fans_id' => $user_id,
                    ];
                    $results=$client->post($this->access_token,$method,$params);
                    //Kohana::$log->add("yz2", print_r($results, true));
                    $point = $results['response']['point'];
                    if(isset($point)){
                        $yzscore =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->yz_score;
                        $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                        $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                        $score_sum = $point*$config['switch'] + $score;
                        $score_details=ORM::factory('rwb_score');
                        //Kohana::$log->add("yz_score", print_r($yzscore, true));
                        if($yzscore==0){
                            $method = 'kdt.crm.fans.points.payin.get';
                            $params =[
                            'fans_id' => $user_id,
                            'kdt_id' => $kdt_id,
                            'points' => floor($score/$config['switch']),
                            ];
                            $a=$client->post($this->access_token,$method,$params);
                            if($a['response']['is_success']=='true'||$score==0){
                                if($point!=0){
                                    $score_details->bid=$bid;
                                    $score_details->qid=$scores->id;
                                    $score_details->type=5;
                                    $score_details->score=$point*$config['switch'];
                                    $score_details->save();
                                }
                                $scores->score=$score_sum;
                                $scores->yz_score = floor($score_sum/$config['switch']);
                                $scores->save();
                            }
                        }
                        //当表中有赞积分不为零，肯定不是第一次同步
                        if($yzscore!=0&&$point!=$yzscore){
                            $score_details->bid=$bid;
                            $score_details->qid=$scores->id;
                            if($point>$yzscore){
                                $score_details->type=5;
                            }else{
                                $score_details->type=6;
                            }
                            $score_details->score=($point-$yzscore)*$config['switch'];
                            $score_details->save();
                            $scores->score=$point*$config['switch'];
                            $scores->yz_score=$point;
                            $scores->save();
                        }
                        if($yzscore!=0&&$point==$yzscore&&$score!=$yzscore*$config['switch']){
                            if($score>$yzscore*$config['switch']){
                                $method = 'kdt.crm.fans.points.payin.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($score/$config['switch']-$yzscore),
                                ];
                            }else{
                                $method = 'kdt.crm.fans.points.payout.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($yzscore-$score/$config['switch']),
                                ];
                            }
                            $a=$client->post($this->access_token,$method,$params);
                            if($a['response']['is_success']=='true'){
                                $scores->yz_score=floor($score/$config['switch']);
                                $scores->save();
                            }
                        }
                        $method = 'kdt.crm.fans.points.payout.get';
                        $params =[
                        'fans_id' => $user_id,
                        'kdt_id' => $kdt_id,
                        'points' => floor($order->score/$config['switch']),
                        ];
                        $a=$client->post($this->access_token,$method,$params);
                        if($a['response']['is_success']=='true'){
                            $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                            $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                            //$scores->score=$score+$config['goal0'];
                            $scores->yz_score=floor(($score-$order->score)/$config['switch']);
                            $scores->save();
                        }
                        //Kohana::$log->add("yz4", print_r($a, true));
                    }
                }
                $userobj = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/rwb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //话费流量
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('rwb_order');
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
                if($config['switch']==1){
                    $method ='kdt.shop.basic.get';
                    $params =[
                    ];
                    $result =$client->post($this->access_token,$method,$params);
                    $kdt_id = $result['response']['sid'];
                    $method = 'kdt.users.weixin.follower.get';
                    $params =[
                    'weixin_openid'=>$this->openid,
                    ];
                    $result=$client->post($this->access_token,$method,$params);
                    //Kohana::$log->add("yz1", print_r($result, true));
                    $user_id = $result['response']['user']['user_id'];
                    $method = 'kdt.crm.fans.points.get';
                    $params =[
                    'fans_id' => $user_id,
                    ];
                    $results=$client->post($this->access_token,$method,$params);
                    //Kohana::$log->add("yz2", print_r($results, true));
                    $point = $results['response']['point'];
                    if(isset($point)){
                        $yzscore =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->yz_score;
                        $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                        $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                        $score_sum = $point*$config['switch'] + $score;
                        $score_details=ORM::factory('rwb_score');
                        //Kohana::$log->add("yz_score", print_r($yzscore, true));
                        if($yzscore==0){
                            $method = 'kdt.crm.fans.points.payin.get';
                            $params =[
                            'fans_id' => $user_id,
                            'kdt_id' => $kdt_id,
                            'points' => floor($score/$config['switch']),
                            ];
                            $a=$client->post($this->access_token,$method,$params);
                            if($a['response']['is_success']=='true'||$score==0){
                                if($point!=0){
                                    $score_details->bid=$bid;
                                    $score_details->qid=$scores->id;
                                    $score_details->type=5;
                                    $score_details->score=$point*$config['switch'];
                                    $score_details->save();
                                }
                                $scores->score=$score_sum;
                                $scores->yz_score = floor($score_sum/$config['switch']);
                                $scores->save();
                            }
                        }
                        //当表中有赞积分不为零，肯定不是第一次同步
                        if($yzscore!=0&&$point!=$yzscore){
                            $score_details->bid=$bid;
                            $score_details->qid=$scores->id;
                            if($point>$yzscore){
                                $score_details->type=5;
                            }else{
                                $score_details->type=6;
                            }
                            $score_details->score=($point-$yzscore)*$config['switch'];
                            $score_details->save();
                            $scores->score=$point*$config['switch'];
                            $scores->yz_score=$point;
                            $scores->save();
                        }
                        if($yzscore!=0&&$point==$yzscore&&$score!=$yzscore*$config['switch']){
                            if($score>$yzscore*$config['switch']){
                                $method = 'kdt.crm.fans.points.payin.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($score/$config['switch']-$yzscore),
                                ];
                            }else{
                                $method = 'kdt.crm.fans.points.payout.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($yzscore-$score/$config['switch']),
                                ];
                            }
                            $a=$client->post($this->access_token,$method,$params);
                            if($a['response']['is_success']=='true'){
                                $scores->yz_score=floor($score/$config['switch']);
                                $scores->save();
                            }
                        }
                        $method = 'kdt.crm.fans.points.payout.get';
                        $params =[
                        'fans_id' => $user_id,
                        'kdt_id' => $kdt_id,
                        'points' => floor($order->score/$config['switch']),
                        ];
                        $a=$client->post($this->access_token,$method,$params);
                        if($a['response']['is_success']=='true'){
                            $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                            //$scores->score=$score+$config['goal0'];
                            $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                            $scores->yz_score=floor(($score-$order->score)/$config['switch']);
                            $scores->save();
                        }
                        //Kohana::$log->add("yz4", print_r($a, true));
                    }
                }
                $userobj = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/rwb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //微信红包
        if ($_POST['data']['type']==4&&Security::check($_POST['csrf'])==1) {

            $order = ORM::factory('rwb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;


                //发红包
                $tempname=ORM::factory("rwb_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("rwb_item")->where("id","=",$iid)->find()->price;
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
                    if($config['switch']==1){
                        $method ='kdt.shop.basic.get';
                        $params =[
                        ];
                        $result =$client->post($this->access_token,$method,$params);
                        $kdt_id = $result['response']['sid'];
                        $method = 'kdt.users.weixin.follower.get';
                        $params =[
                        'weixin_openid'=>$this->openid,
                        ];
                        $result=$client->post($this->access_token,$method,$params);
                        //Kohana::$log->add("yz1", print_r($result, true));
                        $user_id = $result['response']['user']['user_id'];
                        $method = 'kdt.crm.fans.points.get';
                        $params =[
                        'fans_id' => $user_id,
                        ];
                        $results=$client->post($this->access_token,$method,$params);
                        //Kohana::$log->add("yz2", print_r($results, true));
                        $point = $results['response']['point'];
                        if(isset($point)){
                            $yzscore =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->yz_score;
                            $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                            $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                            $score_sum = $point*$config['switch'] + $score;
                            $score_details=ORM::factory('rwb_score');
                            //Kohana::$log->add("yz_score", print_r($yzscore, true));
                            if($yzscore==0){
                                $method = 'kdt.crm.fans.points.payin.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($score/$config['switch']),
                                ];
                                $a=$client->post($this->access_token,$method,$params);
                                if($a['response']['is_success']=='true'||$score==0){
                                    if($point!=0){
                                        $score_details->bid=$bid;
                                        $score_details->qid=$scores->id;
                                        $score_details->type=5;
                                        $score_details->score=$point*$config['switch'];
                                        $score_details->save();
                                    }
                                    $scores->score=$score_sum;
                                    $scores->yz_score = floor($score_sum/$config['switch']);
                                    $scores->save();
                                }
                            }
                            //当表中有赞积分不为零，肯定不是第一次同步
                            if($yzscore!=0&&$point!=$yzscore){
                                $score_details->bid=$bid;
                                $score_details->qid=$scores->id;
                                if($point>$yzscore){
                                    $score_details->type=5;
                                }else{
                                    $score_details->type=6;
                                }
                                $score_details->score=($point-$yzscore)*$config['switch'];
                                $score_details->save();
                                $scores->score=$point*$config['switch'];
                                $scores->yz_score=$point;
                                $scores->save();
                            }
                            if($yzscore!=0&&$point==$yzscore&&$score!=$yzscore*$config['switch']){
                                if($score>$yzscore*$config['switch']){
                                    $method = 'kdt.crm.fans.points.payin.get';
                                    $params =[
                                    'fans_id' => $user_id,
                                    'kdt_id' => $kdt_id,
                                    'points' => floor($score/$config['switch']-$yzscore),
                                    ];
                                }else{
                                    $method = 'kdt.crm.fans.points.payout.get';
                                    $params =[
                                    'fans_id' => $user_id,
                                    'kdt_id' => $kdt_id,
                                    'points' => floor($yzscore-$score/$config['switch']),
                                    ];
                                }
                                $a=$client->post($this->access_token,$method,$params);
                                if($a['response']['is_success']=='true'){
                                    $scores->yz_score=floor($score/$config['switch']);
                                    $scores->save();
                                }
                            }
                            $method = 'kdt.crm.fans.points.payout.get';
                            $params =[
                            'fans_id' => $user_id,
                            'kdt_id' => $kdt_id,
                            'points' => floor($order->score/$config['switch']),
                            ];
                            $a=$client->post($this->access_token,$method,$params);
                            if($a['response']['is_success']=='true'){
                                $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                                $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                                //$scores->score=$score+$config['goal0'];
                                $scores->yz_score=floor(($score-$order->score)/$config['switch']);
                                $scores->save();
                            }
                            //Kohana::$log->add("yz4", print_r($a, true));
                        }
                    }
                   $userobj = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                   $userobj->scores->scoreOut($userobj, 4, $order->score);
                   $goal_url = '/rwb/orders';
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
            $order = ORM::factory('rwb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item


            //gift
            //$wx['appid'] = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appid')->find()->value;
            //$wx['appsecret'] = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appsecert')->find()->value;
            $oid = ORM::factory('rwb_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            $client = new KdtApiOauthClient();

            // echo '赠品列表:<br><br><br>';
            $method = 'kdt.ump.presents.ongoing.all';
            $params = [

            ];
            $results = $client->post($this->access_token,$method,$params);
            //Kohana::$log->add('weixin:giftresult:$this->bid', print_r($results, true));//写入日志，可以删除
            for($i=0;$results['response']['presents'][$i];$i++){
                $res = $results['response']['presents'][$i];
                $present_id=$res['present_id'];
                //echo 'present_id:'.$present_id.'<br>';
                if($present_id==$oid){//找到指定赠品
                    //根据openid获取userid
                    $method = 'kdt.users.weixin.follower.get';
                    $params = [
                       'weixin_openid'=>$this->openid,
                       'fields'=>'user_id',
                    ];
                    $results = $client->post($this->access_token,$method,$params);
                    $user_id = $results['response']['user']['user_id'];
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'kdt.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $result1s = $client->post($this->access_token,$method,$params);
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
                       if($config['switch']==1){
                            $method ='kdt.shop.basic.get';
                            $params =[
                            ];
                            $result =$client->post($this->access_token,$method,$params);
                            $kdt_id = $result['response']['sid'];
                            $method = 'kdt.users.weixin.follower.get';
                            $params =[
                            'weixin_openid'=>$this->openid,
                            ];
                            $result=$client->post($this->access_token,$method,$params);
                            //Kohana::$log->add("yz1", print_r($result, true));
                            $user_id = $result['response']['user']['user_id'];
                            $method = 'kdt.crm.fans.points.get';
                            $params =[
                            'fans_id' => $user_id,
                            ];
                            $results=$client->post($this->access_token,$method,$params);
                            //Kohana::$log->add("yz2", print_r($results, true));
                            $point = $results['response']['point'];
                            if(isset($point)){
                                $yzscore =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->yz_score;
                                $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                                $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                                $score_sum = $point*$config['switch'] + $score;
                                $score_details=ORM::factory('rwb_score');
                                //Kohana::$log->add("yz_score", print_r($yzscore, true));
                                if($yzscore==0){
                                    $method = 'kdt.crm.fans.points.payin.get';
                                    $params =[
                                    'fans_id' => $user_id,
                                    'kdt_id' => $kdt_id,
                                    'points' => floor($score/$config['switch']),
                                    ];
                                    $a=$client->post($this->access_token,$method,$params);
                                    if($a['response']['is_success']=='true'||$score==0){
                                        if($point!=0){
                                            $score_details->bid=$bid;
                                            $score_details->qid=$scores->id;
                                            $score_details->type=5;
                                            $score_details->score=$point*$config['switch'];
                                            $score_details->save();
                                        }
                                        $scores->score=$score_sum;
                                        $scores->yz_score = floor($score_sum/$config['switch']);
                                        $scores->save();
                                    }
                                }
                                //当表中有赞积分不为零，肯定不是第一次同步
                                if($yzscore!=0&&$point!=$yzscore){
                                    $score_details->bid=$bid;
                                    $score_details->qid=$scores->id;
                                    if($point>$yzscore){
                                        $score_details->type=5;
                                    }else{
                                        $score_details->type=6;
                                    }
                                    $score_details->score=($point-$yzscore)*$config['switch'];
                                    $score_details->save();
                                    $scores->score=$point*$config['switch'];
                                    $scores->yz_score=$point;
                                    $scores->save();
                                }
                                if($yzscore!=0&&$point==$yzscore&&$score!=$yzscore*$config['switch']){
                                    if($score>$yzscore*$config['switch']){
                                        $method = 'kdt.crm.fans.points.payin.get';
                                        $params =[
                                        'fans_id' => $user_id,
                                        'kdt_id' => $kdt_id,
                                        'points' => floor($score/$config['switch']-$yzscore),
                                        ];
                                    }else{
                                        $method = 'kdt.crm.fans.points.payout.get';
                                        $params =[
                                        'fans_id' => $user_id,
                                        'kdt_id' => $kdt_id,
                                        'points' => floor($yzscore-$score/$config['switch']),
                                        ];
                                    }
                                    $a=$client->post($this->access_token,$method,$params);
                                    if($a['response']['is_success']=='true'){
                                        $scores->yz_score=floor($score/$config['switch']);
                                        $scores->save();
                                    }
                                }
                                $method = 'kdt.crm.fans.points.payout.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($order->score/$config['switch']),
                                ];
                                $a=$client->post($this->access_token,$method,$params);
                                if($a['response']['is_success']=='true'){
                                    $scores = ORM::factory('rwb_qrcode')->where('bid','=', $bid)->where('openid','=',$this->openid)->find();
                                    //$scores->score=$score+$config['goal0'];
                                    $score =ORM::factory('rwb_qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find()->score;
                                    $scores->yz_score=floor(($score-$order->score)/$config['switch']);
                                    $scores->save();
                                }
                                //Kohana::$log->add("yz4", print_r($a, true));
                            }
                        }
                       $userobj = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                       $userobj->scores->scoreOut($userobj, 4, $order->score);
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
        $old_order = ORM::factory('rwb_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location(){
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=G5LBZ-A7IRW-JYVRB-RWPFF-PRFLH-AVFPM';
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
          $area = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/rwb/check_location";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $count = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('rwb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $we->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
    }
    public function action_check_post() {//海报购买界面      不可删
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $client = new KdtApiOauthClient($this->access_token);
        $method = 'youzan.pay.qrcode.create';
        $params = [
            'qr_name' =>'支付即可生成海报',
            'qr_price' => $this->config['needpay'],
            'qr_type' => 'QR_TYPE_DYNAMIC',
        ];
        $test=$client->post($method, $this->methodVersion, $params, $files);

        $postuser = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $postuser->needpost = $test['response']['qr_id'];
        $postuser->save();
        Request::instance()->redirect($test['response']['qr_url']);
    }

    public function action_ticket($cardId,$bid) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/rwb/ticket";
        $this->config=$config = ORM::factory('rwb_cfg')->getCfg($bid,1);
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];
        Kohana::$log->add("bid", print_r($bid, true));
        Kohana::$log->add("appid", print_r($wx['appid'], true));
        Kohana::$log->add("appsecret", print_r($wx['appsecret'], true));
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);
        $ticket = $we->getJsCardTicket();
        $sign = $we->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));
        Kohana::$log->add("sign", print_r($sign, true));
        $this->template->content = View::factory($view)
            ->bind('cardId', $cardId)
            ->bind('jsapi', $jsapi)
            ->bind('ticket', $ticket)
            ->bind('sign', $sign);
    }

    //已兑换表单
    public function action_orders() {
        $view = "weixin/rwb/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('rwb_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }
    public function action_xiangqing(){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/rwb/xiangqing";
        $obj = ORM::factory('rwb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('rwb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('rwb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('rwb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
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
        $table = "rwb_$type";

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


    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

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

        if (!file_exists(rootca_file)) {
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
