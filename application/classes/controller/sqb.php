<?php defined('SYSPATH') or die('No direct script access.');

class Controller_sqb extends Controller_Base {
    public $template = 'weixin/sqb/tpl/blank';
    public $access_token;
    public $config;
    public $openid;
    public $uid;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://dd.smfyun.com/sqb/';
    var $we;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();

        if (Request::instance()->action == 'test') return;
        // if (Request::instance()->action == 'shequnguangchang') return;
        // if (Request::instance()->action == 'liaotian') return;
        $_SESSION =& Session::instance()->as_array();

        $this->config = $_SESSION['sqb']['config'];
        $this->openid = $_SESSION['sqb']['openid'];
        $this->uid = $_SESSION['sqb']['uid'];
        $this->access_token = $_SESSION['sqb']['access_token'];

        if ($_GET['debug']) print_r($_SESSION['sqb']);
        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['sqba']['bid']) die('请通过微信访问。');
        if (Request::instance()->action != 'index_oauth' && Request::instance()->action != 'login' && $_SESSION['sqb']['login'] != 1) {
            // header('location:/qwta/login');
            header('location:/sqb/index_oauth/login');
            exit;
        }
    }

    public function after() {
        parent::after();
    }

    //Oauth 入口
    public function action_index_oauth($url='login') {
        // $config = ORM::factory('sqb_cfg')->getCfg($bid);

        //没有 Oauth 授权过才需要
        if (!$_SESSION['sqb']['openid']) {

            require Kohana::find_file('vendor', 'weixin/wechat.class');
            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

            $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
            if (!$_GET['callback']) $callback_url .= "{$split}callback=1";

            $we = new Wechat(array('appid'=>'wxd3a678cfeb03e3a3', 'appsecret'=>'661fb2647a804e14ded1f65fad682695'));

            if (!$_GET['callback']) {
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            } else {
                $token = $we->getOauthAccessToken();
                $userinfo = $we->getOauthUserinfo($token['access_token'], $token['openid']);
                $openid = $userinfo['openid'];
            }
            if (!$openid) $_SESSION['sqb'] = NULL;

            if ($openid) {
                $userobj = ORM::factory('sqb_qrcode')->where('openid', '=', $openid)->find();
                $userobj->values($userinfo);
                $userobj->save();

                $_SESSION['sqb']['config'] = $config;
                $_SESSION['sqb']['openid'] = $openid;
                $_SESSION['sqb']['uid'] = $userobj->id;
            }
        }

        Request::instance()->redirect('/sqb/'.$url);
    }
    public function action_login(){
        $user = ORM::factory('sqb_qrcode')->where('openid','=',$this->openid)->find();
        if ($user->tel) {
            $_SESSION['sqb']['id'] = $user->id;
            $_SESSION['sqb']['login'] = 1;
        }
        if ($_SESSION['sqb']['login'] == 1) {
            echo $_SESSION['sqb']['login'];
            Request::instance()->redirect('/sqb/index');
        }
        $view = 'weixin/sqb/login';
        $result['status'] = 'normal';
        if ($_POST) {
            $tel = ORM::factory('sqb_qrcode')->where('tel','=',$_POST['tel'])->find();
            if ($tel->id) {
                $result['status'] = 'error';
            }else{
                $user = ORM::factory('sqb_qrcode')->where('openid','=',$this->openid)->find();
                $user->tel = $_POST['tel'];
                // echo "<pre>";
                // var_dump($user2->tel);
                // exit;
                $user->save();
                // $user2 = ORM::factory('sqb_qrcode')->where('username','=',$_POST['username'])->find();
                // $password = $user2->password;
                // if ($_POST['password'] == $password) {
                //     $user2->regist = 1;
                //     $user2->save();
                //     $_SESSION['sqb']['login'] = 1;
                $_SESSION['sqb']['id'] = $user->id;
                //     Request::instance()->redirect('/sqb/index');
                // }else{
                    // $result['status'] = 'error';
                $_SESSION['sqb']['login'] = 1;
                Request::instance()->redirect('/sqb/index');
            }
        }
        // if ($user->regist == 0) {
        //     $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        //     $username = "";
        //     for ( $i = 0; $i < 5; $i++ )
        //     {
        //     $username .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        //     }
        //     $username .= $user->id;
        //     $user->username = $username;
        //     $chars = "0123456789";
        //     $password = "";
        //     for ( $i = 0; $i < 6; $i++ )
        //     {
        //     $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        //     }
        //     $user->password = $password;
        //     $user->save();
        //     $result['status'] = 'new';
        // }
            // 密码字符集，可任意添加你需要的字符
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
        $this->template->content = View::factory($view)
            ->bind('result',$result);
            // ->bind('username',$username)
            // ->bind('password',$password);
    }
    public function action_index(){
        $view = 'weixin/sqb/index';
        $user = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
        $avator = $user->headimgurl;
        $rmsg = ORM::factory('sqb_talk')->where('tid','=',$_SESSION['sqb']['id'])->where('type','=',1)->where('status','=',1)->find();
        if ($rmsg->id) {
            $tome = 1;
        }else{
            $rome = 2;
        }
        $smsg = ORM::factory('sqb_talk')->where('qid','=',$_SESSION['sqb']['id'])->where('type','=',2)->where('status','=',1)->find();
        if ($smsg->id) {
            $meto = 1;
        }else{
            $meto = 2;
        }
        $notice = ORM::factory('sqb_cfg')->where('key','=','notice')->find()->value;
        $this->template->content = View::factory($view)
            ->bind('user',$user)
            ->bind('notice',$notice)
            ->bind('tome',$tome)
            ->bind('meto',$meto)
            ->bind('avator',$avator);
    }
    public function action_liaotian($tid){
        $view = 'weixin/sqb/liaotian';
        $user = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
        $tuser = ORM::factory('sqb_qrcode')->where('id','=',$tid)->find();
        $msg = ORM::factory('sqb_talk')->where('qid','=',$_SESSION['sqb']['id'])->where('tid','=',$tid)->find_all()->as_array();
        $qid = $_SESSION['sqb']['id'];
        DB::query(Database::UPDATE,"UPDATE `sqb_talks` SET `status` = 2  where qid = $qid and tid = $tid and type = 2 and status = 1")->execute();
        if ($_GET['content']) {
            $talk = ORM::factory('sqb_talk');
            $talk->qid = $_SESSION['sqb']['id'];
            $talk->tid = $tid;
            $talk->status = 1;
            $talk->type = 1;
            DB::query(Database::UPDATE,"UPDATE `sqb_talks` SET `last` = 2  where qid = $qid and tid = $tid and last = 1")->execute();
            $talk->last = 1;
            $talk->content = $_GET['content'];
            $talk->save();
            $time = time();
            $result['content'] = $_GET['content'];
            $result['time'] = date('Y-m-d H:i',$time);
            echo json_encode($result);
            exit;
        }
        $this->template->content = View::factory($view)
            ->bind('msg',$msg)
            ->bind('tuser',$tuser)
            ->bind('user',$user);
    }
    public function action_beiliao($qid){
        $view = 'weixin/sqb/beiliao';
        $user = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
        $fuser = ORM::factory('sqb_qrcode')->where('id','=',$qid)->find();
        $msg = ORM::factory('sqb_talk')->where('tid','=',$_SESSION['sqb']['id'])->where('qid','=',$qid)->find_all()->as_array();
        $tid = $_SESSION['sqb']['id'];
        DB::query(Database::UPDATE,"UPDATE `sqb_talks` SET `status` = 2  where qid = $qid and tid = $tid and type = 1 and status = 1")->execute();
        if ($_GET['content']) {
            $talk = ORM::factory('sqb_talk');
            $talk->qid = $qid;
            $talk->tid = $tid;
            $talk->status = 1;
            $talk->type = 2;
            DB::query(Database::UPDATE,"UPDATE `sqb_talks` SET `last` = 2  where qid = $qid and tid = $tid and last = 1")->execute();
            $talk->last = 1;
            $talk->content = $_GET['content'];
            $talk->save();
            $time = time();
            $result['content'] = $_GET['content'];
            $result['time'] = date('Y-m-d H:i',$time);
            echo json_encode($result);
            exit;
        }
        $this->template->content = View::factory($view)
            ->bind('msg',$msg)
            ->bind('fuser',$fuser)
            ->bind('user',$user);
    }
    public function action_woyaozhaoren(){
        $view = 'weixin/sqb/woyaozhaoren';
        $msg = ORM::factory('sqb_talk')->where('qid','=',$_SESSION['sqb']['id'])->where('last','=',1)->find_all()->as_array();
        $this->template->content = View::factory($view)
            ->bind('msg',$msg);
    }
    public function action_yourenzhaowo(){
        $view = 'weixin/sqb/yourenzhaowo';
        $msg = ORM::factory('sqb_talk')->where('tid','=',$_SESSION['sqb']['id'])->where('last','=',1)->find_all()->as_array();
        $this->template->content = View::factory($view)
            ->bind('msg',$msg);
    }
    public function action_gerenxinxi(){
        $view = 'weixin/sqb/gerenxinxi';
        $user = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
        if ($_POST) {
            $user2 = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
            $user2->job = $_POST['text']['job'];
            $user2->company = $_POST['text']['company'];
            $user2->area = $_POST['text']['area'];
            $user2->ihave = $_POST['text']['ihave'];
            $user2->ineed = $_POST['text']['ineed'];
            $user2->fansnum = $_POST['text']['fansnum'];
            $user2->sellsnum = $_POST['text']['sellsnum'];
            if ($user->admin==1) {
                if ($_POST['notice']) {
                    $notice2 = ORM::factory('sqb_cfg')->where('key','=','notice')->find();
                    $notice2->value = $_POST['notice'];
                    $notice2->save();
                }
            }
            $user2->save();
            $result['ok'] = 1;
            Request::instance()->redirect('sqb/index');
        }
        if ($user->admin==1) {
            $notice = ORM::factory('sqb_cfg')->where('key','=','notice')->find();
        }
        $avator = $user->headimgurl;
        $this->template->content = View::factory($view)
            ->bind('notice',$notice)
            ->bind('result',$result)
            ->bind('user',$user)
            ->bind('avator',$avator);
    }
    public function action_lianxiguanjia(){
        $view = 'weixin/sqb/lianxiguanjia';
        $this->template->content = View::factory($view);
    }
    public function action_password(){
        $view = 'weixin/sqb/password';
        if ($_POST) {
            $user2 = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
            if ($_POST['confirm']['account']==$user2->username) {
                if ($_POST['confirm']['password']==$user2->password) {
                    if ($_POST['new']['account']!='新账号') {
                        $user2->username = $_POST['new']['account'];
                        $user2->save();
                        $result['err'] = 3;
                    }
                    if ($_POST['new']['password']!='') {
                        if ($_POST['new']['password']==$_POST['check']['password']) {
                            $user2->password = $_POST['new']['password'];
                            $user2->save();
                            $result['err'] = 3;
                        }else{
                            $result['err'] = 2;
                        }
                    }
                }else{
                    $result['err'] = 1;
                }
            }else{
                $result['err'] = 1;
            }
        }
        $user = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
        $this->template->content = View::factory($view)
            ->bind('result',$result)
            ->bind('user',$user);
    }
    public function action_shequnguangchang(){
        $view = 'weixin/sqb/shequnguangchang';
        $result['range'] = 'all';
        $result['type'] = '1';
        $url = '../sqb/shequnguangchang';
        $pagsize=6;
        $time = time()-Date::DAY*7;
        $partys=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 1 and lastupdate > $time order by lastupdate desc limit $pagsize offset 0 ")->execute()->as_array();
        if ($_POST['keyword']) {
            $keyword = $_POST['keyword'];
            $sql='SELECT * from  sqb_posts where type = 1 and lastupdate > '.$time.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset 0';
            // echo $sql;
            // exit;
            $partys=DB::query(Database::SELECT,$sql)->execute()->as_array();
        }
        if ($_GET['scroll']) {
            $offset = $pagsize*$_GET['scroll'];
            if($_GET['search']){
                $keyword = $_GET['search'];
                $sql='SELECT * from  sqb_posts where type = 1 and lastupdate > '.$time.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset '.$offset;
                $party=DB::query(Database::SELECT,$sql)->execute()->as_array();
            }else{
                $party=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 1 and lastupdate > $time order by lastupdate desc limit $pagsize offset $offset ")->execute()->as_array();
            }
            echo json_encode($party);
            exit;
        }
        $this->template->content = View::factory($view)
            ->bind('_POST',$_POST)
            ->bind('url',$url)
            ->bind('partys',$partys)
            ->bind('result',$result);
    }
    public function action_woyaozhaoqudao(){
        $view = 'weixin/sqb/shequnguangchang';
        $result['range'] = 'all';
        $result['type'] = '2';
        $url = '../sqb/woyaozhaoqudao';
        $pagsize=6;
        $time = time()-Date::DAY*7;
        $partys=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 2 and lastupdate > $time order by lastupdate desc limit $pagsize offset 0 ")->execute()->as_array();
        if ($_POST['keyword']) {
            $keyword = $_POST['keyword'];
            $sql='SELECT * from  sqb_posts where type = 2 and lastupdate > '.$time.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset 0';
            // echo $sql;
            // exit;
            $partys=DB::query(Database::SELECT,$sql)->execute()->as_array();
        }
        if ($_GET['scroll']) {
            $offset = $pagsize*$_GET['scroll'];
            if($_GET['search']){
                $keyword = $_GET['search'];
                $sql='SELECT * from  sqb_posts where type = 2 and lastupdate > '.$time.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset '.$offset;
                $party=DB::query(Database::SELECT,$sql)->execute()->as_array();
            }else{
                $party=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 2 and lastupdate > $time order by lastupdate desc limit $pagsize offset $offset ")->execute()->as_array();
            }
            echo json_encode($party);
            exit;
        }
        $this->template->content = View::factory($view)
            ->bind('url',$url)
            ->bind('partys',$partys)
            ->bind('result',$result);
    }
    public function action_wodefabu(){
        $view = 'weixin/sqb/shequnguangchang';
        $result['range'] = 'me';
        $result['type'] = '1';
        $url = '../sqb/wodefabu';
        $pagsize=6;
        $time = time()-Date::DAY*7;
        $uid = $_SESSION['sqb']['id'];
        $partys=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 1 and qid = $uid order by lastupdate desc limit $pagsize offset 0 ")->execute()->as_array();
        if ($_POST['keyword']) {
            $keyword = $_POST['keyword'];
            $sql='SELECT * from  sqb_posts where type = 1 and qid = '.$uid.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset 0';
            // echo $sql;
            // exit;
            $partys=DB::query(Database::SELECT,$sql)->execute()->as_array();
        }
        if ($_GET['scroll']) {
            $offset = $pagsize*$_GET['scroll'];
            if($_GET['search']){
                $keyword = $_GET['search'];
                $sql='SELECT * from  sqb_posts where type = 1 and qid = '.$uid.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset '.$offset;
                $party=DB::query(Database::SELECT,$sql)->execute()->as_array();
            }else{
                $party=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 1 and qid = $uid order by lastupdate desc limit $pagsize offset $offset ")->execute()->as_array();
            }
            echo json_encode($party);
            exit;
        }
        $this->template->content = View::factory($view)
            ->bind('time',$time)
            ->bind('url',$url)
            ->bind('partys',$partys)
            ->bind('result',$result);
    }
    public function action_wodezhaoqudao(){
        $view = 'weixin/sqb/shequnguangchang';
        $result['range'] = 'me';
        $result['type'] = '2';
        $url = '../sqb/wodezhaoqudao';
        $pagsize=6;
        $time = time()-Date::DAY*7;
        $uid = $_SESSION['sqb']['id'];
        $partys=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 2 and qid = $uid order by lastupdate desc limit $pagsize offset 0 ")->execute()->as_array();
        if ($_POST['keyword']) {
            $keyword = $_POST['keyword'];
            $sql='SELECT * from  sqb_posts where type = 2 and qid = '.$uid.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset 0';
            // echo $sql;
            // exit;
            $partys=DB::query(Database::SELECT,$sql)->execute()->as_array();
        }
        if ($_GET['scroll']) {
            $offset = $pagsize*$_GET['scroll'];
            if($_GET['search']){
                $keyword = $_GET['search'];
                $sql='SELECT * from  sqb_posts where type = 2 and qid = '.$uid.' and (title like "%'.$keyword.'%" or content like "%'.$keyword.'%") order by lastupdate desc limit '.$pagsize.' offset '.$offset;
                $party=DB::query(Database::SELECT,$sql)->execute()->as_array();
            }else{
                $party=DB::query(Database::SELECT,"SELECT * from  sqb_posts where type = 2 and qid = $uid order by lastupdate desc limit $pagsize offset $offset ")->execute()->as_array();
            }
            echo json_encode($party);
            exit;
        }
        $this->template->content = View::factory($view)
            ->bind('time',$time)
            ->bind('url',$url)
            ->bind('partys',$partys)
            ->bind('result',$result);
    }
    public function action_update(){
        if ($_GET['update']) {
            $pid = $_GET['update'];
            $post = ORM::factory('sqb_post')->where('qid','=',$_SESSION['sqb']['id'])->where('id','=',$pid)->find();
            $post->lastupdate = time();
            $post->save();
            echo "发布成功";
            exit;
        }
    }
    public function action_xinfabu(){
        $view = 'weixin/sqb/xinfabu';
        if ($_POST) {
            if ($_POST['title'] == '' || $_POST['content'] == '') {
                $title = $_POST['title'];
                $result['err'] = 1;
            }else{
                $user = ORM::factory('sqb_qrcode')->where('id','=',$_SESSION['sqb']['id'])->find();
                $user2 = ORM::factory('sqb_post');
                $user2->qid = $user->id;
                $user2->job = $user->job;
                $user2->headimgurl = $user->headimgurl;
                $user2->type = $_POST['type'];
                $user2->title = $_POST['title'];
                $user2->content = $_POST['content'];
                $user2->save();
                Request::instance()->redirect('sqb/wodefabu');
            }
        }
        $this->template->content = View::factory($view)
            ->bind('title',$title)
            ->bind('result',$result);
    }
    public function action_logout(){
        $_SESSION['sqb'] = null;
        header('location:/sqb/index_oauth/login');
        exit;
    }
}
