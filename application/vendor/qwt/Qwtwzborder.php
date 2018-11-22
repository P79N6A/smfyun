<?php
class Qwtwzborder{
    public $methodVersion='3.0.0';
    public $bid;
    public $msg;
    public $yzaccess_token;
    public $config;
    public $status;
    var $wx;
    var $client;
    var $smfy;
    public function __construct($bid,$msg,$status) {
        Kohana::$log->add("bid", print_r($bid, true));
        Kohana::$log->add("msg", print_r($msg, true));
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->msg = $msg;
        $this->status=$status;
        $this->smfy=new SmfyQwt();
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $this->bid)->find()->yzaccess_token;
        if(!$this->yzaccess_token) throw new Exception('请授权有赞');
        $this->config=ORM::factory('qwt_wzbcfg')->getCfg($bid,1);
        $this->client = new YZTokenClient($this->yzaccess_token);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
           $this->wx = new Wxoauth($this->bid,$options);
        }
    }
    public function orderpush(){
        $bid=$this->bid;
        $config=$this->config;
        $posttid=urldecode($this->msg);
        $jsona=json_decode($posttid,true);
        $tid=$jsona['tid'];
        $method = 'youzan.trade.get';
        $params = [
            // 'with_childs'=>true,
            'tid'=>$tid,
        ];
        $result = $this->client->post($method,'4.0.0', $params, $files);
        Kohana::$log->add("$bid:$tid", print_r($result, true));
        $trade=$result['response']['full_order_info'];
        if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
            $this->tradeImport($trade, $bid, $this->client, $this->wx, $config,$this->status);
        } else {
            $this->tradeImport($trade, $bid, $this->client, $this->wx, $config,$this->status);
        }
    }
    private function tradeImport($trade, $bid, $client, $wx, $config,$status) {
        // print_r($trade);exit;
        $tid = $trade['order_info']['tid'];
        Kohana::$log->add('wzb', print_r(111, true));
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        // $setstatus=0;
        // foreach ($trade['orders'] as $order) {
        //     $num_iid=$order['item_id'];
        //     $setgood=ORM::factory('qwt_wzbsetgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
        //     if($setgood->status==1){
        //         $setstatus=1;
        //     }
        // }

        // if($setstatus==0){
        //     Kohana::$log->add('pass', print_r($trade, true));
        //     return;
        // }
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS', 'TRADE_CLOSED');

        if (!in_array($status, $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($status, true));
        $qwt_wzbtrade = ORM::factory('qwt_wzbtrade')->where('tid', '=', $tid)->find();
        //跳过已导入订单
        if ($qwt_wzbtrade->id) {
            //更新订单状态
            if ($qwt_wzbtrade->status != $trade['order_info']['status']) {
                $qwt_wzbtrade->status = $trade['order_info']['status'];
                //echo "$tid status updated.\n";
            }
            return;
        }
        if($trade['order_info']['status']=='TRADE_CLOSED_BY_USER'){
            return;
        }
        Kohana::$log->add('11111', '111111');
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['order_info']['type'], true));
        if ($trade['order_info']['type'] != 0&&$trade['order_info']['type'] !=4) return;
        //男人袜不参与火种用户的商品
        Kohana::$log->add('payment', print_r($trade['pay_info']['payment'], true));
        //付款金额为 0
        if ($trade['pay_info']['payment'] <= 0) return;
        Kohana::$log->add('8888', '8888');

        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'fans_id'=>$trade['buyer_info']['fans_id'],
        ];

        $result = $client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        $wzb_qrcode = ORM::factory('qwt_wzbqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();

        if (!$wzb_qrcode->id) {
            return;
        }
        $trade['qid'] = $wzb_qrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        $trade['num']=0;
        foreach ($trade['orders'] as $order1){
            $trade['num']=$trade['num']+$order1['num'];
        }
        $trade['tid']=$trade['order_info']['tid'];
        $trade['title']=$trade['orders'][0]['title'];
        $trade['pic_thumb_path']=$trade['orders'][0]['pic_path'];
        $trade['total_fee']=$trade['pay_info']['total_fee'];
        $trade['payment']=$trade['pay_info']['payment'];
        // $trade['receiver_mobile']=$trade['address_info']['receiver_tel'];
        // $trade['receiver_name']=$trade['address_info']['receiver_name'];
        // $trade['receiver_city']=$trade['address_info']['delivery_city'];
        // $trade['receiver_district']=$trade['address_info']['delivery_district'];
        // $trade['receiver_state']=$trade['address_info']['delivery_province'];
        // $trade['receiver_address']=$trade['address_info']['delivery_address'];
        $trade['post_fee']=$trade['pay_info']['post_fee'];
        $trade['pay_time']=$trade['order_info']['pay_time'];
        $trade['update_time']=$trade['order_info']['update_time'];
        $trade['status']=$trade['order_info']['status'];
        // $trade['pay_time']=$trade['order_info']['pay_time'];
        // $trade['pay_time']=$trade['order_info']['pay_time'];
        $qwt_wzbtrade->values($trade);
        $qwt_wzbtrade->save();
        Kohana::$log->add('55555', '55555');
    }
}
