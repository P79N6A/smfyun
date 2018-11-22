<?php defined('SYSPATH') or die('No direct script access.');
require_once DOCROOT.'../application/vendor/tx_msg/autoload.php';
use Qcloud\Sms\SmsSingleSender;
class Controller_Qwta extends Controller_Base {

    public $template = 'weixin/qwt/tpl/atpl';
    public $pagesize = 10;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public $appId = 'wx4d981fffa8e917e7';

    public function before() {
        Database::$default = "qwt";
        // echo ini_get('session.gc_maxlifetime');//得到ini中设定值
        ini_set('session.gc_maxlifetime', 43200); //设置时间

        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'register') return;
        if (Request::instance()->action == 'help') return;
        if (Request::instance()->action == 'anounce_testsku') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'product_preview') return;
        if (Request::instance()->action == 'self_oauth') return;
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'wait_order') return;
        if (Request::instance()->action == 'forget') return;
        $this->bid = $_SESSION['qwta']['bid'];
        if($this->bid){
            $login=ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            //$couponcfg=ORM::factory('qwt_cfg')->getCfg($this->bid,1);
            // echo $login->yzaccess_token.'<br>';
            // echo $couponcfg['coupon'].'<br>';
            // if($login->yzaccess_token){
            //     // echo 'aa';
            //     $this->couponrsync($this->bid,$login->yzaccess_token);
            // }
            // exit();
        }
        //未登录
        $this->template->barrage = ORM::factory('qwt_cfg')->where('bid','=',0)->where('key','=','barrage')->find()->value;
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:/qwta/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            // $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->where('expiretime', '>=', time())->find_all();
            $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->find_all();
            $this->template->todo = $todo;
        }
        @View::bind_global('bid', $this->bid);
        parent::after();
    }
    // public function couponrsync($bid,$yzaccess_token){
    //     require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
    //     require_once Kohana::find_file("vendor/kdt","YZTokenClient");
    //     $smfy=new SmfyQwt();
    //     $client = new YZTokenClient($yzaccess_token);
    //     $result['yzcoupons']=$smfy->getyzcoupons1($bid,$client);
    //     // echo '<pre>';
    //     // var_dump($result['yzcoupons']);
    //     // echo '</pre>';
    //     // exit();
    //     $type=0;
    //     foreach ($result['yzcoupons'] as $key => $yzcoupon) {
    //         $coupon=ORM::factory('qwt_coupon')->where('bid','=',$bid)->where('coupon_id','=',$yzcoupon['group_id'])->find();
    //         $coupon->bid=$bid;
    //         if($yzcoupon['title']){
    //             $coupon->title=$yzcoupon['title'];
    //         }
    //         $coupon->state=1;
    //         if($yzcoupon['coupon_type']=='PROMOCODE'){
    //             $type=7;
    //         }elseif($yzcoupon['coupon_type']=='PROMOCARD'){
    //             $type=9;
    //         }
    //         $coupon->type=$type;
    //         $coupon->coupon_id=$yzcoupon['group_id'];
    //         $coupon->save();
    //     }
    //     //ORM::factory('qwt_cfg')->setCfg($bid,'coupon',1);
    // }
    public function action_orm_err(){
        $err = ORM::factory('qwt_login')->where('bid','=',6)->find();
        $err->sss=1;
        $err->save();
    }
    public function action_wait_order(){
        if($_POST['wait_id']){
            $wait_order = ORM::factory('qwt_waitorder')->where('id','=',$_POST['wait_id'])->find();
            $wait_order->delete();
            $result['state'] = 1;
            echo json_encode($result);
        }
        exit;
    }
    public function action_set_option(){
        $smfyun = Model::factory('smfyun');
        $res = $smfyun->set_option($this->bid,'location_report',1);
        if($res['errcode'] == 0){

        }else{
            $result['error'] = '微信公众号【获取用户地理位置】开关打开失败，错误信息：'.$res['errcode'].$res['errmsg'];
        }
        var_dump($res);
        exit;
    }
    public function action_test(){
        $logins=ORM::factory('qwt_login')->where('memo','like','%赞先科技-高级代理商%')->find_all();
        // $logins=ORM::factory('qwt_login')->where('memo','NOT LIKE','%赞先科技%')->where('flag','=',1)->find_all();
        // foreach ($logins as $k => $v) {
        //     $bid=$v->id;
        //     echo $bid.'<br>';
        // }
        // exit();
        foreach ($logins as $k => $v) {
            $bid=$v->id;
            echo $bid.'<br>';
            $buy=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',27)->find();
            $buy->bid=$bid;
            $buy->iid=27;
            $buy->buy_time=time();
            $buy->expiretime=1559923200;
            $buy->switch=1;
            $buy->status=1;
            $buy->save();
            $sku1=ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('iid','=',27)->where('sid','=',46182 )->find();
            $sku1->bid=$bid;
            $sku1->iid=27;
            $sku1->sid=46182;
            $sku1->name='推荐有你:包月';
            $sku1->state=1;
            $sku1->old_price=500;
            $sku1->price=225;
            $sku1->save();
            $sku2=ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('iid','=',27)->where('sid','=',46183)->find();
            $sku2->bid=$bid;
            $sku2->iid=27;
            $sku2->sid=46183;
            $sku2->name='推荐有你:包年';
            $sku2->state=1;
            $sku2->old_price=4000;
            $sku2->price=1800;
            $sku2->save();
        }
        exit();
    }
    public function action_oauth_info(){
        // require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        // $options['token'] = $this->token;
        // $options['encodingaeskey'] = $this->encodingAesKey;
        // $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        // $wx = new Wxoauth($bid,$options);
        // $data['component_appid'] = 'wx4d981fffa8e917e7';
        // $data['authorizer_appid'] = $options['appid'];
        // $res = $wx->authorizer_user_get($data);
        // $arr = json_decode($res,true);
        // echo '<pre>';
        // var_dump($arr);
    }
    public function action_login() {
        $this->template = 'weixin/qwt/tpl/login';
        $this->before();
        require_once Kohana::find_file('vendor', 'geetest/lib/class.geetestlib');
        require_once Kohana::find_file('vendor', 'geetest/config/config');
        $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
        session_start();
        if($_GET['StartCaptchaServlet']==1){
            $data = array(
                    "user_id" => Request::$client_ip, # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => Request::$client_ip # 请在此处传输用户请求验证时所携带的IP
                );

            $status = $GtSdk->pre_process($data, 1);
            $_SESSION['gtserver'] = $status;
            $_SESSION['user_id'] = $data['user_id'];
            echo $GtSdk->get_response_str();
            exit;
        }
        if ($_POST['username'] && $_POST['password']) {
            //geetest
            $data = array(
                    "user_id" => Request::$client_ip, # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => Request::$client_ip # 请在此处传输用户请求验证时所携带的IP
                );


            if ($_SESSION['gtserver'] == 1) {   //服务器正常
                $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
                if ($result) {
                    // echo '{"status":"success"}';
                } else{
                    // echo '{"status":"fail"}';
                    $this->template->error = '未通过安全验证！';
                    return;
                }
            }else{  //服务器宕机,走failback模式
                if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                    // echo '{"status":"success"}';
                }else{
                    // echo '{"status":"fail"}';
                    $this->template->error = '未通过安全验证！';
                    return;
                }
            }
            //geetest
            $biz = ORM::factory('qwt_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {
                if ($biz->isblack==1) {
                    $this->template->error = '账户异常';
                }else{
                    $_SESSION['qwta']['bid'] = $biz->id;
                    $_SESSION['qwta']['user'] = $_POST['username'];
                    $_SESSION['qwta']['admin'] = $biz->admin;
                    $_SESSION['qwta']['dlflag'] = $biz->flag; //超管
                }
            } else {
                $this->template->error = '账号或密码错误';
            }
        }
        if ($_SESSION['qwta']['bid']) {
            // if (!$_GET['from']) $_GET['from'] = 'userinfo';
            if($_GET['from']){
                header('location:/qwta/'.$_GET['from']);
                exit;
            }
        }
    }
    public function action_setgoods(){
        $goods=ORM::factory('qwt_item');
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('name', 'like', $s);
        }
        if ($_POST['form']['num_iid']) {
            $goodid = $_POST['form']['num_iid'];
            if($_POST['form']['smoney']){
                foreach ($_POST['form']['smoney'] as $k => $v) {
                    $sku = ORM::factory('qwt_dldgoodsku')->where('id','=',$v['skuid'])->find();
                    if($sku->sku_id){

                    }else{
                        $sku->sku_id = 0;
                    }
                    $smoney = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$v['suiteid'])->where('sku_id','=',$sku->sku_id)->where('item_id','=',$_POST['form']['num_iid'])->find();
                    $smoney->bid = $bid;
                    $smoney->sid = $v['suiteid'];
                    $smoney->sku_id = $sku->sku_id;
                    $smoney->item_id = $_POST['form']['num_iid'];
                    $smoney->money = $v['money'];
                    $smoney->save();
                }
            }
            if($_POST['form']['type']==1){
                $sku=$_POST['form']['money'];
                foreach ($sku as $k => $v) {
                    //echo $k."<br>";
                   $setsku=ORM::factory('qwt_dldgoodsku')->where('id','=',$k)->find();
                   $setsku->money=$v;
                   $setsku->save();
                }
            }
            $good = ORM::factory('qwt_dldsetgood')->where('bid', '=', $bid)->where('num_iid','=',$goodid)->find();
            if(isset($_POST['form']['status'])){
                $good->status=$_POST['form']['status'];
            }
            if($_POST['form']['type']!=1){
                $good->money=$_POST['form']['money'];
            }
            $good->save();
        }
        $result['countall'] = $countall = $goods->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');
        $result['goods'] =$goods->order_by('status', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['suite'] = ORM::factory('qwt_dldsuite')->where('bid','=',$bid)->find_all();
        $this->template->content = View::factory('weixin/qwt/admin/setgoods')
        ->bind('result',$result)
        ->bind('pages',$pages);
    }
    public function action_help() {
        $this->template = 'weixin/qwt/tpl/help';
        $this->before();
    }
    public function action_anounce_testsku() {
        $this->template = 'weixin/qwt/tpl/anounce_testsku';
        $this->before();
    }
    public function action_sms(){
        require_once Kohana::find_file('vendor', 'aliyun-dysms-php-sdk-lite/demo/sendSms');
        // require_once DOCROOT.'../application/vendor/aliyun-dysms-php-sdk-lite/demo/sendSms.php';
        $params['appid'] = 'LTAIvJvaLwxAKeLd';
        $params['Secret'] = 'F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt';
        $params['PhoneNumbers'] = '13720363990';
        $params["SignName"] =  '神码浮云';
        $params["TemplateCode"] = 'SMS_96275008';
        $params['TemplateParam'] = Array (
            "code" => "199468",
            "product" => "阿里通信"
        );
        // $sms = new sendSms();
        $result = json_decode(json_encode(sendSms($params)),true);
        var_dump($result);
        exit;
    }
    public function action_register() {
        $this->template = 'weixin/qwt/tpl/register';
        $this->before();
        require_once Kohana::find_file('vendor', 'geetest/lib/class.geetestlib');
        require_once Kohana::find_file('vendor', 'geetest/config/config');
        // require_once Kohana::find_file('vendor', 'aliyun_mns/test_tel');
        require_once Kohana::find_file('vendor', 'aliyun-dysms-php-sdk-lite/demo/sendSms');
        $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
        session_start();
        //geetest
        if($_GET['createcode']==1&&$_GET['tel']){
            $mem = Cache::instance('memcache');
            $cachename1 ='qwt_tel_die:'.$_GET['tel'];
            $cachename2 ='qwt_code_die:'.$_GET['tel'];
            $tel_token = $mem->get($cachename1);//判断这个手机号60s内有没有获取过
            if($tel_token){
                $res['error'] = 'too_busy';
                $res['content'] = '发送短信太频繁了！';
                echo json_encode($res);
            }else{//不存在就申请，并且存入
                //发送短信
                // $sms = new PublishBatchSMSMessageDemo;
                $identify_code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
                // $result = $sms->run($_GET['tel'],$identify_code);
                $params['appid'] = 'LTAIvJvaLwxAKeLd';
                $params['Secret'] = 'F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt';
                $params['PhoneNumbers'] = $_GET['tel'];
                $params["SignName"] =  '神码浮云';
                $params["TemplateCode"] = 'SMS_96275008';
                $params['TemplateParam'] = Array (
                    "code" => $identify_code,
                    "product" => "阿里通信"
                );
                // $sms = new sendSms();
                $result = json_decode(json_encode(sendSms($params)),true);
                Kohana::$log->add('qwt_tel_register:'.$_GET['tel'].':', print_r($result,true));//写入日志，可以删除
                if($result["Message"]=='OK'){
                    $mem->set($cachename1,$identify_code,60);//60s内不能重新请求
                    $mem->set($cachename2,$identify_code,300);//5min内有效
                    $res['error'] = 'no_error';
                    $res['content'] = '验证码已发送';
                    echo json_encode($res);
                }else{
                    $res['error'] = 'error';
                    $res['content'] = '服务异常，请稍后再试！'.$result['Message'];
                    echo json_encode($res);
                }
            }
            exit;
        }
        if($_GET['StartCaptchaServlet']==1){
            $data = array(
                    "user_id" => Request::$client_ip, # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => Request::$client_ip # 请在此处传输用户请求验证时所携带的IP
                );

            $status = $GtSdk->pre_process($data, 1);
            $_SESSION['gtserver'] = $status;
            $_SESSION['user_id'] = $data['user_id'];
            echo $GtSdk->get_response_str();
            exit;
        }
        if ($_POST['userid'] && $_POST['pass'] && $_POST['identify']) {

            //geetest
            $data = array(
                    "user_id" => Request::$client_ip, # 网站用户id
                    "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                    "ip_address" => Request::$client_ip # 请在此处传输用户请求验证时所携带的IP
                );


            if ($_SESSION['gtserver'] == 1) {   //服务器正常
                $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
                if ($result) {
                    // echo '{"status":"success"}';
                } else{
                    // echo '{"status":"fail"}';
                    $this->template->error = '未通过安全验证！';
                    return;
                }
            }else{  //服务器宕机,走failback模式
                if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                    // echo '{"status":"success"}';
                }else{
                    // echo '{"status":"fail"}';
                    $this->template->error = '未通过安全验证！';
                    return;
                }
            }
            //geetest

            $mem = Cache::instance('memcache');
            $cachename2 ='qwt_code_die:'.$_POST['userid'];
            $tel_token = $mem->get($cachename2);
            if($tel_token!=$_POST['identify']){
                $this->template->error = '验证输入不正确!';
                return;
            }
            if(!preg_match("/^1[34578]{1}\d{9}$/",$_POST['userid'])){
                $this->template->error = '手机号格式不正确哟';
                return;
            }
            if($_POST['code']){
                $fubid = ORM::factory('qwt_login')->where('code', '=', $_POST['code'])->find()->id;
                if(!$fubid){
                    $this->template->error = '邀请码不正确哟';
                    return;
                }
            }
            $biz = ORM::factory('qwt_login')->where('user', '=', $_POST['userid'])->find();
            if ($biz->id) {
                $this->template->error = '您已经注册过了哟';
            } else {
                $lastcode=ORM::factory('qwt_login')->order_by('id','DESC')->find()->id;
                $biz->code = $lastcode.rand(100,1000);
                $biz->user = $_POST['userid'];
                $biz->pass = $_POST['pass'];
                if($fubid) {
                    $biz->fubid = $fubid;
                }
                $biz->save();
                $this->template->success = '恭喜您，账号注册成功！';
                // return;
                $_SESSION['qwta']['bid'] = $biz->id;
                $_SESSION['qwta']['user'] = $biz->user;
                Request::instance()->redirect('/qwta/login');
            }
        }
    }
    public function action_forget() {
        $this->template = 'weixin/qwt/tpl/forgetpass';
        $this->before();
        if($_GET['tel']){
            $mem = Cache::instance('memcache');
            $cachename1 ='qwt_forget_tel_die:'.$_GET['tel'];
            $cachename2 ='qwt_forget_code_die:'.$_GET['tel'];
            $tel_token = $mem->get($cachename1);//判断这个手机号60s内有没有获取过
            if($tel_token){
                $res['error'] = 'too_busy';
                $res['content'] = '发送短信太频繁了！';
                echo json_encode($res);
            }else{//不存在就申请，并且存入
                //发送短信
                // $sms = new PublishBatchSMSMessageDemo;
                $identify_code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
                // $result = $sms->run($_GET['tel'],$identify_code);
                $ssender = new SmsSingleSender('1400090648', '7e8879bd06763bc00fe360f9c54ad078');
                $smsSign = '神码浮云';
                $params = [$identify_code];
                $end = $ssender->sendWithParam("86", $_GET['tel'], 133698,
                        $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                $result = json_decode($end,true);
                // var_dump($result);
                if($result["errmsg"]=='OK'){
                    $mem->set($cachename1,$identify_code,60);//60s内不能重新请求
                    $mem->set($cachename2,$identify_code,300);//5min内有效
                    $res['error'] = 'no_error';
                    $res['content'] = '验证码已发送';
                    echo json_encode($res);
                }else{
                    $res['error'] = 'error';
                    $res['content'] = '服务异常，请稍后再试！'.$result['errmsg'];
                    echo json_encode($res);
                }
            }
            exit;
        }
        if ($_POST['userid'] && $_POST['pass'] && $_POST['identify']) {

            $mem = Cache::instance('memcache');
            $cachename2 ='qwt_forget_code_die:'.$_POST['userid'];
            $tel_token = $mem->get($cachename2);
            if($tel_token!=$_POST['identify']){
                $this->template->error = '验证输入不正确!';
                return;
            }
            if(!preg_match("/^1[34578]{1}\d{9}$/",$_POST['userid'])){
                $this->template->error = '手机号格式不正确哟';
                return;
            }
            $biz = ORM::factory('qwt_login')->where('user', '=', $_POST['userid'])->find();
            if ($biz->id) {
                $biz->pass = $_POST['pass'];

                $biz->save();
                $this->template->success = '密码已重置！';
                // return;
                $_SESSION['qwta']['bid'] = $biz->id;
                $_SESSION['qwta']['user'] = $biz->user;
                Request::instance()->redirect('/qwta/login');
            }else{
                $this->template->error = '用户不存在！';
                return;
            }
        }
    }
    public function action_layout() {
        $_SESSION['qwta'] = null;
        setcookie("session_smfyun", "", time() - 3600);
        header('location:/qwta/login');
        exit;
    }
    public function action_home() {
        $this->template->title = '首页';
    }
    public function action_qwta() {
        Request::instance()->redirect('/qwta/login');
    }
    public function action_oauth() {
        $bid = $this->bid;
        $this->template->title = '一键授权';
        //微信授权
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$this->appId;
        $ctoken = $mem->get($cachename1);//获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$ctoken;
        $post_data = array(
          'component_appid' =>$this->appId
        );
        $post_data = json_encode($post_data);
        $res = json_decode($this->request_post($url, $post_data),true);
        // var_dump($res);
        $pre_auth_code = $res['pre_auth_code'];
        $pre_auth_code = substr($pre_auth_code,14);//去掉前缀 preauthcode@@@
        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        if ($_GET['auth_code']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
            $post_data = array(
              'component_appid' =>$this->appId,
              'authorization_code' =>$_GET['auth_code']
            );
            $post_data = json_encode($post_data);
            $res = json_decode($this->request_post($url, $post_data),true);
            $appid = $res['authorization_info']['authorizer_appid'];
            $access_token = $res['authorization_info']['authorizer_access_token'];
            $refresh_token = $res['authorization_info']['authorizer_refresh_token'];
            $expires_in = time()+7200;
            for($i=0;$res['authorization_info']['func_info'][$i];$i++){
                $auth_info = $auth_info.','.$res['authorization_info']['func_info'][$i]['funcscope_category']['id'];
            }
            // echo $auth_info.'<br>';
            // var_dump($res);
            // if($this->bid==929){
            //     echo '<pre>';
            //     var_dump($res);
            //     Kohana::$log->add('qwt:oauth:$this->bid:', print_r($res,true));
            //     exit;
            // }
            if($res['errcode']){
                Kohana::$log->add('qwt:oauth:{$this->bid}:', print_r($res,true));
                echo '授权失败，错误如下：<br>';
                var_dump($res);
                exit;
            }
            $has_user = ORM::factory('qwt_login')->where('appid','=',$appid)->find();
            if($has_user->refresh_token){
                echo '授权失败，错误如下：<br>';
                echo '你已经使用该公众号授权过了，对应登陆手机号为：'.$has_user->user;
                exit;
            }
            $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='qwt.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        if($user->appid){
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = $user->appid;
            $wx = new Wxoauth($bid,$options);
            $data['component_appid'] = 'wx4d981fffa8e917e7';
            $data['authorizer_appid'] = $options['appid'];
            $res = $wx->authorizer_user_get($data);
            $arr = @json_decode($res,true);
            // var_dump($arr);
            // exit;
            //图片单独存服务器数据库
            $image_name = DOCROOT."qwt/tmp/$bid/wx_qr_img.jpg";
            umask(0002);
            @mkdir(dirname($image_name),0777,true);
            $ch = curl_init ($arr['authorizer_info']['qrcode_url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $img = curl_exec ($ch);
            curl_close ($ch);
            $fp = fopen($image_name,'w');
            fwrite($fp, $img);
            $cfg = ORM::factory('qwt_cfg');
            $cfg->setCfg($bid, 'wx_qr_img', '', file_get_contents($image_name));
            fclose($fp);
            $user->weixin_name = $arr['authorizer_info']['nick_name'];
            $user->headimg = $arr['authorizer_info']['head_img'];
            $user->save();
        }
        $this->template->content = View::factory('weixin/qwt/admin/oauth')->bind('pre_auth_code',$pre_auth_code)->bind('user',$user)->bind('arr',$arr);
    }
    public function action_self_oauth($id){
        //我们自己的公众号授权我们自己的开放平台。
        $this->template->title = '一键授权';
        //微信授权
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$this->appId;
        $ctoken = $mem->get($cachename1);//获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$ctoken;
        $post_data = array(
          'component_appid' =>$this->appId
        );
        $post_data = json_encode($post_data);
        $res = json_decode($this->request_post($url, $post_data),true);
        // var_dump($res);
        $pre_auth_code = $res['pre_auth_code'];
        $pre_auth_code = substr($pre_auth_code,14);//去掉前缀 preauthcode@@@
        $user = ORM::factory('qwt_oauth')->where('id','=',$id)->find();
        if ($_GET['auth_code']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
            $post_data = array(
              'component_appid' =>$this->appId,
              'authorization_code' =>$_GET['auth_code']
            );
            $post_data = json_encode($post_data);
            $res = json_decode($this->request_post($url, $post_data),true);
            $appid = $res['authorization_info']['authorizer_appid'];
            $access_token = $res['authorization_info']['authorizer_access_token'];
            $refresh_token = $res['authorization_info']['authorizer_refresh_token'];
            $expires_in = time()+7200;
            for($i=0;$res['authorization_info']['func_info'][$i];$i++){
                $auth_info = $auth_info.','.$res['authorization_info']['func_info'][$i]['funcscope_category']['id'];
            }
            // echo $auth_info.'<br>';
            // var_dump($res);
            // if($this->bid==929){
            //     echo '<pre>';
            //     var_dump($res);
            //     Kohana::$log->add('qwt:oauth:$this->bid:', print_r($res,true));
            //     exit;
            // }
            if($res['errcode']){
                Kohana::$log->add('qwt:oauth:{$this->bid}:', print_r($res,true));
                echo '授权失败，错误如下：<br>';
                var_dump($res);
                exit;
            }
            $user = ORM::factory('qwt_oauth')->where('id','=',$id)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='qwt.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        if($user->appid){
            require_once Kohana::find_file('vendor', 'oauth/selfoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = $user->appid;
            $wx = new Wxoauth($bid,$options);
            $data['component_appid'] = 'wx4d981fffa8e917e7';
            $data['authorizer_appid'] = $options['appid'];
            $res = $wx->authorizer_user_get($data);
            $arr = @json_decode($res,true);
            //图片单独存服务器数据库
            // var_dump($arr);
            $user->weixin_name = $arr['authorizer_info']['nick_name'];
            $user->save();
        }
        $this->template->content = View::factory('weixin/qwt/admin/self_oauth')->bind('pre_auth_code',$pre_auth_code)->bind('user',$user)->bind('arr',$arr);
    }
    public function action_images($bid,$key) {
        $field = 'pic';

        $pic = ORM::factory('qwt_cfg')->where('bid','=',$bid)->where('key','=',$key)->find()->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_yzoauth(){
        $bid=$this->bid;
        // echo  $bid."<br>";
        $yzaccess_token = ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        // echo $yzaccess_token."<br>";
        if(!$yzaccess_token){
            $oauth=1;
        }
        if($_GET['yzoauth']==1){
            Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=b8c8058d79f5cca370&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/qwta/callback');
        }
        // echo $oauth."<br>";
        // exit();
        $this->template->content = View::factory('weixin/qwt/admin/yzoauth')->bind('oauth',$oauth);
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"b8c8058d79f5cca370",
            "client_secret"=>"d8e2924a0d4f342ba1266800d38d4da0",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/qwta/callback'
        );
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $result=json_decode($output);
        // echo "<pre>";
        // var_dump($result);
        // echo "<pre>";
        // exit();
        if(isset($result->access_token))
        {
            require Kohana::find_file("vendor","kdt/YZTokenClient");
            $oauth=new YZTokenClient($result->access_token);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            $sid = $value['id'];
            $name = $value['name'];
            $usershop = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            $usershop->yzaccess_token = $result->access_token;
            $usershop->yzexpires_in = time()+$result->expires_in;
            $usershop->yzrefresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->shopname = $name;
            $usershop->shoplogo = $value['logo'];
            $usershop->shopintro = $value['intro'];
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("qwta/yzoauth")."';</script>";
        }
        //Request::instance()->redirect('qwta/home');
    }
    public function action_wxpay(){
        $bid=$this->bid;

        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."wfb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        //$result['rootca_file_exists'] = file_exists($rootca_file);
        if ($_POST['cfg']) {
            $cfg = ORM::factory('qwt_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                // //AppID 填写后不能修改
                // if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }
             //证书上传
            if ($_FILES['cert']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'qwt_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'qwt_file_key', '', file_get_contents($key_file));
            //if (file_exists($rootca_file)) $cfg->setCfg($bid, 'wfb_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('qwt_cfg')->getCfg($bid, 1);
        }
        if($_POST['userinfo']){
            $userinfo = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            $userinfo->name=$_POST['userinfo']['name'];
            $userinfo->save();
        }

        $config = ORM::factory('qwt_cfg')->getCfg($bid, 1);
        $userinfo = ORM::factory('qwt_login')->where('id','=',$bid)->find();

        $this->template->title = '微信支付';
        $this->template->content = View::factory('weixin/qwt/admin/wxpay')
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('userinfo',$userinfo)
            ->bind('bid',$bid);
    }
    //应用开关开始
    public function action_switch() {
        $bid=$this->bid;
        $buys=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('status','=',1)->find_all();
        if ($_POST['iid']) {
            $switch = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$_POST['iid'])->find();
            if ($_POST['value']==0){
                if($_POST['iid'] == 12){
                    $smfyun = Model::factory('smfyun');
                    $res = $smfyun->set_option($this->bid,'location_report',1);
                    var_dump($res);
                    if($res['errcode'] > 0){
                        $result['error'] = '微信公众号【获取用户地理位置】开关打开失败，错误信息：'.$res['errcode'].$res['errmsg'];
                    }
                }
                $switch->switch = 1;
                $switch->save();
            }
            if ($_POST['value']==1){
                $switch->switch = 0;
                $switch->save();
            }
            var_dump($result);
            exit;
        }
        // if ($_POST['switch']){
        //     // var_dump($_POST['item']);
        //     // exit;
        //     foreach ($buys as $k => $v) {
        //         $outuser = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$v->iid)->find();
        //         if($_POST['item']==null){
        //             $outuser->switch = 0;
        //             $outuser->save();
        //         }else{
        //             if(!in_array($v->iid, $_POST['item'])){//将不存在的过期
        //                 $outuser->switch = 0;
        //                 $outuser->save();
        //             }
        //         }
        //     }
        //     if($_POST['item']!=null){
        //         foreach ($_POST['item'] as $k => $v) {
        //             echo $k.'<br>';
        //             $buser = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$k)->find();
        //             $buser->switch = 1;
        //             $buser->lastupdate = time();
        //             $buser->save();
        //         }
        //     }
        //     $result['ok'] = 1;
        //     $buys=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('status','=',1)->where('expiretime','>',time())->find_all();
        // }
        $this->template->title = '账户信息';
        $this->template->content = View::factory('weixin/qwt/admin/switch')->bind('bid',$bid)->bind('buys',$buys)->bind('result',$result);
    }
    //应用开关结束
    public function action_userinfo(){
        $bid=$this->bid;
        $code=ORM::factory('qwt_login')->where('id','=',$bid)->find()->code;
        if($code!=0){
           $exist=ORM::factory('qwt_sku')->where('bid','=',$bid)->where('iid','=',1)->where('show1','=',1)->count_all();
           if($exist==0){
                $skus1=ORM::factory('qwt_sku')->where('bid','=',0)->find_all();
                 foreach ($skus1 as $element) {
                    $skus=ORM::factory('qwt_sku');
                    $skus->bid=$bid;
                    $skus->iid=$element->iid;
                    $skus->name=$element->name;
                    $skus->show1=$element->show1;
                    $skus->price=$element->price;
                    $skus->time=$element->time;
                    $skus->save();
                }
            }
        }
        if($_POST['userinfo']){
            $userinfo = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            // $userinfo->name=$_POST['userinfo']['name'];
            // $userinfo->user=$_POST['userinfo']['user'];
            // $userinfo->weixin_name=$_POST['userinfo']['weixin_name'];
            $userinfo->pass = $_POST['userinfo']['pass'];
            $userinfo->save();
            $success='ok';
        }
        $userinfo = ORM::factory('qwt_login')->where('id','=',$bid)->find();

        $this->template->title = '账户信息';
        $this->template->content = View::factory('weixin/qwt/admin/userinfo')
            ->bind('userinfo',$userinfo)
            ->bind('success',$success)
            ->bind('bid',$bid);
    }
    //试用自定义菜单开始
    public function action_diy() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_cfg')->getCfg($bid, 1);
        $i = 0;
        foreach ($config['menu_cfg'] as $k => $v) {
            $buy = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('switch','=',1)->where('status','=',1)->where('expiretime','>',time())->where('iid','=',$v['iid'])->find();
            if($buy->id){
                $menu[$i]['name'] = $v['name'];
                $menu[$i]['iid'] = $v['iid'];
                $menu[$i]['type'] = $v['type'];
                if($v['url']){
                    $menu[$i]['url'] = str_replace('%s', $bid, $v['url']);
                }
                $i++;
            }

        }
        //菜单配置
        if ($_POST['menu']) {

            $buser = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            if(!$buser->appid){
                $res['error'] = '请在【绑定我们】->【微信一键授权】处，点击【一键授权】';
            }
            // var_dump(json_decode($_POST['menu'],true));
            // exit;
            Kohana::$log->add('qwt_menu:{'.$bid.'}:input', print_r($_POST['menu'],true));
            $menu_edit = json_decode($_POST['menu'],true);
            $limit = 0;
            if($menu_edit[0][1]['do']=='yes') $menu_edit[0][0]['type']='';
            if($menu_edit[1][1]['do']=='yes') $menu_edit[1][0]['type']='';
            if($menu_edit[2][1]['do']=='yes') $menu_edit[2][0]['type']='';

            foreach ($menu_edit as $k => $v) {
                foreach ($v as $key => $value) {
                    if($value['type']=='qrcode'&&$value['do']=='yes'){//判断是否有多个生成海报的菜单
                        $limit++;
                    }
                }
            }
            if($limit>=2){//海报菜单冲突
                $res['error'] = '菜单中有多个生成海报的菜单产生冲突！请重新设置';
            }
            // var_dump($menu_edit);
            if($res['error']){
                echo json_encode($res);
                exit;
            }else{
                //配置菜单更新
                require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
                $options['token'] = $this->token;
                $options['encodingaeskey'] = $this->encodingAesKey;
                $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
                if(!$bid) Kohana::$log->add('qwtwfbbid:', 'meun');//写入日志，可以删除
                $wx = new Wxoauth($bid,$options);
                $m = 0;
                for ($a=0; $a<=2; $a++) {
                    if($menu_edit[$m][0]['do']=='yes'){
                        if($menu_edit[$m][1]['do']=='yes'){
                            $data['button'][$m]['name']=trim($menu_edit[$m][0]['text']);
                            for ($i=1; $i <=5 ; $i++) {
                                if($menu_edit[$m][$i]['do']=='yes'){
                                    switch ($menu_edit[$m][$i]['type']) {
                                        case 'qrcode':
                                            $type = 'click';
                                            $key = 'qrcode';
                                            $menu_edit[$m][$i]['k_key'] = 'qrcode';
                                            break;
                                        case 'item':
                                            $type = 'click';
                                            $key = 'item';
                                            $menu_edit[$m][$i]['k_key'] = 'item';
                                            break;
                                        case 'score':
                                            $type = 'click';
                                            $key = 'score';
                                            $menu_edit[$m][$i]['k_key'] = 'score';
                                            break;
                                        case 'url':
                                            $type = 'view';
                                            $key = $menu_edit[$m][$i]['keyword'];
                                            break;
                                        case '_text':
                                            $type = 'click';
                                            $key = '_text_'.$bid.$m.$i;
                                            $menu_edit[$m][$i]['k_key'] = $key;
                                            break;
                                        case '_url':
                                            $type = 'view';
                                            $key = $menu_edit[$m][$i]['keyword'];
                                            break;
                                    }
                                    if($menu_edit[$m][$i]['type']=='score'||$menu_edit[$m][$i]['type']=='qrcode'||$menu_edit[$m][$i]['type']=='item'||$menu_edit[$m][$i]['type']=='_text'){
                                        $data['button'][$m]['sub_button'][$i-1]['key']=$key;
                                    }else{
                                        $data['button'][$m]['sub_button'][$i-1]['url']=trim($key);
                                    }
                                    $data['button'][$m]['sub_button'][$i-1]['type']=$type;
                                    $data['button'][$m]['sub_button'][$i-1]['name']=trim($menu_edit[$m][$i]['text']);
                                }
                            }
                        }else{
                            switch ($menu_edit[$m][0]['type']) {
                                case 'qrcode':
                                    $type = 'click';
                                    $key = 'qrcode';
                                    $menu_edit[$m][0]['k_key'] = 'qrcode';
                                    break;
                                case 'score':
                                    $type = 'click';
                                    $key = 'score';
                                    $menu_edit[$m][$i]['k_key'] = 'score';
                                    break;
                                case 'item':
                                    $type = 'click';
                                    $key = 'item';
                                    $menu_edit[$m][$i]['k_key'] = 'item';
                                    break;
                                case 'url':
                                    $type = 'view';
                                    $key = $menu_edit[$m][0]['keyword'];
                                    break;
                                case '_text':
                                    $type = 'click';
                                    $key = '_text_'.$bid.$m.'0';
                                    $menu_edit[$m][0]['k_key'] = $key;
                                    break;
                                case '_url':
                                    $type = 'view';
                                    $key = $menu_edit[$m][0]['keyword'];
                                    break;
                            }
                            if($menu_edit[$m][0]['type']=='score'||$menu_edit[$m][0]['type']=='qrcode'||$menu_edit[$m][0]['type']=='item'||$menu_edit[$m][0]['type']=='_text'){
                                $data['button'][$m]['key']=$key;
                            }else{
                                $data['button'][$m]['url']=trim($key);
                            }
                            $data['button'][$m]['type']=$type;
                            $data['button'][$m]['name']=trim($menu_edit[$m][0]['text']);
                        }
                        $m = $m+1;
                    }
                }
            }

            $menu = $wx->createMenu($data);
            Kohana::$log->add('qwt_menu:{'.$bid.'}:menu_end1:', print_r($data,true));
            Kohana::$log->add('qwt_menu:{'.$bid.'}:menu_end2:', print_r(json_encode($data),true));
            Kohana::$log->add('qwt_menu:{'.$bid.'}:menu_end3:', print_r($menu,true));
            if($menu['errcode']==0){
                $res['error'] = '菜单配置成功!';
                foreach ($menu_edit as $key => $value) {
                    foreach ($value as $k => $v) {
                        $menu_tb = ORM::factory('qwt_menu')->where('bid','=',$bid)->where('menu','=',$key)->where('lv','=',$k)->find();
                        $menu_tb->bid = $bid;
                        $menu_tb->menu = $key;
                        $menu_tb->lv = $k;
                        $menu_tb->text = $v['text'];
                        $menu_tb->iid = $v['iid'];
                        $menu_tb->keyword = $v['keyword'];
                        $menu_tb->k_key = $v['k_key'];
                        $menu_tb->type = $v['type'];
                        $menu_tb->save();
                    }
                }
                echo json_encode($res);
                exit;
            }else{
                $res['error'] = '菜单配置失败!'.$menu['errcode'].'：'.$menu['errmsg'];
                echo json_encode($res);
                exit;
            }
            //重新读取配置
            $config = ORM::factory('qwt_cfg')->getCfg($bid, 1);
        }
        $menu_0 = ORM::factory('qwt_menu')->where('bid','=',$bid)->where('menu','=',0)->find_all();
        $menu_1 = ORM::factory('qwt_menu')->where('bid','=',$bid)->where('menu','=',1)->find_all();
        $menu_2 = ORM::factory('qwt_menu')->where('bid','=',$bid)->where('menu','=',2)->find_all();

        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/qwt/admin/diy')
            ->bind('menu_0', $menu_0)
            ->bind('menu_1', $menu_1)
            ->bind('menu_2', $menu_2)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('menu', $menu)
            ->bind('bid',$bid);
    }
    //试用自定义菜单结束
    public function action_order(){
        $bid=$this->bid;

        $this->action_refreshbuy();

        $orders=ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('status','=',1);
        $orders=$orders->reset(FALSE);
        $countall=$orders->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');

        $result['orders'] = $orders->order_by('buy_id', 'DESC')->order_by('rebuy_time','DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['countall']=$countall;
        $this->template->title = '订购记录';
        $this->template->content = View::factory('weixin/qwt/admin/order')
            ->bind('result',$result)
            ->bind('pages',$pages)
            ->bind('bid',$bid);
    }
    public function action_rebuy(){
        $this->action_refreshbuy();
        $bid=$this->bid;
        $rebuys=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('status','=',1);
        $rebuys=$rebuys->reset(FALSE);
        $countall=$rebuys->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');
        $result['rebuys'] = $rebuys->order_by('id','DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['countall']=$countall;
        $this->template->title = '续费信息';
        $this->template->content = View::factory('weixin/qwt/admin/rebuy')
            ->bind('result',$result)
            ->bind('pages',$pages)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['qwta']['admin'] < 1) Request::instance()->redirect('qwta/userinfo');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);
        $bid =$this->bid;
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find()->admin;
        if($admin==100){
            $logins = ORM::factory('qwt_login');
            $logins = $logins->reset(FALSE);
        }else{
            $logins = ORM::factory('qwt_login')->where('fubid','=',$bid);
            $logins = $logins->reset(FALSE);
        }
       if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $logins = $logins->where('user', 'like', $s)->or_where('name', 'like', $s);
        }

        $result['countall'] = $countall = $logins->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/qwt/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result);
    }

    public function action_logins_add() {
        if ($_SESSION['qwta']['admin'] < 2) Request::instance()->redirect('qwta/userinfo');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('qwt_login');
            $login->values($_POST['data']);
            if (!$_POST['pass'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                // $login->expiretime = strtotime($_POST['expiretime']);
                $login->save();
                //修改商户权限
                $buserid = $login->id;
                foreach ($_POST['item'] as $k => $v) {
                    $buser = ORM::factory('qwt_buy');
                    $buser->bid = $buserid;
                    $buser->iid = $v;
                    $buser->buy_time = time();
                    $buser->status = 1;
                    $buser->lastupdate = time();
                    if($v==1){
                        $buser->hbnum = $_POST['pro'][$k];
                        $buser->expiretime = time()+3600*24*30*12;
                    }else{
                        $buser->expiretime = strtotime($_POST['pro'][$k]);
                    }
                    $buser->save();
                }
                Request::instance()->redirect('qwta/logins');
            }
        }

        $result['action'] = 'add';
        $items =  ORM::factory('qwt_item')->find_all();
        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/qwt/admin/logins_add')
            ->bind('result', $result)
            ->bind('items', $items);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['qwta']['admin'] < 2) Request::instance()->redirect('qwta/userinfo');

        $bid = $this->bid;

        $login = ORM::factory('qwt_login', $id);
        if (!$login) die('404 Not Found!');

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                // $login->expiretime = strtotime($_POST['expiretime']);
                $login->save();
                //修改商户权限
                $buys = ORM::factory('qwt_buy')->where('bid','=',$id)->find_all();
                foreach ($buys as $k => $v) {
                    $outuser = ORM::factory('qwt_buy')->where('bid','=',$id)->where('iid','=',$v->iid)->find();
                    if(!in_array($v->iid, $_POST['item'])){//将不存在的过期
                        $outuser->expiretime = time();
                        $outuser->status = 0;
                        $outuser->save();
                    }
                }
                foreach ($_POST['item'] as $k => $v) {
                    $buser = ORM::factory('qwt_buy')->where('bid','=',$id)->where('iid','=',$v)->find();
                    $buser->bid = $id;
                    $buser->iid = $v;
                    $buser->buy_time = time();
                    $buser->status = 1;
                    $buser->lastupdate = time();
                    if($v==1){
                        $buser->hbnum = $_POST['pro'][$k];
                        $buser->expiretime = time()+3600*24*30*12;
                    }else{
                        $buser->expiretime = strtotime($_POST['pro'][$k]);
                    }
                    $buser->save();
                    if($_POST['zhibo']){
                        $user = ORM::factory('qwt_login')->where('id','=',$id)->find();
                        $user->stream_data = $_POST['zhibo'][11];
                        $user->save();
                    }
                }
                Request::instance()->redirect('qwta/logins');
            }
        }

        $_POST['data'] = $result['login'] = $login->as_array();
        $items =  ORM::factory('qwt_item')->find_all();
        $result['action'] = 'edit';
        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/qwt/admin/logins_add')
            ->bind('result', $result)
            ->bind('bid', $id)
            ->bind('items', $items);
    }
    public function action_products(){
        $bid=$this->bid;
        $fubid=ORM::factory('qwt_login')->where('id','=',$bid)->find()->fubid;
        $result['system'] = ORM::factory('qwt_item')->where('type','=','1')->find_all();
        $result['plug'] = ORM::factory('qwt_item')->where('type','=','2')->find_all();
        $result['title'] = $this->template->title = '购买应用';
        $this->template->content = View::factory('weixin/qwt/admin/item')
            ->bind('fubid',$fubid)
            ->bind('result', $result)
            ->bind('bid', $this->bid);
    }
    public function action_product_preview($iid){
        if ($_SESSION['qwta']['bid']) {
            Request::instance()->redirect('/qwta/product/'.$iid);
        }
        $this->template = 'tpl/blank';
        self::before();
        $result['product'] = ORM::factory('qwt_item')->where('id','=',$iid)->find();
        $result['sku'] = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->order_by('tryout', 'DESC')->find_all();
        $oldprice = 0;
        $shprice = 0;
        // $oldprice=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('show1','=',1)->find()->old_price;
        // $shprice=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('show1','=',1)->find()->price;
        $count=ORM::factory('qwt_item')->where('id','=',$iid)->find()->count;
        $result['title'] = $this->template->title = $result['product']->name;
        $this->template->content = View::factory('weixin/qwt/tpl/product_preview')
            ->bind('shprice',$shprice)
            ->bind('oldprice',$oldprice)
            ->bind('count',$count)
            ->bind('iid',$iid)
            ->bind('result', $result);
    }
    public function action_product($iid){
        if(!$iid) return $this->action_products();
        $bid =$this->bid;
        $result['product'] = ORM::factory('qwt_item')->where('id','=',$iid)->find();
        $fubid=ORM::factory('qwt_login')->where('id','=',$bid)->find()->fubid;
        //$qrcode_edit->flag==1||$flogin->flag==1
        $exist=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->id;
        $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $flogin=ORM::factory('qwt_login')->where('id','=',$qrcode_edit->fubid)->find();
        $zkprice=0;
        if($exist){
            if($qrcode_edit->flag==1||$flogin->flag==1){
                if($qrcode_edit->flag==1){
                    $bid1=$qrcode_edit->id;
                }else{
                    $bid1=$flogin->id;
                }
                $cfg=ORM::factory('qwt_cfg')->getCfg($bid1,1);
                $showskuid=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->find()->id;
                $dailisku=ORM::factory('qwt_dlsku')->where('bid','=',$bid1)->where('iid','=',$iid)->where('sid','=',$showskuid)->where('state','=',1)->find();
                if($cfg['ifdiscount']==1&&$dailisku->id){
                    $zkprice=ceil(ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->find()->price*$cfg['discount']/100);
                }
            }
            $result['sku'] = ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('tryout','=',0)->find_all();
            $result['tryout'] = ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('tryout','=',1)->find();
            $shprice=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->find()->price;
            $oldprice=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->find()->old_price;
        }else{
            if($qrcode_edit->flag==1||$flogin->flag==1){
                if($qrcode_edit->flag==1){
                    $bid1=$qrcode_edit->id;
                }else{
                    $bid1=$flogin->id;
                }
                $cfg=ORM::factory('qwt_cfg')->getCfg($bid1,1);
                $showskuid=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('show1','=',1)->find()->id;
                $dailisku=ORM::factory('qwt_dlsku')->where('bid','=',$bid1)->where('iid','=',$iid)->where('sid','=',$showskuid)->where('state','=',1)->find();
                if($cfg['ifdiscount']==1&&$dailisku->id){
                    $zkprice=ceil(ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('show1','=',1)->find()->price*$cfg['discount']/100);
                }
            }
            $result['sku'] = ORM::factory('qwt_sku')->where('bid','=',0)->where('tryout','=',0)->where('iid','=',$iid)->find_all();
            $result['tryout'] = ORM::factory('qwt_sku')->where('bid','=',0)->where('tryout','=',1)->where('iid','=',$iid)->find();
            $oldprice=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('show1','=',1)->find()->old_price;
            $shprice=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$iid)->where('show1','=',1)->find()->price;
        }
        $count=ORM::factory('qwt_item')->where('id','=',$iid)->find()->count;
        //$result['sku'] = ORM::factory('qwt_sku')->where('iid','=',$iid)->find_all();
        $result['title'] = $this->template->title = $result['product']->name;
        $this->template->content = View::factory('weixin/qwt/admin/product')
            ->bind('shprice',$shprice)
            ->bind('oldprice',$oldprice)
            ->bind('zkprice',$zkprice)
            ->bind('count',$count)
            ->bind('result', $result)
            ->bind('bid', $this->bid);
    }
    public function action_buy($skuid,$orderid){
        $this->template = 'tpl/blank';
        self::before();

        $sku = ORM::factory('qwt_sku')->where('id','=',$skuid)->find();
        $buser = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',$sku->iid)->find();
        if($buser->id){//续费

        }else{//初次购买
            $buser->iid=$sku->iid;
            $buser->buy_time = time();
            $buser->expiretime = time();
            $buser->bid = $this->bid;
        }
        $buser->save();
        // require_once Kohana::find_file("vendor",'unit/phpqrcode/phpqrcode');
        // require_once Kohana::find_file("vendor","lib/WxPay.Api");
        // require_once Kohana::find_file("vendor","unit/WxPay.NativePay");
        // require_once Kohana::find_file("vendor","unit/log");
        $productname = ORM::factory('qwt_item')->where('id','=',$sku->iid)->find()->name;
        $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $flogin=ORM::factory('qwt_login')->where('id','=',$qrcode_edit->fubid)->find();
        $sjprice=$sku->price;
        if($qrcode_edit->flag==1||$flogin->flag==1){
            if($qrcode_edit->flag==1){
                $bid1=$qrcode_edit->id;
            }else{
                $bid1=$flogin->id;
            }
            $cfg=ORM::factory('qwt_cfg')->getCfg($bid1,1);
            $dailisku=ORM::factory('qwt_dlsku')->where('bid','=',$bid1)->where('sid','=',$skuid)->where('state','=',1)->find();
            if($cfg['ifdiscount']==1&&$dailisku->id){
                $sjprice=ceil($sku->price*$cfg['discount']/100);
            }
        }
        // $no = $orderid;
        // $notify = new NativePay();
        // $input = new WxPayUnifiedOrder();
        // $input->SetBody($productname);
        // $input->SetAttach($this->bid.'.'.$sku['id']);
        // $input->SetOut_trade_no($no);
        // $input->SetTotal_fee($sku['price']*100);
        // $input->SetTime_start(date("YmdHis"));
        // $input->SetTime_expire(date("YmdHis", time() + 600));
        // $input->SetGoods_tag($productname);
        // $input->SetNotify_url('http://'.$_SERVER["HTTP_HOST"].'/qwta/notify');
        // $input->SetTrade_type("NATIVE");
        // $input->SetProduct_id($sku['id']);
        // $input->SetSpbill_create_ip("127.0.0.1");
        // // var_dump($input);
        // // echo '<br>';
        // $result = $notify->GetPayUrl($input);
        // // var_dump($result);
        // // echo '<br>';
        // $url = $result["code_url"];
        // //echo $url.'<br>';
        // $url = urldecode($url);
        // //header('Content-type: image/jpg');
        // $res = QRcode::png($url);
        // //echo $res.'<br>';
        // require Kohana::find_file("vendor/kdt","KdtApiClient");

        // $appId = 'c27bdd1e37cd8300fb';
        // $appSecret = '3e7d8db9463b1e2fd92083418677c638';
        // $client = new KdtApiClient($appId, $appSecret);

        // $method = 'kdt.pay.qrcode.createQrCode';
        // $params = [
        //     'qr_name' =>$productname.'-营销应用平台',

        //     'qr_price' => $sjprice*100,
        //     //'qr_price' => 1,
        //     'qr_type' => 'QR_TYPE_DYNAMIC',
        //     // 'qr_source'=>$_POST['type'].'.'.$_POST['stream'],//类型和流量拼接  给了跟不给  获取不到  没有给的意义
        // ];
        // $test=$client->post($method, $params);
        require_once Kohana::find_file("vendor","kdt/YZTokenClient");
        $smfyun = ORM::factory('qwt_login')->where('id','=',6)->find();
        $oauth=new YZTokenClient($smfyun->yzaccess_token);
        $params = [
            'qr_name' =>$productname.'-营销应用平台',

            'qr_price' => $sjprice*100,
            //'qr_price' => 1,
            'qr_type' => 'QR_TYPE_DYNAMIC',
            // 'qr_source'=>$_POST['type'].'.'.$_POST['stream'],//类型和流量拼接  给了跟不给  获取不到  没有给的意义
        ];
        $test = $oauth->get('youzan.pay.qrcode.create',$this->methodVersion,$params);//生成付款二维码
        // header('Content-type: image/jpg');
        // echo "<img src='".$test['response']['qr_code']."'>";
        // echo $test['response']['qr_id'];
        // var_dump($test);
        // exit;

        $rebuy = ORM::factory('qwt_rebuy')->where('status','=',0)->where('bid','=',$this->bid)->where('sku_id','=',$sku->id)->find();
        $rebuy->qrid = $test['response']['qr_id'];
        $rebuy->tid = $orderid;
        $rebuy->bid = $this->bid;
        $rebuy->buy_id = $buser->id;
        $rebuy->rebuy_price = $sjprice;
        $rebuy->price = $sku->price;
        $rebuy->rebuy_time = time();
        $rebuy->sku_id = $sku->id;
        $rebuy->save();

        $data = array('imgurl' => $test['response']['qr_code'],'qrid' =>$test['response']['qr_id'],'url'=>$test['response']['qr_url']);
        echo json_encode($data);
        exit;
    }
    public function action_notify_qr($qr_id){
        //6079962
        // require_once Kohana::find_file("vendor/kdt","KdtApiClient");

        // $appId = 'c27bdd1e37cd8300fb';
        // $appSecret = '3e7d8db9463b1e2fd92083418677c638';
        // $client = new KdtApiClient($appId, $appSecret);
        // $method1 = 'kdt.trades.qr.get';
        // $params = [
        //     'status' =>'TRADE_RECEIVED'
        // ];

        // $resultarr=$client->post($method1,$params);

        require_once Kohana::find_file("vendor","kdt/YZTokenClient");
        $smfyun = ORM::factory('qwt_login')->where('id','=',6)->find();
        $oauth=new YZTokenClient($smfyun->yzaccess_token);
        $params = [
            'status' =>'TRADE_RECEIVED',
            'page_size'=>50
        ];
        $resultarr = $oauth->get('youzan.trades.qr.get',$this->methodVersion,$params);//查询订单
        echo '<pre>';
        var_dump($resultarr);
        exit;
        $qrarr=$resultarr["response"]["qr_trades"];
        echo '<pre>';
        var_dump($qrarr);
        for($i=0;$qrarr[$i];$i++){
            if($qrarr[$i]['qr_id']==$qr_id){
                $flag = 1;
                echo '付款成功';
            }
        }
        exit;
    }
    public function action_refreshbuy(){
        $bid = $this->bid;
        $wait_pay = ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('status','=',0)->find_all();//找出未支付
        // require_once Kohana::find_file("vendor",'unit/phpqrcode/phpqrcode');
        // require_once Kohana::find_file("vendor","lib/WxPay.Api");
        // require_once Kohana::find_file("vendor","unit/WxPay.MicroPay");
        // require_once Kohana::find_file("vendor","unit/log");
        // $notify = new MicroPay();
        // $succCode = 2;

        // require_once Kohana::find_file("vendor/kdt","KdtApiClient");

        // $appId = 'c27bdd1e37cd8300fb';
        // $appSecret = '3e7d8db9463b1e2fd92083418677c638';
        // $client = new KdtApiClient($appId, $appSecret);

        // $method1 = 'kdt.trades.qr.get';
        // $params = [
        //     'status' =>'TRADE_RECEIVED'
        // ];

        // $resultarr=$client->post($method1,$params);
        require_once Kohana::find_file("vendor","kdt/YZTokenClient");
        $smfyun = ORM::factory('qwt_login')->where('id','=',6)->find();
        $oauth=new YZTokenClient($smfyun->yzaccess_token);
        $params = [
            'status' =>'TRADE_RECEIVED',
            'page_size'=>50
        ];
        $resultarr = $oauth->get('youzan.trades.qr.get',$this->methodVersion,$params);//查询订单
        $qrarr = $resultarr["response"]["qr_trades"];

        foreach ($wait_pay as $v) {
            if($v->rebuy_time<(time()-3*3600)){//删除无效订单
                $v->delete();
                continue;
            }
            // $res = $notify->query($v->tid,$succCode);
            $flag = 0;
            for($i=0;$qrarr[$i];$i++){
                if($qrarr[$i]['qr_id']==$v->qrid){
                    $flag = 1;
                }
            }
            if($flag==1){//购买成功
                $sku = ORM::factory('qwt_sku')->where('id','=',$v->sku_id)->find();
                $buser = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$sku->iid)->find();
                if($sku->iid==1||$sku->iid==14){//如果是红包
                    $buser->hbnum = $buser->hbnum+$sku->time;
                    if($buser->expiretime<time()){
                        $buser->expiretime = time()+12*30*24*3600;
                    }else{
                        $buser->expiretime = $buser->expiretime+12*30*24*3600;
                    }
                }else{
                    // if($sku->iid==14){
                    //     $buser->hbnum = $buser->hbnum+$sku->time;
                    //     if($buser->expiretime<time()){
                    //         $buser->expiretime = time()+12*30*24*3600;
                    //     }else{
                    //         $buser->expiretime = $buser->expiretime+12*30*24*3600;
                    //     }
                    // }
                    if($sku->iid==11){//直播就赠送500g流量
                        $login = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                        $login->stream_data = $login->stream_data+500;
                        $login->save();
                    }
                    if($buser->expiretime<time()){
                        $buser->expiretime = time()+$sku->time*30*24*3600;
                    }else{
                        $buser->expiretime = $buser->expiretime+$sku->time*30*24*3600;
                    }
                }
                $buser->bid = $this->bid;
                $buser->status = 1;
                $buser->save();
                $rebuy = ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('tid','=',$v->tid)->find();
                $rebuy->status = 1;
                //$rebuy->rebuy_price = $sku->price;
                $rebuy->rebuy_time = time();
                $rebuy->sku_id = $sku->id;
                $rebuy->save();
                kohana::$log->add("1wdlbid$this->bid",print_r($this->bid,true));
                kohana::$log->add("1wdlsku$this->bid",print_r($sku->id,true));
                //代理加收益
                $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                $flogin=ORM::factory('qwt_login')->where('id','=',$qrcode_edit->fubid)->find();
                kohana::$log->add('1wdlbid1',print_r($qrcode_edit->flag,true));
                kohana::$log->add('1wdlbid2',print_r($flogin->flag,true));
                if($qrcode_edit->flag==1||$flogin->flag==1){
                    if($qrcode_edit->flag==1){
                        $dlsku=ORM::factory('qwt_dlsku')->where('bid','=',$qrcode_edit->id)->where('sid','=',$sku->id)->where('state','=',1)->find();
                        kohana::$log->add('1wdlbid3',print_r($dlsku->price,true));
                        kohana::$log->add('1wdlbid4',print_r($dlsku->old_price,true));
                        kohana::$log->add('1wdlbid5',print_r($dlsku->price,true));
                        if($dlsku->price&&$dlsku->old_price>=$dlsku->price){
                            ORM::factory('qwt_score')->scoreIn($qrcode_edit,0,$dlsku->old_price-$dlsku->price,$dlsku->id,$rebuy->id,$qrcode_edit->id,'');
                        }
                    }elseif ($flogin->flag==1) {
                        $dlsku=ORM::factory('qwt_dlsku')->where('bid','=',$flogin->id)->where('sid','=',$sku->id)->where('state','=',1)->find();
                        kohana::$log->add('1wdlbid6',print_r($dlsku->price,true));
                        kohana::$log->add('1wdlbid7',print_r($dlsku->old_price,true));
                        kohana::$log->add('1wdlbid8',print_r($dlsku->price,true));
                        if($dlsku->price&&$dlsku->old_price>=$dlsku->price){
                            ORM::factory('qwt_score')->scoreIn($flogin,0,$dlsku->old_price-$dlsku->price,$dlsku->id,$rebuy->id,$qrcode_edit->id,'');
                        }
                    }

                }
                $item = ORM::factory('qwt_item')->where('id','=',$sku->iid)->find();
                $item->count = $item->count+1;
                $item->save();
            }
        }
    }
    public function action_notify($orderid,$sku_id,$qrid){
        $this->template = 'tpl/blank';
        $bid=$this->bid;
        $this->config=$config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        self::before();
        if(ORM::factory('qwt_rebuy')->where('bid','=',$this->bid)->where('status','=',1)->where('tid','=',$orderid)->find()->id){
            $content = '您已经刚刚购买成功了，请前往【插件中心】查看';
            echo $content;
            return;
        }
        if ($qrid=='tryout') {
            $iid = ORM::factory('qwt_sku')->where('id','=',$sku_id)->find()->iid;
            $check = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',$iid)->where('status','=',1)->find();
            if ($check->id) {
                $content = '开通失败，您已经使用过该产品了';
                echo $content;
                return;
            }else{
                $check2 = ORM::factory('qwt_rebuy')->where('bid','=',$this->bid)->where('buy_id','=',$check->id)->where('status','=',1)->find();
                if ($check2->id) {
                    $content = '开通失败，您已经使用过该产品了';
                    echo $content;
                    return;
                }else{
                    $sku = ORM::factory('qwt_sku')->where('id','=',$sku_id)->find();
                    $buser = ORM::factory('qwt_buy');
                    $buser->iid = $sku->iid;
                    if($sku->iid==1||$sku->iid==14){//如果是红包
                        $buser->hbnum = $buser->hbnum+$config['hbnum'];
                        $buser->expiretime = time()+$config['day']*24*3600;
                    }else{
                        if($sku->iid==11){//直播就赠送1g流量
                            $login = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                            $login->stream_data = $login->stream_data+$config['wzbnum'];
                            $login->save();
                        }
                        $buser->expiretime = time()+$config['day']*24*3600;
                    }
                    $buser->bid = $this->bid;
                    $buser->status = 1;
                    $buser->save();
                    $rebuy = ORM::factory('qwt_rebuy');
                    $rebuy->bid = $this->bid;
                    $rebuy->tid = $orderid;
                    $rebuy->status = 1;
                    $rebuy->buy_id = $buser->id;
                    $rebuy->experience = 1;
                    //$rebuy->rebuy_price = $sku->price;
                    $rebuy->rebuy_time = time();
                    $rebuy->sku_id = $sku->id;
                    $rebuy->save();
                    kohana::$log->add("wdlbidtryout$this->bid",print_r($this->bid,true));
                    kohana::$log->add("wdlskutryout$this->bid",print_r($sku->id,true));

                    //加销量
                    $item = ORM::factory('qwt_item')->where('id','=',$sku->iid)->find();
                    $item->count = $item->count+1;
                    $item->save();

                    $content = '开通成功';
                    echo $content;
                    return;
                }
            }
        }
        // require_once Kohana::find_file("vendor",'unit/phpqrcode/phpqrcode');
        // require_once Kohana::find_file("vendor","lib/WxPay.Api");
        // require_once Kohana::find_file("vendor","unit/WxPay.MicroPay");
        // require_once Kohana::find_file("vendor","unit/log");
        // $notify = new MicroPay();
        // $succCode = 2;
        // $res = $notify->query($orderid,$succCode);
        // require_once Kohana::find_file("vendor/kdt","KdtApiClient");

        // $appId = 'c27bdd1e37cd8300fb';
        // $appSecret = '3e7d8db9463b1e2fd92083418677c638';
        // $client = new KdtApiClient($appId, $appSecret);

        // $method1 = 'kdt.trades.qr.get';
        // $params = [
        //     'status' =>'TRADE_RECEIVED',
        // ];

        // $resultarr=$client->post($method1,$params);
        // $qrarr=$resultarr["response"]["qr_trades"];

        require_once Kohana::find_file("vendor","kdt/YZTokenClient");
        $smfyun = ORM::factory('qwt_login')->where('id','=',6)->find();
        $oauth=new YZTokenClient($smfyun->yzaccess_token);
        $params = [
            'status' =>'TRADE_RECEIVED',
            'page_size'=>50
        ];
        $resultarr = $oauth->get('youzan.trades.qr.get',$this->methodVersion,$params);//查询订单
        $qrarr = $resultarr["response"]["qr_trades"];

        $flag = 0;
        for($i=0;$qrarr[$i];$i++){
            if($qrarr[$i]['qr_id']==$qrid){
                $flag = 1;
            }
        }
        if($flag==1){
            $sku = ORM::factory('qwt_sku')->where('id','=',$sku_id)->find();
            $buser = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',$sku->iid)->find();
            if($sku->iid==1||$sku->iid==14){//如果是红包
                $buser->hbnum = $buser->hbnum+$sku->time;
                if($buser->expiretime<time()){
                    $buser->expiretime = time()+12*30*24*3600;
                }else{
                    $buser->expiretime = $buser->expiretime+12*30*24*3600;
                }
            }else{
                // if($sku->iid==14){
                //     $buser->hbnum = $buser->hbnum+$sku->time;
                //     if($buser->expiretime<time()){
                //         $buser->expiretime = time()+12*30*24*3600;
                //     }else{
                //         $buser->expiretime = $buser->expiretime+12*30*24*3600;
                //     }
                // }
                if($sku->iid==11){//直播就赠送500g流量
                    $login = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                    $login->stream_data = $login->stream_data+500;
                    $login->save();
                }
                if($buser->expiretime<time()){
                    $buser->expiretime = time()+$sku->time*30*24*3600;
                }else{
                    $buser->expiretime = $buser->expiretime+$sku->time*30*24*3600;
                }
            }
            $buser->bid = $this->bid;
            $buser->status = 1;
            $buser->save();
            $rebuy = ORM::factory('qwt_rebuy')->where('bid','=',$this->bid)->where('tid','=',$orderid)->find();
            $rebuy->status = 1;
            //$rebuy->rebuy_price = $sku->price;
            $rebuy->rebuy_time = time();
            $rebuy->sku_id = $sku->id;
            $rebuy->save();
            kohana::$log->add("wdlbid$this->bid",print_r($this->bid,true));
            kohana::$log->add("wdlsku$this->bid",print_r($sku->id,true));
            //代理加收益
            $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            $flogin=ORM::factory('qwt_login')->where('id','=',$qrcode_edit->fubid)->find();
            kohana::$log->add('wdlbid1',print_r($qrcode_edit->flag,true));
            kohana::$log->add('wdlbid2',print_r($flogin->flag,true));
            if($qrcode_edit->flag==1||$flogin->flag==1){
                if($qrcode_edit->flag==1){
                    $dlsku=ORM::factory('qwt_dlsku')->where('bid','=',$qrcode_edit->id)->where('sid','=',$sku->id)->where('state','=',1)->find();
                    kohana::$log->add('wdlbid3',print_r($dlsku->price,true));
                    kohana::$log->add('wdlbid4',print_r($dlsku->old_price,true));
                    kohana::$log->add('wdlbid5',print_r($dlsku->price,true));
                    if($dlsku->price&&$dlsku->old_price>=$dlsku->price){
                        ORM::factory('qwt_score')->scoreIn($qrcode_edit,0,$dlsku->old_price-$dlsku->price,$dlsku->id,$rebuy->id,$qrcode_edit->id,'');
                    }
                }elseif ($flogin->flag==1) {
                    $dlsku=ORM::factory('qwt_dlsku')->where('bid','=',$flogin->id)->where('sid','=',$sku->id)->where('state','=',1)->find();
                    kohana::$log->add('wdlbid6',print_r($dlsku->price,true));
                    kohana::$log->add('wdlbid7',print_r($dlsku->old_price,true));
                    kohana::$log->add('wdlbid8',print_r($dlsku->price,true));
                    if($dlsku->price&&$dlsku->old_price>=$dlsku->price){
                        ORM::factory('qwt_score')->scoreIn($flogin,0,$dlsku->old_price-$dlsku->price,$dlsku->id,$rebuy->id,$qrcode_edit->id,'');
                    }
                }

            }
            $item = ORM::factory('qwt_item')->where('id','=',$sku->iid)->find();
            $item->count = $item->count+1;
            $item->save();
            $content = '支付成功,请前往【插件中心】查看';
        }else{
            $content = '支付失败，请重试';
        }
        echo $content;
    }
    public function action_createorder($orderid,$sku_id){//预支付 先创建订单
        $this->template = 'tpl/blank';
        self::before();
        $sku = ORM::factory('qwt_sku')->where('id','=',$sku_id)->find();
        $buser = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',$sku->iid)->find();
        if($buser->id){//续费

        }else{//初次购买
            $buser->iid=$sku->iid;
            $buser->buy_time = time();
            $buser->expiretime = time();
            $buser->bid = $this->bid;
        }
        $buser->save();
        $rebuy = ORM::factory('qwt_rebuy')->where('status','=',0)->where('bid','=',$this->bid)->where('sku_id','=',$sku->id)->find();
        $rebuy->tid = $orderid;
        $rebuy->bid = $this->bid;
        $rebuy->buy_id = $buser->id;
        $rebuy->rebuy_price = $sku->price;
        $rebuy->rebuy_time = time();
        $rebuy->sku_id = $sku->id;
        $rebuy->save();
        echo '1';
        exit;
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
}
