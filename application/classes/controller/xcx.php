<?php defined('SYSPATH') or die('No direct script access.');

class Controller_xcx extends Controller_Base{
    public $template = 'tpl/blank';
    public function before() {
        Database::$default = "xcx";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_item(){
        if($_GET['code']){
            if($_GET['code']=='item'){
              $product=ORM::factory('xcx_product')->where('url','!=','0')->find_all();
              foreach ($product as $key => $value) {
                 $result[$key]['name']=$value->product_name;
                 $result[$key]['price']=$value->product_price;
                 $result[$key]['old_price']=$value->old_price;
                 $result[$key]['image']='http://www.smfyun.com'.$value->product_img;
                 $result[$key]['time']=$value->add_time;
                 $result[$key]['abstract']=$value->abstract;
                 $result[$key]['id']=$value->product_id;
              }
              $aaa= json_encode($result);
              echo $aaa;
            }else{
                $enddata = array('msg'=>'code码不正确');
                $rtjson =json_encode($enddata);
                echo $rtjson;
            }
        }else {
            $enddata = array('msg'=>'url不符合规范');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }

    }
    public function action_h5(){
        if($_GET['code']){
            if($_GET['code']=='h5'){
              $product=ORM::factory('xcx_product')->where('url','=','0')->where('product_id','!=',17)->find_all();
              foreach ($product as $key => $value) {
                 $result[$key]['name']=$value->product_name;
                 $result[$key]['price']=$value->product_price;
                 $result[$key]['old_price']=$value->old_price;
                 $result[$key]['image']='http://www.smfyun.com'.$value->product_img;
                 $result[$key]['time']=$value->add_time;
                 $result[$key]['abstract']=$value->abstract;
                 $result[$key]['id']=$value->product_id;
              }
              $aaa= json_encode($result);
              echo $aaa;
            }else{
                $enddata = array('msg'=>'code码不正确');
                $rtjson =json_encode($enddata);
                echo $rtjson;
            }
        }else {
            $enddata = array('msg'=>'url不符合规范');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }

    }
    public function action_sku(){
        if($_GET['pid']){
            $pid=$_GET['pid'];
            $sku=ORM::factory('xcx_sku')->where('product_id','=',$pid)->find_all();
            $product=ORM::factory('xcx_product')->where('product_id','=',$pid)->find();
            foreach ($sku as $key => $value) {
                $result[$key]['name']=$value->sku_name;
                $result[$key]['pro']=$value->sku_pro;
                $result[$key]['price']=$value->sku_price;
                $result[$key]['old_price']=$value->old_price;
                $result[$key]['date']=$value->other;
                $result[$key]['time']=$value->time;
                $result[$key]['sku_id']=$value->sku_id;

                $result[$key]['item_name']=$product->product_name;
                $result[$key]['item_price']=$product->product_price;
                $result[$key]['item_old_price']=$product->old_price;
                $result[$key]['item_image']='http://www.smfyun.com'.$product->product_img;
                $result[$key]['item_abstract']=$product->abstract;
                $result[$key]['item_id']=$product->product_id;
            }
            $aaa= json_encode($result);
            echo $aaa;

        }else {
            $enddata = array('msg'=>'url不符合规范');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }

    }
    public function action_onLogin(){
        if($_GET['code']){
          $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx963d1a274a56374f&secret=d75702999482039a1b56ed83eed59003&js_code=".$_GET['code']."&grant_type=authorization_code";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $output = curl_exec($ch);
          curl_close($ch);
          echo $output;
        }
    }
    public function action_notify(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('xcx_notify', print_r($postStr, true));

        require_once Kohana::find_file('vendor/Wxpay', 'example/WxPay.JsApiPay');
        require_once Kohana::find_file('vendor/Wxpay', 'lib/WxPay.Notify');
        require_once Kohana::find_file('vendor/Wxpay', 'lib/PayNotifyCallBack');

        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
        Kohana::$log->add('xcx_notify_end', print_r($notify, true));
    }
    public function action_wx_pay(){
      if($_GET["openid"]){
        $buyid = $_GET["buyid"];
        // $sku = ORM::factory('xcx_sku')->where('sku_id','=',$buyid)->find();
        //$product = ORM::factory('xcx_product')->where('product_id','=',$sku->product_id)->find();
        require_once Kohana::find_file('vendor/Wxpay', 'example/WxPay.JsApiPay');
        $tools = new JsApiPay();
        // $openId = $tools->GetOpenid();
        $openId = $_GET['openid'];
        $input = new WxPayUnifiedOrder();
        $input->SetBody('aaa');
        $input->SetAttach('ssa');
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        // $input->SetTotal_fee($sku->sku_price*100);
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url("https://xcx.smfyun.com/xcx/notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        // function printf_info($data){
        // foreach($data as $key=>$value){
        //         // echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        //     }
        // }
        // printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        echo ($jsApiParameters);
      }
    }
    public function action_order(){
        if($_GET['openid']){
          $orders = ORM::factory('xcx_order')->where('openid','=',$_GET['openid'])->order_by('time_end','DESC')->find_all();
          $my_orders = array();
          foreach ($orders as $k => $v) {
            $my_orders[$k]['attach'] = $v->attach;
            $my_orders[$k]['out_trade_no'] = $v->out_trade_no;
            $my_orders[$k]['transaction_id'] = $v->transaction_id;
            $my_orders[$k]['time_end'] = date("Y-m-d H:i:s",strtotime($v->time_end));
            $my_orders[$k]['total_fee'] = $v->total_fee/100;
            $my_orders[$k]['openid'] = $v->openid;
            $my_orders[$k]['price'] = $v->ordersku->sku_price;
            $my_orders[$k]['pro'] = $v->ordersku->sku_pro;
            $product = ORM::factory('xcx_product')->where('product_id','=', $v->ordersku->product_id)->find();
            $my_orders[$k]['product'] = $product->product_name;
          }
          echo json_encode($my_orders);
        }
    }
    public function action_music(){
        $url = "http://c.y.qq.com/v8/fcg-bin/fcg_v8_toplist_cp.fcg?g_tk=5381&uin=0&format=json&inCharset=utf-8&outCharset=utf-8&notice=0&platform=h5&needNewCode=1&tpl=3&page=detail&type=top&topid=4&_=1482905999489";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $output = curl_exec($ch);
          curl_close($ch);

          echo $output;
    }
    // public function action_send_tpl(){
    //     require_once Kohana::find_file('vendor/weixin', 'wechat.class');
    //     $config['appid'] = 'wx963d1a274a56374f';
    //     $config['appsecret'] = 'd75702999482039a1b56ed83eed59003';
    //     $we = new Wechat($config);

    //     $tplmsg['touser'] = 'oOkHq0HqPpbn_xS6tzssaGW8TQpI';
    //     $tplmsg['template_id'] = 'uH_yNKegsIpOJ_mtOb8uezzD62Cac9rIpDyrGLuIBX0';
    //     $tplmsg['page'] = 'pages/my/order/order';
    //     // $tplmsg['form_id'] = 'wx20170109133252c805bb199a0116794387';
    //     $tplmsg['form_id'] = "1483940854442";
    //     $tplmsg['data']['keyword1']['value'] = '1';
    //     $tplmsg['data']['keyword1']['color'] = '#FF0000';

    //     $tplmsg['data']['keyword2']['value'] = '2';
    //     $tplmsg['data']['keyword2']['color'] = '#FF0000';

    //     $tplmsg['data']['keyword3']['value'] = '3';
    //     $tplmsg['data']['keyword3']['color'] = '#FF0000';

    //     $result = $we->sendTemplateMessage_xcx($tplmsg);
    //     var_dump($result);
    // }
}
