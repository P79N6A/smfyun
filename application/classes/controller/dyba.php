<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dyba extends Controller_Base {

    public $template = 'weixin/dyb/tpl/atpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "dyb";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        if (Request::instance()->action == 'oauthscript') return;
        if (Request::instance()->action == 'oauthscript2') return;
        $this->bid = $_SESSION['dyba']['bid'];
        $this->config = $_SESSION['dyba']['config'];
        $this->access_token=ORM::factory('dyb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/dyba/login');
            header('location:/dyba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('dyb_qrcode')->where('bid', '=', $this->bid)->where('openid','!=','')->count_all();
            $todo['tickets'] = ORM::factory('dyb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            $todo['items'] = ORM::factory('dyb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            $todo['all'] = $todo['items'] + $todo['users'];
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }

    public function action_index() {
        $this->action_login();
    }

    public function action_otag(){
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $appId = ORM::factory('dyb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;

        $appSecret = ORM::factory('dyb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;

        $client = new YZTokenClient($this->access_token);

        $tag_name =  ORM::factory('dyb_cfg')->where('bid','=',$this->bid)->where('key','=','tag_name')->find()->value;

        $sql = DB::query(Database::SELECT,"SELECT openid as OP FROM dyb_qrcodes where (`bid`=$this->bid and `ticket`!= 'NULL') or (`bid`=$this->bid and `fopenid`!= 'NULL')");
        $openid = $sql->execute()->as_array();
        for($p=0;$openid[$p];$p++){
            //echo $openid[$p]['OP'].'<br>';
            $method = 'youzan.users.weixin.follower.tags.add';
            $params = [
            'tags' =>$tag_name,

            'weixin_openid' =>$openid[$p]['OP'],
            ];
            $test=$client->post($method, $this->methodVersion, $params, $files);
            // echo $appId.'<br>';
            // echo $appSecret.'<br>';
            // echo '<pre>';
            // var_dump($test);
            // echo '</pre>';
          }
    }
    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('dyb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        // $cert_file = DOCROOT."dyb/tmp/$bid/cert.{$config['appsecret']}.pem";
        // $key_file = DOCROOT."dyb/tmp/$bid/key.{$config['appsecret']}.pem";
        // $rootca_file=DOCROOT."dyb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        // $result['cert_file_exists'] = file_exists($cert_file);
        // $result['key_file_exists'] = file_exists($key_file);
        // $result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('dyb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('dyb_login')->where("id","=",$bid)->find()->user;
             //证书上传
            // if ($_FILES['cert']['error'] == 0) {
            //     @mkdir(dirname($cert_file));
            //     $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
            //      $result['ok'] += $ok;
            //     $result['err1'] = '证书文件已更新！';
            // }

            // if ($_FILES['key']['error'] == 0) {
            //     @mkdir(dir($key_file));
            //     $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
            //      $result['ok'] += $ok;
            //     $result['err1'] = '证书文件已更新！';
            // }

            //  if ($_FILES['rootca']['error'] == 0) {
            //     @mkdir(dir($rootca_file));
            //     $ok = move_uploaded_file($_FILES['rootca']['tmp_name'], $rootca_file);
            //      $result['ok'] += $ok;
            //     $result['err1'] = '证书文件已更新！';
            // }

            // if (file_exists($cert_file)) $cfg->setCfg($bid, 'dyb_file_cert', '', file_get_contents($cert_file));
            // if (file_exists($key_file)) $cfg->setCfg($bid, 'dyb_file_key', '', file_get_contents($key_file));
            // if (file_exists($rootca_file)) $cfg->setCfg($bid, 'dyb_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('dyb_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            // echo "<pre>";
            // var_dump($_POST['text']);
            // echo "<pre>";
            // exit;
            $cfg = ORM::factory('dyb_cfg');
            $qrfile = DOCROOT."dyb/tmp/tpl.$bid.jpg";

            //海报有效期
            if ($_POST['text']['ticket_lifetime'] >= 30) $_POST['text']['ticket_lifetime'] = 30;

            //二维码海报
            if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*400) {
                    $result['err3'] = '海报模板文件不能超过 400K';
                } else {
                    $result['ok3']++;
                    $cfg->setCfg($bid, 'tpl', '', file_get_contents($_FILES['pic']['tmp_name']));
                    @unlink($qrfile);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                }
            }

            //默认头像
            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."dyb/tmp/head.$bid.jpg";
                    $cfg->setCfg($bid, 'tplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
                    @unlink($default_head_file);
                    move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }

            if (!$result['err3']) {
                foreach ($_POST['text'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;
                }

                //海报配置
                foreach ($_POST['px'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;

                    //更新海报缓存 key
                    if ($result['ok3'] && file_exists($qrfile)) {
                        touch($qrfile);

                        $tpl = ORM::factory('dyb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('dyb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置
        // if ($_POST['area']){
        //     // print_r($_POST['area']);
        //     // exit;
        //     $area = $_POST['area'];
        //     $cfg = ORM::factory('dyb_cfg');
        //     // $count = ORM::factory('dyb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
        //     $count = $area['count'];
        //     for ($i=1; $i<=$count ; $i++) {
        //         if (!$area['city'.$i]){
        //         $area['city'.$i]='';
        //         }
        //         if (!$area['dis'.$i]){
        //         $area['dis'.$i]='';
        //         }
        //     }
        //     // if (!$area['city']){
        //     //     $area['city']='';
        //     // }
        //     // if (!$area['dis']){
        //     //     $area['dis']='';
        //     // }
        //     foreach ($area as $k=>$v) {
        //         $ok = $cfg->setCfg($bid, $k, $v);
        //         $result['ok5'] += $ok;
        //     }

        //     $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        //     //根据bid来存储 地理位置 一起存储
        // }

        // if ($_POST['tag']){
        //     $tag = $_POST['tag'];
        //     $cfg = ORM::factory('dyb_cfg');
        //     foreach ($tag as $k=>$v) {
        //         $ok = $cfg->setCfg($bid, $k, $v);
        //         $result['ok6'] += $ok;
        //     }


        //     $this->action_otag();
        //     $result['fresh'] = 1;
        //     $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);

        // }
        // if ($_POST['rsync']['switch']==1){
        //     if($this->access_token){
        //         $tag = $_POST['rsync'];
        //         $cfg = ORM::factory('dyb_cfg');
        //         foreach ($tag as $k=>$v) {
        //             $ok = $cfg->setCfg($bid, $k, $v);
        //             $result['ok7'] += $ok;
        //         }
        //     }else{
        //         $result['error7']=7;
        //     }
        //     $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        // }
        $result['tpl'] = ORM::factory('dyb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('dyb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('dyb_login')->where('id', '=', $bid)->find()->expiretime;
        $access_token = ORM::factory('dyb_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/dyb/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }

    public function action_zero() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('dyb_login')->where('id', '=', $bid)->find()->access_token;
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/dyb/admin/zero')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_lab() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('dyb_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['tag']){
            $tag = $_POST['tag'];
            $cfg = ORM::factory('dyb_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }
            $this->action_otag();
            $result['fresh'] = 1;
            $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/dyb/admin/lab')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_area() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('dyb_login')->where('id', '=', $bid)->find()->access_token;
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('dyb_cfg');
            // $count = ORM::factory('dyb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $count = $area['count'];
            for ($i=1; $i<=$count ; $i++) {
                if (!$area['city'.$i]){
                $area['city'.$i]='';
                }
                if (!$area['dis'.$i]){
                $area['dis'.$i]='';
                }
            }
            // if (!$area['city']){
            //     $area['city']='';
            // }
            // if (!$area['dis']){
            //     $area['dis']='';
            // }
            foreach ($area as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok5'] += $ok;
            }

            $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/dyb/admin/area')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_rsync() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('dyb_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('dyb_cfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('dyb_cfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/dyb/admin/rsync')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
        //有赞授权刷新脚本 七天一次
    public function action_oauthscript($bid=39){
        $this->template = 'tpl/blank';
        self::before();
        $shop = ORM::factory('dyb_login')->where('id','=',$bid)->find();
        $url="https://open.youzan.com/oauth/token";
        if($shop->access_token&&$shop->id){
            $data=array(
                "client_id"=>"49e609597c5d9c3969",
                "client_secret"=>"ab43c9f4110fb14afacf9d65b431968b",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
            }
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 成功';
            echo '</pre>';
            exit;
        }else{
            die('no id or no access_token!');
        }
    }
    //轮询版本
    public function action_oauthscript2(){
        $this->template = 'tpl/blank';
        self::before();
        $shoparr = ORM::factory('dyb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"49e609597c5d9c3969",
                "client_secret"=>"ab43c9f4110fb14afacf9d65b431968b",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            $shop->access_token = $result->access_token;
            $shop->expires_in = time()+$result->expires_in;
            $shop->refresh_token = $result->refresh_token;
            $shop->save();
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 成功';
            echo '</pre>';
        }
    }
    public function action_oauth(){
        // $shop = ORM::factory('dyb_login')->where('id', '=', $this->bid)->find();
        // $config = $this->config;
        // require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        // if($config['yz_appid']){
        //     $client = new YZTokenClient($config['yz_appid'], $config['yz_appsecert']);
        //     $method = 'youzan.shop.basic.get';
        //     $params = [
        //     ];
        //     $results = $client->post($method,$params);
        //     $shop->shopid = $results['response']['sid'];
        //     $shop->save();
        // }else{
        //     Kohana::$log->add("dyb:$this->bid:oauth", print_r('有赞参数未填', true));
        // }
        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=49e609597c5d9c3969&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/dyba/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"49e609597c5d9c3969",
            "client_secret"=>"ab43c9f4110fb14afacf9d65b431968b",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/dyba/callback'
        );
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $result=json_decode($output);

        if(isset($result->access_token))
        {
            require Kohana::find_file("vendor","kdt/YZTokenClient");
            $oauth=new YZTokenClient($result->access_token);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['id'];
            $name = $value['name'];

            $usershop = ORM::factory('dyb_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("dyba/home")."';</script>";
        }
        //Request::instance()->redirect('dyba/home');
    }

    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                if ($_POST['form']['score']){

                    $qrcode_edit = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
                    ORM::factory('dyb_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                }
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('openid','!=','');
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where('nickname', 'like', $s); //->or_where('openid', 'like', $s);
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM dyb_qrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
            $tempid=array();
              if($firstchild[0]['openid']==null)
              {
                $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
              }
              else
              {
                  for($i=0;$firstchild[$i];$i++)
                  {
                    $tempid[$i]=$firstchild[$i]['openid'];
                  }
              }
              //$qrcode = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid);
              $qrcode =$qrcode->where('fopenid', 'IN',$tempid);


        }

        //按状态搜索
        if ($_GET['lock']) {
            $result['lock'] = $_GET['lock'];
            $qrcode = $qrcode->where('lock', '=', $result['lock']);
        }

        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dyb/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->where('openid','!=','')->where('fuopenid','!=','')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/dyb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid);

        //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');

            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[15], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 17) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[15]);
                    $shipcode = iconv('gbk', 'utf-8', $data[16]);
                } else {
                    $shiptype = $data[15];
                    $shipcode = $data[16];
                }

                $order = ORM::factory('dyb_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                if ($order->status == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
                    $order->status = 1;
                    $order->shiptype = $shiptype;
                    $order->shipcode = $shipcode;
                    $order->save();
                    $i++;
                }
            }

            fclose($fh);
            $result['ok'] = "共批量发货 $i 个订单。";
        }

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('dyb_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                $order->save();
            }

            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        if ($action == 'ship' && $id) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $we = new Wechat($config);

            $order = ORM::factory('dyb_order')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();

                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['dyba']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['dyba']['shipcode'] = $_REQUEST['shipcode'];
                    $order->shiptype = $_REQUEST['shiptype'];
                    $order->shipcode = $_REQUEST['shipcode'];

                    $order->save();

                    //发微信消息给用户
                    $shipmsg = "%s，您的积分兑换奖品已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $we->sendCustomMessage($msg);
                }
                if(($order->type)==3)
                {

                    $shipmsg = "%s，您的积分兑换奖品已经充值，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $we->sendCustomMessage($msg);
                }

                //Request::instance()->redirect('dyba/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('dyb_order')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            if($countuser>0){
                $user = ORM::factory('dyb_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
                $userarr = array();
                foreach ($user as $k => $v) {
                    $userarr[$k] = $v->id;
                }
                $order = $order->where('qid', 'IN', $userarr);
            }else{
                $order = $order->and_where_open();
                $s = '%'.trim($_GET['s'].'%');
                $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
                $order = $order->and_where_close();
            }
        }

        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('dyb_qrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        $active_type="total";
        //分类展示 1实物需发货的
         if ($_GET['type']=="object") {
            $order = $order->where('type', '=', null);
            $active_type="object";
        }
        //2虚拟话费和流量充值
         if ($_GET['type']=="fare") {
            $order = $order->where('type', '=', 3);
            $active_type="fare";
        }
        //3优惠码
         if ($_GET['type']=="code") {
            $order = $order->where('type', '=', 4);
            $active_type="code";
        }

        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
             $tempname="全部";
            switch ($_GET["tag"]) {
                case 'fare':
                    $orders=$order->where('type','=',3)->limit(1000)->find_all();
                    $tempname="充值";
                    break;
                case'object':
                    $orders=$order->where('type','=',null)->limit(1000)->find_all();
                    $tempname="实物";
                    break;
                case'code':
                    $orders=$order->where('type','=',4)->limit(1000)->find_all();
                    $tempname="优惠码";
                    break;
                default:
                    $orders = $order->find_all();
                    break;
            }
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('id', '收货人', '收货电话', '收货城市', '收货地址', '备注', '兑换产品','金额','消耗积分', '订单时间', '是否有关注', '产品ID', 'OpenID', '是否锁定', '直接粉丝', '间接粉丝', '物流公司', '物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($orders as $o) {
                //$count2 = ORM::factory('dyb_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('dyb_qrcode')->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('dyb_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

                //地址处理
                list($prov, $city, $dist) = explode(' ', $o->city);
                $array = array($o->id, $o->name, $o->tel, "{$prov} {$city} {$dist}", $o->address, $o->memo, $o->item->name,$o->item->price, $o->score, date('Y-m-d H:i:s', $o->createdtime), $o->user->subscribe, $o->item->id, $o->user->openid, $o->user->lock, $count2, $count3);

                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }

                fputcsv($fp, $array);
            }
            exit;
        }

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dyb/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/dyb/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('activetype',$active_type);
    }

    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        if ($_POST['yz']=='2'){
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            $appId = ORM::factory('dyb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;
            $appSecret = ORM::factory('dyb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;
            if($this->access_token){
                $client = new YZTokenClient($this->access_token);
                $method = 'youzan.ump.presents.ongoing.all';
                $params = [
                ];
                $results = $client->post($method, $this->methodVersion, $params, $files);
                // for($i=0;$results['response']['presents'][$i];$i++){
                //     $res = $results['response']['presents'][$i];
                //     $present_id[$i]=$res['present_id'];
                //     $title1[$i]=$res['title'];
            }else{
                $results[0] = 'fail';
            }
            echo json_encode($results);
            exit;
        }
        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid);

        $result['items'] = ORM::factory('dyb_item')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all();
        $iid = ORM::factory('dyb_item')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('dyb_order')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }

        $this->template->title = '奖品管理';
        $this->template->content = View::factory('weixin/dyb/admin/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $item = ORM::factory('dyb_item');
            $item->values($_POST['data']);

            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['score'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "dyb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('dyba/items');
            }
        }

        $result['action'] = 'add';
        $result['present_id']=$present_id;
        $result['title1']=$title1;
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->content = View::factory('weixin/dyb/admin/items_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('dyb_cfg')->getCfg($bid);

        $item = ORM::factory('dyb_item', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('dyb_order')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('dyba/items');
            }
        }

        if ($_POST['data']) {
            $item->values($_POST['data']);
            $item->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交（请在基础设置-微信参数，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';

            if ($_FILES['pic']['error'] == 0) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "dyb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('dyba/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        $this->template->content = View::factory('weixin/dyb/admin/items_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['dyba']['admin'] < 1) Request::instance()->redirect('dyba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('dyb_login');
        $logins = $logins->reset(FALSE);

       if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $logins = $logins->where('user', 'like', $s)->or_where('name', 'like', $s);
        }

        $result['countall'] = $countall = $logins->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dyb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/dyb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['dyba']['admin'] < 2) Request::instance()->redirect('dyba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('dyb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('dyb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('dyba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/dyb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['dyba']['admin'] < 2) Request::instance()->redirect('dyba/home');

        $bid = $this->bid;

        $login = ORM::factory('dyb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('dyb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('dyba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('dyb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                if ($_POST['data']['copyright']) {
                    $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
                }
                //appid 重置
                // if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                // }

                Request::instance()->redirect('dyba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/dyb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/dyb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('dyb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                // $username = $_POST['username'];
                // $sql = DB::query(Database::SELECT,"select user_id from user where user_name='$username'");
                // $user_id = $sql->execute();

                // $sql = DB::query(Database::SELECT,"select expiretime from buy where user_id=$user_id and product_id = 6 ");
                // $expiretime = $sql->execute();
                // if($expiretime){
                //     $expiretime = strtotime($expiretime);
                // }else{
                //     $expiretime = strtotime(ORM::factory('dyb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['dyba']['bid'] = $biz->id;
                    $_SESSION['dyba']['user'] = $_POST['username'];
                    $_SESSION['dyba']['admin'] = $biz->admin; //超管
                    $_SESSION['dyba']['config'] = ORM::factory('dyb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['dyba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/dyba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['dyba'] = null;
        header('location:/dyba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "dyb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('dyb_score')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('dyb_qrcode')->table_name())
            ->set(array('score' => '0','yz_score' =>'0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            $this->config = ORM::factory('dyb_cfg')->getCfg($this->bid, 1);
            Request::instance()->redirect('dyba/home');
        }
    }
  public function action_stats_totle($action='')
    {
         $daytype='%Y-%m-%d';
         $length=10;
         $status=1;
        if($_GET['qid']==3||$action=='shaixuan')
        {
            $status=3;
            $newadd=array();
            if($_GET['data']['begin']!=null&&$_GET['data']['over']!=null)//搜索
            {
                $begin=$_GET['data']['begin'];
                $over=$_GET['data']['over'];
               if(strtotime($begin)>strtotime($over))
               {
                 $begin=$_GET['data']['over'];
                 $over=$_GET['data']['begin'];
               }
               // echo $begin.$over;
               if(strtotime($begin)==strtotime($over))
               {
                 $newadd[0]['time']=$begin;
               }
               else
               {
                $newadd[0]['time']=$begin.'~'.$over;
               }

                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from dyb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from dyb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];
                //活动参与人数
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `dyb_scores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from dyb_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' or FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over')")->execute()->as_array();
                $newadd[0]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `dyb_orders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')>='$begin' and FROM_UNIXTIME(`createdtime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];

            }
        }
        else
        {

            if($_GET['qid']==2||$action=='month')//按月统计
            {
                $daytype='%Y-%m';
                $length=7;
                $status=2;
            }
            //$days=DB::query(Database::SELECT,"SELECT  FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `dyb_qrcodes` where bid=$this->bid UNION select FROM_UNIXTIME(`lastupdate`, '$daytype') from dyb_scores where bid =$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `dyb_qrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            // echo "<pre>";
            // var_dump($days);
            // echo "<pre>";
            // exit;
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/dyb/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `dyb_qrcodes` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from dyb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from dyb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from dyb_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' or FROM_UNIXTIME(`jointime`, '$daytype')='$time')")->execute()->as_array();
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `dyb_scores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `dyb_orders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `dyb_qrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
        $num=count($duringdata);
        if(strtotime($duringdata[0]['time'])<strtotime($duringdata[$num-1]['time']))
        {
        $duringtime['begin']=$duringdata[0]['time'];
        echo $duringtime['begin']."pppp";
        $duringtime['over']=$duringdata[$num-1]['time'];
        }
        else
        {
        $duringtime['begin']=$duringdata[$num-1]['time'];
        $duringtime['over']=$duringdata[0]['time'];
        }
        $this->template->content = View::factory('weixin/dyb/admin/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }
    //API证书上传函数
    private function Upload($name){
        $dir=Kohana::include_paths()[0].'vendor/weixin/cert/';
        $flag=true;
        if($_FILES['filecert']['error']>0)
        {
           $flag=false;
        }
        if(is_uploaded_file($_FILES['filecert']['tmp_name']))//判断该文件是否通过http post方式正确上传
        {

            if(!is_dir($dir.$name)){
                $new=mkdir($dir.$name);
            }
            if(move_uploaded_file($_FILES['filecert']['tmp_name'], $dir.$name.'/1.zip')){
                $zip = new ZipArchive();
                if ($zip->open($dir.$name.'/1.zip') === TRUE)
                {
                    $zip->extractTo($dir.$name.'/');
                    $zip->close();

                }
                else
                {
                    $flag=false;;

                }
            }
            else
            {
                $flag=false;

            }
        }
        else
        {
            $flag=false;

        }
        return$flag;
    }
    private function GetAgent(){

            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $is_pc = (strpos($agent, 'windows nt')) ? true : false;
            $is_mac = (strpos($agent, 'mac os')) ? true : false;
            $is_iphone = (strpos($agent, 'iphone')) ? true : false;
            $is_android = (strpos($agent, 'android')) ? true : false;
            $is_ipad = (strpos($agent, 'ipad')) ? true : false;

            $device="unknow";
            if($is_pc){
                  $device = 'pc';
            }

            if($is_mac){
                  $device = 'mac';
            }

            if($is_iphone){
                  $device = 'iphone';
            }

            if($is_android){
                  $device = 'android';
            }

            if($is_ipad){
                  $device = 'ipad';
            }

            return $device;
    }
}
