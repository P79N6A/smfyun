<?php defined('SYSPATH') or die('No direct script access.');

class Controller_tb extends Controller{
    public $bid;
    public $access_token = '62012150b76672c58cf84c5ZZ96c6a935a812d6acff67a0684778581';
    public $appkey = '23657631';
    public $secretKey = '3b94e6488bac4f18d9968263ac7a9a90';
    public function action_tb(){
        Request::instance()->redirect("https://oauth.taobao.com/authorize?response_type=code&client_id=23657631&redirect_uri=http://jfb.dev.smfyun.com/tb/callback&state=1212&view=web");
    }
    public function action_callback(){
        $code = $_GET['code'];
        $client_id=$_GET['client_id'];
        $client_secret=$secretKey;
        $url = 'https://oauth.taobao.com/token';
        $postfields= array('grant_type'=>'authorization_code',
        'client_id'=>$client_id,
        'client_secret'=>$client_secret,
        'code'=>$code,
        'redirect_uri'=>'https://'.$_SERVER["HTTP_HOST"].'/taobao/callback');
        $post_data = '';

        foreach($postfields as $key=>$value){
        $post_data .="$key=".urlencode($value)."&";}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);

        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post_data,0,-1));
        $output = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $httpStatusCode;
        curl_close($ch);
        var_dump($output);
        exit;
    }
    public function action_trades($bid){
        $this->bid = $bid;
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        Database::$default = "yyx";
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $c->format = 'json';
        $c->simplify = true;
        $req = new TradesSoldGetRequest;

        echo '<pre>';
        for($pg=1,$next=true;$next==true;$pg++){
            $req->setFields("created, pay_time,payment, total_fee,pic_path, modified,price,tid, status,buyer_nick,receiver_name,receiver_address, receiver_state, receiver_city, receiver_district,  receiver_mobile,seller_memo, post_fee,orders.sku_properties_name, orders.title,orders.price, orders.pic_path, orders.num, orders.num_iid, orders.outer_iid, orders.outer_sku_id,orders.cid");
            $req->setStartCreated("2016-10-19 00:00:00");
            $req->setEndCreated("2016-10-22 23:59:59");

            $req->setPageNo($pg);
            $req->setPageSize("20");
            $req->setUseHasNext("true");
            $results = json_decode(json_encode($c->execute($req, $this->access_token)),true);//不规则对象先转 json 再转数组
            echo $pg.'<br>';
            $next = $results['has_next'];
            echo $next.'<br>';
            print_r($results);

            for($i=0;$results['trades']['trade'][$i];$i++){
                $res=$results['trades']['trade'][$i];
                echo 'TRADES'.$res['status'];
                if($res['status']=='WAIT_SELLER_SEND_GOODS'||$res['status']=='WAIT_BUYER_CONFIRM_GOODS'||$res['status']=='TRADE_BUYER_SIGNED'||$res['status']=='TRADE_FINISHED'){
                    $num = 0;
                    for($a=0;$res['orders']['order'][$a];$a++){
                        $num = $num + $res['orders']['order'][$a]['num'];
                        // $price = $price + $res['orders']['order'][$a]['price'];
                        $this->action_item($res['orders']['order'][$a]['num_iid'],$c);
                    }
                    $iid =  ORM::factory('yyx_item')->where('bid','=',$this->bid)->where('num_iid','=',$res['orders']['order'][0]['num_iid'])->find()->id;
                     echo '1';
                    $tb_trade = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$res['tid'])->find();
                    if(!$tb_trade->id){
                        $tb_trade->platform = 'taobao';
                        $tb_trade->bid = $bid;
                        $tb_trade->iid = $iid;
                        $tb_trade->num = $num;
                        $tb_trade->tid = $res['tid'];
                        $tb_trade->title = $res['orders']['order'][0]['title'];
                        $tb_trade->buyer_nick = $res['buyer_nick'];
                        $tb_trade->price = $res['orders']['order'][0]['price'];//第一个商品的价格
                        $tb_trade->pic_path = $res['orders']['order'][0]['pic_path'];//第一个商品图片
                        $tb_trade->receiver_state = $res['receiver_state'];
                        $tb_trade->receiver_city = $res['receiver_city'];
                        $tb_trade->receiver_district = $res['receiver_district'];
                        $tb_trade->receiver_address = $res['receiver_address'];
                        $tb_trade->receiver_mobile = $res['receiver_mobile'];
                        $tb_trade->status = $res['status'];
                        $tb_trade->post_fee = $res['post_fee'];
                        $tb_trade->total_fee = $res['total_fee'];
                        $tb_trade->payment = $res['payment'];
                        $tb_trade->created = strtotime($res['created']);
                        $tb_trade->update_time = strtotime($res['modified']);
                        $tb_trade->pay_time = $res['pay_time'];
                        echo '新订单';
                    }else{
                        if($tb_trade->status==$res['status']){
                            echo '状态未更新！pass';
                        }else{
                            $tb_trade->update_time = strtotime($res['modified']);
                            $tb_trade->status = $res['status'];
                            echo '状态更新';
                        }
                    }
                    $tb_trade->save();
                    // echo 'bid:'.$bid.'<br>';
                    // echo 'iid:'.$res['tid'].'<br>';
                    // echo 'num:'.$num.'<br>';
                    // echo 'product_id:'.$res['orders']['order'][0]['num_iid'].'<br>';
                    // echo 'tid:'.$res['tid'].'<br>';
                    // echo 'title:'.$res['orders']['order'][0]['title'].'<br>';
                    // echo 'buyer_nick:'.$res['buyer_nick'].'<br>';
                    // echo 'price:'.$price.'<br>';
                    // echo 'pic_path:'.$res['orders']['order'][0]['pic_path'].'<br>';
                    // echo 'receiver_state:'.$res['receiver_state'].'<br>';
                    // echo 'receiver_city:'.$res['receiver_city'].'<br>';
                    // echo 'receiver_district:'.$res['receiver_district'].'<br>';
                    // echo 'receiver_address:'.$res['receiver_address'].'<br>';
                    // echo 'receiver_mobile:'.$res['receiver_mobile'].'<br>';
                    // echo 'status:'.$res['status'].'<br>';
                    // echo 'post_fee:'.$res['post_fee'].'<br>';
                    // echo 'total_fee:'.$res['total_fee'].'<br>';
                    // echo 'payment:'.$res['payment'].'<br>';
                    // echo 'created:'.$res['created'].'<br>';
                    // echo 'update_time:'.$res['modified'].'<br>';
                    // echo 'pay_time:'.$res['pay_time'].'<br>';
                    // echo '2';
                }
                // exit;
            }
        }
        exit;
    }
    public function action_increment($bid){
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        Database::$default = "yyx";
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $c->format = 'json';
        $c->simplify = true;
        $req = new TradesSoldIncrementGetRequest;
        echo '<pre>';
        $num = 0;
        $price = 0;
        for($pg=1,$next=true;$next==true;$pg++){
            $req->setFields("created, pay_time,payment, total_fee,pic_path, modified,price,tid, status,buyer_nick,receiver_name,receiver_address, receiver_state, receiver_city, receiver_district,  receiver_mobile,seller_memo, post_fee,orders.sku_properties_name, orders.title,orders.price, orders.pic_path, orders.num, orders.num_iid, orders.outer_iid, orders.outer_sku_id");
            $req->setStartModified("2016-10-19 00:00:00");
            $req->setEndModified("2016-10-19 23:59:59");

            $req->setPageNo($pg);
            $req->setPageSize("20");
            $req->setUseHasNext("true");
            $results = json_decode(json_encode($c->execute($req, $this->access_token)),true);//不规则对象先转 json 再转数组
            echo $pg.'<br>';
            $next = $results['has_next'];
            echo $next.'<br>';
            // print_r($results);

            for($i=0;$results['trades']['trade'][$i];$i++){
                $res=$results['trades']['trade'][$i];
                echo 'TRADES'.$res['status'];
                if($res['status']=='WAIT_SELLER_SEND_GOODS'||$res['status']=='WAIT_BUYER_CONFIRM_GOODS'||$res['status']=='TRADE_BUYER_SIGNED'||$res['status']=='TRADE_FINISHED'){
                    for($a=0;$res['orders']['order'][$a];$a++){
                        $num = $num + $res['orders']['order'][$a]['num'];
                        $price = $price + $res['orders']['order'][$a]['price'];
                         echo '0';
                    }
                     echo '1';
                    $tb_trade = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$res['tid'])->find();
                    if(!$tb_trade->id){
                        $tb_trade->platform = 'taobao';
                        $tb_trade->bid = $bid;
                        $tb_trade->iid = 1;
                        $tb_trade->num = $num;
                        $tb_trade->tid = $res['tid'];
                        $tb_trade->title = $res['orders']['order'][0]['title'];
                        $tb_trade->buyer_nick = $res['buyer_nick'];
                        $tb_trade->price = $price;//总价格
                        $tb_trade->pic_path = $res['orders']['order'][0]['pic_path'];//第一个商品图片
                        $tb_trade->receiver_state = $res['receiver_state'];
                        $tb_trade->receiver_city = $res['receiver_city'];
                        $tb_trade->receiver_district = $res['receiver_district'];
                        $tb_trade->receiver_address = $res['receiver_address'];
                        $tb_trade->receiver_mobile = $res['receiver_mobile'];
                        $tb_trade->status = $res['status'];
                        $tb_trade->post_fee = $res['post_fee'];
                        $tb_trade->total_fee = $res['total_fee'];
                        $tb_trade->payment = $res['payment'];
                        $tb_trade->created = strtotime($res['created']);
                        $tb_trade->update_time = strtotime($res['modified']);
                        $tb_trade->pay_time = $res['pay_time'];
                        echo '新订单';
                    }else{
                        if($tb_trade->status==$res['status']){
                            echo '状态未更新！pass';
                        }else{
                            $tb_trade->update_time = strtotime($res['modified']);
                            $tb_trade->status = $res['status'];
                            echo '状态更新';
                        }
                    }
                    $tb_trade->save();
                    echo 'bid:'.$bid.'<br>';
                    echo 'iid:'.$res['tid'].'<br>';
                    echo 'num:'.$num.'<br>';
                    echo 'tid:'.$res['tid'].'<br>';
                    echo 'title:'.$res['orders']['order'][0]['title'].'<br>';
                    echo 'buyer_nick:'.$res['buyer_nick'].'<br>';
                    echo 'price:'.$price.'<br>';
                    echo 'pic_path:'.$res['orders']['order'][0]['pic_path'].'<br>';
                    echo 'receiver_state:'.$res['receiver_state'].'<br>';
                    echo 'receiver_city:'.$res['receiver_city'].'<br>';
                    echo 'receiver_district:'.$res['receiver_district'].'<br>';
                    echo 'receiver_address:'.$res['receiver_address'].'<br>';
                    echo 'receiver_mobile:'.$res['receiver_mobile'].'<br>';
                    echo 'status:'.$res['status'].'<br>';
                    echo 'post_fee:'.$res['post_fee'].'<br>';
                    echo 'total_fee:'.$res['total_fee'].'<br>';
                    echo 'payment:'.$res['payment'].'<br>';
                    echo 'created:'.$res['created'].'<br>';
                    echo 'update_time:'.$res['modified'].'<br>';
                    echo 'pay_time:'.$res['pay_time'].'<br>';
                    echo '2';
                }
                // exit;
            }
        }
        // foreach ($resp->trades as $trade) {
        //     foreach ($trade as $order) {
        //         echo $order->tid;
        //         $tid = $order->tid;
        //         var_dump($tid);
        //         echo "<pre>";
        //         print_r($this->FullTrade($tid, $c));
        //     }
        // }
        exit;
    }
    public function action_item($iid,$c){
        $this->bid = 1;
        $resitem = new ItemSellerGetRequest;
        $resitem->setNumIid($iid);
        $resitem->setFields("title,num,price,pic_url,sale_num,product_id");
        $results = json_decode(json_encode($c->execute($resitem, $this->access_token)),true);//不规则对象先转 json 再转数组
        echo 'bid'.$this->bid.'<br>';
        echo 'num_iid'.$iid.'<br>';
        $item = ORM::factory('yyx_item')->where('bid','=',$this->bid)->where('num_iid','=',$iid)->find();
        $item->platform = 'taobao';
        $item->bid = $this->bid;
        $item->num_iid = $iid;
        $item->title = $results['item']['title'];
        $item->price = $results['item']['price'];
        $item->pic_url = $results['item']['pic_url'];
        $item->num = $results['item']['num'];
        $item->save();
        // $item->product_id = $results['product_id'];
        echo '<pre>';
        var_dump($results);
    }
    public function action_permit(){//开通消息权限
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TmcUserPermitRequest;
        $req->setTopics("taobao_trade_TradeBuyerPay");
        $resp = $c->execute($req, $this->access_token);
        echo '<pre>';
        var_dump($resp);
    }
    public function action_consume(){//消费消息
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TmcMessagesConsumeRequest;
        $resp = json_decode(json_encode($c->execute($req, $this->access_token)),true);
        echo '<pre>';
        print_r($resp);
        echo '</pre>';
        for($i=0;$resp['messages']['tmc_message'][$i];$i++){
            echo 'request_id'.$resp['request_id'].'<br>';
            echo 'id'.$resp['messages']['tmc_message'][$i]['id'].'<br>';
            echo 'content'.$resp['messages']['tmc_message'][$i]['content'].'<br>';
            echo 'tid'.json_decode($resp['messages']['tmc_message'][$i]['content'],ture)['tid'].'<br>';
            $tid = json_decode($resp['messages']['tmc_message'][$i]['content'],ture)['tid'];
            $res = $this->action_FullTrade($tid,$c);
            echo '<pre>';
            print_r($res);
            echo '</pre>';
            $this->saveorders($res,$c);
        }
    }
    public function action_confirm(){//确认消息
        require_once Kohana::find_file('vendor', 'taobao/TopSdk');
        $c = new TopClient;
        $c->appkey = $this->appkey;
        $c->secretKey = $this->secretKey;
        $req = new TmcMessagesConfirmRequest;
        $req->setSMessageIds("123,456");
        $resp = $c->execute($req, $this->access_token);
        echo '<pre>';
        var_dump($resp);
    }
    public function action_FullTrade($tid, $c) {
        $req = new TradeFullinfoGetRequest;
        $req->setFields("created, pay_time,payment, total_fee,pic_path, modified,title,price,tid, status,buyer_nick,receiver_name,receiver_address, receiver_state, receiver_city, receiver_district,  receiver_mobile,seller_memo, post_fee,orders.sku_properties_name, num,orders.title,orders.price, orders.pic_path, orders.num, orders.num_iid, orders.outer_iid, orders.outer_sku_id,orders.cid");
        $req->setTid($tid);
        $resp = json_decode(json_encode($c->execute($req, $this->access_token)),true);

        return $resp;
    }
    public function saveorders($res,$c){
        $bid = 1;
        Database::$default = "yyx";
        $res = $res['trade'];

        $this->action_item($res['orders']['order']['num_iid'],$c);
        $iid =  ORM::factory('yyx_item')->where('bid','=',$this->bid)->where('num_iid','=',$res['orders']['order']['num_iid'])->find()->id;
        $tb_trade = ORM::factory('yyx_order')->where('bid','=',$bid)->where('tid','=',$res['tid'])->find();
        if(!$tb_trade->id){
            $tb_trade->platform = 'taobao';
            $tb_trade->bid = $bid;
            $tb_trade->iid = $iid;
            $tb_trade->num = $res['num'];
            $tb_trade->tid = $res['tid'];
            $tb_trade->title = $res['title'];
            $tb_trade->buyer_nick = $res['buyer_nick'];
            $tb_trade->price = $res['price'];//第一个商品的价格
            $tb_trade->pic_path = $res['pic_path'];//第一个商品图片
            $tb_trade->receiver_state = $res['receiver_state'];
            $tb_trade->receiver_city = $res['receiver_city'];
            $tb_trade->receiver_district = $res['receiver_district'];
            $tb_trade->receiver_address = $res['receiver_address'];
            $tb_trade->receiver_mobile = $res['receiver_mobile'];
            $tb_trade->status = $res['status'];
            $tb_trade->post_fee = $res['post_fee'];
            $tb_trade->total_fee = $res['total_fee'];
            $tb_trade->payment = $res['payment'];
            $tb_trade->created = strtotime($res['created']);
            $tb_trade->update_time = strtotime($res['modified']);
            $tb_trade->pay_time = $res['pay_time'];
            echo '新订单';
        }else{
            if($tb_trade->status==$res['status']){
                echo '状态未更新！pass';
            }else{
                $tb_trade->update_time = strtotime($res['modified']);
                $tb_trade->status = $res['status'];
                echo '状态更新';
            }
        }
            echo 'bid:'.$bid.'<br>';
            echo 'iid:'.$res['tid'].'<br>';
            echo 'num:'.$num.'<br>';
            echo 'product_id:'.$res['orders']['order'][0]['num_iid'].'<br>';
            echo 'tid:'.$res['tid'].'<br>';
            echo 'title:'.$res['orders']['order'][0]['title'].'<br>';
            echo 'buyer_nick:'.$res['buyer_nick'].'<br>';
            echo 'price:'.$price.'<br>';
            echo 'pic_path:'.$res['orders']['order'][0]['pic_path'].'<br>';
            echo 'receiver_state:'.$res['receiver_state'].'<br>';
            echo 'receiver_city:'.$res['receiver_city'].'<br>';
            echo 'receiver_district:'.$res['receiver_district'].'<br>';
            echo 'receiver_address:'.$res['receiver_address'].'<br>';
            echo 'receiver_mobile:'.$res['receiver_mobile'].'<br>';
            echo 'status:'.$res['status'].'<br>';
            echo 'post_fee:'.$res['post_fee'].'<br>';
            echo 'total_fee:'.$res['total_fee'].'<br>';
            echo 'payment:'.$res['payment'].'<br>';
            echo 'created:'.$res['created'].'<br>';
            echo 'update_time:'.$res['modified'].'<br>';
            echo 'pay_time:'.$res['pay_time'].'<br>';
            echo '2';
            $tb_trade->save();
    }
}
