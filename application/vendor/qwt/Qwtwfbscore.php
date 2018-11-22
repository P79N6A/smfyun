<?php
class Qwtwfbscore{
    public $methodVersion='3.0.0';
    public $bid;
    public $msg;
    public $yzaccess_token;
    public $config;
    public $trade;
    public $orders;
    public $userinfo;
    var $wx;
    var $client;
    var $smfy;
    public function __construct($bid,$msg) {
        Kohana::$log->add("bid", print_r($bid, true));
        Kohana::$log->add("msg", print_r($msg, true));
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->msg = $msg;
        $this->smfy=new SmfyQwt();
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $this->bid)->find()->yzaccess_token;
        if(!$this->yzaccess_token) throw new Exception('请授权有赞');
        $this->config=ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
        $this->client = new YZTokenClient($this->yzaccess_token);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
           $this->wx = new Wxoauth($this->bid,$options); 
        }
    }
    public function scorepush(){
        $bid=$this->bid;
        $config=$this->config;
        $client_id='b8c8058d79f5cca370';
        $msg_array=json_decode(urldecode($this->msg),true);
        Kohana::$log->add('msg_array', print_r($msg_array, true));
        $fans_id=$msg_array['fans_id'];
        $mobile=$msg_array['mobile'];
        $amount=$msg_array['amount'];
        $total=$msg_array['total'];
        $yzaccess_token=$this->yzaccess_token;
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'fans_id'=>$fans_id,
        ];
        $result=$this->client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add("qwt_wfb".$bid."fans_id".$fans_id, print_r($result, true));
        $openid=$result['response']['user']['weixin_openid'];
        Kohana::$log->add("wdy:openid", print_r($openid, true));
        $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if(!$qrcode->id) die ('没有这个用户');
        //针对以前有赞积分没有同步过来的进行处理
        Kohana::$log->add("wdyscore", print_r($qrcode->score, true));
        Kohana::$log->add("wdyscore", print_r($qrcode->yz_score, true));
        Kohana::$log->add("wdyscore", print_r($total, true));
        Kohana::$log->add("wdyscore", print_r($amount, true));
        if($qrcode->yz_score!=0&&$qrcode->score!=$total-$amount){
            if(!$msg_array['client_hash']||$msg_array['client_hash']!=md5($client_id)){
                $score_change=$total-$amount-$qrcode->score;
                $qrcode->scores->scoreIn($qrcode,13,$score_change);
            }
        }
        if($qrcode->yz_score==0&&$qrcode->score!=0){
            $method = 'youzan.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $a=$client->post($method, $this->methodVersion, $params, $files);
            if($a['response']['is_success']=='true'){
                $qrcode->scores->scoreIn($qrcode,12,$total);
                $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $qrcode->yz_score=1;
                $qrcode->save(); 
            }else{
                $failscore=ORM::factory('qwt_wfbfailscore');
                $failscore->bid=$bid;
                $failscore->qid=$qrcode->id;
                $failscore->type=1;
                $failscore->log=$a['error_response']['code'].$a['error_response']['message'];
                $failscore->save();
            }
        }else{
            if($msg_array['client_hash']){
                if($msg_array['client_hash']!=md5($client_id)){
                    $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->yz_score=1;
                    $qrcode->save(); 
                    $qrcode->scores->scoreIn($qrcode,11,$amount);
                }
            }else{
                if($amount>=0){
                    $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->yz_score=1;
                    $qrcode->save();
                    $qrcode->scores->scoreIn($qrcode,5,$amount);
                }else{
                    $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->yz_score=1;
                    $qrcode->save();
                    $qrcode->scores->scoreIn($qrcode,6,$amount);
                }
            }
        }
        
    }
}
