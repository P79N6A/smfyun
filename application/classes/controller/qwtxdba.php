<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtxdba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/xdbatpl';
    public $pagesize = 20;
    public $yzaccess_token;
    public $config;
    public $bid;

    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',4)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    public function action_test(){
        $qr_num=ORM::factory('qwt_xdbqrcode')->where('bid','=',6)->delete_all();
        exit();
    }
    //系统配置
    public function action_home() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        //$this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        //文案配置
        if ($_POST['text']) {


            $cfg = ORM::factory('qwt_xdbcfg');
            $qrfile = DOCROOT."qwt/xdb/tmp/tpl.$bid.jpg";

            //海报有效期
            if ($_POST['text']['ticket_lifetime'] >= 30) $_POST['text']['ticket_lifetime'] = 30;

            //二维码海报
            if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*400) {
                    $result['err3'] = '海报模板文件不能超过 400K';
                } else {
                    $result['ok3']++;
                    umask(0002);
                    @mkdir(dirname($qrfile),0777,true);
                    $cfg->setCfg($bid, 'tpl', '', file_get_contents($_FILES['pic']['tmp_name']));
                    @unlink($qrfile);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                }
            }
            //默认头像
            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."xdb/tmp/head.$bid.jpg";
                    umask(0002);
                    @mkdir(dirname($default_head_file),0777,true);
                    $cfg->setCfg($bid, 'tplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
                    @unlink($default_head_file);
                    move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }

            if (!$result['err3']) {
                foreach ($_POST['text'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, trim($v));
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;
                }

                //海报配置
                foreach ($_POST['px'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;

                    //更新海报缓存 key
                    if ($result['ok3'] && file_exists($qrfile)) {
                        touch($qrfile);

                        $tpl = ORM::factory('qwt_xdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('qwt_xdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }
            //重新读取配置
            $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        }
        $result['tpl'] = ORM::factory('qwt_xdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('qwt_xdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }

    public function action_setgoods1(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $tempconfig=ORM::factory('qwt_xdbcfg')->getCfg($this->bid);
        if($this->yzaccess_token){
            $client = new YZTokenClient($this->yzaccess_token);
            $pg=1;
            $method = 'youzan.items.onsale.get';
            $params =[
                // 'q' =>'title',
            ];
            $total_result= $client->post($method, '3.0.0', $params, $files);
            // echo '<pre>';
            // var_dump($total_result);
            // echo '</pre>';
            $total =$total_result['response']['count'];
            if(isset($total_result['response']['count'])){
                $item_num=ORM::factory('qwt_xdbsetgood')->where('bid','=',$bid)->count_all();
                // echo $total."<br>";
                // echo $item_num."<br>";
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
                        // echo '<pre>';
                        // var_dump($results);
                        // echo '</pre>';
                        // echo "===============<br>";
                        for($i=0;$results['response']['items'][$i];$i++){
                            $res=$results['response']['items'][$i];
                            $method = 'youzan.item.get';
                            $params = array(
                                 'item_id'=>$res['item_id'],
                            );
                            $result = $client->post($method, '3.0.0', $params, $files);
                            // echo '<pre>';
                            // var_dump($result);
                            // echo '</pre>';
                            // echo "===============<br>";
                            $item=$result['response']['item'];
                            $skus=$item['skus'];
                            $type=0;
                            if($skus){
                                // echo "aaa<br>";
                                $type=1;
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
                                    // echo $sku_id."<br>";
                                    $sku_num = ORM::factory('qwt_xdbgoodsku')->where('sku_id', '=', $sku_id)->count_all();
                                    // echo $sku_num.'<br>';
                                    if($sku_num==0 && $sku_id){
                                        // echo "上面<br>";
                                        $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_xdbgoodskus` (`bid`,`item_id`,`title`,`sku_id`, `price`,`status`,`state`,`num`) VALUES ($bid,$item_id,'$title' ,$sku_id,$price,1,1,$num)");
                                        $sql->execute();
                                    }else{
                                        // echo "下面<br>";
                                        $sql = DB::query(Database::UPDATE,"UPDATE `qwt_xdbgoodskus` SET `bid` = $bid ,`item_id` = $item_id,`title` ='$title',`sku_id`=$sku_id, `price`=$price,`state` = 1 , `num`= $num where `sku_id` = $sku_id ");
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
                            $num_num = ORM::factory('qwt_xdbsetgood')->where('num_iid', '=', $num_iid)->count_all();
                            if($num_num==0 && $num_iid){
                                $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_xdbsetgoods` (`bid`,`num_iid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',1,1,$type,$num)");
                                $sql->execute();
                            }else{
                                $sql = DB::query(Database::UPDATE,"UPDATE `qwt_xdbsetgoods` SET `bid` = $bid ,`num_iid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 1 , `type` =$type where `num_iid` = $num_iid ");
                                $sql->execute();
                            }
                        }
                    }
                    $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_xdbgoodskus` where `state` =0 and `bid` = $bid ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `qwt_xdbgoodskus` SET `state` =0 where `bid` = $bid");
                    $sql->execute();
                    $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_xdbsetgoods` where `state` =0 and `bid` = $bid ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `qwt_xdbsetgoods` SET `state` =0 where `bid` = $bid");
                    $sql->execute();
                }
            }

        }
        Request::instance()->redirect('qwtxdba/setgoods');
    }
    public function action_setgoods(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        $goods = ORM::factory('qwt_xdbsetgood')->where('bid','=',$bid);
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('title', 'like', $s);
        }
        if ($_POST['form']['num_iid']) {
            $goodid = $_POST['form']['num_iid'];
            // echo '<pre>';
            // var_dump($_POST['form']);

            $good = ORM::factory('qwt_xdbsetgood')->where('bid', '=', $bid)->where('num_iid','=',$goodid)->find();
            if(isset($_POST['form']['status'])){
                $good->status=$_POST['form']['status'];
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
        ))->render('weixin/qwt/admin/xdb/pages');

        $result['goods'] =$goods->order_by('status', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['suite'] = ORM::factory('qwt_xdbsuite')->where('bid','=',$bid)->find_all();
      //   //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
      //   require_once Kohana::find_file("vendor/kdt","YZTokenClient");
      //   $this->yzaccess_token = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
      //   $tempconfig=ORM::factory('qwt_xdbcfg')->getCfg($this->bid);
      //   if($this->yzaccess_token)
      //   {
      //       $page = max($_GET['page'], 1);

      //       $client = new YZTokenClient($this->yzaccess_token);
      //       $method = 'kdt.items.onsale.get';
      //       $params = array(
      //            'page_size'=>20,
      //            'page_no'=>$page,
      //           'fields' => 'num_iid,title,price,pic_url,num,sold_num,detail_url',
      //       );


      //           //修改佣金


      //        $result = $client->post($method, '1.0.0', $params, $files);
      //         $pages = Pagination::factory(array(
      //           'total_items'   =>$result['response']['total_results'],
      //           'items_per_page'=> $this->pagesize,
      //       ))->render('weixin/qwt/admin/xdb/pages');
      // }
      // else
      //   $result['response']=array();

    $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/setgoods')
    ->bind('result',$result)
    ->bind('pages',$pages)
    ->bind('bid',$this->bid);

     }


    public function action_history_trades()
    {

        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $trade = ORM::factory('qwt_xdbtrade')->where('bid', '=', $bid);
        $trade = $trade->reset(FALSE);
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='订单记录';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '订单名称')
                        ->setCellValue('B'.$num, '付款时间')
                        ->setCellValue('C'.$num, '金额')
                        ->setCellValue('D'.$num, '客户昵称')
                        ->setCellValue('E'.$num, '所属代理')
                        ->setCellValue('F'.$num, '需扣除的销售利润')
                        ->setCellValue('G'.$num, '订单状态');
            $orders=$trade->order_by('int_time','DESC')->limit(400)->find_all();
            foreach($orders as $k => $v){
                $fuser=ORM::factory('qwt_xdbqrcode')->where('bid','=',$bid)->where('openid','=',$v->fopenid)->find();
                switch ($v->status) {
                    case 'WAIT_SELLER_SEND_GOODS':
                        $status='已付款';
                        break;
                    case 'WAIT_BUYER_CONFIRM_GOODS':
                        $status='已发货';
                        break;
                    case 'TRADE_BUYER_SIGNED':
                        $status='已签收';
                        break;
                    case 'TRADE_CLOSED':
                        $status='已退款';
                        break;
                    default:
                        $status='不详';
                        break;
                }
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->title)
                            ->setCellValue('B'.$num, $v->pay_time)
                            ->setCellValue('C'.$num, $v->payment)
                            ->setCellValue('D'.$num, $v->qrcode->nickname)
                            ->setCellValue('E'.$num, $fuser->nickname)
                            ->setCellValue('F'.$num, $v->money1)
                            ->setCellValue('G'.$num, $status);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if($_GET['qid']||$_GET['flag']){
            $openid=ORM::factory('qwt_xdbqrcode')->where('id','=',$_GET['qid'])->find()->openid;
            $group=ORM::factory('qwt_xdbgroup')->where('bid','=',$bid)->where('qid','=',$_GET['qid'])->find();
            $bottom=$group->bottom;
            if($bottom){
                $child_group=explode(",",$bottom);
                Array_push($child_group,$group->id);
            }else{
                $child_group=array();
                Array_push($child_group,$group->id);
            }
            $month=date('Y-m',time()).'%';
            // $month=(string)$month
            $day=date('Y-m-d',time()).'%';
            if($_GET['flag']=='dayp'){
                // echo $openid.'<br>';
                // echo $day."<br>";
                // exit();
                $trade=$trade->where('fopenid','=',$openid)->where('pay_time','like',$day);
            }elseif($_GET['flag']=='monthp'){
                $trade=$trade->where('fopenid','=',$openid)->where('pay_time','like',$month);
            }elseif($_GET['flag']=='allp'){
                $trade=$trade->where('fopenid','=',$openid);
            }elseif($_GET['flag']=='dayt'){
                $trade=$trade->where('gid','IN',$child_group)->where('pay_time','like',$day);
            }elseif($_GET['flag']=='montht'){
                $trade=$trade->where('gid','IN',$child_group)->where('pay_time','like',$month);
            }elseif($_GET['flag']=='allt'){
                $trade=$trade->where('gid','IN',$child_group);
            }elseif($_GET['flag']=='cnum'){
                $trade=$trade->where('openid','=',$openid);
            }
        }
        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from qwt_xdbqrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

            $trade =$trade->where('title', 'like', $s);

            if(count($openids)>0)
            $trade=$trade->or_where('openid', 'IN', $openids);

            $trade = $trade->and_where_close();
        }

        $result['countall'] = $countall = $trade->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/xdb/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_lab() {
        //require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        //$this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        if ($_POST['tag']['tag_name']){//点击刷新
            $tag = $_POST['tag'];
            $cfg = ORM::factory('qwt_xdbcfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }
            $result['fresh'] = 1;
            $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
            // $next_qid=$config['next_qid'];
            // if(!$next_qid) $next_qid=0;
            $users = ORM::factory('qwt_xdbqrcode')->where('bid','=',$bid)->find_all();
            foreach ($users as $k => $v) {
                $labuser[]="($v->bid,$v->id,'{$config['tag_name']}')";
            }
            $sql='INSERT IGNORE INTO qwt_xdblabs (`bid`,`qid`,`lab_name`) VALUES '. join(',',$labuser);
            DB::query(Database::INSERT,$sql)->execute();
            $result['fresh'] = 1;
        }
        $result['islab'] = ORM::factory('qwt_xdblab')->where('bid','=',$bid)->where('status','=',1)->count_all();
        $result['alllab'] = ORM::factory('qwt_xdblab')->where('bid','=',$bid)->count_all();
        $this->template->title = '首页';
        //$this->template->content = View::factory('weixin/wdy/admin/lab')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/lab')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_kanlink() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/kanlink')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_yzorder() {
        $bid = $this->bid;
        $fopenid = $_GET['fopenid'];
        $yzorder = ORM::factory('qwt_xdbtrade')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->find_all();
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid, 1);
        $this->template->title = '订单明细';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/yzorder')
            ->bind('yzorder', $yzorder)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if ($_POST['form']['score']) {
                    $qrcode_edit = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('id', '=', $id)->find();
                    $sid=ORM::factory('qwt_xdbscore')->scoreIn($qrcode_edit,$_POST['form']['score']);
                    Kohana::$log->add('qwt_xdbconfig'.$bid, print_r($config,true));
                }
            }
        }
        $qrcode = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where('nickname', 'like', $s); //->or_where('openid', 'like', $s);
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_xdbqrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
            $tempid=array();
              if($firstchild[0]['openid']==null)
              {
                $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
              }
              else
              {
                  for($i=0;$firstchild[$i];$i++)
                  {
                    $tempid[$i]=$firstchild[$i]['openid'];
                  }
              }
              //$qrcode = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid);
              $qrcode =$qrcode->where('fopenid', 'IN',$tempid);


        }

        //按状态搜索
        if ($_GET['lock']) {
            $result['lock'] = $_GET['lock'];
            $qrcode = $qrcode->where('lock', '=', $result['lock']);
        }

        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/xdb/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $this->config = $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);

        //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            // echo "<pre>";
            // var_dump($_FILES);
            // echo "</pre>";
            // exit();
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');

            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[17], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 19) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[17]);
                    $shipcode = iconv('gbk', 'utf-8', $data[18]);
                } else {
                    $shiptype = $data[17];
                    $shipcode = $data[18];
                }

                $order = ORM::factory('qwt_xdbchange')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                if ($order->status == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
                    $order->status = 1;
                    $order->shiptype = $shiptype;
                    $order->shipcode = $shipcode;
                    $order->save();
                    $i++;
                }
            }
            // while ($data = fgetcsv($fh, 1024)) {
            //     $encode = mb_detect_encoding($data[7], array("ASCII",'UTF-8',"GB2312","GBK"));

            //     // print_r($data);
            //     // if (count($data) < 19) continue;
            //     // if (!is_numeric($data[0])) continue;

            //     //发货
            //     $oid = $data[0];

            //     if ($encode == 'EUC-CN') {
            //         $shiptype = iconv('gbk', 'utf-8', $data[7]);
            //         $shipcode = iconv('gbk', 'utf-8', $data[6]);
            //     } else {
            //         $shiptype = $data[7];
            //         $shipcode = $data[6];
            //     }

            //     $order = ORM::factory('qwt_xdborder')->where('bid', '=', $bid)->where('tel', '=', $data[1])->find();
            //     // if ($order->status == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
            //         $order->status = 1;
            //         $order->shiptype = $shiptype;
            //         $order->shipcode = $shipcode;
            //         $order->save();
            //         $i++;
            //     // }
            // }
            fclose($fh);
            $result['ok'] = "共批量发货 $i 个订单。";
        }

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }
        if($_POST['edit_oid']){
            // echo '<pre>';
            // var_dump($_POST['edit']);
            // exit;
            $edit_order = ORM::factory('qwt_xdbchange')->where('id','=',$_POST['edit_oid'])->find();
            $edit_order->name = $_POST['edit']['name'];
            $edit_order->tel = $_POST['edit']['tel'];
            $edit_order->city = $_POST['edit']['city'];
            $edit_order->address = $_POST['edit']['address'];
            $edit_order->save();
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('qwt_xdbchange')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';
                    $tempname=ORM::factory("qwt_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("qwt_xdbitem")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("qwt_xdbqrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                }
                $order->save();
            }

            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        if ($action == 'ship' && $id) {
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
            if(!$bid) Kohana::$log->add('xdbbid:', 'order');//写入日志，可以删除
            $wx = new Wxoauth($bid,$options);

            $order = ORM::factory('qwt_xdbchange')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();
                kohana::$log->add('qwtxdb111',print_r(111,true));
                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['qwtxdba']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['qwtxdba']['shipcode'] = $_REQUEST['shipcode'];
                    $order->shiptype = $_REQUEST['shiptype'];
                    $order->shipcode = $_REQUEST['shipcode'];

                    $order->save();

                    //发微信消息给用户
                    $shipmsg = "%s，您的积分兑换奖品已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $res = $wx->sendCustomMessage($msg);
                }
                if(($order->type)==3)
                {

                    $shipmsg = "%s，您的积分兑换奖品已经充值，请注意查收。";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $res = $wx->sendCustomMessage($msg);
                }
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';
                     kohana::$log->add('qwtxdb111',print_r(222,true));
                    $tempname=ORM::factory("qwt_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("qwt_xdbitem")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("qwt_xdbqrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                    kohana::$log->add('qwtxdb111',print_r($hbresult,true));
                }
                //Request::instance()->redirect('qwtxdba/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('qwt_xdbchange')->where('bid', '=', $bid)->where('status', '=', $result['status'])->and_where_open()->where('need_money','=',0)->or_where_open()->where('need_money','>',0)->and_where('order_state','=','1')->or_where_close()->and_where_close();
        // $order = ORM::factory('qwt_xdborder')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            $order = $order->and_where_open();
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('city', 'like', $s)->or_where('address', 'like', $s);
            if($countuser>0){
                $user = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
                $userarr = array();
                foreach ($user as $k => $v) {
                    $userarr[$k] = $v->id;
                }
                $order = $order->or_where('qid', 'IN', $userarr);
            }
            $order = $order->and_where_close();
            // $order = $order->and_where_open();
            // $result['s'] = $_GET['s'];
            // $s = '%'.trim($_GET['s'].'%');
            // $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            // $order = $order->and_where_close();
        }

        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('qwt_xdbqrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        $active_type="total";
        //分类展示 1实物需发货的
         if ($_GET['type']=="object") {
            $order = $order->where('type', '=', null);
            $active_type="object";
        }
        //2虚拟话费和流量充值
         if ($_GET['type']=="fare") {
            $order = $order->where('type', '=', 3);
            $active_type="fare";
        }
        //3优惠码
         if ($_GET['type']=="hb") {
            $order = $order->where('type', '=', 4);
            $active_type="hb";
        }

        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
             $tempname="全部";
             // var_dump($_GET);
             // exit();
            switch ($_GET["tag"]) {
                case 'fare':
                    $orders=$order->where('type','=',3)->limit(1000)->find_all();
                    $tempname="充值";
                    break;
                case'object':
                    $orders=$order->where('type','=',NULL)->limit(1000)->find_all();
                    $tempname="实物";
                    break;
                case'code':
                    $orders=$order->where('type','=',5)->limit(1000)->find_all();
                    $tempname="优惠码";
                    break;
                default:
                    $orders = $order->find_all();
                    break;
            }
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('id','昵称', '收货人', '收货电话', '收货城市', '收货地址', '备注', '兑换产品','金额','消耗积分', '订单时间', '是否有关注', '产品ID', 'OpenID', '是否锁定', '直接粉丝', '间接粉丝', '物流公司', '物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($orders as $o) {
                //$count2 = ORM::factory('qwt_xdbscore')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($o->bid))->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();

                //地址处理
                list($prov, $city, $dist) = explode(' ', $o->city);
                $array = array($o->id,$o->user->nickname,$o->name, $o->tel, "{$prov} {$city} {$dist}", $o->address, $o->memo, $o->item->name,$o->item->price, $o->score, date('Y-m-d H:i:s', $o->createdtime), $o->user->subscribe, $o->item->id, $o->user->openid, $o->user->lock, $count2);

                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }

                fputcsv($fp, $array);
            }
            exit;
        }

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/xdb/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('activetype',$active_type);
    }
    public function converta($state){
        switch ($state) {
            case '1':
                return '发送成功';
                break;
            case '0':
                return '发送失败';
                break;
            default:
                break;
        }
    }
    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);
        $item=ORM::factory('qwt_xdbitem')->where('bid', '=', $bid);
        $item = $item->reset(FALSE);
        if ($_GET['s']) {
            $item = $item->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $item = $item->where('km_content', 'like', $s);
            $item = $item->and_where_close();
        }
        $countall = $item->count_all();
        $iid = ORM::factory('qwt_xdbitem')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('qwt_xdbchange')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }
         //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/xdb/pages');
        $result['items'] = $item->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '奖品管理';
        $result['title'] = '添加新奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('pages',$pages)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $smfy=new SmfyQwt();
        if($admin->yzaccess_token){
            //echo $admin->yzaccess_token."<br>";
            $result['yz']=1;
            $client = new YZTokenClient($admin->yzaccess_token);
            $result['yzcoupons']=$smfy->getyzcoupons($bid,$client);
            $result['yzgifts']=$smfy->getyzgifts($bid,$client);
        }
        if($admin->appid){
            $result['wx']=1;
            $result['wxcards']=$smfy->getwxcards($bid);
        }
        // echo "<pre>";
        // var_dump($result);
        // echo "<pre>";
        // exit();
        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信卡券列表';
                }
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==6){
                if(!$_POST['yzgift']){
                    $result['error'] = '未拉取到有赞赠品列表';
                }
                $_POST['data']['url']=$_POST['yzgift'];
            }elseif ($_POST['data']['type']==5) {
                if(!$_POST['yzcoupons']){
                    $result['error'] = '未拉取到有赞优惠券列表';
                }
                $_POST['data']['url']=$_POST['yzcoupons'];
            }elseif ($_POST['data']['type']==0) {
                unset($_POST['data']['url']);
            }
            $item = ORM::factory('qwt_xdbitem');
            $item->values($_POST['data']);

            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['score'] || !$_POST['data']['stock']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*300) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "qwtxdb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtxdba/items');
            }
        }

        $result['action'] = 'add';
        $result['present_id']=$present_id;
        $result['title1']=$title1;
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/items_add')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $smfy=new SmfyQwt();
        if($admin->yzaccess_token){
            //echo $admin->yzaccess_token."<br>";
            $result['yz']=1;
            $client = new YZTokenClient($admin->yzaccess_token);
            $result['yzcoupons']=$smfy->getyzcoupons($bid,$client);
            $result['yzgifts']=$smfy->getyzgifts($bid,$client);
        }
        if($admin->appid){
            $result['wx']=1;
            $result['wxcards']=$smfy->getwxcards($bid);
        }
        $item = ORM::factory('qwt_xdbitem', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('qwt_xdbchange')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('qwtxdba/items');
            }else{
                $result['error'] = '已经被兑换过的奖品不能删除，您可以设置为隐藏';
            }
        }

        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信卡券列表';
                }
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==6){
                if(!$_POST['yzgift']){
                    $result['error'] = '未拉取到有赞赠品列表';
                }
                $_POST['data']['url']=$_POST['yzgift'];
            }elseif ($_POST['data']['type']==5) {
                if(!$_POST['yzcoupons']){
                    $result['error'] = '未拉取到有赞优惠券列表';
                }
                $_POST['data']['url']=$_POST['yzcoupons'];
            }
            $item->values($_POST['data']);
            $item->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交（请在基础设置-微信参数，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';

            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                $tmpfile = $_FILES['pic']['tmp_name'];
                // echo $_FILES['pic']['size'];
                if ($_FILES['pic']['size'] > 1024*300) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
                // echo $result['error'];
                // exit;
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "xdb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('qwtxdba/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        // echo "<pre>";
        // var_dump($_POST['data']);
        // var_dump($result);
        // echo "</pre>";
        // exit();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/items_add')
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }
     public function action_items_delete($id){
        $value =ORM::factory('qwt_xdbitem')->where('bid','=',$this->bid)->where('id','=',$id)->find()->value;
        $type =ORM::factory('qwt_xdbitem')->where('bid','=',$this->bid)->where('id','=',$id)->find()->key;
        $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_xdbitems` where `bid` = $this->bid and `id` = $id");
        $sql->execute();
        Request::instance()->redirect('qwtxdba/items');
    }
      //积分奖品管理
    public function action_tasks($action='', $id=0) {
        if ($action == 'add') return $this->action_tasks_add();
        if ($action == 'edit') return $this->action_tasks_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);
        $tasks=ORM::factory('qwt_xdbtask')->where('bid', '=', $bid);
        $tasks = $tasks->reset(FALSE);
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            $tid=$_GET['tid'];
            $task=ORM::factory('qwt_xdbtask')->where('id','=',$tid)->find();
            $begin=$task->begintime;
            $task->endtime=$begin;
            $task->save();
        }
        if ($_GET['s']) {
            $tasks = $tasks->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $tasks = $tasks->where('name', 'like', $s);
            $tasks = $tasks->and_where_close();
        }
        // echo $bid."<br>";
        $result['countall'] = $tasks->count_all();
        // var_dump($result);
        // exit();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/xdb/pages');

        $result['tasks'] = $tasks->order_by('endtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '任务管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/tasks')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }
    //奖品发送明细
    public function action_items_num($tid) {
        $bid = $this->bid;
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);
        $items_num = ORM::factory('qwt_xdbsku')->where('bid', '=', $bid)->where('tid', '=', $tid);
        $items_num = $items_num->reset(FALSE);
        $result['countall'] = $items_num->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $result['countall'],
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/xdb/pages');
        $result['tid_name'] = ORM::factory('qwt_xdbtask')->where('bid', '=', $bid)->where('id', '=', $tid)->find()->name;
        $result['items_num'] = $items_num->order_by('id', 'ASC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = $result['tid_name'].'的奖品发送情况';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/items_num')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }

    public function action_tasks_add() {

        $bid = $this->bid;
        echo $bid.'<br>';
        $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);

        if ($_POST['data']) {
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
            $task = ORM::factory('qwt_xdbtask');
            $task->values($_POST['data']);
            $task->bid = $bid;
            $past=ORM::factory('qwt_xdbtask')->where('bid','=',$bid)->where('endtime','>',time())->find();
            if($_POST['data']['begintime']>$_POST['data']['endtime']||$_POST['data']['endtime']<time()){
                $result['error'] = '时间设置不合理，请检查后再提交';
            }
            if(!$config['mgtpl']){
                $result['error'] = '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】';
            }
            if($past && $past->begintime!=$past->endtime ){
                $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
            }
            if (!$_POST['data']['name'] || !$_POST['data']['endtime']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $task->save();
                foreach ($_POST['goal'] as $k => $v) {
                    $sku = ORM::factory('qwt_xdbsku');
                    $sku->bid = $bid;
                    $sku->lv = $k;
                    $sku->tid = $task->id;
                    $sku->num = $v;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k];
                    $sku->text = $_POST['text'][$k];
                    $sku->save();
                }

                Request::instance()->redirect('qwtxdba/tasks');
            }
        }
        $items = ORM::factory('qwt_xdbitem')->where('bid','=',$bid)->find_all();
        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新任务';
        $result['text'] = '添加新任务';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/tasks_add')
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    // public function action_tasks_edit($id) {
    //     $bid = $this->bid;
    //     $config = ORM::factory('qwt_xdbcfg')->getCfg($bid,1);
    //     $task = ORM::factory('qwt_xdbtask', $id);
    //     if (!$task || $task->bid != $bid) die('404 Not Found!');
    //     if ($_POST['data']) {
    //         $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
    //         $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
    //         $task->values($_POST['data']);
    //         $task->bid = $bid;
    //         if($_POST['data']['begintime']>$_POST['data']['endtime']||$_POST['data']['endtime']<time()){
    //             $result['error'] = '时间设置不合理，请检查后再提交';
    //         }
    //         if(!$config['mgtpl']){
    //             $result['error'] = '请在【个性化设置】->【消息设置】里面配置【下发奖品时的消息模板ID】';
    //         }
    //         if($past && $past->begintime!=$past->endtime ){
    //             $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
    //         }
    //         if (!$_POST['data']['name'] || !$_POST['data']['endtime']) $result['error'] = '请填写完整后再提交';

    //         if (!$result['error']) {
    //             $task->save();
    //             // echo "<pre>";
    //             // var_dump($_POST['goal']);
    //             // echo "</pre>";

    //             foreach ($_POST['goal'] as $k => $v) {
    //                 $sku = $skus = ORM::factory('qwt_xdbsku')->where('bid', '=', $bid)->where('tid', '=', $id)->where('lv', '=', $k)->find();
    //                 $ordernum=ORM::factory('qwt_xdborder')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
    //                 $sku->bid = $bid;
    //                 $sku->tid = $task->id;
    //                 $sku->num = $v;
    //                 $sku->lv =$k;
    //                 $sku->iid = $_POST['prize'][$k];
    //                 $sku->stock = $_POST['stock'][$k]+$ordernum;
    //                 $sku->text = $_POST['text'][$k];
    //                 $sku->save();
    //                 $form = $k+1;
    //                 //echo 'form'.$form.'<br>';
    //             }
    //             $mysql = ORM::factory('qwt_xdbsku')->where('bid', '=', $this->bid)->where('tid', '=', $id)->count_all();
    //             //echo 'mysql'.$mysql.'<br>';
    //             //exit;
    //             if($mysql>$form){
    //                 for ($i=0; $i <$mysql-$form ; $i++) {
    //                     $result = DB::query(Database::DELETE,"DELETE  from qwt_xdbskus  where bid=$bid and tid =$id and lv= $form+$i")->execute();
    //                 }
    //             }
    //             Request::instance()->redirect('qwtxdba/tasks');
    //         }
    //     }

    //     $_POST['data'] = $result['task'] = $task->as_array();
    //     $result['action'] = 'edit';
    //     $items = ORM::factory('qwt_xdbitem')->where('bid','=',$bid)->find_all();
    //     $skus = ORM::factory('qwt_xdbsku')->where('bid','=',$bid)->where('tid','=',$id)->find_all();
    //     $result['title'] = $this->template->title = '修改任务';
    //     $result['text'] = '保存';
    //     $this->template->father = View::factory('weixin/qwt/tpl/atpl');
    //     $this->template->content = View::factory('weixin/qwt/admin/xdb/tasks_add')
    //         ->bind('skus', $skus)
    //         ->bind('items', $items)
    //         ->bind('result', $result)
    //         ->bind('config', $config);
    // }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_xdb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        $this->config=ORM::factory('qwt_xdbcfg')->getCfg($this->bid,1);
        if($this->config['totle']!=date('Y-m-d',time())){
            if($this->config['totle']){
                $time_totle=strtotime($this->config['totle']);
            }else{
                $time_totle=0;
            }
            $daytype='%Y-%m-%d';
            $length=10;
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`int_time`, '$daytype')as time FROM `qwt_xdbtrades` where bid=$this->bid and  int_time >= $time_totle ORDER BY `time` DESC ")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                $tradenums=DB::query(Database::SELECT,"SELECT count(id) as tradenum from qwt_xdbtrades where bid=$this->bid  and FROM_UNIXTIME(`int_time`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['tradenum']=$tradenums[0]['tradenum'];
                //产生海报数
                $ordernums=DB::query(Database::SELECT,"SELECT count(id) as ordernum from qwt_xdbchanges where bid=$this->bid  and FROM_UNIXTIME(`createdtime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['ordernum']=$ordernums[0]['ordernum'];
                //参加活动人数
                $duihuansums=DB::query(Database::SELECT,"SELECT SUM(num) as scoresum from qwt_xdbscores where bid=$this->bid and num>0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['duihuansum']=$duihuansums[0]['scoresum'];
                //奖品兑换数量
                $moneysums= DB::query(Database::SELECT,"SELECT SUM(money) as moneysum FROM `qwt_xdbtrades` where bid =$this->bid and FROM_UNIXTIME(`int_time`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['moneysum']=$moneysums[0]['moneysum'];
            }
            foreach ($newadd as $key => $value) {
                $totle=ORM::factory('qwt_xdbtotle')->where('bid','=',$this->bid)->where('time_quantum','=',$value['time'])->find();
                $totle->bid=$this->bid;
                $totle->tradenum=$value['tradenum'];
                $totle->ordernum=$value['ordernum'];
                $totle->duihuansum=$value['duihuansum'];
                $totle->moneysum=$value['moneysum'];
                $totle->timestamp=strtotime($value['time']);
                $totle->time_quantum=$value['time'];
                $totle->save();
            }
            $ok=ORM::factory('qwt_xdbcfg')->setCfg($this->bid,'totle',date('Y-m-d',time()));
            $this->config=ORM::factory('qwt_xdbcfg')->getCfg($this->bid,1);
        }else{
            $time_today=strtotime(date('Y-m-d',time()));
            $tradenum=ORM::factory('qwt_xdbtrade')->where('bid','=',$this->bid)->where('int_time','>=',$time_today)->count_all();
            $ordernum=ORM::factory('qwt_xdbchange')->where('bid','=',$this->bid)->where('createdtime','>=',$time_today)->count_all();
            $duihuansum=ORM::factory('qwt_xdbscore')->select(array('SUM("num")','duihuansum'))->where('bid','=',$this->bid)->where('num','>',0)->where('lastupdate','>=',$time_today)->find()->duihuansum;
            $moneysum=ORM::factory('qwt_xdbtrade')->select(array('SUM("money")','moneysum'))->where('bid','=',$this->bid)->where('int_time','>=',$time_today)->find()->moneysum;
            if($fnum>0||$tnum>0||$qnum>0||$onum>0){
                $totle=ORM::factory('qwt_xdbtotle')->where('bid','=',$this->bid)->where('time_quantum','=',date('Y-m-d',time()))->find();
                $totle->bid=$this->bid;
                $totle->tradenum=$tradenum;
                $totle->time_quantum=date('Y-m-d',time());
                $totle->timestamp=strtotime(date('Y-m-d',time()));
                $totle->ordernum=$ordernum;
                $totle->duihuansum=$duihuansum;
                $totle->moneysum=$moneysum;
                $totle->save();
            }
        }
        if($_GET['qid']==3||$action=='shaixuan')
        {
            $status=3;
            $newadd=array();
            if($_GET['data']['begin']!=null&&$_GET['data']['over']!=null)//搜索
            {
                $begin=$_GET['data']['begin'];
                $over=$_GET['data']['over'];
               if(strtotime($begin)>strtotime($over))
               {
                 $begin=$_GET['data']['over'];
                 $over=$_GET['data']['begin'];
               }
               // echo $begin.$over;
               if(strtotime($begin)==strtotime($over))
               {
                 $newadd[0]['time']=$begin;
               }
               else
               {
                $newadd[0]['time']=$begin.'~'.$over;
               }
                //新增用户
                $tradenums=DB::query(Database::SELECT,"SELECT count(id) as tradenum from qwt_xdbtrades where bid=$this->bid  and FROM_UNIXTIME(`int_time`, '$daytype')>='$begin' and FROM_UNIXTIME(`int_time`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['tradenum']=$tradenums[0]['tradenum'];
                //产生海报数
                $ordernums=DB::query(Database::SELECT,"SELECT count(id) as ordernum from qwt_xdbchanges where bid=$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')>='$begin' and FROM_UNIXTIME(`createdtime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['ordernum']=$ordernums[0]['ordernum'];
                $duihuansums=DB::query(Database::SELECT,"SELECT SUM(num) as duihuansum from qwt_xdbscores where bid=$this->bid and num >0 and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over'")->execute()->as_array();
                $newadd[0]['duihuansum']=$duihuansums[0]['duihuansum'];
                //奖品兑换数量
                $moneysums= DB::query(Database::SELECT,"SELECT SUM(money) as moneysum FROM `qwt_xdbtrades` where bid =$this->bid and FROM_UNIXTIME(`int_time`, '$daytype')>='$begin' and FROM_UNIXTIME(`int_time`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['moneysum']=$moneysums[0]['moneysum'];
            }
        }
        else
        {
            if($_GET['qid']==2||$action=='month')//按月统计
            {
                $daytype='%Y-%m';
                $length=7;
                $status=2;
            }
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_xdbtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qwt/admin/xdb/pages');
            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_xdbtotles` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $tradenums=DB::query(Database::SELECT,"SELECT sum(tradenum) as tradenums from qwt_xdbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['tradenum']=$tradenums[0]['tradenums'];
                //产生海报数
                $ordernums=DB::query(Database::SELECT,"SELECT sum(ordernum) as ordernums from qwt_xdbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernum']=$ordernums[0]['ordernums'];
                //参加活动人数
                $duihuansums=DB::query(Database::SELECT,"SELECT sum(duihuansum) as duihuansums from qwt_xdbtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['duihuansum']=$duihuansums[0]['duihuansums'];
                //奖品兑换数量
                $moneysums= DB::query(Database::SELECT,"SELECT sum(moneysum) as moneysums FROM `qwt_xdbtotles` where bid =$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['moneysum']=$moneysums[0]['moneysums'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '%Y-%m-%d')as time FROM `qwt_xdbtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
        $num=count($duringdata);
        if(strtotime($duringdata[0]['time'])<strtotime($duringdata[$num-1]['time']))
        {
        $duringtime['begin']=$duringdata[0]['time'];
        echo $duringtime['begin']."pppp";
        $duringtime['over']=$duringdata[$num-1]['time'];
        }
        else
        {
        $duringtime['begin']=$duringdata[$num-1]['time'];
        $duringtime['over']=$duringdata[0]['time'];
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xdb/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
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
