<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtpush extends Controller_Base{
    public $template = 'tpl/blank';
    public $methodVersion = '3.0.0';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'smfyun';

    public function before() {
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'qwttest') return;
        if (Request::instance()->action == 'md5') return;
    }
    public function action_qwttest1(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('qwtpostStr1111', print_r($postStr, true));
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
    }
    public function action_qwttest(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('qwtpostStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        Kohana::$log->add('qwtresult11', print_r($result11, true));
        $msg=$result11['msg'];
        $client_id='b8c8058d79f5cca370';
        $client_secret='d8e2924a0d4f342ba1266800d38d4da0';
        $sign_string = $client_id."".$msg."".$client_secret;
        $sign = md5($sign_string);
        Kohana::$log->add('sign1', print_r($sign, true));
        Kohana::$log->add('sign2', print_r($result11['sign'], true));
        if($sign != $result11['sign']){
            exit();
        }else{
            $type=$result11['type'];
            $pushid=$result11['id'];
            Kohana::$log->add('type', print_r($type, true));
            $kdt_id =$result11['kdt_id'];
            $bid = ORM::factory('qwt_login')->where('shopid','=',$kdt_id)->find()->id;
            //fans_2690783,E20180510112217094900007,yzuser_2626003
            Kohana::$log->add("$bid:$type:$pushid", print_r($postStr, true));
            $hasbuylog= ORM::factory('qwt_buy')->where('bid','=',$bid)->find_all()->as_array();
            $hasbuys=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('switch','=',1)->where('status','=',1)->where('expiretime','>=',time())->find_all();
            if($type=='TRADE_ORDER_STATE'){
                $status=$result11['status'];
                foreach ($hasbuys as $hasbuy) {
                    $item=ORM::factory('qwt_item')->where('id','=',$hasbuy->iid)->find();
                    Kohana::$log->add('iid', print_r($item->id, true));
                    if($item->orderflag==1){
                        if($item->alias=='yyx'){
                            $status=$result11['status'];
                            // echo 'aa';
                            if($result11['status']=='WAIT_SELLER_SEND_GOODS'||$result11['status']=='WAIT_BUYER_CONFIRM_GOODS'||$result11['status']=='TRADE_SUCCESS'||$result11['status']=='TRADE_CLOSED'){
                                //$shop = ORM::factory('qwt_yyxshop')->where('shopid','=',$kdt_id)->find();
                                //$sid=$shop->id;
                                require_once Kohana::find_file('vendor', 'qwt/Qwtyyxorder');
                                $qwtyyxorder = new  Qwtyyxorder($bid,$msg,1);
                                $qwtyyxorder->orderpush();
                            }

                        }
                        Kohana::$log->add('alias', print_r($item->alias, true));
                        if($item->alias=='kmi'){
                            $status=$result11['status'];
                            Kohana::$log->add('status', print_r($result11['status'], true));
                            Kohana::$log->add("$bid:kmiorder", print_r($bid, true));
                            if($result11['status']=='WAIT_SELLER_SEND_GOODS'||$result11['status']=='WAIT_BUYER_CONFIRM_GOODS'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtkmiorder');
                                $qwtkmiorder = new  Qwtkmiorder($bid,$msg);
                                $qwtkmiorder->orderpush();
                            }

                        }
                        if($item->alias=='mbb'){
                            $status=$result11['status'];
                            Kohana::$log->add('status', print_r($result11['status'], true));
                            Kohana::$log->add("$bid:mbborder", print_r($bid, true));
                            if($result11['status']=='WAIT_SELLER_SEND_GOODS'||$result11['status']=='WAIT_BUYER_CONFIRM_GOODS'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtmbborder');
                                $qwtmbborder = new  Qwtmbborder($bid,$msg);
                                $qwtmbborder->orderpush();
                            }

                        }
                        if($item->alias=='xdb'){
                            $status=$result11['status'];
                            Kohana::$log->add('status', print_r($result11['status'], true));
                            Kohana::$log->add("$bid:xdborder", print_r($bid, true));
                            if($result11['status']=='WAIT_SELLER_SEND_GOODS'||$result11['status']=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtxdborder');
                                $qwtxdborder = new  Qwtxdborder($bid,$msg,$status);
                                $qwtxdborder->orderpush();
                            }

                        }
                        if($item->alias=='yty'){
                            $status=$result11['status'];
                            Kohana::$log->add('status', print_r($result11['status'], true));
                            Kohana::$log->add("$bid:xdborder", print_r($bid, true));
                            if($result11['status']=='WAIT_SELLER_SEND_GOODS'||$result11['status']=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'||||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED_BY_USER'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtxdborder');
                                $qwtytyorder = new  Qwtytyorder($bid,$msg,$status);
                                $qwtytyorder->orderpush();
                            }

                        }
                        if($item->alias=='qfx'){
                            $status=$result11['status'];
                            Kohana::$log->add("$bid:dldorder", print_r($bid, true));
                            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtqfxorder');
                                $qwtqfxorder = new  Qwtqfxorder($bid,$msg,$status);
                                $qwtqfxorder->orderpush();
                            }
                        }
                        if($item->alias=='dld'){
                            $status=$result11['status'];
                            Kohana::$log->add("$bid:dldorder", print_r($status, true));
                            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtdldorder');
                                $qwtdldorder = new  Qwtdldorder($bid,$msg,$status);
                                $qwtdldorder->orderpush();
                            }
                        }
                        if($item->alias=='fxb'){
                            $status=$result11['status'];
                            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtfxborder');
                                $qwtfxborder = new  Qwtfxborder($bid,$msg,$status);
                                $qwtfxborder->orderpush();
                            }
                        }
                        if($item->alias == 'zdf'){
                            $status = $result11['status'];
                            if($status == 'WAIT_SELLER_SEND_GOODS'){
                                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                                require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
                                $yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
                                Kohana::$log->add('zdf_pay:yzaccess:{$bid}', print_r($bid, true));
                                Kohana::$log->add('zdf_pay:yzaccess:{$bid}', print_r($yzaccess_token, true));
                                $client = new YZTokenClient($yzaccess_token);
                                $appid = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
                                $options['token'] = $this->token;
                                $options['encodingaeskey'] = $this->encodingAesKey;
                                $options['appid'] = $appid;//商户appid
                                $wx = new Wxoauth($bid,$options);
                                $pay = ORM::factory('qwt_zdfpayed')->where('bid','=',$bid)->find();
                                if($pay->switch == 1){
                                    $posttid = urldecode($msg);
                                    Kohana::$log->add('zdf_pay1:{$bid}', print_r($posttid, true));
                                    $jsona = json_decode($posttid,true);
                                    Kohana::$log->add('zdf_pay2:{$bid}', print_r($jsona, true));
                                    $tid = $jsona['tid'];

                                    $method = 'youzan.trade.get';
                                    $params = [
                                        'with_childs'=>true,
                                        'tid'=>$tid,
                                    ];
                                    $result = $client->post($method, $this->methodVersion, $params, $files);
                                    Kohana::$log->add('zdf_pay3:{$bid}', print_r($result, true));
                                    $trade = $result['response']['trade'];
                                    $method = 'youzan.users.weixin.follower.get';
                                    $params = [
                                        'fans_id'=>$trade['fans_info']['fans_id'],
                                    ];

                                    $result = $client->post($method, $this->methodVersion, $params, $files);
                                    Kohana::$log->add('zdf_pay4:{$bid}', print_r($result, true));

                                    $msgs['touser'] = $result['response']['user']['weixin_openid'];
                                    // $msgs['touser'] = 'oDt2QjtTeio8l0dBl28SQGhcHSH4';
                                    $msgs['msgtype'] = 'miniprogrampage';
                                    $msgs['miniprogrampage']['title'] = $pay->msg->title;
                                    $msgs['miniprogrampage']['appid'] = $pay->msg->appid;
                                    if($pay->msg->path){
                                        $msgs['miniprogrampage']['pagepath'] = $pay->msg->path;
                                    }
                                    $msgs['miniprogrampage']['thumb_media_id'] = $pay->msg->media_id;
                                    Kohana::$log->add('zdf_paydata:{$bid}', print_r($msgs, true));
                                    $wx_result = $wx->sendCustomMessage($msgs);
                                    Kohana::$log->add('zdf_pay5:{$bid}', print_r($wx_result, true));
                                    if ($pay->text) {
                                        $msgs['touser'] = $result['response']['user']['weixin_openid'];
                                        // $msgs['touser'] = 'oDt2QjtTeio8l0dBl28SQGhcHSH4';
                                        $msgs['msgtype'] = 'text';
                                        $msgs['text']['content'] = '@'.$result['response']['user']['nick'].','.$pay->text;
                                        $wx_result = $wx->sendCustomMessage($msgs);
                                        Kohana::$log->add('zdf:pay:text:'.$bid,print_r($wx_result,true));
                                    }
                                }
                            }
                        }
                        if($item->alias == 'wzb'){
                            $status=$result11['status'];
                            Kohana::$log->add("$bid:wzborder", print_r($status, true));
                            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtwzborder');
                                $qwtwzborder = new  Qwtwzborder($bid,$msg,$status);
                                $qwtwzborder->orderpush();
                            }
                        }
                    }
                }
            }elseif ($type=='ITEM_STATE'||$type=='ITEM_INFO') {
                $status=$result11['status'];
                foreach ($hasbuys as $hasbuy) {
                    $item=ORM::factory('qwt_item')->where('id','=',$hasbuy->iid)->find();
                    if($item->itemflag==1){
                        if($item->alias=='dld'){
                            Kohana::$log->add('status', print_r($status, true));
                            if($status=='ITEM_DELETE'||$status=='ITEM_SALE_DOWN'||$status=='ITEM_SALE_UP'||$status=='SOLD_OUT_PART'||$status=='SOLD_OUT_ALL'||$status=='SOLD_OUT_REVERT'||$status=='ITEM_CREATE'||$status=='ITEM_UPDATE'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtdlditem');
                                $qwtdlditem = new  Qwtdlditem($bid,$msg,$status);
                                $qwtdlditem->itempush();
                            }
                        }
                        if($item->alias=='kmi'){
                            Kohana::$log->add('status', print_r($status, true));
                            if($status=='ITEM_DELETE'||$status=='ITEM_SALE_DOWN'||$status=='ITEM_SALE_UP'||$status=='SOLD_OUT_PART'||$status=='SOLD_OUT_ALL'||$status=='SOLD_OUT_REVERT'||$status=='ITEM_CREATE'||$status=='ITEM_UPDATE'){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtkmiitem');
                                $qwtkmiitem = new  Qwtkmiitem($bid,$msg,$status);
                                $qwtkmiitem->itempush();
                            }
                        }
                    }
                }
            }elseif ($type=='POINTS'){
                foreach ($hasbuys as $hasbuy) {
                    $item=ORM::factory('qwt_item')->where('id','=',$hasbuy->iid)->find();
                    if($item->scoreflag==1){
                        if($item->alias=='wfb'){
                            $wfbconfig=ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
                            if($wfbconfig['switch']==1){
                                require_once Kohana::find_file('vendor', 'qwt/Qwtwfbscore');
                                $Qwtwfbscore = new  Qwtwfbscore($bid,$msg);
                                $Qwtwfbscore->scorepush();
                            }
                        }
                    }
                }
            }elseif($type=='COUPON_PROMOTION'){
                $status=$result11['status'];
                Kohana::$log->add("couponmsg0", print_r($msg, true));
                $coupon_flag=0;
                foreach ($hasbuys as $hasbuy) {
                    $couponitem = array(3,4,5,8,9,10,11,13,15);
                    if(in_array($hasbuy->iid,$couponitem)){
                        $coupon_flag=1;
                    }
                }
                if($coupon_flag==1){
                    require_once Kohana::find_file('vendor', 'qwt/Qwtyzcoupon');
                    $Qwtwfbscore = new  Qwtyzcoupon($bid,$msg,$status);
                    $Qwtwfbscore->couponpush();
                }
            }
        }
    }
}
