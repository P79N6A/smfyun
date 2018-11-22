<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_wfbticket extends Controller_API {

    var $token = 'weifubao';
    var $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    var $appId = 'wxc520face24b8f175';
    var $appserect = '805d1c2f3a6a9189f12271bf4625fc45';

    var $FromUserName;
    var $Keyword;
    var $access_token;
    // var $baseurl = 'http://jfb.smfyun.com/wfb/';
    // var $cdnurl = 'http://cdn.jfb.smfyun.com/wfb/';

    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "wfb";
        if (!is_numeric($bid)) $bid = ORM::factory('wfb_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('wfb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."微服宝 by 1nnovator");
    }

    //
    public function action_post()
    {
        $encryptMsg = file_get_contents('php://input', 'r');
        Kohana::$log->add('wfbticket:postdata', print_r($encryptMsg, true));
        $timeStamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        require_once Kohana::find_file('vendor', 'wx_oauth/wxBizMsgCrypt');
        $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
        $xml_tree = new DOMDocument();
        // $xml_tree->loadXML($encryptMsg);
        // $array_e = $xml_tree->getElementsByTagName('Encrypt');
        // //$array_s = $xml_tree->getElementsByTagName('MsgSignature');
        // $encrypt = $array_e->item(0)->nodeValue;
        $msg_sign = $_GET["msg_signature"];
        // Kohana::$log->add('hbticket:encrypt', print_r($encrypt, true));
        // Kohana::$log->add('hbticket:nonce', print_r($nonce, true));
        // Kohana::$log->add('hbticket:msg_sign', print_r($msg_sign, true));
        // $format = "<xml><AppId><![CDATA[toUser]]></AppId><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        // $from_xml = sprintf($format, $encrypt);

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
            Kohana::$log->add('wfbticket:错误码', print_r($errCode, true));
        }
        echo 'success';
    }
    // public function encrypt($data){//加密
    //     $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
    //     $text = "<xml><AppId><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></AppId><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";
    //     $timeStamp = time();
    //     $nonce = $this->getRandChar(32);
    //     $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $data);
    //     if ($errCode == 0) {
    //         Kohana::$log->add('hbticket:加密', print_r($data, true));
    //         return $data;
    //     } else {
    //         Kohana::$log->add('hbticket:加密失败', print_r($errCode, true));
    //     }
    // }
    public function getRandChar($length){//随机数
       $str = null;
       $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
       $max = strlen($strPol)-1;

       for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
       }

       return $str;
    }
    // public function dencrypt($data,$timeStamp,$nonce){//解密
    //     $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
    //     $format = "<xml><AppId><![CDATA[toUser]]></AppId><Encrypt><![CDATA[%s]]></Encrypt></xml>";
    //     $from_xml = sprintf($format, $encrypt);
    //     // 第三方收到公众号平台发送的消息
    //     $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $data);
    //     if ($errCode == 0) {
    //         print("解密后: " . $msg . "\n");
    //     } else {
    //         print($errCode . "\n");
    //     }
    // }
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
