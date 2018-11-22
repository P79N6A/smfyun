<?php defined('SYSPATH') or die('No direct script access.');

class Controller_kmi extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    public $methodVersion='3.0.0';
    var $we;
    var $client;
    public function before() {
        Database::$default = "kmi";
        parent::before();
        if (Request::instance()->action == 'jump') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'kmpass') return;
    }
    public function action_delete($appid){
        $mem = Cache::instance('memcache');
        $cachename='wechat_access_token'.$appid;
        $result = $mem->get($cachename);
        var_dump($result);
        $result = $mem->delete($cachename);
        var_dump($result);
    }
    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add("cxb$bid", print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add("cxb$bid", print_r($result11, true));
        if($postStr){
            //Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $appid =$result11['app_id'];
        //$id=$result11['id'];
        $ordermsg=$result11['msg'];
        $kdt_id=$result11['kdt_id'];
        $status=$result11['status'];
        //Kohana::$log->add('$status', print_r($status, true));
        Kohana::$log->add('type', print_r($result11['type'], true));
        if($result11['type']=='TRADE_ORDER_STATE'){
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'){
                $bid = ORM::factory('kmi_login')->where('shopid','=',$kdt_id)->find()->id;
                Kohana::$log->add('cxb1$bid', print_r($bid, true));
                $this->bid=$bid;
                $this->access_token=ORM::factory('kmi_login')->where('id', '=', $this->bid)->find()->access_token;
                Kohana::$log->add('access_token1', print_r($this->access_token, true));
                $expiretime=ORM::factory('kmi_login')->where('id', '=', $bid)->find()->expiretime;
                if(strtotime($expiretime) < time()) die ('插件已过期');
                Kohana::$log->add('expiretime1', print_r($expiretime, true));
                $config=ORM::factory('kmi_cfg')->getCfg($bid,1);
                $this->config=$config;
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $this->we=new Wechat($this->config);
                Kohana::$log->add('cxb1$bid', print_r(2, true));
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                if($this->access_token){
                    $this->client = new YZTokenClient($this->access_token);
                }else{
                    Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                Kohana::$log->add('cxb1$bid', print_r(1, true));
                $posttid=urldecode($ordermsg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("cxb1$bid", print_r($jsona, true));
                $tid=$jsona['tid'];
                $mem = Cache::instance('memcache');
                $nametid=$bid.$tid;
                $gettid = $mem->get($nametid);
                Kohana::$log->add("nametid$bid", print_r($nametid, true));
                Kohana::$log->add("nametid$bid", print_r($gettid, true));
                if($gettid==$nametid){
                    Kohana::$log->add("cxb1x$bid", print_r($tid, true));
                    return; 
                } 
                $gettid = $mem->get($tid);
                Kohana::$log->add("tid$bid", print_r($tid, true));
                Kohana::$log->add("tid$bid", print_r($gettid, true));
                $mem->set($nametid, $nametid, 1);
                $method = 'youzan.trade.get';
                $params = [
                    // 'with_childs'=>true,
                    'tid'=>$tid,
                ];
                $result = $this->client->post($method,'4.0.0', $params, $files);
                $trade=$result['response']['full_order_info'];
                Kohana::$log->add('trade', print_r($result, true));
                $num_iid = $trade['orders'][0]['item_id'];
                // $num =$trade['num'];
                $num=0;
                foreach ($trade['orders'] as $order1){
                    $num=$num+$order1['num'];
                }
                //$this->config=$config;
                $tid=$trade['order_info']['tid'];
                $title=$trade['orders'][0]['title'];
                $name=$trade['address_info']['receiver_name'];
                $type=$trade['order_info']['type'];
                $price=$trade['orders'][0]['price'];
                $weixin_user_id =$trade['buyer_info']['fans_id'];
                $method = 'youzan.users.weixin.follower.get';
                $params = [
                'fans_id'=>$weixin_user_id,
                ];
                $openids = $this->client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add('openids', print_r($openids, true));
                $openid=$openids['response']['user']['weixin_openid'];
                $nick=$openids['response']['user']['nick'];
                $avatar=$openids['response']['user']['avatar'];
                $pay_time=$trade['order_info']['pay_time'];
                // echo "<pre>";
                // var_dump($openids);
                // echo "<pre>";
                // echo $openid;
                // exit;
                Kohana::$log->add('openid', print_r($openid, true));
                if(!$pay_time) $pay_time=date('Y-m-d H:i:s',time());
                $key = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->key;
                //echo $key.'<br>';
                //Kohana::$log->add('$key', print_r($key, true));
                $value =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
                //echo $value.'<br>';
                //判断限时
                //Kohana::$log->add('$value', print_r($value, true));
                $startdate =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->startdate;
                //echo $startdate.'<br>';
                //Kohana::$log->add('$startdate', print_r($startdate, true));
                $enddate =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->enddate;
                //echo $enddate.'<br>';
                //$config = $this->config;
                $tpl =$config['tpl'];
                if($pay_time){
                    $nowdate=strtotime($pay_time);
                }else{
                    $nowdate=time();
                }
                Kohana::$log->add('$enddate', print_r($enddate, true));
                Kohana::$log->add('$tpl', print_r($tpl, true));
                if($enddate){
                    if($startdate && $startdate<$nowdate&&$nowdate<$enddate ){

                        if($key=='kmi'){
                            //echo '即将进入卡密<br>';
                            $this->action_km($bid,$this->config,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='hongbao'){
                            //echo '即将进入红包<br>';
                            $tempname=ORM::factory("kmi_login")->where('id','=',$bid)->find()->user;
                            $tempmoney=ORM::factory("kmi_item")->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
                            $tempmoney=$tempmoney*$num;
                            $this->action_hongbao($this->config, $openid, '', $tempname, $tempmoney,$bid,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='coupon'){
                            //echo '即将进入卡劵<br>';
                            $this->action_coupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='gift'){
                            //echo '即将进入赠品<br>';
                            $this->action_gift($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='yzcoupon'){
                            //echo '即将进入有赞优惠券<br>';
                            $this->action_yzcoupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='freedom'){
                            //echo "即将进入自定义文本消息";
                            $this->action_freedom($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，您的购买时间不在奖品有效期内，无法为您发送奖品';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            Kohana::$log->add('msg', print_r($msg, true));
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，您的购买时间不在奖品有效期内，无法为您发送奖品';
                            Kohana::$log->add('msg1', print_r($msg, true));
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }else{
                    //echo "外键<br>";
                    if($startdate && $startdate<$nowdate){
                        if($key=='kmi'){
                            //echo '即将进入卡密<br>';
                            $this->action_km($bid,$this->config,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='hongbao'){
                            //echo '即将进入红包<br>';
                            $tempname=ORM::factory("kmi_login")->where('id','=',$bid)->find()->user;
                            $tempmoney=ORM::factory("kmi_item")->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
                            $tempmoney=$tempmoney*$num;
                            $this->action_hongbao($this->config, $openid, '', $tempname, $tempmoney,$bid,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='coupon'){
                            //echo '即将进入卡劵<br>';
                            //Kohana::$log->add('$coupon', '11111');
                            $this->action_coupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='gift'){
                            //echo '发赠品啦<br>';
                            $this->action_gift($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='yzcoupon'){
                            //echo '即将进入有赞优惠券<br>';
                            $this->action_yzcoupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='freedom'){
                            //echo "即将进入自定义文本消息";
                            $this->action_freedom($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                    }

                }

            }
            exit;
        }elseif ($result11['type']=='TRADE') {
            if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'){
                $bid = ORM::factory('kmi_login')->where('shopid','=',$kdt_id)->find()->id;
                Kohana::$log->add("cxb2$bid", print_r($bid, true));
                $this->bid=$bid;
                $this->access_token=ORM::factory('kmi_login')->where('id', '=', $this->bid)->find()->access_token;
                Kohana::$log->add('access_token2', print_r($this->access_token, true));
                $expiretime=ORM::factory('kmi_login')->where('id', '=', $bid)->find()->expiretime;
                Kohana::$log->add('expiretime2', print_r($expiretime, true));
                if(strtotime($expiretime) < time()) die ('插件已过期');
                $config=ORM::factory('kmi_cfg')->getCfg($bid,1);
                $this->config=$config;
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $this->we=new Wechat($this->config);
                Kohana::$log->add('cxb2$bid', print_r(2, true));
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                if($this->access_token){
                    $this->client = new YZTokenClient($this->access_token);
                }else{
                    Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
                }
                Kohana::$log->add('cxb2$bid', print_r(1, true));
                $posttid=urldecode($ordermsg);
                $jsona=json_decode($posttid,true);
                //$status=$jsona['trade']['status'];
                Kohana::$log->add("1cxb$bid", print_r($jsona, true));
                //Kohana::$log->add('$kmi', '111111');
                $num_iid = $jsona['trade']['num_iid'];
                $num =$jsona['trade']['num'];
                //$this->config=$config;
                $tid=$jsona['trade']['tid'];
                $mem = Cache::instance('memcache');
                $nametid=$bid.$tid;
                $gettid = $mem->get($nametid);
                Kohana::$log->add("nametid$bid", print_r($nametid, true));
                Kohana::$log->add("nametid$bid", print_r($gettid, true));
                if($gettid==$nametid){
                    Kohana::$log->add("cxbxx$bid", print_r($tid, true));
                    return; 
                } 
                $mem->set($nametid, $nametid, 1);
                $title=$jsona['trade']['title'];
                $name=$jsona['trade']['buyer_nick'];
                $type=$jsona['trade']['type'];
                $price=$jsona['trade']['price'];
                $weixin_user_id =$jsona['trade']['fans_info']['fans_id'];
                $method = 'youzan.users.weixin.follower.get';
                $params = [
                'fans_id'=>$weixin_user_id,
                ];
                $openids = $this->client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add('openids1', print_r($openids, true));
                $openid=$openids['response']['user']['weixin_openid'];
                $nick=$openids['response']['user']['nick'];
                $avatar=$openids['response']['user']['avatar'];
                $pay_time=$jsona['trade']['pay_time'];
                Kohana::$log->add('openid1', print_r($openid, true));
                if(!$pay_time) $pay_time=date('Y-m-d H:i:s',time());
                $key = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->key;
                //echo $key.'<br>';
                //Kohana::$log->add('$key', print_r($key, true));
                $value =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
                //echo $value.'<br>';
                //判断限时
                //Kohana::$log->add('$value', print_r($value, true));
                $startdate =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->startdate;
                //echo $startdate.'<br>';
                //Kohana::$log->add('$startdate', print_r($startdate, true));
                $enddate =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->enddate;
                //echo $enddate.'<br>';
                //$config = $this->config;
                $tpl =$config['tpl'];
                if($pay_time){
                    $nowdate=strtotime($pay_time);
                }else{
                    $nowdate=time();
                }
                //Kohana::$log->add('$enddate', print_r($enddate, true));
                //Kohana::$log->add('$tpl', print_r($tpl, true));
                if($enddate){
                    if($startdate && $startdate<$nowdate&&$nowdate<$enddate ){

                        if($key=='kmi'){
                            //echo '即将进入卡密<br>';
                            $this->action_km($bid,$this->config,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='hongbao'){
                            //echo '即将进入红包<br>';
                            $tempname=ORM::factory("kmi_login")->where('id','=',$bid)->find()->user;
                            $tempmoney=ORM::factory("kmi_item")->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
                            $tempmoney=$tempmoney*$num;
                            $this->action_hongbao($this->config, $openid, '', $tempname, $tempmoney,$bid,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='coupon'){
                            //echo '即将进入卡劵<br>';
                            $this->action_coupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='gift'){
                            //echo '即将进入赠品<br>';
                            $this->action_gift($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='yzcoupon'){
                            //echo '即将进入有赞优惠券<br>';
                            $this->action_yzcoupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='freedom'){
                            //echo "即将进入自定义文本消息";
                            $this->action_freedom($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，您的购买时间不在奖品有效期内，无法为您发送奖品';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{

                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，您的购买时间不在奖品有效期内，无法为您发送奖品';
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }else{
                    //echo "外键<br>";

                    if($startdate && $startdate<$nowdate){
                        if($key=='kmi'){
                            //echo '即将进入卡密<br>';
                            $this->action_km($bid,$this->config,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='hongbao'){
                            //echo '即将进入红包<br>';
                            $tempname=ORM::factory("kmi_login")->where('id','=',$bid)->find()->user;
                            $tempmoney=ORM::factory("kmi_item")->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
                            $tempmoney=$tempmoney*$num;
                            $this->action_hongbao($this->config, $openid, '', $tempname, $tempmoney,$bid,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='coupon'){
                            //echo '即将进入卡劵<br>';
                            //Kohana::$log->add('$coupon', '11111');
                            $this->action_coupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='gift'){
                            //echo '发赠品啦<br>';
                            $this->action_gift($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='yzcoupon'){
                            //echo '即将进入有赞优惠券<br>';
                            $this->action_yzcoupon($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                        if($key=='freedom'){
                            //echo "即将进入自定义文本消息";
                            $this->action_freedom($bid,$this->config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar);
                        }
                    }

                }
            }
            exit;
        }
        //exit;
    }
    // //有赞优惠卷
    public function action_yzcoupon($bid,$config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar){
        //echo "来到有赞优惠劵啦<br>";
        //$we = new Wechat($config);
        // $mem = Cache::instance('memcache');
        // $gettid = $mem->get($tid);
        // if($gettid==$tid){
        //     Kohana::$log->add("cxb$bid", print_r($tid, true));
        //     return; 
        // } 
        // $mem->set($tid, $tid, 1);
        $tid_num = ORM::factory('kmi_tid')->where('tid', '=', $tid)->where('num_iid','=',$num_iid)->count_all();
        if($tid_num==0 && $tid!=""){
            $value=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $km_content=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;

            $km_text =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
            $tpl=$config['tpl'];
            $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_tids`( `tid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`,`km_comtent`) VALUES ('$tid',$bid,$num_iid,'$pay_time','$type','$name','$title',$price,$num,'$openid',$weixin_user_id,'$nick','$avatar','yzcoupon','$km_content')");
            $trade1=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($trade1->id) return;
            $sql->execute();
            $km_num=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
            $limit=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
            //echo "即将进入限购";
            if ($limit > 0) {
                $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from kmi_tids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
                $result =$sql->execute()->as_array();
                $sum = $result[0]['sum'];
                if ($sum >= $limit){
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您的领取资格已到达上限';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您的领取资格已到达上限';
                        $this->we->sendCustomMessage($msg);
                    }
                }else{
                    if($km_num>=$num){
                        for ($c=0; $c <$num ; $c++) {

                            //$yz['appid'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appid')->find()->value;
                            //echo $yz['appid'].'<br>';
                            //$yz['appsecret'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appsecret')->find()->value;
                            //echo $yz['appsecret'].'<br>';
                            $oid = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
                            //echo $oid.'<br>';
                            //$client = new KdtApiClient($yz['appid'],$yz['appsecret']);
                            $method = 'youzan.ump.coupon.take';
                            $params = [
                                'coupon_group_id'=>$value,
                                'weixin_openid'=>$openid,
                             ];
                            $results = $this->client->post($method, $this->methodVersion, $params, $files);
                            //var_dump($results);
                            Kohana::$log->add('weixin:yzcoupon', print_r($results, true));//写入日志，可以删除
                            if($results['response']){
                                if(isset($tpl)){
                                    $tplmsg['touser'] = $openid;
                                    $tplmsg['template_id'] = $tpl;

                                    $tplmsg['data']['keyword1']['value'] = '有赞优惠卷';
                                    $tplmsg['data']['keyword1']['color'] = '#999999';
                                    // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                    // $tplmsg['data']['keyword2']['color'] = '#999999';
                                    $tplmsg['data']['remark']['value'] = $km_text;
                                    $tplmsg['data']['remark']['color'] = '#999999';
                                    $result=$this->we->sendTemplateMessage($tplmsg);
                                    $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                    $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                    $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                    //echo 'vvvvvvvv<br>';
                                    //$tids->log=$results['response']['present_name'];
                                    $item->km_num--;
                                    $item->save();
                                    $tids->state=1;
                                    $tids->save();
                                }else{
                                    $msg['msgtype'] = 'text';
                                    $msg['touser'] = $openid;
                                    $msg['text']['content'] = $km_text;
                                    $this->we->sendCustomMessage($msg);
                                    $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                    $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                    $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                    //echo 'vvvvvvvv<br>';
                                    //$tids->log=$results['response']['present_name'];
                                    $item->km_num--;
                                    $item->save();
                                    $tids->state=1;
                                    $tids->save();
                                }
                            }else if($results['error_response']){
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $tids->log=$results['error_response']['code'].$results['error_response']['msg'];
                                $tids->save();
                            }
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，有赞优惠券已被领完';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，您来晚了一步，有赞优惠券已被领完';
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
            }else{
                if($km_num>=$num){
                    //echo "进入限购啦<br>";
                    for ($c=0; $c <$num; $c++) {
                        //echo "按熟练循环输出<br>";

                        //$yz['appid'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appid')->find()->value;
                        //echo $yz['appid'].'<br>';
                        //$yz['appsecret'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appsecret')->find()->value;
                        //echo $yz['appsecret'].'<br>';
                        $oid = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
                        //echo $oid.'<br>';
                        //$client = new KdtApiClient($yz['appid'],$yz['appsecret']);
                        $method = 'youzan.ump.coupon.take';
                        $params = [
                            'coupon_group_id'=>$value,
                            'weixin_openid'=>$openid,
                         ];
                        $results = $this->client->post($method, $this->methodVersion, $params, $files);
                        //echo "<pre>";
                        //var_dump($results);
                        //echo "<pre>";
                        Kohana::$log->add('weixin:yzcoupon', print_r($results, true));//写入日志，可以删除
                        if($results['response']){
                            if(isset($tpl)){
                                $tplmsg['touser'] = $openid;
                                $tplmsg['template_id'] = $tpl;
                                $tplmsg['data']['keyword1']['value'] = '有赞优惠卷';
                                $tplmsg['data']['keyword1']['color'] = '#999999';
                                // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                // $tplmsg['data']['keyword2']['color'] = '#999999';
                                $tplmsg['data']['remark']['value'] = $km_text;
                                $tplmsg['data']['remark']['color'] = '#999999';
                                $result=$this->we->sendTemplateMessage($tplmsg);
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                //echo 'vvvvvvvv<br>';
                                //$tids->log=$results['response']['present_name'];
                                $item->km_num--;
                                $item->save();
                                $tids->state=1;
                                $tids->save();
                            }else{
                                $msg['msgtype'] = 'text';
                                $msg['touser'] = $openid;
                                $msg['text']['content'] = $km_text;
                                $this->we->sendCustomMessage($msg);
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                //echo 'vvvvvvvv<br>';
                                //$tids->log=$results['response']['present_name'];
                                $item->km_num--;
                                $item->save();
                                $tids->state=1;
                                $tids->save();
                            }
                         }else if($results['error_response']){
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            //echo $results['error_response']['code'].$results['error_response']['msg'].'<br>';
                            $tids->log=$results['error_response']['code'].$results['error_response']['msg'];
                            $tids->save();
                        }
                    }

                }else{
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，有赞优惠券已被领完';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您来晚了一步，有赞优惠券已被领完';
                        $this->we->sendCustomMessage($msg);
                    }
                }
            }
        }
    }
    //自定义文本消息
    public function action_freedom($bid,$config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar){
        //$we = new Wechat($config);
        //Kohana::$log->add('aaaaa', 'bbbbb');
        // $mem = Cache::instance('memcache');
        // $gettid = $mem->get($tid);
        // if($gettid==$tid){
        //     Kohana::$log->add("cxb$bid", print_r($tid, true));
        //     return; 
        // } 
        // $mem->set($tid, $tid, 1);
        $tid_num = ORM::factory('kmi_tid')->where('tid', '=', $tid)->where('num_iid','=',$num_iid)->count_all();
        if($tid_num==0 && $tid!=""){
            $km_text=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
            $km_content=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
            $tpl =$config['tpl'];
            //echo $tpl."<br>";
            $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_tids`( `tid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`,`km_comtent`) VALUES ('$tid',$bid,$num_iid,'$pay_time','$type','$name','$title',$price,$num,'$openid',$weixin_user_id,'$nick','$avatar','freedom','$km_content')");
            $trade1=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($trade1->id) return;
            $sql->execute();
            if(isset($tpl)){
                $tplmsg['touser'] = $openid;
                $tplmsg['template_id'] = $tpl;
                $tplmsg['data']['keyword1']['value'] = '文本消息';
                $tplmsg['data']['keyword1']['color'] = '#999999';
                // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                // $tplmsg['data']['keyword2']['color'] = '#999999';
                $tplmsg['data']['remark']['value'] = $km_text;
                $tplmsg['data']['remark']['color'] = '#999999';
                $result=$this->we->sendTemplateMessage($tplmsg);

            }else{
                $msg['msgtype'] = 'text';
                $msg['touser'] = $openid;
                $msg['text']['content'] = $km_text;
                $result=$this->we->sendCustomMessage($msg);
            }
            //echo "<pre>";
            //var_dump($result);
            //echo "<pre>";
            //echo "<br>";
            if($result['errmsg']=='ok'){
                //echo "发送成功<br>";
                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                Kohana::$log->add('weixin:freedommsg', print_r($msg, true));//写入日志，可以删除
                Kohana::$log->add('weixin:freedomresult', print_r($result, true));//写入日志，可以删除
                $tids->state=1;
                $tids->save();
            }else{
                //echo "发送失败<br>";
                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                Kohana::$log->add('weixin:freedommsg', print_r($msg, true));//写入日志，可以删除
                Kohana::$log->add('weixin:freedomresult', print_r($result, true));//写入日志，可以删除
                $tids->log=$result['errmsg'];
                $tids->save();
            }
        }
    }
    //红包
    public function action_hongbao($config, $openid, $we='', $tempname=1,$money,$bid,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar){
        // $mem = Cache::instance('memcache');
        // $gettid = $mem->get($tid);
        // if($gettid==$tid){
        //     Kohana::$log->add("cxb$bid", print_r($tid, true));
        //     return; 
        // } 
        // $mem->set($tid, $tid, 1);
        //if (!$we) {
        //$we = new Wechat($config);
        $tid_num = ORM::factory('kmi_tid')->where('tid', '=', $tid)->where('num_iid','=',$num_iid)->count_all();
        //echo $tid_num.'<br>';
        if($tid_num==0 && $tid!=""){
            $value=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $km_content=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
            $km_text=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
            $tpl =$config['tpl'];
            //echo $tpl."<br>";
            $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_tids`( `tid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`,`km_comtent`) VALUES ('$tid',$bid,$num_iid,'$pay_time','$type','$name','$title',$price,$num,'$openid',$weixin_user_id,'$nick','$avatar','hongbao','$km_content')");
            $sql->execute();
            $trade1=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($trade1->id) return;
            //判断库存
            //echo "yiyiyi.<br>";
            $km_num=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
            $limit=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
            if ($limit > 0) {
                $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from kmi_tids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
                $result =$sql->execute()->as_array();
                $sum = $result[0]['sum'];
                if ($sum >= $limit){
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您的领取资格已到达上限';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您的领取资格已到达上限';
                        $this->we->sendCustomMessage($msg);
                    }
                }else{
                    if($km_num>0){
                        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
                        require_once Kohana::find_file('vendor', 'weixin/inc');
                        $we = new Wechat($config);
                        $mch_billno = $config['partnerid']. date('YmdHis').rand(1000, 9999); //订单号
                        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
                        $data["mch_billno"] = $mch_billno; //订单号
                        $data["mch_id"] = $config['partnerid']; //支付商户号
                        $data["wxappid"] = $config['appid'];//appid
                        $data["re_openid"] =$openid;//用户openid
                        $data["total_amount"] = $money;//红包金额
                        $data["total_num"] = 1; //总人数
                        $data["act_name"] = "本次活动"; //活动名称
                        //$data["nick_name"] = $config['name'].""; //提供方名称
                        $data["send_name"] = $config['name'].""; //红包发送者名称
                        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
                        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
                        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案
                        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
                        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
                        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
                        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

                        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除
                        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $tempname);
                        //支付安全验证函数（核心函数）
                        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
                        //将xml格式数据转化为string
                        $result['xml'] = $resultXml;
                        $result['return_code'] = (string)$response->return_code;
                        $result['return_msg'] = (string)$response->return_msg[0];
                        $result['result_code'] = (string)$response->result_code[0];
                        $result['re_openid'] = (string)$response->re_openid[0];
                        $result['total_amount'] = (string)$response->total_amount[0];
                        $result['err_code'] = (string)$response->err_code[0];
                        Kohana::$log->add('weixin:hongbaotest', print_r($result, true));//写入日志，可以删除
                        //echo "d.<br>";
                        //var_dump($result);
                        //return $result;//hash数组
                        if($result['result_code']=='SUCCESS'){
                            if(isset($tpl)){
                                $tplmsg['touser'] = $openid;
                                $tplmsg['template_id'] = $tpl;
                                //$tplmsg['url'] = $this->baseurl.'index/'. $bid.'?url=dka&cksum='. $cksum .'&openid='. base64_encode($op->openid);
                                ////echo $tplmsg['url'].'<br>';
                               $tplmsg['data']['keyword1']['value'] = '红包';
                                $tplmsg['data']['keyword1']['color'] = '#999999';
                                // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                // $tplmsg['data']['keyword2']['color'] = '#999999';
                                $tplmsg['data']['remark']['value'] = $km_text;
                                $tplmsg['data']['remark']['color'] = '#999999';
                                $this->we->sendTemplateMessage($tplmsg);
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $item->km_num=$item->km_num-$num;
                                $item->save();
                                $tids->state=1;
                                $tids->save();
                            }else{
                                $msg['msgtype'] = 'text';
                                $msg['touser'] = $openid;
                                $msg['text']['content'] = $km_text;
                                $this->we->sendCustomMessage($msg);
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $item->km_num=$item->km_num-$num;
                                $item->save();
                                $tids->state=1;
                                $tids->save();
                            }
                        }else{
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $tids->log= $result['return_msg'];
                            $tids->save();
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，红包已被领完';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，您来晚了一步，红包已被领完';
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
            }else{
                if($km_num>0){
                    //echo "wwwww";
                    require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
                    require_once Kohana::find_file('vendor', 'weixin/inc');
                    $we = new Wechat($config);
                    $mch_billno = $config['partnerid']. date('YmdHis').rand(1000, 9999); //订单号
                    $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
                    $data["mch_billno"] = $mch_billno; //订单号
                    $data["mch_id"] = $config['partnerid']; //支付商户号
                    $data["wxappid"] = $config['appid'];//appid
                    $data["re_openid"] =$openid;//用户openid
                    $data["total_amount"] = $money;//红包金额
                    $data["total_num"] = 1; //总人数
                    $data["act_name"] = "本次活动"; //活动名称
                    //$data["nick_name"] = $config['name'].""; //提供方名称
                    $data["send_name"] = $config['name'].""; //红包发送者名称
                    $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
                    $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
                    //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案
                    $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
                    $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
                    $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
                    $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址
                    if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除
                    $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $tempname);
                    //支付安全验证函数（核心函数）
                    $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
                    //将xml格式数据转化为string
                    $result['xml'] = $resultXml;
                    $result['return_code'] = (string)$response->return_code;
                    $result['return_msg'] = (string)$response->return_msg[0];
                    $result['result_code'] = (string)$response->result_code[0];
                    $result['re_openid'] = (string)$response->re_openid[0];
                    $result['total_amount'] = (string)$response->total_amount[0];
                    $result['err_code'] = (string)$response->err_code[0];
                    Kohana::$log->add('weixin:hongbaotest', print_r($result, true));//写入日志，可以删除
                    //echo "d.<br>";
                    //var_dump($result);
                    //return $result;//hash数组
                    if($result['result_code']=='SUCCESS'){
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            //$tplmsg['url'] = $this->baseurl.'index/'. $bid.'?url=dka&cksum='. $cksum .'&openid='. base64_encode($op->openid);
                            ////echo $tplmsg['url'].'<br>';
                            $tplmsg['data']['keyword1']['value'] = '红包';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = $km_text;
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $result=$this->we->sendTemplateMessage($tplmsg);
                            Kohana::$log->add('weixin:tplmsg', print_r($tplmsg, true));//写入日志，可以删除
                            Kohana::$log->add('weixin:tplresult', print_r($result, true));//写入日志，可以删除
                            $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                            $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $item->km_num=$item->km_num-$num;
                            $item->save();
                            $tids->state=1;
                            $tids->save();
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = $km_text;
                            $we->sendCustomMessage($msg);
                            $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                            $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $item->km_num=$item->km_num-$num;
                            $item->save();
                            $tids->state=1;
                            $tids->save();
                        }
                    }elseif ($result['err_code']=='SEND_FAILED'){
                        $mch_billno = $config['partnerid']. date('YmdHis').rand(1000, 9999); //订单号
                        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
                        $data["mch_billno"] = $mch_billno; //订单号
                        $data["mch_id"] = $config['partnerid']; //支付商户号
                        $data["wxappid"] = $config['appid'];//appid
                        $data["re_openid"] =$openid;//用户openid
                        $data["total_amount"] = $money;//红包金额
                        $data["total_num"] = 1; //总人数
                        $data["act_name"] = "本次活动"; //活动名称
                        //$data["nick_name"] = $config['name'].""; //提供方名称
                        $data["send_name"] = $config['name'].""; //红包发送者名称
                        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
                        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
                        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案
                        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
                        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
                        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
                        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址
                        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除
                        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $tempname);
                        //支付安全验证函数（核心函数）
                        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
                        //将xml格式数据转化为string
                        $result['xml'] = $resultXml;
                        $result['return_code'] = (string)$response->return_code;
                        $result['return_msg'] = (string)$response->return_msg[0];
                        $result['result_code'] = (string)$response->result_code[0];
                        $result['re_openid'] = (string)$response->re_openid[0];
                        $result['total_amount'] = (string)$response->total_amount[0];
                        $result['err_code'] = (string)$response->err_code[0];
                        Kohana::$log->add('weixin:hongbaotest', print_r($result, true));//写入日志，可以删除
                        if($result['result_code']=='SUCCESS'){
                            if(isset($tpl)){
                                $tplmsg['touser'] = $openid;
                                $tplmsg['template_id'] = $tpl;
                                //$tplmsg['url'] = $this->baseurl.'index/'. $bid.'?url=dka&cksum='. $cksum .'&openid='. base64_encode($op->openid);
                                ////echo $tplmsg['url'].'<br>';
                                $tplmsg['data']['keyword1']['value'] = '红包';
                                $tplmsg['data']['keyword1']['color'] = '#999999';
                                // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                // $tplmsg['data']['keyword2']['color'] = '#999999';
                                $tplmsg['data']['remark']['value'] = $km_text;
                                $tplmsg['data']['remark']['color'] = '#999999';
                                $this->we->sendTemplateMessage($tplmsg);
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $item->km_num=$item->km_num-$num;
                                $item->save();
                                $tids->state=1;
                                $tids->save();
                            }else{
                                $msg['msgtype'] = 'text';
                                $msg['touser'] = $openid;
                                $msg['text']['content'] = $km_text;
                                $we->sendCustomMessage($msg);
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $item->km_num=$item->km_num-$num;
                                $item->save();
                                $tids->state=1;
                                $tids->save();
                            }
                        }else{
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $tids->log= $result['return_msg'];
                            $tids->save();
                        }
                    }else{
                        $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                        $tids->log= $result['return_msg'];
                        $tids->save();
                    }
                }else{
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，红包已被领完';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您来晚了一步，红包已被领完';
                        $this->we->sendCustomMessage($msg);
                    }
                }
            }
        }
    }
    //微信卡劵
    public function action_coupon($bid,$config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar){
        //$we = new Wechat($config);
        //echo "来到卡劵啦<br>";
        //Kohana::$log->add('$coupon', '1234');
        // $mem = Cache::instance('memcache');
        // $gettid = $mem->get($tid);
        // if($gettid==$tid){
        //     Kohana::$log->add("cxb$bid", print_r($tid, true));
        //     return; 
        // } 
        // $mem->set($tid, $tid, 1);
        $tid_num = ORM::factory('kmi_tid')->where('tid', '=', $tid)->where('num_iid','=',$num_iid)->count_all();
        if($tid_num==0 && $tid!=""){
            $value=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $km_content=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
            $km_text=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
            $tpl=$config['tpl'];
            //echo $km_text.'<br';
            $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_tids`( `tid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`,`km_comtent`) VALUES ('$tid',$bid,$num_iid,'$pay_time','$type','$name','$title',$price,$num,'$openid',$weixin_user_id,'$nick','$avatar','coupon','$km_content')");
            $trade1=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($trade1->id) return;
            $sql->execute();
            $km_num=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
            $limit=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
            //echo "即将进入限购";
            if ($limit > 0) {
                $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from kmi_tids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
                $result =$sql->execute()->as_array();
                $sum = $result[0]['sum'];
                if ($sum >= $limit){
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您的领取资格已到达上限';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您的领取资格已到达上限';
                        $this->we->sendCustomMessage($msg);
                    }
                }else{
                    if($km_num>=$num){
                        for ($c=0; $c <$num ; $c++) {
                            $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                            $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                            $item->km_num--;
                            $item->save();
                            $password =base64_encode($tid.','.$num_iid.','.$value.','.$bid);
                            $url = 'http://jfb.dev.smfyun.com/kmi/jump/?psd='.$password;
                            $msg1 = '<a href="'.$url.'">领取卡劵</a>';
                            $msgs = str_replace("「%s」",$msg1, $km_text);
                            if(isset($tpl)){
                                $tplmsg['touser'] = $openid;
                                $tplmsg['template_id'] = $tpl;
                                $tplmsg['url'] = $url;
                                ////echo $tplmsg['url'].'<br>';

                                $tplmsg['data']['keyword1']['value'] = '微信卡劵';
                                $tplmsg['data']['keyword1']['color'] = '#999999';
                                // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                // $tplmsg['data']['keyword2']['color'] = '#999999';
                                $tplmsg['data']['remark']['value'] = '赶紧点此领取卡劵吧';
                                $tplmsg['data']['remark']['color'] = '#999999';
                                $this->we->sendTemplateMessage($tplmsg);
                            }else{
                                $msg['msgtype'] = 'text';
                                $msg['touser'] = $openid;
                                $msg['text']['content'] = $msgs;
                                $a=$this->we->sendCustomMessage($msg);
                            }
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，微信卡劵已被领完';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，您来晚了一步，微信卡劵已被领完';
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
            }else{
                if($km_num>=$num){
                    //echo "进入限购啦<br>";
                    for ($c=0; $c <$num; $c++) {
                        //echo "按熟练循环输出<br>";
                        $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                        //echo "pid<br>";
                        $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                        $item->km_num--;
                        $item->save();
                        $password =base64_encode($tid.','.$num_iid.','.$value.','.$bid.','.$num);
                        $url = 'http://jfb.dev.smfyun.com/kmi/jump/?psd='.$password;
                        $msg1 = '<a href="'.$url.'">领取卡劵</a>';
                        $msgs = str_replace("「%s」",$msg1,$km_text);
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['url'] = $url;
                            ////echo $tplmsg['url'].'<br>';
                            $tplmsg['data']['keyword1']['value'] = '微信卡劵';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '赶紧点此领取卡劵吧';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = $msgs;
                            $a=$this->we->sendCustomMessage($msg);
                        }
                    }
                }else{
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，微信卡劵已被领完';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您来晚了一步，微信卡劵已被领完';
                        $this->we->sendCustomMessage($msg);
                    }
                }
            }
        }
    }
    //有赞赠品
    public function action_gift($bid,$config,$num_iid,$tid,$name,$title,$type,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar){
        //$we = new Wechat($config);
        // $mem = Cache::instance('memcache');
        // $gettid = $mem->get($tid);
        // if($gettid==$tid){
        //     Kohana::$log->add("cxb$bid", print_r($tid, true));
        //     return; 
        // } 
        // $mem->set($tid, $tid, 1);
        $tid_num = ORM::factory('kmi_tid')->where('tid', '=', $tid)->where('num_iid','=',$num_iid)->count_all();
        if($tid_num==0 && $tid!=""){
            //echo $num_iid.'<br>';
            //echo '到这里来啦.<br>';
            $value=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $km_content=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
            $km_text = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
            $tpl=$config['tpl'];
            $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_tids`( `tid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`,`km_comtent`) VALUES ('$tid',$bid,$num_iid,'$pay_time','$type','$name','$title',$price,$num,'$openid',$weixin_user_id,'$nick','$avatar','gift','$km_content')");
            $trade1=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($trade1->id) return;
            $sql->execute();
            $km_num=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
            $limit=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
            //echo 'zzzzzz<br>';
            if ($limit > 0) {
                //echo 'xxxxxxx<br>';
                $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from kmi_tids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
                $result =$sql->execute()->as_array();
                $sum = $result[0]['sum'];
                //echo $sum.'<br>';
                //echo $limit.'<br>';
                if ($sum >= $limit){
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您的领取资格已到达上限';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您的领取资格已到达上限';
                        $this->we->sendCustomMessage($msg);
                    }
                }else{
                    if($km_num>=$num){
                        for ($c=0; $c < $num; $c++) {

                            //echo $km_num.'111111<br>';
                            //$yz['appid'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appid')->find()->value;
                            //echo $yz['appid'].'<br>';
                            //$yz['appsecret'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appsecret')->find()->value;
                            //echo $yz['appsecret'].'<br>';
                            $oid = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
                            //echo $oid.'<br>';
                            //$client = new KdtApiClient($yz['appid'],$yz['appsecret']);
                                // //echo '赠品列表:<br><br><br>';
                            $method = 'youzan.ump.presents.ongoing.all';
                            $params = [
                            ];
                            $results = $this->client->post($method, $this->methodVersion, $params, $files);
                            //var_dump($results);
                            for($h=0;$results['response']['presents'][$h];$h++){
                                $res = $results['response']['presents'][$h];
                                $present_id=$res['present_id'];
                                //echo 'present_id:'.$present_id.'<br>';
                                if($present_id==$oid){//找到指定赠品
                                    //echo "王旭文.<br>";
                                        //根据openid获取userid
                                    $method = 'youzan.users.weixin.follower.get';
                                    $params = [
                                        'weixin_openid'=>$openid,
                                        'fields'=>'user_id',
                                     ];
                                    $results = $this->client->post($method, $this->methodVersion, $params, $files);
                                    //var_dump($results);
                                    $user_id = $results['response']['user']['user_id'];
                                        ////echo 'user_id:'.$user_id;
                                        //根据openid发送奖品
                                    $method = 'youzan.ump.present.give';
                                    $params = [
                                        'activity_id'=>$oid,
                                        'fans_id'=>$user_id,
                                     ];
                                    $results = $this->client->post($method, $this->methodVersion, $params, $files);
                                    //echo "<pre>";
                                    //var_dump($results);
                                    //echo "<pre>";
                                    if($results['response']){
                                        if(isset($tpl)){
                                            $tplmsg['touser'] = $openid;
                                            $tplmsg['template_id'] = $tpl;
                                            $tplmsg['data']['keyword1']['value'] = '有赞赠品';
                                            $tplmsg['data']['keyword1']['color'] = '#999999';
                                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                                            $tplmsg['data']['remark']['value'] = $km_text;
                                            $tplmsg['data']['remark']['color'] = '#999999';
                                            $this->we->sendTemplateMessage($tplmsg);
                                            $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                            $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                            //echo 'vvvvvvvv<br>';
                                            //$tids->log=$results['response']['present_name'];
                                            $item->km_num--;
                                            $item->save();
                                            $tids->state=1;
                                            $tids->save();
                                        }else{
                                            $msg['msgtype'] = 'text';
                                            $msg['touser'] = $openid;
                                            $msg['text']['content'] = $km_text;
                                            $this->we->sendCustomMessage($msg);
                                            $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                            $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                            //echo 'vvvvvvvv<br>';
                                            //$tids->log=$results['response']['present_name'];
                                            $item->km_num--;
                                            $item->save();
                                            $tids->state=1;
                                            $tids->save();
                                        }
                                    }else if($results['error_response']){
                                        $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                        $tids->log=$results['error_response']['code'].$results['error_response']['msg'];
                                        $tids->save();
                                    }
                                }else{
                                    $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                    $tids->log='赠品id有错';
                                    $tids->save();
                                }
                            }
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，赠品已被领完';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，您来晚了一步，赠品已被领完';
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
            }else{
                //echo 'ccccccc<br>';
                if($km_num>$num){
                    //echo $km_num.'222222<br>';
                    //echo $num.'<br>';
                    for ($c=0; $c < $num; $c++) {
                        //echo $km_text.'<br>';
                        //echo 'for循环.<br>';
                        //$yz['appid'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appid')->find()->value;
                        //echo $yz['appid'].'<br>';
                        //$yz['appsecret'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appsecret')->find()->value;
                        //echo $yz['appsecret'].'<br>';
                        $oid = ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
                        //echo $oid.'<br>';
                        //$client = new KdtApiClient($yz['appid'],$yz['appsecret']);
                            // //echo '赠品列表:<br><br><br>';
                        $method = 'youzan.ump.presents.ongoing.all';
                        $params = [

                        ];
                        $results = $this->client->post($method, $this->methodVersion, $params, $files);
                        //var_dump($results);
                        for($h=0;$results['response']['presents'][$h];$h++){
                            $res = $results['response']['presents'][$h];
                            $present_id=$res['present_id'];
                            //echo 'present_id:'.$present_id.'<br>';
                            if($present_id==$oid){//找到指定赠品
                                //echo "王旭文.<br>";
                                    //根据openid获取userid
                                $method = 'youzan.users.weixin.follower.get';
                                $params = [
                                    'weixin_openid'=>$openid,
                                    'fields'=>'user_id',
                                 ];
                                $results = $this->client->post($method, $this->methodVersion, $params, $files);
                                //var_dump($results);
                                $user_id = $results['response']['user']['user_id'];
                                    ////echo 'user_id:'.$user_id;
                                    //根据openid发送奖品
                                $method = 'youzan.ump.present.give';
                                $params = [
                                    'activity_id'=>$oid,
                                    'fans_id'=>$user_id,
                                 ];
                                $results = $this->client->post($method, $this->methodVersion, $params, $files);
                                //echo "<pre>";
                                //var_dump($results);
                                //echo "<pre>";
                                if($results['response']){
                                    if(isset($tpl)){
                                        $tplmsg['touser'] = $openid;
                                        $tplmsg['template_id'] = $tpl;
                                        $tplmsg['data']['keyword1']['value'] = '有赞赠品';
                                        $tplmsg['data']['keyword1']['color'] = '#999999';
                                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                                        $tplmsg['data']['remark']['value'] = $km_text;
                                        $tplmsg['data']['remark']['color'] = '#999999';
                                        $this->we->sendTemplateMessage($tplmsg);
                                        $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                        $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                        $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                        //echo 'vvvvvvvv<br>';
                                        //$tids->log=$results['response']['present_name'];
                                        $item->km_num--;
                                        $item->save();
                                        $tids->state=1;
                                        $tids->save();
                                    }else{
                                        $msg['msgtype'] = 'text';
                                        $msg['touser'] = $openid;
                                        $msg['text']['content'] = $km_text;
                                        $this->we->sendCustomMessage($msg);
                                        $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                        $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                        $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                        //echo 'vvvvvvvv<br>';
                                        //$tids->log=$results['response']['present_name'];
                                        $item->km_num--;
                                        $item->save();
                                        $tids->state=1;
                                        $tids->save();
                                    }

                                }else if($results['error_response']){
                                    $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                    $tids->log=$results['error_response']['code'].$results['error_response']['msg'];
                                    $tids->save();
                                }
                            }else{
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $tids->log='赠品id有错';
                                $tids->save();
                            }
                        }
                    }
                }else{
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您来晚了一步，赠品已被领完';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您来晚了一步，赠品已被领完';
                        $this->we->sendCustomMessage($msg);
                    }
                }
            }
        }
    }
    //卡密
    public function action_km($bid,$config,$tid,$title,$name,$type,$num_iid,$price,$num,$openid,$weixin_user_id,$pay_time,$nick,$avatar){
        $mem = Cache::instance('memcache');
        $gettid = $mem->get($tid);
        Kohana::$log->add("tid$bid", print_r($tid, true));
        Kohana::$log->add("tid$bid", print_r($gettid, true));
        // if($gettid==$tid){
        //     Kohana::$log->add("cxbpass$tid", print_r($tid, true));
        //     return; 
        // }
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211); 
        Kohana::$log->add("cxbnopass$tid", print_r($tid, true));
        $mem->set($tid, $tid, 1);
        //$we = new Wechat($config);
        //echo '欢迎来到卡密<br>';
        $tid_num = ORM::factory('kmi_tid')->where('tid', '=', $tid)->where('num_iid','=',$num_iid)->count_all();
        if($tid_num==0 && $tid!=""){
            //$kmi_num = ORM::factory('kmi_km')->where('live','=', 1)->count_all();
            ////echo 'kmi_num'.$kmi_num.'<br>';
            //if($kmi_num>=$num){
            ////echo 'num'.$num.'<br>';
            //echo "订单为重复<br>";
            $value=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $km_content=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
            //echo $value.'<br>';
            $tpl=$config['tpl'];
            $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_tids`( `tid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`) VALUES ('$tid',$bid,$num_iid,'$pay_time','$type','$name','$title',$price,$num,'$openid',$weixin_user_id,'$nick','$avatar','kmi')");
            $trade1=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($trade1->id) return;
            $sql->execute();
            Kohana::$log->add("cxbcunru$tid", print_r('aaaaa', true));
            $sql = DB::query(Database::SELECT,"SELECT SUM(live) AS sum from kmi_kms where `live` = 1 and `startdate` = $value and `bid` =$bid");
            $result =$sql->execute()->as_array();
            $km_num = $result[0]['sum'];
            Kohana::$log->add("cxbcunru$tid", print_r($km_num, true));
            //echo $km_num.'<br>';
            $limit=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
            Kohana::$log->add("cxbcunru$tid", print_r($limit, true));
            if ($limit > 0) {
                //echo 'xxxxxxx<br>';
                $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from kmi_tids where `openid` = '$openid' and `num_iid` = $num_iid and `state` =1");
                $result =$sql->execute()->as_array();
                $sum = $result[0]['sum'];
                if ($sum >= $limit){
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您的领取资格已到达上限';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您的领取资格已到达上限';
                        $this->we->sendCustomMessage($msg);
                    }
                }else{
                    if($km_num>=$num){
                        for ($c=0; $c < $num; $c++) {
                            do {
                                $sql = DB::query(Database::SELECT,"SELECT * FROM kmi_kms where `live`=1 and `startdate`= $value  and `bid`= $bid");
                                $kmikm = $sql->execute()->as_array();
                                $keyname="kmi_id:{$bid}:{$kmikm[0]['id']}";
                                $m->add($keyname,$kmikm[0]['id'],5);
                            } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                            $password1=$kmikm[0]['password1'];
                            $password2=$kmikm[0]['password2'];
                            $password3=$kmikm[0]['password3'];
                            $id =$kmikm[0]['id'];
                            $sql = DB::query(Database::UPDATE,"UPDATE  `kmi_kms` set `live`=2 where `id`= $id");
                            $sql->execute();
                            $msgs =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
                            $msgs = str_replace("「%a」",$password1,$msgs);
                            $password = $password1;
                            if($password2){
                                $msgs = str_replace("「%b」",$password2,$msgs);
                                $password = $password.','.$password2;
                                if($password3){
                                    $msgs = str_replace("「%c」",$password3,$msgs);
                                    $password = $password.','.$password3;
                                }
                            }
                            if(isset($tpl)){
                                $tplmsg['touser'] = $openid;
                                $tplmsg['template_id'] = $tpl;
                                $tplmsg['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/kmi/kmpass/'.$id.'/'.$bid.'/'.$num_iid;
                                ////echo $tplmsg['url'].'<br>';
                                // $tplmsg['data']['work']['value'] = $msgs;
                                // $tplmsg['data']['work']['color'] = '#999999';
                                $tplmsg['data']['keyword1']['value'] = '卡密';
                                $tplmsg['data']['keyword1']['color'] = '#999999';
                                // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                                // $tplmsg['data']['keyword2']['color'] = '#999999';
                                $tplmsg['data']['remark']['value'] = $msgs;
                                $tplmsg['data']['remark']['color'] = '#999999';
                                $a=$this->we->sendTemplateMessage($tplmsg);
                            }else{
                                $msg['msgtype'] = 'text';
                                $msg['touser'] = $openid;
                                $msg['text']['content'] = $msgs;
                                $a=$this->we->sendCustomMessage($msg);
                            }
                            Kohana::$log->add('weixin:kmimsg', print_r($msg, true));//写入日志，可以删除
                            Kohana::$log->add('weixin:kmiesult', print_r($a, true));//写入日志，可以删除
                            if($a['errmsg']=='ok'){
                                $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                                $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $item->km_num--;
                                $item->save();
                                $tids->state=1;
                                if($tids->km_comtent){
                                    $tids->km_comtent=$tids->km_comtent.'<br>'.$password;
                                    Kohana::$log->add('cxb:kmiesult', print_r($tids->km_comtent.'<br>'.$password, true));
                                }else{
                                    $tids->km_comtent=$password;
                                    Kohana::$log->add('cxb:kmiesult1', print_r($password, true));
                                }
                                $tids->save();
                                $sql = DB::query(Database::UPDATE,"UPDATE  `kmi_kms` set `live`=0 where `id`= $id");
                                $sql->execute();
                            }else{
                                $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                if(isset($a['errmsg'])){
                                    $tids->log = $a['errmsg'];
                                }else{
                                    $tids->log = $a;
                                }
                                $tids->save();
                                $sql = DB::query(Database::UPDATE,"UPDATE  `kmi_kms` set `live`=1 where `id`= $id");
                                $sql->execute();
                            }
                        }
                    }else{
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['data']['keyword1']['value'] = '提醒';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = '对不起，卡密已发完';
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = '对不起，卡密已发完';
                            $this->we->sendCustomMessage($msg);
                        }

                    }
                }
            }else{
                if($km_num>=$num){
                    for ($c=0; $c < $num; $c++) {
                        do {
                            $sql = DB::query(Database::SELECT,"SELECT * FROM kmi_kms where `live`=1 and `startdate`= $value  and `bid`= $bid");
                            $kmikm = $sql->execute()->as_array();
                            $keyname="kmi_id:{$bid}:{$kmikm[0]['id']}";
                            $m->add($keyname,$kmikm[0]['id'],5);
                        } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                        $password1=$kmikm[0]['password1'];
                        //echo $password1.'<br>';
                        $password2=$kmikm[0]['password2'];
                        //echo $password2.'<br>';
                        $password3=$kmikm[0]['password3'];
                        //echo $password3.'<br>';
                        $id =$kmikm[0]['id'];
                        $sql = DB::query(Database::UPDATE,"UPDATE  `kmi_kms` set `live`=2 where `id`= $id");
                        $sql->execute();
                        $msgs =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
                        //echo $msgs.'第一个<br>';
                        $msgs = str_replace("「%a」",$password1,$msgs);
                        $password = $password1;
                        if($password2){
                            $msgs = str_replace("「%b」",$password2,$msgs);
                            $password =$password.','.$password2;
                            //echo $msgs.'第二个<br>';
                            if($password3){
                                $msgs = str_replace("「%c」",$password3,$msgs);
                                $password = $password.','.$password3;
                                //echo $msgs.'第三个<br>';
                            }
                        }
                        if(isset($tpl)){
                            $tplmsg['touser'] = $openid;
                            $tplmsg['template_id'] = $tpl;
                            $tplmsg['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/kmi/kmpass/'.$id.'/'.$bid.'/'.$num_iid;
                            ////echo $tplmsg['url'].'<br>';
                            $tplmsg['data']['keyword1']['value'] = '卡密';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                            // $tplmsg['data']['keyword2']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = $msgs;
                            $tplmsg['data']['remark']['color'] = '#999999';
                            Kohana::$log->add("cxbcunru$tid", print_r($tplmsg, true));
                            $a=$this->we->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = $msgs;
                            Kohana::$log->add("cxbcunru$tid", print_r($msg, true));
                            $a=$this->we->sendCustomMessage($msg);
                        }
                        Kohana::$log->add('weixin:kmimsg', print_r($msg, true));//写入日志，可以删除
                        Kohana::$log->add('weixin:kmiesult', print_r($a, true));//写入日志，可以删除
                        if($a['errmsg']=='ok'){
                            $pid=ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                            $item =ORM::factory('kmi_prize')->where('id','=',$pid)->find();
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $item->km_num--;
                            $item->save();
                            $tids->state=1;
                            if($tids->km_comtent){
                                $tids->km_comtent=$tids->km_comtent.'<br>'.$password;
                                Kohana::$log->add('cxb:kmiesult', print_r($tids->km_comtent.'<br>'.$password, true));//
                            }else{
                                $tids->km_comtent=$password;
                                Kohana::$log->add('cxb:kmiesult1', print_r($password, true));
                            }
                            $tids->save();
                            $sql = DB::query(Database::UPDATE,"UPDATE  `kmi_kms` set `live`=0 where `id`= $id");
                            $sql->execute();
                        }else{
                            $tids=ORM::factory('kmi_tid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            if(isset($a['errmsg'])){
                                $tids->log = $a['errmsg'];
                            }else{
                                $tids->log = $a;
                            }
                            $tids->save();
                            $sql = DB::query(Database::UPDATE,"UPDATE  `kmi_kms` set `live`=1 where `id`= $id");
                            $sql->execute();
                        }
                    }
                }else{
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        // $tplmsg['data']['keyword2']['value'] = date('Y-m-d H:i:s');
                        // $tplmsg['data']['keyword2']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，卡密已发完';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->we->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，卡密已发完';
                        $this->we->sendCustomMessage($msg);
                    }
                }
            }
        }
    }
    //微信卡劵中间跳转
    public function action_jump(){
        $password =$_GET['psd'];
        $aaac=base64_decode($password);
        //按逗号分离字符串
        $hello = explode(',',$aaac);
        $check = ORM::factory('kmi_tid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find()->state;
        $num = ORM::factory('kmi_tid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find()->num;
        $openid=ORM::factory('kmi_tid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find()->openid;
        If($check!=$num){
            $checks =ORM::factory('kmi_tid')->where('tid','=',$hello[0])->where('num_iid','=',$hello[1])->find();
            $checks->state++;
            $checks->save();
            $url = '/kmi/ticket/'.$hello[2].'/'.$hello[3];
            Request::instance()->redirect($url);
        }else{
            $config = ORM::factory('kmi_cfg')->getCfg($hello[3],1);
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $wx['appid'] = $config['appid'];
            $wx['appsecret'] = $config['appsecret'];
            $we = new Wechat($wx);
            $msg['msgtype'] = 'text';
            $msg['touser'] = $openid;
            $msg['text']['content'] = '您的微信卡劵以领取完毕，请勿重复领取';
            $we->sendCustomMessage($msg);
        }
    }
    //微信卡劵
    public function action_kmpass($id,$bid,$num_iid){
        $this->template = 'tpl/blank';
        self::before();
        $km_text =ORM::factory('kmi_item')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
        $password1 = ORM::factory('kmi_km')->where('id','=',$id)->find()->password1;
        $password2 = ORM::factory('kmi_km')->where('id','=',$id)->find()->password2;
        $password3 = ORM::factory('kmi_km')->where('id','=',$id)->find()->password3;
        $km_text = str_replace("「%a」",$password1,$km_text);
        $password = $password1;
        if($password2){
            $km_text = str_replace("「%b」",$password2,$km_text);
            $password = $password.','.$password2;
            if($password3){
                $km_text = str_replace("「%c」",$password3,$km_text);
                $password = $password.','.$password3;
            }
        }
        // echo $km_text;
        $view = "weixin/kmi/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('km_text', $km_text);
    }
    public function action_ticket($cardId,$bid) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/kmi/ticket";

        //echo $bid.'<br>';
        //echo $cardId.'<br>';
        //exit;
        $config=ORM::factory('kmi_cfg')->getCfg($bid,1);
        $wx['appid'] = $config['appid'];
        //echo $wx['appid'].'<br>';
        $wx['appsecret'] = $config['appsecret'];
        //echo $wx['appsecret'].'<br>';
        //exit;
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);

        $jsapi = $we->getJsSign($callback_url);
        $ticket = $we->getJsCardTicket();
        //Kohana::$log->add('$jsapi', print_r($jsapi["timestamp"], true));
        //Kohana::$log->add('$ticket', print_r($ticket, true));
        //Kohana::$log->add('$cardId', print_r($cardId, true));
        $sign = $we->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));
        //Kohana::$log->add('$sign', print_r($sign, true));
        //echo "aaaa.<br>";
        $this->template->content = View::factory($view)
            ->bind('cardId', $cardId)
            ->bind('jsapi', $jsapi)
            ->bind('ticket', $ticket)
            ->bind('sign', $sign);
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        //echo 'appsecret:'.$config['appsecret'].'<br>';
        $cert_file = DOCROOT."kmi/tmp/$bid/cert.{$config['appsecret']}.pem";
        //echo 'cert:'.$cert_file.'<br>';
        $key_file = DOCROOT."kmi/tmp/$bid/key.{$config['appsecret']}.pem";
        //echo 'key:'.$key_file.'<br>';
        //证书分布式异步更新
        $file_cert = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key', '=', 'fxb_file_cert')->find();
        $file_key = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key', '=', 'fxb_file_key')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }


        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_file);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $key_file);

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);

        $data = curl_exec($ch);

        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo curl_error($ch);
            curl_close($ch);
            return false;
        }

    }
}
