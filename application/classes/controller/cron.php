<?php defined('SYSPATH') or die('No direct script access.');
require_once DOCROOT.'../application/vendor/tx_msg/autoload.php';
use Qcloud\Sms\SmsSingleSender;
class Controller_Cron extends Controller {
    public $methodVersion='3.0.0';
    public function before()
    {
        parent::before();
        set_time_limit(0);
        //error_reporting(0);

        //if (!Kohana::$is_cli && IN_PRODUCTION) die('Run me at cmd line only!');
    }

    private function debug($msg)
    {
        echo trim($msg).PHP_EOL;
        @flush();
        @ob_flush();
    }

    public function action_index()
    {
        echo "Hello Cron!".PHP_EOL;
    }
    public function action_wait_refresh(){
        $orders = ORM::factory('qwt_waitorder')->order_by('id','asc')->limit(1000)->find_all();
        foreach ($orders as $k => $v) {
            # code...
            if($v->createtime+300<time()){//300s 5min 过期
                $item = ORM::factory($v->item_name)->where('id','=',$v->iid)->find();
                $item->stock++;
                $item->save();
                $v->delete();
            }
        }
        exit;
    }
    public function action_wsdqrcode($bid){
        $month = date("Y-m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        $qrcodes = ORM::factory('wsd_qrcode')->where('bid', '=', $bid)->where('lv','=',1)->order_by('lastupdate','ASC')->limit(5)->find_all();
        foreach ($qrcodes as $k=>$v) {
            // echo $v->id.'<br>';
            $groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
            $nawtime=time();
            $monthtype='%Y-%m';
            $typearry[0]=2;
            $typearry[1]=3;
            $score=ORM::factory('wsd_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$month)->where('type','IN',$typearry)->find();
            if($score->id){
              $flag=1;
            }else{
              $flag=0;
            }
            $month_tmoney=0;
            $month_pmoney=0;
            $monthjs_tmoney=0;
            $monthjs_pmoney=0;
            foreach ($groups as $group) {
                if($group->bottom){
                    $bottom1='('.$group->id.','.$group->bottom.')';
                }else{
                    $bottom1='('.$group->id.')';
                }
                $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney1 from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                $month_tmoney1=$month_tmoney1[0]['month_tmoney1'];
                $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from wsd_trades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                //echo  $month_tmoney.'<br>';
                $skujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                if(!$skujs->id){
                    $fskujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                    if(!$fskujs->id){
                       $scalejs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                    }else{
                        $scalejs=0;
                    }
                }else{
                    $scalejs=$skujs->scale;
                }
                $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                if(!$sku->id){
                    $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                    if(!$fsku->id){
                        $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                    }else{
                        $scale=0;
                    }
                }else{
                    $scale=$sku->scale;
                }
                $month_tmoney+=$month_tmoney1*$scale/100;//团队奖励
                $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;//可结算团队奖励
                $child_groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                $child_moneys=0;
                $childjs_moneys=0;
                foreach ($child_groups as $child_group) {
                    if($child_group->bottom){
                       $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                    }else{
                          $bottom2='('.$child_group->id.')';
                    }
                    $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from wsd_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from wsd_trades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                    $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                    $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>',$month_ltmoney)->find();
                    $skujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>',$monthjs_ltmoney)->find();
                    if(!$skujs->id){
                        $fskujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                        if(!$fskujs->id){
                            $scalejs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scalejs=0;
                        }
                    }else{
                        $scalejs=$skujs->scale;
                    }
                    if(!$sku->id){
                        $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_ltmoney)->find();
                        if(!$fsku->id){
                            $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scale=0;
                        }
                    }else{
                        $scale=$sku->scale;
                    }
                    $child_money= $month_ltmoney*$scale/100;
                    $child_moneys+=$child_money;
                    $childjs_money= $monthjs_ltmoney*$scalejs/100;
                    $childjs_moneys+=$childjs_money;
                }
                $month_pmoney+=$month_tmoney-$child_moneys;
                $monthjs_pmoney+=$monthjs_tmoney-$childjs_moneys;
            }
            $monthyjs_pmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthyjs_pmoney from wsd_scores where bid=$v->bid and qid = $v->id and type IN (2,3) and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
            $monthyjs_pmoney=$monthyjs_pmoney[0]['monthyjs_pmoney'];
            // echo $month.'<br>';
            $qrcode=ORM::factory('wsd_qrcode')->where('id','=',$v->id)->find();
            $qrcode->month_tmoney=$month_tmoney;
            $qrcode->monthjs_tmoney=$monthjs_tmoney;
            $qrcode->monthdjs_tmoney=$month_tmoney-$monthjs_tmoney;
            $qrcode->month_pmoney=$month_pmoney;
            $qrcode->monthjs_pmoney=$monthjs_pmoney;
            $qrcode->monthdjs_pmoney=$month_pmoney-$monthjs_pmoney;
            $qrcode->monthyjs_pmoney=-$monthyjs_pmoney;
            $qrcode->monthdate=$month;
            $qrcode->save();
        }
    }
    public function action_send_over_msg(){
        // require_once Kohana::find_file('vendor', 'aliyun-dysms-php-sdk-lite/demo/sendSms');
        $buys = ORM::factory('qwt_buy')->where('status','=',1)->where('expiretime','>',time())->find_all();
        $less3day = date("Y-m-d",strtotime("+3 day"));
        $less1day = date("Y-m-d",strtotime("+1 day"));
        $smsSign = "神码浮云";
        $ssender = new SmsSingleSender('1400090648', '7e8879bd06763bc00fe360f9c54ad078');
        foreach ($buys as $k => $v) {
            unset($res);
            $bid = $v->bid;
            $item = ORM::factory('qwt_item')->where('id','=',$v->iid)->find();
            $check = Model::factory('select_experience')->fenzai($bid,$item->alias);
            if($check == false){
                if($less1day == date('Y-m-d',$v->expiretime)){
                    $res = $v->bid.'您的'.$item->name.'试用版1天后即将到期，如需继续使用，请前往yingyong.smfyun.com订购正式版。';
                    $params = [$item->name,1];
                    $result = $ssender->sendWithParam("86", $v->login->user, 120176,
                        $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                    $rsp = json_decode($result);
                    // var_dump($rsp);
                }
            }else{
                if($less3day == date('Y-m-d',$v->expiretime)){
                    $res = $v->bid.'您的'.$item->name.'3天后即将到期，如需继续使用，请前往yingyong.smfyun.com续费。';
                    $params = [$item->name,3];
                    $result = $ssender->sendWithParam("86", $v->login->user, 120147,
                        $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                    $rsp = json_decode($result);
                    // var_dump($rsp);
                }
            }
        }
        exit;
    }
    public function bnktest($openid,$form_id,$keyword1){
        Kohana::$log->add("1bnk$openid", print_r($openid,true));
        Kohana::$log->add("2bnk$openid", print_r($form_id,true));
        Kohana::$log->add("3bnk$openid", print_r($keyword1,true));
        $wx['appid']='wxbc550991f98c2c7b';
        $wx['appsecret']='f5fb50132783f898ceb3aaef37c5bc2d';
        $template_id='6BmI_tPKfGj4NIQks-Lem-PqbvQ8ONZ1hKkCFD1jVmg';
        $openid=$openid;
        $form_id=$form_id;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we=new wechat($wx);
        $tplmsg['touser']=$openid;
        $tplmsg['template_id']=$template_id;
        $tplmsg['page']='pages/cash/index';
        $tplmsg['form_id']=$form_id;
        $tplmsg['data']['keyword1']['value']=$keyword1;
        $tplmsg['data']['keyword1']['color']='#173177';
        $tplmsg['data']['keyword2']['value']='红包未抢完';
        $tplmsg['data']['keyword2']['color']='#173177';
        $tplmsg['data']['keyword3']['value']='小程序账户余额';
        $tplmsg['data']['keyword3']['color']='#173177';
        $tplmsg['data']['keyword4']['value']='点击此处查看账户余额并进行提现';
        $tplmsg['data']['keyword4']['color']='#173177';
        Kohana::$log->add("bbnk$openid", print_r($tplmsg,true));
        $result=$we->sendXcxTpl($tplmsg);
        Kohana::$log->add("bbnk$openid", print_r($result,true));
        return $result;
    }
    public function action_bnk(){
        $time=time()-Date::DAY*1;
        $order_num=ORM::factory('bnk_order')->where('status','=',2)->where('createdtime','<=',$time)->count_all();
        if($order_num==0) die('没有需要改变状态的order');
        $orders=ORM::factory('bnk_order')->where('status','=',2)->where('createdtime','<=',$time)->find_all();
        foreach ($orders as $key => $order) {
            $order->used_fee=ceil($order->used_money*100*0.02)/100;
            $order->return_money=$order->all_money-$order->used_money-$order->used_fee;
            $order->status=3;
            $order->save();
            $order->qrcode->scores->scoreIn($order->qrcode,4,$order->return_money,$order->id);
            $result1=$this->bnktest($order->qrcode->openid,$order->form_id,$order->return_money.'元');
            Kohana::$log->add("bbnk$order->id", print_r($result1,true));
        }
    }
    public function action_qwt_dpm(){
        set_time_limit(0);
        $time=time();
        $dpm_nums = ORM::factory('qwt_buy')->where('iid','=',16)->where('switch','=',1)->where('status','=',1)->where('expiretime','>',$time)->where('dpm_order','=',1)->count_all();
        if($dpm_nums==0) die('没有需要拉取订单的商户');
        $dpm_buys=ORM::factory('qwt_buy')->where('iid','=',16)->where('switch','=',1)->where('status','=',1)->where('expiretime','>',$time)->where('dpm_order','=',1)->order_by('id','ASC')->find_all();
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        foreach ($dpm_buys as $key => $dpm_buy) {
            $bid=$dpm_buy->bid;
            $config=ORM::factory('qwt_yyxcfg')->getCfg($bid,1);
            $access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
            if($config['start_time']<=time()&&$access_token){
                $start_time=date('Y-m-d H:i:s',$config['start_time']);
                $end_time=date('Y-m-d H:i:s',$config['start_time']+Date::DAY*7);
                $this->action_qwt_order($bid,$access_token,$start_time,$end_time);
            }else{
                $dpm_buy->dpm_order=0;
                $dpm_buy->save();
            }
            ORM::factory('qwt_yyxcfg')->setCfg($bid,'start_time',$config['start_time']+Date::DAY*7);
        }
    }
    public function action_qwt_order($bid,$access_token,$start_created,$end_created){
        $client = new YZTokenClient($access_token);
        echo 'WAIT_BUYER_CONFIRM_GOODS<br>';
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>TRADE_BUYER_SIGNED,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                if($num_iid){
                    $item  = ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                    $method = 'kdt.item.get';
                    $params = [
                    'num_iid'=>$num_iid,
                    ];
                    $items = $client->post($method, '1.0.0', $params, $files);
                    if($items['response']['item']['num_iid']){
                        $item->bid=$bid;
                        $item->num_iid=$items['response']['item']['num_iid'];
                        $item->alias=$items['response']['item']['alias'];
                        $item->title=$items['response']['item']['title'];
                        $item->tag_ids=$items['response']['item']['tag_ids'];
                        $item->origin_price=$items['response']['item']['origin_price'];
                        $item->price=$items['response']['item']['price'];
                        $item->outer_id=$items['response']['item']['outer_id'];
                        $item->created=$items['response']['item']['created'];
                        $item->buy_quota=$items['response']['item']['buy_quota'];
                        $item->is_virtual=$items['response']['item']['is_virtual'];
                        $item->virtual_type=$items['response']['item']['virtual_type'];
                        $item->is_used=$items['response']['item']['is_used'];
                        $item->num=$items['response']['item']['num'];
                        $item->pic_url=$items['response']['item']['pic_thumb_url'];
                        $item->sold_num=$items['response']['item']['sold_num'];
                        $item->post_type=$items['response']['item']['post_type'];
                        $item->post_fee=$items['response']['item']['post_fee'];
                        $item->item_type=$items['response']['item']['item_type'];
                        $item->is_supplier_item=$items['response']['item']['is_supplier_item'];
                        $item->join_level_discount=$items['response']['item']['join_level_discount'];
                        $item->purchase_right=$items['response']['item']['purchase_right'];
                        $item->save();
                    }
                }
                $weixin_user_id =$res['fans_info']['fans_id'];
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $client->post($method, $this->methodVersion, $params, $files);
                    //Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
                    $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode->nick=$qrcodes['response']['user']['nick'];
                    $qrcode->avatar=$qrcodes['response']['user']['avatar'];
                    $qrcode->sex=$qrcodes['response']['user']['sex'];
                    $qrcode->area=$qrcodes['response']['user']['province'].','.$qrcodes['response']['user']['city'];
                    $qrcode->points=$qrcodes['response']['user']['points'];
                    $qrcode->traded_num=$qrcodes['response']['user']['traded_num'];
                    $qrcode->traded_money=$qrcodes['response']['user']['traded_money'];
                    $qrcode->level_info=json_encode($qrcodes['response']['user']['level_info']);
                    $qrcode->is_follow=$qrcodes['response']['user']['is_follow'];
                    $qrcode->save();
                }
                $tid=$res['tid'];
                $order  = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                $iid;
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $qid=ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type'] != 'PRESENT'){
                    echo $res['tid'].'<br>';
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->num= $res['num'];
                    $order->tid=$res['tid'];
                    $order->title=$res['title'];
                    $order->buyer_nick=$res['buyer_nick'];
                    $order->type=$res['type'];
                    $order->price=$res['price'];
                   $order->pic_path=$res['pic_path'];
                   $order->relation_type=$res['relation_type'];
                   $order->buyer_type=$res['buyer_type'];
                   $order->seller_flag=$res['seller_flag'];
                   $order->trade_memo=$res['trade_memo'];
                   $order->receiver_state=$res['receiver_state'];
                   $order->receiver_city=$res['receiver_city'];
                   $order->receiver_district=$res['receiver_district'];
                   $order->receiver_address=$res['receiver_address'];
                   $order->receiver_mobile=$res['receiver_mobile'];
                   $order->feedback=$res['feedback'];
                   $order->refund_state=$res['refund_state'];
                   $order->status=$res['status'];
                   $order->post_fee=$res['post_fee'];
                   $order->total_fee=$res['total_fee'];
                   $order->payment=$res['payment'];
                   $order->created=strtotime($res['created']);
                   $order->update_time=strtotime($res['update_time']);
                   $order->pay_type=$res['pay_type'];
                   $order->points_price=$res['points_price'];
                    $order->pay_time=strtotime($res['pay_time']);
                    $order->save();
                }
            }
        }
        echo 'WAIT_BUYER_CONFIRM_GOODS<br>';
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>WAIT_BUYER_CONFIRM_GOODS,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                if($num_iid){
                    $item  = ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                    $method = 'youzan.item.get';
                    $params = [
                    'num_iid'=>$num_iid,
                    ];
                    $items = $client->post($this->access_token,$method,$params);
                    if($items['response']['item']['num_iid']){
                        $item->bid=$bid;
                        $item->num_iid=$items['response']['item']['num_iid'];
                        $item->alias=$items['response']['item']['alias'];
                        $item->title=$items['response']['item']['title'];
                        $item->tag_ids=$items['response']['item']['tag_ids'];
                        $item->origin_price=$items['response']['item']['origin_price'];
                        $item->price=$items['response']['item']['price'];
                        $item->outer_id=$items['response']['item']['outer_id'];
                        $item->created=$items['response']['item']['created'];
                        $item->buy_quota=$items['response']['item']['buy_quota'];
                        $item->is_virtual=$items['response']['item']['is_virtual'];
                        $item->virtual_type=$items['response']['item']['virtual_type'];
                        $item->is_used=$items['response']['item']['is_used'];
                        $item->num=$items['response']['item']['num'];
                        $item->pic_url=$items['response']['item']['pic_thumb_url'];
                        $item->sold_num=$items['response']['item']['sold_num'];
                        $item->post_type=$items['response']['item']['post_type'];
                        $item->post_fee=$items['response']['item']['post_fee'];
                        $item->item_type=$items['response']['item']['item_type'];
                        $item->is_supplier_item=$items['response']['item']['is_supplier_item'];
                        $item->join_level_discount=$items['response']['item']['join_level_discount'];
                        $item->purchase_right=$items['response']['item']['purchase_right'];
                        $item->save();
                    }
                }
                $weixin_user_id =$res['fans_info']['fans_id'];
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $client->post($method, $this->methodVersion, $params, $files);
                    //Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
                    $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode->nick=$qrcodes['response']['user']['nick'];
                    $qrcode->avatar=$qrcodes['response']['user']['avatar'];
                    $qrcode->sex=$qrcodes['response']['user']['sex'];
                    $qrcode->area=$qrcodes['response']['user']['province'].','.$qrcodes['response']['user']['city'];
                    $qrcode->points=$qrcodes['response']['user']['points'];
                    $qrcode->traded_num=$qrcodes['response']['user']['traded_num'];
                    $qrcode->traded_money=$qrcodes['response']['user']['traded_money'];
                    $qrcode->level_info=json_encode($qrcodes['response']['user']['level_info']);
                    $qrcode->is_follow=$qrcodes['response']['user']['is_follow'];
                    $qrcode->save();
                }
                $tid=$res['tid'];
                $order  = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                $iid;
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $qid=ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type']!='PRESENT'){
                    echo $res['tid'].'<br>';
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->num= $res['num'];
                    $order->tid=$res['tid'];
                    $order->title=$res['title'];
                    $order->buyer_nick=$res['buyer_nick'];
                    $order->type=$res['type'];
                    $order->price=$res['price'];
                   $order->pic_path=$res['pic_path'];
                   $order->buyer_type=$res['buyer_type'];
                   $order->seller_flag=$res['seller_flag'];
                   $order->relation_type=$res['relation_type'];
                   $order->trade_memo=$res['trade_memo'];
                   $order->receiver_state=$res['receiver_state'];
                   $order->receiver_city=$res['receiver_city'];
                   $order->receiver_district=$res['receiver_district'];
                   $order->receiver_address=$res['receiver_address'];
                   $order->receiver_mobile=$res['receiver_mobile'];
                   $order->feedback=$res['feedback'];
                   $order->refund_state=$res['refund_state'];
                   $order->status=$res['status'];
                   $order->post_fee=$res['post_fee'];
                   $order->total_fee=$res['total_fee'];
                   $order->payment=$res['payment'];
                   $order->created=strtotime($res['created']);
                   $order->update_time=strtotime($res['update_time']);
                   $order->pay_type=$res['pay_type'];
                   $order->points_price=$res['points_price'];
                    $order->pay_time=strtotime($res['pay_time']);
                    $order->save();
                }
            }
        }
        echo 'WAIT_SELLER_SEND_GOODS<br>';
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             'status'=>WAIT_SELLER_SEND_GOODS,
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                $num_iid =$res['num_iid'];
                if($num_iid){
                    $item  = ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                    $method = 'kdt.item.get';
                    $params = [
                    'num_iid'=>$num_iid,
                    ];
                    $items = $client->post($method, '1.0.0', $params, $files);
                    if($items['response']['item']['num_iid']){
                        $item->bid=$bid;
                        $item->num_iid=$items['response']['item']['num_iid'];
                        $item->alias=$items['response']['item']['alias'];
                        $item->title=$items['response']['item']['title'];
                        $item->tag_ids=$items['response']['item']['tag_ids'];
                        $item->origin_price=$items['response']['item']['origin_price'];
                        $item->price=$items['response']['item']['price'];
                        $item->outer_id=$items['response']['item']['outer_id'];
                        $item->created=$items['response']['item']['created'];
                        $item->buy_quota=$items['response']['item']['buy_quota'];
                        $item->is_virtual=$items['response']['item']['is_virtual'];
                        $item->virtual_type=$items['response']['item']['virtual_type'];
                        $item->is_used=$items['response']['item']['is_used'];
                        $item->num=$items['response']['item']['num'];
                        $item->pic_url=$items['response']['item']['pic_thumb_url'];
                        $item->sold_num=$items['response']['item']['sold_num'];
                        $item->post_type=$items['response']['item']['post_type'];
                        $item->post_fee=$items['response']['item']['post_fee'];
                        $item->item_type=$items['response']['item']['item_type'];
                        $item->is_supplier_item=$items['response']['item']['is_supplier_item'];
                        $item->join_level_discount=$items['response']['item']['join_level_discount'];
                        $item->purchase_right=$items['response']['item']['purchase_right'];
                        $item->save();
                    }
                }
                $weixin_user_id =$res['fans_info']['fans_id'];
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                    'fans_id'=>$weixin_user_id,
                    ];
                    $qrcodes = $client->post($method, $this->methodVersion, $params, $files);
                    //Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                    $openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode  = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    $qrcode->bid=$bid;
                    $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
                    $qrcode->nick=$qrcodes['response']['user']['nick'];
                    $qrcode->avatar=$qrcodes['response']['user']['avatar'];
                    $qrcode->sex=$qrcodes['response']['user']['sex'];
                    $qrcode->area=$qrcodes['response']['user']['province'].','.$qrcodes['response']['user']['city'];
                    $qrcode->points=$qrcodes['response']['user']['points'];
                    $qrcode->traded_num=$qrcodes['response']['user']['traded_num'];
                    $qrcode->traded_money=$qrcodes['response']['user']['traded_money'];
                    $qrcode->level_info=json_encode($qrcodes['response']['user']['level_info']);
                    $qrcode->is_follow=$qrcodes['response']['user']['is_follow'];
                    $qrcode->save();
                }
                $tid=$res['tid'];
                $order  = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $iid=ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->id;
                $iid;
                if($weixin_user_id&&$res['fans_info']['fans_type']==1){
                    $qid=ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                    $order->qid=$qid;
                }
                if($res['type']!='PRESENT'){
                    echo $res['tid'].'<br>';
                    $order->bid=$bid;
                    $order->iid=$iid;
                    $order->num= $res['num'];
                    $order->tid=$res['tid'];
                    $order->title=$res['title'];
                    $order->buyer_nick=$res['buyer_nick'];
                    $order->type=$res['type'];
                    $order->price=$res['price'];
                   $order->pic_path=$res['pic_path'];
                   $order->buyer_type=$res['buyer_type'];
                   $order->seller_flag=$res['seller_flag'];
                   $order->trade_memo=$res['trade_memo'];
                   $order->relation_type=$res['relation_type'];
                   $order->receiver_state=$res['receiver_state'];
                   $order->receiver_city=$res['receiver_city'];
                   $order->receiver_district=$res['receiver_district'];
                   $order->receiver_address=$res['receiver_address'];
                   $order->receiver_mobile=$res['receiver_mobile'];
                   $order->feedback=$res['feedback'];
                   $order->refund_state=$res['refund_state'];
                   $order->status=$res['status'];
                   $order->post_fee=$res['post_fee'];
                   $order->total_fee=$res['total_fee'];
                   $order->payment=$res['payment'];
                   $order->created=strtotime($res['created']);
                   $order->update_time=strtotime($res['update_time']);
                   $order->pay_type=$res['pay_type'];
                   $order->points_price=$res['points_price'];
                    $order->pay_time=strtotime($res['pay_time']);
                    $order->save();
                }
            }
        }
    }
    public function action_generate_cron(){
        $users = ORM::factory('qwt_hbbcron')->where('state','=',0)->find_all();
        require Kohana::find_file("vendor/code","QwtCommonHelper");
        foreach ($users as $k => $v) {
            $buynum = $v->num;
            Helper::GenerateCode($v->time,$v->bid,$buynum);
            $v->state = 1;
            $v->save();
        }
        exit;
    }
    public function action_hby_cron_back(){
        set_time_limit(0);
        $hb_cron = ORM::factory('qwt_hbycron')->where('has_qr','=',0)->order_by('id','asc')->find();
        $bid = $hb_cron->bid;
        $time = date('ymd',time());
        $code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->limit(3000)->find_all();//5min 3000
        $count_code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->count_all();
        if($count_code>0){//有口令才生成
            require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
            $zipname = DOCROOT."qwt/hby/qr_code/$bid/code.zip";
            umask(0002);
            @mkdir(dirname($zipname),0777,true);
            $zip = new ZipArchive();
            $zip->open($zipname, ZIPARCHIVE::CREATE);
            foreach ($code as $k => $v) {
                //aes加密
                $privateKey = "sjdksldkwospaisk";
                $iv = "wsldnsjwisqweskl";
                $data = $v->code;
                $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                $hb_code = urlencode(base64_encode($encrypted));

                $qrurl[$k] =  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/hby/user_snsapi_base?hb_code='.$hb_code;
                $localfile = DOCROOT."qwt/hby/qr_code/$v->bid/".$time."_code/$v->code.png";
                umask(0002);
                @mkdir(dirname($localfile),0777,true);
                QRcode::png($qrurl[$k],$localfile,'L','6','2');
                $src_im = imagecreatefrompng($localfile);
                $im = imagecreatetruecolor(270, 300);
                $black = imagecolorallocate($im, 0, 0, 0);
                imagecopy($im,$src_im,0,0,0,0,270,300);
                $str = ['a','b','c','d','e','f','g','h','i','j','k'];//10
                $string = 'NO.'.$v->id.$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)];
                imagestring($im, 5, 20, 270, $string, $black);
                imagepng($im,$localfile);
                $zip->addFile($localfile, basename($localfile));
                $end_kl = $v->id;
            }
            $zip->close();
            $last = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$end_kl)->where('lastupdate','=',$hb_cron->time)->find();//5min 3000
            $hb_cron->loop = $hb_cron->loop+1;
            if($last->id){//还有没生成完的
                $hb_cron->end_id = $end_kl;
                $hb_cron->save();
            }else{//二维码已经生成完了
                $hb_cron->has_qr = 1;
                $hb_cron->code = file_get_contents($zipname);
                $hb_cron->save();
                @unlink($zipname);
            }
            //删除文件
            foreach ($code as $k => $v) {
                $localfile = DOCROOT."qwt/hby/qr_code/$bid/".$time."_code/$v->code.png";
                @unlink($localfile);
            }
        }else{
            die('异常');
        }
        exit;
    }
    public function action_hby_cron(){
        set_time_limit(0);
        $hb_cron = ORM::factory('qwt_hbycron')->where('has_qr','=',0)->order_by('id','asc')->find();
        if(!$hb_cron->id) die('没有需要生成的口令');
        $bid = $hb_cron->bid;
        $time = date('ymd',time());
        $code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->limit(10000)->find_all();//1min 1w
        $count_code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->count_all();
        if($count_code>0){//有口令才生成
            $name=$bid.'.'.$hb_cron->id.'.'.$hb_cron->num;
            $xlsname = DOCROOT."qwt/hby/qr_code/$bid/code.".$name.".xls";
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            //echo $xlsname.'<br>';
            if (!file_exists($xlsname)) {
                //echo '1<br>';
                umask(0002);
                @mkdir(dirname($xlsname),0777,true);
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
                $num=1;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, '链接')
                            ->setCellValue('B'.$num, '红包id');
                foreach ($code as $k => $v) {
                    $privateKey = "sjdksldkwospaisk";
                    $iv = "wsldnsjwisqweskl";
                    $data = $v->code;
                    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                    $hb_code = urlencode(base64_encode($encrypted));
                    $url=  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/hby/user_snsapi_base?hb_code='.$hb_code;
                    $num=$k+2;
                    $str = ['a','b','c','d','e','f','g','h','i','j','k'];//10
                    $string = 'NO.'.$v->id.$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)];
                    $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $url)
                            ->setCellValue('B'.$num, $string);
                    $end_kl = $v->id;
                }
                $objPHPExcel->getActiveSheet()->setTitle('User');
                $objPHPExcel->setActiveSheetIndex(0);
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save($xlsname);
            }else{
                //echo '2<br>';
                $reader = PHPExcel_IOFactory::createReader('Excel5');
                $PHPExcel = $reader->load($xlsname);
                $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                foreach ($code as $k => $v) {
                    $privateKey = "sjdksldkwospaisk";
                    $iv = "wsldnsjwisqweskl";
                    $data = $v->code;
                    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                    $hb_code = urlencode(base64_encode($encrypted));
                    $url=  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/hby/user_snsapi_base?hb_code='.$hb_code;
                    $clow=$k+$highestRow+1;
                    $str = ['a','b','c','d','e','f','g','h','i','j','k'];//10
                    $string = 'NO.'.$v->id.$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)];
                    $sheet->getCell('A'.$clow)->setValue($url);
                    $sheet->getCell('B'.$clow)->setValue($string);
                    $end_kl = $v->id;
                }
                $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
                $objWriter->save($xlsname);
            }
            $last = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$end_kl)->where('lastupdate','=',$hb_cron->time)->find();//5min 3000
            $hb_cron->loop = $hb_cron->loop+1;
            if($last->id){//还有没生成完的
                //echo 'lastid'.$last->id.'<br>';
                $hb_cron->end_id = $end_kl;
                $hb_cron->save();
            }else{//二维码已经生成完了
                //echo '生成完了';
                $hb_cron->has_qr = 1;
                $hb_cron->code = file_get_contents($xlsname);
                $hb_cron->save();
                @unlink($xlsname);
            }
        }else{
            die('异常');
        }
        exit;
    }
    // public function action_ywm_cron1(){
    //     set_time_limit(0);
    //     $hb_cron = ORM::factory('qwt_ywmcron')->where('has_qr','=',0)->order_by('id','asc')->find();
    //     $bid = $hb_cron->bid;
    //     $iid = $hb_cron->iid;
    //     $time = date('ymd',time());
    //     $code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->limit(3000)->find_all();//5min 3000
    //     $count_code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->count_all();
    //     if($count_code>0){//有口令才生成
    //         require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
    //         $zipname = DOCROOT."qwt/ywm/qr_code/$bid/$iid/code.zip";
    //         umask(0002);
    //         @mkdir(dirname($zipname),0777,true);
    //         $zip = new ZipArchive();
    //         $zip->open($zipname, ZIPARCHIVE::CREATE);
    //         foreach ($code as $k => $v) {
    //             //aes加密
    //             $privateKey = "sjdksldkwospaisk";
    //             $iv = "wsldnsjwisqweskl";
    //             $data = $v->code.'+-+'.$iid;
    //             $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
    //             $hb_code = urlencode(base64_encode($encrypted));

    //             $qrurl[$k] =  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/ywm/user_snsapi_base?hb_code='.$hb_code;
    //             $localfile = DOCROOT."qwt/ywm/qr_code/$v->bid/$iid/".$time."_code/$v->code.png";
    //             umask(0002);
    //             @mkdir(dirname($localfile),0777,true);
    //             QRcode::png($qrurl[$k],$localfile,'L','6','2');
    //             // $src_im = imagecreatefrompng($localfile);
    //             // $im = imagecreatetruecolor(270, 300);
    //             // $black = imagecolorallocate($im, 0, 0, 0);
    //             // imagecopy($im,$src_im,0,0,0,0,270,300);
    //             // $string = 'NO.'.$v->id;
    //             // imagestring($im, 5, 20, 270, $string, $black);
    //             // imagepng($im,$localfile);
    //             $zip->addFile($localfile, basename($localfile));
    //             $end_kl = $v->id;
    //         }
    //         $zip->close();
    //         $last = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('id','>',$end_kl)->where('lastupdate','=',$hb_cron->time)->find();//5min 3000
    //         $hb_cron->loop = $hb_cron->loop+1;
    //         if($last->id){//还有没生成完的
    //             $hb_cron->end_id = $end_kl;
    //             $hb_cron->save();
    //         }else{//二维码已经生成完了
    //             $hb_cron->has_qr = 1;
    //             $hb_cron->code = file_get_contents($zipname);
    //             $hb_cron->save();
    //             @unlink($zipname);
    //         }
    //         //删除文件
    //         foreach ($code as $k => $v) {
    //             $localfile = DOCROOT."qwt/ywm/qr_code/$bid/$iid/".$time."_code/$v->code.png";
    //             @unlink($localfile);
    //         }
    //     }else{
    //         die('异常');
    //     }
    //     exit;
    // }
    public function action_ywm_cron(){
        set_time_limit(0);
        $hb_cron = ORM::factory('qwt_ywmcron')->where('has_qr','=',0)->order_by('id','asc')->find();
        if(!$hb_cron->id) die('没有需要生成的口令');
        $bid = $hb_cron->bid;
        $iid = $hb_cron->iid;
        $item=ORM::factory('qwt_ywmsetgood')->where('id','=',$iid)->find();
        $time = date('ymd',time());
        $code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->limit(10000)->find_all();//5min 3000
        $count_code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->count_all();
        if($count_code>0){//有口令才生成
            $name=$item->title.$hb_cron->num;
            $xlsname = DOCROOT."qwt/ywm/qr_code/$bid/$iid/{$name}.xls";
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            //echo $xlsname.'<br>';
            if (!file_exists($xlsname)) {
                //echo '1<br>';
                umask(0002);
                @mkdir(dirname($xlsname),0777,true);
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
                $num=1;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, 'id')
                            ->setCellValue('B'.$num, '链接')
                            ->setCellValue('C'.$num, '口令');
                foreach ($code as $k => $v) {
                    $privateKey = "sjdksldkwospaisk";
                    $iv = "wsldnsjwisqweskl";
                    $data = $v->code.'+-+'.$iid;
                    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                    $hb_code = urlencode(base64_encode($encrypted));
                    $url=  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/ywm/user_snsapi_base?hb_code='.$hb_code;
                    $num=$k+2;
                    $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->id)
                            ->setCellValue('B'.$num, $url)
                            ->setCellValue('C'.$num,$v->code);
                    $end_kl = $v->id;
                }
                $objPHPExcel->getActiveSheet()->setTitle('User');
                $objPHPExcel->setActiveSheetIndex(0);
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save($xlsname);
            }else{
                //echo '2<br>';
                $reader = PHPExcel_IOFactory::createReader('Excel5');
                $PHPExcel = $reader->load($xlsname);
                $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                foreach ($code as $k => $v) {
                    $privateKey = "sjdksldkwospaisk";
                    $iv = "wsldnsjwisqweskl";
                    $data = $v->code.'+-+'.$iid;
                    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                    $hb_code = urlencode(base64_encode($encrypted));
                    $url=  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/ywm/user_snsapi_base?hb_code='.$hb_code;
                    $clow=$k+$highestRow+1;
                    $sheet->getCell('A'.$clow)->setValue($v->id);
                    $sheet->getCell('B'.$clow)->setValue($url);
                    $sheet->getCell('C'.$clow)->setValue($v->code);
                    $end_kl = $v->id;
                }
                $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
                $objWriter->save($xlsname);
            }
            $last = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('id','>',$end_kl)->where('lastupdate','=',$hb_cron->time)->find();//5min 3000
            $hb_cron->loop = $hb_cron->loop+1;
            if($last->id){//还有没生成完的
                //echo 'lastid'.$last->id.'<br>';
                $hb_cron->end_id = $end_kl;
                $hb_cron->save();
            }else{//二维码已经生成完了
                //echo '生成完了';
                $hb_cron->has_qr = 1;
                $hb_cron->code = file_get_contents($xlsname);
                $hb_cron->save();
                @unlink($xlsname);
            }
        }else{
            die('异常');
        }
        exit;
    }
    public function action_dka_tplcheck($bid){
        $bname=ORM::factory('dka_login')->where('id', '=', $bid)->find();
        // 过期时间存在并且 插件已过期或者搜不到该店铺  就死掉
        if((!$bname||strtotime($bname->expiretime)<=time())&&$bname->expiretime) die('不存在的bid或者已过期');
        echo 'bid:'.$bid;
        $usertime = date('H');//服务器时间

        $btime = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','start')->find()->value;
        $tplid = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','tplid')->find()->value;
        echo '商家时间：'.$btime.'<br>';
        if($btime==$usertime&&$tplid){//服务器时间和商家设定时间一直并且模板消息存在
            $ops = ORM::factory('dka_qrcode')->where('bid','=',$bid)->where('dka_join','!=',0)->find_all()->as_array();//找出此商家下的 加入了打卡的用户
            $config = ORM::factory('dka_cfg')->getCfg($bid,1);
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $we = new Wechat($config);
            foreach ($ops as $op) {
                $cksum = md5($op->openid.$config['appsecret'].date('Y-m-d'));
                $tplmsg['touser'] = $op->openid;
                $tplmsg['template_id'] = $tplid;
                $tplmsg['url'] = 'http://dkb.smfyun.com/dka/index/'. $bid.'?url=dka&cksum='. $cksum .'&openid='. base64_encode($op->openid);
                // echo $tplmsg['url'].'<br>';

                $tplmsg['data']['first']['value'] = '今天的打卡时间:'.date('Y-m-d H:i');
                $tplmsg['data']['first']['color'] = '#999999';

                $tplmsg['data']['work']['value'] = '亲，快来打卡哦~';
                $tplmsg['data']['work']['color'] = '#999999';

                $tplmsg['data']['remark']['value'] = '连续坚持打卡会有额外的积分奖励，兑换超值奖品~';
                $tplmsg['data']['remark']['color'] = '#999999';
                $result = $we->sendTemplateMessage($tplmsg);
                var_dump($result);
                Kohana::$log->add('dka:tplcheckopenid:{$bid}', $op->openid);//
            }
            exit;
            // echo $res;
        }else{
            echo '时间不一致或tplid不存在';
        }
        exit;
    }
    //刷新用户关系
    public function action_refresh($bid){//全分销刷新用户关系
        set_time_limit(0);
        $config = ORM::factory('qfx_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        $users = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('lv','=',1)->find_all();
        echo '<pre>';
        foreach ($users as $key => $u) {
            echo '分销商用户名：，'.$u->nickname.'分销商用户openid：'.$u->openid.'<br>';
            $childs = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('fopenid','=',$u->openid)->find_all();
            foreach ($childs as $k => $v) {
                $chuserinfo=array();
                if($v->subscribe==1){
                    $chuserinfo = $we->getUserInfo($v->openid);
                    if($chuserinfo['subscribe']==0){
                        $v->subscribe = 0;
                        $v->save();
                    }
                }
                echo '普通客户：，'.$v->nickname.'普通客户openid：'.$v->openid.'<br>';
            }
        }
        exit;
    }
    //刷新用户关系
    public function action_refresh_openid($bid,$openid){//全分销刷新用户关系
        set_time_limit(0);
        $config = ORM::factory('qfx_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        echo '<pre>';
        echo '分销商用户openid：'.$openid.'<br>';
        $childs = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->find_all();
        $num = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();
        // echo '<pre>';
        // $flag = 1;
        $data = array();
        $i = 0;
        $flag = $num-1;
        foreach ($childs as $k => $v) {
            $data['user_list'][$i]['openid'] = $v->openid;
            $data['user_list'][$i]['lang'] = "zh_CN";
            echo $childs[$k].'<br>';
            echo $k.'<br>';
            echo $i.'<br>';
            $i++;
            if(!$v->id) {
                break;
            }
            if(($k!=0&&$k%99==0)||$k==$flag) {
                var_dump($data);
                $result = $we->getUserInfo_list($data);
                var_dump($result);
                foreach ($result['user_info_list'] as $a => $b) {
                    $child_user = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('openid','=',$b['openid'])->find();
                    if($b['subscribe']===0){
                        echo '取消openid:'.$b['openid'];
                        $child_user->subscribe=0;
                        $child_user->save();
                    }
                    if($b['subscribe']==1&&$child_user->subscribe==0){
                        echo '恢复openid:'.$b['openid'];
                        $child_user->subscribe=1;
                        $child_user->save();
                    }
                }
                $i = 0;
                $data = array();
            }
        }
        exit;
    }
    public function action_wdy_tag(){
        set_time_limit(0);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");

        $users = ORM::factory('wdy_lab')->where('status','=',0)->limit(1000)->find_all();
        foreach ($users as $k => $v) {
            $access_token = ORM::factory('wdy_login')->where('id','=',$v->bid)->find()->access_token;
            if($access_token){
                $client = new YZTokenClient($access_token);
                $method = 'youzan.users.weixin.follower.tags.add';
                $params = [
                'tags' =>$v->lab_name,

                'weixin_openid' =>$v->qrcode->openid,
                ];
                $test=$client->post($method, '3.0.0', $params, $files);
            }
            $v->status = 1;
            $v->save();
        }
    }
    public function action_rwb_tag(){
        set_time_limit(0);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $users = ORM::factory('rwb_lab')->where('status','=',0)->limit(1000)->find_all();
        foreach ($users as $k => $v) {
            $access_token = ORM::factory('rwb_login')->where('id','=',$v->bid)->find()->access_token;
            if($access_token){
                $client = new YZTokenClient($access_token);
                $method = 'youzan.users.weixin.follower.tags.add';
                $params = [
                'tags' =>$v->lab_name,

                'weixin_openid' =>$v->qrcode->openid,
                ];
                $test=$client->post($method, '3.0.0', $params, $files);
            }
            $v->status = 1;
            $v->save();
        }
    }
    public function action_qwt_rwbtag(){
        set_time_limit(0);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $users = ORM::factory('qwt_rwblab')->where('status','=',0)->limit(1000)->find_all();
        foreach ($users as $k => $v) {
            // echo $v->id.'<br>';
            $access_token = ORM::factory('qwt_login')->where('id','=',$v->bid)->find()->yzaccess_token;
            if($access_token){
                $client = new YZTokenClient($access_token);
                $method = 'youzan.users.weixin.follower.tags.add';
                $params = [
                'tags' =>$v->lab_name,

                'weixin_openid' =>$v->qrcode->openid,
                ];
                $test=$client->post($method, '3.0.0', $params, $files);
                // var_dump($test);
            }
            $v->status = 1;
            $v->save();
        }
    }
    public function action_qwt_dkatag(){
        set_time_limit(0);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");

        $users = ORM::factory('qwt_dkalab')->where('status','=',0)->limit(1000)->find_all();
        foreach ($users as $k => $v) {
            $yzaccess_token = ORM::factory('qwt_login')->where('id','=',$v->bid)->find()->yzaccess_token;
            if($access_token){
                $client = new YZTokenClient($yzaccess_token);
                $method = 'youzan.users.weixin.follower.tags.add';
                $params = [
                'tags' =>$v->lab_name,

                'weixin_openid' =>$v->qrcode->openid,
                ];
                $test=$client->post($method, '3.0.0', $params, $files);
            }
            $v->status = 1;
            $v->save();
        }
    }
    public function action_wdy_onezero(){
        set_time_limit(0);
        Database::$default = "wdy";
        $zero = ORM::factory('wdy_zero')->where('status', '=', 0)->find();
        if(!$zero->id){
            exit;
        }
        $bid=$zero->bid;
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        // $qrcodes=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('score','!=',0)->limit(2000)->find_all();
        $qrcodes = DB::query(Database::SELECT,"select * from wdy_qrcodes where `bid` = $bid and `score` != 0 order by id desc limit 0,4000")->execute()->as_array();//32000
        // var_dump($qrcodes);
        // exit;
        foreach ($qrcodes as $v) {
            $config = ORM::factory('wdy_cfg')->getCfg($bid);
            if($config['switch']==1){
                $shop = ORM::factory('wdy_login')->where('id', '=', $bid)->find();
                $access_token=$shop->access_token;
                $client = new YZTokenClient($access_token);
                $weixin_openid=$v['openid'];

                $method='youzan.users.weixin.follower.get';
                $params=[
                    'weixin_openid'=>$weixin_openid,
                ];
                $results=$client->post($method, $this->methodVersion, $params, $files);
                $user_id = $results['response']['user']['user_id'];
                $method = 'youzan.crm.fans.points.get';
                $params =[
                'fans_id' => $user_id,
                ];
                $results=$client->post($method, $this->methodVersion, $params, $files);

                $point = $results['response']['point'];
                echo $point.'<br>';
                if($point!=0){
                    $method = 'youzan.crm.customer.points.decrease';
                    $params =[
                    'fans_id' => $user_id,
                    'kdt_id' => $shop->shopid,
                    'points' => $point,
                    ];
                    $a=$client->post($method, $this->methodVersion, $params, $files);
                    echo "<pre>";
                    var_dump($a);
                    if($a['error_response']){
                        Kohana::$log->add('wdy_onezero', print_r($a, true));
                    }
                    if($a['response']['is_success']==true){
                        $empty = ORM::factory('wdy_score')->where('bid', '=', $bid)->where('qid', '=', $v['id']);
                        $empty->delete_all();
                        $qrcode = ORM::factory('wdy_qrcode')->where('id', '=', $v['id'])->find();
                        $qrcode->score = 0;
                        $qrcode->yz_score = 0;
                        $qrcode->save();
                    }
                }else{
                    $empty = ORM::factory('wdy_score')->where('bid', '=', $bid)->where('qid', '=', $v['id']);
                    $empty->delete_all();
                    $qrcode = ORM::factory('wdy_qrcode')->where('id', '=', $v['id'])->find();
                    $qrcode->score = 0;
                    $qrcode->yz_score = 0;
                    $qrcode->save();
                }
            }else{
                $empty = ORM::factory('wdy_score')->where('bid', '=', $bid);
                $empty->delete_all();
                DB::update(ORM::factory('wdy_qrcode')->table_name())
                ->set(array('score' => '0','yz_score' =>'0'))
                ->where('bid', '=', $bid)
                ->execute();
            }

        }
        $num=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('score','!=',0)->count_all();
        if($num==0){
            $zero->status=1;
            $zero->save();
        }
    }
    public function action_qwt_wfbonezero(){
        set_time_limit(0);
        Database::$default = "wdy";
        $zero = ORM::factory('qwt_wfbzero')->where('status', '=', 0)->find();
        if(!$zero->id){
            exit;
        }
        $bid=$zero->bid;
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $qrcodes = DB::query(Database::SELECT,"SELECT * from qwt_wfbqrcodes where `bid` = $bid and `score` != 0 order by id desc limit 0,4000")->execute()->as_array();//32000
        // var_dump($qrcodes);
        // exit;
        foreach ($qrcodes as $v) {
            $config = ORM::factory('qwt_wfbcfg')->getCfg($bid);
            $shop = ORM::factory('qwt_login')->where('id', '=', $bid)->find();
            if($config['switch']==1&&$shop->yzaccess_token){
                $yzaccess_token=$shop->yzaccess_token;
                $client = new YZTokenClient($yzaccess_token);
                $weixin_openid=$v['openid'];

                $method='youzan.users.weixin.follower.get';
                $params=[
                    'weixin_openid'=>$weixin_openid,
                ];
                $results=$client->post($method, $this->methodVersion, $params, $files);
                $user_id = $results['response']['user']['user_id'];
                $method = 'youzan.crm.fans.points.get';
                $params =[
                'fans_id' => $user_id,
                ];
                $results=$client->post($method, $this->methodVersion, $params, $files);
                $point = $results['response']['point'];
                echo $point.'<br>';
                if($point!=0){
                    $method = 'youzan.crm.customer.points.decrease';
                    $params =[
                    'fans_id' => $user_id,
                    'kdt_id' => $shop->shopid,
                    'points' => $point,
                    ];
                    $a=$client->post($method, $this->methodVersion, $params, $files);
                    echo "<pre>";
                    var_dump($a);
                    if($a['error_response']){
                        Kohana::$log->add('qwt_onezero', print_r($a, true));
                    }
                    if($a['response']['is_success']==true){
                        $empty = ORM::factory('qwt_wfbscore')->where('bid', '=', $bid)->where('qid', '=', $v['id']);
                        $empty->delete_all();
                        $qrcode = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('id', '=', $v['id'])->find();
                        $qrcode->score = 0;
                        $qrcode->yz_score = 0;
                        $qrcode->save();
                    }
                }else{
                    $empty = ORM::factory('qwt_wfbscore')->where('bid', '=', $bid)->where('qid', '=', $v['id']);
                    $empty->delete_all();
                    $qrcode = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('id', '=', $v['id'])->find();
                    $qrcode->score = 0;
                    $qrcode->yz_score = 0;
                    $qrcode->save();
                }
            }else{
                $empty = ORM::factory('qwt_wfbscore')->where('bid', '=', $bid);
                $empty->delete_all();
                DB::update(ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->table_name())
                ->set(array('score' => '0','yz_score' =>'0'))
                ->where('bid', '=', $bid)
                ->execute();
            }

        }
        $num=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('score','!=',0)->count_all();
        if($num==0){
            $zero->status=1;
            $zero->save();
        }
    }
    public function action_oauthscript_wdy(){//积分宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "wdy";
        $shoparr = ORM::factory('wdy_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"41eeb7e302f34f799d",
                "client_secret"=>"6fd4c2a9c3ce4ab8f855e8a3a61d7a62",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_dld(){//dld 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "wdy";
        $shoparr = ORM::factory('dld_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"83f328eed03bcd7d49",
                "client_secret"=>"a4eb0f7c054c11e815c074e6f8328663",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_wsd(){//wsd 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "wdy";
        $shoparr = ORM::factory('wsd_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"c84f1b66c949fc9725",
                "client_secret"=>"adc84459c41905c00fdcb749b0139a04",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            Kohana::$log->add('wsd_yz_access_token:'.$shop->id, print_r($result, true));
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_dyb(){//订阅宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "dyb";
        $shoparr = ORM::factory('dyb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"49e609597c5d9c3969",
                "client_secret"=>"ab43c9f4110fb14afacf9d65b431968b",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_kmi(){//卡密 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "kmi";
        $shoparr = ORM::factory('kmi_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"b8da602aa7006efe50",
                "client_secret"=>"265c5e4a2b4af7f96b15e1df1f45c478",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_dka(){//打卡宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "dka";
        $shoparr = ORM::factory('dka_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"5e0eb86d157bf92bd1",
                "client_secret"=>"25a24f622c90dddf8ec8b12d29ec9303",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_fxb(){//订单宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "fxb";
        $shoparr = ORM::factory('fxb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"a96b6e270cdf71556c",
                "client_secret"=>"153236abe2942f15f3b5c5a2ba7c43f9",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_yyx(){//yyx 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "yyx";
        $shoparr = ORM::factory('yyx_shop')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"fdc4425fff26d518af",
                "client_secret"=>"75d7e0d4b2a2836c26e2edaf35faed9c",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->bid.'<br>店铺名称:'.$shop->name.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_ytb(){//ytb 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "ytb";
        $shoparr = ORM::factory('ytb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"14a5811e15cdc8802e",
                "client_secret"=>"da0ebf3b21046079d8d909a08457e5a6",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_qfx(){//全员分销 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "qfx";
        $shoparr = ORM::factory('qfx_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"234f0564ec0d951f34",
                "client_secret"=>"0260f2c5eb62fb486cab11fc4d4f9469",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_flb(){//福利宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "flb";
        $shoparr = ORM::factory('flb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"437ac1951309902ae4",
                "client_secret"=>"0e356d46e2391b3a1852aad203fa88b4",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_yty(){//云天佑 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "wdy";
        $shoparr = ORM::factory('yty_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"56cc2588c33f681d3a",
                "client_secret"=>"0f1e07171efc8fc731635a575320f4cf",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_smfyun(){//smfyun官网 店铺刷新
        set_time_limit(0);
        Database::$default = "wdy";
        require Kohana::find_file("vendor/youzan","YZOauthClient");
        $shop = ORM::factory('smfyun_token')->where('id','=',1)->find();
        $token = new YZOauthClient( '4c9de9bef8a67e2377' , '76205c185262737c355ee5d5df9a6dd1' );
        $keys = array();
        $type = 'token';//如要刷新access_token，这里的值为refresh_token
        $keys['refresh_token'] = $shop->refresh_token;//如要刷新access_token，这里为$keys['refresh_token']
        $result = $token->getToken( $type , $keys );
        echo $shop->id.'<br>';
        if($result['access_token']&&$result['refresh_token']){
            $shop->access_token = $result['access_token'];
            $shop->expires_in = time()+$result['expires_in'];
            $shop->refresh_token = $result['refresh_token'];
            $shop->save();
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 成功';
            echo '</pre>';
        }else{
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 失败';
            echo '</pre>';
        }
        die();
    }
    public function action_oauthscript_wzb(){//微直播 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "wdy";
        require Kohana::find_file("vendor/youzan","YZOauthClient");
        $shoparr = ORM::factory('wzb_login')->where('yz_access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $token = new YZOauthClient( '8b719902ac4d921bdb' , '95065db0a9dcef9bcf455d7a54af8615' );
            $keys = array();
            $type = 'token';//如要刷新access_token，这里的值为refresh_token
            $keys['refresh_token'] = $shop->yz_refresh_token;//如要刷新access_token，这里为$keys['refresh_token']
            // $keys['redirect_uri'] = 'http://'.$_SERVER["HTTP_HOST"].'/wzba/callback';
            $result = $token->getToken( $type , $keys );
            echo $shop->id.'<br>';
            if($result['access_token']&&$result['refresh_token']){
                $shop->yz_access_token = $result['access_token'];
                $shop->yz_expires_in = time()+$result['expires_in'];
                $shop->yz_refresh_token = $result['refresh_token'];
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_rwb(){//福利宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "rwb";
        $shoparr = ORM::factory('rwb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"bfedbd84029bdaf77f",
                "client_secret"=>"ff419e515885e8eb8afb7be243f14e8f",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    public function action_oauthscript_yyb(){//福利宝 商户 有赞授权 刷新 脚本  7天一次
        set_time_limit(0);
        Database::$default = "wdy";
        $shoparr = ORM::factory('yyb_login')->where('access_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"db138abb0214ec1d48",
                "client_secret"=>"1c7734762d70d1566023ed7d49738eae",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->access_token = $result->access_token;
                $shop->expires_in = time()+$result->expires_in;
                $shop->refresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    //全网通 红包发送状态脚本
    public function action_qwt_hb_cron($app){
        Database::$default = "qwt";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $senddates=ORM::factory('qwt_'.$app.'weixinsatu')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->qwt_hongbaocron($value->bid,$value->mch_billno);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();

            }
            echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
        }
        exit();
    }
    public function action_qwt_smfyun(){
        set_time_limit(0);
        Database::$default = "wdy";
        $shoparr = ORM::factory('qwt_login')->where('yzaccess_token','!=','')->find_all();
        foreach ($shoparr as $shop) {
            $url="https://open.youzan.com/oauth/token";
            $data=array(
                "client_id"=>"b8c8058d79f5cca370",
                "client_secret"=>"d8e2924a0d4f342ba1266800d38d4da0",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->yzrefresh_token
            );
            echo 'bid:'.$shop->id.'<br>';
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            if($result->access_token&&$result->refresh_token){
                $shop->yzaccess_token = $result->access_token;
                $shop->yzexpires_in = time()+$result->expires_in;
                $shop->yzrefresh_token = $result->refresh_token;
                $shop->save();
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 成功';
                echo '</pre>';
            }else{
                echo '<pre>';
                var_dump($result);
                echo '刷新 token 失败';
                echo '</pre>';
            }
        }
        die();
    }
    private function qwt_hongbaocron($bid,$mch_billno){

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $wx = new Wxoauth($bid);
        $config = $config=ORM::factory('qwt_cfg')->getCfg($bid,1);

        if (!$config['mchid']) die("$bid not found.\n");

        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));

        $postXml = $wx->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->hb_curl_post_ssl($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function hb_curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config=ORM::factory('qwt_cfg')->getCfg($bid,1);

        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";

        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";

        // $rootca_file = DOCROOT."hbb/tmp/$bid/rootca.pem";
        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();
        //$file_rootca = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        //if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
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
    //全网通 红包雨
    public function action_qwt_hby_cron(){
        Database::$default = "qwt";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $senddates=ORM::factory('qwt_hbyweixin')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->qwt_hbycron($value->bid,$value->mch_billno);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();
                    $buy = ORM::factory('qwt_login')->where('id','=',$value->bid)->find();
                    if($result['status'] == 'RFUND_ING'||$result['status'] == 'REFUND'){
                        $buy->hby_money = number_format($buy->hby_money+$value->money/100,2);
                        $biz = ORM::factory('qwt_hbyorder');
                        $biz->money = number_format($value->money/100,2);
                        $biz->bid = $value->bid;
                        $biz->wxid = $value->id;
                        $biz->left = $buy->hby_money;
                        $biz->state = 1;
                        $buy->save();
                        $biz->save();
                    }
            }
            echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
        }
        exit();
    }
    //全网通 红包雨
    public function action_qwt_ywm_cron(){
        Database::$default = "qwt";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $senddates=ORM::factory('qwt_ywmweixin')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->qwt_ywmcron($value->bid,$value->mch_billno);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();
                    $buy = ORM::factory('qwt_login')->where('id','=',$value->bid)->find();
                    if($result['status'] == 'RFUND_ING'||$result['status'] == 'REFUND'){
                        $buy->ywm_money = number_format($buy->ywm_money+$value->money/100,2);
                        $biz = ORM::factory('qwt_ywmorder');
                        $biz->money = number_format($value->money/100,2);
                        $biz->bid = $value->bid;
                        $biz->wxid = $value->id;
                        $biz->left = $buy->ywm_money;
                        $biz->state = 1;
                        $buy->save();
                        $biz->save();
                    }
            }
            echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
        }
        exit();
    }

    private function qwt_ywmcron($bid,$mch_billno){

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $wx = new Wxoauth($bid);

        $Appid = 'wx31d7e1641cdeaf00';
        $config['mchid'] = 1275904301;
        $config['apikey'] = 'r1IPFhzbD14cO4gRsJXC2fas9WexVadF';


        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = $Appid;
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));

        $postXml = $wx->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->hby_curl_post_ssl($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function qwt_hbycron($bid,$mch_billno){

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $wx = new Wxoauth($bid);

        $Appid = 'wx31d7e1641cdeaf00';
        $config['mchid'] = 1275904301;
        $config['apikey'] = 'r1IPFhzbD14cO4gRsJXC2fas9WexVadF';


        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = $Appid;
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));

        $postXml = $wx->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->hby_curl_post_ssl($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function hby_curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config=ORM::factory('qwt_cfg')->getCfg($bid,1);

        $cert_file = DOCROOT."qwt/hby/cert/cert.pem";
        $key_file = DOCROOT."qwt/hby/cert/key.pem";

        // $rootca_file = DOCROOT."hbb/tmp/$bid/rootca.pem";
        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_key')->find();
        //$file_rootca = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        //if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
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
    //全网通 红包发送状态脚本
    public function action_cron(){
        Database::$default = "hbb";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $senddates=ORM::factory('hbb_weixinsatu')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->hongbaocron($value->bid,$value->mch_billno);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();

            }
            echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
        }
        exit();
    }
    private function hongbaocron($bid,$mch_billno){

        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth($bid,'hbb','wx47384b8b7a68241e');
        $config = $config=ORM::factory('hbb_cfg')->getCfg($bid,1);

        if (!$config['mchid']) die("$bid not found.\n");

        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = ORM::factory('hbb_login')->where('id','=',$bid)->find()->appid;
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));

        $postXml = $wx->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config=ORM::factory('hbb_cfg')->getCfg($bid,1);

        $cert_file = DOCROOT."hbb/tmp/$bid/cert.pem";

        $key_file = DOCROOT."hbb/tmp/$bid/key.pem";

        // $rootca_file = DOCROOT."hbb/tmp/$bid/rootca.pem";
        //证书分布式异步更新
        $file_cert = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_cert')->find();
        $file_key = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_key')->find();
        //$file_rootca = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        //if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
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
    //wxp红包发送状态脚本
    public function action_wxp_cron(){
        Database::$default = "hbb";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $senddates=ORM::factory('wxp_weixinsatu')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->wxp_hongbaocron($value->bid,$value->mch_billno);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();

            }
            echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
        }
        exit();
    }
    private function wxp_hongbaocron($bid,$mch_billno){

        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth($bid,'hbb','wx47384b8b7a68241e');
        $config = $config=ORM::factory('wxp_cfg')->getCfg($bid,1);

        if (!$config['mchid']) die("$bid not found.\n");

        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = ORM::factory('wxp_login')->where('id','=',$bid)->find()->appid;
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));

        $postXml = $wx->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->wxp_curl_post_ssl($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function wxp_curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config=ORM::factory('wxp_cfg')->getCfg($bid,1);

        $cert_file = DOCROOT."wxp/tmp/$bid/cert.pem";

        $key_file = DOCROOT."wxp/tmp/$bid/key.pem";

        // $rootca_file = DOCROOT."hbb/tmp/$bid/rootca.pem";
        //证书分布式异步更新
        $file_cert = ORM::factory('wxp_cfg')->where('bid', '=', $bid)->where('key', '=', 'wxp_file_cert')->find();
        $file_key = ORM::factory('wxp_cfg')->where('bid', '=', $bid)->where('key', '=', 'wxp_file_key')->find();
        //$file_rootca = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        //if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
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
    public  function action_cron1(){
        Database::$default = "flb";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $details=ORM::factory('flb_detail')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($details as $value)
         {
            if($value->mch_billno){
               $result=$this->hongbaocron1($value->bid,$value->mch_billno);
               if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS"){
                    $value->status=$result['status'];
                    $value->save();
                }
                //echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
                Kohana::$log->add('flb_cron2', print_r($result, true));
            }

        }
        $hbs=ORM::factory('flb_hb')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($hbs as $value)
         {
            if($value->mch_billno){
               $result=$this->hongbaocron1($value->bid,$value->mch_billno);
               if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS"){
                    $value->status=$result['status'];
                    $value->save();
                }
                //echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
                Kohana::$log->add('flb_cron2', print_r($result, true));
            }

        }

        exit();
    }
    private function hongbaocron1($bid,$mch_billno){
        $config=ORM::factory('flb_cfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        if (!$config['partnerid']) die("$bid not found.\n");

        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = $config['appid'];
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $we->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->curl_post_ssl1($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function curl_post_ssl1($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        $config=ORM::factory('flb_cfg')->getCfg($bid,1);
        //$bid = $this->bid;
        //echo 'appsecret:'.$config['appsecret'].'<br>';
        $cert_file = DOCROOT."flb/tmp/$bid/cert.{$config['appsecret']}.pem";
        //echo 'cert:'.$cert_file.'<br>';
        $key_file = DOCROOT."flb/tmp/$bid/key.{$config['appsecret']}.pem";
        //echo 'key:'.$key_file.'<br>';
        //证书分布式异步更新
        $file_cert = ORM::factory('flb_cfg')->where('bid', '=', $bid)->where('key', '=', 'flb_file_cert')->find();
        $file_key = ORM::factory('flb_cfg')->where('bid', '=', $bid)->where('key', '=', 'flb_file_key')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        if (!file_exists($cert_file)) {
            umask(0002);
            @mkdir(dirname($cert_file),0777,true);
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            umask(0002);
            @mkdir(dirname($key_file),0777,true);
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
    public function action_cron2(){
        Database::$default = "whb";
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(0);
        $senddates=ORM::factory('whb_order')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(1000)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->hongbaocron2($value->bid,$value->tid);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();

            }
            echo $value->bid.'---mch_billno:'.$value->tid."---".$result['return_msg']."</br>";
        }
        exit();
    }

    private function hongbaocron2($bid,$mch_billno){
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth($bid,'whb','wx47384b8b7a68241e');
        $config = $config=ORM::factory('whb_cfg')->getCfg($bid,1);

        if (!$config['mchid']) die("$bid not found.\n");

        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] = ORM::factory('whb_login')->where('id','=',$bid)->find()->appid;
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));

        $postXml = $wx->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = $this->curl_post_ssl2($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }
    private function curl_post_ssl2($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config=ORM::factory('whb_cfg')->getCfg($bid,1);

        $cert_file = DOCROOT."whb/tmp/$bid/cert.pem";

        $key_file = DOCROOT."whb/tmp/$bid/key.pem";

        // $rootca_file = DOCROOT."hbb/tmp/$bid/rootca.pem";
        //证书分布式异步更新
        $file_cert = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_cert')->find();
        $file_key = ORM::factory('whb_cfg')->where('bid', '=', $bid)->where('key', '=', 'whb_file_key')->find();
        //$file_rootca = ORM::factory('hbb_cfg')->where('bid', '=', $bid)->where('key', '=', 'hbb_file_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        //if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
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
