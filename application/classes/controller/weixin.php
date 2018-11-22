<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Weixin extends Controller_Base {
    public $template = 'tpl/mobile';

    //微信绑定
    public function action_bind($hash) {
        $this->template->title = __('绑定微信');

        $content = $this->template->content = View::factory('weixin/bind')
        ->bind('hash', $hash)
        ->bind('result', $result);

        //按手机号绑定
        if ($_POST['email'] && $_POST['tel']) {
            $user = ORM::factory('user')->where('email', '=', $_POST['email'])->find();

            if ($user->id != NULL) {
                $order = $user->orders;
                $order = $order->where('stats', 'IN', array($order::NEWW, $order::PAID, $order::NORMAL, $order::DONE, $order::UPGRADED))
                                    ->order_by('updated', 'DESC')->find();

                if ($order->tel == $_POST['tel']) {
                    $weixin = ORM::factory('weixin')->where('hash', '=', $hash)->find();
                    $weixin->hash = $hash;
                    $weixin->user_id = $user->id;
                    $weixin->save();

                    $result['msg'] = 'success';
                } else {
                    $result['data'] = '帐号信息不正确，请重试';
                }


            } else {
                $result['data'] = '帐号不存在，请重试';
            }

        }

        //按密码绑定
        if ($_POST['email'] && $_POST['password']) {
            $user = ORM::factory('user');

            if ($user->login($_POST)) {
                $weixin = ORM::factory('weixin')->where('hash', '=', $hash)->find();
                $weixin->hash = $hash;
                $weixin->user_id = Auth::instance()->get_user()->id;
                $weixin->save();

                $result['msg'] = 'success';

            } else {
                $result['data'] = '帐号不正确，请重试';
            }

        }

        if (Request::$is_ajax) die(json_encode($result));
    }

    //找回密码
    public function action_forgot() {
        $this->template->title = __('找回密码');
        $content = $this->template->content = View::factory('weixin/forgot');
    }

    //货到付款
    public function action_cod($hash='') {
        $this->template->title = __('微信快速下单');
        $content = $this->template->content = View::factory('weixin/cod')
        ->bind('hash', $hash)
        ->bind('result', $result);

        //内裤默认 3 条
        if ($_POST['plan_id'] == 13) $_POST['shipnum'] = 3;

        //加载已绑定用户资料
        if ($hash) {
            $user = ORM::factory('weixin')->where('hash', '=', $hash)->find()->user;
            if ($user) {
                $_REQUEST = $user->addresses->where('address', '<>', '')->order_by('lastupdate', 'desc')->find()->as_array();
                $_REQUEST['email'] = $user->email;
            }
        }

        if ($_POST['plan_id'] && $_POST['email'] && $_POST['tel']) {
            $_POST['plan_api'] = ORM::factory('plan', $_POST['plan_id'])->api;

            //已经订购过的用户
            $_POST['user_id'] = (int)ORM::factory('user')->where('email', '=', $_POST['email'])->find()->id;

            //自动绑定
            if ($_POST['user_id']) {
                $weixin = ORM::factory('weixin')->where('hash', '=', $hash)->find();
                $weixin->hash = $hash;
                $weixin->user_id = $_POST['user_id'];
                $weixin->save();
            }

            $order = ORM::factory('order');

            //获取价格
            $plans = Controller_Buy::getPlans();
            $plan = $plans[$_POST['plan_api']];

            //材质处理
            if ($_POST['plan_id'] == 13) {
                //内裤
                $_POST['sku_id'] = 10;
                $_POST['size_id'] = 0;
                $_REQUEST['socks'] = '条内裤';
            }

            //船袜
            if ($_POST['plan_id'] == 10) {
                $_POST['sku_id'] = 4;
                $_REQUEST['socks'] = '双船袜';
            }

            //防弹袜
            if ($_POST['plan_id'] == 16) {
                $_POST['sku_id'] = 16;
                $_POST['color_id'] = 1;
                $_POST['shipnum'] = 1;
            }

            //口罩
            if ($_POST['plan_id'] == 17) {
                $_POST['sku_id'] = 17;
                $_POST['color_id'] = 0;
                $_POST['shipnum'] = 10;
                $_REQUEST['socks'] = '只防雾霾口罩';
            }

            //价格计算
            if ($plan->price > 0 ) {
                $_POST['total'] = $plan->price;

                //标准套装
                $_POST['shipcount'] = $plan->shipcount;
                $_POST['shipnum'] = $plan->shipnum;
                $_POST['shipcyc'] = $plan->shipcyc;

            } else {

                //非标准套装
                //计算发货次数
                $_POST['shipcount'] = floor(365/$_POST['shipcyc']);

                $method = 'getPrice_'.$plan->api;
                $tmp = Controller_Buy::$method($_POST['shipcyc'], $_POST['shipnum']);
                $_POST['total'] = $tmp['price'];
            }

            $_POST['shipdesc'] = sprintf('%d %s，一次性发出', $_POST['shipnum'], $_REQUEST['socks'] ? $_REQUEST['socks'] : '双袜子');

            $order->values($_POST);
            $order->iscod = 1;
            $order->source = 'weixin';
            $order->memo2 = '微信订购，请电话和我确认尺码、颜色、数量';
            $result = $order->save();
        }

    }

    //静态页
    public function action_s($view) {
        $view = str_replace('.', '/', $view);

        $this->template->title = __('男人袜');
        $content = $this->template->content = View::factory("weixin/static/$view");
    }

    //SDK 测试
    public function action_test() {
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $opt = Kohana::config('weixin');

        echo '<pre>';

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'?callback=1';

        $we = new Wechat($opt);
        $we->checkAuth();

        if (!$_GET['callback']) {
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        } else {
            $result = $we->getOauthAccessToken();
            // var_dump($result);

            $userInfo = $we->getUserInfo($result['openid']);
            print_r($userInfo);
        }

        print_r($we);

        //$jsSign = $we->getJsSign($callback_url);
        //print_r($jsSign);

        // $userInfo = $we->getUserInfo($_GET['openid']);
        // print_r($userInfo);

        $data['touser'] = 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4';
        $data['msgtype'] = 'text';
        $data['text']['content'] = 'Hello World!'. time() .' <a href="http://www.nanrenwa.com/">男人袜首页</a>';
        $result = $we->sendCustomMessage($data);
        print_r($result);

        exit;
    }

    //语音红包
    public function action_voicehongbao($bid=1, $openid='') {
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        require Kohana::find_file('vendor', 'weixin/inc');
        require Kohana::find_file('vendor', "weixin/biz/$bid");

        $this->template->content = View::factory($view)
            ->bind('result', $result)
            ->bind('user', $user)
            ->bind('config', $config)
            ->bind('bid', $bid);

        //微信 SDK
        $we = new Wechat($config);
        $we->checkAuth();

        $user = $we->getUserInfo($openid);

        /*
            1. 判断是否关注
            2. 判断红包数量
            3. 判断有没有领过
        */

        $result['error'] = 1;

        $hb = ORM::factory('weixinhb')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        $hb->bid = $bid;
        $hb->openid = $openid;

        $hb->ip = Request::$client_ip;
        if ($fuser['openid']) $hb->fopenid = $fuser['openid'];

        if ($user['nickname']) {
            $hb->nickname = $user['nickname'];
            $hb->headimgurl = $user['headimgurl'];
        }

        if ($user['subscribe'] == 1) {

            //判断发多少红包了
            $count = ORM::factory('weixinhb')->where('bid', '=', $bid)->where('ct', '>', 0)->count_all();
            if (isset($config['max']) && $count <= $config['max']) {

                if ($fuser['openid'] && $fuser['openid'] != $user['openid']) $hb->fopenid = $fuser['openid'];

                $result['ct'] = $hb->ct;

                //判断有没有领过
                if ($hb->ct == 0) {
                    $saved = $hb->save();

                    //调用接口发红包
                    if ($saved) $result = $this->_hongbao($config, $openid, $we, $bid, $money);

                    //领成功
                    if ($saved && $result['return_code'] == 'SUCCESS') {
                        $hb->ct++;
                        $hb->money = $money;
                        $hb->lastupdate = time();

                        $result['error'] = 0;
                        $result['success'] = 1;
                    }

                    $result['ct'] = $hb->ct;
                    $hb->save();

                } else {
                    $result['have'] = 1;
                    $hb->save();
                }

            } else {
                $result['limit'] = 1;
            }

        } else {
            $result['needfollow'] = 1;
            $user['headimgurl'] = $fuser['headimgurl'];
            $hb->save();
        }

    }

   public function action_hongbao($bid=1) {
        $this->template = 'tpl/blank';
        self::before();

        $debug = isset($_GET['debug2']);

        require Kohana::find_file('vendor', 'weixin/wechat.class');
        require Kohana::find_file('vendor', 'weixin/inc');
        require Kohana::find_file('vendor', "weixin/biz/$bid");

        $view = "weixin/hongbao$bid";
        if (!file_exists( Kohana::find_file('views', $view) )) $view = "weixin/hongbao";

        $this->template->content = View::factory($view)
            ->bind('result', $result)
            ->bind('jsapi', $jsapi)
            ->bind('user', $user)
            ->bind('fuser', $fuser)
            ->bind('config', $config)
            ->bind('bid', $bid);

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) $callback_url .= "{$split}callback=1";

        //微信 SDK
        $we = new Wechat($config);
        $we->checkAuth();

        //Oauth
        if (!$_GET['callback']) {
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            Request::instance()->redirect($auth_url);
        } else {
            $token = $we->getOauthAccessToken();
            $user = $we->getUserInfo($token['openid']);

            //推荐人
            $fuser = $we->getUserInfo($_GET['fopenid']);
            if ($_GET['fopenid'] && ($fuser == false)) die('<h1>获取红包来源错误，请稍候再试！</h1>');

            $count = ORM::factory('weixinhb')->where('bid', '=', $bid)->count_all();
            $fopenid = ORM::factory('weixinhb')->where('bid', '=', $bid)->where('openid', '=', $user['openid'])->find()->fopenid;
            if (!$fopenid && !$fuser['openid'] && !$debug && $count > 10 && $user['nickname'] != '陈伯乐@男人袜') die('<h1>需要朋友推荐才可以领红包</h1>');
        }

        $jsapi = $we->getJsSign($callback_url);
        $openid = $token['openid'];

        /*
            1. 判断是否关注
            2. 判断红包数量
            3. 判断有没有领过
        */

        $result['error'] = 1;

        $hb = ORM::factory('weixinhb')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

        $hb->bid = $bid;
        $hb->openid = $openid;

        if (!$hb->openid) Kohana::$log->add('Weixin::NoOpenId', print_r($_REQUEST, true));

        $hb->ip = Request::$client_ip;
        if ($fuser['openid']) $hb->fopenid = $fuser['openid'];

        if ($user['nickname']) {
            $hb->nickname = $user['nickname'];
            $hb->headimgurl = $user['headimgurl'];
        }

        if ($user['subscribe'] == 1) {

            //判断发多少红包了
            $count = ORM::factory('weixinhb')->where('bid', '=', $bid)->where('ct', '>', 0)->count_all();
            if (isset($config['max']) && $count <= $config['max']) {

                if ($fuser['openid'] && $fuser['openid'] != $user['openid']) $hb->fopenid = $fuser['openid'];

                $result['ct'] = $hb->ct;

                //判断有没有领过
                if ($hb->ct == 0) {
                    $saved = $hb->save();

                    //调用接口发红包
                    if ($saved) $result = $this->_hongbao($config, $openid, $we, $bid, $money);

                    //领成功
                    if ($saved && $result['return_code'] == 'SUCCESS') {
                        $hb->ct++;
                        $hb->money = $money;
                        $hb->lastupdate = time();

                        $result['error'] = 0;
                        $result['success'] = 1;

                        //红包分裂
                        if ($hb->fopenid) {

                            //推荐人数量
                            $fcount = ORM::factory('weixinhb')->where('fopenid', '=', $hb->fopenid)->count_all();

                            //最多通知 4 条
                            if ($fcount <= 4) {
                                $fstr = '好友%s领取了您发的红包！';
                                if ($fcount == 4) $fstr = "%s领取了您发的红包。超过4位朋友领取红包，后续不再通知。<a href='{$callback_url}'>请点击查看</a>";

                                $data['touser'] = $hb->fopenid;
                                $data['msgtype'] = 'text';
                                $data['text']['content'] = sprintf($fstr, $user['nickname']);
                                $we->sendCustomMessage($data);

                                Kohana::$log->add('Weixin::sendCustomMessage', print_r($data, true));
                            }
                        }
                    }

                    $result['ct'] = $hb->ct;
                    $hb->save();

                } else {
                    $result['have'] = 1;
                    $hb->save();

                    //好友领取记录
                    $result['hbs'] = ORM::factory('weixinhb')->where('fopenid', '=', $openid)->find_all();
                }

            } else {
                $result['limit'] = 1;
            }

        } else {
            $result['needfollow'] = 1;
            $user['headimgurl'] = $fuser['headimgurl'];
            $hb->save();
        }

        // if ($debug)
        // print_r($result);exit;
    }

    private function _hongbao($config, $openid, $we='', $bid=1, $money=100) {
        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
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

        $data["act_name"] = "[{$config['name']}]新年送红包"; //活动名称
        $data["nick_name"] = $config['name']; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'祝您新年快乐！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["client_ip"] = '127.0.0.1';
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));

        $postXml = $we->xml_encode($data);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';

        $resultXml = curl_post_ssl($url, $postXml, 5, array(), $bid);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];

        return $result;
    }

}