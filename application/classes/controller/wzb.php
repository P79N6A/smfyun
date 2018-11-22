<?php
// use Ecs\Request\V20140526 as Ecs;
defined('SYSPATH') or die('No direct script access.');
require_once DOCROOT.'../GatewayWorker/vendor/autoload.php';
require_once DOCROOT.'../application/vendor/aliyun-oss-php-sdk/autoload.php';
use GatewayClient\Gateway;
use Green\Request\V20170112 as Green;
use OSS\OssClient;
class Controller_wzb extends Controller_Base {
    public $template = 'weixin/wzb/tpl/fftpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    public $appId = 'wxd0b3a6ff48335255';
    public $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'zhibo';

    var $baseurl = 'http://dd.smfyun.com/wzb/';
    var $wx;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'order_test') return;
        if (Request::instance()->action == 'push_order') return;
        if (Request::instance()->action == 'send_live_comment') return;
        if (Request::instance()->action == 'index_oauth') return;
        if (Request::instance()->action == 'aliyun') return;
        if (Request::instance()->action == 'oss') return;
        if (Request::instance()->action == 'SnapshotInfo') return;
        if (Request::instance()->action == 'jianhuang') return;
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'dbimages') return;
        if (Request::instance()->action == 'download_view') return;
        if (Request::instance()->action == 'live_status') return;
        if (Request::instance()->action == 'download') return;
        if (Request::instance()->action == 'sendsub') return;
        if (Request::instance()->action == 'domain_data') return;
        if (Request::instance()->action == 'stream_data') return;
        if (Request::instance()->action == 'isplay') return;
        if (Request::instance()->action == 'del_mem') return;
        if (Request::instance()->action == 'live') return;
        if (Request::instance()->action == 'memcache_type') return;
        if (Request::instance()->action == 'buy_good') return;
        // if (Request::instance()->action == 'sendhongbao') return;
        // if (Request::instance()->action == 'sweepstakes') return;
        $_SESSION =& Session::instance()->as_array();
        if (!$_GET['openid']) {
            if (!$_SESSION['wzb']['bid']) die('页面已过期。请重新点击相应菜单');
            if (!$_SESSION['wzb']['openid']) die('Access Deined..请重新点击相应菜单');
        }

        $biz = ORM::factory('wzb_login')->where('id','=',$_SESSION['wzb']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime)+86400 < time()) die('您的账号已过期');
        $this->config = $_SESSION['wzb']['config'];
        $this->openid = $_SESSION['wzb']['openid'];
        $this->bid = $_SESSION['wzb']['bid'];
        $this->uid = $_SESSION['wzb']['uid'];
        $this->sid = $_SESSION['wzb']['sid'];
        $this->access_token = $_SESSION['wzb']['access_token'];


        if ($_GET['debug']) print_r($_SESSION['wzb']);


        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['wzba']['bid']) die('请通过微信访问。');
    }

    public function after() {

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    // public function access_order_test(){
    //     $bid=2990;
    //     set_time_limit(0);
    //     $start_created = "2017-10-26 16:00:00";
    //     $end_created = "2017-10-26 21:00:00";
    //     $this->bid=$bid=2990;
    //     $this->access_token=ORM::factory('_login')->where('id', '=', $this->bid)->find()->access_token;
    //     $config=ORM::factory('flb_cfg')->getCfg($bid,1);
    //     require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
    //     if($this->access_token){
    //          $this->client = new KdtApiOauthClient();
    //     }else{
    //          echo "没有access_token";
    //     }
    //     for($pg=1,$next=true;$next==true;$pg++){
    //         $method = 'kdt.trades.sold.get';
    //         $params = [
    //          'page_size' =>100,
    //          'page_no' =>$pg ,
    //          'use_has_next'=>true,
    //          'status'=>TRADE_BUYER_SIGNED,
    //          'start_created'=>$start_created,
    //          'end_created'=>$end_created,
    //         ];
    //         $results = $this->client->post($this->access_token,$method,$params);
    //         $next = $results['response']['has_next'];
    //         for($i=0;$results['response']['trades'][$i];$i++){
    //             $res=$results['response']['trades'][$i];
    // }
    public function action_stream_data($bid){
        Kohana::$log->add('malv',print_r($_GET,true));
        $user = ORM::factory('wzb_login')->where('id','=',$bid)->find();
        $online_num = Gateway::getClientCountByGroup($user->shopid)+1;
        $live = ORM::factory('wzb_live')->where('bid','=',$bid)->where('end_time','=',0)->find();//找出未开播的
        $live->data = $live->data+$online_num*10*$_GET['BITRATE']/(8*1024);
        $live->save();
        echo $_GET['BITRATE'].'<br>';
        echo $online_num.'<br>';
        echo $live->data.'<br>';
        exit;
    }
    public function action_sweepstakes(){//大转盘
        // echo 0;
        if($_POST['sweepstakes']==1){
            // $bid = $this->bid = 1;
            // $openid = $this->openid = 'oDt2QjtTeio8l0dBl28SQGhcHSH4';
            $bid = $this->bid;
            $openid = $this->openid;
            if(!$bid) die('no bid');
            if(!$openid) die('no openid');
            $user = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            //判断有没有开播
            $post_data = array();
            // echo $result['sid'];
            $url = 'http://jfb.dev.smfyun.com/wzb/isplay/'.$bid;
            // var_dump($this->request_post($url, $post_data));
            $res = $this->request_post($url, $post_data);
            if($res=='false'){
                $result['state'] = 0;
                $result['content'] = '当前直播未开启哦！';
                echo json_encode($result);
                exit;
            }
            $live = ORM::factory('wzb_live')->where('bid','=',$bid)->where('start_time','>',0)->order_by('id','desc')->find();
            $config = ORM::factory('wzb_cfg')->getCfg($bid,1);
            $start = $live->start_time;
            $end = strtotime(date('Y-m-d',strtotime('+1 day')));
            $_has = ORM::factory('wzb_sweepstake')->where('bid','=',$bid)->where('qid','=',$user->id)->where('lastupdate','<',$end)->where('lastupdate','>',$start)->count_all();
            if($config['switch']==1){
                // echo 1;
                if(!$live->id){
                    $result['state'] = 0;
                    $result['content'] = '未找到正确的直播id，请在【直播分析】中';
                }
                if($_has>$config['times']){
                    $result['state'] = 0;
                    $result['content'] = '本次抽奖次数已耗尽';
                }else{
                    // echo 2;
                    $fail = array(2,4,6,8);
                    $success = array(1,3,5,7);
                    $rand = rand(1,100);
                    if($rand<=$config['probability']){
                        //type::1积分 2优惠券 3红包 4赠品
                        $prize1 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',1)->find();
                        $prize2 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',2)->find();
                        $prize3 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',3)->find();
                        $prize4 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',4)->find();
                        if($prize1->num==0&&$prize2->num==0&&$prize3->num==0&&$prize4->num==0){
                            //都没有库存了 直接是未中奖
                            $result['state'] = 2;
                            $result['content'] = '很遗憾，奖品已经都发放完了！';
                            $result['iid'] = $fail[rand(0,3)];
                        }else{
                            //必定中奖
                            $rand_prize = rand(1,4);
                            // $rand_prize =3;
                            $prize = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',$rand_prize)->find();
                            while ($prize->num==0) {
                                $rand_prize = rand(1,4);
                                $prize = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',$rand_prize)->find();
                            }
                            if($prize->type==3){//中了红包
                                // echo 3;
                                $result_api = $this->sendhongbao($bid,$openid,$prize->other);
                                if($result_api['result_code']=='SUCCESS'){
                                    $result['state'] = 1;
                                    $result['iid'] = $success[$prize->item-1];
                                    $result['type'] = 3;
                                    $result['num'] = $prize->other;
                                    $content = round($prize->other/100,2).'元现金红包';
                                }else{
                                    $result['state'] = 'error';
                                    $result['iid'] = $success[$prize->item-1];
                                    $result['type'] = 3;
                                    $result['error_response'] = $result_api['return_msg'];
                                }
                                // echo 4;
                            }else{//中了其他
                                require_once Kohana::find_file("vendor/youzan","YZTokenClient");
                                $this->access_token = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->yz_access_token;
                                $client = new YZTokenClient($this->access_token);
                                $method = 'youzan.users.weixin.follower.get';
                                $methodVersion = '3.0.0';
                                $params = [
                                    'weixin_openid' => $openid,
                                ];
                                $res = $client->post($method, $methodVersion, $params, $files);
                                $user_id = $res['response']['user']['user_id'];
                                if(!isset($user_id)){
                                    $result['state'] = 'error';
                                    $result['content'] = '有赞fans_id未获取到';
                                }else{
                                    switch ($prize->type) {
                                        case 1://增加积分
                                            # code...
                                            $enddata = $this->addpoint($bid,$user_id,$prize->other);
                                            // $enddata = json_decode($end,true);
                                            if($enddata['response']['is_success']==true){
                                                $result['state'] = 1;
                                                $result['iid'] = $success[$prize->item-1];
                                                $result['type'] = 1;
                                                $result['point'] = $prize->other;
                                                $content = $prize->other.'积分';
                                            }else{
                                                $result['state'] = 'error';
                                                $result['iid'] = $success[$prize->item-1];
                                                $result['error_response'] = $enddata['error_response']['msg'];
                                            }
                                            break;
                                        case 2://下发优惠券
                                            # code...
                                            $enddata = $this->sendcoupon($bid,$user_id,$prize->other);
                                            // $enddata = json_decode($end,true);
                                            // var_dump($enddata);
                                            if($enddata['response']['promocard']['detail_url']||$enddata['response']['promocode']['detail_url']){
                                                $result['state'] = 1;
                                                $result['iid'] = $success[$prize->item-1];
                                                $result['url'] = $enddata['response']['promocard']['detail_url']?$enddata['response']['promocard']['detail_url']:$enddata['response']['promocode']['detail_url'];
                                                $result['type'] = 2;
                                                $content = $enddata['response']['promocard']['title']?$enddata['response']['promocard']['title']:$enddata['response']['promocode']['title'];
                                            }else{
                                                $result['state'] = 'error';
                                                $result['iid'] = $success[$prize->item-1];
                                                $result['error_response'] = $enddata['error_response']['msg'];
                                            }
                                            break;
                                        case 4://下发赠品
                                            # code...
                                            $enddata = $this->sendgift($bid,$user_id,$prize->other);
                                            // $enddata = json_decode($end,true);
                                            if($enddata['response']['receive_address']){
                                                $result['state'] = 1;
                                                $result['iid'] = $success[$prize->item-1];
                                                $result['type'] = 4;
                                                $result['url'] = $enddata['response']['receive_address'];
                                                $content = $enddata['response']['present_name'];
                                            }else{
                                                $result['state'] = 'error';
                                                $result['iid'] = $success[$prize->item-1];
                                                $result['error_response'] = $enddata['error_response']['msg'];
                                            }
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }
                                    if($result['state'] == 1){
                                        $wzb_sweepstake = ORM::factory('wzb_sweepstake');
                                        $wzb_sweepstake->bid = $bid;
                                        $wzb_sweepstake->qid = $user->id;
                                        $wzb_sweepstake->state = 1;
                                        $wzb_sweepstake->iid = $prize->id;
                                        $wzb_sweepstake->lid = $live->id;
                                        $wzb_sweepstake->content = $content;
                                        $wzb_sweepstake->save();
                                        $prize->num = $prize->num-1;
                                        $prize->save();
                                    }
                                }
                            }
                        }
                    }else{
                        $result['state'] = 2;
                        $result['content'] = '很遗憾，没有中奖';
                        $result['iid'] = $fail[rand(0,3)];
                        $wzb_sweepstake = ORM::factory('wzb_sweepstake');
                        $wzb_sweepstake->bid = $bid;
                        $wzb_sweepstake->qid = $user->id;
                        $wzb_sweepstake->state = 2;
                        $wzb_sweepstake->iid = 0;
                        $wzb_sweepstake->lid = $live->id;
                        $wzb_sweepstake->save();
                    }
                }
            }else{
                $result['state'] = 0;
                $result['content'] = '幸运抽奖轮盘未开启';
            }
            echo json_encode($result);
            exit;
        }
    }

    public function action_isplay($bid){
        $shop = ORM::factory('wzb_login')->where('id','=',$bid)->find();
        $post_data = array(
          'bid' =>$bid
        );
        // echo $result['sid'];
        $url = 'http://jfb.dev.smfyun.com/wzb/aliyun?online=1';
        // var_dump($this->request_post($url, $post_data));
        $res = $this->request_post($url, $post_data);
        $result['onlines'] = json_decode($res,true);
        if($result['onlines']['OnlineInfo']['LiveStreamOnlineInfo']){
            $onliens = $result['onlines']['OnlineInfo']['LiveStreamOnlineInfo'];
            for ($i=0; $onliens[$i]['StreamName'] ; $i++) {
                if($onliens[$i]['StreamName']==$bid){
                    echo 'true';
                    exit;
                }
            }
        }
        echo 'false';
        exit;
    }
    public function action_del_mem($name){
        $mem = Cache::instance('memcache');
        $mem->set($name, '', 0);
    }
    public function action_send_live_comment(){
        kohana::$log->add('post',print_r($_POST,true));
        // Gateway::$registerAddress = '127.0.0.1:5678';
        // // 假设用户已经登录，用户uid和群组id在session中
        // $uid      = $_POST['openid'];
        $group_id = $_POST['bid'];
        // $client_id = $_POST['client_id'];
        // // client_id与uid绑定
        // Gateway::bindUid($client_id, $uid);
        // // 加入某个群组（可调用多次加入多个群组）
        // Gateway::joinGroup($client_id, $group_id);
        // Gateway::setSession($client_id, array('group_id'=>$group_id));
        // 向任意uid的网站页面发送数据
        // Gateway::sendToUid($uid, $message);
        if($_POST['connect']==1){
            $res['status'] = 'connect';
            $res['msg'] = 'success';
            echo json_encode($res);
            exit;
        }
        if($_POST['login']){//comment
            // echo 'login';
            $this->bid = $bid = $_POST['bid'];
            $this->openid = $openid = $_POST['openid'];
            // $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
            // 推送的数据，包含uid字段，表示是给这个uid推送
            $user = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $mem = Cache::instance('memcache');
            // echo $openid;
            $rankkey = $bid.'_live_imgs';
            $imgs = $mem->get($rankkey);
            // var_dump($imgs);
            if($user->id){
                if(!$imgs){//为空
                    $imgs = array();
                    $imgs[0] = $user->id;
                    $mem->set($rankkey, $imgs, 24*3600);
                    // var_dump($imgs);
                }else{//有值
                    $num = sizeof($imgs);
                    if(!in_array($user->id,$imgs)){
                        if($num>=5){
                            for ($i=0; $i <$num-1 ; $i++) {
                                $imgs[$i] = $imgs[$i+1];
                            }
                        }
                        if($num>=5){
                            $imgs[$num-1] = $user->id;
                        }else{
                            $imgs[$num] = $user->id;
                        }
                    }
                    // var_dump($imgs);
                    // echo $i;
                    $mem->set($rankkey, $imgs, 24*3600);
                }
                for ($i=0; $imgs[$i] ; $i++) {
                    $img[$i] = ORM::factory('wzb_qrcode')->where('id','=',$imgs[$i])->find()->headimgurl;
                }
                echo json_encode($img);
            }

            exit;
        }
        if($_POST['scid']){//comment
            $this->bid = $bid = $_POST['bid'];
            $this->openid = $openid = $_POST['openid'];
            // $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
            // 推送的数据，包含uid字段，表示是给这个uid推送
            if($_POST['scid']=='admin'){
                $shop = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
                $user->headimgurl = $shop->logo;
                $user->nickname = $shop->name;
                $user->sex = 1;
            }else{
                $user = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            }
            if($user->lock==1){//被禁言
                $content = array('status' => 'locked' );
                echo json_encode($content);
                exit;
            }
            $content = '{"avatar": "'.$user->headimgurl.'","content": "'.$_POST['comment'].'","createtime": 1494217319,"experience": 41,"iscontrol": 0,"level": 1,"memberid": 27532848,"mtype": 0,"nickname": "'.$user->nickname.'","sex": '.$user->sex.',"ts": 7941000,"ytypename": "普通","ytypevt": 0,"msg": "评论成功","result": 1,"status": "comment"}';
        }
        if($_POST['buy']==1){
            $this->bid = $bid = $_POST['bid'];
            $this->openid = $openid = $_POST['openid'];
            // $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
            // 推送的数据，包含uid字段，表示是给这个uid推送
            $cfg = ORM::factory('wzb_cfg')->where('bid','=',$bid)->where('key','=','buynum')->find();
            if(!$cfg->value||$cfg->value==0){
                $cfg->bid = $bid;
                $cfg->key = 'buynum';
                $cfg->value = 1;
                $cfg->lastupdate = time();
            }else{
                $cfg->value = $cfg->value+1;
            }
            $cfg->save();

            $user = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            if($user->nickname){
                $cfg2 = ORM::factory('wzb_cfg')->where('bid','=',$bid)->where('key','=','buyname')->find();
                $cfg2->bid = $bid;
                $cfg2->key = 'buyname';
                $cfg2->value = $user->nickname;
                $cfg2->lastupdate = time();
                $cfg2->save();
            }

            $content = '{"avatar": "'.$user->headimgurl.'","content": "'.$_POST['comment'].'","createtime": 1494217319,"experience": 41,"iscontrol": 0,"level": 1,"memberid": 27532848,"mtype": 0,"nickname": "'.$user->nickname.'","sex": '.$user->sex.',"ts": 7941000,"ytypename": "普通","ytypevt": 0,"msg": "购买路上","result": 1,"status": "buy","buynum": "'.$cfg->value.'"}';
        }
        if($_POST['sub']==1){
            $this->bid = $bid = $_POST['bid'];
            $this->openid = $openid = $_POST['openid'];
            // $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
            // 推送的数据，包含uid字段，表示是给这个uid推送
            $user = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $content = '{"avatar": "'.$user->headimgurl.'","content": "'.$_POST['comment'].'","createtime": 1494217319,"experience": 41,"iscontrol": 0,"level": 1,"memberid": 27532848,"mtype": 0,"nickname": "'.$user->nickname.'","sex": '.$user->sex.',"ts": 7941000,"ytypename": "普通","ytypevt": 0,"msg": "订阅了主播","result": 1,"status": "sub"}';
        }
        // 向任意群组的网站页面发送数据
        if($content){
            Gateway::sendToGroup($group_id, $content);
        }
        exit;
        // $data = array('uid'=>$sid, 'content'=>$content);
        // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
        // fwrite($client, json_encode($data)."\n");
        // 读取推送结果
        // echo fread($client, 8192);
    }
    public function action_buy(){
        if($_POST['bid']){
            $bid = $_POST['bid'];
            $cfg = ORM::factory('wzb_cfg')->where('bid','=',$bid)->where('key','=','buynum')->find();
        }
        if($_POST['buyadd']==1){
            if(!$cfg->value||$cfg->value==0||time()-$cfg->lastupdate>5*60){
                $cfg->bid = $bid;
                $cfg->key = 'buynum';
                $cfg->value = 1;
                $cfg->lastupdate = time();
            }else{
                $cfg->value = $cfg->value+1;
            }
            $cfg->save();
            $arr = array('status' => 'added');
            ob_flush();
            echo json_encode($arr);
            exit;
        }
        if($_POST['buyget']==1){
            if(time()-$cfg->lastupdate>5*60){
                $cfg->value=0;
                $cfg->save();
            }
            $arr = array('buynum' => $cfg->value);
            ob_flush();
            echo json_encode($arr);
            exit;
        }
    }

    //Oauth 入口
    public function action_index_oauth($bid, $url='live') {
       $_SESSION =& Session::instance()->as_array();
       $bid = ORM::factory('wzb_login')->where('id','=',$bid)->find()->id;
       if(!$bid){
        die('不合法');
       }
       $config = ORM::factory('wzb_cfg')->getCfg($bid,1);
        if ($config) {

            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('wzb_login')->where('id','=',$bid)->find()->appid;
            $wx = new Wxoauth($bid,'wzb',$this->appId,$options);
            if(!$_GET['callback_sn']){
                $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
                $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
                if (!$_GET['callback']) $callback .= $split."callback=1";
                if (!$_GET['callback']) {
                    $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
                    header("Location:$auth_url");exit;
                } else{
                    $token = $wx->sns_getOauthAccessToken();
                    $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
                    $openid = $userinfo['openid'];
                    $userinfo['lv'] = 0;
                }
            }
            $userobj = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();

            if(!$userobj->id||$_GET['callback_sn']){//插入新的
                $callback_sn = 'http://'.$_SERVER["HTTP_HOST"].'/wzb/index_oauth/'.$bid;
                if (!$_GET['callback_sn']) $callback_sn .= "?callback_sn=1";
                if (!$_GET['callback_sn']) {
                    $auth_url = $wx->sns_getOauthRedirect($callback_sn, '', 'snsapi_userinfo');
                    header("Location:$auth_url");exit;
                } else {
                    $token = $wx->sns_getOauthAccessToken();
                    $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
                    $openid = $userinfo['openid'];
                    $userinfo['lv'] = 0;
                    if($openid){
                        $userobj = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                        if(!$userobj->id){
                            $userobj->values($userinfo);
                            $userobj->bid = $bid;
                            $userobj->ip = Request::$client_ip;
                            $userobj->save();
                        }
                    }else{
                        $_SESSION['wzb'] = NULL;
                        die('获取用户信息失败！！！');
                    }
                }
            }
            if(!$openid){
                $_SESSION['wzb'] = NULL;
                die('获取用户信息失败');
            }
            $shop = ORM::factory('wzb_login')->where('id','=',$bid)->find();
            $_SESSION['wzb']['sid'] = $shop->shopid;
            $_SESSION['wzb']['config'] = $config;
            $_SESSION['wzb']['openid'] = $openid;
            $_SESSION['wzb']['bid'] = $bid;
            $_SESSION['wzb']['uid'] = $userobj->id;
            // if (!$_GET['callback']) {
            //     $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_userinfo');
            //     header("Location:$auth_url");exit;
            // } else {
            //     $token = $wx->sns_getOauthAccessToken();
            //     $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
            //     $openid = $userinfo['openid'];
            //     $userinfo['lv'] = 0;
            // }
            // if (!$openid) $_SESSION['wzb'] = NULL;

            // if ($openid) {
            //     $userobj = ORM::factory('wzb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            //     if(!$userobj->id){
            //         $userobj->values($userinfo);
            //         $userobj->bid = $bid;
            //         $userobj->ip = Request::$client_ip;
            //         $userobj->save();
            //     }
            //     $_SESSION['wzb']['sid'] = $sid;
            //     $_SESSION['wzb']['config'] = $config;
            //     $_SESSION['wzb']['openid'] = $openid;
            //     $_SESSION['wzb']['bid'] = $bid;
            //     $_SESSION['wzb']['uid'] = $userobj->id;
            // }
        }
        Request::instance()->redirect('/wzb/'.$url.'?bid='.$bid);
    }
    public function action_sub(){
        // echo $this->bid;
        // echo $this->openid;
        $user = ORM::factory('wzb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        $user->sub = 1;
        if($_POST['cancel']==1){
            $user->sub = 0;
        }
        $user->save();
        if($user->sub==1){
            $result['content'] = '您已经成功订阅';
        }else{
            $result['content'] = '您已经成功取消订阅';
        }
        $view = "weixin/wzb/sub";
        $this->template->content = View::factory($view)
                        ->bind('user', $user)
                        ->bind('result', $result);
    }
    public function action_SnapshotInfo(){
        // date_default_timezone_set("UTC");
        if($_GET['bid']) {
            // $sid = ORM::factory('wzb_login')->where('id','=',$_GET['bid'])->find()->shopid;
            require Kohana::find_file('vendor/aliyun_sdk', 'aliyun-php-sdk-core/Config');
            $iClientProfile = DefaultProfile::getProfile("cn-shanghai", "LTAIvJvaLwxAKeLd", "F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt");
            $client = new DefaultAcsClient($iClientProfile);
            $request = new live\Request\V20161101\DescribeLiveStreamSnapshotInfoRequest();
            $request->setDomainName("live.smfyun.com");
            $request->setAppName("AppName");
            $request->setStreamName($bid);
            // echo date('c', time());
            // echo gmdate("Y-m-d\TH:i:s\Z");
            // echo '<br>';
            // echo '2017-06-01T02:17:15Z';
            // exit;
            // echo gmdate("Y-m-d\TH:i:s\Z");
            $request->setStartTime(gmdate("Y-m-d\TH:i:s\Z",time()-10));
            $request->setEndTime(gmdate("Y-m-d\TH:i:s\Z"));
            // $request->setStartTime('2017-06-01T00:00:00Z');
            // $request->setEndTime('2017-06-01T20:00:00Z');
            $request->setLimit(1);
            $response = $client->getAcsResponse($request);
            echo json_encode($response);
            exit;
        }
    }
    public function action_oss(){
        if($_POST['object_name']){
            $accessKeyId = "LTAIvJvaLwxAKeLd"; ;
            $accessKeySecret = "F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt";
            $endpoint = "oss-cn-shanghai.aliyuncs.com";
            require_once Kohana::find_file('vendor/aliyun-oss-php-sdk', 'src/OSS/OssClient');
            // require_once DOCROOT.'../application/vendor/aliyun-oss-php-sdk/src/OSS/OssClient.php';
            try {
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
                // $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                echo $ossClient->signUrl('smfyunliveyellow',$_POST['object_name']);
                // echo '<pre>';
                // var_dump($ossClient->signUrl('smfyunliveyellow','screen_shot/AppName/936565/1.jpg'));
                // var_dump('http://smfyunliveyellow.oss-cn-shanghai.aliyuncs.com/screen_shot/AppName/936565/1.jpg?Expires=1496303410&OSSAccessKeyId=TMP.AQH12_K365aNZiGanRDTfW49O0cRKJTre3UaGcUrhN037ZvMJ1AYeFzqUBzwMC4CFQCvbMIsYgv2ZgOnwcMLinwEIL58ywIVAOfh2-q4duBzf6V0apC_GAyMF1js&Signature=Nav43JcX4FJhXhYWFHVwI5iCPaY%3D');
            } catch (OssException $e) {
                print $e->getMessage();
            }
            exit;
        }
    }
    public function action_memcache_type($action,$key,$value=''){
        $mem = Cache::instance('memcache');
        if($action=='set'){
            $mem->set($key, $value, 3600);
            $res = $mem->get($key);
        }
        if($action=='get'){
            $res = $mem->get($key);
        }
        if($action=='del'){
            $mem->set($key, '', 60);
            $res = $mem->get($key);
        }
        echo '<pre>';
        var_dump($res);
        echo $res.$action;
        exit;
    }
    public function action_jianhuang($bid){
        if($_GET['BITRATE']==0){
            $echomsg='码率为0';
            $enddata = array('msg'=>'malv=0','echomsg'=>$echomsg);
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        Kohana::$log->add('jianhuang:',$bid);
        // $sid = ORM::factory('wzb_login')->where('id','=',$bid)->find()->shopid;
        $user = ORM::factory('wzb_login')->where('id','=',$bid)->find();
        $online_num = Gateway::getClientCountByGroup($bid);
        $live = ORM::factory('wzb_live')->where('bid','=',$bid)->where('end_time','=',0)->find();//找出未开播的
        if($live->id){
            if($_GET['BITRATE']>3000*1024) {
                Kohana::$log->add($bid.':malv:异常',print_r($_GET,true));
                $_GET['BITRATE'] = 1600*1024;
            }
            if($bid){
                $mem = Cache::instance('memcache');
                if($live->data==0){//一开始计算出以前所消耗流量
                    $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM wzb_lives where bid=$bid ");
                    $num = $sql->execute()->as_array();
                    $use_stream =  $num[0]['CT'];
                    $rankkey = $live->id.'_live_stream_data_use';
                    $mem->set($rankkey, $use_stream, 24*3600);
                    Kohana::$log->add($bid.':memset:',$use_stream);
                }else{
                   $rankkey = $live->id.'_live_stream_data_use';
                   $use_stream = $mem->get($rankkey);
                }
            }
            $live->data = $live->data+$online_num*15*$_GET['BITRATE']/(8);
            if($online_num>$live->max_num){
                $live->max_num = $online_num;
            }
            $live->save();
            if($bid){
                $all = ORM::factory('wzb_login')->where('id','=',$bid)->find()->stream_data;
                Kohana::$log->add($bid.':used0:',($live->data+$use_stream));
                if(($live->data+$online_num*15*$_GET['BITRATE']/(8)+$use_stream)>1024*1024*1024*$all){
                    $enddata = array('msg'=>'porn','echomsg'=>'');
                    $rtjson =json_encode($enddata);
                    echo $rtjson;
                    //echo 'porn';//消耗殆尽
                    Kohana::$log->add($bid.':used:',($live->data+$use_stream));
                    exit;
                }
                if(($live->data+$online_num*15*$_GET['BITRATE']/(8)+$use_stream)>0.9*1024*1024*1024*$all){
                    Kohana::$log->add($bid.':overing:',($live->data+$use_stream));
                    $residue=$all-(($live->data+$online_num*15*$_GET['BITRATE']/(8)+$use_stream)/(1024*1024*1024));
                    $echomsg='您的流量还剩下不到10%，仅剩余'.$residue.'G,请及时充值流量,否则直播将关闭！';
                    $enddata = array('msg'=>'overing','echomsg'=>$echomsg);
                    kohana::$log->add('get1',print_r($enddata,true));
                    $rtjson =json_encode($enddata);
                    echo $rtjson;
                    //echo 'overing';//快消耗殆尽
                    exit;
                }
            }
            Kohana::$log->add($bid.':malv:',print_r($_GET,true));
        }
        $echomsg='成功';
        $enddata = array('msg'=>'success','echomsg'=>$echomsg);
        $rtjson =json_encode($enddata);
        echo $rtjson;
        exit;
        // $content = '{"status": "porn_stop"}';
        // Gateway::sendToGroup($sid, $content);
        // echo $sid.$content;
        // exit;
        require Kohana::find_file('vendor/green-sdk-sample-doc-2017-01-12', 'green-sdk-sample/v2017-01-12/green-php-sdk-sample-v20170112/aliyuncs/aliyun-php-sdk-core/Config');
        date_default_timezone_set("PRC");

        // $ak = parse_ini_file(DOCROOT."application/vendor/green-sdk-sample-doc-2017-01-12/green-sdk-sample/v2017-01-12/green-php-sdk-sample-v20170112/aliyun.ak.ini");
        //请替换成你自己的accessKeyId、accessKeySecret
        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", 'LTAIvJvaLwxAKeLd', 'F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt'); // TODO
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-shanghai", "Green", "green.cn-hangzhou.aliyuncs.com");
        $client = new DefaultAcsClient($iClientProfile);

        $request = new Green\ImageSyncScanRequest();
        $request->setMethod("POST");
        $request->setAcceptFormat("JSON");
        //获取截图信息
        $post_data = array('bid'=>$bid);
        // echo $result['sid'];
        $url = 'http://jfb.dev.smfyun.com/wzb/SnapshotInfo?bid='.$bid;
        // var_dump($this->request_post($url, $post_data));
        $res = $this->request_post($url, $post_data);
        $oss_shot = json_decode($res,true);
        if($oss_shot['LiveStreamSnapshotInfoList']['LiveStreamSnapshotInfo'][0]['OssObject']){
            $post_data = array('object_name'=>$oss_shot['LiveStreamSnapshotInfoList']['LiveStreamSnapshotInfo'][0]['OssObject']);
            // echo $result['sid'];
            $url = 'http://jfb.dev.smfyun.com/wzb/oss';
            // var_dump($this->request_post($url, $post_data));
            $oss_url = $this->request_post($url, $post_data);
        }
        if($oss_url){
            $task1 = array('dataId' =>  uniqid(),
            'url' => $oss_url,
            'time' => round(microtime(true)*1000)
        );
            $request->setContent(json_encode(array("tasks" => array($task1),
                                          "scenes" => array("porn"))));
            try {
                $response = $client->getAcsResponse($request);
                $ispron = json_decode(json_encode($response),true);
                Kohana::$log->add('jianhuang_status:',$ispron['data'][0]['results'][0]['label']);
                $mem = Cache::instance('memcache');
                $rankkey = $bid.'_live_status';
                $res_porn = $mem->get($rankkey);
                if($ispron['data'][0]['results'][0]['label']=='porn'){
                    $mem->set($rankkey, 'porn', 60);
                }
                if($res_porn=='porn'){
                    $content = '{"status": "porn_stop"}';
                    Gateway::sendToGroup($bid, $content);
                    ob_flush();
                    echo 'porn';
                }else{
                    echo 'normal';
                }
                if(200 == $response->code){
                    $taskResults = $response->data;
                    foreach ($taskResults as $taskResult) {
                        if(200 == $taskResult->code){
                            $sceneResults = $taskResult->results;
                            foreach ($sceneResults as $sceneResult) {
                                $scene = $sceneResult->scene;
                                $suggestion = $sceneResult->suggestion;
                                //根据scene和suggetion做相关的处理
                                //do something
                                // print_r($scene);
                                // print_r($suggestion);
                            }
                        }else{
                            echo "task process fail:" + $response->code;
                            // print_r("task process fail:" + $response->code);
                        }
                    }
                }else{
                    echo "detect not success. code:" + $response->code;
                    // print_r("detect not success. code:" + $response->code);
                }
            } catch (Exception $e) {
                // echo json_encode($e);
                print_r($e);
            }
            exit;
        }
        exit;
    }
    public function action_download_view(){
        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/wzb/download_view";
        $this->template->content = View::factory($view);
    }

    public function action_download(){
        $file = DOCROOT.'wzb/apk/smfyun.apk';
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename=smfyun.apk');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
    public function action_live_status($bid){
        Kohana::$log->add('bid:',print_r($bid,true));
        Kohana::$log->add('action:',print_r($_GET['action'],true));
        if(!$bid) die('不合法');
        if($_GET['action']=='start'){
            //阿里云判断是否正在推流
            $post_data = array();
            // echo $result['sid'];
            $url = 'http://jfb.dev.smfyun.com/wzb/isplay/'.$bid;
            // var_dump($this->request_post($url, $post_data));
            $res = $this->request_post($url, $post_data);
            if($res=='false'){//没有直播
                if(ORM::factory('wzb_live')->where('bid','=',$bid)->where('end_time','=',0)->count_all()>=1){
                    // echo 'hasstart';
                    $live = ORM::factory('wzb_live')->where('bid','=',$bid)->where('end_time','=',0)->find();
                    $live->end_time = time();
                    $live->save();
                    //终止旧的 新建新的
                    $new_live = ORM::factory('wzb_live');
                    $new_live->bid = $bid;
                    $new_live->start_time = time();
                    $new_live->save();
                }else{
                    $new_live = ORM::factory('wzb_live');
                    $new_live->bid = $bid;
                    $new_live->start_time = time();
                    $new_live->save();
                    // echo $live->id;
                }
                echo $new_live->id;
                exit;
            }else{//在直播
                echo 'hasstart';
                exit;
            }
        }
        if($_GET['action']=='stop'&&$_GET['lid']){
            $live = ORM::factory('wzb_live')->where('id','=',$_GET['lid'])->find();
            $live->end_time = time();
            $live->save();
            exit;
        }
    }
    public function action_aliyun($bid=1){
        if($_POST['bid']) $bid = $_POST['bid'];
        $this->template = 'tpl/blank';
        self::before();
        require Kohana::find_file('vendor/aliyun_sdk', 'aliyun-php-sdk-core/Config');
        // include_once '../aliyun-php-sdk-core/Config.php';

        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", "LTAIvJvaLwxAKeLd", "F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt");
        $client = new DefaultAcsClient($iClientProfile);
        if ($_GET['num']==1) {

            //获取当前推送的流
            $request = new live\Request\V20161101\DescribeLiveStreamsOnlineListRequest();
            $request->setDomainName("live.smfyun.com");

            // print_r($request->getQueryParameters());
            $response = $client->getAcsResponse($request);
            // print_r($response);

            //获取在线人数
            $request = new live\Request\V20161101\DescribeLiveStreamOnlineUserNumRequest();
            $request->setDomainName("live.smfyun.com");

            //按时间为颗粒度
            // $request->setStartTime('2017-05-22T00:00:00Z');
            // $request->setEndTime('2017-05-22T05:00:00Z');
            // $request->setStartTime(date('Y-m-d\TH:i:s\Z', time()-1200));
            // $request->setEndTime(date('Y-m-d\TH:i:s\Z', time()+1200));
            $request->setAppName("AppName");
            $request->setStreamName($bid);

            // $request->setNotifyUrl("GET");
            // echo '<pre>';
            // var_dump($request->getQueryParameters());
            $response = $client->getAcsResponse($request);
            // print_r($response);

            // echo $response['OnlineUserInfo']['LiveStreamOnlineUserNumInfo']['Time'].'<br>';
            // echo $response['OnlineUserInfo']['LiveStreamOnlineUserNumInfo'][0]['UserNumber'].'<br>';
        }
        if ($_GET['online']==1) {
            $aliyun = Model::factory('aliyun');
            $response = $aliyun->getAcsResponse();
            //获取当前推送的流
            // $request = new live\Request\V20161101\DescribeLiveStreamsOnlineListRequest();
            // $request->setDomainName("live.smfyun.com");
            // $request->setAppName("AppName");
            // // $request->setStreamName($sid);
            // $response = $client->getAcsResponse($request);
        }
        ob_flush();
        echo json_encode($response);
        exit;
        // echo $response;
    }
    public function action_domain_data($domain){
        if($_POST['domain']) $domain = $_POST['domain'];
        $this->template = 'tpl/blank';
        self::before();
        require Kohana::find_file('vendor/aliyun_sdk', 'aliyun-php-sdk-core/Config');

        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", "LTAIvJvaLwxAKeLd", "F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt");
        $client = new DefaultAcsClient($iClientProfile);
        $request = new Cdn\Request\V20141111\DescribeDomainFlowDataRequest();
        $request->setDomainName($domain);
        //按时间为颗粒度
        $request->setStartTime(gmdate("Y-m-d\TH:i\Z",time()-89*24*3600));
        // $request->setEndTime('2017-06-08T03:11:00Z');
        // $request->setStartTime(gmdate("Y-m-d\TH:i:s\Z",time()-10));
        $request->setEndTime(gmdate("Y-m-d\TH:i\Z"));
        echo '起始时间：'.gmdate("Y-m-d\TH:i\Z",time()-89*24*3600).'<br>';
        echo '结束时间：'.gmdate("Y-m-d\TH:i\Z").'<br>';
        // echo '<pre>';
        $response = json_decode(json_encode($client->getAcsResponse($request)),true);
        echo '<pre>';
        // var_dump($response);
        $flow = 0;
        if($response['FlowDataPerInterval']['DataModule']){
            for ($i=0; $response['FlowDataPerInterval']['DataModule'][$i] ; $i++) {
                $flow = $flow + $response['FlowDataPerInterval']['DataModule'][$i]['Value'];
            }
        }
        echo '所耗流量：'.$flow.'B'.'<br>';
        echo '所耗流量：'.number_format($flow/(1024*1024),2).'MB<br>';
        echo '所耗流量：'.number_format($flow/(1024*1024*1024),2).'GB';
        // echo json_encode($response);
        exit;
        // echo $response;
    }
    public function action_buy_good(){
        $this->bid = 1;
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $this->access_token = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->yz_access_token;
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.trade.bill.good.url.get';
        $methodVersion = '3.0.0';
        $params = [
            'source' => '购物车',
            'price' => 1,
            'order_type'=>0,
            'num' => 1,
            'item_id' => 210749882,
            'kdt_id' => 936565,
        ];
        echo '<pre>';
        var_dump($params);
        $result = $client->post($method, $methodVersion, $params, $files);
        var_dump($result);
        exit;
    }
    public function action_zan(){
        $live = ORM::factory('wzb_live')->where('bid','=',$_POST['bid'])->where('end_time','=',0)->find();
        if($live->id){
            $live->zan_num = $live->zan_num + $_POST['zan'];
            $live->save();
            echo $live->zan_num;
        }else{
            echo 0;
        }
        exit;
    }
    public function action_live(){
        $_SESSION =& Session::instance()->as_array();
        if($_GET['debug']==1){
            $_SESSION['wzb']['openid'] = 'oDt2QjtTeio8l0dBl28SQGhcHSH4';
            $_SESSION['wzb']['bid'] = 1;
            $_SESSION['wzb']['uid'] = 1008;
            $_SESSION['wzb']['sid'] = 936565;
        }
        if(!$_SESSION['wzb']['bid']){
            Request::instance()->redirect('/wzb/index_oauth/'.$_GET['bid'].'/live');
        }
        $biz = ORM::factory('wzb_login')->where('id','=',$_SESSION['wzb']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime) < time()) die('您的账号已过期');
        // $this->config = $_SESSION['wzb']['config'];
        $this->openid = $_SESSION['wzb']['openid'];
        $this->bid = $_SESSION['wzb']['bid'];
        $this->config = ORM::factory('wzb_cfg')->getCfg($this->bid,1);
        $this->uid = $_SESSION['wzb']['uid'];
        $this->sid = $_SESSION['wzb']['sid'];
        $this->access_token = $_SESSION['wzb']['access_token'];

        $view = "weixin/wzb/live";
        $this->template = 'tpl/blank';
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        self::before();
        $shop = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
        $result['tpl'] = ORM::factory('wzb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'wzbtpl')->find()->id;
        $result['tplhead'] = ORM::factory('wzb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'wzbtplhead')->find()->id;
        $result['logo'] = $result['tplhead']?"/wzb/images/cfg/".$result['tplhead'].".v".time().".jpg":$shop->logo;
        $result['poster'] = $result['tpl']?"/wzb/images/cfg/".$result['tpl'].".v".time().".jpg":$shop->logo;
        $result['name'] = $this->config['name']?$this->config['name']:$shop->name;
        $result['openid'] = $this->openid;
        $user = ORM::factory('wzb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        $result['sid'] = $this->sid;
        $result['bid'] = $this->bid;
        $goods = ORM::factory('wzb_setgood')->where('bid','=',$this->bid)->where('status','=',1)->order_by('priority', 'DESC')->find_all();
        $post_data = array(
          'bid' =>$result['bid']
        );
        // echo $result['sid'];
        $url = 'http://jfb.dev.smfyun.com/wzb/aliyun?num=1';
        // var_dump($this->request_post($url, $post_data));
        $res = $this->request_post($url, $post_data);
        $result['online'] = json_decode($res,true);
        // var_dump($result['online']);
        //wx_share
        $post_data = array(
          'bid' =>$result['bid']
        );
        // echo $result['sid'];
        $url = 'http://jfb.dev.smfyun.com/wzb/aliyun?online=1';
        // var_dump($this->request_post($url, $post_data));
        $res = $this->request_post($url, $post_data);
        $result['onlines'] = json_decode($res,true);
        if($result['onlines']['OnlineInfo']['LiveStreamOnlineInfo']){
            $onliens = $result['onlines']['OnlineInfo']['LiveStreamOnlineInfo'];
            for ($i=0; $onliens[$i]['StreamName'] ; $i++) {
                if($onliens[$i]['StreamName']==$this->bid){
                    $result['isonline'] = 1;
                }
            }
        }
        $cfg = ORM::factory('wzb_cfg')->where('bid','=',$this->bid)->where('key','=','buynum')->find();
        $cfg2 = ORM::factory('wzb_cfg')->where('bid','=',$this->bid)->where('key','=','buyname')->find();
        if(!$result['isonline']){//未开播
                $cfg->value=0;
                $cfg->save();

                $cfg2->value='';
                $cfg2->save();
        }
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($this->bid,'wzb',$this->appId,$options);
        $jsapi = $wx->getJsSign($callback_url);
        $wsimg = ORM::factory('wzb_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'wzbtplshare')->find()->id;
        $result['wsimg'] = $wsimg?"http://".$_SERVER['HTTP_HOST']."/wzb/images/cfg/".$wsimg.".v".time().".jpg":$shop->logo;
        if($_POST['subaction']){
            if($_POST['subaction'] =='1'){//订阅
                $user->sub=1;
                $user->save();
                echo '订阅成功';
            }else{//取消订阅
                $user->sub=0;
                $user->save();
                echo '成功取消订阅';
            }
            exit;
        }
        if($this->config['coupon']==1&&$this->config['couponid']){//下发优惠券
            if($user->coupon!=1){
                $this->access_token = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->yz_access_token;
                $client = new YZTokenClient($this->access_token);
                $method = 'youzan.ump.coupon.take';
                $methodVersion = '3.0.0';
                $params = [
                    'weixin_openid' => $user->openid,
                    'coupon_group_id'=>$this->config['couponid']
                ];
                $cpresult = $client->post($method, $methodVersion, $params, $files);
                if($cpresult['response']){
                    if($cpresult['response']['coupon_type']=='PROMOCODE'){
                        $type = 'promocode';
                    }
                    if($cpresult['response']['coupon_type']=='PROMOCARD'){
                        $type = 'promocard';
                    }
                    $user->coupon = 1;
                    $user->save();
                    $result['coupon']['title'] = $cpresult['response'][$type]['title'];
                    // echo $type;
                    // echo $result['coupon']['title'];
                    // exit;
                }
                if($cpresult['error_response']){
                    $cpresult = $client->post($method, $methodVersion, $params, $files);
                    if($cpresult['response']){
                        if($cpresult['response']['coupon_type']=='PROMOCODE'){
                            $type = 'promocode';
                        }
                        if($cpresult['response']['coupon_type']=='PROMOCARD'){
                            $type = 'promocard';
                        }
                        $user->coupon = 1;
                        $user->save();
                        $result['coupon']['title'] = $cpresult['response'][$type]['title'];
                        // echo $type;
                        // echo $result['coupon']['title'];
                        // exit;
                    }
                    if($cpresult['error_response']){
                        // $result['coupon']['error'] = $cpresult['error_response']['code'].$cpresult['error_response']['msg'].$user->openid;
                        // if($cpresult['error_response']['msg']=='您来晚啦已经被领光了'){
                            $result['coupon']['error'] = $cpresult['error_response']['msg'];
                        // }
                    }
                }
            }
        }
        if($_POST['num_iid']){
            $this->access_token = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->yz_access_token;
            $client = new YZTokenClient($this->access_token);
            $method = 'kdt.item.get';
            $methodVersion = '1.0.0';
            $params = [
                'num_iid' => $_POST['num_iid'],
                'fields' => 'skus,title,price',
                'extend_fields' => 'sku_tree',
            ];
            $result = $client->post($method, $methodVersion, $params, $files);
            echo json_encode($result);
            exit;
            // $item = $result['response']['item']['skus'];
            // foreach ($item as $k => $v) {
            //     echo $v['sku_id'].'<br>';
            //     echo $v['price'].'<br>';
            //     echo $v['properties_name_json'].'<br>';
            // }
        }
        $live = ORM::factory('wzb_live')->where('bid','=',$this->bid)->where('end_time','=',0)->find();
        if($live->id){
            $live->pv = $live->pv+1;
            if($user->uvtime<$live->start_time){
                $user->uvtime = time();
                $live->uv = $live->uv+1;
                $user->save();
            }
            $live->save();
            $result['zan_num'] = $live->zan_num;
        }else{
            $result['zan_num'] = 0;
        }
        $result['goods_count'] = ORM::factory('wzb_setgood')->where('bid','=',$this->bid)->where('status','=',1)->order_by('priority', 'DESC')->count_all();
        $result['buynum'] = $cfg->value;
        $result['buyname'] = $cfg2->value;

        $access_token = ORM::factory('wzb_login')->where('id', '=', $this->bid)->find()->yz_access_token;
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $client = new YZTokenClient($access_token);
        $method = 'youzan.ump.coupons.unfinished.search';
        $methodVersion = '3.0.0';
        $params = [
            'fields' => 'title,group_id',
        ];
        $coupons = $client->post($method, $methodVersion, $params, $files);

        $method = 'youzan.ump.presents.ongoing.all';
        $methodVersion = '3.0.0';
        $params = [
            'fields' => 'title,present_id',
        ];
        $gifts = $client->post($method, $methodVersion, $params, $files);

        $data1 = ORM::factory('wzb_lottery')->where('bid','=',$this->bid)->where('item','=',1)->find();
        if ($data1->type == 1) {
            $content['type1'] = $data1->other.'积分';
        }elseif ($data1->type == 2) {
            foreach ($coupons['response']['coupons'] as $k => $v) {
                if ($v['group_id'] == $data1->other) {
                    $content['type1'] = $v['title'];
                }
            }
        }elseif ($data1->type == 3) {
            $content['type1'] = round($data1->other/100,2).'元现金红包';
        }else{
            foreach ($gifts['response']['presents'] as $k => $v) {
                if ($v['present_id'] == $data1->other) {
                    $content['type1'] = $v['title'];
                }
            }
        }
        $data2 = ORM::factory('wzb_lottery')->where('bid','=',$this->bid)->where('item','=',2)->find();
        if ($data2->type == 1) {
            $content['type2'] = $data2->other.'积分';
        }elseif ($data2->type == 2) {
            foreach ($coupons['response']['coupons'] as $k => $v) {
                if ($v['group_id'] == $data2->other) {
                    $content['type2'] = $v['title'];
                }
            }
        }elseif ($data2->type == 3) {
            $content['type2'] = round($data2->other/100,2).'元现金红包';
        }else{
            foreach ($gifts['response']['presents'] as $k => $v) {
                if ($v['present_id'] == $data2->other) {
                    $content['type2'] = $v['title'];
                }
            }
        }
        $data3 = ORM::factory('wzb_lottery')->where('bid','=',$this->bid)->where('item','=',3)->find();
        if ($data3->type == 1) {
            $content['type3'] = $data3->other.'积分';
        }elseif ($data3->type == 2) {
            foreach ($coupons['response']['coupons'] as $k => $v) {
                if ($v['group_id'] == $data3->other) {
                    $content['type3'] = $v['title'];
                }
            }
        }elseif ($data3->type == 3) {
            $content['type3'] = round($data3->other/100,2).'元现金红包';
        }else{
            foreach ($gifts['response']['presents'] as $k => $v) {
                if ($v['present_id'] == $data3->other) {
                    $content['type3'] = $v['title'];
                }
            }
        }
        $data4 = ORM::factory('wzb_lottery')->where('bid','=',$this->bid)->where('item','=',4)->find();
        if ($data4->type == 1) {
            $content['type4'] = $data4->other.'积分';
        }elseif ($data4->type == 2) {
            foreach ($coupons['response']['coupons'] as $k => $v) {
                if ($v['group_id'] == $data4->other) {
                    $content['type4'] = $v['title'];
                }
            }
        }elseif ($data4->type == 3) {
            $content['type4'] = round($data4->other/100,2).'元现金红包';
        }else{
            foreach ($gifts['response']['presents'] as $k => $v) {
                if ($v['present_id'] == $data4->other) {
                    $content['type4'] = $v['title'];
                }
            }
        }
        $lswitch = ORM::factory('wzb_cfg')->where('bid','=',$this->bid)->where('key','=','switch')->find()->value;
        $this->template->content = View::factory($view)
                        ->bind('result', $result)
                        ->bind('goods', $goods)
                        ->bind('jsapi',$jsapi)
                        ->bind('user',$user)
                        ->bind('item', $item)
                        ->bind('lswitch', $lswitch)
                       ->bind('coupons',$coupons)
                       ->bind('gifts',$gifts)
                       ->bind('content',$content)
                       ->bind('data1',$data1)
                       ->bind('data2',$data2)
                       ->bind('data3',$data3)
                       ->bind('data4',$data4);
    }
    public function action_sendsub(){
        if($_GET['bid']){
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->appid;

            $bid = $_GET['bid'];
            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
            $this->config = ORM::factory('wzb_cfg')->getCfg($bid,1);
            $this->wx = new Wxoauth($bid,'wzb',$this->appId,$options);
            $shop = ORM::factory('wzb_login')->where('id','=',$bid)->find();
            $users = ORM::factory('wzb_qrcode')->where('bid','=',$bid)->where('sub','=',1)->find_all();
            // $name = $this->config['name']?$this->config['name']:$shop->name;
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/wzb/index_oauth/'.$bid;
            if($this->config['starttpl']){
                foreach ($users as $k => $v) {
                    echo json_encode($this->sendstarttpl($v->openid,$url));
                }
            }else{
                die('模板消息未设置！');
            }
            exit;
        }
    }
    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            // return false;
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
    private function sendstarttpl($openid, $url) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['starttpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = $this->config['tpl_top'];
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $this->config['tpl_content'];
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $this->config['tpl_bottom'];
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_wzb:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_wzb:$bid:tplmsg", print_r($tplmsg, true));
        return $this->wx->sendTemplateMessage($tplmsg);
    }


    //产品图片
    public function action_dbimages($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "wzb_$type";

        $pic = ORM::factory($table, $id)->db_pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "wzb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_push_order(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('$postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('wzb', '111');
        Kohana::$log->add('$result11', print_r($result11, true));
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $type=$result11['type'];
        if($type=='TRADE'){
            $msg=$result11['msg'];
            $kdt_id=$result11['kdt_id'];
            $status=$result11['status'];
            //Kohana::$log->add('$status', print_r($status, true));
            Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
            require_once Kohana::find_file('vendor', 'weixin/inc');
            $bid = ORM::factory('wzb_login')->where('shopid','=',$kdt_id)->find()->id;
            $this->bid=$bid;
            $this->config = $config = ORM::factory('wzb_cfg')->getCfg($bid);
            //Kohana::$log->add('$config', print_r($config, true));
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            $this->access_token=ORM::factory('wzb_login')->where('id', '=', $bid)->find()->yz_access_token;
            if($this->access_token){
                $this->client =$client= new KdtApiOauthClient();
            }else{
                Kohana::$log->add("wzb:$bid:bname", print_r('有赞参数未填', true));
            }
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
                $posttid=urldecode($msg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("wzb$bid", print_r($jsona, true));
                $tid=$jsona['trade']['tid'];
                $method = 'kdt.trade.get';
                $params = [
                    'with_childs'=>true,
                    'tid'=>$tid,
                ];
                $result = $client->post($this->access_token,$method, $params);
                Kohana::$log->add("wzb$bid", print_r($result, true));
                $trade=$result['response']['trade'];
                if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                    $this->tradeImport($trade, $bid, $client, $config);
                } else {
                    $this->tradeImport($trade, $bid, $client, $config);
                }
            }

        }elseif ($type=='TRADE_ORDER_STATE') {
            $msg=$result11['msg'];
            $kdt_id=$result11['kdt_id'];
            $status=$result11['status'];
            //Kohana::$log->add('$status', print_r($status, true));
            Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
            require_once Kohana::find_file('vendor', 'weixin/inc');
            $bid = ORM::factory('wzb_login')->where('shopid','=',$kdt_id)->find()->id;
            $this->bid=$bid;
            $this->config = $config = ORM::factory('wzb_cfg')->getCfg($bid);
            //Kohana::$log->add('$config', print_r($config, true));
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            $this->access_token=ORM::factory('wzb_login')->where('id', '=', $bid)->find()->yz_access_token;
            if($this->access_token){
                $this->client =$client= new KdtApiOauthClient();
            }else{
                Kohana::$log->add("wzb:$bid:bname", print_r('有赞参数未填', true));
            }
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
                $posttid=urldecode($msg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("wzb$bid", print_r($jsona, true));
                $tid=$jsona['tid'];
                $method = 'kdt.trade.get';
                $params = [
                    'with_childs'=>true,
                    'tid'=>$tid,
                ];
                $result = $client->post($this->access_token,$method, $params);
                Kohana::$log->add("wzb$bid", print_r($result, true));
                $trade=$result['response']['trade'];
                if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                    $this->tradeImport($trade, $bid, $client, $config);
                } else {
                    $this->tradeImport($trade, $bid, $client, $config);
                }
            }
        }

    }
    private function tradeImport($trade, $bid, $client, $config) {
        // print_r($trade);exit;
        $tid = $trade['tid'];
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_BUYER_SIGNED', 'TRADE_CLOSED', 'TRADE_CLOSED_BY_USER');

        if (!in_array($trade['status'], $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            exit;
        }
        Kohana::$log->add('$trade1', print_r($trade['status'], true));
        $wzb_trade = ORM::factory('wzb_trade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($wzb_trade->id) {

            //更新订单状态
            if ($wzb_trade->status != $trade['status']) {
                $wzb_trade->status = $trade['status'];
                $wzb_trade->update_time = $trade['status_time'];
                $wzb_trade->save();
            }
            exit;
        }
        if ($trade['type'] != 'FIXED') exit;

        $method = 'kdt.users.weixin.follower.get';
        $params = [
            'user_id'=>$trade['fans_info']['fans_id'],
        ];
        $result = $client->post($this->access_token,$method, $params);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        //只处理进入过直播间的用户订单
        $wzb_qrcode = ORM::factory('wzb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();

        if (!$wzb_qrcode->id) {
            exit;
        }

        $trade['qid'] = $wzb_qrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        $wzb_trade->values($trade);
        $wzb_trade1 = ORM::factory('wzb_trade')->where('tid', '=', $tid)->find();
        if($wzb_trade1->id) return;
        $wzb_trade->save();
        $group_id = ORM::factory('wzb_login')->where('id','=',$bid)->find()->shopid;
        // $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
        // 推送的数据，包含uid字段，表示是给这个uid推送
        $content = '{"avatar": "'.$wzb_qrcode->headimgurl.'","content": "'.$_POST['comment'].'","createtime": 1494217319,"experience": 41,"iscontrol": 0,"level": 1,"memberid": 27532848,"mtype": 0,"nickname": "'.$wzb_qrcode->nickname.'","sex": '.$wzb_qrcode->sex.',"ts": 7941000,"ytypename": "普通","ytypevt": 0,"msg": "已经购买了","result": 1,"status": "hasbuy"}';
        // 向任意群组的网站页面发送数据
        if($content){
            Gateway::sendToGroup($group_id, $content);
        }

        flush();ob_flush();
        exit;
    }
    public function sendcoupon($bid,$user_id,$gifts){
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $this->access_token = ORM::factory('wzb_login')->where('id','=',$bid)->find()->yz_access_token;
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.ump.coupon.take';
        $methodVersion = '3.0.0';
        $params = [
            'fans_id'=>$user_id,
            'coupon_group_id'=>$gifts,
        ];
        $result = $client->post($method, $methodVersion, $params, $files);
        return $result;
    }
    public function addpoint($bid,$user_id=773070814,$points){
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $this->access_token = ORM::factory('wzb_login')->where('id','=',$bid)->find()->yz_access_token;
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.crm.customer.points.increase';
        $methodVersion = '3.0.1';
        $params = [
            'points' => $points,
            'reason'=>'直播转盘抽奖',
            'fans_id'=>$user_id,
        ];
        $result = $client->post($method, $methodVersion, $params, $files);
        return $result;
    }
    public function sendgift($bid,$user_id,$gifts){
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $this->access_token = ORM::factory('wzb_login')->where('id','=',$bid)->find()->yz_access_token;
        $client = new YZTokenClient($this->access_token);
        $method = 'youzan.ump.present.give';
        $methodVersion = '3.0.0';
        $params = [
            'fans_id' => $user_id,
            'activity_id'=>$gifts,
        ];
        $result = $client->post($method, $methodVersion, $params, $files);
        return $result;
    }
    public function sendhongbao($bid,$openid,$money){
        $config = ORM::factory('wzb_cfg')->getCfg($bid,1);
        $result = $this->hongbao($config,$openid,$money,$bid);
        return $result;
    }
    private function hongbao($config, $openid, $money, $bid=1)
    {
        $this->bid = $bid;
        if (!$wx) {
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/qwt.inc');
            //require_once Kohana::find_file('vendor', "weixin/smfyun/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('wzb_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('wzbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,$options);
        }
        $config['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->name;
        $mch_billno = $config['partnerid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //支付商户号
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
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写

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
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=1) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."wzb/tmp/$bid/cert.pem";
        $key_file = DOCROOT."wzb/tmp/$bid/key.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzb_file_cert')->find();
        $file_key = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzb_file_key')->find();

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

        // Kohana::$log->add("weixin_wzb:$bid:curl_post_ssl:cert_file", $cert_file);

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
