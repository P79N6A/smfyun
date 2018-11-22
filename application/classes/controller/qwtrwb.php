<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtrwb extends Controller_Base {
    public $template = 'weixin/sqb/tpl/blank';
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public $access_token;
    public $methodVersion = '3.0.0';
    public function before() {
        Database::$default = "qwt";
        parent::before();
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'kmpass') return;
        if (Request::instance()->action == 'ticket') return;
        if (Request::instance()->action == 'shiwu') return;
        $_SESSION =& Session::instance()->as_array();
    }

    public function after() {
        $this->template->user = $user;
        parent::after();
    }
    public function action_shiwu($e='order'){
        $this->template = 'tpl/blank';
        self::before();
        $bid=$_GET['bid'];
        $qid=$_GET['qid'];
        $tid=$_GET['tid'];
        $kid=$_GET['kid'];
        $iid=$_GET['iid'];
        $item1=ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find();
        $order=ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->where('iid','=',$iid)->find();
        $item['need_money']=($item1->need_money)/100;
        if(!$order->id) die('您的url有误');
        if($order->id&&$_POST){
            $receive_name=$_POST['data']['name'];
            $tel=$_POST['data']['tel'];
            $address=$_POST['s_province'].$_POST['s_city'].$_POST['s_dist'].$_POST['data']['address'];
            $order->receive_name=$receive_name;
            $order->tel=$tel;
            $order->address=$address;
            $order->pay_money=$item['need_money'];
            $order->save();
        }
        if($order->order_state>0||($order->pay_money==0&&$order->tel)){
            $result['status']=1;
            $neirong='';
            if($order->status==0){
                $result['neirong']='请耐心等待管理员发货';
            }else{
                $result['neirong']='您的奖品已发货，请注意查收';
            }
        }else{
            $result['status']=0;
        }
        $item['pic']='http://'.$_SERVER['HTTP_HOST'].'/qwtrwb/images/item/'.$item1->id.'v'.$item1->lastupdate.'.jpg';
        $item['km_content']=$item1->km_content;
        $item['id']=$item1->id;
        // var_dump($item);
        // exit();
        $view = "weixin/smfyun/rwb/gerenxinxi";

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
        //$order=
        $this->template->content = View::factory($view)
            ->bind('jsapi',$jsapi)
            ->bind('item',$item)
            ->bind('bid',$bid)
            ->bind('qid',$qid)
            ->bind('result', $result)
            ->bind('order', $order);
    }
    public function action_wxpay(){
        $item = ORM::factory('qwt_rwbitem')->where('id','=',$_POST['iid'])->find();
        if($_POST['oid']){
            $order=ORM::factory('qwt_rwborder')->where('id','=',$_POST['oid'])->find();
            if($order->id){
                $receive_name = $_POST['name'];
                $tel = $_POST['tel'];
                $address = $_POST['address'];
                $order->receive_name = $receive_name;
                $order->tel = $tel;
                $order->address = $address;
                $order->pay_money = $item->need_money/100;
                $order->save();
            }
        }
        if($_POST['iid']){
            if(!$item->id) {
                $result['error'] = '未找到奖品';
            }else{
                $config = ORM::factory('qwt_cfg')->getCfg($_POST['bid']);
                require_once Kohana::find_file('vendor/wx_pay', 'WeixinPay');
                $biz = ORM::factory('qwt_login')->where('id','=',$_POST['bid'])->find();
                $appid = $biz->appid;
                $qiz = ORM::factory('qwt_rwbqrcode')->where('id','=',$_POST['qid'])->find();
                $openid = $qiz->openid;
                $mch_id = $config['mchid'];
                $key = $config['apikey'];
                $out_trade_no = $mch_id.time();
                $total_fee = floor($item->need_money);
                $body = $item->km_content.'费用';
                $attach = base64_encode('qwt_rwborder:'.$_POST['oid'].':order_state');//表名 oid  字段状态
                $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/notify_url';
                $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$attach,$notify_url);
                $result=$weixinpay->pay();
            }
            echo json_encode($result);
        }
        exit;
    }
    public function action_ticket($cardId,$bid) {
        $this->template = 'tpl/blank';
        self::before();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $wx = new Wxoauth($bid,$options);
        $view = "weixin/qwt/ticket";
        $config=ORM::factory('qwt_rwbcfg')->getCfg($bid,1);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $jsapi = $wx->getJsSign($callback_url);
        $ticket = $wx->getJsCardTicket();
        $sign = $wx->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));
        $this->template->content = View::factory($view)
            ->bind('cardId', $cardId)
            ->bind('jsapi', $jsapi)
            ->bind('ticket', $ticket)
            ->bind('sign', $sign);
    }
    public function action_gerenxinxi() {
        $bid = 6;
        $view = "weixin/smfyun/rwb/gerenxinxi";
        $this->template->content = View::factory($view)
            ->bind('bid',$bid);
    }
    public function action_kmpass($id,$iid){
        $this->template = 'tpl/blank';
        self::before();
        $km_text =ORM::factory('qwt_rwbitem')->where('id','=',$iid)->find()->km_text;
        $password1 = ORM::factory('qwt_rwbkm')->where('id','=',$id)->find()->password1;
        $password2 = ORM::factory('qwt_rwbkm')->where('id','=',$id)->find()->password2;
        $password3 = ORM::factory('qwt_rwbkm')->where('id','=',$id)->find()->password3;
        $km_text = str_replace("「%a」",$password1,$km_text);
        $password = $password1;
        if($password2){
            $km_text = str_replace("「%b」",$password2,$km_text);
            $password = $password.','.$password2;
            if($password3){
                $km_text = str_replace("「%c」",$password3,$km_text);
                $password = $password.','.$password3;
            }
        }
        // echo $km_text;
        $view = "weixin/rwb/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('km_text', $km_text);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_rwb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
