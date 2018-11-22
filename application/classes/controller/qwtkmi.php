<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtkmi extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    public $methodVersion='3.0.0';
    var $wx;
    var $client;
    public function before() {
        Database::$default = "kmi";
        parent::before();
        if (Request::instance()->action == 'jump') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'kmpass') return;
        if (Request::instance()->action == 'kmtest') return;
    }
    public function action_delete($appid){
        $mem = Cache::instance('memcache');
        $cachename='wechat_access_token'.$appid;
        $result = $mem->get($cachename);
        var_dump($result);
        $result = $mem->delete($cachename);
        var_dump($result);
    }
    //微信卡劵中间跳转
    public function action_jump(){
        $password =$_GET['psd'];
        $aaac=base64_decode($password);
        //按逗号分离字符串
        $hello = explode(',',$aaac);
        $check = ORM::factory('qwt_kmitid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find()->state;
        $num = ORM::factory('qwt_kmitid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find()->num;
        $openid = ORM::factory('qwt_kmitid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find()->openid;
        if($check!=$num){
            $checks =ORM::factory('qwt_kmitid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find();
            $checks->state++;
            $checks->save();
            $url = '/qwtkmi/ticket/'.$hello[2].'/'.$hello[3];
            Request::instance()->redirect($url);
        }else{
            $config = ORM::factory('qwt_kmicfg')->getCfg($hello[3],1);
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$hello[3])->find()->appid;
            $wx = new Wxoauth($hello[3],$options);
            $msg['msgtype'] = 'text';
            $msg['touser'] = $openid;
            $msg['text']['content'] = '您的微信卡劵以领取完毕，请勿重复领取';
            $result=$wx->sendCustomMessage($msg);
            var_dump($result);

        }
    }
    public function action_kmtest(){
        // $this->template = 'tpl/blank';
        $view = "weixin/qwt/kmi_text";
        $this->template->content = View::factory($view);
    }
    //微信卡劵
    public function action_kmpass($id,$bid,$num_iid){
        $this->template = 'tpl/blank';
        self::before();
        $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
        $regex1='「%a」';
        $regex2='「%b」';
        $regex3='「%c」';
        $num1=strpos($km_text,$regex1);
        $num2=strpos($km_text,$regex2);
        $num3=strpos($km_text,$regex3);
        $result[0]=substr($km_text,0,$num1);
        if($num2&&$num3){
            $result[1]=substr($km_text,$num1+8,$num2-$num1-8);
            $result[2]=substr($km_text,$num2+8,$num3-$num2-8);
            $result[3]=substr($km_text,$num3+8);
        }elseif($num2){
            $result[1]=substr($km_text,$num1+8,$num2-$num1-8);
            $result[3]=substr($km_text,$num2+8);
        }else{
            $result[3]=substr($km_text,$num1+8);
        }
        $password1 = ORM::factory('qwt_kmikm')->where('id','=',$id)->find()->password1;
        $password2 = ORM::factory('qwt_kmikm')->where('id','=',$id)->find()->password2;
        $password3 = ORM::factory('qwt_kmikm')->where('id','=',$id)->find()->password3;
        $km_text = str_replace("「%a」",$password1,$km_text);
        $password = $password1;
        if($password2){
            $km_text = str_replace("「%b」",$password2,$km_text);
            $password = $password.','.$password2;
            if($password3){
                $km_text = str_replace("「%c」",$password3,$km_text);
                $password = $password.','.$password3;
            }
        }
        $view = "weixin/qwt/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('result',$result)
            ->bind('km_text', $km_text)
            ->bind('password1', $password1)
            ->bind('password2', $password2)
            ->bind('password3', $password3);
    }
    public function action_ticket($cardId,$bid) {
        $this->template = 'tpl/blank';
        self::before();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        $view = "weixin/qwt/ticket";
        $config=ORM::factory('qwt_kmicfg')->getCfg($bid,1);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        // echo "<pre>";
        // var_dump($cardId);

        $jsapi = $wx->getJsSign($callback_url);

        // var_dump($jsapi);
        $ticket = $wx->getJsCardTicket();
        // var_dump($ticket);
        $sign = $wx->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));
        // var_dump($sign);
        // echo "<pre>";
        // exit();
        $this->template->content = View::factory($view)
            ->bind('cardId', $cardId)
            ->bind('jsapi', $jsapi)
            ->bind('ticket', $ticket)
            ->bind('sign', $sign);
    }
}
