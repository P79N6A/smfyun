<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtgl extends Controller_Base {
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
        if (Request::instance()->action == 'shiwu') return;
        $_SESSION =& Session::instance()->as_array();
    }

    public function after() {
        $this->template->user = $user;
        parent::after();
    }
    public function action_shiwu($iid){
        $this->template = 'tpl/blank';
        self::before();
        $oid=$_GET['oid'];
        $item1=ORM::factory('qwt_glitem')->where('id','=',$iid)->find();
        $order=ORM::factory('qwt_glorder')->where('id','=',$oid)->find();
        $bid=$item1->bid;
        $oid=$order->id;
        $item['need_money']=($item1->need_money)/100;
        if(!$order->id) die('您的url有误');
        if($order->id&&$_POST){
            // var_dump($_POST);
            // exit();
            $receive_name=$_POST['data']['name'];
            $tel=$_POST['data']['tel'];
            $address=$_POST['s_province'].$_POST['s_city'].$_POST['s_dist'].$_POST['data']['address'];
            $order->receive_name=$receive_name;
            $order->tel=$tel;
            $order->address=$address;
            $order->pay_money=$item['need_money'];
            $order->save();
        }
        if($order->address&&$order->tel){
            $result['status']=1;
            $neirong='';
            if($order->state==0){
                $result['neirong']='请耐心等待管理员发货';
            }else{
                $result['neirong']='您的奖品已发货，请注意查收';
            }
        }else{
            $result['status']=0;
        }
        $item['pic']='http://'.$_SERVER['HTTP_HOST'].'/qwtgl/images/item/'.$item1->id.'v'.$item1->lastupdate.'.jpg';
        $item['km_content']=$item1->name;
        $item['id']=$item1->id;
        // var_dump($item);
        // exit();
        $view = "weixin/smfyun/gl/gerenxinxi";

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
            ->bind('oid',$oid)
            ->bind('result', $result)
            ->bind('order', $order);
    }
    public function action_wxpay(){
        if($_POST['iid']){
            $item = ORM::factory('qwt_glitem')->where('bid','=',$_POST['bid'])->where('id','=',$_POST['iid'])->find();
            if(!$item->id) {
                $result['error'] = '未找到奖品';
            }else{
                $config = ORM::factory('qwt_cfg')->getCfg($_POST['bid']);
                require_once Kohana::find_file('vendor/lib', 'WeixinPay');
                $biz = ORM::factory('qwt_login')->where('id','=',$_POST['bid'])->find();
                $appid = $biz->appid;
                $qiz = ORM::factory('qwt_glorder')->where('id','=',$_POST['oid'])->find();
                $openid = $qiz->openid;
                $mch_id = $config['mchid'];
                $key = $config['apikey'];
                $out_trade_no = $mch_id.time();
                $total_fee = floor($item->need_money);
                $body = $item->name.'费用';
                $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
                $result=$weixinpay->pay();
            }
            echo json_encode($result);
        }
        exit;
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_gl$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
