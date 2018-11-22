<?php defined('SYSPATH') or die('No direct script access.');

class Controller_QwtYyb extends Controller_Base {
    public $template = 'tpl/blank';
    public $config;
    public $wx;
    public $client;
    public $cronnum=1;
    public $yzaccess_token;
    public $methodVersion='3.0.0';
    public $cdnurl = 'http://cdn.jfb.smfyun.com/qwtyyb/';
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
        $qrcode=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $item=ORM::factory('qwt_yybitem')->where('bid','=',$bid)->where('qid','=',$qrcode->id)->where('oid','=',$oid)->find();
        $item->bid=$bid;
        $item->qid=$qrcode->id;
        $item->oid=$oid;
        if(!$admin){
            $item->save();
        }
        $iid=$item->id;
        if($admin||$iid){
            if(!$admin&&$iid){
                $item=ORM::factory('qwt_yybitem')->where('id','=',$iid)->find();
                if($item->status==1){
                    die('您已经领取过奖品了');
                }
            }
        }else{
            die('不合法');
        }
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $this->yzaccess_token=$yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        if($yzaccess_token){
            $this->client=$client = new YZTokenClient($this->yzaccess_token);
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
        $view = "weixin/smfyun/yyb/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('km_text', $km_text);
    }
    public function action_yzgift(){
        $yzgift=$_GET['url'];
        $openid=$_GET['openid'];
        $bid=$_GET['bid'];
        $oid=$_GET['oid'];
        $admin=$_GET['admin'];
        $qrcode=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $item=ORM::factory('qwt_yybitem')->where('bid','=',$bid)->where('qid','=',$qrcode->id)->where('oid','=',$oid)->find();
        $item->bid=$bid;
        $item->qid=$qrcode->id;
        $item->oid=$oid;
        if(!$admin){
            $item->save();
        }
        $iid=$item->id;
        if($admin||$iid){
            if(!$admin&&$iid){
                $item=ORM::factory('qwt_yybitem')->where('id','=',$iid)->find();
                if($item->status==1){
                    die('您已经领取过奖品了');
                }
            }
        }else{
            die('不合法');
        }
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $this->yzaccess_token=$yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        if($yzaccess_token){
            $this->client=$client = new YZTokenClient($this->yzaccess_token);
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
        $qnum=ORM::factory('qwt_buy')->where('iid','=',10)->where('qrcron','=',0)->count_all();
        if($qnum==0) die('已经全部轮询完');
        $yybbuy=ORM::factory('qwt_buy')->where('iid','=',10)->where('qrcron','=',0)->order_by('qrtime','ASC')->find();
        $bid=$yybbuy->bid;
        echo 'bid:'.$bid."<br>";
        Kohana::$log->add("qwtyybqrcron",$bid);
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$admin->appid){
            $yybbuy->qrtime=time();
            $yybbuy->save();
            die('请先授权微信');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $admin->appid;
        $wx = new Wxoauth($bid,$options);
        $next_openid=$config['next_openid'];
        $result=$wx->getUserList($next_openid);
        $total=$result['total'];
        echo 'total:'.$total."<br>";
        if(!$total){
            $yybbuy->qrtime=time();
            $yybbuy->save(); 
            die ('拉取用户失败'); 
        } 
        $count=$result['count'];
        echo 'count:'.$count."<br>";
        $ok=ORM::factory('qwt_yybcfg')->setCfg($bid,'qr_total',$total);
        $ok=ORM::factory('qwt_yybcfg')->setCfg($bid,'qr_count',$count);
        if($count>0){
            $openids=$result['data']['openid'];
            foreach ($openids as $openid) {
                $values[] = "($bid, '{$openid}')";
            }
            $next_openid=$result['next_openid'];
            $ok=ORM::factory('qwt_yybcfg')->setCfg($bid,'next_openid',$next_openid);
        }elseif (isset($count)&&$count==0) {
            $yybbuy->qrtime=time();
            $yybbuy->qrcron=1;
            $yybbuy->save();
            die('拉取完毕该商户');
        }
        if($values){
            $SQL = 'INSERT IGNORE INTO qwt_yybqrcodes (`bid`,`openid`) VALUES '. join(',', $values);
            $colum = DB::query(NULL,$SQL)->execute();
        }
        // $ok=ORM::factory('qwt_yybcfg')->setCfg($bid,'qr_total',$total);
        // $ok=ORM::factory('qwt_yybcfg')->setCfg($bid,'qr_count',$count);
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
    }
    public function action_info(){
        set_time_limit(0);
        Kohana::$log->add("yybinfo{$bid}",'info');
        $config=ORM::factory('qwt_yybcfg')->getCfg(1,1);
        $next_qid=$config['next_qid'];
        if(!$next_qid) $next_qid=0;
        echo 'next_qid:'.$next_qid."<br>";
        $last_num=ORM::factory('qwt_yybqrcode')->where('id','>',$next_qid)->count_all();
        if($last_num<=0) die('用户信息更新完毕');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $admin->appid;
        $qrcodes=ORM::factory('qwt_yybqrcode')->where('id','>',$next_qid)->order_by('id','ASC')->limit(100)->find_all();
        $wx = new Wxoauth($bid,$options);
        foreach ($qrcodes as $qrcode) {
            $bid=$qrcode->bid;
            echo 'bid:'.$bid.'<br>';
            $wx = new Wxoauth($bid,$options);
            $qid=$qrcode->id;
            $openid=$qrcode->openid;
            echo 'openid:'.$openid.'<br>';
            if(!$qrcode->nickname){
                $userinfo=$wx->getUserInfo($openid);
                $qr=ORM::factory('qwt_yybqrcode')->where('id','=',$qid)->find();
                $nickname=$userinfo['nickname'];
                $sex=$userinfo['sex'];
                $headimgurl=$userinfo['headimgurl'];
                $qr->nickname=$nickname;
                $qr->sex=$sex;
                $qr->headimgurl=$headimgurl;
                $qr->save();
            }
        }
        $ok=ORM::factory('qwt_yybcfg')->setCfg(1,'next_qid',$qid);
        exit();
    }
    public function action_storefuop($pri){// 获取服务号openid和进行跳转
        $privacy=base64_decode($pri);
        $hello=explode('$',$privacy);
        $bid=$hello[0];
        $tag=$hello[1];
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $admin->appid;
        $wx = new Wxoauth($bid,$options);
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtyyb/getopenid/'.$pri;
        $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
        header("Location:$auth_url");
        exit;
        }
    public function action_getopenid($pri){
        $privacy=base64_decode($pri);
        //echo $privacy."<br>";
        $hello=explode('$',$privacy);
        $bid=$hello[0];
        $tag=$hello[1];
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $config=ORM::factory('qwt_yybcfg')->getCfg($bid,1);
        // require Kohana::find_file('vendor', 'weixin/wechat.class');
        // $wx = new Wechat($config);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $admin->appid;
        $wx = new Wxoauth($bid,$options);
        $token = $wx->sns_getOauthAccessToken();
        //$token = $wx->sns_getOauthAccessToken();
        $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
        //$userinfo = $wx->getUserInfo($token['openid']);
        $user=$wx->getUserInfo($userinfo['openid']);
        $openid=$userinfo['openid'];
        $nickname=$userinfo['nickname'];
        $sex=$userinfo['sex'];
        $headimgurl=$userinfo['headimgurl'];
        $result['subscribe']=$user['subscribe'];
        $result['2dimage'] = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'wx_qr_img')->find()->id;
        //echo $openid."<br>";
        $qr=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $qr->bid=$bid;
        $qr->openid=$openid;
        $qr->nickname=$nickname;
        $qr->sex=$sex;
        $qr->headimgurl=$headimgurl;
        $qr->save();
        $qrcode=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if($tag=='yulan'){
            $qrcode->admin=1;
            $result['text']='你已成功绑定管理员预览';
        }else{
            $appointment=ORM::factory('qwt_yybappointment')->where('id','=',$tag)->find();
            $record=ORM::factory('qwt_yybrecord')->where('bid','=',$bid)->where('aid','=',$tag)->where('qid','=',$qrcode->id)->find();
            if($record->id){
                $result['text']='你已经预约过'.$appointment->name.'啦，请勿重复预约';
            }else{
                $result['text']='您已经成功预约'.$appointment->name.'，将会第一时间收到店铺的促销、优惠信息，专享各种福利~';
                $record->bid=$bid;
                $record->aid=$tag;
                $record->qid=$qrcode->id;
                $record->save();
                $renum=ORM::factory('qwt_yybrecord')->where('bid','=',$bid)->where('aid','=',$appointment->id)->count_all();
                $appointment->renum=$renum;
                $appointment->save();
            }
        }
        $qrcode->save();
        $qid=ORM::factory('qwt_yybqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);
        $jsapi = $wx->getJsSign($callback_url);
        $ticket = $wx->getJsCardTicket();
        $sign = $wx->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));
        $this->template->content = View::factory('weixin/smfyun/yyb/getopenid')
            ->bind('jsapi', $jsapi)
            ->bind('ticket', $ticket)
            ->bind('sign', $sign)
            ->bind('pri',$pri)
            ->bind('result',$result);
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
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('result',print_r($result,'true'));
        return $result;
    }
     //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_$type";

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
