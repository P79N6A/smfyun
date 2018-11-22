<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Yyba extends Controller_Base {

    public $template = 'weixin/yyb/tpl/atpl';
    public $pagesize = 10;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "yyb";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'images') return;
        $this->bid = $_SESSION['yyba']['bid'];
        $this->config = $_SESSION['yyba']['config'];
        $this->access_token=ORM::factory('yyb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/yyba/login');
            header('location:/yyba/login?from='.Request::instance()->action);
            exit;
        }
    }
    public function after() {
        if ($this->bid) {
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
    public function action_get_token(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        $we = new Wechat($config);
        $res = $we->refresh_token('wxed32d14f1779ad46','7f1e7d48a13d80cafa665d0a48dc47b4');
        var_dump($res);
        exit;
    }

    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('yyb_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('yyb_login', $bid);
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
            $cfg = ORM::factory('yyb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }
            //二维码海报
            if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*100) {
                    $result['err'] = '二维码图片不能超过 100K';
                } else {
                    $result['ok']++;
                    $cfg->setCfg($bid, '2dimage', '', file_get_contents($_FILES['pic']['tmp_name']));
                }
            }
            $Toname = ORM::factory('yyb_login')->where("id","=",$bid)->find()->user;
            //重新读取配置
            $config = ORM::factory('yyb_cfg')->getCfg($bid, 1);
        }
        $result['2dimage'] = ORM::factory('yyb_cfg')->where('bid', '=', $bid)->where('key', '=', '2dimage')->find()->id;
        $result['expiretime'] = ORM::factory('yyb_login')->where('id', '=', $bid)->find()->expiretime;
        $access_token = ORM::factory('yyb_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yyb/admin/home')
            ->bind('oauth',$oauth)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
       //有赞授权刷新脚本 七天一次
    public function action_oauthscript($bid=39){
        $shop = ORM::factory('yyb_login')->where('id','=',$bid)->find();
        $url="https://open.youzan.com/oauth/token";
        if($shop->access_token&&$shop->id){
            $data=array(
                "client_id"=>"db138abb0214ec1d48",
                "client_secret"=>"1c7734762d70d1566023ed7d49738eae",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            $shop->access_token = $result->access_token;
            $shop->expires_in = time()+$result->expires_in;
            $shop->refresh_token = $result->refresh_token;
            $shop->save();
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 成功';
            echo '</pre>';
            exit;
        }else{
            die('no id or no access_token!');
        }
    }
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=db138abb0214ec1d48&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/yyba/callback');
    }
     //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"db138abb0214ec1d48",
            "client_secret"=>"1c7734762d70d1566023ed7d49738eae",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/yyba/callback'
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
            require Kohana::find_file("vendor","kdt/YZTokenClient");
            $oauth=new YZTokenClient($result->access_token);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['id'];
            $name = $value['name'];

            $usershop = ORM::factory('yyb_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("yyba/home")."';</script>";
        }
        //Request::instance()->redirect('kmia/home');
    }
    public function action_orders(){
        $bid = $this->bid;
        $order=ORM::factory('yyb_order')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if($_GET['flag']=='delete'&&$_GET['oid']){
            ORM::factory('yyb_order')->where('id', '=', $_GET['oid'])->delete_all();
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('title', 'like', $s);
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yyb/admin/pages');

        $result['orders'] = $order->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yyb/admin/orders')
            ->bind('result', $result)
            ->bind('pages',$pages)
            ->bind('countall',$countall)
            ->bind('bid',$bid);
    }
    public function action_rsync() {
        $bid = $this->bid;
        $config = ORM::factory('yyb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('yyb_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['rsync']['switch']){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('yyb_cfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('yyb_cfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/rsync')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_recodes(){
        $bid = $this->bid;
        // $orders=DB::query(Database::SELECT,"SELECT * FROM yyb_orders Where `bid` = $bid and `state`=1")->execute()->as_array();
        // $tempid=array();
        // if($orders[0]['id']==null)
        // {
        //   $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        // }
        // else
        // {
        //   for($i=0;$orders[$i];$i++)
        //   {
        //     $tempid[$i]=$orders[$i]['id'];
        //   }
        // }
        // $items = ORM::factory('yyb_item')->where('bid', '=', $bid)->where('oid','IN',$tempid);
        $items = ORM::factory('yyb_item')->where('bid', '=', $bid)->where('flag','=',1);
        $items = $items->reset(FALSE);
        // if ($_GET['s']) {
        //     $items = $items->and_where_open();
        //     $result['s'] = $_GET['s'];
        //     $s = '%'.trim($_GET['s'].'%');
        //     $items = $items->where('item', 'like', $s);
        //     $items = $items->and_where_close();
        // }
        $countall=$items->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yyb/admin/pages');

        $result['user'] = $items->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yyb/admin/recodes')
            ->bind('result', $result)
            ->bind('countall',$countall)
            ->bind('pages',$pages)
            ->bind('bid',$bid);
    }
    public function action_play($oid){
        $bid = $this->bid;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        $we=new Wechat($config);
        $mbtpl = $config['mbtpl'];
        $url=ORM::factory('yyb_order')->where('id','=',$oid)->find()->url;
        $title=ORM::factory('yyb_order')->where('id','=',$oid)->find()->title;
        $item3=ORM::factory('yyb_order')->where('id','=',$oid)->find()->item;
        $content=ORM::factory('yyb_order')->where('id','=',$oid)->find()->content;
        $time=ORM::factory('yyb_order')->where('id','=',$oid)->find()->time;
        $flag=ORM::factory('yyb_order')->where('id','=',$oid)->find()->flag;
        if($flag==1){
            $items=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$oid)->find_all();
            foreach ($items as $item) {
                $qid=$item->qid;
                //echo $qid."<br>";
                $iid=$item->id;
                //echo $iid."<br>";
                $openid=ORM::factory('yyb_qrcode')->where('id','=',$qid)->find()->openid;
                $nickname =ORM::factory('yyb_qrcode')->where('id','=',$qid)->find()->nickname;
                $tplmsg['template_id'] = $mbtpl;
                $tplmsg['touser'] = $openid;
                $tplmsg['url'] = $url;
                $tplmsg['data']['first']['value'] = $title;
                $tplmsg['data']['first']['color'] = '#FF0000';
                $tplmsg['data']['keyword3']['value'] = $nickname;
                $tplmsg['data']['keyword1']['value'] = $item3;
                $tplmsg['data']['keyword1']['color'] = '#FF0000';
                $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',$time);
                $tplmsg['data']['remark']['value'] = $content;
                $tplmsg['data']['remark']['color'] = '#666666';
                $result=$we->sendTemplateMessage($tplmsg);
            }
        }elseif($flag==0){
            $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->find_all();
            foreach ($qrcodes as $qrcode) {
                $qid=$qrcode->id;
                $openid=$qrcode->openid;
                $nickname =$qrcode->nickname;
                $tplmsg['template_id'] = $mbtpl;
                $tplmsg['touser'] = $openid;
                $tplmsg['url'] = $url;
                $tplmsg['data']['first']['value'] = $title;
                $tplmsg['data']['first']['color'] = '#FF0000';
                $tplmsg['data']['keyword3']['value'] = $nickname;
                $tplmsg['data']['keyword1']['value'] = $item3;
                $tplmsg['data']['keyword1']['color'] = '#FF0000';
                $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',$time);
                $tplmsg['data']['remark']['value'] = $content;
                $tplmsg['data']['remark']['color'] = '#666666';
                $result=$we->sendTemplateMessage($tplmsg);
            }
        }
        Request::instance()->redirect('yyba/orders');
    }
    public function action_orders_edit($oid){
        $bid = $this->bid;
        $config = ORM::factory('yyb_cfg')->getCfg($bid,1);
        $order = ORM::factory('yyb_order')->where('bid', '=', $bid)->where('id','=',$oid)->find();
        $this->access_token=$access_token=ORM::factory('yyb_login')->where('id','=',$bid)->find()->access_token;
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we=new Wechat($config);
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];
        }
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        if ($_POST['data']){
            //$orders=ORM::factory('yyb_order');
            if($_POST['data']['title']&&$_POST['data']['content']){
                if($_POST['ordertype']==1){
                    $order->url=$_POST['data']['url'];
                }elseif($_POST['ordertype']==2){
                    $order->url=$_POST['yzcode'];
                }elseif($_POST['ordertype']==3){
                    $order->url=$_POST['yzgift'];
                }
                $order->bid=$bid;
                $order->time=strtotime($_POST['data']['expiretime']);
                $order->title=$_POST['data']['title'];
                // $order->item=$_POST['data']['item'];
                $order->flag=$_POST['orderflag'];
                $order->type=$_POST['ordertype'];
                $order->way=$_POST['orderway'];
                $order->content=$_POST['data']['content'];
                // $order->url=$_POST['data']['url'];
                if($order->save()){
                    $ok=1;
                    Request::instance()->redirect('yyba/orders');
                }
            }else{
                $result['error']='请填写完整后再提交!';
            }

        }
        $action1=2;
        $title=$this->template->title = '修改群发';
        $this->template->content = View::factory('weixin/yyb/admin/orders_add')
            ->bind('action1', $action1)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('order',$order)
            ->bind('oid',$oid)
            ->bind('bid',$bid)
            ->bind('title',$title)
            ->bind('ok',$ok);
    }
    public function action_orders_add(){
        $bid = $this->bid;
        $config = ORM::factory('yyb_cfg')->getCfg($bid);
        $this->access_token=$access_token=ORM::factory('yyb_login')->where('id','=',$bid)->find()->access_token;
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we=new Wechat($config);
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];
        }
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        if ($_POST['data']){
            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';
            // exit();
            if($_POST['orderway']==0&&time()>strtotime($_POST['data']['expiretime'])){
                $result['error']='预约时间不合理!';
            }elseif($_POST['data']['title']&&$_POST['data']['content']){
                $orders=ORM::factory('yyb_order');
                $orders->bid=$bid;
                if($_POST['ordertype']==1){
                    $orders->url=$_POST['data']['url'];
                }elseif($_POST['ordertype']==2){
                    $orders->url=$_POST['yzcode'];
                }elseif($_POST['ordertype']==3){
                    $orders->url=$_POST['yzgift'];
                }
                $orders->time=strtotime($_POST['data']['expiretime']);
                $orders->title=$_POST['data']['title'];
                $orders->flag=$_POST['orderflag'];
                $orders->way=$_POST['orderway'];
                $orders->type=$_POST['ordertype'];
                $orders->content=$_POST['data']['content'];
                $orders->save();
                Request::instance()->redirect('yyba/orders');
            }else{
                $result['error']='请填写完整后再提交!';
            }
        }
        if($_GET){
            Kohana::$log->add('get',print_r($_GET,true));
            $time=time();
            $num=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('admin','=',1)->count_all();
            if($num==0) $returnmsg='请先绑定预览用户';
            $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('admin','=',1)->find_all();
            foreach ($qrcodes as $qrcode) {
                if($_GET['typev']==1){
                    $url=$_GET['url'];
                }elseif($_GET['typev']==2){
                    $url=$_SERVER["HTTP_HOST"].'/yyb/yzcode?url='.$_GET['yzcodev'].'&admin=1&openid='.$qrcode->openid.'&bid='.$bid;
                }elseif($_GET['typev']==3){
                    $url=$_SERVER["HTTP_HOST"].'/yyb/yzgift?url='.$_GET['yzgiftv'].'&admin=1&openid='.$qrcode->openid.'&bid='.$bid;
                }
                $tplmsg['template_id'] = $config['mbtpl'];
                $tplmsg['touser'] = $qrcode->openid;
                $tplmsg['url'] = $url;
                $tplmsg['data']['first']['value'] = $_GET['title'];
                $tplmsg['data']['first']['color'] = '#FF0000';
                $tplmsg['data']['keyword1']['value'] = $qrcode->nickname;
                $tplmsg['data']['keyword3']['value'] = '预约通知';
                // $tplmsg['data']['keyword1']['color'] = '#FF0000';
                $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',time());
                $tplmsg['data']['remark']['value'] = $_GET['content'];
                $tplmsg['data']['remark']['color'] = '#666666';
                Kohana::$log->add('tplmsg',print_r($tplmsg,true));
                $result=$we->sendTemplateMessage($tplmsg);
                Kohana::$log->add('result',print_r($result,true));
                if($result['errmsg']=='ok'){
                    $returnmsg='发送成功！';
                }else{
                    $returnmsg='发送失败！';
                }
            }
            echo $returnmsg;
            exit();
        }
        $action1=1;
        $title=$this->template->title = '新建群发';
        $this->template->content = View::factory('weixin/yyb/admin/orders_add')
            ->bind('action1', $action1)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('title', $title)
            ->bind('bid',$bid);

    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['yyba']['admin'] < 1) Request::instance()->redirect('yyba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('yyb_login');
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
        ))->render('weixin/yyb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/yyb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['yyba']['admin'] < 2) Request::instance()->redirect('yyba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('yyb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('yyb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('yyba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/yyb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_test(){
        $stime=microtime(true);
        $bid =1;
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we=new Wechat($config);
        $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->limit(100)->find_all();
        $url='www.baidu.com';
        $openid='oXYBfwId3Wh23y5cWNqwplAkLVdk';
        for ($i=0; $i <100 ; $i++) {
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = '测试';
            $result=$we->sendCustomMessage($msg);
            // $tplmsg['template_id'] = '6jBk_0RPs8aw6WvRoRGUcgnCCY7rmGC_VCjRTit7zjQ';
            // $tplmsg['touser'] = $openid;
            // $tplmsg['url'] = $url;
            // $tplmsg['data']['first']['value'] = '标题';
            // $tplmsg['data']['first']['color'] = '#FF0000';
            // $tplmsg['data']['keyword1']['value'] = $qrcode->nickname;
            // $tplmsg['data']['keyword3']['value'] = '预约通知';
            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',$time);
            // $tplmsg['data']['remark']['value'] = '内容';
            // $tplmsg['data']['remark']['color'] = '#666666';
            // $result=$we->sendTemplateMessage($tplmsg);
        }
        // foreach ($qrcodes as $qrcode) {
        //     // $msg['touser'] = $qrcode->openid;
        //     // $msg['msgtype'] = 'text';
        //     // $msg['text']['content'] = '测试';
        //     $tplmsg['template_id'] = '9wLEQGgDaZEKFwNcrmigIlbC5FlmVLF5PnCVm23qSQQ';
        //     $tplmsg['touser'] = $qrcode->openid;
        //     $tplmsg['url'] = $url;
        //     $tplmsg['data']['first']['value'] = '标题';
        //     $tplmsg['data']['first']['color'] = '#FF0000';
        //     $tplmsg['data']['keyword1']['value'] = $qrcode->nickname;
        //     $tplmsg['data']['keyword3']['value'] = '预约通知';
        //     $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',$time);
        //     $tplmsg['data']['remark']['value'] = '内容';
        //     $tplmsg['data']['remark']['color'] = '#666666';
        //     $result=$we->sendTemplateMessage($tplmsg);
        // }
        var_dump($result);
        $etime=microtime(true);
        $total=$etime-$stime;   //计算差值
        echo "<br />[页面执行时间：{$total} ]秒";
        exit();
    }
    public function action_url(){
        $bid = $this->bid;
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        $result['title'] = $this->template->title = '预约链接';
        $this->template->content = View::factory('weixin/yyb/admin/url')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_qrcode(){
        $bid = $this->bid;
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        if($_GET['refresh']==1){
            $admin=ORM::factory('yyb_login')->where('id','=',$bid)->find();
            $admin->qrcron=0;
            $admin->save();
            ORM::factory('yyb_cfg')->where('bid','=',$bid)->where('key','=','next_openid')->delete_all();
        }
        // if($_GET['flag']==1){
        //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        //     $we=new Wechat($config);
        //     $next_openid=$config['next_openid'];
        //     $result=$we->getUserList($next_openid);
        //     $total=$result['total'];
        //     $count=$result['count'];
        //     if($count>0){
        //         $openids=$result['data']['openid'];
        //         foreach ($openids as $openid) {
        //             $qrcode=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        //             $qrcode->bid=$bid;
        //             $qrcode->openid=$openid;
        //             $qrcode->save();
        //         }
        //         $next_openid=$result['next_openid'];
        //         $ok=ORM::factory('yyb_cfg')->setCfg($bid,'next_openid',$next_openid);
        //         $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        //     }
        //     // for ($count=1,$next_openid=''; $count!=0;$next_openid=$result['next_openid']) {
        //     // }
        // }
        $number1=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('flag','=',1)->count_all();
        $number2=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->count_all();
        $qrcron=ORM::factory('yyb_login')->where('id','=',$bid)->find()->qrcron;
        $result['title'] = $this->template->title = '预约链接';
        $this->template->content = View::factory('weixin/yyb/admin/qrcode')
            ->bind('reset(array)', $result)
            ->bind('number1', $number1)
            ->bind('number2', $number2)
            ->bind('qrcron',$qrcron)
            ->bind('config', $config);
    }
    public function action_logins_edit($id) {
        if ($_SESSION['yyba']['admin'] < 2) Request::instance()->redirect('yyba/home');

        $bid = $this->bid;

        $login = ORM::factory('yyb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('yyb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('yyba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('yyb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                if ($_POST['data']['copyright']) {
                    $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
                }
                //appid 重置
                // if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                // }

                Request::instance()->redirect('yyba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/yyb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/yyb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('yyb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('yyb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['yyba']['bid'] = $biz->id;
                    $_SESSION['yyba']['user'] = $_POST['username'];
                    $_SESSION['yyba']['admin'] = $biz->admin; //超管
                    $_SESSION['yyba']['config'] = ORM::factory('yyb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['yyba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/yyba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['yyba'] = null;
        header('location:/yyba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "yyb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
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
