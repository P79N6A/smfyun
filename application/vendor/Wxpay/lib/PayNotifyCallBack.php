<?php
class PayNotifyCallBack extends WxPayNotify{
  //查询订单
  public function Queryorder($transaction_id)
  {
    $input = new WxPayOrderQuery();
    $input->SetTransaction_id($transaction_id);
    $result = WxPayApi::orderQuery($input);
    // Log::DEBUG("query:" . json_encode($result));
    Kohana::$log->add('xcx_notify_1', print_r(json_encode($result), true));
    if(array_key_exists("return_code", $result)
      && array_key_exists("result_code", $result)
      && $result["return_code"] == "SUCCESS"
      && $result["result_code"] == "SUCCESS")
    {
      return true;
    }
    return false;
  }

  //重写回调处理函数
  public function NotifyProcess($data, &$msg)
  {
    // Log::DEBUG("call back:" . json_encode($data));
    Kohana::$log->add('xcx_notify_2', print_r(json_encode($data), true));
    Database::$default = "xcx";
    $order = ORM::factory('xcx_order')->where('out_trade_no','=',$data['out_trade_no'])->find();

    $match = explode(':',$data['attach'],2);
    $order->attach = $match[0];
    $order->out_trade_no = $data['out_trade_no'];
    $order->transaction_id = $data['transaction_id'];
    $order->time_end = $data['time_end'];
    $order->is_subscribe = $data['is_subscribe'];
    $order->total_fee = $data['total_fee'];
    $order->openid = $data['openid'];
    $order->save();


    $openid = $data['openid'];
    $template_id = 'uH_yNKegsIpOJ_mtOb8uezzD62Cac9rIpDyrGLuIBX0';
    $form_id = $match[1];
    $value = array();
    $product = ORM::factory('xcx_product')->where('product_id','=', $order->ordersku->product_id)->find();
    $value[0]= $product->product_name.'（'.$order->ordersku->sku_pro.'）';
    $value[1] = date("Y-m-d H:i:s",strtotime($data['time_end']));
    $value[2] = '￥'.$data['total_fee']/100;
    $this->send_tpl($openid,$template_id,$form_id,$value);

    $notfiyOutput = array();

    if(!array_key_exists("transaction_id", $data)){
      $msg = "输入参数不正确";

      return false;
    }
    //查询订单，判断订单真实性
    if(!$this->Queryorder($data["transaction_id"])){
      $msg = "订单查询失败";
      return false;
    }
    return true;
  }
  public function send_tpl($openid,$template_id,$form_id,$value){
      require_once Kohana::find_file('vendor/weixin', 'wechat.class');
      $config['appid'] = 'wx963d1a274a56374f';
      $config['appsecret'] = 'd75702999482039a1b56ed83eed59003';
      $we = new Wechat($config);

      $tplmsg['touser'] = $openid;
      $tplmsg['template_id'] = $template_id;
      $tplmsg['page'] = 'pages/my/order/order';
      // $tplmsg['form_id'] = 'wx20170109133252c805bb199a0116794387';
      $tplmsg['form_id'] = $form_id;
      $tplmsg['data']['keyword1']['value'] = $value[0];
      $tplmsg['data']['keyword1']['color'] = '#FF0000';

      $tplmsg['data']['keyword2']['value'] = $value[1];
      $tplmsg['data']['keyword2']['color'] = '#FF0000';

      $tplmsg['data']['keyword3']['value'] = $value[2];
      $tplmsg['data']['keyword3']['color'] = '#FF0000';

      $result = $we->sendTemplateMessage_xcx($tplmsg);
      // var_dump($result);
  }
}
