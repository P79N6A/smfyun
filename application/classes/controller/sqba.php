<?php defined('SYSPATH') or die('No direct script access.');

class Controller_sqba extends Controller_Base {

    public $template = 'weixin/sqb/tpl/atpl';
    public $pagesize = 20;
    //public $access_token;
    public $config;
    public $bid;
    public $appid = 'wxd3a678cfeb03e3a3';
    public $appSecret = '661fb2647a804e14ded1f65fad682695';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'weifubao';
    public function before() {
        Database::$default = "wfb";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        if (Request::instance()->action == 'oauthscript') return;
        if (Request::instance()->action == 'oauthscript2') return;
        $this->bid = $_SESSION['sqba']['bid'];
        $this->config = $_SESSION['sqba']['config'];
        //$this->access_token=ORM::factory('sqb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/sqba/login');
            header('location:/sqba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('sqb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['all'] = $todo['items'] + $todo['users'];
            $this->template->bid = $this->bid;
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }

    public function action_index() {
        $this->action_login();
    }


    //系统配置
    public function action_home() {
        $bid = $this->bid;
        //$this->access_token=ORM::factory('sqb_login')->where('id', '=', $bid)->find()->access_token;
        //微信授权
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$this->appid;
        $ctoken = $mem->get($cachename1);//获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$ctoken;
        $post_data = array(
          'component_appid' =>$this->appid
        );
        $post_data = json_encode($post_data);
        $res = json_decode($this->request_post($url, $post_data),true);
        // var_dump($res);
        $pre_auth_code = $res['pre_auth_code'];
        $pre_auth_code = substr($pre_auth_code,14);//去掉前缀 preauthcode@@@

        if ($_GET['auth_code']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
            $post_data = array(
              'component_appid' =>$this->appid,
              'authorization_code' =>$_GET['auth_code']
            );
            $post_data = json_encode($post_data);
            $res = json_decode($this->request_post($url, $post_data),true);
            $appid = $res['authorization_info']['authorizer_appid'];
            $access_token = $res['authorization_info']['authorizer_access_token'];
            $refresh_token = $res['authorization_info']['authorizer_refresh_token'];
            $expires_in = time()+7200;
            for($i=0;$res['authorization_info']['func_info'][$i];$i++){
                $auth_info = $auth_info.','.$res['authorization_info']['func_info'][$i]['funcscope_category']['id'];
            }
            // echo $auth_info.'<br>';
            // var_dump($res);
            $user = ORM::factory('sqb_login')->where('id','=',$this->bid)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='sqb.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('sqb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."sqb/tmp/$bid/cert.pem";
        $key_file = DOCROOT."sqb/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."sqb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        //$result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('sqb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                // //AppID 填写后不能修改
                // if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('sqb_login')->where("id","=",$bid)->find()->user;
             //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file));
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dir($key_file));
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            //  if ($_FILES['rootca']['error'] == 0) {
            //     @mkdir(dir($rootca_file));
            //     $ok = move_uploaded_file($_FILES['rootca']['tmp_name'], $rootca_file);
            //      $result['ok'] += $ok;
            //     $result['err1'] = '证书文件已更新！';
            // }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'sqb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'sqb_file_key', '', file_get_contents($key_file));
            //if (file_exists($rootca_file)) $cfg->setCfg($bid, 'sqb_file_rootca', '', file_get_contents($rootca_file));

        }

        //文案配置
        if ($_POST['text']) {
            // echo "<pre>";
            // var_dump($_POST['text']);
            // echo "<pre>";
            // exit;
            $cfg = ORM::factory('sqb_cfg');
            $qrfile = DOCROOT."sqb/tmp/tpl.$bid.jpg";

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
                    $default_head_file = DOCROOT."sqb/tmp/head.$bid.jpg";
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

                        $tpl = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
        }
        if ($_POST['notice']) {
            $notice2 = ORM::factory('sqb_cfg')->where('bid','=',$bid)->where('key','=','notice')->find();
            $notice2->value = $_POST['notice'];
            $notice2->save();
        }
        $notice = ORM::factory('sqb_cfg')->where('bid','=',$bid)->where('key','=','notice')->find()->value;

        $result['tpl'] = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('sqb_login')->where('id', '=', $bid)->find()->expiretime;
        $user = ORM::factory('sqb_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/sqb/admin/home')
            ->bind('result', $result)
            ->bind('notice', $notice)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('sqb_cfg')->getCfg($bid);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('sqb_qrcode')->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['admin'])) $qrcode_edit->admin = (int)$_POST['admin'];
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('sqb_qrcode');
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
        ))->render('weixin/sqb/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/sqb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['sqba']['admin'] < 1) Request::instance()->redirect('sqba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('sqb_login');
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
        ))->render('weixin/sqb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/sqb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['sqba']['admin'] < 2) Request::instance()->redirect('sqba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('sqb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('sqb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('sqba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/sqb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['sqba']['admin'] < 2) Request::instance()->redirect('sqba/home');

        $bid = $this->bid;

        $login = ORM::factory('sqb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('sqb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('sqba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('sqb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('sqba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/sqb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/sqb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('sqb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('sqb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['sqba']['bid'] = $biz->id;
                    $_SESSION['sqba']['user'] = $_POST['username'];
                    $_SESSION['sqba']['admin'] = $biz->admin; //超管
                    $_SESSION['sqba']['config'] = ORM::factory('sqb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['sqba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/sqba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['sqba'] = null;
        header('location:/sqba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "sqb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('sqb_score')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('sqb_qrcode')->table_name())
            ->set(array('score' => '0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            Request::instance()->redirect('sqba/home');
        }
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

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."sqb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."sqb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."sqb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'sqb_file_cert')->find();
        $file_key = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'sqb_file_key')->find();
        $file_rootca = ORM::factory('sqb_cfg')->where('bid', '=', $bid)->where('key', '=', 'sqb_file_rootca')->find();

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
