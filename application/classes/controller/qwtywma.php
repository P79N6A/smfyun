<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtywma extends Controller_Base {

    public $template = 'weixin/qwt/tpl/ywmatpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        if (Request::instance()->action == 'generate_cron') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            if(Request::instance()->action == 'home'){
                $hasover = 1;
                @View::bind_global('hasover', $hasover);
            }
        }


    }
    public function after() {
        if ($this->bid) {
            $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->find_all();
            $this->template->todo = $todo;
        }
        @View::bind_global('bid', $this->bid);
        @View::bind_global('todo', $todo);
        parent::after();
    }
    // public function action_index() {
    //     $this->action_home();
    // }
    //系统配置
    // public function action_test(){
    //     $kls=ORM::factory('qwt_ywmkl')->order_by('id', 'ASC');

    //     $name='奖励对账单';
    //     $iid=1;
    //     $objPHPExcel = new PHPExcel();
    //     require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
    //     $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
    //     $aaa = DOCROOT."qwt/ywm/test.xls";
    //     $PHPExcel = $reader->load($aaa);
    //     $kls=$kls->find_all();
    //     $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
    //     $highestRow = $sheet->getHighestRow(); // 取得总行数
    //     //echo $highestRow.'<br>';
    //     $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    //     //echo $highestColumm.'<br>';
    //     // /** 循环读取每个单元格的数据 */
    //     // for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
    //     //     for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
    //     //     $PHPExcel->getActiveSheet()->getCell("$x$h")->setValue($txt);
    //     //         //$dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
    //     //     }
    //     // }
    //     foreach ($kls as $key => $v) {
    //         $privateKey = "sjdksldkwospaisk";
    //         $iv = "wsldnsjwisqweskl";
    //         $iid=1;
    //         $data = $v->code.'+-+'.$iid;
    //         $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
    //         $hb_code = urlencode(base64_encode($encrypted));
    //         $url =  'http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/'.$v->bid.'/ywm/user_snsapi_base?hb_code='.$hb_code;
    //         $clow=$key+$highestRow+1;
    //         $sheet->getCell('A'.$clow)->setValue($v->id);
    //         $sheet->getCell('B'.$clow)->setValue($url);
    //     }
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
    //     header('Cache-Control: max-age=0');
    //     $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
    //     $objWriter->save('php://output');
    //     exit;
    // }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid, 1);
        //文案配置
        if ($_POST) {
            // echo '<pre>';
            // var_dump($_POST);
            // var_dump($_FILES);
            // echo '<pre>';
            // exit();
            $cfg = ORM::factory('qwt_ywmcfg');
            foreach ($_POST['cus'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }
            $config = ORM::factory('qwt_ywmcfg')->getCfg($bid, 1);
            //背景图片
            if ($_FILES['bgpic']['error'] == 0||$_FILES['bgpic']['error'] ==2) {
                // $bgpic = DOCROOT."qwt/ywm/tmp/bgpic.$bid.jpg";
                if ($_FILES['bgpic']['size'] > 1024*400) {
                    $result['err3'] = '背景图片文件不能超过 400K';
                } else {
                    $result['ok2']++;
                    $cfg->setCfg($bid, 'bgpic', '', file_get_contents($_FILES['bgpic']['tmp_name']));
                    // @unlink($bgpic);
                    // move_uploaded_file($_FILES['bgpic']['tmp_name'], $bgpic);
                }
            }
            if ($_FILES['logo']['error'] == 0||$_FILES['logo']['error'] ==2) {
                // $logo = DOCROOT."qwt/ywm/tmp/logo.$bid.jpg";
                if ($_FILES['logo']['size'] > 1024*200) {
                    $result['err3'] = '品牌logo文件不能超过 200K';
                } else {
                    $result['ok2']++;
                    $cfg->setCfg($bid, 'logo', '', file_get_contents($_FILES['logo']['tmp_name']));
                    // @unlink($logo);
                    // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                }
            }
            if ($_FILES['sharelogo']['error'] == 0||$_FILES['sharelogo']['error'] ==2) {
                // $logo = DOCROOT."qwt/ywm/tmp/logo.$bid.jpg";
                if ($_FILES['sharelogo']['size'] > 1024*200) {
                    $result['err3'] = '品牌logo文件不能超过 200K';
                } else {
                    $result['ok2']++;
                    $cfg->setCfg($bid, 'sharelogo', '', file_get_contents($_FILES['sharelogo']['tmp_name']));
                    // @unlink($logo);
                    // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                }
            }
        }
        //红包
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        $lastupdate=ORM::factory('qwt_ywmkl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',14)->find()->lastupdate;//rebuy_time是时间戳
        $hb_cron = ORM::factory('qwt_ywmcron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
        if($hb_cron->time==0||$buytimenew>$hb_cron->time)
        // if(empty($lastupdate)||$buytimenew>$lastupdate)
          $flag=1;
        else
        {
            $days=(time()-$hb_cron->time)/(24*60*60);
            if($days>=7)
            {
             $flag=1;
            }
        }
        $left=$flag;

        $result['cron'] = ORM::factory('qwt_ywmcron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
        $result['bgpic'] = ORM::factory('qwt_ywmcfg')->where('bid', '=', $this->bid)->where('key', '=', 'bgpic')->find()->id;
        $result['logo'] = ORM::factory('qwt_ywmcfg')->where('bid', '=', $this->bid)->where('key', '=', 'logo')->find()->id;
        $result['sharelogo'] = ORM::factory('qwt_ywmcfg')->where('bid', '=', $this->bid)->where('key', '=', 'sharelogo')->find()->id;
        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('oauth', $oauth)
            ->bind('left',$left)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }
    public function action_payment(){
        $bid=$this->bid;

        // $this->action_refreshbuy();

        $orders=ORM::factory('qwt_ywmorder')->where('bid','=',$bid)->where('state','=',1);
        $orders=$orders->reset(FALSE);
        $countall=$orders->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');

        $result['orders'] = $orders->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['countall']=$countall;
        $this->template->title = '充值记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/payment')
            ->bind('result',$result)
            ->bind('pages',$pages)
            ->bind('bid',$bid);
    }
    public function action_account(){
        $bid = $this->bid;
        $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmorders where bid=$bid and state = 1 ")->execute()->as_array();
        $result['all'] = number_format($money[0]['money'],2);

        $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmweixins where bid=$bid and ct = 1 ")->execute()->as_array();
        $result['used'] = number_format($money[0]['money']/100,2);
        $this->template->title = '余额管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/account')
            ->bind('result',$result)
            ->bind('bid',$bid);
    }
    public function action_buy($money){
        $this->template = 'tpl/blank';
        self::before();
        require Kohana::find_file("vendor/kdt","KdtApiClient");

        $appId = 'c27bdd1e37cd8300fb';
        $appSecret = '3e7d8db9463b1e2fd92083418677c638';
        $client = new KdtApiClient($appId, $appSecret);

        $method = 'kdt.pay.qrcode.createQrCode';
        $params = [
            'qr_name' =>'红包雨充值-营销应用平台',

            'qr_price' => $money*100,
            //'qr_price' => 1,
            'qr_type' => 'QR_TYPE_DYNAMIC',
            // 'qr_source'=>$_POST['type'].'.'.$_POST['stream'],//类型和流量拼接  给了跟不给  获取不到  没有给的意义
        ];
        $test=$client->post($method, $params);

        $order = ORM::factory('qwt_ywmorder')->where('state','=',0)->where('bid','=',$this->bid)->find();
        $order->qrid = $test['response']['qr_id'];
        $order->tid = 'E'.$money.time();
        $order->bid = $this->bid;
        $order->money = $money;
        $order->time = time();
        $order->save();

        $data = array('imgurl' => $test['response']['qr_code'],'qrid' =>$test['response']['qr_id'],'url'=>$test['response']['qr_url']);
        echo json_encode($data);
        exit;
    }
    public function action_notify_qr($qr_id){
        //6079962
        require_once Kohana::find_file("vendor/kdt","KdtApiClient");

        $appId = 'c27bdd1e37cd8300fb';
        $appSecret = '3e7d8db9463b1e2fd92083418677c638';
        $client = new KdtApiClient($appId, $appSecret);
        $method1 = 'kdt.trades.qr.get';
        $params = [
            'status' =>'TRADE_RECEIVED'
        ];

        $resultarr=$client->post($method1,$params);
        $qrarr=$resultarr["response"]["qr_trades"];
        // echo '<pre>';
        // var_dump($qrarr);
        for($i=0;$qrarr[$i];$i++){
            if($qrarr[$i]['qr_id']==$qr_id){
                $flag = 1;
                // echo '付款成功';
            }
        }
        if($flag==1){
            $order = ORM::factory('qwt_ywmorder')->where('qrid','=',$qr_id)->find();
            $buy = ORM::factory('qwt_login')->where('id','=',$order->bid)->find();
            if($order->id){
                $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_ywmorders where bid=$this->bid and state = 1 ")->execute()->as_array();
                $buy->ywm_money = number_format($money[0]['money']+$order->money,2);
                $order->state = 1;
                $order->left = $buy->ywm_money;
                $buy->save();
                $order->save();
                $content = '支付成功';
            }else{
                $content = '支付异常，错误id为'.$qr_id;
            }
        }else{
            $content = '充值失败';
        }
        echo $content;
        exit;
    }
    public function action_marketing(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid,1);
        require_once Kohana::find_file('vendor','qwt/SmfyQwt');
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $smfy=new SmfyQwt();
        if($admin->yzaccess_token){
            //echo $admin->yzaccess_token."<br>";
            $result['yz']=1;
            $client = new YZTokenClient($admin->yzaccess_token);
            $result['yzcoupons']=$smfy->getyzcoupons($bid,$client);
        }
        if($_POST){
            if($_POST['market']['ifyzcoupons']==1&&!$_POST['market']['yzcoupons']){
                $result['err3']='优惠券不存在';
            }else{
               $cfg = ORM::factory('qwt_ywmcfg');
                foreach ($_POST['market'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok2'] += $ok;
                }
            }
            $config = ORM::factory('qwt_ywmcfg')->getCfg($bid, 1);
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/marketing')
        ->bind('config',$config)
        ->bind('result',$result)
        ->bind('bid',$this->bid);
    }
    public function action_setgood(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid, 1);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $tempconfig=ORM::factory('qwt_ywmcfg')->getCfg($this->bid);
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
            $pg=1;
            $method = 'youzan.items.onsale.get';
            $params =[
                // 'q' =>'title',
            ];
            $total_result= $client->post($method, '3.0.0', $params, $files);
            $total =$total_result['response']['count'];
            if(isset($total_result['response']['count'])){
                $item_num=ORM::factory('qwt_ywmsetgood')->where('bid','=',$bid)->count_all();
                if($total!=$item_num||$_GET['refresh']==1){
                    $a = ceil($total/100);
                    for($k=0;$k<$a;$k++){
                        // echo $k."<br>";
                        $method = 'youzan.items.onsale.get';
                        $params = array(
                            'page_size'=>100,
                            'page_no'=>$k+1,
                            // 'q' => 'item_id','title',
                            );
                        $results = $client->post($method, '3.0.0', $params, $files);
                        for($i=0;$results['response']['items'][$i];$i++){
                            $res=$results['response']['items'][$i];
                            $method = 'youzan.item.get';
                            $params = array(
                                 'item_id'=>$res['item_id'],
                            );
                            $result = $client->post($method, '3.0.0', $params, $files);
                            $item=$result['response']['item'];
                            $skus=$item['skus'];
                            $type=0;
                            $num_iid=$item['item_id'];
                            $name=$item['title'];
                            $price=$item['price']/100;
                            $pic=$item['pic_url'];
                            $url=$item['detail_url'];
                            $num=$item['quantity'];
                            $num_num = ORM::factory('qwt_ywmsetgood')->where('num_iid', '=', $num_iid)->count_all();
                            if($num_num==0 && $num_iid){
                                $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_ywmsetgoods` (`bid`,`num_iid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,1,$type,$num)");
                                $sql->execute();
                            }else{
                                $sql = DB::query(Database::UPDATE,"UPDATE `qwt_ywmsetgoods` SET `bid` = $bid ,`num_iid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 1 , `type` =$type where `num_iid` = $num_iid ");
                                $sql->execute();
                            }
                        }
                    }
                    $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_ywmsetgoods` where `state` =0 and `bid` = $bid and type =0 ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `qwt_ywmsetgoods` SET `state` =0 where `bid` = $bid and type =0 ");
                    $sql->execute();
                }
            }
        }
        Request::instance()->redirect('qwtywma/setgoods');
    }
    public function action_setgoods(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid, 1);
        $goods = ORM::factory('qwt_ywmsetgood')->where('bid','=',$bid)->where('type','=',0);
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('title', 'like', $s);
        }
        $hbnum=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',15)->find()->hbnum;
        $klnum=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->count_all();
        if($hbnum<=$klnum){
            $status==4;
        }
        $now_cron = ORM::factory('qwt_ywmcron')->where('bid','=',$this->bid)->where('state','=',1)->where('has_qr','=',0)->order_by('id','desc')->find();
        //计算耗时
        $crons = ORM::factory('qwt_ywmcron')->where('id','<=',$now_cron->id)->where('state','=',1)->where('has_qr','=',0)->find_all();
        $time = 0;
        foreach ($crons as $k => $v) {
            $time = $time + round(($v->num-$v->loop*3000)/3000)*5+5;
        }
        // echo $time;
        // exit();
        $result['time'] = $time;
        $mostnum=$hbnum-$klnum;
        if ($_POST['form']) {
            if($_POST['form']['status']==2&&$_POST['form']['priority']>0){
                // echo '<pre>';
                // var_dump($_POST);
                // echo '</pre>';
                $time = time();
                $ywmcron = ORM::factory('qwt_ywmcron');
                $ywmcron->bid = $this->bid;
                $ywmcron->iid = $_POST['form']['iid'];
                $ywmcron->time = $time;
                $ywmcron->state = 1;
                if($mostnum>=$_POST['form']['priority']){
                    $code_num=$_POST['form']['priority'];
                }else{
                    $code_num=$mostnum;
                }
                $ywmcron->num=$code_num;
                require Kohana::find_file("vendor/code","ywmCommonHelper");
                Helper::GenerateCode($time,$this->bid,$code_num,$_POST['form']['iid']);
                $ywmcron->save();
                // $ywmcron=ORM::factory('qwt_ywmcron');
                // $ywmcron->bid=$bid;
                // $ywmcron->iid=$_POST['form']['iid'];
                // $ywmcron->time=time();
                // if($mostnum>=$_POST['form']['priority']){
                //     $ywmcron->num=$_POST['form']['priority'];
                // }else{
                //     $ywmcron->num=$mostnum;
                // }
                // $ywmcron->save();
            }elseif($_POST['form']['status']==3){
                $this->action_downzip($_POST['form']['iid'],'setgoods');
            }
            // $goodid = $_POST['form']['num_iid'];
            // $good = ORM::factory('qwt_ywmsetgood')->where('bid', '=', $bid)->where('num_iid','=',$goodid)->find();
            // if(isset($_POST['form']['status'])){
            //     $good->status=$_POST['form']['status'];
            // }
            // if($_POST['form']['type']!=1){
            //     $good->money=$_POST['form']['money'];
            // }
            // $good->save();
        }
        $result['countall'] = $countall = $goods->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/ywm/pages');

        $result['goods'] =$goods->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/setgoods')
        ->bind('mostnum',$mostnum)
        ->bind('status',$status)
        ->bind('result',$result)
        ->bind('pages',$pages)
        ->bind('bid',$this->bid);

    }
    public function action_item_totle(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid, 1);
        $goods = ORM::factory('qwt_ywmsetgood')->where('bid','=',$bid);
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('title', 'like', $s);
        }
        $result['countall'] = $countall = $goods->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/ywm/pages');
        $result['goods'] =$goods->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/item_totle')
        ->bind('result',$result)
        ->bind('pages',$pages)
        ->bind('bid',$this->bid);

    }
    public function action_generate_cron(){
        set_time_limit(0);
        $hb_cron = ORM::factory('qwt_ywmcron')->where('has_qr','=',0)->order_by('id','asc')->find();
        $bid = $hb_cron->bid;
        $iid=$hb_cron->iid;
        $time = date('ymd',time());
        $code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->limit(3000)->find_all();//5min 3000
        $count_code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->count_all();
        if($count_code>0){//有口令才生成
            require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
            $zipname = DOCROOT."qwt/ywm/qr_code/$bid/$iid/code.zip";
            umask(0002);
            @mkdir(dirname($zipname),0777,true);
            $zip = new ZipArchive();
            $zip->open($zipname, ZIPARCHIVE::CREATE);
            foreach ($code as $k => $v) {
                //aes加密
                $privateKey = "sjdksldkwospaisk";
                $iv = "wsldnsjwisqweskl";
                $data = $v->code.'+-+'.$iid;
                $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                $hb_code = base64_encode($encrypted);

                $qrurl[$k] =  'http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$v->bid.'/ywm/user_snsapi_base?hb_code='.$hb_code;
                $localfile = DOCROOT."qwt/ywm/qr_code/$v->bid/$v->iid/".$time."_code/$v->code.png";
                umask(0002);
                @mkdir(dirname($localfile),0777,true);
                QRcode::png($qrurl[$k],$localfile,'L','6','2');
                $zip->addFile($localfile, basename($localfile));
                $end_kl = $v->id;
            }
            $zip->close();
            $last = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('id','>',$end_kl)->where('lastupdate','=',$hb_cron->time)->find();//5min 3000
            $hb_cron->loop = $hb_cron->loop+1;
            if($last->id){//还有没生成完的
                $hb_cron->end_id = $end_kl;
                $hb_cron->save();
            }else{//二维码已经生成完了
                $hb_cron->has_qr = 1;
                $hb_cron->code = file_get_contents($zipname);
                $hb_cron->save();
                @unlink($zipname);
            }
            //删除文件
            foreach ($code as $k => $v) {
                $localfile = DOCROOT."qwt/ywm/qr_code/$bid/$iid/".$time."_code/$v->code.png";
                @unlink($localfile);
            }
        }else{
            die('异常');
        }
        exit;
    }
    public function action_downzip($iid,$url){
        $hb_cron = ORM::factory('qwt_ywmcron')->where('state','=',1)->where('has_down','=',0)->where('iid','=',$iid)->order_by('id','desc')->find();
        if(!$hb_cron->id) return '您没有未下载的文件';
        $bid=$hb_cron->bid;
        $iid=$hb_cron->iid;
        $item=ORM::factory('qwt_ywmsetgood')->where('id','=',$iid)->find();
        $name=$item->title.$hb_cron->num;
        $zipname = DOCROOT."qwt/ywm/qr_code/$bid/$iid/{$name}.xls";
        //$zipname = DOCROOT."qwt/ywm/qr_code/$bid/$iid/code.zip";
        umask(0002);
        @mkdir(dirname($zipname),0777,true);
        @file_put_contents($zipname,$hb_cron->code);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.$name.".xls"); //文件名
        header("Content-Type: application/xls"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($zipname)); //告诉浏览器，文件大小
        @readfile($zipname);
        $hb_cron->has_down = 1;
        $hb_cron->save();
        @unlink($zipname);
        exit;
    }
    public function action_good($action='',$id=0){
        if ($action == 'add') return $this->action_good_add();
        if ($action == 'edit') return $this->action_good_edit($id);
        $bid = $this->bid;
        $other_setgoods = ORM::factory('qwt_ywmsetgood')->where('bid','=',$bid)->where('type','=',2);
        $other_setgoods = $other_setgoods->reset(false);

        $hbnum=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',15)->find()->hbnum;
        $klnum=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->count_all();
        $mostnum=$hbnum-$klnum;
        if($hbnum<=$klnum){
            $status==4;
        }
        $now_cron = ORM::factory('qwt_ywmcron')->where('bid','=',$this->bid)->where('state','=',1)->where('has_qr','=',0)->order_by('id','desc')->find();
        //计算耗时
        $crons = ORM::factory('qwt_ywmcron')->where('id','<=',$now_cron->id)->where('state','=',1)->where('has_qr','=',0)->find_all();
        $time = 0;
        foreach ($crons as $k => $v) {
            $time = $time + round(($v->num-$v->loop*3000)/3000)*5+5;
        }
        $result['time'] = $time;
        // echo $status;
        // exit();
        $mostnum=$hbnum-$klnum;
        if ($_POST['form']) {
            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';
            // exit();
            if($_POST['form']['status']==2&&$_POST['form']['priority']>0){
                $time = time();
                $ywmcron = ORM::factory('qwt_ywmcron');
                $ywmcron->bid = $this->bid;
                $ywmcron->iid = $_POST['form']['iid'];
                $ywmcron->time = $time;
                $ywmcron->state = 1;
                if($mostnum>=$_POST['form']['priority']){
                    $code_num=$_POST['form']['priority'];
                }else{
                    $code_num=$mostnum;
                }
                $ywmcron->num=$code_num;
                require Kohana::find_file("vendor/code","ywmCommonHelper");
                Helper::GenerateCode($time,$this->bid,$code_num,$_POST['form']['iid']);
                $ywmcron->save();
            }elseif($_POST['form']['status']==3){
                $this->action_downzip($_POST['form']['iid'],'good');
            }
            // $goodid = $_POST['form']['num_iid'];
            // $good = ORM::factory('qwt_ywmsetgood')->where('bid', '=', $bid)->where('num_iid','=',$goodid)->find();
            // if(isset($_POST['form']['status'])){
            //     $good->status=$_POST['form']['status'];
            // }
            // if($_POST['form']['type']!=1){
            //     $good->money=$_POST['form']['money'];
            // }
            // $good->save();
        }
        $result['countall'] = $countall = $other_setgoods->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/ywm/admin/pages');

        $result['items'] = $other_setgoods->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '其他商品管理';
        //$this->template->content = View::factory('weixin/ywm/admin/other_setgoods')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/goods')
            ->bind('bid',$bid)
            ->bind('mostnum',$mostnum)
            ->bind('status', $status)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_code_totle($action='', $id=0) {
        $bid =$this->bid;
        $result['hbnum']=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',15)->find()->hbnum;
        $result['has_created']=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->count_all();
        $result['residue_num']=$result['hbnum']-$result['has_created'];
        $result['has_scan']=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('used','!=',0)->count_all();
        if($result['has_created']){
            $result['scan_rate']=$result['has_scan']/$result['has_created']*100;
        }else{
            $result['scan_rate']=0;
        }

        $result['title'] = $this->template->title = '二维码数据统计';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/code_totle')
            ->bind('result', $result);
    }
    public function action_good_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid);

        if ($_POST['data']) {
            // echo '<pre>';
            // var_dump($_POST['data']);
            // var_dump($_FILES);
            // echo '</pre>';
            // exit();
            $item = ORM::factory('qwt_ywmsetgood');
            $item->values($_POST['data']);
            $item->lastupdate=time();
            $item->type=2;
            $item->bid = $bid;

            if (!$_POST['data']['title'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->db_pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "ywm:other_setgood:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtywma/good');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新商品';
        //$this->template->content = View::factory('weixin/ywm/admin/other_setgoods_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/goods_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
        //产品图片
    public function action_dbimages($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_ywm$type";

        $pic = ORM::factory($table, $id)->db_pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_good_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid);
        $item = ORM::factory('qwt_ywmsetgood', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');
        if ($_GET['DELETE'] == 1) {
            $item->delete();
            Request::instance()->redirect('qwtywma/good');
        }
        if ($_POST['data']) {
            $item->values($_POST['data']);
            $item->bid = $bid;
            $item->lastupdate=time();
            if (!$_POST['data']['title']) $result['error'] = '请填写完整后再提交';
            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];
                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->db_pic = file_get_contents($tmpfile);
                }
            }
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "ywm:other_setgood:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtywma/good');
            }
        }
        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改商品';
        //$this->template->content = View::factory('weixin/ywm/admin/other_setgoods_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/goods_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_ywm$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_download(){
       $dir=Kohana::include_paths()[0].'vendor/';
        $file=$dir.'code/hongbao.zip';
        if(!file_exists($file))
       {
        echo "素材不存在！";
        exit();
       }
        $value=fopen($file,'r+');
        header('Content-type: application/force-download');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        header('Content-Disposition: attachment; filename='.basename($file));
        //@readfile($file);
        echo fread($value,filesize($file));
        fclose($value);
        @unlink($file);
    }

    public function action_getdata(){
        $bid=$this->bid;
        $result=array();
        $buycodenum=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',15)->find()->hbnum;//购买总数
        $creatcodenum = ORM::factory('qwt_ywmkl')->where('bid', '=', $bid)->count_all();//产生的口令总数
        $koulinused=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('used','>',0)->count_all();//普通已使用的口令数
        $hongbaonum1=ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('ct','=',1)->count_all();
        $hongbaonum2=ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('ct','=',2)->count_all();
        $hongbaonum3=ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('ct','=',3)->count_all();
        $hongbaomoney1=DB::query(Database::SELECT,"SELECT SUM(money) as hongbaomoney1 from qwt_ywmweixins where bid = $bid and ct=1 ")->execute()->as_array();
        $hongbaomoney1=$hongbaomoney1[0]['hongbaomoney1'];
        $hongbaomoney2=DB::query(Database::SELECT,"SELECT SUM(money) as hongbaomoney2 from qwt_ywmweixins where bid = $bid and ct=2 ")->execute()->as_array();
        $hongbaomoney2=$hongbaomoney2[0]['hongbaomoney2'];
        $hongbaomoney3=DB::query(Database::SELECT,"SELECT SUM(money) as hongbaomoney3 from qwt_ywmweixins where bid = $bid and ct=3 ")->execute()->as_array();
        $hongbaomoney3=$hongbaomoney3[0]['hongbaomoney3'];

        $usedcodenum=ORM::factory('qwt_ywmkl')->where('used', '>', 0)->where('bid', '=', $bid)->count_all();
        if($creatcodenum<=0){
            //echo '0';
        }
        else
        {
            //echo json_encode($result);
        }
        $result['buycodenum']=$buycodenum;
        $result['creatcodenum']=$creatcodenum;
        $result['koulinused']=$koulinused;
        $result['residue']=$buycodenum-$creatcodenum;
        $result['hongbaonum1']=$hongbaonum1;
        // $result['hongbaonum2']=$hongbaonum2;
        // $result['hongbaonum3']=$hongbaonum3;
        $result['hongbaomoney1']=$hongbaomoney1;
        // $result['hongbaomoney2']=$hongbaomoney2;
        // $result['hongbaomoney3']=$hongbaomoney3;
        $this->template->title = '概况';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/getdata')
            ->bind('result', $result)
            ->bind('config', $this->config);
    }
    public function action_download_csv(){
        set_time_limit(0);
        $bid = $this->bid;
        $hb_cron = ORM::factory('qwt_ywmcron')->where('bid','=',$bid)->order_by('id','DESC')->find();
        $code = ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('lastupdate','=',$hb_cron->time)->find_all();
        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
        $zipname = DOCROOT."qwt/ywm/qr_code/$bid/code.zip";
        umask(0002);
        @mkdir(dirname($zipname),0777,true);
        $zip = new ZipArchive();
        $zip->open($zipname, ZIPARCHIVE::CREATE);
        foreach ($code as $k => $v) {
            $qrurl[$k] =  'http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$bid.'/ywm/user_snsapi_base?hb_code='.$v->code;
            $localfile = DOCROOT."qwt/ywm/qr_code/$bid/".date('ymd',time())."_code/$v->code.png";
            umask(0002);
            @mkdir(dirname($localfile),0777,true);
            QRcode::png($qrurl[$k],$localfile,'L','6','2');
            $zip->addFile($localfile, basename($localfile));
        }
        $zip->close();
        $hb_cron->has_down = 1;
        $hb_cron->save();
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($zipname)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($zipname)); //告诉浏览器，文件大小
        @readfile($zipname);
        @unlink($zipname);
        foreach ($code as $k => $v) {
            $localfile = DOCROOT."qwt/ywm/qr_code/$bid/".date('ymd',time())."_code/$v->code.png";
            @unlink($localfile);
        }
        // @unlink(DOCROOT."qwt/ywm/qr_code/$bid/".date('ymd',time())."_code");
        exit;
    }
    public function action_pre_generate(){
        $buynum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->qr_num;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('qwt_ywmkl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->lastupdate;
        // $hb_cron = ORM::factory('qwt_ywmcron')->where('bid', '=', $this->bid)->where('state','=',0)->find();
        // if(empty($lastupdate)||$buytimenew>$lastupdate||!$hb_cron->id)
        $hb_cron = ORM::factory('qwt_ywmcron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
        if($hb_cron->time==0||$buytimenew>$hb_cron->time)
          $flag=1;
        else
        {
            $days=(time()-$hb_cron->time)/(24*60*60);
            if($days>=7)
            {
             $flag=1;
            }
            else
                Request::instance()->redirect('/qwtywma/home');
           }

            if($flag==1)
            {
              $ywmcron = ORM::factory('qwt_ywmcron');
              $ywmcron->bid = $this->bid;
              $ywmcron->time = time();
              $ywmcron->state = 0;
              $ywmcron->num = $buynum;
              $ywmcron->save();
             //直接退出
             Request::instance()->redirect('/qwtywma/home');
             //echo "<script>history.go(-1);</script>";产生的csv会有参杂html代码
            }
    }
    // public function action_generate_cron(){
    //     $users = ORM::factory('qwt_ywmcron')->where('state','=',0)->find_all();
    //     require Kohana::find_file("vendor/code","ywmCommonHelper");
    //     foreach ($users as $k => $v) {
    //         $buynum = $v->num;
    //         Helper::GenerateCode($v->time,$v->bid,$buynum);
    //         $v->state = 1;
    //         $v->save();
    //     }
    //     exit;
    // }
    public function action_generate(){//生成口令
        set_time_limit(0);
        require Kohana::find_file("vendor/code","ywmCommonHelper");
        $buynum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->qr_num;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('qwt_ywmkl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->lastupdate;

        if(empty($lastupdate)||$buytimenew>$lastupdate)
          $flag=1;
        else
        {
            $days=(time()-$lastupdate)/(24*60*60);
            if($days>=7)
            {
             $flag=1;
            }
            else
                Request::instance()->redirect('/qwtywma/home');
           }

            if($flag==1)
            {
             Helper::GenerateCode($this->bid,$buynum);
             //直接退出
             exit();
             //echo "<script>history.go(-1);</script>";产生的csv会有参杂html代码
            }
    }

    //兑换管理
    public function action_qrcodes(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid);
        $order = ORM::factory('qwt_ywmweixin')->where('bid', '=', $bid)->where('kouling', '!=','');
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('nickname', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/ywm/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '扫码记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/qrcode')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_sendrecord(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_ywmcfg')->getCfg($bid);
        $order = ORM::factory('qwt_ywmweixin')->where('bid', '=', $bid)->where('kouling', '!=','')->where('share','=',1);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('nickname', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/ywm/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '发送记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/ywm/sendrecord')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //API证书上传函数
    private function Upload($name){
        $dir=Kohana::include_paths()[0].'vendor/weixin/cert/';
        $flag=true;
        if($_FILES['filecert']['error']>0)
        {
           $flag=false;
        }
        if(is_uploaded_file($_FILES['filecert']['tmp_name']))//判断该文件是否通过http post方式正确上传
        {

            if(!is_dir($dir.$name)){
                $new=mkdir($dir.$name);
            }
            if(move_uploaded_file($_FILES['filecert']['tmp_name'], $dir.$name.'/1.zip')){
                $zip = new ZipArchive();
                if ($zip->open($dir.$name.'/1.zip') === TRUE)
                {
                    $zip->extractTo($dir.$name.'/');
                    $zip->close();

                }
                else
                {
                    $flag=false;;

                }
            }
            else
            {
                $flag=false;

            }
        }
        else
        {
            $flag=false;

        }
        return$flag;
    }
    private function GetAgent(){

            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $is_pc = (strpos($agent, 'windows nt')) ? true : false;
            $is_mac = (strpos($agent, 'mac os')) ? true : false;
            $is_iphone = (strpos($agent, 'iphone')) ? true : false;
            $is_android = (strpos($agent, 'android')) ? true : false;
            $is_ipad = (strpos($agent, 'ipad')) ? true : false;

            $device="unknow";
            if($is_pc){
                  $device = 'pc';
            }

            if($is_mac){
                  $device = 'mac';
            }

            if($is_iphone){
                  $device = 'iphone';
            }

            if($is_android){
                  $device = 'android';
            }

            if($is_ipad){
                  $device = 'ipad';
            }

            return $device;
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
