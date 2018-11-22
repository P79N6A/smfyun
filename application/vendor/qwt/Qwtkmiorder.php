<?php
class Qwtkmiorder{
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
        $this->config=ORM::factory('qwt_kmicfg')->getCfg($bid,1);
        $this->client = new YZTokenClient($this->yzaccess_token);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
           $this->wx = new Wxoauth($this->bid,$options); 
        }
    }
    public function orderpush(){
        $bid=$this->bid;
        $config=$this->config;
        $posttid=urldecode($this->msg);
        $jsona=json_decode($posttid,true);
        $tid=$jsona['tid'];
        Kohana::$log->add("$bid:$tid", print_r($bid, true));
        $method = 'youzan.trade.get';
        $params = [
            // 'with_childs'=>true,
            'tid'=>$tid,
        ];
        $result = $this->client->post($method,'4.0.0', $params, $files);
        Kohana::$log->add("result", print_r($result, true));
        $this->trade=$result['response']['full_order_info'];
        $this->orders=$result['response']['full_order_info']['orders'];
        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'fans_id'=>$this->trade['buyer_info']['fans_id'],
        ];
        $result = $this->client->post($method, $this->methodVersion, $params, $files);
        $this->userinfo = $result['response']['user'];
        foreach ($this->orders as $order) {
            Kohana::$log->add("order", print_r($order, true));
            $trade_detail = ORM::factory('qwt_kmidetail')->where('bid','=',$bid)->where('tid','=',$this->trade['order_info']['tid'])->where('iid','=',$order['item_id'])->find();
            $trade_detail->bid = $bid;
            $trade_detail->tid = $this->trade['order_info']['tid'];
            $trade_detail->iid = $order['item_id'];
            $trade_detail->num = $order['num'];//件数
            $trade_detail->save();
            if(!$this->trade['order_info']['pay_time']) $this->trade['order_info']['pay_time']=date('Y-m-d H:i:s',time());
            $num_iid=$order['item_id'];
            Kohana::$log->add("num_iid", print_r($num_iid, true));
            $key = ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->key;
            Kohana::$log->add("key", print_r($key, true));
            if($key){
                $tid_num = ORM::factory('qwt_kmitid')->where('tid', '=', $this->trade['order_info']['tid'])->where('oid','=',$order['oid'])->where('num_iid','=',$order['item_id'])->count_all();
                $tid=$this->trade['order_info']['tid'];
                if($tid_num==0 && $tid!=""){
                    $km_content=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
                    $trade=$this->trade;
                    $userinfo=$this->userinfo;
                    $oid=$order['oid'];
                    $pay_time=$trade['order_info']['pay_time'];
                    $type=$trade['order_info']['type'];
                    $name=$trade['address_info']['receiver_name'];
                    $openid=$userinfo['weixin_openid'];
                    $payment=$order['payment'];
                    $num=$order['num'];
                    $title=$order['title'];
                    $user_id=$userinfo['user_id'];
                    $nick=$userinfo['nick'];
                    $avatar=$userinfo['avatar'];
                    $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_kmitids`( `tid`,`oid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`heardimageurl`,`km_type`,`residue`) VALUES ('$tid',$oid,$bid,$num_iid,'$pay_time','$type','$name','$title',$payment,$num,'$openid',$user_id,'$nick','$avatar','$key',$num)");
                    $sql->execute();
                }else{
                    continue;
                }
            }
            $value =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $startdate =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->startdate;
            $enddate =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->enddate;
            $tpl =$config['kmitpl'];
            if($pay_time){
                $nowdate=strtotime($pay_time);
            }else{
                $nowdate=time();
            }
            if($enddate){
                //echo $enddate."aaa1<br>";
                if($startdate && $startdate<$nowdate&&$nowdate<$enddate ){
                    //echo $key."aaa1<br>";
                    if($key=='kmi'){
                        //echo '即将进入卡密<br>';
                        $this->action_km($this->trade,$order,$this->userinfo);
                    }
                    if($key=='hongbao'){
                        $this->action_hongbao($this->trade,$order,$this->userinfo);
                    }
                    if($key=='coupon'){
                        //echo '即将进入卡劵<br>';
                        $this->action_coupon($this->trade,$order,$this->userinfo);
                    }
                    if($key=='gift'){
                        //echo '即将进入赠品<br>';
                        $this->action_gift($this->trade,$order,$this->userinfo);
                    }
                    if($key=='yzcoupon'){
                        //echo '即将进入有赞优惠券<br>';
                        $this->action_yzcoupon($this->trade,$order,$this->userinfo);
                    }
                    if($key=='freedom'){
                        //echo "即将进入自定义文本消息";
                        $this->action_freedom($this->trade,$order,$this->userinfo);
                    }
                }else{
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['data']['keyword1']['value'] = '提醒';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = '对不起，您的购买时间不在奖品有效期内，无法为您发送奖品';
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $this->wx->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = '对不起，您的购买时间不在奖品有效期内，无法为您发送奖品';
                        $this->wx->sendCustomMessage($msg);
                    }
                }
            }else{
                if($startdate && $startdate<$nowdate){
                    if($key=='kmi'){
                        //echo '即将进入卡密<br>';
                        $this->action_km($this->trade,$order,$this->userinfo);
                    }
                    if($key=='hongbao'){
                        $this->action_hongbao($this->trade,$order,$this->userinfo);
                    }
                    if($key=='coupon'){
                        //echo '即将进入卡劵<br>';
                        //Kohana::$log->add('$coupon', '11111');
                        $this->action_coupon($this->trade,$order,$this->userinfo);
                    }
                    if($key=='gift'){
                        //echo '发赠品啦<br>';
                        $this->action_gift($this->trade,$order,$this->userinfo);
                    }
                    if($key=='yzcoupon'){
                        //echo '即将进入有赞优惠券<br>';
                        $this->action_yzcoupon($this->trade,$order,$this->userinfo);
                    }
                    if($key=='freedom'){
                        //echo "即将进入自定义文本消息";
                        $this->action_freedom($this->trade,$order,$this->userinfo);
                    }
                }

            }
        }
    }
    public function action_yzcoupon($trade,$order,$userinfo){
        $tpl=$this->config['kmitpl'];
        $bid=$this->bid;
        $tid=$trade['order_info']['tid'];
        $num_iid=$order['item_id'];
        $num=$order['num'];
        $openid =$userinfo['weixin_openid'];
        $value=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
        $km_content=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
        $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
        $km_num=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
        $limit=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
        if ($limit > 0) {
            $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from qwt_kmitids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
            $result =$sql->execute()->as_array();
            $sum = $result[0]['sum'];
            if ($sum >= $limit){
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您的领取资格已到达上限');
                }else{
                    $this->sendmsg($openid,'对不起，您的领取资格已到达上限');
                }
            }else{
                if($km_num>=$num){
                    for ($c=0; $c <$num ; $c++) {
                        $oid = ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
                        $results = $this->sendyzcoupon($openid,$oid);
                        if($results['response']){
                            if(isset($tpl)){
                                $this->sendtplmsg($openid,$tpl,'有赞优惠卷',$km_text);
                            }else{
                                $this->sendmsg($openid,$km_text);  
                            }
                            $this->subtract($bid,$num_iid,$tid);
                        }else if($results['error_response']){
                            $logmsg=$results['error_response']['code'].$results['error_response']['msg'];
                            $this->fall($bid,$tid,$num_iid,$logmsg);
                        }
                    }
                }else{
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，有赞优惠券已被领完');
                    }else{
                        $this->sendmsg($openid,'对不起，您来晚了一步，有赞优惠券已被领完');
                    }
                }
            }
        }else{
            echo "aaa2<br>";
            if($km_num>=$num){
                for ($c=0; $c <$num; $c++) {
                    $oid = ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
                    $results = $this->sendyzcoupon($openid,$oid);
                    if($results['response']){
                        if(isset($tpl)){
                            $this->sendtplmsg($openid,$tpl,'有赞优惠卷',$km_text);
                        }else{
                            $this->sendmsg($openid,$km_text);
                        }
                        $this->subtract($bid,$num_iid,$tid);
                     }else if($results['error_response']){
                        $logmsg=$results['error_response']['code'].$results['error_response']['msg'];
                        $this->fall($bid,$tid,$num_iid,$logmsg); 
                    }
                }

            }else{
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，有赞优惠券已被领完');
                }else{
                    $this->sendmsg($openid,'对不起，您来晚了一步，有赞优惠券已被领完');
                }
            }
        }
    }
    //自定义文本消息
    public function action_freedom($trade,$order,$userinfo){
        $tid=$trade['order_info']['tid'];
        $bid=$this->bid;
        $num_iid=$order['item_id'];
        $openid=$userinfo['weixin_openid'];
        $num=$order['num'];
        $tpl=$this->config['kmitpl'];
        Kohana::$log->add("config", print_r($this->config,1));
        $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$this->bid)->where('num_iid','=',$order['item_id'])->find()->prize->km_text;
        if(isset($tpl)){
            Kohana::$log->add("tpl1", print_r($tpl,1));
            $result=$this->sendtplmsg($openid,$tpl,'文本消息',$km_text);
        }else{
            Kohana::$log->add("tpl2", print_r($tpl,1));
            $result=$this->sendmsg($openid,$km_text);
        }
        if($result['errmsg']=='ok'){
            $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
            $tids->state=1;
            $tids->save();
        }else{
            $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
            $tids->log=$result['errmsg'];
            $tids->save();
        }
    }
    //红包

    public function action_hongbao($trade,$order,$userinfo){
        $tpl=$this->config['kmitpl'];
        Kohana::$log->add("config", print_r($this->config,1));
        $bid=$this->bid;
        $tid=$trade['order_info']['tid'];
        $num_iid=$order['item_id'];
        $num=$order['num'];
        $openid =$userinfo['weixin_openid'];
        //判断库存
        $value=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
        $km_content=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
        $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
        $km_num=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
        $limit=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
        if ($limit > 0) {
            $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from qwt_kmitids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
            $result =$sql->execute()->as_array();
            $sum = $result[0]['sum'];
            if ($sum >= $limit){
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您的领取资格已到达上限');
                }else{
                    $this->sendmsg($openid,'对不起，您的领取资格已到达上限');
                }
            }else{
                if($km_num>0){
                    $tempmoney=ORM::factory("qwt_kmiitem")->where('bid','=',$bid)->where('num_iid','=',$order['item_id'])->find()->prize->value;
                    $money=$tempmoney*$order['num'];
                    $result=$this->smfy->sendhongbao($bid,'kmi',$this->config,$userinfo['weixin_openid'],$money);
                    if($result['result_code']=='SUCCESS'){
                        if(isset($tpl)){
                            Kohana::$log->add("tpl3", print_r($tpl,1));
                            $this->sendtplmsg($openid,$tpl,'红包',$km_text);
                        }else{
                            Kohana::$log->add("tpl4", print_r($tpl,1));
                            $this->sendmsg($openid,$km_text);
                        }
                        $this->subtract($bid,$num_iid,$tid);
                    }else{
                        $this->fall($bid,$num_iid,$tid,$result['return_msg']);
                    }
                }else{
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，红包已被领完');
                    }else{
                        $this->sendmsg($openid,'对不起，您来晚了一步，红包已被领完');
                    }
                }
            }
        }else{
            if($km_num>0){
                $tempmoney=ORM::factory("qwt_kmiitem")->where('bid','=',$bid)->where('num_iid','=',$order['item_id'])->find()->prize->value;
                $money=$tempmoney*$order['num'];
                $result=$this->smfy->sendhongbao($bid,'kmi',$this->config,$userinfo['weixin_openid'],$money);
                if($result['result_code']=='SUCCESS'){
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'红包',$km_text);
                    }else{
                        $this->sendmsg($openid,$km_text);
                    }
                    $this->subtract($bid,$num_iid,$tid);
                }elseif ($result['err_code']=='SEND_FAILED'){
                    $tempmoney=ORM::factory("qwt_kmiitem")->where('bid','=',$bid)->where('num_iid','=',$order['item_id'])->find()->prize->value;
                    $money=$tempmoney*$order['num'];
                    $result=$this->smfy->sendhongbao($bid,'kmi',$this->config,$userinfo['weixin_openid'],$money);
                    if($result['result_code']=='SUCCESS'){
                        if(isset($tpl)){
                            $this->sendtplmsg($openid,$tpl,'红包',$km_text);
                        }else{
                            $this->sendmsg($openid,$km_text);
                        }
                        $this->subtract($bid,$num_iid,$tid);
                    }else{
                        $this->fall($bid,$num_iid,$tid,$result['return_msg']);
                    }
                }else{
                    $this->fall($bid,$num_iid,$tid,$result['return_msg']);
                }
            }else{
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，红包已被领完');
                }else{
                    $this->sendmsg($openid,'对不起，您来晚了一步，红包已被领完');
                }
            }
        }
    }
    //微信卡劵
    public function action_coupon($trade,$order,$userinfo){
        $tpl=$this->config['kmitpl'];
        $bid=$this->bid;
        $tid=$trade['order_info']['tid'];
        $num_iid=$order['item_id'];
        $num=$order['num'];
        $openid =$userinfo['weixin_openid'];
        $value=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
        $km_content=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
        $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
        $km_num=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
        $limit=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
        if ($limit > 0) {
            $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from qwt_kmitids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
            $result =$sql->execute()->as_array();
            $sum = $result[0]['sum'];
            if ($sum >= $limit){
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您的领取资格已到达上限');
                }else{
                    $this->sendmsg($openid,'对不起，您的领取资格已到达上限');
                }
            }else{
                if($km_num>=$num){
                    for ($c=0; $c <$num ; $c++) {
                        $pid=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                        $item =ORM::factory('qwt_kmiprize')->where('id','=',$pid)->find();
                        $item->km_num--;
                        $item->save();
                        $password =base64_encode($tid.','.$num_iid.','.$value.','.$bid);
                        $url = 'http://'.$_SERVER ['HTTP_HOST'].'/qwtkmi/jump/?psd='.$password;
                        $msg1 = '<a href="'.$url.'">领取卡劵</a>';
                        $msgs = str_replace("「%s」",$msg1, $km_text);
                        if(isset($tpl)){
                            $this->sendcouponmsg($openid,$url,$tpl,'微信卡劵','赶紧点此领取卡劵吧');
                        }else{
                            $this->sendmsg($openid,$msgs);
                        }
                    }
                }else{
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，微信卡劵已被领完');
                    }else{
                        $this->sendmsg($openid,'对不起，您来晚了一步，微信卡劵已被领完');
                    }
                }
            }
        }else{
            if($km_num>=$num){
                for ($c=0; $c <$num; $c++) {
                    //echo "按熟练循环输出<br>";
                    $pid=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                    //echo "pid<br>";
                    $item =ORM::factory('qwt_kmiprize')->where('id','=',$pid)->find();
                    $item->km_num--;
                    $item->save();
                    $password =base64_encode($tid.','.$num_iid.','.$value.','.$bid.','.$num);
                    $url = 'http://'.$_SERVER ['HTTP_HOST'].'/qwtkmi/jump/?psd='.$password;
                    $msg1 = '<a href="'.$url.'">领取卡劵</a>';
                    $msgs = str_replace("「%s」",$msg1,$km_text);
                    if(isset($tpl)){
                        $this->sendcouponmsg($openid,$url,$tpl,'微信卡劵','赶紧点此领取卡劵吧');
                    }else{
                        $this->sendmsg($openid,$msgs);
                    }
                }
            }else{
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，微信卡劵已被领完');
                }else{
                    $this->sendmsg($openid,'对不起，您来晚了一步，微信卡劵已被领完');

                }
            }
        }
    }
    //有赞赠品
    public function action_gift($trade,$order,$userinfo){
        $tpl=$this->config['kmitpl'];
        $bid=$this->bid;
        $tid=$trade['order_info']['tid'];
        $num_iid=$order['item_id'];
        $num=$order['num'];
        $openid =$userinfo['weixin_openid'];
        $tpl=$this->config['kmitpl'];
            $value=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
            $km_content=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
            $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
            $km_num=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_num;
            $limit=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
            //echo 'zzzzzz<br>';
            if ($limit > 0) {
                //echo 'xxxxxxx<br>';
                $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from qwt_kmitids where `openid` = '$openid' and `num_iid` = $num_iid and `state` = 1");
                $result =$sql->execute()->as_array();
                $sum = $result[0]['sum'];
                //echo $sum.'<br>';
                //echo $limit.'<br>';
                if ($sum >= $limit){
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'提醒','对不起，您的领取资格已到达上限');
                    }else{
                        $this->sendmsg($openid,'对不起，您的领取资格已到达上限');
                    }
                }else{
                    if($km_num>=$num){
                        for ($c=0; $c < $num; $c++) {
                            $oid = ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
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
                                    $results=$this->sendyzgift($user_id,$oid);
                                    if($results['response']){
                                        if(isset($tpl)){
                                            $this->sendtplmsg($openid,$tpl,'有赞赠品',$km_text);
                                        }else{
                                            $this->sendmsg($openid,$km_text);
                                        }
                                        $this->subtract($bid,$num_iid,$tid);
                                    }else if($results['error_response']){
                                        $this->fall($bid,$num_iid,$tid,$results['error_response']);
                                    }
                                }
                                // else{

                                //     $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                //     $tids->log='赠品id有错';
                                //     $tids->save();
                                // }
                            }
                        }
                    }else{
                        if(isset($tpl)){
                            $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，赠品已被领完');
                        }else{
                            $this->sendmsg($openid,'对不起，您来晚了一步，赠品已被领完');
                        }
                    }
                }
            }else{
                if($km_num>$num){
                    for ($c=0; $c < $num; $c++) {
                        $oid = ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value; //? iid
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
                                $results=$this->sendyzgift($user_id,$oid);
                                if($results['response']){
                                    if(isset($tpl)){
                                        $this->sendtplmsg($openid,$tpl,'有赞赠品',$km_text);
                                    }else{
                                        $this->sendmsg($openid,$km_text);
                                    }
                                    $this->subtract($bid,$num_iid,$tid);
                                }else if($results['error_response']){
                                    $this->fall($bid,$num_iid,$tid,$results['error_response']);
                                }
                            }
                            // else{
                            //     $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            //     $tids->log='赠品id有错';
                            //     $tids->save();
                            // }
                        }
                    }

                }else{
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'提醒','对不起，您来晚了一步，赠品已被领完');
                    }else{
                        $this->sendmsg($openid,'对不起，您来晚了一步，赠品已被领完');
                    }
                }
        }
    }
    //卡密
    public function action_km($trade,$order,$userinfo){
        $tpl=$this->config['kmitpl'];
        $bid=$this->bid;
        $tid=$trade['order_info']['tid'];
        $num_iid=$order['item_id'];
        $num=$order['num'];
        $openid =$userinfo['weixin_openid'];
        $value=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
        $sql = DB::query(Database::SELECT,"SELECT SUM(live) AS sum from qwt_kmikms where `live` = 1 and `flag`=1 and `startdate` = $value and `bid` =$bid");
        $result =$sql->execute()->as_array();
        $km_num = $result[0]['sum'];
        //echo $km_num.'<br>';
        $km_content=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_content;
        $km_text =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
        $limit=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_limit;
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        if ($limit > 0) {
            $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from qwt_kmitids where `openid` = '$openid' and `num_iid` = $num_iid and `state` =1");
            $result =$sql->execute()->as_array();
            $sum = $result[0]['sum'];
            if ($sum >= $limit){
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，您的领取资格已到达上限');
                }else{
                    $this->sendmsg($openid,'对不起，您的领取资格已到达上限');
                }
            }else{
                if($km_num>=$num){
                    for ($c=0; $c < $num; $c++) {
                        do {
                            $sql = DB::query(Database::SELECT,"SELECT * FROM qwt_kmikms where `live`=1 and `flag`=1 and `startdate`= $value  and `bid`= $bid");
                            $kmikm = $sql->execute()->as_array();
                            $keyname="qkmi_id:{$bid}:{$kmikm[0]['id']}";
                            if(!$kmikm[0]['id']){
                                $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                                $tids->log = '库存不足';
                                $tids->save();
                                if(isset($tpl)){
                                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，卡密已发完');
                                }else{
                                    $this->sendmsg($openid,'对不起，卡密已发完');
                                }
                                return;
                            }
                            $m->add($keyname,$kmikm[0]['id'],5);
                        } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                        $password1=$kmikm[0]['password1'];
                        $password2=$kmikm[0]['password2'];
                        $password3=$kmikm[0]['password3'];
                        $id =$kmikm[0]['id'];
                        $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=2 where `id`= $id");
                        $sql->execute();
                        $msgs =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
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
                            $tplmsg['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwtkmi/kmpass/'.$id.'/'.$bid.'/'.$num_iid;
                            $tplmsg['data']['keyword1']['value'] = '卡密';
                            $tplmsg['data']['keyword1']['color'] = '#999999';
                            $tplmsg['data']['remark']['value'] = $msgs;
                            $tplmsg['data']['remark']['color'] = '#999999';
                            $a=$this->wx->sendTemplateMessage($tplmsg);
                        }else{
                            $msg['msgtype'] = 'text';
                            $msg['touser'] = $openid;
                            $msg['text']['content'] = $msgs;
                            $a=$this->wx->sendCustomMessage($msg);
                        }
                        Kohana::$log->add('weixin:kmimsg', print_r($msg, true));//写入日志，可以删除
                        Kohana::$log->add('weixin:kmiesult', print_r($a, true));//写入日志，可以删除
                        if($a['errmsg']=='ok'){
                            $pid=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                            $item =ORM::factory('qwt_kmiprize')->where('id','=',$pid)->find();
                            $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $item->km_num--;
                            $item->save();
                            $tids->pid=$pid;
                            $tids->kid=$id;
                            $tids->residue--;
                            $tids->state=1;
                            if($tids->km_comtent){
                                $tids->km_comtent=$tids->km_comtent.'<br>'.$password;
                                Kohana::$log->add('cxb:kmiesult', print_r($tids->km_comtent.'<br>'.$password, true));
                            }else{
                                $tids->km_comtent=$password;
                                Kohana::$log->add('cxb:kmiesult1', print_r($password, true));
                            }
                            $tids->save();
                            $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=0 where `id`= $id");
                            $sql->execute();
                        }else{
                            $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            if(isset($a['errmsg'])){
                                $tids->log = $a['errmsg'];
                            }else{
                                $tids->log = $a;
                            }
                            $tids->save();
                            $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=1 where `id`= $id");
                            $sql->execute();
                        }
                    }
                }else{
                    $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                    $tids->log = '库存不足';
                    $tids->save();
                    if(isset($tpl)){
                        $this->sendtplmsg($openid,$tpl,'提醒','对不起，卡密已发完');
                    }else{
                        $this->sendmsg($openid,'对不起，卡密已发完');
                    }

                }
            }
        }else{
            if($km_num>=$num){
                for ($c=0; $c < $num; $c++) {
                    do {
                        $sql = DB::query(Database::SELECT,"SELECT * FROM qwt_kmikms where `live`=1 and `flag`=1 and `startdate`= $value  and `bid`= $bid");
                        $kmikm = $sql->execute()->as_array();
                        $keyname="qkmi_id:{$bid}:{$kmikm[0]['id']}";
                        //Kohana::$log->add('weixinkmimsg111', print_r($keyname, true));
                        if(!$kmikm[0]['id']){
                            $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                            $tids->log = '库存不足';
                            $tids->save();
                            if(isset($tpl)){
                                $this->sendtplmsg($openid,$tpl,'提醒','对不起，卡密已发完');
                            }else{
                                $this->sendmsg($openid,'对不起，卡密已发完');
                            }
                            return;
                        }
                        $m->add($keyname, $kmikm[0]['id'],5);
                        // Kohana::$log->add('weixinkmimsg111', print_r($m->getResultCode(), true));
                        // Kohana::$log->add('weixinkmimsg111', print_r(Memcached::RES_SUCCESS, true));
                    } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                    // $sql = DB::query(Database::SELECT,"SELECT * FROM qwt_kmikms where `live`=1 and `startdate` = $value and `bid` = $bid");
                    // $kmikm = $sql->execute()->as_array();
                    $password1=$kmikm[0]['password1'];
                    //echo $password1.'<br>';
                    $password2=$kmikm[0]['password2'];
                    //echo $password2.'<br>';
                    $password3=$kmikm[0]['password3'];
                    //echo $password3.'<br>';
                    $id =$kmikm[0]['id'];
                    $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=2 where `id`= $id");
                    $sql->execute();
                    $msgs =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
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
                        $tplmsg['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwtkmi/kmpass/'.$id.'/'.$bid.'/'.$num_iid;
                        ////echo $tplmsg['url'].'<br>';
                        $tplmsg['data']['keyword1']['value'] = '卡密';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = $msgs;
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $a=$this->wx->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = $msgs;
                        $a=$this->wx->sendCustomMessage($msg);
                    }
                    Kohana::$log->add('weixin:kmimsg', print_r($msg, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:kmiesult', print_r($a, true));//写入日志，可以删除
                    if($a['errmsg']=='ok'){
                        $pid=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                        $item =ORM::factory('qwt_kmiprize')->where('id','=',$pid)->find();
                        $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                        $item->km_num--;
                        $item->save();
                        $tids->pid=$pid;
                        $tids->kid=$id;
                        $tids->residue--;
                        $tids->state=1;
                        if($tids->km_comtent){
                            $tids->km_comtent=$tids->km_comtent.'<br>'.$password;
                            Kohana::$log->add('cxb:kmiesult', print_r($tids->km_comtent.'<br>'.$password, true));//
                        }else{
                            $tids->km_comtent=$password;
                            Kohana::$log->add('cxb:kmiesult1', print_r($password, true));
                        }
                        $tids->save();
                        $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=0 where `id`= $id");
                        $sql->execute();
                    }else{
                        $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                        if(isset($a['errmsg'])){
                            $tids->log = $a['errmsg'];
                        }else{
                            $tids->log = $a;
                        }
                        $tids->save();
                        $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=1 where `id`= $id");
                        $sql->execute();
                    }
                }
            }else{
                $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
                $tids->log = '库存不足';
                $tids->save();
                if(isset($tpl)){
                    $this->sendtplmsg($openid,$tpl,'提醒','对不起，卡密已发完');
                }else{
                    $this->sendmsg($openid,'对不起，卡密已发完');
                }
            }
        }
    }
    public function sendyzcoupon($openid,$value){
        $method = 'youzan.ump.coupon.take';
        $params = [
            'coupon_group_id'=>$value,
            'weixin_openid'=>$openid,
         ];
        $results = $this->client->post($method, $this->methodVersion, $params, $files);
        return $results;
    }
    public function sendyzgift($user_id,$oid){
        $method = 'youzan.ump.present.give';
        $params = [
            'activity_id'=>$oid,
            'fans_id'=>$user_id,
         ];
        $results = $this->client->post($method, $this->methodVersion, $params, $files);
        return $results;
    }
    private function sendtplmsg($openid,$tpl,$keyword1,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = '#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';
        Kohana::$log->add('tplmsg', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('tplresult', print_r($result, true));
        return $result;
    }
    private function sendcouponmsg($openid,$url,$tpl,$keyword1,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        $tplmsg['url'] = $url;
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = '#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';
        Kohana::$log->add('couponmsgtpl', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('couponmsgresult', print_r($result, true));
        return $result;

    }
    private function sendmsg($openid,$content){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $content;
        Kohana::$log->add('sendmsg', print_r($msg, true));
        $result= $this->wx->sendCustomMessage($msg);
        Kohana::$log->add('msgresult', print_r($result, true));
        return $result;
    }
    public function subtract($bid,$num_iid,$tid){
        Kohana::$log->add('bid', print_r($bid, true));
        Kohana::$log->add('tid', print_r($tid, true));
        Kohana::$log->add('num_iid', print_r($num_iid, true));
        $pid=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
        $item =ORM::factory('qwt_kmiprize')->where('id','=',$pid)->find();
        $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
        $item->km_num--;
        $item->save();
        $tids->state=1;
        $tids->save();
    }
    public function fall($bid,$num_iid,$tid,$logmsg){
        Kohana::$log->add('bid', print_r($bid, true));
        Kohana::$log->add('tid', print_r($tid, true));
        Kohana::$log->add('num_iid', print_r($num_iid, true));
        Kohana::$log->add('logmsg', print_r($logmsg, true));
        $tids=ORM::factory('qwt_kmitid')->where('bid','=',$bid)->where('tid','=',$tid)->where('num_iid','=',$num_iid)->find();
        $tids->log=$logmsg;
        $tids->save();
    }
}
