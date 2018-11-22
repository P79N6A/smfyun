<?php defined('SYSPATH') or die('No direct script access.');

class Controller_sns extends Controller_Base {
    public $template = 'weixin/sns/tpl/fftpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    var $baseurl = 'http://dd.smfyun.com/sns/';
    var $we;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "sns";
        parent::before();
        Request::instance()->redirect('/sns/dateout');
        // if (Request::instance()->action == 'prize_get') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'index_oauth') return;
        if (Request::instance()->action == 'getappointment') return;
        if (Request::instance()->action == 'appointment') return;
        if (Request::instance()->action == 'share') return;
        if (Request::instance()->action == 'msg') return;
        // if (Request::instance()->action == 'address') return;
        // if (Request::instance()->action == 'join') return;
        //if (Request::instance()->action == 'index_oauth') return;
        $_SESSION =& Session::instance()->as_array();

        if (!$_SESSION['sns']['openid']) {
            // die('请重新进入页面');
            Request::instance()->redirect('/sns/msg/请重新进入页面哦');
        }
        // if(time()>0){//时间过了

        // }
        $this->config = $_SESSION['sns']['config'];
        $this->openid = $_SESSION['sns']['openid'];
        $this->bid = $_SESSION['sns']['bid'];
        $this->uid = $_SESSION['sns']['uid'];

        if ($_GET['debug']) print_r($_SESSION['sns']);

        //只能通过微信打开
        // if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['snsa']['bid']) die('请通过微信访问。');
    }

    public function after() {
        parent::after();
        $cnum = ORM::factory('sns_cfg')->where('bid','=',1)->where('key','=','count')->find();
        $bobao = array();
        $config = array();
        $config['name'][0] = '鱼儿';
        $config['name'][1] = '花开半夏';
        $config['name'][2] = '笨小孩';
        $config['name'][3] = 'Angelamama';
        $config['name'][4] = 'Alex';
        $config['name'][5] = '冰冰有理';
        $config['name'][6] = '奔跑的Cand*';
        $config['name'][7] = '请叫我小师妹';
        $config['name'][8] = 'ZYY';
        $config['name'][9] = 'T-T鹏鹏';
        $config['name'][10] = '鱼儿';
        $config['name'][11] = '花开半夏';
        $config['name'][12] = '笨小孩';
        $config['name'][13] = 'Angelamama';
        $config['name'][14] = 'Alex';
        $config['name'][15] = '冰冰有理';
        $config['name'][16] = '奔跑的Cand*';
        $config['name'][17] = '请叫我小师妹';
        $config['name'][18] = 'ZYY';
        $config['name'][19] = 'T-T鹏鹏';

        // for ($i=0; $i < 20; $i++) {
        //     $str = $config['name'][$i];
        //     $bobao[$i]['name'] = $this->hidetext($str,4);
        //     $bobao[$i]['gift'] = $this->config['gift'][$i];
        // }

        if($cnum->value&&$cnum->value>=20){
            $orders = ORM::factory('sns_order')->where('bid','=',1)->where('qid','!=','')->order_by('id', 'DESC')->limit(20)->find_all();
            foreach ($orders as $k => $v) {
                $str = $v->qrcode->nickname;
                $bobao[$k]['name'] = $this->hidetext($str,3);
                // $bobao[$k]['name'] = substr($v->qrcode->nickname,0,strlen($v->qrcode->nickname)-1).'*';
                $bobao[$k]['gift'] = $v->item->name;
            }
        }else{
            $num = ORM::factory('sns_order')->where('bid','=',1)->count_all();//统计多少条order
            if($num>=20){
                $orders = ORM::factory('sns_order')->where('bid','=',1)->order_by('lastupdate', 'DESC')->limit(20)->find_all();
                $cfg = ORM::factory('sns_cfg');
                $cfg->setCfg(1, 'cnum', $num);
                foreach ($orders as $k => $v) {
                    $str = $v->qrcode->nickname;
                    $bobao[$k]['name'] = $this->hidetext($str,3);
                    // $bobao[$k]['name'] = substr($v->qrcode->nickname,0,strlen($v->qrcode->nickname)-1).'*';
                    $bobao[$k]['gift'] = $v->item->name;
                }
            }else{
                $orders = ORM::factory('sns_order')->where('bid','=',1)->order_by('lastupdate', 'DESC')->limit(20)->find_all();
                foreach ($orders as $k => $v) {
                    $str = $v->qrcode->nickname;
                    $bobao[$k]['name'] = $this->hidetext($str,3);
                    // $bobao[$k]['name'] = substr($v->qrcode->nickname,0,strlen($v->qrcode->nickname)-1).'*';
                    $bobao[$k]['gift'] = $v->item->name;
                }
                if($num == 0){
                    $a = 0;
                }else{
                    $a = 1;
                }
                for ($i=0; $i < 20-$num; $i++) {
                    $str = $config['name'][$i];
                    $bobao[$k+$a+$i]['name'] = $this->hidetext($str,3);
                    $bobao[$k+$a+$i]['gift'] = $this->config['gift'][$i];
                }
            }
        }
        for($p=0;$p<20;$p++){
            $bobao[$p]['name'] = str_replace("\"","?",$bobao[$p]['name']);
            $bobao[$p]['name'] = str_replace('"',"?",$bobao[$p]['name']);
            $bobao[$p]['name'] = str_replace('.',"?",$bobao[$p]['name']);
            $bobao[$p]['name'] = str_replace('$',"?",$bobao[$p]['name']);
            $bobao[$p]['name'] = str_replace('+',"?",$bobao[$p]['name']);
        }
        // echo '<pre>';
        // var_dump($bobao);
        // exit;
        // array_merge($config['sns'], (array)$cache);
        View::bind_global('bobao', $bobao);
        View::bind_global('config', $this->config);
    }
    public function hidetext($str,$bit){
       $continue = 1;
       $i = 1;
       $a = 1;
       while ( $continue <= $bit) {
         if(ord(substr($str, $i-1, $i))>127){//第个是中文
           $i = $i+3;
           $a = 3;
         }else{
           $i = $i+1;
           $a = 1;
         }
         // echo $i.'<br>';
         $continue++;
       }
       $num = strlen($str);
       if($i<$num)$str = substr($str, 0,$i-1).'*';
       return $str;
    }
    public function action_appointment(){//预约回调
        var_dump($_GET['ret']);
        var_dump($_GET['retmsg']);
        if($_GET['fopenid']){
            Request::instance()->redirect('/sns/index_oauth/1/'.$_GET['fopenid']);
        }else{
            Request::instance()->redirect('/sns/index_oauth/1/');
        }
    }
    public function action_coupon(){//卡券发放回调
        var_dump($_GET['ret']);
        var_dump($_GET['retmsg']);
        if(!$_GET['oid']){
            die('参数错误');
        }
        if($_GET['retmsg']==0){
            if($_GET['ret']==147){//此人是黑名单 发送失败
                $oid = $_GET['oid'];
                if($_GET['oid']){
                    $order = ORM::factory('sns_order')->where('id','=',$_GET['oid'])->find();
                    $user = ORM::factory('sns_qrcode')->where('id','=',$order->qid)->find();
                    $user->locked = 1;
                    $user->save();
                    Request::instance()->redirect('/sns/net');
                }
            }
            $qid = $this->uid;
            $order = ORM::factory('sns_order')->where('bid','=',$this->bid)->where('qid','=',$qid)->where('id','=',$_GET['oid'])->find();
            if(!$order->id){
                die('参数错误');
            }
            $type = $_GET['type'];
            echo 'oid:'.$_GET['oid'].'<br>';
            echo 'type:'.$type;
            // exit;
            if($order->type==1){//团长情况
                $order->bid = 1;
                $order->type = 1;
                $order->lastupdate = time();
                $order->flag = 1;
                $order->ret = $_GET['ret'];
                $order->save();
                $user = ORM::factory('sns_qrcode')->where('bid','=',$this->bid)->where('id','=',$qid)->find();
                $user->oid1 = $order->id;
                $user->save();
                $num=ORM::factory('sns_order')->where('bid','=',$this->bid)->where('goodid','=',$order->goodid)->count_all();
                $item=ORM::factory('sns_item')->where('id','=',$order->goodid)->find();
                $item->residue=$item->num-$num;
                $item->save();
                Request::instance()->redirect('/sns/join');
            }
            if($order->type==0){//团员情况
                $order->bid = 1;
                $order->type = 0;
                $order->flag = 1;
                $order->ret = $_GET['ret'];
                $order->lastupdate = time();
                $order->save();
                $user = ORM::factory('sns_qrcode')->where('bid','=',$this->bid)->where('id','=',$qid)->find();
                $user->oid2 = $order->id;
                $user->save();
                $num=ORM::factory('sns_order')->where('bid','=',$this->bid)->where('goodid','=',$order->goodid)->count_all();
                $item=ORM::factory('sns_item')->where('id','=',$order->goodid)->find();
                $item->residue=$item->num-$num;
                $item->save();
                Request::instance()->redirect('/sns/yijiaru');
            }
        }
    }
    public function action_getappointment(){//发起预约
        $activeid = 572102;
        if($_GET['fopenid']){
            $url = 'http://wq.jd.com/bases/yuyue/partneractive?activeId='.$activeid.'&returl=http://langsha.nanrenwa.net/sns/appointment?fopenid='.$_GET['fopenid'];
            // $url = 'http://wq.jd.com/bases/yuyue/partneractive?activeId=572102&returl=http://langsha.nanrenwa.net/sns/appointment?fopenid=1111111';
        }else{
            $url = 'http://wq.jd.com/bases/yuyue/partneractive?activeId='.$activeid.'&returl=http://langsha.nanrenwa.net/sns/appointment';
        }
        Request::instance()->redirect($url);
    }
    public function action_getcoupon(){//发放卡券
        //type 0代表团员 1代表团长
        //iid代表中的商品id
        //good代表卡券id
        //qid代表用户id
        $qid = $_GET['qid'];
        $iid = $_GET['iid'];
        $good = $_GET['good'];
        $type = $_GET['type'];
        $oid = $_GET['oid'];
        // echo $qid.'<br>';
        // echo $iid.'<br>';
        // echo $good.'<br>';
        // echo $type.'<br>';
        // exit;
        $gift[0] = 'vender_b5d2cb73843742388162ee9230889cbf';//99大礼包
        $gift[1] = 'vender_61781d41789c43e38cdc8e77e4493b65';//丝袜一双
        $gift[2] = 'vender_9397b4f2fa35457a939ab3c6f4294dcf';//无门槛10元
        $biz = 'langsha0323';
        $aes = '3nt5y*@ug-n9rh#t';
        $iv = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $coupon = $gift[$good];//京东券key
        $uuid = $qid;
        $pinlimitcnt = 1;
        $time = time();
        $data = $biz.'&'.$coupon.'&'.$time.'&'.$uuid.'&'.$pinlimitcnt;
        $data = str_pad($data, strlen($data)+16-strlen($data)%16, "\0");
        $cert = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $aes, $data, MCRYPT_MODE_CBC, $iv));
        $cert = urlencode($cert);
        $url = 'http://wq.jd.com/activeapi/opensendcouponapi?biz='.$biz.'&cert='.$cert.'&returl=http://langsha.nanrenwa.net/sns/coupon?oid='.$oid.'&type='.$type;
        // echo $url;
        // exit;
        Request::instance()->redirect($url);
    }
    //Oauth 入口
    public function action_index_oauth($bid,$fopenid='',$url='form') {
        if(time()>1492099199){//时间过了
                    header("Location: http://langsha.nanrenwa.net/sns/dateout");
                }
        $this->template = 'tpl/blank';
        self::before();
        $config = ORM::factory('sns_cfg')->getCfg($bid,1);
            require Kohana::find_file('vendor', 'weixin/wechat.class');
            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

            $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
            if (!$_GET['callback']) $callback_url .= "{$split}callback=1";

            $we = new Wechat(array('appid'=>$config['appid'], 'appsecret'=>$config['appsecret']));

            if (!$_GET['callback']) {
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            } else {
                $token = $we->getOauthAccessToken();
                $userinfo = $we->getOauthUserinfo($token['access_token'], $token['openid']);
                $openid = $userinfo['openid'];
                // var_dump($userinfo);
                // exit;
            }

            if (!$openid) $_SESSION['sns'] = NULL;

            if ($openid) {
                $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                if(!$userobj->id){
                    $userobj->values($userinfo);
                    if($userinfo['headimgurl']=='') {
                        $userobj->headimgurl = 'http://langsha.nanrenwa.net/sns/img/logo.jpeg';
                    }
                    $userobj->bid = $bid;
                    $userobj->ip = Request::$client_ip;
                    $userobj->save();
                }
                $_SESSION =& Session::instance()->as_array();
                $_SESSION['sns']['config'] = $config;
                $_SESSION['sns']['openid'] = $openid;
                $_SESSION['sns']['bid'] = $bid;
                $_SESSION['sns']['uid'] = $userobj->id;
                if($userobj->locked==1){//黑名单
                    Request::instance()->redirect('/sns/net');
                    header("Location: http://langsha.nanrenwa.net/sns/net");
                }

                    $item1 = ORM::factory('sns_item')->where('id','=',1)->find();
                    $item2 = ORM::factory('sns_item')->where('id','=',2)->find();
                    $item3 = ORM::factory('sns_item')->where('id','=',3)->find();
                    $item4 = ORM::factory('sns_item')->where('id','=',4)->find();
                    $item5 = ORM::factory('sns_item')->where('id','=',5)->find();
                if($item1->residue<=0&&$item2->residue<=0&&$item3->residue<=0&&$item4->residue<=0&&$item5->residue<=0){//团长情况没有奖品了
                    header("Location: http://langsha.nanrenwa.net/sns/giftout");
                }
            }

        $view = 'weixin/sns/start';
        if($fopenid!=''){//通过分享链接进来
            $fuser = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fopenid)->find();//找出上级
            $count = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('gid', '=', $fuser->flag)->count_all();
            if($fopenid==$openid){//能分享到朋友圈肯定是团长 自己点自己 查看自己的团
                $type = 'join';
            }else{//判断当前团是否满员
                if($userobj->gid){//已经是别的团的团员
                    $flag = 'a';
                }else{
                    $flag = 'b';
                }
                if($userobj->flag){//是别的团长了还点 查看当前fopenid的团， 查看我的美腿团
                    $flag = $flag.'a';
                }else{//我要成为新团长
                    $flag = $flag.'b';
                }
                // if($fuser->groups->count==10){//当前团满员
                if($count>=9){//当前团满员
                    $flag = $flag.'a';
                }else{//未满员
                    $flag = $flag.'b';
                }
                $type = $flag;
            }
            if($fuser->flag==$userobj->gid){
                $type = 'yijiaru';
            }
        }else{//自己点击按钮进来
            // $fopenid = $userobj->openid;
            if($userobj->flag){//是团长 组建
                $type = 'join';
            }else{
                if($userobj->gid){//已经加入 不是团长，只是团员，显示当前团的情况  可以新建团
                    $type = 'yijiaru';
                }else{
                    $type = 'group';//现在创建
                }
            }
        }
        $this->template->content = View::factory($view)->bind('type', $type)->bind('fopenid', $fopenid);
        // }
        // Request::instance()->redirect('/sns/'.$url.'/'.$userobj->openid);
    }
    public function action_msg($text){
        $this->template = 'tpl/blank';
        self::before();
        $a = $text;
        $view = 'weixin/sns/msg';
        $this->template->content = View::factory($view)->bind('text',$a);
    }
    public function action_share(){
        if($_GET['ShareApp']==1){//分享给朋友
            $config = ORM::factory('sns_cfg')->where('bid','=',1)->where('key','=','ShareApp')->find();
            $config->value = $config->value+1;
            $config->save();
        }
        if($_GET['Timeline']==1){//朋友圈
            $config = ORM::factory('sns_cfg')->where('bid','=',1)->where('key','=','Timeline')->find();
            $config->value = $config->value+1;
            $config->save();
        }
        exit;
    }
    public function action_net(){
        $this->template = 'tpl/blank';
        self::before();
        $view = 'weixin/sns/net';
        $config = $this->config;
        $this->template->content = View::factory($view);
    }
    public function action_dateout(){
        $this->template = 'tpl/blank';
        self::before();
        $view = 'weixin/sns/dateout';
        $config = $this->config;
        $this->template->content = View::factory($view);
    }
    public function action_giftout(){
        $this->template = 'tpl/blank';
        self::before();
        $view = 'weixin/sns/giftout';
        $config = $this->config;
        $this->template->content = View::factory($view);
    }
    // 是否参团  是否是团长  是否满员
    //已经参团
    public function action_aaa(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $view = 'weixin/sns/aaa';
        $this->template->content = View::factory($view)->bind('leader', $leader)->bind('config', $config)->bind('jsapi', $jsapi);
    }
    public function action_aab(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $view = 'weixin/sns/aaa';
        $this->template->content = View::factory($view)->bind('leader', $leader)->bind('config', $config)->bind('jsapi', $jsapi);
    }
    public function action_aba(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $view = 'weixin/sns/aba';
        $this->template->content = View::factory($view)->bind('leader', $leader)->bind('config', $config)->bind('jsapi', $jsapi);
    }
    public function action_abb(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $view = 'weixin/sns/aba';
        $this->template->content = View::factory($view)->bind('leader', $leader)->bind('config', $config)->bind('jsapi', $jsapi);
    }
    //没有参团
    public function action_baa(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();
        $view = 'weixin/sns/baa';
        $this->template->content = View::factory($view)->bind('leader', $leader)->bind('config', $config)->bind('jsapi', $jsapi)->bind('followers', $followers);
    }
    public function action_bab(){//两种 有人或没人
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();

        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find()->as_array();
        $userobj['iscount'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->count_all();

        if($userobj['iscount']==9){//满员了
            Request::instance()->redirect('/sns/msg/对不起，该团已经满了');
            // $view = "weixin/sns/join/full";
            // $userobj['full'] = 1;
            // $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->order_by('jointime', 'asc')->find_all();
        }else if($userobj['iscount']==0){//一个都没有
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/bab/join";
        }else{//有人参团
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/bab/has";
            $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();
        }
        $this->template->content = View::factory($view)->bind('user', $userobj)->bind('config', $config)->bind('followers', $followers)->bind('jsapi', $jsapi)->bind('leader', $leader);
    }
    public function action_bba(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();

        $view = "weixin/sns/bba";

        $this->template->content = View::factory($view)->bind('user', $userobj)->bind('config', $config)->bind('followers', $followers)->bind('jsapi', $jsapi)->bind('leader', $leader);
    }
    public function action_bbb(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();

        $config = $this->config;
        $fopenid = $_GET['fopenid'];
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find();
        $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();

        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $fopenid)->find()->as_array();
        $userobj['iscount'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->count_all();

        if($userobj['iscount']==9){//满员了
            Request::instance()->redirect('/sns/msg/对不起，该团已经满了');
            // $view = "weixin/sns/join/full";
            // $userobj['full'] = 1;
            // $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->order_by('jointime', 'asc')->find_all();
        }else if($userobj['iscount']==0){//一个都没有
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/bbb/join";
        }else{//有人参团
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/bbb/has";
            $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();
        }
        $this->template->content = View::factory($view)->bind('user', $userobj)->bind('config', $config)->bind('followers', $followers)->bind('jsapi', $jsapi)->bind('leader', $leader);
    }
    public function action_add(){
        $item5 = ORM::factory('sns_item')->where('id','=',5)->find();
        if($item5->residue<=0){//团员没有库存了
            Request::instance()->redirect('/sns/giftout');
        }
        $fopenid = $_GET['fopenid'];
        $openid = $this->openid;
        $leader = ORM::factory('sns_qrcode')->where('bid','=',$this->bid)->where('openid','=',$fopenid)->find();
        $user = ORM::factory('sns_qrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
        $fuser = ORM::factory('sns_qrcode')->where('bid','=',$this->bid)->where('openid','=',$fopenid)->find();
        $count = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $fuser->flag)->count_all();
        if($user->gid){
            // die('对不起，您已经加入了团哦');
            Request::instance()->redirect('/sns/msg/对不起，您已经加入了团哦');
        }
        if($count>=9){//满员了
            // Request::instance()->redirect('/sns/index_oauth/1?fopenid='.$fopenid);
            // exit;
            // die('对不起，该团已经满了');
            Request::instance()->redirect('/sns/msg/对不起，该团已经满了');
        }
        $group = ORM::factory('sns_group')->where('qid','=',$leader->id)->find();
        $group->qid = $leader->id;
        $group->count = $group->count+1;
        $group->starttime = time();
        $group->lastupdate = time();
        $group->save();

        $order=ORM::factory('sns_order')->where('type','=',0)->where('bid','=',$this->bid)->where('qid','=',$user->id)->find();
        $order->bid = $this->bid;
        $order->type = 0;
        $order->goodid = 5;
        $order->flag = 2;
        $order->qid = $user->id;
        $order->save();

        $user->gid = $group->id;
        $user->gider = 1;//这个人第一次 需要蒙层
        $user->jointime = time();
        $user->lastupdate = time();
        $user->save();
        Request::instance()->redirect('/sns/getcoupon?qid='.$user->id.'&oid='.$order->id.'&good=2&type=0');
    }
    public function action_prize_get(){
        $openid = $this->openid;
        $bid=$this->bid;
        //$bid=1;
        //$openid='oap88uFXCRJfq5QLlde6vFtxTl0w';
        $user = ORM::factory('sns_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $order=ORM::factory('sns_order')->where('type','=',1)->where('bid','=',$bid)->where('qid','=',$user->id)->find();
        if($user->locked==1){
            Request::instance()->redirect('/sns/net');
        }
        if($order->id){
            // die ('已经领过一次奖了');
            Request::instance()->redirect('/sns/msg/对不起，您已经领过一次奖品了');
        }
        $result=DB::query(Database::SELECT,"SELECT SUM(residue) as num_all from sns_items where bid = $bid")->execute()->as_array();
        $num_all=$result[0]['num_all'];
        echo $num_all."<br>";
        if($num_all==0){
            // die ('奖品已送完');
            Request::instance()->redirect('/sns/giftout');
        }
        $item1=ORM::factory('sns_item')->where('id','=',1)->find();
        $item2=ORM::factory('sns_item')->where('id','=',2)->find();
        $item3=ORM::factory('sns_item')->where('id','=',3)->find();
        $item4=ORM::factory('sns_item')->where('id','=',4)->find();
        $prize=mt_rand(1,$num_all);
        if ($prize<=$item1->residue){
            $order->bid = $bid;
            $order->qid = $user->id;
            $order->type = 1;
            $order->goodid = 1;
            $order->flag = 2;//2代表有订单还没填写地址;1代表完全领了
            $order->save();
            $num = ORM::factory('sns_order')->where('bid','=',$bid)->where('goodid','=',1)->count_all();
            $item1->residue = $item1->residue-$num;
            $item1->save();
            $user->oid1 = $order->id;
            $user->save();
            Request::instance()->redirect('/sns/join');
        }elseif ($prize<=$item1->residue+$item2->residue){
            Request::instance()->redirect('/sns/join');
            $order->bid = $bid;
            $order->qid = $user->id;
            $order->type = 1;
            $order->goodid = 2;
            $order->flag = 2;//2代表有订单还没填写地址;1代表完全领了
            $order->save();
            $num = ORM::factory('sns_order')->where('bid','=',$bid)->where('goodid','=',2)->count_all();
            $item2->residue = $item2->residue-$num;
            $item2->save();
            $user->oid1 = $order->id;
            $user->save();
        }elseif ($prize<=$item1->residue+$item2->residue+$item3->residue) {
            $order->bid = $bid;
            $order->qid = $user->id;
            $order->type = 1;
            $order->goodid = 3;
            $order->flag = 1;//2代表有订单还没填写地址;1代表完全领了
            $order->save();
            // $user->oid1 = $order->id;
            // $user->save();
            $good=0;
            // Request::instance()->redirect('/sns/getcoupon/'.$user->id.'/'.$good.'/'.$item3->id.'/'.'1');
            Request::instance()->redirect('/sns/getcoupon?qid='.$user->id.'&good='.$good.'&oid='.$order->id.'&type=1');
        }else{
            $order->bid = $bid;
            $order->qid = $user->id;
            $order->type = 1;
            $order->goodid = 4;
            $order->flag = 1;//2代表有订单还没填写地址;1代表完全领了
            $order->save();
            // $user->oid1 = $order->id;
            // $user->save();
            $good=1;
            // Request::instance()->redirect('/sns/getcoupon/'.$user->id.'/'.$good.'/'.$item4->id.'/'.'1');
            Request::instance()->redirect('/sns/getcoupon?qid='.$user->id.'&good='.$good.'&oid='.$order->id.'&type=1');
        }
    }
    public function action_address(){
        $this->template = 'tpl/blank';
        self::before();
        $view = 'weixin/sns/address';
        $user = ORM::factory('sns_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        $order = ORM::factory('sns_order')->where('bid','=',$this->bid)->where('id','=',$user->oid1)->find();
        if(!$order->id){
            Request::instance()->redirect('/sns/msg/对不起，您没有对应订单哦');
        }
        if($_POST['data']){
            $order->goodid = $_POST['data']['goodid'];
            $order->flag = 1;
            $order->name = $_POST['data']['name'];
            $order->tel = $_POST['data']['tel'];
            $order->pro = $_POST['data']['pro'];
            $order->address = $_POST['data']['address'];
            $order->lastupdate = time();
            $order->save();
            echo 'success';
            exit;
        }
        $this->template->content = View::factory($view)->bind('goodid', $order->goodid);
    }
    public function action_group(){//未加入
        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/sns/group";
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template->content = View::factory($view)->bind('config', $this->config)->bind('jsapi', $jsapi);

    }
    public function action_join(){//创建者视角
        // $this->openid = 'oap88uBUF5VzVbgyHakW_5A0jwyQ';
        // $this->bid = 1;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();
        $config = ORM::factory('sns_cfg')->getCfg($this->bid,1);
        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if(!$userobj->flag){//新建立的
            $item1 = ORM::factory('sns_item')->where('id','=',1)->find();
            $item2 = ORM::factory('sns_item')->where('id','=',2)->find();
            $item3 = ORM::factory('sns_item')->where('id','=',3)->find();
            $item4 = ORM::factory('sns_item')->where('id','=',4)->find();
            if($item1->residue<=0&&$item2->residue<=0&&$item3->residue<=0&&$item4->residue<=0){//团长情况没有奖品了
                Request::instance()->redirect('/sns/giftout');
            }
            $group = ORM::factory('sns_group')->where('qid', '=', $userobj->id)->find();
            $group->qid = $userobj->id;
            $group->count = 1;
            $group->starttime = time();
            $group->lastupdate = time();
            $group->save();
            $userobj->flag = $group->id;
            $userobj->content0 = $config['group'][0][rand(0,9)];
            $userobj->jointime = time();
            $userobj->save();
        }

        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $userobj['iscount'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->count_all();

        if($userobj['iscount']==9){//满员了
            $view = "weixin/sns/join/full";
            $userobj['full'] = 1;
            $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->order_by('jointime', 'asc')->find_all();
            if($userobj['oid1']){//已经拆了
                $view = "weixin/sns/join/yichai";
                $order = ORM::factory('sns_order')->where('bid', '=', $this->bid)->where('id', '=', $userobj['oid1'])->find();
                if($order->goodid==1||$order->goodid==2){//实物
                    if($order->flag==2){
                        $btnname='点击领取';
                        $pic = '/sns/img/gift/'.$order->goodid.'.png';
                        $url = '/sns/address';
                    }
                    if($order->flag==1){
                        $pic = '/sns/img/gift/'.$order->goodid.'.png';
                        $view = "weixin/sns/join/ylq";
                    }
                }
                if($order->goodid==3||$order->goodid==4){//优惠券
                    $btnname='立即使用';
                    $pic = '/sns/img/gift/'.$order->goodid.'.png';
                    $url = $config['lsjd'];
                }
            }
        }else if($userobj['iscount']==0){//一个都没有
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/join/join";
        }else{//有人参团
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/join/has";
            $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $userobj['flag'])->order_by('jointime', 'asc')->find_all();
        }
        $this->template->content = View::factory($view)->bind('user', $userobj)->bind('config', $config)->bind('followers', $followers)->bind('jsapi', $jsapi)->bind('url', $url)->bind('btnname', $btnname)->bind('pic',$pic)->bind('first',$first);
    }
    public function action_yijiaru(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $jsapi = $we->getJsSign($callback_url);

        $this->template = 'tpl/blank';
        self::before();
        $config = ORM::factory('sns_cfg')->getCfg($this->bid,1);
        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $leader = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('flag', '=', $userobj->gid)->find();
        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $userobj['iscount'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->count_all();
        if($userobj['iscount']==9){//满员了
            $view = "weixin/sns/yijiaru/full";
            $userobj['full'] = 1;
            $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();
        }else if($userobj['iscount']==0){//一个都没有
            // $userobj['count'] = 9-$userobj['iscount'];
            // $view = "weixin/sns/yijiaru/join";
            Request::instance()->redirect('/sns/msg/请退出重新进入页面哦');
        }else{//有人参团
            $userobj['count'] = 9-$userobj['iscount'];
            $view = "weixin/sns/yijiaru/has";
            $followers = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('gid', '=', $leader->flag)->order_by('jointime', 'asc')->find_all();
        }
        if($userobj['flag']){
            $content = '查看我的美腿团';
            $url = '/sns/join';
        }else{
            $content = '我也要成为新团长';
            $url = '/sns/group';
        }
        $this->template->content = View::factory($view)->bind('user', $userobj)->bind('config', $config)->bind('followers', $followers)->bind('jsapi', $jsapi)->bind('leader', $leader)->bind('coupon', $coupon)->bind('content',$content)->bind('url',$url);
    }
    public function action_form($openid) {
        $view = "weixin/sns/form";
        $this->template = 'tpl/blank';
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        if($_POST['form']['name']&&$_POST['form']['shop']&&$_POST['form']['tel']&&$_POST['form']['memo']){
            $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
            $userobj->lv = 2;
            $userobj->name = $_POST['form']['name'];
            $userobj->tel = $_POST['form']['tel'];
            $userobj->shop = $_POST['form']['shop'];
            $userobj->bz = $_POST['form']['memo'];
            $userobj->save();
            $result['content'] = '申请提交成功，请耐心等待';
        }
        $userobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv==1){
            $result['content'] = '恭喜您的申请已经通过，成功获得分销资格，<br>赶紧点击菜单生成海报开始哦~';
        }
        if($userobj->lv==2){
            $result['content'] = '申请提交成功，请耐心等待';
        }
        if($userobj->lv==3){
            $result['content'] = '对不起，您的审核未被通过或已被取消，请联系管理员';
        }
        $result['lv'] = $userobj->lv;

        $this->template->content = View::factory($view)->bind('result', $result);
    }
    //默认页面
    public function action_home() {
        $view = "weixin/sns/home";

        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('sns_qrcode', $this->uid);

        //可转出金额
        $result['money_now'] = $userobj->scores->select(array('SUM("score")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
        //当前收益
        $result['score'] = $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
        //预计收益
        $userobj->money = $result['money'] = $userobj->scores->select(array('SUM("score")', 'total_score'))->where('score', '>', 0)->find()->total_score;
        //累计付款金额
        $userobj->paid = $result['paid'] = $userobj->scores->select(array('SUM("money")', 'money_paid'))->where('type', 'IN', array(2,3))->find()->money_paid;
         $result['aaa']=$this->config['title5'];
        if ($userobj->id) $userobj->save();

        $this->template->title = '我的奖励';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //转出
    public function action_money($out=0, $cksum='') {
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $view = "weixin/sns/money";
        $userobj = ORM::factory('sns_qrcode', $this->uid);

        $title5=$this->config['title5'];
        $result['aaa']=$this->config['title5'];

        //可转出金额
        $result['money_now'] = $userobj->scores->select(array('SUM("score")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
        //已结算金额
        $result['money_paid'] = $userobj->scores->select(array('SUM("score")', 'money_paid'))->where('paydate', '<', time())->where('type', 'IN', array(1,2,3))->find()->money_paid;
        //待结算金额
        $result['money_nopaid'] = $userobj->scores->select(array('SUM("score")', 'money_nopaid'))->where('paydate', '>=', time())->where('type', 'IN', array(1,2,3))->find()->money_nopaid;

        //判断转出条件
        $result['money_flag'] = false;
        $result['money_out'] = $this->config['money_out'];

        if($title5=='收益'){
            $title5="元";
        }

        if ($result['money_now']>=number_format($result['money_out']/100, 2)) {
            //判断成功购买金额
            if($userobj->lv==1){
                $result['money_flag'] = true;
            }else if($userobj->lv==0){
                $result['money_out_msg'] = '对不起您还未提交审核';
            }else if($userobj->lv==2){
                $result['money_out_msg'] = '对不起您的申请还在审核中';
            }else if($userobj->lv==3){
                $result['money_out_msg'] = '对不起您的申请已经被管理员取消，请联系管理员';
            }
        } else {
            $result['money_out_msg'] = '满'. number_format($result['money_out']/100, 2) .$title5.'即可转出。';
        }

        //转出
        //只能提取整数
        $MONEY = floor($result['money_now']);
        $md5 = md5($this->openid.$this->config['appsecret'].$_GET['time'].$_GET['rand']);
        // echo "cks:$cksum<br />md5:$md5";
        if ( ($cksum == $md5) && (time() - $_GET['time'] < 600) ) $cksum_flag = true;

        if ($out == 1 && $cksum_flag == true && ($MONEY >= $this->config['money_out']/100) ) {
            if (!$this->config['partnerid'] || !$this->config['partnerkey']) die('ERRROR: Partnerid 和 Partnerkey 未配置，不能自动转出，请联系管理员！');

            $this->we = $we = new Wechat($this->config);
            $result_m = $this->sendMoney($userobj, $MONEY*100);

            if ($result_m['result_code'] == 'SUCCESS') {
                $userobj->scores->scoreOut($userobj, 4, $MONEY);

                $cksum = md5($userobj->openid.$this->config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $this->bid .'?url=score/3&cksum='. $cksum .'&openid='. base64_encode($userobj->openid);

                //发消息通知
                $fmsg = "申请转出{$MONEY} 元成功！请到微信钱包中查收。";
                if ($this->config['msg_money_tpl']) {
                    $this->sendMoneyMessage($userobj->openid, '转出成功', -$MONEY, $userobj->score, $url);
                } else {
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $userobj->openid;
                    $msg['text']['content'] = $fmsg;
                    $we->sendCustomMessage($msg);
                }

                $result['ok']++;
                $result['alert'] = '转出成功!';
                return $this->action_msg("转出成功，请到微信钱包中查收。", 'suc');

            } else {
                // print_r($result);exit;
                Kohana::$log->add("weixin_sns:$bid:money", print_r($result, true));
                $result['alert'] = '转出失败：'.$result_m['return_msg'];
            }
        }

        $this->template->title = '结算中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //积分排行榜
    public function action_top2() {
        $mem = Cache::instance('memcache');
        $view = "weixin/sns/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '业绩排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('sns_qrcode', $this->uid)->as_array();

        $rankkey = "sns:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "sns:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //收益明细
    public function action_score($type=0) {
        $view = "weixin/sns/scores";
        $userobj = ORM::factory('sns_qrcode', $this->uid);

        $title = array('收支明细', '待结算', '已结算', '转出记录');

        $this->template->title = $title[$type];
        $this->template->content = View::factory($view)->bind('scores', $scores);

        $scores = $userobj->scores;

        if ($type == 1) $scores = $scores->where('type', 'IN', array(1,2,3))->where('paydate', '>', time());
        if ($type == 2) $scores = $scores->where('type', 'IN', array(1,2,3))->where('paydate', '<=', time());
        if ($type == 3) $scores = $scores->where('type', '=', 4);

        $scores = $scores->order_by('lastupdate', 'DESC')->limit(500)->find_all();
    }

    //订单明细
    public function action_orders() {
        $view = "weixin/sns/orders";
        $userobj = ORM::factory('sns_qrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->scores->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }

    public function action_order($tid) {
        $view = "weixin/sns/order";

        $order = ORM::factory('sns_trade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }

    //排行榜
    public function action_top() {
        $view = 'weixin/sns/top';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('sns_qrcode', $this->uid);
        $top = $this->config['rank_sns'] ? $this->config['rank_sns'] : 10;

        $result['rank'] = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('lv','=',1)->where('paid', '>', $user->paid)->count_all()+1;

        $usersobj = ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->order_by('paid', 'DESC')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }

    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/sns/customer';
        $this->template->title = '累计客户';
        $this->template->content = View::factory($view)
        ->bind('config',$this->config)
        ->bind('mycustomers',$totlecustomer)//绑定所有用户（1）级
        ->bind('result', $result)
        ->bind('totlenum',$totlenum)
        ->bind('page',$pages)
        ->bind('pagenum',$page)
        ->bind('newadd',$newadd);
        //$this->template->content = View::factory($view)->bind('result', $result);

        $user = ORM::factory('sns_qrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM sns_qrcodes WHERE fopenid='$user->openid' and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM sns_qrcodes WHERE fopenid='$user->openid'")->execute()->as_array();

           if($newadd=='month')
           {
            $customer=ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month);
           }
           else
             $customer=ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/sns/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('sns_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();


    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "sns_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('$postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('sns', '111');
        Kohana::$log->add('$result11', print_r($result11, true));
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $appid =$result11['app_id'];
        //$id=$result11['id'];
        $msg=$result11['msg'];
        $kdt_id=$result11['kdt_id'];
        $status=$result11['status'];
        //Kohana::$log->add('$status', print_r($status, true));
        Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = ORM::factory('sns_login')->where('shopid','=',$kdt_id)->find()->id;
        $this->bid=$bid;
        $this->config = $config = ORM::factory('sns_cfg')->getCfg($bid);
        //Kohana::$log->add('$config', print_r($config, true));
        $this->we = $we = new Wechat($config);
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        $this->access_token=ORM::factory('sns_login')->where('id', '=', $bid)->find()->access_token;
        if($this->access_token){
            $this->client =$client= new KdtApiOauthClient();
        }else{
            Kohana::$log->add("sns:$bid:bname", print_r('有赞参数未填', true));
        }

        if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
            $posttid=urldecode($msg);
            $jsona=json_decode($posttid,true);
            Kohana::$log->add("sns:$bid", print_r($jsona, true));
            $trade=$jsona['trade'];
            if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                $this->tradeImport($trade, $bid, $client, $we, $config);
            } else {
                $this->tradeImport($trade, $bid, $client, $we, $config);
            }
        }
    }

    private function tradeImport($trade, $bid, $client, $we, $config) {
        // print_r($trade);exit;
        $tid = $trade['tid'];
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));
        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_BUYER_SIGNED', 'TRADE_CLOSED', 'TRADE_CLOSED_BY_USER');

        if (!in_array($trade['status'], $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($trade['status'], true));
        $sns_trade = ORM::factory('sns_trade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($sns_trade->id) {

            //更新订单状态
            if ($sns_trade->status != $trade['status']) {
                $sns_trade->status = $trade['status'];
                $sns_trade->save();

                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($trade['status'] == 'TRADE_CLOSED') ORM::factory('sns_score')->where('tid', '=', $sns_trade->id)->delete_all();
            if ($trade['status'] == 'TRADE_CLOSED_BY_USER') ORM::factory('sns_score')->where('tid', '=', $sns_trade->id)->delete_all();
            if ($trade['refund_state'] != 'NO_REFUND') ORM::factory('sns_score')->where('tid', '=', $sns_trade->id)->delete_all();
            //订单完成金额 达到一定值 进行升级
            $method = 'kdt.users.weixin.follower.get';
            $params = [
                'user_id'=>$trade['weixin_user_id'],
            ];

            $result = $client->post($this->access_token,$method, $params);
            Kohana::$log->add('result', print_r($result, true));
            $userinfo = $result['response']['user'];
            $fuser = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
            $ffuser = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            $all_payment = ORM::factory('sns_trade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
            $skus = DB::query(Database::SELECT,"SELECT * FROM sns_skus WHERE bid=$bid and `money`<=$all_payment")->execute()->as_array();
            Kohana::$log->add('all_payment', $all_payment);
            Kohana::$log->add('skus', print_r($skus,true));
            Kohana::$log->add('sid', $skus[count($skus)-1]['id']);
            $ffuser->sid = $skus[count($skus)-1]['id'];
            $ffuser->save();
            //echo "$tid pass.\n";
            return;
        }
        Kohana::$log->add('11111', '111111');
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['type'], true));
        if ($trade['type'] != 'FIXED') return;

        //男人袜不参与火种用户的商品

        Kohana::$log->add('payment', print_r($trade['payment'], true));
        //付款金额为 0
        if ($trade['payment'] <= 0) return;
        Kohana::$log->add('8888', '8888');

        $method = 'kdt.users.weixin.follower.get';
        $params = [
            'user_id'=>$trade['weixin_user_id'],
        ];

        $result = $client->post($this->access_token,$method, $params);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        //$userinfo = $this->youzanid2OpenID($trade['weixin_user_id'], $client);
        // print_r($userinfo);

        //只处理有下线的订单
        $sns_qrcode = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($sns_qrcode->id, true));
        if (!$sns_qrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }

        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['pay_time']);

        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $sns_qrcode->subscribe : $sns_qrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            //echo "$tid Time pass.\n";
            return;
        }

        $trade['qid'] = $sns_qrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        //计算返利金额
        Kohana::$log->add('8888', '8888');
        //某些特殊情况订单改价问题
         $ordersumpayment = 0;
         $trade['adjust_fee']['pay_change'];//订单改价
         $trade['adjust_fee']['post_change'];//邮费改价
         foreach ($trade['orders'] as $order) {
            $ordersumpayment = $ordersumpayment+$order['payment'];//计算出 各个商品花费价格
         }
        $money  = $trade['money'] = $trade['payment'] - $trade['post_fee'];//实付金额-改价后的邮费
        // echo 'postfee'.$trade['post_fee'].'<br>';
        // echo 'postch'.$trade['adjust_fee']['post_change'].'<br>';
        // var_dump($moeny);
        Kohana::$log->add('money', print_r($money,true));
        $average=$money/($money+$trade['discount_fee']);//权重
        // echo 'average';
        // var_dump($average);
        Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid){//有一级
            $rank=1;
            $ffuser = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            $trade['fopenid'] = $ffuser->openid;
        }
             $money1 = 0;
             // echo 'tradeorders';
             // var_dump($trade['orders']);
             Kohana::$log->add('trade[orders]', print_r($trade['orders'],true));
             foreach ($trade['orders'] as $order) {
                $tempmoney=($order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment))*$average;
                Kohana::$log->add('tempmoney', print_r($tempmoney,true));
                Kohana::$log->add('orderpayment', print_r($orderpayment,true));
                Kohana::$log->add('ordersumpayment', print_r($ordersumpayment,true));
                // echo 'tempmoney';
                // var_dump($tempmoney);
                $goodid=$order['num_iid'];
                $goodidcof=ORM::factory('sns_setgood')->where('goodid','=',$goodid)->find();
                //按照分销商等级 设置比例
                if($ffuser->sid!=0){
                    $config['money1'] = ORM::factory('sns_sku')->where('bid','=',$bid)->where('id','=',$ffuser->sid)->find()->scale;
                    Kohana::$log->add('scale', print_r($config['money1'],true));
                }
                if($goodidcof->id)//用户单独配置了
                {
                    if($rank>=1) $money1=$money1+$tempmoney*$goodidcof->money1/100;
                }
                else//没有配置就默认的数据
                {
                    if($rank>=1) $money1 =$money1+$tempmoney * $config['money1'] / 100; //一级
                }

             }

        if($ffuser->lv==1){
            $money1 = $trade['money1'] = number_format($money1, 2); //一级
        }
        //订单完成金额 达到一定值 进行升级
        $all_payment = ORM::factory('sns_trade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
        if($all_payment){
            $skus = DB::query(Database::SELECT,"SELECT * FROM sns_skus WHERE bid=$bid and `money`<=$all_payment")->execute()->as_array();
            Kohana::$log->add('all_payment', $all_payment);
            Kohana::$log->add('skus', print_r($skus,true));
            Kohana::$log->add('sid', $skus[count($skus)-1]['id']);
            if($skus[count($skus)-1]['id']){
                $ffuser->sid = $skus[count($skus)-1]['id'];
                $ffuser->save();
            }
        }
        $sns_trade->values($trade);
        $sns_trade->save();

        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['num_iid'];
            $num=$order['num'];
            $price=$order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment);
            $sns_order=ORM::factory('sns_order')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$sns_order->id)//跳过已导入的order
            {
                $sns_order->bid=$bid;
                $sns_order->tid=$tid;
                $sns_order->goodid=$goodid;
                $sns_order->title=$title;
                $sns_order->num=$num;
                $sns_order->price=$price;
                $sns_order->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('sns_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('sns_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('sns_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money0', print_r($money0, true));

        //订单上线返利
        if ($money1 > 0) {
            $fuser = ORM::factory('sns_qrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $sns_qrcode->fopenid)->find();
            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                $fuser->scores->scoreIn($fuser, 2, $money1, $sns_qrcode->id, $sns_trade->id);

                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

                $msg['text']['content'] = "您推荐的{$config['title1']}「{$sns_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money1 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->score, $url);
                else
                    $we_result = $we->sendCustomMessage($msg);
            }
        }

        //TODO:更多级别返利

        //echo "$tid done.\n";
        flush();ob_flush();
    }

    // private function youzanid2OpenID($fansid, $client) {
    //     $method = 'kdt.users.weixin.follower.get';
    //     $params = array('user_id' => $fansid,);

    //     $result = $client->post($this->access_token,$method, $params);
    //     $user = $result['response']['user'];
    //     return $user;
    // }

    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔'.$this->config['title5'].'！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_sns:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_sns:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
    }

    //账户余额通知模板：openid、类型、收益、总金额、网址
    private function sendMoneyMessage($openid, $title, $money, $total, $url) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_money_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = $title;
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = '提现到账户';

        $tplmsg['data']['keyword4']['value'] = '-'.number_format($money, 2);
        $tplmsg['data']['keyword4']['color'] = '#FF0000';

        $tplmsg['data']['keyword5']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword5']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = '时间：'.date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';

        // Kohana::$log->add("weixin_sns:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
    }
    private function hongbao($config, $openid, $we='', $bid=1, $money)
    {
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $we = new Wechat($config);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //支付商户号
        $data["wxappid"] = $config['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        // $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        // $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);//支付安全验证函数（核心函数）
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        return $result;//hash数组
    }
    //企业付款：https://pay.weixin.qq.com/wiki/doc/api/mch_pay.php?chapter=14_2
    private function sendMoney($userobj, $money) {
        $config = $this->config;
        $openid = $userobj->openid;

        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $this->we = $we = new Wechat($config);
        }

        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["mch_appid"] = $config['appid'];
        $data["mchid"] = $config['partnerid']; //商户号
        $data["nonce_str"] = $this->we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.$config['title5'].'转出';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->we->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_sns:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_sns:hongbaoresult', print_r($result, true));
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."sns/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."sns/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('sns_cfg')->where('bid', '=', $bid)->where('key', '=', 'sns_file_cert')->find();
        $file_key = ORM::factory('sns_cfg')->where('bid', '=', $bid)->where('key', '=', 'sns_file_key')->find();

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

        // Kohana::$log->add("weixin_sns:$bid:curl_post_ssl:cert_file", $cert_file);

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
            echo curl_error($ch);
            curl_close($ch);
            return false;
        }

    }

}
