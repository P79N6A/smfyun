<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtwfba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/wfbatpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        if (Request::instance()->action == 'state_order') return;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',3)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    // public function action_index() {
    //     $this->action_home();
    // }
    // public function action_scoretest(){
    //     $bid=1313;
    //     $orders=ORM::factory('qwt_wfborder')->where('bid','=',$bid)->find_all();
    //     foreach ($orders as $key => $order) {
    //         $score=$order->score;
    //         $qid=$order->qid;
    //         echo 'qid:'.$qid.'<br>';
    //         echo 'score:'.$score.'<br>';
    //         $qrcode=ORM::factory('qwt_wfbqrcode')->where('id','=',$qid)->find();
    //         echo 'name:'.$qrcode->nickname.'<br>';
    //         $sid=ORM::factory('qwt_wfbscore')->scoreIn($qrcode, 0, $score);
    //         echo 'sid:'.$sid.'<br>';
    //     }
    // }
    //系统配置
    public function action_home() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->access_token;
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
            $access_token = $res['authorization_info']['authorizer_access_token'];
            $refresh_token = $res['authorization_info']['authorizer_refresh_token'];
            $expires_in = time()+7200;
            for($i=0;$res['authorization_info']['func_info'][$i];$i++){
                $auth_info = $auth_info.','.$res['authorization_info']['func_info'][$i]['funcscope_category']['id'];
            }
            // echo $auth_info.'<br>';
            // var_dump($res);
            $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='wfb.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        //文案配置
        if ($_POST['text']) {
            // echo "<pre>";
            // var_dump($_POST['text']);
            // echo "<pre>";
            // exit;
            $cfg = ORM::factory('qwt_wfbcfg');
            $qrfile = DOCROOT."qwt/wfb/tmp/tpl.$bid.jpg";

            //海报有效期
            if ($_POST['text']['ticket_lifetime'] >= 30) $_POST['text']['ticket_lifetime'] = 30;

            //二维码海报
            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
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
                    $default_head_file = DOCROOT."qwt/wfb/tmp/head.$bid.jpg";
                    $cfg->setCfg($bid, 'tplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
                    @unlink($default_head_file);
                    move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }

            if (!$result['err3']) {
                foreach ($_POST['text'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, str_replace('\n', "\n",$v));
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

                        $tpl = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        }
        //菜单配置
        if ($_POST['menu']) {
            $buser = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            if(!$buser->appid) die('请在【绑定我们】【微信一键授权】处，点击【一键授权】');

            $cfg = ORM::factory('qwt_wfbcfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }
            //配置菜单更新
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
            if(!$bid) Kohana::$log->add('wfbbid:', 'meun');//写入日志，可以删除
            $wx = new Wxoauth($bid,$options);

            $data['button'][0]['name']=$_POST['menu']['key_menu'];
            $data['button'][0]['sub_button'][0]['type']='click';
            $data['button'][0]['sub_button'][0]['name']=$_POST['menu']['key_qrcode'];
            $data['button'][0]['sub_button'][0]['key']='qrcode';
            $data['button'][0]['sub_button'][1]['type']='click';
            $data['button'][0]['sub_button'][1]['name']=$_POST['menu']['key_score'];
            $data['button'][0]['sub_button'][1]['key']='score';
            $data['button'][0]['sub_button'][2]['type']='click';
            $data['button'][0]['sub_button'][2]['name']=$_POST['menu']['key_item'];
            $data['button'][0]['sub_button'][2]['key']='item';
            $data['button'][0]['sub_button'][3]['type']='click';
            $data['button'][0]['sub_button'][3]['name']=$_POST['menu']['key_top'];
            $data['button'][0]['sub_button'][3]['key']='top';
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
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('qwt_wfbcfg');
            // $count = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
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

            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        $result['tpl'] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;

        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }

    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])){
                    $qrcode_edit->lock = (int)$_POST['form']['lock'];
                    $qrcode_edit->save();
                }
                if ($_POST['form']['score']) {
                    $qrcode_edit = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('id', '=', $id)->find();
                    $sid=ORM::factory('qwt_wfbscore')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                    Kohana::$log->add('qwt_wfbconfig'.$bid, print_r($config,true));
                    if($config['switch']==1){
                        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                        $smfy=new SmfyQwt();
                        $result1=$smfy->wfbrsync($bid,$qrcode_edit->openid,$yzaccess_token,$_POST['form']['score'],$sid,'积分操作');
                    }
                }

            }
        }
        $qrcode1 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid);
        $qrcode1 = $qrcode1->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode1 = $qrcode1->where('nickname', 'like', $s); //->or_where('openid', 'like', $s);
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode1 = $qrcode1->where('id', '=', $result['id']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode1 = $qrcode1->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode1 = $qrcode1->where('fopenid', '=', $result['fopenid']);
        }
        if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_wfbqrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
              $qrcode1 =$qrcode1->where('fopenid', 'IN',$tempid);
        }

        //按状态搜索
        if ($_GET['lock']) {
            $result['lock'] = $_GET['lock'];
            $qrcode1 = $qrcode1->where('lock', '=', $result['lock']);
        }

        $result['countall'] = $countall = $qrcode1->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/wfb/pages');
        // echo $result['sort'].'<br>';
        // exit();
        if ($result['sort']) $qrcode1 = $qrcode1->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode1->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '用户明细';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_state_order($bid){
        $this->bid = $bid;
        $orders = ORM::factory('qwt_wfborder')->where('bid','=',$this->bid)->where('status','=',1)->find_all();
        $counts = ORM::factory('qwt_wfborder')->where('bid','=',$this->bid)->where('status','=',1)->count_all();
        echo $counts;
        foreach ($orders as $k => $v) {
            echo $v->item->type;
        }
        exit;
    }
    public function action_csv_export(){
        $orders = ORM::factory('qwt_wfborder')->where('bid','=',$this->bid)->where('status','=',1)->find_all();
    }
    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $this->config = $config = ORM::factory('qwt_wfbcfg')->getCfg($bid,1);

        //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            // echo "<pre>";
            // var_dump($_FILES);
            // echo "</pre>";
            // exit();
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');

            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[17], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 19) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[17]);
                    $shipcode = iconv('gbk', 'utf-8', $data[18]);
                } else {
                    $shiptype = $data[17];
                    $shipcode = $data[18];
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
            // while ($data = fgetcsv($fh, 1024)) {
            //     $encode = mb_detect_encoding($data[7], array("ASCII",'UTF-8',"GB2312","GBK"));

            //     // print_r($data);
            //     // if (count($data) < 19) continue;
            //     // if (!is_numeric($data[0])) continue;

            //     //发货
            //     $oid = $data[0];

            //     if ($encode == 'EUC-CN') {
            //         $shiptype = iconv('gbk', 'utf-8', $data[7]);
            //         $shipcode = iconv('gbk', 'utf-8', $data[6]);
            //     } else {
            //         $shiptype = $data[7];
            //         $shipcode = $data[6];
            //     }

            //     $order = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('tel', '=', $data[1])->find();
            //     // if ($order->status == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
            //         $order->status = 1;
            //         $order->shiptype = $shiptype;
            //         $order->shipcode = $shipcode;
            //         $order->save();
            //         $i++;
            //     // }
            // }
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
            $edit_order = ORM::factory('qwt_wfborder')->where('id','=',$_POST['edit_oid'])->find();
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
                $order = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';
                    $tempname=ORM::factory("qwt_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("qwt_wfbitem")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("qwt_wfbqrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                }
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
            if(!$bid) Kohana::$log->add('wfbbid:', 'order');//写入日志，可以删除
            $wx = new Wxoauth($bid,$options);

            $order = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

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
                if(($order->type)==3)
                {

                    $shipmsg = "%s，您的积分兑换奖品已经充值，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $res = $wx->sendCustomMessage($msg);
                }
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';
                     kohana::$log->add('qwtwfb111',print_r(222,true));
                    $tempname=ORM::factory("qwt_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("qwt_wfbitem")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("qwt_wfbqrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                    kohana::$log->add('qwtwfb111',print_r($hbresult,true));
                }
                //Request::instance()->redirect('qwtwfba/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('status', '=', $result['status'])->and_where_open()->where('need_money','=',0)->or_where_open()->where('need_money','>',0)->and_where('order_state','=','1')->or_where_close()->and_where_close();
        // $order = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            $order = $order->and_where_open();
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            if($countuser>0){
                $user = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
                $userarr = array();
                foreach ($user as $k => $v) {
                    $userarr[$k] = $v->id;
                }
                $order = $order->or_where('qid', 'IN', $userarr);
            }
            $order = $order->and_where_close();
            // $order = $order->and_where_open();
            // $result['s'] = $_GET['s'];
            // $s = '%'.trim($_GET['s'].'%');
            // $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            // $order = $order->and_where_close();
        }

        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('qwt_wfbqrcode', $result['qid']);
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
         if ($_GET['type']=="hb") {
            $order = $order->where('type', '=', 4);
            $active_type="hb";
        }

        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
             $tempname="全部";
             // var_dump($_GET);
             // exit();
            switch ($_GET["tag"]) {
                case 'fare':
                    $orders=$order->where('type','=',3)->limit(1000)->find_all();
                    $tempname="充值";
                    break;
                case'object':
                    $orders=$order->where('type','=',NULL)->limit(1000)->find_all();
                    $tempname="实物";
                    break;
                case'code':
                    $orders=$order->where('type','=',5)->limit(1000)->find_all();
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

            $title = array('id','昵称', '收货人', '收货电话', '收货城市', '收货地址', '备注', '兑换产品','金额','消耗积分', '订单时间', '是否有关注', '产品ID', 'OpenID', '是否锁定', '直接粉丝', '间接粉丝', '物流公司', '物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($orders as $o) {
                //$count2 = ORM::factory('qwt_wfbscore')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($o->bid))->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('qwt_wfbscore')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

                //地址处理
                list($prov, $city, $dist) = explode(' ', $o->city);
                $array = array($o->id,$o->user->nickname,$o->name, $o->tel, "{$prov} {$city} {$dist}", $o->address, $o->memo, $o->item->name,$o->item->price, $o->score, date('Y-m-d H:i:s', $o->createdtime), $o->user->subscribe, $o->item->id, $o->user->openid, $o->user->lock, $count2, $count3);

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
        ))->render('weixin/qwt/admin/wfb/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('activetype',$active_type);
    }

    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid);
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            $id=$_GET['id'];
            $item=ORM::factory('qwt_wfbitem')->where('id','=',$id)->find();
            if (ORM::factory('qwt_wfborder')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('qwtwfba/items');
            }else{
                $result['error'] = '已经被兑换过的奖品不能删除，您可以设置为隐藏';
            }
        }
        $result['items'] = ORM::factory('qwt_wfbitem')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all();
        $iid = ORM::factory('qwt_wfbitem')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('qwt_wfborder')->where('bid', '=', $bid)->where('iid','=',$value->id)->and_where_open()->where('need_money','=',0)->or_where_open()->where('need_money','>',0)->and_where('order_state','=','1')->or_where_close()->and_where_close()->count_all();
           //echo $convert[$key].'<br>';
        }

        $this->template->title = '奖品管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid);
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
        // echo "<pre>";
        // var_dump($result);
        // echo "<pre>";
        // exit();
        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信卡券列表';
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
            }elseif ($_POST['data']['type']==0) {
                unset($_POST['data']['url']);
            }
            $item = ORM::factory('qwt_wfbitem');
            $item->values($_POST['data']);

            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['score'] || !$_POST['data']['stock']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
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
                $key = "qwtwfb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtwfba/items');
            }
        }

        $result['action'] = 'add';
        $result['present_id']=$present_id;
        $result['title1']=$title1;
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/items_add')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid);
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
        $item = ORM::factory('qwt_wfbitem', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('qwt_wfborder')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('qwtwfba/items');
            }else{
                $result['error'] = '已经被兑换过的奖品不能删除，您可以设置为隐藏';
            }
        }

        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信卡券列表';
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

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交（请在基础设置-微信参数，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];
                // echo $_FILES['pic']['size'];
                if ($_FILES['pic']['size'] > 1024*300) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
                // echo $result['error'];
                // exit;
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "wfb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtwfba/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        // echo "<pre>";
        // var_dump($_POST['data']);
        // var_dump($result);
        // echo "</pre>";
        // exit();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/items_add')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_wfb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    // public function action_empty() {
    //     if ($_GET['DELETE'] == 1) {
    //         $empty = ORM::factory('qwt_wfbscore')->where('bid', '=', $this->bid);
    //         $empty->delete_all();
    //         DB::update(ORM::factory('qwt_wfbqrcode')->table_name())
    //         ->set(array('score' => '0'))
    //         ->where('bid', '=', $this->bid)
    //         ->execute();
    //         Request::instance()->redirect('qwtwfba//zero');
    //     }
    // }
    public function action_empty() {
        //ignore_user_abort(true);//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(0);
        if ($_GET['DELETE'] == 1) {
            $zero = ORM::factory('qwt_wfbzero')->where('bid', '=', $this->bid)->find();
            $zero->bid = $this->bid;
            $zero->status = 0;
            $zero->lastupdate = time();
            $zero->save();
            // exit;
            Request::instance()->redirect('qwtwfba/zero');
        }
    }
    public function action_cancle() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        if ($_POST['cancle']){
                $cancle = $_POST['cancle'];
                $cfg = ORM::factory('qwt_wfbcfg');
                foreach ($cancle as $k => $v) {
                    $ok=$cfg->setCfg($bid,$k,$v);
                    $result['ok8']+=$ok;
                }
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);

        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/cancle')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_rsync() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('qwt_wfbcfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/rsync')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_hb_check() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('qwt_wfblogin')->where('id', '=', $bid)->find()->access_token;

        if ($_POST['hb_check']){
            $hb_check = $_POST['hb_check'];
            $cfg = ORM::factory('qwt_wfbcfg');
            foreach ($hb_check as $k => $v) {
                $ok=$cfg->setCfg($bid,$k,$v);
                $result['ok8']+=$ok;
            }
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        }

        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/hb_check')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        $this->config=ORM::factory('qwt_wfbcfg')->getCfg($this->bid,1);
        if($this->config['totle']!=date('Y-m-d',time())){
            if($this->config['totle']){
                $time_totle=strtotime($this->config['totle']);
            }else{
                $time_totle=0;
            }
            $daytype='%Y-%m-%d';
            $length=10;
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_wfbqrcodes` where bid=$this->bid and  jointime >= $time_totle ORDER BY `time` DESC ")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_wfbqrcodes where bid=$this->bid and old =0 and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qwt_wfbqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from qwt_wfbqrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' or FROM_UNIXTIME(`jointime`, '$daytype')='$time')")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `qwt_wfborders` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
            foreach ($newadd as $key => $value) {
                $totle=ORM::factory('qwt_wfbtotle')->where('bid','=',$this->bid)->where('time_quantum','=',$value['time'])->find();
                $totle->bid=$this->bid;
                $totle->fans_num=$value['fansnum'];
                $totle->time_quantum=$value['time'];
                $totle->timestamp=strtotime($value['time']);
                $totle->haibao_num=$value['tickets'];
                $totle->qr_num=$value['actnums'];
                $totle->order_num=$value['ordernums'];
                $totle->save();

            }
            $ok=ORM::factory('qwt_wfbcfg')->setCfg($this->bid,'totle',date('Y-m-d',time()));
            $this->config=ORM::factory('qwt_wfbcfg')->getCfg($this->bid,1);
        }else{
            $time_today=strtotime(date('Y-m-d',time()));
            $fnum=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->where('old','=',0)->count_all();
            $tnum=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->where('ticket','!=','')->count_all();
            $qnum=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->and_where_open()->where('jointime','>=',$time_today)->or_where('lastupdate','>=',$time_today)->and_where_close()->count_all();
            $onum=ORM::factory('qwt_wfborder')->where('bid','=',$this->bid)->where('lastupdate','>=',$time_today)->count_all();
            if($fnum>0||$tnum>0||$qnum>0||$onum>0){
                $totle=ORM::factory('qwt_wfbtotle')->where('bid','=',$this->bid)->where('time_quantum','=',date('Y-m-d',time()))->find();
                $totle->bid=$this->bid;
                $totle->fans_num=$fnum;
                $totle->time_quantum=date('Y-m-d',time());
                $totle->timestamp=strtotime(date('Y-m-d',time()));
                $totle->haibao_num=$tnum;
                $totle->qr_num=$qnum;
                $totle->order_num=$onum;
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
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_wfbqrcodes where bid=$this->bid and old =0 and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from qwt_wfbqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];
                //活动参与人数
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `qwt_wfbscores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from qwt_wfbqrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' or FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over')")->execute()->as_array();
                $newadd[0]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `qwt_wfborders` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['ordernums']=$ordernums[0]['ordernum'];

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
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_wfbtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qwt/admin/wfb/pages');
            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_wfbtotles` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"SELECT sum(fans_num) as fansnum from qwt_wfbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $ticket=DB::query(Database::SELECT,"SELECT sum(haibao_num) as tickets from qwt_wfbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"SELECT sum(qr_num) as actnum from qwt_wfbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"SELECT sum(order_num) as ordernum FROM `qwt_wfbtotles` where bid =$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '%Y-%m-%d')as time FROM `qwt_wfbtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/qwt/admin/wfb/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }
    public function action_area() {
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('qwt_wfblogin')->where('id', '=', $bid)->find()->access_token;
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('qwt_wfbcfg');
            // $count = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
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
            if($area['status'] == 1){
                $smfyun = Model::factory('smfyun');
                $res = $smfyun->set_option($this->bid,'location_report',1);
                if($res['errcode'] > 0){
                    $result['error'] = '微信公众号【获取用户地理位置】开关打开失败，错误信息：'.$res['errcode'].$res['errmsg'];
                }
            }
            // echo $area['status'];
            // exit;
            foreach ($area as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok5'] += $ok;
            }
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/area')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_zero() {
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_wfbcfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('qwt_wfblogin')->where('id', '=', $bid)->find()->access_token;
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wfb/zero')
            //->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
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
        kohana::$log->add('qwtwfb111',print_r($bid,true));
        if (!$wx) {
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/qwt.inc');
            //require_once Kohana::find_file('vendor', "weixin/smfyun/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('qwtwfbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,$options);
        }
        $config['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->name;
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $options['appid'];//三方appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        //$data["min_value"] = $money; //最小金额
        //$data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        //$data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name'].""; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        kohana::$log->add('qwtwfb111',print_r($data,true));
        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);//支付安全验证函数（核心函数）
        kohana::$log->add('qwtwfb111',print_r($resultXml,true));
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        kohana::$log->add('qwtwfb111',print_r($result,true));
        return $result;//hash数组
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;
        kohana::$log->add('qwtwfb1111',print_r($config,true));
        kohana::$log->add('qwtwfb1112',print_r($bid,true));
        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."wfb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wfbfile_cert')->find();
        $file_key = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wfbfile_key')->find();
        //$file_rootca = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_wfbfile_rootca')->find();

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

        // if (!file_exists(rootca_file)) {
        //     @mkdir(dirname($rootca_file));
        //     @file_put_contents($rootca_file, $file_rootca->pic);
        // }

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
        kohana::$log->add('qwtwfb1113',print_r($data,true));
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
