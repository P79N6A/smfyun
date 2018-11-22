<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_wzbticket extends Controller_API {
    //微信开放平台：神码浮云营销应用平台
    var $token = 'zhibo';
    var $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    var $appId = 'wxd0b3a6ff48335255';
    var $appserect = 'c5c35a468cc1440da618aa3f598a53d9';

    var $FromUserName;
    var $Keyword;
    var $access_token;

    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "whb";
        // if (!is_numeric($bid)) $bid = ORM::factory('whb_login')->where('user', '=', $bid)->find()->id;
        // $config = ORM::factory('whb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."微红包 by 1nnovator");
    }

    //
    public function action_post()
    {
        $encryptMsg = file_get_contents('php://input', 'r');
        Kohana::$log->add('whbticket:postdata', print_r($encryptMsg, true));
        $timeStamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        require_once Kohana::find_file('vendor', 'wx_oauth/wxBizMsgCrypt');
        $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
        $xml_tree = new DOMDocument();

        $msg_sign = $_GET["msg_signature"];
        // 第三方收到公众号平台发送的消息

        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $encryptMsg, $endcode);
        if ($errCode == 0) {
            // Kohana::$log->add('hbticket:解密后', print_r($endcode, true));
            $mem = Cache::instance('memcache');
            $cachename1 ='component_access_token'.$this->appId;
            $cachename2 ='expiretime'.$this->appId;
            $ctoken = $mem->get($cachename1);
            $ctime = $mem->get($cachename2);
            if(!$ctoken||(time()+1800>=$ctime)){// 不存在 或在过期范围内
                $xml_tree->loadXML($endcode);
                $array_c = $xml_tree->getElementsByTagName('ComponentVerifyTicket');
                $ticket = $array_c->item(0)->nodeValue;
                // Kohana::$log->add('cticket:', print_r($ticket, true));
                $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
                $post_data = array(
                  'component_appid' =>$this->appId,
                  'component_appsecret' =>$this->appserect,
                  'component_verify_ticket' =>$ticket
                );
                $post_data = json_encode($post_data);
                // Kohana::$log->add('wxappid:', print_r($this->appId, true));
                // Kohana::$log->add('postdata:', print_r($post_data, true));
                // Kohana::$log->add('hbjson:', print_r($this->request_post($url, $post_data), true));
                $res = json_decode($this->request_post($url, $post_data),true);
                // Kohana::$log->add('hbarray:', print_r($res, true));
                $component_access_token = $res['component_access_token'];
                $mem->set($cachename1, $component_access_token, 7200);
                $mem->set($cachename2, time()+7200, 5400);
                // Kohana::$log->add('ctoken:', print_r($component_access_token, true));
                // Kohana::$log->add('extime:', print_r($res, true));
            }
        } else {
            Kohana::$log->add('hbticket:encrypt', print_r($encrypt, true));
            Kohana::$log->add('hbticket:nonce', print_r($nonce, true));
            Kohana::$log->add('hbticket:msg_sign', print_r($msg_sign, true));
            Kohana::$log->add('whbticket:错误码', print_r($errCode, true));
        }
        echo 'success';
    }

    public function getRandChar($length){//随机数
       $str = null;
       $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
       $max = strlen($strPol)-1;

       for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
       }

       return $str;
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
    //检查签名
    private function checkSignature()
    {
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
