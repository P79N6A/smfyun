<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Yyb extends Controller_Base {
    public $template = 'tpl/blank';
    public $config;
    public $we;
    public $client;
    public $cronnum=1;
    public $access_token;
    public $methodVersion='3.0.0';
    public $cdnurl = 'http://cdn.jfb.smfyun.com/yyb/';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "yyb";
        parent::before();
    }

    public function after() {
        $this->template->user = $user;
        parent::after();
    }
    public function action_yzcode(){
        $yzcode=$_GET['url'];
        $openid=$_GET['openid'];
        $bid=$_GET['bid'];
        $oid=$_GET['oid'];
        $admin=$_GET['admin'];
        $qrcode=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $item=ORM::factory('yyb_item')->where('bid','=',$bid)->where('qid','=',$qrcode->id)->where('oid','=',$oid)->find();
        $item->bid=$bid;
        $item->qid=$qrcode->id;
        $item->oid=$oid;
        if(!$admin){
            $item->save();
        }
        $iid=$item->id;
        if($admin||$iid){
            if(!$admin&&$iid){
                $item=ORM::factory('yyb_item')->where('id','=',$iid)->find();
                if($item->status==1){
                    die('您已经领取过奖品了');
                }
            }
        }else{
            die('不合法');
        }
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $this->access_token=$access_token=ORM::factory('yyb_login')->where('id','=',$bid)->find()->access_token;
        if($access_token){
            $this->client=$client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $method = 'youzan.ump.coupon.take';
        $params = [
            'coupon_group_id'=>$yzcode,
            'weixin_openid'=>$openid,
        ];
        $results = $this->client->post($method, $this->methodVersion, $params, $files);
        if ($results['response']){
            if(!$admin&&$iid){
                $item->status=1;
                $item->save();
            }
            $km_text='有赞优惠券领取成功，请在个人中心查看';
        }else{
            $km_text= $results['error_response']['code'].$results['error_response']['msg'];
        }
        $view = "weixin/yyb/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('km_text', $km_text);
    }
    public function action_yzgift(){
        $yzgift=$_GET['url'];
        $openid=$_GET['openid'];
        $bid=$_GET['bid'];
        $oid=$_GET['oid'];
        $admin=$_GET['admin'];
        $qrcode=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $item=ORM::factory('yyb_item')->where('bid','=',$bid)->where('qid','=',$qrcode->id)->where('oid','=',$oid)->find();
        $item->bid=$bid;
        $item->qid=$qrcode->id;
        $item->oid=$oid;
        if(!$admin){
            $item->save();
        }
        $iid=$item->id;
        if($admin||$iid){
            if(!$admin&&$iid){
                $item=ORM::factory('yyb_item')->where('id','=',$iid)->find();
                if($item->status==1){
                    die('您已经领取过奖品了');
                }
            }
        }else{
            die('不合法');
        }
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $this->access_token=$access_token=ORM::factory('yyb_login')->where('id','=',$bid)->find()->access_token;
        if($access_token){
            $this->client=$client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $method = 'youzan.ump.presents.ongoing.all';
        $params = [

        ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        for($i=0;$results['response']['presents'][$i];$i++){
            $res = $results['response']['presents'][$i];
            $present_id=$res['present_id'];
            if($present_id==$yzgift){//找到指定赠品
                //根据openid获取userid
                $method = 'youzan.users.weixin.follower.get';
                $params = [
                   'weixin_openid'=>$openid,
                   'fields'=>'user_id',
                ];
                $results = $client->post($method, $this->methodVersion, $params, $files);
                $user_id = $results['response']['user']['user_id'];
                //echo 'user_id:'.$user_id;
                //根据openid发送奖品
                $method = 'youzan.ump.present.give';
                $params = [
                 'activity_id'=>$yzgift,
                 'fans_id'=>$user_id,
                ];
                $result1s = $client->post($method, $this->methodVersion, $params, $files);
                if($result1s['response']['is_success']==true){
                    if(!$admin&&$iid){
                        $item->status=1;
                        $item->save();
                    }
                    Request::instance()->redirect($result1s["response"]["receive_address"]);
                }else{
                    echo $result1s['error_response']['code'].$result1s['error_response']['msg'];
                    exit;
                }

            }
        }
    }
    public function action_qrcron(){
        set_time_limit(0);
        Kohana::$log->add("yybcron{$bid}",'qrcron');
        $qnum=ORM::factory('yyb_login')->where('qrcron','=',0)->count_all();
        if($qnum==0) die('已经全部轮询完');
        $admin=ORM::factory('yyb_login')->where('qrcron','=',0)->order_by('qrtime','ASC')->find();
        $bid=$admin->id;
        echo 'bid:'.$bid."<br>";
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        if(!$config['appid']||!$config['appsecret']){
            $admin->qrtime=time();
            $admin->save();
            die('有赞参数未填写');
        }
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we=new Wechat($config);
        $next_openid=$config['next_openid'];
        $result=$we->getUserList($next_openid);
        // echo "<pre>";
        // var_dump($result);
        // echo "<pre>";
        // exit;
        $total=$result['total'];
        if(!$total) {
            $admin->qrtime=time();
            $admin->save();
            die ('拉取用户失败');
        }
        echo 'total:'.$total."<br>";
        $count=$result['count'];
        echo 'count:'.$count."<br>";
        $ok=ORM::factory('yyb_cfg')->setCfg($bid,'qr_total',$total);
        $ok=ORM::factory('yyb_cfg')->setCfg($bid,'qr_count',$count);
        if($count>0){
            $openids=$result['data']['openid'];
            foreach ($openids as $openid) {
                $values[] = "($bid, '{$openid}')";
            }
            $next_openid=$result['next_openid'];
            $ok=ORM::factory('yyb_cfg')->setCfg($bid,'next_openid',$next_openid);
        }elseif (isset($count)&&$count==0) {
            $admin->qrtime=time();
            $admin->qrcron=1;
            $admin->save();
            die('拉取完毕该商户');
        }
        if($values){
            $SQL = 'INSERT IGNORE INTO yyb_qrcodes (`bid`,`openid`) VALUES '. join(',', $values);
            $colum = DB::query(NULL,$SQL)->execute();
        }
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
    }
    public function action_info(){
        set_time_limit(0);
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $config1=ORM::factory('yyb_cfg')->getCfg(1,1);
        $next_qid=$config1['next_qid'];
        if(!$next_qid) $next_qid=0;
        echo 'next_qid:'.$next_qid."<br>";
        $last_num=ORM::factory('yyb_qrcode')->where('id','>',$next_qid)->count_all();
        if($last_num=0) die('用户信息更新完毕');
        $qrcodes=ORM::factory('yyb_qrcode')->where('id','>',$next_qid)->order_by('id','ASC')->limit(100)->find_all();
        foreach ($qrcodes as $qrcode) {
            $bid=$qrcode->bid;
            echo 'bid:'.$bid.'<br>';
            $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
            $we=new Wechat($config);
            $qid=$qrcode->id;
            $openid=$qrcode->openid;
            echo 'openid:'.$openid.'<br>';
            if(!$qrcode->nickname){
                $userinfo=$we->getUserInfo($openid);
                // var_dump($userinfo);
                $qr=ORM::factory('yyb_qrcode')->where('id','=',$qid)->find();
                $nickname=$userinfo['nickname'];
                $sex=$userinfo['sex'];
                $headimgurl=$userinfo['headimgurl'];
                $qr->nickname=$nickname;
                $qr->sex=$sex;
                $qr->headimgurl=$headimgurl;
                $qr->save();
            }
            echo "----------------------------------<br>";

        }
        $ok=ORM::factory('yyb_cfg')->setCfg(1,'next_qid',$qid);
        exit();
    }
    // public function action_info(){
    //     set_time_limit(0);
    //     Kohana::$log->add("yybinfo{$bid}",'info');
    //     $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
    //     if(!$config['appid']||!$config['appsecret']) die('有赞参数未填写');
    //     $next_qid=$config['next_qid'];
    //     if(!$next_qid) $next_qid=0;
    //     $last_num=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('id','>',$next_qid)->count_all();
    //     if($last_num<=0) die('用户信息更新完毕');
    //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');
    //     $we=new Wechat($config);
    //     $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('id','>',$next_qid)->order_by('id','ASC')->limit(150)->find_all();
    //     foreach ($qrcodes as $qrcode) {
    //         $qid=$qrcode->id;
    //         $openid=$qrcode->openid;
    //         // echo $openid.'<br>';
    //         if(!$qrcode->nickname){
    //             $userinfo=$we->getUserInfo($openid);
    //             // var_dump($userinfo);
    //             $qr=ORM::factory('yyb_qrcode')->where('id','=',$qid)->find();
    //             $nickname=$userinfo['nickname'];
    //             $sex=$userinfo['sex'];
    //             $headimgurl=$userinfo['headimgurl'];
    //             $qr->nickname=$nickname;
    //             $qr->sex=$sex;
    //             $qr->headimgurl=$headimgurl;
    //             $qr->save();
    //         }

    //     }
    //     $ok=ORM::factory('yyb_cfg')->setCfg($bid,'next_qid',$qid);
    //     $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
    //     exit();
    // }
    //入口
    // public function action_transform($bid){
    //     set_time_limit(0);
    //     $time=time();
    //     $mem = Cache::instance('memcache');
    //     $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
    //     $login=ORM::factory('yyb_login')->where('id','=',$bid)->find();
    //     if(!$login->id) die('没有此用户');
    //     $order=ORM::factory('yyb_order')->where('bid','=',$bid)->where('transform','=',0)->and_where_open()->where('way','=',1)->or_where('time','<',$time)->and_where_close()->order_by('id','ASC')->find();
    //     if(!$order->id) die('没有未完全加入队列的任务');
    //     $oid=$order->id;
    //     $memname='cron1'.$bid.$oid;
    //     //echo $oid.'<br>';
    //     $order->start=1;
    //     $order->save();
    //     $order=ORM::factory('yyb_order')->where('id','=',$oid)->find();
    //     if($order->flag==1){
    //         $number1=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('flag','=',1)->count_all();
    //         $number2=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$order->id)->count_all();
    //         $bigqid=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('flag','=',1)->order_by('id','DESC')->find()->id;
    //     }else{
    //         $number1=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->count_all();
    //         $number2=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$order->id)->count_all();
    //         $bigqid=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->order_by('id','DESC')->find()->id;
    //     }
    //     //echo $number1.'<br>';
    //     //echo $number2.'<br>';
    //     if($number1<=2000){
    //         if($number1>$number2){
    //             if($order->flag==1){
    //                 $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('flag','=',1)->order_by('id','ASC')->find_all();
    //             }else{
    //                 $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->order_by('id','ASC')->find_all();
    //             }
    //             foreach ($qrcodes as $qr) {
    //                 $item=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$order->id)->where('qid','=',$qr->id)->find();
    //                 $item->bid=$bid;
    //                 $item->oid=$order->id;
    //                 $item->qid=$qr->id;
    //                 $item->cron= 1;
    //                 $item->save();
    //             }
    //         }else{
    //             $order->transform=1;
    //             $order->save();
    //         }
    //         exit();
    //     }
    //     $cname='oid'.$order->id;
    //     if($bigqid!=$config[$cname]){
    //         if(!$config[$cname]){
    //             $config[$cname]=0;
    //         }
    //         $numbers=floor($number1/30);//2
    //         //echo $numbers.'<br>';
    //         $cronnum=$mem->get($memname);
    //         //echo $cronnum.'<br>';
    //         if(!$cronnum) $cronnum=1;
    //         //echo $cronnum.'<br>';
    //         $cronnums=ORM::factory('yyb_item')->where('bid','=',$bid)->where('cron','=',$cronnum)->count_all();
    //         if($numbers==$cronnums){
    //             $cronnum=$cronnum+1;
    //             $cronnums=ORM::factory('yyb_item')->where('bid','=',$bid)->where('cron','=',$cronnum)->count_all();
    //         }
    //         //echo $cronnum.'<br>';
    //         //echo $config[$cname].'<br>';
    //         if($numbers-$cronnums>=2000){
    //             if($order->flag==1){
    //                 $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('flag','=',1)->where('id','>',$config[$cname])->order_by('id','ASC')->limit(2000)->find_all();
    //             }else{
    //                 $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('id','>',$config[$cname])->order_by('id','ASC')->limit(2000)->find_all();
    //             }
    //         }else{
    //             if($cronnum==30){
    //                 $limit=$numbers;
    //             }else{
    //                 $limit=$numbers-$cronnums;
    //             }
    //             if($order->flag==1){
    //                 $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('flag','=',1)->where('id','>',$config[$cname])->order_by('id','ASC')->limit($limit)->find_all();
    //             }else{
    //                 $qrcodes=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('id','>',$config[$cname])->order_by('id','ASC')->limit($limit)->find_all();
    //             }
    //         }
    //         $cronnum=floor($number2/$numbers)+1;
    //         if($cronnum>30){
    //             $cronnum=30;
    //         }
    //         //echo $cronnum.'<br>';
    //         foreach ($qrcodes as $qr) {
    //             $item=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$order->id)->where('qid','=',$qr->id)->find();
    //             $item->bid=$bid;
    //             $item->oid=$order->id;
    //             $item->qid=$qr->id;
    //             $item->cron= $cronnum;
    //             $item->save();
    //             $qid=$qr->id;
    //         }
    //         $mem->set($memname, $cronnum, 100);
    //         if($qid){
    //             $ok=ORM::factory('yyb_cfg')->setCfg($bid,$cname,$qid);
    //         }
    //         $this->config=$config=ORM::factory('yyb_cfg')->getCfg($bid,1);
    //     }else{
    //         $order->transform=1;
    //         $order->save();
    //     }
    //     exit();
    // }
    // public function action_cron1($bid){
    //     set_time_limit(0);
    //     $this->config=$config=ORM::factory('yyb_cfg')->getCfg($bid,1);
    //     require Kohana::find_file('vendor', 'weixin/wechat.class');
    //     $this->we=$we = new Wechat($config);
    //     $time=time();
    //     $order=ORM::factory('yyb_order')->where('bid','=',$bid)->where('state','=',0)->and_where_open()->where('way','=',1)->or_where('time','<',$time)->and_where_close()->order_by('id','ASC')->find();
    //     if(!$order->id) die('没有未处理的任务');
    //     $type=$order->type;
    //     $items=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$order->id)->where('cron','=',1)->where('flag','=',0)->limit(200)->find_all();
    //     foreach ($items as $item) {
    //         // echo '进items啦';
    //         $qid=$item->qid;
    //         $iid=$item->id;
    //         // echo $iid."<br>";
    //         // echo $qid."<br>";
    //         $openid=ORM::factory('yyb_qrcode')->where('id','=',$qid)->find()->openid;
    //         $nickname=ORM::factory('yyb_qrcode')->where('id','=',$qid)->find()->nickname;
    //         if(!$nickname){
    //             $userinfo=$we->getUserInfo($openid);
    //             $nickname=$userinfo['nickname'];
    //             $sex=$userinfo['sex'];
    //             $headimgurl=$userinfo['headimgurl'];
    //             $qrcode=ORM::factory('yyb_qrcode')->where('id','=',$qid)->find();
    //             $qrcode->nickname=$nickname;
    //             $qrcode->sex=$sex;
    //             $qrcode->headimgurl=$headimgurl;
    //             $qrcode->save();
    //         }
    //         if($type==1){
    //             $url=$order->url;
    //         }elseif($type==2){
    //             $url=$_SERVER["HTTP_HOST"].'/yyb/yzcode?url='.$order->url.'&openid='.$openid.'&bid='.$bid.'&iid='.$iid;
    //         }elseif($type==3){
    //             $url=$_SERVER["HTTP_HOST"].'/yyb/yzgift?url='.$order->url.'&openid='.$openid.'&bid='.$bid.'&iid='.$iid;
    //         }
    //         $title=$order->title;
    //         $content=$order->content;
    //         $time=$order->time;
    //         $result=$this->sendMessage($openid,$nickname,$url,$title,$content,$time);
    //         $item1=ORM::factory('yyb_item')->where('id','=',$iid)->find();
    //         $item1->flag=1;
    //         if($result['errmsg']=='ok'){
    //             $item1->state=1;
    //         }else{
    //             $item1->reason=$result['errmsg'];
    //         }
    //         $item1->save();
    //     }
    //     $count=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$order->id)->where('flag','=',0)->count_all();
    //     if($count==0&&$order->transform==1){
    //        $order->state=1;
    //        $order->save();
    //     }
    //     exit();
    // }
    public function action_storefuop($pri){// 获取服务号openid和进行跳转
        $privacy=base64_decode($pri);
        $hello=explode('$',$privacy);
        $bid=$hello[0];
        $tag=$hello[1];
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        //$time=ORM::factory('yyb_order')->where('id','=',$oid)->find()->time;
        //if($time<time())  die ('该活动已过期');
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        //$client->redirect('http://jfb.dev.smfyun.com/yyb/getopenid/'.$pri, 'snsapi_base');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/yyb/getopenid/'.$pri;
        $we = new Wechat($config);
        $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
        header("Location:$auth_url");
        exit;
        }
    public function action_getopenid($pri){
        $privacy=base64_decode($pri);
        //echo $privacy."<br>";
        $hello=explode('$',$privacy);
        $bid=$hello[0];
        $tag=$hello[1];
        $config=ORM::factory('yyb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        $token = $we->getOauthAccessToken();
        $userinfo = $we->getOauthUserinfo($token['access_token'], $token['openid']);
        $user=$we->getUserInfo($userinfo['openid']);
        $openid=$userinfo['openid'];
        $nickname=$userinfo['nickname'];
        $sex=$userinfo['sex'];
        $headimgurl=$userinfo['headimgurl'];
        $result['subscribe']=$user['subscribe'];
        $result['2dimage'] = ORM::factory('yyb_cfg')->where('bid', '=', $bid)->where('key', '=', '2dimage')->find()->id;
        //echo $openid."<br>";
        $qr=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $qr->bid=$bid;
        $qr->openid=$openid;
        $qr->nickname=$nickname;
        $qr->sex=$sex;
        $qr->headimgurl=$headimgurl;
        $qr->save();
        $qrcode=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if($qr->flag!=1&&$tag=='yuyue'){
            $qrcode->flag=1;
            $result['text']='您已经成功预约，将会第一时间收到店铺的促销、优惠信息，专享各种福利~';
        }elseif($qr->flag==1&&$tag=='yuyue'){
            $result['text']='你已经预约过啦，请勿重复预约';
        }elseif($tag=='yulan'){
            $qrcode->admin=1;
            $result['text']='你已成功绑定管理员预览';
        }elseif($tag=='cancel'){
            $qrcode->flag=0;
            $result['text']='你已成功取消预约收到消息！';
        }
        $qrcode->save();
        $qid=ORM::factory('yyb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
        //$iid=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$oid)->where('qid','=',$qid)->find()->id;
        //$title=ORM::factory('yyb_order')->where('id','=',$oid)->find()->title;
        //$item=ORM::factory('yyb_order')->where('id','=',$oid)->find()->item;
        //$content=ORM::factory('yyb_order')->where('id','=',$oid)->find()->content;
        // if(!$iid){
        //     $user=ORM::factory('yyb_item');
        //     $user->bid=$bid;
        //     $user->oid=$oid;
        //     $user->qid=$qid;
        //     $user->save();
        //     $orders=ORM::factory('yyb_order')->where('id','=',$oid)->find();
        //     $number=ORM::factory('yyb_item')->where('bid','=',$bid)->where('oid','=',$oid)->count_all();
        //     $orders->number=$number;
        //     $orders->save();
        //     // $result['title']=$title;
        //     // $result['item']=$item;
        //     // $result['content']=$content;
        //     $result['text']='你预约成功啦';
        // }else{
        //     // $result['title']=$title;
        //     // $result['item']=$item;
        //     // $result['content']=$content;
        //     $result['text']='你已经预约过啦，请勿重复预约<br>';
        // }
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        // $wx['appid'] = $config['appid'];
        // $wx['appsecret'] = $config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        // $we = new Wechat($wx);

        $jsapi = $we->getJsSign($callback_url);
        $ticket = $we->getJsCardTicket();
        $sign = $we->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory('weixin/yyb/getopenid')
            ->bind('jsapi', $jsapi)
            ->bind('ticket', $ticket)
            ->bind('sign', $sign)
            ->bind('result',$result);
        //exit;
    }
    public function sendMessage($openid,$nickname,$url,$title,$content,$time){
        $bid=$this->bid;
        $tplmsg['template_id'] = $this->config['mbtpl'];
        $tplmsg['touser'] = $openid;
        $tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = $title;
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = $nickname;
        $tplmsg['data']['keyword3']['value'] = '预约通知';
        // $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s',$time);
        $tplmsg['data']['remark']['value'] = $content;
        $tplmsg['data']['remark']['color'] = '#666666';
        Kohana::$log->add("{$bid}tplmsgyyb{$openid}",print_r($tplmsg,'true'));
        $result=$this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add('result',print_r($result,'true'));
        return $result;
    }
     //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "yyb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }
}
