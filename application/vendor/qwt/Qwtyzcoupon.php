<?php
class Qwtyzcoupon{
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
        Kohana::$log->add("couponbid", print_r($bid, true));
        Kohana::$log->add("couponmsg", print_r($msg, true));
        Kohana::$log->add("couponstatus", print_r($status, true));
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->msg = $msg;
        $this->status=$status;
        $this->smfy=new SmfyQwt();
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $this->bid)->find()->yzaccess_token;
        if(!$this->yzaccess_token) throw new Exception('请授权有赞');
        $this->config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        $this->client = new YZTokenClient($this->yzaccess_token);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
           $this->wx = new Wxoauth($this->bid,$options); 
        }
    }
    public function couponpush(){
        $bid=$this->bid;
        $config=$this->config;
        $posttid=urldecode($this->msg);
        Kohana::$log->add("$bidcouponmsg1", print_r($posttid, true));
        $status=$this->status;
        $data=json_decode($posttid,true);
        Kohana::$log->add("$bidcouponmsg3", print_r($data, true));
        //$coupon_id=$data['id'];
        //youzan.ump.coupon.detail.get
        $coupon_id=$data['id'];
        $status=$data['status'];
        $event_time=$data['event_time'];
        $state=0;
        if($status=='CARD_CREATED'||$status=='UPDATED_CARD'||$status=='CODE_CREATED'||$status=='CODE_UPDATED'){
            $state=1;
        }elseif ($status=='CARD_GROUP_INVALID'||$status=='CODE_GROUP_INVALID') {
            $state=0;
        }
        $method = 'youzan.ump.coupon.detail.get';
        $params =[
        'id' => $coupon_id,
        ];
        $result=$this->client->post($method, $this->methodVersion, $params, $files);
        $title=$result['response']['title'];
        $coupon=ORM::factory('qwt_coupon')->where('bid','=',$bid)->where('coupon_id','=',$coupon_id)->find();
        $coupon->bid=$bid;
        if($title){
            $coupon->title=$title;
        }
        $coupon->state=$state;
        $coupon->type=$result['response']['group_type'];
        $coupon->coupon_id=$coupon_id;
        $coupon->event_time=$event_time;
        $coupon->save();
    }
}
