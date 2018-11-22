<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwthfca extends Controller_Base {

    public $template = 'weixin/qwt/tpl/hfcatpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',22)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    public function action_qrcodes(){
        $bid = $this->bid;
        // $this->config=$config=ORM::factory('')
        $qrcodes=ORM::factory('qwt_hfcqrcode')->where('bid','=',$bid);
        $qrcodes=$qrcodes->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcodes = $qrcodes->where('nickName', 'like', $s);
        }
        $result['countall'] = $countall = $qrcodes->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/pages');
        if ($result['sort']) $qrcodes = $qrcodes->order_by($result['sort'], 'DESC');
        $result['qrcode']=$qrcodes->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/qrcodes')
            ->bind('bid',$bid)
            ->bind('pages',$pages)
            ->bind('result',$result);
    }
    public function action_groups(){
        $bid = $this->bid;
        $group = ORM::factory('qwt_hfcteam')->where('bid','=',$bid)->find_all();
        //按团长昵称，商品名称搜索
        if ($_GET['s']) {

        }
        // foreach ($group as $key => $v) {
        //     echo $v->id;
        // }
        // exit();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/group')
            ->bind('group',$group);
    }
    public function action_items($action='', $id=0){
        $bid = $this->bid;
        if ($action=='add') return $this->action_items_add();
        if ($action=='edit') return $this->action_items_edit($id);
        $item = ORM::factory('qwt_hfcitem')->where('bid','=',$bid)->where('show','=',1)->order_by('pri','DESC')->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/items')
            ->bind('item',$item);
    }
    public function action_group_orders($type=''){
        $bid=$this->bid;
        if ($type=='done') {
            $title = '已处理订单';
            $result['status'] = 1;
        }else{
            $title = '未处理订单';
            $result['status'] = 0;
        }
        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('qwt_hfctrade')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->state = 1;
                $order->save();
            }
            $result['ok'] = "共批量处理 $i 个订单。";
        }
        $team=ORM::factory('qwt_hfcteam')->where('bid','=',$bid)->where('state','=',1)->find_all();
        $teams[]=0;
        foreach ($team as $k => $v) {
            $teams[] = $v->id;
        }
        $trades = ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('state','=',$result['status'])->where('teamid','IN',$teams)->where('status','=',1)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/group_orders')
            ->bind('trades',$trades)
            ->bind('title',$title)
            ->bind('result',$result);
    }
    public function action_orders(){
        $bid = $this->bid;

        $team=ORM::factory('qwt_hfcteam')->where('bid','=',$bid)->where('state','=',0)->find_all();
        $teams[]=0;
        foreach ($team as $k => $v) {
            $teams[] = $v->id;
        }
        $trades = ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('teamid','IN',$teams)->where('status','=',1)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/orders')
            ->bind('trades',$trades)
            ->bind('title',$title)
            ->bind('result',$result);
    }
    public function action_items_add(){
        $bid = $this->bid;
        $result['action']='添加新商品';
        if($_POST['form']){
            $test = ORM::factory('qwt_hfcitem')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find();
            if ($test->id) {
                $result['err3']="已有该名称的商品，请重新编辑";
            }else{
                $item = ORM::factory('qwt_hfcitem');
                $item->bid = $bid;
                $item->name = $_POST['form']['name'];
                $item->pri = $_POST['form']['pri'];
                $item->timeouttype = $_POST['form']['timeouttype'];
                $item->groupnum = $_POST['form']['groupnum'];
                $item->old_price = $_POST['form']['old_price'];
                $item->price = $_POST['form']['price'];
                if ($item->timeouttype==1) {
                    if( $_POST['form']['timeout1']){
                        $item->timeout = $_POST['form']['timeout1']*24*3600;
                    }else{
                        $result['err3'] = '截止时间不能为空';
                    }
                }else{
                    if($_POST['form']['timeout0']){
                        $item->timeout = strtotime($_POST['form']['timeout0']);
                    }else{
                        $result['err3'] = '截止时间不能为空';
                    }

                }
                if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                    if ($_FILES['pic']['size'] > 1024*400) {
                        $result['err3'] = '图片不能超过 400K';
                    } else {
                        $item->pic = file_get_contents($_FILES['pic']['tmp_name']);
                    }
                }
                if (!$result['err3']) {
                    $item->save();
                    foreach ($_POST['pri'] as $k => $v) {
                        $img = ORM::factory('qwt_hfcdesc');
                        if ($_FILES['pic'.$v]['tmp_name']) {
                            $img->bid = $bid;
                            $img->pri = $v;
                            $img->pic = file_get_contents($_FILES['pic'.$v]['tmp_name']);
                            $img->iid = $item->id;
                            $img->save();
                        }else{
                            $result['err3'] = '第'.$v.'张描述图未上传';
                        }
                    }
                    if (!$result['err3']) {
                        Request::instance()->redirect('qwthfca/items');
                    }
                }
            }
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/items_add')
            ->bind('result',$result);
    }
    public function action_itemsdelete($id){
        $bid = $this->bid;
        $itemnum=ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('iid','=',$id)->count_all();
        $item=ORM::factory('qwt_hfcitem')->where('id','=',$id)->find();
        if($itemnum>0){
            $item->show=0;
            $item->save();
        }else{
            $item->delete();
        }
        Request::instance()->redirect('qwthfca/items');
    }
    public function action_orderdone($id){
        $bid = $this->bid;
        $order=ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('id','=',$id)->find();
        $order->state = 1;
        $order->save();
        Request::instance()->redirect('qwthfca/group_orders');
    }
    public function action_items_edit($id){
        $bid = $this->bid;
        $result['action']='修改商品';
        $item=ORM::factory('qwt_hfcitem')->where('id','=',$id)->find();
        $imgs = ORM::factory('qwt_hfcdesc')->where('bid','=',$bid)->where('iid','=',$id)->find_all();
        if($_POST['form']){
            $item->bid = $bid;
            $item->name = $_POST['form']['name'];
            $item->pri = $_POST['form']['pri'];
            $item->timeouttype = $_POST['form']['timeouttype'];
            $item->groupnum = $_POST['form']['groupnum'];
            $item->old_price = $_POST['form']['old_price'];
            $item->price = $_POST['form']['price'];
            if ($item->timeouttype==1) {
                if( $_POST['form']['timeout1']){
                    $item->timeout = $_POST['form']['timeout1']*24*3600;
                }else{
                    $result['err3'] = '截止时间不能为空';
                }
            }else{
                if($_POST['form']['timeout0']){
                    $item->timeout = strtotime($_POST['form']['timeout0']);
                }else{
                    $result['err3'] = '截止时间不能为空';
                }

            }
            //$qrfile = DOCROOT."qwt/hfc/tmp/tpl.$bid.jpg";
            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                if ($_FILES['pic']['size'] > 1024*400) {
                    $result['err3'] = '海报模板文件不能超过 400K';
                } else {
                    $item->pic = file_get_contents($_FILES['pic']['tmp_name']);
                    //@unlink($qrfile);
                    //move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                }
            }
            if (!$result['err3']) {
                $item->save();
                $count = count($_POST['pri']);
                $imgs = ORM::factory('qwt_hfcdesc')->where('bid','=',$bid)->where('iid','=',$id)->where('pri','>',$count);
                $imgs->delete_all();
                foreach ($_POST['pri'] as $k => $v) {
                    $img = ORM::factory('qwt_hfcdesc');
                    if ($_FILES['pic'.$v]['tmp_name']) {
                        $img = ORM::factory('qwt_hfcdesc')->where('bid','=',$bid)->where('iid','=',$id)->where('pri','=',$v)->find();
                        if ($img->id) {
                            $img->pic = file_get_contents($_FILES['pic'.$v]['tmp_name']);
                            $img->save();
                        }else{
                            $img = ORM::factory('qwt_hfcdesc');
                            $img->bid = $bid;
                            $img->pri = $v;
                            $img->pic = file_get_contents($_FILES['pic'.$v]['tmp_name']);
                            $img->iid = $item->id;
                            $img->save();
                        }
                    }
                }
                if (!$result['err3']) {
                    Request::instance()->redirect('qwthfca/items');
                }
            }
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/items_add')
            ->bind('imgs',$imgs)
            ->bind('item',$item)
            ->bind('result',$result);
    }
    public function action_faqs($action='', $id=0){
        $bid = $this->bid;
        if ($action=='add') return $this->action_faqs_add();
        if ($action=='edit') return $this->action_faqs_edit($id);
        $faqs = ORM::factory('qwt_hfcfaq')->where('bid', '=', $bid);
        $faqs = $faqs->reset(FALSE);
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/hfc/pages');

        if ($_GET['t']) {
            $faqs = $faqs->where('tid','=',$_GET['t']);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $faqs = $faqs->where('name', 'like', $s)->or_where('title', 'like', $s); //->or_where('openid', 'like', $s);
        }
        if ($_GET['delete']) {
            $delete = ORM::factory('qwt_hfcfaq')->where('id','=',$_GET['id'])->find();
            $delete->delete();
        }
        $faq = $faqs->limit($this->pagesize)->offset($offset)->order_by('createtime','desc')->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/faqs')
            ->bind('faq',$faq);
    }
    public function action_faqs_edit($id){
        $bid = $this->bid;
        $result['action']='修改问题';
        $faq = ORM::factory('qwt_hfcfaq')->where('id','=',$id)->find();
        if($_POST['form']){
            // echo '<pre>';
            // var_dump($_POST['form']);
            // exit;
            $test = ORM::factory('qwt_hfcfaq')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find();
            if ($test->id&&$test->id!=$faq->id) {
                $result['err3']="已有该名称的问题，请重新编辑";
            }else{
                // $faq = ORM::factory('qwt_hfcfaq');
                // $faq->bid = $bid;
                $faq->name = $_POST['form']['name'];
                $faq->tid = $_POST['form']['type'];
                $faq->title = $_POST['form']['question'];
                $faq->comment = $_POST['form']['comment'];
                $faq->save();
                Request::instance()->redirect('qwthfca/faqs');
            }
        }
        $type = ORM::factory('qwt_hfctype')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hfc/faqs_add')
            ->bind('faq',$faq)
            ->bind('type',$type)
            ->bind('result',$result);
    }
    //产品图片
    public function action_images($type='msg', $id=1, $cksum='') {
        $field = 'img';
        $table = "qwt_hfc$type";
        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');
        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
