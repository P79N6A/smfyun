<?php defined('SYSPATH') or die('No direct script access.');

class Controller_wj extends Controller_Base{
    public $template = 'tpl/blank';
    public $appId = 'edd2c0ac95f585f448';
    public $appSecret = '42594af47a9fea45c8172b7ebdd6c8f7';
    var $client;
    public function before() {
        Database::$default = "wj";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('wujiu', print_r($postStr, true));
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
        //Kohana::$log->add('$status', print_r($status, true));
        //Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
        if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'){
            require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            $client = new KdtApiClient($this->appId, $this->appSecret);
            $posttid=urldecode($msg);
            $jsona=json_decode($posttid,true);
            $num_iid=$jsona['trade']['num_iid'];
            if($num_iid==320208565||$num_iid==320843848){
                $tid=$jsona['trade']['tid'];
                $weixin_user_id=$jsona['trade']['weixin_user_id'];
                $num=$jsona['trade']['num'];
                $title=$jsona['trade']['title'];
                $pay_time=$jsona['trade']['pay_time'];
                $price=$jsona['trade']['price'];
                $receiver_name=$jsona['trade']['receiver_name'];
                $receiver_state=$jsona['trade']['receiver_state'];
                $receiver_city=$jsona['trade']['receiver_city'];
                $receiver_district=$jsona['trade']['receiver_district'];
                $receiver_addjsonas=$jsona['trade']['receiver_addjsonas'];
                $receiver_mobile=$jsona['trade']['receiver_mobile'];
                $receiver_addresss=$receiver_state.$receiver_city.$receiver_district.$receiver_address;

                $method = 'kdt.users.weixin.follower.get';
                $params = [
                'user_id'=>$weixin_user_id,
                ];
                $openids = $client->post($method,$params);
                $openid=$openids['response']['user']['weixin_openid'];
                $tid_num=ORM::factory('wj_tid')->where('tid','=',$tid)->count_all();
                $tids=ORM::factory('wj_tid')->where('tid','=',$tid)->find();
                if($tid_num==0&&$tid!=''){
                    $tids->tid=$tid;
                    $tids->tradename=$title;
                    $tids->price=$price;
                    $tids->time=$pay_time;
                    $tids->num=$num;
                    $tids->name=$receiver_name;
                    $tids->tel=$receiver_mobile;
                    $tids->address=$receiver_addresss;
                    $tids->openid=$openid;
                    $tids->remaintimes=$num;
                    $tids->uid=$weixin_user_id;
                    $tids->save();
                    Kohana::$log->add('wj', '添加一条记录');
                }
           }
       }
        //exit;
    }

}
