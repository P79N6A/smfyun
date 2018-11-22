<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtqfx extends Controller_Base {
    public $template = 'weixin/smfyun/qfx/tpl/fftpl';
    public $yzaccess_token;
    public $config;
    public $qwt_config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://jfb.smfyun.com/qwtqfx/';
    var $wx;
    var $client;
    var $token = 'smfyun';
    var $appId = 'wx4d981fffa8e917e7';
    var $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    var $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();

        if (Request::instance()->action == 'test') return;
        //if (Request::instance()->action == 'index_oauth') return;
        $_SESSION =& Session::instance()->as_array();

        $this->config = $_SESSION['qwtqfx']['config'];
        $this->openid = $_SESSION['qwtqfx']['openid'];
        $this->bid = $_SESSION['qwtqfx']['bid'];
        $this->uid = $_SESSION['qwtqfx']['uid'];
        $this->yzaccess_token = $_SESSION['qwtqfx']['access_token'];


        if ($_GET['debug']) print_r($_SESSION['qwtqfx']);


        //只能通过微信打开
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['qwtqfxa']['bid']) die('请通过微信访问。');
    }

    public function after() {
        $user = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();

         $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_qfxqrcodes WHERE fopenid='$this->openid'")->execute()->as_array();

        $customer=ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->order_by('paid', 'DESC');
        $user['follows'] =$customer->count_all();


        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_qfxqrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();

        $user['follows_month']=ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $this->openid)->where('jointime','>=',$month)->count_all();
        $user['trades'] = ORM::factory('qwt_qfxscore')->where('qid', '=', $user['id'])->where('type', 'IN', array(2,3))->count_all();

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
        //if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['qwtqfxa']['bid']) return $this->action_msg('请通过微信打开！', 'warn');

        $config = ORM::factory('qwt_qfxcfg')->getCfg($bid,1);
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if (!$_GET['openid']) $_SESSION['qwtqfx'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m'))) {
                $_SESSION['qwtqfx'] = NULL;
                die('该页面已过期！');
            }

            $userobj = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

            $_SESSION['qwtqfx']['config'] = $config;
            $_SESSION['qwtqfx']['openid'] = $openid;
            $_SESSION['qwtqfx']['bid'] = $bid;
            $_SESSION['qwtqfx']['uid'] = $userobj->id;
            $_SESSION['qwtqfx']['access_token'] =$this->yzaccess_token;
            Request::instance()->redirect('/qwtqfx/'.$_GET['url']);
        }
    }

    //申请分销
    public function action_form() {
        $view = "weixin/smfyun/qfx/form";
        $this->template = 'tpl/blank';
        $openid = $this->openid;
        self::before();
        if(!$this->bid) die('页面已过期，请重试');
        if($_POST['form']['name']&&$_POST['form']['shop']&&$_POST['form']['tel']&&$_POST['form']['memo']){
            $userobj = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
            $userobj->lv = 2;
            $userobj->name = $_POST['form']['name'];
            $userobj->tel = $_POST['form']['tel'];
            $userobj->shop = $_POST['form']['shop'];
            $userobj->bz = $_POST['form']['memo'];
            $userobj->save();
            $result['content'] = '申请提交成功，请耐心等待';
        }
        $userobj = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $openid)->find();
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
    //资产查询
    public function action_home() {
        $view = "weixin/smfyun/qfx/home";

        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('qwt_qfxqrcode', $this->uid);
        set_time_limit(0);
        $bid = $this->bid;
        $openid = $userobj->openid;
        $config = ORM::factory('qwt_qfxcfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        // echo '<pre>';
        // echo '分销商用户openid：'.$openid.'<br>';
        $childs = ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->find_all();
        $num = ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();
        // echo '<pre>';
        // $flag = 1;
        $data = array();
        $i = 0;
        $flag = $num-1;
        foreach ($childs as $k => $v) {
            $data['user_list'][$i]['openid'] = $v->openid;
            $data['user_list'][$i]['lang'] = "zh_CN";
            // echo $childs[$k].'<br>';
            // echo $k.'<br>';
            // echo $i.'<br>';
            $i++;
            if(!$v->id) {
                break;
            }
            if(($k!=0&&$k%99==0)||$k==$flag) {
                // var_dump($data);
                $result = $wx->getUserInfo_list($data);
                // var_dump($result);
                foreach ($result['user_info_list'] as $a => $b) {
                    $child_user = ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('openid','=',$b['openid'])->find();
                    if($b['subscribe']===0){
                        // echo '取消openid:'.$b['openid'];
                        $child_user->subscribe=0;
                        $child_user->save();
                    }
                    if($b['subscribe']==1&&$child_user->subscribe==0){
                        // echo '恢复openid:'.$b['openid'];
                        $child_user->subscribe=1;
                        $child_user->save();
                    }
                }
                $i = 0;
                $data = array();
            }
        }
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
        $view = "weixin/smfyun/qfx/money";
        $userobj = ORM::factory('qwt_qfxqrcode', $this->uid);

        $title5=$this->config['title5'];
        $result['aaa']=$this->config['title5'];
        $this->qwt_config = $qwt_config = ORM::factory('qwt_cfg')->getCfg($this->bid,1);

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
            if (!$this->qwt_config['mchid'] || !$this->qwt_config['apikey']) die('ERRROR: Partnerid 和 Partnerkey 未配置，不能自动转出，请联系管理员！');
            $mem = Cache::instance('memcache');
            $isget = $mem->get($this->bid.$this->openid.$MONEY);
            if($isget == 1) die('您的转出申请已经提交，请耐心等候零钱到账！');
            $mem->set($this->bid.$this->openid.$MONEY,1,60);

            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
            $shop = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            $this->qwt_config['appid'] = $shop->appid;
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = $shop->appid;
            $this->wx = $wx = new Wxoauth($this->bid,$options);

            $result_m = $this->sendMoney($userobj, $MONEY*100);

            if ($result_m['result_code'] == 'SUCCESS') {
                $userobj->scores->scoreOut($userobj, 4, $MONEY);
                $mem->set($this->bid.$this->openid.$MONEY,0,60);
                $cksum = md5($userobj->openid.$this->config['appsecret'].date('Y-m'));
                // $url = $this->baseurl.'index/'. $this->bid .'?url=score/3&cksum='. $cksum .'&openid='. base64_encode($userobj->openid);
                $url = 'http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$bid.'/qfx/score';
                //发消息通知
                $fmsg = "申请转出{$MONEY} 元成功！请到微信钱包中查收。";

                if ($this->config['msg_money_tpl']) {
                    $this->sendMoneyMessage($userobj->openid, '转出成功', $MONEY, $userobj->score, $url);
                } else {
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $userobj->openid;
                    $msg['text']['content'] = $fmsg;
                    $wx->sendCustomMessage($msg);
                }
                // exit;
                $result['ok']++;
                $result['alert'] = '转出成功!';
                return $this->action_msg("转出成功，请到微信钱包中查收。", 'suc');

            } else {
                // print_r($result);exit;
                Kohana::$log->add("weixin_qwtqfx:$bid:money", print_r($result, true));
                $result['alert'] = '转出失败：'.$result_m['return_msg'];
            }
        }

        $this->template->title = '结算中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //积分排行榜
    public function action_top2() {
        $mem = Cache::instance('memcache');
        $view = "weixin/smfyun/qfx/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '业绩排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('qwt_qfxqrcode', $this->uid)->as_array();

        $rankkey = "qwtqfx:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "qwtqfx:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //收益明细
    public function action_score($type=0) {
        $view = "weixin/smfyun/qfx/scores";
        $userobj = ORM::factory('qwt_qfxqrcode', $this->uid);

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
        $view = "weixin/smfyun/qfx/orders";
        $userobj = ORM::factory('qwt_qfxqrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->scores->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }

    public function action_order($tid) {
        $view = "weixin/smfyun/qfx/order";

        $order = ORM::factory('qwt_qfxtrade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }

    //排行榜
    public function action_top() {
        $view = 'weixin/smfyun/qfx/top';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('qwt_qfxqrcode', $this->uid);
        $top = $this->config['rank_qwtqfx'] ? $this->config['rank_qwtqfx'] : 10;

        $result['rank'] = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->where('lv','=',1)->where('paid', '>', $user->paid)->count_all()+1;

        $usersobj = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('lv','=',1)->order_by('paid', 'DESC')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }

    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/smfyun/qfx/customer';
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

        $user = ORM::factory('qwt_qfxqrcode', $this->uid);

        $bid = $this->bid;
        $openid = $user->openid;
        $config = $this->config;
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        // echo '<pre>';
        // echo '分销商用户openid：'.$openid.'<br>';
        $childs = ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->find_all();
        $num = ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();
        // echo '<pre>';
        // $flag = 1;
        $data = array();
        $i = 0;
        $flag = $num-1;
        foreach ($childs as $k => $v) {
            $data['user_list'][$i]['openid'] = $v->openid;
            $data['user_list'][$i]['lang'] = "zh_CN";
            // echo $childs[$k].'<br>';
            // echo $k.'<br>';
            // echo $i.'<br>';
            $i++;
            if(!$v->id) {
                break;
            }
            if(($k!=0&&$k%99==0)||$k==$flag) {
                // var_dump($data);
                $result = $wx->getUserInfo_list($data);
                // var_dump($result);
                foreach ($result['user_info_list'] as $a => $b) {
                    $child_user = ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('openid','=',$b['openid'])->find();
                    if($b['subscribe']===0){
                        // echo '取消openid:'.$b['openid'];
                        $child_user->subscribe=0;
                        $child_user->save();
                    }
                    if($b['subscribe']==1&&$child_user->subscribe==0){
                        // echo '恢复openid:'.$b['openid'];
                        $child_user->subscribe=1;
                        $child_user->save();
                    }
                }
                $i = 0;
                $data = array();
            }
        }
         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_qfxqrcodes WHERE fopenid='$user->openid' and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_qfxqrcodes WHERE fopenid='$user->openid'")->execute()->as_array();

           if($newadd=='month')
           {
            $customer=ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month);
           }
           else
             $customer=ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/smfyun/qfx/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->where('jointime','>=',$month)->order_by('jointime', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('qwt_qfxqrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user->openid)->order_by('jointime', 'DESC')->limit(500)->offset($offset)->find_all();


    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_qfx$type";

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

        $view = "weixin/smfyun/qfx/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }
    public function action_test(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('postStr', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('qwtqfx', '111');
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


        $bid = ORM::factory('qwt_login')->where('shopid','=',$kdt_id)->find()->id;
        $this->bid=$bid;
        $this->config = $config = ORM::factory('qwt_qfxcfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);

        require_once Kohana::find_file('vendor', 'youzan/YZTokenClient');
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if($this->yzaccess_token){
            $this->client =$client= new YZTokenClient($this->yzaccess_token);
        }else{
            Kohana::$log->add("qwtqfx:$bid:bname", print_r('有赞参数未填', true));
        }

        if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'||$status=='TRADE_CLOSED'||$status=='TRADE_CLOSED_BY_USER'){
            $posttid=urldecode($msg);
            $jsona=json_decode($posttid,true);
            Kohana::$log->add("qwtqfx:$bid", print_r($jsona, true));
            $trade=$jsona['trade'];
            if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                $this->tradeImport($trade, $bid, $client, $wx, $config);
            } else {
                $this->tradeImport($trade, $bid, $client, $wx, $config);
            }
        }
    }

    private function tradeImport($trade, $bid, $client, $wx, $config) {
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
        $qwt_qfxtrade = ORM::factory('qwt_qfxtrade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($qwt_qfxtrade->id) {

            //更新订单状态
            if ($qwt_qfxtrade->status != $trade['status']) {
                $qwt_qfxtrade->status = $trade['status'];
                $qwt_qfxtrade->save();

                //echo "$tid status updated.\n";
            }
            //退款订单删返利
            if ($trade['status'] == 'TRADE_CLOSED') ORM::factory('qwt_qfxscore')->where('tid', '=', $qwt_qfxtrade->id)->delete_all();
            if ($trade['status'] == 'TRADE_CLOSED_BY_USER') ORM::factory('qwt_qfxscore')->where('tid', '=', $qwt_qfxtrade->id)->delete_all();
            if ($trade['refund_state'] != 'NO_REFUND') ORM::factory('qwt_qfxscore')->where('tid', '=', $qwt_qfxtrade->id)->delete_all();
            //订单完成金额 达到一定值 进行升级
            $method = 'youzan.users.weixin.follower.get';
            $params = [
                'fans_id'=>$trade['weixin_user_id'],
            ];

            $result = $client->post($method, $this->methodVersion, $params, $files);
            Kohana::$log->add('result', print_r($result, true));
            $userinfo = $result['response']['user'];
            $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
            $ffuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            $all_payment = ORM::factory('qwt_qfxtrade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
            $skus = DB::query(Database::SELECT,"SELECT * FROM qwt_qfxskus WHERE bid=$bid and `money`<=$all_payment")->execute()->as_array();
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

        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'fans_id'=>$trade['weixin_user_id'],
        ];

        $result = $client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add('result', print_r($result, true));
        $userinfo = $result['response']['user'];
        //$userinfo = $this->youzanid2OpenID($trade['weixin_user_id'], $client);
        // print_r($userinfo);

        //只处理有下线的订单
        $qwt_qfxqrcode = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();
        Kohana::$log->add('id', print_r($qwt_qfxqrcode->id, true));
        if (!$qwt_qfxqrcode->id) {
            //echo "$tid no OpenID pass.\n";
            return;
        }

        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['pay_time']);

        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $qwt_qfxqrcode->subscribe : $qwt_qfxqrcode->jointime;
        Kohana::$log->add('pay_time', print_r($pay_time, true));
        Kohana::$log->add('fromtime', print_r($fromtime, true));
        if ($pay_time < $fromtime) {
            //echo "$tid Time pass.\n";
            return;
        }

        $trade['qid'] = $qwt_qfxqrcode->id;
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
        $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $trade['openid'])->find();
        if($fuser->fopenid){//有一级
            $rank=1;
            $ffuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
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
                $goodidcof=ORM::factory('qwt_qfxsetgood')->where('goodid','=',$goodid)->find();
                //按照分销商等级 设置比例
                if($ffuser->sid!=0){
                    $config['money1'] = ORM::factory('qwt_qfxsku')->where('bid','=',$bid)->where('id','=',$ffuser->sid)->find()->scale;
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
        $all_payment = ORM::factory('qwt_qfxtrade')->select(array('SUM("payment")', 'all_payment'))->where('bid','=',$bid)->where('fopenid','=',$ffuser->openid)->where('status','=','TRADE_BUYER_SIGNED')->find()->all_payment;
        if($all_payment){
            $skus = DB::query(Database::SELECT,"SELECT * FROM qwt_qfxskus WHERE bid=$bid and `money`<=$all_payment")->execute()->as_array();
            Kohana::$log->add('all_payment', $all_payment);
            Kohana::$log->add('skus', print_r($skus,true));
            Kohana::$log->add('sid', $skus[count($skus)-1]['id']);
            if($skus[count($skus)-1]['id']){
                $ffuser->sid = $skus[count($skus)-1]['id'];
                $ffuser->save();
            }
        }
        $qwt_qfxtrade->values($trade);
        $qwt_qfxtrade->save();

        Kohana::$log->add('55555', '55555');
        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['num_iid'];
            $num=$order['num'];
            $price=$order['payment']-$trade['adjust_fee']['pay_change']*($order['payment']/$ordersumpayment);
            $qwt_qfxorder=ORM::factory('qwt_qfxorder')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$qwt_qfxorder->id)//跳过已导入的order
            {
                $qwt_qfxorder->bid=$bid;
                $qwt_qfxorder->tid=$tid;
                $qwt_qfxorder->goodid=$goodid;
                $qwt_qfxorder->title=$title;
                $qwt_qfxorder->num=$num;
                $qwt_qfxorder->price=$price;
                $qwt_qfxorder->save();
            }
        }
        Kohana::$log->add('4444444', '444444');
        //删除重复返利记录
        ORM::factory('qwt_qfxscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('qwt_qfxscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('qwt_qfxscore')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();
        Kohana::$log->add('33333', '333333');
        $msg['msgtype'] = 'text';
        $title5=$config['title5'];
        Kohana::$log->add('222222', '22222222');
        Kohana::$log->add('money0', print_r($money0, true));

        //订单上线返利
        if ($money1 > 0) {
            $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $bid)->where('lv', '=', 1)->where('openid', '=', $qwt_qfxqrcode->fopenid)->find();
            if ($fuser->id) {
                //echo "$tid money1:$money1 \n";
                $fuser->scores->scoreIn($fuser, 2, $money1, $qwt_qfxqrcode->id, $qwt_qfxtrade->id);

                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m'));
                // $url = $this->baseurl.'index/'. $bid .'?url=orders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
                $url = 'http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$bid.'/qfx/order';
                $msg['text']['content'] = "您推荐的{$config['title1']}「{$qwt_qfxqrcode->nickname}」完成一笔订单！\n\n实付金额：$money {$title5}\n推广佣金：$money1 {$title5}\n\n<a href=\"$url\">查看我的{$config['title5']}明细</a>";

                if ($config['msg_score_tpl'])
                    $wx_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->score, $url);
                else
                    $wx_result = $wx->sendCustomMessage($msg);
            }
        }

        //TODO:更多级别返利

        //echo "$tid done.\n";
        flush();ob_flush();
    }

    // private function youzanid2OpenID($fansid, $client) {
    //     $method = 'youzan.users.weixin.follower.get';
    //     $params = array('user_id' => $fansid,);

    //     $result = $client->post($this->yzaccess_token,$method, $params);
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
        //Kohana::$log->add("weixin_qwtqfx:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_qwtqfx:$bid:tplmsg", print_r($tplmsg, true));
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

        $tplmsg['data']['keyword4']['value'] = '-'.number_format($money, 2);
        $tplmsg['data']['keyword4']['color'] = '#FF0000';

        $tplmsg['data']['keyword5']['value'] = ''.number_format($total, 2);
        $tplmsg['data']['keyword5']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = '时间：'.date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';

        // Kohana::$log->add("weixin_qwtqfx:$bid:tplmsg", print_r($tplmsg, true));
        return $this->wx->sendTemplateMessage($tplmsg);
    }
    private function hongbao($config, $openid, $wx='', $bid=1, $money)
    {
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);


        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $qwt_config['mchid']; //支付商户号
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
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $qwt_config['apikey']));//将签名转化为大写
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
        $qwt_config = $this->qwt_config;
        // if (!$this->wx) {
        //     require_once Kohana::find_file('vendor', 'weixin/inc');
        //     require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        //     $this->wx = $wx = new Wechat($config);
        // }

        $mch_billno = $qwt_config['mchid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["mch_appid"] = $qwt_config['appid'];
        $data["mchid"] = $qwt_config['mchid']; //商户号
        $data["nonce_str"] = $this->wx->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.$config['title5'].'转出';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->wx->getSignature($data, 'trim')."&key=" . $qwt_config['apikey']));
        $postXml = $this->wx->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_qwtqfx:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_qwtqfx:hongbaoresult', print_r($result, true));
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();

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

        // Kohana::$log->add("weixin_qwtqfx:$bid:curl_post_ssl:cert_file", $cert_file);

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
