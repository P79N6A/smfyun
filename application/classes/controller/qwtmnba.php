<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtmnba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/mnbatpl';
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
    public function action_home(){
        $bid = $this->bid;

        $config = ORM::factory('qwt_mnbcfg')->getCfg($bid,1);
        if ($_POST['text']) {
            $config = ORM::factory('qwt_mnbcfg')->setCfg($bid,'max_send',$_POST['text']['max_send']);
            $config = ORM::factory('qwt_mnbcfg')->getCfg($bid,1);
            $result['ok3'] = 1;
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/home')
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid', $bid);
    }
    public function action_csv(){
        $bid = $this->bid;
        if ($_FILES['pic']) {
            $tmp_file = $_FILES ['pic']['tmp_name'];
            if($_FILES['pic']['error']==1){
                echo '文件过大，请删除多余行数之后再重新上传';
                exit;
            }
            $file_types = explode ( ".", $_FILES ['pic']['name'] );
            $file_type = $file_types [count ( $file_types ) - 1];
             /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls"){
               echo '不是Excel文件，重新上传';
               exit;
            }else{
                require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                $aaa = $tmp_file;
                $PHPExcel = $reader->load($aaa); // 载入excel文件
                $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                echo $highestRow.'<br>';
                $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                echo $highestColumm.'<br>';
                // /** 循环读取每个单元格的数据 */
                for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
                    for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                        $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                    }
                }
                echo '<pre>';
                var_dump($dataset);
                echo '</pre>';
                // exit;
                foreach ( $dataset as $data ) {
                    $km =ORM::factory('qwt_mnbqrcode')->where('bid','=',$this->bid)->where('tel','=',$data["E"])->find();
                    if($data['E']&&$data['C']&&$data['F'])
                    $km->bid = $this->bid;
                    $km->tel = (string)$data['E'];
                    $km->password = '123456';
                    $km->name = (string)$data['C'];
                    $km->pcode = $data['D']?$data['D']:'';
                    $km->wx_username = (string)$data['F'];
                    // A 无相关ID
                    // B 代理等级
                    // C 代理名称
                    // D 代理授权码
                    // E 代理手机号
                    // F 代理微信昵称
                    $lv = ORM::factory('qwt_mnblv')->where('bid','=',$this->bid)->where('lv','=',$data['B'])->find();
                    $km->lid = $lv->id?$lv->id:0;
                    $km->save();
                }
            }
        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/tbt/home')
            ->bind('bid',$bid);
    }
    public function action_groups($action='', $id=0){
        $bid = $this->bid;
        if ($action=='add') return $this->action_groups_add();
        if ($action=='edit') return $this->action_groups_edit($id);
        if ($_POST['delete']) {
            $test = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('lid','=',$_POST['delete'])->find();
            if ($test->id) {
                $result['err']='还有用户处于该分组中，不能删除！';
            }else{
                $delete = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->where('id','=',$_POST['delete'])->find();
                $delete->delete();
            }
        }
        $lv = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/group')
            ->bind('result',$result)
            ->bind('lv',$lv);
    }
    public function action_groups_add(){
        $bid = $this->bid;
        $result['action']='添加代理等级';
        if ($_POST['form']) {
            $test = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->where('lv','=',$_POST['form']['name'])->find();
            if ($test->id) {
                $result['err3']='已存在该名字的代理等级，请重新编辑';
            }else{
                $group = ORM::factory('qwt_mnblv');
                $group->bid = $bid;
                $group->lv = $_POST['form']['name'];
                $group->save();
                Request::instance()->redirect('qwtmnba/groups');
            }
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/group_add')
            ->bind('result',$result);
    }
    public function action_groups_edit($lid){
        $bid = $this->bid;
        $result['action']='编辑代理等级';
        if ($_POST['form']) {
            $test = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->where('lv','=',$_POST['form']['name'])->find();
            if ($test->id==$lid||!$test->id) {
                $group = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->where('id','=',$lid)->find();
                $group->lv = $_POST['form']['name'];
                $group->save();
                Request::instance()->redirect('qwtmnba/groups');
            }else{
                $result['err3']='已存在该名字的代理等级，请重新编辑';
            }
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/group_add')
            ->bind('result',$result);
    }
    public function action_qrcodes($action='', $id=0){
        $bid = $this->bid;
        if ($action=='add') return $this->action_qrcodes_add();
        if ($action=='edit') return $this->action_qrcodes_edit($id);

        if ($_POST['change']) {
            $qid = $_POST['change']['id'];
            $tlid = $_POST['change']['lv'];
            $user = ORM::factory('qwt_mnbqrcode')->where('id','=',$qid)->find();
            $user->lid = $tlid;
            $user->save();
        }
        $qrcode1 = ORM::factory('qwt_mnbqrcode')->where('bid', '=', $bid);
        $qrcode1 = $qrcode1->reset(FALSE);
        // $user = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->limit($this->pagesize)->offset($offset)->find_all();

        if ($_GET['t']) {
            $qrcode1 = $qrcode1->where('lid','=',$_GET['t']);
        }
        if ($_GET['status']) {
            $result['status'] = $_GET['status'];
            if ($_GET['status']=='all') {
                $qrcode1 = $qrcode1;
            }elseif($_GET['status']=='zero'){
                $result['status'] = 'zero';
                $qrcode1 = $qrcode1->where('status','=',0);
            }else{
                $qrcode1 = $qrcode1->where('status','=',$_GET['status']);
            }
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode1 = $qrcode1->where('nickname', 'like', $s)->or_where('tel', 'like', $s)->or_where('name','like',$s); //->or_where('openid', 'like', $s);
        }
        //分页
        $num = $qrcode1->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/mnb/pages');

        $user = $qrcode1->limit($this->pagesize)->offset($offset)->order_by('lastupdate','desc')->find_all();
        $lv = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/qrcodes')
            ->bind('result',$result)
            ->bind('pages',$pages)
            ->bind('lv',$lv)
            ->bind('user',$user);
    }
    public function action_qrcodes_add(){
        $bid = $this->bid;
        $result['action']='添加新代理';
        if ($_POST['form']) {
            $find = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('tel','=',$_POST['form']['tel'])->find();
            if($find->id){
                $result['err3'] = '该手机号已经存在了！';
            }else{
                $user = ORM::factory('qwt_mnbqrcode');
                $user->bid = $bid;
                $user->tel = $_POST['form']['tel'];
                $user->lid = $_POST['form']['lv'];
                $user->pcode = $_POST['form']['pcode'];
                $user->name = $_POST['form']['name'];
                $user->password = $_POST['form']['password'];
                $user->save();
                Request::instance()->redirect('qwtmnba/qrcodes');
            }
        }
        $lv = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/qrcodes_add')
            ->bind('lv',$lv)
            ->bind('result',$result);
    }
    public function action_qrcodes_edit($id){
        $bid = $this->bid;
        $result['action']='修改代理';
        $user = ORM::factory('qwt_mnbqrcode')->where('id','=',$id)->find();
        if ($_POST['form']) {
            // $user = ORM::factory('qwt_mnbqrcode');
            // $user->bid = $bid;
            $find = ORM::factory('qwt_mnbqrcode')->where('bid','=',$bid)->where('tel','=',$_POST['form']['tel'])->find();
            if(!$find->id||$find->id==$user->id){
                $user->tel = $_POST['form']['tel'];
                $user->lid = $_POST['form']['lv'];
                $user->pcode = $_POST['form']['pcode'];
                $user->name = $_POST['form']['name'];
                $user->fpcode = $_POST['form']['fpcode'];
                $user->fname = $_POST['form']['fname'];
                $user->wx_username = $_POST['form']['wx_username'];
                $user->password = $_POST['form']['password'];
                if ($_POST['check']) {
                    if ($_POST['check']['pass']==1) {
                        $user->openid = NULL;
                        $user->nickname = '';
                        $user->wx_username = '';
                        $user->fpcode = '';
                        $user->fopenid = '';
                        $user->fname = '';
                        $user->headimgurl = '';
                        $user->status = 0;
                    }elseif($_POST['check']['pass']==0){
                        $user->status = 3;
                    }
                }
                $user->save();
                Request::instance()->redirect('qwtmnba/qrcodes');
            }else{
                $result['err3'] = '该手机号已经存在了！';
            }
        }
        $lv = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/qrcodes_add')
            ->bind('lv',$lv)
            ->bind('user',$user)
            ->bind('result',$result);
    }
    public function action_types($action='', $id=0){
        $bid = $this->bid;
        if ($action=='add') return $this->action_types_add();
        if ($action=='edit') return $this->action_types_edit($id);
        $type = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->find_all();
        foreach ($type as $k => $v) {
            $arr = explode(',', $v->auth);

            foreach ($arr as $key => $value) {
                $lv = ORM::factory('qwt_mnblv')->where('id','=',$value)->find();
                if($key == 0){
                    $str = $lv->lv;
                }else{
                    $str = $str.','.$lv->lv;
                }
            }
            $auth[$k] = $str;
        }
        if ($_POST['delete']) {
            $test = ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('tid','=',$_POST['delete'])->find();
            if ($test->id) {
                $result['err']='还有问题处于该分类中，不能删除！';
            }else{
                $delete = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->where('id','=',$_POST['delete'])->find();
                $delete->delete();
            }
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/types')
            ->bind('auth',$auth)
            ->bind('type',$type);
    }
    public function action_types_add(){
        $bid = $this->bid;
        $result['action']='添加问题分类';
        if ($_POST['form']) {
            $test = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find();
            if ($test->id) {
                $result['err3']='已有该名称的分类，请重新编辑';
            }else{
                $type = ORM::factory('qwt_mnbtype');
                $type->bid = $bid;
                $type->name = $_POST['form']['name'];

                foreach ($_POST['form']['lv'] as $k => $v) {
                    if($v>0){
                        if($k == 0 ){
                            $str = $v;
                        }else{
                            if($_POST['form']['lv'][$k-1]>0){
                                $str = $str.','.$v;
                            }else{
                                if(strlen($str)>0){
                                    $str = $str.','.$v;
                                }else{
                                    $str = $v;
                                }
                            }
                        }
                    }
                    // echo $str.'<br>';
                }
                $type->auth = $str;
                $type->save();
                // $tid = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find()->id;
                // foreach ($_POST['form']['lv'] as $k => $v) {
                //     $bind = ORM::factory('qwt_mnbbind');
                //     $bind->bid = $bid;
                //     $bind->tid = $tid;
                //     $bind->lid = $v;
                //     $bind->save();
                // }
                Request::instance()->redirect('qwtmnba/types');
            }
        }

        $lv = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/types_add')
            ->bind('lv',$lv)
            ->bind('result',$result);
    }
    public function action_types_edit($tid){
        $bid = $this->bid;
        $result['action']='添加问题分类';
        $type = ORM::factory('qwt_mnbtype')->where('id','=',$tid)->find();
        if ($_POST['form']) {
            $test = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find();
            if ($test->id&&$test->id!=$type->id) {
                $result['err3']='已有该名称的分类，请重新编辑';
            }else{
                // $type = ORM::factory('qwt_mnbtype');
                // $type->bid = $bid;
                $type->name = $_POST['form']['name'];

                foreach ($_POST['form']['lv'] as $k => $v) {
                    if($v>0){
                        if($k == 0 ){
                            $str = $v;
                        }else{
                            if($_POST['form']['lv'][$k-1]>0){
                                $str = $str.','.$v;
                            }else{
                                if(strlen($str)>0){
                                    $str = $str.','.$v;
                                }else{
                                    $str = $v;
                                }
                            }
                        }
                    }
                    // echo $str.'<br>';
                }
                // exit;
                $type->auth = $str;
                $type->save();
                // $tid = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find()->id;
                // foreach ($_POST['form']['lv'] as $k => $v) {
                //     $bind = ORM::factory('qwt_mnbbind');
                //     $bind->bid = $bid;
                //     $bind->tid = $tid;
                //     $bind->lid = $v;
                //     $bind->save();
                // }
                Request::instance()->redirect('qwtmnba/types');
            }
        }
        $lv = ORM::factory('qwt_mnblv')->where('bid','=',$bid)->find_all();
        $auth = explode(',', $type->auth);

        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/types_add')
            ->bind('lv',$lv)
            ->bind('auth',$auth)
            ->bind('type',$type)
            ->bind('result',$result);
    }
    public function action_faqs($action='', $id=0){
        $bid = $this->bid;
        if ($action=='add') return $this->action_faqs_add();
        if ($action=='edit') return $this->action_faqs_edit($id);
        $faqs = ORM::factory('qwt_mnbfaq')->where('bid', '=', $bid);
        $faqs = $faqs->reset(FALSE);
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/mnb/pages');

        if ($_GET['t']) {
            $faqs = $faqs->where('tid','=',$_GET['t']);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $faqs = $faqs->where('name', 'like', $s)->or_where('title', 'like', $s); //->or_where('openid', 'like', $s);
        }
        if ($_GET['delete']) {
            $delete = ORM::factory('qwt_mnbfaq')->where('id','=',$_GET['id'])->find();
            $delete->delete();
        }
        $faq = $faqs->limit($this->pagesize)->offset($offset)->order_by('createtime','desc')->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/faqs')
            ->bind('faq',$faq);
    }
    public function action_faqs_add(){
        $bid = $this->bid;
        $result['action']='添加新问题';
        if($_POST['form']){
            // echo '<pre>';
            // var_dump($_POST['form']);
            // exit;
            $test = ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find();
            if ($test->id) {
                $result['err3']="已有该名称的问题，请重新编辑";
            }else{
                $faq = ORM::factory('qwt_mnbfaq');
                $faq->bid = $bid;
                $faq->name = $_POST['form']['name'];
                $faq->tid = $_POST['form']['type'];
                $faq->title = $_POST['form']['question'];
                $faq->comment = $_POST['form']['comment'];
                $faq->save();
                Request::instance()->redirect('qwtmnba/faqs');
            }
        }
        $type = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/faqs_add')
            ->bind('type',$type)
            ->bind('result',$result);
    }
    public function action_faqs_edit($id){
        $bid = $this->bid;
        $result['action']='修改问题';
        $faq = ORM::factory('qwt_mnbfaq')->where('id','=',$id)->find();
        if($_POST['form']){
            // echo '<pre>';
            // var_dump($_POST['form']);
            // exit;
            $test = ORM::factory('qwt_mnbfaq')->where('bid','=',$bid)->where('name','=',$_POST['form']['name'])->find();
            if ($test->id&&$test->id!=$faq->id) {
                $result['err3']="已有该名称的问题，请重新编辑";
            }else{
                // $faq = ORM::factory('qwt_mnbfaq');
                // $faq->bid = $bid;
                $faq->name = $_POST['form']['name'];
                $faq->tid = $_POST['form']['type'];
                $faq->title = $_POST['form']['question'];
                $faq->comment = $_POST['form']['comment'];
                $faq->save();
                Request::instance()->redirect('qwtmnba/faqs');
            }
        }
        $type = ORM::factory('qwt_mnbtype')->where('bid','=',$bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/mnb/faqs_add')
            ->bind('faq',$faq)
            ->bind('type',$type)
            ->bind('result',$result);
    }
    //产品图片
    public function action_images($type='msg', $id=1, $cksum='') {
        $field = 'img';
        $table = "qwt_mnb$type";

        $pic = ORM::factory($table, $id)->img;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
