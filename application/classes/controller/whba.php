<?php defined('SYSPATH') or die('No direct script access.');

class Controller_whba extends Controller_Base {

    public $template = 'weixin/whb/tpl/atpl';
    public $pagesize = 20;
    //public $access_token;
    public $config;
    public $bid;
    public $appId = 'wxd0b3a6ff48335255';
    public $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'weihongbao';
    public function before() {
        Database::$default = "whb";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        if (Request::instance()->action == 'oauthscript') return;
        if (Request::instance()->action == 'oauthscript2') return;
        $this->bid = $_SESSION['whba']['bid'];
        $this->config = $_SESSION['whba']['config'];
        //$this->access_token=ORM::factory('whb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/whba/login');
            header('location:/whba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('whb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            // $todo['items'] = ORM::factory('whb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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


    //系统配置
    public function action_home() {
        $bid = $this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('whb_login')->where('id', '=', $bid)->find()->access_token;
        //微信授权
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$this->appId;
        $ctoken = $mem->get($cachename1);//获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$ctoken;
        $post_data = array(
          'component_appid' =>$this->appId
        );
        $post_data = json_encode($post_data);
        $res = json_decode($this->request_post($url, $post_data),true);
        // var_dump($res);
        $pre_auth_code = $res['pre_auth_code'];
        $pre_auth_code = substr($pre_auth_code,14);//去掉前缀 preauthcode@@@

        if ($_GET['auth_code']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
            $post_data = array(
              'component_appid' =>$this->appId,
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
            $user = ORM::factory('whb_login')->where('id','=',$this->bid)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='whb.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('whb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."whb/tmp/$bid/cert.pem";
        $key_file = DOCROOT."whb/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."whb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        //$result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('whb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                // //AppID 填写后不能修改
                // if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('whb_login')->where("id","=",$bid)->find()->user;
             //证书上传
            if ($_FILES['cert']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($cert_file),0775,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($key_file),0775,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'whb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'whb_file_key', '', file_get_contents($key_file));


            //重新读取配置
            $config = ORM::factory('whb_cfg')->getCfg($bid, 1);
        }

        //文案配置

        //菜单配置
        if ($_POST['menu']) {
            $buser = ORM::factory('whb_login')->where('id','=',$bid)->find();
            if(!$buser->appid) die('请在【微信参数】处，点击【一键授权】');

            $cfg = ORM::factory('whb_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }
            //配置菜单更新


            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('whb_login')->where('id','=',$bid)->find()->appid;
            if(!$bid) Kohana::$log->add('whbbid:', 'meun');//写入日志，可以删除
            $wx = new Wxoauth($bid,'whb',$this->appId,$options);

            if($_POST['menu']['key_a0']){
                if($_POST['menu']['value_a1']){
                    for ($i=1; $i <=5 ; $i++) {
                        if($_POST['menu']['key_a'.$i]){
                            $m=$i;
                        }
                    }
                    $data['button'][0]['name']=$_POST['menu']['key_a0'];
                    for ($i=0 ; $i<$m; $i++){
                        $y=$i+1;
                        if(substr($_POST['menu']['value_a'.$y], 0,4)=='http'){
                            $data['button'][0]['sub_button'][$i]['type']='view';
                            $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_a'.$y];
                            $data['button'][0]['sub_button'][$i]['url']=$_POST['menu']['value_a'.$y];
                        }else{
                            $data['button'][0]['sub_button'][$i]['type']='click';
                            $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_a'.$y];
                            $data['button'][0]['sub_button'][$i]['key']='key_a'.$y;
                        }
                    }
                }else{
                    if(substr($_POST['menu']['value_a0'], 0,4)=='http'){
                        $data['button'][0]['type']='view';
                        $data['button'][0]['name']=$_POST['menu']['key_a0'];
                        $data['button'][0]['url']=$_POST['menu']['value_a0'];
                    }else{
                        $data['button'][0]['type']='click';
                        $data['button'][0]['name']=$_POST['menu']['key_a0'];
                        $data['button'][0]['key']='key_a0';
                    }
                }
            }
            if($_POST['menu']['key_b0']){
                if($_POST['menu']['value_b1']){
                    for ($i=1; $i <=5 ; $i++) {
                        if($_POST['menu']['key_b'.$i]){
                            $m=$i;
                        }
                    }
                    $data['button'][1]['name']=$_POST['menu']['key_b0'];
                    for ($i=0 ; $i<$m; $i++){
                        $y=$i+1;
                        if(substr($_POST['menu']['value_b'.$y], 0,4)=='http'){
                            $data['button'][1]['sub_button'][$i]['type']='view';
                            $data['button'][1]['sub_button'][$i]['name']=$_POST['menu']['key_b'.$y];
                            $data['button'][1]['sub_button'][$i]['url']=$_POST['menu']['value_b'.$y];
                        }else{
                            $data['button'][1]['sub_button'][$i]['type']='click';
                            $data['button'][1]['sub_button'][$i]['name']=$_POST['menu']['key_b'.$y];
                            $data['button'][1]['sub_button'][$i]['key']='key_b'.$y;
                        }
                    }
                }else{
                    if(substr($_POST['menu']['value_b0'], 0,4)=='http'){
                        $data['button'][1]['type']='view';
                        $data['button'][1]['name']=$_POST['menu']['key_b0'];
                        $data['button'][1]['url']=$_POST['menu']['value_b0'];
                    }else{
                        $data['button'][1]['type']='click';
                        $data['button'][1]['name']=$_POST['menu']['key_b0'];
                        $data['button'][1]['key']='key_b0';
                    }
                }
            }
             if($_POST['menu']['key_c0']){
                if($_POST['menu']['value_c1']){
                    for ($i=1; $i <=5 ; $i++) {
                        if($_POST['menu']['key_c'.$i]){
                            $m=$i;
                        }
                    }
                    $data['button'][2]['name']=$_POST['menu']['key_c0'];
                    for ($i=0 ; $i<$m; $i++){
                        $y=$i+1;
                        if(substr($_POST['menu']['value_c'.$y], 0,4)=='http'){
                            $data['button'][2]['sub_button'][$i]['type']='view';
                            $data['button'][2]['sub_button'][$i]['name']=$_POST['menu']['key_c'.$y];
                            $data['button'][2]['sub_button'][$i]['url']=$_POST['menu']['value_c'.$y];
                        }else{
                            $data['button'][2]['sub_button'][$i]['type']='click';
                            $data['button'][2]['sub_button'][$i]['name']=$_POST['menu']['key_c'.$y];
                            $data['button'][2]['sub_button'][$i]['key']='key_c'.$y;
                        }
                    }
                }else{
                     if(substr($_POST['menu']['value_c0'], 0,4)=='http'){
                        $data['button'][2]['type']='view';
                        $data['button'][2]['name']=$_POST['menu']['key_c0'];
                        $data['button'][2]['url']=$_POST['menu']['value_c0'];
                    }else{
                        $data['button'][2]['type']='click';
                        $data['button'][2]['name']=$_POST['menu']['key_c0'];
                        $data['button'][2]['key']='key_c0';
                    }
                }
            }

            $menu = $wx->createMenu($data);

            if($menu==true) {
                $result['menu']=1;
            }else{
                $result['menu']=0;
            }
            //重新读取配置
            $config = ORM::factory('whb_cfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置

        $result['tpl'] = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('whb_login')->where('id', '=', $bid)->find()->expiretime;
        $user = ORM::factory('whb_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/whb/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }
    public function action_hb_check() {

        $bid = $this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('whb_login')->where('id', '=', $bid)->find()->access_token;

        if ($_POST['hb_check']){
            $hb_check = $_POST['hb_check'];
            $cfg = ORM::factory('whb_cfg');
            foreach ($hb_check as $k => $v) {
                $ok=$cfg->setCfg($bid,$k,$v);
                $result['ok8']+=$ok;
            }
            $config = ORM::factory('whb_cfg')->getCfg($bid, 1);
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/whb/admin/hb_check')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid,1);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        $qrcode = ORM::factory('whb_qrcode')->where('bid', '=', $bid);
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
        if ($_GET['qr_id']) {//这个二维码 发了红包的用户
            $result['qr_id'] = (int)$_GET['qr_id'];
            $qrcode = $qrcode->where('from_qr','=',$_GET['qr_id'])->where('status','=',1);
        }
        if ($_GET['all_qr_id']) {//这个二维码下的所有用户
            $result['all_qr_id'] = (int)$_GET['all_qr_id'];
            $qrcode = $qrcode->where('from_qr','=',$_GET['all_qr_id']);
        }

        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/whb/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/whb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid,1);
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        $order = ORM::factory('whb_order')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            if($_GET['s']=='已发放待领取'){
                $s='SENT';
            }elseif ($_GET['s']=='已领取') {
                $s='RECEIVED';
            }elseif ($_GET['s']=='过期未领取') {
                $s='REFUND';
            }elseif ($_GET['s']=='发放失败') {
                $s='FAILED';
            }
            $result['s'] = $_GET['s'];
            //$s = '%'.trim($_GET['s'].'%');
            $order = $order->where('status', '=', $s); //->or_where('openid', 'like', $s);
        }
        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('whb_qrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/whb/admin/pages');

        $result['order'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/whb/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //二维码管理
    public function action_qrs($action='', $id=0) {
        if ($action == 'add') return $this->action_qrs_add();
        if ($action == 'edit') return $this->action_qrs_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid);

        $result['qrs'] = ORM::factory('whb_qr')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all();

        $this->template->title = '二维码管理';
        $this->template->content = View::factory('weixin/whb/admin/qrs')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid', $bid);
    }

    public function action_qrs_add() {
        $bid = $this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $qr = ORM::factory('whb_qr');
            $qr->values($_POST['data']);
            $qr->starttime = strtotime($_POST['data']['starttime']);
            $qr->endtime = strtotime($_POST['data']['endtime']);
            $qr->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['maxprice']|| !$_POST['data']['minprice']) {
                $result['error'] = '请填写完整后再提交';
            }

            if (strtotime($_POST['data']['starttime']) >= strtotime($_POST['data']['endtime'])) {
                $result['error'] = '请规范填写有效期哦';
            }

            if (!$result['error']) {
                $sceneid = time().'qr'.$bid;
                require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
                $options['token'] = $this->token;
                $options['encodingaeskey'] = $this->encodingAesKey;
                $options['appid'] = ORM::factory('whb_login')->where('id','=',$bid)->find()->appid;//商户appid

                $wx = new Wxoauth($bid,'whb',$this->appId,$options);
                $qrcode_type = 1;
                $ticket_lifetime = 3600*24*365*3;
                $result = $wx->getQRCode($sceneid, $qrcode_type, $ticket_lifetime);

                if($result['ticket']){
                    $qrurl = $wx->getQRUrl($result['ticket']);//同一个 sceneid 的 ticket 不会变
                    $qr->qrurl = $qrurl;
                    $qr->sceneid = $sceneid;
                }

                $qr->save();
                Request::instance()->redirect('whba/qrs');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新的二维码';
        $this->template->content = View::factory('weixin/whb/admin/qrs_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_qrs_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('whb_cfg')->getCfg($bid);

        $qr = ORM::factory('whb_qr', $id);
        if (!$qr || $qr->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('whb_qrcode')->where('bid', '=', $bid)->where('from_qr', '=', $id)->count_all() == 0) {
                $qr->delete();
                Request::instance()->redirect('whba/qrs');
            }
        }

        if ($_POST['data']) {
            $qr->values($_POST['data']);
            $qr->starttime = strtotime($_POST['data']['starttime']);
            $qr->endtime = strtotime($_POST['data']['endtime']);
            $qr->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['maxprice']|| !$_POST['data']['minprice']) {
                $result['error'] = '请填写完整后再提交';
            }

            if (strtotime($_POST['data']['starttime']) >= strtotime($_POST['data']['endtime'])) {
                $result['error'] = '请规范填写有效期哦';
            }

            if (!$result['error']) {
                $qr->save();
                Request::instance()->redirect('whba/qrs');
            }
        }

        $_POST['data'] = $result['qr'] = $qr->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改二维码';
        $this->template->content = View::factory('weixin/whb/admin/qrs_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['whba']['admin'] < 1) Request::instance()->redirect('whba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('whb_login');
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
        ))->render('weixin/whb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/whb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['whba']['admin'] < 2) Request::instance()->redirect('whba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('whb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('whb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('whba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/whb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['whba']['admin'] < 2) Request::instance()->redirect('whba/home');

        $bid = $this->bid;

        $login = ORM::factory('whb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('whb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('whba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('whb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                if ($_POST['data']['copyright']) {
                    $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
                }
                //appid 重置
                if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                }

                Request::instance()->redirect('whba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/whb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/whb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('whb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('whb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['whba']['bid'] = $biz->id;
                    $_SESSION['whba']['user'] = $_POST['username'];
                    $_SESSION['whba']['admin'] = $biz->admin; //超管
                    $_SESSION['whba']['config'] = ORM::factory('whb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['whba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/whba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['whba'] = null;
        header('location:/whba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "whb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('whb_score')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('whb_qrcode')->table_name())
            ->set(array('score' => '0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            Request::instance()->redirect('whba/home');
        }
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
    private function hongbao($config, $openid, $wx='', $bid=1, $money){
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$wx) {
            //require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('whb_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('whbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,'whb',$this->appId,$options);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $options['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        // $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        // $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

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
        return $result;//hash数组
    }


    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."whb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."whb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."whb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_cert')->find();
        $file_key = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_key')->find();
        $file_rootca = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_rootca')->find();

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
