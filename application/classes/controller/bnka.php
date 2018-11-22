<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_bnka extends Controller_Base {

    public $template = 'weixin/bnk/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $we;
    public $appId = 'wxd0b3a6ff48335255';
    public $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'zhibo';
    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['bnka']['bid'];
        $this->config = $_SESSION['bnka']['config'];
        $this->access_token=ORM::factory('bnk_login')->where('id', '=', $this->bid)->find()->yz_access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/bnka/login');
            header('location:/bnka/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('bnk_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['flag']=1;
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

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=8b719902ac4d921bdb&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/bnka/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"8b719902ac4d921bdb",
            "client_secret"=>"33054b3b0a38988342879da6231d2d5a",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/bnka/callback'
        );
        require Kohana::find_file("vendor/youzan","YZOauthClient");
        $token = new YZOauthClient( '8b719902ac4d921bdb' , '95065db0a9dcef9bcf455d7a54af8615' );
        $keys = array();
        $type = 'code';//如要刷新access_token，这里的值为refresh_token
        $keys['code'] = $code;//如要刷新access_token，这里为$keys['refresh_token']
        $keys['redirect_uri'] = 'http://'.$_SERVER["HTTP_HOST"].'/bnka/callback';
        $result = $token->getToken( $type , $keys );

        if(isset($result['access_token']))
        {
            require Kohana::find_file("vendor/youzan","YZTokenClient");
            $client = new YZTokenClient($result['access_token']);

            $method = 'youzan.shop.get';//要调用的api名称
            $methodVersion = '3.0.0';//要调用的api版本号

            $params = [

            ];
            $value = $client->post($method, $methodVersion, $params, $files);
            // var_dump($value);
            // exit;
            if($value['response']){
                $sid = $value['response']['id'];
                $name = $value['response']['name'];
                $usershop = ORM::factory('bnk_login')->where('id','=',$this->bid)->find();
                $usershop->logo = $value['response']['logo'];
                $usershop->yz_access_token = $result['access_token'];
                $usershop->yz_expires_in = time()+$result['expires_in'];
                $usershop->yz_refresh_token = $result['refresh_token'];
                $usershop->shopid = $sid;
                $_SESSION['bnka']['sid'] = $sid;
                $usershop->name = $name;
                $usershop->url = 'rtmp://video-center.alivecdn.com/AppName/'.$this->bid.'?vhost=live.smfyun.com';
                // $usershop->url = 'rtmp://video-center.alivecdn.com/AppName/'.$sid.'?vhost='.$usershop->domain.'.smfyun.com';
                $usershop->save();
                echo "<script>alert('授权成功');location.href='".URL::site("bnka/qrcodes")."';</script>";
            }else if($value['error_response']){
                echo "<script>alert('获取店铺信息失败，code：".$value['error_response']['code']."msg：".$value['error_response']['msg']."');</script>";
            }
        }
        //Request::instance()->redirect('bnka/home');
    }
    public function action_lottery_history() {
        $bid = $this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
        $trade = ORM::factory('bnk_trade')->where('bid','=',$bid);
        $trade = $trade->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes = ORM::factory('bnk_qrcode')->where('nickname', 'like', $s)->find_all();
            foreach ($qrcodes as $key => $qrcode) {
                $qids[$key]=$qrcode->id;
            }
            if(!$qids){
                $qids=array(0 => 1);
            }
            $trade=$trade->where('qid','IN',$qids);
        }
        if($_GET['qid']){
            $trade=$trade->where('qid','=',$_GET['qid']);
        }
        $result['countall'] = $countall = $trade->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/bnk/admin/pages');
        $result['sort'] = 'lastupdate';
        if ($result['sort']) $trade = $trade->order_by($result['sort'], 'DESC');
        $result['trade'] = $trade->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '领取记录';
        $this->template->content = View::factory('weixin/bnk/admin/lottery_history')
                    ->bind('pages', $pages)
                    ->bind('result', $result)
                    ->bind('config', $config);
    }
    public function action_rsync() {
        $bid = $this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
        if($_POST['rsync']['send_self']){
            $ok=ORM::factory('bnk_cfg')->setCfg($bid,'send_self',$_POST['rsync']['send_self']);
            if($ok){
                $result['ok']=1;
                $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
            }
        }
        $this->template->title = '提现开关';
        $this->template->content = View::factory('weixin/bnk/admin/rsync')
            ->bind('result',$result)
            ->bind('config',$config);
    }
    // public function action_orders() {
    //     $bid = $this->bid;
    //     $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
    //     $this->template->title = '提现开关';
    //     $this->template->content = View::factory('weixin/bnk/admin/orders');
    // }
    //会员中心-购买记录
    public function action_buymentrecord() {
        $bid = $this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
        $this->template->title = '收支明细';
        $scores = ORM::factory('bnk_score')->where('bid','=',$bid);
        $scores = $scores->reset(FALSE);
        if($_GET['qid']){
            $qid=$_GET['qid'];
            $all_score=DB::query(Database::SELECT,"SELECT SUM(score) as all_score from bnk_scores where qid =$qid")->execute()->as_array();
            $all_score=$all_score[0]['all_score'];
            $qrcode=ORM::factory('bnk_qrcode')->where('id','=',$qid)->find();
            $qrcode->score=$all_score;
            $qrcode->save();
            $scores=$scores->where('qid','=',$_GET['qid']);
        }
        if($_GET['type']){
            $scores=$scores->where('type','=',$_GET['type']);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes = ORM::factory('bnk_qrcode')->where('nickname', 'like', $s)->find_all();
            foreach ($qrcodes as $key => $qrcode) {
                $qids[$key]=$qrcode->id;
            }
            if(!$qids){
                $qids=array(0 => 1);
            }
            $scores=$scores->where('qid','IN',$qids);
        }
        if($_GET['data']['begin']){
            $begin=strtotime($_GET['data']['begin']);
            $scores=$scores->where('createdtime','>=',$begin);
        }
        if($_GET['data']['over']){
            $over=strtotime($_GET['data']['over']);
            $scores=$scores->where('createdtime','<=',$over);
        }
        $result['countall'] = $countall = $scores->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/bnk/admin/pages');

        $result['scores'] = $scores->order_by('createdtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/bnk/admin/buymentrecord')->bind('pages',$pages)->bind('result',$result);
    }
    public function action_orders() {
        $bid = $this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
        $this->template->title = '提现申请';
        $score1s = ORM::factory('bnk_score')->where('bid','=',$bid)->where('type','=',5)->where('flag','=',2);
        $score1s = $score1s->reset(FALSE);
        if($_POST['qid']&&$_POST['sid']){
            $this_score=ORM::factory('bnk_score')->where('id','=',$_POST['sid'])->find();
            if($this_score->id&&$this_score->flag==2){
                $result=$this->action_sendMoney($_POST['qid'],$_POST['sid']);
                if($result['result_code']=='SUCCESS'){
                    $this_score->flag=1;
                    $this_score->save();
                    $result['ok']='发送成功!';
                }else{
                    $result['ok']=$result['err_code_des'];
                }
            }else{
                $result['ok']='次记录不存在';
            }
            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';
            // exit();
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes = ORM::factory('bnk_qrcode')->where('nickname', 'like', $s)->find_all();
            foreach ($qrcodes as $key => $qrcode) {
                $qids[$key]=$qrcode->id;
            }
            if(!$qids){
                $qids=array(0 => 1);
            }
            $score1s=$score1s->where('qid','IN',$qids);
        }
        $result['countall'] = $countall = $score1s->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/bnk/admin/pages');

        $result['scores'] = $score1s->order_by('createdtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/bnk/admin/orders')->bind('pages',$pages)->bind('result',$result);
    }
    //会员中心-购买记录
    public function action_score() {
        $bid = $this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid, 1);
        $this->template->title = '提现记录';
        $score1s = ORM::factory('bnk_score')->where('bid','=',$bid)->where('type','=',5)->where('flag','=',1);
        $score1s = $score1s->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes = ORM::factory('bnk_qrcode')->where('nickname', 'like', $s)->find_all();
            foreach ($qrcodes as $key => $qrcode) {
                $qids[$key]=$qrcode->id;
            }
            if(!$qids){
                $qids=array(0 => 1);
            }
            $score1s=$score1s->where('qid','IN',$qids);
        }
        $result['countall'] = $countall = $score1s->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/bnk/admin/pages');

        $result['scores'] = $score1s->order_by('createdtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/bnk/admin/score')->bind('pages',$pages)->bind('result',$result);
    }
    //发送记录
    public function action_analyze($action='', $id=0) {
        $bid=$this->bid;

        $orders = ORM::factory('bnk_order')->where('bid','=',$bid);
        $orders = $orders->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes = ORM::factory('bnk_qrcode')->where('nickname', 'like', $s)->find_all();
            foreach ($qrcodes as $key => $qrcode) {
                $qids[$key]=$qrcode->id;
            }
            if(!$qids){
                $qids=array(0 => 1);
            }
            $orders=$orders->where('qid','IN',$qids);
        }
        if($_GET['qid']){
            $orders=$orders->where('qid','=',$_GET['qid']);
        }
        if($_POST['form']){
            $oid=$_POST['form']['id'];
            $status=$_POST['form']['status'];
            $order=ORM::factory('bnk_order')->where('id','=',$oid)->find();
            if($order->id&&$status){
                $order->status=$status;
                if($status==4){
                    $order->used_fee=ceil($order->used_money*100*0.02)/100;
                    $order->return_money=$order->all_money-$order->used_money-$order->used_fee;
                }
                $order->save();
                if($status==4){
                    $order->qrcode->scores->scoreIn($order->qrcode,4,$order->return_money,$order->id);
                    $result1=$this->bnktest($order->qrcode->openid,$order->form_id,$order->return_money.'元');
                    Kohana::$log->add("abnk$order->id", print_r($result1,true));
                }
            }
        }
        $result['countall'] = $countall = $orders->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/bnk/admin/pages');

        //if ($result['sort']) $orders = $orders->order_by('start_time', $result['sort']);
        $sort='lastupdate';
        if($_GET['sort']) $sort='ts_num';
        $result['order'] = $orders->order_by($sort, 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '发送记录';
        $this->template->content = View::factory('weixin/bnk/admin/analyze')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function bnktest($openid,$form_id,$keyword1){
        Kohana::$log->add("4bnk$openid", print_r($openid,true));
        Kohana::$log->add("5bnk$openid", print_r($form_id,true));
        Kohana::$log->add("6bnk$openid", print_r($keyword1,true));
        $wx['appid']='wxbc550991f98c2c7b';
        $wx['appsecret']='f5fb50132783f898ceb3aaef37c5bc2d';
        $template_id='6BmI_tPKfGj4NIQks-Lem-PqbvQ8ONZ1hKkCFD1jVmg';
        $openid=$openid;
        $form_id=$form_id;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we=new wechat($wx);
        $tplmsg['touser']=$openid;
        $tplmsg['template_id']=$template_id;
        $tplmsg['page']='pages/cash/index';
        $tplmsg['form_id']=$form_id;
        $tplmsg['data']['keyword1']['value']=$keyword1;
        $tplmsg['data']['keyword1']['color']='#173177';
        $tplmsg['data']['keyword2']['value']='活动已被终止';
        $tplmsg['data']['keyword2']['color']='#173177';
        $tplmsg['data']['keyword3']['value']='小程序账户余额';
        $tplmsg['data']['keyword3']['color']='#173177';
        $tplmsg['data']['keyword4']['value']='点击此处查看账户余额并进行提现';
        $tplmsg['data']['keyword4']['color']='#173177';
        Kohana::$log->add("abnk$openid", print_r($tplmsg,true));
        $result=$we->sendXcxTpl($tplmsg);
        Kohana::$log->add("abnk$openid", print_r($result,true));
        return $result;
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        //修改用户
        // if ($_POST['form']['id']) {
        //     $id = $_POST['form']['id'];
        //     $qrcode_edit = ORM::factory('bnk_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
        //     if ($qrcode_edit->id) {
        //         $qrcode_edit->save();
        //     }
        // }
        $qrcode = ORM::factory('bnk_qrcode')->where('bid', '=', $bid);
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
        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/bnk/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/bnk/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_general($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('bnk_cfg')->getCfg($bid);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        // $qrcode = ORM::factory('bnk_qrcode')->where('bid', '=', $bid);
        // $qrcode = $qrcode->reset(FALSE);
        // if ($_GET['s']) {
        //     $result['s'] = $_GET['s'];
        //     $s = '%'.trim($_GET['s'].'%');
        //     $qrcode = $qrcode->where('nickname', 'like', $s);
        // }
        // if ($_GET['id']) {
        //     $result['id'] = (int)$_GET['id'];
        //     $qrcode = $qrcode->where('id', '=', $result['id']);
        // }
        //$result['countall'] = $countall = $qrcode->count_all();

        //分页
        // $page = max($_GET['page'], 1);
        // $offset = ($this->pagesize * ($page - 1));
        // $pages = Pagination::factory(array(
        //     'total_items'   => $countall,
        //     'items_per_page'=> $this->pagesize,
        // ))->render('weixin/bnk/admin/pages');
        //if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        //$result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '概况';
        $this->template->content = View::factory('weixin/bnk/admin/general')
            ->bind('config', $config);
    }
    public function action_login() {
        $this->template = 'weixin/bnk/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('bnk_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['bnka']['bid'] = $biz->id;
                    $_SESSION['bnka']['sid'] = $biz->shopid;
                    $_SESSION['bnka']['user'] = $_POST['username'];
                    $_SESSION['bnka']['admin'] = $biz->admin; //超管
                    $_SESSION['bnka']['config'] = ORM::factory('bnk_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['bnka']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'qrcodes';
            header('location:/bnka/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['bnka'] = null;
        header('location:/bnka/qrcodes');
        exit;
    }
    //产品图片
    public function action_dbimages() {
        $a=  DOCROOT."wdy/news_score.jpg";
        $pic=file_get_contents($a);
        if (!$pic) die('404 Not Found!');
        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'image';
        $table = "bnk_$type";
        $pic = ORM::factory($table, $id)->image;
        if (!$pic) die('404 Not Found!');
        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
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
    public function action_sendMoney($qid,$sid) {
        //$openid= $_GET['openid'];
        //$money1= $_GET['money'];
        $qrcode=ORM::factory('bnk_qrcode')->where('id','=',$qid)->find();
        //$qid=$qrcode->id;
        $time=time();
        $money1=ORM::factory('bnk_score')->where('id','=',$sid)->find()->score;
        $money=-100*$money1;
        //$openid='oOkHq0HqPpbn_xS6tzssaGW8TQpI';
        $wx['appid']='wxbc550991f98c2c7b';
        $wx['appsecret']='f5fb50132783f898ceb3aaef37c5bc2d';
        $key='vdY25BlR1U58kBDiuJ1DFHPgldXnOkD6';
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($wx);
        $mch_id='1229635702';
        $mch_billno = $mch_id. date('YmdHis').rand(1000, 9999); //订单号
        $data["mch_appid"] = $wx['appid'];
        $data["mchid"] = $mch_id; //商户号
        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号
        $data["openid"] = $qrcode->openid;
        $data["re_user_name"] = $qrcode->nickName;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名
        $data["amount"] = $money;
        $data["desc"] = '【看样子小程序】账户余额提现';
        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $key));

        $postXml = $we->xml_encode($data);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        // Kohana::$log->add('weixin_fxb:hongbaopost', print_r($data, true));
        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        // echo "<pre>";
        // var_dump($response);
        // echo "</pre>";
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['err_code_des'] = (string)$response->err_code_des[0];
        // echo "<pre>";
        // var_dump($result);
        // echo "</pre>";
        // exit();
        // if($result['result_code']=='SUCCESS'){
        //   ORM::factory('bnk_score')->scoreOut($qrcode,5,$money1,0,0,1);
        //   // ORM::factory('yjb_score')->scoreOut($qrcode,4,$fee);
        //   $result1['state']='SUCCESS';
        //   $result1['code']=$money1;
        //   echo json_encode($result1);
        // }else{
        //   $result1['state']='FAIL';
        //   $result1['code']=$result['err_code_des'];
        //   echo json_encode($result1);
        // }
        // exit();
        return $result;
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        // $config = $this->config;
        // $bid = $this->bid;
        $cert_file = DOCROOT."bnk/cert/apiclient_cert.pem";
        $key_file = DOCROOT."bnk/cert/apiclient_key.pem";
        //证书分布式异步更新
        // $file_cert = ORM::factory('fxb_cfg')->where('bid', '=', $bid)->where('key', '=', 'fxb_file_cert')->find();
        // $file_key = ORM::factory('fxb_cfg')->where('bid', '=', $bid)->where('key', '=', 'fxb_file_key')->find();

        // if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        // if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        // if (!file_exists($cert_file)) {
        //     @mkdir(dirname($cert_file));
        //     @file_put_contents($cert_file, $file_cert->pic);
        // }

        // if (!file_exists($key_file)) {
        //     @mkdir(dirname($key_file));
        //     @file_put_contents($key_file, $file_key->pic);
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
