<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_qfxa extends Controller_Base {

    public $template = 'weixin/qfx/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $we;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qfx";

        $_SESSION =& Session::instance()->as_array();
        parent::before();

        $this->bid = $_SESSION['qfxa']['bid'];
        $this->config = $_SESSION['qfxa']['config'];
        $this->access_token=ORM::factory('qfx_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qfxa/login');
            header('location:/qfxa/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('qfx_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('qfx_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('qfx_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=234f0564ec0d951f34&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/qfxa/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"234f0564ec0d951f34",
            "client_secret"=>"0260f2c5eb62fb486cab11fc4d4f9469",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/qfxa/callback'
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
            $sid = $value['id'];
            $name = $value['name'];
            $usershop = ORM::factory('qfx_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("qfxa/home")."';</script>";
        }
        //Request::instance()->redirect('qfxa/home');
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('qfx_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."qfx/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."qfx/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('qfx_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }

            //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dirname($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'qfx_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'qfx_file_key', '', file_get_contents($key_file));

            //重新读取配置
            $config = ORM::factory('qfx_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('qfx_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('qfx_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('qfx_cfg');
            $qrfile = DOCROOT."qfx/tmp/tpl.$bid.jpg";

            //二维码海报
            if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*400) {
                    $result['err3'] = '海报模板文件不能超过 400K';
                } else {
                    $result['ok3']++;
                    $cfg->setCfg($bid, 'qfxtpl', '', file_get_contents($_FILES['pic']['tmp_name']));
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
                    $default_head_file = DOCROOT."qfx/tmp/head.$bid.jpg";
                    $cfg->setCfg($bid, 'qfxtplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
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

                        $tpl = ORM::factory('qfx_cfg')->where('bid', '=', $bid)->where('key', '=', 'qfxtpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('qfx_cfg')->where('bid', '=', $bid)->where('key', '=', 'qfxtplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('qfx_cfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('qfx_cfg')->where('bid', '=', $bid)->where('key', '=', 'qfxtpl')->find()->id;
        $result['tplhead'] = ORM::factory('qfx_cfg')->where('bid', '=', $bid)->where('key', '=', 'qfxtplhead')->find()->id;
        $access_token = ORM::factory('qfx_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/qfx/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('config', $config);
    }
    public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        $result['skus'] = ORM::factory('qfx_sku')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '返还管理';
        $this->template->content = View::factory('weixin/qfx/admin/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('qfx_sku');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('qfxa/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/qfx/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        $sku = ORM::factory('qfx_sku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('qfxa/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('qfxa/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/qfx/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_group($action='', $id=0) {
        if ($action == 'add') return $this->action_group_add();
        if ($action == 'edit') return $this->action_group_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        $result['group'] = ORM::factory('qfx_group')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '分销商分组';
        $this->template->content = View::factory('weixin/qfx/admin/group')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_group_add() {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('qfx_group');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('qfxa/group');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/qfx/admin/group_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_group_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        $group = ORM::factory('qfx_group', $id);
        if (!$group || $group->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sum = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('group_id','=',$id)->count_all();
            if($sum>0){
                die('该分组下有分销商不允许删除！');
            }
            $group->delete();
            Request::instance()->redirect('qfxa/group');
        }

        if ($_POST['data']) {
            $group->values($_POST['data']);
            $group->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $group->save();
                Request::instance()->redirect('qfxa/group');
            }
        }

        $_POST['data'] = $result['group'] = $group->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/qfx/admin/group_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户详细
    public function action_qrcodes_detail($id){
        $bid = $this->bid;
        $this->template->title = '用户详细';
        if($_GET['data']['begin']&&$_GET['data']['over']){
            $result['time'] = $_GET['data']['begin'].'——'.$_GET['data']['over'];
        }else{
            if($_GET['data']['time']=='today'||!$_GET['data']['time']){
                $result['time'] = date('Y-m-d 00:00:00',time()).'——'.'到现在';
            }
        }
        $result['begin'] = $_GET['data']['begin'];
        $result['over'] = $_GET['data']['over'];
        $this->template->content = View::factory('weixin/qfx/admin/qrcodes_detail')
            ->bind('result', $result);
    }
    //审核管理
    public function action_qrcodes_m($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];

                    $qrcode_edit->name = $_POST['form']['name'];
                    $qrcode_edit->tel = $_POST['form']['tel'];
                    $qrcode_edit->bz = $_POST['form']['bz'];
                    $qrcode_edit->group_id = $_POST['form']['groupid'];
                    $qrcode_edit->save();
                }
                if ($_POST['form']['score'])
                    ORM::factory('qfx_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',1)->or_where('lv','=',3)->where_close();
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('name', 'like', $s)->or_where('bz', 'like', $s)->or_where('shop', 'like', $s)->or_where('tel', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['gid']) {
            $result['gid'] = (int)$_GET['gid'];
            $qrcode = $qrcode->where('group_id', '=', $result['gid']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
        if ($_GET['type']) {
            $result['type'] = $_GET['type'];
            if($result['type']!='all'){
                $qrcode = $qrcode->where('lv', '=', $result['type']);
            }
        }
        if ($_GET['group']) {
            $result['group'] = $_GET['group'];
            if($result['group']!='all'){
                $qrcode = $qrcode->where('group_id', '=', $result['group']);
            }
        }
        $result['countall'] = $countall = $qrcode->count_all();
        if ($_GET['sort']=='fans_num'){
            $qrcodes = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',1)->or_where('lv','=',3)->where_close()->find_all();
            foreach ($qrcodes as $k => $v) {
                $num = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('fopenid','=',$v->openid)->where('subscribe','=',1)->count_all();
                if($v->fans_num!=$num){
                    $v->fans_num = $num;
                    $v->save();
                }
            }
        }
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qfx/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $group = ORM::factory('qfx_group')->where('bid','=',$bid)->find_all();
        $gnum = ORM::factory('qfx_group')->where('bid','=',$bid)->count_all();
        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/qfx/admin/qrcodes_m')
            ->bind('pages', $pages)
            ->bind('group', $group)
            ->bind('gnum', $gnum)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //分销审核
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];
                    $qrcode_edit->name = $_POST['form']['name'];
                    $qrcode_edit->tel = $_POST['form']['tel'];
                    $qrcode_edit->bz = $_POST['form']['bz'];
                    $qrcode_edit->group_id = $_POST['form']['groupid'];
                    $qrcode_edit->save();
                    if((int)$_POST['form']['lv']==1){
                        //给予编号
                        $front_user = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('id', '<', $qrcode_edit->id)->order_by('id','desc')->find();
                        $qrcode_edit->fid = $front_user->fid + 1;
                        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                        $this->we = new Wechat($config);
                        if($config['msg_success_tpl']){
                            $this->sendsuccess($qrcode_edit->openid,$qrcode_edit->nickname);
                        }else{
                            $msg['touser'] = $qrcode_edit->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = "恭喜您的申请已经通过，成功获得资格，赶紧点击菜单【生成海报】吧";
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',2)->or_where('lv','=',4)->where_close();
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('name', 'like', $s)->or_where('bz', 'like', $s)->or_where('shop', 'like', $s)->or_where('tel', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }
        if ($_GET['type']) {
            $result['type'] = $_GET['type'];
            if($result['type']!='all'){
                $qrcode = $qrcode->where('lv', '=', $result['type']);
            }
        }
        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qfx_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qfx/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $group = ORM::factory('qfx_group')->where('bid','=',$bid)->find_all();
        $gnum = ORM::factory('qfx_group')->where('bid','=',$bid)->count_all();
        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/qfx/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('group', $group)
            ->bind('gnum', $gnum)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['qfxa']['admin'] < 1) Request::instance()->redirect('qfxa/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('qfx_login');
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
        ))->render('weixin/qfx/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/qfx/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['qfxa']['admin'] < 2) Request::instance()->redirect('qfxa/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('qfx_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qfx_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('qfxa/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/qfx/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['qfxa']['admin'] < 2) Request::instance()->redirect('qfxa/home');

        $bid = $this->bid;

        $login = ORM::factory('qfx_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('qfx_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('qfxa/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qfx_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('qfxa/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/qfx/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/qfx/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('qfx_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['qfxa']['bid'] = $biz->id;
                    $_SESSION['qfxa']['user'] = $_POST['username'];
                    $_SESSION['qfxa']['admin'] = $biz->admin; //超管
                    $_SESSION['qfxa']['config'] = ORM::factory('qfx_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['qfxa']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/qfxa/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['qfxa'] = null;
        header('location:/qfxa/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qfx_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_export_data_users(){
        $bid = $this->bid;
        $daytype='%Y-%m-%d';
        $length=10;
        if($_POST['data']['begin']!=NULL&&$_POST['data']['over']!=NULL){
            $begin=$_POST['data']['begin'];
            $over=$_POST['data']['over'];
           if(strtotime($begin)>strtotime($over)){
             $begin=$_POST['data']['over'];
             $over=$_POST['data']['begin'];
           }
           if(strtotime($begin)==strtotime($over))
           {
             $temp='所有用户'.$begin;
           }
           else
           {
            $temp='所有用户'.$begin.'~'.$over;
           }
            $users = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('lv','=',1)->find_all();

            $filename = 'ORDERS.'.$temp.'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('用户id', '用户昵称', '用户姓名','所属分组', '新增粉丝数量', '有赞订单数量', '有赞商品交易数量', '有赞成交金额');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($users as $k => $v) {
                //新增粉丝数量
                $newadd[$k]['fansnum'] = 0;
                $newadd[$k]['tradesnum'] = 0;
                $newadd[$k]['goodsnum'] = 0;
                $newadd[$k]['payment'] = 0;
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and fopenid='$v->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[$k]['fansnum']=$fans[0]['fansnum'];
                //有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and fopenid='$v->openid' and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[$k]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$k]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$k]['payment']=$tradesdata[0]['payment'];

                $array = array($v->id, $v->nickname, $v->name, $v->groups->name,$newadd[$k]['fansnum'], $newadd[$k]['tradesnum'], $newadd[$k]['goodsnum'],$newadd[$k]['payment']);

                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }

                fputcsv($fp, $array);
            }
        }
        exit;
    }
    public function action_export_data_groups(){
        $bid = $this->bid;
        $daytype='%Y-%m-%d';
        $length=10;
        if($_POST['data']['begin']!=NULL&&$_POST['data']['over']!=NULL){
            $begin=$_POST['data']['begin'];
            $over=$_POST['data']['over'];
           if(strtotime($begin)>strtotime($over)){
             $begin=$_POST['data']['over'];
             $over=$_POST['data']['begin'];
           }
           if(strtotime($begin)==strtotime($over))
           {
             $temp='所有分组'.$begin;
           }
           else
           {
            $temp='所有分组'.$begin.'~'.$over;
           }
            $groups = ORM::factory('qfx_group')->where('bid','=',$bid)->find_all();

            $filename = 'ORDERS.'.$temp.'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('分组id', '分组名称', '组成员数量', '新增粉丝数量', '有赞订单数量', '有赞商品交易数量', '有赞成交金额');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($groups as $k => $v) {
                $group_users = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('group_id','=',$v->id)->find_all();
                $group_num = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('group_id','=',$v->id)->count_all();
                $addcount[$k] = 0;
                $addtradesnum[$k] = 0;
                $addgoodnum[$k] = 0;
                $addpayment[$k] = 0;
                foreach ($group_users as $g => $u) {
                    //新增用户
                    $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and fopenid='$u->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                    $addcount[$k] = $addcount[$k] + $fans[0]['fansnum'];
                    //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                    $tradesdata =DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and fopenid='$u->openid' and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                    $addtradesnum[$k] = $addtradesnum[$k] + $tradesdata[0]['tradesnum'];
                    $addgoodnum[$k] = $addgoodnum[$k] + $tradesdata[0]['goodnum'];
                    $addpayment[$k] = $addpayment[$k] + $tradesdata[0]['payment'];
                }
                $array = array($v->id, $v->name, $group_num, $addcount[$k],$addtradesnum[$k], $addgoodnum[$k],$addpayment[$k]);

                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }
                fputcsv($fp, $array);
            }
        }
        exit;
    }
    public function diff_date($date1, $date2){
        if($date1>$date2){
            $startTime = strtotime($date1);
            $endTime = strtotime($date2);
        }else{
            $startTime = strtotime($date2);
            $endTime = strtotime($date1);
        }
            $diff = $startTime-$endTime;
            $day = $diff/86400;
            return intval($day);
    }
     public function action_stats_totle($action='')
    {
         $daytype='%Y-%m-%d';
         $length=10;
         $status=1;
         $group = ORM::factory('qfx_group')->where('bid','=',$this->bid)->find_all();
         $users = ORM::factory('qfx_qrcode')->where('bid','=',$this->bid)->where('lv','=',1)->find_all();
        if($_GET['qid']==3||$action=='shaixuan')
        {   //时间轴
            if($_GET['data']['begin']==NULL&&$_GET['data']['group']==NULL&&$_GET['data']['user']==NULL){
                $_GET['data']['begin'] = '2016-10-24';
                $_GET['data']['over'] = date('Y-m-d',time());
                $_GET['data']['group'] ='all';
                $_GET['data']['user'] ='all';
                $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qfx_trades where bid=$this->bid ORDER BY `time` ASC")->execute()->as_array();
                $timeline=array();
                for($i=0;$days[$i];$i++)
                {

                    $time=$days[$i]['time'];
                    $timeline[$i]['time']=$time;
                    //新增用户
                    $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                    $timeline[$i]['fansnum']=$fans[0]['fansnum']==NULL?0:$fans[0]['fansnum'];

                    //产生海报数
                    $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                    $timeline[$i]['tickets']=$ticket[0]['tickets']==NULL?0:$ticket[0]['tickets'];

                    //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                    $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                    $timeline[$i]['tradesnum']=$tradesdata[0]['tradesnum']==NULL?0:$tradesdata[0]['tradesnum'];
                    $timeline[$i]['goodsnum']=$tradesdata[0]['goodnum']==NULL?0:$tradesdata[0]['goodnum'];
                    $timeline[$i]['payment']=$tradesdata[0]['payment']==NULL?0:$tradesdata[0]['payment'];

                    //所有佣金 已结算的佣金、待结算的佣金
                    $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
                   // var_dump($commision);
                    $timeline[$i]['commision']=$commision[0]['paymoney']==NULL?0:$commision[0]['paymoney'];
                }
            }
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


               if($_GET['data']['group']=='all'&&$_GET['data']['user']=='all'){
                    //新增用户
                    $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                    $newadd[0]['fansnum']=$fans[0]['fansnum'];
                    //产生海报数
                    $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                    $newadd[0]['tickets']=$ticket[0]['tickets'];
                    //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                    $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                    $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                    $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                    $newadd[0]['payment']=$tradesdata[0]['payment'];
                    //所有佣金 已结算的佣金、待结算的佣金
                    $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();

                    $newadd[0]['commision']=$commision[0]['paymoney'];
                    $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qfx_trades where bid=$this->bid ORDER BY `time` ASC")->execute()->as_array();
                    $timeline=array();
                    for($i=0;$days[$i];$i++)
                    {

                        $time=$days[$i]['time'];
                        $timeline[$i]['time']=$time;
                        //新增用户
                        $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                        $timeline[$i]['fansnum']=$fans[0]['fansnum']==NULL?0:$fans[0]['fansnum'];

                        //产生海报数
                        $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                        $timeline[$i]['tickets']=$ticket[0]['tickets']==NULL?0:$ticket[0]['tickets'];

                        //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                        $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                        $timeline[$i]['tradesnum']=$tradesdata[0]['tradesnum']==NULL?0:$tradesdata[0]['tradesnum'];
                        $timeline[$i]['goodsnum']=$tradesdata[0]['goodnum']==NULL?0:$tradesdata[0]['goodnum'];
                        $timeline[$i]['payment']=$tradesdata[0]['payment']==NULL?0:$tradesdata[0]['payment'];

                        //所有佣金 已结算的佣金、待结算的佣金
                        $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
                       // var_dump($commision);
                        $timeline[$i]['commision']=$commision[0]['paymoney']==NULL?0:$commision[0]['paymoney'];
                    }
               }
               if($_GET['data']['user']!='all'){
                    //指定用户的 新增用户
                    $user = ORM::factory('qfx_qrcode')->where('bid','=',$this->bid)->where('id','=',$_GET['data']['user'])->find();
                    $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and fopenid='$user->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                    $newadd[0]['fansnum']=$fans[0]['fansnum'];
                    //产生海报数
                    $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and fopenid='$user->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                    $newadd[0]['tickets']=$ticket[0]['tickets'];
                    //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                    $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and fopenid='$user->openid' and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                    $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                    $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                    $newadd[0]['payment']=$tradesdata[0]['payment'];
                    //所有佣金 已结算的佣金、待结算的佣金
                    $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and qid='$user->id' and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();
                    $newadd[0]['commision']=$commision[0]['paymoney'];

                    $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qfx_trades where bid=$this->bid ORDER BY `time` ASC")->execute()->as_array();

                    $timeline=array();
                    for($i=0;$days[$i];$i++)
                    {
                        $time=$days[$i]['time'];
                        $timeline[$i]['time']=$time;
                        //新增用户
                        $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and fopenid='$user->openid' and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                        $timeline[$i]['fansnum']=$fans[0]['fansnum']==NULL?0:$fans[0]['fansnum'];

                        //产生海报数
                        $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and fopenid='$user->openid' and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                        $timeline[$i]['tickets']=$ticket[0]['tickets']==NULL?0:$ticket[0]['tickets'];

                        //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                        $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and fopenid='$user->openid' and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                        $timeline[$i]['tradesnum']=$tradesdata[0]['tradesnum']==NULL?0:$tradesdata[0]['tradesnum'];
                        $timeline[$i]['goodsnum']=$tradesdata[0]['goodnum']==NULL?0:$tradesdata[0]['goodnum'];
                        $timeline[$i]['payment']=$tradesdata[0]['payment']==NULL?0:$tradesdata[0]['payment'];

                        //所有佣金 已结算的佣金、待结算的佣金
                        $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and qid='$user->id' and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
                       // var_dump($commision);
                        $timeline[$i]['commision']=$commision[0]['paymoney']==NULL?0:$commision[0]['paymoney'];
                    }
               }
               if($_GET['data']['user']=='all'&&$_GET['data']['group']!='all'){
                    //指定分组的 新增用户
                    $group_users = ORM::factory('qfx_qrcode')->where('bid','=',$this->bid)->where('group_id','=',$_GET['data']['group'])->find_all();
                    $addcount = 0;
                    $addticket = 0;
                    $addtradesnum = 0;
                    $addgoodnum = 0;
                    $addpayment = 0;
                    $addcommision = 0;
                    $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qfx_trades where bid=$this->bid ORDER BY `time` ASC")->execute()->as_array();
                    $timeline=array();
                    for($i=0;$days[$i];$i++){
                        $time=$days[$i]['time'];
                        $timeline[$i]['time']=$time;
                        $timeline[$i]['fansnum'] = 0;
                        $timeline[$i]['tickets'] = 0;
                        $timeline[$i]['tradesnum'] = 0;
                        $timeline[$i]['goodsnum'] = 0;
                        $timeline[$i]['payment'] = 0;
                        $timeline[$i]['commision'] = 0;
                        foreach ($group_users as $k => $v) {
                            //新增用户
                            $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and fopenid='$v->openid' and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                            $timeline[$i]['fansnum']=$timeline[$i]['fansnum'] + (int)$fans[0]['fansnum'];
                            //产生海报数
                            $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and fopenid='$v->openid' and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                            $timeline[$i]['tickets']= $timeline[$i]['tickets']+ (int)$ticket[0]['tickets'];

                            //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                            $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and fopenid='$v->openid' and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                            $timeline[$i]['tradesnum']= $timeline[$i]['tradesnum']+(int)$tradesdata[0]['tradesnum'];
                            $timeline[$i]['goodsnum']=$timeline[$i]['goodsnum']+(int)$tradesdata[0]['goodnum'];
                            $timeline[$i]['payment']=$timeline[$i]['payment']+(int)$tradesdata[0]['payment'];

                            //所有佣金 已结算的佣金、待结算的佣金
                            $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and qid='$v->id' and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
                           // var_dump($commision);
                            $timeline[$i]['commision']=$timeline[$i]['commision']+(int)$commision[0]['paymoney'];
                        }
                    }

                    foreach ($group_users as $k => $v) {
                        //新增用户
                        $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and fopenid='$v->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                        $addcount = $addcount + $fans[0]['fansnum'];
                        //产生海报数
                        $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and fopenid='$v->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                        $addticket = $addticket + $ticket[0]['tickets'];
                        //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                        $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and fopenid='$v->openid' and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                        $addtradesnum = $addtradesnum + $tradesdata[0]['tradesnum'];
                        $addgoodnum = $addgoodnum + $tradesdata[0]['goodnum'];
                        $addpayment = $addpayment + $tradesdata[0]['payment'];
                        //所有佣金 已结算的佣金、待结算的佣金
                        $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and qid='$v->id' and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();
                        $addcommision = $addcommision + $commision[0]['paymoney'];
                    }
                    $newadd[0]['fansnum']=$addcount;
                    $newadd[0]['tickets']=$addticket;

                    $newadd[0]['tradesnum']=$addtradesnum;
                    $newadd[0]['goodsnum']=$addgoodnum;
                    $newadd[0]['payment']=$addpayment;

                    $newadd[0]['commision']=$addcommision;
               }
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
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qfx_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qfx/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qfx_trades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qfx_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qfx_trades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$i]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qfx_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
               // var_dump($commision);
                $newadd[$i]['commision']=$commision[0]['paymoney'];
            }
        }


        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `qfx_qrcodes` where bid=$this->bid UNION select left(pay_time,10) from qfx_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/qfx/admin/stats_totle')
        ->bind('begin',$begin)
        ->bind('over',$over)
        ->bind('timeline',$timeline)
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('group',$group)
        ->bind('users',$users)
        ->bind('duringtime',$duringtime);
    }


    public function action_history_trades()
    {

        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $trade = ORM::factory('qfx_trade')->where('bid', '=', $bid)->where('fopenid','!=','');
        $trade = $trade->reset(FALSE);

        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from qfx_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

            $trade =$trade->where('title', 'like', $s);

            if(count($openids)>0)
            $trade=$trade->or_where('openid', 'IN', $openids);

            $trade = $trade->and_where_close();
        }

        $result['countall'] = $countall = $trade->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qfx/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/qfx/admin/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid);
        $outmoney=ORM::factory('qfx_score')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from qfx_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

            if(count($qid)>0)
            $outmoney=$outmoney->where('qid', 'IN', $qid);
            else
            $outmoney=$outmoney->where('qid', "=",-100);
        }
        $result['countall'] = $countall = $outmoney->count_all();

        $result['sort'] = 'lastupdate';
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qfx/admin/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/qfx/admin/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }

    public function action_num()
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            $tradeid=ORM::factory('qfx_trade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('qfx_order')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('qfx_cfg')->getCfg($tempbid);
                    $this->access_token = ORM::factory('qfx_login')->where('id','=',$tempbid)->find()->access_token;
                    if (!$this->access_token) //die("$bid not found.\n");
                    continue;

                    $client = new YZTokenClient($this->access_token);
                    $method = 'youzan.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($method, $this->methodVersion, $params, $files);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('qfx_order')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
                        if(!$good->id)
                        {
                        $good->bid=$tempbid;
                        $good->tid=$k->tid;
                        $good->goodid=$result['response']['trade']['orders'][$j]['num_iid'];
                        $good->num=$result['response']['trade']['orders'][$j]['num'];
                        $good->price=$result['response']['trade']['orders'][$j]['payment'];
                        $good->title=$result['response']['trade']['orders'][$j]['title'];
                        $good->save();
                        }
                    }
              }

       }
       echo $i."////"."jj".$j;

    exit();
    }


    public function action_numtest($tid)
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            echo $tid;
            $bid=ORM::factory('qfx_trade')->where('tid','=',$tid)->find()->bid;

            $this->access_token = ORM::factory('qfx_login')->where('id','=',$bid)->find()->access_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('qfx_cfg')->getCfg($tempbid);

            if (!$this->access_token)  die("$bid not found.\n");


            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.trade.get';
            $params = array(
                'tid'=>$tid,
                //'fields' => 'tid,title,num_iid,orders,status,pay_time',
            );

             $result = $client->post($method, $this->methodVersion, $params, $files);
             echo "<pre>";
             var_dump($result);



    exit();
    }

    public function action_stats_goods()
    {
        //$goods=ORM::factory('qfx_order')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `qfx_orders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qfx_orders.* FROM `qfx_trades`,qfx_orders WHERE qfx_orders.tid=qfx_trades.tid and qfx_trades.status!='TRADE_CLOSED' and qfx_trades.status!='TRADE_CLOSED_BY_USER' and qfx_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qfx_orders.* FROM `qfx_trades`,qfx_orders WHERE qfx_orders.tid=qfx_trades.tid and qfx_trades.status!='TRADE_CLOSED' and qfx_trades.status!='TRADE_CLOSED_BY_USER' and qfx_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qfx/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qfx_orders.* FROM `qfx_trades`,qfx_orders WHERE qfx_orders.tid=qfx_trades.tid and qfx_trades.status!='TRADE_CLOSED' and qfx_trades.status!='TRADE_CLOSED_BY_USER' and qfx_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qfx_orders.* FROM `qfx_trades`,qfx_orders WHERE qfx_orders.tid=qfx_trades.tid and qfx_trades.status!='TRADE_CLOSED' and qfx_trades.status!='TRADE_CLOSED_BY_USER' and qfx_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        $this->template->content = View::factory('weixin/qfx/admin/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }

    public function action_setgoods()
    {
        $bid = $this->bid;
        $config = ORM::factory('qfx_cfg')->getCfg($bid, 1);
        //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->access_token = ORM::factory('qfx_login')->where('id','=',$bid)->find()->access_token;
        $tempconfig=ORM::factory('qfx_cfg')->getCfg($this->bid);
        if($this->access_token)
        {
            $page = max($_GET['page'], 1);

            $client = new YZTokenClient($this->access_token);
            $method = 'kdt.items.onsale.get';
            $params = array(
                 'page_size'=>20,
                 'page_no'=>$page,
                'fields' => 'num_iid,title,price,pic_url,num,sold_num',
            );


                //修改佣金
            if ($_POST['form']['num_iid']) {
                $goodid = $_POST['form']['num_iid'];
                $setgoods = ORM::factory('qfx_setgood')->where('bid', '=', $bid)->where('goodid','=',$goodid)->find();
                    $setgoods->money1=$_POST['form']['money1'];
                    $setgoods->goodid=$_POST['form']['num_iid'];
                    $setgoods->bid=$bid;
                    $setgoods->title=$_POST['form']['title'];
                    $setgoods->save();
            }

             $result = $client->post($method, '1.0.0', $params, $files);

              $pages = Pagination::factory(array(
                'total_items'   =>$result['response']['total_results'],
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qfx/admin/pages');
      }
      else
        $result['response']=array();

    $this->template->content=View::factory('weixin//qfx/admin/setgoods')
    ->bind('result',$result['response'])
    ->bind('pages',$pages)
    ->bind('bid',$this->bid);

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
    private function sendsuccess($openid, $nickname,  $remark='恭喜您成功获得资格，赶紧点击菜单【生成海报】吧') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_success_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '尊敬的用户，您提交的申请已经审核通过！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $nickname;
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = '已通过';
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword3']['color'] = '#666666';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_qfx:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_qfx:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_qfx:tpl", print_r($result, true));
        return $result;
    }
}
