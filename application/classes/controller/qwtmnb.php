<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtmnb extends Controller_Base {
    public $template = 'weixin/smfyun/wfb/tpl/blank';
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
        if (Request::instance()->action == 'shareurl') return;
        if (Request::instance()->action == 'error') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'bind_user') return;
        if (Request::instance()->action == 'bind_qr') return;
        if (Request::instance()->action == 'sns_login') return;
        if (Request::instance()->action == 'api_ticket') return;
        // if (!$_GET['openid']) {
        //     if (!$_SESSION['qwtmnb']['bid']) die('页面已过期。请重新点击相应菜单');
        //     if (!$_SESSION['qwtmnb']['openid']) die('Access Deined..请重新点击相应菜单');
        // }
        if (!$_SESSION['qwtmnb']['bid']) die('页面已过期。请重新点击相应菜单');
        $this->config = $_SESSION['qwtmnb']['config'];
        $this->openid = $_SESSION['qwtmnb']['openid'];
        $this->bid = $_SESSION['qwtmnb']['bid'];
        $this->uid = $_SESSION['qwtmnb']['uid'];
        $this->myopenid = $_SESSION['qwtmnb']['myopenid'];
        $this->userinfo = $_SESSION['qwtmnb']['userinfo'];
        if(!$this->openid) die('请重新点击链接谢谢！');
        if(!$this->bid) die('请重新点击链接谢谢！!');


        // $sname = ORM::factory('qwt_mnbcfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        if ($_GET['debug']) print_r($_SESSION['mnb']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('qwt_mnbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);
        View::bind_global('scorename', $this->scorename);
        $this->template->user = $user;
        parent::after();
    }
    public function action_bind_qr($bid,$lid){//生成二维码图片
        if(!$bid) die('no bid');
        if(!$lid) die('no lid');
        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
        $qrurl =  'http://'.$_SERVER['HTTP_HOST'].'/qwtmnb/bind_user/'.$bid.'/'.$lid;
        QRcode::png($qrurl,false,'L','6','2');
        header('Content-type: image/png');
        exit;
    }
    public function action_bind_user($bid,$lid,$app='mnb'){//移动端绑定用户
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getUserInfo($token['openid']);
            $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$qr_user->id){
                $qr_user->bid = $bid;
                $qr_user->values($userinfo);
            }else{//更新头像  跑路的不更新
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $qr_user->subscribe_time = $userinfo['subscribe_time'];
                    $qr_user->jointime = time();
                    $qr_user->nickname = $userinfo['nickname'];
                    $qr_user->headimgurl = $userinfo['headimgurl'];
                }
                $qr_user->subscribe = $userinfo['subscribe'];
            }
            $qr_user->save();

            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$user->id){
                $user->qid = $qr_user->id;
                $user->bid = $bid;
                $user->values($userinfo);
            }else{//更新头像
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $user->subscribe_time = $userinfo['subscribe_time'];
                    $user->jointime = time();
                    $user->nickname = $userinfo['nickname'];
                    $user->headimgurl = $userinfo['headimgurl'];
                }
                $user->subscribe = $userinfo['subscribe'];
            }
            if($user->subscribe==0){
                $result['error'] = '请先关注公众号再绑定管理员。';
            }else{
                $luser = ORM::factory('qwt_mnblogin')->where('id','=',$lid)->find();
                if($luser->id){
                    $luser->wx_bind = $user->id;
                    $luser->save();
                }else{
                    $result['error'] = '请先添加用户再绑定管理员。';
                }
            }
            $user->save();

            $view = "weixin/smfyun/mnb/bind";
            $this->template->title = '绑定管理员';
            $this->template->content = View::factory($view)
                ->bind('result', $result)
                ->bind('luser', $luser)
                ->bind('bid', $this->bid);
        }
    }
    public function action_test(){
        $this->template->content = View::factory('weixin/smfyun/mnb/login');
    }
    public function action_login(){//移动端登陆
        $bid = $this->bid;
        $view = 'weixin/smfyun/mnb/login';
        $this->template->title = '登陆';
        // $openid = $_SESSION['qwtmnb']['admin_openid'];
        // $config = $_SESSION['qwtmnb']['config'];
        // $uid = $_SESSION['qwtmnb']['admin_uid'];

        $openid = $this->openid;
        // $login = ORM::factory('qwt_mnblogin')->where('bid','=',$bid)->where('wx_bind','=',$uid)->find();
        // if(!$login->id){
        //     $result['error'] = '当前用户不是管理员。';
        // }
        $usered = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if ($usered->id) {
            if ($usered->status==1) {
                $view = 'weixin/smfyun/mnb/father';
                $this->template->title = '审核';
                if ($_POST) {
                    $pass = 1;
                    if ($_POST['fpcode']) {
                        $check = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('pcode','=',$_POST['fpcode'])->find();
                        if (!$check->id) {
                            $pass = 0;
                            $result['error'] = '没有找到授权码为'.$_POST['fpcode'].'的用户';
                        }
                    }
                    if ($pass == 1) {
                        if ($_POST['wx_username']) {
                            $user = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                            $user->wx_username = $_POST['wx_username'];
                            if ($_POST['fname']) {
                                $user->fname = $_POST['fname'];
                            }
                            if ($_POST['fpcode']) {
                                $user->fpcode = $_POST['fpcode'];
                                $fopenid = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('pcode','=',$_POST['fpcode'])->find()->openid;
                                $user->fopenid = $fopenid;
                            }
                            $user->status = 2;
                            $user->save();
                            Request::instance()->redirect('/qwtmnb/login');
                        }else{
                            $result['error'] = "请至少填写微信号！";
                        }
                    }
                }
            }
            if ($usered->status==2) {
                $result['ok'] = '已申请，请耐心等待管理员审核';
            }
            if ($usered->status==3) {
                Request::instance()->redirect('/qwtmnb/onepage');
            }
        }else{
            if ($_POST) {
                if($_POST['tel']&&$_POST['passwd']&&$_POST['name']&&$_POST['pcode']){
                    $luser = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('tel','=',$_POST['tel'])->where('password','=',$_POST['passwd'])->where('name','=',$_POST['name'])->where('pcode','=',$_POST['pcode'])->find();
                    if($luser->id>0){
                        if($luser->openid==NULL){
                            $user = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('tel','=',$_POST['tel'])->where('password','=',$_POST['passwd'])->find();
                            $user->openid = $openid;
                            $user->nickname = $this->userinfo['nickname'];
                            $user->headimgurl = $this->userinfo['headimgurl'];
                            $user->status = 1;
                            $user->save();
                            Request::instance()->redirect('/qwtmnb/login');
                        }else{
                            $result['error'] = '账户与绑定用户不相符或已在审核中。';
                        }
                    }else{
                        $result['error'] = '输入的信息不正确！';
                    }
                }else{
                    $result['error'] = '请输入完整！';
                }
            }
        }

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory($view)
            ->bind('jsapi',$jsapi)
            ->bind('result', $result)
            ->bind('login', $login)
            ->bind('bid', $this->bid);
    }
    public function action_onepage(){
        $bid = $this->bid;
        $openid = $this->openid;
        $user = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $lid = $user->lid;
        $type = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->find_all();
        foreach ($type as $k => $v) {
            $ly_arr = explode(',', $v->auth);
            if (in_array($lid, $ly_arr)) {
                $usertype[$k] = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->where('id','=',$v->id)->find()->id;
                // $userfaq[$k]=ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('tid','=',$v->id)->find_all();

            }
        }
        if($_POST['keyword']){
            $result['keyword'] = $_POST['keyword'];
            $keyword = '%'.trim($_POST['keyword'].'%');
            // var_dump($usertype);
            $faqs = ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('tid','IN',$usertype)->where('title','like',$keyword)->find_all();
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $view = 'weixin/smfyun/mnb/onepage';
        $this->template->content = View::factory($view)
            ->bind('jsapi',$jsapi)
            ->bind('result',$result)
            ->bind('usertype',$usertype)
            ->bind('faqs',$faqs);
    }
    public function action_detail($fid){
        $bid = $this->bid;
        $openid = $this->openid;
        $faq = ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('id','=',$fid)->find();

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $view = 'weixin/smfyun/mnb/detail';
        $this->template->content = View::factory($view)
            ->bind('jsapi',$jsapi)
            ->bind('faq',$faq);
    }
    public function action_qr($code){
        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
        $privateKey = "sjdksldkwospaisk";
        $iv = "wsldnsjwisqweskl";
        $data = $code;
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        $hb_code = urlencode(base64_encode($encrypted));

        $qrurl = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_userinfo/'.$this->bid.'/mnb/user_snsapi_base?hb_code='.$hb_code;
        QRcode::png($qrurl,false,'L','6','2');
        header('Content-type: image/png');
        exit;
    }
    public function action_shareurl(){
        if($_GET['bid']&&$_GET['openid']&&$_GET['hb_code']){
            $bid = $_GET['bid'];
            $openid = $_GET['openid'];
            //aes解密
            // $privateKey = "sjdksldkwospaisk";
            // $iv = "wsldnsjwisqweskl";
            // $encryptedData = base64_decode(urldecode($_GET['hb_code']));
            // $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
            // $_GET['hb_code'] = $decrypted;
            $code = $_GET['hb_code'];
            $login = ORM::factory('qwt_mnbkl')->where('bid','=',$bid)->where('code','=',$code)->find();
            $rconfig = ORM::factory('qwt_mnbrcfg')->getCfg($login->from_lid,1);//门店config
            // $config = ORM::factory('qwt_mnbcfg')->getCfg($bid,1);
            if($rconfig['shareurl']){
                $weixin = ORM::factory('qwt_mnbweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('kouling','=',$code)->find();
                if($weixin->id){
                    $weixin->pv = $weixin->pv+1;
                    $ip = str_replace('.', '_', Request::$client_ip);
                    // echo "<pre>";
                    // var_dump($_COOKIE);
                    if(!$_COOKIE[$ip]){//如果cookie存在 代表uv不增加
                        $weixin->uv = $weixin->uv+1;
                    }
                    setcookie($ip,1, time()+3600*24);//ip 24小时的cookie
                    $weixin->save();
                    $url = $rconfig['shareurl'];
                    echo $url;
                    Request::instance()->redirect($url);exit;
                }else{
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtmnb/error/1';
                    Request::instance()->redirect($url);exit;
                }
            }else{
                $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtmnb/error/2';
                Request::instance()->redirect($url);exit;
            }
        }else{
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtmnb/error/3';
            Request::instance()->redirect($url);exit;
        }
    }
    public function action_error($i){
        die('分享url未填写'.$i);
    }
    public function action_user_snsapi_base(){
        $url = 'qr_hb';
        $bid = $this->bid;
        $app = 'mnb';
        require_once Kohana::find_file('vendor', 'oauth/selfoauth.class');
        $biz = ORM::factory('qwt_oauth')->where('id','=',3)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;
        $wx = new Wxoauth(3,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("mnb_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            // echo '<pre>';
            // var_dump($token);
            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find();
            $user->myopenid = $token['openid'];
            $user->save();

            $_SESSION['qwt'.$app]['myopenid'] = $user->myopenid;
            // var_dump($_SESSION);
            // exit;
            Request::instance()->redirect('/qwt'.$app.'/'.$url."?hb_code=".urlencode($_GET['hb_code']));
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/qwt/ticket";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwtmnbbid:', 'ticket');//写入日志，可以删除
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
    public function action_api_ticket($cardId,$bid) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/qwt/ticket";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwtmnbbid:', 'ticket');//写入日志，可以删除
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
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_mnb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/mnb/cert/cert.pem";
        $key_file = DOCROOT."qwt/mnb/cert/key.pem";
        //$rootca_file=DOCROOT."mnb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_mnbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_mnbfile_cert')->find();
        $file_key = ORM::factory('qwt_mnbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_mnbfile_key')->find();
        //$file_rootca = ORM::factory('qwt_mnbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_mnbfile_rootca')->find();

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
