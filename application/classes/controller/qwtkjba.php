<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtkjba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/kjbatpl';
    public $pagesize = 20;
    public $yzaccess_token;
    public $config;
    public $bid;

    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action == 'rsync_count' ) return;
        if (Request::instance()->action == 'rsync_paymoney' ) return;
        if (Request::instance()->action != 'login' && !$this->bid) {
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',24)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    public function action_test(){
        $qr_num=ORM::factory('qwt_kjbqrcode')->where('bid','=',6)->delete_all();
        exit();
    }
    //系统配置
    public function action_home() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid, 1);
        //$this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
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
        if ($_POST['shop']) {
            if (!$_POST['shop']['tel']) {
                $result['err3']='店铺咨询电话为必填，请填写完整后再提交！';
            }elseif (!$_POST['shop']['url']) {
                $result['err3']='店铺链接为必填，请填写完整后再提交！';
            }elseif (!$_POST['shop']['title']) {
                $result['err3']='砍价活动标题为必填，请填写完整后再提交！';
            }else{
                $result['ok3']='保存成功';
            }
            $buser = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            if(!$buser->appid) die('请在【绑定我们】【微信一键授权】处，点击【一键授权】');

            $cfg = ORM::factory('qwt_kjbcfg');

            foreach ($_POST['shop'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }
            //配置菜单更新
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
            if(!$bid) Kohana::$log->add('kjbbid:', 'meun');//写入日志，可以删除
            $wx = new Wxoauth($bid,$options);
            $data['button'][0]['name']=$_POST['menu']['key_menu'];
            $data['button'][0]['sub_button'][0]['type']='click';
            $data['button'][0]['sub_button'][0]['name']=$_POST['menu']['key_qrcode'];
            $data['button'][0]['sub_button'][0]['key']='qrcode';

            $menu = $wx->createMenu($data);

            if($menu==true) {
                $result['menu']=1;
            }else{
                $result['menu']=0;
            }
            //重新读取配置
            $config = ORM::factory('qwt_kjbcfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('qwt_kjbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_kjbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        // $_POST['shop']['name'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$bid)->where('key','=','name')->find()->value;
        $_POST['shop']['tel'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$bid)->where('key','=','tel')->find()->value;
        $_POST['shop']['url'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$bid)->where('key','=','url')->find()->value;
        $_POST['shop']['title'] = ORM::factory('qwt_kjbcfg')->where('bid','=',$bid)->where('key','=','title')->find()->value;
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        //修改用户
        // if ($_POST['form']['id']) {
        //     $id = $_POST['form']['id'];
        //     $qrcode_edit = ORM::factory('qwt_kjbqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
        //     if ($qrcode_edit->id) {
        //         if (isset($_POST['form']['lock'])){
        //          $qrcode_edit->lock = (int)$_POST['form']['lock'];
        //          $qrcode_edit->save();
        //         }
        //     }
        // }
        $qrcode = ORM::factory('qwt_kjbqrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where('nickname', 'like', $s); //->or_where('openid', 'like', $s);
        }
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kjb/pages');
        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '用户明细';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/qrcodes')
            ->bind('bid', $bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_order_cut(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $events=ORM::factory('qwt_kjbevent')->where('bid','=',$bid);
        $events = $events->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrary[]=0;
            $itary[]=0;
            $qrcodes = ORM::factory('qwt_kjbqrcode')->where('bid','=',$bid)->where('nickname', 'like', $s)->find_all();
            foreach ($qrcodes as $qrcode) {
                $qrary[]=$qrcode->id;
            }
            $items = ORM::factory('qwt_kjbitem')->where('bid','=',$bid)->where('name', 'like', $s)->find_all();
            foreach ($items as  $item) {
                $itary[]=$item->id;
            }
            $events=$events->and_where_open()->where('qid','IN',$qrary)->or_where('iid','IN',$itary)->and_where_close();
        }
        if($_GET['qid']){
            $events=$events->where('qid','=',$_GET['qid']);
        }
        $result['countall'] = $countall =$events->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kjb/pages');
        $events = $events->order_by('lastupdate', 'DESC');
        $result['events'] = $events->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '发起的砍价';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/order_cut')
            ->bind('bid', $bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //砍价记录
    public function action_cutlist($eid){
        $bid = $this->bid;
        $cut = ORM::factory('qwt_kjbcut')->where('bid','=',$bid)->where('eid','=',$eid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/cutlist')
            ->bind('bid', $bid)
            ->bind('cut', $cut);
    }
    //参与的砍价
    public function action_cutjoin($qid){
        $bid = $this->bid;
        $cut = ORM::factory('qwt_kjbcut')->where('bid','=',$bid)->where('qid','=',$qid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/cutjoin')
            ->bind('bid', $bid)
            ->bind('cut', $cut);
    }
    //砍价链接
    public function action_kanlink(){
        $bid = $this->bid;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/kanlink')
            ->bind('bid', $bid);
    }
    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
         //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');
            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[6], array("ASCII",'UTF-8',"GB2312","GBK"));
                // print_r($data);
                if (count($data) < 8) continue;
                if (!is_numeric($data[0])) continue;
                //发货
                $oid = $data[0];
                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[6]);
                    $shipcode = iconv('gbk', 'utf-8', $data[7]);
                } else {
                    $shiptype = $data[6];
                    $shipcode = $data[7];
                }

                $order = ORM::factory('qwt_kjborder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                if ($order->state == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
                    $order->state = 1;
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
            $edit_order = ORM::factory('qwt_kjborder')->where('id','=',$_POST['edit_oid'])->find();
            $edit_order->receive_name = $_POST['edit']['receive_name'];
            $edit_order->tel = $_POST['edit']['tel'];
            // $edit_order->city = $_POST['edit']['city'];
            $edit_order->address = $_POST['edit']['address'];
            $edit_order->save();
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('qwt_kjborder')->where('id', '=', $oid)->find();
                $order->state = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                $order->save();
            }
            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        if ($action == 'ship' && $id) {
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
            $wx = new Wxoauth($bid,$options);
            $order = ORM::factory('qwt_kjborder')->where('id', '=', $id)->find();
            if ($order->state == 0) {
                $order->state = 1;
                $order->save();
                kohana::$log->add('qwtwfb111',print_r(111,true));
                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['qwtwfba']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['qwtwfba']['shipcode'] = $_REQUEST['shipcode'];
                    $order->shiptype = $_REQUEST['shiptype'];
                    $order->shipcode = $_REQUEST['shipcode'];
                    $order->save();
                    //发微信消息给用户
                    $shipmsg = "%s，您的订单已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $res = $wx->sendCustomMessage($msg);
                }
            }
        }
        $result['state'] = 0;
        if ($action == 'done') {
            $result['state'] = 1;
        }
        $order = ORM::factory('qwt_kjborder')->where('bid', '=', $bid)->where('order_state','=',1)->where('state', '=', $result['state']);
        $order = $order->reset(FALSE);
        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('qwt_kjbqrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('qwt_kjbqrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            $order = $order->and_where_open();
                $s = '%'.trim($_GET['s'].'%');
                $order = $order->where('name', 'like', $s)->or_where('tel','like',$s)->or_where('address','like',$s);
            if($countuser>0){
                $user = ORM::factory('qwt_kjbqrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
                $userarr = array();
                foreach ($user as $k => $v) {
                    $userarr[$k] = $v->id;
                }
                $order = $order->or_where('qid', 'IN', $userarr);
            }
            $order = $order->and_where_close();
        }
        if ($_GET['export']=='csv') {
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $title = array('id', '姓名', '商品名称', '收货人', '联系电话', '收货地址','物流公司','物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $order = $order->order_by('lastupdate', 'DESC')->limit(1000)->find_all();
            foreach ($order as $o) {
                $array = array($o->id, $o->user->nickname, $o->item_name, $o->name, $o->tel, $o->address, $o->shiptype, $o->shipcode);
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }
                fputcsv($fp, $array);
            }
            exit;
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kjb/pages');

        $result['orders'] = $order->order_by('pay_time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '订单管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/orders')
            ->bind('bid', $bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
      //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $item=ORM::factory('qwt_kjbitem')->where('bid', '=', $bid);
        $item = $item->reset(FALSE);
        if ($_GET['s']) {
            $item = $item->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $item = $item->where('name', 'like', $s);
            $item = $item->and_where_close();
        }
        $countall = $item->count_all();
        $iid = ORM::factory('qwt_kjbitem')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all()->as_array();
        //var_dump($iid);
         //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kjb/pages');
        $result['items'] = $item->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('pages',$pages)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        if ($_POST['item']) {
            if (!$_POST['item']['name']) {
                $result['err']='商品名称必填，请填写完整后再提交！';
            }elseif (!$_POST['item']['stock']) {
                $result['err']='商品库存必填，请填写完整后再提交！';
            }elseif (!$_POST['item']['old_price']) {
                $result['err']='商品原价必填，请填写完整后再提交！';
            }else{
                if (!$_POST['item']['title']) {
                    $result['err']='砍价活动标题必填，请填写完整后再提交！';
                }elseif ($_POST['item']['price'] > $_POST['item']['old_price']) {
                    $result['err']='商品最低价不得高于原价，请重新填写！';
                }elseif (!$_POST['item']['cut_num']) {
                    $result['err']='商品砍至最低价所需次数必填，请填写完整后再提交！';
                }
            }
            if ($_FILES['pic1']['error'] == 0||$_FILES['pic1']['error'] ==2) {
                if ($_FILES['pic1']['size'] > 1024*400) {
                    $result['err'] = '图片不能超过 400K';
                }
            }
            if ($_POST['item']['endtime']) {
                if ($_POST['item']['begintime']) {
                    if (strtotime($_POST['item']['endtime']) < strtotime($_POST['item']['begintime'])) {
                        $result['err']='终止时间不得在起始时间之前，请重新填写！';
                    }
                }
                if (strtotime($_POST['item']['endtime']) < time()) {
                    $result['err']='终止时间已到，请重新填写！';
                }
            }
            if (!$result['err']) {
                $item = ORM::factory('qwt_kjbitem');
                $item->bid = $bid;
                $item->name = $_POST['item']['name'];
                if ($_FILES['pic1']['error'] == 0) {
                    $item->pic = file_get_contents($_FILES['pic1']['tmp_name']);
                }
                $item->stock = $_POST['item']['stock'];
                $item->old_price = $_POST['item']['old_price'];
                $item->need_sub = $_POST['item']['need_sub'];
                $item->cut_sub = $_POST['item']['cut_sub'];
                // $item->cut_onece = $_POST['item']['cut_onece'];
                $item->begintime = strtotime($_POST['item']['begintime']);
                $item->endtime = strtotime($_POST['item']['endtime']);
                $item->price = $_POST['item']['price'];
                $item->title = $_POST['item']['title'];
                $item->subtitle = $_POST['item']['subtitle'];
                $item->cut_num = $_POST['item']['cut_num'];
                $item->desc = $_POST['item']['desc'];
                $item->rule = $_POST['item']['rule'];
                $item->save();
                Request::instance()->redirect('qwtkjba/items');
            }
        }
        $result['title'] = '添加新奖品';
        $result['action'] = 'add';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/items_add')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);

    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $result['item']['id'] = $id;
        $item=ORM::factory('qwt_kjbitem')->where('id','=',$id)->find();
        if ($_POST['item']) {
            if (!$_POST['item']['name']) {
                $result['err']='商品名称必填，请填写完整后再提交！';
            }elseif (!$_POST['item']['stock']) {
                $result['err']='商品库存必填，请填写完整后再提交！';
            // }elseif (!$_POST['item']['old_price']) {
            //     $result['err']='商品原价必填，请填写完整后再提交！';
            }else{
                if (!$_POST['item']['title']) {
                    $result['err']='砍价活动标题必填，请填写完整后再提交！';
                // }elseif ($_POST['item']['price'] > $_POST['item']['old_price']) {
                //     $result['err']='商品最低价不得高于原价，请重新填写！';
                // }elseif (!$_POST['item']['cut_num']) {
                //     $result['err']='商品砍至最低价所需次数必填，请填写完整后再提交！';
                }
            }
            // echo '<pre>';
            // var_dump($_FILES);
            // exit();
            if ($_FILES['pic1']['error'] == 0) {
                if ($_FILES['pic1']['size'] > 1024*400) {
                    $result['err'] = '图片不能超过 400K';
                }
            }
            if ($_POST['item']['endtime']) {
                if ($_POST['item']['begintime']) {
                    if (strtotime($_POST['item']['endtime']) < strtotime($_POST['item']['begintime'])) {
                        $result['err']='终止时间不得在起始时间之前，请重新填写！';
                    }
                }
                if (strtotime($_POST['item']['endtime']) < time()) {
                    $result['err']='终止时间已到，请重新填写！';
                }
            }
            if (!$result['err']) {
                $item->bid = $bid;
                $item->name = $_POST['item']['name'];
                if ($_FILES['pic1']['error'] == 0) {
                    $item->pic = file_get_contents($_FILES['pic1']['tmp_name']);
                }
                $item->stock = $_POST['item']['stock'];
                // $item->old_price = $_POST['item']['old_price'];
                $item->need_sub = $_POST['item']['need_sub'];
                $item->cut_sub = $_POST['item']['cut_sub'];
                // $item->cut_onece = $_POST['item']['cut_onece'];
                // $item->begintime = strtotime($_POST['item']['begintime']);
                $item->endtime = strtotime($_POST['item']['endtime']);
                // $item->price = $_POST['item']['price'];
                $item->title = $_POST['item']['title'];
                $item->subtitle = $_POST['item']['subtitle'];
                // $item->cut_num = $_POST['item']['cut_num'];
                $item->desc = $_POST['item']['desc'];
                $item->rule = $_POST['item']['rule'];
                $item->save();
                Request::instance()->redirect('qwtkjba/items');
            }
        }
        $item1=ORM::factory('qwt_kjbitem')->where('id','=',$id)->find()->as_array();
        $_POST['item']=$result['item']=$item1;
        $result['title'] = '修改奖品';
        $result['action'] = 'edit';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/items_add')
            ->bind('bid', $bid)
            ->bind('item', $item1)
            ->bind('result',$result);
    }
    public function action_items_delete($id){
        $bid = $this->bid;
        $events=ORM::factory('qwt_kjbevent')->where('bid','=',$bid)->where('iid','=',$id)->count_all();
        if($events==0){
            $item=ORM::factory('qwt_kjbitem')->where('id','=',$id)->find()->delete();
        }
        Request::instance()->redirect('qwtkjba/items');
    }
    public function action_items_recover($id){
        $bid = $this->bid;
        $item=ORM::factory('qwt_kjbitem')->where('id','=',$id)->find();
        $item->status=0;
        $item->save();
        Request::instance()->redirect('qwtkjba/items');
    }
    public function action_items_terminate($id){
        $bid = $this->bid;
        $item=ORM::factory('qwt_kjbitem')->where('id','=',$id)->find();
        $item->status=3;
        $item->save();
        Request::instance()->redirect('qwtkjba/items');
    }
      //积分奖品管理
    public function action_tasks($action='', $id=0) {
        if ($action == 'add') return $this->action_tasks_add();
        if ($action == 'edit') return $this->action_tasks_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $tasks=ORM::factory('qwt_kjbtask')->where('bid', '=', $bid);
        $tasks = $tasks->reset(FALSE);
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            $tid=$_GET['tid'];
            $task=ORM::factory('qwt_kjbtask')->where('id','=',$tid)->find();
            $begin=$task->begintime;
            $task->endtime=$begin;
            $task->save();
        }
        if ($_GET['s']) {
            $tasks = $tasks->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $tasks = $tasks->where('name', 'like', $s);
            $tasks = $tasks->and_where_close();
        }
        // echo $bid."<br>";
        $result['countall'] = $tasks->count_all();
        // var_dump($result);
        // exit();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kjb/pages');

        $result['tasks'] = $tasks->order_by('endtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '任务管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/tasks')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }
    //奖品发送明细
    public function action_items_num($tid) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $items_num = ORM::factory('qwt_kjbsku')->where('bid', '=', $bid)->where('tid', '=', $tid);
        $items_num = $items_num->reset(FALSE);
        $result['countall'] = $items_num->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $result['countall'],
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kjb/pages');
        $result['tid_name'] = ORM::factory('qwt_kjbtask')->where('bid', '=', $bid)->where('id', '=', $tid)->find()->name;
        $result['items_num'] = $items_num->order_by('id', 'ASC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = $result['tid_name'].'的奖品发送情况';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/items_num')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }

    public function action_tasks_add() {

        $bid = $this->bid;
        echo $bid.'<br>';
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);

        if ($_POST['data']) {
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
            $task = ORM::factory('qwt_kjbtask');
            $task->values($_POST['data']);
            $task->bid = $bid;
            $past=ORM::factory('qwt_kjbtask')->where('bid','=',$bid)->where('endtime','>',time())->find();
            if($_POST['data']['begintime']>$_POST['data']['endtime']||$_POST['data']['endtime']<time()){
                $result['error'] = '时间设置不合理，请检查后再提交';
            }
            if(!$config['mgtpl']){
                $result['error'] = '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】';
            }
            if($past && $past->begintime!=$past->endtime ){
                $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
            }
            if (!$_POST['data']['name'] || !$_POST['data']['endtime']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $task->save();
                foreach ($_POST['goal'] as $k => $v) {
                    $sku = ORM::factory('qwt_kjbsku');
                    $sku->bid = $bid;
                    $sku->lv = $k;
                    $sku->tid = $task->id;
                    $sku->num = $v;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k];
                    $sku->text = $_POST['text'][$k];
                    $sku->save();
                }

                Request::instance()->redirect('qwtkjba/tasks');
            }
        }
        $items = ORM::factory('qwt_kjbitem')->where('bid','=',$bid)->find_all();
        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新任务';
        $result['text'] = '添加新任务';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/tasks_add')
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_tasks_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_kjbcfg')->getCfg($bid,1);
        $task = ORM::factory('qwt_kjbtask', $id);
        if (!$task || $task->bid != $bid) die('404 Not Found!');
        if ($_POST['data']) {
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
            $task->values($_POST['data']);
            $task->bid = $bid;
            if($_POST['data']['begintime']>$_POST['data']['endtime']||$_POST['data']['endtime']<time()){
                $result['error'] = '时间设置不合理，请检查后再提交';
            }
            if(!$config['mgtpl']){
                $result['error'] = '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】';
            }
            if($past && $past->begintime!=$past->endtime ){
                $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
            }
            if (!$_POST['data']['name'] || !$_POST['data']['endtime']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $task->save();
                // echo "<pre>";
                // var_dump($_POST['goal']);
                // echo "</pre>";

                foreach ($_POST['goal'] as $k => $v) {
                    $sku = $skus = ORM::factory('qwt_kjbsku')->where('bid', '=', $bid)->where('tid', '=', $id)->where('lv', '=', $k)->find();
                    $ordernum=ORM::factory('qwt_kjborder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                    $sku->bid = $bid;
                    $sku->tid = $task->id;
                    $sku->num = $v;
                    $sku->lv =$k;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k]+$ordernum;
                    $sku->text = $_POST['text'][$k];
                    $sku->save();
                    $form = $k+1;
                    //echo 'form'.$form.'<br>';
                }
                $mysql = ORM::factory('qwt_kjbsku')->where('bid', '=', $this->bid)->where('tid', '=', $id)->count_all();
                //echo 'mysql'.$mysql.'<br>';
                //exit;
                if($mysql>$form){
                    for ($i=0; $i <$mysql-$form ; $i++) {
                        $result = DB::query(Database::DELETE,"DELETE  from qwt_kjbskus  where bid=$bid and tid =$id and lv= $form+$i")->execute();
                    }
                }
                Request::instance()->redirect('qwtkjba/tasks');
            }
        }

        $_POST['data'] = $result['task'] = $task->as_array();
        $result['action'] = 'edit';
        $items = ORM::factory('qwt_kjbitem')->where('bid','=',$bid)->find_all();
        $skus = ORM::factory('qwt_kjbsku')->where('bid','=',$bid)->where('tid','=',$id)->find_all();
        $result['title'] = $this->template->title = '修改任务';
        $result['text'] = '保存';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/tasks_add')
            ->bind('skus', $skus)
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_kjb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        $this->config=ORM::factory('qwt_kjbcfg')->getCfg($this->bid,1);
        if($this->config['totle']!=date('Y-m-d',time())){
            if($this->config['totle']){
                $time_totle=strtotime($this->config['totle']);
            }else{
                $time_totle=0;
            }
            $daytype='%Y-%m-%d';
            $length=10;
            $days=DB::query(Database::SELECT,"SELECT   FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_kjbqrcodes` where bid=$this->bid and  jointime >= $time_totle UNION SELECT  FROM_UNIXTIME(`createdtime`, '$daytype') FROM `qwt_kjbevents` where bid=$this->bid and createdtime >= $time_totle UNION SELECT FROM_UNIXTIME(`lastupdate`, '$daytype') FROM `qwt_kjbcuts` where bid=$this->bid and  lastupdate >= $time_totle UNION SELECT   FROM_UNIXTIME(`pay_time`, '$daytype') FROM `qwt_kjborders` where bid=$this->bid and  pay_time >= $time_totle and order_state=1  ORDER BY `time` DESC ")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新参加活动的人数
                $fans=DB::query(Database::SELECT,"SELECT count(id) as fansnum from qwt_kjbqrcodes where bid=$this->bid  and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //发起的砍价数量
                $events=DB::query(Database::SELECT,"SELECT count(id) as eventnum from qwt_kjbevents where bid=$this->bid  and FROM_UNIXTIME(`createdtime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['eventnum']=$events[0]['eventnum'];
                //参加砍价的人数
                $cuts=DB::query(Database::SELECT,"SELECT count(id) as cutnum from qwt_kjbcuts where bid=$this->bid  and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['cutnum']=$cuts[0]['cutnum'];
                //完成的订单数量
                $ordernums=DB::query(Database::SELECT,"SELECT count(id) as ordernum from qwt_kjborders where bid=$this->bid and order_state=1 and FROM_UNIXTIME(`pay_time`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernum']=$ordernums[0]['ordernum'];
                //完成的订单金额
                $ordermoneys=DB::query(Database::SELECT,"SELECT SUM(pay_money) as ordermoney from qwt_kjborders where bid=$this->bid and order_state=1 and (FROM_UNIXTIME(`pay_time`, '$daytype')='$time')")->execute()->as_array();
                $newadd[$i]['ordermoney']=$ordermoneys[0]['ordermoney'];
            }
            foreach ($newadd as $key => $value) {
                $totle=ORM::factory('qwt_kjbtotle')->where('bid','=',$this->bid)->where('time_quantum','=',$value['time'])->find();
                $totle->bid=$this->bid;
                $totle->fansnum=$value['fansnum'];
                $totle->eventnum=$value['eventnum'];
                $totle->cutnum=$value['cutnum'];
                $totle->ordernum=$value['ordernum'];
                $totle->ordermoney=$value['ordermoney'];
                $totle->timestamp=strtotime($value['time']);
                $totle->time_quantum=$value['time'];
                $totle->save();

            }
            $ok=ORM::factory('qwt_kjbcfg')->setCfg($this->bid,'totle',date('Y-m-d',time()));
            $this->config=ORM::factory('qwt_kjbcfg')->getCfg($this->bid,1);
        }else{
            $time_today=strtotime(date('Y-m-d',time()));
            $fansnum=ORM::factory('qwt_kjbqrcode')->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->where('old','=',0)->count_all();
            $eventnum=ORM::factory('qwt_kjbevent')->where('bid','=',$this->bid)->where('createdtime','>=',$time_today)->count_all();
            $cutnum=ORM::factory('qwt_kjbcut')->where('bid','=',$this->bid)->where('lastupdate','>=',$time_today)->count_all();
            $ordernum=ORM::factory('qwt_kjborder')->where('bid','=',$this->bid)->where('order_state','=',1)->where('pay_time','>=',$time_today)->count_all();
            $ordermoneys=DB::query(Database::SELECT,"SELECT SUM(pay_money) as ordermoney from qwt_kjborders where bid=$this->bid and order_state=1 and pay_time >= $time_today ")->execute()->as_array();
            $ordermoney=$ordermoneys[0]['ordermoney'];
            if($fnum>0||$tnum>0||$qnum>0||$onum>0){
                $totle=ORM::factory('qwt_kjbtotle')->where('bid','=',$this->bid)->where('time_quantum','=',date('Y-m-d',time()))->find();
                $totle->bid=$this->bid;
                $totle->time_quantum=date('Y-m-d',time());
                $totle->timestamp=strtotime(date('Y-m-d',time()));
                $totle->fansnum=$fansnum;
                $totle->eventnum=$eventnum;
                $totle->cutnum=$cutnum;
                $totle->ordernum=$ordernum;
                $totle->ordermoney=$ordermoney;
                $totle->save();
            }
        }
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
                $fansnum=DB::query(Database::SELECT,"SELECT count(id) as fansnum from qwt_kjbqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fansnum[0]['fansnum'];

                //
                $eventnum=DB::query(Database::SELECT,"SELECT count(id) as eventnum from qwt_kjbevents where bid=$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')>='$begin' and FROM_UNIXTIME(`createdtime`, '$daytype')<='$over'")->execute()->as_array();
                $newadd[0]['eventnum']=$eventnum[0]['eventnum'];
                //
                $cutnum=DB::query(Database::SELECT,"SELECT count(id) as cutnum from qwt_kjbcuts where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['cutnum']=$cutnum[0]['cutnum'];
                //奖品兑换数量
                $ordernum= DB::query(Database::SELECT,"select count(id) as ordernum FROM `qwt_kjborders` where bid =$this->bid and order_state=1 and FROM_UNIXTIME(`pay_time`, '$daytype')>='$begin' and FROM_UNIXTIME(`pay_time`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['ordernum']=$ordernum[0]['ordernum'];

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
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_kjbtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qwt/admin/kjb/pages');
            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_kjbtotles` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"SELECT sum(fansnum) as fansnum from qwt_kjbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $eventnum=DB::query(Database::SELECT,"SELECT sum(eventnum) as eventnum from qwt_kjbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['eventnum']=$eventnum[0]['eventnum'];
                //参加活动人数
                $cutnum=DB::query(Database::SELECT,"SELECT sum(cutnum) as cutnum from qwt_kjbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['cutnum']=$cutnum[0]['cutnum'];
                //奖品兑换数量
                $ordernum= DB::query(Database::SELECT,"SELECT sum(ordernum) as ordernum FROM `qwt_kjbtotles` where bid =$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernum']=$ordernum[0]['ordernum'];
                //奖品兑换数量
                $ordermoney= DB::query(Database::SELECT,"SELECT sum(ordermoney) as ordermoney FROM `qwt_kjbtotles` where bid =$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordermoney']=$ordernums[0]['ordermoney'];
            }
            // echo '<pre>';
            // var_dump($newadd);
            // exit();
        }

        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '%Y-%m-%d')as time FROM `qwt_kjbtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kjb/stats_totle')
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
    public function action_rsync_count() {
        $item = ORM::factory('qwt_kjbitem')->find_all();
        foreach ($item as $k => $v) {
            $pvall=ORM::factory('qwt_kjbevent')->select(array('SUM("PV")', 'pvall'))->where('bid','=',$v->bid)->where('iid','=',$v->id)->find()->pvall;
            $v->pv = $pvall;

            $bid=$v->bid;
            $iid=$v->id;
            $cutarray=DB::query(Database::SELECT,"SELECT COUNT(id) as cutcount from qwt_kjbcuts where eid IN (SELECT id from qwt_kjbevents where bid = $bid and iid= $iid)")->execute()->as_array();
            $cutcount=$cutarray[0]['cutcount'];
            $v->cutcount = $cutcount;

            $v->eventcount = ORM::factory('qwt_kjbevent')->where('bid','=',$v->bid)->where('iid','=',$v->id)->count_all();

            $v->save();
        }
    }
    public function action_rsync_paymoney() {
        $order = ORM::factory('qwt_kjborder')->where('bid','=',1143)->where('pay_money','=',0)->find_all();
        foreach ($order as $k => $v) {
            $pay_money = ORM::factory('qwt_kjbevent')->where('id','=',$v->eid)->find()->now_price;
            $v->pay_money = $pay_money;
            $v->save();
        }

    }
}
