<?php defined('SYSPATH') or die('No direct script access.');

//curl http://www.nanrenwa.com/api/weixin --data @wwwroot/tmp/weixin.xml

Class Controller_Api_Weixin extends Controller_API {

    var $token = 'nanrenwaweixin1111';
    var $FromUserName;
    var $Keyword;

    const MSG_NOBIND = "该功能需要先绑定用户\n如果您已经是男人袜会员\n请回复【绑定】继续";

    //http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-callbackapi-doc&lang=zh_CN#token
    //验证
    public function action_get($bid=1)
    {
        if (isset($_GET['debug'])) {
            $this->action_post($_GET['debug']);
        }

        require_once Kohana::find_file('vendor', "weixin/biz/$bid");

        if ($this->checkSignature() == true)
            die($_GET['echostr']);
        else
            die($config['name']."@微信接口 by bole");
    }

    //收发消息: $bid、附加处理函数
    public function action_post($bid=1, $method='')
    {
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/biz.inc'); //默认配置
        require_once Kohana::find_file('vendor', "weixin/biz/$bid");

        if ($config['youzan_appid']) {
            require Kohana::find_file('vendor', 'kdt/KdtApiClient');
            $kdt = new KdtApiClient($config['youzan_appid'], $config['youzan_appsecret']);
        }

        $postStr = file_get_contents("php://input");
        Kohana::$log->add('weixin:xml', $postStr);

        $time = time();
        $msgType = "text";

        $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
        $freeTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>1</ArticleCount><Articles><item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item></Articles></xml>";

        $postStr = file_get_contents("php://input");

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

        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);

        //扫码事件
        $scene_id = $config['scene_id'];
        if (($Event == 'SCAN' && $EventKey == $scene_id) || ($Event == 'subscribe' && $EventKey == "qrscene_$scene_id")) {
            // Kohana::$log->add('weixin:scan', $postStr);
            $tpl = '你扫了我：Openid->%s, Ticket->%s';

            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = sprintf($tpl, $openid, $Ticket);
            $we->sendCustomMessage($msg);
        }

        //菜单点击事件
        if ($Event == 'CLICK') {
            $msg['touser'] = $openid;

            if ($EventKey == '获取海报') {

                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '海报生成中，请稍等……';
                $we->sendCustomMessage($msg);

                $result = $we->getQRCode($config['scene_id'], 0, 604800);
                $qrurl = $we->getQRUrl($result['ticket']);
                Kohana::$log->add('weixin:scan', $qrurl);

                $localfile = '/tmp/'.$result['ticket'].'.jpg';
                file_put_contents($localfile, file_get_contents($qrurl));
                $uploadresult = $we->uploadMedia(array('media'=>"@$localfile"), 'image');
                Kohana::$log->add('weixin:scan', print_r($uploadresult, true));

                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                $we->sendCustomMessage($msg);
            }

            if ($EventKey) {
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = '你点了：'.$EventKey;
                $we->sendCustomMessage($msg);
            }
        }

        //获取昵称
        $kdtresult = $kdt->post('kdt.users.weixin.follower.get', array('weixin_openid' => $openid));
        $nickname = $kdtresult['response']['user']['nick'];
        $config['user'] = $kdtresult['response']['user'];

        //语音识别
        if ($postObj->Recognition && strlen($postObj->Recognition) < 20) {
            $yuyin = trim($postObj->Recognition);

            $result = $config['yuyin']['NOMATCH'];
            if (strpos($config['yuyin']['NOMATCH'], '%s') == true) $result = sprintf($config['yuyin']['NOMATCH'], $yuyin);

            foreach ($config['yuyin'] as $k=>$v) if ($k == $yuyin) {
                $result = $v;
                //触发红包
                if ($result == 'MONEY') $result = $this->sendHongbao($config, $openid, $bid, $money);
            }
        }

        //商户单独处理函数
        $method_name = 'hongbao_'.$bid;
        if ($method) $method_name.="_$method";

        if (method_exists($this, $method_name)) $result = $this->$method_name($keyword, $result, $config, $openid, $bid, $money);

        if ($result) {
            $result2 = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, '@'.$nickname.' ~ '.$result);
            echo $result2;
            Kohana::$log->add('weixin:result2', $result2);
        }

        exit;
    }

    //男人袜
    public function hongbao_1($keyword, $result, $config, $openid, $bid, $money)
    {
        $mem = Cache::instance('memcache');

        //爬楼插件
        if ($keyword == '爱爸爸送男人袜') {
            $lou_key = 'nanrenwa:weixin:lou_count';
            $lou_count = (int)$mem->get($lou_key);
            $lou_count += rand(1, 10);
            $mem->set($lou_key, $lou_count, 0);

            $msg = "你现在是第 %s 楼，离大奖就差一点点啦，快快继续盖楼抢父亲节礼！\n\n爸比这么爱你，父亲节不送他点儿贴心礼物逗他开心？伦家已经为你备好了暖心到爆的礼盒。说不出来的话，就用小礼物表达：父亲节浓情礼盒（链接：http://dwz.cn/OTNSN）\n\n活动详情： http://dwz.cn/2015fd ";
            return sprintf($msg, $lou_count);
        }

        //红包口令
        /*
        0.判断 cksum
        1.判断有没有领过
        2.判断口令对不对
        3.判断出错次数
        */
        if (!preg_match('/^\d{10}$/', $keyword)) return;
        // if ($this->checkSum($keyword) == false) return;

        $hack_key = "hongbao:kouling_error_$bid_$openid";
        $hack_count = (int)$mem->get($hack_key);
        $hack_limit = 3; //允许错几次？

        //后门
        $backdoor_key = '9876543210';
        if ($keyword == $backdoor_key) {
            $mem->delete($hack_key);

            $hb = ORM::factory('weixinhb2')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            if ($hb->id) {
                $kl = ORM::factory('weixinkl')->where('bid', '=', $bid)->where('code', '=', $hb->kouling)->find();
                if ($kl->id) {
                    $kl->used = 0;
                    $kl->save();
                }
                $hb->delete();
            }
            return '。。。';

        } else {
            //0.出错三次后，只回复优惠券
            if ($hack_count >= $hack_limit) return $config['hb']['success2'];
        }

        //1.判断有没有领过
        $hb = ORM::factory('weixinhb2')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        $hb->bid = $bid;
        $hb->openid = $openid;
        $hb->ip = Request::$client_ip;
        if ($config['user']['nick']) $hb->nickname = $config['user']['nick'];
        if ($config['user']['avatar']) $hb->headimgurl = $config['user']['avatar'];
        $hb->save();

        if ($hb->ct >= 1) return $config['hb']['got'];

        //2.判断口令对不对
        //2.1不符合规则
        if ($this->checkSum($keyword) == false) {
            $hack_count++;
            $mem->set($hack_key, $hack_count, 3600);

            $result = sprintf($config['hb']['hack'], $hack_limit-$hack_count);
            if ($hack_limit-$hack_count == 0) $result = $config['hb']['success2'];
            return $result;
        }

        //2.2判断数据库
        $kl = ORM::factory('weixinkl')->where('bid', '=', $bid)->where('code', '=', $keyword)->find();
        if ($kl->used) return $config['hb']['payed'];

        //防止暴力破解
        if (!$kl->id) {
            $hack_count++;
            $mem->set($hack_key, $hack_count, 3600*24);
            return sprintf($config['hb']['hack'], $hack_limit-$hack_count);
        }

        //红包概率
        $rate = mt_rand(1,100);

        if ($rate > $config['hb']['rate']) {
            //领不到红包的情况
            $hb->ct++;
            $hb->lastupdate = time();
            $hb->money = 0;
            $hb->save();

            $result = $config['hb']['success2'];
            $success = true;
        } else {
            $result = $this->sendHongbao($config, $openid, $bid, $money);
        }

        //领成功后口令作废
        if ($result == $config['hb']['success'] || $success == true) {
            $kl->used = time();
            $kl->save();
        }

        return $result;
    }

    //检查红包口令是否合法
    private function checkSum($str)
    {
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

    private function getUser()
    {
        $weixin = ORM::factory('weixin')->where('hash', '=', $this->FromUserName)->find();
        return $weixin->user;
    }

    //发红包
    public function sendHongbao($config, $openid, $bid, $money)
    {
        $hb = ORM::factory('weixinhb2')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        $hb->bid = $bid;
        $hb->openid = $openid;
        $hb->ip = Request::$client_ip;

        //用户信息记录
        if ($config['user']['nick']) $hb->nickname = $config['user']['nick'];
        if ($config['user']['avatar']) $hb->headimgurl = $config['user']['avatar'];

        //判断发多少红包了
        $count = ORM::factory('weixinhb2')->where('bid', '=', $bid)->where('ct', '>', 0)->count_all();
        if (isset($config['max']) && $count <= $config['max']) {

            //判断有没有领过
            if ($hb->ct == 0) {
                $hbresult = $this->_hongbao($config, $openid, '', $bid, $money);
                if ($hbresult['result_code'] == 'SUCCESS') {
                    $result = $config['hb']['success'];

                    $hb->ct++;
                    $hb->kouling = $this->Keyword;
                    $hb->money = $money;
                    $hb->lastupdate = time();

                } else {
                    Kohana::$log->add('weixin:hongbao:fail', print_r($hbresult, true));

                    if ($hbresult['err_code'] == 'TIME_LIMITED')
                        $result .= $config['hb']['timelimit'];
                    else
                        if ($hbresult['return_msg']) $result .= $hbresult['return_msg'];
                }
            } else {
                $result = $config['hb']['got'];
            }

        } else {
            $result = $config['hb']['limit'];
        }

        $hb->save();
        return $result;
    }

    private function _hongbao($config, $openid, $we='', $bid=1, $money=100)
    {
        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', "weixin/biz/$bid");

            $we = new Wechat($config);
        }

        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //商户号
        $data["wxappid"] = $config['appid'];

        $data["re_openid"] = $openid;
        $data["total_amount"] = $money;
        $data["min_value"] = $money; //最小金额
        $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "[{$config['name']}]送红包"; //活动名称
        $data["nick_name"] = $config['name']; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));

        $postXml = $we->xml_encode($data);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';

        Kohana::$log->add('weixin:hongbaopost', print_r($data, true));

        $resultXml = curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        return $result;
    }

}