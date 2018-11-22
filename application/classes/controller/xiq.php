<?php defined('SYSPATH') or die('No direct script access.');

class Controller_xiq extends Controller_Base{
    public $template = 'tpl/blank';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'smfyun';

    public function before() {
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_xiqiu(){
        //用神码浮云来做jssdk
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $biz = ORM::factory('qwt_login')->where('id','=',6)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

        $wx = new Wxoauth(6,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $view = 'weixin/xiq/index';
        $this->template->title = '戏球';
        $this->template->content = View::factory($view)
                ->bind('jsapi',$jsapi);
    }
}
