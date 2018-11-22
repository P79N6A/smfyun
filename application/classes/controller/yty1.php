<?php defined('SYSPATH') or die('No direct script access.');

class Controller_yty extends Controller_Base {
    public $template = 'weixin/yty/tpl/fftpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    var $baseurl = 'http://yty.smfyun.com/yty/';
    var $we;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'images') return;
        //if (Request::instance()->action == 'index_oauth') return;
        $_SESSION =& Session::instance()->as_array();
        $this->config = $_SESSION['yty']['config'];
        $this->openid = $_SESSION['yty']['openid'];
        $this->bid = $_SESSION['yty']['bid'];
        $this->uid = $_SESSION['yty']['uid'];
        $this->access_token = $_SESSION['yty']['access_token'];
        if (Request::instance()->action == 'home'){
            $qrcode=ORM::factory('yty_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
            $agent=$qrcode->agent;
            $stock1=DB::query(Database::SELECT,"SELECT SUM(money) as stock from yty_stocks where qid = $qrcode->id and flag = 1")->execute()->as_array();
            $stock2=$stock1[0]['stock'];
            $agent->stock=$stock2;
            $agent->save();
        }
        if ($_GET['debug']) print_r($_SESSION['yty']);
        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['ytya']['bid']) die('请通过微信访问。');
    }

    public function after() {
        $user = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

         $firstchild=DB::query(Database::SELECT,"SELECT openid FROM yty_qrcodes WHERE fopenid='$this->openid'")->execute()->as_array();

        $customer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->order_by('paid', 'DESC');
        $customer = $customer->reset(FALSE);
        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM yty_qrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();
        $user['follows_month']=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('lv','=',1)->where('jointime','>=',$month)->count_all();
        $user['kehus_month']=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('lv','!=',1)->where('jointime','>=',$month)->count_all();
        $user['follows']=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('lv','=',1)->count_all();
        $user['kehus'] =ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('lv','!=',1)->count_all();
        $user['trades'] = ORM::factory('yty_score')->where('qid', '=', $user['id'])->where('type', 'IN', array(2,3))->count_all();
        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);
        $this->template->user = $user;
        parent::after();
    }
    //入口
    public function action_index($bid) {
        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['ytya']['bid']) return $this->action_msg('请通过微信打开！', 'warn');

        $config = ORM::factory('yty_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('yty_login')->where('id', '=', $bid)->find()->access_token;
        if (!$_GET['openid']) $_SESSION['yty'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m'))) {
                $_SESSION['yty'] = NULL;
                die('该页面已过期！');
            }

            $userobj = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

            $_SESSION['yty']['config'] = $config;
            $_SESSION['yty']['openid'] = $openid;
            $_SESSION['yty']['bid'] = $bid;
            $_SESSION['yty']['uid'] = $userobj->id;
            $_SESSION['yty']['access_token'] =$this->access_token;
            Request::instance()->redirect('/yty/'.$_GET['url']);
        }
    }
    public function action_storefuop($bid){// 静默授权
        $config=ORM::factory('yty_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/yty/getopenid/'.$bid;
        $we = new Wechat($config);
        $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
        header("Location:$auth_url");
        exit;
    }
    public function action_getopenid($bid){//通过code获取openid
        $config=ORM::factory('yty_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        $token = $we->getOauthAccessToken();
        $openid=$token['openid'];
        echo $openid.'<br>';
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        $client = new KdtApiOauthClient();
        $access_token=ORM::factory('yty_login')->where('id', '=', $bid)->find()->access_token;
        $method = 'kdt.users.weixin.follower.get';
        $params = [
            'weixin_openid'=>$openid,
         ];
        $results = $client->post($access_token,$method,$params);
        echo '<pre>';
        var_dump($results);
        echo '</pre>';
        $user = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if($results['response']['user']['sex']=='m'){
            $sex=1;//男
        }else if($results['response']['user']['sex']=='f'){
            $sex=2;//女
        }else{
            $sex=0;//人妖
        }
        $user->openid = $openid;
        $user->nickname = $results['response']['user']['nick'];
        if($user->subscribe!=1){//一旦关注为1 就不允许撤销
            $user->subscribe = $results['response']['user']['is_follow'];
        }
        $user->sex = $sex;
        $user->bid = $bid;
        $user->headimgurl = $results['response']['user']['avatar'];
        $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
        $user->save();

        $_SESSION['yty']['config'] = $config;
        $_SESSION['yty']['openid'] = $openid;
        $_SESSION['yty']['bid'] = $bid;
        $_SESSION['yty']['uid'] = $user->id;
        $_SESSION['yty']['access_token'] =$this->access_token;
        Request::instance()->redirect('/yty/commends/'.$openid.'/'.$bid);
    }
    public function action_commends($mopenid,$bid){//奖品list分享页面
        $fopenid = $mopenid;
        $config=ORM::factory('yty_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/yty/commends/'.$mopenid.'/'.$bid;
        $we = new Wechat($config);
        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_urlsdk);
        //$userobj = ORM::factory('yty_qrcode', $this->uid);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/yty/tpl/tpl2';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            }
            $user=ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $user->bid=$bid;
            $user->openid=$openid;
            $user->save();
            // echo $fopenid.'<br>';
            // echo $openid.'<br>';
            //exit;
            $user = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid){
                // echo '有上线';
                $status = 1;
                $result['title'] = $fuser->nickname.'的推荐商品';
            }else{
                $result['title'] = $fuser->nickname.'的推荐商品';
                // echo $user->id.'<br>';
                // echo $fuser->id.'<br>';
                if($fopenid != $openid&&$fuser->id < $user->id){//上线id大于本人id
                    require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
                    $client = new KdtApiOauthClient();
                    $access_token=ORM::factory('yty_login')->where('id', '=', $bid)->find()->access_token;
                    $method = 'kdt.users.weixin.follower.get';
                    $params = [
                        'weixin_openid'=>$openid,
                     ];
                    $results = $client->post($access_token,$method,$params);

                    if($results['response']['user']['sex']=='m'){
                        $sex=1;//男
                    }else if($results['response']['user']['sex']=='f'){
                        $sex=2;//女
                    }else{
                        $sex=0;//人妖
                    }
                    $user->nickname = $results['response']['user']['nick'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $sex;
                    $user->headimgurl = $results['response']['user']['avatar'];
                    $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->fopenid = $fopenid;
                    $user->save();
                    $status = 1;
                    //关系绑定之后 发送消息通知
                    $text = $fuser->nickname.'，恭喜你增加了一个新的客户!';
                    if($config['coupontpl']){
                        $this->sendtplcoupon($fopenid,$config,$text,$we);
                    }else{
                        $msg['touser'] = $fopenid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        $we->sendCustomMessage($msg);
                    }
                }
            }
            $view = "weixin/yty/commendsother";//别人直接是url进，不需要加密
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                $result['title'] = $user->nickname.'的推荐商品';
                $view = "weixin/yty/commends";//自己进 跳入shareopenid 需要加密url
            }
            $this->template->title = $fuser->nickname.'推荐商品';
            $result['commends'] = ORM::factory('yty_setgood')->where('bid','=',$bid)->where('status','=',1)->find_all();
            $result['openid'] = $user->openid;

            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }
    }
    public function action_shareopenid($mopenid,$gid,$bid){//商品分享页面 自己可以打开 别人也可以打开
        $fopenid = base64_decode($mopenid);
        $config=ORM::factory('yty_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/yty/shareopenid/'.$mopenid.'/'.$gid.'/'.$bid;
        $we = new Wechat($config);
        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_urlsdk);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/yty/tpl/tpl';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            }
            $user=ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $user->bid=$bid;
            $user->openid=$openid;
            $user->save();
            $user = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid){
                // echo '有上线';
                $status = 1;
            }else{
                $status = 1;
                if($fopenid != $openid&&$fuser->id < $user->id){
                    require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
                    $client = new KdtApiOauthClient();
                    $access_token=ORM::factory('yty_login')->where('id', '=', $bid)->find()->access_token;
                    $method = 'kdt.users.weixin.follower.get';
                    $params = [
                        'weixin_openid'=>$openid,
                     ];
                    $results = $client->post($access_token,$method,$params);

                    if($results['response']['user']['sex']=='m'){
                        $sex=1;//男
                    }else if($results['response']['user']['sex']=='f'){
                        $sex=2;//女
                    }else{
                        $sex=0;//人妖
                    }
                    $user->nickname = $results['response']['user']['nick'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $sex;
                    $user->headimgurl = $results['response']['user']['avatar'];
                    $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->fopenid = $fopenid;
                    $user->save();
                    //关系绑定之后 发送消息通知
                    $text = $fuser->nickname.'，恭喜你增加了一个新的客户!';
                    if($config['coupontpl']){
                        $this->sendtplcoupon($fopenid,$config,$text,$we);
                    }else{
                        $msg['touser'] = $fopenid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        $we->sendCustomMessage($msg);
                    }
                }
            }
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                // $result['content'] = '快将该商品推荐给你的好友吧';
            }
            $commend = ORM::factory('yty_setgood')->where('bid','=',$bid)->where('id','=',$gid)->find();
            $view = "weixin/yty/share";
            $this->template->title = $fuser->nickname.':'.$commend->title;
            $this->template->content = View::factory($view)->bind('signPackage', $signPackage)->bind('status', $status)->bind('commend', $commend)->bind('result', $result)->bind('nickname', $user->nickname);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }
    }
    //Oauth 入口
    public function action_index_oauth($bid,$fopenid, $url='form') {
        $config = ORM::factory('yty_cfg')->getCfg($bid,1);
        $this->bid=$bid;
        if ($config) {
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
                $userinfo['lv'] = 0;
            }
            if (!$openid) $_SESSION['yty'] = NULL;
            $fopenid=base64_decode($fopenid);
            if ($openid) {
                $userobj = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                if(!$userobj->id){
                    $userobj->values($userinfo);
                    $userobj->bid = $bid;
                    if($fopenid!='sjifenxiaoshang'){
                        $userobj->fopenid=$fopenid;
                    }
                    $userobj->ip = Request::$client_ip;
                    $userobj->save();
                }elseif(!$userobj->fopenid&&$fopenid!='sjifenxiaoshang'){
                    $userobj->fopenid=$fopenid;
                    $userobj->save();
                }else{
                    if($userobj->fopenid!=$fopenid){
                        die ('您已经绑定过关系，请勿多次绑定');
                    }
                }
                $_SESSION['yty']['config'] = $config;
                $_SESSION['yty']['openid'] = $openid;
                $_SESSION['yty']['bid'] = $bid;
                $_SESSION['yty']['uid'] = $userobj->id;
            }
        }
        Request::instance()->redirect('/yty/'.$url.'/'.$userobj->openid);
    }
    public function action_form($openid) {
        $view = "weixin/yty/form";
        $this->template = 'tpl/blank';
        self::before();
        $config=$this->config;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = new Wechat($config);
        if(!$this->bid) die('页面已过期，请重试');
        $bid=$this->bid;
        $qrcode=ORM::factory('yty_qrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
        if($qrcode->fopenid){
            $fuser=ORM::factory('yty_qrcode')->where('bid','=',$this->bid)->where('openid','=',$qrcode->fopenid)->find();
            $num=ORM::factory('yty_sku')->where('bid','=',$this->bid)->count_all();
            if($fuser->agent->skus->order>=$num){
                die ('您的上级已是最低等级分销商，您不能成为他的下级分销商');
            }else{
                $order=$fuser->agent->skus->order+1;
                $sku=ORM::factory('yty_sku')->where('bid','=',$this->bid)->where('order','=',$order)->find();
            }
        }else{
            $sku=ORM::factory('yty_sku')->where('bid','=',$this->bid)->where('order','=',1)->find();
        }
        if($_POST['form']['name']&&$_POST['form']['address']&&$_POST['form']['tel']&&$_POST['form']['id_card']&&$_POST['form']['account']&&$_POST['form']['sid']){
            $s=ORM::factory('yty_sku')->where('id','=',$_POST['form']['sid'])->find()->order;
            $userobj = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
            $agent=ORM::factory('yty_agent')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
            $agent->openid =$openid;
            $agent->bid=$this->bid;
            $agent->name =$_POST['form']['name'];
            $agent->tel =$_POST['form']['tel'];
            $agent->address =$_POST['form']['address'];
            $agent->id_card =$_POST['form']['id_card'];
            $agent->account =$_POST['form']['account'];
            $agent->sid =$_POST['form']['sid'];
            $agent->money =$_POST['form']['money'];
            $agent->save();
            $agent=ORM::factory('yty_agent')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
            $userobj->aid=$agent->id;
            $userobj->s=$s;
            $userobj->lv=2;
            $userobj->save();
            $result['lv'] = 1;
            $result['content'] = '申请提交成功，请耐心等待';
            if($userobj->fopenid){
                $fuser=ORM::factory('yty_qrcode')->where('bid','=',$this->bid)->where('openid','=',$userobj->fopenid)->find();
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $msg['text']['content'] = "有人申请成为你的下级经销商，请及时处理\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";
                if ($config['task_deal_tpl']){
                    $we_result = $this->sendTaskMessage($msg['touser'], $url, '经销商申请', '经销商申请通知','' ,'有人申请成为你的下级经销商，请及时处理');
                }else{
                    $we_result = $we->sendCustomMessage($msg);
                }
            }
        }
        $userobj = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv==1){
            $result['content'] = '恭喜您的申请已经通过，成功获得经销销资格，<br>赶紧联系您的上级经销商哦~';
        }
        if($userobj->lv==2){
            $result['content'] = '申请提交成功，请耐心等待';
        }
        if($userobj->lv==3){
            $result['content'] = '对不起，您的审核未被通过或已被取消，请联系管理员';
        }
        $result['lv'] = $userobj->lv;
        $this->template->content = View::factory($view)->bind('result', $result)->bind('skus', $sku);
    }
    //默认页面
    public function action_home() {
        $view = "weixin/yty/home";

        // die('系统维护中...');
        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('yty_qrcode', $this->uid);

        //可转出金额
        $result['money_now'] = $userobj->scores->select(array('SUM("score")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
        //当前收益
        $result['money_yu'] = $userobj->score = $userobj->scores->select(array('SUM("score")', 'money_yu'))->where('paydate', '>', time())->find()->money_yu;
        //累计收益
        $result['money_all'] = $userobj->score = $userobj->scores->select(array('SUM("score")', 'money_all'))->where('score','>',0)->find()->money_all;

        $result['stock']=$userobj->agent->stock;

        //预计收益
        $userobj->money = $result['money'] = $userobj->scores->select(array('SUM("score")', 'total_score'))->where('score', '>', 0)->find()->total_score;
        //累计付款金额
        $userobj->paid = $result['paid'] = $userobj->scores->select(array('SUM("score")', 'money_paid'))->where('type', 'IN', array(2,3))->find()->money_paid;
         $result['aaa']=$this->config['title5'];
        if ($userobj->id) $userobj->save();

        $this->template->title = '经销商中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //转出
    public function action_money($out=0, $cksum='') {
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid=$this->bid;
        $view = "weixin/yty/money";
        $userobj = ORM::factory('yty_qrcode', $this->uid);

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
                $cfg = ORM::factory('yty_cfg');
                $cfg->setCfg($bid, 'pay', 1);
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
                if($result_m['err_code']=='NOTENOUGH'){
                    $cfg = ORM::factory('yty_cfg');
                    $cfg->setCfg($bid, 'pay', 0);
                }
                Kohana::$log->add("weixin_yty:$bid:money", print_r($result_m, true));
                Kohana::$log->add("weixin_yty:$bid:money", print_r($result, true));
                $result['alert'] = '转出失败：'.$result_m['return_msg'];
            }
        }

        $this->template->title = '结算中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //积分排行榜
    public function action_top2() {
        $mem = Cache::instance('memcache');
        $view = "weixin/yty/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '业绩排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('yty_qrcode', $this->uid)->as_array();

        $rankkey = "yty:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "yty:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //我的团队：分为两部分，第一部分为代理申请请求，第二部分为成员列表，需显示成员微信昵称、代理等级、进货额的余额。
    public function action_team() {
        $mem = Cache::instance('memcache');
        $view = "weixin/yty/team";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;
        $bid=$this->bid;
        $config=$this->config;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = new Wechat($config);
        $this->template->title = '我的团队';
        $this->template->content = View::factory($view)->bind('page',$pages)->bind('qrcode', $qrcode)->bind('user', $user)->bind('result', $result)->bind('signPackage',$signPackage)->bind('member',$member);
        if ($_POST['data']['id']) {
            $id = $_POST['data']['id'];
            $qrcode1 = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            $fuser1=ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$qrcode1->fopenid)->find();
            if ($qrcode1->id&&$qrcode1->agent->money<=$fuser1->agent->stock) {
                if (isset($_POST['data'])) {
                    $qrcode1->lv = (int)$_POST['data']['lv'];
                    $agent=ORM::factory('yty_agent')->where('bid','=',$bid)->where('openid','=',$qrcode1->openid)->find();
                    $suser=$fuser1->agent->suser;
                    if($suser){
                        $agent->suser=$user;
                    }else{
                         $agent->suser=$fuser1->id;
                    }
                    if((int)$_POST['form']['lv']==1){
                        //给予编号
                        $money=$agent->money;
                        $qrcode1->stocks->stockIn($qrcode1, $type=0, $money,$fuser1->id,1);
                        if($config['money_arrived_tpl']){
                            $money1= $qrcode1->agent->stock-$money;
                            $money2=$qrcode1->agent->stock;
                            $remark='恭喜您获得'.$money.'元进货额。';
                           $this->stocksuccess($qrcode1->openid,$money1,$money2,$remark);
                        }else{
                            $msg['touser'] = $qrcode1->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = '恭喜您获得'.$money.'元进货额。';
                            $this->we->sendCustomMessage($msg);
                        }
                        $money_f=$money*$fuser1->agent->skus->scale/100;
                        //上级扣除进货额
                        $fuser1->stocks->stockOut($fuser1, $type=2, $money_f,$qrcode1->id,1);
                        //跟上级发送模板消息
                        if($config['money_arrived_tpl']){
                            $money1= $fuser1->agent->stock+$money_f;
                            $money2=$fuser1->agent->stock;
                            $remark='您出货扣除'.$money_f.'元进货额。';
                           $this->stocksuccess($fuser1->openid,$money1,$money2,$remark);
                        }else{
                            $msg['touser'] = $fuser1->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = '您出货获得'.$money.'元进货额。';
                            $this->we->sendCustomMessage($msg);
                        }
                        $front_user = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('id', '<', $qrcode1->id)->order_by('id','desc')->find();
                        $qrcode1->fid = $front_user->fid + 1;
                        if($config['msg_success_tpl']){
                            $this->sendsuccess($qrcode1->openid,$qrcode1->nickname);
                        }else{
                            $msg['touser'] = $qrcode1->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = "恭喜您成功获得经销商资格，赶紧把商品分享给你的用户购买吧";
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
                $agent->save();
                $qrcode1->save();
                $result=array();
                $result['flag']='success';
                $result['echo']='审核成功';
                echo json_encode($result);
                exit;
            }else{
                $result=array();
                $result['flag']='fail';
                $result['echo']='您的进货额不足';
                echo json_encode($result);
                exit;
            }
        }

        //计算排名
        $member=0;
        $user = ORM::factory('yty_qrcode', $this->uid)->as_array();
        $qrcode = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',2)->where('fopenid','=',$user['openid'])->find_all();
        $result['num1']=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('fopenid','=',$user['openid'])->count_all();
        $result['num2']=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',2)->where('fopenid','=',$user['openid'])->count_all();
        $totlenum=$result['num2'];
        if($_GET['member']==1){
            $member=1;
            $qrcode = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('fopenid','=',$user['openid'])->find_all();
            $totlenum=$result['num1'];
        }
        $result['status']=$status;
        $page = max($_GET['page'], 1);
        $offset = (50 * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $totlenum,
            'items_per_page'=>50,
        ))->render('weixin/yty/admin/pages');
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $signPackage = $this->we->getJsSign($callback_url);

    }
    //申请进货额，点击后出现2部分，第一部分为我的进货申请，第二部分为代理进货申请。
    public function action_stock() {
        $mem = Cache::instance('memcache');
        $view = "weixin/yty/stock";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;
        $this->template->title = '申请进货额';
        $this->template->content = View::factory($view)->bind('page',$pages)->bind('user', $user)->bind('stocks', $stocks)->bind('result', $result);
        $bid=$this->bid;
        $config=$this->config;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = new Wechat($config);
        //计算排名
         if($_POST['data']){
            $id=$_POST['data']['id'];
            $stock=ORM::factory('yty_stock')->where('id','=',$id)->find();
            if($stock->money<=$stock->fqrcode->agent->stock){
                $stock->flag=1;
                $stock->save();
                $qrcode=$stock->qrcode;
                $stocks=DB::query(Database::SELECT,"SELECT SUM(money) as stock from yty_stocks where bid = $bid and qid = $qrcode->id ")->execute()->as_array();
                $stock1=$stocks[0]['stock'];
                $agent=$qrcode->agent;
                $agent->stock=$stock1;
                $agent->save();
                $money=$stock->money;
                if($config['money_arrived_tpl']){
                    $money1= $qrcode->agent->stock-$money;
                    $money2=$qrcode->agent->stock;
                    $remark='恭喜您获得'.$money.'元进货额。';
                   $this->stocksuccess($qrcode->openid,$money1,$money2,$remark);
                }else{
                    $msg['touser'] = $qrcode->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '恭喜您获得'.$money.'元进货额。';
                    $this->we->sendCustomMessage($msg);
                }
                $fuser=ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$qrcode->fopenid)->find();
                $money_f=$money*$fuser->agent->skus->scale/100;
                $fuser->stocks->stockOut($fuser, $type=2, $money_f,$qrcode->id,1);
                //跟上级发送模板消息
                if($config['money_arrived_tpl']){
                    $money1= $fuser->agent->stock+$money_f;
                    $money2=$fuser->agent->stock;
                    $remark='您出货扣除'.$money_f.'元进货额。';
                   $this->stocksuccess($fuser->openid,$money_f,$money2,$remark);
                }else{
                    $msg['touser'] = $fuser->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '您出货扣除'.$money_f.'元进货额。';
                    $this->we->sendCustomMessage($msg);
                }
                $sqrcodes=ORM::factory('yty_sqrcode')->where('bid','=',$bid)->where('qid','=',$qrcode->id)->find();
                foreach ($sqrcodes as $sqrcode) {
                    $sqid=$sqrcode->sqid;
                    $sqrcode1=ORM::factory('yty_qrcode')->where('id','=',$sqid)->find();
                    $money_s=$money*$config['money1'];
                    $sqrcode1->stocks->stockIn($sqrcode1, $type=5, $money_s,$qrcode->id,1);
                    if($config['money_arrived_tpl']){
                        $money1= $sqrcode1->agent->stock-$money_s;
                        $money2=$sqrcode1->agent->stock;
                        $remark='您出货扣除'.$money.'元进货额。';
                       $this->stocksuccess($sqrcode1->openid,$money1,$money2,$remark);
                    }else{
                        $msg['touser'] = $sqrcode1->openid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = '您出货扣除'.$money_s.'元进货额。';
                        $this->we->sendCustomMessage($msg);
                    }
                }
                $result=array();
                $result['flag']='success';
                $result['echo']='恭喜您审核成功';
                echo json_encode($result);
                exit;
            }else{
                $result=array();
                $result['flag']='fail';
                $result['echo']='您的进货额不足';
                echo json_encode($result);
                exit;
            }
        }
        $status=1;
        $user = ORM::factory('yty_qrcode', $this->uid)->as_array();

        $stocks=ORM::factory('yty_stock')->where('bid','=',$this->bid)->where('type','in',array(0,1))->where('qid','=',$user['id'])->order_by('flag', 'ASC')->find_all();
        $result['num1']=ORM::factory('yty_stock')->where('bid','=',$this->bid)->where('type','in',array(0,1))->where('qid','=',$user['id'])->count_all();
        $result['num2']=ORM::factory('yty_stock')->where('bid','=',$this->bid)->where('type','in',array(0,1))->where('fqid','=',$user['id'])->count_all();
        $totlenum= $result['num1'];
        if($_GET['stock']==1){
            $status=2;
            $stocks=ORM::factory('yty_stock')->where('bid','=',$this->bid)->where('type','in',array(0,1))->where('fqid','=',$user['id'])->order_by('flag', 'ASC')->find_all();
            $totlenum= $result['num2'];
        }
        $result['status']=$status;
        $page = max($_GET['page'], 1);
        $offset = (50 * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $totlenum,
            'items_per_page'=>50,
        ))->render('weixin/yty/admin/pages');

    }
    public function action_mstock() {
        $mem = Cache::instance('memcache');
        $view = "weixin/yty/mstock";
        $config=$this->config;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = new Wechat($config);
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $bid=$this->bid;
        $this->template->title = '申请进货';
        $this->template->content = View::factory($view)->bind('user', $user)->bind('stocks', $stocks)->bind('result', $result);
        $user = ORM::factory('yty_qrcode', $this->uid)->as_array();
        $fuser=ORM::factory('yty_qrcode')->where('bid','=',$this->bid)->where('openid','=',$user['fopenid'])->find();
        if($_POST['form']['money']){
            $qrcode=ORM::factory('yty_qrcode')->where('id','=',$user['id'])->find();
            $qrcode->stocks->stockIn($qrcode, $type=1, $_POST['form']['money'], $fuser->id);
            if($fuser){
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $msg['text']['content'] = "有下级经销商申请补货，请及时处理\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";
                Kohana::$log->add("weixin_yty", print_r($config, true));
                if ($config['task_deal_tpl']){
                    $we_result = $this->sendTaskMessage($msg['touser'], $url, '补货通知', '经销商补货通知','' ,'有下级经销商申请补货，请及时处理');
                }else{
                    $we_result = $this->we->sendCustomMessage($msg);
                }
                Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($we_result, true));
            }

            Request::instance()->redirect('yty/stock');
        }
    }



    public function action_inputmoney() {
        $mem = Cache::instance('memcache');
        $view = "weixin/yty/inputmoney";

        $this->template->title = '进货额明细';
        $this->template->content = View::factory($view)->bind('inputmoneys', $inputmoney)->bind('user', $user)->bind('num', $num);
        $user = ORM::factory('yty_qrcode', $this->uid)->as_array();

        $inputmoney=ORM::factory('yty_stock')->where('qid','=',$user['id'])->where('bid','=',$this->bid)->where('flag','=',1)->find_all();
        $num=ORM::factory('yty_stock')->where('qid','=',$user['id'])->where('bid','=',$this->bid)->where('flag','=',1)->count_all();

    }
    //收益明细
    public function action_score($type=0) {
        $view = "weixin/yty/scores";
        $userobj = ORM::factory('yty_qrcode', $this->uid);

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
        $view = "weixin/yty/orders";
        $userobj = ORM::factory('yty_qrcode', $this->uid);
        $bid=$this->bid;
        $config=$this->config;
        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('page',$pages)->bind('trades', $trades)->bind('nums',$nums);
        //只显示直接和间接推广订单，自购不显示
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = new Wechat($config);

        if($_POST['data']['id']){
            $id=$_POST['data']['id'];

            $trade=ORM::factory('yty_trade')->where('id','=',$id)->find();
            $fuser=ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$trade->fopenid)->find();
            if($fuser->agent->stock>=$trade->money1){
                $trade->flag=1;
                $trade->save();

                $fuser->scores->scoreIn($fuser, 2, $trade->money, $trade->qrcode->id, $trade->id);
                //发消息

                $fuser->stocks->stockOut($fuser, $type=3, $trade->money1, $fuser->id, 1);

                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $msg['text']['content'] = "您推荐的{$config['title1']}「{$trade->qrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n您扣除进货额：$money1 {$title5}\n获得佣金：$money{$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl']){
                    $we_result = $this->sendScoreMessage($trade->title,$msg['touser'], $trade->payment , $trade->money, $fuser->score, $url);
                }else{
                    $we_result = $we->sendCustomMessage($msg);
                }
                 Kohana::$log->add("weixin_yty:g", print_r($we_result, true));
                if($config['money_arrived_tpl']){
                    $money1=$fuser->agent->stock+$money1;
                    $money2=$fuser->agent->stock;
                    $remark='扣除您的'.$trade->money1.'元进货额。';
                   $this->stocksuccess($fuser->openid,$money1,$money2,$remark);
                }else{
                    $msg['touser'] = $fuser->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '扣除您的'.$trade->money1.'元进货额。';
                    $this->we->sendCustomMessage($msg);
                }
                Kohana::$log->add('result',print_r($we_result,true));
                $result=array();
                $result['flag']='success';
                $result['echo']='审核成功';
                echo json_encode($result);
                exit;
            }else{
                $result=array();
                $result['flag']='fail';
                $result['echo']='进货额不足';
                echo json_encode($result);
                exit;
            }
        }
        $trades = ORM::factory('yty_trade')->where('bid','=',$bid)->where('fopenid','=',$userobj->openid)->order_by('flag', 'ASC')->find_all();
        $nums = ORM::factory('yty_trade')->where('bid','=',$bid)->where('fopenid','=',$userobj->openid)->count_all();

        $totlenum=$nums;
        $page = max($_GET['page'], 1);
        $offset = (50 * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $totlenum,
            'items_per_page'=>50,
        ))->render('weixin/yty/admin/pages');
    }

    public function action_order($tid) {
        $view = "weixin/yty/order";

        $order = ORM::factory('yty_trade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }

    //排行榜
    public function action_top() {
        $view = 'weixin/yty/top';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('yty_qrcode', $this->uid);
        $top = $this->config['rank_yty'] ? $this->config['rank_yty'] : 10;

        $result['rank'] = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('lv','=',1)->where('paid', '>', $user->paid)->count_all()+1;

        $usersobj = ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->order_by('paid', 'DESC')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }

    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/yty/customer';
        $this->template->title = '我的客户';
        $this->template->content = View::factory($view)
        ->bind('config',$this->config)
        ->bind('mycustomers',$totlecustomer)//绑定所有用户（1）级
        ->bind('result', $result)
        ->bind('totlenum',$totlenum)
        ->bind('page',$pages)
        ->bind('pagenum',$page)
        ->bind('newadd',$newadd);
        //$this->template->content = View::factory($view)->bind('result', $result);

        $user = ORM::factory('yty_qrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM yty_qrcodes WHERE fopenid='$user->openid' and lv != 1 and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM yty_qrcodes WHERE fopenid='$user->openid' and lv != 1 ")->execute()->as_array();

           if($newadd=='month')
           {
            $customer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->where('lv','!=',1);
           }
           else
             $customer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('lv','!=',1);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/yty/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('lv','!=',1)->where('jointime','>=',$month)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','!=',1)->where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();


    }

     //查看自己客户(下线和二级 以及三级)
    public function action_customer1($newadd='') {
        $view = 'weixin/yty/customer1';
        $this->template->title = '我的经销商';
        $this->template->content = View::factory($view)
        ->bind('config',$this->config)
        ->bind('mycustomers',$totlecustomer)//绑定所有用户（1）级
        ->bind('result', $result)
        ->bind('totlenum',$totlenum)
        ->bind('page',$pages)
        ->bind('pagenum',$page)
        ->bind('newadd',$newadd);
        //$this->template->content = View::factory($view)->bind('result', $result);

        $user = ORM::factory('yty_qrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM yty_qrcodes WHERE fopenid='$user->openid' and lv =1 and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM yty_qrcodes WHERE  fopenid='$user->openid' and lv =1 ")->execute()->as_array();

           if($newadd=='month')
           {
            $customer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->where('lv','=',1);
           }
           else
             $customer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('lv','=',1);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/yty/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->where('lv','=',1)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('yty_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();


    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "yty_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    //提示页面
    public function action_msg($msg, $type='suc') {
        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/yty/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }
    public function action_test(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('ytypostStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('yty', '111');
        Kohana::$log->add('$result11', print_r($result11, true));
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $appid =$result11['app_id'];
        $msg=$result11['msg'];
        $kdt_id=$result11['kdt_id'];
        $status=$result11['status'];
        //Kohana::$log->add('$status', print_r($status, true));
        Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = ORM::factory('yty_login')->where('shopid','=',$kdt_id)->find()->id;
        $this->bid=$bid;
        $this->config = $config = ORM::factory('yty_cfg')->getCfg($bid);
        //Kohana::$log->add('$config', print_r($config, true));
        $this->we = $we = new Wechat($config);
        require_once Kohana::find_file('vendor', 'kdt/KdtApiOauthClient');
        $this->access_token=ORM::factory('yty_login')->where('id', '=', $bid)->find()->access_token;
        if($this->access_token){
            $this->client =$client= new KdtApiOauthClient();
        }else{
            Kohana::$log->add("yty:$bid:bname", print_r('有赞参数未填', true));
        }
        if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
            $posttid=urldecode($msg);
            $jsona=json_decode($posttid,true);
            Kohana::$log->add("yty:$bid", print_r($jsona, true));
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
        $yty_trade = ORM::factory('yty_trade')->where('tid', '=', $tid)->find();
        //跳过已导入订单
        if ($yty_trade->id) {
            //更新订单状态
            if ($yty_trade->status != $trade['status']) {
                $yty_trade->status = $trade['status'];
                $yty_trade->save();

                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($trade['status'] == 'TRADE_CLOSED') ORM::factory('yty_score')->where('tid', '=', $yty_trade->id)->delete_all();
            if ($trade['status'] == 'TRADE_CLOSED_BY_USER') ORM::factory('yty_score')->where('tid', '=', $yty_trade->id)->delete_all();
            if ($trade['refund_state'] != 'NO_REFUND') ORM::factory('yty_score')->where('tid', '=', $yty_trade->id)->delete_all();
            return;
        }
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['type'], true));
        if ($trade['type'] != 'FIXED') return;
        //男人袜不参与火种用户的商品
        Kohana::$log->add('payment', print_r($trade['payment'], true));
        //付款金额为 0
        if ($trade['payment'] <= 0) return;
        $method = 'kdt.users.weixin.follower.get';
        $params = [
            'user_id'=>$trade['weixin_user_id'],
        ];
        $result = $client->post($this->access_token,$method, $params);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        $yty_qrcode = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($yty_qrcode->id, true));
        if (!$yty_qrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }
        $trade['qid'] = $yty_qrcode->id;
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
        Kohana::$log->add('money', print_r($money,true));
        $average=$money/($money+$trade['discount_fee']);//权重
        Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid){//有一级
            $rank=1;
            $ffuser = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            $trade['fopenid'] = $ffuser->openid;
            if($ffuser->lv!=1&&$fuser->lv!=1){
                return;
            }
        }
        $money1 = 0;
        foreach ($trade['orders'] as $order) {
            $tempmoney=($order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment))*$average;
            Kohana::$log->add('tempmoney', print_r($tempmoney,true));
            Kohana::$log->add('orderpayment', print_r($orderpayment,true));
            Kohana::$log->add('ordersumpayment', print_r($ordersumpayment,true));
            $goodid=$order['num_iid'];
            $goodidcof=ORM::factory('yty_setgood')->where('goodid','=',$goodid)->find();
            //按照分销商等级 设置比例
            if($fuser->lv==1){
                $sku_lv=$fuser->agent->skus->order-1;
            }else{
                $sku_lv=$ffuser->agent->skus->order-1;
            }
            Kohana::$log->add('sku_lv', print_r($sku_lv,true));
            $goodname="money{$sku_lv}";
            Kohana::$log->add('goodname', print_r($goodname,true));
            $money1=$money1+$goodidcof->$goodname;
        }
        $money1 = $trade['money1'] = number_format($money1, 2); //一级
        $yty_trade->values($trade);
        $yty_trade->save();
        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['num_iid'];
            $num=$order['num'];
            $price=$order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment);
            $yty_order=ORM::factory('yty_order')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$yty_order->id)//跳过已导入的order
            {
                $yty_order->bid=$bid;
                $yty_order->tid=$tid;
                $yty_order->goodid=$goodid;
                $yty_order->title=$title;
                $yty_order->num=$num;
                $yty_order->price=$price;
                $yty_order->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('yty_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('yty_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('yty_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        $trade1=ORM::factory('yty_trade')->where('bid','=',$bid)->where('tid','=',$trade['tid'])->find();
        //订单上线返利
        if ($money1 > 0&&$yty_qrcode->lv!=1) {
            $fuser = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $yty_qrcode->fopenid)->find();
            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                if($fuser->agent->stock>=$money1){
                    $trade1->flag=1;
                    $trade1->save();
                    $fuser->scores->scoreIn($fuser, 2, $money, $yty_qrcode->id, $yty_trade->id);
                    //发消息
                    $fuser->stocks->stockOut($fuser, $type=3, $money1, $yty_qrcode->id, 1);
                    $msg['touser'] = $fuser->openid;
                    $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                    $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                    $msg['text']['content'] = "您推荐的{$config['title1']}「{$yty_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n您扣除进货额：$money1 {$title5}\n获得佣金：$money{$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                    if ($config['msg_score_tpl']){
                        $we_result = $this->sendScoreMessage($yty_trade->title,$msg['touser'], $yty_trade->payment, $money, $fuser->score, $url);
                    }else{
                        $we_result = $we->sendCustomMessage($msg);
                    }
                    Kohana::$log->add('result',print_r($we_result,true));
                    if($config['money_arrived_tpl']){
                        $money1= $fuser->agent->stock+$money1;
                        $money2=$fuser->agent->stock;
                        $remark='扣除您的'.$yty_trade->money1.'元进货额。';
                       $this->stocksuccess($fuser->openid,$money1,$money2,$remark);
                    }else{
                        $msg['touser'] = $fuser->openid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = '扣除您的'.$yty_trade->money1.'元进货额。';
                        $this->we->sendCustomMessage($msg);
                    }
                }else{
                    $msg['touser'] = $fuser->openid;
                    $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                    $url = $this->baseurl.'index/'. $bid .'?url=stock&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                    $msg['text']['content'] = "您的进货额不足，订单收益无法到账，请及时补充进货额\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";
                    if ($config['task_deal_tpl']){
                        $we_result = $this->sendTaskMessage($msg['touser'], $url, '进货额余额不足', '未处理订单通知','' ,'您的进货额不足，订单收益无法到账，请及时补充进货额');
                    }else{
                        $we_result = $we->sendCustomMessage($msg);
                    }
                    $trade1->flag=0;
                    $trade1->save();
                }
            }
        }elseif ($money1 > 0&&$yty_qrcode->lv==1) {
            if ($yty_qrcode->id) {
                if($yty_qrcode->agent->stock>=$money1){
                    $trade1->fopenid=$yty_qrcode->openid;
                    $trade1->flag=1;
                    $trade1->save();
                    $yty_qrcode->scores->scoreIn($yty_qrcode, 2, $money, $yty_qrcode->id, $yty_trade->id);
                    //发消息
                    $yty_qrcode->stocks->stockOut($yty_qrcode, $type=3, $money1, $yty_qrcode->id, 1);
                    $msg['touser'] = $yty_qrcode->openid;
                    $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                    $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                    $msg['text']['content'] = "您完成一笔订单！\n\n实付金额：$money {$title5}\n您扣除进货额：$money1 {$title5}\n获得佣金：$money{$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                    if ($config['msg_score_tpl']){
                        $we_result = $this->sendScoreMessage($yty_trade->title,$msg['touser'], $yty_trade->payment, $money, $yty_qrcode->score, $url);
                    }else{
                        $we_result = $we->sendCustomMessage($msg);
                    }

                    if($config['money_arrived_tpl']){
                        $money1=$yty_qrcode->agent->stock+$money1;
                        $money2=$yty_qrcode->agent->stock;
                        $remark='扣除您的'.$yty_trade->money1.'元进货额。';
                       $this->stocksuccess($yty_qrcode->openid,$money1,$money2,$remark);
                    }else{
                        $msg['touser'] = $yty_qrcode->openid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = '扣除您的'.$yty_trade->money1.'元进货额。';
                        $this->we->sendCustomMessage($msg);
                    }
                    Kohana::$log->add('result',print_r($we_result,true));
                }else{
                    $msg['touser'] = $yty_qrcode->openid;
                    $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                    $url = $this->baseurl.'index/'. $bid .'?url=stock&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                    $msg['text']['content'] = "您的进货额不足，订单收益无法到账，请及时补充进货额\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";
                    if ($config['task_deal_tpl']){
                        $we_result = $this->sendTaskMessage($msg['touser'], $url, '进货额余额不足', '未处理订单通知','' ,'您的进货额不足，订单收益无法到账，请及时补充进货额');
                    }else{
                        $we_result = $we->sendCustomMessage($msg);
                    }
                    $trade1->flag=0;
                    $trade1->save();
                    $trade1->fopenid=$yty_qrcode->openid;
                    $trade1->flag=0;
                    $trade1->save();
                }
            }
        }
        flush();ob_flush();
        exit;
    }
    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($title,$openid,$payment, $score, $total, $url, $remark='干的漂亮，请继续加油哦！') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = '您的客户刚才完成了一笔'.$payment.'元的交易，您获得相应收益!';
        $tplmsg['data']['first']['color'] = '#FFFFFF';
        $tplmsg['data']['keyword1']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword1']['color'] = '#FFFFFF';
        $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#FFFFFF';
        $tplmsg['data']['remark']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($openid, true));
        Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
    }
    private function sendTaskMessage($openid, $url,$first,$keyword1,$keyword2,$remark) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['task_deal_tpl'];
        $tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = $first;
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = $keyword1;
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['keyword2']['value'] = $keyword2;
        $tplmsg['data']['keyword2']['color'] = '#06bf04';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        Kohana::$log->add("tplmsg", print_r( $tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("result", print_r( $result, true));
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

        // Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($tplmsg, true));
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

        // Kohana::$log->add('weixin_yty:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_yty:hongbaoresult', print_r($result, true));
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."yty/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."yty/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('yty_cfg')->where('bid', '=', $bid)->where('key', '=', 'yty_file_cert')->find();
        $file_key = ORM::factory('yty_cfg')->where('bid', '=', $bid)->where('key', '=', 'yty_file_key')->find();

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

        // Kohana::$log->add("weixin_yty:$bid:curl_post_ssl:cert_file", $cert_file);

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
    private function stocksuccess($openid,$money1,$money2,$remark) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['money_arrived_tpl'];
        //$tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = 进货额余额变更通知;
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = number_format($money1,2);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = number_format($money2,2);
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword3']['color'] = '#666666';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($openid, true));
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_yty:tpl", print_r($result, true));
        return $result;
    }
    private function sendsuccess($openid, $nickname,  $remark='恭喜您成功获得经销商资格，点击菜单可以进入经销商个人中心。') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_success_tpl'];
        //$tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = '尊敬的用户，您提交的经销商申请已经审核通过！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $nickname;
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = '已通过';
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword3']['color'] = '#666666';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($openid, true));
        //Kohana::$log->add("weixin_yty:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_yty:tpl", print_r($result, true));
        return $result;
    }
       //产品图片
    // public function action_images($type='item', $id=1, $cksum='') {
    //     $field = 'pic';
    //     $table = "yty_$type";

    //     $pic = ORM::factory($table, $id)->pic;
    //     if (!$pic) die('404 Not Found!');

    //     header("Content-Type: image/jpeg");
    //     header("Content-Length: ".strlen($pic));
    //     echo $pic;
    //     exit;
    // }
}
