<?php
class SmfyQwt{
    public $methodVersion='3.0.0';
    public function getwxcards($bid) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
            $wx = new Wxoauth($bid,$options);
            $result=$wx->getCardIdList();
            $total_num=$result['total_num'];
            $num=floor($total_num/50)+1;
            $last_num=0;
            $a=0;
            for ($i=0; $i < $num; $i++) { 
                $result=$wx->getCardIdList($last_num,50);
                if($result['errmsg']=='ok'){
                    foreach ($result['card_id_list'] as $card_id) {
                        $wxcards[$a]['id']=$card_id;
                        $infos=$wx->getCardInfo($card_id);
                        // echo '<pre>';
                        // var_dump($infos);
                        if($infos['errmsg']=='ok'){
                            if($infos['card']['card_type']=='DISCOUNT'){
                                $base_info=$infos['card']['discount']['base_info'];
                            }elseif($infos['card']['card_type']=='GENERAL_COUPON'){
                                $base_info=$infos['card']['general_coupon']['base_info'];
                            }elseif($infos['card']['card_type']=='CASH'){
                                $base_info=$infos['card']['cash']['base_info'];
                            }elseif($infos['card']['card_type']=='MEMBER_CARD'){
                                $base_info=$infos['card']['member_card']['base_info'];
                            }elseif($infos['card']['card_type']=='GIFT'){
                                $base_info=$infos['card']['gift']['base_info'];
                            }elseif($infos['card']['card_type']=='GROUPON'){
                                $base_info=$infos['card']['groupon']['base_info'];
                            }
                            $title=$base_info['title'];
                            $wxcards[$a]['title']=$title;
                        }
                        $a++;
                    }
                }
                $last_num+=50;
            } 
            return $wxcards; 
        }else {
            return '没有';
        }
    }
    public function getyzcoupons($bid,$client) {
        $method = 'youzan.ump.coupons.unfinished.search';
        $params = [
            'fields'=>"group_id,title,coupon_type"
        ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        $yzcoupons=$results['response']['coupons'];
        Kohana::$log->add("$bid:getyzcoupon", print_r($yzcoupons, true));
        // echo "<pre>";
        // var_dump($results);
        // echo "</pre>";
        
        //$yzcoupons=DB::query(Database::SELECT,"SELECT title ,coupon_id as group_id from qwt_coupons where bid = $bid and state =1")->execute()->as_array();
        // echo "<pre>";
        // var_dump($yzcoupons);
        // echo "</pre>";
        // exit();
        return $yzcoupons;

    }
    public function getyzcoupons1($bid,$client) {
        $method = 'youzan.ump.coupons.unfinished.search';
        $params = [
            'fields'=>"group_id,title,coupon_type"
        ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        $yzcoupons=$results['response']['coupons'];
        Kohana::$log->add("$bid:getyzcoupon1", print_r($yzcoupons, true));
        // echo "<pre>";
        // var_dump($results);
        // echo "</pre>";
        return $yzcoupons;
    }
    public function getyzgifts($bid,$client) {
        $method = 'youzan.ump.presents.ongoing.all';
        $params = [
            'fields'=>"present_id,title"
        ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        // echo "<pre>";
        // var_dump($results);
        // echo "</pre>";
        $yzgifts=$results['response']['presents'];
        Kohana::$log->add("$bid:getyzgift", print_r($yzgifts, true));
        return $yzgifts;
    }
    public function sendhongbao($bid,$item,$config,$openid,$money){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $options['appid'] = $admin->appid;
        $wx = new Wxoauth($bid,$options);
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);
        Kohana::$log->add("mch_id",print_r($config['mchid'],true));
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        Kohana::$log->add("mch",print_r($mch_billno,true));
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $options['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        $data["total_num"] = 1; //总人数
        $data["act_name"] = "本次活动"; //活动名称
        $data["send_name"] = $admin->name; //红包发送者名称
        $data["wishing"] = $admin->name.'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        Kohana::$log->add("data1",print_r($data,true));

        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        Kohana::$log->add("data",print_r($data['sign'],true));
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 30, array(), $bid);//支付安全验证函数（核心函数）
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        Kohana::$log->add("$bid:hongbaoresult", print_r($result, true));
        return $result;//hash数组
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        $config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();
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
    public function sendtplmsg1($bid,$item,$custom,$tpl,$openid,$url,$first,$keyword1,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        if($url){
            $tplmsg['url'] = $url;
        }
        $first=explode('&',$first)[0];
        $keyword1=explode('&',$keyword1)[0];
        $remark=explode('&',$remark)[0];
        $tplmsg['data']['first']['value']=$first;
        $tplmsg['data']['first']['color'] = explode('&',$first)[1]?explode('&',$first)[1]:'#FF0000';
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = explode('&',$keyword1)[1]?explode('&',$keyword1)[1]:'#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = explode('&',$remark)[1]?explode('&',$remark)[1]:'#999999';
        Kohana::$log->add('tplmsg', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('tplresult', print_r($result, true));
        return $result;
    }
    public function sendtplmsg2($bid,$item,$custom,$tpl,$openid,$url,$first,$keyword1,$keyword2,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        if($url){
            $tplmsg['url'] = $url;
        }
        $first=explode('&',$first)[0];
        $keyword1=explode('&',$keyword1)[0];
        $keyword2=explode('&',$keyword2)[0];
        $remark=explode('&',$remark)[0];
        $tplmsg['data']['first']['value']=$first;
        $tplmsg['data']['first']['color'] = explode('&',$first)[1]?explode('&',$first)[1]:'#FF0000';
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = explode('&',$keyword1)[1]?explode('&',$keyword1)[1]:'#999999';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = explode('&',$keyword2)[1]?explode('&',$keyword2)[1]:'#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = explode('&',$remark)[1]?explode('&',$remark)[1]:'#999999';
        Kohana::$log->add('tplmsg', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('tplresult', print_r($result, true));
        return $result;
    }
    public function sendtplmsg3($bid,$item,$custom,$tpl,$openid,$url,$first,$keyword1,$keyword2,$keyword3,$remark){
        $tplmsg['template_id'] = $tpl;
        if($url){
            $tplmsg['url'] = $url;
        }
        $first=explode('&',$first)[0];
        $keyword1=explode('&',$keyword1)[0];
        $keyword2=explode('&',$keyword2)[0];
        $keyword3=explode('&',$keyword3)[0];
        $remark=explode('&',$remark)[0];
        $tplmsg['data']['first']['value']=$first;
        $tplmsg['data']['first']['color'] = explode('&',$first)[1]?explode('&',$first)[1]:'#FF0000';
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = explode('&',$keyword1)[1]?explode('&',$keyword1)[1]:'#999999';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = explode('&',$keyword2)[1]?explode('&',$keyword2)[1]:'#999999';
        $tplmsg['data']['keyword3']['value'] = $keyword3;
        $tplmsg['data']['keyword3']['color'] = explode('&',$keyword3)[1]?explode('&',$keyword3)[1]:'#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = explode('&',$remark)[1]?explode('&',$remark)[1]:'#999999';
        Kohana::$log->add('tplmsg', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('tplresult', print_r($result, true));
        return $result;
    }
    public function sendtplmsg4($bid,$item,$custom,$tpl,$openid,$url,$first,$keyword1,$keyword2,$keyword3,$keyword4,$remark){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        if($url){
            $tplmsg['url'] = $url;
        }
        $first=explode('&',$first)[0];
        $keyword1=explode('&',$keyword1)[0];
        $keyword2=explode('&',$keyword2)[0];
        $keyword3=explode('&',$keyword3)[0];
        $keyword4=explode('&',$keyword4)[0];
        $remark=explode('&',$remark)[0];
        $tplmsg['data']['first']['value']=$first;
        $tplmsg['data']['first']['color'] = explode('&',$first)[1]?explode('&',$first)[1]:'#FF0000';
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = explode('&',$keyword1)[1]?explode('&',$keyword1)[1]:'#999999';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = explode('&',$keyword2)[1]?explode('&',$keyword2)[1]:'#999999';
        $tplmsg['data']['keyword3']['value'] = $keyword3;
        $tplmsg['data']['keyword3']['color'] = explode('&',$keyword3)[1]?explode('&',$keyword3)[1]:'#999999';
        $tplmsg['data']['keyword4']['value'] = $keyword4;
        $tplmsg['data']['keyword4']['color'] = explode('&',$keyword4)[1]?explode('&',$keyword4)[1]:'#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = explode('&',$remark)[1]?explode('&',$remark)[1]:'#999999';
        Kohana::$log->add('tplmsg', print_r($tplmsg, true));
        $result=$this->wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('tplresult', print_r($result, true));
        return $result;
    }
    public  function wfbunfollow($bid,$openid){
        Kohana::$log->add('qwt_wfbunfollow:bid'.$bid , print_r($openid,true));
        $config=ORM::factory('qwt_wfbcfg')->getCfg($bid,1);
        $yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $qg=ORM::factory('qwt_wfbqgscore')->where('bid','=',$bid)->where('openid','=',$openid)->find()->openid;
        $userobj0=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        $userobj0->subscribe=0;
        $userobj0->save();
        if(!$qg){
            $qg=ORM::factory('qwt_wfbqgscore');
            $qg->bid=$bid;
            $qg->openid=$openid;
            $qg->save();
            $userobj0 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            if($userobj0->score>=$config['goal0']){
                $sid=$userobj0->scores->scoreOut($userobj0, 9,$config['goal0']);
                if($config['switch']==1){
                    $this->wfbrsync($bid,$userobj0->openid,$yzaccess_token,-$config['goal0'],$sid,'取消关注扣积分1');
                }
            }
            $userobj1 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $userobj0->fopenid)->find();
            if(!$userobj1->id) return;
            if($userobj1->score>=$config['goal']){
                $sid=$userobj1->scores->scoreOut($userobj1, 10,$config['goal']);
                if($config['switch']==1){
                    $this->wfbrsync($bid,$userobj1->openid,$yzaccess_token,-$config['goal'],$sid,'取消关注扣积分2');
                }
            }
            $userobj2 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $userobj1->fopenid)->find();
            if(!$userobj2->id) return;
            if($userobj2->score>=$config['goal2']){
                $sid=$userobj2->scores->scoreOut($userobj2, 10,$config['goal2']);
                if($config['switch']==1){
                    $this->wfbrsync($bid,$userobj2->openid,$yzaccess_token,-$config['goal2'],$sid,'取消关注扣积分3');
                }
            }
        }
    }
    public function wfbrsync($bid,$openid,$yzaccess_token,$chscore,$sid,$reason){
        Kohana::$log->add('qwt_wfbrsync1:bid'.$bid , print_r($openid,true));
        Kohana::$log->add('qwt_wfbrsync2:bid'.$bid , print_r($chscore,true));
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($yzaccess_token){
            $client = new YZTokenClient($yzaccess_token);
        }else{
            die('请在后台一键授权给有赞');
        }
        $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        $methodVersion = '3.0.0';
        $result=$client->post($method, $methodVersion, $params, $files);
        Kohana::$log->add('qwt_wfbrsync7:bid'.$bid , print_r($result,true));
        $fans_id= $result['response']['user']['user_id'];
        if(!$fans_id){
            Kohana::$log->add("qwtbid{$bid}openid{$openid}", print_r($result, true));
            return;
        }
        Kohana::$log->add('failscoreid8',print_r($qrcode->score,true));
        if($qrcode->yz_score==0){
            $method = 'youzan.crm.fans.points.get';
            $params =[
            'fans_id' => $fans_id,
            ];
            $methodVersion = '3.0.0';
            $results=$client->post($method, $methodVersion, $params, $files);
            Kohana::$log->add('qwt_wfbrsync6:bid'.$bid , print_r($results,true));
            $point = $results['response']['point'];
            $method = 'youzan.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $methodVersion = '3.0.0';
            if($qrcode->score>0){
                $a=$client->post($method, $methodVersion, $params, $files);
                Kohana::$log->add('qwt_wfbrsync3bid'.$bid.'openid'.$openid, print_r($a,true));
            }else{
                $qrcodescore0=1;
                Kohana::$log->add('qwt_wfbrsync3bidsocre0','1');
            }
            if($a['response']['is_success']=='true'||$qrcodescore0==1){
                $score_change=$point-$qrcode->score;
                if($point!=0){
                   $qrcode->scores->scoreIn($qrcode,12,$point); 
                }
                $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $qrcode->yz_score=1;
                $qrcode->save();
            }else{
                $failscore=ORM::factory('qwt_wfbfailscore')->where('bid','=',$bid)->where('sid','=',$sid)->find();
                $failscore->bid=$bid;
                $failscore->sid=$sid;
                $failscore->type=1;
                $failscore->qid=$qrcode->id;
                $failscore->log=$a['error_response']['code'].$a['error_response']['message'];
                $failscore->save();
                Kohana::$log->add('failscoreid5',print_r($failscore->id,true));

            }
            $qrcode=ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{
            // if($chscore>=0){
            //     $method = 'youzan.crm.customer.points.increase';
            //     $params =[
            //     'fans_id' => $fans_id,
            //     'points' => $chscore,
            //     ];
            //     $methodVersion = '3.0.0';
            //     $a=$client->post($method, $methodVersion, $params, $files);
            //     Kohana::$log->add('qwt_wfbrsync4bid'.$bid.'openid'.$openid, print_r($a,true));
            // }else{
            //     $method = 'youzan.crm.customer.points.decrease';
            //     $params =[
            //     'fans_id' => $fans_id,
            //     'points' => -$chscore,
            //     ];
            //     $methodVersion = '3.0.0';
            //     $a=$client->post($method, $methodVersion, $params, $files);
            //     Kohana::$log->add('qwt_wfbrsync5bid'.$bid.'openid'.$openid, print_r($a,true));
            // }
            $method = 'youzan.crm.customer.points.sync';
            $params =[
            'reason' => $reason,
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $methodVersion = '3.0.0';
            $a=$client->post($method, $methodVersion, $params, $files);
            Kohana::$log->add('result',print_r($a,true));
            if($a['response']['is_success']!='true'){
                $failscore=ORM::factory('qwt_wfbfailscore')->where('bid','=',$bid)->where('sid','=',$sid)->find();
                $failscore->bid=$bid;
                $failscore->sid=$sid;
                $failscore->type=2;
                $failscore->qid=$qrcode->id;
                $failscore->log=$a['error_response']['code'].$a['error_response']['message'];
                $failscore->save();
                Kohana::$log->add('failscoreid4',print_r($failscore->id,true));
            }
        }
    }
}
