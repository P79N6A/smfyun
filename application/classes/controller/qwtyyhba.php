<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtyyhba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/yyhbatpl';
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
        if (Request::instance()->action != 'login' && !$this->bid) {
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',4)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
        $qr_num=ORM::factory('qwt_yyhbqrcode')->where('bid','=',6)->delete_all();
        exit();
    }
    //系统配置
    public function action_home() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
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
        if ($_GET['auth_code']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
            $post_data = array(
              'component_appid' =>$this->appid,
              'authorization_code' =>$_GET['auth_code']
            );
            $post_data = json_encode($post_data);
            $res = json_decode($this->request_post($url, $post_data),true);
            $appid = $res['authorization_info']['authorizer_appid'];
            $yzaccess_token = $res['authorization_info']['authorizer_access_token'];
            $refresh_token = $res['authorization_info']['authorizer_refresh_token'];
            $expires_in = time()+7200;
            for($i=0;$res['authorization_info']['func_info'][$i];$i++){
                $auth_info = $auth_info.','.$res['authorization_info']['func_info'][$i]['funcscope_category']['id'];
            }
            // echo $auth_info.'<br>';
            // var_dump($res);
            $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            //$user->yzaccess_token = $yzaccess_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='wfb.access_token'.$this->bid;
            $mem->set($cachename1, $yzaccess_token, 5400);//有效期两小时
        }        //菜单配置
        if ($_POST['menu']) {
            $buser = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            if(!$buser->appid) die('请在【绑定我们】【微信一键授权】处，点击【一键授权】');

            $cfg = ORM::factory('qwt_yyhbcfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }
            //配置菜单更新
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
            if(!$bid) Kohana::$log->add('yyhbbid:', 'meun');//写入日志，可以删除
            $wx = new Wxoauth($bid,$options);
            $data['button'][0]['name']=$_POST['menu']['key_menu'];
            $data['button'][0]['sub_button'][0]['type']='click';
            $data['button'][0]['sub_button'][0]['name']=$_POST['menu']['key_qrcode'];
            $data['button'][0]['sub_button'][0]['key']='qrcode';
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
            $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
        }
        //文案配置
        if ($_POST['text']) {


            $cfg = ORM::factory('qwt_yyhbcfg');
            $qrfile = DOCROOT."qwt/yyhb/tmp/tpl.$bid.jpg";

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
                    $default_head_file = DOCROOT."yyhb/tmp/head.$bid.jpg";
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

                        $tpl = ORM::factory('qwt_yyhbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('qwt_yyhbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }
            //重新读取配置
            $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
        }
        $result['tpl'] = ORM::factory('qwt_yyhbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_yyhbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }
    public function action_lab() {
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
        //$this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if ($_POST['tag']['tag_name']){//点击刷新
            $tag = $_POST['tag'];
            $cfg = ORM::factory('qwt_yyhbcfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }
            $result['fresh'] = 1;
            $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
            // $next_qid=$config['next_qid'];
            // if(!$next_qid) $next_qid=0;
            $users = ORM::factory('qwt_yyhbqrcode')->where('bid','=',$bid)->find_all();
            foreach ($users as $k => $v) {
                $labuser[]="($v->bid,$v->id,'{$config['tag_name']}')";
            }
            $sql='INSERT IGNORE INTO qwt_yyhblabs (`bid`,`qid`,`lab_name`) VALUES '. join(',',$labuser);
            DB::query(Database::INSERT,$sql)->execute();
            $result['fresh'] = 1;
        }
        $result['islab'] = ORM::factory('qwt_yyhblab')->where('bid','=',$bid)->where('status','=',1)->count_all();
        $result['alllab'] = ORM::factory('qwt_yyhblab')->where('bid','=',$bid)->count_all();
        $this->template->title = '首页';
        //$this->template->content = View::factory('weixin/wdy/admin/lab')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/lab')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_area() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
        if ($_POST['area']){
            $area = $_POST['area'];
            $cfg = ORM::factory('qwt_yyhbcfg');
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
            $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/area')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_yyhbqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])){
                 $qrcode_edit->lock = (int)$_POST['form']['lock'];
                 $qrcode_edit->save();
                }
            }
        }
        $qrcode = ORM::factory('qwt_yyhbqrcode')->where('bid', '=', $bid);
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
            $result['fuser'] = ORM::factory('qwt_yyhbqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('qwt_yyhbqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_yyhbqrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
        ))->render('weixin/qwt/admin/yyhb/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
         //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');

            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[8], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 10) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[9]);
                    $shipcode = iconv('gbk', 'utf-8', $data[10]);
                } else {
                    $shiptype = $data[9];
                    $shipcode = $data[10];
                }

                $order = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
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
            // echo '<pre>';
            // var_dump($_POST);
            // echo '<pre>';
            // exit();
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('qwt_yyhborder')->where('id', '=', $oid)->find();
                $order->status = 1;
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
            $order = ORM::factory('qwt_yyhborder')->where('id', '=', $id)->find();
            if ($order->status == 0) {
                $order->status = 1;
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
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $res = $wx->sendCustomMessage($msg);
                }
            }
        }
        $result['status'] = 0;
        if ($action == 'done') {
            $result['status'] = 1;
        }
        $order = ORM::factory('qwt_yyhborder')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);
        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('qwt_yyhbqrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('qwt_yyhbqrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            $order = $order->and_where_open();
                $s = '%'.trim($_GET['s'].'%');
                $order = $order->where('name', 'like', $s)->or_where('task_name', 'like', $s);
            if($countuser>0){
                $user = ORM::factory('qwt_yyhbqrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
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
            $title = array('id', '姓名', '任务名称', '奖品名称','发送状态（1为成功0为失败）','原因','收货人','联系电话','收货地址','物流公司','物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $order = $order->order_by('lastupdate', 'DESC')->limit(1000)->find_all();
            foreach ($order as $o) {
                $array = array($o->id, $o->name, $o->task_name, $o->item_name, $o->state, $o->log,$o->receive_name,$o->tel,$o->address);
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
        ))->render('weixin/qwt/admin/yyhb/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
      //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
        $item=ORM::factory('qwt_yyhbitem')->where('bid', '=', $bid);
        $item = $item->reset(FALSE);
        if ($_GET['s']) {
            $item = $item->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $item = $item->where('km_content', 'like', $s);
            $item = $item->and_where_close();
        }
        $countall = $item->count_all();
        $iid = ORM::factory('qwt_yyhbitem')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('qwt_yyhborder')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }
         //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/yyhb/pages');
        $result['items'] = $item->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '奖品管理';
        $result['title'] = '添加新奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('pages',$pages)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
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
        // if($_POST){
        //     echo "<pre>";
        //     var_dump($_POST);
        //     echo "</pre>";
        //     exit();
        // }
        if(isset($_POST['type'])&&$_POST['type']==0){
            $item = ORM::factory('qwt_yyhbitem');
            if($_FILES['pic1']['error'] == 0){
                // echo '<pre>';
                // var_dump($_FILES);
                // echo '</pre>';
                // exit();
                if ($_FILES['pic1']['size'] > 1024*400) {
                    $result['error'] = '奖品图片大小不能超过 400K';
                } else {
                    $item->pic=file_get_contents($_FILES['pic1']['tmp_name']);
                }
            }
            $item->bid= $bid;
            $item->type=$_POST['type'];
            $item->key= 'shiwu';
            $item->need_money = intval($_POST['need_money']);
            $item->km_content= $_POST['shiwu']['km_content'];
            if(!$result['error']){
                $item->save();
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==4) {
            $item = ORM::factory('qwt_yyhbitem');
            $item->bid= $bid;
            $item->type=$_POST['type'];
            $item->key= 'hongbao';
            $item->value =$_POST['hongbao']['value'];
            $item->km_content= $_POST['hongbao']['km_content'];
            if (!$_POST['hongbao']['value']) $result['error']['hongbao'] = '请填写完整后再提交（请在绑定我们-微信支付，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==1) {
            $item = ORM::factory('qwt_yyhbitem');
            $item->bid =$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'coupon';
            $item->value =$_POST['coupon']['value'];
            $item->km_content=$_POST['coupon']['km_content'];
            if (!$_POST['coupon']['value']) $result['error']['coupon'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==6) {
            $item = ORM::factory('qwt_yyhbitem');
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'gift';
            $item->value =$_POST['gift']['value'];
            $item->km_content=$_POST['gift']['km_content'];
            if (!$_POST['gift']['value']) $result['error']['gift'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==5) {
            $item = ORM::factory('qwt_yyhbitem');
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'yzcoupon';
            $item->value =$_POST['yhq']['value'];

            $item->km_content=$_POST['yhq']['km_content'];

            if (!$_POST['yhq']['value']) $result['error']['yhq'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==8){
            $text = $_POST['yhm']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $time=time();
            $text = $_POST['yhm']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $item = ORM::factory('qwt_yyhbitem');
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'yhm';
            $item->value=$time;
            $item->km_text=$text;
            $item->km_content=$_POST['yhm']['km_content'];
            if (!$_POST['yhm']['km_content'] || !is_uploaded_file($_FILES['pic']['tmp_name'])) $result['error']['yhm'] = '请填写完整后再提交';
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error']['yhm'] ='不是Excel文件，重新上传';
                }else{
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    //echo $highestRow.'<br>';
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    //echo $highestColumm.'<br>';
                    // /** 循环读取每个单元格的数据 */
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    foreach ( $dataset as $data ) {
                        $km =ORM::factory('qwt_yyhbkm');
                        $km->bid = $this->bid;
                        $km->starttime = $time;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
            }
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }

        }
        if ($_POST['type']==7){
            $item = ORM::factory('qwt_yyhbitem');
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'kmi';
            $item->value=$_POST['kmi']['value'].'&'.$_POST['kmi']['url'];

            $item->km_content=$_POST['kmi']['km_content'];

            if (!$_POST['kmi']['value']) $result['error']['yyhb'] = '请填写完整后再提交';

            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        $result['title'] = '添加新奖品';
        $result['action'] = 'add';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/items_add')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);

    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
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
        $item1 = ORM::factory('qwt_yyhbitem')->where('bid','=',$bid)->where('id','=',$id)->find()->as_array();
        $this->template->title = '修改';
        $item=ORM::factory('qwt_yyhbitem')->where('bid','=',$bid)->where('id','=',$id)->find();
        if(isset($_POST['type'])&&$_POST['type']==0){
            if($_FILES['pic1']['error'] == 0){
                // echo '<pre>';
                // var_dump($_FILES);
                // echo '</pre>';
                // exit();
                if ($_FILES['pic1']['size'] > 1024*400) {
                    $result['error'] = '奖品图片大小不能超过 400K';
                } else {
                    $item->pic=file_get_contents($_FILES['pic1']['tmp_name']);
                }
            }
            $item->bid= $bid;
            $item->type=$_POST['type'];
            $item->key= 'shiwu';
            $item->need_money = intval($_POST['need_money']);
            $item->km_content= $_POST['shiwu']['km_content'];
            if(!$result['error']){
                $item->save();
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==4) {
            $item->bid= $bid;
            $item->type=$_POST['type'];
            $item->key= 'hongbao';
            $item->value =$_POST['hongbao']['value'];
            $item->km_content= $_POST['hongbao']['km_content'];
            if (!$_POST['hongbao']['value']) $result['error']['hongbao'] = '请填写完整后再提交（请在绑定我们-微信支付，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==1) {
            $item->bid =$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'coupon';
            $item->value =$_POST['coupon']['value'];
            $item->km_content=$_POST['coupon']['km_content'];
            if (!$_POST['coupon']['value']) $result['error']['coupon'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==6) {
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'gift';
            $item->value =$_POST['gift']['value'];
            $item->km_content=$_POST['gift']['km_content'];
            if (!$_POST['gift']['value']) $result['error']['gift'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==5) {
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'yzcoupon';
            $item->value =$_POST['yhq']['value'];

            $item->km_content=$_POST['yhq']['km_content'];

            if (!$_POST['yhq']['value']) $result['error']['yhq'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        if ($_POST['type']==8){
            $text = $_POST['yhm']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $time=time();
            $text = $_POST['yhm']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'yhm';
            $item->value=$time;
            $item->km_text=$text;
            $item->km_content=$_POST['yhm']['km_content'];
            if (!$_POST['yhm']['km_content']) $result['error']['yhm'] = '请填写完整后再提交';
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error']['yhm'] ='不是Excel文件，重新上传';
                }else{
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    //echo $highestRow.'<br>';
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    //echo $highestColumm.'<br>';
                    // /** 循环读取每个单元格的数据 */
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    foreach ( $dataset as $data ) {
                        $km =ORM::factory('qwt_yyhbkm');
                        $km->bid = $this->bid;
                        $km->starttime = $time;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
            }
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }

        }
        if ($_POST['type']==7){
            $item->bid=$this->bid;
            $item->type=$_POST['type'];
            $item->key= 'kmi';
            $item->value=$_POST['kmi']['value'].'&'.$_POST['kmi']['url'];

            $item->km_content=$_POST['kmi']['km_content'];

            if (!$_POST['kmi']['value']) $result['error']['yyhb'] = '请填写完整后再提交';

            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "yyhb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtyyhba/items');
            }
        }
        $result['item']['id'] = $id;
        $result['title'] = '修改奖品';
        $result['action'] = 'edit';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/items_add')
            ->bind('bid', $bid)
            ->bind('item', $item1)
            ->bind('result',$result);
    }
     public function action_items_delete($id){
        $value =ORM::factory('qwt_yyhbitem')->where('bid','=',$this->bid)->where('id','=',$id)->find()->value;
        $type =ORM::factory('qwt_yyhbitem')->where('bid','=',$this->bid)->where('id','=',$id)->find()->key;
        $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_yyhbitems` where `bid` = $this->bid and `id` = $id");
        $sql->execute();
        Request::instance()->redirect('qwtyyhba/items');
    }
      //积分奖品管理
    public function action_tasks($action='', $id=0) {
        if ($action == 'add') return $this->action_tasks_add();
        if ($action == 'edit') return $this->action_tasks_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
        $tasks=ORM::factory('qwt_yyhbtask')->where('bid', '=', $bid);
        $tasks = $tasks->reset(FALSE);
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
        ))->render('weixin/qwt/admin/yyhb/pages');

        $result['tasks'] = $tasks->order_by('endtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '任务管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/tasks')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }
    //奖品发送明细
    public function action_items_num($tid) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
        $items_num = ORM::factory('qwt_yyhborder')->where('bid', '=', $bid)->where('tid', '=', $tid);
        $items_num = $items_num->reset(FALSE);
        $result['countall'] = $items_num->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $result['countall'],
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/yyhb/pages');
        $result['tid_name'] = ORM::factory('qwt_yyhbtask')->where('bid', '=', $bid)->where('id', '=', $tid)->find()->name;
        $result['items_num'] = $items_num->order_by('id', 'ASC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = $result['tid_name'].'的奖品发送情况';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/items_num')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }

    public function action_tasks_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
        if ($_POST['data']) {
            // echo '<pre>';
            // var_dump($_POST);
            // var_dump($_FILES);
            // echo '</pre>';
            // exit();
            // $_POST['data']['pic']='';
            if($_FILES['pic1']['error']==0){
                $_POST['data']['pic']=file_get_contents($_FILES['pic1']['tmp_name']);
            }
            // if ($_FILEST['pic1']['size'] > $_POST['MAX_FILE_SIZE']) $result['error'] = '图片尺寸过大';
            // $first_task=ORM::factory('qwt_yyhbtask')->where('flag','=',1)->order_by('begintime','ASC')->find_all();
            // $num_task=ORM::factory('qwt_yyhbtask')->where('flag','=',1)->count_all();
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);

            $time = time();
            $tasknow = ORM::factory('qwt_yyhbtask')->where('bid','=',$bid)->where('begintime','<',$time)->where('endtime','>',$time)->where('flag','=',1)->find();
            if ($tasknow->id) {
                $result['error'] = '目前已经有活动在进行中，请先终止该活动再添加';
            }else{
                if ($_POST['data']['endtime']<$_POST['data']['begintime']||$_POST['data']['endtime']<$time) {
                    $result['error'] = '时间设置不合理，请重试';
                }
            }
            //$last_task=ORM::factory('qwt_yyhbtask')->where('flag','=',1)->order_by('','DESC')->find();
            $_POST['data']['flag']=1;
            $task = ORM::factory('qwt_yyhbtask');
            $task->values($_POST['data']);
            $task->bid = $bid;
            // $past=ORM::factory('qwt_yyhbtask')->where('bid','=',$bid)->where('endtime','>',time())->find();
            // if(!$config['mgtpl']){
            //     $result['error'] = '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】';
            // }
            // if($past && $past->begintime!=$past->endtime ){
            //     $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
            // }
            if (!$_POST['data']['name'] || !$_POST['data']['keyword']) $result['error'] = '请填写完整后再提交';
            if (!$_POST['prize'][0] || !$_POST['stock'][0]) $result['error'] = '该活动至少添加一个奖品';
            if (!$result['error']) {
                $task->save();
                foreach ($_POST['prize'] as $k => $v) {
                    $sku = ORM::factory('qwt_yyhbsku');
                    $sku->bid = $bid;
                    $sku->lv = $k;
                    $sku->tid = $task->id;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k];
                    $sku->save();
                }

                Request::instance()->redirect('qwtyyhba/tasks');
            }
        }
        $items = ORM::factory('qwt_yyhbitem')->where('bid','=',$bid)->find_all();
        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新任务';
        $result['text'] = '添加新任务';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/tasks_add')
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_tasks_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyhbcfg')->getCfg($bid,1);
        $task = ORM::factory('qwt_yyhbtask', $id);
        if (!$task || $task->bid != $bid) die('404 Not Found!');
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            //$begin=$task->begintime;
            $task->flag=0;
            $task->save();
            Request::instance()->redirect('qwtyyhba/tasks');
        }
        if ($_GET['TRASH'] == 1) {
            //有兑换记录的产品不能删除
            //$begin=$task->begintime;
            $task->delete();
            Request::instance()->redirect('qwtyyhba/tasks');
        }
        if ($_POST['data']) {
            // var_dump($_FILEST);
            // exit();
            //$_POST['data']['pic']='';
            if($_FILES['pic1']['error']==0){

                $_POST['data']['pic']=file_get_contents($_FILES['pic1']['tmp_name']);
            }
            // if ($_FILES['pic1']['size'] > $_POST['MAX_FILE_SIZE']) $result['error'] = '图片尺寸过大';
            // $first_task=ORM::factory('qwt_yyhbtask')->where('flag','=',1)->order_by('begintime','ASC')->where('id','!=',$id)->find_all();
            // $num_task=ORM::factory('qwt_yyhbtask')->where('flag','=',1)->count_all();
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
            $task->values($_POST['data']);
            $task->bid = $bid;

            $time = time();
            $tasknow = ORM::factory('qwt_yyhbtask')->where('bid','=',$bid)->where('begintime','<',$time)->where('endtime','>',$time)->where('flag','=',1)->find();
            if (!$tasknow->id||$tasknow->id==$task->id) {
                if ($_POST['data']['endtime']<$_POST['data']['begintime']||$_POST['data']['endtime']<$time) {
                    $result['error'] = '时间设置不合理，请重试';
                }
            }else{
                $result['error'] = '目前已经有活动在进行中，请先终止该活动再添加';
            }

            if (!$_POST['data']['name'] || !$_POST['data']['keyword']) $result['error'] = '请填写完整后再提交';
            if (!$_POST['prize'][0] || !$_POST['stock'][0]) $result['error'] = '该活动至少添加一个奖品';
            if (!$result['error']) {
                $task->save();
                foreach ($_POST['prize'] as $k => $v) {
                    $sku = $skus = ORM::factory('qwt_yyhbsku')->where('bid', '=', $bid)->where('tid', '=', $id)->where('lv', '=', $k)->find();
                    $ordernum=ORM::factory('qwt_yyhborder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                    $sku->bid = $bid;
                    $sku->tid = $task->id;
                    $sku->lv =$k;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k];
                    $sku->save();
                    $form = $k+1;
                }
                $mysql = ORM::factory('qwt_yyhbsku')->where('bid', '=', $this->bid)->where('tid', '=', $id)->count_all();
                if($mysql>$form){
                    for ($i=0; $i <$mysql-$form ; $i++) {
                        $result = DB::query(Database::DELETE,"DELETE  from qwt_yyhbskus  where bid=$bid and tid =$id and lv= $form+$i")->execute();
                    }
                }
                Request::instance()->redirect('qwtyyhba/tasks');
            }else{
                // var_dump($result);
                // exit();
            }
        }
        $_POST['data'] = $result['task'] = $task->as_array();
        // exit;
        $result['action'] = 'edit';
        $items = ORM::factory('qwt_yyhbitem')->where('bid','=',$bid)->find_all();
        $skus = ORM::factory('qwt_yyhbsku')->where('bid','=',$bid)->where('tid','=',$id)->find_all();
        $result['title'] = $this->template->title = '修改活动';
        $result['text'] = '保存';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/tasks_add')
            ->bind('bid',$bid)
            ->bind('task',$task)
            ->bind('skus', $skus)
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_yyhb$type";
        // echo $type.'<br>';
        // echo $id.'<br>';
        // echo $cksum.'<br>';
        // exit();
        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    public function action_stats_totle(){
        $bid = $this->bid;
        $tasks = ORM::factory('qwt_yyhbtask')->where('bid','=',$bid)->find_all();
        if ($_POST) {
            $start = strtotime($_POST['time']['begintime']);
            $end = strtotime($_POST['time']['endtime']);
            if ($start==false||$end==false) {
                if ($_POST['event']) {
                    $tid = $_POST['event'];
                    $join_num = DB::query(Database::SELECT,"SELECT count(distinct qid) as num from qwt_yyhbrecords where bid = $bid and tid = $tid")->execute()->as_array();
                    $fans_num = ORM::factory('qwt_yyhbqrcode')->where('bid','=',$bid)->count_all();
                    $gift_num = ORM::factory('qwt_yyhborder')->where('bid','=',$bid)->where('tid','=',$tid)->count_all();
                    $pv=ORM::factory('qwt_yyhbuv')->where('bid','=',$bid)->where('tid','=',$tid)->count_all();
                    $uv=DB::query(Database::SELECT,"SELECT count(distinct qid) as num from qwt_yyhbuvs where bid = $bid and tid = $tid")->execute()->as_array();
                }
            }else{

                $join_num = DB::query(Database::SELECT,"SELECT count(distinct qid) as num from qwt_yyhbrecords where bid = $bid and jointime > $start and jointime < $end")->execute()->as_array();
                $fans_num = ORM::factory('qwt_yyhbqrcode')->where('bid','=',$bid)->where('jointime','>',$start)->where('jointime','<',$end)->count_all();
                $gift_num = ORM::factory('qwt_yyhborder')->where('bid','=',$bid)->where('lastupdate','>',$start)->where('lastupdate','<',$end)->count_all();
                $pv=ORM::factory('qwt_yyhbuv')->where('bid','=',$bid)->where('lastupdate','>',$start)->where('lastupdate','<',$end)->count_all();
                $uv=DB::query(Database::SELECT,"SELECT count(distinct qid) as num from qwt_yyhbuvs where bid = $bid and lastupdate > $start and lastupdate < $end")->execute()->as_array();
            }
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyhb/stats_totle')
        ->bind('join_num',$join_num)
        ->bind('fans_num',$fans_num)
        ->bind('gift_num',$gift_num)
        ->bind('pv', $pv)
        ->bind('tasks', $tasks)
        ->bind('uv',$uv);
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
}
