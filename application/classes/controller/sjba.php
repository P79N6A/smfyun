
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sjba extends Controller_Base {
    public $template = 'weixin/sjb/tpl/atpl';
    public $pagesize = 20;
    public function before() {
        Database::$default = "wdy";
        $_SESSION =& Session::instance()->as_array();
        parent::before(); 
    }
    public function after() {
        @View::bind_global('bid', $this->bid);
        parent::after();
    }
    //兑换管理
    public function action_orders($action='', $id=0) {
        //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');
            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[8], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 8) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[7]);
                    $shipcode = iconv('gbk', 'utf-8', $data[8]);
                } else {
                    $shiptype = $data[7];
                    $shipcode = $data[8];
                }

                $order = ORM::factory('sjb_tid')->where('id', '=', $oid)->find();
                if ($order->status == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
                    $order->status = 1;
                    $order->shiptype = $shiptype;
                    $order->shipcode = $shipcode;
                    $order->save();
                    $i++;
                }
            }

            fclose($fh);
            $result['ok'] = "共批量发 $i 个订单";
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
                $order = ORM::factory('sjb_tid')->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                $order->save();
            }
            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        if ($action == 'ship' && $id) {
            // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            // $we = new Wechat($config);
            $order = ORM::factory('sjb_tid')->where('id', '=', $id)->find();
            if ($order->status == 0) {
                $order->status = 1;
                $order->save();
                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['sjba']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['sjba']['shipcode'] = $_REQUEST['shipcode'];
                    $order->shiptype = $_REQUEST['shiptype'];
                    $order->shipcode = $_REQUEST['shipcode'];
                    $order->save();
                    // //发微信消息给用户
                    // $shipmsg = "%s，您的积分兑换奖品已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收";
                    // $msg['msgtype'] = 'text';
                    // $msg['touser'] = $order->user->openid;
                    // $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    // $we->sendCustomMessage($msg);
                }
            }
        }
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($action == 'done') {
            $result['status'] = 1;
        }
        $order = ORM::factory('sjb_tid')->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
            $order = $order->and_where_close();
        }
        if ($_GET['openid']) {
            $result['openid'] = (int)$_GET['openid'];
            $order = $order->where('openid', '=', $result['openid']);
        }
        $active_type="total";
        $countall = $order->count_all();
        //下载
        if($_GET['export']=='csv'){
            $tempname="全部";
            $orders = $order->order_by($result['sort'], 'DESC')->limit(1000)->find_all();
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('id', '收货人', '收货电话','收货地址','金额','订单时间','OpenID','物流公司', '物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            foreach ($orders as $o) {
                //地址处理
                // list($prov, $city, $dist) = explode(' ', $o->city);
                $array = array($o->id, $o->name, $o->tel,  $o->address, $o->money,  date('Y-m-d H:i:s', $o->jointime), $o->openid);

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
        ))->render('weixin/sjb/admin/pages');
        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/sjb/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('activetype',$active_type);
    }
}
