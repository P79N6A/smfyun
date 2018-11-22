<?php
class Qwtxdborder{
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
        $this->config=ORM::factory('qwt_xdbcfg')->getCfg($bid,1);
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
        Kohana::$log->add('xdb', print_r(111, true));
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        $setstatus=0;
        foreach ($trade['orders'] as $order) {
            $num_iid=$order['item_id'];
            $setgood=ORM::factory('qwt_xdbsetgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
            if($setgood->status==1){
                $setstatus=1;
            }
        }

        if($setstatus==0){
            Kohana::$log->add("pass:$tid", print_r($trade, true));
            return;
        }
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS', 'TRADE_CLOSED');
        if (!in_array($status, $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($status, true));
        $qwt_xdbtrade = ORM::factory('qwt_xdbtrade')->where('tid', '=', $tid)->find();
        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'fans_id'=>$trade['buyer_info']['fans_id'],
        ];
        $result = $client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        $qwt_xdbqrcode = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        // $qorder=ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('qid','=',$qwt_xdbqrcode->id)->find();
        // if($qorder->id){
        //     Kohana::$log->add("pass:$qorder->id:$tid", print_r($trade, true));
        //     return;
        // }
        if($qwt_xdbqrcode->fopenid){
            $fuser = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $qwt_xdbqrcode->fopenid)->find();
        }else{
            Kohana::$log->add("passnof:$tid", print_r($trade, true));
            return;
        }
        Kohana::$log->add('id', print_r($qwt_xdbqrcode->id, true));
        if (!$qwt_xdbqrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }
        //跳过已导入订单
        if ($qwt_xdbtrade->id) {
            //更新订单状态
            if ($qwt_xdbtrade->status != $trade['order_info']['status']) {
                $qwt_xdbtrade->status = $trade['order_info']['status'];
                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($status == 'TRADE_CLOSED'||$trade['order_info']['refund_state'] != 0){
                $qwt_xdbtrade->deletedd=1;
                $qwt_xdbtrade->save();
            }
            if($status == 'TRADE_SUCCESS'){
                $qwt_xdbtrade->out_time=time();
                $qwt_xdbtrade->save();
                $successnum=ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('fopenid','=',$qwt_xdbqrcode->fopenid)->where('deletedd','=',0)->where('flag','=',0)->where('out_time','<=',time())->count_all();
                $tradetpl=$config['tradetpl'];
                $weixin_name=ORM::factory('qwt_login')->where('id','=',$bid)->find()->weixin_name;
                $neednum=$config['order_to_ticket']-$successnum%$config['order_to_ticket'];
                $text='恭喜您，您的好友'.$qwt_xdbqrcode->nickname.'完成了一笔订单。'.'您还需要好友完成'.$neednum.'笔订单您就可以获得一个兑换券了';
                if($tradetpl&&$successnum<$config['order_to_ticket']){
                    $result=$this->sendmsg1($qwt_xdbqrcode->fopenid,$tradetpl,$weixin_name,date('Y-m-d H:i:s',time()),$text);
                    Kohana::$log->add("$tid:sendmsgresult2", print_r($result,true));
                }
            }
            Kohana::$log->add("$tid:successnum", print_r($successnum,true));
            Kohana::$log->add("$tid:successnum2", print_r($config['order_to_ticket'],true));
            $successnum=ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('fopenid','=',$qwt_xdbqrcode->fopenid)->where('deletedd','=',0)->where('flag','=',0)->where('out_time','<=',time())->count_all();
            if($qwt_xdbqrcode->fopenid){
                if($successnum>=$config['order_to_ticket']){
                    $num=$successnum/$config['order_to_ticket'];
                    $sid=ORM::factory('qwt_xdbscore')->scoreIn($fuser,$num,$qwt_xdbqrcode->id);
                    $realnum=$num*$config['order_to_ticket'];
                    $success=ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('fopenid','=',$qwt_xdbqrcode->fopenid)->where('deletedd','=',0)->where('flag','=',0)->where('out_time','<=',time())->limit($realnum)->find_all();
                    foreach ($success as $key => $value) {
                        $value->flag=1;
                        $value->sid=$sid;
                        $value->save();
                    }
                    $url='http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$bid.'/xdb/index';
                    $scoretpl=$config['scoretpl'];
                    $text='恭喜您，获得了'.$num.'张兑换券了，请点击去使用吧';
                    $result=$this->sendmsg2($qwt_xdbqrcode->fopenid,$url,$scoretpl,$fuser->nickname,'兑换券',date('Y-m-d H:i:s',time()),$text);
                    Kohana::$log->add("$tid:sendmsgresult3", print_r($result,true));
                } 
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
        
        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['order_info']['pay_time']);
        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $qwt_xdbqrcode->subscribe : $qwt_xdbqrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
        }
        $trade['qid'] = $qwt_xdbqrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;
        Kohana::$log->add('8888', '8888');
        $money  = $trade['money'] = $trade['pay_info']['payment'];
        Kohana::$log->add('money', print_r($money,true));
        $rank=0;
        if($qwt_xdbqrcode->fopenid){
            $fuser = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $qwt_xdbqrcode->fopenid)->find();
        }
        if($qwt_xdbqrcode->fopenid){
            $trade['fopenid']=$qwt_xdbqrcode->fopenid;
        }
        
        $trade['int_time']=strtotime($trade['order_info']['pay_time']);
        $trade['out_time']=strtotime($trade['order_info']['pay_time'])+Date::DAY*7;
        $trade['num']=0;
        foreach ($trade['orders'] as $order1){
            $trade['num']=$trade['num']+$order1['num'];
        }
        $trade['tid']=$trade['order_info']['tid'];
        $trade['title']=$trade['orders'][0]['title'];
        $trade['pic_thumb_path']=$trade['orders'][0]['pic_path'];
        $trade['total_fee']=$trade['pay_info']['total_fee'];
        $trade['payment']=$trade['pay_info']['payment'];
        $trade['receiver_mobile']=$trade['address_info']['receiver_tel'];
        $trade['receiver_name']=$trade['address_info']['receiver_name'];
        $trade['receiver_city']=$trade['address_info']['delivery_city'];
        $trade['receiver_district']=$trade['address_info']['delivery_district'];
        $trade['receiver_state']=$trade['address_info']['delivery_province'];
        $trade['receiver_address']=$trade['address_info']['delivery_address'];
        $trade['post_fee']=$trade['pay_info']['post_fee'];
        $trade['pay_time']=$trade['order_info']['pay_time'];
        $trade['update_time']=$trade['order_info']['update_time'];
        $trade['status']=$trade['order_info']['status'];
        // $trade['pay_time']=$trade['order_info']['pay_time'];
        // $trade['pay_time']=$trade['order_info']['pay_time'];
        $qwt_xdbtrade->values($trade);
        $qwt_xdbtrade->save();
        if($qwt_xdbqrcode->fopenid){
            $successnum=ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('fopenid','=',$qwt_xdbqrcode->fopenid)->where('deletedd','=',0)->where('flag','=',0)->where('out_time','<=',time())->count_all();
            if($successnum>=$config['order_to_ticket']){
                $num=$successnum/$config['order_to_ticket'];
                $sid=ORM::factory('qwt_xdbscore')->scoreIn($fuser,$num,$qwt_xdbqrcode->id);
                $realnum=$num*$config['order_to_ticket'];
                $success=ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('fopenid','=',$qwt_xdbqrcode->fopenid)->where('deletedd','=',0)->where('flag','=',0)->where('out_time','<=',time())->limit($realnum)->find_all();
                foreach ($success as $key => $value) {
                    $value->flag=1;
                    $value->sid=$sid;
                    $value->save();
                }
                $url='http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$bid.'/xdb/index';
                $scoretpl=$config['scoretpl'];
                $text='恭喜您，获得了'.$num.'张兑换券了，请点击去使用吧';
                $result=$this->sendmsg2($qwt_xdbqrcode->fopenid,$url,$scoretpl,$fuser->nickname,'兑换券',date('Y-m-d H:i:s',time()),$text);
                Kohana::$log->add("$tid:sendmsgresult1", print_r($result,true));
            }
        }
        Kohana::$log->add('55555', '55555');
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $num_iid=$order['item_id'];
            $num=$order['num'];
            $price=$order['total_fee'];
            $qwt_xdborder=ORM::factory('qwt_xdborder')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$num_iid)->find();
            if(!$qwt_xdborder->id)//跳过已导入的order
            {
                $qwt_xdborder->bid=$bid;
                $qwt_xdborder->tid=$tid;
                $qwt_xdborder->goodid=$num_iid;
                $qwt_xdborder->title=$title;
                $qwt_xdborder->num=$num;
                $qwt_xdborder->price=$price;
                $qwt_xdborder->save();
            }
        }
        //新来的代理商和普通的客户   循环他的上线发消息
        flush();ob_flush();
    }
    private function sendmsg1($openid,$tpl,$keyword1,$keyword2,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = '#999999';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = '#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';
        Kohana::$log->add('couponmsgtpl', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('couponmsgresult', print_r($result, true));
        return $result;
    }
    private function sendmsg2($openid,$url,$tpl,$keyword1,$keyword2,$keyword3,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        $tplmsg['url'] = $url;
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = '#999999';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = '#999999';
        $tplmsg['data']['keyword3']['value'] = $keyword3;
        $tplmsg['data']['keyword3']['color'] = '#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';
        Kohana::$log->add('couponmsgtpl', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('couponmsgresult', print_r($result, true));
        return $result;
    }
}
