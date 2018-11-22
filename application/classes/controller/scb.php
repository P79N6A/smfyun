<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Scb extends Controller_Base {
    public $template = 'weixin/scb/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "scb";
        parent::before();

        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'scorersync') return;
        $_SESSION =& Session::instance()->as_array();
        if(!$_SESSION['scb']['openid']&&Request::instance()->action != 'index') die('访问错误哟');

        $this->config = $_SESSION['scb']['config'];
        $this->openid = $_SESSION['scb']['openid'];
        $this->bid = $_SESSION['scb']['bid'];
        $this->userobj = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $this->uid = $this->userobj->id;
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    //入口
    public function action_index($bid,$url='scoreshop') {
        $config = ORM::factory('scb_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor/kdt', 'lib/KdtRedirectApiClient');

        if(!isset($_GET['open_id'])){
            $appId = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'yz_appid')->find()->value;
            $appSecret = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'yz_appsecert')->find()->value;
            $client = new KdtRedirectApiClient($appId, $appSecret);
            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
            $client->redirect($callback_url, 'snsapi_userinfo');
        }else{
            $_SESSION['scb']['config'] = $config;
            $_SESSION['scb']['openid'] = $_GET['open_id'];
            $_SESSION['scb']['bid'] = $bid;

            $userobj = ORM::factory('scb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $_GET['open_id'])->find();
            $userobj->openid=$_GET['open_id'];
            $userobj->nickname = $_GET['nickname'];
            $userobj->headimgurl = $_GET['avatar'];
            $userobj->subscribe = $_GET['subscribe'];
            $userobj->sex = $_GET['sex'];
            $userobj->bid = $bid;
            $userobj->ip = Request::$client_ip;
            $userobj->save();
            Request::instance()->redirect('/scb/'.$url);
        }
    }
    public function action_scoreshop() {
        //$config = ORM::factory('scb_cfg')->getCfg($this->bid,1);
        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/scb/shop";
        $num=0;
        for ($i=1; $i <=4 ; $i++) {

            if(ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'ban'.$i)->find()->pic){
                $num++;
            }
        }
        $user = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $obj = ORM::factory('scb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $this->template->content = View::factory($view)->bind('config',$this->config)->bind('num',$num)->bind('bid',$this->bid)->bind('user',$user)->bind('items',$items);
        // echo 'openid:'.$this->openid.'<br>';
        // echo 'uid:'.$this->bid.'<br>';
        // echo 'appid:'.$this->config['appid'].'<br>';
        // echo 'bid:'.$this->bid;
    }
    public function action_scorersync($bid){
        echo $bid.'<br>';
        $this->bid=$bid;
        $this->config = ORM::factory('scb_cfg')->getCfg($bid,1);
        echo "11<br>";
        require_once Kohana::find_file("vendor/kdt","KdtApiClient");
        $config =$this->config;
        $bid = $this->bid;
        if(isset($config['switch'])){
            $yzappid = $config['yz_appid'];
            $yzappsecret =$config['yz_appsecert'];
            echo $yzappid.'<br>';
            echo $yzappsecret.'<br>';
            if(isset($yzappid)){
                $client = new KdtApiClient($yzappid, $yzappsecret);
            }else{
                Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
            }
            $method ='kdt.shop.basic.get';
            $params =[
            ];
            $result =$client->post($method,$params);
            $kdt_id = $result['response']['sid'];
            echo '店铺id:'.$kdt_id."<br>";
            $last_fans_id = 0;
            $last = 0;
            for($next=true;$next==true;$last=$last_fans_id){
                echo "aaaa.<br>";
                echo $last."<br>";
                $method ='kdt.users.weixin.followers.pull';
                $params =[
                'after_fans_id' =>$last,
                ];
                $result =$client->post($method,$params);
                $next = $result['response']['has_next'];
                echo $next."<br>";
                $last_fans_id = $result['response']['last_fans_id'];
                echo $last_fans_id."<br>";
                for($i=0;$result['response']['users'][$i];$i++){
                    echo "bbbb<br>";
                    $users = $result['response']['users'][$i];
                    $user_id = $users['user_id'];
                    $weixin_openid = $users['weixin_openid'];
                    $nick = $users['nick'];
                    echo $user_id."<br>";
                    echo $weixin_openid."<br>";
                    echo $nick."<br>";
                    $num = ORM::factory('scb_qrcode')->where('bid','=',$bid)->where('openid','=',$weixin_openid)->count_all();
                    if($num!=0){
                        $method = 'kdt.crm.fans.points.get';
                        $params =[
                        'fans_id' => $user_id,
                        ];
                        $results=$client->post($method,$params);
                        $point = $results['response']['point'];
                        if(!isset($point))  die('有赞接口请求失败！！');
                        echo "积分:".$point.'<br>';
                        $yzscore = ORM::factory('scb_qrcode')->where('bid','=', $bid)->where('openid','=',$weixin_openid)->find()->yz_score;
                        echo 'yzscore:'.$yzscore.'<br>';
                        $score = ORM::factory('scb_qrcode')->where('bid','=', $bid)->where('openid','=',$weixin_openid)->find()->score;
                        echo 'score:'.$score.'<br>';
                        $score_sum=($point*$config['switch'])+$score;
                        echo 'score_sum:'.$score_sum."<br>";
                        $score_details=ORM::factory('scb_score');
                        $scores = ORM::factory('scb_qrcode')->where('bid','=', $bid)->where('openid','=',$weixin_openid)->find();
                        //当表中有赞积分为零，拉取到的有赞积分不为零。1，第一次同步。2，积分消耗完了，然后重新获取到了积分。
                        if($yzscore==0){
                            $method = 'kdt.crm.fans.points.payin.get';
                            $params =[
                            'fans_id' => $user_id,
                            'kdt_id' => $kdt_id,
                            'points' => floor($score/$config['switch']),
                            ];
                            $a=$client->post($method,$params);
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
                            $a=$client->post($method,$params);
                            if($a['response']['is_success']=='true'){
                                $scores->yz_score=floor($score/$config['switch']);
                                $scores->save();
                            }
                        }
                        echo "<br><br>";
                    }
                }
            }
        }

    }

    //积分排行榜
    public function action_top() {
        if(!$_SESSION['scb']['openid']) Request::instance()->redirect('/scb/index/'.$bid);
        $mem = Cache::instance('memcache');
        $view = "weixin/scb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;



        //计算排名
        $user = ORM::factory('scb_qrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;


        if(isset($_POST['rank'])){//今日排名
            $user = ORM::factory('scb_qrcode', $this->uid)->as_array();

            $ranktoday = "scb:ranktoday:{$this->bid}:{$this->openid}:$top";
            //$mem->delete($ranktoday);
            $result['rank'] = $mem->get($ranktoday);

            $topday = "scb:toptoday:{$this->bid}:$top";
            //$mem->delete($topday);
            $users = $mem->get($topday);
            if (!$users) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT scb_qrcodes.nickname,scb_qrcodes.headimgurl,scb_scores.qid,sum(scb_scores.score) as score from scb_qrcodes , scb_scores where scb_qrcodes.id=scb_scores.qid and scb_scores.bid=582 and from_unixtime(scb_scores.lastupdate,'%Y-%m-%d')= '$today' group by scb_scores.qid order by score desc limit $top");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users[] = $userobj;
                    // $sql = DB::query(Database::SELECT,"SELECT nickname,headimgurl FROM scb_qrcodes where `id`=$userobj->qid");
                    // $qr = $sql->execute()->as_array();
                    // $users['nickname'] = $qr['nickname'];
                    // $users['headimgurl'] = $qr['headimgurl'];
                }
                $mem->set($topday, $users, 600);
            }
            if (!$result['rank']) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT scb_qrcodes.nickname,scb_qrcodes.headimgurl,scb_scores.qid,sum(scb_scores.score) as score from scb_qrcodes , scb_scores where scb_qrcodes.id=scb_scores.qid and scb_scores.bid=582 and from_unixtime(scb_scores.lastupdate,'%Y-%m-%d')= '$today' group by scb_scores.qid order by score desc ");
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
            $rankkey = "scb:rank3:{$this->bid}:{$this->openid}:$top";
            $result['rank'] = $mem->get($rankkey);
            if (!$result['rank']) {//全部排名
            $result['rank'] = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
            }

            $topkey = "scb:top3:{$this->bid}:$top";
            $users = $mem->get($topkey);
            if (!$users) {
                $usersobj = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
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
        $view = "weixin/scb/scores";

        $this->template->title = '我的'. $this->scorename;
        $this->template->content = View::factory($view)->bind('scores', $scores)->bind('scorename', $this->scorename);

        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('scb_qrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/scb/items";

        $obj = ORM::factory('scb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('scb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('scb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit);
        // $key = "scb:items:{$this->bid}";
        // $items = $mem->get($key);
        // if (!$items) {
        //     $obj = ORM::factory('scb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        //     foreach($obj as $i) $items[] = $i->as_array();
        //     $mem->set($key, $items, 600);
        // }
    }

    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/scb/neworder";
        $config = $this->config;
        $bid = $this->bid;
        require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
        if($config['yz_appid']){
            $client = new KdtApiClient($config['yz_appid'], $config['yz_appsecert']);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }

        $item = ORM::factory('scb_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/scb/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->score} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('scb_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('scb_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('scb_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            //用是否生成海报判断下线数量 （生成海报不一定有积分） 下面计算的是 下线中 没有生成海报的数量
            //$count3 = ORM::factory('scb_qrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
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
            $order = ORM::factory('scb_order');
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
                    $order->url = '/scb/ticket/'.$item->url;
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

                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/scb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //话费流量
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('scb_order');
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

                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/scb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //微信红包
        if ($_POST['data']['type']==4) {

            $order = ORM::factory('scb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;


                //发红包
                $tempname=ORM::factory("scb_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("scb_item")->where("id","=",$iid)->find()->price;
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

                   $userobj->scores->scoreOut($userobj, 4, $order->score);
                   $goal_url = '/scb/orders';
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
            $order = ORM::factory('scb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item


            //gift
            $wx['appid'] = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appid')->find()->value;
            $wx['appsecret'] = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appsecert')->find()->value;
            $oid = ORM::factory('scb_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            $client = new KdtApiClient($wx['appid'],$wx['appsecret']);

            // echo '赠品列表:<br><br><br>';
            $method = 'kdt.ump.presents.ongoing.all';
            $params = [

            ];
            $results = $client->post($method,$params);
            //Kohana::$log->add('weixin:giftresult', print_r($results, true));//写入日志，可以删除
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
                    $results = $client->post($method,$params);
                    $user_id = $results['response']['user']['user_id'];
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'kdt.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $results = $client->post($method,$params);
                    Kohana::$log->add('weixin:oid', print_r($oid, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:fans_id', print_r($user_id, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:gift', print_r($results, true));//写入日志，可以删除
                    if($results['response']['is_success']==true){
                        $order->status = 1;
                        $order->save();

                        //减库存
                       $item->stock--;
                       $item->save();
                        //扣积分
                       $userobj->scores->scoreOut($userobj, 4, $order->score);
                       //echo "<script>alert('领取成功，请回到公众号主页查看！')</script>";
                        Request::instance()->redirect($results["response"]["receive_address"]);
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
        $old_order = ORM::factory('scb_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location(){
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
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
          $area = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/scb/check_location";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $count = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('scb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $we->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
    }
    public function action_check_post() {//海报购买界面      不可删
        require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
        $client = new KdtApiClient($this->config['yz_appid'], $this->config['yz_appsecert']);
        $method = 'kdt.pay.qrcode.createQrCode';
        $params = [
            'qr_name' =>'支付即可生成海报',
            'qr_price' => $this->config['needpay'],
            'qr_type' => 'QR_TYPE_DYNAMIC',
        ];
        $test=$client->post($method, $params);
        $postuser = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $postuser->needpost = $test['response']['qr_id'];
        $postuser->save();
        Request::instance()->redirect($test['response']['qr_url']);
    }

    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/scb/ticket";
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
        $view = "weixin/scb/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('scb_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }
    public function action_xiangqing(){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/scb/xiangqing";
        $obj = ORM::factory('scb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('scb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('scb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
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
        $table = "scb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_banimages($key, $bid=1) {

        $pic = ORM::factory('scb_cfg')->where('bid','=',$bid)->where('key','=',$key)->find()->pic;
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
        //$data["min_value"] = $money; //最小金额
        //$data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name'].""; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写

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

        $cert_file = DOCROOT."scb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."scb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."scb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'scb_file_cert')->find();
        $file_key = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'scb_file_key')->find();
        $file_rootca = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'scb_file_rootca')->find();

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
