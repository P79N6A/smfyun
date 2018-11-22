<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtywm extends Controller_Base {
    public $template = 'weixin/smfyun/ywm/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'smfyun';
    public $scorename;
    public $methodVersion='3.0.0';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "qwt";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'index') return;
        if (Request::instance()->action == 'shareurl') return;
        if (Request::instance()->action == 'error') return;
        // if (!$_GET['openid']) {
        //     if (!$_SESSION['qwtywm']['bid']) die('页面已过期。请重新点击相应菜单');
        //     if (!$_SESSION['qwtywm']['openid']) die('Access Deined..请重新点击相应菜单');
        // }
        if (!$_SESSION['qwtywm']['bid']) die('页面已过期。请重新点击相应菜单');
        $this->config = $_SESSION['qwtywm']['config'];
        $this->openid = $_SESSION['qwtywm']['openid'];
        $this->bid = $_SESSION['qwtywm']['bid'];
        $this->uid = $_SESSION['qwtywm']['uid'];
        $this->myopenid = $_SESSION['qwtywm']['myopenid'];


        $sname = ORM::factory('qwt_ywmcfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        if ($_GET['debug']) print_r($_SESSION['ywm']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('qwt_ywmqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('qwt_ywmqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);
        View::bind_global('scorename', $this->scorename);
        $this->template->user = $user;
        parent::after();
    }
    public function action_shareurl(){
        if($_GET['bid']&&$_GET['openid']&&$_GET['hb_code']){
            $bid = $_GET['bid'];
            $openid = $_GET['openid'];
            $code = $_GET['hb_code'];
            $config = ORM::factory('qwt_ywmcfg')->getCfg($bid,1);
            if($config['jumpurl']){
                $weixin = ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('kouling','=',$code)->find();
                if($weixin->id){
                    $weixin->pv = $weixin->pv+1;
                    $ip = str_replace('.', '_', Request::$client_ip);
                    // echo "<pre>";
                    // var_dump($_COOKIE);
                    if(!$_COOKIE[$ip]){//如果cookie存在 代表uv不增加
                        $weixin->uv = $weixin->uv+1;
                    }
                    setcookie($ip,1, time()+3600*24);//ip 24小时的cookie
                    $weixin->save();
                    $url = $config['jumpurl'];
                    echo $url;
                    Request::instance()->redirect($url);exit;
                }else{
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtywm/error';
                    Request::instance()->redirect($url);exit;
                }
            }else{
                $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtywm/error';
                Request::instance()->redirect($url);exit;
            }
        }else{
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtywm/error';
            Request::instance()->redirect($url);exit;
        }
    }
    public function action_error(){
        die('分享url未填写');
    }
    public function action_user_snsapi_base(){
        $url = 'qr_hb';
        $bid = $this->bid;
        $app = 'ywm';
        require_once Kohana::find_file('vendor', 'oauth/selfoauth.class');
        $biz = ORM::factory('qwt_oauth')->where('id','=',3)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;
        $wx = new Wxoauth(3,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("ywm_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            // echo '<pre>';
            // var_dump($token);
            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$this->openid)->find();
            $user->myopenid = $token['openid'];
            $user->save();

            $_SESSION['qwt'.$app]['myopenid'] = $user->myopenid;
            // var_dump($_SESSION);
            // exit;
            Request::instance()->redirect('/qwt'.$app.'/'.$url."?hb_code=".urlencode($_GET['hb_code']));
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_goshopping($iid){
        $bid=$_GET['bid'];
        $openid=$_GET['openid'];
        $code=$_GET['code'];
        $item=ORM::factory('qwt_ywmsetgood')->where('id','=',$iid)->find();
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid,1);
        $yzaccess_token=ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $weixin = ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('kouling','=',$code)->find();
        Kohana::$log->add('goshopping1'.$bid.':openid:'.$openid, print_r($code,true));
        Kohana::$log->add('goshopping2'.$bid.':openid:'.$openid, print_r($config['ifyzcoupons'],true));
        Kohana::$log->add('goshopping3'.$bid.':openid:'.$openid, print_r($weixin->yzcoupon,true));
        Kohana::$log->add('goshopping4'.$bid.':openid:'.$openid, print_r($yzaccess_token,true));
        if($config['ifyzcoupons']==1&&$weixin->yzcoupon==0){
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $client = new YZTokenClient($yzaccess_token);
            $method = 'youzan.ump.coupon.take';
            $params = [
                'coupon_group_id'=>$config['yzcoupons'],
                'weixin_openid'=>$openid,
             ];
            $results = $client->post($method, '3.0.0', $params, $files);
            Kohana::$log->add('goshopping5'.$bid.':openid:'.$openid, print_r($results,true));
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
            $wx = new Wxoauth($bid,$options);
            Kohana::$log->add('goshopping5'.$bid.':openid:'.$openid, print_r($wx,true));
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            if($results['response']){
                $weixin->yzcoupon=1;
                $weixin->save();
                $msg['text']['content'] = '优惠券下发成功!';
            }elseif($results['error_response']){
                $msg['text']['content'] = $results['error_response']['code'].':'.$results['error_response']['msg'];
            }
            $wxresult=$wx->sendCustomMessage($msg);
        }
        Request::instance()->redirect($item->url);
        exit;
        // echo $bid.'<br>';
        // echo $iid.'<br>';
    }
    public function action_qr_hb(){
        $config = $this->config;
        $this->template = 'tpl/blank';
        self::before();
        
        $view = "weixin/smfyun/ywm/hb";

        $user = ORM::factory('qwt_ywmqrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        $privateKey = "sjdksldkwospaisk";
        $iv = "wsldnsjwisqweskl";
        $encryptedData = base64_decode($_GET['hb_code']);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        // echo $decrypted.'<br>';
        $_GET['hb_code'] = explode('+-+',$decrypted)[0];
        $_GET['iid'] = explode('+-+',$decrypted)[1];
        $buy = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $result['all'] = $buy->ywm_money;

        // $money1=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmorders where bid=$this->bid and state = 1 ")->execute()->as_array();
        // $result['all'] = number_format($money1[0]['money'],2);

        $money2=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmweixins where bid=$this->bid and ct = 1 ")->execute()->as_array();
        $result['used'] = number_format($money2[0]['money']/100,2);
        if($result['all']<=0){
            $result['error'] = '商户余额不足！';
        }
        // echo $_GET['hb_code'].'<br>';
        // echo $_GET['iid'].'<br>';
        $item=ORM::factory('qwt_ywmsetgood')->where('id','=',$_GET['iid'])->find();
        // $qr_user = ORM::factory('qwt_qrcode')->where('id','=',$user->qid)->find();
        //ct=0代表扫了但是没有分享
        //ct=1代表红包下发成功
        //ct=2代表红包下发失败 会有错误提示
        //ct=3代表已经分享了 等待下发
        //used代表被扫了
        if($_POST['hasshare']==1){
            $weixin = ORM::factory('qwt_ywmweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('kouling','=',$_POST['hb_code'])->find();
            if(!$weixin->id) $result['error'] = '口令不存在';
            if($weixin->ct==1) $result['error'] = '红包已经领取了';
            if($weixin->ct==2) $result['error'] = '红包下发失败'.$weixin->error;
            if($weixin->ct==3) {
                $result['wx_qr_img'] = $this->bid;
                $result['error'] = '需要关注后才能领取红包哦';
            }
            // $count = ORM::factory('qwt_ywmweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->count_all();
            // if($count>=$config['mostscan']||$count>=10){
            //     $result['error'] = '已达到最大的扫码次数';
            // }
            if($result['error']){
                echo json_encode($result);
                exit;
            }
            $weixin->share = 1;
            $weixin->ct = 3;
            if($config['ifattention']==1){//开启了关注后才能领取
                if($user->qrcodes->subscribe==1){//已经关注了
                    $money = rand($config['leastmoney'],$config['mostmoney']);
                    if($result['all']>=$money/100){
                        $hbresult = $this->hongbao($config,$user->myopenid,$this->bid,$money);
                        if($hbresult['result_code'] == 'SUCCESS'){//下发成功
                            $weixin->ct = 1;
                            $weixin->money = $money;
                            $weixin->mch_billno=$hbresult['mch_billno'];
                            $buy->ywm_money = $buy->ywm_money-number_format($weixin->money/100,2,'.','');
                            $buser = ORM::factory('qwt_ywmorder');
                            $buser->money = -number_format($weixin->money/100,2,'.','');
                            $buser->bid = $weixin->bid;
                            $buser->wxid = $weixin->id;
                            $buser->state = 1;
                            $buser->left = $buy->ywm_money;

                            $buy->save();
                            $buser->save();
                            $result['content'] = '红包下发成功';
                            $result['headimgurl'] = $user->headimgurl;
                            $result['nickname'] = $user->nickname;
                            $result['time'] = date('Y-m-d H:i:s',time());
                            $result['money'] = number_format($money/100,2);
                        }else{
                            $weixin->ct = 2;
                            $weixin->money = $money;
                            $weixin->error = $hbresult['err_code'].$hbresult['return_msg'];
                            $result['error'] = $weixin->error;
                        }
                    }else{
                        $weixin->ct = 2;
                        $weixin->money = $money;
                        $weixin->error = '账户余额不足，请前往后台充值';
                        $result['error'] = $weixin->error;
                    }
                }else{
                    $result['error'] = '需要关注后才能领取红包哦';
                    $result['wx_qr_img'] = $this->bid;
                }
            }else{
                $money = rand($config['leastmoney'],$config['mostmoney']);
                $hbresult = $this->hongbao($config,$this->myopenid,$this->bid,$money);
                if($hbresult['result_code'] == 'SUCCESS'){//下发成功
                    $weixin->ct = 1;
                    $weixin->money = $money;
                    $result['content'] = '红包下发成功';
                    $result['money'] = number_format($money/100,2);
                }else{
                    $weixin->ct = 2;
                    $weixin->money = $money;
                    $weixin->error = $hbresult['err_code'].$hbresult['return_msg'];
                    $result['error'] = $weixin->error;
                }
            }
            $weixin->sendtime = time();
            $weixin->save();
            echo json_encode($result);
            exit;
        }
        $kl = ORM::factory('qwt_ywmkl')->where('bid','=',$this->bid)->where('iid','=',$_GET['iid'])->where('code','=',$_GET['hb_code'])->find();
        $weixin = ORM::factory('qwt_ywmweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('kouling','=',$_GET['hb_code'])->find();
        // echo $user->qrcodes->id."<br>";
        // echo $user->qrcodes->nickname."<br>";
        // echo $user->qrcodes->subscribe;
        // exit();
        $count = ORM::factory('qwt_ywmweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->count_all();
        if($count>=$config['mostscan']||$count>=10){
            $result['error'] = '已达到最大的扫码次数';
        }else{
            if($kl->id){
                if($kl->used>0){
                    if(!$weixin->id) $result['error'] = '该二维码已经被扫了';//不是本人
                    if($weixin->ct==1) $result['error'] = '红包已经领取了';
                    if($weixin->ct==2) $result['error'] = '红包下发失败'.$weixin->error;
                    if($weixin->ct==3) $result['error'] = '等待下发';
                }else{
                    $kl->used = time();
                    $kl->save();
                    $weixin->qid = $user->id;
                    $weixin->nickname = $user->nickname;
                    $weixin->headimgurl = $user->headimgurl;
                    $weixin->openid = $this->openid;
                    $weixin->kouling = $_GET['hb_code'];
                    $weixin->iid = $_GET['iid'];
                    $weixin->bid = $this->bid;
                    $weixin->save();
                }
            }else{
                $result['error'] = '红包不存在！';
            }
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $biz = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $biz->appid;
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $result['bgpic'] = ORM::factory('qwt_ywmcfg')->where('bid', '=', $this->bid)->where('key', '=', 'bgpic')->find()->id;
        $result['logo'] = ORM::factory('qwt_ywmcfg')->where('bid', '=', $this->bid)->where('key', '=', 'logo')->find()->id;
        $result['sharelogo'] = ORM::factory('qwt_ywmcfg')->where('bid', '=', $this->bid)->where('key', '=', 'sharelogo')->find()->id;
        $result['users'] = ORM::factory('qwt_ywmweixin')->where('ct','=',1)->where('nickname','!=','')->order_by('id','desc')->limit(10)->find_all();
        $result['all'] = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',15)->find()->hbnum;//购买总数
        $result['used'] = ORM::factory('qwt_ywmkl')->where('bid','=',$this->bid)->where('used','>',0)->count_all();//普通已使用的口令数
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->title = '获得红包';
        $this->template->content = View::factory($view)
                ->bind('subscribe',$user->qrcodes->subscribe)
                ->bind('item',$item)
                ->bind('user', $user)
                ->bind('bid', $this->bid)
                ->bind('jsapi', $jsapi)
                ->bind('result', $result)
                ->bind('hb_code', $_GET['hb_code'])
                ->bind('config', $config);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_ywm$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

     //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
      private function hongbao($config, $openid, $bid=1, $money, $wx=''){
        $Appid = 'wx31d7e1641cdeaf00';
        $config['mchid'] = 1275904301;
        $config['apikey'] = 'r1IPFhzbD14cO4gRsJXC2fas9WexVadF';
        if (!$wx) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');

            $options['appid'] = 'wx31d7e1641cdeaf00';
            $options['appsecret'] = '592511efdf1e6444951fd158155f8cee';
            $wx = new Wechat($options);
        }
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //商户号
        $data["wxappid"] = $Appid;

        $data["re_openid"] = $openid;
        $data["total_amount"] = $money;
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "[{$config['logoname']}]送红包"; //活动名称
        //$data["nick_name"] = $config['name']; //提供方名称
        $data["send_name"] = $config['logoname']; //红包发送者名称
        $data["wishing"] = $config['logoname'].'恭喜发财！'; //红包祝福
        $data["remark"] = '运气太好啦！'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        Kohana::$log->add('$qwt_ywm',print_r($data, true));
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));
        Kohana::$log->add('$qwt_ywm',print_r($data, true));

        $postXml = $wx->xml_encode($data);
        Kohana::$log->add('hbbpostXml:',print_r($postXml, true));

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        // Kohana::$log->add('weixin:hongbao:fail:'.$config['name'], print_r($data, true));
        // Kohana::$log->add('weixin:hongbaopartnerkey:fail:'.$config['name'], $config['partnerkey']);
        if ($bid == 6) Kohana::$log->add('qwt_hbb:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);
        Kohana::$log->add('$qwt_ywm_resultXml:',print_r($resultXml,true));

        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        Kohana::$log->add('$qwt_ywm_response:',print_r($response,true));
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['mch_billno']=$mch_billno;
        Kohana::$log->add('$qwt_ywm:',print_r($result, true) );
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/ywm/cert/cert.pem";
        $key_file = DOCROOT."qwt/ywm/cert/key.pem";
        //$rootca_file=DOCROOT."ywm/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_ywmcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_ywmfile_cert')->find();
        $file_key = ORM::factory('qwt_ywmcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_ywmfile_key')->find();
        //$file_rootca = ORM::factory('qwt_ywmcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_ywmfile_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        // if (!file_exists(rootca_file)) {
        //     @mkdir(dirname($rootca_file));
        //     @file_put_contents($rootca_file, $file_rootca->pic);
        // }

        // Kohana::$log->add("weixin_fxb:$bid:curl_post_ssl:cert_file", $cert_file);

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

        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $rootca_file);// CA根证书（用来验证的网站证书是否是CA颁布）

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
