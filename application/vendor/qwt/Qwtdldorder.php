<?php
class Qwtdldorder{
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
        $this->config=ORM::factory('qwt_dldcfg')->getCfg($bid,1);
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
        Kohana::$log->add('dld', print_r(111, true));
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        $setstatus=0;
        foreach ($trade['orders'] as $order) {
            $num_iid=$order['item_id'];
            $setgood=ORM::factory('qwt_dldsetgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
            if($setgood->status==1){
                $setstatus=1;
            }
        }

        if($setstatus==0){
            Kohana::$log->add('pass', print_r($trade, true));
            return;
        }
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS', 'TRADE_CLOSED');

        if (!in_array($status, $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($status, true));
        $qwt_dldtrade = ORM::factory('qwt_dldtrade')->where('tid', '=', $tid)->find();
        //跳过已导入订单
        if ($qwt_dldtrade->id) {
            //更新订单状态
            if ($qwt_dldtrade->status != $trade['order_info']['status']) {
                $qwt_dldtrade->status = $trade['order_info']['status'];
                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($status == 'TRADE_CLOSED'||$trade['order_info']['refund_state'] != 0){
                $qrcods_lv=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('tid','=',$tid)->find();
                // if($qrcods_lv->id) {
                //     $qrcods_lv->lv=4;
                //     $qrcods_lv->save();
                // }
                $qwt_dldtrade->deletedd=1;
                ORM::factory('qwt_dldscore')->where('tid', '=', $qwt_dldtrade->id)->delete_all();
            }
            if($status == 'TRADE_SUCCESS'){
                $score=ORM::factory('qwt_dldscore')->where('bid','=',$bid)->where('tid','=',$qwt_dldtrade->id)->find();
                $score->paydate=time();
                $score->save();
                $qwt_dldtrade->out_time=time();
            }
            $qwt_dldtrade->save();
            //echo "$tid pass.\n";
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
        $qwt_dldqrcode = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($qwt_dldqrcode->id, true));
        if (!$qwt_dldqrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }
        if(!$qwt_dldqrcode->receiver_mobile){
            $qwt_dldqrcode->receiver_mobile=$trade['address_info']['receiver_tel'];
            $qwt_dldqrcode->save();
             $qwt_dldqrcode = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        }
        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['order_info']['pay_time']);
        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $qwt_dldqrcode->subscribe : $qwt_dldqrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            //echo "$tid Time pass.\n";
            // return;
        }
        $trade['qid'] = $qwt_dldqrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        //计算返利金额
        Kohana::$log->add('8888', '8888');
        //某些特殊情况订单改价问题
         // $ordersumpayment = 0;
         // $trade['pay_info']['pay_change'];//订单改价
         // $trade['adjust_fee']['post_change'];//邮费改价
         // foreach ($trade['orders'] as $order) {
         //    $ordersumpayment = $ordersumpayment+$order['payment'];//计算出 各个商品花费价格
         // }
        $money  = $trade['money'] = $trade['pay_info']['payment'];//实付金额-改价后的邮费
        Kohana::$log->add('money', print_r($money,true));
        // $average=$money/($money+$trade['discount_fee']);//权重
        // Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid||$fuser->lv==1){//有一级
            $rank=1;
            if($fuser->lv==1){
                $ffuser = $fuser;
            }else{
                $ffuser = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            }
            $trade['fopenid'] = $ffuser->openid;
            if($ffuser->tid){
                $gointime=ORM::factory('qwt_dldtrade')->where('bid', '=', $bid)->where('tid','=',$ffuser->tid)->find()->int_time;
            }else{
                $gointime=$ffuser->jointime;
            }
            if($gointime>strtotime($trade['order_info']['pay_time'])) return;
        }
        $money1 = 0;
        Kohana::$log->add('trade[orders]', print_r($trade['orders'],true));
        Kohana::$log->add('orders', print_r($trade['orders'],true));
        foreach ($trade['orders'] as $order) {
            $item_price=$order['price'];
            $num_iid=$order['item_id'];
            $setgood=ORM::factory('qwt_dldsetgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
            Kohana::$log->add('sku_id', print_r($order['sku_id'],true));
            if($setgood->status==1){
                if($order['sku_id']&&$order['sku_id']!=0){
                    $sku_iid=$order['sku_id'];
                    $goodidcof=ORM::factory('qwt_dldgoodsku')->where('sku_id','=',$order['sku_id'])->where('bid','=',$bid)->find();
                }else{
                    $sku_iid=0;
                    $goodidcof=ORM::factory('qwt_dldsetgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                }
                if($ffuser->group_id==0){
                    $money1=$money1+$order['num']*($item_price-$goodidcof->money);
                }else{
                    $skumoney=ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$ffuser->group_id)->where('item_id','=',$num_iid)->where('sku_id','=',$sku_iid)->find();
                    $money1=$money1+$order['num']*($item_price-$skumoney->money);
                }
            }
            Kohana::$log->add('$money11', print_r($money1,true));
        }
        if($ffuser->lv==1){
            $money1 = $trade['money1'] = number_format($money1, 2,'.',''); //一级
        }
        if($qwt_dldqrcode->lv==1){
            $fuser = $qwt_dldqrcode;
        }else{
            $fuser = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $qwt_dldqrcode->fopenid)->find();
        }
        $group=ORM::factory('qwt_dldgroup')->where('bid','=',$bid)->where('qid','=',$fuser->id)->order_by('id','DESC')->find();
        $trade['gid']=$group->id;
        $trade['fopenid']=$fuser->openid;
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
        $qwt_dldtrade->values($trade);
        $qwt_dldtrade->save();
        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        // echo '<pre>';
        // var_dump($trade);
        // exit();
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $num_iid=$order['item_id'];
            $num=$order['num'];
            $price=$order['total_fee'];
            $qwt_dldorder=ORM::factory('qwt_dldorder')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$num_iid)->find();
            if(!$qwt_dldorder->id)//跳过已导入的order
            {
                $qwt_dldorder->bid=$bid;
                $qwt_dldorder->tid=$tid;
                $qwt_dldorder->goodid=$num_iid;
                $qwt_dldorder->title=$title;
                $qwt_dldorder->num=$num;
                $qwt_dldorder->price=$price;
                $qwt_dldorder->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('qwt_dldscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('qwt_dldscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('qwt_dldscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money0', print_r($money0, true));
        //订单上线返利
        if ($money1 > 0) {
            if($qwt_dldqrcode->lv==1){
                $fuser = $qwt_dldqrcode;
            }else{
                $fuser = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $qwt_dldqrcode->fopenid)->find();
            }

            if ($fuser->id) {
                $fuser->scores->scoreIn($fuser, 1, $money1, $qwt_dldqrcode->id, $qwt_dldtrade->id);
                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $msg['text']['content'] = "您推荐的「{$qwt_dldqrcode->nickname}」完成一笔订单！\n\n实付金额：$money 元\n销售利润：$money1 元\n\n";
                }
            }
        $newuser_self_tpl = "
销售利润+{$money1}元
个人销量+{$money}元
团队销量+{$money}元";
        $newuser_fuser_tpl = "
团队销量+{$money}元";
        if($qwt_dldqrcode->lv==2&&$trade['pay_info']['payment']>=$config['buy_money']){//已经申请了 并且金额也达到了要求
            $new = 1;
            $qwt_dldqrcode->lv = 1;
            $qwt_dldqrcode->receiver_mobile=$trade['address_info']['receiver_tel'];
            $qwt_dldqrcode->tid=$trade['order_info']['tid'];
            $qwt_dldqrcode->save();
            //自己购买之后 成为了代理商
            $msg['touser'] = $qwt_dldqrcode->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $config['text_self'];
            $wx->sendCustomMessage($msg);

            $qwt_dldgroup = ORM::factory('qwt_dldgroup');
            $qwt_dldgroup->qid = $qwt_dldqrcode->id;
            $fuser = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $qwt_dldqrcode->fopenid)->find();
            if($fuser->id) {
                $qwt_dldgroup->fqid = $fuser->id;
                $qwt_dldgroup->fgid = ORM::factory('qwt_dldgroup')->where('bid','=',$bid)->where('qid','=',$fuser->id)->order_by('id','DESC')->find()->id;
            }
            $qwt_dldgroup->bid = $bid;
            $qwt_dldgroup->save();
            $qwt_dldflag = 1;
            $loop_qrcode = $qwt_dldqrcode;
            $loop_group = $qwt_dldgroup;
            while($qwt_dldflag == 1){
                $loop_qrcode = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $loop_qrcode->fopenid)->find();
                $loop_group = ORM::factory('qwt_dldgroup')->where('bid', '=', $bid)->where('qid', '=', $loop_group->fqid)->order_by('id','DESC')->find();
                if($loop_qrcode->id){
                    //新代理进来之后 给直属上代理发
                    if($loop_qrcode->openid == $qwt_dldqrcode->fopenid){
                        $text = str_replace('%s', $qwt_dldqrcode->nickname, $config['text_direct']).$newuser_self_tpl;
                    }else{//新代理进来之后 给所有上级代理发
                        $text = str_replace('%s', $qwt_dldqrcode->nickname, $config['text_group']);
                        $text = str_replace('%t', $fuser->nickname, $text).$newuser_fuser_tpl;
                    }
                    $msg['touser'] = $loop_qrcode->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    $wx->sendCustomMessage($msg);

                    if(!strlen($loop_qrcode->bottom) > 0) {
                        $loop_qrcode->bottom = $qwt_dldqrcode->id;
                    }else{
                        $loop_qrcode->bottom = $loop_qrcode->bottom.','.$qwt_dldqrcode->id;
                    }
                    $loop_qrcode->save();
                    if(!strlen($qwt_dldqrcode->top) > 0) {
                        $qwt_dldqrcode->top = $loop_qrcode->id;
                    }else{
                        $qwt_dldqrcode->top = $loop_qrcode->id.','.$qwt_dldqrcode->top;
                    }
                    $qwt_dldqrcode->save();
                    if(!strlen($loop_group->bottom) > 0) {
                        $loop_group->bottom = $qwt_dldgroup->id;
                    }else{
                        $loop_group->bottom = $loop_group->bottom.','.$qwt_dldgroup->id;
                    }
                    $loop_group->save();
                }else{
                    $qwt_dldflag = 0;
                }
            }

        }
        // 已经是代理商情况
        // 没有上线的代理商 买东西 肯定能收到消息
        // 又上线的的代理商 买东西 自己和上面都有
        // 只要是代理商 就会轮询发

        //订单名称
        //订单金额
        //您的销售利润+
        //您的个人销量+
        //您的团队销量+
        $order_self_tpl = "
订单名称：{$trade['title']}元
订单金额：{$trade['money']}元
您的销售利润+{$money1}元
您的个人销量+{$money}元
您的团队销量+{$money}元 ";
        $order_fuser_tpl = "
订单名称：{$trade['title']}元
订单金额：{$trade['money']}元
您的团队销量+{$money}元 ";
        if($qwt_dldqrcode->lv==1&&$new!=1){//先给自己发一条
            $text = $config['text_selforder'];
            $msg['touser'] = $qwt_dldqrcode->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $text.$order_self_tpl;
            $wx->sendCustomMessage($msg);
            $fuser = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $qwt_dldqrcode->fopenid)->find();
            if(strlen($qwt_dldqrcode->top) > 0) {
                $fusers = explode(",",$qwt_dldqrcode->top);
                for ($i=0; $fusers[$i]; $i++) {
                    $now_user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('id','=',$fusers[$i])->find();
                    if($now_user->openid == $qwt_dldqrcode->fopenid){ //发直属上级
                        $text = str_replace('%s', $qwt_dldqrcode->nickname, $config['text_dirctorder']).$order_fuser_tpl;
                    }else{ //上级的上级
                        $text = str_replace('%s', $qwt_dldqrcode->nickname, $config['text_order']);
                        $text = str_replace('%t', $fuser->nickname, $text).$order_fuser_tpl;
                    }
                    $msg['touser'] = $now_user->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    if($now_user->lv==1) $wx->sendCustomMessage($msg);
                }
            }
        }
        //新来的代理商和普通的客户   循环他的上线发消息
        if($new ==1 ||($qwt_dldqrcode->lv != 1 && $qwt_dldqrcode->fopenid)){
            //发直属上级
            $qwt_dldfuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$qwt_dldqrcode->fopenid)->find();
            $text = str_replace('%s', $qwt_dldqrcode->nickname, $config['text_dirctorder']).$order_self_tpl;
            $msg['touser'] = $qwt_dldfuser->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $text;
            if($new!=1)$wx->sendCustomMessage($msg);//新代理就不发
            //发上级的上级
            if(strlen($qwt_dldfuser->top) > 0) {
                $fusers = explode(",",$qwt_dldfuser->top);
                for ($i=0; $fusers[$i]; $i++) {
                    $now_user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('id','=',$fusers[$i])->find();
                    $text = str_replace('%s', $qwt_dldqrcode->nickname, $config['text_order']);
                    $text = str_replace('%t', $qwt_dldfuser->nickname, $text).$order_fuser_tpl;
                    $msg['touser'] = $now_user->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    if($now_user->lv==1&&$new!=1) $wx->sendCustomMessage($msg);//新代理就不发
                }
            }
        }
        flush();ob_flush();
    }
}
