<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_Hbb extends Controller_API {
    var $token = 'hongbao';
    var $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    var $appId = 'wx47384b8b7a68241e';
    var $appserect = '46fc1715ef1c0c1ffe1c6bd2579cf0b8';
    var $FromUserName;
    var $Keyword;
    var $access_token;
    var $nickname;
    var $baseurl = 'http://jfb.smfyun.com/hbb/';
    var $cdnurl = 'http://cdn.jfb.smfyun.com/hbb/';
    var $cdnurl2 = 'http://jfb.dev.smfyun.com/hbb/';
    var $scorename;
    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "hbb";
        if (!is_numeric($bid)) $bid = ORM::factory('hbb_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('hbb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by 1nnovator");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($Appid){
        $wxps = ORM::factory('wxp_login')->find_all();
        foreach ($wxps as $k => $v) {
            $hb_users[$k] = $v->appid;
        }
        if(in_array($Appid, $hb_users)){//神码浮云技术部和唯新食品网
            Database::$default = "hbb";
            Kohana::$log->add('$hbb','aaaaaaaaaa');
            Kohana::$log->add('$hbb', print_r($Appid, true));
            // a
            $postStr = file_get_contents("php://input");
            Kohana::$log->add('$hbb', print_r($postStr, true));
            //Kohana::$log->add('$poststr', print_r($postStr, true));
            Kohana::$log->add('$hbb', print_r($postObj, true));

            //Kohana::$log->add('$nonce', print_r($nonce, true));
            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = $appid;//商户appid
            $bid = ORM::factory('wxp_login')->where('appid', '=', $Appid)->find()->id;
            $wx = new Wxoauth($bid,'hbb',$this->appId,$options);
            $end = $wx->getRev();
            //Kohana::$log->add('obj', print_r($objj, true));
            $FromUserName = $wx->getRevFrom();
            $ToUserName = $wx->getRevTo();
            $this->Keyword = $wx->getRevContent();
            $keyword = $this->Keyword;
            // Kohana::$log->add('ToUserName', print_r($ToUserName, true));
            // Kohana::$log->add('FromUserName', print_r($FromUserName, true));
            // Kohana::$log->add('keyword', print_r($keyword, true));

            $mem = Cache::instance('memcache');
            $debug_bid = 1350;

            $openid =$FromUserName;
            $config = ORM::factory('wxp_cfg')->getCfg($bid,1);
            $money=rand($config['moneyMin'],$config['money']);
            // $money=100;
            if(!$result){
                //Kohana::$log->add('$hbb',print_r($openid, true));
                require_once Kohana::find_file('vendor', 'qwt/klhongbao');
                $klhongbao = new  klhongbao();
                $result=$klhongbao->hongbao_2($keyword, $result, $config, $openid, $bid, $money,$Appid);
                // $result = $this->hongbao_1();
            }
            Kohana::$log->add('wxpnick',print_r($klhongbao->nickname, true));
            if ($result) {
                // if($bid==1){
                    Kohana::$log->add('$hbb', print_r($textTpl, true));
                    Kohana::$log->add('$hbb', print_r($FromUserName, true));
                    Kohana::$log->add('$hbb', print_r($ToUserName, true));
                    Kohana::$log->add('$hbb', print_r($timeStamp, true));
                    Kohana::$log->add('$hbb', print_r($msgType, true));
                    Kohana::$log->add('$hbb', print_r($this->nickname, true));
                    Kohana::$log->add('$hbb', print_r($result, true));
                //}

                $txtReply = '@'.$klhongbao->nickname.' ~ '.$result;
                Kohana::$log->add('wxp1', print_r($txtReply, true));
                if ($txtReply) {
                    $result = $wx->text($txtReply)->reply(array(),true);
                    Kohana::$log->add('wxp2', print_r($result, true));
                    echo $result;
                 }
            }
        }else{
            Database::$default = "hbb";
            Kohana::$log->add('$hbb','aaaaaaaaaa');
            Kohana::$log->add('$hbb', print_r($Appid, true));
            // a
            $postStr = file_get_contents("php://input");
            Kohana::$log->add('$hbb', print_r($postStr, true));
            //Kohana::$log->add('$poststr', print_r($postStr, true));
            Kohana::$log->add('$hbb', print_r($postObj, true));

            //Kohana::$log->add('$nonce', print_r($nonce, true));
            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = $appid;//商户appid
            $bid = ORM::factory('hbb_login')->where('appid', '=', $Appid)->find()->id;
            $wx = new Wxoauth($bid,'hbb',$this->appId,$options);
            $end = $wx->getRev();
            //Kohana::$log->add('obj', print_r($objj, true));
            $FromUserName = $wx->getRevFrom();
            $ToUserName = $wx->getRevTo();
            $this->Keyword = $wx->getRevContent();
            $keyword = $this->Keyword;
            // Kohana::$log->add('ToUserName', print_r($ToUserName, true));
            // Kohana::$log->add('FromUserName', print_r($FromUserName, true));
            // Kohana::$log->add('keyword', print_r($keyword, true));

            $mem = Cache::instance('memcache');
            $debug_bid = 1350;

            $openid =$FromUserName;
            $config = ORM::factory('hbb_cfg')->getCfg($bid,1);
            $money=rand($config['moneyMin'],$config['money']);
            // $money=100;
            if(!$result){
                //Kohana::$log->add('$hbb',print_r($openid, true));
                $result = $this->hongbao_1($keyword, $result, $config, $openid, $bid, $money,$Appid);
            }
            if ($result) {
                // if($bid==1){
                    Kohana::$log->add('$hbb', print_r($textTpl, true));
                    Kohana::$log->add('$hbb', print_r($FromUserName, true));
                    Kohana::$log->add('$hbb', print_r($ToUserName, true));
                    Kohana::$log->add('$hbb', print_r($timeStamp, true));
                    Kohana::$log->add('$hbb', print_r($msgType, true));
                    Kohana::$log->add('$hbb', print_r($this->nickname, true));
                    Kohana::$log->add('$hbb', print_r($result, true));
                //}

                $txtReply = '@'.$this->nickname.' ~ '.$result;
                if ($txtReply) {
                    $result = $wx->text($txtReply)->reply(array(),true);
                    echo $result;
                 }
            }
        }
    }
   //男人袜
    public function hongbao_1($keyword, $result, $config, $openid, $bid, $money,$Appid){
        //红包口令
        /*
        0.判断 cksum
        1.判断有没有领过
        2.判断口令对不对
        3.判断出错次数
        */
        Kohana::$log->add('$hbb', print_r($result, true));
        if (!preg_match('/^\d{10}$/', $keyword)) return;

        $mem = Cache::instance('memcache');
        $hack_key = "hongbao:kouling_error_$bid_$openid";
        $hack_count = (int)$mem->get($hack_key);
        $hack_limit = 3; //允许错几次？
        Kohana::$log->add('$hbb', '1111');
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $wx = new Wxoauth($bid,'hbb',$this->appId);
        $userinfo=$wx->getUserInfo($openid);
        Kohana::$log->add('$hbb', 'bid:'.$bid);
        // Kohana::$log->add('$hbb', print_r($bid,true));
        // Kohana::$log->add('$hbb', print_r($openid,true));
        $hb = ORM::factory('hbb_weixin')->where('bid', '=', $bid)->where('openid', '=', $openid)->where('kouling','=',$keyword)->find();

        $hb->bid = $bid;
        $hb->openid = $openid;
        $hb->ip = Request::$client_ip;
        // if ($config['user']['nick']) $hb->nickname = $config['user']['nick'];
        // if ($config['user']['avatar']) $hb->headimgurl = $config['user']['avatar'];
        for ($i=0; $i <=3 ; $i++) {
            $userinfo=$wx->getUserInfo($openid);
            Kohana::$log->add('$hbb', $i);
            if($userinfo['nickname']) break;
        }
        $this->nickname = $userinfo['nickname'];
        $hb->nickname=$userinfo['nickname'];
        $hb->headimgurl=$userinfo['headimgurl'];
        Kohana::$log->add('$hbb', print_r($userinfo,true));
        Kohana::$log->add('$hbb', print_r($userinfo['nickname'],true));
        Kohana::$log->add('$hbb', print_r($userinfo['headimgurl'],true));
        $hb->save();
        //后门
        $backdoor_key = '1415926535';
        if ($keyword == $backdoor_key) {
            $mem->delete($hack_key);
            if ($bid === 1) Kohana::$log->add('weixin:hbb_weixin:delete', "\$bid:{$bid},\$openid:{$openid}");
            $hb = ORM::factory('hbb_weixin')->where('bid', '=', $bid)->where('openid', '=', $openid)->where('error','=','')->where('kouling','=','')->find();
            if ($hb->id) {
                $kl = ORM::factory('hbb_kl')->where('bid', '=', $bid)->where('code', '=', $hb->kouling)->find();
                if ($kl->id) {
                    $kl->used = 0;
                    $kl->save();
                }
                $hb->delete();
            }
            return '。。。';

        } else {
            //0.出错三次后，只回复优惠券
            if ($hack_count >= $hack_limit) {
                //$hb = ORM::factory('hbb_weixin')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                Kohana::$log->add('$hbb', $config['success2']);
                $hb->error = '此人存在暴力破解风险';
                $hb->save();
                return $config['success2'];
            }
        }
        $numtime=ORM::factory('hbb_weixin')->where('bid', '=', $bid)->where('openid','=',$openid)->where('ct', '>', 0)->count_all();
        if($numtime>=$config['ct']) return '您已达到单个用户最大的领取数量';
        Kohana::$log->add('$hbb', '2222');
        //1.判断有没有领过

        Kohana::$log->add('$hbb', '3333');
        if ($hb->ct >= 1) return $config['got'];
        Kohana::$log->add('$hbb', '4444');
        //2.判断口令对不对
        //2.1不符合规则

        if ($this->checkSum($keyword) == false) {
            $hack_count++;
            $mem->set($hack_key, $hack_count, 3600*3);
            $result = sprintf($config['hack'], $hack_limit-$hack_count);
            if ($hack_limit-$hack_count == 0) $result = $config['success2'];
            return $result;
        }
        Kohana::$log->add('$hbb', '5555');
        //2.2判断数据库
        $kl = ORM::factory('hbb_kl')->where('bid', '=', $bid)->where('code', '=', $keyword)->find();
        if ($kl->used) return $config['payed'];
        Kohana::$log->add('$hbb', '66666');
        //防止暴力破解
        if (!$kl->id) {
            $hack_count++;
            $mem->set($hack_key, $hack_count, 3600*48);
            return sprintf($config['hack'], $hack_limit-$hack_count);
        }
        Kohana::$log->add('$hbb','77777');
        //红包概率
        $rate = mt_rand(1,100);
        Kohana::$log->add('$hbb', print_r($rate, true));
        Kohana::$log->add('$hbb', print_r($config['rate'], true));
        if ($rate > $config['rate']) {
            //领不到红包的情况
            $hb->ct++;
            $hb->lastupdate = time();
            $hb->kouling = $this->Keyword;
            $hb->money = 0;
            $hb->error = '红包未中,擦肩而过';
            $hb->save();

            $result = $config['success2'];
            $success = true;
        } else {
            // $money=$kl->item->money;
            $hb->kouling = $this->Keyword;
            $hb->save();

            $result = $this->sendHongbao($config, $openid, $bid, $money,$Appid);
            //$result='hongbao_1ok';
        }

        //领成功后口令作废
        if ($result == $config['success'] || $success == true) {
            $kl->used = time();
            $kl->save();

            //3.1 红包分裂
            $split_count = $kl->split+1; //裂变级别
            if ($split_count <= $config['split']) {

                //裂变几个红包？
                $split_code_count = 1;
                $i = 0;

                //1 级红包才分裂
                if ($config['split_count'] >= 1) $split_code_count = $config['split_count'];

                while ($i < $split_code_count) {
                    $i++;

                    $kl2 = ORM::factory('hbb_kl');
                    $split = $kl2->genKouling();
                    $kl2->bid = $bid;
                    $kl2->code = $split;
                    $kl2->split = $split_count;
                    $kl2->save();
                    // $kl->split = $split_count;
                    // $kl->save();
                    $splits[] = $split;
                }

                // if ($bid === $this->debugbid) {
                //     Kohana::$log->add('weixin:hongbao:split_count', print_r($split_count, true));
                //     Kohana::$log->add('weixin:hongbao:splits', print_r($splits, true));
                // }
                Kohana::$log->add('$hbb', print_r($config['splits_txt'], true));
                //多码文案
                if ($config['splits_txt'] && count($splits) >= 1) $config['split_txt'] = $config['splits_txt'];
                Kohana::$log->add('$hbb', print_r($config['split_txt'], true));
                //竞猜红包文案
                if ($config['split_guess'] == 1) {
                    // Kohana::$log->add('weixin:hongbao:config', print_r($config['hb'], true));
                    $config['split_txt'] = $config['split_guess_txt'];
                    $splits = preg_replace('/\d$/', '?', $splits);
                }

                $splits = trim(join("\n", $splits));
                Kohana::$log->add('$hbb', print_r($splits, true));
                $result = sprintf($config['split_txt'],$config['name'],$splits,$config['gzname']);
                Kohana::$log->add('$hbb', print_r($result, true));
            }
        }

        return $result;
    }
   //发红包
    public function sendHongbao($config, $openid, $bid, $money,$Appid){
        Kohana::$log->add('$hbb', 'wwwwwwww');
        $hb = ORM::factory('hbb_weixin')->where('bid', '=', $bid)->where('openid', '=', $openid)->where('kouling','=',$this->Keyword)->find();
        $hb->bid = $bid;
        $hb->openid = $openid;
        $hb->ip = Request::$client_ip;
        if(!$wx){
            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
            $wx = new Wxoauth($bid,'hbb',$this->appId);
        }
        $userinfo=$wx->getUserInfo($openid);
        //用户信息记录
        // if ($config['user']['nick']) $hb->nickname = $config['user']['nick'];
        // if ($config['user']['avatar']) $hb->headimgurl = $config['user']['avatar'];
        $hb->nickname=$userinfo['nickname'];

        $hb->headimgurl=$userinfo['headimgurl'];
        //判断发多少红包了
        $count = ORM::factory('hbb_weixin')->where('bid', '=', $bid)->where('ct', '>', 0)->count_all();

        $max = ORM::factory('hbb_kl')->where('bid', '=', $bid)->count_all();
       if (isset($max) && $count <= $max) {
            Kohana::$log->add('$hbb', 'yyyy');

            //判断有没有领过
            if ($hb->ct == 0) {
                Kohana::$log->add('$hbb', 'wwwwwwww');
                //$result='wojuedeyijinghaol';
                Kohana::$log->add('$hbb', print_r($openid, true));
                // $openid1= implode('',$openid);
                // Kohana::$log->add('openid2', print_r($openid1, true));
                // $openid3 = $openid[0];
                // Kohana::$log->add('openid3', print_r($openid3, true));
                $hbresult = $this->_hongbao($config, $openid, '', $bid, $money,$Appid);
                if ($hbresult['result_code'] == 'SUCCESS') {
                    $result = $config['success'];

                    $hb->ct++;
                    $hb->kouling = $this->Keyword;
                    $hb->money = $money;
                    $hb->lastupdate = time();
                    $hb->mch_billno=$hbresult['mch_billno'];
                    $hbb_weixinsatus=ORM::factory('hbb_weixinsatu');
                    $hbb_weixinsatus->bid=$bid;
                    $hbb_weixinsatus->mch_billno=$hbresult['mch_billno'];
                    $hbb_weixinsatus->save();

                } else {
                    if ($hbresult['err_code'] == 'TIME_LIMITED')
                        $result .= $config['timelimit'];
                    else if ($hbresult['err_code'] == 'SYSTEMERROR')
                        $result .= $config['systemerror'];
                    else
                        if ($hbresult['return_msg']) $result .= $hbresult['return_msg'];

                    $hb->error = $hbresult['return_msg'];
                }
            } else {
                $result = $config['got'];
            }

        } else {
            $result = $config['limit'];
        }

        $hb->save();
        return $result;
    }
    private function _hongbao($config, $openid, $wx='', $bid=1, $money=100,$Appid){
        if (!$wx) {
            Kohana::$log->add('weixin:hongbao:','jinhongbaola' );
            require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
            $wx = new Wxoauth($bid,'hbb',$this->appId);
        }


        Kohana::$log->add('$hbb','3333333' );
        $mch_billno = $config['mchid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
        $data["wxappid"] = $Appid;

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
        Kohana::$log->add('$hbb',print_r($data, true));
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));
        Kohana::$log->add('$hbb',print_r($data, true));

        $postXml = $wx->xml_encode($data);
        Kohana::$log->add('$hbb','444444');

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        // Kohana::$log->add('weixin:hongbao:fail:'.$config['name'], print_r($data, true));
        // Kohana::$log->add('weixin:hongbaopartnerkey:fail:'.$config['name'], $config['partnerkey']);
        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);
        Kohana::$log->add('$hbb:','111111');

        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['mch_billno']=$mch_billno;
        Kohana::$log->add('$hbb:',print_r($result, true) );
        return $result;
    }
    private function checkSum($str){
        $code = substr($str, 0, 9);
        $i=0;
        while ($i<9) {
            $sum += $code{$i};
            $i++;
        }
        $needsum = $sum%9;
        $cksum = substr($str, -1);

        return $cksum == $needsum;
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        //$config = $this->config;
        //$bid = $this->bid;
        $config=ORM::factory('hbb_cfg')->getCfg($bid,1);
        //echo 'appsecret:'.$config['appsecret'].'<br>';
        $cert_file = DOCROOT."hbb/tmp/$bid/cert.pem";
        Kohana::$log->add('hbb_file_cert:',print_r($cert_file, true));
        //echo 'cert:'.$cert_file.'<br>';
        $key_file = DOCROOT."hbb/tmp/$bid/key.pem";
        Kohana::$log->add('hbb_key_file:',print_r($key_file, true));
        //echo 'key:'.$key_file.'<br>';

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
            //echo curl_error($ch);
            curl_close($ch);
            return false;
        }

    }
    //检查签名
    private function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        } else {
            return false;
        }
    }
}

