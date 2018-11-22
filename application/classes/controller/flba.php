<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_flba extends Controller_Base {
    public $template = 'weixin/flb/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion = '3.0.0';
    public function before() {
        Database::$default = "flb";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'test5') return;
        $this->bid = $_SESSION['flba']['bid'];
        $this->config = $_SESSION['flba']['config'];
        $this->access_token=ORM::factory('flb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/flba/login');
            header('location:/flba/login?from='.Request::instance()->action);
            exit;
        }
    }
    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('flb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }
        @View::bind_global('bid', $this->bid);
        parent::after();
    }
    public function action_index() {
        $this->action_login();
    }

    public function action_oauth(){
        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=437ac1951309902ae4&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/flba/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"437ac1951309902ae4",
            "client_secret"=>"0e356d46e2391b3a1852aad203fa88b4",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/flba/callback'
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
            $value=$oauth->get('kdt.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['id'];
            $name = $value['name'];
            $usershop = ORM::factory('flb_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("flba/home")."';</script>";
        }
        //Request::instance()->redirect('flba/home');
    }
    public function action_test5(){
        $tempname="全部";
        $bid=2;
        $order = ORM::factory('flb_detail')->where('bid', '=', $bid)->where('state','=',1)->where('time','<=',1493568000)->where('time','>=',1490976000)->find_all();
        $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);
        $fp = fopen('php://output', 'w');
        $title = array('微信昵称', '姓名', '电话', '地址', '订单名称', '订单金额', '返还总额','第几次返还','本次返还金额','红包状态','时间');
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
        fputcsv($fp, $title);
        foreach ($order as $o) {
            if ($o->status == 'SENT'){
                $name='已发放待领取';
            }elseif($o->status == 'RECEIVED'){
                $name='已领取';
            }elseif ($o->status == 'REFUND'){
                $name='过期未领取';
            }elseif ($o->status == 'FAILED'){
                $name='发放失败';
            }
            $data=date('Y-m-d',$o->lastupdate);
            $money=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$o->oid)->select(array('SUM("money")', 'moneys'))->find()->moneys;
            $array = array($o->order->user->nickname, $o->order->receiver_name, $o->order->tel,$o->order->adress, $o->order->title, $o->order->price,$money,$o->num,$o->money,$name,$data);
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                //非 Mac 转 gbk
                foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $array);
        }
        exit;

    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $this->config= $config= ORM::factory('flb_cfg')->getCfg($bid, 1);
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('flb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."flb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."flb/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('flb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }

            //证书上传
            if ($_FILES['cert']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                umask(0002);
                @mkdir(dir($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'flb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'flb_file_key', '', file_get_contents($key_file));

            //重新读取配置
            $config = ORM::factory('flb_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('flb_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('flb_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('flb_cfg');

            if (!$result['err3']) {
                foreach ($_POST['text'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;
                }
            }

            //重新读取配置
            $config = ORM::factory('flb_cfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('flb_cfg')->where('bid', '=', $bid)->where('key', '=', 'flbtpl')->find()->id;
        $result['tplhead'] = ORM::factory('flb_cfg')->where('bid', '=', $bid)->where('key', '=', 'flbtplhead')->find()->id;
        $access_token = ORM::factory('flb_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/flb/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('config', $config);
    }
    public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);

        $result['skus'] = ORM::factory('flb_sku')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '返还管理';
        $this->template->content = View::factory('weixin/flb/admin/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);

        if ($_POST['data']) {

            $sku = ORM::factory('flb_sku');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['times'] || !$_POST['data']['start']|| !$_POST['data']['end']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('flba/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/flb/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_sending() {
        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we=$we = new Wechat($config);
        $access_token=$this->access_token;
        $sending = ORM::factory('flb_detail')->where('bid', '=', $bid)->where('state','=',0)->where('time','!=',0)->where('time','<=',time());
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            //$order = ORM::factory('flb_order')->where('bid', '=', $bid);
            $order=DB::query(Database::SELECT,"SELECT id FROM flb_orders Where `bid` = $bid and `receiver_name` like '$s' ")->execute()->as_array();
            $tempid=array();
            if($order[0]['id']==null)
            {
              $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
            }
            else
            {
              for($i=0;$order[$i];$i++)
              {
                $tempid[$i]=$order[$i]['id'];
              }
            }
            $sending = ORM::factory('flb_detail')->where('bid', '=', $bid)->where('oid','IN',$tempid)->where('state','=',0)->where('time','!=',0)->where('time','<=',time());
            //var_dump($details);
            //exit;
            //$has_send = $order->where('receiver_name', 'like', $s)->find_all()->details->where('state','=',1)->where('time','<=',time())->find_all()->as_array();
            //var_dump($has_send);
        }
        $sending = $sending->reset(FALSE);
        $result['countall'] = $countall = $sending->count_all();
        if($_POST['form']){
            $status=$_POST['form']['status'];
            if($status==1){
                $id=$_POST['form']['id'];
                $detail=ORM::factory('flb_detail')->where('id','=',$id)->find();
                if($detail->money1==0){
                    $money=$detail->money;
                }else{
                    $money=$detail->money1;
                }
                $money2=0;
                $oid=$detail->oid;
                $tid_name=$detail->order->title;
                $openid=$detail->order->user->openid;
                $remain=$money%200;  //余数
                $quot=floor($money/200);  //求商
                if($remain!=0){
                    //Kohana::$log->add('remain', print_r($remain, true));
                    $result=$this->hongbao1($config, $openid, $we, $bid, $remain);
                    if($result['result_code']!='SUCCESS'){
                        $money2+=$remain;
                    }else{
                        $hb=ORM::factory('flb_hb');
                        $hb->bid=$bid;
                        $hb->did=$id;
                        $hb->money=$remain;
                        $hb->mch_billno=$result['mch_billno'];
                        $hb->save();
                    }
                }
                for ($i=0; $i <$quot ; $i++) {
                    $result=$this->hongbao1($config, $openid, $we, $bid, 200);
                    if($result['result_code']!='SUCCESS'){
                       $money2+=200;
                    }else{
                        $hb=ORM::factory('flb_hb');
                        $hb->bid=$bid;
                        $hb->did=$id;
                        $hb->money=200;
                        $hb->mch_billno=$result['mch_billno'];
                        $hb->save();
                    }
                }
                //Kohana::$log->add('remain', print_r($remain, true));
                if($money2){
                    $detail2=ORM::factory('flb_detail')->where('bid','=',$bid)->where('id','=',$id)->find();
                    $detail2->money1=$money2;
                    $detail2->save();
                }
                // echo $money2."<br>";
                // echo $oid."<br>";
                // echo $tid_name.'<br>';
                // echo $openid.'<br>';
                // echo "<pre>";
                // var_dump($result);
                // echo "</pre>";
                // exit;
                if($result['result_code']=='SUCCESS'){
                    $detail->mch_billno=$result['mch_billno'];
                    $detail->state=1;
                    $detail->save();
                    $money1=$money;
                    $pay=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$oid)->where('state','=',0)->select(array('SUM("money")', 'money1'))->find()->money1;
                    $has_time=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$oid)->where('state','=',0)->count_all();
                    if($config['msg_tpl']){
                        $keyword1=$tid_name;
                        if($has_time==0){
                            $keyword2="恭喜您获得嘀的商城返还红包{$money1}元\\n您的红包已返还完毕，谢谢您的参与";
                        }else{
                            $keyword2="恭喜您获得嘀的商城返还红包{$money1}元\\n您还将获得嘀的商城{$pay}元返还红包\\n分{$has_time}次返还。";
                        }
                        $this->sendTemplateMessage1($openid,$config['msg_tpl'],'',$keyword1,$keyword2);
                    }else{
                        if($has_time==0){
                            $keyword="恭喜您获得嘀的商城返还红包{$money1}元,您的红包已返还完毕，谢谢您的参与";
                        }else{
                            $keyword="恭喜您获得嘀的商城返还红包{$money1}元,您还将获得嘀的商城{$pay}元返还红包，分{$has_time}次返还。";
                        }
                        $this->sendCustomMessage1($openid,$keyword);
                    }
                }else{
                    $detail->log=$result['return_msg'];
                    $detail->save();
                }
            }elseif($status==0){
                $id=$_POST['form']['id'];
                $detail=ORM::factory('flb_detail')->where('id','=',$id)->find();
                $detail->state=3;
                $detail->save();
            }
        }
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/flb/admin/pages');
        $result['sending'] = $sending->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/flb/admin/sending')
            ->bind('bid',$bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function sendCustomMessage1($openid,$km_text){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $km_text;
        $result=$this->we->sendCustomMessage($msg);
        return $result;
    }
    public function sendTemplateMessage1($openid,$mgtpl,$keyword,$keyword1,$keyword2){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $mgtpl;
        $tplmsg['url']=$url;
        $tplmsg['data']['first']['value']=urlencode($keyword);
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = urlencode($keyword1);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['remark']['value'] = urlencode($keyword2);
        $tplmsg['data']['remark']['color'] = '#FF0000';
        $result=$this->we->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        return $result;
    }
     private function hongbao1($config, $openid, $we='', $bid=1, $money){
        //记录 用户 请求红包
        Kohana::$log->add("进发红包了",print_r($openid,true));
        Kohana::$log->add("进发红包了",print_r($bid,true));
        Kohana::$log->add("进发红包了",print_r($money,true));
        $money=$money*100;
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);
        Kohana::$log->add("mch_id",print_r($config['partnerid'],true));
        $mch_billno = $config['partnerid']. date('YmdHis').rand(1000, 9999); //订单号
        Kohana::$log->add("mch",print_r($mch_billno,true));
        $data["nonce_str"] = $this->we->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //支付商户号
        $data["wxappid"] = $config['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数
        $data["act_name"] = "本次活动"; //活动名称
        // $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        // $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案
        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        Kohana::$log->add("data1",print_r($data,true));
        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
        Kohana::$log->add("data",print_r($data['sign'],true));
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $this->we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址
        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除
        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);//支付安全验证函数（核心函数）
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['mch_billno']=$mch_billno;
        Kohana::$log->add("result",print_r($result,true));
        return $result;//hash数组
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        $config = $this->config;
        $bid = $this->bid;
        //echo 'appsecret:'.$config['appsecret'].'<br>';
        $cert_file = DOCROOT."flb/tmp/$bid/cert.{$config['appsecret']}.pem";
        //echo 'cert:'.$cert_file.'<br>';
        $key_file = DOCROOT."flb/tmp/$bid/key.{$config['appsecret']}.pem";
        //echo 'key:'.$key_file.'<br>';
        //证书分布式异步更新
        $file_cert = ORM::factory('flb_cfg')->where('bid', '=', $bid)->where('key', '=', 'flb_file_cert')->find();
        $file_key = ORM::factory('flb_cfg')->where('bid', '=', $bid)->where('key', '=', 'flb_file_key')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }


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
    public function action_has_send() {
        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $has_send = ORM::factory('flb_detail')->where('bid', '=', $bid)->where('state','=',1)->where('time','<=',time());

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            //$order = ORM::factory('flb_order')->where('bid', '=', $bid);
            $order=DB::query(Database::SELECT,"SELECT id FROM flb_orders Where `bid` = $bid and `receiver_name` like '$s' ")->execute()->as_array();
            $tempid=array();
            if($order[0]['id']==null)
            {
              $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
            }
            else
            {
              for($i=0;$order[$i];$i++)
              {
                $tempid[$i]=$order[$i]['id'];
              }
            }
            $has_send = ORM::factory('flb_detail')->where('bid', '=', $bid)->where('oid','IN',$tempid)->where('state','=',1)->where('time','<=',time());
            //var_dump($details);
            //exit;
            //$has_send = $order->where('receiver_name', 'like', $s)->find_all()->details->where('state','=',1)->where('time','<=',time())->find_all()->as_array();
            //var_dump($has_send);
        }
        $has_send = $has_send->reset(FALSE);
        $result['countall'] = $countall = $has_send->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/flb/admin/pages');

        $result['has_send'] = $has_send->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/flb/admin/has_send')
            ->bind('bid',$bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);

        $sku = ORM::factory('flb_sku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('flba/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['times'] || !$_POST['data']['start']|| !$_POST['data']['end']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('flba/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/flb/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $qrcode = ORM::factory('flb_qrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/flb/admin/pages');

        $result['qrcodes'] = $qrcode->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/flb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('flb_cfg')->getCfg($bid,1);
        $order = ORM::factory('flb_order')->where('bid', '=', $bid);

        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('title', 'like', $s)->or_where('receiver_name', 'like', $s);
        }
        if ($_POST){
            $deltid=$_POST['deltid'];
            $tid=ORM::factory('flb_order')->where('id','=',$deltid)->find()->tid;
            $result1= DB::query(Database::DELETE,"DELETE from flb_details where `bid` = $bid and `oid` = $deltid")->execute();
            $result2= DB::query(Database::DELETE,"DELETE from flb_orders where `id` = $deltid")->execute();
            $tids=ORM::factory('flb_tid')->where('tid','=',$tid)->find();
            $tids->bid=$bid;
            $tids->tid=$tid;
            $tids->save();
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/flb/admin/pages');
        $result['orders'] = $order->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/flb/admin/orders')
            ->bind('bid',$bid)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['flba']['admin'] < 1) Request::instance()->redirect('flba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('flb_login');
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
        ))->render('weixin/flb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/flb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['flba']['admin'] < 2) Request::instance()->redirect('flba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('flb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('flb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('flba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/flb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['flba']['admin'] < 2) Request::instance()->redirect('flba/home');

        $bid = $this->bid;

        $login = ORM::factory('flb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('flb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('flba/skus');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('flb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('flba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/flb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/flb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('flb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['flba']['bid'] = $biz->id;
                    $_SESSION['flba']['user'] = $_POST['username'];
                    $_SESSION['flba']['admin'] = $biz->admin; //超管
                    $_SESSION['flba']['config'] = ORM::factory('flb_cfg')->getCfg($biz->id,1);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['flba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/flba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['flba'] = null;
        header('location:/flba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "flb_$type";

        $pic = ORM::factory($table, $id)->pic;
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
}
