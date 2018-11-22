<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtdld extends Controller_Base {
    // public $template = 'weixin/smfyun/dld/tpl/fftpl';
    public $template = 'weixin/smfyun/dld/tpl/fatpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://dld.smfyun.com/qwtdld/';
    var $wx;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'cron') return;
        if (Request::instance()->action == 'myteam') return;
        if (Request::instance()->action == 'userinfo') return;
        if (Request::instance()->action == 'err_trade') return;
        if (Request::instance()->action == 'order_top') return;
        //if (Request::instance()->action == 'index_oauth') return;
        $this->config = $_SESSION['qwtdld']['config'];
        $this->openid = $_SESSION['qwtdld']['openid'];
        $this->bid = $_SESSION['qwtdld']['bid'];
        $this->uid = $_SESSION['qwtdld']['uid'];
        //只能通过微信打开
    //     if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['qwtwfbs']['bid']) die('请通过微信访问。');
    }

    public function after() {
        $user = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

         $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_dldqrcodes WHERE fopenid='$this->openid'")->execute()->as_array();

        $customer=ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->order_by('paid', 'DESC');
        $user['follows'] =$customer->count_all();


        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_dldqrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();

        $user['follows_month']=ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('jointime','>=',$month)->count_all();
        $user['trades'] = ORM::factory('qwt_dldscore')->where('qid', '=', $user['id'])->where('type', 'IN', array(2,3))->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    public function action_err_trade($bid){
        $trades = ORM::factory('qwt_dldtrade')->where('bid','=',$bid)->where('fopenid','=',null)->find_all();
        foreach ($trades as $k => $v) {
            echo $k.'<br>';
            $user = ORM::factory('qwt_dldqrcode')->where('id','=',$v->qid)->where('bid','=',$bid)->find();
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

        $config = ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->access_token;
        if (!$_GET['openid']) $_SESSION['dld'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m'))) {
                $_SESSION['dld'] = NULL;
                die('该页面已过期！');
            }

            $userobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

            $_SESSION['dld']['config'] = $config;
            $_SESSION['dld']['openid'] = $openid;
            $_SESSION['dld']['bid'] = $bid;
            $_SESSION['dld']['uid'] = $userobj->id;
            $_SESSION['dld']['access_token'] =$this->access_token;
            Request::instance()->redirect('/qwtdld/'.$_GET['url']);
        }
    }
    public function action_cron($bid){
        set_time_limit(0);
        $this->config=$config=ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        $login=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$login->id) die('没有此商户');
        $day=date('d',time());
        // if($day!=$config['date']) die ('没有到结算日期');
        $qrs=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->find_all();
        foreach ($qrs as $v) {
            $nawtime=time();
            $monthtype='%Y-%m';
            $date=date('Y-m-d',time());
            $timestamp=strtotime($date);
            $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
            $month=date('Y-m',strtotime("$firstday +1 month -1 day"));
            $score=ORM::factory('qwt_dldscore')->where('bid','=',$bid)->where('qid','=',$v->id)->where('bz','=',$month)->find();
            if(!$score->id){
                $groups=ORM::factory('qwt_dldgroup')->where('bid','=',$bid)->where('qid','=',$v->id)->find_all();
                $monthjs_pmoney=0;
                foreach ($groups as $group) {
                    if($group->bottom){
                        $bottom1='('.$group->id.','.$group->bottom.')';
                    }else{
                        $bottom1='('.$group->id.')';
                    }
                    $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from qwt_dldtrades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                    $skujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                    if(!$skujs->id){
                        $fskujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                        if(!$fskujs->id){
                           $scalejs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scalejs=0;
                        }
                    }else{
                        $scalejs=$skujs->scale;
                    }
                    $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;
                    $child_groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                    $childjs_moneys=0;
                    $monthjs_pmoney=0;
                    foreach ($child_groups as $child_group) {
                        if($child_group->bottom){
                            $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                        }else{
                            $bottom2='('.$child_group->id.')';
                        }

                          //echo $bottom2."<br>";
                        $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from qwt_dldtrades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from qwt_dldtrades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                        $skujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>=',$monthjs_ltmoney)->find();
                        if(!$skujs->id){
                            $fskujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                            if(!$fskujs->id){
                                $scalejs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
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
                    ORM::factory('qwt_dldscore')->scoreOut($v,4, $monthjs_pmoney,'','',$month);
                }

            }
        }
        exit;
    }
    private function sendMoney1($userobj, $money,$time) {
        $config = $this->config;
        $openid = $userobj->openid;
        if (!$this->wx) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            $this->wx=$wx = new Wxoauth($this->bid,$options);
        }
        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号
        $data["mch_appid"] = $config['appid'];
        $data["mchid"] = $config['partnerid']; //商户号
        $data["nonce_str"] = $this->wx->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.'的'.$time.'月收益';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->wx->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->wx->xml_encode($data);

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
       $config = ORM::factory('qwt_dldcfg')->getCfg($bid,1);
       $urls = array('form','memberpage','order_detail','account_set','code');
       if(!in_array($url,$urls)) die('url不合法'.$url);
        // require_once Kohana::find_file('vendor/kdt', 'lib/KdtRedirectApiClient');

        // if(!isset($_GET['open_id'])){
        //     $appId = ORM::factory('qwt_dldcfg')->where('bid', '=', $bid)->where('key', '=', 'youzan_appid')->find()->value;
        //     $appSecret = ORM::factory('qwt_dldcfg')->where('bid', '=', $bid)->where('key', '=', 'youzan_appsecret')->find()->value;
        //     $client = new KdtRedirectApiClient($appId, $appSecret);
        //     $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        //     $client->redirect($callback_url, 'snsapi_userinfo');
        // }else{


        //     $userobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $_GET['open_id'])->find();
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

            //require Kohana::find_file('vendor', 'weixin/wechat.class');
            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

            $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
            if (!$_GET['callback']) $callback_url .= "{$split}callback=1";

            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            $this->wx=$wx = new Wxoauth($this->bid,$options);

            if (!$_GET['callback']) {
                $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            } else {
                $token = $wx->sns_getOauthAccessToken();
                $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
                $openid = $userinfo['openid'];
                $userinfo['lv'] = 0;
            }

            if (!$openid) $_SESSION['dld'] = NULL;

            if ($openid) {
                $userobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
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
        Request::instance()->redirect('/qwtdld/'.$url.'/'.$userobj->openid);
    }
    //个人中心
    public function action_memberpage(){
        $view = "weixin/smfyun/dld/memberpage";
        $this->template = 'tpl/blank';
        $openid = $this->openid;
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        $config = ORM::factory('qwt_dldcfg')->getCfg($this->bid,1);
        // $config['buy_money'] = 35;
        $userobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv==1){//正常
            $v = $userobj;
            $group1=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('qid','=',$v->id)->order_by('lastupdate','DESC')->find();
          if($group1->bottom){
            $bottom='('.$group1->bottom.')';
            //echo $bottom.'<br>';
            $group_ay=DB::query(Database::SELECT,"SELECT count(id) as group_num from qwt_dldgroups where bid=$v->bid and id in $bottom ")->execute()->as_array();
            $group_num=$group_ay[0]['group_num'];
          }else{
            $group_num=0;
          }
          //echo $group_num.'<br>';所辖团队成员
          $qr_num=ORM::factory('qwt_dldqrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','!=',1)->where('fopenid','!=','')->count_all();
           $groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
           $month=date('Y-m',time());
               //echo $month.'<br>';
            $daytype='%Y-%m-%d';
            $monthtype='%Y-%m';
            $day=date('Y-m-d',time());
            $month_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as month_pnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
            $result['month_pnum']=$month_pnum[0]['month_pnum'];
            //echo $month_pnum.'<br>';当月个人销量
            $day_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as day_pnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
            $result['day_pnum']=$day_pnum=$day_pnum[0]['day_pnum'];
             //echo $day_pnum.'<br>';当天个人销量
            $all_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as all_pnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' ")->execute()->as_array();
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
              $day_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as day_tnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
              $day_tnum+=$day_tnum1[0]['day_tnum'];
              $result['day_tnum'] = $day_tnum;
              //echo  $day_tnum.'<br>';当天团队销量
              $month_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
              $month_tnum+=$month_tnum1[0]['month_tnum'];
              $result['month_tnum'] = $month_tnum;
              //echo  $month_tnum.'<br>';当月团队销量
              $all_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as all_tnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 ")->execute()->as_array();
              $all_tnum+=$all_tnum1[0]['all_tnum'];
              $result['all_tnum'] = $all_tnum;
              //累计团队销量
              $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
              $month_tmoney1=$month_tmoney1[0]['month_tmoney'];
              //echo  $month_tmoney.'<br>';
              $sku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                if(!$sku->id){
                    $fsku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                    if(!$fsku->id){
                       $scale=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
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
              $child_groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
              $child_moneys=0;
              foreach ($child_groups as $child_group) {
                    if($child_group->bottom){
                         $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                      }else{
                            $bottom2='('.$child_group->id.')';
                      }

                    //echo $bottom2."<br>";
                    $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from qwt_dldtrades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                    //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                    $sku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>=',$month_ltmoney)->find();
                    if(!$sku->id){
                        $fsku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>',$month_ltmoney)->find();
                        if(!$fsku->id){
                           $scale=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
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
            $day_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as day_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$day' ")->execute()->as_array();
            $result['day_pxmoney'] = $day_pxmoney=$day_pxmoney[0]['day_pxmoney'];
            //当天销售利润
            $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
            $result['month_pxmoney']=$month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
            //当月销售利润
            //echo  $month_pxmoney.'<br>';
            $all_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as all_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
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
            $tel = ORM::factory('qwt_dldqrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->find();
            if($tel->id){
                $result['error'] = '对不起，您的手机号已经注册了';
            }else{
                $tel = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
                $tel->tel = $_POST['tel'];
                $tel->save();
                Request::instance()->redirect($config['buy_url']);
            }
        }
        $result['lv'] = $userobj->lv;

        $this->template->content = View::factory($view)->bind('result', $result)->bind('user',$userobj)->bind('config',$config);
    }
    public function action_myteam($openid,$bid){
        $view = "weixin/smfyun/dld/myteam";
        $this->template = 'tpl/blank';
        self::before();
        $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $result['follows'] = ORM::factory('qwt_dldqrcode')->where('lv','=',1)->where('bid','=',$bid)->where('fopenid','=',$openid)->order_by('id','DESC')->find_all();
        $result['num'] = ORM::factory('qwt_dldqrcode')->where('lv','=',1)->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();

        $this->template->content = View::factory($view)
            ->bind('result', $result);
    }
    //结算记录
    public function action_account_record($uid){
        if(!$this->bid) die('页面已过期，请重试');
        $view = "weixin/smfyun/dld/account_record";
        $this->template = 'tpl/blank';
        self::before();
        $this->uid = $uid;
        $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$this->bid)->where('id','=',$this->uid)->find();
        $records = ORM::factory('qwt_dldscore')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('score','<',0)->find_all();
        $this->template->content = View::factory($view)
            ->bind('records', $records)
            ->bind('config', $config);
    }
    public function action_order_top($bid){
        $view = "weixin/smfyun/dld/now_top";
        $this->template = 'tpl/blank';
        self::before();
        $userobjs = ORM::factory('qwt_dldqrcode')->where('lv','=',1)->where('bid','=',$bid)->find_all();
        $user = array();
        foreach ($userobjs as $k => $v) {
            if($_POST['start']&&$_POST['end']){
                $now_payment = ORM::factory('qwt_dldtrade')->where('bid','=',$bid)->where('fopenid','=',$v->openid)->where('int_time','<',strtotime($_POST['end']))->where('int_time','>',strtotime($_POST['start']))->select(array('SUM("payment")', 'all_payment'))->find()->all_payment;
            }else{
                $now_payment = ORM::factory('qwt_dldtrade')->where('bid','=',$bid)->where('fopenid','=',$v->openid)->select(array('SUM("payment")', 'all_payment'))->find()->all_payment;
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
        $view = "weixin/smfyun/dld/order_detail";
        $this->template = 'tpl/blank';
        self::before();

        // echo $this->uid;
        // exit;
        $this->uid = $uid;
        $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
        $orders = ORM::factory('qwt_dldtrade')->where('bid','=',$this->bid)->where('fopenid','=',$openid)->find_all();
        // echo $orders;
        // var_dump($orders);
        $this->template->content = View::factory($view)->bind('user',$user)->bind('orders',$orders);
    }
    //结算信息设置
    public function action_account_set($uid){
        if(!$this->bid) die('页面已过期，请重试');
        $view = "weixin/smfyun/dld/account_set";
        $this->template = 'tpl/blank';
        self::before();
        // echo $this->uid;
        // exit;
        $this->uid = $uid;
        $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$this->bid)->where('id','=',$this->uid)->find();
        if ($_POST['form']) {
            $user->name=$_POST['form']['name'];
            $user->tel=$_POST['form']['tel'];
            $user->alipay_name=$_POST['form']['zfb'];
            $user->save();
            Request::instance()->redirect('/qwtdld/memberpage/'.$user->openid);
        }
        $this->template->content = View::factory($view)->bind('user',$user);
    }
    //预约链接
    public function action_form() {
        $view = "weixin/smfyun/dld/form";
        $this->template = 'tpl/blank';
        $openid = $this->openid;
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        $config = ORM::factory('qwt_dldcfg')->getCfg($this->bid,1);
        // $config['buy_money'] = 35;

        $userobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if(!Model::factory('select_experience')->dopinion($bid,'dld')){
            die('对不起，体验代理商名额已达到上限，请前往商城续费');
        }
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
            $tel = ORM::factory('qwt_dldqrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->find();
            if($tel->id){
                $result['error'] = '对不起，您的手机号已经注册了';
            }else{
                $tel = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
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
    public function action_code(){
        $view = "weixin/smfyun/dld/code";
        $this->template = 'tpl/blank';
        $openid = $this->openid;
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        $config = ORM::factory('qwt_dldcfg')->getCfg($this->bid,1);
        // $config['buy_money'] = 35;
        if(!Model::factory('select_experience')->dopinion($bid,'dld')){
            die('对不起，体验代理商名额已达到上限，请前往商城续费');
        }
        $userobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
        if($userobj->lv==1){
            $result['content'] = '您已经具有代理商资格了，不需要在申请了！';
        }

        if($_POST['code']){
            $tel = ORM::factory('qwt_dldqrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel'])->find();
            if($_POST['code'] == $config['code']&&!$tel->id){//code正确并且tel正常
                $userobj->lv = 1;
                $userobj->code = $_POST['code'];
                $userobj->fopenid = '';//上级置为空
                $userobj->save();
                $group = ORM::factory('qwt_dldgroup');
                $group->bid = $this->bid;
                $group->qid = $userobj->id;
                $group->fgid = 0;
                $group->fqid = 0;
                $group->save();
                $result['content'] = '恭喜您成为了第一层代理商！';

                $tel = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
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
            $tel = ORM::factory('qwt_dldqrcode')->where('lv', '=', 1)->where('bid', '=', $this->bid)->where('tel', '=', $_POST['tel2'])->find();
            if($tel->id){
                $result['error'] = '对不起，您的手机号已经注册了';
            }else{
                $tel = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
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
        $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
        if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
        if(!$fuser->id) die('不合法2');
        $config=ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        // require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtdld/commends/'.$mopenid.'/'.$bid;
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        $this->wx=$wx = new Wxoauth($this->bid,$options);

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $signPackage = $wx->getJsSign($callback_url);
        $userobj = ORM::factory('qwt_dldqrcode', $this->uid);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/smfyun/dld/tpl/tpl2';
            self::before();
            $token = $wx->sns_getOauthAccessToken();
            $openid=$token['openid'];
            $userinfo = $wx->getOauthUserinfo($token['access_token'],$openid);
            Kohana::$log->add("1commends_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            // if($openid=='okxUMwaR4qmy8axy2Acx5dok15e0'){
            //     echo '<pre>';
            //     var_dump($userinfo);
            //     exit;
            // }
            if(!$openid||!$userinfo['nickname']){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
            Kohana::$log->add("2commends_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            $user=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
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

            $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
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
                         $loop_qrcode = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$loop_qrcode->fopenid)->find();
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
                        if($loop_qrcode->lv==1) $wx->sendCustomMessage($msg);
                    }
                }
            }
            $view = "weixin/smfyun/dld/commendsother";//别人直接是url进，不需要加密
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                $result['title'] = $user->nickname.'的推荐商品';
                $view = "weixin/smfyun/dld/commends";//自己进 跳入shareopenid 需要加密url
            }
            $this->template->title = $fuser->nickname.'推荐商品';
            $result['commends'] = ORM::factory('qwt_dldsetgood')->where('bid','=',$bid)->where('status','=',1)->find_all();
            $result['openid'] = $user->openid;
            $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('shop', $shop)->bind('user', $user);
        }else{//得到code为止
            $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }
    }
    public function action_userinfo($lv){
        $bid = 4;
        // $openid = 'okxUMwdbayaJNX_8axH7tDl29kjQ';
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $config = ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        //require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        $this->wx=$wx = new Wxoauth($this->bid,$options);
        $users = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('lv','=',$lv)->where('nickname','=','')->find_all();
        foreach ($users as $k => $v) {
            $result = $wx->getUserInfo($v->openid);
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
        $this->bid=$bid;
        $fopenid = base64_decode($mopenid);
        $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
        if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
        if(!$fuser->id) die('不合法1');
        $config=ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        // require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtdld/shareopenid/'.$mopenid.'/'.$gid.'/'.$bid;
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        $this->wx=$wx = new Wxoauth($this->bid,$options);

        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $wx->getJsSign($callback_urlsdk);

        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/smfyun/dld/tpl/tpl';
            self::before();
            $token = $wx->sns_getOauthAccessToken();
            $openid=$token['openid'];
            $userinfo = $wx->getOauthUserinfo($token['access_token'],$openid);
            Kohana::$log->add("1shareopenid_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            if(!$openid||!$userinfo['nickname']){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
            Kohana::$log->add("2shareopenid_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            // if($openid=='okxUMwaR4qmy8axy2Acx5dok15e0'){
            //     echo '<pre>';
            //     var_dump($userinfo);
            //     exit;
            // }
            $user=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
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
            $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
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
                         $loop_qrcode = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$loop_qrcode->fopenid)->find();
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
                        if($loop_qrcode->lv==1) $wx->sendCustomMessage($msg);
                    }
                }
            }
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                // $result['content'] = '快将该商品推荐给你的好友吧';
            }
            $commend = ORM::factory('qwt_dldsetgood')->where('bid','=',$bid)->where('id','=',$gid)->find();
            if($status==2){//自己进入
                if($commend->type==1){//多规格
                    if($user->group_id>0){//有分组
                        $sku_commends = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('item_id','=',$commend->num_iid)->find_all();
                    }else{
                        $sku_commends = ORM::factory('qwt_dldgoodsku')->where('bid','=',$bid)->where('item_id','=',$commend->num_iid)->find_all();
                    }
                }else{
                    if($user->group_id>0){//有分组
                        $smoney = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('sku_id','=',0)->where('item_id','=',$commend->num_iid)->find();
                        $commend->money = $smoney->money?$smoney->money:'0.00';
                    }else{

                    }
                }
            }
            $view = "weixin/smfyun/dld/share";
            $this->template->title = $fuser->nickname.':'.$commend->title;
            $this->template->content = View::factory($view)->bind('status', $status)->bind('commend', $commend)->bind('result', $result)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('sku_commends', $sku_commends)->bind('user', $user)->bind('bid', $bid);
        }else{//得到code为止
            $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }
    }
    public function action_shop($mopenid,$bid){//有赞店铺分享页面 自己可以打开 别人也可以打开
        $this->bid=$bid;
        $fopenid = $mopenid;
        $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
        if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
        $config=ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        //require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtdld/shop/'.$mopenid.'/'.$bid;
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        $this->wx=$wx = new Wxoauth($this->bid,$options);

        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $wx->getJsSign($callback_urlsdk);

        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/smfyun/qwtdld/tpl/tpl';
            self::before();
            $token = $wx->sns_getOauthAccessToken();
            $openid=$token['openid'];
            $userinfo = $wx->getOauthUserinfo($token['access_token'],$openid);
            Kohana::$log->add("1shop_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            if(!$openid||!$userinfo['nickname']){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                header("Location:$auth_url");exit;
            }
            Kohana::$log->add("2shop_userinfo_dld:$bid:$openid", print_r($userinfo, true));
            // if($openid=='okxUMwaR4qmy8axy2Acx5dok15e0'){
            //     echo '<pre>';
            //     var_dump($userinfo);
            //     exit;
            // }
            $user=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
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
            $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
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
            $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
            $view = "weixin/smfyun/dld/shop";
            $this->template->title = $fuser->nickname.'推荐商品';
            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('shop', $shop);
        }else{//得到code为止
            $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }
    }
    public function action_appointment($mopenid,$bid){//推荐代理给别人
        $this->bid=$bid;
        $fopenid = $mopenid;
        $config = ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        //require Kohana::find_file('vendor', 'weixin/wechat.class');
        if($_POST['tel']&&$_POST['openid']){
            $openid = $_POST['openid'];
            $tel = ORM::factory('qwt_dldqrcode')->where('lv', '=', 1)->where('bid', '=', $bid)->where('tel', '=', $_POST['tel'])->find();
            if($tel->id){
                $result['content'] = '您成功申请代理商资格，请单笔订单金额满足'.$config['buy_money'].'元即可激活资格！';
                $user = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                $result['error'] = '对不起，您的手机号已经注册了';
                $result['lv'] = 2;
            }else{
                $user = ORM::factory('qwt_dldqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
                $user->tel = $_POST['tel'];
                $user->save();
                Request::instance()->redirect($config['buy_url']);
            }
        }else{
            $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/qwtdld/appointment/'.$mopenid.'/'.$bid;
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            $this->wx=$wx = new Wxoauth($this->bid,$options);

            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
            if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
            $signPackage = $wx->getJsSign($callback_url);
            $userobj = ORM::factory('qwt_dldqrcode', $this->uid);
            if($_GET['code']){//静默授权当事人
                $this->template = 'tpl/blank';
                self::before();
                $token = $wx->sns_getOauthAccessToken();
                // var_dump($token);
                $openid=$token['openid'];

                $userinfo = $wx->getOauthUserinfo($token['access_token'],$openid);

                if(!isset($token['openid'])){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                    // echo 'code错误下面跳转';
                    $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');
                    header("Location:$auth_url");exit;
                }else{
                    // echo '2222222222222222';
                }
                //取出当前用户
                $user = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                if($fuser->lv != 1) die($fuser->nickname.'代理商资格未获得！');
                if($user->id){//如果当前用户存在
                    if($user->openid == $fopenid){//如果是自己点击预约链接
                        $result['content'] = '快将本页面分享给好友邀请他们成为代理商吧！';
                        $result['_title'] = '邀请代理';
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
                            // $wx->sendCustomMessage($msg);
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
                    // $access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->access_token;
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
                    //     $this->sendtplcoupon($fopenid,$config,$text,$wx);
                    // }else{
                    //     $msg['touser'] = $fopenid;
                    //     $msg['msgtype'] = 'text';
                    //     $msg['text']['content'] = $text;
                    //     $wx->sendCustomMessage($msg);
                    // }
                }

            }else{//得到code为止
                $auth_url = $wx->sns_getOauthRedirect($callback_url, '', 'snsapi_userinfo');

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

        $view = "weixin/smfyun/dld/appointment";//别人直接是url进，不需要加密

        $this->template->title = $fuser->nickname.'的邀请';

        $result['openid'] = $user->openid;
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname)->bind('shop', $shop)->bind('fuser',$fuser);
    }
    //默认页面
    public function action_home() {
        $view = "weixin/smfyun/dld/home";

        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('qwt_dldqrcode', $this->uid);

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
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $view = "weixin/smfyun/dld/money";
        $userobj = ORM::factory('qwt_dldqrcode', $this->uid);

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

            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            $this->wx=$wx = new Wxoauth($this->bid,$options);
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
                    $wx->sendCustomMessage($msg);
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
        $view = "weixin/smfyun/dld/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '业绩排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('qwt_dldqrcode', $this->uid)->as_array();

        $rankkey = "dld:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "dld:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //收益明细
    public function action_score($type=0) {
        $view = "weixin/smfyun/dld/scores";
        $userobj = ORM::factory('qwt_dldqrcode', $this->uid);

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
        $view = "weixin/smfyun/dld/orders";
        $userobj = ORM::factory('qwt_dldqrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->scores->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }

    public function action_order($tid) {
        $view = "weixin/smfyun/dld/order";

        $order = ORM::factory('qwt_dldtrade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }

    //排行榜
    public function action_top() {
        $view = 'weixin/smfyun/dld/top';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('qwt_dldqrcode', $this->uid);
        $top = $this->config['rank_dld'] ? $this->config['rank_dld'] : 10;

        $result['rank'] = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('lv','=',1)->where('paid', '>', $user->paid)->count_all()+1;

        $usersobj = ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->order_by('paid', 'DESC')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }

    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/smfyun/dld/customer';
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

        $user = ORM::factory('qwt_dldqrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_dldqrcodes WHERE fopenid='$user->openid' and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_dldqrcodes WHERE fopenid='$user->openid'")->execute()->as_array();

           if($newadd=='month')
           {
            $customer=ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month);
           }
           else
             $customer=ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/smfyun/dld/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('qwt_dldqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->order_by('paid', 'DESC')->limit(500)->offset($offset)->find_all();


    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_dld$type";

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

        $view = "weixin/smfyun/dld/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }
    public function action_test() {
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/dld/orders";
        $this->template->content = View::factory($view);
    }
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
        return $this->wx->sendTemplateMessage($tplmsg);
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
        return $this->wx->sendTemplateMessage($tplmsg);
    }
    private function hongbao($config, $openid, $wx='', $bid=1, $money)
    {
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$wx) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            $this->wx=$wx = new Wxoauth($this->bid,$options);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
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
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
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

        if (!$this->wx) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            $this->wx=$wx = new Wxoauth($this->bid,$options);
        }

        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["mch_appid"] = $config['appid'];
        $data["mchid"] = $config['partnerid']; //商户号
        $data["nonce_str"] = $this->wx->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.$config['title5'].'转出';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->wx->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->wx->xml_encode($data);

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
        $file_cert = ORM::factory('qwt_dldcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_dldfile_cert')->find();
        $file_key = ORM::factory('qwt_dldcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_dldfile_key')->find();

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
