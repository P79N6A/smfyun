<?php defined('SYSPATH') or die('No direct script access.');

class Controller_dld extends Controller_Base {
    public $template = 'weixin/dld/tpl/fftpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://dd.smfyun.com/dld/';
    var $we;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();

        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'cron') return;
        if (Request::instance()->action == 'myteam') return;
        if (Request::instance()->action == 'userinfo') return;
        if (Request::instance()->action == 'err_trade') return;
        if (Request::instance()->action == 'order_top') return;
        //if (Request::instance()->action == 'index_oauth') return;
        $_SESSION =& Session::instance()->as_array();
        $biz = ORM::factory('dld_login')->where('id','=',$_SESSION['dld']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime)+86400 < time()) die('您的账号已过期');
        $this->config = $_SESSION['dld']['config'];
        $this->openid = $_SESSION['dld']['openid'];
        $this->bid = $_SESSION['dld']['bid']?$_SESSION['dld']['bid']:$_SESSION['dlda']['bid'];
        $this->uid = $_SESSION['dld']['uid'];
        $this->access_token = $_SESSION['dld']['access_token'];


        if ($_GET['debug']) print_r($_SESSION['dld']);


        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['dlda']['bid']) die('请通过微信访问。');
    }

    public function after() {
        $user = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

         $firstchild=DB::query(Database::SELECT,"SELECT openid FROM dld_qrcodes WHERE fopenid='$this->openid'")->execute()->as_array();

        $customer=ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->order_by('paid', 'DESC');
        $user['follows'] =$customer->count_all();


        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM dld_qrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();

        $user['follows_month']=ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('jointime','>=',$month)->count_all();
        $user['trades'] = ORM::factory('dld_score')->where('qid', '=', $user['id'])->where('type', 'IN', array(2,3))->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    public function action_err_trade($bid){
        $trades = ORM::factory('dld_trade')->where('bid','=',$bid)->where('fopenid','=',null)->find_all();
        foreach ($trades as $k => $v) {
            echo $k.'<br>';
            $user = ORM::factory('dld_qrcode')->where('id','=',$v->qid)->where('bid','=',$bid)->find();
            if($user->fopenid==''){
                echo $user->openid.'<br>';
            }
        }
        exit;
    }
    //入口
    public function action_index($bid) {
        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['dlda']['bid']) return $this->action_msg('请通过微信打开！', 'warn');

        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('dld_login')->where('id', '=', $bid)->find()->access_token;
        if (!$_GET['openid']) $_SESSION['dld'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m'))) {
                $_SESSION['dld'] = NULL;
                die('该页面已过期！');
            }

            $userobj = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

            $_SESSION['dld']['config'] = $config;
            $_SESSION['dld']['openid'] = $openid;
            $_SESSION['dld']['bid'] = $bid;
            $_SESSION['dld']['uid'] = $userobj->id;
            $_SESSION['dld']['access_token'] =$this->access_token;
            Request::instance()->redirect('/dld/'.$_GET['url']);
        }
    }
    public function action_cron($bid){
        set_time_limit(0);
        $this->config=$config=ORM::factory('dld_cfg')->getCfg($bid,1);
        $login=ORM::factory('dld_login')->where('id','=',$bid)->find();
        if(!$login->id) die('没有此商户');
        $day=date('d',time());
        // if($day!=$config['date']) die ('没有到结算日期');
        $qrs=ORM::factory('dld_qrcode')->where('bid','=',$bid)->find_all();
        foreach ($qrs as $v) {
            $nawtime=time();
            $monthtype='%Y-%m';
            $date=date('Y-m-d',time());
            $timestamp=strtotime($date);
            $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
            $month=date('Y-m',strtotime("$firstday +1 month -1 day"));
            $score=ORM::factory('dld_score')->where('bid','=',$bid)->where('qid','=',$v->id)->where('bz','=',$month)->find();
            if(!$score->id){
                $groups=ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$v->id)->find_all();
                $monthjs_pmoney=0;
                foreach ($groups as $group) {
                    if($group->bottom){
                        $bottom1='('.$group->id.','.$group->bottom.')';
                    }else{
                        $bottom1='('.$group->id.')';
                    }
                    $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from dld_trades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                    $skujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                    if(!$skujs->id){
                        $fskujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                        if(!$fskujs->id){
                           $scalejs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scalejs=0;
                        }
                    }else{
                        $scalejs=$skujs->scale;
                    }
                    $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;
                    $child_groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                    $childjs_moneys=0;
                    $monthjs_pmoney=0;
                    foreach ($child_groups as $child_group) {
                        if($child_group->bottom){
                            $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                        }else{
                            $bottom2='('.$child_group->id.')';
                        }

                          //echo $bottom2."<br>";
                        $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from dld_trades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                        $skujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>=',$monthjs_ltmoney)->find();
                        if(!$skujs->id){
                            $fskujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                            if(!$fskujs->id){
                                $scalejs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                            }else{
                                $scalejs=0;
                            }
                        }else{
                            $scalejs=$skujs->scale;
                        }
                        $childjs_money= $monthjs_ltmoney*$scalejs/100;
                        $childjs_moneys+=$childjs_money;
                    }
                    $monthjs_pmoney+=$monthjs_tmoney-$childjs_moneys;
                }
                $result = $this->sendMoney1($v,$monthjs_pmoney*100,$month);
                if($result['result_code']=='FAIL'){
                    echo '付款失败：'.$result['err_code'];
                    Kohana::$log->add("{$v->nickname}:{$month}:{$monthjs_pmoney}",print_r($result,true));
                }else{
                    ORM::factory('dld_score')->scoreOut($v,4, $monthjs_pmoney,'','',$month);
                }

            }
        }
        exit;
    }
    private function sendMoney1($userobj, $money,$time) {
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
        $data["desc"] = $userobj->nickname.'的'.$time.'月收益';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->we->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_dld:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_dld:hongbaoresult', print_r($result, true));
        return $result;
    }
    //Oauth 入口
    public function action_index_oauth($bid, $url='form') {
       $config = ORM::factory('dld_cfg')->getCfg($bid,1);
       $urls = array('form','memberpage','order_detail','account_set','code');
       if(!in_array($url,$urls)) die('url不合法'.$url);
        // require_once Kohana::find_file('vendor/kdt', 'lib/KdtRedirectApiClient');

        // if(!isset($_GET['open_id'])){
        //     $appId = ORM::factory('dld_cfg')->where('bid', '=', $bid)->where('key', '=', 'youzan_appid')->find()->value;
        //     $appSecret = ORM::factory('dld_cfg')->where('bid', '=', $bid)->where('key', '=', 'youzan_appsecret')->find()->value;
        //     $client = new KdtRedirectApiClient($appId, $appSecret);
        //     $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        //     $client->redirect($callback_url, 'snsapi_userinfo');
        // }else{


        //     $userobj = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $_GET['open_id'])->find();
        //     $userobj->openid=$_GET['open_id'];
        //     $userobj->nickname = $_GET['nickname'];
        //     $userobj->headimgurl = $_GET['avatar'];
        //     $userobj->subscribe = $_GET['subscribe'];
        //     $userobj->sex = $_GET['sex'];
        //     $userobj->bid = $bid;
        //     $userobj->ip = Request::$client_ip;
        //     $userobj->save();

        //     $_SESSION['dld']['config'] = $config;
        //     $_SESSION['dld']['openid'] = $_GET['open_id'];
        //     $_SESSION['dld']['bid'] = $bid;
        //     $_SESSION['dld']['uid'] = $userobj->id;
        //     Request::instance()->redirect('/dld/'.$url.'/'.$userobj->openid);
        // }
                //没有 Oauth 授权过才需要
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

            if (!$openid) $_SESSION['dld'] = NULL;

            if ($openid) {
                $userobj = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                if(!$userobj->id){
                    $userobj->values($userinfo);
                    $userobj->bid = $bid;
                    $userobj->ip = Request::$client_ip;
                    $userobj->save();
                }
                $_SESSION['dld']['config'] = $config;
                $_SESSION['dld']['openid'] = $openid;
                $_SESSION['dld']['bid'] = $bid;
                $_SESSION['dld']['uid'] = $userobj->id;
            }
        }
        Request::instance()->redirect('/dld/'.$url.'/'.$userobj->openid);
    }
    //个人中心
    public function action_memberpage($openid){
        $view = "weixin/dld/memberpage";
        $this->template = 'tpl/blank';
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        $config = ORM::factory('dld_cfg')->getCfg($this->bid,1);
        // $config['buy_money'] = 35;
        $userobj = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv==1){//正常
            $v = $userobj;
            $group1=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->order_by('lastupdate','DESC')->find();
          if($group1->bottom){
            $bottom='('.$group1->bottom.')';
            //echo $bottom.'<br>';
            $group_ay=DB::query(Database::SELECT,"SELECT count(id) as group_num from dld_groups where bid=$v->bid and id in $bottom ")->execute()->as_array();
            $group_num=$group_ay[0]['group_num'];
          }else{
            $group_num=0;
          }
          //echo $group_num.'<br>';所辖团队成员
          $qr_num=ORM::factory('dld_qrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','!=',1)->where('fopenid','!=','')->count_all();
           $groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
           $month=date('Y-m',time());
               //echo $month.'<br>';
            $daytype='%Y-%m-%d';
            $monthtype='%Y-%m';
            $day=date('Y-m-d',time());
            $month_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as month_pnum from dld_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
            $result['month_pnum']=$month_pnum[0]['month_pnum'];
            //echo $month_pnum.'<br>';当月个人销量
            $day_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as day_pnum from dld_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
            $result['day_pnum']=$day_pnum=$day_pnum[0]['day_pnum'];
             //echo $day_pnum.'<br>';当天个人销量
            $all_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as all_pnum from dld_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' ")->execute()->as_array();
            $result['all_pnum'] = $all_pnum=$all_pnum[0]['all_pnum'];
            $day_tnum=0;
            $month_tnum=0;
            $all_tnum=0;
            $month_tmoney=0;
            $month_pmoney=0;
            foreach ($groups as $group) {
              if($group->bottom){
                  $bottom1='('.$group->id.','.$group->bottom.')';
              }else{
                  $bottom1='('.$group->id.')';
              }
              //echo $bottom1.'<br>';
              $day_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as day_tnum from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
              $day_tnum+=$day_tnum1[0]['day_tnum'];
              $result['day_tnum'] = $day_tnum;
              //echo  $day_tnum.'<br>';当天团队销量
              $month_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tnum from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
              $month_tnum+=$month_tnum1[0]['month_tnum'];
              $result['month_tnum'] = $month_tnum;
              //echo  $month_tnum.'<br>';当月团队销量
              $all_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as all_tnum from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 ")->execute()->as_array();
              $all_tnum+=$all_tnum1[0]['all_tnum'];
              $result['all_tnum'] = $all_tnum;
              //累计团队销量
              $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
              $month_tmoney1=$month_tmoney1[0]['month_tmoney'];
              //echo  $month_tmoney.'<br>';
              $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                if(!$sku->id){
                    $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                    if(!$fsku->id){
                       $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                    }else{
                        $scale=0;
                    }
                }else{
                    $scale=$sku->scale;
                }
                $month_tmoney+=$month_tmoney1*$scale/100;
                $result['month_tmoney'] = $month_tmoney;
                // echo  $month_tmoney.'<br>';
                // echo $group->id."<br>";
              $child_groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
              $child_moneys=0;
              foreach ($child_groups as $child_group) {
                    if($child_group->bottom){
                         $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                      }else{
                            $bottom2='('.$child_group->id.')';
                      }

                    //echo $bottom2."<br>";
                    $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                    //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                    $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>=',$month_ltmoney)->find();
                    if(!$sku->id){
                        $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>',$month_ltmoney)->find();
                        if(!$fsku->id){
                           $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scale=0;
                        }
                    }else{
                        $scale=$sku->scale;
                    }
                    $child_money= $month_ltmoney*$scale/100;
                    $child_moneys+=$child_money;
              }
              //echo  $child_moneys.'<br>';
              $month_pmoney+=$month_tmoney-$child_moneys;
              $result['month_pmoney'] = $month_pmoney;
            }

            //echo  $month_pmoney.'<br>';当月个人团队奖励
            $day_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as day_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$day' ")->execute()->as_array();
            $result['day_pxmoney'] = $day_pxmoney=$day_pxmoney[0]['day_pxmoney'];
            //当天销售利润
            $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
            $result['month_pxmoney']=$month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
            //当月销售利润
            //echo  $month_pxmoney.'<br>';
            $all_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as all_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
            $result['all_pxmoney']=$all_pxmoney=$all_pxmoney[0]['all_pxmoney'];
            //累计销售利润
        }
        if($userobj->lv==0){
            $result['content'] = '抱歉，您未获得代理资格，无法进入。';
        }
        if($userobj->lv==2){
            $result['content'] = str_replace('%s', $config['buy_money'], $config['buytip']);
            // $result['content'] = '恭喜您成功申请代理资格，请前往微商城完成单笔金额'.$config['buy_money'].'元以上的消费，即可激活代理资格，享有相应权益。';
        }
        if($userobj->lv==3){
            $result['content'] = '抱歉，您未获得代理资格，无法进入。';
        }
        if($_POST['tel']){
            $tel = ORM::factory('dld_qrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->find();
            if($tel->id){
                $result['error'] = '对不起，您的手机号已经注册了';
            }else{
                $tel = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
                $tel->tel = $_POST['tel'];
                $tel->save();
                Request::instance()->redirect($config['buy_url']);
            }
        }
        $result['lv'] = $userobj->lv;

        $this->template->content = View::factory($view)->bind('result', $result)->bind('user',$userobj)->bind('config',$config);
    }
    public function action_myteam($openid,$bid){
        $view = "weixin/dld/myteam";
        $this->template = 'tpl/blank';
        self::before();
        $user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $result['follows'] = ORM::factory('dld_qrcode')->where('lv','=',1)->where('bid','=',$bid)->where('fopenid','=',$openid)->order_by('id','DESC')->find_all();
        $result['num'] = ORM::factory('dld_qrcode')->where('lv','=',1)->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();

        $this->template->content = View::factory($view)
            ->bind('result', $result);
    }
    //结算记录
    public function action_account_record($uid){
        if(!$this->bid) die('页面已过期，请重试');
        $view = "weixin/dld/account_record";
        $this->template = 'tpl/blank';
        self::before();
        $this->uid = $uid;
        $user = ORM::factory('dld_qrcode')->where('bid','=',$this->bid)->where('id','=',$this->uid)->find();
        $records = ORM::factory('dld_score')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('score','<',0)->find_all();
        $this->template->content = View::factory($view)
            ->bind('records', $records)
            ->bind('config', $config);
    }
    public function action_order_top($bid){
        $view = "weixin/dld/now_top";
        $this->template = 'tpl/blank';
        self::before();
        $userobjs = ORM::factory('dld_qrcode')->where('lv','=',1)->where('bid','=',$bid)->find_all();
        $user = array();
        foreach ($userobjs as $k => $v) {
            if($_POST['start']&&$_POST['end']){
                $now_payment = ORM::factory('dld_trade')->where('bid','=',$bid)->where('fopenid','=',$v->openid)->where('int_time','<',strtotime($_POST['end']))->where('int_time','>',strtotime($_POST['start']))->select(array('SUM("payment")', 'all_payment'))->find()->all_payment;
            }else{
                $now_payment = ORM::factory('dld_trade')->where('bid','=',$bid)->where('fopenid','=',$v->openid)->select(array('SUM("payment")', 'all_payment'))->find()->all_payment;
            }
            $user[$k]['id'] = $v->id;
            $user[$k]['nickname'] = $v->nickname;
            $user[$k]['headimgurl'] = $v->headimgurl;
            $user[$k]['payment'] = $now_payment;
        }

        if($user[0]){
            foreach ($user as $k => $v){
                $payment[$k]  = $v['payment'];
                $id[$k]  = $v['id'];
            }
            // echo '<pre>';
            // var_dump($user);
            // var_dump($payment);
            // var_dump($id);
            //先按照销量后按照id
            array_multisort($payment,SORT_DESC,$id, SORT_ASC,$user);
        }
        $this->template->content = View::factory($view)
            ->bind('user', $user);
    }
    //订单详情
    public function action_order_detail($openid){
        if(!$this->bid) die('页面已过期，请重试');
        $view = "weixin/dld/order_detail";
        $this->template = 'tpl/blank';
        self::before();
        // echo $this->uid;
        // exit;
        $this->uid = $uid;
        $user = ORM::factory('dld_qrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
        $orders = ORM::factory('dld_trade')->where('bid','=',$this->bid)->where('fopenid','=',$openid)->find_all();
        // echo $orders;
        // var_dump($orders);
        $this->template->content = View::factory($view)->bind('user',$user)->bind('orders',$orders);
    }
    //结算信息设置
    public function action_account_set($uid){
        if(!$this->bid) die('页面已过期，请重试');
        $view = "weixin/dld/account_set";
        $this->template = 'tpl/blank';
        self::before();
        // echo $this->uid;
        // exit;
        $this->uid = $uid;
        $user = ORM::factory('dld_qrcode')->where('bid','=',$this->bid)->where('id','=',$this->uid)->find();
        if ($_POST['form']) {
            $user->name=$_POST['form']['name'];
            $user->tel=$_POST['form']['tel'];
            $user->alipay_name=$_POST['form']['zfb'];
            $user->save();
            Request::instance()->redirect('/dld/memberpage/'.$user->openid);
        }
        $this->template->content = View::factory($view)->bind('user',$user);
    }
    //预约链接
    public function action_form($openid) {
        $view = "weixin/dld/form";
        $this->template = 'tpl/blank';
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        $config = ORM::factory('dld_cfg')->getCfg($this->bid,1);
        // $config['buy_money'] = 35;

        $userobj = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv == 0){
            $userobj->lv = 2;//申请成功，但是金额没有达到
        }
        if($userobj->lv==1){
            $result['content'] = '您已经具有代理商资格了，不需要在申请了！';
        }
        if($userobj->lv==2){
            $result['content'] = str_replace('%s', $config['buy_money'], $config['buytip']);
            // $result['content'] = '恭喜您成功申请代理资格，请前往微商城完成单笔金额'.$config['buy_money'].'元以上的消费，即可激活代理资格，享有相应权益。';
        }
        if($userobj->lv==3){
            $result['content'] = '抱歉，您未获得代理资格，无法进入。';
        }
        if($_POST['tel']){
            $tel = ORM::factory('dld_qrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->find();
            if($tel->id){
                $result['error'] = '对不起，您的手机号已经注册了';
            }else{
                $tel = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
                $tel->tel = $_POST['tel'];
                $tel->save();
                Request::instance()->redirect($config['buy_url']);
            }
        }
        $userobj->save();
        $result['lv'] = $userobj->lv;

        $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $config);
    }
    //官方邀请码
    public function action_code($openid){
        $view = "weixin/dld/code";
        $this->template = 'tpl/blank';
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        $config = ORM::factory('dld_cfg')->getCfg($this->bid,1);
        // $config['buy_money'] = 35;

        $userobj = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv==1){
            $result['content'] = '您已经具有代理商资格了，不需要在申请了！';
        }

        if($_POST['code']){
            $tel = ORM::factory('dld_qrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->find();
            if($_POST['code'] == $config['code']&&!$tel->id){//code正确并且tel正常
                $userobj->lv = 1;
                $userobj->code = $_POST['code'];
                $userobj->fopenid = '';//上级置为空
                $userobj->save();
                $group = ORM::factory('dld_group');
                $group->bid = $this->bid;
                $group->qid = $userobj->id;
                $group->fgid = 0;
                $group->fqid = 0;
                $group->save();
                $result['content'] = '恭喜您成为了第一层代理商！';

                $tel = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
                $tel->tel = $_POST['tel'];
                $tel->save();
            }else{
                if($tel->id){
                    $result['content'] = '手机号已经注册了';
                }
                if($_POST['code'] != $config['code']){
                    $result['content'] = '邀请码填写的不正确';
                }
                if($tel->id&&$_POST['code'] != $config['code']){
                    $result['content'] = '手机号已经注册并且邀请码填写不正确';
                }
            }
        }
        if($userobj->lv == 0||$userobj->lv == 2){

        }

        // if($userobj->lv==2){
            // $result['content'] = '恭喜您成功申请代理资格，请前往微商城完成单笔金额'.$config['buy_money'].'元以上的消费，即可激活代理资格，享有相应权益。';
        // }
        if($userobj->lv==3){
            $result['content'] = '抱歉，您未获得代理资格，无法进入。';
        }
        if($_POST['tel2']){
            $tel = ORM::factory('dld_qrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel2'])->find();
            if($tel->id){
                $result['error'] = '对不起，您的手机号已经注册了';
            }else{
                $tel = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
                $tel->tel = $_POST['tel2'];
                $tel->save();
                Request::instance()->redirect($config['buy_url']);
            }
        }
        $result['lv'] = $userobj->lv;

        $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $config);
    }
    public function action_commends($mopenid,$bid){//奖品list分享页面
        $fopenid = $mopenid;
        $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
        if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
        if(!$fuser->id) die('不合法2');
        $config=ORM::factory('dld_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/dld/commends/'.$mopenid.'/'.$bid;
        $this->we = $we = new Wechat($config);

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_url);
        $userobj = ORM::factory('dld_qrcode', $this->uid);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/dld/tpl/tpl2';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            $userinfo = $we->getOauthUserinfo($token['access_token'],$openid);
            Kohana::$log->add("1commends_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            // if($openid=='okxUMwaR4qmy8axy2Acx5dok15e0'){
            //     echo '<pre>';
            //     var_dump($userinfo);
            //     exit;
            // }
            if(!$openid||!$userinfo['nickname']){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
            Kohana::$log->add("2commends_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            $user=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            if(!$user->id){
                $user->bid=$bid;
                $user->openid=$openid;
                $user->lv = 0;
                $user->save();
            }

            $_SESSION['dld']['config'] = $config;
            $_SESSION['dld']['openid'] = $openid;
            $_SESSION['dld']['bid'] = $bid;
            $_SESSION['dld']['uid'] = $user->id;

            $this->config = $_SESSION['dld']['config'];
            $this->openid = $_SESSION['dld']['openid'];
            $this->bid = $_SESSION['dld']['bid'];
            $this->uid = $_SESSION['dld']['uid'];

            $user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid||$user->lv==1){
                // echo '有上线';
                $status = 1;
                $result['title'] = $fuser->nickname.'的推荐商品';
                $user->sex = $userinfo['sex'];
                $user->headimgurl = $userinfo['headimgurl'];
                $user->nickname = $userinfo['nickname'];
                $user->save();
            }else{
                $result['title'] = $fuser->nickname.'的推荐商品';
                if($fopenid != $openid&&$fuser->id < $user->id){//上线id大于本人id
                    $user->nickname = $userinfo['nickname'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        // $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $userinfo['sex'];
                    $user->headimgurl = $userinfo['headimgurl'];
                    // $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->bid = $bid;
                    $user->openid = $openid;
                    $user->fopenid = $fopenid;
                    $user->save();
                    $status = 1;
                    //关系绑定之后 发送消息通知
                    $loop_qrcode = $user;
                    while ($loop_qrcode->fopenid) {
                         $loop_qrcode = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$loop_qrcode->fopenid)->find();
                        //直属上级
                        if($loop_qrcode->openid == $user->fopenid){
                            $text = str_replace('%s', $user->nickname, $config['text_dirctcus']);
                        }else{//非直属上级
                            $text = str_replace('%s', $user->nickname, $config['text_customer']);
                            $text = str_replace('%t', $fuser->nickname, $text);
                        }
                        $msg['touser'] = $loop_qrcode->openid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        if($loop_qrcode->lv==1) $we->sendCustomMessage($msg);
                    }
                }
            }
            $view = "weixin/dld/commendsother";//别人直接是url进，不需要加密
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                $result['title'] = $user->nickname.'的推荐商品';
                $view = "weixin/dld/commends";//自己进 跳入shareopenid 需要加密url
            }
            $this->template->title = $fuser->nickname.'推荐商品';
            $result['commends'] = ORM::factory('dld_setgood')->where('bid','=',$bid)->where('status','=',1)->find_all();
            $result['openid'] = $user->openid;
            $shop = ORM::factory('dld_login')->where('id','=',$bid)->find();
            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('shop', $shop)->bind('user', $user);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }
    }
    public function action_userinfo($lv){
        $bid = 4;
        // $openid = 'okxUMwdbayaJNX_8axH7tDl29kjQ';
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        $we = new Wechat($config);
        $users = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('lv','=',$lv)->where('nickname','=','')->find_all();
        foreach ($users as $k => $v) {
            $result = $we->getUserInfo($v->openid);
            // if($result)
            if($result['nickname']){
                echo $v->openid.'-------------保存成功<br>';
                $v->sex = $result['sex'];
                $v->headimgurl = $result['headimgurl'];
                $v->nickname = $result['nickname'];
                $v->save();
            }else{
                echo $v->openid.'-------------获取失败<br>';
            }
            echo '<pre>';
            var_dump($result);
        }
        exit;
    }
    public function action_shareopenid($mopenid,$gid,$bid){//商品分享页面 自己可以打开 别人也可以打开
        $fopenid = base64_decode($mopenid);
        $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
        if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
        if(!$fuser->id) die('不合法1');
        $config=ORM::factory('dld_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/dld/shareopenid/'.$mopenid.'/'.$gid.'/'.$bid;
        $this->we = $we = new Wechat($config);

        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_urlsdk);

        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/dld/tpl/tpl';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            $userinfo = $we->getOauthUserinfo($token['access_token'],$openid);
            Kohana::$log->add("1shareopenid_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            if(!$openid||!$userinfo['nickname']){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
            Kohana::$log->add("2shareopenid_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            // if($openid=='okxUMwaR4qmy8axy2Acx5dok15e0'){
            //     echo '<pre>';
            //     var_dump($userinfo);
            //     exit;
            // }
            $user=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            if(!$user->id){
                $user->bid=$bid;
                $user->openid=$openid;
                $user->lv = 0;
                $user->save();
            }


            $_SESSION['dld']['config'] = $config;
            $_SESSION['dld']['openid'] = $openid;
            $_SESSION['dld']['bid'] = $bid;
            $_SESSION['dld']['uid'] = $user->id;

            $this->config = $_SESSION['dld']['config'];
            $this->openid = $_SESSION['dld']['openid'];
            $this->bid = $_SESSION['dld']['bid'];
            $this->uid = $_SESSION['dld']['uid'];
            $user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid||$user->lv==1){//成为代理的情况的话也进入
                // echo '有上线';
                $status = 1;

                $user->sex = $userinfo['sex'];
                $user->headimgurl = $userinfo['headimgurl'];
                $user->nickname = $userinfo['nickname'];
                $user->save();
            }else{
                $status = 1;
                $user->nickname = $userinfo['nickname'];
                if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                    // $user->subscribe = $results['response']['user']['is_follow'];
                }
                $user->sex = $userinfo['sex'];
                $user->headimgurl = $userinfo['headimgurl'];
                // $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                if($fopenid != $openid&&$fuser->id < $user->id){
                    $user->bid = $bid;
                    $user->openid = $openid;
                    $user->fopenid = $fopenid;
                    $user->save();
                    //关系绑定之后 发送消息通知
                    $loop_qrcode = $user;
                    while ($loop_qrcode->fopenid) {
                         $loop_qrcode = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$loop_qrcode->fopenid)->find();
                        //直属上级
                        if($loop_qrcode->openid == $user->fopenid){
                            $text = str_replace('%s', $user->nickname, $config['text_dirctcus']);
                        }else{//非直属上级
                            $text = str_replace('%s', $user->nickname, $config['text_customer']);
                            $text = str_replace('%t', $fuser->nickname, $text);
                        }
                        $msg['touser'] = $loop_qrcode->openid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        if($loop_qrcode->lv==1) $we->sendCustomMessage($msg);
                    }
                }
            }
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                // $result['content'] = '快将该商品推荐给你的好友吧';
            }
            $commend = ORM::factory('dld_setgood')->where('bid','=',$bid)->where('id','=',$gid)->find();
            if($status==2){//自己进入
                if($commend->type==1){//多规格
                    if($user->group_id>0){//有分组
                        $sku_commends = ORM::factory('dld_smoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('item_id','=',$commend->num_iid)->find_all();
                    }else{
                        $sku_commends = ORM::factory('dld_goodsku')->where('bid','=',$bid)->where('item_id','=',$commend->num_iid)->find_all();
                    }
                }else{
                    if($user->group_id>0){//有分组
                        $smoney = ORM::factory('dld_smoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('sku_id','=',0)->where('item_id','=',$commend->num_iid)->find();
                        $commend->money = $smoney->money?$smoney->money:'0.00';
                    }else{

                    }
                }
            }
            $view = "weixin/dld/share";
            $this->template->title = $fuser->nickname.':'.$commend->title;
            $this->template->content = View::factory($view)->bind('status', $status)->bind('commend', $commend)->bind('result', $result)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('sku_commends', $sku_commends)->bind('user', $user)->bind('bid', $bid);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }
    }
    public function action_shop($mopenid,$bid){//有赞店铺分享页面 自己可以打开 别人也可以打开
        $fopenid = $mopenid;
        $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
        if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
        $config=ORM::factory('dld_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/dld/shop/'.$mopenid.'/'.$bid;
        $this->we = $we = new Wechat($config);

        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_urlsdk);

        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/dld/tpl/tpl';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            $userinfo = $we->getOauthUserinfo($token['access_token'],$openid);
            Kohana::$log->add("1shop_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            if(!$openid||!$userinfo['nickname']){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
            Kohana::$log->add("2shop_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            // if($openid=='okxUMwaR4qmy8axy2Acx5dok15e0'){
            //     echo '<pre>';
            //     var_dump($userinfo);
            //     exit;
            // }
            $user=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            if(!$user->id){
                $user->bid=$bid;
                $user->openid=$openid;
                $user->lv = 0;
                $user->save();
            }


            $_SESSION['dld']['config'] = $config;
            $_SESSION['dld']['openid'] = $openid;
            $_SESSION['dld']['bid'] = $bid;
            $_SESSION['dld']['uid'] = $user->id;

            $this->config = $_SESSION['dld']['config'];
            $this->openid = $_SESSION['dld']['openid'];
            $this->bid = $_SESSION['dld']['bid'];
            $this->uid = $_SESSION['dld']['uid'];
            $user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid||$user->lv==1){
                // echo '有上线';
                $status = 1;

                $user->sex = $userinfo['sex'];
                $user->headimgurl = $userinfo['headimgurl'];
                $user->nickname = $userinfo['nickname'];
                $user->save();
                if($user->openid!=$fopenid){
                    header("Location:".$config['shopurl']);
                    exit;
                }
            }else{
                $status = 1;
                if($fopenid != $openid&&$fuser->id < $user->id){
                    $user->nickname = $userinfo['nickname'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        // $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $userinfo['sex'];
                    $user->headimgurl = $userinfo['headimgurl'];
                    // $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->bid = $bid;
                    $user->openid = $openid;
                    $user->fopenid = $fopenid;
                    $user->save();
                    header("Location:".$config['shopurl']);
                    exit;
                }
            }
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                // $result['content'] = '快将该商品推荐给你的好友吧';
            }
            $shop = ORM::factory('dld_login')->where('id','=',$bid)->find();
            $view = "weixin/dld/shop";
            $this->template->title = $fuser->nickname.'推荐商品';
            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('shop', $shop);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }
    }
    public function action_appointment($mopenid,$bid){//推荐代理给别人
        $fopenid = $mopenid;
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        if($_POST['tel']&&$_POST['openid']){
            $openid = $_POST['openid'];
            $tel = ORM::factory('dld_qrcode')->where('lv', '=', 1)->where('bid', '=', $bid)->where('tel', '=', $_POST['tel'])->find();
            if($tel->id){
                $result['content'] = '您成功申请代理商资格，请单笔订单金额满足'.$config['buy_money'].'元即可激活资格！';
                $user = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                $result['error'] = '对不起，您的手机号已经注册了';
                $result['lv'] = 2;
            }else{
                $user = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                $user->tel = $_POST['tel'];
                $user->save();
                Request::instance()->redirect($config['buy_url']);
            }
        }else{
            $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/dld/appointment/'.$mopenid.'/'.$bid;
            $this->we = $we = new Wechat($config);

            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
            if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
            $signPackage = $we->getJsSign($callback_url);
            $userobj = ORM::factory('dld_qrcode', $this->uid);
            if($_GET['code']){//静默授权当事人
                $this->template = 'tpl/blank';
                self::before();
                $token = $we->getOauthAccessToken();
                $openid=$token['openid'];

                $userinfo = $we->getOauthUserinfo($token['access_token'],$openid);

                if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                    $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                    header("Location:$auth_url");exit;
                }
                //取出当前用户
                $user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
                if($user->id){//如果当前用户存在
                    if($user->openid == $fopenid){//如果是自己点击预约链接
                        $result['content'] = '快将本页面分享给好友邀请他们成为代理商吧！';
                        $result['lv'] = 0;//只有这种情况下才能分享
                    }else{//别人点击链接
                        //客户转成代理商
                        if($user->lv==0){//&&$user->fopenid
                            $user->lv = 2;
                            $user->fopenid = $fuser->openid;
                            $user->save();
                            // $text = str_replace('%s',$user->nickname,$config['text_group']);

                            // $msg['touser'] = $fuser->openid;
                            // $msg['msgtype'] = 'text';
                            // $msg['text']['content'] = $text;
                            // $we->sendCustomMessage($msg);
                        }
                        if($user->lv == 1){//自己本来就是代理商
                            $result['content'] = '您已经是代理商了！';
                            $result['lv'] = 1;
                        }else if($user->lv == 2 ){//还差一步
                            $result['content'] = '您成功申请代理商资格，请单笔订单金额满足'.$config['buy_money'].'元即可激活资格！';
                            $result['lv'] = 2;
                        }else if($user->lv == 3 ){//被取消
                            $result['content'] = '抱歉，您未获得代理资格，无法进入。';
                            $result['lv'] = 3;
                        }else if($user->lv == 4 ){//退款
                            $result['lv'] = 4;
                        }
                    }
                }else{//存入新用户
                    // require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                    // $access_token=ORM::factory('dld_login')->where('id', '=', $bid)->find()->access_token;
                    // $client = new YZTokenClient($access_token);

                    // $method = 'youzan.users.weixin.follower.get';
                    // $params = [
                    //     'weixin_openid'=>$openid,
                    //  ];
                    // $results = $client->post($method, $this->methodVersion, $params, $files);
                    // echo "<pre>";
                    // var_dump($results);
                    // exit;
                    // if($results['response']['user']['sex']=='m'){
                    //     $sex=1;//男
                    // }else if($results['response']['user']['sex']=='f'){
                    //     $sex=2;//女
                    // }else{
                    //     $sex=0;//人妖
                    // }
                    $user->nickname = $userinfo['nickname'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        // $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $userinfo['sex'];
                    $user->headimgurl = $userinfo['headimgurl'];
                    // $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->openid = $userinfo['openid'];
                    $user->fopenid = $fopenid;
                    $user->bid = $bid;
                    $user->lv = 2;
                    $user->save();
                    $result['content'] = str_replace('%s', $config['buy_money'], $config['buytip']);
                    // $result['content'] = '恭喜您成功申请代理资格，请前往微商城完成单笔金额'.$config['buy_money'].'元以上的消费，即可激活代理资格，享有相应权益。';
                    $result['lv'] = 2;
                    //关系绑定之后 发送消息通知
                    // $text = $fuser->nickname.'，恭喜你增加了一个新的团队成员!';
                    // if($config['coupontpl']){
                    //     $this->sendtplcoupon($fopenid,$config,$text,$we);
                    // }else{
                    //     $msg['touser'] = $fopenid;
                    //     $msg['msgtype'] = 'text';
                    //     $msg['text']['content'] = $text;
                    //     $we->sendCustomMessage($msg);
                    // }
                }

            }else{//得到code为止
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
        }
        $result['title'] = '来自'.$user->nickname.'的邀请';
        $_SESSION['dld']['config'] = $config;
        $_SESSION['dld']['openid'] = $openid;
        $_SESSION['dld']['bid'] = $bid;
        $_SESSION['dld']['uid'] = $user->id;

        $this->config = $_SESSION['dld']['config'];
        $this->openid = $_SESSION['dld']['openid'];
        $this->bid = $_SESSION['dld']['bid'];
        $this->uid = $_SESSION['dld']['uid'];

        $view = "weixin/dld/appointment";//别人直接是url进，不需要加密

        $this->template->title = $fuser->nickname.'的邀请';

        $result['openid'] = $user->openid;
        $shop = ORM::factory('dld_login')->where('id','=',$bid)->find();
        $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('shop', $shop)->bind('fuser',$fuser);
    }
    //默认页面
    public function action_home() {
        $view = "weixin/dld/home";

        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('dld_qrcode', $this->uid);

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

        $view = "weixin/dld/money";
        $userobj = ORM::factory('dld_qrcode', $this->uid);

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

        if ($result['money_now']>=number_format($result['money_out']/100, 2,'.','')) {
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
            $result['money_out_msg'] = '满'. number_format($result['money_out']/100, 2,'.','') .$title5.'即可转出。';
        }

        //转出
        //只能提取整数
        $MONEY = floor($result['money_now']);
        $md5 = md5($this->openid.$this->config['appsecret'].$_GET['time'].$_GET['rand']);
        // echo "cks:$cksum<br />md5:$md5";
        if ( ($cksum == $md5) && (time() - $_GET['time'] < 600) ) $cksum_flag = true;

        if ($out == 1 && $cksum_flag == true && ($MONEY >= $this->config['money_out']/100) ) {
            if (!$this->config['partnerid'] || !$this->config['partnerkey']) die('ERRROR: Partnerid 和 Partnerkey 未配置，不能自动转出，请联系管理员！');
            $mem = Cache::instance('memcache');
            $isget = $mem->get($this->bid.$this->openid.$MONEY);
            if($isget == 1) die('您的转出申请已经提交，请耐心等候零钱到账！');
            $mem->set($this->bid.$this->openid.$MONEY,1,60);

            $this->we = $we = new Wechat($this->config);
            $result_m = $this->sendMoney($userobj, $MONEY*100);

            if ($result_m['result_code'] == 'SUCCESS') {
                $userobj->scores->scoreOut($userobj, 4, $MONEY);
                $mem->set($this->bid.$this->openid.$MONEY,0,60);
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
                Kohana::$log->add("weixin_dld:$bid:money", print_r($result, true));
                $result['alert'] = '转出失败：'.$result_m['return_msg'];
            }
        }

        $this->template->title = '结算中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //积分排行榜
    public function action_top2() {
        $mem = Cache::instance('memcache');
        $view = "weixin/dld/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '业绩排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('dld_qrcode', $this->uid)->as_array();

        $rankkey = "dld:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "dld:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //收益明细
    public function action_score($type=0) {
        $view = "weixin/dld/scores";
        $userobj = ORM::factory('dld_qrcode', $this->uid);

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
        $view = "weixin/dld/orders";
        $userobj = ORM::factory('dld_qrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->scores->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }

    public function action_order($tid) {
        $view = "weixin/dld/order";

        $order = ORM::factory('dld_trade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }

    //排行榜
    public function action_top() {
        $view = 'weixin/dld/top';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('dld_qrcode', $this->uid);
        $top = $this->config['rank_dld'] ? $this->config['rank_dld'] : 10;

        $result['rank'] = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('lv','=',1)->where('paid', '>', $user->paid)->count_all()+1;

        $usersobj = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->order_by('paid', 'DESC')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }

    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/dld/customer';
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

        $user = ORM::factory('dld_qrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM dld_qrcodes WHERE fopenid='$user->openid' and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM dld_qrcodes WHERE fopenid='$user->openid'")->execute()->as_array();

           if($newadd=='month')
           {
            $customer=ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month);
           }
           else
             $customer=ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/dld/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();


    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "dld_$type";

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

        $view = "weixin/dld/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }
    // public function action_test(){
    //     $this->template = 'tpl/blank';
    //     self::before();
    //     $postStr = file_get_contents("php://input");
    //     Kohana::$log->add('postStr', print_r($postStr, true));
    //     $result11=json_decode($postStr,true);
    //     Kohana::$log->add('dld', '111');
    //     Kohana::$log->add('result11', print_r($result11, true));
    //     if($postStr){
    //         Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
    //         $enddata = array('code' => 0,'msg'=>'success');
    //         $rtjson =json_encode($enddata);
    //         echo $rtjson;
    //     }
    //     $appid =$result11['app_id'];
    //     //$id=$result11['id'];
    //     $msg=$result11['msg'];
    //     $kdt_id=$result11['kdt_id'];
    //     $status=$result11['status'];
    //     //Kohana::$log->add('$status', print_r($status, true));
    //     Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
    //     require_once Kohana::find_file('vendor', 'weixin/inc');
    //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');
    //     $bid = ORM::factory('dld_login')->where('shopid','=',$kdt_id)->find()->id;
    //     $this->bid=$bid;
    //     $this->config = $config = ORM::factory('dld_cfg')->getCfg($bid);
    //     //Kohana::$log->add('$config', print_r($config, true));
    //     $this->we = $we = new Wechat($config);
    //     require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
    //     $this->access_token=ORM::factory('dld_login')->where('id', '=', $bid)->find()->access_token;
    //     if($this->access_token){
    //         $this->client =$client= new YZTokenClient($this->access_token);
    //     }else{
    //         Kohana::$log->add("dld:$bid:bname", print_r('有赞参数未填', true));
    //     }

    //     if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
    //         $posttid=urldecode($msg);
    //         $jsona=json_decode($posttid,true);
    //         Kohana::$log->add("dld:$bid", print_r($jsona, true));
    //         $trade=$jsona['trade'];
    //         if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
    //             $this->tradeImport($trade, $bid, $client, $we, $config);
    //         } else {
    //             $this->tradeImport($trade, $bid, $client, $we, $config);
    //         }
    //     }
    // }
    public function action_test(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStrdld', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        Kohana::$log->add('result11dld', print_r($result11, true));
        $msg=$result11['msg'];
        $client_id='83f328eed03bcd7d49';
        $client_secret='a4eb0f7c054c11e815c074e6f8328663';
        $sign_string = $client_id."".$msg."".$client_secret;
        $sign = md5($sign_string);
        Kohana::$log->add('sign1', print_r($sign, true));
        Kohana::$log->add('sign2', print_r($result11['sign'], true));
        if($sign != $result11['sign']){
            exit();
        }else{
            $type=$result11['type'];
            $kdt_id =$result11['kdt_id'];
            if($type=='TRADE_ORDER_STATE'){
                $status=$result11['status'];
                Kohana::$log->add('$kdt_id', print_r($kdt_id, true));
                require_once Kohana::find_file('vendor', 'weixin/inc');
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $bid = ORM::factory('dld_login')->where('shopid','=',$kdt_id)->find()->id;
                $this->bid=$bid;
                $this->config = $config = ORM::factory('dld_cfg')->getCfg($bid);
                $expiretime=ORM::factory('dld_login')->where('id', '=', $bid)->find()->expiretime;
                // if(strtotime($expiretime) < time()) die ('插件已过期');
                $this->we = $we = new Wechat($config);
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                $this->access_token=ORM::factory('dld_login')->where('id', '=', $bid)->find()->access_token;
                if($this->access_token){
                    $this->client =$client= new YZTokenClient($this->access_token);
                }else{
                    Kohana::$log->add("dld:$bid:bname", print_r('有赞参数未填', true));
                }
                if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_SUCCESS'||$status=='TRADE_CLOSED'){
                    $posttid=urldecode($msg);
                    $jsona=json_decode($posttid,true);
                    Kohana::$log->add("dld:$bid", print_r($jsona, true));
                    $tid=$jsona['tid'];
                    $method = 'youzan.trade.get';
                    $params = [
                        'with_childs'=>true,
                        'tid'=>$tid,
                    ];
                    $result = $client->post($method, $this->methodVersion, $params, $files);
                    $trade=$result['response']['trade'];
                    if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                        $this->tradeImport($trade, $bid, $client, $we, $config,$status);
                    } else {
                        $this->tradeImport($trade, $bid, $client, $we, $config,$status);
                    }
                }
            }elseif ($type=='ITEM_STATE'||$type=='ITEM_INFO') {
                $bid = ORM::factory('dld_login')->where('shopid','=',$kdt_id)->find()->id;
                $this->bid=$bid;
                $this->config = $config = ORM::factory('dld_cfg')->getCfg($bid);
                $expiretime=ORM::factory('dld_login')->where('id', '=', $bid)->find()->expiretime;
                require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                $this->access_token=ORM::factory('dld_login')->where('id', '=', $bid)->find()->access_token;
                if($this->access_token){
                    $this->client =$client= new YZTokenClient($this->access_token);
                }else{
                    Kohana::$log->add("dld:$bid:bname", print_r('有赞参数未填', true));
                }
                $status=$result11['status'];
                $posttid=urldecode($msg);
                $jsona=json_decode($posttid,true);
                Kohana::$log->add("dld{$bid}item", print_r($jsona, true));
                $data=json_decode($jsona['data'],true);
                $item_id=$data['item_id'];
                if ($status=='ITEM_DELETE'||$status=='ITEM_SALE_DOWN') {
                    //商品删除和下架
                    ORM::factory('dld_setgood')->where('bid', '=', $bid)->where('num_iid','=',$item_id)->delete_all();
                    ORM::factory('dld_goodsku')->where('bid', '=', $bid)->where('item_id','=',$item_id)->delete_all();
                }elseif($status=='ITEM_SALE_UP'||$status=='SOLD_OUT_PART'||$status=='SOLD_OUT_ALL'||$status=='SOLD_OUT_REVERT'||$status=='ITEM_CREATE'||$status=='ITEM_UPDATE'){
                    //部分售罄，全部售罄，售罄恢复
                    $method = 'youzan.item.get';
                    $params = array(
                         'item_id'=>$item_id,
                    );
                    $result = $client->post($method, '3.0.0', $params, $files);
                    Kohana::$log->add('result', print_r($result, true));
                    $item=$result['response']['item'];
                    Kohana::$log->add('item', print_r($item, true));
                    $item_id=$item['item_id'];
                    $skus=$item['skus'];
                    $type=0;
                    $sql = DB::query(Database::UPDATE,"UPDATE `dld_goodskus` SET `state` = 3  where `item_id` = $item_id");
                    $sql->execute();
                    if($skus){
                        $type=1;
                        Kohana::$log->add('sku', print_r($skus, true));
                        foreach ($skus as $sku) {
                            $properties_name_json=$sku['properties_name_json'];
                            $msgs=json_decode( $properties_name_json,true);
                            $skutitle='';
                            foreach ($msgs as $msg) {
                                if($skutitle){
                                    $skutitle=$skutitle.'/'.$msg['k'].':'.$msg['v'];
                                }else{
                                    $skutitle=$msg['k'].':'.$msg['v'];
                                }
                            }
                            $price=$sku['price']/100;
                            $title=$skutitle;
                            $sku_id=$sku['sku_id'];
                            $item_id=$sku['item_id'];
                            $num=$sku['quantity'];
                            //echo $sku_id."<br>";
                            $sku_num = ORM::factory('dld_goodsku')->where('sku_id', '=', $sku_id)->where('item_id','=',$item_id)->count_all();
                            //echo $sku_num.'<br>';
                            Kohana::$log->add('sku_id', print_r($sku_id, true));
                            if($sku_num==0 && $sku_id){
                                //echo "上面<br>";
                                $sql = DB::query(Database::INSERT,"INSERT INTO `dld_goodskus` (`bid`,`item_id`,`title`,`sku_id`, `price`,`status`,`state`,`num`) VALUES ($bid,$item_id,'$title' ,$sku_id,$price,0,1,$num)");
                                $sql->execute();
                            }else{
                                //echo "下面<br>";
                                $sql = DB::query(Database::UPDATE,"UPDATE `dld_goodskus` SET `bid` = $bid ,`item_id` = $item_id,`title` ='$title',`sku_id`=$sku_id, `price`=$price,`state` = 1 , `num`= $num where `sku_id` = $sku_id and  `item_id` = $item_id ");
                                $sql->execute();
                            }
                        }
                    }
                    $num_iid=$item['item_id'];
                    $name=$item['title'];
                    $price=$item['price']/100;
                    $pic=$item['pic_url'];
                    $url=$item['detail_url'];
                    $num=$item['quantity'];
                    $num_num = ORM::factory('dld_setgood')->where('num_iid', '=', $num_iid)->count_all();
                    if($num_num==0 && $num_iid){
                        $sql = DB::query(Database::INSERT,"INSERT INTO `dld_setgoods` (`bid`,`num_iid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,0,$type,$num)");
                        $sql->execute();
                    }else{
                        $sql = DB::query(Database::UPDATE,"UPDATE `dld_setgoods` SET `bid` = $bid ,`num_iid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 0 , `type` =$type where `num_iid` = $num_iid ");
                        $sql->execute();
                    }
                    $sql = DB::query(Database::DELETE,"DELETE FROM `dld_goodskus` where `state` =3 and `bid` = $bid ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `dld_goodskus` SET `state` =0 where `bid` = $bid");
                    $sql->execute();
                }
            }
        }

    }
    private function tradeImport($trade, $bid, $client, $we, $config,$status) {
        // print_r($trade);exit;
        $tid = $trade['tid'];
        Kohana::$log->add('$trade', print_r($trade, true));
        Kohana::$log->add('$bid', print_r($bid, true));

        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_SUCCESS', 'TRADE_CLOSED');

        if (!in_array($status, $okstatus)) {
            //echo "$tid status {$trade['status']} pass..\n";
            return;
        }
        Kohana::$log->add('$trade1', print_r($status, true));
        $dld_trade = ORM::factory('dld_trade')->where('tid', '=', $tid)->find();
        //跳过已导入订单
        if ($dld_trade->id) {
            //更新订单状态
            if ($dld_trade->status != $trade['status']) {
                $dld_trade->status = $trade['status'];
                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($status == 'TRADE_CLOSED'||$trade['refund_state'] != 'NO_REFUND'){
                $qrcods_lv=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('tid','=',$tid)->find();
                // if($qrcods_lv->id) {
                //     $qrcods_lv->lv=4;
                //     $qrcods_lv->save();
                // }
                $dld_trade->deletedd=1;
                ORM::factory('dld_score')->where('tid', '=', $dld_trade->id)->delete_all();
            }
            if($status == 'TRADE_SUCCESS'){
                $score=ORM::factory('dld_score')->where('bid','=',$bid)->where('tid','=',$dld_trade->id)->find();
                $score->paydate=time();
                $score->save();
                $dld_trade->out_time=time();
            }
            $dld_trade->save();
            //echo "$tid pass.\n";
            return;
        }
        $setstatus=0;
        foreach ($trade['orders'] as $order) {
            $num_iid=$order['item_id'];
            $setgood=ORM::factory('dld_setgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
            if($setgood->status==1){
                $setstatus=1;
            }
        }

        if($setstatus==0){
            Kohana::$log->add('pass', print_r($trade, true));
            return;
        }
        if($trade['status']=='TRADE_CLOSED_BY_USER'){
            return;
        }
        Kohana::$log->add('11111', '111111');
        //只处理一口价商品
        Kohana::$log->add('type', print_r($trade['type'], true));
        if ($trade['type'] != 'FIXED'&&$trade['type'] !='PRESENT') return;
        //男人袜不参与火种用户的商品
        Kohana::$log->add('payment', print_r($trade['payment'], true));
        //付款金额为 0
        if ($trade['payment'] <= 0) return;
        Kohana::$log->add('8888', '8888');

        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'fans_id'=>$trade['fans_info']['fans_id'],
        ];

        $result = $client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        //$userinfo = $this->youzanid2OpenID($trade['weixin_user_id'], $client);
        // print_r($userinfo);

        //只处理有下线的订单
        $dld_qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($dld_qrcode->id, true));
        if (!$dld_qrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }
        if(!$dld_qrcode->receiver_mobile){
            $dld_qrcode->receiver_mobile=$trade['receiver_mobile'];
            $dld_qrcode->save();
             $dld_qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        }
        // if (!$dld_qrcode->lv!=0&&!$dld_qrcode->lv!=1&&!$dld_qrcode->lv!=2) {
        //     //echo "$tid no OpenID pass.\n";
        //     return;
        // }
        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['pay_time']);
        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $dld_qrcode->subscribe : $dld_qrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            Kohana::$log->add('dld:之后加入的pass', $tid);
            return;
        }
        $trade['qid'] = $dld_qrcode->id;
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
        $money  = $trade['money'] = $trade['payment'];//实付金额-改价后的邮费
        // echo 'postfee'.$trade['post_fee'].'<br>';
        // echo 'postch'.$trade['adjust_fee']['post_change'].'<br>';
        // var_dump($moeny);
        Kohana::$log->add('money', print_r($money,true));
        $average=$money/($money+$trade['discount_fee']);//权重
        // echo 'average';
        // var_dump($average);
        Kohana::$log->add('average', print_r($average,true));
        $rank=0;
        $fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid||$fuser->lv==1){//有一级
            $rank=1;
            if($fuser->lv==1){
                $ffuser = $fuser;
            }else{
                $ffuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            }
            $trade['fopenid'] = $ffuser->openid;
            if($ffuser->tid){
                $gointime=ORM::factory('dld_trade')->where('bid', '=', $bid)->where('tid','=',$ffuser->tid)->find()->int_time;
            }else{
                $gointime=$ffuser->jointime;
            }
            if($gointime>strtotime($trade['pay_time'])) return;
        }
        $money1 = 0;
        Kohana::$log->add('trade[orders]', print_r($trade['orders'],true));
        foreach ($trade['orders'] as $order) {
            // $tempmoney=($order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment))*$average;
            // Kohana::$log->add('tempmoney', print_r($tempmoney,true));
            // Kohana::$log->add('orderpayment', print_r($orderpayment,true));
            Kohana::$log->add('orders', print_r($trade['orders'],true));
            // echo 'tempmoney';
            // var_dump($tempmoney);
            $item_price=$order['price'];
            $num_iid=$order['item_id'];
            $setgood=ORM::factory('dld_setgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
            Kohana::$log->add('sku_id', print_r($order['sku_id'],true));
            if($setgood->status==1){
                if($order['sku_id']&&$order['sku_id']!=0){
                    $sku_iid=$order['sku_id'];
                    $goodidcof=ORM::factory('dld_goodsku')->where('sku_id','=',$order['sku_id'])->where('bid','=',$bid)->find();
                }else{
                    $sku_iid=0;
                    $goodidcof=ORM::factory('dld_setgood')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find();
                }
                if($ffuser->group_id==0){
                    $money1=$money1+$order['num']*($item_price-$goodidcof->money);
                }else{
                    $skumoney=ORM::factory('dld_smoney')->where('bid','=',$bid)->where('sid','=',$ffuser->group_id)->where('item_id','=',$num_iid)->where('sku_id','=',$sku_iid)->find();
                    $money1=$money1+$order['num']*($item_price-$skumoney->money);
                }
            }
            Kohana::$log->add('$money11', print_r($money1,true));
        }
        if($ffuser->lv==1){
            $money1 = $trade['money1'] = number_format($money1, 2,'.',''); //一级
        }
        //订单完成金额 达到一定值 进行升级
        // $all_payment = ORM::factory('dld_trade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
        // if($all_payment){
        //     $skus = DB::query(Database::SELECT,"SELECT * FROM dld_skus WHERE bid=$bid and `money`<=$all_payment")->execute()->as_array();
        //     Kohana::$log->add('all_payment', $all_payment);
        //     Kohana::$log->add('skus', print_r($skus,true));
        //     Kohana::$log->add('sid', $skus[count($skus)-1]['id']);
        //     if($skus[count($skus)-1]['id']){
        //         $ffuser->sid = $skus[count($skus)-1]['id'];
        //         $ffuser->save();
        //     }
        // }
        // echo $dld_qrcode->lv.'<br>';
        // echo $trade['payment'].'<br>';
        // echo $config['buy_money'].'<br>';

        if($dld_qrcode->lv==1){
            $fuser = $dld_qrcode;
        }else{
            $fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $dld_qrcode->fopenid)->find();
        }
        $group=ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$fuser->id)->order_by('id','DESC')->find();
        $trade['gid']=$group->id;
        $trade['fopenid']=$fuser->openid;
        $trade['int_time']=strtotime($trade['pay_time']);
        $trade['out_time']=strtotime($trade['pay_time'])+Date::DAY*7;
        $dld_trade->values($trade);
        $dld_trade->save();

        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $num_iid=$order['item_id'];
            $num=$order['num'];
            $price=$order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment);
            $dld_order=ORM::factory('dld_order')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$num_iid)->find();
            if(!$dld_order->id)//跳过已导入的order
            {
                $dld_order->bid=$bid;
                $dld_order->tid=$tid;
                $dld_order->goodid=$num_iid;
                $dld_order->title=$title;
                $dld_order->num=$num;
                $dld_order->price=$price;
                $dld_order->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('dld_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('dld_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('dld_score')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money0', print_r($money0, true));
        //订单上线返利
        if ($money1 > 0) {
            if($dld_qrcode->lv==1){
                $fuser = $dld_qrcode;
            }else{
                $fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $dld_qrcode->fopenid)->find();
            }

            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                $fuser->scores->scoreIn($fuser, 1, $money1, $dld_qrcode->id, $dld_trade->id);
                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $msg['text']['content'] = "您推荐的「{$dld_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money 元\n销售利润：$money1 元\n\n";
                // if ($config['msg_score_tpl'])
                //     $we_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->score, $url);
                // else
                //     $we_result = $we->sendCustomMessage($msg);
                }
            }
        $newuser_self_tpl = "
销售利润+{$money1}元
个人销量+{$money}元
团队销量+{$money}元";
        $newuser_fuser_tpl = "
团队销量+{$money}元";
        if($dld_qrcode->lv==2&&$trade['payment']>=$config['buy_money']){//已经申请了 并且金额也达到了要求
            $new = 1;
            $dld_qrcode->lv = 1;
            $dld_qrcode->receiver_mobile=$trade['receiver_mobile'];
            $dld_qrcode->tid=$trade['tid'];
            $dld_qrcode->save();
            //自己购买之后 成为了代理商
            $msg['touser'] = $dld_qrcode->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $config['text_self'];
            $we->sendCustomMessage($msg);

            $dld_group = ORM::factory('dld_group');
            $dld_group->qid = $dld_qrcode->id;
            $fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $dld_qrcode->fopenid)->find();
            if($fuser->id) {
                $dld_group->fqid = $fuser->id;
                // $fgroup = ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$fuser->id)->find();
                $dld_group->fgid = ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$fuser->id)->order_by('id','DESC')->find()->id;
            }
            $dld_group->bid = $bid;
            $dld_group->save();
            $dld_flag = 1;
            $loop_qrcode = $dld_qrcode;
            $loop_group = $dld_group;
            while($dld_flag == 1){
                $loop_qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $loop_qrcode->fopenid)->find();
                $loop_group = ORM::factory('dld_group')->where('bid', '=', $bid)->where('id', '=', $loop_group->fgid)->order_by('id','DESC')->find();
                if($loop_qrcode->id){
                    //新代理进来之后 给直属上代理发
                    if($loop_qrcode->openid == $dld_qrcode->fopenid){
                        $text = str_replace('%s', $dld_qrcode->nickname, $config['text_direct']).$newuser_self_tpl;
                    }else{//新代理进来之后 给所有上级代理发
                        $text = str_replace('%s', $dld_qrcode->nickname, $config['text_group']);
                        $text = str_replace('%t', $fuser->nickname, $text).$newuser_fuser_tpl;
                    }
                    $msg['touser'] = $loop_qrcode->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    $we->sendCustomMessage($msg);

                    if(!strlen($loop_qrcode->bottom) > 0) {
                        $loop_qrcode->bottom = $dld_qrcode->id;
                    }else{
                        $loop_qrcode->bottom = $loop_qrcode->bottom.','.$dld_qrcode->id;
                    }
                    $loop_qrcode->save();
                    if(!strlen($dld_qrcode->top) > 0) {
                        $dld_qrcode->top = $loop_qrcode->id;
                    }else{
                        $dld_qrcode->top = $loop_qrcode->id.','.$dld_qrcode->top;
                    }
                    $dld_qrcode->save();
                    if(!strlen($loop_group->bottom) > 0) {
                        $loop_group->bottom = $dld_group->id;
                    }else{
                        $loop_group->bottom = $loop_group->bottom.','.$dld_group->id;
                    }
                    $loop_group->save();
                }else{
                    $dld_flag = 0;
                }
            }

        }
        // 已经是代理商情况
        // 没有上线的代理商 买东西 肯定能收到消息
        // 又上线的的代理商 买东西 自己和上面都有
        // 只要是代理商 就会轮询发

        //订单名称
        //订单金额
        //您的销售利润+
        //您的个人销量+
        //您的团队销量+
        $order_self_tpl = "
订单名称：{$trade['title']}元
订单金额：{$trade['money']}元
您的销售利润+{$money1}元
您的个人销量+{$money}元
您的团队销量+{$money}元 ";
        $order_fuser_tpl = "
订单名称：{$trade['title']}元
订单金额：{$trade['money']}元
您的团队销量+{$money}元 ";
        if($dld_qrcode->lv==1&&$new!=1){//先给自己发一条
            $text = $config['text_selforder'];
            $msg['touser'] = $dld_qrcode->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $text.$order_self_tpl;
            $we->sendCustomMessage($msg);
            $fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $dld_qrcode->fopenid)->find();
            if(strlen($dld_qrcode->top) > 0) {
                $fusers = explode(",",$dld_qrcode->top);
                for ($i=0; $fusers[$i]; $i++) {
                    $now_user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('id','=',$fusers[$i])->find();
                    if($now_user->openid == $dld_qrcode->fopenid){ //发直属上级
                        $text = str_replace('%s', $dld_qrcode->nickname, $config['text_dirctorder']).$order_fuser_tpl;
                    }else{ //上级的上级
                        $text = str_replace('%s', $dld_qrcode->nickname, $config['text_order']);
                        $text = str_replace('%t', $fuser->nickname, $text).$order_fuser_tpl;
                    }
                    $msg['touser'] = $now_user->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    if($now_user->lv==1) $we->sendCustomMessage($msg);
                }
            }
        }
        //新来的代理商和普通的客户   循环他的上线发消息
        if($new ==1 ||($dld_qrcode->lv != 1 && $dld_qrcode->fopenid)){
            //发直属上级
            $dld_fuser = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$dld_qrcode->fopenid)->find();
            $text = str_replace('%s', $dld_qrcode->nickname, $config['text_dirctorder']).$order_self_tpl;
            $msg['touser'] = $dld_fuser->openid;
            $msg['msgtype'] = 'text';
            $msg['text']['content'] = $text;
            if($new!=1)$we->sendCustomMessage($msg);//新代理就不发
            //发上级的上级
            if(strlen($dld_fuser->top) > 0) {
                $fusers = explode(",",$dld_fuser->top);
                for ($i=0; $fusers[$i]; $i++) {
                    $now_user = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('id','=',$fusers[$i])->find();
                    $text = str_replace('%s', $dld_qrcode->nickname, $config['text_order']);
                    $text = str_replace('%t', $dld_fuser->nickname, $text).$order_fuser_tpl;
                    $msg['touser'] = $now_user->openid;
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = $text;
                    if($now_user->lv==1&&$new!=1) $we->sendCustomMessage($msg);//新代理就不发
                }
            }
        }
        //TODO:更多级别返利

        //echo "$tid done.\n";
        flush();ob_flush();
    }

    // private function youzanid2OpenID($fansid, $client) {
    //     $method = 'youzan.users.weixin.follower.get';
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

        $tplmsg['data']['keyword1']['value'] = ''.number_format($total, 2,'.','');
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = ''.number_format($score, 2,'.','');
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_dld:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_dld:$bid:tplmsg", print_r($tplmsg, true));
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

        $tplmsg['data']['keyword4']['value'] = '-'.number_format($money, 2,'.','');
        $tplmsg['data']['keyword4']['color'] = '#FF0000';

        $tplmsg['data']['keyword5']['value'] = ''.number_format($total, 2,'.','');
        $tplmsg['data']['keyword5']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = '时间：'.date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';

        // Kohana::$log->add("weixin_dld:$bid:tplmsg", print_r($tplmsg, true));
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

        // Kohana::$log->add('weixin_dld:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_dld:hongbaoresult', print_r($result, true));
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."dld/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dld/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('dld_cfg')->where('bid', '=', $bid)->where('key', '=', 'dld_file_cert')->find();
        $file_key = ORM::factory('dld_cfg')->where('bid', '=', $bid)->where('key', '=', 'dld_file_key')->find();

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

        // Kohana::$log->add("weixin_dld:$bid:curl_post_ssl:cert_file", $cert_file);

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
