<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qda extends Controller_Base {

    public $template = 'weixin/qd/tpl/atpl';
    public $pagesize = 20;

    public function before() {
        Database::$default = "qd";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action != 'login' && $_SESSION['qda']['success']!='ok') {
            header('location:/qda/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function action_index() {
        $this->action_login();
    }
    //兑换管理
    public function action_orders($action='', $id=0) {
        $order = ORM::factory('qd_order');
        //$order = $order->reset(FALSE);
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $title = array('id', '收货人', '收货电话', '收货地址', '兑换产品','任务数', '备注','订单时间','OpenID');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $orders= ORM::factory('qd_order')->find_all();
            foreach ($orders as $o) {
                //地址处理
                $array = array($o->id, $o->name, $o->tel, $o->address, $o->item->name,$o->score,$o->memo, date('Y-m-d H:i:s', $o->lastupdate), $o->user->openid);

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
        ))->render('weixin/qd/admin/pages');
        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/qd/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result);
    }

    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        $result['items'] = ORM::factory('qd_item')->find_all();
        $this->template->title = '奖品管理';
        $this->template->content = View::factory('weixin/qd/admin/items')
            ->bind('result', $result);
    }

    public function action_items_add() {
        if ($_POST['data']) {
            $item = ORM::factory('qd_item');
            $item->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['score'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0) {
                $tmpfile = $_FILES['pic']['tmp_name'];
                if ($_FILES['pic']['size'] > 1487*1487) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $mem->delete($key);
                Request::instance()->redirect('qda/items');
            }
        }
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->content = View::factory('weixin/qd/admin/items_add')
            ->bind('result', $result);
    }

    public function action_items_edit($id) {
        $item = ORM::factory('qd_item', $id);
        if (!$item) die('404 Not Found!');
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('qd_order')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('qda/items');
            }
        }
        if ($_POST['data']) {
            $item->values($_POST['data']);

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交';
            if ($_FILES['pic']['error'] == 0) {
                $tmpfile = $_FILES['pic']['tmp_name'];
                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->pic = file_get_contents($tmpfile);
                }
            }
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "qd:items";
                $mem->delete($key);
                Request::instance()->redirect('qda/items');
            }
        }
        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';
        $result['title'] = $this->template->title = '修改奖品';
        $this->template->content = View::factory('weixin/qd/admin/items_add')
            ->bind('result', $result);
    }
    public function action_login() {
        $this->template = 'weixin/qd/tpl/login';
        $this->before();
        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);
        if ($_POST['username'] && $_POST['password']) {
            if($_POST['username']=='www'&&$_POST['password']=='www'){
                $_SESSION['qda']['success'] = 'ok';
            }else {
                $this->template->error = '天王盖地虎';
            }
        }
        if ($_SESSION['qda']['success']=='ok') {
            if (!$_GET['from']) $_GET['from'] = 'orders';
            header('location:/qda/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['qda'] = null;
        header('location:/qda/orders');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qd_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
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
