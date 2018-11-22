<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwttbt extends Controller_Base {
    // public $template = 'weixin/smfyun/tbt/tpl/fftpl';
    public $template = 'weixin/smfyun/hby/tpl/blank';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://tbt.smfyun.com/qwttbt/';
    var $wx;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        //if (Request::instance()->action == 'index_oauth') return;
        $this->openid = $_SESSION['qwttbt']['openid'];
        $this->userinfo = $_SESSION['qwttbt']['userinfo'];
        $this->bid = $_SESSION['qwttbt']['bid'];
        if(!$this->openid) die('请重新点击链接谢谢！');
        if(!$this->bid) die('请重新点击链接谢谢！!');
        // $this->uid = $_SESSION['qwttbt']['uid'];
        //只能通过微信打开
    //     if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['qwtwfbs']['bid']) die('请通过微信访问。');
    }

    public function action_login(){
        // echo $this->openid;
        $user = ORM::factory('qwt_tbtqrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        Kohana::$log->add('qwt_tbt:userinfo:', print_r($this->userinfo,true));
        Kohana::$log->add('qwt_tbt:openid:', print_r($this->openid,true));
        if($user->flag == 1&& $user->logintime+30*24*3600>time()){
            // echo $user->flag;
            $user->logintime = time();
            $user->save();
            header('location:https://j.youzan.com/1bJzYY');
            exit;
        }
        if($_POST['tel']){
            $user = ORM::factory('qwt_tbtqrcode')->where('bid','=',$this->bid)->where('telphone','=',$_POST['tel'])->find();
            if($user->name == $_POST['name']){
                $user->password = $_POST['editpwd'];
                $user->save();
                $result['state'] = 1;
            }else{
                $result['error'] = '姓名和手机号不相符！';
                $result['state'] = 0;
            }
            echo json_encode($result);
            exit;
        }
        // if($_POST['editpwd']){
        //     $user->password = $_POST['editpwd'];
        //     $user->save();
        //     $result['state'] = 1;
        //     echo json_encode($result);
        //     exit;
        // }
        if($_POST['user']&&$_POST['passwd']){
            $tel_user = ORM::factory('qwt_tbtqrcode')->where('bid','=',$this->bid)->where('telphone','=',$_POST['user'])->find();
            if(!$tel_user->id){
                $result['error'] = '该用户名不存在！';
            }else if($tel_user->password!=$_POST['passwd']){
                $result['error'] = '密码不正确！';
            }else if($tel_user->flag!=1){
                $result['error'] = '审核未通过！';
            }
            if(!$result['error']){
                $tel_user->rem = $_POST['rem']?$_POST['rem']:0;
                $tel_user->logintime = time();
                // if(strpos($tel_user->openid,'wap_user') === false){
                //     //老用户没有openid的
                //     if(!$tel_user->openid){
                //         $tel_user->openid = $this->openid;
                //         $tel_user->nickname = $this->userinfo['nickname'];
                //         $tel_user->headimgurl = $this->userinfo['headimgurl'];
                //     }
                // }else{//老用户有wap_user的
                //     $tel_user->openid = $this->openid;
                //     $tel_user->nickname = $this->userinfo['nickname'];
                //     $tel_user->headimgurl = $this->userinfo['headimgurl'];
                // }
                $tel_user->openid = $this->openid;
                $tel_user->nickname = $this->userinfo['nickname'];
                $tel_user->headimgurl = $this->userinfo['headimgurl'];
                $tel_user->save();
                header('location:https://j.youzan.com/1bJzYY');
                exit;
            }
        }
        $this->template->content = View::factory('weixin/smfyun/tbt/login')->bind('result',$result)->bind('user',$user);
    }
    public function action_registration(){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        if($_POST['tbt']){
            $has = ORM::factory('qwt_tbtqrcode')->where('bid','=',$this->bid)->where('telphone','=',$_POST['tbt']['tel'])->find();
            if($has->id){
                $result['error'] = '手机号已经被注册过了！';
            }else{
                // echo $this->uid;
                // echo '<pre>';
                // var_dump($_POST['tbt']);
                Kohana::$log->add('qwt_tbt:registration:', print_r($_POST,true));
                Kohana::$log->add('qwt_tbt:tel:', print_r($_POST['tbt']['tel'],true));
                $user = ORM::factory('qwt_tbtqrcode');
                $user->openid = $this->openid;
                $user->nickname = $this->userinfo['nickname'];
                $user->headimgurl = $this->userinfo['headimgurl'];
                $user->name = $_POST['tbt']['name'];
                $user->telphone = $_POST['tbt']['tel'];
                $user->password = $_POST['tbt']['passwd'];
                $user->type = $_POST['tbt']['type'];
                $user->bid = $this->bid;
                $user->address = $_POST['tbt']['address'];
                $user->shop_pic = $wx->downjpg($_POST['tbt']['shopimg']);
                $user->ic_pic = $wx->downjpg($_POST['tbt']['userimg']);
                $user->save();
                // exit;
                header('location:login');
                exit;
            }
        }
        $this->template->content = View::factory('weixin/smfyun/tbt/registration')
            ->bind('jsapi', $jsapi)
            ->bind('result', $result);
    }
}
