<?php defined('SYSPATH') or die('No direct script access.');

class Controller_afu extends Controller_Base{
    public $template = 'tpl/blank';
    public $webappkey = '23656856';
    public $phpappkey = '23657631';
    public $appsecret = '3b94e6488bac4f18d9968263ac7a9a90';

    public function before() {
        Database::$default = "afu";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_afu(){
        // $view = "weixin/afu/afu";
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        $config['webappkey'] = $this->webappkey;
        $c = new TopClient;
        $c->appkey = $this->phpappkey;
        $c->secretKey = $this->appsecret;
        $req = new AlibabaInteractSensorAuthorizeRequest;
        $resp = $c->execute($req);
        // $this->template->content = View::factory($view)->bind('config', $config);
        'https://oauth.taobao.com/authorize?response_type=code&client_id=23657631&redirect_uri=http://jfb.smfyun.com/afu/oauth&state=1212&view=web'
    }
    public function action_oauth(){
        // $view = "weixin/afu/afu";
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        $config['webappkey'] = $this->webappkey;
        $c = new TopClient;
        $c->appkey = $this->phpappkey;
        $c->secretKey = $this->appsecret;
        $url = 'https://oauth.taobao.com/token';
        $postfields= array('grant_type'=>'authorization_code',
        'client_id'=>$this->phpappkey,
        'client_secret'=>$this->appsecret,
        'code'=>'test',
        'redirect_uri'=>'http://www.test.com');
        $post_data = '';
        foreach($postfields as $key=>$value){
             $post_data .="$key=".urlencode($value)."&";}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);
        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post_data,0,-1));
        $output = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $httpStatusCode;
        curl_close($ch);
        var_dump($output);
        // $this->template->content = View::factory($view)->bind('config', $config);
    }
}
