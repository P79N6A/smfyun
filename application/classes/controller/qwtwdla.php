<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_qwtwdla extends Controller_Base {

    public $template = 'weixin/qwt/tpl/wdlatpl';
    public $pagesize = 20;
    public $yzaccess_token;
    public $config;
    public $bid;
    public $wx;
    public $methodVersion='3.0.0';
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
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',9)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    public function action_index() {
        $this->action_login();
    }
    public function action_hb_pay_list(){
        $countall = ORM::factory('qwt_hbyorder')->where('money','>',0)->where('state','=',1)->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');
        // $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $orders  = ORM::factory('qwt_hbyorder')->where('money','>',0)->where('state','=',1)->order_by('id','DESC')->limit($this->pagesize)->offset($offset)->find_all()->as_array();
        // var_dump($orders);
        // exit;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/hb_pay_list')
            ->bind('orders',$orders)
            ->bind('pages', $pages)
            ->bind('result', $result);
    }
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
        $type=0;
        if($_GET['type']){
            $type=$_GET['type'];
            if($type==1){
                $logins=$logins->where('flag','=',1);
            }elseif($type==2){
                $logins=$logins->where('flag','=',0);
            }
        }
       if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s']).'%';
            $logins=$logins->where_open();
            $logins = $logins->where('user', 'like', $s)->or_where('name', 'like', $s)->or_where('weixin_name','like',$s);
            $logins=$logins->where_close();
        }
        if($_GET['qid']){
            $logins=$logins->where('id','=',$_GET['qid']);
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
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/logins')
            ->bind('type',$type)
            ->bind('pages', $pages)
            ->bind('result', $result);
    }
    public function action_qrcode(){
        $bid =$this->bid;
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find()->admin;
        $logins = ORM::factory('qwt_login')->where('fubid','=',$bid);
        $logins->reset(FALSE);
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
        $result['customers'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['title'] = $this->template->title = '客户管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/qrcode')
            ->bind('bid',$bid)
            ->bind('pages', $pages)
            ->bind('result', $result);
    }
    public function action_speedy(){
        $biz = ORM::factory('qwt_login')->where('id', '=', $_GET['bid'])->find();
        if ($biz->id) {
            $_SESSION['qwta']['bid'] = $biz->id;
            $_SESSION['qwta']['user'] = $biz->user;
            $_SESSION['qwta']['admin'] = $biz->admin;
            $_SESSION['qwta']['dlflag'] = $biz->flag; //超管
        }
        if ($_SESSION['qwta']['bid']) {
            header('location:/qwta/userinfo');
            exit;
        }else{
            header('location:/qwtwdla/logins');
            exit;
        }
    }
    public function action_logins_add() {
        if ($_SESSION['qwta']['admin'] < 2) Request::instance()->redirect('qwta/userinfo');
        $bid = $this->bid;
        $lastcode=ORM::factory('qwt_login')->order_by('id','DESC')->find()->id;
        $itemsku=ORM::factory('qwt_sku')->where('show1','=',1);
        $itemsku=$itemsku->reset(FALSE);
        if ($_POST['data']) {
            $login = ORM::factory('qwt_login');
            $login->values($_POST['data']);
            if (!$_POST['pass'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';
            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                //代理商
                $id=$login->id;
                if($_POST['flag']==1){
                    $login->flag=1;
                    $login->dlname=$_POST['vpn1']['name'];
                    $login->save();
                    if($_POST['vpn']){
                        foreach ($_POST['vpn'] as $key => $sid) {
                            $sku=ORM::factory('qwt_sku')->where('id','=',$sid)->find();
                            $iid=$sku->iid;
                            $showname=$sku->item->name.':'.$sku->name;
                            $price=$_POST['pri'][$sid];
                            $dlsku[$key]="($id,$iid,$sid,'{$showname}',1,$sku->price,$price)";
                            // $dlsku[$key]['bid']=$bid;
                            // $dlsku[$key]['sid']=$sid;
                            // $dlsku[$key]['name']=$sku->item->name.':'.$sku->name;
                            // $dlsku[$key]['state']=1;
                            // $dlsku[$key]['old_price']=$sku->price;
                            // $dlsku[$key]['price']=$_POST['pri'][$sid];
                        }
                        // echo "<pre>";
                        // var_dump($dlsku);
                        // echo "</pre>";
                        $SQL = 'INSERT IGNORE INTO qwt_dlskus (`bid`,`iid`,`sid`,`name`,`state`,`old_price`,`price`) VALUES '. join(',', $dlsku);
                        // echo $SQL."<br>";
                        // exit();
                        $colum = DB::query(Database::INSERT,$SQL)->execute();
                    }
                }else{
                    $login->flag=0;
                    $login->save();

                }
                if($_POST['ifdiscount']){
                    ORM::factory('qwt_cfg')->setCfg($id,'ifdiscount',$_POST['ifdiscount']);
                    if($_POST['discount']){
                        ORM::factory('qwt_cfg')->setCfg($id,'discount',$_POST['discount']);
                    }
                }
                //修改商户权限
                $buserid = $login->id;
                foreach ($_POST['item'] as $k => $v) {
                    $buser = ORM::factory('qwt_buy');
                    $buser->bid = $buserid;
                    $buser->iid = $v;
                    $buser->buy_time = time();
                    $buser->status = 1;
                    $buser->lastupdate = time();
                    if($v==1||$v==14){
                        $buser->hbnum = $_POST['pro'][$k];
                        $buser->expiretime = time()+3600*24*30*12;
                    }else{
                        $buser->expiretime = strtotime($_POST['pro'][$k]);
                    }
                    $buser->save();
                }
                Request::instance()->redirect('qwtwdla/logins');
            }
        }
        $result['action'] = 'add';
        $items =  ORM::factory('qwt_item')->find_all();
        foreach ($items as $k => $v) {
            $sku[$k] = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$v->id)->find_all();
            foreach ($sku[$k] as $i => $n) {
                $guide[$k][$i]['title'] = $v->name;
                $guide[$k][$i]['sid'] = $n->id;
                $guide[$k][$i]['name'] = $n->name;
                $guide[$k][$i]['old_price'] = $n->price;
            }
        }
        $flag = $login->flag;
        $result['itemsku']=$itemsku->find_all();
        $result['title'] = $this->template->title = '添加用户';
        //$this->template->content = View::factory('weixin/qwt/admin/logins_add')
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/logins_add')
            ->bind('lastcode',$lastcode)
            ->bind('guide',$guide)
            ->bind('sku',$sku)
            ->bind('flag',$flag)
            ->bind('result', $result)
            ->bind('items', $items);
    }
    public function action_ddorder(){
        $bid = $this->bid;
        $ddorders=ORM::factory('qwt_rebuy')->where('status','=',1);
        $ddorders=$ddorders->reset(FALSE);
        if($_POST['form']['id']){
            $ddorder1=ORM::factory('qwt_rebuy')->where('id','=',$_POST['form']['id'])->find();
            $ddorder1->refund=$_POST['form']['refund'];
            $ddorder1->save();
            if($_POST['form']['refund']==1){
                ORM::factory('qwt_score')->where('rid','=',$_POST['form']['id'])->find()->delete();
                $buser = ORM::factory('qwt_buy')->where('id','=',$ddorder1->buy_id)->find();
                $sku = ORM::factory('qwt_sku')->where('id','=',$ddorder1->sku_id)->find();
                kohana::$log->add("1wdlbid$buser->bid",print_r($buser->bid,true));
                kohana::$log->add('1wdlbid1',print_r($sku->id,true));
                kohana::$log->add('1wdlbid2',print_r($sku->iid,true));
                kohana::$log->add('1wdlbid3',print_r($buser->id,true));
                kohana::$log->add('1wdlbid4',print_r($buser->expiretime,true));
                kohana::$log->add('1wdlbid5',print_r(time(),true));
                if($sku->iid==1||$sku->iid==14){//如果是红包
                    $buser->hbnum = $buser->hbnum-$sku->time;
                    if($buser->expiretime > time()+12*30*24*3600){
                       $buser->expiretime =$buser->expiretime-12*30*24*3600;
                    }else{
                        $buser->expiretime = time();
                    }
                    //$buser->expiretime=time();
                    // if($buser->expiretime<time()){
                    //     $buser->expiretime = time()+12*30*24*3600;
                    // }else{
                    //     $buser->expiretime = $buser->expiretime+12*30*24*3600;
                    // }
                }else{
                    if($sku->iid==11){//直播就赠送500g流量
                        $login = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
                        $login->stream_data = $login->stream_data-500;
                        $login->save();
                    }
                    if($buser->expiretime > time()+$sku->time*30*24*3600){
                       $buser->expiretime =$buser->expiretime-$sku->time*30*24*3600;
                    }else{
                        $buser->expiretime = time();
                    }
                    // if($buser->expiretime<time()){
                    //     $buser->expiretime = time()+$sku->time*30*24*3600;
                    // }else{
                    //     $buser->expiretime = $buser->expiretime+$sku->time*30*24*3600;
                     //}
                }
                $rebuy_num=ORM::factory('qwt_rebuy')->where('bid','=',$ddorder1->bid)->where('buy_id','=',$ddorder1->buy_id)->count_all();
                if($rebuy_num==1){
                    $buser->status=0;
                }
                $buser->save();
            }
            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';
            // exit();
        }
        if($_SESSION['qwta']['admin'] ==0){
            $scores=ORM::factory('qwt_score')->where('bid','=',$bid)->where('type','=',0)->find_all();
            foreach ($scores as $key => $score) {
                $rids[$key]=$score->rid;
            }
            if(!$rids){
                $rids=array(0 => 1);

            }
            $ddorders=$ddorders->where('id','IN',$rids);
        }
        $type=0;
        if($_GET['type']){
            $type=$_GET['type'];
            if($type==1){
                $scores=ORM::factory('qwt_score')->where('type','=',0)->find_all();
                foreach ($scores as $key => $score) {
                    $rids[$key]=$score->rid;
                }
                if(!$rids){
                    $rids=array(0 => 1);
                }
                $ddorders=$ddorders->where('id','IN',$rids);
            }elseif ($type==2) {
                $scores=ORM::factory('qwt_score')->where('type','=',0)->find_all();
                foreach ($scores as $key => $score) {
                    $rids[$key]=$score->rid;
                }
                if(!$rids){
                    $rids=array(0 => 1);
                }
                $ddorders=$ddorders->where('id','NOT IN',$rids);
            }
            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';
            // exit();
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $logins=ORM::factory('qwt_login')->where_open()->where('weixin_name','like',$s)->or_where('user','like',$s)->where_close()->find_all();
            $items=ORM::factory('qwt_item')->where('name','like',$s)->find_all();
            $sids[0]=0;
            $bids[0]=0;
            foreach ($items as $key => $item) {
                $key++;
                $skus=ORM::factory('qwt_sku')->where('iid','=',$item->id)->find_all();
                foreach ($skus as $key1 => $sku) {
                    $sids[$key.$key1]=$sku->id;
                }
            }
            foreach ($logins as $k => $login) {
                //$k++;
                $bids[$k]=$login->id;
            }
            // echo '<pre>';
            // var_dump($sids);
            // var_dump($bids);
            // echo '</pre>';
            // exit();
            $ddorders = $ddorders->where_open()->where('bid', 'IN', $bids)->or_where('sku_id','IN',$sids)->where_close();
        }
        if($_GET['cbid']){
            $scores=ORM::factory('qwt_score')->where('cbid','=',$customer->id)->find_all();
            foreach ($scores as $key => $score) {
                $rids[$key]=$score->rid;
            }
            if(!$rids){
                $rids=array(0 => 1);

            }
            $ddorders=$ddorders->where('id','IN',$rids);
        }
        if($_GET['sid']){
            //$rids= array();
            $scores=ORM::factory('qwt_score')->where('sid','=',$v->id)->find_all();
            foreach ($scores as $key => $score) {
                $rids[$key]=$score->rid;
            }
            if(!$rids){
                $rids=array(0 => 1);

            }
            $ddorders=$ddorders->where('id','IN',$rids);

        }
        if($_GET['qid']){
            //$rids= array();
            $scores=ORM::factory('qwt_score')->where('bid','=',$_GET['qid'])->where('type','=',0)->find_all();
            foreach ($scores as $key => $score) {
                $rids[$key]=$score->rid;
            }
            if(!$rids){
                $rids=array(0 => 1);

            }
            $ddorders=$ddorders->where('id','IN',$rids);
        }
        if($_GET['bid']){
            $ddorders=$ddorders->where('bid','=',$_GET['bid']);
        }
        if ($_GET['export']=='xls') {
            $ddorders=$ddorders->order_by('id', 'DESC');
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='奖励对账单';
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
                        ->setCellValue('A'.$num, '公众号名称')
                        ->setCellValue('B'.$num, '登录名')
                        ->setCellValue('C'.$num, '应用名称')
                        ->setCellValue('D'.$num, '应用规格')
                        ->setCellValue('E'.$num, '付款金额')
                        ->setCellValue('F'.$num, '付款时间')
                        ->setCellValue('G'.$num, '代理商佣金')
                        ->setCellValue('H'.$num, '来源');
            $ddorders=$ddorders->find_all();
            foreach($ddorders as $k => $v){

                $login=ORM::factory('qwt_login')->where('id','=',$v->bid)->find();
                $fulogin=ORM::factory('qwt_login')->where('id','=',$login->fubid)->find();
                if($login->fubid&&$fulogin->flag==1){
                    $source='经销商:'.$fulogin->memo.'/'.$fulogin->user;
                }else{
                    $source='营销应用平台';
                }
                $score=ORM::factory('qwt_score')->where('rid','=',$v->id)->find();
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $login->weixin_name)
                            ->setCellValue('B'.$num, $login->user)
                            ->setCellValue('C'.$num,$v->pro->item->name)
                            ->setCellValue('D'.$num, $v->pro->name)
                            ->setCellValue('E'.$num, number_format($v->rebuy_price,2))
                            ->setCellValue('F'.$num, date('Y-m-d H:i:s',$v->lastupdate))
                            ->setCellValue('G'.$num, number_format($score->score,2))
                            ->setCellValue('H'.$num, $source);
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
        $result['countall'] =$countall= $ddorders->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');
        $result['ddorders'] = $ddorders->order_by('lastupdate','DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/ddorders')
            ->bind('type',$type)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    public function action_setgoods(){
        $bid = $this->bid;
        $goods = ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('state','=',1);
        $goods=DB::query(Database::SELECT,"SELECT iid from qwt_dlskus where bid = $bid and state = 1")->execute()->as_array();
        foreach ($goods as $key => $good) {
            $iids[$key]=$good['iid'];
        }
        // echo "<pre>";
        // var_dump($iids);
        // echo "<pre>";
        // exit();
        $items=ORM::factory('qwt_item')->where('id','IN',$iids);
        $items = $items->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            //$goods=DB::query(Database::SELECT,"SELECT * from qwt_items where `name` like '$s' id IN (SELECT iid from qwt_skus where id IN (SELECT a.sid as id from(SELECT * from qwt_dlskus where bid = $bid and state = 1) a))")->execute()->as_array();
            $items = $items->where('name', 'like', $s);
        }
        $result['countall1']=ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('state','=',1)->where('iid','IN',$iids)->count_all();
        $result['countall'] = $countall = $items->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');

        $result['goods'] =$items->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $code = ORM::factory('qwt_login')->where('id','=',$bid)->find()->code;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
            $this->template->content = View::factory('weixin/qwt/admin/wdl/setgoods')
            ->bind('code',$code)
        ->bind('result',$result)
        ->bind('pages',$pages)
        ->bind('bid',$this->bid);

     }
    public function action_calculates(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $yzaccess_token=$this->yzaccess_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        $month = date("Y-m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        if ($_GET['data']['begin']) {
            $month= $_GET['data']['begin'];
        }else{
            $_GET['data']['begin']=$month;
        }
        //修改用户
        if ($_POST['form']['id']) {
            // echo "<pre>";
            // var_dump($_POST);
            // echo "</pre>";
            // exit();
            $id = $_POST['form']['id'];
            $time=$_POST['form']['time'];
            $money=$_POST['form']['money'];
            $qrcode_edit=ORM::factory('qwt_login')->where('id','=',$id)->find();
            // if ($money){

            // }
            ORM::factory('qwt_score')->scoreOut($qrcode_edit, 1, $money,'','','',$time);
            // if($_POST['form']['type']==1){
            //     $type=2;

            // }elseif($_POST['form']['type']==2){
            //     $type=3;
            //     if ($money){
            //         ORM::factory('qwt_score')->scoreOut($qrcode_edit, $type, $money,'','','',$time);
            //     }
            // }
            $qrcode_edit->save();
        }
        $qrcode = ORM::factory('qwt_login')->where('flag','=',1);
        $qrcode = $qrcode->reset(FALSE);
        if($_SESSION['qwta']['admin'] ==0){
            $qrcode=$qrcode->where('id','=',$bid);
        }
        if ($_GET['export']=='xls') {
            $qrcode->order_by('id', 'DESC');
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='奖励对账单';
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
                        ->setCellValue('A'.$num, '代理商名称')
                        ->setCellValue('B'.$num, '登录名')
                        ->setCellValue('C'.$num, '商户备注')
                        ->setCellValue('D'.$num, '当月佣金')
                        ->setCellValue('E'.$num, '已结算佣金')
                        ->setCellValue('F'.$num, '总佣金')
                        ->setCellValue('G'.$num, '是否结算');
            $qrcode1s=$qrcode->find_all();
            foreach($qrcode1s as $k => $v){
                $monthtype='%Y-%m';
                $month_money=DB::query(Database::SELECT,"SELECT SUM(score) as month_money from qwt_scores where bid=$v->id and type =0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                $month_money=$month_money[0]['month_money'];
                $all_money=DB::query(Database::SELECT,"SELECT SUM(score) as all_money from qwt_scores where bid=$v->id and type =0 ")->execute()->as_array();
                $all_money=$all_money[0]['all_money'];
                $gave_money=DB::query(Database::SELECT,"SELECT SUM(score) as gave_money from qwt_scores where bid=$v->id and type =1  ")->execute()->as_array();
                $gave_money=$gave_money[0]['gave_money'];
                $dai_money=DB::query(Database::SELECT,"SELECT SUM(score) as dai_money from qwt_scores where bid=$v->id ")->execute()->as_array();
                $dai_money=$dai_money[0]['dai_money'];
                $num=$k+2;
                // if($v->flag==0){
                //     $flag='未结算';
                // }elseif ($v->flag==1) {
                //     $flag='以结算';
                // }
                $score=ORM::factory('qwt_score')->where('bid','=',$v->id)->where('bz','=',$_GET['data']['begin'])->find();
                if($score->id){
                  $flag='已结算';
                }else{
                  $flag='未结算';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->dlname)
                            ->setCellValue('B'.$num, $v->user)
                            ->setCellValue('C'.$num, $v->memo)
                            ->setCellValue('D'.$num, number_format($month_money,2))
                            ->setCellValue('E'.$num, number_format(-$gave_money,2))
                            ->setCellValue('F'.$num, $all_money)
                            ->setCellValue('G'.$num, $flag);
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
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('shopname', 'like', $s)->or_where('dlname', 'like', $s)->or_where('user', 'like', $s)->or_where('memo', 'like', $s)->or_where('weixin_name', 'like', $s)->where_close();
        }
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');

        $result['qrcodes'] = $qrcode->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/calculate')
            ->bind('pages',$pages)
            ->bind('month',$month)
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid',$this->bid);

    }
    public function action_history_scores(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        // $type=array();
        // $type[1]=1;
        $scores = ORM::factory('qwt_score')->where('type','=',1)->where('score','!=','0.00');
        if($_SESSION['qwta']['admin'] ==0){
            $scores=$scores->where('bid','=',$bid);
        }
        if($_GET['qid']){
           $scores=$scores->where('bid','=',$_GET['qid']);
        }
        if($_GET['s']){
            $s = '%'.trim($_GET['s'].'%');
            $user = ORM::factory('qwt_login')->where_open()->where('dlname', 'like', $s)->or_where('memo', 'like', $s)->or_where('user', 'like', $s)->where_close()->find_all();
            $user_arr[0] = 0;//qid
            foreach ($user as $k => $v) {
                $k++;
                $user_arr[$k] = $v->id;//qid
            }
            $scores=$scores->where('bid','IN',$user_arr);
        }
            // if($_GET['s']['type']==1){
            //     $scores=$scores->where('type','IN', array(2,3));
            // }
            // if($_GET['s']['type']==2){
            //     $scores=$scores->where('type','IN', array(5,6));
            // }


        $scores = $scores->reset(FALSE);
        if($_GET['qid']){
            $scores=$scores->where('bid','=',$_GET['qid']);
        }
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='结算记录';
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
                        ->setCellValue('A'.$num, '代理商名称')
                        ->setCellValue('B'.$num, '登录名')
                        ->setCellValue('C'.$num, '备注')
                        ->setCellValue('D'.$num, '结算时间')
                        ->setCellValue('E'.$num, '结算金额');
            $score1s=$scores->order_by('lastupdate','DESC')->limit(400)->find_all();
            foreach($score1s as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->login->dlname)
                            ->setCellValue('B'.$num, $v->login->user)
                            ->setCellValue('C'.$num, $v->login->memo)
                            ->setCellValue('D'.$num, date('Y-m-d H:i:s',$v->lastupdate))
                            ->setCellValue('E'.$num, number_format(-$v->score,2));
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

        $result['countall'] = $countall = $scores->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');

        $result['scores'] = $scores->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/history_scores')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    public function action_clearoauth(){
        if($_POST['bid']){
            $login = ORM::factory('qwt_login')->where('id','=',$_POST['bid'])->find();
            $login->refresh_token = '';
            $login->save();
            $result['content'] = '成功清除微信授权！';
            echo json_encode($result);
            exit;
        }
    }
    public function action_logins_edit($id) {
        if ($_SESSION['qwta']['admin'] < 2) Request::instance()->redirect('qwta/userinfo');

        $bid = $this->bid;

        $login = ORM::factory('qwt_login', $id);
        if (!$login) die('404 Not Found!');

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            //if (ORM::factory('qwt_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
            //     $result['error'] = '该登录名已经存在';
            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                // $login->expiretime = strtotime($_POST['expiretime']);
                $login->save();
                //代理商
                if($_POST['flag']==1){
                    $login->flag=1;
                    $login->dlname=$_POST['vpn1']['name'];
                    $login->save();
                    //ORM::factory('qwt_dlsku')->where('bid','=',$bid)->delete_all();
                    $colum = DB::query(Database::UPDATE,"UPDATE qwt_dlskus set state =0 where bid = $id")->execute();
                    $dlsku1s=ORM::factory('qwt_dlsku')->where('bid','=',$id)->find_all();
                    if($_POST['vpn']){
                        foreach ($dlsku1s as $dlsku1) {
                            if(in_array($dlsku1->sid,$_POST['vpn'])){
                                echo $dlsku1->sid."<br>";
                                $dlsku1->state=1;
                                $dlsku1->save();
                            }
                        }
                        foreach ($_POST['vpn'] as $key => $sid) {
                            $sku=ORM::factory('qwt_sku')->where('id','=',$sid)->find();
                            $showname=$sku->item->name.':'.$sku->name;
                            $iid=$sku->iid;
                            $price=$_POST['pri'][$sid];
                            $dlsku[$key]="($id,$iid,$sid,'{$showname}',1,$sku->price,$price)";
                        }
                        $SQL = 'INSERT IGNORE INTO qwt_dlskus (`bid`,`iid`,`sid`,`name`,`state`,`old_price`,`price`) VALUES '. join(',', $dlsku);
                        // echo $SQL."<br>";
                        // exit();
                        $colum = DB::query(Database::INSERT,$SQL)->execute();
                    }
                }else{
                    $login->flag=0;
                    $login->save();
                }
                if($_POST['ifdiscount']){
                    ORM::factory('qwt_cfg')->setCfg($id,'ifdiscount',$_POST['ifdiscount']);
                    if($_POST['discount']){
                        ORM::factory('qwt_cfg')->setCfg($id,'discount',$_POST['discount']);
                    }
                }
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
                    if($v==1||$v==14){
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
                Request::instance()->redirect('qwtwdla/logins');
            }
        }
        //$result['data'] = $login->as_array();
        $items =  ORM::factory('qwt_item')->find_all();
        foreach ($items as $k => $v) {
            $sku[$k] = ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$v->id)->find_all();
            foreach ($sku[$k] as $i => $n) {
                $guide[$k][$i]['title'] = $v->name;
                $guide[$k][$i]['sid'] = $n->id;
                $guide[$k][$i]['name'] = $n->name;
                $guide[$k][$i]['old_price'] = $n->price;
            }
        }
        $_POST['vpn1']['name']=$login->dlname;
        $_POST['data'] = $result['login'] = $login->as_array();
        $flag = $login->flag;
        // echo "<pre>";
        // var_dump($_POST);
        // echo "<pre>";
        // exit();
        $result['action'] = 'edit';
        $config=ORM::factory('qwt_cfg')->getCfg($id,1);
        $result['title'] = $this->template->title = '修改用户';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/wdl/logins_add')
            ->bind('config',$config)
            ->bind('login',$login)
            ->bind('guide',$guide)
            ->bind('sku',$sku)
            ->bind('result', $result)
            ->bind('flag', $flag)
            ->bind('bid', $id)
            ->bind('items', $items);
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        Kohana::$log->add('bid:',print_r($bid, true));
        $config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
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
        if (!file_exists($rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
        }
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
        Kohana::$log->add('curl_post_ssl:',print_r($data, true));
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            Kohana::$log->add('curl_post_ssl_error:',print_r($error, true));
            //echo curl_error($ch);
            curl_close($ch);
            return false;
        }
    }
}
