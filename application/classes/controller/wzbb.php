<?php defined('SYSPATH') or die('No direct script access.');
//分销宝后台
class Controller_wzbb extends Controller_Base {
    public $template = 'weixin/wzb/tpl/fatpl';
    public $pagesize = 20;
    public $aid;
    public function before() {
        Database::$default = "wdy";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->aid = $_SESSION['wzba']['aid'];
        //未登录
        if (Request::instance()->action != 'admin' && !$this->aid) {
            // header('location:/wzba/login');
            header('location:/wzbb/admin?from='.Request::instance()->action);
            exit;
        }
    }
    public function after() {
        if($this->aid){
            $todo['flag']=2;
            $this->template->todo = $todo;
        }
        @View::bind_global('aid', $this->aid);
        parent::after();
    }
    public function action_index() {
        $this->action_login();
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);
        $biz=ORM::factory('wzb_admin')->where('id','=',$this->aid)->find();
        if($biz->admin==3){
            $logins = ORM::factory('wzb_login');
            $logins = $logins->reset(FALSE);
        }elseif($biz->admin==2){
            $logins = ORM::factory('wzb_login')->where('fadmin','=',$this->aid);
            $logins = $logins->reset(FALSE);
        }
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
        ))->render('weixin/wzb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $all=ORM::factory('wzb_login')->where('fadmin','=',$this->aid)->count_all();

        $number=$biz->number-$all;
        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/wzb/admin/logins')
            ->bind('biz',$biz)
            ->bind('number',$number)
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('wzbb/logins');
        $aid = $this->aid;
        $biz=ORM::factory('wzb_admin')->where('id','=',$aid)->find();
        $all=ORM::factory('wzb_login')->where('fadmin','=',$aid)->count_all();
        if ($_POST['data']) {
            $login = ORM::factory('wzb_login');
            $login->values($_POST['data']);
            // $time = time();
            // if($_POST['data']['guige']==1){
            //     $login->stream_data=100;
            //     $login->expiretime=date('Y',$time).'-'.(date('m',$time) + 1).'-'.date('d');
            // }elseif($_POST['data']['guige']==2){
            //     $login->stream_data=1000;
            //     $login->expiretime=date('Y',$time) + 1 .'-'.date('m-d');
            // }
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wzb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';
            if($biz->number-$all<1){
                $result['error'] = '您的开账号数量以达到上限';
            }
            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                $order = ORM::factory('wzb_order');
                $order->bid = $login->id;
                $order->time = time();
                $order->tid = 'E'.date('Ymdhis');
                $order->type = 'year';
                $order->title = '包年：神码云直播（手动）';
                $order->price = 720.00;
                $order->save();
                Request::instance()->redirect('wzbb/logins');
            }
        }
        $admins=ORM::factory('wzb_admin')->where('fadmin','=',$aid)->find_all();
        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/wzb/admin/logins_add')
            ->bind('biz',$biz)
            ->bind('admins',$admins)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_logins_edit($id) {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('wzbb/logins');
        $stream_data=0;
        $stream_data=ORM::factory('wzb_login')->where('id','=',$id)->find()->stream_data;
        $aid = $this->aid;
        $biz=ORM::factory('wzb_admin')->where('id','=',$aid)->find();
        $login = ORM::factory('wzb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('wzb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('wzbb/admins');
        }
        if ($_POST['data']) {
            $_POST['data']['stream_data']=$_POST['data']['stream_data']+$stream_data;
            $login->values($_POST['data']);
            // $time = $login->creatdate;
            // if($_POST['data']['guige']==1){
            //     $login->stream_data=100;
            //     $login->expiretime=date('Y',$time).'-'.date('m',$time) + 1 .'-'.date('d');
            // }elseif($_POST['data']['guige']==2){
            //     $login->stream_data=1000;
            //     $login->expiretime=date('Y',$time) + 1 .'-'.date('m-d');
            // }
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wzb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                if ($_POST['data']['copyright']) {
                    $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
                }
                //appid 重置
                if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                }

                Request::instance()->redirect('wzbb/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';
        $admins=ORM::factory('wzb_admin')->where('fadmin','=',$aid)->find_all();
        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/wzb/admin/logins_add')
            ->bind('biz', $biz)
             ->bind('admins', $admins)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //管理员管理
    public function action_admins($action='', $id=0) {
        if ($action == 'add') return $this->action_admins_add();
        if ($action == 'edit') return $this->action_admins_edit($id);
        $logins = ORM::factory('wzb_admin')->where('fadmin','=',$this->aid);
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
        ))->render('weixin/wzb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '代理商管理';
        $this->template->content = View::factory('weixin/wzb/admin/admins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_admins_add() {
        $aid = $this->aid;
        $biz=ORM::factory('wzb_admin')->where('id','=',$aid)->find();
        if ($_POST['data']) {
            $login = ORM::factory('wzb_admin');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wzb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';
            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('wzbb/admins');
            }
        }
        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加代理商';
        $this->template->content = View::factory('weixin/wzb/admin/admins_add')
            ->bind('biz',$biz)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_admins_edit($id) {
        $aid = $this->aid;
        $biz=ORM::factory('wzb_admin')->where('id','=',$aid)->find();
        $chbiz=ORM::factory('wzb_admin')->where('id','=',$id)->find();
        $login = ORM::factory('wzb_admin', $id);
        if (!$login) die('404 Not Found!');
        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('wzbb/admins');
        }
        if ($_POST['data']) {
            $all=ORM::factory('wzb_login')->where('fadmin','=',$id)->count_all();
            $_POST['data']['number']=$_POST['data']['number']+$all;
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wzb_admin')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';
            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('wzbb/admins');
            }
        }
        $_POST['data'] = $result['login'] = $login->as_array();
        $result['action'] = 'edit';
        $result['title'] = $this->template->title = '修改代理商';
        $this->template->content = View::factory('weixin/wzb/admin/admins_add')
            ->bind('chbiz',$chbiz)
            ->bind('biz',$biz)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_sales(){
        $aid = $this->aid;
        $admin= ORM::factory('wzb_admin')->where('id','=',$aid)->find()->admin;
        $biz=ORM::factory('wzb_admin')->where('id','=',$aid)->find();
        $chadmins=ORM::factory('wzb_admin')->where('fadmin','=',$biz->id)->find_all();
        if($biz->admin==3){
            $firstchild=ORM::factory('wzb_login')->find_all();
        }else{
            $firstchild=ORM::factory('wzb_login')->where('fadmin','=',$biz->id)->find_all();  
        }
        if($_GET['aid']){
            if($admin==3){
                $admins=ORM::factory('wzb_admin')->where('fadmin','=',$_GET['aid'])->find_all();
                $i=0;
                $tempid=array('0' =>'!!!');
                foreach ($admins as $m) {
                    $tempid[$i]=$m->id;
                    $i++;
                }
                Array_push($tempid,$_GET['aid']);
                $firstchild=ORM::factory('wzb_login')->where('faid','IN',$tempid)->find_all();
            }else{
                $firstchild=ORM::factory('wzb_login')->where('faid','=',$_GET['aid'])->find_all();
            }
        }
        if($_POST['aid']&&$_POST['zaid']=='select'){
            if($admin==3){
                $admins=ORM::factory('wzb_admin')->where('fadmin','=',$_POST['aid'])->find_all();
                $i=0;
                $tempid=array('0' =>'!!!');
                foreach ($admins as $m) {
                    $tempid[$i]=$m->id;
                    $i++;
                }
                Array_push($tempid,$_POST['aid']);
                $firstchild=ORM::factory('wzb_login')->where('faid','IN',$tempid)->find_all();
            }else{
                $firstchild=ORM::factory('wzb_login')->where('faid','=',$_POST['aid'])->find_all();
            }
        }
        if($_POST['zaid']&&$_POST['zaid']!='select'){
            $firstchild=ORM::factory('wzb_login')->where('faid','=',$_POST['zaid'])->find_all();
        }
        $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
        $z=0;
        foreach ($firstchild as $child) {
            $tempiid[$z]=$child->id;
            $z++;
        }
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');
        $orders = ORM::factory('wzb_order')->where('bid', 'IN',$tempiid);
        $orders=$orders->reset(false);
        if($_GET['bid']){
            $orders = ORM::factory('wzb_order')->where('bid', '=',$_GET['bid']);
             $orders=$orders->reset(false);
        }
        if($_POST['type']){
            $orders = $orders->where('type','=',$_POST['type']);
        }
        if($_POST['begin']!=null&&$_POST['over']!=null){
            $begin=$_POST['begin'];
            $over=$_POST['over'];
           if(strtotime($begin)>strtotime($over)){
                $begin=$_POST['over'];
                $over=$_POST['begin'];
           }
           $beg=strtotime($begin);
           $ov=strtotime($over);
           $orders = $orders->where('time','>=',$beg)->where('time','<=',$ov);
        }
        $count=$orders->count_all();

        $result['orders']=$orders->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $sum=0;
        foreach ($result['orders'] as $order) {
            $sum+=$order->price;
        }
        $this->template->content = View::factory('weixin/wzb/admin/sales')
            ->bind('sum',$sum)
            ->bind('chadmins',$chadmins)
            ->bind('count', $count)
            ->bind('pages', $pages)
            ->bind('biz',$biz)
            ->bind('orders', $result['orders']);
    }
    public function action_admin() {
        $this->template = 'weixin/wzb/tpl/admin';
        $this->before();
        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);
        if ($_POST['username'] && $_POST['password']) {
            // echo "<pre>";
            // var_dump($_POST);
            // echo "</pre>";
            //exit;
            $biz = ORM::factory('wzb_admin')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();
            // echo $biz->id."<br>";
            // echo $biz->admin."<br>";
            // exit();
            if ($biz->id&&$biz->admin>1) {
                //判断账号是否到期
                $_SESSION['wzba']['aid'] = $biz->id;
                $_SESSION['wzba']['user'] = $_POST['username'];
                $_SESSION['wzba']['admin'] = $biz->admin; //超管
                $biz->lastlogin = time();
                $biz->logins++;
                $biz->save();
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }
        if ($_SESSION['wzba']['aid']) {
            if (!$_GET['from']) $_GET['from'] = 'admins';
            header('location:/wzbb/'.$_GET['from']);
            exit;
        }
    }
    public function action_logout() {
        $_SESSION['wzba'] = null;
        header('location:/wzbb/admin');
        exit;
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
