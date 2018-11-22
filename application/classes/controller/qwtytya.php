<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_qwtytya extends Controller_Base {

    public $template = 'weixin/yty/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $we;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'aa') return;
        $this->bid = $_SESSION['ytya']['bid'];
        $this->config = $_SESSION['ytya']['config'];
        $this->access_token=ORM::factory('qwt_ytylogin')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/ytya/login');
            header('location:/ytya/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('qwt_ytyorder')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=56cc2588c33f681d3a&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/ytya/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"56cc2588c33f681d3a",
            "client_secret"=>"0f1e07171efc8fc731635a575320f4cf",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/ytya/callback'
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
            $usershop = ORM::factory('qwt_ytylogin')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("ytya/home")."';</script>";
        }
        //Request::instance()->redirect('ytya/home');
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('qwt_ytylogin', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."yty/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."yty/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('qwt_ytycfg');

            foreach ($_POST['cfg'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }

            //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dirname($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'qwt_ytyfile_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'qwt_ytyfile_key', '', file_get_contents($key_file));

            //重新读取配置
            $config = ORM::factory('qwt_ytycfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('qwt_ytycfg');
            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }
               //默认头像
            
            //重新读取配置
            $config = ORM::factory('qwt_ytycfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('qwt_ytycfg');
 
             $qrfile = DOCROOT."yty/tmp/tpl.$bid.jpg";

            //默认头像
             if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."yty/tmp/tpl.$bid.jpg";
                    $cfg->setCfg($bid, 'tpl', '', file_get_contents($_FILES['pic']['tmp_name']));
                    @unlink($default_head_file);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $default_head_file);
                }
            }

            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."yty/tmp/head.$bid.jpg";
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
                $tplhead = ORM::factory('qwt_ytycfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                if ($tplhead) {
                    $tplhead->lastupdate = time();
                    $tplhead->save();
                }

                $tpl = ORM::factory('qwt_ytycfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                if ($tpl) {
                    $tpl->lastupdate = time();
                    $tpl->save();
                }

            }
            

            //重新读取配置
            $config = ORM::factory('qwt_ytycfg')->getCfg($bid, 1);
        }
        $result['tpl'] = ORM::factory('qwt_ytycfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_ytycfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $access_token = ORM::factory('qwt_ytylogin')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yty/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('config', $config);
    }
    public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid);

        $result['skus'] = ORM::factory('qwt_ytysku')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '经销商等级设置';
        $this->template->content = View::factory('weixin/yty/admin/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('qwt_ytysku');
            $sku->values($_POST['data']);
            $num=ORM::factory('qwt_ytysku')->where('bid','=',$bid)->count_all();
            $sku->order=$num+1;
            $sku->bid = $bid;
            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('ytya/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/yty/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid);

        $sku = ORM::factory('qwt_ytysku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('ytya/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('ytya/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/yty/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_qrcode_p(){
        $bid=$this->bid;
        $config=ORM::factory('qwt_ytycfg')->getCfg($bid,1);
        $qrcodes=ORM::factory('qwt_ytyqrcode')->where('bid','=',$bid)->where('lv','!=',1);
        $qrcodes->reset(FALSE);
        $result['countall'] = $countall = $qrcodes->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');
        $result['qrcodes'] = $qrcodes->order_by('jointime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '客户管理';
        $this->template->content = View::factory('weixin/yty/admin/qrcodes_p')
            ->bind('bid',$bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //审核管理
    public function action_qrcodes_m($action='', $id=0) {
        $bid = $this->bid;
        $this->config = ORM::factory('qwt_ytycfg')->getCfg($bid,1);
        $config=$this->config;
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];
                    $agent=ORM::factory('qwt_ytyagent')->where('bid','=',$bid)->where('openid','=',$qrcode_edit->openid)->find();
                    $agent->name=$_POST['form']['name'];
                    $agent->tel=$_POST['form']['tel'];
                    $agent->id_card=$_POST['form']['id_card'];
                    $agent->address=$_POST['form']['address'];
                }
                if ($_POST['form']['status']){
                    $status=$_POST['form']['status'];
                    if($status==1){
                        $fuser=ORM::factory('qwt_ytyqrcode')->where('bid','=',$bid)->where('openid','=',$qrcode_edit->fopenid)->find();
                        if($fuser->s==1){
                            $sku=ORM::factory('qwt_ytysku')->where('bid','=',$bid)->where('order','=',1)->find();
                            $qrcode_edit->s=1;
                            $agent->sid=$sku->id;
                            $agent->suser=$qrcode_edit->id;
                        }else{
                            $ffuser=ORM::factory('qwt_ytyqrcode')->where('bid','=',$bid)->where('openid','=',$fuser->fopenid)->find();
                            $qrcode_edit->fopenid=$ffuser->openid;
                            $agent->sid=$fuser->agent->skus->id;
                        }
                        $money3=$qrcode_edit->agent->skus->money*$config['money0']/100;
                        $fuser->stocks->stockIn($fuser, $type=4,$money3,$qrcode_edit->id,1);
                        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                        $this->we = new Wechat($config);
                        if($config['money_arrived_tpl']){
                            $money1= $fuser->agent->stock-$money3;
                            $money2=$fuser->agent->stock;
                            $remark='您的下级经销商'.$qrcode_edit->nickname.'已升级,恭喜您获得'.$money3.'元进货额奖励。';
                           $this->stocksuccess($fuser->openid,$money1,$money2,$remark);
                        }else{
                            $msg['touser'] = $fuser->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = '您的下级经销商'.$qrcode_edit->nickname.'已升级,恭喜您获得'.$money3.'元进货额奖励。';
                            $this->we->sendCustomMessage($msg);
                        }
                        $sqrcode=ORM::factory('qwt_ytysqrcode');
                        $sqrcode->bid=$bid;
                        $sqrcode->qid=$qrcode_edit->id;
                        $sqrcode->sqid=$fuser->id;
                        $sqrcode->save();
                    }
                }
                $agent->save();
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',1)->or_where('lv','=',3)->where_close();
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
        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '经销商列表';
        $this->template->content = View::factory('weixin/yty/admin/qrcodes_m')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //分销审核
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $this->config = ORM::factory('qwt_ytycfg')->getCfg($bid,1);
        $config=$this->config;
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];
                    $agent=ORM::factory('qwt_ytyagent')->where('bid','=',$bid)->where('openid','=',$qrcode_edit->openid)->find();
                    $agent->name=$_POST['form']['name'];
                    $agent->tel=$_POST['form']['tel'];
                    $agent->id_card=$_POST['form']['id_card'];
                    $agent->address=$_POST['form']['address'];
                    if((int)$_POST['form']['lv']==1){
                        //给予编号
                        $money=$agent->money;
                        $qrcode_edit->stocks->stockIn($qrcode_edit, $type=0, $money,'',1);
                        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                        $this->we = new Wechat($config);
                        if($config['money_arrived_tpl']){
                            $money1= $qrcode_edit->agent->stock-$money;
                            $money2=$qrcode_edit->agent->stock;
                            $remark='恭喜您获得'.$money.'元进货额。';
                           $this->stocksuccess($qrcode_edit->openid,$money1,$money2,$remark);
                        }else{
                            $msg['touser'] = $qrcode_edit->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = '恭喜您获得'.$money.'元进货额。';
                            $this->we->sendCustomMessage($msg);
                        }
                        $front_user = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where('id', '<', $qrcode_edit->id)->order_by('id','desc')->find();
                        $qrcode_edit->fid = $front_user->fid + 1;
                        if($config['msg_success_tpl']){
                            $this->sendsuccess($qrcode_edit->openid,$qrcode_edit->nickname);
                        }else{
                            $msg['touser'] = $qrcode_edit->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = "恭喜您成功获得经销商资格，赶紧把商品分享给你的用户购买吧";
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
                $agent->save();
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where('lv','=',2);
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

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qwt_ytyqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');

        $result['qrcodes'] = $qrcode->order_by('s', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '经销商审核';
        $this->template->content = View::factory('weixin/yty/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_stock(){
        $bid=$this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = new Wechat($config);
        if($_POST['form']){
            $id=$_POST['form']['id'];
            $stock=ORM::factory('qwt_ytystock')->where('id','=',$id)->find();
            $stock->flag=1;
            $stock->save();
            $qrcode=$stock->qrcode;
            $stocks=DB::query(Database::SELECT,"SELECT SUM(money) as stock from qwt_ytystocks where bid = $bid and qid = $qrcode->id ")->execute()->as_array();
            $stock1=$stocks[0]['stock'];
            $agent=$qrcode->agent;
            $agent->stock=$stock1;
            $agent->save();
            if($config['money_arrived_tpl']){
                $money1= $qrcode->agent->stock-$stock->money;
                $money2=$qrcode->agent->stock;
                $remark='恭喜您获得'.$stock->money.'元进货额。';
               $this->stocksuccess($qrcode->openid,$money1,$money2,$remark);
            }else{
                $msg['touser'] = $qrcode->openid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '恭喜您获得'.$money.'元进货额。';
                $this->we->sendCustomMessage($msg);
            }
        }
        $stocks=ORM::factory('qwt_ytystock')->where('bid','=',$bid)->where('flag','=',0)->where('type','=',1);
        $stocks->reset(false);

        $result['total_results']=$stocks->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');
        $result['stocks'] = $stocks->order_by('fqid', 'ASC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '补货申请';
        $this->template->content = View::factory('weixin/yty/admin/stock')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_stock_history(){
        $bid=$this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid,1);
        $stocks=ORM::factory('qwt_ytystock')->where('bid','=',$bid)->where('flag','=',1)->where_open()->where('type','=',1)->or_where('type','=',0)->where_close();
        $stocks->reset(false);
        if($_GET['id']){
            $stocks=$stocks->where('qid','=',$_GET['id']);
        }
        $result['total_results']=$stocks->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');
        $result['stocks'] = $stocks->order_by('fqid', 'ASC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '补货记录';
        $this->template->content = View::factory('weixin/yty/admin/stock_history')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['ytya']['admin'] < 1) Request::instance()->redirect('ytya/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('qwt_ytylogin');
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
        ))->render('weixin/yty/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/yty/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['ytya']['admin'] < 2) Request::instance()->redirect('ytya/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('qwt_ytylogin');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_ytylogin')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('ytya/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/yty/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['ytya']['admin'] < 2) Request::instance()->redirect('ytya/home');

        $bid = $this->bid;

        $login = ORM::factory('qwt_ytylogin', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('qwt_ytycfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('ytya/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_ytylogin')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('ytya/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/yty/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/yty/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('qwt_ytylogin')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['ytya']['bid'] = $biz->id;
                    $_SESSION['ytya']['user'] = $_POST['username'];
                    $_SESSION['ytya']['admin'] = $biz->admin; //超管
                    $_SESSION['ytya']['config'] = ORM::factory('qwt_ytycfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['ytya']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/ytya/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['ytya'] = null;
        header('location:/ytya/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_yty$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    // //产品图片
    // public function action_goodimages($id=1) {

    //     $tempdate = ORM::factory(setgood)->where('id','=',$id)->find();
    //     $pic=$tempdate->pic;
    //     if (!$pic) die('404 Not Found!');

    //     header("Content-Type:image/jpeg");
    //     header("Content-Length: ".strlen($pic));
    //     echo $pic;
    //     exit;
    // }
    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        if($_GET['qid']==3||$action=='shaixuan'){
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
                $fans=DB::query(Database::SELECT,"SELECT count(openid) as fansnum from qwt_ytyqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and lv !=1  ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];
                //新增经销商
                $user=DB::query(Database::SELECT,"SELECT count(openid) as usernum from qwt_ytyqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and lv =1 ")->execute()->as_array();
                $newadd[0]['user']=$user[0]['usernum'];
                //进货额
                $stock=DB::query(Database::SELECT,"SELECT SUM(money) as stock from qwt_ytystocks where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' and fqid is null ")->execute()->as_array();
                $newadd[0]['stock']=$stock[0]['stock'];
                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qwt_ytytrades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[0]['payment']=$tradesdata[0]['payment'];
                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qwt_ytyscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and type =4")->execute()->as_array();
                $newadd[0]['commision']=$commision[0]['paymoney'];
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
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytyscores` where bid=$this->bid and type =4 UNION SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytystocks` where bid=$this->bid and fqid is NUll UNION SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_ytyqrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qwt_ytytrades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/yty/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytyscores` where bid=$this->bid and type =4 UNION SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytystocks` where bid=$this->bid and fqid is NUll UNION SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_ytyqrcodes` where bid=$this->bid UNION select left(pay_time,$length) from qwt_ytytrades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"SELECT count(openid) as fansnum from qwt_ytyqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and lv != 1")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //新增经销商
                $user=DB::query(Database::SELECT,"SELECT count(openid) as usernum from qwt_ytyqrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and lv = 1")->execute()->as_array();
                $newadd[$i]['user']=$user[0]['usernum'];
                //进货额
                $stock=DB::query(Database::SELECT,"SELECT SUM(money) as stock from qwt_ytystocks where bid=$this->bid and  FROM_UNIXTIME(`lastupdate`, '$daytype') ='$time' and fqid is NULL ")->execute()->as_array();
                $newadd[$i]['stock']=$stock[0]['stock'];
                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qwt_ytytrades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$i]['payment']=$tradesdata[0]['payment'];
                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qwt_ytyscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and type =4")->execute()->as_array();
               // var_dump($commision);
                $newadd[$i]['commision']=$commision[0]['paymoney'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `qwt_ytyqrcodes` where bid=$this->bid UNION select left(pay_time,10) from qwt_ytytrades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/yty/admin/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }
    public function action_stats_item($action=''){
        $daytype='%Y-%m';
        $length=7;
        $status=1;
        if($_GET['qid']==3||$action=='shaixuan'){
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
                //进货额
                $stock=DB::query(Database::SELECT,"SELECT SUM(money) as stock from qwt_ytystocks where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' and fqid is null ")->execute()->as_array();
                $newadd[0]['stock']=$stock[0]['stock'];
                //退款金额
                $trade_close=DB::query(Database::SELECT,"SELECT SUM(payment) as payment from qwt_ytytrades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over' and status ='TRADE_CLOSED' ")->execute()->as_array();
                $newadd[0]['trade_close']=$trade_close[0]['payment'];
                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qwt_ytytrades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[0]['payment']=$tradesdata[0]['payment'];
                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qwt_ytyscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and type =4")->execute()->as_array();
                $newadd[0]['commision']=$commision[0]['paymoney'];
            }
        }
        else
        {
            if($_GET['qid']==2||$action=='year')//按月统计
            {
                $daytype='%Y';
                $length=4;
                $status=2;
            }
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytyscores` where bid=$this->bid and type =4 UNION SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytystocks` where bid=$this->bid and fqid is NUll UNION select left(pay_time,$length) from qwt_ytytrades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/yty/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytyscores` where bid=$this->bid and type =4 UNION SELECT FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_ytystocks` where bid=$this->bid and fqid is NUll UNION select left(pay_time,$length) from qwt_ytytrades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //进货额
                $stock=DB::query(Database::SELECT,"SELECT SUM(money) as stock from qwt_ytystocks where bid=$this->bid and  FROM_UNIXTIME(`lastupdate`, '$daytype') ='$time' and fqid is NULL ")->execute()->as_array();
                $newadd[$i]['stock']=$stock[0]['stock'];
                //退款金额
                $trade_close=DB::query(Database::SELECT,"SELECT SUM(payment) as payment from qwt_ytytrades where bid=$this->bid and left(pay_time,$length) LIKE '$time' and status ='TRADE_CLOSED' ")->execute()->as_array();
                $newadd[$i]['trade_close']=$trade_close[0]['payment'];
                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from qwt_ytytrades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$i]['payment']=$tradesdata[0]['payment'];
                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from qwt_ytyscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and type =4")->execute()->as_array();
               // var_dump($commision);
                $newadd[$i]['commision']=$commision[0]['paymoney'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `qwt_ytyqrcodes` where bid=$this->bid UNION select left(pay_time,10) from qwt_ytytrades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/yty/admin/stats_item')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }
    public function action_money(){
        $bid=$this->bid;
        $has=DB::query(Database::SELECT,"SELECT SUM(score) as money_has from qwt_ytyscores where bid = $bid and type =4 ")->execute()->as_array();
        $money_has=$has[0]['money_has'];
        $having=DB::query(Database::SELECT,"SELECT SUM(score) as money_having from qwt_ytyscores where bid = $bid and type =2 ")->execute()->as_array();
        $money_having=$having[0]['money_having'];
        $result['money_has']=$money_has;
        $result['money_having']=$money_having;
        $this->template->content = View::factory('weixin/yty/admin/money')
        ->bind('result',$result);
    }
    public function action_history_trades()
    {

        $bid = $this->bid;
        $this->config = ORM::factory('qwt_ytycfg')->getCfg($bid);
        $config=$this->config;
        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }
        if($_POST['form']){
            $status=$_POST['form']['status'];
            if($status==1){
                $id=$_POST['form']['id'];
                $trade=ORM::factory('qwt_ytytrade')->where('id','=',$id)->find();
                $trade->status='TRADE_CLOSED';
                $trade->save();
                if($trade->flag==1){
                    $fopenid=$trade->fopenid;
                    $fuser=ORM::factory('qwt_ytyqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                    $fuser->stocks->stockIn($fuser, $type=6,$trade->money1,$trade->qrcode->id,1);
                    require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                    $this->we = new Wechat($config);
                    if($config['money_arrived_tpl']){
                        $money1= $fuser->agent->stock-$trade->money1;
                        $money2= $fuser->agent->stock;
                        $remark='您的客户'.$trade->qrcode->nickname.'购买的'.$trade->title.'已退货。退回给您'.$trade->money1.'元进货额';
                       $this->stocksuccess($qrcode_edit->openid,$money1,$money2,$remark);
                    }else{
                        $msg['touser'] = $qrcode_edit->openid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = '您的客户'.$trade->qrcode->nickname.'购买的'.$trade->title.'已退货。退回给您'.$trade->money1.'元进货额';
                        $this->we->sendCustomMessage($msg);
                    }
                    ORM::factory('qwt_ytyscore')->where('tid', '=', $trade->id)->delete_all();
                }
            }
        }
        $trade = ORM::factory('qwt_ytytrade')->where('bid', '=', $bid);
        $trade = $trade->reset(FALSE);
        if ($_GET['id']){
            $openid=ORM::factory('qwt_ytyqrcode')->where('id','=',$_GET['id'])->find()->openid;
            $trade=$trade->where('fopenid','=',$openid);
        }
        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from qwt_ytyqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

            $trade =$trade->where('title', 'like', $s)->or_where('tid','like',$s);

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
        ))->render('weixin/yty/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/yty/admin/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid);
        $outmoney=ORM::factory('qwt_ytyscore')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from qwt_ytyqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();
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
        ))->render('weixin/yty/admin/pages');
        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/yty/admin/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }
    public function action_num(){
            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            $tradeid=ORM::factory('qwt_ytytrade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('qwt_ytyorder')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('qwt_ytycfg')->getCfg($tempbid);
                    $this->access_token = ORM::factory('qwt_ytylogin')->where('id','=',$tempbid)->find()->access_token;
                    if (!$this->access_token) //die("$bid not found.\n");
                    continue;

                    $client = new YZTokenClient($this->access_token);
                    $method = 'youzan.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($method, $this->methodVersion, $params, $files);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('qwt_ytyorder')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
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
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            echo $tid;
            $bid=ORM::factory('qwt_ytytrade')->where('tid','=',$tid)->find()->bid;

            $this->access_token = ORM::factory('qwt_ytylogin')->where('id','=',$bid)->find()->access_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('qwt_ytycfg')->getCfg($tempbid);

            if (!$this->access_token)  die("$bid not found.\n");


            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.trade.get';
            $params = array(
                'tid'=>$tid,
                //'fields' => 'tid,title,num_iid,orders,status,pay_time',
            );

             $result = $client->post($method, $this->methodVersion, $params, $files);
             echo "<pre>";
             var_dump($result);



    exit();
    }

    public function action_stats_goods()
    {
        //$goods=ORM::factory('qwt_ytyorder')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `qwt_ytyorders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_ytyorders.* FROM `qwt_ytytrades`,qwt_ytyorders WHERE qwt_ytyorders.tid=qwt_ytytrades.tid and qwt_ytytrades.status!='TRADE_CLOSED' and qwt_ytytrades.status!='TRADE_CLOSED_BY_USER' and qwt_ytytrades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_ytyorders.* FROM `qwt_ytytrades`,qwt_ytyorders WHERE qwt_ytyorders.tid=qwt_ytytrades.tid and qwt_ytytrades.status!='TRADE_CLOSED' and qwt_ytytrades.status!='TRADE_CLOSED_BY_USER' and qwt_ytytrades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_ytyorders.* FROM `qwt_ytytrades`,qwt_ytyorders WHERE qwt_ytyorders.tid=qwt_ytytrades.tid and qwt_ytytrades.status!='TRADE_CLOSED' and qwt_ytytrades.status!='TRADE_CLOSED_BY_USER' and qwt_ytytrades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT qwt_ytyorders.* FROM `qwt_ytytrades`,qwt_ytyorders WHERE qwt_ytyorders.tid=qwt_ytytrades.tid and qwt_ytytrades.status!='TRADE_CLOSED' and qwt_ytytrades.status!='TRADE_CLOSED_BY_USER' and qwt_ytytrades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        $this->template->content = View::factory('weixin/yty/admin/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }

    public function action_setgoods()
    {
        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid, 1);
        //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->access_token = ORM::factory('qwt_ytylogin')->where('id','=',$bid)->find()->access_token;
        $tempconfig=ORM::factory('qwt_ytycfg')->getCfg($this->bid);
        if($this->access_token)
        {
            $client = new YZTokenClient($this->access_token);
            $pg=1;
            $method = 'kdt.items.onsale.get';
            $params =[
                //'fields' =>'total_results',
            ];
            $total_result= $client->post($method, '1.0.0', $params, $files);
            $total =$total_result['response']['total_results'];
            $a = $total/100;
            for($k=0;$k<$a;$k++){
                $method = 'kdt.items.onsale.get';
                $params = array(
                    'page_size'=>100,
                    'page_no'=>$k,
                    'fields' => 'num_iid,title,price,pic_thumb_url,num,detail_url',
                    );
                $results = $client->post($method,'1.0.0', $params, $files);
                for($i=0;$results['response']['items'][$i];$i++){
                    $res=$results['response']['items'][$i];
                    $num_iid=$res['num_iid'];
                    $name=$res['title'];
                    $price=$res['price'];
                    $pic=$res['pic_thumb_url'];
                    $url=$res['detail_url'];
                    $num=$res['num'];
                    $num_num = ORM::factory('qwt_ytysetgood')->where('goodid', '=', $num_iid)->count_all();
                    if($num_num==0 && $num_iid!=""){
                        $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_ytysetgoods` (`bid`,`goodid`,`title`,`price`, `picurl`,`url`,`status`,`state`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,1)");
                        $sql->execute();
                    }else{
                        $sql = DB::query(Database::UPDATE,"UPDATE `qwt_ytysetgoods` SET `bid` = $bid ,`goodid` = $num_iid,`title` ='$name',`price`=$price, `picurl`='$pic',`url`='$url' ,`state` = 1 where `goodid` = $num_iid ");
                        $sql->execute();
                    }
                }
            }
            $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_ytysetgoods` where `state` =0 and `bid` = $bid ");
            $sql->execute();
            $sql = DB::query(Database::UPDATE,"UPDATE `qwt_ytysetgoods` SET `state` =0 where `bid` = $bid");
            $sql->execute();
        }
        Request::instance()->redirect('ytya/setgood1s');
    }
    public function action_setgood1s(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ytycfg')->getCfg($bid, 1);
        $setgoods = ORM::factory('qwt_ytysetgood')->where('bid','=',$bid);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $setgoods = $setgoods->where('title', 'like', $s);
        }
        if ($_POST['form']['num_iid']) {
            $goodid = $_POST['form']['num_iid'];
            $setgoods = ORM::factory('qwt_ytysetgood')->where('bid', '=', $bid)->where('goodid','=',$goodid)->find();
            $setgoods->money0=$_POST['form']['money0'];
            $setgoods->money1=$_POST['form']['money1'];
            $setgoods->money2=$_POST['form']['money2'];
            $setgoods->money3=$_POST['form']['money3'];
            $setgoods->money4=$_POST['form']['money4'];
            $setgoods->status=$_POST['form']['status'];
            $setgoods->goodid=$_POST['form']['num_iid'];
            $setgoods->picurl=$_POST['form']['picurl'];
            $setgoods->price=$_POST['form']['price'];
            $setgoods->url=$_POST['form']['url'];
            $setgoods->bid=$bid;
            $setgoods->title=$_POST['form']['title'];
            $setgoods->information=$_POST['form']['desc'];
                //$file = $_FILES['form']['goodpic'];//得到传输的数据
            if($_FILES['goodpic']['error']==0){
               $setgoods->pic=file_get_contents($_FILES['goodpic']['tmp_name']);
               $setgoods->pictype=$_FILES["goodpic"]["type"];
            }
            $setgoods->save();
        }
        $setgoods = ORM::factory('qwt_ytysetgood')->where('bid','=',$bid);
        $setgoods = $setgoods->reset(FALSE);
        $result['countall'] = $countall = $setgoods->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yty/admin/pages');

        $result['setgoods'] =$setgoods->order_by('status', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['title'] = $this->template->title = '商品审核';
        $this->template->content = View::factory('weixin/yty/admin/setgoods')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('bid',$bid)
            ->bind('config', $config);
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
    private function sendsuccess($openid, $nickname,  $remark='恭喜您成功获得经销商资格，点击菜单可以进入经销商个人中心。') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_success_tpl'];
        //$tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = '尊敬的用户，您提交的经销商申请已经审核通过！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $nickname;
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = '已通过';
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword3']['color'] = '#666666';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($openid, true));
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_yty:tpl", print_r($result, true));
        return $result;
    }
    private function sendTaskMessage($openid, $url,$first,$keyword1,$keyword2,$remark) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['task_deal_tpl'];
        $tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = $first;
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = '#06bf04';
        $tplmsg['data']['remark']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';
        return $this->we->sendTemplateMessage($tplmsg);
    }
    private function stocksuccess($openid,$money1,$money2,$remark) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['money_arrived_tpl'];
        //$tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = 进货额余额变更通知;
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = number_format($money1,2);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = number_format($money2,2);
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword3']['color'] = '#666666';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($openid, true));
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_yty:tpl", print_r($result, true));
        return $result;    }
}
