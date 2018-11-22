<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_snsa extends Controller_Base {

    public $template = 'weixin/sns/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $we;
    public function before() {

        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();

        $this->bid = $_SESSION['snsa']['bid'];
        $this->config = $_SESSION['snsa']['config'];
        //$this->access_token=ORM::factory('sns_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/snsa/login');
            header('location:/snsa/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->count_all();

            //$todo['tickets'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('sns_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            //$todo['all'] = $todo['items'] + $todo['users'];
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

        Request::instance()->redirect('https://open.koudaitong.com/oauth/authorize?client_id=234f0564ec0d951f34&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/snsa/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.koudaitong.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"234f0564ec0d951f34",
            "client_secret"=>"0260f2c5eb62fb486cab11fc4d4f9469",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/snsa/callback'
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
            require Kohana::find_file("vendor/kdt","oauth/KdtApiOauthClient");
            $oauth=new KdtApiOauthClient();
            $value=$oauth->get($result->access_token,'kdt.shop.basic.get')["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['sid'];
            $name = $value['name'];
            $usershop = ORM::factory('sns_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("snsa/home")."';</script>";
        }
        //Request::instance()->redirect('snsa/home');
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('sns_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('sns_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }
            //重新读取配置
            $config = ORM::factory('sns_cfg')->getCfg($bid, 1);
        }


       // $result['tpl'] = ORM::factory('sns_cfg')->where('bid', '=', $bid)->where('key', '=', 'snstpl')->find()->id;
        //$result['tplhead'] = ORM::factory('sns_cfg')->where('bid', '=', $bid)->where('key', '=', 'snstplhead')->find()->id;
       // $access_token = ORM::factory('sns_login')->where('id', '=', $bid)->find()->access_token;

       // if(!$access_token){
           // $oauth=1;
        //}
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/sns/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('config', $config);
    }
        public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);

        $result['skus'] = ORM::factory('sns_sku')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '返还管理';
        $this->template->content = View::factory('weixin/sns/admin/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('sns_sku');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('snsa/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/sns/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);

        $sku = ORM::factory('sns_sku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('snsa/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('snsa/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/sns/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_customer($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid,1);

        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) {
                    $qrcode_edit->locked = $_POST['form']['lock'];
                    $qrcode_edit->save();
                }
            }
        }

       // $qrcode = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',1)->or_where('lv','=',3)->where_close();
        $qrcode = ORM::factory('sns_qrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where('nickname', 'like', $s);
            //->or_where('openid', 'like', $s);
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        // if ($_GET['fopenid']) {//下线
        //     $result['fopenid'] = trim($_GET['fopenid']);
        //     $result['fuser'] = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
        //     $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        // }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/sns/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/sns/admin/customer')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

     public function action_order()
    {

        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('sns_order')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('nickname', 'like', $s)->find()->id;
            $order=$order->where('qid','=',$qid);
        }
        $statue=0;
        if($_GET['type'])
        {
            if($_GET['type']==1)
            {
                $statue=1;
                $order=$order->where('goodid','>',2);
            }
            if($_GET['type']==2)
            {
                $statue=2;
                $order=$order->where('goodid','<',3);
            }
        }
        $result['countall'] = $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/sns/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/sns/admin/order')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('status',$statue)
            ->bind('config', $config);

    }

    public function action_item()
    {

        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $items = ORM::factory('sns_item')->where('bid', '=', $bid);
        $items = $items->reset(FALSE);

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $items = $items->where('name', 'like', $s);
            //->or_where('openid', 'like', $s);
        }
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('sns_item')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['residue'])) {
                    $qrcode_edit->residue = $_POST['form']['residue'];
                    $qrcode_edit->save();
                }
            }
        }

        $result['countall'] = $items->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/sns/admin/pages');

        $result['items'] = $items->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/sns/admin/item')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    public function action_statistics()
    {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);
        $injoin=orm::factory('sns_qrcode')->where('bid','=',$bid)->where_open()->where('flag','>',0)->or_where('gid','>',0)->where_close()->count_all();
        $ininjoin=$injoin-orm::factory('sns_qrcode')->where('bid','=',$bid)->where('flag','>',0)->where('gid','>',0)->count_all();
        $shareapp=ORM::factory('sns_cfg')->where('bid','=',$bid)->where('key','=','ShareApp')->find();

        $shareline=ORM::factory('sns_cfg')->where('bid','=',$bid)->where('key','=','Timeline')->find();
        $groupnum=ORM::factory('sns_qrcode')->where('oid1','>',0)->count_all();
        $result['injoin']=$injoin;
        $result['ininjoin']=$ininjoin;
        $result['shareapp']=$shareapp->value;
        $result['shareline']=$shareline->value;
        $result['groupnum']=$groupnum;
        $result['kaituan']=ORM::factory('sns_qrcode')->where('flag','>',0)->count_all();
        $result['uv']=ORM::factory('sns_qrcode')->count_all();
        $this->template->content = View::factory('weixin/sns/admin/statistics')
            ->bind('result', $result);

    }


    //审核管理
    public function action_qrcodes_m($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];

                    $qrcode_edit->name = $_POST['form']['name'];
                    $qrcode_edit->tel = $_POST['form']['tel'];
                    $qrcode_edit->bz = $_POST['form']['bz'];
                    $qrcode_edit->save();
                }
                if ($_POST['form']['score'])
                    ORM::factory('sns_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',1)->or_where('lv','=',3)->where_close();
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

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/sns/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/sns/admin/qrcodes_m')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //分销审核
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];
                    $qrcode_edit->name = $_POST['form']['name'];
                    $qrcode_edit->tel = $_POST['form']['tel'];
                    $qrcode_edit->bz = $_POST['form']['bz'];
                    $qrcode_edit->save();
                    if((int)$_POST['form']['lv']==1){
                        //给予编号
                        $front_user = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('id', '<', $qrcode_edit->id)->order_by('id','desc')->find();
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

        $qrcode = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('lv','=',2);
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

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/sns/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/sns/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['snsa']['admin'] < 1) Request::instance()->redirect('snsa/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('sns_login');
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
        ))->render('weixin/sns/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/sns/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['snsa']['admin'] < 2) Request::instance()->redirect('snsa/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('sns_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('sns_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('snsa/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/sns/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['snsa']['admin'] < 2) Request::instance()->redirect('snsa/home');

        $bid = $this->bid;

        $login = ORM::factory('sns_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('sns_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('snsa/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('sns_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('snsa/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/sns/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/sns/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('sns_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['snsa']['bid'] = $biz->id;
                    $_SESSION['snsa']['user'] = $_POST['username'];
                    $_SESSION['snsa']['admin'] = $biz->admin; //超管
                    $_SESSION['snsa']['config'] = ORM::factory('sns_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['snsa']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/snsa/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['snsa'] = null;
        header('location:/snsa/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "sns_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
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
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from sns_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from sns_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from sns_trades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[0]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from sns_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();

                $newadd[0]['commision']=$commision[0]['paymoney'];
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
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `sns_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from sns_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/sns/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `sns_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from sns_trades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from sns_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from sns_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from sns_trades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$i]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from sns_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
               // var_dump($commision);
                $newadd[$i]['commision']=$commision[0]['paymoney'];
            }
        }


        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `sns_qrcodes` where bid=$this->bid UNION select left(pay_time,10) from sns_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/sns/admin/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }


    public function action_history_trades()
    {

        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $trade = ORM::factory('sns_trade')->where('bid', '=', $bid);
        $trade = $trade->reset(FALSE);

        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from sns_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/sns/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/sns/admin/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid);
        $outmoney=ORM::factory('sns_score')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from sns_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/sns/admin/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/sns/admin/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }

    public function action_num()
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","KdtApiOauthClient");
            $tradeid=ORM::factory('sns_trade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('sns_order')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('sns_cfg')->getCfg($tempbid);
                    $this->access_token = ORM::factory('sns_login')->where('id','=',$tempbid)->find()->access_token;
                    if (!$this->access_token) //die("$bid not found.\n");
                    continue;

                    $client = new KdtApiOauthClient();
                    $method = 'kdt.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($this->access_token,$method, $params);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('sns_order')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
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
            require_once Kohana::find_file("vendor/kdt","KdtApiOauthClient");
            echo $tid;
            $bid=ORM::factory('sns_trade')->where('tid','=',$tid)->find()->bid;

            $this->access_token = ORM::factory('sns_login')->where('id','=',$bid)->find()->access_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('sns_cfg')->getCfg($tempbid);

            if (!$this->access_token)  die("$bid not found.\n");


            $client = new KdtApiOauthClient();
            $method = 'kdt.trade.get';
            $params = array(
                'tid'=>$tid,
                //'fields' => 'tid,title,num_iid,orders,status,pay_time',
            );

             $result = $client->post($this->access_token,$method, $params);
             echo "<pre>";
             var_dump($result);



    exit();
    }

    public function action_stats_goods()
    {
        //$goods=ORM::factory('sns_order')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `sns_orders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT sns_orders.* FROM `sns_trades`,sns_orders WHERE sns_orders.tid=sns_trades.tid and sns_trades.status!='TRADE_CLOSED' and sns_trades.status!='TRADE_CLOSED_BY_USER' and sns_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT sns_orders.* FROM `sns_trades`,sns_orders WHERE sns_orders.tid=sns_trades.tid and sns_trades.status!='TRADE_CLOSED' and sns_trades.status!='TRADE_CLOSED_BY_USER' and sns_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/sns/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT sns_orders.* FROM `sns_trades`,sns_orders WHERE sns_orders.tid=sns_trades.tid and sns_trades.status!='TRADE_CLOSED' and sns_trades.status!='TRADE_CLOSED_BY_USER' and sns_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT sns_orders.* FROM `sns_trades`,sns_orders WHERE sns_orders.tid=sns_trades.tid and sns_trades.status!='TRADE_CLOSED' and sns_trades.status!='TRADE_CLOSED_BY_USER' and sns_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        $this->template->content = View::factory('weixin/sns/admin/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }

    public function action_group($id=''){
        $bid = $this->bid;
        $config = ORM::factory('sns_cfg')->getCfg($bid, 1);

        if($_GET['qid'])
        {
           $resgroups=DB::query(database::SELECT,"select * from sns_qrcodes where gid= $id")->execute()->as_array();
            $flag=1;
            $qid=$_GET['qid'];
        }
        else
        {
            $resgroups=DB::query(database::SELECT,"select * from sns_groups ORDER by id ")->execute()->as_array();

            $flag=0;
        }
        $countall=count($resgroups);
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
         ))->render('weixin/sns/admin/pages');

        if($flag==1)
        {
            $resgroups=DB::query(database::SELECT,"select * from sns_qrcodes where gid= $id ORDER by id desc limit $this->pagesize offset $offset")->execute()->as_array();
        }
        else
        {

            $resgroups=DB::query(database::SELECT,"select * from sns_groups ORDER by id desc limit $this->pagesize offset $offset")->execute()->as_array();
        }
        $this->template->content=View::factory('weixin/sns/admin/group')
        ->bind('result',$resgroups)
        ->bind('pages',$pages)
        ->bind('bid',$this->bid)
        ->bind('flag',$flag)
        ->bind('qid',$qid)
        ->bind('total',$countall);
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
        //Kohana::$log->add("weixin_sns:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_sns:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_sns:tpl", print_r($result, true));
        return $result;
    }
}
