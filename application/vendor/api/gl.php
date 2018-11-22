<?php defined('SYSPATH') or die('No direct script access.');
class gl {
  public $access_token;
  public $txtReply;
  public $methodVersion = '3.0.0';
  public function __construct($keyword,$wx,$bid,$openid,$userinfo,$biz,$appid){
        $config = ORM::factory('qwt_glcfg')->getCfg($bid,1);
        $config['name'] = $biz->name;
        // Kohana::$log->add('qwt_gl:cfg.{$bid}', print_r($config,true));
        //获取昵称
        $this->access_token = ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if($this->access_token){
            require Kohana::find_file('vendor', 'youzan/YZTokenClient');
            $client = new YZTokenClient($this->access_token);

            $method = 'youzan.users.weixin.follower.get';
            $params = [
                'weixin_openid' => $openid
            ];
            $kdtresult = $client->post($method,$this->methodVersion,$params);
        }
        $nickname = $userinfo['nickname'];
        $user_id = $kdtresult['response']['user']['user_id'];
        $config['user'] = $kdtresult['response']['user'];

        if($keyword != $config['keyword']){
            exit;
        }
        if($config['times']>0)//单个用户每日盖楼次数上限(0表示不限次数)
        {
            $timestamp=strtotime(date("Y-m-d"));
            $user=ORM::factory('qwt_glusertime')->where('bid','=',$bid)->where('openid','=',$openid)->find();

            if(!$user->id)
            {
                $user->bid=$bid;
                $user->num=1;
                $user->openid=$openid;
                $user->save();
            }
             else
            {
                $lastupdate=$user->lastupdate;
                if($lastupdate<strtotime(date('Y-m-d')))//今天未盖楼
                {
                    $user->num=1;
                    $user->save();
                }
                else
                {
                    if(($user->num)>=$config['times'])
                    {
                         $res="亲~你今天的盖楼次数已经达到上限啦，请明天再来参与哦~";
                        // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $res);
                         $this->txtReply = $res;
                         return $this->txtReply;
                    }
                    else
                    {
                       $user->num=$user->num+1;
                       $user->save();
                    }

                 }
            }



        }
        // if($bid==6){
        //     $nowfloor = $this->getfloor1($keyword,$config,$bid,$openid);
        // }else{
        //     $nowfloor = $this->getfloor($keyword,$config,$bid,$openid);
        // }
        if(Model::factory('select_experience')->dopinion($bid,'gl')){
           $nowfloor = $this->getfloor1($keyword,$config,$bid,$openid);
           $temp='你现在是第'.$nowfloor."楼！";
           Kohana::$log->add("$bid$nickname", $nowfloor);
           Kohana::$log->add("$bid$openid", $nowfloor);
           $setfloor=ORM::factory('qwt_glfloor')->where('bid','=',$bid)->where('floor','=',$nowfloor)->find();
            if (!$setfloor->id) {
                $msg=$temp.$config['fword'];
                $tempresult='@'.$nickname.' ~ '.$msg;
                $this->txtReply = $tempresult;
                // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $tempresult);
                // echo $result2;

            }
            else{
                $setitem=ORM::factory('qwt_glitem')->where('id','=',$setfloor->iid)->find();
                $tempresult='@'.$nickname.' ~ '.$temp;
                $sendstatus=1;
                switch  ($setitem->type)
                 {
                    case 5:

                        $sendres=$this->Sendyouhuiquan($user_id,$setitem->code);
                        if($sendres==1)
                        {
                            $sendstatus='1';
                            $temp=$setitem->word;
                            $this->txtReply = $tempresult.$temp;
                            // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $tempresult.$temp);
                            // echo $result2;
                        }
                        else
                        {

                         // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $tempresult."优惠券发送失败".$sendres['msg']);
                         $sendstatus=$sendres;
                         Kohana::$log->add('qwt_gl:result2zengping.{$bid}', $sendres);
                         $this->txtReply = $tempresult."优惠券发送失败".$sendres;
                         // echo $result2;
                        }
                        break;
                    case 4:
                        //$temp2='获得红包一个点击领取！';
                        $temp2=$setitem->word;

                        // for($i=1;$sendres['result_code']!='SUCCESS'&&$i<=3;$i++){
                            $sendres=$this->sendmoney($config, $openid, $wx, $bid,$setitem->code,$appid);
                            if($sendres['result_code']=='SUCCESS')
                             {
                                $sendstatus='1';
                                Kohana::$log->add('qwt_gl:{$bid}:result2hongbao.{$bid}', '111');
                                $this->txtReply = $tempresult.$temp2;
                                // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$tempresult.$temp2);
                                // echo $result2;
                                break;
                             }
                            else
                            {
                             $sendstatus=$sendres['return_msg'];
                             // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$tempresult."\n红包发放失败".$sendres['return_msg'].$sendres['err_code']);
                             Kohana::$log->add('qwt_gl:{$bid}:result2hongbao.{$bid}', $sendres['return_msg']);
                             if($sendres['return_msg']=='') Kohana::$log->add('qwt_gl:{$bid}:result2hongbao.{$bid}', $sendres['err_code']);
                             $this->txtReply = $tempresult."\n红包发放失败".$sendres['return_msg'].$sendres['err_code'];
                             // echo $result2;
                            }
                        // }

                        break;
                    case 0:
                        $temp2=$setitem->word;
                        $sendstatus='1';
                        $orderflag='shiwu';
                        $this->txtReply = $tempresult.$temp2;
                        break;
                    case 6:
                        //$temp='获得有赞赠品，请点击领取';
                        $sendres=$this->Sendzengpin($user_id,$setitem->code);
                        if($sendres==1)
                        {
                          $sendstatus='1';
                          $temp=$setitem->word;
                          // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $tempresult.$temp);
                          // echo $result2;
                          $this->txtReply = $tempresult.$temp;
                        }
                        else
                        {
                         // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$tempresult."有赞赠品发送失败".$sendres['msg']);
                         $sendstatus=$sendres;
                         Kohana::$log->add('qwt_gl:{$bid}:result2zengping.{$bid}', $sendres);
                         // echo $result2;
                         $this->txtReply = $tempresult."有赞赠品发送失败".$sendres['msg'];
                        }
                        break;
                    default:
                        $temp='';
                       // $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $tempresult.$temp);
                       // echo $result2;
                       $this->txtReply = $tempresult.$temp;
                        break;
                    }

                $glorders=ORM::factory('qwt_glorder');
                // $glorders->bid = $bid;
                $glorders->bid = $bid;
                $glorders->openid=$openid;
                $glorders->floor=$nowfloor;
                $glorders->nickname=$nickname;
                $glorders->name=$setitem->name;
                $glorders->word=$setitem->word;
                $glorders->code=$setitem->code;
                $glorders->type=$setitem->type;
                $glorders->status=$sendstatus;
                $glorders->save();
                if($orderflag=='shiwu'){
                    $glorders->state=0;
                    $glorders->save();
                    $str_url = "<a href=\"http://".$_SERVER['HTTP_HOST']."/qwtgl/shiwu/".$setitem->id."?oid=".$glorders->id."\">点此填写地址领取奖品</a>";
                    //$url="http://".$_SERVER['HTTP_HOST']."/qwtgl/shiwu/".$glorders->id;
                    $this->txtReply = $this->txtReply.$str_url;
                }
            }
       }else{
            $this->txtReply='盖楼楼层已达到试用上限，请到官网续费';
       }
  }
  public function end(){
      return $this->txtReply;
  }
  public function getfloor($keyword,$config,$bid,$openid){
        $result['used']=0;
        //爬楼插件
        if ($keyword == $config['keyword']) {
            $mem = Cache::instance('memcache');
            $lou_key = "qwt_gl:{$bid}:$bid:gl_count";
            $lou_count = (int)$mem->get($lou_key);
            //$lou_count += rand(1, $config['gl']['step']);
            $lou_count +=1;
            $mem->set($lou_key, $lou_count, 0);
            //$result['lou_count']=$lou_count;
            return $lou_count;
        }
    }
    public function getfloor1($keyword,$config,$bid,$openid){
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        if ($keyword == $config['keyword']) {
            $lou_key = "qwt_gl:{$bid}:$bid:gl_count";
            // Kohana::$log->add('glsss', print_r($lou_key,true));
            do {
                $lou_count = $m->get($lou_key, null, $cas);
                // Kohana::$log->add('glsss', print_r($lou_count,true));
                // Kohana::$log->add('glsss', print_r($m->getResultCode(),true));
                // Kohana::$log->add('glsss', print_r(Memcached::RES_NOTFOUND,true));
                if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                    $lou_count=1;
                    $m->add($lou_key, $lou_count);
                } else {
                    $lou_count+=1;
                    $m->cas($cas, $lou_key, $lou_count);
                }
                //Kohana::$log->add('glsss', print_r(Memcached::RES_SUCCESS,true));
            } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            //Kohana::$log->add('glsss', print_r($lou_count,true));
            return $lou_count;
        }
    }
  public function  Sendzengpin($user_id,$oid){
        if(!$this->access_token){
            // require Kohana::find_file('vendor', 'kdt/YZSignClient');
            // $kdt = new YZSignClient($appid, $appsecret);
            // $method = 'youzan.ump.present.give';
            // $params = [
            //  'activity_id'=>$oid,
            //  'fans_id'=>$user_id,
            // ];
            // $results = $kdt->post($method,$this->methodVersion,$params);
            Kohana::$log->add('qwt_gl:{$bid}:zengping', '没授权');
        }else{
            // require Kohana::find_file('vendor', 'oauth/YZTokenClient');
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.present.give';
            $params = [
             'activity_id'=>$oid,
             'fans_id'=>$user_id,
            ];
            $results = $client->post($method,$this->methodVersion,$params);
            Kohana::$log->add('qwt_gl:{$bid}:zengping', '有授权');
        }



        if($results['response'])
          {
            return 1;
          }
        else
        {
            $fail['userid']=$user_id;
            $fail['oid']=$oid;
            $fail['yzappid'] =$appid ;
            $fail['yz_appsecret']=$appsecret;
            Kohana::$log->add('qwt_gl:{$bid}:gailouzengping参数', print_r($fail, true));
            Kohana::$log->add('qwt_gl:{$bid}:gailouzegnping', print_r($results, true));
            return $results['error_response'];
        }

    }

  public function  Sendyouhuiquan($user_id,$groupid){
        if(!$this->access_token){
            // require Kohana::find_file('vendor', 'kdt/YZSignClient');
            $kdt = new YZSignClient($appid, $appsecret);
            $method = 'youzan.ump.coupon.take';
            $params = [
                 'fans_id'=>$user_id,
                 "coupon_group_id"=>$groupid
            ];
            $results = $kdt->post($method,$this->methodVersion,$params);//
            // Kohana::$log->add('qwt_gl:{$bid}:youhuiquan1', print_r($results, true));
        }else{
            // require Kohana::find_file('vendor', 'oauth/YZTokenClient');
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupon.take';
            $params = [
                 'fans_id'=>$user_id,
                 "coupon_group_id"=>$groupid
            ];
            $results = $client->post($method,$this->methodVersion,$params);
            // Kohana::$log->add('qwt_gl:{$bid}:youhuiquan2', print_r($results, true));
        }

        // $results = $kdt->post($method,$params);

        if($results['response'])
          {
            Kohana::$log->add('qwt_gl:{$bid}:gailouyouhuiquan_response', print_r($results, true));
            return 1;
          }
        else
        {
            $fail['userid']=$user_id;
            $fail['coupon_group_id']=$coupon_group_id;
            $fail['yzappid'] =$appid ;
            $fail['yz_appsecret']=$appsecret;
            Kohana::$log->add('qwt_gl:{$bid}:gailouyouhuiquan参数', print_r($fail, true));
            Kohana::$log->add('qwt_gl:{$bid}:gailouyouhuiquan', print_r($results, true));
            return $results['error_response']['msg'];
        }

    }
  private function sendmoney($config, $openid, $wx='', $bid=1, $money=100,$appid){
        Kohana::$log->add('$gl','3333333' );
        $mch_billno = $config['mchid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
        $data["wxappid"] = $appid;

        $data["re_openid"] = $openid;
        $data["total_amount"] = $money;
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "[{$config['name']}]送红包"; //活动名称
        //$data["nick_name"] = $config['name']; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '运气太好啦！'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        Kohana::$log->add('$gl',print_r($data, true));
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));
        Kohana::$log->add('$gl',print_r($data, true));

        $postXml = $wx->xml_encode($data);
        Kohana::$log->add('glpostXml:',print_r($postXml, true));

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        // Kohana::$log->add('weixin:hongbao:fail:'.$config['name'], print_r($data, true));
        // Kohana::$log->add('weixin:hongbaopartnerkey:fail:'.$config['name'], $config['partnerkey']);
        if ($bid == 6) Kohana::$log->add('qwt_gl:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl_gl($url, $postXml, 5, array(), $bid);
        Kohana::$log->add('$qwt_resultXml:',print_r($resultXml,true));

        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        Kohana::$log->add('$qwt_response:',print_r($response,true));
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['mch_billno']=$mch_billno;
        Kohana::$log->add('$qwt_gl:',print_r($result, true) );
        return $result;
    }
  private function curl_post_ssl_gl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        //$config = $this->config;
        //$bid = $this->bid;
        $config=ORM::factory('qwt_glcfg')->getCfg($bid,1);
        //echo 'appsecret:'.$config['appsecret'].'<br>';
        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        Kohana::$log->add('gl_qwt_file_cert:',print_r($cert_file, true));
        //echo 'cert:'.$cert_file.'<br>';
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        Kohana::$log->add('gl_qwt_key_file:',print_r($key_file, true));
        //echo 'key:'.$key_file.'<br>';

        // $rootca_file = DOCROOT."hbb/tmp/$bid/rootca.pem";
        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();
        //$file_rootca = ORM::factory('qwt_hbbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbbfile_rootca')->find();

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
        Kohana::$log->add('curl_post_ssl:',print_r($data, true));
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            Kohana::$log->add('curl_post_ssl_error:',print_r($error, true));
            //echo curl_error($ch);
            curl_close($ch);
            return false;
        }
    }
}
