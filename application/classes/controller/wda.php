<?php defined('SYSPATH') or die('No direct script access.');

class Controller_wda extends Controller_Base{
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
        if (Request::instance()->action == 'wda') return;
    }
    public function action_testwdy(){
        $postStr = file_get_contents("php://input");
        //Kohana::$log->add('postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        //Kohana::$log->add('result11', print_r($result11, true));
        //积分宝cilent_id
        $client_id='41eeb7e302f34f799d';
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
            $fans_id=$msg_array['fans_id'];
            $mobile=$msg_array['mobile'];
            $amount=$msg_array['amount'];
            $total=$msg_array['total'];
            $bid = ORM::factory('wdy_login')->where('shopid','=',$kdt_id)->find()->id;
            $access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
            $expiretime=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->expiretime;
            if(strtotime($expiretime) < time()) die ('插件已过期');
            $config=ORM::factory('wdy_cfg')->getCfg($bid,1);
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
                Kohana::$log->add("wdy", print_r($result, true));
                $openid=$result['response']['user']['weixin_openid'];
                Kohana::$log->add("wdy:openid", print_r($openid, true));
                $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                //针对以前有赞积分没有同步过来的进行处理
                if($qrcode->yz_score!=0&&$qrcode->score!=$total-$amount){
                    if(!$msg_array['client_hash']||$msg_array['client_hash']!=md5($client_id)){
                        $score_change=$total-$amount-$qrcode->score;
                        $qrcode->scores->scoreIn($qrcode,13,$score_change);
                    }
                }
                if($qrcode->yz_score==0){
                    $qrcode->scores->scoreIn($qrcode,12,$total);
                    $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
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
                $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $score=$qrcode->score;
                if($score!=$total){
                    Kohana::$log->add("wdy:scoreboom:$bid", print_r($score, true));
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
    public function action_testdyb(){
        $postStr = file_get_contents("php://input");
        $result11=json_decode($postStr,true);
        $client_id='49e609597c5d9c3969';
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
    public function action_score(){
        set_time_limit(0);
        $bid = 1327;
        $qrcodes=ORM::factory('wdy_qr')->where('flag','=',0)->find_all();
        $config = ORM::factory('wdy_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;
        if($access_token){
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            $client = new KdtApiOauthClient();
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        foreach ($qrcodes as $q) {
            $openid=$q->openid;
            $qid=$q->id;
            $name=$q->nickname;
            $method = 'kdt.users.weixin.follower.get';
            $params =[
            'weixin_openid'=>$openid,
            ];
            $result=$client->post($access_token,$method,$params);
            $user_id = $result['response']['user']['user_id'];
            $method = 'kdt.crm.customer.points.changelog.get';
            $params =[
            'fans_id' => $user_id,
            'start_date' => "2017-04-18 00:00:00",
            ];
            $a=$client->post($access_token,$method,$params);
            $result=$a['response']['details'];
            $allscore=$a['response']['total_points'];
            $num=$a['response']['total_results'];
            $smscore=0;
            $hfscore=0;
            $flag=0;
            $wdqrcode=ORM::factory('wdy_wda')->where('openid','=',$openid)->find();
            if(!$wdqrcode->id){
                if($num!=0){
                    foreach ($result as $r) {
                        $amount=$r['amount'];
                        $description=$r['description'];
                        if($description=='[第三方应用]'||strpos($description,'恢复异常积分')!== false){
                            $flag=1;
                            if($description=='[第三方应用]'){
                                $smscore+=$amount;
                            }else{
                                $hfscore+=$amount;
                            }
                        }
                    }
                    if($flag==1){
                       $qrcode['name']=$name;
                       $qrcode['openid']=$openid;
                       $qrcode['smscore']=$smscore;
                       $qrcode['hfscore']=$hfscore;
                       $qrcode['rscore']=0-$smscore-$hfscore;
                       $qrcode['allscore']=$allscore;
                       $qr=ORM::factory('wdy_wda')->where('openid','=',$openid)->find();
                       $qr->values($qrcode);
                       $qr->save();
                    }
                }
            }else{
                $qr2=ORM::factory('wdy_wda')->where('id','=',$wdqrcode->id)->find();
                $qr2->allscore=$allscore;
                $qr2->save();
            }
            $qr1=ORM::factory('wdy_qr')->where('id','=',$qid)->find();
            $qr1->flag=1;
            $qr1->save();
        }
    }
    public function action_md5(){
        // $qrcodes=ORM::factory('wdy_wda')->find_all();
        // $tempname="全部";
        // $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
        // header( 'Content-Type: text/csv' );
        // header( 'Content-Disposition: attachment;filename='.$filename);
        // $fp = fopen('php://output', 'w');
        // $title = array('姓名', 'openid', '神码浮云操作积分', '恢复异常积分','最后需要操作的积分','有赞积分');
        // if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
        // fputcsv($fp, $title);
        // foreach ($qrcodes as $q) {
        //     $array = array($q->name,$q->openid,$q->smscore,$q->hfscore,$q->rscore,$q->allscore);
        //     if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
        //         //非 Mac 转 gbk
        //         foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
        //     }
        //     fputcsv($fp, $array);
        // }
        // exit;
        // $bid=19;
        // $access_token=ORM::factory('fxb_login')->where('id', '=', $bid)->find()->access_token;
        // require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        // $openid='oDt2QjpBI3pJLoWTMrdAukgDbQX8';
        // $client = new YZTokenClient($access_token);
        // $method = 'youzan.users.weixin.follower.get';
        // $params =[
        // 'weixin_openid'=>$openid,
        // ];
        // $methodVersion = '3.0.0';
        // $result=$client->post($method, $methodVersion, $params, $files);
        // $fans_id= $result['response']['user']['user_id'];
        // echo $fans_id."<br>";
        $date=date("Y-m-d");
        echo $date."<br>";
        $logins=ORM::factory('wdy_login')->where('expiretime','>',$date)->find_all();
        foreach ($logins as $l) {
            $config=ORM::factory('wdy_cfg')->getCfg($l->id,1);
            if($config['switch']==1){
                echo '名称:'.$l->name."----------账号:".$l->user.'<br>';
            }
            
        }
    }
    public function action_test(){
        $msg='%7B%22trade%22:%7B%22num%22:1,%22goods_kind%22:1,%22num_iid%22:%22328786775%22,%22price%22:%2280.00%22,%22pic_path%22:%22https://img.yzcdn.cn/upload_files/2017/03/07/Fvo8KrbuHPQIz1Wu_EfVfg-5VkHR.jpg%22,%22pic_thumb_path%22:%22https://img.yzcdn.cn/upload_files/2017/03/07/Fvo8KrbuHPQIz1Wu_EfVfg-5VkHR.jpg%3FimageView2/2/w/200/h/0/q/75/format/jpg%22,%22title%22:%22%E5%87%BA%E5%8F%A3%E6%97%A5%E6%9C%AC%E5%8D%95%20%E9%95%82%E7%A9%BA%E5%A5%B3%E8%A3%99%20%E7%BA%AF%E8%89%B2%E5%A4%A7%E6%B0%94%E6%89%93%E5%BA%95%E8%A3%99WHQ081%22,%22type%22:%22FIXED%22,%22discount_fee%22:%2221.00%22,%22order_type%22:%220%22,%22status%22:%22TRADE_CLOSED_BY_USER%22,%22status_str%22:%22%E5%B7%B2%E5%85%B3%E9%97%AD%22,%22refund_state%22:%22NO_REFUND%22,%22shipping_type%22:%22express%22,%22post_fee%22:%225.00%22,%22total_fee%22:%2259.00%22,%22refunded_fee%22:%220.00%22,%22payment%22:%2264.00%22,%22created%22:%222017-05-04%2016:51:51%22,%22update_time%22:%222017-05-04%2016:56:51%22,%22pay_time%22:%22%22,%22pay_type%22:%22WEIXIN_DAIXIAO%22,%22consign_time%22:%22%22,%22sign_time%22:%22%22,%22buyer_area%22:%22%E6%B5%99%E6%B1%9F%E7%9C%81%E5%98%89%E5%85%B4%E5%B8%82%22,%22seller_flag%22:0,%22buyer_message%22:%22%22,%22orders%22:[%7B%22alias%22:%223nvevyxquiz5y%22,%22oid%22:17999169,%22outer_sku_id%22:%2201%22,%22outer_item_id%22:%22WHQ081%22,%22title%22:%22%E5%87%BA%E5%8F%A3%E6%97%A5%E6%9C%AC%E5%8D%95%20%E9%95%82%E7%A9%BA%E5%A5%B3%E8%A3%99%20%E7%BA%AF%E8%89%B2%E5%A4%A7%E6%B0%94%E6%89%93%E5%BA%95%E8%A3%99WHQ081%22,%22seller_nick%22:%22%E5%A4%96%E8%B4%B8%E5%A4%A7%E7%8E%8B%E7%B2%BE%E5%93%81%E5%BA%97%22,%22fenxiao_price%22:%220.00%22,%22fenxiao_payment%22:%220.00%22,%22price%22:%2280.00%22,%22total_fee%22:%2280.00%22,%22payment%22:%2259.00%22,%22discount_fee%22:%220.00%22,%22sku_id%22:%2236129351%22,%22sku_unique_code%22:%2232878677536129351%22,%22sku_properties_name%22:%22%E9%A2%9C%E8%89%B2:%E7%99%BD;%E5%B0%BA%E5%AF%B8:M%22,%22pic_path%22:%22https://img.yzcdn.cn/upload_files/2017/03/07/Fvo8KrbuHPQIz1Wu_EfVfg-5VkHR.jpg%22,%22pic_thumb_path%22:%22https://img.yzcdn.cn/upload_files/2017/03/07/Fvo8KrbuHPQIz1Wu_EfVfg-5VkHR.jpg%3FimageView2/2/w/200/h/0/q/75/format/jpg%22,%22item_type%22:0,%22buyer_messages%22:[],%22order_promotion_details%22:[],%22state_str%22:%22%E5%BE%85%E5%8F%91%E8%B4%A7%22,%22allow_send%22:1,%22is_send%22:0,%22item_refund_state%22:%22NO_REFUND%22,%22is_virtual%22:0,%22is_present%22:0,%22refunded_fee%22:%220.00%22,%22unit%22:%22%E4%BB%B6%22,%22num_iid%22:%22328786775%22,%22num%22:%221%22%7D],%22fetch_detail%22:null,%22coupon_details%22:[],%22promotion_details%22:[%7B%22promotion_id%22:%22131742%22,%22promotion_name%22:%22%E7%A7%92%E6%9D%80%22,%22promotion_type%22:%22SECKILL%22,%22promotion_condition%22:null,%22used_at%22:%222017-05-04%2016:51:51%22,%22discount_fee%22:%2221.00%22%7D],%22adjust_fee%22:%7B%22change%22:%220.00%22,%22pay_change%22:%220.00%22,%22post_change%22:%220.00%22%7D,%22sub_trades%22:[],%22weixin_user_id%22:%222552945172%22,%22button_list%22:[%7B%22tool_icon%22:%22https://img.yzcdn.cn/upload_files/2015/08/28/FpO1UIXyOEZO026tWIgUOm9uZnT2.png%22,%22tool_title%22:%22%E5%A4%87%E6%B3%A8%22,%22tool_value%22:%22%22,%22tool_type%22:%22goto_native:trade_memo%22,%22tool_parameter%22:%22%7B%7D%22,%22new_sign%22:%220%22,%22create_time%22:%22%22%7D],%22feedback_num%22:0,%22trade_memo%22:%22%22,%22fans_info%22:%7B%22fans_nickname%22:%22%E5%BD%BC%E5%B2%B8%F0%9F%8C%B9%22,%22fans_id%22:%222552945172%22,%22buyer_id%22:%22251351941%22,%22fans_type%22:%221%22%7D,%22buy_way_str%22:%22%22,%22pf_buy_way_str%22:%22%E8%BF%90%E8%B4%B9%E5%88%B0%E4%BB%98%22,%22send_num%22:0,%22user_id%22:%22251351941%22,%22kind%22:1,%22relation_type%22:%22%22,%22relations%22:[],%22out_trade_no%22:[],%22group_no%22:%22%22,%22outer_user_id%22:0,%22offline_id%22:%2216879494%22,%22shop_id%22:%220%22,%22shop_type%22:%221%22,%22points_price%22:0,%22delivery_start_time%22:0,%22delivery_end_time%22:0,%22tuan_no%22:%22%22,%22is_tuan_head%22:0,%22delivery_time_display%22:%22%22,%22hotel_info%22:%22%22,%22order_mark%22:%22%22,%22qr_id%22:0,%22buyer_nick%22:%22%E5%BD%BC%E5%B2%B8%F0%9F%8C%B9%22,%22tid%22:%22E20170504165151090144568%22,%22buyer_type%22:%221%22,%22buyer_id%22:%222552945172%22,%22receiver_city%22:%22%E5%98%89%E5%85%B4%E5%B8%82%22,%22receiver_district%22:%22%E6%B5%B7%E5%AE%81%E5%B8%82%22,%22receiver_name%22:%22Maggie%E6%9C%B1%22,%22receiver_state%22:%22%E6%B5%99%E6%B1%9F%E7%9C%81%22,%22receiver_address%22:%22%E8%A5%BF%E5%B1%B1%E8%B7%AF%E9%87%91%E8%B4%B8%E5%A4%A7%E5%8E%A6%E4%B8%83%E6%A5%BC%22,%22receiver_zip%22:%22314400%22,%22receiver_mobile%22:%2213736869312%22,%22feedback%22:0,%22outer_tid%22:%22%22,%22transaction_tid%22:null,%22period_order_detail%22:null,%22service_phone%22:%2217193307600%22%7D%7D';
        $posttid=urldecode($msg);
        $jsona=json_decode($posttid,true);
        echo "<pre>";
        var_dump($jsona);
        echo "</pre>";
    }
     private function rsync($bid,$openid,$access_token,$chscore){
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        if($access_token){
            $client = new KdtApiOauthClient();
        }else{
            die('请在后台一键授权给有赞');
        }

        $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'kdt.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        $result=$client->post($access_token,$method,$params);
        $fans_id= $result['response']['user']['user_id'];
        if($qrcode->yz_score==0){
            $method = 'kdt.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $a=$client->post($access_token,$method,$params);

            $qrcode->yz_score=1;
            $qrcode->save();
            $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{

            if($chscore>=0){
                $method = 'kdt.crm.customer.points.increase';
                $params =[
                'fans_id' => $fans_id,
                'points' => $chscore,
                ];
                $a=$client->post($access_token,$method,$params);
            }else{
                $method = 'kdt.crm.customer.points.decrease';
                $params =[
                'fans_id' => $fans_id,
                'points' => -$chscore,
                ];
                $a=$client->post($access_token,$method,$params);
            }
        }
        $method = 'kdt.crm.fans.points.get';
        $params =[
        'fans_id' => $fans_id,
        ];
        $results=$client->post($access_token,$method,$params);
        $point = $results['response']['point'];
        if($point&&$point!=$qrcode->score){
            $score_change=$point-$qrcode->score;
            $qrcode->scores->scoreIn($qrcode,5,$score_change);
            echo $point."<br>";
            echo $qrcode->score."<br>";
        }
    }
    public function action_wda(){
        set_time_limit(0);
        $bid = 1327;
        $config = ORM::factory('wdy_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;
        if($access_token){
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            $client = new KdtApiOauthClient();
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        for ($last_fans_id=0,$next=true; $next==true; $i++) {
            Kohana::$log->add("last_fans_id", print_r($last_fans_id, true));
            $method = 'kdt.users.weixin.followers.pull';
            $params =[
            'fields'=>'weixin_openid,nick',
            'after_fans_id'=>$last_fans_id,
            ];
            $result=$client->post($access_token,$method,$params);
            $next=$result['response']['has_next'];
            $last_fans_id=$result['response']['last_fans_id'];
            $users=$result['response']['users'];
            foreach ($users as $u) {
                $openid=$u['weixin_openid'];
                //echo $openid."<br>";
                $nick=$u['nick'];
                $qrcode1=ORM::factory('wdy_qr')->where('openid','=',$openid)->find();
                $qrcode1->nickname=$nick;
                $qrcode1->openid=$openid;
                $qrcode1->save();
            }
        }
        

    }

}
