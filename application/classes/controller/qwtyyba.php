<?php defined('SYSPATH') or die('No direct script access.');

class Controller_QwtYyba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/yybatpl';
    public $pagesize = 10;
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
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',10)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_yybcfg')->getCfg($bid, 1);
        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('qwt_yybcfg');
            foreach ($_POST['cfg'] as $k=>$v) {
                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }
            //二维码海报
            // if ($_FILES['pic']['error'] == 0) {
            //     if ($_FILES['pic']['size'] > 1024*100) {
            //         $result['err'] = '二维码图片不能超过 100K';
            //     } else {
            //         $result['ok']++;
            //         $cfg->setCfg($bid, '2dimage', '', file_get_contents($_FILES['pic']['tmp_name']));
            //     }
            // }
            $Toname = ORM::factory('Qwt_login')->where("id","=",$bid)->find()->user;
            //重新读取配置
            $config = ORM::factory('qwt_yybcfg')->getCfg($bid, 1);
        }
        $result['2dimage'] = ORM::factory('qwt_yybcfg')->where('bid', '=', $bid)->where('key', '=', '2dimage')->find()->id;
        $yzaccess_token = ORM::factory('Qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;

        if(!$yzaccess_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        // $this->template->content = View::factory('weixin/yyb/admin/home')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/home')
            ->bind('oauth',$oauth)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_appointment(){
        $bid = $this->bid;
        $appointment=ORM::factory('qwt_yybappointment')->where('bid', '=', $bid);
        $appointment = $appointment->reset(FALSE);
        if($_POST['appointment']){
            $id=$_POST['appointment']['id'];
            $appointment=ORM::factory('qwt_yybappointment')->where('id','=',$id)->find();
            $appointment->name=$_POST['appointment']['name'];
            $appointment->save();
        }
        if($_GET['flag']=='delete'&&$_GET['aid']){
            ORM::factory('qwt_yybappointment')->where('id', '=', $_GET['aid'])->find()->delete();
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $appointment = $appointment->where('name', 'like', $s);
        }
        $countall = $appointment->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yyb/admin/pages');

        $result['appointments'] = $appointment->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '首页';
        //$this->template->content = View::factory('weixin/yyb/admin/orders')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/appointment')
            ->bind('result', $result)
            ->bind('pages',$pages)
            ->bind('countall',$countall)
            ->bind('bid',$bid);
    }
    public function action_appointment_add(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        if ($_POST['data']){
            $appointment=ORM::factory('qwt_yybappointment');
            $appointment->bid=$bid;
            $appointment->name=$_POST['data']['name'];
            $appointment->save();
            Request::instance()->redirect('qwtyyba/appointment');
        }
        $title=$this->template->title = '新建预约分组';
        $result['title']='新建预约分组';
        //$this->template->content = View::factory('weixin/yyb/admin/orders_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/appointment_add')
            ->bind('result', $result)
            ->bind('bid',$bid);

    }
    public function action_orders(){
        $bid = $this->bid;
        $order=ORM::factory('qwt_yyborder')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if($_GET['flag']=='delete'&&$_GET['oid']){
            ORM::factory('qwt_yyborder')->where('id', '=', $_GET['oid'])->delete_all();
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('title', 'like', $s);
        }
        if($_GET['sendoid']){
            $sendorder=ORM::factory('qwt_yyborder')->where('id','=',$_GET['sendoid'])->find();
            $sendorder->ifsend=1;
            $sendorder->save();
        }
        if($_GET['deleteoid']){
            $deleteorder=ORM::factory('qwt_yyborder')->where('id','=',$_GET['deleteoid'])->find();
            if($deleteorder->way==1||$deleteorder->time>time()){
                $deleteorder->delete();
            }
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
        //$this->template->content = View::factory('weixin/yyb/admin/orders')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/orders')
            ->bind('result', $result)
            ->bind('pages',$pages)
            ->bind('countall',$countall)
            ->bind('bid',$bid);
    }
    public function action_recodes(){
        $bid = $this->bid;
        $items = ORM::factory('qwt_yybitem')->where('bid', '=', $bid)->where('flag','=',1);
        $items = $items->reset(FALSE);
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
        //$this->template->content = View::factory('weixin/yyb/admin/recodes')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/recodes')
            ->bind('result', $result)
            ->bind('countall',$countall)
            ->bind('pages',$pages)
            ->bind('bid',$bid);
    }
    public function action_play($oid){
        $bid = $this->bid;
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        $mbtpl = $config['mbtpl'];
        $url=ORM::factory('qwt_yyborder')->where('id','=',$oid)->find()->url;
        $title=ORM::factory('qwt_yyborder')->where('id','=',$oid)->find()->title;
        $item3=ORM::factory('qwt_yyborder')->where('id','=',$oid)->find()->item;
        $content=ORM::factory('qwt_yyborder')->where('id','=',$oid)->find()->content;
        $time=ORM::factory('qwt_yyborder')->where('id','=',$oid)->find()->time;
        $flag=ORM::factory('qwt_yyborder')->where('id','=',$oid)->find()->flag;
        if($flag==1){
            $items=ORM::factory('qwt_yybitem')->where('bid','=',$bid)->where('oid','=',$oid)->find_all();
            foreach ($items as $item) {
                $qid=$item->qid;
                //echo $qid."<br>";
                $iid=$item->id;
                //echo $iid."<br>";
                $openid=ORM::factory('qwt_yybqrcode')->where('id','=',$qid)->find()->openid;
                $nickname =ORM::factory('qwt_yybqrcode')->where('id','=',$qid)->find()->nickname;
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
                $result=$wx->sendTemplateMessage($tplmsg);
            }
        }elseif($flag==0){
            $qrcodes=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->find_all();
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
                $result=$wx->sendTemplateMessage($tplmsg);
            }
        }
        Request::instance()->redirect('qwtyyba/orders');
    }
    public function action_orders_edit($oid){
        $bid = $this->bid;
        $config = ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        $order = ORM::factory('qwt_yyborder')->where('bid', '=', $bid)->where('id','=',$oid)->find();
        $this->yzaccess_token=$yzaccess_token=ORM::factory('Qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        // $we=new Wechat($config);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];
        }
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        if ($_POST['data']){
            //$orders=ORM::factory('qwt_yyborder');
            if($_POST['data']['title']&&$_POST['data']['content']){
                if($_POST['ordertype']==1){
                    $order->url=$_POST['data']['url'];
                }elseif($_POST['ordertype']==2){
                    $order->url=$_POST['yzcode'];
                }elseif($_POST['ordertype']==3){
                    $order->url=$_POST['yzgift'];
                }elseif($_POST['ordertype']==4){
                    $order->appid=$_POST['data']['xcxappid'];
                    $order->url=$_POST['data']['xcxurl'];
                }
                $order->bid=$bid;
                if($_POST['orderway']==1){
                    $order->ifsend=0;
                    $order->time=time();
                }else{
                    $order->time=strtotime($_POST['data']['expiretime']);
                }
                if($_POST['orderflag']==1){
                    $order->aid=$_POST['appointment'];
                }
                $order->title=$_POST['data']['title'];
                // $order->item=$_POST['data']['item'];
                $order->flag=$_POST['orderflag'];
                $order->type=$_POST['ordertype'];
                $order->way=$_POST['orderway'];
                $order->content=$_POST['data']['content'];
                // $order->url=$_POST['data']['url'];
                if($order->save()){
                    $ok=1;
                    Request::instance()->redirect('qwtyyba/orders');
                }
            }else{
                $result['error']='请填写完整后再提交!';
            }

        }
        $appointments=ORM::factory('qwt_yybappointment')->where('bid','=',$bid)->where('renum','>',0)->find_all();
        $action1=2;
        $title=$this->template->title = '修改群发';
        //$this->template->content = View::factory('weixin/yyb/admin/orders_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/orders_add')
            ->bind('appointments',$appointments)
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
        $config = ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        $this->yzaccess_token=$yzaccess_token=ORM::factory('Qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        // $we=new Wechat($config);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];
        }
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
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
                $orders=ORM::factory('qwt_yyborder');
                $orders->bid=$bid;
                if($_POST['ordertype']==1){
                    $orders->url=$_POST['data']['url'];
                }elseif($_POST['ordertype']==2){
                    $orders->url=$_POST['yzcode'];
                }elseif($_POST['ordertype']==3){
                    $orders->url=$_POST['yzgift'];
                }elseif($_POST['ordertype']==4){
                    $orders->appid=$_POST['data']['xcxappid'];
                    $orders->url=$_POST['data']['xcxurl'];
                }
                if($_POST['orderway']==1){
                    $orders->ifsend=0;
                    $orders->time=time();
                }else{
                    $orders->time=strtotime($_POST['data']['expiretime']);
                }
                if($_POST['orderflag']==1&&$_POST['appointment']){
                    $orders->aid=$_POST['appointment'];
                }
                $orders->title=trim($_POST['data']['title']);
                $orders->flag=$_POST['orderflag'];
                $orders->way=$_POST['orderway'];
                $orders->type=$_POST['ordertype'];
                $orders->content=trim($_POST['data']['content']);
                $orders->save();
                Request::instance()->redirect('qwtyyba/orders');
            }else{
                $result['error']='请填写完整后再提交!';
            }
        }
        $appointments=ORM::factory('qwt_yybappointment')->where('bid','=',$bid)->where('renum','>',0)->find_all();
        $action1=1;
        $title=$this->template->title = '新建群发';
        //$this->template->content = View::factory('weixin/yyb/admin/orders_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/orders_add')
            ->bind('appointments', $appointments)
            ->bind('action1', $action1)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('title', $title)
            ->bind('bid',$bid);

    }
    public function action_yulan(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        if($_GET){
            Kohana::$log->add('get',print_r($_GET,true));
            $time=time();
            $order=ORM::factory('qwt_yyborder')->where('id','=',$_GET['oid'])->find();
            $num=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('admin','=',1)->count_all();
            if($num==0) $returnmsg='请先绑定预览用户';
            $qrcodes=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('admin','=',1)->find_all();
            foreach ($qrcodes as $qrcode) {
                if($order->type==1){
                    $url=$order->url;
                }elseif($order->type==2){
                    $url=$_SERVER["HTTP_HOST"].'/qwtyyb/yzcode?url='.$order->url.'&admin=1&openid='.$qrcode->openid.'&bid='.$bid;
                }elseif($order->type==3){
                    $url=$_SERVER["HTTP_HOST"].'/qwtyyb/yzgift?url='.$order->url.'&admin=1&openid='.$qrcode->openid.'&bid='.$bid;
                }elseif($order->type==4){
                    $tplmsg['miniprogram']['appid']=$order->appid;
                    $tplmsg['miniprogram']['pagepath']=$order->url;
                }
                $tplmsg['template_id'] = $config['mbtpl'];
                $tplmsg['touser'] = $qrcode->openid;
                $tplmsg['url'] = $url;
                $tplmsg['data']['first']['value'] = $order->title;
                $tplmsg['data']['first']['color'] = '#FF0000';
                $tplmsg['data']['keyword1']['value'] = $qrcode->nickname;
                $tplmsg['data']['keyword3']['value'] = '预约通知';
                // $tplmsg['data']['keyword1']['color'] = '#FF0000';
                $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',time());
                $tplmsg['data']['remark']['value'] = $order->content;
                $tplmsg['data']['remark']['color'] = '#666666';
                Kohana::$log->add('yybtplmsg',print_r($tplmsg,true));
                //Kohana::$log->add('tplmsg',print_r($tplmsg,true));
                $result=$wx->sendTemplateMessage($tplmsg);
                Kohana::$log->add('yybresult',print_r($result,true));
                if($result['errmsg']=='ok'){
                    $returnmsg='发送成功！';
                }else{
                    $returnmsg='发送失败！';
                }
            }
            echo $returnmsg;
            exit();
        }
    }
    public function action_test(){
        $stime=microtime(true);
        $bid =1;
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        // $we=new Wechat($config);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        $qrcodes=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->limit(100)->find_all();
        $url='www.baidu.com';
        $openid='oXYBfwId3Wh23y5cWNqwplAkLVdk';
        for ($i=0; $i <100 ; $i++) {
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = '测试';
            $result=$wx->sendCustomMessage($msg);
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
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        $result['title'] = $this->template->title = '预约链接';
        //$this->template->content = View::factory('weixin/yyb/admin/url')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/url')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_qrcode(){
        $bid = $this->bid;
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);

        if($_GET['refresh']==1){
            $admin=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',10)->find();
            $admin->qrcron=0;
            $admin->save();
            ORM::factory('qwt_yybcfg')->where('bid','=',$bid)->where('key','=','next_openid')->delete_all();
        }
        // echo "<pre>";
        // var_dump($config);
        // echo "</pre>";
        // if (isset($config['qr_count'])&&$config['qr_count']==0){
        //     echo "拉取完毕<br>";
        // }
        // if(!isset($config['qr_count'])){
        //     echo "微信授权<br>";
        // }
        // exit();
        $number1=ORM::factory('qwt_yybrecord')->where('bid','=',$bid)->count_all();
        $number2=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->count_all();
        $qrcron=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',10)->find()->qrcron;
        $login=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $result['title'] = $this->template->title = '预约链接';
        //$this->template->content = View::factory('weixin/yyb/admin/qrcode')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyb/qrcode')
            ->bind('reset(array)', $result)
            ->bind('login',$login)
            ->bind('number1', $number1)
            ->bind('qrcron',$qrcron)
            ->bind('number2', $number2)
            ->bind('config', $config);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_yyb$type";

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
