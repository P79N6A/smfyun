<?php
class Qwtqfxorder{
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
        if(!$this->yzaccess_token) return;
        $this->config=ORM::factory('qwt_qfxcfg')->getCfg($bid,1);
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
        $trade=$result['response']['full_order_info'];
        if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
            $this->tradeImport($trade, $bid, $this->client, $this->wx, $config,$this->status);
        } else {
            $this->tradeImport($trade, $bid, $this->client, $this->wx, $config,$this->status);
        }
    }
    private function tradeImport($trade, $bid, $client, $wx, $config,$status) {
        $tid = $trade['order_info']['tid'];
        Kohana::$log->add('qwtqfxtrade', print_r($trade, true));
        Kohana::$log->add('qwtqfxbid', print_r($bid, true));
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS', 'TRADE_CLOSED');

        if (!in_array($status, $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('qwttrade1', print_r($trade['order_info']['status'], true));
        $qwt_qfxtrade = ORM::factory('qwt_qfxtrade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($qwt_qfxtrade->id) {

            //更新订单状态
            if ($qwt_qfxtrade->status != $trade['order_info']['status']) {
                $qwt_qfxtrade->status = $trade['order_info']['status'];
                $qwt_qfxtrade->save();
            }
            if ($status == 'TRADE_CLOSED'||$trade['order_info']['refund_state'] != 0) ORM::factory('qwt_qfxscore')->where('tid', '=', $qwt_qfxtrade->id)->delete_all();
            //订单完成金额 达到一定值 进行升级
            $method = 'youzan.users.weixin.follower.get';
            $params = [
                'fans_id'=>$trade['buyer_info']['fans_id'],
            ];
            $result = $client->post($method, $this->methodVersion, $params, $files);
            Kohana::$log->add('result', print_r($result, true));
            $userinfo = $result['response']['user'];
            $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
            $ffuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            $all_payment = ORM::factory('qwt_qfxtrade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
            Kohana::$log->add('all_payment', $all_payment);
            if($all_payment){
                $skus = DB::query(Database::SELECT,"SELECT * FROM qwt_qfxskus WHERE bid=$bid and `money`<= $all_payment")->execute()->as_array();
                Kohana::$log->add('skus', print_r($skus,true));
                Kohana::$log->add('sid', $skus[count($skus)-1]['id']);
                $ffuser->sid = $skus[count($skus)-1]['id'];
                $ffuser->save();
            }
            return;
        }
        Kohana::$log->add('11111', '111111');
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['order_info']['type'], true));
        if ($trade['order_info']['type'] != 0) return;

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
        //$userinfo = $this->youzanid2OpenID($trade['weixin_user_id'], $client);
        // print_r($userinfo);

        //只处理有下线的订单
        $qwt_qfxqrcode = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($qwt_qfxqrcode->id, true));
        if (!$qwt_qfxqrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }

        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['order_info']['pay_time']);

        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $qwt_qfxqrcode->subscribe : $qwt_qfxqrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            //echo "$tid Time pass.\n";
            return;
        }

        $trade['qid'] = $qwt_qfxqrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        //计算返利金额
        Kohana::$log->add('8888', '8888');
        //某些特殊情况订单改价问题
         // $ordersumpayment = 0;
         // $trade['adjust_fee']['pay_change'];//订单改价
         // $trade['adjust_fee']['post_change'];//邮费改价
         // foreach ($trade['orders'] as $order) {
         //    $ordersumpayment = $ordersumpayment+$order['payment'];//计算出 各个商品花费价格
         // }
        $money  = $trade['money'] = $trade['pay_info']['payment'] - $trade['pay_info']['post_fee'];//实付金额-改价后的邮费
        // echo 'postfee'.$trade['post_fee'].'<br>';
        // echo 'postch'.$trade['adjust_fee']['post_change'].'<br>';
        // var_dump($moeny);
        Kohana::$log->add('money', print_r($money,true));
        // $average=$money/($money+$trade['discount_fee']);
        // echo 'average';
        // var_dump($average);
        Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid){//有一级
            $rank=1;
            $ffuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            $trade['fopenid'] = $ffuser->openid;
        }
             $money1 = 0;
             // echo 'tradeorders';
             // var_dump($trade['orders']);
             Kohana::$log->add('trade[orders]', print_r($trade['orders'],true));
             foreach ($trade['orders'] as $order) {
                $tempmoney=$order['total_fee'];
                Kohana::$log->add('tempmoney', print_r($tempmoney,true));
                // Kohana::$log->add('orderpayment', print_r($orderpayment,true));
                // Kohana::$log->add('ordersumpayment', print_r($ordersumpayment,true));
                // echo 'tempmoney';
                // var_dump($tempmoney);
                $goodid=$order['item_id'];
                $goodidcof=ORM::factory('qwt_qfxsetgood')->where('goodid','=',$goodid)->find();
                //按照分销商等级 设置比例
                if($ffuser->sid!=0){
                    $config['money1'] = ORM::factory('qwt_qfxsku')->where('bid','=',$bid)->where('id','=',$ffuser->sid)->find()->scale;
                    Kohana::$log->add('scale', print_r($config['money1'],true));
                }
                if($goodidcof->id)//用户单独配置了
                {
                    if($rank>=1) $money1=$money1+$tempmoney*$goodidcof->money1/100;
                }
                else//没有配置就默认的数据
                {
                    if($rank>=1) $money1 =$money1+$tempmoney * $config['money1'] / 100; //一级
                }

             }

        if($ffuser->lv==1){
            $money1 = $trade['money1'] = number_format($money1, 2); //一级
        }
        //订单完成金额 达到一定值 进行升级
        $all_payment = ORM::factory('qwt_qfxtrade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
        if($all_payment){
            Kohana::$log->add('all_payment1', $all_payment);
            $skus = DB::query(Database::SELECT,"SELECT * FROM qwt_qfxskus WHERE bid=$bid and `money`<= $all_payment ")->execute()->as_array();
            Kohana::$log->add('all_payment', $all_payment);
            Kohana::$log->add('skus', print_r($skus,true));
            Kohana::$log->add('sid', $skus[count($skus)-1]['id']);
            if($skus[count($skus)-1]['id']){
                $ffuser->sid = $skus[count($skus)-1]['id'];
                $ffuser->save();
            }
        }
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
        $qwt_qfxtrade->values($trade);
        $qwt_qfxtrade->save();

        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['item_id'];
            $num=$order['num'];
            $price=$order['total_fee'];
            $qwt_qfxorder=ORM::factory('qwt_qfxorder')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$qwt_qfxorder->id)//跳过已导入的order
            {
                $qwt_qfxorder->bid=$bid;
                $qwt_qfxorder->tid=$tid;
                $qwt_qfxorder->goodid=$goodid;
                $qwt_qfxorder->title=$title;
                $qwt_qfxorder->num=$num;
                $qwt_qfxorder->price=$price;
                $qwt_qfxorder->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('qwt_qfxscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('qwt_qfxscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('qwt_qfxscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money1', print_r($money1, true));

        //订单上线返利
        if ($money1 > 0) {
            $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $qwt_qfxqrcode->fopenid)->find();
            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                $fuser->scores->scoreIn($fuser, 2, $money1, $qwt_qfxqrcode->id, $qwt_qfxtrade->id);

                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                // $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $url = 'http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$bid.'/qfx/score';
                //发消息通知
                $msg['text']['content'] = "您推荐的{$config['title1']}「{$qwt_qfxqrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money1 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl']){
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->score, $url);
                }
                else{
                    $we_result = $wx->sendCustomMessage($msg);
                }
                 Kohana::$log->add('result', print_r($we_result, true));

            }
        }

        flush();ob_flush();
    }
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔'.$this->config['title5'].'！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = ''.number_format($score, 2).'元';
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = '您的账户余额为'.number_format($total, 2).'元';
        $tplmsg['data']['remark']['color'] = '#666666';
        Kohana::$log->add("weixin_qfx:$bid:tplmsg", print_r($openid, true));
        Kohana::$log->add("weixin_qfx:$bid:tplmsg", print_r($tplmsg, true));
        return $this->wx->sendTemplateMessage($tplmsg);
    }
}
