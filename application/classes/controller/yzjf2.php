<?php defined('SYSPATH') or die('No direct script access.');

class Controller_yzjf2 extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    var $we;
    var $client;
    public function before() {
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'testwdy') return;
        if (Request::instance()->action == 'testdyb') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'score') return;
        if (Request::instance()->action == 'md5') return;
    }
    public function action_test(){
        $bid=5;
        $openid='';
        $failscore->save();
        // $openid='oAZ8vxOuZtPXevUg-WyJs35T1RL0';
        // $user=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        // $sid=$user->scores->scoreOut($user, 4,4294965755);
        // echo $sid;
        // exit();
        // $methodVersion='3.0.0';
        // $bid=1318;
        // require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        // $login=ORM::factory('wdy_login')->where('id','=',$bid)->find();
        // $config=ORM::factory('wdy_cfg')->getCfg($bid,1);
        // $access_token=$login->access_token;
        // $client = new YZTokenClient($access_token);
        // $openid='oAZ8vxOuZtPXevUg-WyJs35T1RL0';
        // $method = 'youzan.users.weixin.follower.get';
        // $params =[
        // 'weixin_openid'=>$openid,
        // ];
        // $result=$client->post($method, $methodVersion, $params, $files);
        // //var_dump($result);
        // $fans_id=$result['response']['user']['user_id'];
        // $method = 'youzan.crm.customer.points.changelog.get';
        // $params =[
        // 'page'=>8,
        // 'start_date'=>'2017-07-27',
        // 'page_size'=>50,
        // 'fans_id'=>$fans_id,
        // ];
        // $result=$client->post($method, $methodVersion, $params, $files);
        // echo '<pre>';
        // var_dump($result);
        // echo '</pre>';
        // exit();
        //echo $fans_id.'<br>';


    }
    // public function action_testwdy(){
    //     $postStr = file_get_contents("php://input");
    //     //Kohana::$log->add('postStr', print_r($postStr, true));
    //     $result11=json_decode($postStr,true);
    //     //Kohana::$log->add('result11', print_r($result11, true));
    //     //积分宝cilent_id
    //     $client_id='41eeb7e302f34f799d';
    //     if($postStr){
    //         $enddata = array('code' => 0,'msg'=>'success');
    //         $rtjson =json_encode($enddata);
    //         echo $rtjson;
    //     }
    //     $kdt_id =$result11['kdt_id'];
    //     if($result11['type']=='POINTS'){
    //         $msg=$result11['msg'];
    //         $msg_array=json_decode(urldecode($msg),true);
    //         Kohana::$log->add('msg_array', print_r($msg_array, true));
    //         $fans_id=$msg_array['fans_id'];
    //         $mobile=$msg_array['mobile'];
    //         $amount=$msg_array['amount'];
    //         $total=$msg_array['total'];
    //         $bid = ORM::factory('wdy_login')->where('shopid','=',$kdt_id)->find()->id;
    //         $access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
    //         $expiretime=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->expiretime;
    //         if(strtotime($expiretime) < time()) die ('插件已过期');
    //         $config=ORM::factory('wdy_cfg')->getCfg($bid,1);
    //         if($config['switch']==1){
    //             require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
    //             if($access_token){
    //                 $this->client=$client = new KdtApiOauthClient();
    //             }else{
    //                 Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
    //             }
    //             $method = 'kdt.users.weixin.follower.get';
    //             $params =[
    //             'user_id'=>$fans_id,
    //             ];
    //             $result=$this->client->post($access_token,$method,$params);
    //             Kohana::$log->add("wdy", print_r($result, true));
    //             $openid=$result['response']['user']['weixin_openid'];
    //             Kohana::$log->add("wdy:openid", print_r($openid, true));
    //             $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
    //             if(!$qrcode->id) die ('没有这个用户');
    //             //针对以前有赞积分没有同步过来的进行处理
    //             if($qrcode->yz_score!=0&&$qrcode->score!=$total-$amount){
    //                 if(!$msg_array['client_hash']||$msg_array['client_hash']!=md5($client_id)){
    //                     $score_change=$total-$amount-$qrcode->score;
    //                     $qrcode->scores->scoreIn($qrcode,13,$score_change);
    //                 }
    //             }
    //             if($qrcode->yz_score==0){
    //                 $qrcode->scores->scoreIn($qrcode,12,$total);
    //                 $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
    //                 $qrcode->yz_score=1;
    //                 $qrcode->save();
    //             }else{
    //                 if($msg_array['client_hash']){
    //                     if($msg_array['client_hash']!=md5($client_id)){
    //                         $qrcode->scores->scoreIn($qrcode,11,$amount);
    //                     }
    //                 }else{
    //                     if($amount>=0){
    //                         $qrcode->scores->scoreIn($qrcode,5,$amount);
    //                     }else{
    //                         $qrcode->scores->scoreIn($qrcode,6,$amount);
    //                     }
    //                 }
    //             }
    //             $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
    //             $score=$qrcode->score;
    //             if($score!=$total){
    //                 Kohana::$log->add("wdy:scoreboom:$bid", print_r($score, true));
    //                 $score_change=$score-$total;
    //                 if($score_change>=0){
    //                     $method = 'kdt.crm.customer.points.increase';
    //                     $params =[
    //                     'fans_id' => $fans_id,
    //                     'points' => $score_change,
    //                     ];
    //                     $a=$client->post($access_token,$method,$params);
    //                 }else{
    //                     $method = 'kdt.crm.customer.points.decrease';
    //                     $params =[
    //                     'fans_id' => $fans_id,
    //                     'points' => -$score_change,
    //                     ];
    //                     $a=$client->post($access_token,$method,$params);
    //                 }
    //             }
    //         }
    //     }
    // }

    // public function action_score($score){
    //     $bid = 5;
    //     $config = ORM::factory('wdy_cfg')->getCfg($bid,1);
    //     $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
    //     $access_token=$this->access_token;
    //     if($access_token){
    //         require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
    //         $client = new KdtApiOauthClient();
    //     }else{
    //         Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
    //     }
    //     $openid='oDt2QjtTeio8l0dBl28SQGhcHSH4';
    //     echo $openid."<br>";
    //     echo $access_token."<br>";
    //     $method = 'kdt.users.weixin.follower.get';
    //     $params =[
    //     'weixin_openid'=>$openid,
    //     ];
    //     $result=$client->post($access_token,$method,$params);
    //     var_dump($result);
    //     echo "<br>-------------------------------------------------------<br>";
    //     //Kohana::$log->add("yz1", print_r($result, true));
    //     $user_id = $result['response']['user']['user_id'];
    //     $method = 'kdt.crm.customer.points.increase';
    //     $params =[
    //     'fans_id' => 773070814,
    //     //'kdt_id' => $kdt_id,
    //     'points' => $score,
    //     ];
    //     $a=$client->post($access_token,$method,$params);
    //     var_dump($a);
    //     //Kohana::$log->add("dyb", print_r($a, true));
    // }
    // public function action_md5(){
    //     //ee5e1bb640c6ea91ab82db72783d2f90
    //     $client='41eeb7e302f34f799d';
    //     $result=md5($client);
    //     echo $result;
    // }

}
