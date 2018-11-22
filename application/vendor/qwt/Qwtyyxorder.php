<?php
class Qwtyyxorder{
    public $methodVersion='3.0.0';
    public $bid;
    public $sid;
    public $msg;
    public $yzaccess_token;
    public $config;
    public $trade;
    public $orders;
    public $userinfo;
    var $wx;
    var $client;
    var $smfy;
    public function __construct($bid,$msg,$sid) {
        Kohana::$log->add("yyxbid", print_r($bid, true));
        Kohana::$log->add("yyxmsg", print_r($msg, true));
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->sid = $sid;
        $this->msg = $msg;
        $this->smfy=new SmfyQwt();
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $this->bid)->find()->yzaccess_token;
        if(!$this->yzaccess_token) throw new Exception('请授权有赞');
        $this->config=ORM::factory('qwt_kmicfg')->getCfg($bid,1);
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
        $sid=$this->sid;
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
        Kohana::$log->add("yyx:trade:$bid", print_r($trade, true));
        $num_iid = $trade['orders'][0]['item_id'];
        $item  = ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
        $method = 'kdt.item.get';
        $params = [
        'num_iid'=>$num_iid,
        ];
        $items = $this->client->post($method,'1.0.0', $params, $files);
        Kohana::$log->add("yyx:item:$bid", print_r($items, true));
        if($items['response']['item']['num_iid']){
            $item->bid=$bid;
            //$item->sid=$sid;
            $item->num_iid=$items['response']['item']['num_iid'];
            $item->alias=$items['response']['item']['alias'];
            $item->title=$items['response']['item']['title'];
            $item->tag_ids=$items['response']['item']['tag_ids'];
            $item->origin_price=$items['response']['item']['origin_price'];
            $item->price=$items['response']['item']['price'];
            $item->outer_id=$items['response']['item']['outer_id'];
            $item->created=$items['response']['item']['created'];
            $item->buy_quota=$items['response']['item']['buy_quota'];
            $item->is_virtual=$items['response']['item']['is_virtual'];
            $item->virtual_type=$items['response']['item']['virtual_type'];
            $item->is_used=$items['response']['item']['is_used'];
            $item->num=$items['response']['item']['num'];
            $item->pic_url=$items['response']['item']['pic_thumb_url'];
            $item->sold_num=$items['response']['item']['sold_num'];
            $item->post_type=$items['response']['item']['post_type'];
            $item->post_fee=$items['response']['item']['post_fee'];
            $item->item_type=$items['response']['item']['item_type'];
            $item->is_supplier_item=$items['response']['item']['is_supplier_item'];
            $item->join_level_discount=$items['response']['item']['join_level_discount'];
            $item->purchase_right=$items['response']['item']['purchase_right'];
            $item->save();
        }
        Kohana::$log->add("yyx:fans:$bid", print_r($trade['buyer_info'], true));
        $weixin_user_id =$trade['buyer_info']['fans_id'];
        Kohana::$log->add("yyx:fans_id:$bid", print_r($weixin_user_id, true));
        if($weixin_user_id&&$trade['buyer_info']['fans_type']==1){
            $method = 'youzan.users.weixin.follower.get';
            $params = [
            'fans_id'=>$weixin_user_id,
            ];
            $qrcodes = $this->client->post($method, $this->methodVersion, $params, $files);
            Kohana::$log->add("yyx:qrcodes:$bid", print_r($qrcodes, true));
            $openid=$qrcodes['response']['user']['weixin_openid'];
            $qrcode  = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $qrcode->bid=$bid;
            //$qrcode->sid=$sid;
            $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
            $qrcode->nick=$qrcodes['response']['user']['nick'];
            $qrcode->avatar=$qrcodes['response']['user']['avatar'];
            $qrcode->sex=$qrcodes['response']['user']['sex'];
            $qrcode->area=$qrcodes['response']['user']['province'].','.$qrcodes['response']['user']['city'];
            $qrcode->points=$qrcodes['response']['user']['points'];
            $qrcode->traded_num=$qrcodes['response']['user']['traded_num'];
            $qrcode->traded_money=$qrcodes['response']['user']['traded_money'];
            $qrcode->level_info=json_encode($qrcodes['response']['user']['level_info']);
            $qrcode->is_follow=$qrcodes['response']['user']['is_follow'];
            $qrcode->save();
        }
        $tid=$trade['order_info']['tid'];
        $order  = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('tid','=',$tid)->find();
        $iid=ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
        // Kohana::$log->add("yyx:qid:$bid", print_r($weixin_user_id, true));
        // Kohana::$log->add("yyx:qid:$bid", print_r($trade['buyer_info']['fans_type'], true));
        if($weixin_user_id&&$trade['buyer_info']['fans_type']==1){
            $qid=ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
            Kohana::$log->add("yyx:qid:$bid", print_r($qid, true));
            $order->qid=$qid;
        }
        if($trade['order_info']['type'] != 4){
            $order->bid=$bid;
            $order->iid=$iid;
            //$order->sid=$sid;
            $trade['num']=0;
            foreach ($trade['orders'] as $order1){
                $trade['num']=$trade['num']+$order1['num'];
            }
            $order->num= $trade['num'];
            $order->tid=$trade['order_info']['tid'];
            $order->title=$trade['orders'][0]['title'];
            $order->buyer_nick=$trade['address_info']['receive_name'];
            $order->type=$trade['order_info']['type'];
            $order->relation_type=$trade['relation_type'];
            $order->price=$trade['orders'][0]['price'];
           $order->pic_path=$trade['orders'][0]['pic_path'];
           // $order->buyer_type=$trade['buyer_type'];
           // $order->seller_flag=$trade['seller_flag'];
           $order->trade_memo=$trade['remark_info']['trade_memo'];
           // $order->relation_type=$trade['relation_type'];
           $order->receiver_state=$trade['address_info']['delivery_province'];
           $order->receiver_city=$trade['address_info']['delivery_city'];
           $order->receiver_district=$trade['address_info']['delivery_district'];
           $order->receiver_address=$trade['address_info']['delivery_address'];
           $order->receiver_mobile=$trade['address_info']['receiver_tel'];
           // $order->feedback=$trade['feedback'];
           $order->refund_state=$trade['order_info']['refund_state'];
           $order->status=$trade['order_info']['status'];
           $order->post_fee=$trade['pay_info']['post_fee'];
           $order->total_fee=$trade['pay_info']['total_fee'];
           $order->payment=$trade['pay_info']['payment'];
           $order->created=strtotime($trade['order_info']['created']);
           $order->update_time=strtotime($trade['order_info']['update_time']);
           $order->pay_type=$trade['order_info']['pay_type'];
           $order->points_price=$trade['orders'][0]['points_price'];
            $order->pay_time=strtotime($trade['order_info']['pay_time']);
            $order->save();
        }
    }
}
