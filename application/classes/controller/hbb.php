<?php defined('SYSPATH') or die('No direct script access.');

class Controller_hbb extends Controller_Base {
    public $template = 'weixin/hbb/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public $appId = 'wx47384b8b7a68241e';
    public $access_token;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "hbb";
        parent::before();

        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'component_access_token') return;
        if (Request::instance()->action == 'dcomponent_access_token') return;
        if (Request::instance()->action == 'access_token') return;
        if (Request::instance()->action == 'daccess_token') return;
        if (Request::instance()->action == 'sendcustommsg') return;
        if (Request::instance()->action == 'userinfo') return;
        if (Request::instance()->action == 'hack') return;
        $_SESSION =& Session::instance()->as_array();

        if (!$_GET['openid']) {
            if (!$_SESSION['hbb']['bid']) die('页面已过期。请重新点击相应菜单');
            if (!$_SESSION['hbb']['openid']) die('Access Deined..请重新点击相应菜单');
        }

        $this->config = $_SESSION['hbb']['config'];
        $this->openid = $_SESSION['hbb']['openid'];
        $this->bid = $_SESSION['hbb']['bid'];
        $this->uid = $_SESSION['hbb']['uid'];
        $this->access_token = $_SESSION['hbb']['access_token'];

        if ($_GET['debug']) print_r($_SESSION['hbb']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    public function action_hack($bid,$openid){ //用户信息
        $mem = Cache::instance('memcache');
        $hack_key = "hongbao:kouling_error_$bid_$openid";
        $hack_count = (int)$mem->get($hack_key);
        echo $hack_count;
        exit;
    }
    public function action_userinfo($openid){ //用户信息
        $this->template = 'tpl/blank';
        self::before();
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth(1,'hbb',$this->appId);
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
        $wx = new Wxoauth(1,'hbb',$this->appId);
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
        $wx = new Wxoauth(1,'hbb',$this->appId);
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
        $cachename1 ='hbb.access_token'.$bid;
        $ctoken = $mem->delete($cachename1);
        var_dump($ctoken);
        exit;
    }
}
