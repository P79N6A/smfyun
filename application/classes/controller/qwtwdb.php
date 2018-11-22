<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtwdb extends Controller_Base {
    public $template = 'weixin/smfyun/wdb/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $token = 'weidingbao';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    //public $access_token;
    public $cdnurl = 'http://cdn.jfb.smfyun.com/qwt/wdb/';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "qwt";
        parent::before();

        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'qrscan') return;

        $_SESSION =& Session::instance()->as_array();

        if (!$_GET['openid']) {
            if (!$_SESSION['qwtwdb']['bid']) {
                // Kohana::$log->add("session不存在:bid", $_SESSION['qwtwdb']['bid']);
                // Kohana::$log->add("session不存在:openid", $_SESSION['qwtwdb']['openid']);
                die('请重新点击验证或者点击菜单进入本页面哦~');
            }
            if (!$_SESSION['qwtwdb']['openid']) die('Access Deined..请重新点击相应菜单');
        }

        $this->config = $_SESSION['qwtwdb']['config'];
        $this->openid = $_SESSION['qwtwdb']['openid'];
        $this->bid = $_SESSION['qwtwdb']['bid'];
        $this->uid = $_SESSION['qwtwdb']['uid'];
        //$this->access_token = $_SESSION['qwtwdb']['access_token'];


        //积分同步 回调
        if ($_GET['debug']) print_r($_SESSION['qwtwdb']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->where('openid','!=','')->where('fuopenid','!=','')->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }

    //入口
    public function action_index($bid) {
        $config = ORM::factory('qwt_wdbcfg')->getCfg($bid,1);
        //$this->access_token=ORM::factory('qwt_wdblogin')->where('id', '=', $bid)->find()->access_token;
        if (!$_GET['openid']) $_SESSION['qwtwdb'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);

            if ($_GET['cksum'] != md5($openid.date('Y-m-d'))) {
                $_SESSION['qwtwdb'] = NULL;
                die('Access Deined!请重新点击相应菜单');
            }
            $userobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            if($userobj->id){
                $userobj->ip = Request::$client_ip;
                $userobj->save();
            }

            $_SESSION['qwtwdb']['config'] = $config;
            $_SESSION['qwtwdb']['openid'] = $openid;
            $_SESSION['qwtwdb']['bid'] = $bid;
            $_SESSION['qwtwdb']['uid'] = $userobj->id;
            //$_SESSION['qwtwdb']['access_token'] = $this->access_token;
            // Kohana::$log->add("session:index:$bid:openid", $_SESSION['qwtwdb']['openid']);
            // Kohana::$log->add("session:index:$bid:bid", $_SESSION['qwtwdb']['bid']);
            if ($bid == 2) {
                // print_r($_SESSION);exit;
            }
            Request::instance()->redirect('/qwtwdb/'.$_GET['url']);
        }
    }
    public function action_storefuop(){// 获取服务号openid和进行跳转

        $bid = $this->bid;
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $ticket_lifetime = 3600*24*7;
        if($this->config['ticket_lifetime']) $ticket_lifetime=time()+3600*24*$this->config['ticket_lifetime'];
        //$client = new KdtRedirectApiClient($appId, $appSecret);
        $storekey = base64_encode($this->openid.'|'.$this->bid.'|'.$ticket_lifetime);

        // $client->redirect('http://'.$_SERVER["HTTP_HOST"].'/wdb/storefuop2/'.$storekey, 'snsapi_base');
        $appid="wxd3a678cfeb03e3a3";//神码浮云技术部的
        $appsecret="661fb2647a804e14ded1f65fad682695";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        if(!$bid) Kohana::$log->add('qwtwdbbid:', 'storefuop');//写入日志，可以删除
        $wx = new Wxoauth($bid,'wdb',$this->appId,$options);

        //$we = new Wechat(array('appid'=>$appid, 'appsecret'=>$appsecret));
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';
        if (!$_GET['callback']) {
                $auth_url = $wx->getOauthRedirect($appid,$callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            } else {
                $token = $wx->getOauthAccessToken($appid,$appsecret);//依托服务号(appid appsecret)的 openid
                $fuopenid = $token['openid'];

                $_SESSION['qwtwdb']['config'] = $this->config;
                $_SESSION['qwtwdb']['openid'] = $this->openid;
                $_SESSION['qwtwdb']['bid'] = $this->bid;
                // Kohana::$log->add("session:$this->bid:openid", $_SESSION['qwtwdb']['openid']);
                // Kohana::$log->add("session:$this->bid:bid", $_SESSION['qwtwdb']['bid']);
                // $_SESSION['qwtwdb']['uid'] = $userobj->id;
            }
        $this->template = 'tpl/blank';
        self::before();
        $url = 'http://'.$_SERVER["HTTP_HOST"].'/smfyun/qwt_wdb/'.$bid;
        //echo 'openid2'.explode('|',base64_decode($storekey))[0];
        //echo 'bid'.explode('|',base64_decode($storekey))[1];
        $bid = explode('|',base64_decode($storekey))[1];
        $openid2 = explode('|',base64_decode($storekey))[0];
        $config = $this->config;
        //$we = new Wechat($config);

        if(!$fuopenid) die('消息不小心走丢啦，请重新点击菜单验证');
        $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();//根据服务号来查
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
            $scan=ORM::factory('qwt_wdbscan')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();
            Kohana::$log->add("wdb{$bid}SCAN1",print_r($fuopenid, true));
            Kohana::$log->add("wdb{$bid}SCAN11",print_r($scan->id, true));
            if($subscribe->id&&$scan->id&&$subscribe->creattime<=$scan->time-60){
                Kohana::$log->add("wfb{$bid}SCAN111",print_r($subscribe->openid, true));
                $has_subscribe=1;
                $model_p = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                $model_p->old=1;
                $model_p->save();
                Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
            }else{
                $model_p = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                $model_p->old=0;
                $model_p->save();
                $has_subscribe=2;
                Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
            }
            Kohana::$log->add("wfb{$bid}SCAN",print_r(time(), true));
            $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
            if($has_subscribe==2&&$fuser->fopenid&&$config['goal2']){
                $ffuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$fuser->fopenid)->find();
                $ffuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$fuser->fopenid)->find();
                $ffuser->scores->scoreIn($ffuser, 3, $config['goal2']);
                $msg['touser'] = $ffuser->openid;
                $msg['msgtype'] = 'text';
                //$msg['text']['content'] = sprintf($config['text_goal2'],$fuser->nickname).'您的当前'.$config['score'].'为：'.$ffuser->score;
                $msg['text']['content'] = str_replace('%s', $fuser->nickname, $config['text_goal2']).'您的当前'.$config['score'].'为：'.$ffuser->score;
                $wx->sendCustomMessage($msg);
            }
            $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
            if($has_subscribe==2){
                $fuser->scores->scoreIn($fuser, 2, $config['goal']);
                $msg['touser'] = $fuser->openid;//fuser 上级用户
                $msg['msgtype'] = 'text';
                //$msg['text']['content'] = sprintf($config['text_goal'],$user2->nickname).'您的当前'.$config['score'].'为：'.$fuser->score;
                $msg['text']['content'] = str_replace('%s', $user2->nickname, $config['text_goal']).'您的当前'.$config['score'].'为：'.$ffuser->score;
                $wx->sendCustomMessage($msg);
            }else{
                $msg['touser'] = $fuser->openid;//fuser 上级用户
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '您的朋友'.$user2->nickname.'已经关注过公众号了，不能再成为您的粉丝了';
                $wx->sendCustomMessage($msg);
            }
            $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();//根据服务号来查
            if($has_subscribe==2){
                $user2->scores->scoreIn($user2, 1, $config['goal0']);
                $msg['touser'] = $user2->openid;//user2 当前用户
                $msg['msgtype'] = 'text';
                //$msg['text']['content'] = sprintf($config['text_goal3'],$fuser->nickname);
                $msg['text']['content'] = str_replace('%s', $fuser->nickname, $config['text_goal3']);
                $wx->sendCustomMessage($msg);
                $bindcon = '恭喜您成为'.$fuser->nickname.'的支持者~，';
            }else{
                $msg['touser'] = $user2->openid;//user2 当前用户
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '您已经关注过公众号了，不能再成为'.$fuser->nickname.'的粉丝了哦。';
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
            $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid)->find();//根据订阅号来查
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
        //Kohana::$log->add('wdb:$bid', print_r($post_data, true));//写入日志，可以删除
        $res = $this->request_post($url, $post_data);
        $bindcon = $bindcon.'<br>海报已生成，请返回对话框查收~<br>';
        $view = "weixin/smfyun/wdb/storefuop";
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
        if(!$bid) Kohana::$log->add('qwtwdbbid:', 'qrscan');//写入日志，可以删除
        $wx = new Wxoauth($bid,'wdb',$this->appId,$options);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';
        if (!$_GET['callback']) {
            $auth_url = $wx->getOauthRedirect($appid,$callback_url, '', 'snsapi_base');
            Kohana::$log->add('qwtwdb:callback:bid:'.$bid, $auth_url);
            header("Location:$auth_url");exit;
        } else {
            $token = $wx->getOauthAccessToken($appid,$appsecret);
        }
        $fuopenid2 = $token['openid'];//当前用户服务号openid
        $scan=ORM::factory('qwt_wdbscan')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid2)->find();
        if(!$scan->id&&$fuopenid2){
            $scan->bid=$bid;
            $scan->fuopenid=$fuopenid2;
            $scan->time=time();
            $scan->save();
        }
        $config = ORM::factory('qwt_wdbcfg')->getCfg($bid,1);
        //$we = new Wechat($config);

        $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid2)->find();//根据服务号查
        $user1 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$openid1)->find();//根据订阅号查上线
        //风险判断
        if ($config['risk_level1'] > 0 && $config['risk_level2'] > 0) {
            //直接用户
            $count2 = ORM::factory('qwt_wdbqrcode', $user1->id)->scores->where('type', '=', 2)->count_all();
            //用是否生成海报判断真实下线
            $count3 = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('fopenid', '=', $user1->openid)->where('ticket', '<>', '')->count_all();
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
                    $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
                    $bindcon= '您已经是'.$fuser->nickname.'的支持者了，不用再扫了哦~';
                }else if($user2->openid==$openid1){//自己扫自己
                    $bindcon= '不能自己扫自己哟';
                }else if($user2->openid==$user1->fopenid){
                    $bindcon= $user1->nickname.'已经是您的粉丝了哟';
                }else{//没有上线
                    $qr_img = 'http://'.$_SERVER['HTTP_HOST'].'/qwta/images/'.$bid.'/wx_qr_img';
                    $bindcon= '亲，请直接进入'.$login->weixin_name.'公众号点击菜单生成海报参与哦~';
                }
            }else{//openid不存在
                if($user2->fuopenid&&$user2->fopenid){//多次扫码但是自己不绑定
                    $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('openid','=',$user2->fopenid)->find();
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
        $view = "weixin/smfyun/wdb/sub";
        $title = $config['name'];
        $this->template->content = View::factory($view)->bind('subhref', $config['subhref'])->bind('over', $over)->bind('href', $href)->bind('bindcon', $bindcon)->bind('title', $title)->bind('qr_img', $qr_img);
    }
    //图文获取
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
        if(!$bid) Kohana::$log->add('qwtwdbbid:', 'qrcheck');//写入日志，可以删除
        $wx = new Wxoauth($bid,'wdb',$this->appId,$options);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';
        if (!$_GET['callback']) {
                $auth_url = $wx->getOauthRedirect($appid,$callback_url, '', 'snsapi_base');
                Kohana::$log->add("qwt$wdbtoken", print_r($auth_url,true));
                header("Location:$auth_url");exit;
            } else {
                $token = $wx->getOauthAccessToken($appid,$appsecret);
                Kohana::$log->add("qwt$wdbtoken", print_r($token,true));
                $_SESSION['qwtwdb']['config'] = $this->config;
                $_SESSION['qwtwdb']['openid'] = $this->openid;
                $_SESSION['qwtwdb']['bid'] = $this->bid;
                // Kohana::$log->add("session:qrcheck:$this->bid:openid", $_SESSION['qwtwdb']['openid']);
                // Kohana::$log->add("session:qrcheck:$this->bid:bid", $_SESSION['qwtwdb']['bid']);
            }
        $openid2 = $this->openid;//当前用户订阅号openid
        $fuopenid2 = $token['openid'];//当前用户服务号openid

        $config = $this->config;
        //$we = new Wechat($config);

        $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('fuopenid','=',$fuopenid2)->find();

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
                $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('fuopenid','=',$fuopenid2)->find();
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
                Kohana::$log->add("wdb{$bid}SCAN1",print_r($fuopenid2, true));
                $scan=ORM::factory('qwt_wdbscan')->where('bid','=',$bid)->where('fuopenid','=',$fuopenid2)->find();
                Kohana::$log->add("wdb{$bid}SCAN11",print_r($scan->id, true));
                if($subscribe->id&&$scan->id&&$subscribe->creattime<=$scan->time-60){
                    Kohana::$log->add("wfb{$bid}SCAN111",print_r($subscribe->openid, true));
                    $has_subscribe=1;
                    $model_p = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                    $model_p->old=1;
                    $model_p->save();
                    Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
                }else{
                    $model_p = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid2)->find();
                    $model_p->old=0;
                    $model_p->save();
                    $has_subscribe=2;
                    Kohana::$log->add("has_subscribe:$bid:$openid2",$has_subscribe);
                }
                Kohana::$log->add("wfb{$bid}SCAN",print_r(time(), true));
                //验证改用户之前是否关注公众号
                $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$user2->fopenid)->find();
                if($has_subscribe==2&&$fuser->fopenid&&$config['goal2']){
                    $ffuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$fuser->fopenid)->find();

                    $ffuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$fuser->fopenid)->find();
                    $ffuser->scores->scoreIn($ffuser, 3, $config['goal2']);
                    $msg['touser'] = $ffuser->openid;
                    $msg['msgtype'] = 'text';
                    //$msg['text']['content'] = sprintf($config['text_goal2'],$fuser->nickname).'您的当前'.$config['score'].'为：'.$ffuser->score;
                    $msg['text']['content'] = str_replace('%s', $fuser->nickname, $config['text_goal2']).'您的当前'.$config['score'].'为：'.$ffuser->score;
                    $wx->sendCustomMessage($msg);

                }
                $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$user2->fopenid)->find();
                if($has_subscribe==2){
                    $fuser->scores->scoreIn($fuser, 2, $config['goal']);
                    $msg['touser'] = $fuser->openid;//fuser 上级用户
                    $msg['msgtype'] = 'text';
                    //$msg['text']['content'] = sprintf($config['text_goal'],$user2->nickname).'您的当前'.$config['score'].'为：'.$fuser->score;
                    $msg['text']['content'] = str_replace('%s', $user2->nickname, $config['text_goal']).'您的当前'.$config['score'].'为：'.$fuser->score;
                    $wx->sendCustomMessage($msg);
                }else{
                    $msg['touser'] = $fuser->openid;//fuser 上级用户
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '您的朋友'.$user2->nickname.'已经关注过公众号了，不能再成为您的粉丝了';
                    $wx->sendCustomMessage($msg);
                }
                $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('fuopenid','=',$fuopenid2)->find();
                if($has_subscribe==2){
                    $user2->scores->scoreIn($user2, 1, $config['goal0']);
                    $msg['touser'] = $user2->openid;//user2 当前用户
                    $msg['msgtype'] = 'text';
                    //$msg['text']['content'] = sprintf($config['text_goal3'],$fuser->nickname);
                    $msg['text']['content'] = str_replace('%s', $fuser->nickname, $config['text_goal3']);
                    $wx->sendCustomMessage($msg);

                    $bindcon='恭喜您成为'.$fuser->nickname.'的粉丝';
                }else{
                    $msg['touser'] = $user2->openid;//user2 当前用户
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '您已经关注过公众号了，不能再成为'.$fuser->nickname.'的粉丝了哦。';
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
                    $fuser = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$user2->fopenid)->find();
                    $bindcon='您已经是'.$fuser->nickname.'的支持者了，不用再点了哦~';
                }
            }
        }

        $view = "weixin/smfyun/wdb/qrcheck";
        $this->template->content = View::factory($view)->bind('over', $over)->bind('bindcon', $bindcon)->bind('config', $config);
    }
    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/smfyun/wdb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;



        //计算排名
        $user = ORM::factory('qwt_wdbqrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;


        if(isset($_POST['rank'])){//今日排名
            $user = ORM::factory('qwt_wdbqrcode', $this->uid)->as_array();

            $ranktoday = "qwtwdb:ranktoday:{$this->bid}:{$this->openid}:$top";
            //$mem->delete($ranktoday);
            $result['rank'] = $mem->get($ranktoday);

            $topday = "qwtwdb:toptoday:{$this->bid}:$top";
            //$mem->delete($topday);
            $users = $mem->get($topday);
            if (!$users) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT qwt_wdbqrcodes.nickname,qwt_wdbqrcodes.headimgurl,qwt_wdbscores.qid,sum(qwt_wdbscores.score) as score from qwt_wdbqrcodes , qwt_wdbscores where qwt_wdbqrcodes.id=qwt_wdbscores.qid and qwt_wdbscores.bid=582 and from_unixtime(qwt_wdbscores.lastupdate,'%Y-%m-%d')= '$today' group by qwt_wdbscores.qid order by score desc limit $top");
                $usersobj = $sql->execute()->as_array();
                foreach ($usersobj as $k => $userobj) {
                    //$users['qid'] = $userobj['qid'];
                    $users[] = $userobj;
                    // $sql = DB::query(Database::SELECT,"SELECT nickname,headimgurl FROM qwt_wdbqrcodes where `id`=$userobj->qid");
                    // $qr = $sql->execute()->as_array();
                    // $users['nickname'] = $qr['nickname'];
                    // $users['headimgurl'] = $qr['headimgurl'];
                }
                $mem->set($topday, $users, 600);
            }
            if (!$result['rank']) {
                $today = date('Y-m-d',time());
                $sql = DB::query(Database::SELECT,"SELECT qwt_wdbqrcodes.nickname,qwt_wdbqrcodes.headimgurl,qwt_wdbscores.qid,sum(qwt_wdbscores.score) as score from qwt_wdbqrcodes , qwt_wdbscores where qwt_wdbqrcodes.id=qwt_wdbscores.qid and qwt_wdbscores.bid=582 and from_unixtime(qwt_wdbscores.lastupdate,'%Y-%m-%d')= '$today' group by qwt_wdbscores.qid order by score desc ");
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
            $rankkey = "qwtwdb:rank3:{$this->bid}:{$this->openid}:$top";
            $result['rank'] = $mem->get($rankkey);
            if (!$result['rank']) {//全部排名
            $result['rank'] = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
            }

            $topkey = "qwtwdb:top3:{$this->bid}:$top";
            $users = $mem->get($topkey);
            if (!$users) {
                $usersobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
                foreach ($usersobj as $userobj) {
                    $users[] = $userobj->as_array();
                }
                $mem->set($topkey, $users, 600);
            }
        }
        $this->template->title = $this->config['score'].'排行榜';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);
    }

    //我的积分
    public function action_score() {
        $view = "weixin/smfyun/wdb/scores";

        $this->template->title = '我的'. $this->config['score'];
        $this->template->content = View::factory($view)->bind('scores', $scores);

        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('qwt_wdbqrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/smfyun/wdb/items";

        $obj = ORM::factory('qwt_wdbitem')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('qwt_wdbcfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('qwt_wdbscore')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit);

    }
    public function action_item($iid) {
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/smfyun/wdb/xiangqing";
        $item = ORM::factory('qwt_wdbitem')->where('bid', '=', $this->bid)->where('id', '=', $iid)->find();
        $day_limit = ORM::factory('qwt_wdbcfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('qwt_wdbscore')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $user2 = ORM::factory('qwt_wdbqrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->as_array();
        // $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('item', $item)->bind('dlimit',$dlimit)->bind('user2',$user2);
    }


    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/smfyun/wdb/neworder";
        //require_once Kohana::find_file("vendor/kdt","KdtApiClient");
        $config = $this->config;
        $bid = $this->bid;


        $item = ORM::factory('qwt_wdbitem', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/qwtwdb/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $stock=$item->stock;
        $keyname="qwdb_itemnum:{$bid}:{$item->id}";
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
        $userobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->config['score']}，您只有 {$userobj->score} {$this->config['score']}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('qwt_wdborder')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('qwt_wdbqrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            $count3 = ORM::factory('qwt_wdbqrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();

            if ($userobj->lock == 0 && $count2 >= $this->config['risk_level1'] & $count3 <= $this->config['risk_level2']) {
                $userobj->lock = 1;
                $userobj->save();

                if ($userobj->lock == 1) die('您的账号存在刷分现象，已被锁定。如果您确认是系统误判断，请联系客服解决。');
            }
        }

        $this->template->title = $item->name;

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url&&$_POST['data']['type']!=5) ) {
            $order = ORM::factory('qwt_wdborder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;

            //虚拟产品
            if ($item->url) {

                $order->status = 1;

                $url = substr($item->url , 0 , 4);
                $order->url = $item->url;

            } else {
                //省份 城市
                $order->city = $_POST['s_province'].' '.$_POST['s_city'].' '.$_POST['s_dist'];
            }

            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();

                $userobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/qwtwdb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
        //微信红包
        if ($_POST['data']['type']==4) {

            $order = ORM::factory('qwt_wdborder');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;

            $fuopenid = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->fuopenid;
                //发红包
                $tempname=ORM::factory("qwt_login")->where("id","=",$this->bid)->find()->name;
                $tempmoney=ORM::factory("qwt_wdbitem")->where("id","=",$iid)->find()->price;
                $tempmoney=$tempmoney*100;
                $hbresult = $this->hongbao($this->config, $fuopenid, '', $tempname, $tempmoney);
                if($hbresult['result_code']=='SUCCESS')
                {
                    $order->memo=$hbresult['mch_billno'];
                    $order->money=$hbresult['total_amount']/100;
                    //成功
                   $order->save();

                    //减库存
                   $item->stock--;
                   $item->save();
                    //扣积分

                   $userobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                   $userobj->scores->scoreOut($userobj, 4, $order->score);
                   $goal_url = '/qwtwdb/orders';
                   if ($order->url) $goal_url = $order->url;

                    //成功后跳转
                    Request::instance()->redirect($goal_url);

                }else{
                    echo $hbresult['return_msg'];
                    exit();
                }

        }
        //话费流量
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('qwt_wdborder');
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

                $userobj = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/qwtwdb/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //自动填写旧地址
        $old_order = ORM::factory('qwt_wdborder')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    // 2015.12.28 增加检查地理位置
    public function action_check_location($openid2=1){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/wdb/check_location";
        //$wx['appid'] = $this->config['appid'];
        //$wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        //$we = new Wechat($wx);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        if(!$this->bid) Kohana::$log->add('qwtwdbbid:', 'localtion');//写入日志，可以删除
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
          $area = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();

          exit;
        }

        $count = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('qwt_wdbcfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
                //->bind('fuopenid', $fuopenid2);
    }

    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/wdb/ticket";
        //$wx['appid'] = $this->config['appid'];
        //$wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        //$we = new Wechat($wx);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        if(!$this->bid) Kohana::$log->add('qwtwdbbid:', 'ticket');//写入日志，可以删除
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
        $view = "weixin/smfyun/wdb/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('qwt_wdborder')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_wdb$type";

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
      private function hongbao($config, $openid, $wx='', $bid=1, $money)
    {
        if (!$wx) {
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/smfyun/inc');
            //require_once Kohana::find_file('vendor', "weixin/smfyun/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('qwtwdbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,'wdb',$this->appId,$options);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = '1364266202'; //支付商户号
        $data["wxappid"] = 'wx5c9fe0a106a87d3e';//三方appid
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
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . 'oNjr8YUAnH6d5OIPZbaPtxRDPe94Yfoo'));//将签名转化为大写

        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('qwtweixin:hongbaopost', print_r($data, true));//写入日志，可以删除

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
        $result['mch_billno'] =$mch_billno;
        return $result;//hash数组
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."wdb/tmp/smfyun/cert.smfyun.pem";
        $key_file = DOCROOT."wdb/tmp/smfyun/key.smfyun.pem";
        $rootca_file=DOCROOT."wdb/tmp/smfyun/rootca.smfyun.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wdbfile_cert')->find();
        $file_key = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wdbfile_key')->find();
        $file_rootca = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wdbfile_rootca')->find();

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
