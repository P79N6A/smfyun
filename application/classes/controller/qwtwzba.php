<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_qwtwzba extends Controller_Base {
    public $template = 'weixin/qwt/tpl/wzbatpl';
    public $pagesize = 20;
    public $yzaccess_token;
    public $config;
    public $bid;
    public $we;
    public $appId = 'wxd0b3a6ff48335255';
    public $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'zhibo';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',11)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            // die('未购买相关产品或产品已过期');
            if(Request::instance()->action == 'information'){
                // echo "<script>alert('未购买相关产品或产品已过期')</script>";
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
    public function action_index() {
        $this->action_login();
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $user = ORM::factory('qwt_login')->where('id','=',$bid)->find();
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
        //播放时间可选
        if ($_POST['time']) {
            $cfg = ORM::factory('qwt_wzbcfg');

            foreach ($_POST['time'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
            }

            //重新读取配置
            $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        }
        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('qwt_wzbcfg');
            $qrfile = DOCROOT."wzb/tmp/tpl.$bid.jpg";

            //默认头像
            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."wzb/tmp/head.$bid.jpg";
                    $cfg->setCfg($bid, 'wzbtplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
                    // @unlink($default_head_file);
                    // move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }
            if ($_FILES['pic3']['error'] == 0) {
                if ($_FILES['pic3']['size'] > 1024*100) {
                    $result['err3'] = '分享图标文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."wzb/tmp/share.$bid.jpg";
                    $cfg->setCfg($bid, 'wzbtplshare', '', file_get_contents($_FILES['pic3']['tmp_name']));
                    // @unlink($default_head_file);
                    // move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }
            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok3'] += $ok;
            }
            //重新读取配置
            $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('qwt_wzbcfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_wzbcfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtplhead')->find()->id;
        $result['tplshare'] = ORM::factory('qwt_wzbcfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtplshare')->find()->id;
        // exit;
        $this->template->title = '首页';
        //$this->template->content = View::factory('weixin/wzb/admin/home')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/home')
            ->bind('result', $result)
            ->bind('user',$user)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('user',$user)
            ->bind('actives',$actives)
            ->bind('config', $config);
    }
    public function action_marketing() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);

        if ($_POST['text']) {
            $cfg = ORM::factory('qwt_wzbcfg');
            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok3'] += $ok;
            }
            //重新读取配置
            $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        }

        $yzaccess_token = ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if($yzaccess_token){
            require_once Kohana::find_file("vendor/youzan","YZTokenClient");
            $client = new YZTokenClient($yzaccess_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $methodVersion = '3.0.0';
            $params = [
                'fields' => 'title,group_id',
            ];
            $coupon = $client->post($method, $methodVersion, $params, $files);
        }
        $this->template->title = '营销模块';
        //$this->template->content = View::factory('weixin/wzb/admin/marketing')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/marketing')
            ->bind('result', $result)
            ->bind('coupon',$coupon)
            ->bind('config', $config);
    }
    public function action_lottery() {
        $bid = $this->bid;
        if($_POST['lottery']){
            $cfg = ORM::factory('qwt_wzbcfg');
            foreach ($_POST['lottery'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }
            if($_POST['lottery']['switch']==1){
                if(!$_POST['data1']['other']||!$_POST['data1']['num']||!$_POST['data1']['other']||!$_POST['data1']['num']||!$_POST['data1']['other']||!$_POST['data1']['num']||!$_POST['data1']['other']||!$_POST['data1']['num']){
                    $result['err'] = '幸运抽奖转盘功能开启时，务必将奖品资料添加完整！';
                }else{
                    if ($_POST['data2']['type']==5) {
                        $_POST['data2']['num']=0;
                    }
                    if ($_POST['data3']['type']==5) {
                        $_POST['data3']['num']=0;
                    }
                    if ($_POST['data4']['type']==5) {
                        $_POST['data4']['num']=0;
                    }
                    // for ($i=1; $i <=4 ; $i++) {
                    //     $data.$i = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',$i)->find();
                    //     $data.$i->bid = $bid;
                    //     $data.$i->item = $i;
                    //     $data.$i->type = $_POST['data'.$i]['type'];
                    //     $data.$i->other = $_POST['data'.$i]['other'];
                    //     $data.$i->num = $_POST['data'.$i]['num'];
                    //     $data.$i->save();
                    // }
                        $data1 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',1)->find();
                        $data1->bid = $bid;
                        $data1->item = 1;
                        $data1->type = $_POST['data1']['type'];
                        $data1->other = $_POST['data1']['other'];
                        $data1->num = $_POST['data1']['num'];
                        if ($_FILES['pic1']['error'] == 0) {
                            if ($_FILES['pic1']['size'] > 1024*100) {
                                $result['err'] = '奖品1图片不能超过 100K';
                            } else {
                                $data1->pic = file_get_contents($_FILES['pic1']['tmp_name']);
                            }
                        }
                        $data1->save();
                        $data2 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',2)->find();
                        $data2->bid = $bid;
                        $data2->item = 2;
                        $data2->type = $_POST['data2']['type'];
                        $data2->other = $_POST['data2']['other'];
                        $data2->num = $_POST['data2']['num'];
                        if ($_FILES['pic2']['error'] == 0) {
                            if ($_FILES['pic2']['size'] > 1024*100) {
                                $result['err'] = '奖品2图片不能超过 100K';
                            } else {
                                $data2->pic = file_get_contents($_FILES['pic2']['tmp_name']);
                            }
                        }
                        $data2->save();
                        $data3 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',3)->find();
                        $data3->bid = $bid;
                        $data3->item = 3;
                        $data3->type = $_POST['data3']['type'];
                        $data3->other = $_POST['data3']['other'];
                        $data3->num = $_POST['data3']['num'];
                        if ($_FILES['pic3']['error'] == 0) {
                            if ($_FILES['pic3']['size'] > 1024*100) {
                                $result['err'] = '奖品3图片不能超过 100K';
                            } else {
                                $data3->pic = file_get_contents($_FILES['pic3']['tmp_name']);
                            }
                        }
                        $data3->save();
                        $data4 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',4)->find();
                        $data4->bid = $bid;
                        $data4->item = 4;
                        $data4->type = $_POST['data4']['type'];
                        $data4->other = $_POST['data4']['other'];
                        $data4->num = $_POST['data4']['num'];
                        if ($_FILES['pic4']['error'] == 0) {
                            if ($_FILES['pic4']['size'] > 1024*100) {
                                $result['err'] = '奖品4图片不能超过 100K';
                            } else {
                                $data4->pic = file_get_contents($_FILES['pic4']['tmp_name']);
                            }
                        }
                        $data4->save();
                }
            }
        }
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $data1 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',1)->find();
        $data2 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',2)->find();
        $data3 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',3)->find();
        $data4 = ORM::factory('qwt_wzblottery')->where('bid','=',$bid)->where('item','=',4)->find();
        $yzaccess_token = ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if($yzaccess_token){
            require_once Kohana::find_file("vendor/youzan","YZTokenClient");
            $client = new YZTokenClient($yzaccess_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $methodVersion = '3.0.0';
            $params = [
                'fields' => 'title,group_id',
            ];
            $coupons = $client->post($method, $methodVersion, $params, $files);

            $method = 'youzan.ump.presents.ongoing.all';
            $methodVersion = '3.0.0';
            $params = [
                'fields' => 'title,present_id',
            ];
            $gifts = $client->post($method, $methodVersion, $params, $files);
        }

        $this->template->title = '幸运抽奖转盘';
        //$this->template->content = View::factory('weixin/wzb/admin/lottery')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/lottery')
                       ->bind('config',$config)
                       ->bind('coupons',$coupons)
                       ->bind('result',$result)
                       ->bind('gifts',$gifts)
                       ->bind('data1',$data1)
                       ->bind('data2',$data2)
                       ->bind('data3',$data3)
                       ->bind('data4',$data4);
    }
    public function action_lottery_history() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $sweep = ORM::factory('qwt_wzbsweepstake')->where('bid','=',$bid)->where('state','=',1);
        $sweep = $sweep->reset(FALSE);
        $result['countall'] = $countall = $sweep->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');
        $result['sort'] = 'id';
        if ($result['sort']) $sweep = $sweep->order_by($result['sort'], 'DESC');
        $result['sweep'] = $sweep->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '幸运抽奖转盘';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/lottery_history')
                    ->bind('pages', $pages)
                    ->bind('result', $result)
                    ->bind('config', $config);
    }
    public function action_download() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $this->template->title = '安卓端APK下载';
        //$this->template->content = View::factory('weixin/wzb/admin/download')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/download');
    }
    public function action_download_ios() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $this->template->title = 'IOS端应用下载';
        //$this->template->content = View::factory('weixin/wzb/admin/download_ios')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/download_ios');
    }

    public function action_get_qrcode(){
        $sku_price['100GB'] = 60;
        $sku_price['500GB'] = 275;
        $sku_price['1TB'] = 542.72;
        $sku_price['5TB'] = 2611.2;
        $sku_price['10TB'] = 5017.6;
        $sku_price['50TB'] = 24064;
        $sku_price['100TB'] = 46080;
        if($_POST['data']){
            require Kohana::find_file("vendor/kdt","KdtApiClient");
            switch ($_POST['type']) {
                case 'month':
                    $product_name = '包月：神码云直播-营销应用平台';
                    $price = 500;
                    break;
                case 'year':
                    $product_name = '包年：神码云直播-营销应用平台';
                    $price = 0.01;
                    break;
                case 'stream':
                    $product_name = '神码云直播流量'.$_POST['stream'].'-营销应用平台';
                    $price = $sku_price[$_POST['stream']];
                    break;
            }
            $appId = 'c27bdd1e37cd8300fb';
            $appSecret = '3e7d8db9463b1e2fd92083418677c638';
            $client = new KdtApiClient($appId, $appSecret);

            $method = 'kdt.pay.qrcode.createQrCode';
            $params = [
                'qr_name' =>$product_name,

                'qr_price' => $price*100,
                //'qr_price' => 1,
                'qr_type' => 'QR_TYPE_DYNAMIC',
                // 'qr_source'=>$_POST['type'].'.'.$_POST['stream'],//类型和流量拼接  给了跟不给  获取不到  没有给的意义
            ];
            $test=$client->post($method, $params);
            header('Content-type: image/jpg');
            // echo "<img src='".$test['response']['qr_code']."'>";
            // echo $test['response']['qr_id'];

            $data = array('imgurl' => "<img src='".$test['response']['qr_code']."'>",'imgid' =>$test['response']['qr_id'],'url'=>$test['response']['qr_url']);
            echo json_encode($data);
            exit;
        }
    }
    public function action_notify(){
        $bid = $this->bid;
        $sku_data['100GB'] = 100;
        $sku_data['500GB'] = 500;
        $sku_data['1TB'] = 1024;
        $sku_data['5TB'] = 5120;
        $sku_data['10TB'] = 10240;
        $sku_data['50TB'] = 51200;
        $sku_data['100TB'] = 102400;
        if($_POST['qrid']){
            require_once Kohana::find_file("vendor/kdt","KdtApiClient");

            $appId = 'c27bdd1e37cd8300fb';
            $appSecret = '3e7d8db9463b1e2fd92083418677c638';
            $client = new KdtApiClient($appId, $appSecret);

            $method1 = 'kdt.trades.qr.get';
            $params = [
                'status' =>'TRADE_RECEIVED'
            ];

            $resultarr=$client->post($method1,$params);
            $qrarr=$resultarr["response"]["qr_trades"];
            $flag=false;
            for($i=0;$qrarr[$i];$i++){
                if($qrarr[$i]['qr_id']==$_POST['qrid']){
                    $type = explode('.', $qrarr[$i]['qr_source'])[0];
                    $stream_data = $_POST['stream_data'];
                    $order = ORM::factory('qwt_wzborder');
                    $order->bid = $this->bid;
                    $order->tid = 'E'.date('YmdHis');
                    $order->time = time();
                    $order->type = $_POST['type'];
                    $order->title = $qrarr[$i]['qr_name'];
                    $order->price = $qrarr[$i]['qr_price'];//元 为单位
                    $order->save();
                    switch ($_POST['type']) {
                        case 'month':
                            $shop = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',11)->find();
                            if($shop->expiretime<time()){
                                $shop->expiretime = strtotime('+1month');
                            }else{
                                $shop->expiretime = strtotime('$shop->expiretime +1month');
                            }
                            $flag = '成功续费一个月！';
                            break;
                        case 'year':
                            $shop = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',11)->find();
                            if($shop->expiretime<time()){
                                $shop->expiretime = strtotime('+1year');
                            }else{
                                $shop->expiretime = strtotime('$shop->expiretime +1year');
                            }
                            $flag = '成功续费有效期一年！';
                            break;
                        case 'stream':
                            $shop = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                            $shop->stream_data = $shop->stream_data+$sku_data[$stream_data];
                            $flag = '充值'.$stream_data.'流量成功！';
                            break;
                    }
                    $shop->save();
                    echo $flag;
                }
            }
        }
        exit;
    }
    //会员中心-基本信息
    public function action_information() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $shop = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',11)->find();
        $this->template->title = '基本信息';
        $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM qwt_wzblives where bid=$bid ");
        $num = $sql->execute()->as_array();
        $use =  $num[0]['CT'];
        $all = ORM::factory('qwt_login')->where('id','=',$bid)->find()->stream_data;

        //$this->template->content = View::factory('weixin/wzb/admin/information')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/information')
                                    ->bind('shop',$shop)
                                    ->bind('result',$result)
                                    ->bind('use',$use)
                                    ->bind('all',$all);
    }
    //会员中心-流量中心
    public function action_flowcenter() {
        $bid = $this->bid;
        $this->template->title = '流量中心';
        $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM qwt_wzblives where bid=$bid ");
        $num = $sql->execute()->as_array();
        $use =  $num[0]['CT'];
        $all = ORM::factory('qwt_login')->where('id','=',$bid)->find()->stream_data;
        //$this->template->content = View::factory('weixin/wzb/admin/flowcenter')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/flowcenter')->bind('use',$use)->bind('all',$all);
    }
    public function action_flowcenter_history() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $this->template->title = '流量中心';
        $lives = ORM::factory('qwt_wzblive')->where('bid','=',$bid);
        $lives = $lives->reset(FALSE);
        $result['countall'] = $countall = $lives->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');

        $result['lives'] = $lives->order_by('start_time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $all = ORM::factory('qwt_login')->where('id','=',$bid)->find()->stream_data;
        //$this->template->content = View::factory('weixin/wzb/admin/flowcenter_history')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/flowcenter_history')
                                ->bind('all',$all)
                                ->bind('result',$result)
                                ->bind('pages',$pages);
    }
    //会员中心-购买记录
    public function action_buymentrecord() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        $this->template->title = '购买记录';
        $orders = ORM::factory('qwt_wzborder')->where('bid','=',$bid);
        $orders = $orders->reset(FALSE);
        $result['countall'] = $countall = $orders->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');

        $result['orders'] = $orders->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        //$this->template->content = View::factory('weixin/wzb/admin/buymentrecord')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/buymentrecord')->bind('pages',$pages)->bind('result',$result);
    }
    //直播分析
    public function action_analyze($action='', $id=0) {
        $bid=$this->bid;

        $lives = ORM::factory('qwt_wzblive')->where('bid','=',$bid);
        $lives = $lives->reset(FALSE);
        $result['countall'] = $countall = $lives->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');

        if ($result['sort']) $lives = $lives->order_by('start_time', $result['sort']);
        $result['lives'] = $lives->order_by('start_time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '直播分析';
        //$this->template->content = View::factory('weixin/wzb/admin/analyze')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/analyze')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);

        $result['skus'] = ORM::factory('qwt_wzbsku')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '返还管理';
        //$this->template->content = View::factory('weixin/wzb/admin/skus')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('qwt_wzbsku');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('/qwtwzba/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        //$this->template->content = View::factory('weixin/wzb/admin/skus_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);

        $sku = ORM::factory('qwt_wzbsku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('qwtwzba/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('qwtwzba/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        //$this->template->content = View::factory('weixin/wzb/admin/skus_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_wzbqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('qwt_wzbqrcode')->where('bid', '=', $bid);
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
        if ($_GET['start_time']) {
            $result['start_time'] = $_GET['start_time'];
            $result['end_time'] = $_GET['end_time'];
            if($result['end_time']==0){
                $qrcode = $qrcode->where('uvtime', '>', $result['start_time']);
            }else{
                $qrcode = $qrcode->where('uvtime', '>', $result['start_time'])->where('uvtime', '<', $_GET['end_time']);
            }

        }
        if ($_GET['fopenid']) {
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qwt_wzbqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('qwt_wzbqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_wzbqrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
        ))->render('weixin/qwt/admin/wzb/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        //$this->template->content = View::factory('weixin/wzb/admin/qrcodes')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    // //用户管理
    // public function action_logins($action='', $id=0) {
    //     if ($_SESSION['wzba']['admin'] < 1) Request::instance()->redirect('qwtwzba/home');

    //     if ($action == 'add') return $this->action_logins_add();
    //     if ($action == 'edit') return $this->action_logins_edit($id);

    //     $logins = ORM::factory('qwt_login')->where('flag','=',0)->where('fadmin','=',$this->bid);
    //     $logins = $logins->reset(FALSE);

    //    if ($_GET['s']) {
    //         $result['s'] = $_GET['s'];
    //         $s = '%'.trim($_GET['s'].'%');
    //         $logins = $logins->where('user', 'like', $s)->or_where('name', 'like', $s);
    //     }

    //     $result['countall'] = $countall = $logins->count_all();

    //     //分页
    //     $page = max($_GET['page'], 1);
    //     $offset = ($this->pagesize * ($page - 1));

    //     $pages = Pagination::factory(array(
    //         'total_items'   => $countall,
    //         'items_per_page'=> $this->pagesize,
    //     ))->render('weixin/wzb/admin/pages');

    //     $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

    //     $result['title'] = $this->template->title = '商户管理';
    //     $this->template->content = View::factory('weixin/wzb/admin/logins')
    //         ->bind('pages', $pages)
    //         ->bind('result', $result)
    //         ->bind('config', $config);
    // }

    // public function action_logins_add() {
    //     if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('qwtwzba/home');

    //     $bid = $this->bid;
    //     $biz=ORM::factory('qwt_login')->where('id','=',$bid)->find();
    //     if ($_POST['data']) {
    //         $login = ORM::factory('qwt_login');
    //         $login->values($_POST['data']);
    //         if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
    //         if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

    //         if (!$result['error']) {
    //             $login->pass = Text::random(NULL, 6);
    //             if ($_POST['pass']) $login->pass = $_POST['pass'];
    //             $login->save();
    //             Request::instance()->redirect('qwtwzba/logins');
    //         }
    //     }
    //     $admins=ORM::factory('qwt_login')->where('flag','=',1)->where('fadmin','=',$bid)->find_all();
    //     $result['action'] = 'add';

    //     $result['title'] = $this->template->title = '添加用户';
    //     $this->template->content = View::factory('weixin/wzb/admin/logins_add')
    //         ->bind('biz',$biz)
    //         ->bind('admins',$admins)
    //         ->bind('result', $result)
    //         ->bind('config', $config);
    // }

    // public function action_logins_edit($id) {
    //     if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('qwtwzba/home');

    //     $bid = $this->bid;

    //     $login = ORM::factory('qwt_login', $id);
    //     if (!$login) die('404 Not Found!');

    //     $cfg = ORM::factory('qwt_wzbcfg');

    //     if ($_GET['DELETE'] == 1) {
    //         //$login->delete();
    //         Request::instance()->redirect('qwtwzba/items');
    //     }

    //     if ($_POST['data']) {
    //         $login->values($_POST['data']);
    //         if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
    //         if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
    //             $result['error'] = '该登录名已经存在';

    //         if (!$result['error']) {
    //             if ($_POST['pass']) $login->pass = $_POST['pass'];
    //             $login->save();
    //             if ($_POST['data']['copyright']) {
    //                 $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
    //             }
    //             //appid 重置
    //             if ($_POST['data']['appid']) {
    //                 $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
    //             }

    //             Request::instance()->redirect('qwtwzba/logins');
    //         }
    //     }

    //     $cfgs = $cfg->getCfg($id, 1);
    //     $_POST['data'] = $result['login'] = $login->as_array();
    //     $_POST['data']['appid'] = $cfgs['appid'];
    //     $_POST['data']['copyright'] = $cfgs['copyright'];
    //     $result['action'] = 'edit';

    //     $result['title'] = $this->template->title = '修改用户';
    //     $this->template->content = View::factory('weixin/wzb/admin/logins_add')
    //         ->bind('result', $result)
    //         ->bind('config', $config);
    // }

    //管理员管理
    public function action_admins($action='', $id=0) {
        if ($_SESSION['wzba']['admin'] < 1) Request::instance()->redirect('qwtwzba/home');
        if ($action == 'add') return $this->action_admins_add();
        if ($action == 'edit') return $this->action_admins_edit($id);
        $logins = ORM::factory('qwt_login')->where('flag','=',1)->where('fadmin','=',$this->bid);
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
        ))->render('weixin/qwt/admin/wzb/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '管理员管理';
        //$this->template->content = View::factory('weixin/wzb/admin/admins')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/admins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_admins_add() {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->
        $bid = $this->bid;
        $biz=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if ($_POST['data']) {
            $login = ORM::factory('qwt_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('qwtwzba/admins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加管理员';
        //$this->template->content = View::factory('weixin/wzb/admin/admins_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/admins_add')
            ->bind('biz',$biz)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_admins_edit($id) {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('qwtwzba/home');
        $bid = $this->bid;
        $login = ORM::factory('qwt_login', $id);
        if (!$login) die('404 Not Found!');
        $cfg = ORM::factory('qwt_wzbcfg');
        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('qwtwzba/admins');
        }
        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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
                Request::instance()->redirect('qwtwzba/admins');
            }
        }
        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改管理员';
        //$this->template->content = View::factory('weixin/wzb/admin/admins_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/admins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    // public function action_login() {
    //     $this->template = 'weixin/wzb/tpl/login';
    //     $this->before();

    //     $agent = $this->GetAgent();
    //     Session::instance()->set("agent",$agent);

    //     if ($_POST['username'] && $_POST['password']) {
    //         $biz = ORM::factory('qwt_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

    //         if ($biz->id) {

    //             //判断账号是否到期
    //             if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
    //                 $this->template->error = '您的账号已到期';
    //             } else {

    //                 $_SESSION['wzba']['bid'] = $biz->id;
    //                 $_SESSION['wzba']['sid'] = $biz->shopid;
    //                 $_SESSION['wzba']['user'] = $_POST['username'];
    //                 $_SESSION['wzba']['admin'] = $biz->admin; //超管
    //                 $_SESSION['wzba']['config'] = ORM::factory('qwt_wzbcfg')->getCfg($biz->id);

    //                 $biz->lastlogin = time();
    //                 $biz->logins++;
    //                 $biz->save();
    //             }
    //         } else {
    //             $this->template->error = '宝塔镇河妖';
    //         }
    //     }

    //     if ($_SESSION['wzba']['bid']) {
    //         if (!$_GET['from']) $_GET['from'] = 'home';
    //         header('location:/qwtwzba/'.$_GET['from']);
    //         exit;
    //     }
    // }
    public function action_admin() {
        $this->template = 'weixin/wzb/tpl/admin';
        $this->before();
        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);
        if ($_POST['username'] && $_POST['password']) {
            $biz1 = ORM::factory('qwt_wzbadmin')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();
            if ($biz->id) {
                //判断账号是否到期
                $_SESSION['wzba']['aid'] = $biz1->id;
                $_SESSION['wzba']['user'] = $_POST['username'];
                $_SESSION['wzba']['admin'] = $biz1->admin; //超管
                $biz->lastlogin = time();
                $biz->logins++;
                $biz->save();
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }
        if ($_SESSION['wzba']['aid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/qwtwzba/'.$_GET['from']);
            exit;
        }
    }
    public function action_logout() {
        $_SESSION['wzba'] = null;
        header('location:/qwtwzba/home');
        exit;
    }
    //产品图片
    public function action_dbimages($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_wzb$type";

        $pic = ORM::factory($table, $id)->db_pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_wzb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    public function action_history_trades($lid)
    {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }
        $live = ORM::factory('qwt_wzblive')->where('id','=',$lid)->find();
        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }
        if($live->end_time){
            $trade = ORM::factory('qwt_wzbtrade')->where('bid', '=', $bid)->where('pay_time','>',date('Y-m-d H:i:s',$live->start_time))->where('pay_time','<',date('Y-m-d H:i:s',$live->end_time));
            if($_GET['now']==1){
                $trade = ORM::factory('qwt_wzbtrade')->where('bid', '=', $bid)->where('pay_time','>',date('Y-m-d H:i:s',$live->start_time));
            }
        }else{
            $trade = ORM::factory('qwt_wzbtrade')->where('bid', '=', $bid)->where('pay_time','>',date('Y-m-d H:i:s',$live->start_time));
        }

        $trade = $trade->reset(FALSE);

        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from qwt_wzbqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/qwt/admin/wzb/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        //$this->template->content = View::factory('weixin/wzb/admin/history_trades')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);
        $outmoney=ORM::factory('qwt_wzbscore')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from qwt_wzbqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/qwt/admin/wzb/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        //$this->template->content = View::factory('weixin/wzb/admin/history_withdrawals')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }

    public function action_num()
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","KdtApiOauthClient");
            $tradeid=ORM::factory('qwt_wzbtrade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('qwt_wzborder')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('qwt_wzbcfg')->getCfg($tempbid);
                    $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$tempbid)->find()->yzaccess_token;
                    if (!$this->yzaccess_token) //die("$bid not found.\n");
                    continue;

                    $client = new KdtApiOauthClient();
                    $method = 'kdt.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($this->yzaccess_token,$method, $params);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('qwt_wzborder')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
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
            $bid=ORM::factory('qwt_wzbtrade')->where('tid','=',$tid)->find()->bid;

            $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('qwt_wzbcfg')->getCfg($tempbid);

            if (!$this->yzaccess_token)  die("$bid not found.\n");


            $client = new KdtApiOauthClient();
            $method = 'kdt.trade.get';
            $params = array(
                'tid'=>$tid,
                //'fields' => 'tid,title,num_iid,orders,status,pay_time',
            );

             $result = $client->post($this->yzaccess_token,$method, $params);
             echo "<pre>";
             var_dump($result);



    exit();
    }

    public function action_stats_goods()
    {
        //$goods=ORM::factory('qwt_wzborder')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `qwt_wzborders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_wzborders.* FROM `qwt_wzbtrades`,qwt_wzborders WHERE qwt_wzborders.tid=qwt_wzbtrades.tid and qwt_wzbtrades.status!='TRADE_CLOSED' and qwt_wzbtrades.status!='TRADE_CLOSED_BY_USER' and qwt_wzbtrades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_wzborders.* FROM `qwt_wzbtrades`,qwt_wzborders WHERE qwt_wzborders.tid=qwt_wzbtrades.tid and qwt_wzbtrades.status!='TRADE_CLOSED' and qwt_wzbtrades.status!='TRADE_CLOSED_BY_USER' and qwt_wzbtrades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_wzborders.* FROM `qwt_wzbtrades`,qwt_wzborders WHERE qwt_wzborders.tid=qwt_wzbtrades.tid and qwt_wzbtrades.status!='TRADE_CLOSED' and qwt_wzbtrades.status!='TRADE_CLOSED_BY_USER' and qwt_wzbtrades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_wzborders.* FROM `qwt_wzbtrades`,qwt_wzborders WHERE qwt_wzborders.tid=qwt_wzbtrades.tid and qwt_wzbtrades.status!='TRADE_CLOSED' and qwt_wzbtrades.status!='TRADE_CLOSED_BY_USER' and qwt_wzbtrades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        //$this->template->content = View::factory('weixin/wzb/admin/stats_goods')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }
    public function action_other_setgood($action='',$id=0){
        if ($action == 'add') return $this->action_other_setgood_add();
        if ($action == 'edit') return $this->action_other_setgood_edit($id);
        $bid = $this->bid;
        $other_setgoods = ORM::factory('qwt_wzbsetgood')->where('bid','=',$bid)->where('type','=',2);
        $other_setgoods = $other_setgoods->reset(false);

        $result['countall'] = $countall = $other_setgoods->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');

        $result['items'] = $other_setgoods->order_by('priority', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '其他商品管理';
        //$this->template->content = View::factory('weixin/wzb/admin/other_setgoods')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/other_setgoods')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_other_setgood_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);

        if ($_POST['data']) {

            $item = ORM::factory('qwt_wzbsetgood');
            $item->values($_POST['data']);
            $item->time=time();
            $item->type=2;
            $item->bid = $bid;

            if (!$_POST['data']['title'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->db_pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "wzb:other_setgood:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtwzba/other_setgood');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新商品';
        //$this->template->content = View::factory('weixin/wzb/admin/other_setgoods_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/other_setgoods_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_other_setgood_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid);

        $item = ORM::factory('qwt_wzbsetgood', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $item->delete();
            Request::instance()->redirect('qwtwzba/other_setgood');
        }

        if ($_POST['data']) {
            $item->values($_POST['data']);
            $item->bid = $bid;
            $item->time=time();
            if (!$_POST['data']['title']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->db_pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "wzb:other_setgood:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtwzba/other_setgood');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改商品';
        //$this->template->content = View::factory('weixin/wzb/admin/other_setgoods_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/other_setgoods_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_setgoods1(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $tempconfig=ORM::factory('qwt_wzbcfg')->getCfg($this->bid);
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
            $pg=1;
            $method = 'youzan.items.onsale.get';
            $params =[
                //'fields' =>'total_results',
            ];
            $total_result= $client->post($method, '3.0.0', $params, $files);
            // echo '<pre>';
            // var_dump($total_result);
            // echo '</pre>';
            $total =$total_result['response']['count'];
            $item_num=ORM::factory('qwt_wzbsetgood')->where('bid','=',$bid)->count_all();
            // echo $total."<br>";
            // echo $item_num."<br>";
            // exit();
            if($total!=$item_num||$_GET['refresh']==1){
                $a = ceil($total/100);
                for($k=0;$k<$a;$k++){
                    // echo $k."<br>";
                    $method = 'youzan.items.onsale.get';
                    $params = array(
                        'page_size'=>100,
                        'page_no'=>$k+1,
                        // 'q' => 'item_id','title',
                        );
                    $results = $client->post($method, '3.0.0', $params, $files);
                    // echo '<pre>';
                    // var_dump($results);
                    // echo '</pre>';
                    // echo "===============<br>";
                    for($i=0;$results['response']['items'][$i];$i++){
                        $res=$results['response']['items'][$i];
                        $method = 'youzan.item.get';
                        $params = array(
                             'item_id'=>$res['item_id'],
                        );
                        $result = $client->post($method, '3.0.0', $params, $files);
                        // echo '<pre>';
                        // var_dump($result);
                        // echo '</pre>';
                        // echo "===============<br>";
                        $item=$result['response']['item'];
                        $skus=$item['skus'];
                        $type=1;
                        $num_iid=$item['item_id'];
                        $name = str_replace("'", " ", $item['title']);
                        $price=$item['price']/100;
                        $pic=$item['pic_url'];
                        $url=$item['detail_url'];
                        $num=$item['quantity'];
                        $num_num = ORM::factory('qwt_wzbsetgood')->where('goodid', '=', $num_iid)->count_all();
                        if($num_num==0 && $num_iid){
                            $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_wzbsetgoods` (`bid`,`goodid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,1,$type,$num)");
                            $sql->execute();
                        }else{
                            $sql = DB::query(Database::UPDATE,"UPDATE `qwt_wzbsetgoods` SET `bid` = $bid ,`goodid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 1 , `type` =$type where `goodid` = $num_iid ");
                            $sql->execute();
                        }
                    }
                }
                $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_wzbsetgoods` where `state` =0 and `bid` = $bid and type =1 ");
                $sql->execute();
                $sql = DB::query(Database::UPDATE,"UPDATE `qwt_wzbsetgoods` SET `state` =0 where `bid` = $bid and type =1");
                $sql->execute();
            }
        }
        Request::instance()->redirect('qwtwzba/setgoods');
    }
    public function action_setgoods(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_wzbcfg')->getCfg($bid, 1);
        //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $goods = ORM::factory('qwt_wzbsetgood')->where('bid','=',$bid)->where('type','=',1);
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('title', 'like', $s);
        }
        $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $tempconfig=ORM::factory('qwt_wzbcfg')->getCfg($this->bid);
        if ($_POST['form']['num_iid']) {
            $goodid = $_POST['form']['num_iid'];
            $setgoods = ORM::factory('qwt_wzbsetgood')->where('bid', '=', $bid)->where('goodid','=',$goodid)->find();
            $setgoods->priority=$_POST['form']['priority'];
            $setgoods->status = $_POST['form']['status'];
            $setgoods->save();
        }
        $result['countall'] = $countall = $goods->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wzb/pages');
        $result['goods'] =$goods->order_by('status', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wzb/setgoods')
        ->bind('result',$result)
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
        //Kohana::$log->add("weixin_wzb:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_wzb:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_wzb:tpl", print_r($result, true));
        return $result;
    }
}
