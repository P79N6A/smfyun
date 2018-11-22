<?php defined('SYSPATH') or die('No direct script access.');

class Controller_yzjf extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    public $methodVersion = '3.0.0';
    var $we;
    var $client;
    public function before() {
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'testwdy') return;
        if (Request::instance()->action == 'yzjfcron') return;
        if (Request::instance()->action == 'testdyb') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'score') return;
        if (Request::instance()->action == 'md5') return;
    }
    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        $client_id='41eeb7e302f34f799d';
        Kohana::$log->add('result11', print_r($result11, true));
        if($postStr){
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $appid =$result11['app_id'];
        if($result11['type']=='POINTS'){
            $msg=$result11['msg'];
            Kohana::$log->add('msg', print_r($msg, true));
            $a=base64_decode($msg);
            Kohana::$log->add('aaaa', print_r($a, true));
            $msg_array=json_decode($a,true);
            Kohana::$log->add('msg_array', print_r($msg_array, true));
        }
    }
    public function action_yzjfcron(){
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $failscores=ORM::factory('wdy_failscore')->where('flag','=',0)->where('type','=',2)->order_by('lastupdate','ASC')->limit(20)->find_all();
        foreach ($failscores as $failscore) {
            $bid=$failscore->bid;
            // echo $bid.'<br>';
            $access_token=ORM::factory('wdy_login')->where('id','=',$bid)->find()->access_token;
            // echo $access_token.'<br>';
            $qid=$failscore->qid;
            if($failscore->sid&&$failscore->type==2&&$access_token){
                $client = new YZTokenClient($access_token);
                $score=$failscore->score->score;
                // echo $score.'<br>';
                $openid=$failscore->user->openid;
                // echo $openid.'<br>';
                $method = 'youzan.users.weixin.follower.get';
                $params =[
                'weixin_openid'=>$openid,
                ];
                $methodVersion = '3.0.0';
                $result=$client->post($method, $methodVersion, $params, $files);
                $fans_id= $result['response']['user']['user_id'];
                // echo $fans_id.'<br>';
                if($score>=0){
                    $method = 'youzan.crm.customer.points.increase';
                    $params =[
                    'fans_id' => $fans_id,
                    'points' => $score,
                    ];
                    $a=$client->post($method, $this->methodVersion, $params, $files);
                }else{
                    $method = 'youzan.crm.customer.points.decrease';
                    $params =[
                    'fans_id' => $fans_id,
                    'points' => -$score,
                    ];
                    $a=$client->post($method, $this->methodVersion, $params, $files);
                }
                // echo '<pre>';
                // var_dump($a);
                // echo '<pre>';
                $fscore=ORM::factory('wdy_failscore')->where('id','=',$failscore->id)->find();
                if($a['response']['is_success']=='true'){
                    $fscore->flag=10;
                }else{
                    $fscore->lastupdate=time();
                }
                $fscore->save();
                // echo '-------------<br>';
            }
        }
        exit();
    }
    public function action_testwdy(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('result11', print_r($result11, true));
        //积分宝cilent_id

        $client_id='41eeb7e302f34f799d';
        if($postStr){
            // $enddata = array('code' => 0,'msg'=>'success');
            // $rtjson =json_encode($enddata);
            // echo $rtjson;
            $result = array("code"=>0,"msg"=>"success") ;
            var_dump($result);
        }
        if($result11['mode']!=1) exit();
        $kdt_id =$result11['kdt_id'];
        if($result11['type']=='POINTS'){
            $msg=$result11['msg'];
            $msg_array=json_decode(urldecode($msg),true);
            Kohana::$log->add('msg_array', print_r($msg_array, true));
            if($msg_array['fans_id']){
                $fans_id=$msg_array['fans_id'];
            }else{
                $fans_id=$msg_array['fan_id'];
            }
            $mobile=$msg_array['mobile'];
            $amount=$msg_array['amount'];
            $total=$msg_array['total'];
            $bid = ORM::factory('wdy_login')->where('shopid','=',$kdt_id)->find()->id;
            $access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
            $expiretime=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->expiretime;
            if(strtotime($expiretime) < time()) die ('插件已过期');
            $config=ORM::factory('wdy_cfg')->getCfg($bid,1);
            if($config['switch']==1){
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                if($access_token){
                    $this->client=$client = new YZTokenClient($access_token);
                }else{
                    Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                $method = 'youzan.users.weixin.follower.get';
                $params =[
                'fans_id'=>$fans_id,
                ];
                $result=$this->client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add("wdy", print_r($result, true));
                $openid=$result['response']['user']['weixin_openid'];
                Kohana::$log->add("wdy:openid", print_r($openid, true));
                $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
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
                    Kohana::$log->add('result',print_r($a,true));
                    if($a['response']['is_success']=='true'){
                        $qrcode->scores->scoreIn($qrcode,12,$total);
                        $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                        $qrcode->yz_score=1;
                        $qrcode->save(); 
                    }else{
                        $failscore=ORM::factory('wdy_failscore');
                        $failscore->bid=$bid;
                        $failscore->qid=$qrcode->id;
                        $failscore->type=1;
                        $failscore->log=$a['error_response']['code'].$a['error_response']['message'];
                        $failscore->save();
                        Kohana::$log->add('failscoreid4',print_r($failscore->id,true));
                    }
                }else{
                    if($msg_array['client_hash']){
                        if($msg_array['client_hash']!=md5($client_id)){
                            $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                            $qrcode->yz_score=1;
                            $qrcode->save(); 
                            $qrcode->scores->scoreIn($qrcode,11,$amount);
                        }
                    }else{
                        if($amount>=0){
                            $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                            $qrcode->yz_score=1;
                            $qrcode->save();
                            $qrcode->scores->scoreIn($qrcode,5,$amount);
                        }else{
                            $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                            $qrcode->yz_score=1;
                            $qrcode->save();
                            $qrcode->scores->scoreIn($qrcode,6,$amount);
                        }
                    }
                }
                // $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                // $score=$qrcode->score;
                // if($score!=$total){
                //     Kohana::$log->add("wdy:scoreboom:$bid", print_r($score, true));
                //     $score_change=$score-$total;
                //     if($score_change>=0){
                //         $method = 'youzan.crm.customer.points.increase';
                //         $params =[
                //         'fans_id' => $fans_id,
                //         'points' => $score_change,
                //         ];
                //         $a=$client->post($method, $this->methodVersion, $params, $files);
                //     }else{
                //         $method = 'youzan.crm.customer.points.decrease';
                //         $params =[
                //         'fans_id' => $fans_id,
                //         'points' => -$score_change,
                //         ];
                //         $a=$client->post($method, $this->methodVersion, $params, $files);
                //     }
                // }
            }
        }
        exit();
    }
    public function action_testdkl(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStrdkl', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        //Kohana::$log->add('result11', print_r($result11, true));
        //积分宝cilent_id
        $client_id='fcb6c3293b33114e48';
        if($postStr){
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $kdt_id =$result11['kdt_id'];
        if($result11['type']=='POINTS'){
            $msg=$result11['msg'];
            $msg_array=json_decode(urldecode($msg),true);
            Kohana::$log->add('msg_arraydkl', print_r($msg_array, true));
            $fans_id=$msg_array['fans_id'];
            $mobile=$msg_array['mobile'];
            $amount=$msg_array['amount'];
            $total=$msg_array['total'];
            $bid = ORM::factory('dkl_login')->where('shopid','=',$kdt_id)->find()->id;
            $access_token=ORM::factory('dkl_login')->where('id', '=', $bid)->find()->access_token;
            $expiretime=ORM::factory('dkl_login')->where('id', '=', $bid)->find()->expiretime;
            if(strtotime($expiretime) < time()) die ('插件已过期');
            $config=ORM::factory('dkl_cfg')->getCfg($bid,1);
            if($config['switch']==1){
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                if($access_token){
                    $this->client=$client = new YZTokenClient($access_token);
                }else{
                    Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                $method = 'youzan.users.weixin.follower.get';
                $params =[
                'fans_id'=>$fans_id,
                ];
                $result=$this->client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add("dkl", print_r($result, true));
                $openid=$result['response']['user']['weixin_openid'];
                Kohana::$log->add("dkl:openid", print_r($openid, true));
                $qrcode=ORM::factory('dkl_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if(!$qrcode->id) die ('没有这个用户');
                //针对以前有赞积分没有同步过来的进行处理
                if($qrcode->yz_score!=0&&$qrcode->score!=$total-$amount){
                    if(!$msg_array['client_hash']||$msg_array['client_hash']!=md5($client_id)){
                        $score_change=$total-$amount-$qrcode->score;
                        $qrcode->scores->scoreIn($qrcode,13,$score_change);
                    }
                }
                if($qrcode->yz_score==0){
                    $qrcode->scores->scoreIn($qrcode,12,$total);
                    $qrcode=ORM::factory('dkl_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->yz_score=1;
                    $qrcode->save();
                }else{
                    if($msg_array['client_hash']){
                        if($msg_array['client_hash']!=md5($client_id)){
                            $qrcode->scores->scoreIn($qrcode,11,$amount);
                        }
                    }else{
                        if($amount>=0){
                            $qrcode->scores->scoreIn($qrcode,5,$amount);
                        }else{
                            $qrcode->scores->scoreIn($qrcode,6,$amount);
                        }
                    }
                }
                $qrcode=ORM::factory('dkl_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $score=$qrcode->score;
                if($score!=$total){
                    Kohana::$log->add("dkl:scoreboom:$bid", print_r($score, true));
                    $score_change=$score-$total;
                    if($score_change>=0){
                        $method = 'youzan.crm.customer.points.increase';
                        $params =[
                        'fans_id' => $fans_id,
                        'points' => $score_change,
                        ];
                        $a=$client->post($method, $this->methodVersion, $params, $files);
                    }else{
                        $method = 'youzan.crm.customer.points.decrease';
                        $params =[
                        'fans_id' => $fans_id,
                        'points' => -$score_change,
                        ];
                        $a=$client->post($method, $this->methodVersion, $params, $files);
                    }
                }
            }
        }
    }
    public function action_testdyb(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('result11', print_r($result11, true));
        //积分宝cilent_id
        $client_id='49e609597c5d9c3969';
        if($postStr){
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $kdt_id =$result11['kdt_id'];
        if($result11['type']=='POINTS'){
            $msg=$result11['msg'];
            $msg_array=json_decode(urldecode($msg),true);
            Kohana::$log->add('msg_array', print_r($msg_array, true));
            //$fans_id=$msg_array['fans_id'];
            if($msg_array['fans_id']){
                $fans_id=$msg_array['fans_id'];
            }else{
                $fans_id=$msg_array['fan_id'];
            }
            $mobile=$msg_array['mobile'];
            $amount=$msg_array['amount'];
            $total=$msg_array['total'];
            $bid = ORM::factory('dyb_login')->where('shopid','=',$kdt_id)->find()->id;
            $access_token=ORM::factory('dyb_login')->where('id', '=', $bid)->find()->access_token;
            $expiretime=ORM::factory('dyb_login')->where('id', '=', $bid)->find()->expiretime;
            if(strtotime($expiretime) < time()) die ('插件已过期');
            $config=ORM::factory('dyb_cfg')->getCfg($bid,1);
            if($config['switch']==1){
                require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
                if($access_token){
                    $this->client=$client = new KdtApiOauthClient();
                }else{
                    Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                $method = 'kdt.users.weixin.follower.get';
                $params =[
                'user_id'=>$fans_id,
                ];
                $result=$this->client->post($access_token,$method,$params);
                Kohana::$log->add("dyb", print_r($result, true));
                $openid=$result['response']['user']['weixin_openid'];
                Kohana::$log->add("dyb:openid", print_r($openid, true));
                $qrcode=ORM::factory('dyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if(!$qrcode->id) die ('没有这个用户');
                //针对以前有赞积分没有同步过来的进行处理
                if($qrcode->yz_score!=0&&$qrcode->score!=$total-$amount){
                    if(!$msg_array['client_hash']||$msg_array['client_hash']!=md5($client_id)){
                        $score_change=$total-$amount-$qrcode->score;
                        $qrcode->scores->scoreIn($qrcode,13,$score_change);
                    }
                }
                if($qrcode->yz_score==0){
                    $qrcode->scores->scoreIn($qrcode,12,$total);
                    $qrcode=ORM::factory('dyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->yz_score=1;
                    $qrcode->save();
                }else{
                    if($msg_array['client_hash']){
                        if($msg_array['client_hash']!=md5($client_id)){
                            $qrcode->scores->scoreIn($qrcode,11,$amount);
                        }
                    }else{
                        if($amount>=0){
                            $qrcode->scores->scoreIn($qrcode,5,$amount);
                        }else{
                            $qrcode->scores->scoreIn($qrcode,6,$amount);
                        }
                    }
                }
                $qrcode=ORM::factory('dyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $score=$qrcode->score;
                if($score!=$total){
                    Kohana::$log->add("dyb:scoreboom:$bid", print_r($score, true));
                    $score_change=$score-$total;
                    if($score_change>=0){
                        $method = 'kdt.crm.customer.points.increase';
                        $params =[
                        'fans_id' => $fans_id,
                        'points' => $score_change,
                        ];
                        $a=$client->post($access_token,$method,$params);
                    }else{
                        $method = 'kdt.crm.customer.points.decrease';
                        $params =[
                        'fans_id' => $fans_id,
                        'points' => -$score_change,
                        ];
                        $a=$client->post($access_token,$method,$params);
                    }
                }
            }
        }
    }
    public function action_score($score){
        $bid = 5;
        $config = ORM::factory('wdy_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;
        if($access_token){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $client = new YZTokenClient();
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $openid='oDt2QjtTeio8l0dBl28SQGhcHSH4';
        echo $openid."<br>";
        echo $access_token."<br>";
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        $result=$client->post($method, $this->methodVersion, $params, $files);
        var_dump($result);
        echo "<br>-------------------------------------------------------<br>";
        //Kohana::$log->add("yz1", print_r($result, true));
        $user_id = $result['response']['user']['user_id'];
        $method = 'youzan.crm.customer.points.increase';
        $params =[
        'fans_id' => 773070814,
        //'kdt_id' => $kdt_id,
        'points' => $score,
        ];
        $a=$client->post($method, $this->methodVersion, $params, $files);
        var_dump($a);
        //Kohana::$log->add("dyb", print_r($a, true));
    }
    public function action_md5(){
        //ee5e1bb640c6ea91ab82db72783d2f90
        $client='41eeb7e302f34f799d';
        $result=md5($client);
        echo $result;
    }

}
