<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Mdt extends Controller_Base {
    public $template = 'weixin/mdt/tpl/mftpl';

    public $config;
    public $openid;
    public $bid;
    public $uid;

    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();

        if (Request::instance()->action == 'images') return;

        $_SESSION =& Session::instance()->as_array();

/*
        if (!$_GET['openid']) {
            if (!$_SESSION['wdy']['bid']) die('Access Deined.');
            if (!$_SESSION['wdy']['openid']) die('Access Deined..');
        }

        $this->config = $_SESSION['wdy']['config'];
        $this->openid = $_SESSION['wdy']['openid'];
        $this->bid = $_SESSION['wdy']['bid'];
        $this->uid = $_SESSION['wdy']['uid'];

        if ($_GET['debug']) print_r($_SESSION['wdy']);
*/

    }

    public function after2() {
        // if (Request::instance()->action == 'images') return;

        $user = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $user['follows'] = ORM::factory('mdt_qrcode')->where('fopenid', '=', $user['openid'])->count_all();

        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }

    //入口
    public function action_index($tid=1, $bid=1, $view='list', $eq='>') {
        View::bind_global('tid', $tid);

        $view = "weixin/mdt/$view";
        $items = ORM::factory('mdt_item')->where('bid', '=', $bid)->where('tid', '=', $tid)->where('endtime', $eq, time())->order_by('click', 'DESC')->order_by('endtime', 'ASC')->find_all();

        $this->template->title = '首页';
        $this->template->content = View::factory($view)->bind('items', $items);
    }

    //秒杀
    public function action_miaosha($end=0) {
        $eq = '>';
        if ($end == 1) $eq = '<';
        return $this->action_index(3, 1, 'miaosha', $eq);
    }

    //跳转
    public function action_click($id) {
        $item = ORM::factory('mdt_item', $id);
        // print_r($item->as_array());
        if ($item && ($item->endtime > time() || $item->tid == 3)) {
            $item->click++;
            $item->save();

            Request::instance()->redirect($item->url);
        } else {
            die('商品非法！');
        }
    }

    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/wdy/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 10;

        $this->template->title = '积分排行榜';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('mdt_qrcode', $this->uid)->as_array();

        //飘飘管理员
        //if ($user['openid'] == 'oDB2TjizEcKT89gcaaSjI137TK1g') $top = 100;
        //if ($user['openid'] == 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4') $top = 100;

        $rankkey = "wdy:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "wdy:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->where('lock', '<>', 1)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }

    //我的积分
    public function action_score() {
        $view = "weixin/wdy/scores";

        $this->template->title = '我的积分';
        $this->template->content = View::factory($view)->bind('scores', $scores);

        //查询积分
        if (time() % 10 == 0) {
            $userobj = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
            $userobj->score = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
            $userobj->save();
            // echo "reCount score.";
        }

        $scores = ORM::factory('mdt_qrcode', $this->uid)->scores->order_by('lastupdate', 'DESC')->find_all();
    }

    //奖品列表
    public function action_items() {
        $mem = Cache::instance('memcache');
        $view = "weixin/wdy/items";

        $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('items', $items);

        $key = "wdy:items:{$this->bid}";
        $items = $mem->get($key);
        if (!$items) {
            $obj = ORM::factory('mdt_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
            foreach($obj as $i) $items[] = $i->as_array();
            $mem->set($key, $items, 600);
        }
    }

    //兑换表单
    public function action_neworder($iid) {
        $view = "weixin/wdy/neworder";

        $item = ORM::factory('mdt_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/wdy/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('mdt_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} 分，您只有 {$userobj->score} 分。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('mdt_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        if ($userobj->lock == 1) die($this->config['text_risk']);

        //3.判断是否刷单：超过 100 个推荐，没有一个下线，则判断为小号
        if ($this->config['risk_level1'] > 0 && $this->config['risk_level2'] > 0) {

            $count2 = ORM::factory('mdt_qrcode', $userobj->id)->scores->where('type', '=', 2)->count_all();
            //$count3 = ORM::factory('mdt_qrcode', $userobj->id)->scores->where('type', '=', 3)->count_all();
            //用是否生成海报判断下线数量
            $count3 = ORM::factory('mdt_qrcode')->where('bid', '=', $userobj->bid)->where('fopenid', '=', $userobj->openid)->where('ticket', '<>', '')->count_all();
            // echo "2:$count2, 3:$count3";

            if ($userobj->lock == 0 && $count2 >= $this->config['risk_level1'] & $count3 <= $this->config['risk_level2']) {
                $userobj->lock = 1;
                $userobj->save();

                if ($userobj->lock == 1) die('您的账号存在刷分现象，已被锁定。如果您确认是系统误判断，请联系客服解决。');
            }
        }

        $this->template->title = $item->name;

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url) ) {
            $order = ORM::factory('mdt_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;

            //虚拟产品
            if ($item->url) {
                $order->url = $item->url;
                $order->status = 1;
            } else {
                //省份 城市
                $order->city = $_POST['s_province'].' '.$_POST['s_city'].' '.$_POST['s_dist'];
            }

            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();

                //扣积分
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/wdy/orders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //自动填写旧地址
        $old_order = ORM::factory('mdt_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }

    //已兑换表单
    public function action_orders() {
        $view = "weixin/wdy/orders";

        $this->template->title = '我的订单';
        $this->template->content = View::factory($view)->bind('orders', $orders);
        $orders = ORM::factory('mdt_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "mdt_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
