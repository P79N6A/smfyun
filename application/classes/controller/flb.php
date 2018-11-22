<?php defined('SYSPATH') or die('No direct script access.');

class Controller_flb extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    public $methodVersion = '3.0.0';
    var $we;
    var $client;
    public function before() {
        Database::$default = "flb";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_delete($appid){
        $mem = Cache::instance('memcache');
        $cachename='wechat_access_token'.$appid;
        $result = $mem->get($cachename);
        var_dump($result);
        $result = $mem->delete($cachename);
        var_dump($result);
    }
    // public function action_detail(){
    //     $detail=ORM::factory('flb_detail')->where('bid','=',2)->find_all();
    //     foreach ($detail as $v) {
    //         echo $v->order->title.'<br>';
    //     }
    // }
    // public function action_detail1(){
    //     $this->bid=$bid=2;
    //     $this->access_token=ORM::factory('flb_login')->where('id', '=', $this->bid)->find()->access_token;
    //     require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
    //     $this->client = new KdtApiOauthClient();
    //     $method = 'kdt.trade.get';
    //         $params = [
    //          'tid' =>"E20161130182857075055895",
    //         ];
    //     $results = $this->client->post($this->access_token,$method,$params);
    //     echo "<pre>";
    //     var_dump($results);
    //     echo "</pre>";
    // }
    public function action_detail(){
        set_time_limit(0);
        $start_created = "2016-10-24 00:00:00";
        $end_created = "2016-12-04 00:00:00";
        $this->bid=$bid=2;
        $this->access_token=ORM::factory('flb_login')->where('id', '=', $this->bid)->find()->access_token;
        $config=ORM::factory('flb_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        if($this->access_token){
             $this->client = new KdtApiOauthClient();
        }else{
             echo "没有access_token";
        }
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'kdt.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>WAIT_SELLER_SEND_GOODS,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $this->client->post($this->access_token,$method,$params);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                // echo "<pre>";
                // var_dump($res);
                // echo "<pre>";
                $weixin_user_id =$res['weixin_user_id'];
                if($weixin_user_id){
                    $method = 'kdt.users.weixin.follower.get';
                    $params = [
                    'user_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $this->client->post($this->access_token,$method,$params);
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('flb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
                    $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode->nickname=$qrcodes['response']['user']['nick'];
                    $qrcode->headimgurl=$qrcodes['response']['user']['avatar'];
                    $qrcode->sex=$qrcodes['response']['user']['sex'];
                    $qrcode->save();
                }
                $status=$res['status'];
                $tid=$res['tid'];
                echo $tid.'<br>';
                $id=ORM::factory('flb_tid')->where('tid','=',$tid)->find()->id;
                Kohana::$log->add('tidid', print_r($id, true));
                if($id){
                    die("订单剔除");
                }
                Kohana::$log->add('bid', print_r($bid, true));
                Kohana::$log->add('tid', print_r($tid, true));
                $order = ORM::factory('flb_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();
                if($order->id){
                    Kohana::$log->add('order', print_r($order->id, true));
                }
                if($weixin_user_id){
                    $qid=ORM::factory('flb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                $order->bid=$bid;
                $order->tid=$res['tid'];
                $order->pic=$res['pic_thumb_path'];
                $order->num=$res['num'];
                $order->title=$res['title'];
                $order->price=$res['payment'];
                $order->shipping_type=$res['shipping_type'];
                $order->time=strtotime($res['update_time']);
                $order->receiver_name=$res['receiver_name'];
                $order->status=$status;
                $order->tel=$res['receiver_mobile'];
                $order->adress=$res['buyer_area'];
                if($status=='TRADE_CLOSED'){
                    $order->state=2;
                }else{
                    $order->state=1;
                }
                $order->save();
                $payment=$res['payment'];
                Kohana::$log->add('flbpayment', print_r($payment, true));
                $skus=ORM::factory('flb_sku')->where('bid','=',$bid)->where('start','<=',$payment)->where('end','>',$payment)->find();
                $num=$skus->times;
                Kohana::$log->add('flbnum', print_r($num, true));
                $chance=$skus->scale/100;
                Kohana::$log->add('flbchance', print_r($chance, true));
                $update_time=strtotime($res['update_time']);
                //$datetime=date('d',$pay_time);
                //Kohana::$log->add('datetime', print_r($datetime, true));
                $oid=ORM::factory('flb_order')->where('bid','=',$bid)->where('tid','=',$tid)->find()->id;
                Kohana::$log->add('flboid', print_r($oid, true));
                Kohana::$log->add('flbnum', print_r($num, true));
                Kohana::$log->add('flbstatus', print_r($status, true));
                if($status=='TRADE_CLOSED'){
                    //删除detail表
                    $result= DB::query(Database::DELETE,"DELETE from flb_details where `bid` = $bid and `oid` = $oid")->execute();
                }elseif ($num&&$status=='WAIT_SELLER_SEND_GOODS') {
                    $tid_name=$res['title'];
                    Kohana::$log->add('flbtid_name', print_r($tid_name, true));
                    $send_money=floor($payment*$chance);
                    Kohana::$log->add('flbsend_money', print_r($tid_name, true));
                    $send_num=$num;
                    // Kohana::$log->add('flbsend_num', print_r($send_num, true));
                    // if($config['msg_tpl']){
                    //     $keyword1=$tid_name;
                    //     $keyword2="感谢您在嘀的商城购买{$tid_name}\\n订单金额：{$payment}元\\n返还红包总额：{$send_money}元\\n分几次返还：{$send_num}\\n订单发货的8天后并且订单成功交易（无退款退货），将会进行第一次返还。剩余的会顺延到次月的28号，直至全部发放完毕。有问题请咨询客服哦~";
                    //     $result=$this->sendTemplateMessage1($openid,$config['msg_tpl'],'',$keyword1,$keyword2);
                    //     Kohana::$log->add('flbesult1', print_r($result, true));
                    // }else{
                    //     $keyword="感谢您在嘀的商城购买{$tid_name}，订单金额：{$payment}元，返还红包总额：{$send_money}元，分{$send_num}次返还,订单发货的8天后并且订单成功交易（无退款退货），将会进行第一次返还。剩余的会顺延到次月的28号，直至全部发放完毕。有问题请咨询客服哦~";
                    //     $result=$this->sendCustomMessage1($openid,$keyword);
                    //     Kohana::$log->add('flbesult2', print_r($result, true));
                    // }
                }else{
                    if($num&&floor($payment*$chance/$num)>=1){
                        $details= DB::query(Database::SELECT,"SELECT id from flb_details where `bid` = $bid and `oid` = $oid")->execute()->as_array();
                        Kohana::$log->add('details', print_r($details, true));
                        if(!$details[0]['id']){
                            for ($i=0; $i <$num ; $i++) {
                                $detail=ORM::factory('flb_detail');
                                $detail->bid=$bid;
                                $detail->oid=$oid;
                                $detail->num=$i+1;
                                $detail->money=floor($payment*$chance/$num);
                                if($i==0){
                                    Kohana::$log->add('i0', print_r($i, true));
                                   $a=date("Ymd",strtotime("+8days",$update_time));
                                   $b=$a*1000000+80000;
                                   $c=strtotime($b);
                                   $detail->time=$c;
                                }else{
                                    $f_time=date("Ymd",strtotime("+8days",$update_time));
                                    $g_time=$f_time*1000000+80000;
                                    $h_time=strtotime($g_time);
                                    Kohana::$log->add('$h_time', print_r($h_time, true));
                                    if(date('m',$update_time)== date('m',$h_time)){
                                        $num1=$i;
                                    }else{
                                        $num1=$i+1;
                                    }
                                    Kohana::$log->add('num1', print_r($num1, true));
                                    $time1="+".$num1."months";
                                    Kohana::$log->add('time1', print_r($time1, true));
                                    $a=date('Ym',strtotime($time1,$update_time));
                                    $b=$a*100000000+28080000;
                                    $c=strtotime($b);
                                    $detail->time=$c;
                                }
                                $detail->state=0;
                                $detail->save();
                            }
                        }
                    }
                }

            }
        }
    }
    // private function sendMoney($userobj, $money) {
    //     $bid=$this->bid=1;
    //     $config=$this->config=ORM::factory('flb_cfg')->getCfg($bid,1);
    //     $openid = $userobj->openid;
    //     if (!$this->we) {
    //         require_once Kohana::find_file('vendor', 'weixin/inc');
    //         require_once Kohana::find_file('vendor', 'weixin/wechat.class');
    //         $this->we = $we = new Wechat($config);
    //     }

    //     $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号
    //     $data["mch_appid"] = $config['appid'];
    //     $data["mchid"] = $config['partnerid']; //商户号
    //     $data["nonce_str"] = $this->we->generateNonceStr(32);
    //     $data["partner_trade_no"] = $mch_billno; //订单号

    //     $data["openid"] = $openid;
    //     $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
    //     // $data["re_user_name"] = $name; //收款用户姓名

    //     $data["amount"] = $money;
    //     $data["desc"] = $userobj->nickname.$config['title5'].'转出';

    //     $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

    //     $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
    //     $postXml = $this->we->xml_encode($data);

    //     $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    //     // Kohana::$log->add('weixin_fxb:hongbaopost', print_r($data, true));

    //     $resultXml = $this->curl_post_ssl($url, $postXml, 10);
    //     $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

    //     $result['xml'] = $resultXml;
    //     $result['return_code'] = (string)$response->return_code;
    //     $result['return_msg'] = (string)$response->return_msg[0];
    //     $result['result_code'] = (string)$response->result_code[0];
    //     $result['re_openid'] = (string)$response->re_openid[0];
    //     $result['total_amount'] = (string)$response->total_amount[0];
    //     $result['err_code'] = (string)$response->err_code[0];

    //     // Kohana::$log->add('weixin_fxb:hongbaoresult', print_r($result, true));
    //     return $result;
    // }
    public function action_cron($bid){
        set_time_limit(0);
        $this->bid=$bid;
        Kohana::$log->add('flb_cron111', print_r($bid, true));
        $config=ORM::factory('flb_cfg')->getCfg($bid,1);
        if ($config['status']==1){
            die ("手动发红包");
        }
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we=$we = new Wechat($config);
        $detail1s=ORM::factory('flb_detail')->where('bid','=',$bid)->where('state','=',0)->where('time','!=',0)->where('time','<=',time())->find_all();
        foreach ($detail1s as $v) {
            if($v->money1==0){
                $money=$v->money;
            }else{
                $money=$v->money1;
            }
            $money2=0;
            $oid=$v->oid;
            $tid_name=$v->order->title;
            $openid=ORM::factory('flb_order')->where('bid','=',$bid)->where('id','=',$oid)->find()->user->openid;
            $remain=$money%200;  //余数
            //Kohana::$log->add('remain', print_r($remain, true));
            $quot=floor($money/200);  //求商
            //Kohana::$log->add('quot', print_r($quot, true));
            //Kohana::$log->add('remain', print_r($remain, true));
            if($remain!=0){
                //Kohana::$log->add('remain', print_r($remain, true));
                $result=$this->hongbao1($config, $openid, $we, $bid, $remain);
                Kohana::$log->add("flbesult:$oid", print_r($result, true));
                if($result['result_code']!='SUCCESS'){
                    $money2+=$remain;
                }else{
                    $hb=ORM::factory('flb_hb');
                    $hb->bid=$bid;
                    $hb->did=$v->id;
                    $hb->money=$remain;
                    $hb->mch_billno=$result['mch_billno'];
                    $hb->save();
                }
            }
            for ($i=0; $i <$quot ; $i++) {
                $result=$this->hongbao1($config, $openid, $we, $bid, 200);
                Kohana::$log->add("flbesult:$oid", print_r($result, true));
                if($result['result_code']!='SUCCESS'){
                    $money2+=200;
                }else{
                    $hb=ORM::factory('flb_hb');
                    $hb->bid=$bid;
                    $hb->did=$v->id;
                    $hb->money=200;
                    $hb->mch_billno=$result['mch_billno'];
                    $hb->save();
                }
            }
            if($money2){
                $detail2=ORM::factory('flb_detail')->where('bid','=',$bid)->where('id','=',$v->id)->find();
                $detail2->money1=$money2;
                $detail2->save();
            }
            $detail1=ORM::factory('flb_detail')->where('bid','=',$bid)->where('id','=',$v->id)->find();
            if($result['result_code']=='SUCCESS'){
                Kohana::$log->add('flb_cron1', print_r($result, true));
                $detail1->state=1;
                $detail1->mch_billno=$result['mch_billno'];
                $detail1->save();
                $money1=$v->money;
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
                $detail1->log=$result['return_msg'];
                $detail1->save();
            }
        }
    }
    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('flb', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        //Kohana::$log->add('$result11', print_r($result11, true));
        if($postStr){
            //Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $appid =$result11['app_id'];
        //$id=$result11['id'];
        $msg=$result11['msg'];
        $kdt_id=$result11['kdt_id'];
        $status=$result11['status'];
        if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'){
            $bid = ORM::factory('flb_login')->where('shopid','=',$kdt_id)->find()->id;
            $this->bid=$bid;
            $this->access_token=ORM::factory('flb_login')->where('id', '=', $this->bid)->find()->access_token;
            $config=ORM::factory('flb_cfg')->getCfg($bid,1);
            $this->config=$config;
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $this->we=new Wechat($this->config);
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            if($this->access_token){
                $this->client = new KdtApiOauthClient();
            }else{
                Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
            }
            $posttid=urldecode($msg);
            $jsona=json_decode($posttid,true);
            $weixin_user_id =$jsona['trade']['weixin_user_id'];
            if($weixin_user_id){
                $method = 'kdt.users.weixin.follower.get';
                $params = [
                'user_id'=>$weixin_user_id,
                ];
                $qrcodes = $this->client->post($this->access_token,$method,$params);
                $openid=$qrcodes['response']['user']['weixin_openid'];
                $qrcode  = ORM::factory('flb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $qrcode->bid=$bid;
                $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
                $qrcode->nickname=$qrcodes['response']['user']['nick'];
                $qrcode->headimgurl=$qrcodes['response']['user']['avatar'];
                $qrcode->sex=$qrcodes['response']['user']['sex'];
                $qrcode->save();
            }
            $tid=$jsona['trade']['tid'];
            $id=ORM::factory('flb_tid')->where('tid','=',$tid)->find()->id;
            Kohana::$log->add('tidid', print_r($id, true));
            if($id){
                die("订单剔除");
            }
            Kohana::$log->add('bid', print_r($bid, true));
            Kohana::$log->add('tid', print_r($tid, true));
            $order = ORM::factory('flb_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($order->id){
                Kohana::$log->add('order', print_r($order->id, true));
            }
            if($weixin_user_id){
                $qid=ORM::factory('flb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                $order->qid=$qid;
            }
            $order->bid=$bid;
            $order->tid=$jsona['trade']['tid'];
            $order->pic=$jsona['trade']['pic_thumb_path'];
            $order->num=$jsona['trade']['num'];
            $order->title=$jsona['trade']['title'];
            $order->price=$jsona['trade']['payment'];
            $order->shipping_type=$jsona['trade']['shipping_type'];
            $order->time=strtotime($jsona['trade']['update_time']);
            $order->receiver_name=$jsona['trade']['receiver_name'];
            $order->status=$status;
            $order->tel=$jsona['trade']['receiver_mobile'];
            $order->adress=$jsona['trade']['buyer_area'];
            if($status=='TRADE_CLOSED'){
                $order->state=2;
            }else{
                $order->state=1;
            }
            $order->save();
            $payment=$jsona['trade']['payment'];
            Kohana::$log->add('flbpayment', print_r($payment, true));
            $skus=ORM::factory('flb_sku')->where('bid','=',$bid)->where('start','<=',$payment)->where('end','>',$payment)->find();
            $num=$skus->times;
            Kohana::$log->add('flbnum', print_r($num, true));
            $chance=$skus->scale/100;
            Kohana::$log->add('flbchance', print_r($chance, true));
            $update_time=strtotime($jsona['trade']['update_time']);
            //$datetime=date('d',$pay_time);
            //Kohana::$log->add('datetime', print_r($datetime, true));
            $oid=ORM::factory('flb_order')->where('bid','=',$bid)->where('tid','=',$tid)->find()->id;
            Kohana::$log->add('flboid', print_r($oid, true));
            Kohana::$log->add('flbnum', print_r($num, true));
            Kohana::$log->add('flbstatus', print_r($status, true));
            if($status=='TRADE_CLOSED'){
                //删除detail表
                $result= DB::query(Database::DELETE,"DELETE from flb_details where `bid` = $bid and `oid` = $oid")->execute();
            }elseif ($num&&$status=='WAIT_SELLER_SEND_GOODS') {
                $tid_name=$jsona['trade']['title'];
                Kohana::$log->add('flbtid_name', print_r($tid_name, true));
                $send_money=floor($payment*$chance);
                Kohana::$log->add('flbsend_money', print_r($tid_name, true));
                $send_num=$num;
                Kohana::$log->add('flbsend_num', print_r($send_num, true));
                if($config['msg_tpl']){
                    $keyword1=$tid_name;
                    $keyword2="感谢您在嘀的商城购买{$tid_name}\\n订单金额：{$payment}元\\n返还红包总额：{$send_money}元\\n分几次返还：{$send_num}\\n订单发货的8天后并且订单成功交易（无退款退货），将会进行第一次返还。剩余的会顺延到次月的28号，直至全部发放完毕。有问题请咨询客服哦~";
                    $result=$this->sendTemplateMessage1($openid,$config['msg_tpl'],'',$keyword1,$keyword2);
                    Kohana::$log->add('flbesult1', print_r($result, true));
                }else{
                    $keyword="感谢您在嘀的商城购买{$tid_name}，订单金额：{$payment}元，返还红包总额：{$send_money}元，分{$send_num}次返还,订单发货的8天后并且订单成功交易（无退款退货），将会进行第一次返还。剩余的会顺延到次月的28号，直至全部发放完毕。有问题请咨询客服哦~";
                    $result=$this->sendCustomMessage1($openid,$keyword);
                    Kohana::$log->add('flbesult2', print_r($result, true));
                }
            }elseif($status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'){
                if($num&&floor($payment*$chance/$num)>=1){
                    $details= DB::query(Database::SELECT,"SELECT id from flb_details where `bid` = $bid and `oid` = $oid")->execute()->as_array();
                    Kohana::$log->add('details', print_r($details, true));
                    if(!$details[0]['id']){
                        for ($i=0; $i <$num ; $i++) {
                            $detail=ORM::factory('flb_detail');
                            $detail->bid=$bid;
                            $detail->oid=$oid;
                            $detail->num=$i+1;
                            $detail->money=floor($payment*$chance/$num);
                            if($i==0){
                                Kohana::$log->add('i0', print_r($i, true));
                               $a=date("Ymd",strtotime("+8days",$update_time));
                               $b=$a*1000000+80000;
                               $c=strtotime($b);
                               $detail->time=$c;
                            }else{
                                $f_time=date("Ymd",strtotime("+8days",$update_time));
                                $g_time=$f_time*1000000+80000;
                                $h_time=strtotime($g_time);
                                Kohana::$log->add('$h_time', print_r($h_time, true));
                                if(date('m',$update_time)== date('m',$h_time)){
                                    $num1=$i;
                                }else{
                                    $num1=$i+1;
                                }
                                Kohana::$log->add('num1', print_r($num1, true));
                                $time1="+".$num1."months";
                                Kohana::$log->add('time1', print_r($time1, true));
                                $a=date('Ym',strtotime($time1,$update_time));
                                $b=$a*100000000+28080000;
                                $c=strtotime($b);
                                $detail->time=$c;
                            }
                            $detail->state=0;
                            $detail->save();
                        }
                    }
                }
            }
        }
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

}
