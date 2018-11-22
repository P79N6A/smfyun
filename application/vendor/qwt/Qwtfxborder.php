<?php
class Qwtfxborder{
    public $methodVersion='3.0.0';
    public $bid;
    public $msg;
    public $yzaccess_token;
    public $config;
    public $status;
    public $baseurl='';
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
        $this->config=ORM::factory('qwt_fxbcfg')->getCfg($bid,1);
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
        Kohana::$log->add("$bid:$tid", print_r($trade, true));
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
        // print_r($trade);exit;
        $tid = $trade['order_info']['tid'];
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS', 'TRADE_CLOSED');

        if (!in_array($status, $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($trade['order_info']['status'], true));
        $qwt_fxbtrade = ORM::factory('qwt_fxbtrade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($qwt_fxbtrade->id) {

            //更新订单状态
            if ($qwt_fxbtrade->status != $trade['order_info']['status']) {
                $qwt_fxbtrade->status = $trade['order_info']['status'];
                $qwt_fxbtrade->save();

                //echo "$tid status updated.\n";
            }

            //退款订单删返利
            if ($trade['order_info']['status'] == 'TRADE_CLOSED') ORM::factory('qwt_fxbscore')->where('tid', '=', $qwt_fxbtrade->id)->delete_all();
            if ($trade['order_info']['status'] == 'TRADE_CLOSED_BY_USER') ORM::factory('qwt_fxbscore')->where('tid', '=', $qwt_fxbtrade->id)->delete_all();
            if ($trade['order_info']['refund_state'] != 0) ORM::factory('qwt_fxbscore')->where('tid', '=', $qwt_fxbtrade->id)->delete_all();

            //echo "$tid pass.\n";
            return;
        }
        Kohana::$log->add('11111', '111111');
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['order_info']['type'], true));
        if ($trade['order_info']['type'] != 0) return;
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
        //只处理有下线的订单
        $qwt_fxbqrcode = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($qwt_fxbqrcode->id, true));
        if (!$qwt_fxbqrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }
        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['order_info']['pay_time']);

        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $qwt_fxbqrcode->subscribe : $qwt_fxbqrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            //echo "$tid Time pass.\n";
            return;
        }

        $trade['qid'] = $qwt_fxbqrcode->id;
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
        Kohana::$log->add('money', print_r($moeny,true));
        // $average=$money/($money+$trade['discount_fee']);//权重
        // echo 'average';
        // var_dump($average);
        Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid){//有一级
            $rank=1;
            $ffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            if($ffuser->fopenid){//有二级
                $rank=2;
                $fffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
                if($fffuser->openid){//有三级
                    $rank=3;
                }
            }
        }
             $money0=$money1=$money2=$money3=0;
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
                $goodidcof=ORM::factory('qwt_fxbsetgood')->where('goodid','=',$goodid)->find();
                if($goodidcof->id)//用户单独配置了
                {
                    $money0=$money0+$tempmoney*$goodidcof->money0/100;
                    if($rank>=1) $money1=$money1+$tempmoney*$goodidcof->money1/100;
                    if($rank>=2) $money2=$money2+$tempmoney*$goodidcof->money2/100;
                    if($rank>=3&&$config['kaiguan_needpay']==1) $money3=$money3+$tempmoney*$goodidcof->money3/100;
                }
                else//没有配置就默认的数据
                {
                    $money0 =$money0+$tempmoney * $config['money0'] / 100; //自购
                    if($rank>=1) $money1 =$money1+$tempmoney * $config['money1'] / 100; //一级
                    if($rank>=2) $money2 =$money2+$tempmoney * $config['money2'] / 100; //二级
                    if($rank>=3&&$config['kaiguan_needpay']==1) $money3 =$money3+$tempmoney * $config['money3'] / 100; //三级
                }

             }

            $money0 = $trade['money0'] = number_format($money0, 2, '.', ''); //自购
            $money1 = $trade['money1'] = number_format($money1, 2, '.', ''); //一级
            $money2 = $trade['money2'] = number_format($money2, 2, '.', ''); //二级
            if($config['kaiguan_needpay']==1) $money3 = $trade['money3'] = number_format($money3, 2, '.', ''); //三级

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
        $qwt_fxbtrade->values($trade);
        $qwt_fxbtrade->save();
        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['item_id'];
            $num=$order['num'];
            $price=$order['total_fee'];
            $qwt_fxborder=ORM::factory('qwt_fxborder')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$qwt_fxborder->id)//跳过已导入的order
            {
                $qwt_fxborder->bid=$bid;
                $qwt_fxborder->tid=$tid;
                $qwt_fxborder->goodid=$goodid;
                $qwt_fxborder->title=$title;
                $qwt_fxborder->num=$num;
                $qwt_fxborder->price=$price;
                $qwt_fxborder->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('qwt_fxbscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('qwt_fxbscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('qwt_fxbscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money0', print_r($money0, true));
        //自购返利
        if ($money0 > 0) {
            //echo "$tid money0:$money0 \n";
            Kohana::$log->add('money0', print_r($money0, true));
            $qwt_fxbqrcode->scores->scoreIn($qwt_fxbqrcode, 1, $money0, 0, $qwt_fxbtrade->id);

            //发消息
            Kohana::$log->add('openid', print_r($qwt_fxbqrcode->openid, true));
            $msg['touser'] = $qwt_fxbqrcode->openid;
            $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
            Kohana::$log->add('cksum', print_r($cksum, true));
            // $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
            $url = "http://yingyong.smfyun.com/smfyun/user_snsapi_base/".$bid."/fxb/score";

            $msg['text']['content'] = "恭喜您完成一笔订单！\n\n实付金额：$money {$title5}\n系统返利：$money0 {$title5}\n\n<a href=\"$url\">查看我的{$title5}明细</a>";
            Kohana::$log->add('msg_score_tpl', print_r($config['msg_score_tpl'],true));
            if ($config['msg_score_tpl']){
                $wx_result = $this->sendScoreMessage($msg['touser'], '购买返利', $money0, $qwt_fxbqrcode->score, $url);
                Kohana::$log->add('ScoreMessage', print_r($wx_result, true));
            }
            else{
                $wx_result = $wx->sendCustomMessage($msg);
                Kohana::$log->add('CustomMessage', print_r($wx_result, true));
            }
        }

        //订单上线返利
        if ($money1 > 0) {
            $fuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $qwt_fxbqrcode->fopenid)->find();
            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                $fuser->scores->scoreIn($fuser, 2, $money1, $qwt_fxbqrcode->id, $qwt_fxbtrade->id);

                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                //$url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $url = "http://yingyong.smfyun.com/smfyun/user_snsapi_base/".$bid."/fxb/score";
                $msg['text']['content'] = "您推荐的{$config['title1']}「{$qwt_fxbqrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money1 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $wx_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->score, $url);
                else
                    $wx_result = $wx->sendCustomMessage($msg);
            }
        }

        //订单上上线返利
        if ($money2 > 0 && $fuser->fopenid) {
            $ffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            if ($ffuser->id) {
                //echo "$tid money2:$money2 \n";
                $ffuser->scores->scoreIn($ffuser, 3, $money2, $fuser->id, $qwt_fxbtrade->id);

                //发消息
                $msg['touser'] = $ffuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                //$url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $url = "http://yingyong.smfyun.com/smfyun/user_snsapi_base/".$bid."/fxb/score";
                $msg['text']['content'] = "您推荐的{$config['title2']}「{$qwt_fxbqrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money2 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $wx_result = $this->sendScoreMessage($msg['touser'], '好友的好友购买返利', $money2, $ffuser->score, $url);
                else
                    $wx_result = $wx->sendCustomMessage($msg);
            }
        }
        //订单上上上线返利  三级返利
        if ($money3 > 0 && $ffuser->fopenid&&$config['kaiguan_needpay']==1) {
            $fffuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $bid)->where('openid', '=', $ffuser->fopenid)->find();
            if ($fffuser->id) {
                //echo "$tid money3:$money3 \n";
                $fffuser->scores->scoreIn($fffuser, 3, $money3, $fuser->id, $qwt_fxbtrade->id);

                //发消息
                $msg['touser'] = $fffuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                //$url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $url = "http://yingyong.smfyun.com/smfyun/user_snsapi_base/".$bid."/fxb/score";
                $msg['text']['content'] = "您推荐的{$config['titlen3']}「{$qwt_fxbqrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money3 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $wx_result = $this->sendScoreMessage($msg['touser'], '好友的好友的好友购买返利', $money3, $fffuser->score, $url);
                else
                    $wx_result = $wx->sendCustomMessage($msg);

            }
        }
        //TODO:更多级别返利

        //echo "$tid done.\n";
        flush();ob_flush();
    }
     //收益模板消息：openid、类型、收益、总金额、网址
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
