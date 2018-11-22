<?php
class Qwtmbborder{
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
        $this->config=ORM::factory('qwt_mbbcfg')->getCfg($bid,1);
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
            if(!$this->trade['order_info']['pay_time']) $this->trade['order_info']['pay_time']=date('Y-m-d H:i:s',time());
            $num_iid=$order['item_id'];
            $tid_num = ORM::factory('qwt_mbbtid')->where('tid', '=', $this->trade['order_info']['tid'])->where('oid','=',$order['oid'])->where('num_iid','=',$order['item_id'])->count_all();
            $tid=$this->trade['order_info']['tid'];
            $trade=$this->trade;
            $userinfo=$this->userinfo;
            $oid=$order['oid'];
            $pay_time=$trade['order_info']['pay_time'];
            $type=$trade['order_info']['type'];
            $name=$trade['address_info']['receiver_name'];
            $tel=$trade['address_info']['receiver_tel'];
            $openid=$userinfo['weixin_openid'];
            $payment=$order['payment'];
            $num=$order['num'];
            $title=$order['title'];
            $user_id=$userinfo['user_id'];
            $nick=$userinfo['nick'];
            $avatar=$userinfo['avatar'];
            if($tid_num==0 && $tid!=""){
                $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_mbbtids`( `tid`,`oid`, `bid`,`num_iid`,`time`,`type`,`name`,`tradename`, `price`, `num`, `openid`, `uid`,`nikename`,`headimageurl`,`residue`,`tel`) VALUES ('$tid',$oid,$bid,$num_iid,'$pay_time','$type','$name','$title',$payment,$num,'$openid',$user_id,'$nick','$avatar',$num,'$tel')");
                $sql->execute();
            }else{
                continue;
            }
        }
        $order=ORM::factory('qwt_mbborder')->where('bid','=',$bid)->where('tid','=',$tid)->find();
        $order->bid=$bid;
        $order->tid=$tid;
        $order->tel=$tel;
        $order->name=$name;
        $content=$config['content'];
        $order->content=$content;
        if(!$order->id||$order->state==0){
            $tpl=$config['mgtpl'];
            $url=$config['href'];
            $result=$this->sendtplmsg($openid,$tpl,$url,$title,$content);
            Kohana::$log->add("sendresult", print_r($result, true));
            if($result['errmsg']=='ok'){
                $order->state=1;
            }else{
                if(isset($result['errmsg'])){
                    $order->log = $result['errmsg'];
                }else{
                    $order->log = $result;
                }
            }
        }
        $order->save();
    }
    private function sendtplmsg($openid,$tpl,$url,$title,$content){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        if($url){
            $tplmsg['url'] = $url;
        }
        $content1=explode('\n', $content)[0];
        $content2=explode('\n', $content)[1];
        $content3=explode('\n', $content)[2];
        $content4=explode('\n', $content)[3];
        $nowtitle=$title."\n\n".$content1."\n".$content2."\n\n".$content3."\n";
        $tplmsg['data']['name']['value'] = $nowtitle;
        $tplmsg['data']['name']['color'] = '#ff0000';
        $tplmsg['data']['remark']['value'] = urlencode($content4);
        $tplmsg['data']['remark']['color'] = '#0000ff';
        Kohana::$log->add("$this->bid:tplmsg:$openid", print_r($tplmsg, true));
        Kohana::$log->add("qwt_rwb_tplmsg1",print_r(json_encode($tplmsg),true));
        Kohana::$log->add("qwt_rwb_tplmsg2",print_r(urldecode(json_encode($tplmsg)),true));
        // $result=$this->wx->sendTemplateMessage($tplmsg);
        $result=$this->wx->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        Kohana::$log->add('tplresult', print_r($result, true));
        return $result;
    }
}
