<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwthby extends Controller_Base {
    public $template = 'weixin/smfyun/hby/tpl/blank';

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
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'bind_user') return;
        if (Request::instance()->action == 'bind_qr') return;
        if (Request::instance()->action == 'sns_login') return;
        if (Request::instance()->action == 'api_ticket') return;
        // if (!$_GET['openid']) {
        //     if (!$_SESSION['qwthby']['bid']) die('页面已过期。请重新点击相应菜单');
        //     if (!$_SESSION['qwthby']['openid']) die('Access Deined..请重新点击相应菜单');
        // }
        if (!$_SESSION['qwthby']['bid']) die('页面已过期。请重新点击相应菜单');
        $this->config = $_SESSION['qwthby']['config'];
        $this->openid = $_SESSION['qwthby']['openid'];
        $this->bid = $_SESSION['qwthby']['bid'];
        $this->uid = $_SESSION['qwthby']['uid'];
        $this->myopenid = $_SESSION['qwthby']['myopenid'];


        $sname = ORM::factory('qwt_hbycfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        if ($_GET['debug']) print_r($_SESSION['hby']);
        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['fxba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('qwt_hbyqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);
        View::bind_global('scorename', $this->scorename);
        $this->template->user = $user;
        parent::after();
    }
    public function action_bind_qr($bid,$lid){//生成二维码图片
        if(!$bid) die('no bid');
        if(!$lid) die('no lid');
        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
        $qrurl =  'http://'.$_SERVER['HTTP_HOST'].'/qwthby/bind_user/'.$bid.'/'.$lid;
        QRcode::png($qrurl,false,'L','6','2');
        header('Content-type: image/png');
        exit;
    }
    public function action_bind_user($bid,$lid,$app='hby'){//移动端绑定用户
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getUserInfo($token['openid']);
            $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$qr_user->id){
                $qr_user->bid = $bid;
                $qr_user->values($userinfo);
            }else{//更新头像  跑路的不更新
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $qr_user->subscribe_time = $userinfo['subscribe_time'];
                    $qr_user->jointime = time();
                    $qr_user->nickname = $userinfo['nickname'];
                    $qr_user->headimgurl = $userinfo['headimgurl'];
                }
                $qr_user->subscribe = $userinfo['subscribe'];
            }
            $qr_user->save();

            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$user->id){
                $user->qid = $qr_user->id;
                $user->bid = $bid;
                $user->values($userinfo);
            }else{//更新头像
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $user->subscribe_time = $userinfo['subscribe_time'];
                    $user->jointime = time();
                    $user->nickname = $userinfo['nickname'];
                    $user->headimgurl = $userinfo['headimgurl'];
                }
                $user->subscribe = $userinfo['subscribe'];
            }
            if($user->subscribe==0){
                $result['error'] = '请先关注公众号再绑定管理员。';
            }else{
                $luser = ORM::factory('qwt_hbylogin')->where('id','=',$lid)->find();
                if($luser->id){
                    $luser->wx_bind = $user->id;
                    $luser->save();
                }else{
                    $result['error'] = '请先添加用户再绑定管理员。';
                }
            }
            $user->save();

            $view = "weixin/smfyun/hby/bind";
            $this->template->title = '绑定管理员';
            $this->template->content = View::factory($view)
                ->bind('result', $result)
                ->bind('luser', $luser)
                ->bind('bid', $this->bid);
        }
    }
    public function action_sns_login($bid,$app='hby'){//管理员授权登陆 获取openid
        $_SESSION =& Session::instance()->as_array();
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            Kohana::$log->add("qwt_hby_token_data:$bid", print_r($token,true));
            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getUserInfo($token['openid']);
            $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$qr_user->id){
                $qr_user->bid = $bid;
                $qr_user->values($userinfo);
            }else{//更新头像  跑路的不更新
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $qr_user->subscribe_time = $userinfo['subscribe_time'];
                    $qr_user->jointime = time();
                    $qr_user->nickname = $userinfo['nickname'];
                    $qr_user->headimgurl = $userinfo['headimgurl'];
                }
                $qr_user->subscribe = $userinfo['subscribe'];
            }
            $qr_user->save();

            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$user->id){
                $user->qid = $qr_user->id;
                $user->bid = $bid;
                $user->values($userinfo);
            }else{//更新头像
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $user->subscribe_time = $userinfo['subscribe_time'];
                    $user->jointime = time();
                    $user->nickname = $userinfo['nickname'];
                    $user->headimgurl = $userinfo['headimgurl'];
                }
                $user->subscribe = $userinfo['subscribe'];
            }
            $user->save();

            $_SESSION['qwt'.$app]['config'] = ORM::factory('qwt_'.$app.'cfg')->getCfg($bid,1);
            $_SESSION['qwt'.$app]['admin_openid'] = $user->openid;
            $_SESSION['qwt'.$app]['bid'] = $bid;
            $_SESSION['qwt'.$app]['admin_uid'] = $user->id;
            $_SESSION['qwt'.$app]['sid'] = $shop->shopid;

            $url = 'login';
            Request::instance()->redirect('/qwt'.$app.'/'.$url);
        }
    }
    public function action_test(){
        $this->template->content = View::factory('weixin/smfyun/hby/hb');
    }
    public function action_login(){//移动端登陆
        $bid = $this->bid;
        $openid = $_SESSION['qwthby']['admin_openid'];
        $config = $_SESSION['qwthby']['config'];
        $uid = $_SESSION['qwthby']['admin_uid'];

        // $login = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('wx_bind','=',$uid)->find();
        // if(!$login->id){
        //     $result['error'] = '当前用户不是管理员。';
        // }

        if($_POST['name']&&$_POST['passwd']){
            $luser = ORM::factory('qwt_hbylogin')->where('status','=',1)->where('bid','=',$bid)->where('account','=',$_POST['name'])->where('password','=',$_POST['passwd'])->find();
            if($luser->id>0){//如果绑定了微信
                if($luser->wx_bind==0){
                    $_SESSION['qwthby']['admin'] = 1;
                    $_SESSION['qwthby']['bid'] = $bid;
                    $_SESSION['qwthby']['admin_lid'] = $luser->id;
                    Request::instance()->redirect('/qwthby/show_qr');
                }
                if($luser->wx_bind==$uid){
                    $_SESSION['qwthby']['admin'] = 1;
                    $_SESSION['qwthby']['bid'] = $bid;
                    $_SESSION['qwthby']['admin_lid'] = $luser->id;
                    Request::instance()->redirect('/qwthby/show_qr');
                }else{
                    $result['error'] = '账户或密码与当前管理员不相符。';
                }
            }else{
                $result['error'] = '账户或密码错误。';
            }
        }
        $view = 'weixin/smfyun/hby/login';
        $this->template->title = '登陆';
        $this->template->content = View::factory($view)
            ->bind('result', $result)
            ->bind('login', $login)
            ->bind('bid', $this->bid);
    }
    private function genKouling($length = 16){
        // 密码字符集，可任意添加你需要的字符
        $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $password = '';
        for($i = 0; $i < $length; $i++){
          // 将 $length 个数组元素连接成字符串
          $password .= $chars[rand(0,25)];
        }
        return $password;
    }
    public function action_show_qr(){//移动端展示qr
        // var_dump($_SESSION);
        // if($_POST['show_qr']==1){
        //     if($_SESSION['qwthby']['admin']==1&&$_SESSION['qwthby']['admin_lid']){
        //         //登陆状态
        //         $code = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('used','=',0)->order_by('id','ASC')->find();
        //         $qr_img = 'http://'.$_SERVER['HTTP_HOST'].'/qwthby/qr/'.$code->code;
        //         $str = ['a','b','c','d','e','f','g','h','i','j','k'];//10
        //         $no = 'NO.'.$code->id.$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)];
        //         $result['status'] = 'ok';
        //         $result['qr_img'] = $qr_img;
        //         $result['no'] = $no;

        //         $code->from_lid = $_SESSION['qwthby']['admin_lid'];
        //         $code->save();
        //     }else{
        //         //未登录
        //         // Request::instance()->redirect('/qwthby/sns_login/'.$this->bid);
        //         $result['status'] = 'fail';
        //         $result['error'] = '登陆已过期，请重新登录！';
        //     }
        //     echo json_encode($result);
        //     exit;
        // }

        if($_SESSION['qwthby']['admin']==1&&$_SESSION['qwthby']['admin_lid']){
            //每分钟生成红包码阈值
            $mem = Cache::instance('memcache');
            $mem_key = 'hby_'.$this->bid.'_'.$_SESSION['qwthby']['admin_lid'];
            $mem_val = $mem->get($mem_key);
            if($mem_val>=60){
                $result['error'] = '每分钟生成红包码达到阈值：60，请稍后再试！';
            }
            $mem->set($mem_key, $mem_val+1, 60);

            //登陆状态
            // $code = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('used','=',0)->order_by('id','ASC')->find();
            $buycodenum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->hbnum;
            $hasused = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('ct','=',1)->count_all();//普通已使用的口令数

            $lid = ORM::factory('qwt_hbylogin')->where('id','=',$_SESSION['qwthby']['admin_lid'])->find();//对应门店
            $rid = ORM::factory('qwt_hbyrule')->where('id','=',$lid->rid)->find();
            $rconfig = ORM::factory('qwt_hbyrcfg')->getCfg($lid->id);//门店config
            $rules = $rid->as_array();
            $config = array_merge($rconfig,$rules);
            // $config = array_merge($config,$new_arr);
            if($buycodenum<=$hasused){
                $result['error'] = '剩余可使用红包码不足';
            }
            if($result['error']){
                // $result['error'] = '剩余可使用红包码不足';
                //有错误不进入
            }else{
                //插入新code
                $code = ORM::factory('qwt_hbykl');
                $kouling = $this->genKouling();

                $code->bid = $this->bid;
                $code->code = $kouling;
                $code->from_lid = $_SESSION['qwthby']['admin_lid'];
                $code->save();

                $qr_img = 'http://'.$_SERVER['HTTP_HOST'].'/qwthby/qr/'.$code->code;
                $str = ['a','b','c','d','e','f','g','h','i','j','k'];//10
                $no = 'NO.'.$code->id.$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)].$str[rand(0,10)];
                $result['status'] = 'ok';
                $result['qr_img'] = $qr_img;
                $result['no'] = $no;
            }
            $result['logo'] = $config['logo'];
            $view = 'weixin/smfyun/hby/hbqr';
            $this->template->title = '红包雨';
            $this->template->content = View::factory($view)
                ->bind('result', $result);
        }else{
            //未登录
            Request::instance()->redirect('/qwthby/sns_login/'.$this->bid);
        }
    }
    private function type($orders){
        if($orders->ct==0){
            return '用户未分享到朋友圈';
        }
        if($orders->ct==1){
            switch($orders->status){
              case 'COUPON SENDING':
                  $statu="卡券待领取";
                  break;
              case 'COUPON GET':
                  $statu="卡券已领取";
                  break;
              case 'SENDING':
                  $statu="发放中";
                  break;
              case 'SENT':
                  $statu="已发放待领取";
                  break;
              case 'FAILED':
                  $statu="发放失败";
                  break;
              case 'RECEIVED':
                  $statu="已领取";
                  break;
              case 'REFUND':
                  $statu="长时间未领取已退款";
                  break;
              default:
                  $statu=$orders->error;
            }
            return $statu;
        }
        if($orders->ct==2){
            return $orders->error;
        }
        if($orders->ct==3){
            return '关注后自动下发';
        }
    }
    public function action_kl_detail(){
        if($_POST['klid']){
            if($_SESSION['qwthby']['admin']==1&&$_SESSION['qwthby']['admin_lid']){
                $kls = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('from_lid','=',$_SESSION['qwthby']['admin_lid'])->where('id','<',$_POST['klid'])->order_by('id','desc')->limit(10)->find_all();
                foreach ($kls as $k => $v) {
                    $wx = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('kouling','=',$v->code)->find();
                    $result['users'][$k]['hb_id'] = $v->id;
                    $result['users'][$k]['tyoe'] = $wx->id;
                    $result['users'][$k]['headimgurl'] = $wx->headimgurl;
                    $result['users'][$k]['nickname'] = $wx->nickname;
                    if($wx->couponname||$wx->money>0){
                        if($wx->couponname){
                            $result['users'][$k]['type'] = '微信卡券';
                        }else{
                            $result['users'][$k]['type'] = '微信红包';
                        }
                        $result['users'][$k]['money'] = $wx->money>0?number_format($wx->money/100,2).'元':$wx->couponname;
                    }else{
                        $result['users'][$k]['type'] = '暂无';
                        $result['users'][$k]['money'] = '暂无';
                    }
                    $result['users'][$k]['createtime'] = date('Y-m-d H:i:s',$v->createtime);
                    $result['users'][$k]['sendtime'] = $wx->sendtime?date('Y-m-d H:i:s',$wx->sendtime):'无';
                    $result['users'][$k]['status'] = $this->type($wx);

                    $result['klid'] = $v->id;
                }
                $result['next_kl'] = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('from_lid','=',$_SESSION['qwthby']['admin_lid'])->where('id','<',$result['klid'])->find()->id;
            }else{
                //未登录
                $result['error'] = '登陆已过期，请重新登录！';
            }
            echo json_encode($result);
            exit;
        }
        if($_SESSION['qwthby']['admin']==1&&$_SESSION['qwthby']['admin_lid']){
            $kls = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('from_lid','=',$_SESSION['qwthby']['admin_lid'])->order_by('id','desc')->limit(10)->find_all();
            //红包id 头像 昵称 红包码生成时间 红包发送时间 红包发送状态  红包金额
            foreach ($kls as $k => $v) {
                $wx = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('kouling','=',$v->code)->find();
                $result['users'][$k]['hb_id'] = $v->id;
                $result['users'][$k]['headimgurl'] = $wx->headimgurl;
                $result['users'][$k]['nickname'] = $wx->nickname;
                if($wx->couponname||$wx->money>0){
                    if($wx->couponname){
                        $result['users'][$k]['type'] = '微信卡券';
                    }else{
                        $result['users'][$k]['type'] = '微信红包';
                    }
                    $result['users'][$k]['money'] = $wx->money>0?number_format($wx->money/100,2).'元':$wx->couponname;
                }else{
                    $result['users'][$k]['type'] = '暂无';
                    $result['users'][$k]['money'] = '暂无';
                }
                $result['users'][$k]['createtime'] = date('Y-m-d H:i:s',$v->createtime);
                $result['users'][$k]['sendtime'] = $wx->sendtime?date('Y-m-d H:i:s',$wx->sendtime):'无';
                $result['users'][$k]['status'] = $this->type($wx);

                $result['klid'] = $v->id;
            }
            $result['next_kl'] = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('from_lid','=',$_SESSION['qwthby']['admin_lid'])->where('id','<',$result['klid'])->find()->id;
            // var_dump($result);
            // exit;
            $view = 'weixin/smfyun/hby/kl_detail';
            $this->template->title = '生成记录';
            $this->template->content = View::factory($view)
                ->bind('result', $result);
        }else{
            //未登录
            Request::instance()->redirect('/qwthby/sns_login/'.$this->bid);
        }
    }
    public function action_qr($code){
        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
        $privateKey = "sjdksldkwospaisk";
        $iv = "wsldnsjwisqweskl";
        $data = $code;
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        $hb_code = urlencode(base64_encode($encrypted));

        $qrurl = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/user_snsapi_userinfo/'.$this->bid.'/hby/user_snsapi_base?hb_code='.$hb_code;
        QRcode::png($qrurl,false,'L','6','2');
        header('Content-type: image/png');
        exit;
    }
    public function action_shareurl(){
        if($_GET['bid']&&$_GET['openid']&&$_GET['hb_code']){
            $bid = $_GET['bid'];
            $openid = $_GET['openid'];
            //aes解密
            // $privateKey = "sjdksldkwospaisk";
            // $iv = "wsldnsjwisqweskl";
            // $encryptedData = base64_decode(urldecode($_GET['hb_code']));
            // $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
            // $_GET['hb_code'] = $decrypted;
            $code = $_GET['hb_code'];
            $login = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('code','=',$code)->find();
            $rconfig = ORM::factory('qwt_hbyrcfg')->getCfg($login->from_lid,1);//门店config
            // $config = ORM::factory('qwt_hbycfg')->getCfg($bid,1);
            if($rconfig['shareurl']){
                $weixin = ORM::factory('qwt_hbyweixin')->where('bid','=',$bid)->where('openid','=',$openid)->where('kouling','=',$code)->find();
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
                    $url = $rconfig['shareurl'];
                    echo $url;
                    Request::instance()->redirect($url);exit;
                }else{
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwthby/error/1';
                    Request::instance()->redirect($url);exit;
                }
            }else{
                $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwthby/error/2';
                Request::instance()->redirect($url);exit;
            }
        }else{
            $url = 'http://'.$_SERVER['HTTP_HOST'].'/qwthby/error/3';
            Request::instance()->redirect($url);exit;
        }
    }
    public function action_error($i){
        die('分享url未填写'.$i);
    }
    public function action_user_snsapi_base(){
        $url = 'qr_hb';
        $bid = $this->bid;
        $app = 'hby';
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
                Kohana::$log->add("hby_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
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
    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/qwt/ticket";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwthbybid:', 'ticket');//写入日志，可以删除
        $wx = new Wxoauth($this->bid,$options);

        $jsapi = $wx->getJsSign($callback_url);
        $ticket = $wx->getJsCardTicket();
        $sign = $wx->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory($view)
                ->bind('cardId', $cardId)
                ->bind('jsapi', $jsapi)
                ->bind('ticket', $ticket)
                ->bind('sign', $sign);
    }
    public function action_api_ticket($cardId,$bid) {
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/qwt/ticket";
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwthbybid:', 'ticket');//写入日志，可以删除
        $wx = new Wxoauth($this->bid,$options);

        $jsapi = $wx->getJsSign($callback_url);
        $ticket = $wx->getJsCardTicket();
        $sign = $wx->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory($view)
                ->bind('cardId', $cardId)
                ->bind('jsapi', $jsapi)
                ->bind('ticket', $ticket)
                ->bind('sign', $sign);
    }
    public function action_qr_hb(){
        $config = $this->config;
        $this->template = 'tpl/blank';
        self::before();
        // echo $this->bid.'<br>';
        // echo $this->openid.'<br>';
        // echo $this->myopenid.'<br>';
        // echo $_GET['hb_code'];
        $view = "weixin/smfyun/hby/hb";

        $user = ORM::factory('qwt_hbyqrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        // $qr_user = ORM::factory('qwt_qrcode')->where('id','=',$user->qid)->find();
        //ct=0代表扫了但是没有分享
        //ct=1代表红包下发成功
        //ct=2代表红包下发失败 会有错误提示
        //ct=3代表已经分享了 等待下发
        //used代表被扫了
        //aes解密
        $privateKey = "sjdksldkwospaisk";
        $iv = "wsldnsjwisqweskl";
        // $_GET['hb_code'] = urlencode('TEp+n179MP2a1UzOZg4Ozg==');
        // echo $_GET['hb_code'].'<br>';
        $encryptedData = base64_decode($_GET['hb_code']);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        // echo $decrypted;
        // exit;
        $_GET['hb_code'] = $decrypted;

        // $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyorders where bid=$this->bid and state = 1 ")->execute()->as_array();
        $buy = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $result['all'] = $buy->hby_money;

        $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyweixins where bid=$this->bid and ct = 1 ")->execute()->as_array();
        $result['used'] = number_format($money[0]['money']/100,2);
        if($result['all']<=0){
            $result['error'] = '账户余额不足！，请前往后台进行充值';
        }
        $buycodenum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->hbnum;
        $hasused = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('ct','=',1)->count_all();//普通已使用的口令数
        if($buycodenum<=$hasused){
            $result['error'] = '剩余可使用红包码不足';
        }
        if($_POST['hb_code']) $_GET['hb_code'] = $_POST['hb_code'];
        $kl = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('code','=',$_GET['hb_code'])->find();
        $lid = ORM::factory('qwt_hbylogin')->where('id','=',$kl->from_lid)->find();//对应门店
        $rid = ORM::factory('qwt_hbyrule')->where('id','=',$lid->rid)->find();
        $rconfig = ORM::factory('qwt_hbyrcfg')->getCfg($lid->id);//门店config
        $rules = $rid->as_array();
        $new_arr = array_merge($rconfig,$rules);
        $config = array_merge($config,$new_arr);

        if($_POST['hasshare']==1){
            $weixin = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('kouling','=',$_POST['hb_code'])->find();
            if(!$weixin->id) $result['error'] = '红包不存在或已被领取';
            if($weixin->ct==1) $result['error'] = '红包奖励已经领取了';
            if($weixin->ct==2) $result['error'] = '红包下发失败'.$weixin->error;
            if($weixin->ct==3) {
                $result['wx_qr_img'] = $this->bid;
                $result['error'] = '需要关注后才能领取红包哦';
            }
            if($result['error']){
                echo json_encode($result);
                exit;
            }
            // $weixin->ct = 3;
            if($config['issub']==1){//开启了关注后才能领取
                if($config['type']==1){//类型是红包
                    if($user->qrcodes->subscribe==1){//已经关注了
                        $money = rand($config['moneyMin'],$config['money']);
                        if($result['all']>=$money/100){
                           $hbresult = $this->hongbao($config,$user->myopenid,$this->bid,$money);
                            if($hbresult['result_code'] == 'SUCCESS'){//下发成功
                                $weixin->ct = 1;
                                $weixin->rule_name = $config['name'];
                                $weixin->money = $money;
                                $weixin->mch_billno=$hbresult['mch_billno'];

                                $buy->hby_money = $buy->hby_money-number_format($weixin->money/100,2,'.','');

                                $buser = ORM::factory('qwt_hbyorder');
                                $buser->money = -number_format($weixin->money/100,2,'.','');
                                $buser->bid = $weixin->bid;
                                $buser->wxid = $weixin->id;
                                $buser->state = 1;
                                $buser->left = $buy->hby_money;

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
                }
                if($config['type']==2){//类型是微信卡券
                    if($user->qrcodes->subscribe==1){//已经关注了
                        $weixin->ct = 1;
                        $weixin->rule_name = $config['name'];
                        $weixin->couponid = $config['couponid'];
                        $weixin->couponname = $config['couponname'];
                        $weixin->status = 'COUPON SENDING';

                        $result['content'] = '领取卡券';
                        $result['headimgurl'] = $user->headimgurl;
                        $result['nickname'] = $user->nickname;
                        $result['time'] = date('Y-m-d H:i:s',time());
                        $result['couponid'] = $config['couponid'];
                    }else{
                        $weixin->couponid = $config['couponid'];
                        $weixin->couponname = $config['couponname'];
                        $result['error'] = '需要关注后才能领取红包哦';
                        $result['wx_qr_img'] = $this->bid;
                    }
                }
            }else{
                if($config['type']==1){//类型是红包
                    $money = rand($config['moneyMin'],$config['money']);
                    if($result['all']>=$money/100){
                        $hbresult = $this->hongbao($config,$this->myopenid,$this->bid,$money);
                        if($hbresult['result_code'] == 'SUCCESS'){//下发成功
                            $weixin->ct = 1;
                            $weixin->rule_name = $config['name'];
                            $weixin->money = $money;
                            $weixin->mch_billno=$hbresult['mch_billno'];

                            $buy->hby_money = $buy->hby_money-number_format($weixin->money/100,2,'.','');

                            $buser = ORM::factory('qwt_hbyorder');
                            $buser->money = -number_format($weixin->money/100,2,'.','');
                            $buser->bid = $weixin->bid;
                            $buser->wxid = $weixin->id;
                            $buser->left = $buy->hby_money;
                            $buser->state = 1;

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
                }
                if($config['type']==2){//类型是微信卡券
                    if($user->qrcodes->subscribe==1){//已经关注了
                        $weixin->ct = 1;
                        $weixin->rule_name = $config['name'];
                        $weixin->couponid = $config['couponid'];
                        $weixin->couponname = $config['couponname'];
                        $weixin->status = 'COUPON SENDING';

                        $result['content'] = '领取卡券';
                        $result['headimgurl'] = $user->headimgurl;
                        $result['nickname'] = $user->nickname;
                        $result['time'] = date('Y-m-d H:i:s',time());
                        $result['couponid'] = $config['couponid'];
                    }else{
                        $weixin->couponid = $config['couponid'];
                        $weixin->couponname = $config['couponname'];
                        $result['error'] = '需要关注后才能领取红包哦';
                        $result['wx_qr_img'] = $this->bid;
                    }
                }
            }
            $weixin->sendtime = time();
            $weixin->save();
            echo json_encode($result);
            exit;
        }

        $weixin = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('kouling','=',$_GET['hb_code'])->find();
        $count = ORM::factory('qwt_hbyweixin')->where('bid','=',$this->bid)->where('qid','=',$user->id)->count_all();
        if(($count>=$config['ct']||$count>=10)&&!$weixin->id){//码没有被扫过
            $result['error'] = '已达到最大的扫码次数';
        }else{
            if($kl->id){
                if($kl->used>0){
                    if(!$weixin->id) $result['error'] = '该二维码已经被扫了';//不是本人
                    if($weixin->ct==1) {
                        if($weixin->money>0){
                            $result['error'] = number_format($weixin->money/100,2).'元红包已经下发了';
                        }else{
                            $result['error'] = "微信卡券：".$weixin->couponname.'已经下发！';
                        }
                    }
                    if($weixin->ct==2) $result['error'] = '红包下发失败'.$weixin->error;
                    if($weixin->ct==3) {
                        $result['error'] = '关注公众号后自动下发';
                        $result['wx_qr_img'] = $this->bid;
                    }
                }else{
                    $kl->used = time();
                    $kl->save();
                    $weixin->from_lid = $kl->from_lid;
                    $weixin->qid = $user->id;
                    $weixin->nickname = $user->nickname;
                    $weixin->headimgurl = $user->headimgurl;
                    $weixin->openid = $this->openid;
                    $weixin->kouling = $_GET['hb_code'];
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
        $result['bgpic'] = $config['bgpic'];
        $result['logo'] = $config['logo'];
        $result['sharelogo'] = $config['sharelogo'];
        $result['users'] = ORM::factory('qwt_hbyweixin')->where('bid', '=', $this->bid)->where('from_lid','=',$lid->id)->where('money','>',0)->where('ct','=',1)->where('nickname','!=','')->order_by('id','desc')->limit(10)->find_all();//只统计红包的
        // $result['all'] = ORM::factory('qwt_hbykl')->where('bid','=',$this->bid)->where('from_lid','=',$user->id)->count_all();//总数
        $result['used'] = ORM::factory('qwt_hbyweixin')->where('bid', '=', $this->bid)->where('from_lid','=',$lid->id)->where('ct','=',1)->count_all();//普通已使用的口令数
        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->title = '获得红包';
        $this->template->content = View::factory($view)
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
        $table = "qwt_hby$type";

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
        Kohana::$log->add('$qwt_hby',print_r($data, true));
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));
        Kohana::$log->add('$qwt_hby',print_r($data, true));

        $postXml = $wx->xml_encode($data);
        Kohana::$log->add('hbbpostXml:',print_r($postXml, true));

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        // Kohana::$log->add('weixin:hongbao:fail:'.$config['name'], print_r($data, true));
        // Kohana::$log->add('weixin:hongbaopartnerkey:fail:'.$config['name'], $config['partnerkey']);
        if ($bid == 6) Kohana::$log->add('qwt_hbb:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);
        Kohana::$log->add('$qwt_hby_resultXml:',print_r($resultXml,true));

        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        Kohana::$log->add('$qwt_hby_response:',print_r($response,true));
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        $result['mch_billno']=$mch_billno;
        Kohana::$log->add('$qwt_hby:',print_r($result, true) );
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/hby/cert/cert.pem";
        $key_file = DOCROOT."qwt/hby/cert/key.pem";
        //$rootca_file=DOCROOT."hby/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_hbycfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_cert')->find();
        $file_key = ORM::factory('qwt_hbycfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_key')->find();
        //$file_rootca = ORM::factory('qwt_hbycfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_hbyfile_rootca')->find();

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
