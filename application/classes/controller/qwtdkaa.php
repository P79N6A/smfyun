<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtdkaa extends Controller_Base {

    public $template = 'weixin/qwt/tpl/dkaatpl';
    public $pagesize = 20;
    public $yzaccess_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',13)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            if(Request::instance()->action == 'home'){
                $hasover = 1;
                @View::bind_global('hasover', $hasover);
            }
        }
    }
    public function after() {
        if ($this->bid) {
            $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->find_all();
            $this->template->todo = $todo;
        }
        @View::bind_global('bid', $this->bid);
        @View::bind_global('todo', $todo);
        parent::after();
    }
    public function action_index() {
        $this->action_login();
    }
    public function action_otag(){
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $client = new YZTokenClient($this->yzaccess_token);
        $tag_name =  ORM::factory('qwt_dkacfg')->where('bid','=',$this->bid)->where('key','=','tag_name')->find()->value;
        $sql = DB::query(Database::SELECT,"SELECT openid as OP FROM qwt_dkaqrcodes where `bid`=$this->bid and `ticket`!= 'NULL'");
        $openid = $sql->execute()->as_array();
        for($p=0;$openid[$p];$p++){
            //echo $openid[$p]['OP'].'<br>';
            $method = 'youzan.users.weixin.follower.tags.add';
            $params = [
            'tags' =>$tag_name,

            'weixin_openid' =>$openid[$p]['OP'],
            ];
            $test=$client->post($method, $this->methodVersion, $params, $files);
          }
    }
    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('qwt_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }
        $cert_file = DOCROOT."dka/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dka/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('qwt_dkacfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }
            //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file));
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                // $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dir($key_file));
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                // $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'fxb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'fxb_file_key', '', file_get_contents($key_file));
            //重新读取配置
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('qwt_dkacfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
                $_POST['menu']['$k']=$v;
            }
            foreach ($_POST['menu'] as $value) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        }
        // if($_POST){
        //     echo "<pre>";
        //     var_dump($_POST);
        //     echo "</pre>";

        // }
        // if($_FILES){
        //     echo "<pre>";
        //     var_dump($_FILES);
        //     echo "</pre>";
        //     exit();
        // }
        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('qwt_dkacfg');
            $qrfile = DOCROOT."dka/tmp/tpl.$bid.jpg";
            //海报有效期
            if ($_POST['text']['ticket_lifetime'] >= 30) $_POST['text']['ticket_lifetime'] = 30;

            //二维码海报
            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] == 2) {
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
                    $default_head_file = DOCROOT."dka/tmp/head.$bid.jpg";
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

                        $tpl = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('qwt_dkacfg');
            // $count = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
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

            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }
        if ($_POST['dka']){
            $dka = $_POST['dka'];
            $cfg = ORM::factory('qwt_dkacfg');
            $qrfile = DOCROOT."dka/tmp/dka.$bid.jpg";
                        //打卡背景图
            if ($_FILES['pic3']['error'] == 0) {
                if ($_FILES['pic3']['size'] > 1024*400) {
                    $result['err5'] = '打卡背景文件不能超过 400K';
                } else {
                    $result['ok5'] += $ok;
                    $cfg->setCfg($bid, 'dka', '', file_get_contents($_FILES['pic3']['tmp_name']));
                    @unlink($qrfile);
                    move_uploaded_file($_FILES['pic3']['tmp_name'], $qrfile);
                }
            }
            foreach ($dka as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok5'] += $ok;
            }

            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        }
        if ($_POST['tag']){
            $tag = $_POST['tag'];
            $cfg = ORM::factory('qwt_dkacfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }


            $this->action_otag();
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);

        }

        //抽奖配置

        if(isset($_POST['draw']) || isset($_POST['prize'])) {
            $draw = $_POST['draw'];
            $cfg=ORM::factory('qwt_dkacfg');

            foreach ($draw as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok7'] += $ok;
            }

            $prize = ORM::factory('qwt_dkaprize')->where('bid', '=', $bid)->find();
            $POSTprize = $_POST['prize'];

            foreach ($POSTprize as $v) {
                // if(!ORM::factory('qwt_dkaprize')->where('bid', '=', $bid)->where('type','=',$v['type'])->find()->id){//插入
                //     $prize = ORM::factory('qwt_dkaprize');
                // }else{//更新
                //     $prize = ORM::factory('qwt_dkaprize')->where('bid', '=', $bid)->where('type','=',$v['type'])->find();
                // }
                $prize = ORM::factory('qwt_dkaprize')->where('bid', '=', $bid)->where('type','=',$v['type'])->find();
                $prize->bid = $this->bid;
                $prize->iid = $v['iid'];
                $prize->type = $v['type'];
                $prize->probability = $v['pro'];
                $prize->endtime = $draw['drawtime'];
                $prize->stock = $v['stock'];
                $ok = $prize->save();
                $result['ok7'] += $ok;
            }
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid,1);

        }

        //奖品设置
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid,1);
        $result['items'] = ORM::factory('qwt_dkaitem')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all()->as_array();
        //设置显示
        $result['prizes'] = ORM::factory('qwt_dkaprize')->where('bid', '=', $bid)->order_by('type', 'ASC')->find_all()->as_array();

        $result['tpl'] = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['dka'] = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'dka')->find()->id;

        $yzaccess_token = ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;

        if(!$yzaccess_token){
            $oauth=1;
        }

        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }
    public function action_lab() {
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        //$this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if ($_POST['tag']['tag_name']){//点击刷新
            $tag = $_POST['tag'];
            $cfg = ORM::factory('qwt_dkacfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }
            $result['fresh'] = 1;
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
            // $next_qid=$config['next_qid'];
            // if(!$next_qid) $next_qid=0;
            $users = ORM::factory('qwt_dkaqrcode')->where('bid','=',$bid)->find_all();
            foreach ($users as $k => $v) {
                $labuser[]="($v->bid,$v->id,'{$config['tag_name']}')";
            }
            $sql='INSERT IGNORE INTO qwt_dkalabs (`bid`,`qid`,`lab_name`) VALUES '. join(',',$labuser);
            DB::query(Database::INSERT,$sql)->execute();
            $result['fresh'] = 1;
        }
        $result['islab'] = ORM::factory('qwt_dkalab')->where('bid','=',$bid)->where('status','=',1)->count_all();
        $result['alllab'] = ORM::factory('qwt_dkalab')->where('bid','=',$bid)->count_all();
        $this->template->title = '首页';
        //$this->template->content = View::factory('weixin/wdy/admin/lab')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/lab')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);

        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                if ($_POST['form']['score']) ORM::factory('qwt_dkascore')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                if ($_POST['form']['cash']) ORM::factory('qwt_dkadetail')->cashIn($qrcode_edit, 0, $_POST['form']['cash']);
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid);
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
            $result['fuser'] = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
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
        ))->render('weixin/dka/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        //$this->template->content = View::factory('weixin/dka/admin/qrcodes')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);

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

                $order = ORM::factory('qwt_dkaorder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
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
        if($_POST['edit_oid']){
            // echo '<pre>';
            // var_dump($_POST['edit']);
            // exit;
            $edit_order = ORM::factory('qwt_dkaorder')->where('id','=',$_POST['edit_oid'])->find();
            $edit_order->name = $_POST['edit']['name'];
            $edit_order->tel = $_POST['edit']['tel'];
            $edit_order->city = $_POST['edit']['city'];
            $edit_order->address = $_POST['edit']['address'];
            $edit_order->save();
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('qwt_dkaorder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';

                    $tempname=ORM::factory("qwt_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("qwt_dkaitem")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("qwt_dkaqrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                }
                $order->save();
            }

            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        if ($action == 'ship' && $id) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $we = new Wechat($config);

            $order = ORM::factory('qwt_dkaorder')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();

                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['dkaa']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['dkaa']['shipcode'] = $_REQUEST['shipcode'];
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
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';

                    $tempname=ORM::factory("qwt_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("qwt_dkaitem")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("qwt_dkaqrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                }
                //Request::instance()->redirect('qwtdkaa/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('qwt_dkaorder')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            $order = $order->and_where_open();
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            if($countuser>0){
                $user = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
                $userarr = array();
                foreach ($user as $k => $v) {
                    $userarr[$k] = $v->id;
                }
                $order = $order->or_where('qid', 'IN', $userarr);
            }
            $order = $order->and_where_close();
        }

        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('qwt_dkaqrcode', $result['qid']);
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
        //  if ($_GET['type']=="code") {
        //     $order = $order->where('type', '=', 4);
        //     $active_type="code";
        // }
        if ($_GET['type']=="hb") {
            $order = $order->where('type', '=', 4);
            $active_type="hb";
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
                    $orders = $order->limit(1000)->find_all();
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
                //$count2 = ORM::factory('qwt_dkascore')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('qwt_dkascore')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

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
        ))->render('weixin/dka/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        //$this->template->content = View::factory('weixin/dka/admin/orders')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('activetype',$active_type);
    }
    //积分清零
    public function action_zero() {
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('qwt_wfblogin')->where('id', '=', $bid)->find()->access_token;
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/zero')
            //->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }

    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            $id=$_GET['id'];
            $item=ORM::factory('qwt_dkaitem')->where('id','=',$id)->find();
            if (ORM::factory('qwt_dkaorder')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('qwtdkaa/items');
            }else{
                $result['error'] = '已经被兑换过的奖品不能删除，您可以设置为隐藏';
            }
        }
        $result['items'] = ORM::factory('qwt_dkaitem')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all();
        $iid = ORM::factory('qwt_dkaitem')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('qwt_dkaorder')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }
        // var_dump($convert);
        // exit();
        $this->template->title = '奖品管理';
        //$this->template->content = View::factory('weixin/dka/admin/items')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/items')
            ->bind('convert',$convert)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);
         require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $smfy=new SmfyQwt();
        if($admin->yzaccess_token){
            //echo $admin->yzaccess_token."<br>";
            $result['yz']=1;
            $client = new YZTokenClient($admin->yzaccess_token);
            $result['yzcoupons']=$smfy->getyzcoupons($bid,$client);
            $result['yzgifts']=$smfy->getyzgifts($bid,$client);
        }
        if($admin->appid){
            $result['wx']=1;
            $result['wxcards']=$smfy->getwxcards($bid);
        }
        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信优惠券列表';
                }
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==6){
                if(!$_POST['yzgift']){
                    $result['error'] = '未拉取到有赞赠品列表';
                }
                $_POST['data']['url']=$_POST['yzgift'];
            }elseif ($_POST['data']['type']==5) {
                if(!$_POST['yzcoupons']){
                    $result['error'] = '未拉取到有赞优惠券列表';
                }
                $_POST['data']['url']=$_POST['yzcoupons'];
            }
            $item = ORM::factory('qwt_dkaitem');
            $item->values($_POST['data']);
            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['score'] || !$_POST['data']['stock']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] == 2) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*300) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }


            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "dka:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtdkaa/items');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加新奖品';
        //$this->template->content = View::factory('weixin/dka/admin/items_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/items_add')
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons',$yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $smfy=new SmfyQwt();
        if($admin->yzaccess_token){
            //echo $admin->yzaccess_token."<br>";
            $result['yz']=1;
            $client = new YZTokenClient($admin->yzaccess_token);
            $result['yzcoupons']=$smfy->getyzcoupons($bid,$client);
            $result['yzgifts']=$smfy->getyzgifts($bid,$client);
        }
        if($admin->appid){
            $result['wx']=1;
            $result['wxcards']=$smfy->getwxcards($bid);
        }
        $item = ORM::factory('qwt_dkaitem', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('qwt_dkaorder')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('qwtdkaa/items');
            }else{
                $result['error'] = '已经被兑换过的奖品不能删除，您可以设置为隐藏';
            }
        }

        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信优惠券列表';
                }
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==6){
                if(!$_POST['yzgift']){
                    $result['error'] = '未拉取到有赞赠品列表';
                }
                $_POST['data']['url']=$_POST['yzgift'];
            }elseif ($_POST['data']['type']==5) {
                if(!$_POST['yzcoupons']){
                    $result['error'] = '未拉取到有赞优惠券列表';
                }
                $_POST['data']['url']=$_POST['yzcoupons'];
            }
            $item->values($_POST['data']);
            $item->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] == 2) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*300) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "dka:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtdkaa/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        //$this->template->content = View::factory('weixin/dka/admin/items_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/items_add')
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons',$yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_logout() {
        $_SESSION['dkaa'] = null;
        header('location:/qwtdkaa/home');
        exit;
    }
    public function action_hb_check() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        //$this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;

        if ($_POST['hb_check']){
            $hb_check = $_POST['hb_check'];
            $cfg = ORM::factory('qwt_dkacfg');
            foreach ($hb_check as $k => $v) {
                $ok=$cfg->setCfg($bid,$k,$v);
                $result['ok8']+=$ok;
            }
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid, 1);
        }

        $this->template->title = '首页';
        //$this->template->content = View::factory('weixin/dka/admin/hb_check')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/hb_check')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_dka$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('qwt_dkascore')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('qwt_dkaqrcode')->table_name())
            ->set(array('score' => '0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            Request::instance()->redirect('qwtdkaa/home');
        }
    }
    // public function action_empty() {
    //     //ignore_user_abort(true);//关掉浏览器，PHP脚本也可以继续执行.
    //     set_time_limit(0);
    //     if ($_GET['DELETE'] == 1) {
    //         $zero = ORM::factory('qwt_wfbzero')->where('bid', '=', $this->bid)->find();
    //         $zero->bid = $this->bid;
    //         $zero->status = 0;
    //         $zero->lastupdate = time();
    //         $zero->save();
    //         exit;
    //     }
    // }
    public function action_setgoods(){
        $bid = $this->bid;
        require Kohana::find_file("vendor/kdt","oauth/YZTokenClient");
        $tempconfig=ORM::factory('qwt_dkacfg')->getCfg($this->bid);
        if($this->yzaccess_token)
        {
            $page = max($_GET['page'], 1);
            $client = new YZTokenClient($this->yzaccess_token);
            $method = 'kdt.items.onsale.get';
            $params = array(
                 'page_size'=>20,
                 'page_no'=>$page,
                'fields' => 'num_iid,title,price,pic_url,num,sold_num',
            );


                //修改佣金
            if ($_POST['form']['num_iid']) {
                $goodid = $_POST['form']['num_iid'];
                $setgoods = ORM::factory('qwt_dkasetgood')->where('bid', '=', $bid)->where('goodid','=',$goodid)->find();

                    $setgoods->money0=$_POST['form']['money0'];
                    $setgoods->money1=$_POST['form']['money1'];
                    $setgoods->money2=$_POST['form']['money2'];

                    $setgoods->goodid=$_POST['form']['num_iid'];
                    $setgoods->bid=$bid;
                    $setgoods->title=$_POST['form']['title'];
                    $setgoods->save();


            }

             $result = $client->post($method, '1.0.0', $params, $files);
              $pages = Pagination::factory(array(
                'total_items'   =>$result['response']['total_results'],
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/dka/admin/pages');
      }
      else{
        die('请先在【绑定有赞】处点击【一键授权给有赞】');
        $result['response']=array();
      }


    //$this->template->content=View::factory('weixin//dka/admin/setgoods')
    $this->template->father = View::factory('weixin/qwt/tpl/atpl');
    $this->template->content = View::factory('weixin/qwt/admin/dka/setgoods')
    ->bind('result',$result['response'])
    ->bind('pages',$pages)
    ->bind('bid',$this->bid);

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
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_dkaqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qwt_dkaqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                // $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qwt_dkatrades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                // $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                // $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                // $newadd[0]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                // $commision=DB::query(Database::SELECT,"SELECT SUM(cash) AS paymoney from qwt_dkadetails where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();

                // $newadd[0]['commision']=$commision[0]['paymoney'];

                //活动参与人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from qwt_dkaqrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' or FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over')")->execute()->as_array();
                $newadd[0]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `qwt_dkaorders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')>='$begin' and FROM_UNIXTIME(`createdtime`, '$daytype')<='$over' ")->execute()->as_array();
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
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_dkaqrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qwt_dkatrades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/dka/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_dkaqrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qwt_dkatrades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_dkaqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qwt_dkaqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
               //  $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qwt_dkatrades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
               //  $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
               //  $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
               //  $newadd[$i]['payment']=$tradesdata[0]['payment'];

               //  //所有佣金 已结算的佣金、待结算的佣金
               //  $commision=DB::query(Database::SELECT,"SELECT SUM(cash) AS paymoney from qwt_dkadetails where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
               // // var_dump($commision);
               //  $newadd[$i]['commision']=$commision[0]['paymoney'];

                //参加活动人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from qwt_dkaqrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' or FROM_UNIXTIME(`jointime`, '$daytype')='$time')")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `qwt_dkaorders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
        }


        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `qwt_dkaqrcodes` where bid=$this->bid UNION select left(pay_time,10) from qwt_dkatrades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        //$this->template->content = View::factory('weixin/dka/admin/stats_totle')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }


    public function action_history_trades()
    {

        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $trade = ORM::factory('qwt_dkatrade')->where('bid', '=', $bid);
        $trade = $trade->reset(FALSE);


        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from qwt_dkaqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/dka/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        //$this->template->content = View::factory('weixin/dka/admin/history_trades')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('qwt_dkacfg')->getCfg($bid);
        $outmoney=ORM::factory('qwt_dkadetail')->where('bid',"=",$bid)->where('cash','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from qwt_dkaqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/dka/admin/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        //$this->template->content = View::factory('weixin/dka/admin/history_withdrawals')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }
public function action_stats_goods()
    {
        //$goods=ORM::factory('qwt_dkaorder')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `qwt_dkaorders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_dkaorder1s.* FROM `qwt_dkatrades`,qwt_dkaorder1s WHERE qwt_dkaorder1s.tid=qwt_dkatrades.tid and qwt_dkatrades.status!='TRADE_CLOSED' and qwt_dkatrades.status!='TRADE_CLOSED_BY_USER' and qwt_dkatrades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_dkaorder1s.* FROM `qwt_dkatrades`,qwt_dkaorder1s WHERE qwt_dkaorder1s.tid=qwt_dkatrades.tid and qwt_dkatrades.status!='TRADE_CLOSED' and qwt_dkatrades.status!='TRADE_CLOSED_BY_USER' and qwt_dkatrades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dka/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_dkaorder1s.* FROM `qwt_dkatrades`,qwt_dkaorder1s WHERE qwt_dkaorder1s.tid=qwt_dkatrades.tid and qwt_dkatrades.status!='TRADE_CLOSED' and qwt_dkatrades.status!='TRADE_CLOSED_BY_USER' and qwt_dkatrades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_dkaorder1s.* FROM `qwt_dkatrades`,qwt_dkaorder1s WHERE qwt_dkaorder1s.tid=qwt_dkatrades.tid and qwt_dkatrades.status!='TRADE_CLOSED' and qwt_dkatrades.status!='TRADE_CLOSED_BY_USER' and qwt_dkatrades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        //$this->template->content = View::factory('weixin/dka/admin/stats_goods')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/dka/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }
    public function action_num()
    {

            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $tradeid=ORM::factory('qwt_dkatrade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('qwt_dkaorder1')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('qwt_dkacfg')->getCfg($tempbid);

                    if (!$tempconfig['yz_appid']) //die("$bid not found.\n");
                    continue;

                    $client = new KdtApiClient($this->yzaccess_token);
                    $method = 'youzan.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($method, $this->methodVersion, $params, $files);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('qwt_dkaorder1')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
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
     private function hongbao($config, $openid, $we='', $bid=1, $money){
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $we = new Wechat($config);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $config['appid'];//appid
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
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
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

        $cert_file = DOCROOT."dka/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dka/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."dka/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_dkafile_cert')->find();
        $file_key = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_dkafile_key')->find();
        $file_rootca = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_dkafile_rootca')->find();

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
