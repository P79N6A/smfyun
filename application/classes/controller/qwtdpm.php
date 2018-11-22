<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtdpm extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    var $we;
    var $client;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "yyx";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'delete') return;
        if (Request::instance()->action == 'login') return;
        if (Request::instance()->action == 'order') return;
        if (Request::instance()->action == 'test2') return;
        if (Request::instance()->action == 'test3') return;
        if (Request::instance()->action == 'test4') return;
        if (Request::instance()->action == 'test5') return;
        if (Request::instance()->action == 'test8') return;
        if (Request::instance()->action == 'smfyun') return;
        if (!$_SESSION['qwta']['bid']) die('页面已过期。请重新点击相应菜单');
        set_time_limit(0);
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',16)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            if(Request::instance()->action == 'home'){
                $hasover = 1;
                @View::bind_global('hasover', $hasover);
            }
        }
    }
    public function sub_enter($a){
       $i = 0;
       $b = 'start';
       while ($b!='') {
        $b = mb_substr($a, $i,10,'utf-8');
        $i = $i+10;
        if($i==10){
          $c = $b;
        }else{
          $c = $c."$".$b;
        }
       }
       return $c;
    }
    public function sub_enter2($a){
       $i = 0;
       $b = 'start';
       while ($b!='') {
        $b = mb_substr($a, $i,10,'utf-8');
        $i = $i+10;
        if($i==10){
          $c = $b;
        }else{
          $c = $c."$".$b;
        }
       }
       return $c;
    }
    public function action_delete(){
        $_SESSION['dpm']['bid'] = '';
        if(!$_SESSION['dpm']['bid'])
        echo '清除成功'.$_SESSION['dpm']['bid'];
    }

    public function action_login($bname) {
        $this->template = 'weixin/yyx/tpl/login';

        $this->before();
        $this->template->username = ORM::factory('qwt_yyxlogin')->where('user','=',$bname)->find()->name;

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('qwt_yyxlogin')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                    $_SESSION['dpm']['bid'] = $biz->id;
                    $_SESSION['dpm']['user'] = $_POST['username'];
                    $_SESSION['dpm']['admin'] = $biz->admin; //超管
                    var_dump($_SESSION['dpm']);
                    // exit;
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['dpm']['bid']) {
            header('location:/dpm/map');
            exit;
        }
    }
    public function action_test2(){
        // $bid=2;
        // $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid  and unix_timestamp(`pay_time`)>=1479657600 and unix_timestamp(`pay_time`)<=1479744000  ")->execute()->as_array();
        // echo "<pre>";
        // var_dump($commision);
        // echo "</pre>";
        // exit;
        $aa=array();
        if($aa['wx']===0){
            echo "aaaa";
        }else{
            echo "bbbb";
        }
        // set_time_limit(0);
        // $result =ORM::factory('qwt_yyxorder')->where('bid','=',1)->find_all();
        // foreach ($result as $a) {
        //     $id=$a->id;
        //     $time=strtotime($a->pay_time);
        //     $order=ORM::factory('qwt_yyxorder')->where('id','=',$id)->find();
        //     $order->timestamp=$time;
        //     $order->save();
        // }
    }
    public function action_test3(){
        // $bid=2;
        // $daytype='%Y-%m-%d';
        // $date=date('Y-m',time());
        // $time=strtotime($date);
        // $day=date('d',time());
        // for ($i=0; $i <$day ; $i++) {
        //     $time1="+".$i."days";
        //    $count_time[$i]= strtotime($time1,$time);
        // }
        // echo "<pre>";
        // var_dump($count_time);
        // echo "</pre>";
        // for($i=0;$count_time[$i];$i++){
        //     $time=date('Y-m-d',$count_time[$i]);
        //     $newadd[$i]['time']=$time;
        //     echo $time.'<br>';
        //     $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`update_time`,'$daytype')='$time' ")->execute()->as_array();
        //     $newadd[$i]['commision']=$commision[0]['paymoney'];
        // }
        // echo "<pre>";
        // var_dump($newadd);
        // echo "</pre>";
        // exit;
        set_time_limit(0);
        $result =ORM::factory('qwt_yyxorder')->where('bid','=',1)->find_all();
        foreach ($result as $a) {
            $id=$a->id;
            $time=$a->timestamp;
            $order=ORM::factory('qwt_yyxorder')->where('id','=',$id)->find();
            $order->pay_time=$time;
            $order->save();
        }
    }
    public function action_test8(){
        $bid=2;
        $daytype='%Y-%m';
        $date=date('Y',time());
        echo $date."<br>";
        $time  = mktime(0, 0, 0, 1, 1, $date);
        $times=strtotime($date);
        echo $time."<br>";
        echo $times."<br>";
        $day=date('m',time());
        echo $day."<br>";
        for ($i=0; $i <$day ; $i++) {
            $time1="+".$i."months";
           $count_time[$i]= strtotime($time1,$time);
        }
        echo "<pre>";
        var_dump($count_time);
        echo "</pre>";
        for($i=0;$count_time[$i];$i++){
            $time=date('Y-m',$count_time[$i]);
            $newadd[$i]['time']=$time;
            echo $time.'<br>';
            $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
            $newadd[$i]['commision']=$commision[0]['paymoney'];
        }
        echo "<pre>";
        var_dump($newadd);
        echo "</pre>";
        exit;
    }
    //热销商品按金额排行
    public function action_test4(){
        //$aaa = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->select(array('SUM("payment")', 'done'))->order_by('done','desc')->find_all();//累计交易额
        $result['hot_items'] = DB::query(Database::SELECT,"select title,zongfen from (select title,iid,sum(payment) as zongfen from qwt_yyxorders where `bid` = 2 group by iid) a order by a.zongfen desc")->execute()->as_array();
        echo "<pre>";
        var_dump($result);
        echo "<pre>";

    }
    //热销城市排行
    public function action_test5(){
        $result['hot_items'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = 2 group by receiver_city) a order by a.zongfen desc")->execute()->as_array();
        echo "<pre>";
        var_dump($result);
        echo "<pre>";
    }
    public function action_map(){//删除商户 token
        $bid = $this->bid;
        Kohana::$log->add("bid", print_r($bid, true));
        //订单来源
        $result['yz_orders'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('tid','!=','order')->count_all();
        $result['sd_orders'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('tid','=','order')->count_all();
        //销售目标
        $result['sx_goal'] = ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal')->find()->value;
        $result['done_goal'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('pay_time','>=',mktime(0,0,0,date('m'),1,date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal1')->find()->value;//本月
        $result['undone_goal'] = $result['sx_goal']-$result['done_goal'];
        // 今日昨日交易
        $result['today_done'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('pay_time','>=',mktime(0,0,0,date('m'),date('d'),date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal2')->find()->value;//今日

        $result['yestoday_done'] = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('pay_time','>=',mktime(0,0,0,date('m'),date('d')-1,date('Y')))->where('pay_time','<=',mktime(0,0,0,date('m'),date('d'),date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal3')->find()->value;//昨日

        $result['all_done'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal4')->find()->value;//累计交易额
        //热销商品，按销量排行
        $result['hot_items'] = DB::query(Database::SELECT,"SELECT lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED'  group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
        foreach ($result['hot_items'] as $k => $hot_items) {
            $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldctn'];
            $result['hot_items'][$k]['title']=$this->sub_enter($result['hot_items'][$k]['title']);
            $result['hot_items'][$k]['oldCTN']=$oldcustomer;
            $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

        }
        //热销商品，按销售额排行
        $result['hot_items_num'] = DB::query(Database::SELECT,"SELECT lv,title,zongfen from (SELECT qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
        foreach ($result['hot_items_num'] as $k => $hot_items_num) {
            $result['hot_items_num'][$k]['title']=$this->sub_enter2($result['hot_items_num'][$k]['title']);
        }
        //热销城市排行
        $result['hot_area'] = DB::query(Database::SELECT,"SELECT receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and status !='TRADE_CLOSED' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
        //新老会员
        $result['old'] = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('traded_num','>',1)->count_all();
        $result['new'] = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('traded_num','=',1)->count_all();
        Kohana::$log->add("result", print_r($result, true));
        //成交量
        $daytype='%Y-%m-%d-%H';
        $date=date('Y-m-d',time());
        $time=strtotime($date);
        $hour=date('H',time())+1;
        $count_time = array();
        for ($i=0; $i <$hour ; $i++) {
            $time1="+".$i."hours";
           $count_time[$i]= strtotime($time1,$time);
        }
        $result['line'] = array();
        $result['line'][0]['commision']= 0 ;
        $result['line'][0]['time'] = '00时';
        for($i=0;$count_time[$i];$i++){
            $time=date('Y-m-d-H',$count_time[$i]);
            $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
            $result['line'][$i+1]['commision']=$commision[0]['paymoney'];
            $result['line'][$i+1]['time'] = (date('H',$count_time[$i])+1).'时';
        }
        // echo '<pre>';
        // var_dump($result);
        // echo '</pre>';
        //exit;
        //地理位置
        $result['orders'] = DB::query(Database::SELECT,"SELECT * FROM (SELECT * FROM qwt_yyxorders where receiver_city!='' and bid=$bid and status!='TRADE_CLOSED' ORDER BY pay_time DESC , has_show ASC) BIAOMING GROUP BY receiver_city ORDER BY pay_time desc LIMIT 40")->execute()->as_array();
        for ($i=0; $result['orders'][$i]; $i++) {
            $t_location = ORM::factory('qwt_yyxlocation')->where('city','=',$result['orders'][$i]['receiver_city'])->find();
            if($t_location->lng&&$t_location->lat){
                $lng = $t_location->lng;
                $lat = $t_location->lat;
            }else{
                $location = urlencode($result['orders'][$i]['receiver_city']);
                $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$location.'&output=json&ak=xS2SZp5OY5QNzy5gVaNbGYFaX4KkRtK9';
                $ch = curl_init(); // 初始化一个 cURL 对象
                curl_setopt($ch, CURLOPT_URL, $url); // 设置你需要抓取的URL
                curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $res = curl_exec($ch); // 运行cURL，请求网页
                curl_close($ch); // 关闭一个curl会话
                $json_obj = json_decode($res, true);
                $t_location->city = $result['orders'][$i]['receiver_city'];
                $t_location->lng = $lng = number_format($json_obj['result']['location']['lng'],3);
                $t_location->lat = $lat = number_format($json_obj['result']['location']['lat'],3);
                $t_location->save();
            }
            $this_order = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('id','=',$result['orders'][$i]['id'])->find();
            if($this_order->has_show!=1){
                $this_order->has_show = $this_order->has_show+1;
                $this_order->save();
            }
            $arr_location[$result['orders'][$i]['receiver_city']] = [$lng,$lat];
            $arr_order[$result['orders'][$i]['receiver_city']] = ['时间：'.date('Y-m-d H:i',$result['orders'][$i]['pay_time']).'<br>地点：'.$result['orders'][$i]['receiver_state'].$result['orders'][$i]['receiver_city'].'<br>订单金额:'.$result['orders'][$i]['payment'].'元'];
            $arr_place[$i] = $result['orders'][$i]['receiver_city'];
            $order_details[$i][0] = date('Y-m-d H:i',$result['orders'][$i]['pay_time']);
            $order_details[$i][1] = $result['orders'][$i]['receiver_state'].$result['orders'][$i]['receiver_city'];
            $order_details[$i][2] = $result['orders'][$i]['payment'];
        }
        //订单详情
        if($_GET['t']==1){
            $geoCoordMap = array('地理位置'=>$arr_location);
            $ordervalue = array('订单详情'=>$arr_order);
            $place = array('位置预设'=>$arr_place);
            $arr = array_merge($geoCoordMap,$ordervalue,$place);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==2){
            $source = array('订单来源'=>
                array(
                '有赞平台' => $result['yz_orders'],
                '自己平台' => $result['sd_orders']
            ));
            $goal = array('本月目标'=>
                array(
                '已完成' => $result['done_goal'],
                '总目标' => $result['sx_goal']
            ));
            $vip = array('成交会员'=>
                array(
                '新会员' => $result['old'],
                '老会员' => $result['new']
            ));
            $arr = array_merge($source,$goal,$vip);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==3){
            //Kohana::$log->add("result1", print_r($result, true));
            $today = array('今日成交额'=>$result['today_done']);
            $yel = array('昨日成交额'=>$result['yestoday_done']);
            $all = array('累计成交额'=>$result['all_done']);
            $arr = array_merge($today,$yel,$all);
            Kohana::$log->add("arr", print_r($arr, true));
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==4){
            $orders = array('交易订单'=>$order_details);
            echo json_encode($orders);
            exit;
        }
        // echo '----------------------------<br>';
        // echo '<pre>';
        // var_dump($result);
        // echo '</pre>';
        if($_GET['t']==5){
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $this->sub_enter($result['hot_items'][$i]['title']);
                   // mb_substr($result['hot_items'][$i]['title'],0,5,'utf-8');
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $items = array('热销商品'=>$arr);
            // echo '----------------------------<br>';
            // echo '<pre>';
            // var_dump($items);
            // echo '</pre>';
            echo json_encode($items);
            exit;
        }
        if($_GET['t']==6){
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $this->sub_enter2($result['hot_items_num'][$i]['title']);
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $items = array('热销商品金额'=>$arr);
            echo json_encode($items);
            exit;
        }
        if($_GET['t']==7){
            echo json_encode($result['hot_area']);
            exit;
        }
                //成交量line
        if($_GET['t']=='d2'){
            $daytype='%Y-%m-%d';
            $date=date('Y-m-d',time());
            //热销商品，按销量排行
            $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date'  group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
            foreach ($result['hot_items'] as $k => $hot_items) {
                $oldcustomer=DB::query(Database::SELECT,"SELECT a.oldnum as oldctn from(SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and FROM_UNIXTIME(pay_time,'$daytype')='$date' and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldnum'];
                $result['hot_items'][$k]['oldCTN']=$oldcustomer;
                $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

            }
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items'][$i]['title'];
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $result['hot_item'] = array('热销商品'=>$arr);
            //热销商品，按销售额排行
            $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items_num'][$i]['title'];
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $result['hot_num'] = array('热销商品金额'=>$arr);
            //热销城市排行
            $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_area'][$i]['receiver_city']){
                    $result['hot_area'][$i]['receiver_city'] = mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8');
                }
            }
            echo json_encode($result);
            exit;
        }elseif($_GET['t']=='m2'){
            $daytype='%Y-%m';
            $date=date('Y-m',time());
           //热销商品，按销量排行
            $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
            foreach ($result['hot_items'] as $k => $hot_items) {
                $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and FROM_UNIXTIME(pay_time,'$daytype')='$date' and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldnum'];
                $result['hot_items'][$k]['oldCTN']=$oldcustomer;
                $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

            }
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items'][$i]['title'];
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $result['hot_item'] = array('热销商品'=>$arr);
            //热销商品，按销售额排行
            $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items_num'][$i]['title'];
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $result['hot_num'] = array('热销商品金额'=>$arr);
            //热销城市排行
            $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_area'][$i]['receiver_city']){
                    $result['hot_area'][$i]['receiver_city'] = mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8');
                }
            }
            echo json_encode($result);
            exit;
        }elseif($_GET['t']=='y2'){
            $daytype='%Y';
            $date=date('Y',time());
           //热销商品，按销量排行
            $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
            foreach ($result['hot_items'] as $k => $hot_items) {
                $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and FROM_UNIXTIME(pay_time,'$daytype')='$date' and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldnum'];
                $result['hot_items'][$k]['oldCTN']=$oldcustomer;
                $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

            }
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items'][$i]['title'];
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $result['hot_item'] = array('热销商品'=>$arr);
            //热销商品，按销售额排行
            $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items_num'][$i]['title'];
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $result['hot_num'] = array('热销商品金额'=>$arr);
            //热销城市排行
            $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_area'][$i]['receiver_city']){
                    $result['hot_area'][$i]['receiver_city'] = mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8');
                }
            }
            echo json_encode($result);
            exit;
        }
        //成交量line
        if($_GET['t']=='d'){
            $daytype='%Y-%m-%d-%H';
            $date=date('Y-m-d',time());
            $time=strtotime($date);
            $hour=date('H',time())+1;
            $count_time = array();
            for ($i=0; $i <$hour ; $i++) {
                $time1="+".$i."hours";
               $count_time[$i]= strtotime($time1,$time);
            }
            $result['line'] = array();
            $result['line'][0]['commision']= 0 ;
            $result['line'][0]['time'] = '00时';
            for($i=0;$count_time[$i];$i++){
                $time=date('Y-m-d-H',$count_time[$i]);
                $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
                $result['line'][$i+1]['commision']=$commision[0]['paymoney'];
                $result['line'][$i+1]['time'] = (date('H',$count_time[$i])+1).'时';
            }
            echo json_encode($result['line']);
            exit;
        }elseif($_GET['t']=='m'){
            $daytype='%Y-%m-%d';
            $date=date('Y-m',time());
            $time=strtotime($date);
            $date=date('d',time());
            $count_time = array();
            for ($i=0; $i <$date ; $i++) {
                $time1="+".$i."days";
               $count_time[$i]= strtotime($time1,$time);
            }
            $result['line'] = array();
            for($i=0;$count_time[$i];$i++){
                $time=date('Y-m-d',$count_time[$i]);
                $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
                $result['line'][$i]['commision']=$commision[0]['paymoney'];
                $result['line'][$i]['time'] = date('d',$count_time[$i]).'日';
            }
            echo json_encode($result['line']);
            exit;
        }elseif($_GET['t']=='y'){
            $daytype='%Y-%m';
            $date=date('Y',time());
            $time  = mktime(0, 0, 0, 1, 1, $date);
            //$time=strtotime($date);
            $month=date('m',time());
            $count_time = array();
            for ($i=0; $i <$month ; $i++) {
                $time1="+".$i."months";
               $count_time[$i]= strtotime($time1,$time);
            }
            $result['line'] = array();
            for($i=0;$count_time[$i];$i++){
                $time=date('Y-m',$count_time[$i]);
                $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
                $result['line'][$i]['commision']=$commision[0]['paymoney'];
                $result['line'][$i]['time'] = date('m',$count_time[$i]).'月';
            }
            echo json_encode($result['line']);
            exit;
        }
        $user = ORM::factory('qwt_login')->where('id','=',$bid)->find()->user;
        $view = 'weixin/smfyun/yyx/map';

        $this->template->content = View::factory($view)
                ->bind('result',$result)
                ->bind('arr_location',$arr_location)
                ->bind('arr_order',$arr_order)
                ->bind('order_details',$order_details)
                ->bind('arr_place',$arr_place);
    }
    public function action_smfyun(){//删除商户 token
        $bid = 2;
        Kohana::$log->add("smfyunbid1", print_r($bid, true));
        //订单来源
        $result['yz_orders'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('tid','!=','order')->count_all();
        $result['sd_orders'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('tid','=','order')->count_all();
        //销售目标
        $result['sx_goal'] = ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal')->find()->value;
        $result['done_goal'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('pay_time','>=',mktime(0,0,0,date('m'),1,date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal1')->find()->value;//本月
        $result['undone_goal'] = $result['sx_goal']-$result['done_goal'];
        // 今日昨日交易
        $result['today_done'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->where('pay_time','>=',mktime(0,0,0,date('m'),date('d'),date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal2')->find()->value;//今日
        $result['today_done'] = 0;
        $result['yestoday_done'] = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('pay_time','>=',mktime(0,0,0,date('m'),date('d')-1,date('Y')))->where('pay_time','<=',mktime(0,0,0,date('m'),date('d'),date('Y')))->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal3')->find()->value;//昨日
        $result['yestoday_done'] = 0;
        $result['all_done'] = ORM::factory('qwt_yyxorder')->where('status','!=','TRADE_CLOSED')->where('bid','=',$bid)->select(array('SUM("payment")', 'done'))->find()->done+ORM::factory('qwt_yyxcfg')->where('bid','=',$bid)->where('key','=','goal4')->find()->value;//累计交易额
        Kohana::$log->add("result111", print_r($result, true));
        $result['all_done'] = 0;
        //热销商品，按销量排行
        $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED'  group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
        // echo "<pre>";
        foreach ($result['hot_items'] as $k => $hot_items) {
            $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldctn'];
            //$oldcustomer=DB::query(Database::SELECT,"SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1")->execute()->as_array();
            //var_dump($oldcustomer);
            $result['hot_items'][$k]['title']=$this->sub_enter($result['hot_items'][$k]['title']);
            $result['hot_items'][$k]['oldCTN']=$oldcustomer;
            $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

        }

        // var_dump($result['hot_items']);
        // echo "</pre>";
        // exit();
        //热销商品，按销售额排行
        $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
        foreach ($result['hot_items_num'] as $k => $hot_items_num) {
            $result['hot_items_num'][$k]['title']=$this->sub_enter2($result['hot_items_num'][$k]['title']);
        }
        //热销城市排行
        $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and status !='TRADE_CLOSED' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
        //新老会员
        $result['old'] = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('traded_num','>',1)->count_all();
        $result['new'] = ORM::factory('qwt_yyxqrcode')->where('bid','=',$bid)->where('traded_num','=',1)->count_all();
        Kohana::$log->add("result1", print_r($result, true));
        //成交量
        $daytype='%Y-%m-%d-%H';
        $date=date('Y-m-d',time());
        $time=strtotime($date);
        $hour=date('H',time())+1;
        $count_time = array();
        for ($i=0; $i <$hour ; $i++) {
            $time1="+".$i."hours";
           $count_time[$i]= strtotime($time1,$time);
        }
        $result['line'] = array();
        $result['line'][0]['commision']= 0 ;
        $result['line'][0]['time'] = '00时';
        for($i=0;$count_time[$i];$i++){
            $time=date('Y-m-d-H',$count_time[$i]);
            $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
            $result['line'][$i+1]['commision']=$commision[0]['paymoney'];
            $result['line'][$i+1]['time'] = (date('H',$count_time[$i])+1).'时';
        }
        // var_dump($result);
        // exit;
        //地理位置
        $result['orders'] = DB::query(Database::SELECT,"SELECT * FROM (SELECT * FROM qwt_yyxorders where receiver_city!='' and bid=$bid and status!='TRADE_CLOSED' ORDER BY pay_time DESC , has_show ASC) BIAOMING GROUP BY receiver_city ORDER BY pay_time desc LIMIT 40")->execute()->as_array();
        for ($i=0; $result['orders'][$i]; $i++) {
            $t_location = ORM::factory('qwt_yyxlocation')->where('city','=',$result['orders'][$i]['receiver_city'])->find();
            if($t_location->lng&&$t_location->lat){
                $lng = $t_location->lng;
                $lat = $t_location->lat;
            }else{
                $location = urlencode($result['orders'][$i]['receiver_city']);
                $url = 'http://api.map.baidu.com/geocoder/v2/?address='.$location.'&output=json&ak=xS2SZp5OY5QNzy5gVaNbGYFaX4KkRtK9';
                $ch = curl_init(); // 初始化一个 cURL 对象
                curl_setopt($ch, CURLOPT_URL, $url); // 设置你需要抓取的URL
                curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $res = curl_exec($ch); // 运行cURL，请求网页
                curl_close($ch); // 关闭一个curl会话
                $json_obj = json_decode($res, true);
                $t_location->city = $result['orders'][$i]['receiver_city'];
                $t_location->lng = $lng = number_format($json_obj['result']['location']['lng'],3);
                $t_location->lat = $lat = number_format($json_obj['result']['location']['lat'],3);
                $t_location->save();
            }
            $this_order = ORM::factory('qwt_yyxorder')->where('bid','=',$bid)->where('id','=',$result['orders'][$i]['id'])->find();
            if($this_order->has_show!=1){
                $this_order->has_show = $this_order->has_show+1;
                $this_order->save();
            }
            $arr_location[$result['orders'][$i]['receiver_city']] = [$lng,$lat];
            $arr_order[$result['orders'][$i]['receiver_city']] = ['时间：'.date('Y-m-d H:i',$result['orders'][$i]['pay_time']).'<br>地点：'.$result['orders'][$i]['receiver_state'].$result['orders'][$i]['receiver_city'].'<br>订单金额:'.$result['orders'][$i]['payment'].'元'];
            $arr_place[$i] = $result['orders'][$i]['receiver_city'];
            $order_details[$i][0] = date('Y-m-d H:i',$result['orders'][$i]['pay_time']);
            $order_details[$i][1] = $result['orders'][$i]['receiver_state'].$result['orders'][$i]['receiver_city'];
            $order_details[$i][2] = $result['orders'][$i]['payment'];
        }
        //订单详情
        if($_GET['t']==1){
            $geoCoordMap = array('地理位置'=>$arr_location);
            $ordervalue = array('订单详情'=>$arr_order);
            $place = array('位置预设'=>$arr_place);
            $arr = array_merge($geoCoordMap,$ordervalue,$place);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==2){
            $source = array('订单来源'=>
                array(
                '有赞平台' => $result['yz_orders'],
                '自己平台' => $result['sd_orders']
            ));
            $goal = array('本月目标'=>
                array(
                '已完成' => $result['done_goal'],
                '总目标' => $result['sx_goal']
            ));
            $vip = array('成交会员'=>
                array(
                '新会员' => $result['old'],
                '老会员' => $result['new']
            ));
            $arr = array_merge($source,$goal,$vip);
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==3){
            $today = array('今日成交额'=>$result['today_done']);
            $yel = array('昨日成交额'=>$result['yestoday_done']);
            $all = array('累计成交额'=>$result['all_done']);
            $arr = array_merge($today,$yel,$all);
            Kohana::$log->add("arr1", print_r($arr, true));
            echo json_encode($arr);
            exit;
        }
        if($_GET['t']==4){
            $orders = array('交易订单'=>$order_details);
            echo json_encode($orders);
            exit;
        }
        if($_GET['t']==5){
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $this->sub_enter($result['hot_items'][$i]['title']);
                   // $arr[$i][1] = mb_substr($result['hot_items'][$i]['title'],0,5,'utf-8');
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $items = array('热销商品'=>$arr);
            echo json_encode($items);
            exit;
        }
        if($_GET['t']==6){
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $this->sub_enter2($result['hot_items_num'][$i]['title']);
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $items = array('热销商品金额'=>$arr);
            echo json_encode($items);
            exit;
        }
        if($_GET['t']==7){
            echo json_encode($result['hot_area']);
            exit;
        }
                //成交量line
        if($_GET['t']=='d2'){
            $daytype='%Y-%m-%d';
            $date=date('Y-m-d',time());
            //热销商品，按销量排行
            $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date'  group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
            foreach ($result['hot_items'] as $k => $hot_items) {
                $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and FROM_UNIXTIME(pay_time,'$daytype')='$date' and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldnum'];
                $result['hot_items'][$k]['oldCTN']=$oldcustomer;
                $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

            }
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items'][$i]['title'];
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $result['hot_item'] = array('热销商品'=>$arr);
            //热销商品，按销售额排行
            $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items_num'][$i]['title'];
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $result['hot_num'] = array('热销商品金额'=>$arr);
            //热销城市排行
            $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_area'][$i]['receiver_city']){
                    $result['hot_area'][$i]['receiver_city'] = mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8');
                }
            }
            echo json_encode($result);
            exit;
        }elseif($_GET['t']=='m2'){
            $daytype='%Y-%m';
            $date=date('Y-m',time());
           //热销商品，按销量排行
            $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
            foreach ($result['hot_items'] as $k => $hot_items) {
                $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and FROM_UNIXTIME(pay_time,'$daytype')='$date' and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldnum'];
                $result['hot_items'][$k]['oldCTN']=$oldcustomer;
                $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

            }
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items'][$i]['title'];
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $result['hot_item'] = array('热销商品'=>$arr);
            //热销商品，按销售额排行
            $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items_num'][$i]['title'];
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $result['hot_num'] = array('热销商品金额'=>$arr);
            //热销城市排行
            $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_area'][$i]['receiver_city']){
                    $result['hot_area'][$i]['receiver_city'] = mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8');
                }
            }
            echo json_encode($result);
            exit;
        }elseif($_GET['t']=='y2'){
            $daytype='%Y';
            $date=date('Y',time());
           //热销商品，按销量排行
            $result['hot_items'] = DB::query(Database::SELECT,"select lv,title,iid,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.num) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by (a.zongfen+a.lv) desc limit 0,10")->execute()->as_array();
            foreach ($result['hot_items'] as $k => $hot_items) {
                $oldcustomer=DB::query(Database::SELECT,"SELECT sum(a.oldnum) as oldctn from (SELECT sum(num) as oldnum from qwt_yyxorders where qid!=0 and FROM_UNIXTIME(pay_time,'$daytype')='$date' and bid = $bid and iid = '{$hot_items['iid']}' group by qid having count(qid) > 1) a")->execute()->as_array()[0]['oldnum'];
                $result['hot_items'][$k]['oldCTN']=$oldcustomer;
                $result['hot_items'][$k]['newCTN']=$hot_items['zongfen']-$oldcustomer;

            }
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items'][$i]['title'];
                   $arr[$i][2] = $result['hot_items'][$i]['zongfen'];
                   $arr[$i][3] = $result['hot_items'][$i]['oldCTN'];
                   $arr[$i][4] = $result['hot_items'][$i]['newCTN'];
                }
            }
            $result['hot_item'] = array('热销商品'=>$arr);
            //热销商品，按销售额排行
            $result['hot_items_num'] = DB::query(Database::SELECT,"select lv,title,zongfen from (select qwt_yyxitems.lv,qwt_yyxorders.iid,qwt_yyxorders.title,sum(qwt_yyxorders.payment) as zongfen from qwt_yyxorders inner join qwt_yyxitems on qwt_yyxorders.iid=qwt_yyxitems.id where qwt_yyxorders.bid=$bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by iid) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_items_num'][$i]['title']){
                   $arr[$i][0] = $i+1;
                   $arr[$i][1] = $result['hot_items_num'][$i]['title'];
                   $arr[$i][2] = $result['hot_items_num'][$i]['zongfen'];
                }
            }
            $result['hot_num'] = array('热销商品金额'=>$arr);
            //热销城市排行
            $result['hot_area'] = DB::query(Database::SELECT,"select receiver_city,zongfen from (select receiver_city,count(id) as zongfen from qwt_yyxorders where `bid` = $bid and qwt_yyxorders.status !='TRADE_CLOSED' and FROM_UNIXTIME(qwt_yyxorders.pay_time,'$daytype')='$date' group by receiver_city) a order by a.zongfen desc limit 0,10")->execute()->as_array();
            $a = 9;
            for ($i=0; $i <=$a ; $i++) {
                if($result['hot_area'][$i]['receiver_city']){
                    $result['hot_area'][$i]['receiver_city'] = mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8');
                }
            }
            echo json_encode($result);
            exit;
        }
        //成交量line
        if($_GET['t']=='d'){
            $daytype='%Y-%m-%d-%H';
            $date=date('Y-m-d',time());
            $time=strtotime($date);
            $hour=date('H',time())+1;
            $count_time = array();
            for ($i=0; $i <$hour ; $i++) {
                $time1="+".$i."hours";
               $count_time[$i]= strtotime($time1,$time);
            }
            $result['line'] = array();
            $result['line'][0]['commision']= 0 ;
            $result['line'][0]['time'] = '00时';
            for($i=0;$count_time[$i];$i++){
                $time=date('Y-m-d-H',$count_time[$i]);
                $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
                $result['line'][$i+1]['commision']=$commision[0]['paymoney'];
                $result['line'][$i+1]['time'] = (date('H',$count_time[$i])+1).'时';
            }
            echo json_encode($result['line']);
            exit;
        }elseif($_GET['t']=='m'){
            $daytype='%Y-%m-%d';
            $date=date('Y-m',time());
            $time=strtotime($date);
            $date=date('d',time());
            $count_time = array();
            for ($i=0; $i <$date ; $i++) {
                $time1="+".$i."days";
               $count_time[$i]= strtotime($time1,$time);
            }
            $result['line'] = array();
            for($i=0;$count_time[$i];$i++){
                $time=date('Y-m-d',$count_time[$i]);
                $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
                $result['line'][$i]['commision']=$commision[0]['paymoney'];
                $result['line'][$i]['time'] = date('d',$count_time[$i]).'日';
            }
            echo json_encode($result['line']);
            exit;
        }elseif($_GET['t']=='y'){
            $daytype='%Y-%m';
            $date=date('Y',time());
            $time  = mktime(0, 0, 0, 1, 1, $date);
            //$time=strtotime($date);
            $month=date('m',time());
            $count_time = array();
            for ($i=0; $i <$month ; $i++) {
                $time1="+".$i."months";
               $count_time[$i]= strtotime($time1,$time);
            }
            $result['line'] = array();
            for($i=0;$count_time[$i];$i++){
                $time=date('Y-m',$count_time[$i]);
                $commision=DB::query(Database::SELECT,"SELECT SUM(payment) AS paymoney from qwt_yyxorders where bid=$bid and FROM_UNIXTIME(`pay_time`,'$daytype')='$time' ")->execute()->as_array();
                $result['line'][$i]['commision']=$commision[0]['paymoney'];
                $result['line'][$i]['time'] = date('m',$count_time[$i]).'月';
            }
            echo json_encode($result['line']);
            exit;
        }
        // $user = ORM::factory('qwt_yyxlogin')->where('id','=',$bid)->find()->user;
        $view = 'weixin/yyx/smfyun';

        $this->template->content = View::factory($view)
                ->bind('result',$result)
                ->bind('arr_location',$arr_location)
                ->bind('arr_order',$arr_order)
                ->bind('order_details',$order_details)
                ->bind('arr_place',$arr_place);
    }
}
