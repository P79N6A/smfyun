<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Scba extends Controller_Base {

    public $template = 'weixin/scb/tpl/atpl';
    public $pagesize = 20;

    public $config;
    public $bid;

    public function before() {
        Database::$default = "scb";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        $this->bid = $_SESSION['scba']['bid'];
        $this->config = $_SESSION['scba']['config'];

        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/scba/login');
            header('location:/scba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('scb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            $todo['items'] = ORM::factory('scb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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
        require_once Kohana::find_file("vendor/kdt","KdtApiClient");
        $appId = ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;

        $appSecret = ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;

        $client = new KdtApiClient($appId, $appSecret);

        $tag_name =  ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','tag_name')->find()->value;

        $sql = DB::query(Database::SELECT,"SELECT openid as OP FROM scb_qrcodes where (`bid`=$this->bid and `ticket`!= 'NULL') or (`bid`=$this->bid and `fopenid`!= 'NULL')");
        $openid = $sql->execute()->as_array();
        for($p=0;$openid[$p];$p++){
            //echo $openid[$p]['OP'].'<br>';
            $method = 'kdt.users.weixin.follower.tags.add';
            $params = [
            'tags' =>$tag_name,

            'weixin_openid' =>$openid[$p]['OP'],
            ];
            $test=$client->post($method, $params);
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
        $config = ORM::factory('scb_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('scb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."scb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."scb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."scb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        $result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('scb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('scb_login')->where("id","=",$bid)->find()->user;
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

             if ($_FILES['rootca']['error'] == 0) {
                @mkdir(dir($rootca_file));
                $ok = move_uploaded_file($_FILES['rootca']['tmp_name'], $rootca_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'scb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'scb_file_key', '', file_get_contents($key_file));
            if (file_exists($rootca_file)) $cfg->setCfg($bid, 'scb_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('scb_cfg')->getCfg($bid, 1);
        }

        // 个性化配置
        if ($_POST['ban']==1){
            $cfg = ORM::factory('scb_cfg');
            //$tmpfile = $_FILES['pic']['tmp_name'];
            for($i=1;$i<=4;$i++){
                if ($_FILES['pic'.$i]['error'] == 0) {
                    if ($_FILES['pic'.$i]['size'] > 1024*300) {
                        $result['picfail'] = 1;
                    } else if($_FILES['pic'.$i]['tmp_name']){
                        $ok = $cfg->setCfg($bid, 'ban'.$i, '', file_get_contents($_FILES['pic'.$i]['tmp_name']));

                        $result['pic'] =1;
                    }
                }
            }
            foreach ($_POST['banurl'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['pic'] = $ok;
            }
            $config = ORM::factory('scb_cfg')->getCfg($bid, 1);
        }
        // 菜单配置

        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('scb_cfg');
            // $count = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $count = $area['count'];
            for ($i=1; $i<=$count ; $i++) {
                if (!$area['city'.$i]){
                $area['city'.$i]='';
                }
                if (!$area['dis'.$i]){
                $area['dis'.$i]='';
                }
            }
            foreach ($area as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok5'] += $ok;
            }

            $config = ORM::factory('scb_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }
        if ($_POST['tag']){
            $tag = $_POST['tag'];
            $cfg = ORM::factory('scb_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }


            $this->action_otag();
            $result['fresh'] = 1;
            $config = ORM::factory('scb_cfg')->getCfg($bid, 1);

        }
        if ($_POST['rsync']){
            if($config['yz_appid']&&$config['yz_appsecert']){
                $tag = $_POST['rsync'];
                $cfg = ORM::factory('scb_cfg');
                if($tag['switch1']==1){
                    foreach ($tag as $k=>$v) {
                        $ok = $cfg->setCfg($bid, $k, $v);
                        $result['ok7'] += $ok;
                    }
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('scb_cfg')->getCfg($bid, 1);
        }
        $result['tpl'] = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('scb_login')->where('id', '=', $bid)->find()->expiretime;
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/scb/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }

    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('scb_cfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
        if($config['yz_appid']){
            $client = new KdtApiClient($config['yz_appid'], $config['yz_appsecert']);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('scb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                if ($_POST['form']['score']) {
                    if(isset($config['switch'])){
                        $openid = $qrcode_edit->openid;
                        $method ='kdt.shop.basic.get';
                        $params =[
                        ];
                        $result =$client->post($method,$params);
                        $kdt_id = $result['response']['sid'];
                        $method = 'kdt.users.weixin.follower.get';
                        $params =[
                        'weixin_openid'=>$openid,
                        ];
                        $result=$client->post($method,$params);
                        $user_id = $result['response']['user']['user_id'];
                        $method = 'kdt.crm.fans.points.get';
                        $params =[
                        'fans_id' => $user_id,
                        ];
                        $results=$client->post($method,$params);
                        //Kohana::$log->add("yz2", print_r($results, true));
                        $point = $results['response']['point'];
                        if(isset($point)){
                            $yzscore =ORM::factory('scb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->yz_score;
                            $score =ORM::factory('scb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->score;
                            $scores = ORM::factory('scb_qrcode')->where('bid','=', $bid)->where('openid','=',$openid)->find();
                            $score_sum = $point*$config['switch'] + $score;
                            $score_details=ORM::factory('scb_score');
                            //Kohana::$log->add("yz_score", print_r($yzscore, true));
                           if($yzscore==0){
                                $method = 'kdt.crm.fans.points.payin.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($score/$config['switch']),
                                ];
                                $a=$client->post($method,$params);
                                if($a['response']['is_success']=='true'||$score==0){
                                    if($point!=0){
                                        $score_details->bid=$bid;
                                        $score_details->qid=$scores->id;
                                        $score_details->type=5;
                                        $score_details->score=$point*$config['switch'];
                                        $score_details->save();
                                    }
                                    $scores->score=$score_sum;
                                    $scores->yz_score = floor($score_sum/$config['switch']);
                                    $scores->save();
                                }
                            }
                            //当表中有赞积分不为零，肯定不是第一次同步
                            if($yzscore!=0&&$point!=$yzscore){
                                $score_details->bid=$bid;
                                $score_details->qid=$scores->id;
                                if($point>$yzscore){
                                    $score_details->type=5;
                                }else{
                                    $score_details->type=6;
                                }
                                $score_details->score=($point-$yzscore)*$config['switch'];
                                $score_details->save();
                                $scores->score=$point*$config['switch'];
                                $scores->yz_score=$point;
                                $scores->save();
                            }
                            if($yzscore!=0&&$point==$yzscore&&$score!=$yzscore*$config['switch']){
                                if($score>$yzscore*$config['switch']){
                                    $method = 'kdt.crm.fans.points.payin.get';
                                    $params =[
                                    'fans_id' => $user_id,
                                    'kdt_id' => $kdt_id,
                                    'points' => floor($score/$config['switch']-$yzscore),
                                    ];
                                }else{
                                    $method = 'kdt.crm.fans.points.payout.get';
                                    $params =[
                                    'fans_id' => $user_id,
                                    'kdt_id' => $kdt_id,
                                    'points' => floor($yzscore-$score/$config['switch']),
                                    ];
                                }
                                $a=$client->post($method,$params);
                                if($a['response']['is_success']=='true'){
                                    $scores->yz_score=floor($score/$config['switch']);
                                    $scores->save();
                                }
                            }
                            if($_POST['form']['score']>0){
                                $method = 'kdt.crm.fans.points.payin.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor($_POST['form']['score']/$config['switch']),
                                ];
                                $a=$client->post($method,$params);
                            }else{
                                $method = 'kdt.crm.fans.points.payout.get';
                                $params =[
                                'fans_id' => $user_id,
                                'kdt_id' => $kdt_id,
                                'points' => floor(-$_POST['form']['score']/$config['switch']),
                                ];
                                $a=$client->post($method,$params);
                            }
                            if($a['response']['is_success']=='true'){
                                $scores = ORM::factory('scb_qrcode')->where('bid','=', $bid)->where('openid','=',$openid)->find();
                                //$scores->score=$score+$config['goal0'];
                                $score =ORM::factory('scb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->score;
                                $scores->yz_score=floor(($score+$_POST['form']['score'])/$config['switch']);
                                $scores->save();
                            }
                        }

                    }
                    $qrcode_edit = ORM::factory('scb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();

                    ORM::factory('scb_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                }
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('scb_qrcode')->where('bid', '=', $bid);
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
            $result['fuser'] = ORM::factory('scb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('scb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM scb_qrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
        ))->render('weixin/scb/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/scb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('scb_cfg')->getCfg($bid);

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

                $order = ORM::factory('scb_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
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
                $order = ORM::factory('scb_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
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

            $order = ORM::factory('scb_order')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();

                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['scba']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['scba']['shipcode'] = $_REQUEST['shipcode'];
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

                //Request::instance()->redirect('scba/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('scb_order')->where('bid', '=', $bid)->where('status', '=', $result['status']);
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
            $result['qrcode'] = ORM::factory('scb_qrcode', $result['qid']);
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
                //$count2 = ORM::factory('scb_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('scb_qrcode')->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('scb_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

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
        ))->render('weixin/scb/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/scb/admin/orders')
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
            require_once Kohana::find_file("vendor/kdt","KdtApiClient");
            $appId = ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;
            $appSecret = ORM::factory('scb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;
            if($appId&&$appSecret){
                $client = new KdtApiClient($appId, $appSecret);
                $method = 'kdt.ump.presents.ongoing.all';
                $params = [
                ];
                $results = $client->post($method,$params);
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
        $config = ORM::factory('scb_cfg')->getCfg($bid);

        $result['items'] = ORM::factory('scb_item')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all();
        $iid = ORM::factory('scb_item')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('scb_order')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }

        $this->template->title = '奖品管理';
        $this->template->content = View::factory('weixin/scb/admin/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('scb_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $item = ORM::factory('scb_item');
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
                $key = "scb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('scba/items');
            }
        }

        $result['action'] = 'add';
        $result['present_id']=$present_id;
        $result['title1']=$title1;
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->content = View::factory('weixin/scb/admin/items_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('scb_cfg')->getCfg($bid);

        $item = ORM::factory('scb_item', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('scb_order')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('scba/items');
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
                $key = "scb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('scba/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        $this->template->content = View::factory('weixin/scb/admin/items_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['scba']['admin'] < 1) Request::instance()->redirect('scba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('scb_login');
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
        ))->render('weixin/scb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/scb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['scba']['admin'] < 2) Request::instance()->redirect('scba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('scb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('scb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('scba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/scb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['scba']['admin'] < 2) Request::instance()->redirect('scba/home');

        $bid = $this->bid;

        $login = ORM::factory('scb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('scb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('scba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('scb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('scba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/scb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/scb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('scb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('scb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['scba']['bid'] = $biz->id;
                    $_SESSION['scba']['user'] = $_POST['username'];
                    $_SESSION['scba']['admin'] = $biz->admin; //超管
                    $_SESSION['scba']['config'] = ORM::factory('scb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['scba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/scba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['scba'] = null;
        header('location:/scba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "scb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    //产品图片
    public function action_banimages($key, $bid=1) {

        $pic = ORM::factory('scb_cfg')->where('bid','=',$bid)->where('key','=',$key)->find()->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('scb_score')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('scb_qrcode')->table_name())
            ->set(array('score' => '0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            Request::instance()->redirect('scba/home');
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
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from scb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from scb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];
                //活动参与人数
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `scb_scores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from scb_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' or FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over')")->execute()->as_array();
                $newadd[0]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `scb_orders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')>='$begin' and FROM_UNIXTIME(`createdtime`, '$daytype')<='$over' ")->execute()->as_array();
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
            //$days=DB::query(Database::SELECT,"SELECT  FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `scb_qrcodes` where bid=$this->bid UNION select FROM_UNIXTIME(`lastupdate`, '$daytype') from scb_scores where bid =$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `scb_qrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
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
            ))->render('weixin/scb/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `scb_qrcodes` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from scb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from scb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from scb_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' or FROM_UNIXTIME(`jointime`, '$daytype')='$time')")->execute()->as_array();
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `scb_scores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `scb_orders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `scb_qrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/scb/admin/stats_totle')
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
