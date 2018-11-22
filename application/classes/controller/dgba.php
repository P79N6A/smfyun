<?php defined('SYSPATH') or die('No direct script access.');

class Controller_dgba extends Controller_Base {

    public $template = 'weixin/dgb/tpl/atpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "wdy";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['dgba']['bid'];
        $this->config = $_SESSION['dgba']['config'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/dgba/login');
            header('location:/dgba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $this->template->config = $this->config;
        }
        @View::bind_global('bid', $this->bid);
        parent::after();
    }
    public function action_index() {
        $this->action_login();
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        $result['sort'] = 'jointime';
        if($_GET['delete']){
            $id=$_GET['delete'];
            ORM::factory('dgb_qrcode')->where('id','=',$id)->find()->delete();
        }
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        //修改用户
        $qrcodes=ORM::factory('dgb_qrcode')->where('bid', '=', $bid)->find_all();
        $time=mktime(0,0,0,date('m'),1,date('Y'));
        if($qrcodes){
            foreach ($qrcodes as $k => $v) {
                // $userobj->money = $result['money'] = $userobj->details->select(array('SUM("cash")', 'total_score'))->where('cash', '>', 0)->find()->total_score;
                $all_money=ORM::factory('dgb_order')->select(array('SUM("payment")', 'all_money'))->where('bid','=',$bid)->where('qid','=',$v->id)->find()->all_money;
                $month_money=ORM::factory('dgb_order')->select(array('SUM("payment")', 'month_money'))->where('bid','=',$bid)->where('qid','=',$v->id)->where('createdtime','>=',$time)->find()->month_money;
                $most_money=ORM::factory('dgb_order')->where('bid','=',$bid)->where('qid','=',$v->id)->order_by('payment','DESC')->find()->payment;
                $v->all_money=$all_money;
                $v->month_money=$month_money;
                $v->most_money=$most_money;
                $v->save();
            }
        }
        $qrcode = ORM::factory('dgb_qrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('weixin_id', 'like', $s)->or_where('name', 'like', $s)->or_where('tel', 'like', $s)->where_close();
        }
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='客户列表';
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
                        ->setCellValue('A'.$num, '会员编号')
                        ->setCellValue('B'.$num, '微信昵称')
                        ->setCellValue('C'.$num, '微信号')
                        ->setCellValue('D'.$num, '姓名')
                        ->setCellValue('E'.$num, '电话')
                        ->setCellValue('F'.$num, '常用收货地址')
                        ->setCellValue('G'.$num, '累计消费金额')
                        ->setCellValue('H'.$num, '当月消费金额')
                        ->setCellValue('I'.$num, '单笔最高消费')
                        ->setCellValue('J'.$num, '已发货订单')
                        ->setCellValue('K'.$num, '待发货订单')
                        ->setCellValue('L'.$num, '已取消订单')
                        ->setCellValue('M'.$num, '备注');
            $qrcode=$qrcode->order_by($result['sort'], 'DESC')->find_all();
            foreach($qrcode as $k => $v){
                $num=$k+2;
                $has_send=ORM::factory('dgb_order')->where('qid','=',$v->id)->where('state','=',1)->count_all();
                $wait_send=ORM::factory('dgb_order')->where('qid','=',$v->id)->where('state','=',0)->count_all();
                $cancel_send=ORM::factory('dgb_order')->where('qid','=',$v->id)->where('state','=',2)->count_all();
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->No)
                            ->setCellValue('B'.$num, $v->nickname)
                            ->setCellValue('C'.$num, $v->weixin_id)
                            ->setCellValue('D'.$num, $v->name)
                            ->setCellValue('E'.$num, $v->tel)
                            ->setCellValue('F'.$num, $v->city.$v->address)
                            ->setCellValue('G'.$num, $v->all_money)
                            ->setCellValue('H'.$num, $v->month_money)
                            ->setCellValue('I'.$num, $v->most_money)
                            ->setCellValue('J'.$num, $has_send)
                            ->setCellValue('K'.$num, $wait_send)
                            ->setCellValue('L'.$num, $cancel_send)
                            ->setCellValue('M'.$num, $v->remark);
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
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dgb/admin/pages');
        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();
        $result['title']=$this->template->title = '客户列表';
        $this->template->content = View::factory('weixin/dgb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_qrcode_add() {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        if ($_POST['qrcode']){
            $item = ORM::factory('dgb_qrcode');
            $item->bid=$this->bid;
            $item->values($_POST['qrcode']);
            if (!$_POST['qrcode']['name']||!$_POST['qrcode']['tel']||!$_POST['qrcode']['city']) $result['error']['dgb'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "dgb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('dgba/qrcodes');
            }
        }
        $lastqrcode=ORM::factory('dgb_qrcode')->where('bid','=',$bid)->order_by('id','DESC')->find();
        $result['title'] = $this->template->title = '添加新客户';
        $this->template->content = View::factory('weixin/dgb/admin/qrcode_add')
            ->bind('result', $result)
            ->bind('lastqrcode', $lastqrcode)
            ->bind('bid', $bid)
            ->bind('config', $config);

    }
    public function action_qrcode_edit($qid) {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        $qrcode=ORM::factory('dgb_qrcode')->where('id','=',$qid)->find();
        if ($_POST['qrcode']){
            $qrcode->bid=$this->bid;
            $qrcode->values($_POST['qrcode']);
            if (!$_POST['qrcode']['name']||!$_POST['qrcode']['tel']||!$_POST['qrcode']['city']) $result['error']['dgb'] = '请填写完整后再提交';
            if (!$result['error']) {
                $qrcode->save();
                $mem = Cache::instance('memcache');
                $key = "dgb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('dgba/qrcodes');
            }
        }
        $_POST['qrcode']=$qrcode->as_array();
        $result['title'] = $this->template->title = '修改客户信息';
        $this->template->content = View::factory('weixin/dgb/admin/qrcode_add')
            ->bind('result', $result)
            ->bind('bid', $bid)
            ->bind('config', $config);

    }
    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid=$this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        if($_GET['delete']){
            $id=$_GET['delete'];
            ORM::factory('dgb_order')->where('id','=',$id)->find()->delete();
        }
        if($_GET['recover']){
            $id=$_GET['recover'];
            $thisorder=ORM::factory('dgb_order')->where('id','=',$id)->find();
            $thisorder->state=0;
            $thisorder->save();
        }
        if($_GET['cancel']){
            $id=$_GET['cancel'];
            $thisorder=ORM::factory('dgb_order')->where('id','=',$id)->find();
            $thisorder->state=2;
            $thisorder->save();
        }
        if($_POST['type']=='single'&&$_POST['tid']&&$_POST['postcode']){
            $thisorder=ORM::factory('dgb_order')->where('id','=',$_POST['tid'])->find();
            $thisorder->state=1;
            if($_POST['posttype']) $thisorder->shiptype=$_POST['posttype'];
            $thisorder->shipcode=$_POST['postcode'];
            $thisorder->save();
            echo 1;
            return;
        }
        if($_POST['type']=='cancelall'&&$_POST['id']){
            // echo '<pre>';
            // var_dump($_POST);
            // exit();
            kohana::$log->add('postcancel',print_r($_POST,true));
            foreach ($_POST['id'] as $k2 => $v2) {
                $thisorder2=ORM::factory('dgb_order')->where('id','=',$v2)->find();
                $thisorder2->state=2;
                $thisorder2->save();
            }
            echo 1;
            return;
        }
        if($_POST['type']=='sendall'&&$_POST['id']&&$_POST['postcode']){
            // echo '<pre>';
            // var_dump($_POST);
            // exit();
            kohana::$log->add('postsend',print_r($_POST,true));
            foreach ($_POST['id'] as $k1 => $v1) {
                kohana::$log->add('postsend1',print_r($v1,true));
                $thisorder1=ORM::factory('dgb_order')->where('id','=',$v1)->find();
                $thisorder1->state=1;
                if($_POST['posttype']){
                    $thisorder1->shiptype=$_POST['posttype'];
                } 
                $thisorder1->shipcode=$_POST['postcode'];
                $thisorder1->save();
            }
            echo 1;
            return;
        }
        $order = ORM::factory('dgb_order')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        $type=0;
        if($_GET['qid']){
            $order=$order->where('qid','=',$_GET['qid']);
        }
        if($_GET['type']){
            $type=$_GET['type'];
            if($type==1){
                $order=$order->where('state','=',0);
            }elseif($type==2){
                $order=$order->where('state','=',1);
            }elseif($type==3){
                $order=$order->where('state','=',2);
            }
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes=ORM::factory('dgb_qrcode')->where('bid','=',$bid)->where_open()->where('No','like',$s)->or_where('nickname','like',$s)->or_where('name','like',$s)->where_close()->find_all();
            $qr[]=0;
            foreach ($qrcodes as $key => $qrcode) {
                // echo $qrcode->nickname.'<br>';
                $qr[]=$qrcode->id;
            }
            // var_dump($qr);
            // exit();
            $order = $order->where('qid','IN',$qr);
        }
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='订单列表';
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
                        ->setCellValue('A'.$num, '订单编号')
                        ->setCellValue('B'.$num, '添加时间')
                        ->setCellValue('C'.$num, '客户（会员编号/微信昵称）')
                        ->setCellValue('D'.$num, '姓名')
                        ->setCellValue('E'.$num, '电话')
                        ->setCellValue('F'.$num, '收货地址')
                        ->setCellValue('G'.$num, '品牌')
                        ->setCellValue('H'.$num, '货号')
                        ->setCellValue('I'.$num, '单价/代购费（元/件）')
                        ->setCellValue('J'.$num, '件数')
                        ->setCellValue('K'.$num, '销售金额/代购费')
                        ->setCellValue('L'.$num, '状态')
                        ->setCellValue('M'.$num, '快递公司')
                        ->setCellValue('N'.$num, '快递单号')
                        ->setCellValue('O'.$num, '备注');
            $order=$order->find_all();
            foreach($order as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->tid)
                            ->setCellValue('B'.$num, date('Y-m-d H:i:s',$v->createdtime))
                            ->setCellValue('C'.$num, $v->qrcode->No.'/'.$v->qrcode->nickname)
                            ->setCellValue('D'.$num, $v->qrcode->name)
                            ->setCellValue('E'.$num, $v->qrcode->tel)
                            ->setCellValue('F'.$num, $v->qrcode->city.$v->qrcode->address)
                            ->setCellValue('G'.$num, $v->item->name)
                            ->setCellValue('H'.$num, $v->style_id)
                            ->setCellValue('I'.$num, $v->price.'/'.$v->fee)
                            ->setCellValue('J'.$num, $v->num)
                            ->setCellValue('K'.$num, ($v->price-$v->fee)*$v->num.'/'.$v->fee*$v->num)
                            ->setCellValue('L'.$num, $this->state($v->state))
                            ->setCellValue('M'.$num, $v->shiptype)
                            ->setCellValue('N'.$num, $v->shipcode)
                            ->setCellValue('O'.$num, $v->remark);
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
        $result['countall'] = $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dgb/admin/pages');
        $result['orders'] = $order->limit($this->pagesize)->offset($offset)->find_all();
        $result['title']=$this->template->title = '订单列表';
        $this->template->content = View::factory('weixin/dgb/admin/orders')
            ->bind('pages', $pages)
            ->bind('type', $type)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function state($state){
        if($state==0){
            return '待发货';
        }elseif ($state==1) {
            return '已发货';
        }elseif ($state==2) {
            return '已取消';
        }
    }
    public function action_order_add() {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        $lastorder=ORM::factory('dgb_order')->where('bid','=',$bid)->order_by('id','DESC')->find();
        if ($_POST['order']){
            $order = ORM::factory('dgb_order');
            $order->bid=$this->bid;
            $_POST['order']['tid']='E'.date('YmdHis',time()).($lastorder->id+1);
            $_POST['order']['payment']=($_POST['order']['price']+$_POST['order']['fee'])*$_POST['order']['num'];
            $order->values($_POST['order']);
            if (!$_POST['order']['price']||!$_POST['order']['num']) $result['error']['dgb'] = '请填写完整后再提交';
            if (!$_POST['order']['qid']) $result['error']['dgb'] = '用户为空';
            if (!$_POST['order']['iid']) $result['error']['dgb'] = '品牌为空';
            if (!$result['error']) {
                $order->save();
                $mem = Cache::instance('memcache');
                $key = "dgb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('dgba/orders');
            }
        }
        $qrcodes=ORM::factory('dgb_qrcode')->where('bid','=',$bid)->find_all();
        $items=ORM::factory('dgb_brand')->where('bid','=',$bid)->find_all();
        $result['title'] = $this->template->title = '添加新订单';
        $this->template->content = View::factory('weixin/dgb/admin/order_add')
            ->bind('result', $result)
            ->bind('qrcodes', $qrcodes)
            ->bind('items', $items)
            ->bind('bid', $bid)
            ->bind('config', $config);

    }
    public function action_order_edit($qid) {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        $order=ORM::factory('dgb_order')->where('id','=',$qid)->find();
        if ($_POST['order']){
            $order = ORM::factory('dgb_order');
            $order->bid=$this->bid;
            $_POST['order']['payment']=($_POST['order']['price']+$_POST['order']['fee'])*$_POST['order']['num'];
            $order->values($_POST['order']);
            if (!$_POST['order']['price']||!$_POST['order']['num']) $result['error']['dgb'] = '请填写完整后再提交';
            if (!$_POST['order']['qid']) $result['error']['dgb'] = '用户为空';
            if (!$_POST['order']['iid']) $result['error']['dgb'] = '品牌为空';
            if (!$result['error']) {
                $order->save();
                $mem = Cache::instance('memcache');
                $key = "dgb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('dgba/orders');
            }
        }
        $_POST['order']=$order->as_array();
        $qrcodes=ORM::factory('dgb_qrcode')->where('bid','=',$bid)->find_all();
        $items=ORM::factory('dgb_brand')->where('bid','=',$bid)->find_all();
        $result['title'] = $this->template->title = '修改订单信息';
        $this->template->content = View::factory('weixin/dgb/admin/order_add')
            ->bind('result', $result)
            ->bind('qrcodes', $qrcodes)
            ->bind('items', $items)
            ->bind('bid', $bid)
            ->bind('config', $config);

    }
    //积分奖品管理
    public function action_items($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        if($_GET['delete']){
            $id=$_GET['delete'];
            ORM::factory('dgb_brand')->where('id','=',$id)->find()->delete();
        }
        $item=ORM::factory('dgb_brand')->where('bid', '=', $bid);
        $item = $item->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $item = $item->where('name', 'like', $s);
        }
        $countall = $item->count_all();
         //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dgb/admin/pages');
        $result['items'] = $item->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['title']= $this->template->title = '品牌库';
        $this->template->content = View::factory('weixin/dgb/admin/items')
            ->bind('result', $result)
            ->bind('pages',$pages)
            ->bind('config', $config);
    }

    public function action_item_add() {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        if ($_POST['item']){
            $item = ORM::factory('dgb_brand');
            $item->bid=$this->bid;
            $item->values($_POST['item']);
            if (!$_POST['item']['name']) $result['error']['dgb'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "dgb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('dgba/items');
            }
        }
        $result['title'] = $this->template->title = '添加品牌';
        $this->template->content = View::factory('weixin/dgb/admin/item_add')
            ->bind('result', $result)
            ->bind('lastqrcode', $lastqrcode)
            ->bind('bid', $bid)
            ->bind('config', $config);

    }

    public function action_item_edit($iid) {
        $bid = $this->bid;
        $config = ORM::factory('dgb_cfg')->getCfg($bid);
        $item=ORM::factory('dgb_brand')->where('id','=',$iid)->find();
        if ($_POST['item']){
            $item->bid=$this->bid;
            $item->values($_POST['item']);
            if (!$_POST['item']['name']) $result['error']['dgb'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "dgb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('dgba/items');
            }
        }
        $_POST['item']=$item->as_array();
        $result['title'] = $this->template->title = '修改品牌';
        $this->template->content = View::factory('weixin/dgb/admin/item_add')
            ->bind('result', $result)
            ->bind('bid', $bid)
            ->bind('config', $config);
    }
    public function action_items_delete($id){
        $value =ORM::factory('dgb_item')->where('bid','=',$this->bid)->where('id','=',$id)->find()->value;
        $type =ORM::factory('dgb_item')->where('bid','=',$this->bid)->where('id','=',$id)->find()->key;
        $sql = DB::query(Database::DELETE,"DELETE FROM `dgb_items` where `bid` = $this->bid and `id` = $id");
        $sql->execute();
        Request::instance()->redirect('dgba/items');
    }


    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['dgba']['admin'] < 1) Request::instance()->redirect('dgba/qrcode_add');
        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('dgb_login');
        $logins = $logins->reset(FALSE);

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
        ))->render('weixin/dgb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/dgb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['dgba']['admin'] < 1) Request::instance()->redirect('dgba/qrcode_add');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('dgb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('dgb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('dgba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/dgb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['dgba']['admin'] < 1) Request::instance()->redirect('dgba/home');

        $bid = $this->bid;

        $login = ORM::factory('dgb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('dgb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('dgba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('dgb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                if ($_POST['data']['copyright']) {
                    $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
                }
                //appid 重置
                // if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                // }

                Request::instance()->redirect('dgba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/dgb/admin/logins_add')
            ->bind('result', $result)
            ->bind('bid', $id)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/dgb/tpl/login';
        $this->before();
        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);
        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('dgb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();
            if ($biz->id) {
                //从smfyun拉取
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                    $_SESSION['dgba']['bid'] = $biz->id;
                    $_SESSION['dgba']['user'] = $_POST['username'];
                    $_SESSION['dgba']['admin'] = $biz->admin; //超管
                    $_SESSION['dgba']['config'] = ORM::factory('dgb_cfg')->getCfg($biz->id);
                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }
        if ($_SESSION['dgba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'qrcode_add';
            header('location:/dgba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['dgba'] = null;
        header('location:/dgba/qrcode_add');
        exit;
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "dgb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
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
}
