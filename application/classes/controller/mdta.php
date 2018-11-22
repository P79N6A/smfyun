<?php defined('SYSPATH') or die('No direct script access.');

//麻袋团后台
class Controller_Mdta extends Controller_Base {

    public $template = 'weixin/mdt/tpl/matpl';
    public $pagesize = 20;

    public $config;
    public $bid;

    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();

        $this->bid = $_SESSION['mdta']['bid'];
        $this->config = $_SESSION['mdta']['config'];

        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/mdta/login');
            header('location:/mdta/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after2() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            $todo['items'] = ORM::factory('mdt_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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
        Request::instance()->redirect('mdta/items');

        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('mdt_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('mdt_login', $bid);
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
            $cfg = ORM::factory('mdt_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('mdt_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('mdt_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('mdt_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('mdt_cfg');
            $qrfile = DOCROOT."wdy/tmp/tpl.$bid.jpg";

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
                    $default_head_file = DOCROOT."wdy/tmp/head.$bid.jpg";
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
                    if ($result['ok3'] && file_exists($qrfile)) touch($qrfile);
                }
            }

            //重新读取配置
            $config = ORM::factory('mdt_cfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('mdt_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('mdt_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/mdt/admin/home')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('mdt_cfg')->getCfg($bid);

        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('mdt_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                if ($_POST['form']['score']) ORM::factory('mdt_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('mdt_qrcode')->where('bid', '=', $bid);
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
            $result['fuser'] = ORM::factory('mdt_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }

        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/mdt/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/mdt/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('mdt_cfg')->getCfg($bid);

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

                $order = ORM::factory('mdt_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                if ($order->status == 0) {
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

        //订单发货
        if ($action == 'ship' && $id) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $we = new Wechat($config);

            $order = ORM::factory('mdt_order')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();

                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['mdta']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['mdta']['shipcode'] = $_REQUEST['shipcode'];
                    $order->shiptype = $_REQUEST['shiptype'];
                    $order->shipcode = $_REQUEST['shipcode'];

                    $order->save();

                    //发微信消息给用户
                    $shipmsg = "%s，您的积分兑换商品已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $we->sendCustomMessage($msg);
                }

                //Request::instance()->redirect('mdta/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('mdt_order')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            $order = $order->and_where_close();
        }

        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('mdt_qrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }

        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
            $orders = $order->limit(1000)->find_all();

            $filename = 'ORDERS.'. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('id', '收货人', '收货电话', '收货城市', '收货地址', '备注', '兑换产品', '消耗积分', '订单时间', '是否有关注', '产品ID', 'OpenID', '是否锁定', '直接粉丝', '间接粉丝', '物流公司', '物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($orders as $o) {
                //$count2 = ORM::factory('mdt_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('mdt_qrcode')->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('mdt_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

                //地址处理
                list($prov, $city, $dist) = explode(' ', $o->city);
                $array = array($o->id, $o->name, $o->tel, "{$prov}省 {$city}市 {$dist}", $o->address, $o->memo, $o->item->name, $o->score, date('Y-m-d H:i:s', $o->createdtime), $o->user->subscribe, $o->item->id, $o->user->openid, $o->user->lock, $count2, $count3);

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
        ))->render('weixin/mdt/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/mdt/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //积分商品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);

        $bid = $this->bid;
        $result['items'] = ORM::factory('mdt_item')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '团购商品管理';
        $this->template->content = View::factory('weixin/mdt/admin/items')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;

        if ($_POST['data']) {
            $item = ORM::factory('mdt_item');

            if ($_POST['data']['starttime']) $_POST['data']['starttime'] = strtotime($_POST['data']['starttime']);
            if ($_POST['data']['endtime']) $_POST['data']['endtime'] = strtotime($_POST['data']['endtime']);

            $item->values($_POST['data']);
            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['url'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';
            if (!$_POST['data']['starttime'] || !$_POST['data']['endtime']) $result['error'] = '请填写商品上下架时间';

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
                $key = "wdy:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('mdta/items');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加新商品';
        $this->template->content = View::factory('weixin/mdt/admin/items_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;

        $item = ORM::factory('mdt_item', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $item->delete();
            Request::instance()->redirect('mdta/items');
        }

        if ($_POST['data']) {
            if ($_POST['data']['starttime']) $_POST['data']['starttime'] = strtotime($_POST['data']['starttime']);
            if ($_POST['data']['endtime']) $_POST['data']['endtime'] = strtotime($_POST['data']['endtime']);

            $item->values($_POST['data']);
            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['url'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';
            if (!$_POST['data']['starttime'] || !$_POST['data']['endtime']) $result['error'] = '请填写商品上下架时间';

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
                $key = "wdy:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('mdta/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改商品';
        $this->template->content = View::factory('weixin/mdt/admin/items_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['mdta']['admin'] < 1) Request::instance()->redirect('mdta/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('mdt_login');
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
        ))->render('weixin/mdt/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/mdt/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['mdta']['admin'] < 2) Request::instance()->redirect('mdta/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('mdt_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('mdt_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('mdta/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/mdt/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['mdta']['admin'] < 2) Request::instance()->redirect('mdta/home');

        $bid = $this->bid;

        $login = ORM::factory('mdt_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('mdt_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('mdta/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('mdt_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();

                //appid 重置
                if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                }

                Request::instance()->redirect('mdta/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];

        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/mdt/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/mdt/tpl/mlogin';
        $this->before();

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('mdt_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['mdta']['bid'] = $biz->id;
                    $_SESSION['mdta']['user'] = $_POST['username'];
                    $_SESSION['mdta']['admin'] = $biz->admin; //超管
                    // $_SESSION['mdta']['config'] = ORM::factory('mdt_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '密码不正确';
            }
        }

        if ($_SESSION['mdta']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/mdta/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['mdta'] = null;
        header('location:/mdta/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "mdt_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

}