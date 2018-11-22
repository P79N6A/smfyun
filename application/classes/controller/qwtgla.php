<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtgla extends Controller_Base {

    public static $file="";
    public static $tempname="";
    public $pagesize = 5;
    public $bid;
    public $name ;
    public $access_token;
    public $methodVersion='3.0.0';
    public $template = 'weixin/qwt/tpl/glatpl';
    public function before()
    {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        $this->access_token=ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->yzaccess_token;
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',6)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            if(Request::instance()->action == 'text'){
                $hasover = 1;
                @View::bind_global('hasover', $hasover);
            }
        }
    }
    public function after() {
        if ($this->bid) {
            // $todo['hasbuy'] = ORM::factory('qwt_buy')->where('bid', '=', $this->bid)->where('expiretime', '>=', time())->find_all();
            $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->find_all();
            $this->template->todo = $todo;
        }
        @View::bind_global('bid', $this->bid);
        @View::bind_global('todo', $todo);
        parent::after();
    }
    public function action_text(){
        $this->template->product_bid=$this->bid;
        if(isset($_POST['text'])){
            $cfg = ORM::factory('qwt_glcfg');
            $text = $_POST['text'];
            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($this->bid, $k, $v);
                $result['ok2'] += $ok;
            }
            if($result['ok2']) $success='text';
        }
        $config=ORM::factory("qwt_glcfg")->getCfg($this->bid,1);
        $this->template->title = '盖楼设置';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/text')
            ->bind('success', $success)
            ->bind('config', $config);
    }
    public function action_item(){
        $this->template->product_bid=$this->bid;
        $config['bid']=$this->bid;
        if(isset($_GET['delete'])){
            $result = ORM::factory('qwt_glitem')->where('bid','=',$this->bid)->where('id','=',$_GET['delete'])->find();
            $result->delete();
            $result = ORM::factory('qwt_glfloor')->where('bid','=',$this->bid)->where('iid','=',$_GET['delete']);
            $result->delete_all();
            Request::instance()->redirect('qwtgla/item');
        }
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $num = ORM::factory('qwt_glitem')->where('bid','=',$this->bid)->count_all();
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wfb/pages');

        $item = ORM::factory('qwt_glitem')->where('bid','=',$this->bid)->limit($this->pagesize)->order_by('lastupdate','DESC')->offset($offset)->find_all()->as_array();
        $this->template->title = '奖品设置';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/item')
            ->bind('item', $item)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }
    public function action_order2s(){
        //$view=View::factory("user/config/gl/order");
        $this->template->product_bid=$this->bid;
        $config['bid']=$this->bid;
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $num = ORM::factory('qwt_glorder')->where('bid','=',$this->bid)->count_all();
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wfb/pages');

        $item = ORM::factory('qwt_glorder')->where('bid','=',$this->bid)->limit($this->pagesize)->order_by('lastupdate','DESC')->offset($offset)->find_all()->as_array();
        $this->template->title = '中奖纪录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/orders')
            ->bind('item', $item)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_glcfg')->getCfg($bid,1);
         //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');

            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[7], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 9) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[7]);
                    $shipcode = iconv('gbk', 'utf-8', $data[8]);
                } else {
                    $shiptype = $data[7];
                    $shipcode = $data[8];
                }

                $order = ORM::factory('qwt_glorder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
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
            $edit_order = ORM::factory('qwt_glorder')->where('id','=',$_POST['edit_oid'])->find();
            $edit_order->receive_name = $_POST['edit']['receive_name'];
            $edit_order->tel = $_POST['edit']['tel'];
            $edit_order->address = $_POST['edit']['address'];
            $edit_order->save();
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('qwt_glorder')->where('id', '=', $oid)->find();
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
            $order = ORM::factory('qwt_glorder')->where('id', '=', $id)->find();
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
                    $shipmsg = "%s，您的积分兑换奖品已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $res = $wx->sendCustomMessage($msg);
                }
            }
        }
        $result['state'] = 0;
        if ($action == 'done') {
            $result['state'] = 1;
        }
        $order = ORM::factory('qwt_glorder')->where('bid', '=', $bid)->where('state', '=', $result['state']);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $order = $order->and_where_open();
                $s = '%'.trim($_GET['s'].'%');
                $order = $order->where('nickname', 'like', $s)->or_where('receive_name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            $order = $order->and_where_close();
        }
        if ($_GET['export']=='csv') {
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $title = array('id', '姓名', '奖品名称','发送状态（1为成功）','收货人','联系电话','收货地址','物流公司','物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $order = $order->order_by('lastupdate', 'DESC')->limit(1000)->find_all();
            foreach ($order as $o) {
                $array = array($o->id, $o->nickname, $o->name, $o->status==1?'发送成功':$o->status,$o->receive_name,$o->tel,$o->address);
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
        ))->render('weixin/qwt/admin/wfb/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '中奖记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_see($bid){
        $mem = Cache::instance('memcache');
        $lou_key = "weixin4:$bid:gl_count";
        $lou_count = (int)$mem->get($lou_key);
        echo $lou_count;
    }
    public function action_delete(){
        //$view=View::factory("user/config/gl/delete");
        $this->template->product_bid=$this->bid;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        if(isset($_POST['delete'])){
            //$mem = Cache::instance('memcache');
            $lou_key = "qwt_gl:{$this->bid}:$this->bid:gl_count";
            $result = $m->set($lou_key, 0, 0);
            if($result){
               $success='delete';
            }
        }
        //$mem = Cache::instance('memcache');
        //echo $this->bid.'<br>';

        $lou_key = "qwt_gl:{$this->bid}:$this->bid:gl_count";
        //echo $lou_key.'<br>';
        $lou_count = (int)$m->get($lou_key);
        $this->template->title = '清空楼层';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/delete')
            ->bind('lou_count', $lou_count)
            ->bind('success', $success)
            ->bind('config', $config);
    }
    public function action_delete_floor_all(){
        $empty = ORM::factory('qwt_glfloor')->where('bid', '=', $this->bid);
        $empty->delete_all();
        Request::instance()->redirect('qwtgla/floor');
    }
    public function action_floor(){
        //$view=View::factory("user/config/gl/floor");
        $this->template->product_bid=$this->bid;
        $config['bid']=$this->bid;
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        if(isset($_POST['delete'])){
            $result = ORM::factory('qwt_glfloor')->where('id','=',$_POST['delete'])->find();
            $result->delete();
            if($result) $success='delete';
        }
        if (isset($_POST['floor'])) {
            $floor=$_POST['floor'];
            if ($floor['type']==1) {
                $floors=ORM::factory('qwt_glfloor')->where('bid','=',$this->bid)->where('floor','=',$floor['floor'])->find();//已经存在的执行覆盖
                $floors->bid=$this->bid;
                $floors->floor=$floor['floor'];
                $floors->iid=$floor['iid'];
                $floors->lastupdate=time();
                $result=$floors->save();
                if($result) $success='floor';
            }
            if ($floor['type']==2) {
                $n=$floor['num'];
                $t=$floor['tail'];
                for ($i=1; $i <= $n; $i++) {
                    $floors=ORM::factory('qwt_glfloor')->where('bid','=',$this->bid)->where('floor','=',$t)->find();//已经存在的不改变 进行顺延
                    if(!$floors->id){
                        $floors->bid=$this->bid;
                        $floors->floor=$t;
                        $floors->iid=$floor['iid2'];
                        $floors->lastupdate=time();
                        $result=$floors->save();
                    }
                    $t=$t+10;
                }
                if($result) $success='floor';
            }
        }
        $num = ORM::factory('qwt_glfloor')->where('bid','=',$this->bid)->count_all();
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wfb/pages');
        $floor = ORM::factory('qwt_glfloor')->where('bid','=',$this->bid)->limit($this->pagesize)->order_by('floor','DESC')->offset($offset)->find_all()->as_array();
        $item = ORM::factory('qwt_glitem')->where('bid','=',$this->bid)->order_by('lastupdate','DESC')->find_all()->as_array();
        $this->template->title = '中奖楼层设置';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/floor')
            ->bind('item', $item)
            ->bind('pages', $pages)
            // ->bind('scripts', array("Resource/js/rebuy.js"))
            ->bind('floor', $floor)
            ->bind('success', $success)
            ->bind('config', $config);
    }
    public function action_item_add(){
        $view=View::factory("weixin/qwt/admin/gl/item_add");
        $this->template->product_bid=$this->bid;
        $config['bid']=$this->bid;

        if(!$this->access_token){//未授权
            $coupon=array();
            $gift=array();
            // die('请在【绑定有赞】点击【一键授权有赞】');
        }else{
            require Kohana::find_file('vendor', 'youzan/YZTokenClient');
            $client = new YZTokenClient($this->access_token);

            $method1 = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields' =>'title,value,stock,fetch_url,group_id'
            ];
            $coupon = $client->post($method1,$this->methodVersion,$params);
            // var_dump($coupon);
            $method1 = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields' =>'present_id,title'
            ];
            $gift=$client->post($method1,$this->methodVersion,$params);
            // var_dump($coupon);
            // echo '2';
        }
        // var_dump($coupon);
        // var_dump($gift);

        if(isset($_POST['item'])){
            $item = $_POST['item'];
            $items = ORM::factory('qwt_glitem');

            if($_POST['type']==5){//优惠券
                if(!$this->access_token){//未授权
                    die('请在【绑定有赞】点击【一键授权有赞】');
                }else{
                    $method1 = 'youzan.ump.coupons.unfinished.search';
                    $params = [
                        'fields' =>'title,value,stock,fetch_url,group_id'
                    ];
                    $coupon=$client->post($method1,$this->methodVersion,$params);
                }

                foreach ($coupon['response']['coupons'] as $coupon) {
                    if($coupon['group_id']==$item['groupid']){
                        $items->bid=$this->bid;
                        $items->name=$coupon['title'];
                        $items->stock=$coupon['stock'];
                       // $items->code=$coupon['fetch_url'];
                        $items->code=$item['groupid'];
                        $items->type=5;
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            if($_POST['type']==4){//红包
                $items->bid=$this->bid;
                $items->name=$item['title'];
                $items->code=$item['code'];
                $items->type=4;
                $items->lastupdate=time();
                $items->word=$item['word'];
                $items->save();
            }
            if($_POST['type']==0){//实物
                if($_FILES['pic1']['error'] == 0){
                    // if ($_FILES['pic1']['size'] > 1024*400) {
                    //     $result['error'] = '奖品图片大小不能超过 400K';
                    // } else {
                        
                    // }
                    $items->pic=file_get_contents($_FILES['pic1']['tmp_name']);
                }
                $items->bid=$this->bid;
                $items->name=$item['shiwuname'];
                $items->need_money=$item['need_money'];
                $items->type=0;
                $items->lastupdate=time();
                $items->word=$item['word'];
                $items->save();
            }
            if($_POST['type']==6){//赠品
                if(!$this->access_token){//未授权
                    die('请在【绑定有赞】点击【一键授权有赞】');
                }else{
                    $method1 = 'youzan.ump.presents.ongoing.all';
                    $params = [
                        'fields' =>'present_id,title'
                    ];
                    $gift=$client->post($method1,$this->methodVersion,$params);
                }
                foreach ($gift['response']['presents'] as $gift) {
                    if($gift['present_id']==$item['presentid']){
                        $items->bid=$this->bid;
                        $items->name=$gift['title'];

                        $items->code=$gift['present_id'];
                        $items->type=6;
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            Request::instance()->redirect('qwtgla/item');
        }
        $result['action'] = 'add';
        $this->template->title = '添加奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/item_add')
            ->bind('bid', $this->bid)
            ->bind('result', $result)
            ->bind('coupon', $coupon)
            ->bind('gift', $gift)
            ->bind('config', $config);
    }
    public function action_item_edit($bid='',$iid=''){
        $view=View::factory("weixin/qwt/admin/gl/item_add");
        $this->template->product_bid=$this->bid;
        $config['bid']=$this->bid;

        if(!$this->access_token){//未授权
            $coupon=array();
            $gift=array();
            // require_once Kohana::find_file("vendor/youzan","YZSignClient");

            // $appId = ORM::factory('config')->where('bid','=',$this->bid)->find()->youzan_appid;
            // $appSecret = ORM::factory('config')->where('bid','=',$this->bid)->find()->youzan_appsecret;
            // $client = new YZSignClient($appId, $appSecret);

            // $method1 = 'youzan.ump.coupons.unfinished.search';
            // $params = [
            //     'fields' =>'title,value,stock,fetch_url,group_id'
            // ];

            // $coupon=$client->post($method1,$this->methodVersion,$params);

            // $method1 = 'youzan.ump.presents.ongoing.all';
            // $params = [
            //     'fields' =>'present_id,title'
            // ];
            // $gift=$client->post($method1,$this->methodVersion,$params);

        }else{
            require Kohana::find_file('vendor', 'youzan/YZTokenClient');
            $client = new YZTokenClient($this->access_token);

            $method1 = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields' =>'title,value,stock,fetch_url,group_id'
            ];
            $coupon = $client->post($method1,$this->methodVersion,$params);

            $method1 = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields' =>'present_id,title'
            ];
            $gift=$client->post($method1,$this->methodVersion,$params);
        }
        if(isset($_GET['delete'])){
            $result = ORM::factory('qwt_glitem')->where('bid','=',$this->bid)->where('id','=',$_GET['delete'])->find();
            $result->delete();
            $result = ORM::factory('qwt_glfloor')->where('bid','=',$this->bid)->where('iid','=',$_GET['delete']);
            $result->delete_all();
            Request::instance()->redirect('qwtgla/item');
        }
        if(isset($_POST['item'])){
            $item = $_POST['item'];
            $items = ORM::factory('qwt_glitem')->where('id','=',$item['id'])->find();

            if($_POST['type']==5){//优惠券
                if(!$this->access_token){//未授权
                    die('请在【绑定有赞】点击【一键授权有赞】');
                    // $method1 = 'youzan.ump.coupons.unfinished.search';
                    // $params = [
                    //     'fields' =>'title,value,stock,fetch_url,group_id'
                    // ];
                    // $coupon=$client->post($method1,$this->methodVersion,$params);
                }else{
                    $method1 = 'youzan.ump.coupons.unfinished.search';
                    $params = [
                        'fields' =>'title,value,stock,fetch_url,group_id'
                    ];
                    $coupon=$client->post($method1,$this->methodVersion,$params);
                }
                foreach ($coupon['response']['coupons'] as $coupon) {
                    if($coupon['group_id']==$item['groupid']){
                        $items->bid=$this->bid;
                        $items->name=$coupon['title'];
                        $items->stock=$coupon['stock'];
                        $items->code=$coupon['group_id'];
                        $items->type=$_POST['type'];
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            if($_POST['type']==4){//红包
                $items->bid=$this->bid;
                $items->name=$item['title'];
                $items->code=$item['code'];
                $items->type=$_POST['type'];
                $items->lastupdate=time();
                $items->word=$item['word'];
                $items->save();
            }
            if($_POST['type']==0){//实物
                // var_dump($_FILES);
                // exit();
                if($_FILES['pic1']['error'] == 0){
                    // if ($_FILES['pic1']['size'] > 1024*400) {
                    //     $result['error'] = '奖品图片大小不能超过 400K';
                    // } else {
                        
                    // }
                    $items->pic=file_get_contents($_FILES['pic1']['tmp_name']);
                }
                $items->bid=$this->bid;
                $items->name=$item['shiwuname'];
                $items->need_money=$item['need_money'];
                $items->type=0;
                $items->lastupdate=time();
                $items->word=$item['word'];
                $items->save();
            }
            if($_POST['type']==6){//赠品
                if(!$this->access_token){//未授权
                    die('请在【绑定有赞】点击【一键授权有赞】');
                    // $method1 = 'youzan.ump.presents.ongoing.all';
                    // $params = [
                    //     'fields' =>'present_id,title'
                    // ];
                    // $gift=$client->post($method1,$this->methodVersion,$params);
                }else{
                    $method1 = 'youzan.ump.presents.ongoing.all';
                    $params = [
                        'fields' =>'present_id,title'
                    ];
                    $gift=$client->post($method1,$this->methodVersion,$params);
                }
                foreach ($gift['response']['presents'] as $gift) {
                    if($gift['present_id']==$item['presentid']){
                        $items->bid=$this->bid;
                        $items->name=$gift['title'];

                        $items->code=$gift['present_id'];
                        $items->type=$_POST['type'];
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            Request::instance()->redirect('qwtgla/item');
        }
        $result['action'] = 'edit';
        $item = ORM::factory('qwt_glitem')->where('bid','=',$bid)->where('id','=',$iid)->find()->as_array();
        $this->template->title = '修改奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/gl/item_add')
            ->bind('result', $result)
            ->bind('item', $item)
            ->bind('coupon', $coupon)
            ->bind('gift', $gift)
            ->bind('config', $config);
    }
    private function Upload($name){
        $dir=Kohana::include_paths()[0].'vendor/weixin/glcert/';
        $flag=true;
       //echo $_FILES['filecert']['error']."fileerror";
        // if($name=="shenmafuyun")
        //    {$name="shenmafuyug-chen";
        //     echo $name;

        //            }
        if($_FILES['filecert']['error']>0)
        {
           $flag=false;
        }
        if(is_uploaded_file($_FILES['filecert']['tmp_name']))
        {
            if(!is_dir($dir.$name)){
                $new=mkdir($dir.$name);
                //echo $name;
                @chmod($dir.$name, 0777);//权限设置为0777
             }
            if(move_uploaded_file($_FILES['filecert']['tmp_name'], $dir.$name.'/1.zip'))
            {
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
        //echo $flag;
        $this->chmodr($dir.$name, 0777);
        return $flag;
    }



    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_gl$type";
        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');
        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
   function chmodr($path, $filemode) {//更改文件夹下文件的权限
        if (!is_dir($path))
        return @chmod($path, $filemode);
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
        if($file != '.' && $file != '..') {
        $fullpath = $path.'/'.$file;
        if(is_link($fullpath))
        return FALSE;
        elseif(!is_dir($fullpath) && !@chmod($fullpath, $filemode))
        return FALSE;
        elseif(!$this->chmodr($fullpath, $filemode))
        return FALSE;
        }
        }
        closedir($dh);
        if(@chmod($path, $filemode))
        return TRUE;
        else
        return FALSE;
     }
}
?>
