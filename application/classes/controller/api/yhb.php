<?php defined('SYSPATH') or die('No direct script access.');

Class Controller_Api_Yhb extends Controller_API {
    var $token = 'smfyun1234';
    var $FromUserName;
    var $Keyword;
    var $tempmax;
    var $debugbid = 'guanzi';
    var $access_token;
    var $bid;
    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug'], 1);
        }

        Database::$default = "yhb";
        if (!is_numeric($bid)) $bid = ORM::factory('yhb_login')->where('user', '=', $bid)->find()->id;
        $config = ORM::factory('yhb_cfg')->getCfg($bid);

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@". $config['score'] ."宝 by 1nnovator");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bname=1, $debug=0){
        Database::$default = "yhb";
        $postStr = file_get_contents("php://input");
        //Kohana::$log->add('$nonce', print_r($nonce, true));
        $time = time();
        $msgType = "text";

        $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
        $freeTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>1</ArticleCount><Articles><item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item></Articles></xml>";
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $this->Keyword = $keyword;
        $this->FromUserName = (string)$postObj->FromUserName;
        $openid = (string)$postObj->FromUserName;
        //关注事件
        $Event = (string)$postObj->Event;
        $EventKey = (string)$postObj->EventKey;
        $Ticket = (string)$postObj->Ticket;
        //获取昵称
        $biz = ORM::factory('yhb_login')->where('user', '=', $bname)->find();
        $bid = $biz->id;
        $config = ORM::factory('yhb_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('yhb_login')->where('id', '=', $bid)->find()->access_token;
        $access_token=$this->access_token;
        if($access_token){
            require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
            $client = new KdtApiOauthClient();
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $method = 'kdt.users.weixin.follower.get';
        $params = [
            'weixin_openid' => $openid
        ];
        $kdtresult = $client->post($access_token,$method,$params);
        $nickname = $kdtresult['response']['user']['nick'];
        $config['user'] = $kdtresult['response']['user'];
        if (!$result) {
            $result = $this->hongbao_1($keyword, $result, $config, $openid, $bid);
        }
        if ($result) {
            $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, '@'.$nickname.' ~ '.$result);
            if ($bid == 1) $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $result);
            echo $result2;
            if ($bid === $this->debugbid) Kohana::$log->add('weixin:result2', $result2);
        }
        exit;
    }
       //男人袜
    public function hongbao_1($keyword, $result, $config, $openid, $bid){
        //红包口令
        /*
        0.判断 cksum
        1.判断有没有领过
        2.判断口令对不对
        3.判断出错次数
        */
        if (!preg_match('/^\d{10}$/', $keyword)) return;
        $mem = Cache::instance('memcache');
        $hack_key = "hongbao:kouling_error_$bid_$openid";
        $hack_count = (int)$mem->get($hack_key);
        $hack_limit = 3; //允许错几次？
        //后门
        $backdoor_key = '9876543210';
        if ($keyword == $backdoor_key) {
            $mem->delete($hack_key);
            if ($bid === $this->debugbid) Kohana::$log->add('weixin:weixinhb2:delete', "\$bid:{$bid},\$openid:{$openid}");
            $hb = ORM::factory('youzan')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            if ($hb->id) {
                $kl = ORM::factory('kl')->where('bid', '=', $bid)->where('code', '=', $hb->kouling)->find();
                if ($kl->id) {
                    $kl->used = 0;
                    $kl->save();
                }
                $hb->delete();
            }
            return '。。。';

        } else {
            //0.出错三次后，只回复优惠券
            if ($hack_count >= $hack_limit) return '错误三次';
        }

        //1.判断有没有领过
        $hb = ORM::factory('youzan')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        $hb->bid = $bid;
        $hb->openid = $openid;
        $hb->ip = Request::$client_ip;
        if ($config['user']['nick']) $hb->nickname = $config['user']['nick'];
        if ($config['user']['avatar']) $hb->headimgurl = $config['user']['avatar'];
        $hb->save();

        if ($hb->ct >= 1) return $config['yhb']['got'];

        //2.判断口令对不对
        //2.1不符合规则
        if ($this->checkSum($keyword) == false) {
            $hack_count++;
            $mem->set($hack_key, $hack_count, 3600*3);
            $result = sprintf($config['yhb']['hack'], $hack_limit-$hack_count);
            if ($hack_limit-$hack_count == 0) $result = $config['yhb']['success2'][0];
            return $result;
        }
        //2.2判断数据库
        $kl = ORM::factory('kl')->where('bid', '=', $bid)->where('code', '=', $keyword)->find();
        if ($kl->used) return $config['yhb']['payed'];

        //防止暴力破解
        if (!$kl->id) {
            $hack_count++;
            $mem->set($hack_key, $hack_count, 3600*48);
            return sprintf($config['yhb']['hack'], $hack_limit-$hack_count);
        }
        $result = $this->sendYzCoupon($config, $openid, $bid);
        //领成功后口令作废
        if ($result == $config['yhb']['success'] || $success == true) {
            $kl->used = time();
            $kl->save();
            //领取红包成功后 & 客服消息发送附加消息
            if ($result == $config['yhb']['success'] && $config['yhb']['success_msg'])  {
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $msg['touser'] = $openid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $config['yhb']['success_msg'];
                $we = new Wechat($config);
                $we_result = $we->sendCustomMessage($msg);
                if ($bid === 'zhiguanguanfangqijia') Kohana::$log->add('weixin:hongbao:we_result', print_r($we_result, true));
            }
        }

        return $result;
    }
        //发红包
    public function sendYzCoupon($config, $openid, $bid){
        $hb = ORM::factory('youzan')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        $hb->bid = $bid;
        $hb->openid = $openid;
        $hb->ip = Request::$client_ip;
        //用户信息记录
        if ($config['user']['nick']) $hb->nickname = $config['user']['nick'];
        if ($config['user']['avatar']) $hb->headimgurl = $config['user']['avatar'];

        //判断发多少红包了
        $count = ORM::factory('youzan')->where('bid', '=', $bid)->where('ct', '>', 0)->count_all();


       if (isset($config['max']) && $count <= $config['max']) {
            //判断有没有领过
            if ($hb->ct == 0) {
                $hbresult = $this->_hongbao($config, $openid, '', $bid);
                if ($hbresult['result_code'] == 'SUCCESS') {
                    $result = $config['yhb']['success'];
                    $hb->ct++;
                    $hb->kouling = $this->Keyword;
                    $hb->money = $money;
                    $hb->lastupdate = time();
                    $hb->mch_billno=$hbresult['mch_billno'];
                    $weixinhbstatus=ORM::factory('weixinhbstatu');
                    $weixinhbstatus->bid=$bid;
                    $weixinhbstatus->mch_billno=$hbresult['mch_billno'];
                    $weixinhbstatus->save();
                } else {
                    Kohana::$log->add('weixin:hongbao:fail', print_r($hbresult, true));
                    if ($hbresult['err_code'] == 'TIME_LIMITED')
                        $result .= $config['yhb']['timelimit'];
                    else if ($hbresult['err_code'] == 'SYSTEMERROR')
                        $result .= $config['yhb']['systemerror'];
                    else
                        if ($hbresult['return_msg']) $result .= $hbresult['return_msg'];
                }
            } else {
                $result = $config['yhb']['got'];
            }

        } else {
            $result = $config['yhb']['limit'];
        }
        $hb->save();
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

