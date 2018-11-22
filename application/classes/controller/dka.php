<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Dka extends Controller_Base {
    public $template = 'weixin/dka/tpl/ftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public $access_token;
    public $methodVersion='3.0.0';
    var $baseurl = 'http://dka.smfyun.com/dka/';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "dka";
        parent::before();

        if (Request::instance()->action == 'images') return;

        if (Request::instance()->action == 'tplcheck') return;

        if (Request::instance()->action == 'cron') return;
        $_SESSION =& Session::instance()->as_array();

        if (!$_GET['openid']) {
            if (!$_SESSION['dka']['bid']) die('页面已过期。');
            if (!$_SESSION['dka']['openid']) die('Access Deined..');
        }
        $biz = ORM::factory('dka_login')->where('id','=',$_SESSION['dka']['bid'])->find();
        if ($biz->expiretime && strtotime($biz->expiretime)+86400 < time()) die('您的账号已过期');
        $this->config = $_SESSION['dka']['config'];
        $this->openid = $_SESSION['dka']['openid'];
        $this->bid = $_SESSION['dka']['bid'];
        $this->uid = $_SESSION['dka']['uid'];
        $this->access_token=ORM::factory('dka_login')->where('id', '=', $this->bid)->find()->access_token;
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['dkaa']['bid']) die('请通过微信访问。');
        $sname = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','scorename')->find()->value;
        if($sname){
            $this->scorename = $sname;
        }else{
            $this->scorename = '积分';
        }
        $this->template->scorename = $this->scorename;
        $config['pstatus'] = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','pstatus')->find()->value;
        $config['ptime'] = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','drawtime')->find()->value;
        if($config['pstatus']==1&&strtotime($config['ptime'])>time()){
            $pstatus=1;
        }else{
            $pstatus=0;
        }
        $this->template->pstatus = $pstatus;
        if ($_GET['debug']) print_r($_SESSION['dka']);
    }

    public function after() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('fopenid', '=', $user['openid'])->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
//入口
    public function action_index($bid) {
        $config = ORM::factory('dka_cfg')->getCfg($bid);

        if (!$_GET['openid']) $_SESSION['dka'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m-d'))) {
                $_SESSION['dka'] = NULL;
                die('Access Deined!');
            }

            $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $userobj->ip = Request::$client_ip;
            $userobj->save();

            $_SESSION['dka']['config'] = $config;
            $_SESSION['dka']['openid'] = $openid;
            $_SESSION['dka']['bid'] = $bid;
            $_SESSION['dka']['uid'] = $userobj->id;

            if ($bid == 2) {
                // print_r($_SESSION);exit;
            }
            Request::instance()->redirect('/dka/'.$_GET['url']);
        }
    }
    //订单宝部分
    public function action_home() {
        $this->template = 'weixin/dka/tpl/fftpl';
        self::before();
        $view = "weixin/dka/home";

        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('dka_qrcode', $this->uid);

        //新用户关注收益
        // if ($this->config['money_init'] > 0 && ORM::factory('dka_score')->where('qid', '=', $this->uid)->where('type', '=', 6)->count_all() == 0) {
        //     $userobj->scores->scoreIn($userobj, 6, $this->config['money_init']/100);
        // }

        //当前收益
        $result['score'] = $userobj->cash = $userobj->details->select(array('SUM("cash")', 'total_score'))->find()->total_score;
        //预计收益
        $userobj->money = $result['money'] = $userobj->details->select(array('SUM("cash")', 'total_score'))->where('cash', '>', 0)->find()->total_score;
        //累计付款金额
        $userobj->paid = $result['paid'] = $userobj->details->select(array('SUM("money")', 'money_paid'))->where('type', 'IN', array(2,3))->find()->money_paid;

        if ($userobj->id) $userobj->save();

        //本月新增用户
        $month = strtotime(date('Y-m-1'));
        $result['follows_month'] = ORM::factory('dka_qrcode')->where('fopenid', '=', $this->openid)->where('jointime', '>', $month)->count_all();

        $this->template->title = '我的收益';
        $this->template->content = View::factory($view)->bind('result', $result);
    }
    //提示页面
    public function action_msg($msg, $type='suc') {
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/dka/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }

    //提现
    public function action_money($out=0, $cksum='') {
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->template = 'weixin/dka/tpl/fftpl';
        self::before();
        $view = "weixin/dka/money";
        $userobj = ORM::factory('dka_qrcode', $this->uid);

        //可提现金额
        $result['money_now'] = $userobj->details->select(array('SUM("cash")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
        //已结算金额
        $result['money_paid'] = $userobj->details->select(array('SUM("cash")', 'money_paid'))->where('paydate', '<', time())->where('type', 'IN', array(1,2,3))->find()->money_paid;
        //待结算金额
        $result['money_nopaid'] = $userobj->details->select(array('SUM("cash")', 'money_nopaid'))->where('paydate', '>=', time())->where('type', 'IN', array(1,2,3))->find()->money_nopaid;

        //判断提现条件
        $result['money_flag'] = false;
        $result['money_out'] = $this->config['money_out'];

        if ($result['money_now'] >= $this->config['money_out']/100) {
            //判断成功购买金额
            $money_buy = $userobj->trades->select(array('SUM("money")', 'money_buy'))->where('status', '=', 'TRADE_BUYER_SIGNED')->find()->money_buy;

            if ($money_buy >= $this->config['money_out_buy']/100) {
                $result['money_flag'] = true;
            } else {
                $result['money_out_msg'] = '您需要成功消费满￥'. number_format($this->config['money_out_buy']/100, 2) .' 元才能提现。';
            }
        } else {
            $result['money_out_msg'] = '收益满￥'. number_format($this->config['money_out']/100, 2) .' 元即可提现。';
        }

        //提现
        //只能提取整数
        $MONEY = floor($result['money_now']);
        $md5 = md5($this->openid.$this->config['appsecret'].$_GET['time'].$_GET['rand']);
        // echo "cks:$cksum<br />md5:$md5";
        if ( ($cksum == $md5) && (time() - $_GET['time'] < 600) ) $cksum_flag = true;

        if ($out == 1 && $cksum_flag == true && ($MONEY >= $this->config['money_out']/100) ) {
            if (!$this->config['partnerid'] || !$this->config['partnerkey']) die('ERRROR: Partnerid 和 Partnerkey 未配置，不能自动提现，请联系管理员！');

            $this->we = $we = new Wechat($this->config);
            $result_m = $this->sendMoney($userobj, $MONEY*100);

            if ($result_m['result_code'] == 'SUCCESS') {
                $userobj->details->cashOut($userobj, 4, $MONEY);

                $cksum = md5($userobj->openid.$this->config['appsecret'].date('Y-m'));
                $url = $this->baseurl.'index/'. $this->bid .'?url=score/3&cksum='. $cksum .'&openid='. base64_encode($userobj->openid);

                //发消息通知
                //$msg = "申请提现￥{$MONEY} 元成功！请到微信钱包中查收。";
                if ($this->config['msg_money_tpl']) {
                    $this->sendMoneyMessage($userobj->openid, '提现成功', -$MONEY, $userobj->score, $url);
                } else {
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $userobj->openid;
                    $msg['text']['content'] = $msg;
                    $we->sendCustomMessage($msg);
                }

                $result['ok']++;
                $result['alert'] = '提现成功!';
                return $this->action_msg("提现成功，请到微信钱包中查收。", 'suc');

            } else {
                // print_r($result);exit;
                Kohana::$log->add("weixin_dka:$bid:money", print_r($result, true));
                $result['alert'] = '提现失败：'.$result_m['return_msg'];
            }
        }

        $this->template->title = '结算中心';
        $this->template->content = View::factory($view)->bind('result', $result);
    }

    //积分排行榜

    public function action_ddtop() {
        $this->template = 'weixin/dka/tpl/fftpl';
        self::before();
        $view = 'weixin/dka/ddtop';

        $this->template->title = '业绩排行';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('result', $result);

        $user = ORM::factory('dka_qrcode', $this->uid);
        $top = $this->config['rank_dka'] ? $this->config['rank_dka'] : 10;

        //$result['rank'] = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('id2', '>', 0)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('paid', '>=', $user->paid)->count_all();
        $rank1 = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('id2', '>', 0)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('paid', '>', $user->paid)->count_all();
        $rank2 = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('id2', '<', $user->id2)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('paid', '=', $user->paid)->count_all();
        $result['rank'] = $rank1+$rank2+1;
        $usersobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('paid', 'DESC')->order_by('id2', 'asc')->limit($top)->find_all();
        foreach ($usersobj as $userobj) {
            $users[] = $userobj->as_array();
        }
    }
    public function action_ddscore($type=0) {
        $this->template = 'weixin/dka/tpl/fftpl';
        self::before();
        $view = "weixin/dka/ddscores";
        $userobj = ORM::factory('dka_qrcode', $this->uid);

        $title = array('收支明细', '待结算', '已结算', '提现记录');

        $this->template->title = $title[$type];
        $this->template->content = View::factory($view)->bind('scores', $details);

        $details = $userobj->details;

        if ($type == 1) $details = $details->where('type', 'IN', array(1,2,3))->where('paydate', '>', time());
        if ($type == 2) $details = $details->where('type', 'IN', array(1,2,3))->where('paydate', '<=', time());
        if ($type == 3) $details = $details->where('type', '=', 4);

        $details = $details->order_by('lastupdate', 'DESC')->limit(500)->find_all();
    }
    public function action_ddorders() {
        $this->template = 'weixin/dka/tpl/fftpl';
        self::before();
        $view = "weixin/dka/ddorders";
        $userobj = ORM::factory('dka_qrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->details->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }
    public function action_ddorder($tid) {
        $this->template = 'weixin/dka/tpl/fftpl';
        self::before();
        $view = "weixin/dka/ddorder";

        $order = ORM::factory('dka_trade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }
    //订单同步脚本
    public function action_cron($bid=1, $time='') {
        //if (!Kohana::$is_cli && IN_PRODUCTION) die('Run me at cmd line only!');
        //set_time_limit(50);
        $rand=rand(1,10);
        sleep($rand);
        ini_set('max_execution_time', 30);
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $this->access_token = ORM::factory('dka_login')->where('id', '=', $bid)->find()->access_token;
        $this->config = $config = ORM::factory('dka_cfg')->getCfg($bid);

        if (!$this->access_token) die("$bid not found.\n");
        $client = new YZTokenClient($this->access_token);
        $this->we = $we = new Wechat($config);

        $start = date("Y-m-d H:i:s", time()-300);
        if ($time == 'all') $start = date('Y-m-d 00:00:00');
        if ($time == 'ALL') $start = date('Y-m-1 00:00:00');

        $method = 'youzan.trades.sold.get';
        $params = array(
            // 'status' => 'WAIT_SELLER_SEND_GOODS', //已付款
            'page_size' => 50,
            'page_no' => 1,
            'use_has_next' => true,
            'start_update' => $start, //同步一个小时内订单
            'fields' => 'tid,title,payment,post_fee,fans_id,type,pay_time,update_time,pic_thumb_path,num,total_fee,status,refund_state,orders',
        );

        print_r($params);//exit;

        $result = $client->post($method, $this->methodVersion, $params, $files);
        $result['response']['has_next'] = 1;

        // print_r($result);exit;

        while ($result['response']['has_next'] == 1) {
            echo "********** PAGE {$params['page_no']} **********\n";
            ob_flush();flush();

            $trades = $result['response']['trades'];
            if (count($trades)) foreach ($trades as $trade) {

                //子订单
                //echo "aaa<br>";
                if ($trade['sub_trades']) foreach ($trade['sub_trades'] as $trade) {
                    $this->tradeImport($trade, $bid, $client, $we, $config);
                } else {
                    $this->tradeImport($trade, $bid, $client, $we, $config);
                }
            }

            $params['page_no']++;
            $result = $client->post($method, $this->methodVersion, $params, $files);
        }

        exit;
    }
    private function tradeImport($trade, $bid, $client, $we, $config) {
        // print_r($trade);exit;
        $tid = $trade['tid'];

        //只需要处理正常订单
        $okstatus = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_BUYER_SIGNED', 'TRADE_CLOSED', 'TRADE_CLOSED_BY_USER2');
        if (!in_array($trade['status'], $okstatus)) {
            echo "$tid status {$trade['status']} pass..\n";
            return;
        }

        $dka_trade = ORM::factory('dka_trade')->where('tid', '=', $tid)->find();

        //跳过已导入订单
        if ($dka_trade->id) {

            //更新订单状态
            if ($dka_trade->status != $trade['status']) {
                $dka_trade->status = $trade['status'];
                $dka_trade->save();

                echo "$tid status updated.\n";
            }

            //退款订单删返利
            if ($trade['status'] == 'TRADE_CLOSED') ORM::factory('dka_detail')->where('tid', '=', $dka_trade->id)->delete_all();
            if ($trade['status'] == 'TRADE_CLOSED_BY_USER') ORM::factory('dka_detail')->where('tid', '=', $dka_trade->id)->delete_all();
            if ($trade['refund_state'] != 'NO_REFUND') ORM::factory('dka_detail')->where('tid', '=', $dka_trade->id)->delete_all();

            echo "$tid pass.\n";
            return;
        }

        //只处理一口价商品
        if ($trade['type'] != 'FIXED') return;

        //男人袜不参与火种用户的商品
        if ($bid == 2) {
            foreach ($trade['orders'] as $od) {
                if ($od['num_iid'] == 222975865 || $od['num_iid'] == 226597275 || $od['num_iid'] == 215414338) {
                    echo "$tid noMoney pass.\n"; //恰型、太阳镜、套套
                    $trade['payment'] -= $od['payment'];
                }
            }
        }

        //付款金额为 0
        if ($trade['payment'] <= 0) return;

        $userinfo = $this->youzanid2OpenID($trade['fans_info']['fans_id'], $client);
        // print_r($userinfo);

        //只处理有下线的订单
        $dka_qrcode = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $userinfo['weixin_openid'])->find();

        if (!$dka_qrcode->id) {
            echo "$tid no OpenID pass.\n";
            return;
        }

        //只处理用户生成海报时间后付款的订单
        $pay_time = strtotime($trade['pay_time']);

        //是否购买过才能生成海报？
        //是：则判断关注后的订单都有收益
        //否：判断生成海报后的订单才算收益
        $fromtime = $config['haibao_needpay'] ? $dka_qrcode->subscribe : $dka_qrcode->jointime;

        if ($pay_time < $fromtime) {
            echo "$tid Time pass.\n";
            return;
        }

        $trade['qid'] = $dka_qrcode->id;
        $trade['openid'] = $userinfo['weixin_openid'];
        $trade['bid'] = $bid;

        //计算返利金额
        $money  = $trade['money'] = $trade['payment'] - $trade['post_fee'];
        $average=$money/($money+$trade['discount_fee']);//权重
        $money0=$money1=$money2=0;
        foreach ($trade['orders'] as $order) {
            $tempmoney=$order['payment']*$average;
            $goodid=$order['num_iid'];
            $goodidcof=ORM::factory('dka_setgood')->where('goodid','=',$goodid)->find();
            if($goodidcof->id)//用户单独配置了
            {
                $money0=$money0+$tempmoney*$goodidcof->money0/100;
                $money1=$money1+$tempmoney*$goodidcof->money1/100;
                $money2=$money2+$tempmoney*$goodidcof->money2/100;
            }
            else//没有配置就默认的数据
            {
                $money0 =$money0+$tempmoney * $config['money0'] / 100; //自购
                $money1 =$money1+$tempmoney * $config['money1'] / 100; //一级
                $money2 =$money2+$tempmoney * $config['money2'] / 100; //二级
            }

        }
        $money0 = $trade['money0'] = number_format($money0, 2, '.', ''); //自购
        $money1 = $trade['money1'] = number_format($money1, 2, '.', ''); //一级
        $money2 = $trade['money2'] = number_format($money2, 2, '.', ''); //二级
        $dka_trade->values($trade);
        $dka_trade->save();

        //同时更新order表中
        foreach ($trade['orders'] as $order) {
            $title=$order['title'];
            $goodid=$order['num_iid'];
            $num=$order['num'];
            $price=$order['payment'];
            $dka_order1=ORM::factory('dka_order1')->where('bid','=',$bid)->where('tid','=',$tid)->where('goodid','=',$goodid)->find();
            if(!$dka_order1->id)//跳过已导入的order
            {
                $dka_order1->bid=$bid;
                $dka_order1->tid=$tid;
                $dka_order1->goodid=$goodid;
                $dka_order1->title=$title;
                $dka_order1->num=$num;
                $dka_order1->price=$price;
                $dka_order1->save();
            }
        }
        echo "aa<br>";

        //删除重复返利记录
        ORM::factory('dka_detail')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 1)->delete_all();
        ORM::factory('dka_detail')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 2)->delete_all();
        ORM::factory('dka_detail')->where('bid', '=', $bid)->where('qid', '=', $trade['qid'])->where('tid', '=', $trade['tid'])->where('type', '=', 3)->delete_all();

        $msg['msgtype'] = 'text';

        //自购返利
        if ($money0 > 0) {
            echo "$tid money0:$money0 \n";
            $dka_qrcode->details->cashIn($dka_qrcode, 1, $money0, 0, $dka_trade->id);

            //发消息
            $msg['touser'] = $dka_qrcode->openid;
            $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m-d'));
            $url = $this->baseurl.'index/'. $bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);
            // $url = $this->baseurl.'index/'. $this->bid .'?url=score/3&cksum='. $cksum .'&openid='. base64_encode($userobj->openid);
            $msg['text']['content'] = "恭喜您完成一笔订单！\n\n实付金额：$money\n系统返利：$money0\n\n<a href=\"$url\">查看我的收益明细</a>";

            if ($config['msg_score_tpl'])
                $we_result = $this->sendScoreMessage($msg['touser'], '购买返利', $money0, $dka_qrcode->cash, $url);
            else
                $we_result = $we->sendCustomMessage($msg);
        }

        //订单上线返利
        if ($money1 > 0) {
            $fuser = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $dka_qrcode->fopenid)->find();
            if ($fuser->id) {
                echo "$tid money1:$money1 \n";
                $fuser->details->cashIn($fuser, 2, $money1, $dka_qrcode->id, $dka_trade->id);

                //发消息
                $msg['touser'] = $fuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m-d'));
                $url = $this->baseurl.'index/'. $bid .'?url=ddorders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

                $msg['text']['content'] = "您推荐的{$config['title1']}「{$dka_qrcode->nickname}」完成一笔订单！\n\n实付金额：$money\n推广佣金：$money1\n\n<a href=\"$url\">查看我的收益明细</a>";

                if ($config['msg_score_tpl'])
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友购买返利', $money1, $fuser->cash, $url);
                else
                    $we_result = $we->sendCustomMessage($msg);
            }
        }

        //订单上上线返利
        if ($money2 > 0 && $fuser->fopenid) {
            $ffuser = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $fuser->fopenid)->find();
            if ($ffuser->id) {
                echo "$tid money2:$money2 \n";
                $ffuser->details->cashIn($ffuser, 3, $money2, $fuser->id, $dka_trade->id);

                //发消息
                $msg['touser'] = $ffuser->openid;
                $cksum = md5($msg['touser'].$config['appsecret'].date('Y-m-d'));
                $url = $this->baseurl.'index/'. $bid .'?url=ddorders&cksum='. $cksum .'&openid='. base64_encode($msg['touser']);

                $msg['text']['content'] = "您推荐的{$config['title2']}「{$fuser->nickname}」完成一笔订单！\n\n实付金额：$money\n推广佣金：$money2\n\n<a href=\"$url\">查看我的收益明细</a>";

                if ($config['msg_score_tpl'])
                    $we_result = $this->sendScoreMessage($msg['touser'], '好友的好友购买返利', $money2, $ffuser->cash, $url);
                else
                    $we_result = $we->sendCustomMessage($msg);
            }
        }

        //TODO:更多级别返利

        echo "$tid done.\n";
        flush();ob_flush();
    }

    private function youzanid2OpenID($fansid, $client) {
        $method = 'youzan.users.weixin.follower.get';
        $params = array('fans_id' => $fansid,);

        $result = $client->post($method, $this->methodVersion, $params, $files);
        $user = $result['response']['user'];
        return $user;
    }

    //收益模板消息：openid、类型、收益、总金额、网址
    private function sendScoreMessage($openid, $title, $score, $total, $url, $remark='干的漂亮，请继续加油哦！') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_score_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '您获得了一笔收益！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $title;

        $tplmsg['data']['keyword2']['value'] = '￥'.number_format($score, 2);
        $tplmsg['data']['keyword2']['color'] = '#FF0000';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');

        $tplmsg['data']['keyword4']['value'] = '￥'.number_format($total, 2);
        $tplmsg['data']['keyword4']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';

        // Kohana::$log->add("weixin_dka:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
    }

    //账户余额通知模板：openid、类型、收益、总金额、网址
    private function sendMoneyMessage($openid, $title, $money, $total, $url) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_money_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = $title;
        $tplmsg['data']['first']['color'] = '#06bf04';

        $tplmsg['data']['keyword1']['value'] = '￥'.number_format($money, 2);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = '￥'.number_format($total, 2);
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['remark']['value'] = '时间：'.date('Y-m-d H:i:s');
        $tplmsg['data']['remark']['color'] = '#666666';

        // Kohana::$log->add("weixin_dka:$bid:tplmsg", print_r($tplmsg, true));
        return $this->we->sendTemplateMessage($tplmsg);
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
        $data["desc"] = $userobj->nickname.'收益提现';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->we->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_dka:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_dka:hongbaoresult', print_r($result, true));
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."dka/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dka/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'dka_file_cert')->find();
        $file_key = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'dka_file_key')->find();

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

        // Kohana::$log->add("weixin_dka:$bid:curl_post_ssl:cert_file", $cert_file);

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
    //以上订单宝
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/dka/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;

        $this->template->title = $this->scorename.'排行榜';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('dka_qrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;

        $rankkey = "dka:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "dka:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('lock', '<>', 4)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }
    //我的积分
    public function action_score() {
        $this->template = 'weixin/dka/tpl/ftpl';
        self::before();
        $view = "weixin/dka/scores";

        $this->template->title = '我的'. $this->scorename;
        //$this->template->content = View::factory($view)->bind('scorename', $scorename);
        $this->template->content = View::factory($view)->bind('scores', $scores)->bind('scorename', $this->scorename);
        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('dka_qrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        //$mem = Cache::instance('memcache');
        $view = "weixin/dka/items";



        $obj = ORM::factory('dka_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
        $day_limit = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('dka_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('dka_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $this->template->title = $this->scorename.'商城';
        $this->template->content = View::factory($view)->bind('items', $items)->bind('dlimit',$dlimit);
        // $key = "dka:items:{$this->bid}";
        // $items = $mem->get($key);
        // if (!$items) {
        //     $obj = ORM::factory('dka_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        //     foreach($obj as $i) $items[] = $i->as_array();
        //     $mem->set($key, $items, 600);
        // }
    }


    //兑换表单
    public function action_neworder($iid,$flag=0) {
        $view = "weixin/dka/neworder";
        $this->template->title = $item->name;
        require Kohana::find_file("vendor","kdt/YZTokenClient");
        $config = $this->config;
        $bid = $this->bid;
        $this->access_token=ORM::factory('dka_login')->where('id', '=', $bid)->find()->access_token;
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $item = ORM::factory('dka_item', $iid);
        $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/dka/items');

        $this->template->content = View::factory($view)->bind('item', $item);
        if(isset($_SESSION['prize'][$userobj->id])){
            $kill=1;
        }
        if($kill==1){

        }else{
            //判断是否满足兑换条件
            //00.到期没？
            if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
            if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

            //0.有库存没？
            if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

            //1.积分够不
            if ($item->score > $userobj->score) die("该奖品需要 {$item->score} 分，您只有 {$userobj->score} 分。");

            //2.是否限购
            if ($item->limit > 0) {
                $limit = ORM::factory('dka_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
                if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
            }

            if ($userobj->lock == 1) die($this->config['text_risk']);

            //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
            if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

                $count2 = ORM::factory('dka_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
                //$count3 = ORM::factory('dka_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
                //用是否生成海报判断下线数量
                $count3 = ORM::factory('dka_qrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
                // echo "2:$count2, 3:$count3";

                if ($userobj->lock == 0 && $count2 >= $this->config['risk_level1'] & $count3 <= $this->config['risk_level2']) {
                    $userobj->lock = 1;
                    $userobj->save();

                    if ($userobj->lock == 1) die('您的账号存在刷分现象，已被锁定。如果您确认是系统误判断，请联系客服解决。');
                }
            }
        }

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url&&$_POST['data']['type']!=5&&$_POST['data']['type']!=6)) {
            $order = ORM::factory('dka_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            if($kill== 1){
                $order->score = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','killScore')->find()->value;
            }
            //虚拟产品
            if ($item->url) {

                $order->status = 1;

                $url = substr($item->url , 0 , 4);
                if ($url == 'http'){
                    $order->url = $item->url;
                } else {
                    $order->url = '/dka/ticket/'.$item->url;
                }

            } else {
                //省份 城市
                $order->city = $_POST['s_province'].' '.$_POST['s_city'].' '.$_POST['s_dist'];
            }

            //成功
            if ($order->save()) {

                if($kill == 1){
                    unset($_SESSION['prize'][$userobj->id]);//页面跳转进来赋值新的session
                }else{
                    //减库存
                    $item->stock--;
                    $item->save();
                    //扣积分
                    $userobj->scores->scoreOut($userobj, 4, $order->score);
                }

                $goal_url = '/dka/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
        //话费流量
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('dka_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            //成功
            if($kill == 1){
                $order->score = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','killScore')->find()->value;
            }
            if ($order->save()) {
                if($kill == 1){
                    unset($_SESSION['prize'][$userobj->id]);//页面跳转进来赋值新的session
                }else{
                    //减库存
                    $item->stock--;
                    $item->save();
                    //扣积分
                    $userobj->scores->scoreOut($userobj, 4, $order->score);
                }

                $goal_url = '/dka/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }
                //微信红包
        if ($_POST['data']['type']==4) {

            $order = ORM::factory('dka_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            if($kill == 1){
                $order->score = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','killScore')->find()->value;
            }
            $order->status = 1;


                //发红包
                $tempname=ORM::factory("dka_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("dka_item")->where("id","=",$iid)->find()->price;
                $tempmoney=$tempmoney*100;
                $hbresult = $this->hongbao($this->config, $this->openid, '', $tempname, $tempmoney);
                if($hbresult['result_code']=='SUCCESS')
                {
                    //成功
                   $order->save();
                   if($kill == 1){
                        unset($_SESSION['prize'][$userobj->id]);//页面跳转进来赋值新的session
                    }else{
                        //减库存
                        $item->stock--;
                        $item->save();
                        //扣积分
                        $userobj->scores->scoreOut($userobj, 4, $order->score);
                    }

                   $goal_url = '/dka/orders';
                   if ($order->url) $goal_url = $order->url;
                    //成功后跳转
                    Request::instance()->redirect($goal_url);

                }else{
                    echo $hbresult['return_msg'];
                    exit();
                }

        }
                //赠品
        if ($_POST['data']['type']==5){
            $order = ORM::factory('dka_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item
            if($kill == 1){
                $order->score = ORM::factory('dka_cfg')->where('bid','=',$this->bid)->where('key','=','killScore')->find()->value;
            }
            //gift
            // $wx['appid'] = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appid')->find()->value;
            // $wx['appsecret'] = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appsecert')->find()->value;
            $oid = ORM::factory('dka_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            if($this->access_token){
                $client = new YZTokenClient($this->access_token);

                // echo '赠品列表:<br><br><br>';
                $method = 'youzan.ump.presents.ongoing.all';
                $params = [

                ];
                $results = $client->post($method, $this->methodVersion, $params, $files);
            }


            for($i=0;$results['response']['presents'][$i];$i++ || $_POST['iskill'] == 1){
                $res = $results['response']['presents'][$i];
                $present_id=$res['present_id'];
                //echo 'present_id:'.$present_id.'<br>';
                if($present_id==$oid){//找到指定赠品
                    //根据openid获取userid
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                       'weixin_openid'=>$this->openid,
                       'fields'=>'user_id',
                    ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);
                    $user_id = $results['response']['user']['user_id'];
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'youzan.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);
                    if($results['response']['is_success']==true){
                        $order->status = 1;
                        $order->save();

                        if($kill == 1){
                            unset($_SESSION['prize'][$userobj->id]);//页面跳转进来赋值新的session
                        }else{
                            //减库存
                            $item->stock--;
                            $item->save();
                            //扣积分
                            $userobj->scores->scoreOut($userobj, 4, $order->score);
                        }
                       //echo "<script>alert('领取成功，请回到公众号主页查看！')</script>";
                        Request::instance()->redirect($results["response"]["receive_address"]);
                       //echo $results["response"]["receive_address"];
                       // exit;
                    }else{
                        echo "您已经兑换过该赠品，每个人只能领一次哦～";
                        exit;
                    }

                }
            }

        }
        if ($_POST['data']['type']==6) {
            $order = ORM::factory('dka_order');
            $order->values($_POST['data']);
            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item
            $oid = ORM::factory('dka_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            $method = 'youzan.ump.coupon.take';
            $params = [
                'coupon_group_id'=>$oid,
                'weixin_openid'=>$userobj->openid,
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            //成功
            if ($results['response']) {
                //减库存
                $order->status = 1;
                $order->save();
                $item->stock--;
                $item->save();
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $we = new Wechat($config);

                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的有赞优惠券优惠码已下发，请在会员中心查看！';
                $we->sendCustomMessage($msg);
                //扣积分
                $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);
                // if($this->config['switch']==1){
                //     $this->rsync($bid,$userobj->openid,$this->access_token,-$order->score);
                // }
                $goal_url = '/dka/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }else{
                echo $results['error_response']['code'].$results['error_response']['msg'];
                exit;
            }
        }
        //自动填写旧地址
        $old_order = ORM::factory('dka_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
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
    public function action_tplcheck($bid){
        ini_set('max_execution_time', 30);
        $url = 'http://jfb.smfyun.com/api/dka';
        $bname=ORM::factory('dka_login')->where('id', '=', $bid)->find();
        // 过期时间存在并且 插件已过期或者搜不到该店铺  就死掉
        if((!$bname||strtotime($bname->expiretime)<=time())&&$bname->expiretime) die('不存在的bid或者已过期');
        echo 'bid:'.$bid;
        $usertime = date('H');//服务器时间

        $btime = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','start')->find()->value;
        $tplid = ORM::factory('dka_cfg')->where('bid','=',$bid)->where('key','=','tplid')->find()->value;
        echo '商家时间：'.$btime.'<br>';
        if($btime==$usertime&&$tplid){//服务器时间和商家设定时间一直并且模板消息存在
            $post_data = array(
              'tplcheck' =>$bname->user
            );
            $res = $this->request_post($url, $post_data);
            Kohana::$log->add('dka:tplcheck:$bid', print_r($res, true));//
            // echo $res;
        }else{
            echo '时间不一致或tplid不存在';
        }
        exit;
    }
    public function action_dka(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $config['copyright'] = $this->config['copyright'];
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $this->template = 'tpl/blank';
        self::before();


        $view = "weixin/dka/dka";
        $bid = $this->bid;
        $openid = $this->openid;
        $Bname = ORM::factory('dka_login')->where('id', '=', $bid)->find()->user;
        $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=',$openid)->find();
        $bidname = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'name')->find()->value;
        //递归
        $qid = $userobj->id;
        $conday = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'con_day')->find()->value;//商家定义签到天数上限，超出部分按照每天积分计算
        $userconday = 1;//用户起始进入签到天数
        $rewardpoint = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'reward')->find()->value; //商家自定义连续签到$countday之后一次性增加的积分
        $addpoint = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'add_point')->find()->value;// 商家自定义连续签到$countday之后每天增加的积分
        $point = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'basic_point')->find()->value;//商家自定义每天签到增加的积分
        $explain = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'explain')->find()->value;
        $today = date('y-m-d',time());
        $rangestart = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'start')->find()->value;//打卡开始时间
        $rangeend = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'end')->find()->value;//打卡关闭时间
        $usertime = date('H',time());
        if($rangestart<=$usertime&&$usertime<$rangeend){//打卡时间范围内
            $gaptime = 0;
        }else if($rangestart>$usertime){//当天打卡还没开始
            $mtime = date('i',time());
            if($mtime>30){
                $usertime = $usertime+1;
            }
            $gaptime = $rangestart-$usertime;
            if($gaptime==0){
                $gaptime =null;
                $gaptime = '即将';
            }else{
                $gaptime = $gaptime.'小时';
            }
            $other = 0;
        }else if($usertime>$rangestart){//当天打卡已结束
            $gaptime = $rangestart+24-$usertime;
            $other = 1;
        }
        $spacetime = $rangestart+24-$usertime;//距离第二天打卡时间
        function countday($userconday,$bid,$qid){
            $frontday = date("Y-m-d",strtotime('-'.$userconday.'day'));
            $continue = ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$frontday)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date;
            if($continue){
                $userconday++;
                return countday($userconday,$bid,$qid);
            }else{
                return $userconday;
            }
        }
        $countday = countday(1,$bid,$qid)-1;
        // echo $countday;
        // exit;
        if (isset($_POST['join'])){
            $jion_time = time();
            DB::update(ORM::factory('dka_qrcode')->table_name())
            ->set(array('dka_join' => $jion_time))
            ->where('bid', '=', $bid)
            ->where('id', '=', $qid)
            ->execute();
        }
        if (isset($_POST['exit'])){
            $jion_time = time();
            DB::update(ORM::factory('dka_qrcode')->table_name())
            ->set(array('dka_join' => 0))
            ->where('bid', '=', $bid)
            ->where('id', '=', $qid)
            ->execute();
            DB::update(ORM::factory('dka_score')->table_name())
            ->set(array('date' => 0))
            ->where('bid', '=', $bid)
            ->where('qid', '=', $qid)
            ->where('date', '!=', $today)
            ->execute();
        }
        if (isset($_GET['dka'])){
            if(ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$today)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date){
                $content = '对不起你今天已经签到过了哟';
            }else{
                $nstart = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'nstart')->find()->value;//正常分开始时间
                $nend = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'nend')->find()->value;//正常分结束时间
                $countday = countday($userconday,$bid,$qid);//用户连续多少天签到
                if($usertime<$nend&&$usertime>=$nstart){

                }else{
                    $point = $point/2;
                    $addpoint = $addpoint/2;
                }
                if($countday<$conday){
                    ORM::factory('dka_score')->scoreIn($userobj, 5, $point);//5表示常规签到
                    $content = '今日签到增加积分'.$point.'分,连续签到'.$conday.'天可获得'.$rewardpoint.'分奖励哟';
                }else if($countday>$conday){
                    $add = $addpoint;
                    ORM::factory('dka_score')->scoreIn($userobj, 6, $add);//6表示超过的连续签到
                    $content = '连续签到'.$countday.'天增加积分'.$add.'分';
                }else if($countday=$conday){
                    $add = $rewardpoint+$point;
                    ORM::factory('dka_score')->scoreIn($userobj, 7, $add);//7表示恰好当天完成的连续签到
                    $content = '完成连续签到'.$conday.'天,一次性奖励'.$add.'积分,继续连续签到,每天可获得'.$addpoint.'分奖励哟';
                }
                $suserobj1 = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=',$userobj->fopenid)->find();//当前用户上级 王旭文
                if($suserobj1->openid){
                    $goal = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'goal')->find()->value;
                    ORM::factory('dka_score')->scoreIn($suserobj1, 2, $goal);//2表示下线打卡奖励
                    $msg['touser'] = $suserobj1->openid;//给当前上级发消息  王旭文
                    $msg['msgtype'] = 'text';
                    $msg['text']['content'] = '您的小伙伴'.$userobj->nickname.'今天'.date('H:i',time()).'成功打卡,您获得了'.$goal.'积分的奖励，继续加油喔~';
                    $we->sendCustomMessage($msg);
                    $suserobj2 = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=',$suserobj1->fopenid)->find();//上上级用户 李静
                    if($suserobj2->openid){
                        $goal2 = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'goal2')->find()->value;
                        ORM::factory('dka_score')->scoreIn($suserobj2, 3, $goal2);//3表示下线打卡奖励
                        $msg['touser'] = $suserobj2->openid;//给上上级用户发消息 李静
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = '您的小伙伴'.$userobj->nickname.'今天'.date('H:i',time()).'成功打卡,您获得了'.$goal2.'积分的奖励，继续加油喔~';
                        $we->sendCustomMessage($msg);
                    }
                }
            }
            $con=$content;
            $score=ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->score;
            $arr=array('con'=>$con,'score'=>$score);
            echo json_encode($arr);
            //echo $content;
            exit();
        }
        if(isset($_GET['egg'])){
            if(ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date', '=', $today)->where('type', '=', 8)->find()->date){
                $content = '对不起你今天已经砸过了哟';
            }else{
                $pointstart = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'eggstart')->find()->value;
                $pointend = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'eggsend')->find()->value;
                $pointchance = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'eggchance')->find()->value;
                $rand = rand(1,100);
                if($rand<=$pointchance){
                    $point = rand($pointstart,$pointend);
                    ORM::factory('dka_score')->scoreIn($userobj, 8, $point);//8表示砸金蛋奖励
                    $content = '恭喜你获得'.$point.'积分';
                }else{
                    $content = '很遗憾您没有中奖，请明天再来哟';
                }
            }
            $con=$content;
            $score=ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $this->openid)->find()->score;
            $arr=array('con'=>$con,'score'=>$score);
            echo json_encode($arr);
            //echo $content;
            exit();
        }
        if(isset($_GET['shake'])){
            if(ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date', '=', $today)->where('type', '=', 9)->find()->date){
                $content = '对不起你今天已经摇过了哟';
            }else{
                $pointstart = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'shakestart')->find()->value;
                $pointend = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'shakeend')->find()->value;
                $pointchance = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'shakechance')->find()->value;
                $rand = rand(1,100);
                if($rand<=$pointchance){
                    $point = rand($pointstart,$pointend);
                    ORM::factory('dka_score')->scoreIn($userobj, 9, $point);//9表示摇一摇奖励
                    $content = '恭喜你获得'.$point.'积分';
                }else{
                    $content = '很遗憾您没有中奖，请明天再来哟';
                }
            }
            $con=$content;
            $score=ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $this->openid)->find()->score;
            $arr=array('con'=>$con,'score'=>$score);
            echo json_encode($arr);
            //echo $content;
            exit();
        }
        if(ORM::factory('dka_score')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$today)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date){
                $flag = 1;
            }else{
                $flag = 0;
            }//判断今日签到

        $startday = date("Y/m/d",strtotime('-'.$countday.'day'));
        $result['dka'] = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'dka')->find()->id;
        $result['point'] = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->score;
        $headimg = ORM::factory('dka_qrcode')->where('dka_join','!=',0)->where('bid', '=', $bid)->order_by('id', 'DESC')->limit(8)->find_all();
        $bname = ORM::factory('dka_login')->where('id', '=', $bid)->find()->name;
        $num = ORM::factory('dka_qrcode')->where('bid', '=', $bid)->where('dka_join', '!=', 0)->count_all();
        $joinstatus = ORM::factory('dka_qrcode')->where('bid','=',$bid)->where('id', '=', $qid)->find()->dka_join;
        if(!$joinstatus==0){
            $joinstatus=1;
        }else{
            $joinstatus=0;
        }
        $egg = ORM::factory('dka_qrcode')->where('bid','=',$bid)->where('id', '=', $qid)->find();
        if($egg->lastlogin==date('Y-m-d')){//非第一次
            $eggflag=2;
        }else{
            $egg->lastlogin=date('Y-m-d');//第一次登陆
            $egg->save();
            $eggflag=1;
        }
        $score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
        $result['cash'] = $userobj->cash = $userobj->details->select(array('SUM("cash")', 'total_score'))->find()->total_score;
        $config['eggst'] = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'eggst')->find()->value;
        $config['shakest'] = ORM::factory('dka_cfg')->where('bid', '=', $bid)->where('key', '=', 'shakest')->find()->value;
        $this->template->content = View::factory($view)
                ->bind('joinstatus',$joinstatus)//加入状态
                ->bind('num',$num)//参与人数
                ->bind('bname',$bname)//商家name
                ->bind('startday',$startday)//商家定义开始时间
                ->bind('headimg', $headimg)//加入打卡用户头像
                ->bind('result', $result)//积分和banner图返利
                ->bind('countday',$countday)//用户连续打卡天数
                ->bind('gaptime',$gaptime)//当天是否在打卡时间范围内
                ->bind('flag',$flag)//今日是否打卡
                ->bind('eggflag',$eggflag)
                ->bind('bidname',$bidname)//商家自定义公众号名字
                ->bind('conday',$conday)//商家定义连续打卡天数
                ->bind('explain',$explain)//商家定义打卡说明
                ->bind('openid', $openid)
                ->bind('bid',$bid)
                ->bind('config',$config)
                ->bind('Bname',$Bname)
                ->bind('spacetime',$spacetime)
                ->bind('other',$other)
                ->bind('rangestart',$rangestart)
                ->bind('score',$score)
                ->bind('nickname', $nickname);
    }

    // 2015.12.28 增加检查地理位置
    public function action_check_location(){
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
          $ch = curl_init(); // 初始化一个 cURL 对象
          curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
          curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
          $res = curl_exec($ch); // 运行cURL，请求网页
          curl_close($ch); // 关闭一个curl会话
          $json_obj = json_decode($res, true);
          //$nation = $json_obj['result']['address_component']['nation'];
          $province = $json_obj['result']['address_component']['province'];
          $city = $json_obj['result']['address_component']['city'];
          $disrict = $json_obj['result']['address_component']['district'];
          //$street = $json_obj['result']['address_component']['street'];
          $content = $province.$city.$disrict;
          echo $content;
          $area = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          $area->area = $content;
          $area->save();
          exit;
        }
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/dka/check_location";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);
        $count = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        // $pro = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'pro')->find()->value;
        // $city = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'city')->find()->value;
        // $dis = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'dis')->find()->value;
        $info = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'info')->find()->value;
        $reply = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'reply')->find()->value;
        $isreply = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'isreply')->find()->value;
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $we->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
    }

    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/dka/ticket";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);

        $jsapi = $we->getJsSign($callback_url);
        $ticket = $we->getJsCardTicket();
        $sign = $we->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory($view)
                ->bind('cardId', $cardId)
                ->bind('jsapi', $jsapi)
                ->bind('ticket', $ticket)
                ->bind('sign', $sign);
    }

    //已兑换表单
    public function action_orders() {
        $view = "weixin/dka/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('dka_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "dka_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;


    }
    //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
      private function hongbao($config, $openid, $we='', $bid=1, $money)
    {
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
        //$data["min_value"] = $money; //最小金额
        //$data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        //$data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name'].""; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写

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

    public function action_draw(){

        $this->template = 'tpl/blank';
        self::before();
        $view = "weixin/dka/draw";
        $bid = $this->bid;
        //查询积分
        $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        $total_score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;

        $res['bid'] = $this->bid;
        $res['openid'] = $this->openid;
        $res['total_score'] = $total_score;
        //获得奖品设置
        $cfg_score = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'killScore')->find();
        $cfg_explain=ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'drawexplain')->find();
        $cfg_drawtime=ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'drawtime')->find();
        $cfg_limittime=ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'limitTime')->find();
        $prizeSet = ORM::factory('dka_prize')->where('bid', '=', $this->bid)->find_all()->as_array();
        $itemset = ORM::factory('dka_item')->where('bid', '=', $this->bid)->find_all()->as_array();
        $userdrawtime = ORM::factory('dka_score')->where('bid', '=', $this->bid)->where('qid', '=', $userobj->id)->where('type', '=', 10)->where('lastupdate', '>', mktime(0,0,0,date('m'),date('d'),date('Y')))->count_all();

        $res['killscore'] = $cfg_score->value;       //一次抽奖消耗积分
        $res['exp'] = $cfg_explain->value;           //活动说明
        $res['enddate'] = $cfg_drawtime->value;      //活动截止时间
        $res['limittime'] = $cfg_limittime->value;   //限次
        $res['prizeset'] = $prizeSet;                //奖品设置
        $res['itemset'] = $itemset;
        $res['usertime'] = $userdrawtime;                //当天已抽奖的次数
        $prize_1 = ORM::factory('dka_prize')->where('bid', '=', $this->bid)->where('type', '=', 1)->find()->probability;
        $prize_2 = ORM::factory('dka_prize')->where('bid', '=', $this->bid)->where('type', '=', 2)->find()->probability;
        $prize_3 = ORM::factory('dka_prize')->where('bid', '=', $this->bid)->where('type', '=', 3)->find()->probability;
        $prize_4 = ORM::factory('dka_prize')->where('bid', '=', $this->bid)->where('type', '=', 4)->find()->probability;

        $prize_5 = 100 - $prize_1 - $prize_2 - $prize_3 - $prize_4;

        //奖项数据
        $prize_arr=array(
            'yidengjiang'=>array('angle'=>array('40-50'),'prize'=>'一等奖','v'=>$prize_1 * 100),

            'erdengjiang'=>array('angle'=>array('130-140'),'prize'=>'二等奖','v'=>$prize_2 * 100),

            'sandengjiang'=>array('angle'=>array('220-230'),'prize'=>'三等奖','v'=>$prize_3 * 100),

            'sidengjiang'=>array('angle'=>array('310-315'),'prize'=>'四等奖','v'=>$prize_4 * 100),

            'weizhongjiang'=>array('angle'=>array('0-10', '80-110','170-190','260-280'),'prize'=>'未中奖','v'=>$prize_5 * 100),
        );
        //根据奖项数据获得具体奖项
        function getPrize($prize_arr) {
            $proSum = '';
            foreach($prize_arr as $v){
               $proSum+=$v['v'];
            }

            foreach($prize_arr as $k=>$v){
                $randNum=mt_rand(0,$proSum);//随机数
                if($randNum<=$v['v']) {
                    return $v;
                }else{
                    $proSum-=$v['v'];
                }
            }
        }

        //获得旋转信息
        function getRotate($prize_arr) {
            //$data=array();
            $prize=getPrize($prize_arr);
            $angle=$prize['angle'];
            shuffle($angle);//打乱

            $angle=$angle[0];

            $angle_arr=explode('-',$angle);

            $min=$angle_arr[0];
            $max=$angle_arr[1];
            $data=mt_rand($min,$max);
            //$data['angle']=$angle;
            return $data;
        }
        function getiid($angle,$bid){
            if($angle >= 40 && $angle <= 50){
                    //一等奖
                $result['ptype']=1;
                $result['iid'] = ORM::factory('dka_prize')->where('bid', '=', $bid)->where('type', '=', 1)->find()->iid;
            }
            else if($angle>= 130 && $angle <= 140){
                //二等奖
                $result['ptype']=2;
                $result['iid'] = ORM::factory('dka_prize')->where('bid', '=', $bid)->where('type', '=', 2)->find()->iid;
            }
            else if($angle >= 220 && $angle <= 230){
                //三等奖
                $result['ptype']=3;
                $result['iid'] = ORM::factory('dka_prize')->where('bid', '=', $bid)->where('type', '=', 3)->find()->iid;
            }
            else if($angle >= 310 && $angle <= 315){
                //四等奖
                $result['ptype']=4;
                $result['iid'] = ORM::factory('dka_prize')->where('bid', '=', $bid)->where('type', '=', 4)->find()->iid;
            }else{
                //未中奖
                $result['ptype']=0;
                $result['iid'] = '0';
            }
            return $result;
        }
        if($_POST['sign']==1){
            $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $total_score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;

            //获得奖品设置
            $cfg_score = ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'killScore')->find();


            $cfg_limittime=ORM::factory('dka_cfg')->where('bid', '=', $this->bid)->where('key', '=', 'limitTime')->find();

            $itemset = ORM::factory('dka_item')->where('bid', '=', $this->bid)->find_all()->as_array();
            $userdrawtime = ORM::factory('dka_score')->where('bid', '=', $this->bid)->where('qid', '=', $userobj->id)->where('type', '=', 10)->where('lastupdate', '>', mktime(0,0,0,date('m'),date('d'),date('Y')))->count_all();

            $res['killscore'] = $cfg_score->value;       //一次抽奖消耗积分


            $res['limittime'] = $cfg_limittime->value;   //限次
            $res['total_score'] = $total_score;       //总分
            $res['itemset'] = $itemset;
            $res['usertime'] = $userdrawtime;                //当天已抽奖的次数
            $res['qid']=$userobj->id;

            //重新得到一次旋转角度
            $data = getRotate($prize_arr);
            $res['angle'] = $data;
            //几等奖
            $res['getprize'] = getiid($data,$bid);
            if($res['getprize']['iid']!='0'){
                $_SESSION['prize'][$qid]=$res['getprize']['iid'];
            }
            echo json_encode($res);
            exit;
        }
        if($_POST['flag']==1){
            //重新查询积分
            $userobj = ORM::factory('dka_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            //重新查询当天已抽奖次数
            $userdrawtime = ORM::factory('dka_score')->where('bid', '=', $this->bid)->where('qid', '=', $userobj->id)->where('type', '=', 10)->where('lastupdate', '>', mktime(0,0,0,date('m'),date('d'),date('Y')))->count_all();

            //判断还能不能抽奖
            if($res['limittime']-$userdrawtime>0){
                $res[0]=true;
                if($userobj->score-$res['killscore']>0){// 判断积分够不够
                    $res[1]=true;
                    //更新积分详情表，将积分消耗记录插入
                    $score = ORM::factory('dka_score');
                    $score->bid = $this->bid;
                    $score->qid = $userobj->id;
                    $score->type = 10;
                    $score->score =0-$cfg_score->value;
                    $score->save();
                    //echo "true";
                    //重新得到一次旋转角度
                    $data = getRotate($prize_arr);
                    $res['angle'] = $data;
                    $res['getprize'] = getiid($data,$bid);//几等奖
                    if($res['getprize']['iid']!='0'){
                        $_SESSION['prize'][$userobj->id]=$res['getprize']['iid'];
                    }
                }else{
                    $res[1]=false;
                }
            }else{
                $res[0]=false;
            }
            echo json_encode($res);
            exit;
        }


        $this->template->content=View::factory($view)->bind('res',$res)->bind('deg_angle', $data);

        //得到概率




        $data = getRotate($prize_arr);

    }
}
