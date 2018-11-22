<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Yyx extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    var $we;
    var $client;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "yyx";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'delete') return;
        if (Request::instance()->action == 'login') return;
        if (Request::instance()->action == 'order') return;
        set_time_limit(0);
        $this->bid = $_SESSION['yyx']['bid'];
        $_SESSION['yyx']['bid'] = $this->bid;
        //未登录
        if (!$this->bid) {
            header('location:/yyx/login');
            exit;
        }
    }
    public function action_delete(){
        $_SESSION['yyx']['bid'] = '';
        if(!$_SESSION['yyx']['bid'])
        echo '清除成功'.$_SESSION['yyx']['bid'];
    }
    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add("yyxpostStr", print_r($postStr, true));
        $result11=json_decode($postStr,true);
        if($postStr){
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        Kohana::$log->add("yyxpostStr", print_r($result11, true));
        $appid =$result11['app_id'];
        $type=$result11['type'];
        if($type=='TRADE'){
            $msg=$result11['msg'];
            $kdt_id=$result11['kdt_id'];
            $status=$result11['status'];
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
                $shop = ORM::factory('yyx_shop')->where('shopid','=',$kdt_id)->find();
                $this->bid=$bid=$shop->bid;
                $sid=$shop->id;
                $this->access_token=$shop->access_token;
                $config=ORM::factory('yyx_cfg')->getCfg($bid,1);
                $this->config=$config;
                // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                // $this->we=new Wechat($this->config);
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                if($this->access_token){
                     $this->client = new YZTokenClient($this->access_token);
                }else{
                     Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                $posttid=urldecode($msg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("yyx1jsona", print_r($jsona, true));
                $num_iid = $jsona['trade']['num_iid'];
                $item  = ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                $method = 'kdt.item.get';
                $params = [
                'num_iid'=>$num_iid,
                ];
                $items = $this->client->post($method,'1.0.0', $params, $files);
                if($items['response']['item']['num_iid']){
                    $item->bid=$bid;
                    $item->sid=$sid;
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
                Kohana::$log->add("yyx:fans:$bid", print_r($jsona['trade']['fans_info'], true));
                $weixin_user_id =$jsona['trade']['fans_info']['fans_id'];
                Kohana::$log->add("yyx:fans_id:$bid", print_r($weixin_user_id, true));
                if($weixin_user_id&&$jsona['trade']['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $this->client->post($method, $this->methodVersion, $params, $files);
                    Kohana::$log->add("yyx:qrcodes:$bid", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
                    $qrcode->sid=$sid;
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
                $tid=$jsona['trade']['tid'];
                $mem = Cache::instance('memcache');
                $gettid = $mem->get($tid);
                if($gettid==$tid) return;
                $mem->set($tid, $tid, 1);
                $order  = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                if($weixin_user_id&&$jsona['trade']['fans_info']['fans_type']==1){
                    $qid=ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type'] != 'PRESENT'){
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->sid=$sid;
                    $order->num= $jsona['trade']['num'];
                    $order->tid=$jsona['trade']['tid'];
                    $order->title=$jsona['trade']['title'];
                    $order->buyer_nick=$jsona['trade']['buyer_nick'];
                    $order->type=$jsona['trade']['type'];
                    $order->relation_type=$jsona['trade']['relation_type'];
                    $order->price=$jsona['trade']['price'];
                   $order->pic_path=$jsona['trade']['pic_path'];
                   $order->buyer_type=$jsona['trade']['buyer_type'];
                   $order->seller_flag=$jsona['trade']['seller_flag'];
                   $order->trade_memo=$jsona['trade']['trade_memo'];
                   $order->relation_type=$jsona['trade']['relation_type'];
                   $order->receiver_state=$jsona['trade']['receiver_state'];
                   $order->receiver_city=$jsona['trade']['receiver_city'];
                   $order->receiver_district=$jsona['trade']['receiver_district'];
                   $order->receiver_address=$jsona['trade']['receiver_address'];
                   $order->receiver_mobile=$jsona['trade']['receiver_mobile'];
                   $order->feedback=$jsona['trade']['feedback'];
                   $order->refund_state=$jsona['trade']['refund_state'];
                   $order->status=$jsona['trade']['status'];
                   $order->post_fee=$jsona['trade']['post_fee'];
                   $order->total_fee=$jsona['trade']['total_fee'];
                   $order->payment=$jsona['trade']['payment'];
                   $order->created=strtotime($jsona['trade']['created']);
                   $order->update_time=strtotime($jsona['trade']['update_time']);
                   $order->pay_type=$jsona['trade']['pay_type'];
                   $order->points_price=$jsona['trade']['points_price'];
                    $order->pay_time=strtotime($jsona['trade']['pay_time']);
                    $order->save();
                }
            }
        }elseif ($type=='TRADE_ORDER_STATE') {
            $msg=$result11['msg'];
            $kdt_id=$result11['kdt_id'];
            $status=$result11['status'];
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                //$bid = ORM::factory('yyx_login')->where('shopid','=',$kdt_id)->find()->id;
                $shop = ORM::factory('yyx_shop')->where('shopid','=',$kdt_id)->find();
                $this->bid=$bid=$shop->bid;
                $sid=$shop->id;
                $this->access_token=$shop->access_token;
                $config=ORM::factory('yyx_cfg')->getCfg($bid,1);
                $this->config=$config;
                // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                // $this->we=new Wechat($this->config);
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                if($this->access_token){
                     $this->client = new YZTokenClient($this->access_token);
                }else{
                     Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                $posttid=urldecode($msg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("yyxjsona", print_r($jsona, true));
                $tid=$jsona['tid'];
                $mem = Cache::instance('memcache');
                $gettid = $mem->get($tid);
                if($gettid==$tid) return;
                $mem->set($tid, $tid, 1);
                $method = 'youzan.trade.get';
                $params = [
                    // 'with_childs'=>true,
                    'tid'=>$tid,
                ];
                $result = $this->client->post($method,'4.0.0', $params, $files);
                $trade=$result['response']['full_order_info'];
                Kohana::$log->add("yyx:trade:$bid", print_r($trade, true));
                $num_iid = $trade['orders'][0]['item_id'];
                $item  = ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                $method = 'kdt.item.get';
                $params = [
                'num_iid'=>$num_iid,
                ];
                $items = $this->client->post($method,'1.0.0', $params, $files);
                Kohana::$log->add("yyx:item:$bid", print_r($items, true));
                if($items['response']['item']['num_iid']){
                    $item->bid=$bid;
                    $item->sid=$sid;
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
                    $qrcode  = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
                    $qrcode->sid=$sid;
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
                $tid=$jsona['trade']['tid'];
                $order  = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                if($weixin_user_id&&$trade['buyer_info']['fans_type']==1){
                    $qid=ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($trade['order_info']['type'] != 4){
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $trade['num']=0;
                    foreach ($trade['orders'] as $order1){
                        $trade['num']=$trade['num']+$order1['num'];
                    }
                    $order->sid=$sid;
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

    }
    public function action_order($a,$b,$bid){
        set_time_limit(0);
        $start_created = $a." 00:00:00";
        $end_created = $b." 00:00:00";
        $this->bid=$bid;
        $this->access_token=ORM::factory('yyx_login')->where('id', '=', $this->bid)->find()->access_token;
        $config=ORM::factory('yyx_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
             $this->client = new YZTokenClient($this->access_token);
        }else{
             echo "没有access_token";
        }
         echo 'TRADE_BUYER_SIGNED<br>';
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>TRADE_BUYER_SIGNED,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $this->client->post($method, $this->methodVersion, $params, $files);
            var_dump($results);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                if($num_iid){
                    $item  = ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                    $method = 'kdt.item.get';
                    $params = [
                    'num_iid'=>$num_iid,
                    ];
                    $items = $this->client->post($method, '1.0.0', $params, $files);
                    if($items['response']['item']['num_iid']){
                        $item->bid=$bid;
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
                        // $item->ump_tags=json_encode($items['response']['item']['ump_tags']);
                        // $item->ump_level=json_encode($items['response']['item']['ump_level']);
                        // $item->ump_tags_text=$items['response']['item']['ump_tags_text'];
                        // $item->ump_level_text=$items['response']['item']['ump_level_text'];
                        $item->save();
                    }
                }
                $weixin_user_id =$res['fans_info']['fans_id'];
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $this->client->post($method, $this->methodVersion, $params, $files);
                    //Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
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
                $tid=$res['tid'];
                $order  = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                $iid;
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $qid=ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type'] != 'PRESENT'){
                    echo $res['tid'].'<br>';
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->num= $res['num'];
                    $order->tid=$res['tid'];
                    $order->title=$res['title'];
                    $order->buyer_nick=$res['buyer_nick'];
                    $order->type=$res['type'];
                    $order->price=$res['price'];
                   $order->pic_path=$res['pic_path'];
                   $order->relation_type=$res['relation_type'];
                   $order->buyer_type=$res['buyer_type'];
                   $order->seller_flag=$res['seller_flag'];
                   $order->trade_memo=$res['trade_memo'];
                   $order->receiver_state=$res['receiver_state'];
                   $order->receiver_city=$res['receiver_city'];
                   $order->receiver_district=$res['receiver_district'];
                   $order->receiver_address=$res['receiver_address'];
                   $order->receiver_mobile=$res['receiver_mobile'];
                   $order->feedback=$res['feedback'];
                   $order->refund_state=$res['refund_state'];
                   $order->status=$res['status'];
                   $order->post_fee=$res['post_fee'];
                   $order->total_fee=$res['total_fee'];
                   $order->payment=$res['payment'];
                   $order->created=strtotime($res['created']);
                   $order->update_time=strtotime($res['update_time']);
                   $order->pay_type=$res['pay_type'];
                   $order->points_price=$res['points_price'];
                    $order->pay_time=strtotime($res['pay_time']);
                    $order->save();
                }
            }
        }
        echo 'WAIT_BUYER_CONFIRM_GOODS<br>';
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>WAIT_BUYER_CONFIRM_GOODS,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $this->client->post($method, $this->methodVersion, $params, $files);
            var_dump($results);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                if($num_iid){
                    $item  = ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                    $method = 'youzan.item.get';
                    $params = [
                    'num_iid'=>$num_iid,
                    ];
                    $items = $this->client->post($this->access_token,$method,$params);
                    if($items['response']['item']['num_iid']){
                        $item->bid=$bid;
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
                        // $item->ump_tags=json_encode($items['response']['item']['ump_tags']);
                        // $item->ump_level=json_encode($items['response']['item']['ump_level']);
                        // $item->ump_tags_text=$items['response']['item']['ump_tags_text'];
                        // $item->ump_level_text=$items['response']['item']['ump_level_text'];
                        $item->save();
                    }
                }
                $weixin_user_id =$res['fans_info']['fans_id'];
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $this->client->post($method, $this->methodVersion, $params, $files);
                    //Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
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
                $tid=$res['tid'];
                $order  = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                $iid;
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $qid=ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type']!='PRESENT'){
                    echo $res['tid'].'<br>';
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->num= $res['num'];
                    $order->tid=$res['tid'];
                    $order->title=$res['title'];
                    $order->buyer_nick=$res['buyer_nick'];
                    $order->type=$res['type'];
                    $order->price=$res['price'];
                   $order->pic_path=$res['pic_path'];
                   $order->buyer_type=$res['buyer_type'];
                   $order->seller_flag=$res['seller_flag'];
                   $order->relation_type=$res['relation_type'];
                   $order->trade_memo=$res['trade_memo'];
                   $order->receiver_state=$res['receiver_state'];
                   $order->receiver_city=$res['receiver_city'];
                   $order->receiver_district=$res['receiver_district'];
                   $order->receiver_address=$res['receiver_address'];
                   $order->receiver_mobile=$res['receiver_mobile'];
                   $order->feedback=$res['feedback'];
                   $order->refund_state=$res['refund_state'];
                   $order->status=$res['status'];
                   $order->post_fee=$res['post_fee'];
                   $order->total_fee=$res['total_fee'];
                   $order->payment=$res['payment'];
                   $order->created=strtotime($res['created']);
                   $order->update_time=strtotime($res['update_time']);
                   $order->pay_type=$res['pay_type'];
                   $order->points_price=$res['points_price'];
                    $order->pay_time=strtotime($res['pay_time']);
                    $order->save();
                }
            }
        }
        echo 'WAIT_SELLER_SEND_GOODS<br>';
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>WAIT_SELLER_SEND_GOODS,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $this->client->post($method, $this->methodVersion, $params, $files);
            var_dump($results);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                if($num_iid){
                    $item  = ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                    $method = 'kdt.item.get';
                    $params = [
                    'num_iid'=>$num_iid,
                    ];
                    $items = $this->client->post($method, '1.0.0', $params, $files);
                    if($items['response']['item']['num_iid']){
                        $item->bid=$bid;
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
                        // $item->ump_tags=json_encode($items['response']['item']['ump_tags']);
                        // $item->ump_level=json_encode($items['response']['item']['ump_level']);
                        // $item->ump_tags_text=$items['response']['item']['ump_tags_text'];
                        // $item->ump_level_text=$items['response']['item']['ump_level_text'];
                        $item->save();
                    }
                }
                $weixin_user_id =$res['fans_info']['fans_id'];
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $this->client->post($method, $this->methodVersion, $params, $files);
                    //Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
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
                $tid=$res['tid'];
                $order  = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('yyx_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                $iid;
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $qid=ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type']!='PRESENT'){
                    echo $res['tid'].'<br>';
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->num= $res['num'];
                    $order->tid=$res['tid'];
                    $order->title=$res['title'];
                    $order->buyer_nick=$res['buyer_nick'];
                    $order->type=$res['type'];
                    $order->price=$res['price'];
                   $order->pic_path=$res['pic_path'];
                   $order->buyer_type=$res['buyer_type'];
                   $order->seller_flag=$res['seller_flag'];
                   $order->trade_memo=$res['trade_memo'];
                   $order->relation_type=$res['relation_type'];
                   $order->receiver_state=$res['receiver_state'];
                   $order->receiver_city=$res['receiver_city'];
                   $order->receiver_district=$res['receiver_district'];
                   $order->receiver_address=$res['receiver_address'];
                   $order->receiver_mobile=$res['receiver_mobile'];
                   $order->feedback=$res['feedback'];
                   $order->refund_state=$res['refund_state'];
                   $order->status=$res['status'];
                   $order->post_fee=$res['post_fee'];
                   $order->total_fee=$res['total_fee'];
                   $order->payment=$res['payment'];
                   $order->created=strtotime($res['created']);
                   $order->update_time=strtotime($res['update_time']);
                   $order->pay_type=$res['pay_type'];
                   $order->points_price=$res['points_price'];
                    $order->pay_time=strtotime($res['pay_time']);
                    $order->save();
                }
            }
        }
    }

    public function action_login($bname) {
        $this->template = 'weixin/yyx/tpl/login';

        $this->before();
        $this->template->username = ORM::factory('yyx_login')->where('user','=',$bname)->find()->name;

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('yyx_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                    $_SESSION['yyx']['bid'] = $biz->id;
                    $_SESSION['yyx']['user'] = $_POST['username'];
                    $_SESSION['yyx']['admin'] = $biz->admin; //超管
                    var_dump($_SESSION['yyx']);
                    // exit;
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['yyx']['bid']) {
            header('location:/yyx/map');
            exit;
        }
    }
    public function action_map(){//删除商户 token
        $bid = $this->bid;
        // Kohana::$log->add("bid", print_r($bid, true));
        //订单来源
        $result['yz_orders'] = ORM::factory('yyx_order')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('tid','!=','order')->count_all();
        $result['sd_orders'] = ORM::factory('yyx_order')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('tid','=','order')->count_all();
        //销售目标
        $result['sx_goal'] = ORM::factory('yyx_cfg')->where('bid','=',$bid)->where('key','=','goal')->find()->value;
        $result['done_goal'] = ORM::factory('yyx_order')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('created','>=',mktime(0,0,0,date('m'),1,date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('yyx_cfg')->where('bid','=',$bid)->where('key','=','goal1')->find()->value;//本月
        $result['undone_goal'] = $result['sx_goal']-$result['done_goal'];
        // 今日昨日交易
        $result['today_done'] = str_split(ORM::factory('yyx_order')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('created','>=',mktime(0,0,0,date('m'),date('d'),date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('yyx_cfg')->where('bid','=',$bid)->where('key','=','goal2')->find()->value);//今日

        $result['yestoday_done'] = ORM::factory('yyx_order')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('created','>=',mktime(0,0,0,date('m'),date('d')-1,date('Y')))->where('created','<=',mktime(0,0,0,date('m'),date('d'),date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('yyx_cfg')->where('bid','=',$bid)->where('key','=','goal3')->find()->value;//昨日

        $result['all_done'] = ORM::factory('yyx_order')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('yyx_cfg')->where('bid','=',$bid)->where('key','=','goal4')->find()->value;//累计交易额
        //热销商品
        $result['hot_items'] = DB::query(Database::SELECT,"select yyx_items.* from yyx_items where bid = $bid order by lv desc, sold_num desc limit 0,3")->execute()->as_array();
        //新老会员
        $result['old'] = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('traded_num','>',1)->count_all();
        $result['new'] = ORM::factory('yyx_qrcode')->where('bid','=',$bid)->where('traded_num','=',1)->count_all();
        // var_dump($result);
        // exit;
        //地理位置
        $result['orders'] = DB::query(Database::SELECT,"SELECT * FROM (SELECT * FROM yyx_orders where receiver_city!='' and bid=$bid and status!='TRADE_CLOSED' ORDER BY created DESC , has_show ASC) BIAOMING GROUP BY receiver_city ORDER BY created desc LIMIT 40")->execute()->as_array();
        for ($i=0; $result['orders'][$i]; $i++) {
            $t_location = ORM::factory('yyx_location')->where('city','=',$result['orders'][$i]['receiver_city'])->find();
            if($t_location->lng&&$t_location->lat){
                $lng = $t_location->lng;
                $lat = $t_location->lat;
            }else{
                $location = urlencode($result['orders'][$i]['receiver_city']);
                $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$location.'&output=json&ak=xS2SZp5OY5QNzy5gVaNbGYFaX4KkRtK9';
                $ch = curl_init(); // 初始化一个 cURL 对象
                curl_setopt($ch, CURLOPT_URL, $url); // 设置你需要抓取的URL
                curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $res = curl_exec($ch); // 运行cURL，请求网页
                curl_close($ch); // 关闭一个curl会话
                $json_obj = json_decode($res, true);
                $t_location->city = $result['orders'][$i]['receiver_city'];
                $t_location->lng = $lng = number_format($json_obj['result']['location']['lng'],3);
                $t_location->lat = $lat = number_format($json_obj['result']['location']['lat'],3);
                $t_location->save();
            }
            $this_order = ORM::factory('yyx_order')->where('bid','=',$bid)->where('id','=',$result['orders'][$i]['id'])->find();
            if($this_order->has_show!=1){
                $this_order->has_show = $this_order->has_show+1;
                $this_order->save();
            }
            $arr_location[$result['orders'][$i]['receiver_city']] = [$lng,$lat];
            $arr_order[$result['orders'][$i]['receiver_city']] = ['时间：'.date('Y-m-d H:i',$result['orders'][$i]['created']).'<br>地点：'.$result['orders'][$i]['receiver_state'].$result['orders'][$i]['receiver_city'].'<br>订单金额:'.$result['orders'][$i]['payment'].'元'];
            $arr_place[$i] = $result['orders'][$i]['receiver_city'];
            $order_details[$i][0] = date('Y-m-d H:i',$result['orders'][$i]['created']);
            $order_details[$i][1] = $result['orders'][$i]['receiver_state'].$result['orders'][$i]['receiver_city'];
            $order_details[$i][2] = $result['orders'][$i]['payment'];
        }
        //订单详情
        if($_GET['t']==1){
            $geoCoordMap = array('地理位置'=>$arr_location);
            $ordervalue = array('订单详情'=>$arr_order);
            $place = array('位置预设'=>$arr_place);
            $arr = array_merge($geoCoordMap,$ordervalue,$place);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==2){
            $source = array('订单来源'=>
                array(
                '有赞平台' => $result['yz_orders'],
                '自己平台' => $result['sd_orders']
            ));
            $goal = array('本月目标'=>
                array(
                '已完成' => $result['done_goal'],
                '未完成' => $result['undone_goal']
            ));
            $vip = array('成交会员'=>
                array(
                '新会员' => $result['old'],
                '老会员' => $result['new']
            ));
            $arr = array_merge($source,$goal,$vip);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==3){
            $today = array('今日成交额'=>$result['today_done']);
            $yel = array('昨日成交额'=>$result['yestoday_done']);
            $all = array('累计成交额'=>$result['all_done']);
            $arr = array_merge($today,$yel,$all);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==4){
            $orders = array('交易订单'=>$order_details);
            echo json_encode($orders);
            exit;
        }
        if($_GET['t']==5){
            if($result['hot_items'][0]['title']){
                $arr[0][0] = '1';
                $arr[0][1] = $result['hot_items'][0]['title'];
                $arr[0][2] = $result['hot_items'][0]['price'];
            }
            if($result['hot_items'][1]['title']){
                $arr[1][0] = '2';
                $arr[1][1] = $result['hot_items'][1]['title'];
                $arr[1][2] = $result['hot_items'][1]['price'];
            }
            if($result['hot_items'][2]['title']){
                $arr[2][0] = '3';
                $arr[2][1] = $result['hot_items'][2]['title'];
                $arr[2][2] = $result['hot_items'][2]['price'];
            }
            $items = array('热销商品'=>$arr);
            echo json_encode($items);
            exit;
        }

        $user = ORM::factory('yyx_login')->where('id','=',$bid)->find()->user;
        $view = 'weixin/yyx/'.$user;
        $this->template->content = View::factory($view)
                ->bind('result',$result)
                ->bind('arr_location',$arr_location)
                ->bind('arr_order',$arr_order)
                ->bind('order_details',$order_details)
                ->bind('arr_place',$arr_place);
    }
}
