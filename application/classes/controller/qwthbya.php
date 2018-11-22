<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwthbya extends Controller_Base {

    public $template = 'weixin/qwt/tpl/hbyatpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'notify_qr') return;
        $this->bid = $_SESSION['qwta']['bid'];
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
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_hbycfg')->getCfg($bid, 1);
        //文案配置
        if ($_POST['cus']) {
            $cfg = ORM::factory('qwt_hbycfg');

            foreach ($_POST['cus'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }
            $config = ORM::factory('qwt_hbycfg')->getCfg($bid, 1);
            //背景图片
            if ($_FILES['bgpic']['error'] == 0||$_FILES['bgpic']['error'] ==2) {
                // $bgpic = DOCROOT."qwt/hby/tmp/bgpic.$bid.jpg";
                if ($_FILES['bgpic']['size'] > 1024*400) {
                    $result['err3'] = '背景图片文件不能超过 400K';
                } else {
                    $result['ok2']++;
                    $cfg->setCfg($bid, 'bgpic', '', @file_get_contents($_FILES['bgpic']['tmp_name']));
                    // @unlink($bgpic);
                    // move_uploaded_file($_FILES['bgpic']['tmp_name'], $bgpic);
                }
            }
            if ($_FILES['logo']['error'] == 0||$_FILES['logo']['error'] ==2) {
                // $logo = DOCROOT."qwt/hby/tmp/logo.$bid.jpg";
                if ($_FILES['logo']['size'] > 1024*200) {
                    $result['err3'] = '品牌logo文件不能超过 200K';
                } else {
                    $result['ok2']++;
                    $cfg->setCfg($bid, 'logo', '', @file_get_contents($_FILES['logo']['tmp_name']));
                    // @unlink($logo);
                    // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                }
            }
            if ($_FILES['sharelogo']['error'] == 0||$_FILES['sharelogo']['error'] ==2) {
                // $logo = DOCROOT."qwt/hby/tmp/logo.$bid.jpg";
                if ($_FILES['sharelogo']['size'] > 1024*200) {
                    $result['err3'] = '分享图标文件不能超过 200K';
                } else {
                    $result['ok2']++;
                    $cfg->setCfg($bid, 'sharelogo', '', @file_get_contents($_FILES['sharelogo']['tmp_name']));
                    // @unlink($logo);
                    // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                }
            }
        }
        //红包
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        $lastupdate=ORM::factory('qwt_hbykl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',14)->find()->lastupdate;//rebuy_time是时间戳
        $hb_cron = ORM::factory('qwt_hbycron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
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
        //当前用户脚本id
        $now_cron = ORM::factory('qwt_hbycron')->where('bid','=',$this->bid)->where('state','=',1)->where('has_qr','=',0)->order_by('id','desc')->find();
        //计算耗时
        $crons = ORM::factory('qwt_hbycron')->where('id','<=',$now_cron->id)->where('state','=',1)->where('has_qr','=',0)->find_all();
        $time = 0;
        foreach ($crons as $k => $v) {
            $time = $time + round(($v->num-$v->loop*3000)/3000)*5+5;
        }
        $result['time'] = $time;
        $result['cron'] = ORM::factory('qwt_hbycron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
        $result['bgpic'] = ORM::factory('qwt_hbycfg')->where('bid', '=', $this->bid)->where('key', '=', 'bgpic')->find()->id;
        $result['logo'] = ORM::factory('qwt_hbycfg')->where('bid', '=', $this->bid)->where('key', '=', 'logo')->find()->id;
        $result['sharelogo'] = ORM::factory('qwt_hbycfg')->where('bid', '=', $this->bid)->where('key', '=', 'sharelogo')->find()->id;
        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('oauth', $oauth)
            ->bind('left',$left)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }
    public function action_hbdownload(){
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_hbycfg')->getCfg($bid, 1);
        //红包
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        $lastupdate=ORM::factory('qwt_hbykl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',14)->find()->lastupdate;//rebuy_time是时间戳
        $hb_cron = ORM::factory('qwt_hbycron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
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
        //当前用户脚本id
        $now_cron = ORM::factory('qwt_hbycron')->where('bid','=',$this->bid)->where('state','=',1)->where('has_qr','=',0)->order_by('id','desc')->find();
        //计算耗时
        $crons = ORM::factory('qwt_hbycron')->where('id','<=',$now_cron->id)->where('state','=',1)->where('has_qr','=',0)->find_all();
        $time = 0;
        foreach ($crons as $k => $v) {
            $time = $time + round(($v->num-$v->loop*3000)/3000)*5+5;
        }
        $result['time'] = $time;
        $result['cron'] = ORM::factory('qwt_hbycron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
        $result['bgpic'] = ORM::factory('qwt_hbycfg')->where('bid', '=', $this->bid)->where('key', '=', 'bgpic')->find()->id;
        $result['logo'] = ORM::factory('qwt_hbycfg')->where('bid', '=', $this->bid)->where('key', '=', 'logo')->find()->id;
        $result['sharelogo'] = ORM::factory('qwt_hbycfg')->where('bid', '=', $this->bid)->where('key', '=', 'sharelogo')->find()->id;
        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/hbdownload')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('oauth', $oauth)
            ->bind('left',$left)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);

    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_hby$type";

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
        $buycodenum=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->hbnum;//购买总数
        $creatcodenum = ORM::factory('qwt_hbykl')->where('bid', '=', $bid)->count_all();//产生的口令总数
        $normalkoulin=ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('split','=',0)->count_all();//普通口令数
        $liebiankoulin=ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('split','>',0)->count_all();//裂变口令数

        $normalkoulinused=ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('split','=',0)->where('used','>',0)->count_all();//普通已使用的口令数
        $liebiankoulinused=ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('split','>',0)->where('used','>',0)->count_all();//裂变已使用的口令数

        $usedcodenum=ORM::factory('qwt_hbyweixin')->where('ct', '=', 1)->where('bid', '=', $bid)->count_all();

        if($creatcodenum<=0){
            //echo '0';
        }
        else
        {

            //echo json_encode($result);
        }
        $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyweixins where bid=$bid and ct = 1 ")->execute()->as_array();
        // $result['money'] = number_format($money[0]['money']/100,2);
        $result['used']['total']=$usedcodenum;
        $result['used']['liebian']=$liebiankoulinused;
        $result['used']['normal']=$normalkoulinused;
        $result['buynum']=$buycodenum;
        $result['creatnum']['total']=$creatcodenum;
        $result['creatnum']['liebian']=$liebiankoulin;
        $result['creatnum']['normal']=$normalkoulin;

        $this->template->title = '概况';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/getdata')
            ->bind('result', $result)
            ->bind('config', $this->config);
    }
    public function action_pre_generate(){
        $buynum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->hbnum;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('qwt_hbykl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->lastupdate;
        // $hb_cron = ORM::factory('qwt_hbycron')->where('bid', '=', $this->bid)->where('state','=',0)->find();
        // if(empty($lastupdate)||$buytimenew>$lastupdate||!$hb_cron->id)
        $hb_cron = ORM::factory('qwt_hbycron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
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
                Request::instance()->redirect('/qwthbya/home');
           }

            if($flag==1)
            {
              $time = time();
              $hbycron = ORM::factory('qwt_hbycron');
              $hbycron->bid = $this->bid;
              $hbycron->time = $time;
              $hbycron->state = 1;
              $hbycron->num = $buynum;
              require Kohana::find_file("vendor/code","hbyCommonHelper");
              Helper::GenerateCode($time,$this->bid,$buynum);
              $hbycron->save();
             //直接退出
             Request::instance()->redirect('/qwthbya/home');
             //echo "<script>history.go(-1);</script>";产生的csv会有参杂html代码
            }
    }
    public function action_payment(){
        $bid=$this->bid;

        // $this->action_refreshbuy();

        $orders=ORM::factory('qwt_hbyorder')->where('bid','=',$bid)->where('state','=',1);
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
        $this->template->content = View::factory('weixin/qwt/admin/hby/payment')
            ->bind('result',$result)
            ->bind('pages',$pages)
            ->bind('bid',$bid);
    }
    public function action_account(){
        $bid = $this->bid;
        // $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyorders where bid=$bid and state = 1 ")->execute()->as_array();
        $buy = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $result['all'] = $buy->hby_money;

        $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyweixins where bid=$bid and ct = 1 ")->execute()->as_array();
        $result['used'] = number_format($money[0]['money']/100,2);
        $this->template->title = '余额管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/account')
            ->bind('result',$result)
            ->bind('bid',$bid);
    }
    public function action_rules($action='', $id=0){
        if ($action == 'add') return $this->action_rules_add();
        if ($action == 'edit') return $this->action_rules_edit($id);
        $bid = $this->bid;
        $account = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->order_by('status','desc')->order_by('lastupdate','desc')->find_all();
        $count = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->count_all();

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $count,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/hby/pages');

        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/rules')
            ->bind('bid',$bid)
            ->bind('count',$count)
            ->bind('pages',$pages)
            ->bind('account',$account);
    }
    public function action_rules_add() {
        $bid = $this->bid;
        $title = '添加';
        $type = 1;
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        $smfy = new SmfyQwt();
        $result['wxcards'] = $smfy->getwxcards($bid);

        if ($_POST) {
            if (ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('name','=',$_POST['name'])->find()->id) {
                $result['error'] = '名称与已使用过的名称冲突，请重新命名';
            }

            if (!$result['error']) {
                $account = ORM::factory('qwt_hbyrule');
                $account->bid = $bid;
                $account->name = $_POST['name'];
                $account->status = 1;
                $account->type = $_POST['type'];
                $account->moneyMin = $_POST['cus']['moneyMin'];
                $account->money = $_POST['cus']['money'];
                $account->couponid = $_POST['coupon'];
                if($result['wxcards']){
                    foreach ($result['wxcards'] as $k => $v) {
                        if($v['id'] == $_POST['coupon']){
                            $account->couponname =$v['title'];
                            break;
                        }
                    }
                }
                $account->issub = $_POST['issub'];
                $account->lastupdate = time();
                $account->save();
                Request::instance()->redirect('/qwthbya/rules');
            }
        }
        // echo '<pre>';
        // var_dump($result['wxcards']);
        // exit;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/ruleadd')
            ->bind('result',$result)
            ->bind('type',$type)
            ->bind('title',$title);
    }
    public function action_ruledelete($id){
        $bid = $this->bid;
        $account = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('status','=',1)->where('id','=',$id)->find();
        $login = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('rid','=',$id)->find();
        if($login->id) die('有相关门店正在应用这个营销规则，不能删除！');
        $account->status = 0;
        $account->save();
        Request::instance()->redirect('/qwthbya/rules');
    }
    public function action_rules_edit($id) {
        $bid = $this->bid;
        $title = '修改';
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        $smfy = new SmfyQwt();
        $result['wxcards'] = $smfy->getwxcards($bid);

        if ($_POST) {
            $nameid = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('name','=',$_POST['name'])->find();
            if ($nameid->id && $nameid->id!=$id) {
                $result['error'] = '名称与已使用过的名称冲突，请重新命名';
            }

            if (!$result['error']) {
                $account = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('id','=',$id)->find();
                $account->bid = $bid;
                $account->name = $_POST['name'];
                $account->status = 1;
                $account->type = $_POST['type'];
                $account->moneyMin = $_POST['cus']['moneyMin'];
                $account->money = $_POST['cus']['money'];
                $account->couponid = $_POST['coupon'];
                foreach ($result['wxcards'] as $k => $v) {
                    if($v['id'] == $_POST['coupon']){
                        $account->couponname = $v['title'];
                        break;
                    }
                }
                $account->issub = $_POST['issub'];
                $account->save();
                Request::instance()->redirect('/qwthbya/rules');
            }
        }

        $account = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('id','=',$id)->find();
        $type = $account->type;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/ruleadd')
            ->bind('result',$result)
            ->bind('type',$type)
            ->bind('title',$title)
            ->bind('account',$account);
    }
    public function action_hbsend($action='', $id=0){
        if ($action == 'add') return $this->action_hbsend_add();
        if ($action == 'edit') return $this->action_hbsend_edit($id);
        $bid = $this->bid;
        $account = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->order_by('status','desc')->order_by('lastupdate','desc')->find_all();
        $count = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->count_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/hbsend')
            ->bind('bid',$bid)
            ->bind('count',$count)
            ->bind('account',$account);
    }
    public function action_hbsend_add() {
        $bid = $this->bid;
        $title = '添加';
        if ($_POST) {
            if (ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('name','=',$_POST['name'])->find()->id) {
                $result['error'] = '名称与已使用过的名称冲突，请重新命名';
            }
            if (ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('status','=',1)->where('account','=',$_POST['account'])->find()->id) {
                $result['error'] = '账号已被占用，请使用其他账号';
            }
            if (!$result['error']) {
                $account = ORM::factory('qwt_hbylogin');
                $account->bid = $bid;
                $account->name = $_POST['name'];
                $account->account = $_POST['account'];
                $account->password = $_POST['password'];
                $account->status = 1;
                $account->rid = $_POST['rule'];
                $account->lastupdate = time();
                $account->save();
                $rconfig = ORM::factory('qwt_hbyrcfg');
                foreach ($_POST['cus'] as $k => $v) {
                    $ok = $rconfig->setCfg($account->id, $k, $v);
                }
                if ($_FILES['bgpic']['error'] == 0||$_FILES['bgpic']['error'] ==2) {
                    // $bgpic = DOCROOT."qwt/hby/tmp/bgpic.$bid.jpg";
                    if ($_FILES['bgpic']['size'] > 1024*400) {
                        $result['err3'] = '背景图片文件不能超过 400K';
                    } else {
                        $result['ok2']++;
                        $rconfig->setCfg($account->id, 'bgpic', '', @file_get_contents($_FILES['bgpic']['tmp_name']));
                        // @unlink($bgpic);
                        // move_uploaded_file($_FILES['bgpic']['tmp_name'], $bgpic);
                    }
                }
                if ($_FILES['logo']['error'] == 0||$_FILES['logo']['error'] ==2) {
                    // $logo = DOCROOT."qwt/hby/tmp/logo.$bid.jpg";
                    if ($_FILES['logo']['size'] > 1024*200) {
                        $result['err3'] = '品牌logo文件不能超过 200K';
                    } else {
                        $result['ok2']++;
                        $rconfig->setCfg($account->id, 'logo', '', @file_get_contents($_FILES['logo']['tmp_name']));
                        // @unlink($logo);
                        // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                    }
                }
                if ($_FILES['sharelogo']['error'] == 0||$_FILES['sharelogo']['error'] ==2) {
                    // $logo = DOCROOT."qwt/hby/tmp/logo.$bid.jpg";
                    if ($_FILES['sharelogo']['size'] > 1024*200) {
                        $result['err3'] = '分享图标文件不能超过 200K';
                    } else {
                        $result['ok2']++;
                        $rconfig->setCfg($account->id, 'sharelogo', '', @file_get_contents($_FILES['sharelogo']['tmp_name']));
                        // @unlink($logo);
                        // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                    }
                }
                Request::instance()->redirect('/qwthbya/hbsend');
            }
        }
        $rules = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('status','=',1)->order_by('lastupdate','desc')->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/hbadd')
            ->bind('rules',$rules)
            ->bind('result',$result)
            ->bind('title',$title);
    }
    public function action_hbsend_edit($id) {
        $bid = $this->bid;
        $title = '修改';
        if ($_POST) {
            $nameid = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('name','=',$_POST['name'])->find();
            if ($nameid->id && $nameid->id!=$id) {
                $result['error'] = '名称与已使用过的名称冲突，请重新命名';
            }
            $accountid = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('status','=',1)->where('account','=',$_POST['account'])->find();
            if ($accountid->id && $accountid->id!=$id) {
                $result['error'] = '账号已被占用，请使用其他账号';
            }
            if (!$result['error']) {
                $account = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('id','=',$id)->find();
                $account->name = $_POST['name'];
                $account->account = $_POST['account'];
                $account->rid = $_POST['rule'];
                $account->password = $_POST['password'];
                $account->save();
                $rconfig = ORM::factory('qwt_hbyrcfg');
                foreach ($_POST['cus'] as $k=>$v) {
                    $ok = $rconfig->setCfg($account->id, $k, $v);
                }
                if ($_FILES['bgpic']['error'] == 0||$_FILES['bgpic']['error'] ==2) {
                    // $bgpic = DOCROOT."qwt/hby/tmp/bgpic.$bid.jpg";
                    if ($_FILES['bgpic']['size'] > 1024*400) {
                        $result['err3'] = '背景图片文件不能超过 400K';
                    } else {
                        $result['ok2']++;
                        $rconfig->setCfg($account->id, 'bgpic', '', @file_get_contents($_FILES['bgpic']['tmp_name']));
                        // @unlink($bgpic);
                        // move_uploaded_file($_FILES['bgpic']['tmp_name'], $bgpic);
                    }
                }
                if ($_FILES['logo']['error'] == 0||$_FILES['logo']['error'] ==2) {
                    // $logo = DOCROOT."qwt/hby/tmp/logo.$bid.jpg";
                    if ($_FILES['logo']['size'] > 1024*200) {
                        $result['err3'] = '品牌logo文件不能超过 200K';
                    } else {
                        $result['ok2']++;
                        $rconfig->setCfg($account->id, 'logo', '', @file_get_contents($_FILES['logo']['tmp_name']));
                        // @unlink($logo);
                        // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                    }
                }
                if ($_FILES['sharelogo']['error'] == 0||$_FILES['sharelogo']['error'] ==2) {
                    // $logo = DOCROOT."qwt/hby/tmp/logo.$bid.jpg";
                    if ($_FILES['sharelogo']['size'] > 1024*200) {
                        $result['err3'] = '分享图标文件不能超过 200K';
                    } else {
                        $result['ok2']++;
                        $rconfig->setCfg($account->id, 'sharelogo', '', @file_get_contents($_FILES['sharelogo']['tmp_name']));
                        // @unlink($logo);
                        // move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                    }
                }
                Request::instance()->redirect('/qwthbya/hbsend');
            }
        }
        $account = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('id','=',$id)->find();
        $rules = ORM::factory('qwt_hbyrule')->where('bid','=',$bid)->where('status','=',1)->order_by('lastupdate','desc')->find_all();
        $rconfig = ORM::factory('qwt_hbyrcfg')->getCfg($account->id,1);
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/hbadd')
            ->bind('result',$result)
            ->bind('rules',$rules)
            ->bind('rconfig',$rconfig)
            ->bind('title',$title)
            ->bind('account',$account);
    }
    public function action_unbind($id){
        $bid = $this->bid;
        $account = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('status','=',1)->where('id','=',$id)->find();
        $account->wx_bind = 0;
        $account->save();
        Request::instance()->redirect('/qwthbya/hbsend');
    }
    public function action_delete($id){
        $bid = $this->bid;
        $account = ORM::factory('qwt_hbylogin')->where('bid','=',$bid)->where('status','=',1)->where('id','=',$id)->find();
        $account->status = 0;
        $account->save();
        Request::instance()->redirect('/qwthbya/hbsend');
    }
    public function action_buy($money){
        $this->template = 'tpl/blank';
        self::before();
        require_once Kohana::find_file("vendor","wuhanhui_wxpay/lib/WxPay.Api");
        require_once Kohana::find_file("vendor","wuhanhui_wxpay/unit/WxPay.NativePay");
        require_once Kohana::find_file("vendor","wuhanhui_wxpay/unit/log");

        $no = time().$this->bid;
        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->SetBody('红包雨充值：'.$money.'元');
        $input->SetAttach($this->bid);
        $input->SetOut_trade_no($no);
        $input->SetTotal_fee($money*100+$money*100*6/1000);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag('红包雨充值：'.$money.'元');
        $input->SetNotify_url('http://'.$_SERVER["HTTP_HOST"].'/qwthbya/notify_qr');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($this->bid);
        $input->SetSpbill_create_ip("127.0.0.1");
        // var_dump($input);
        // echo '<br>';
        $result = $notify->GetPayUrl($input);
        // var_dump($result);
        // exit;
        // echo '<br>';
        $url = $result["code_url"];

        $url = urldecode($url);
        // header('Content-type: image/jpg');

        //echo $res.'<br>';

        $order = ORM::factory('qwt_hbyorder');
        $order->qrid = $no;
        $order->tid = 'E'.$money.time();
        $order->bid = $this->bid;
        $order->money = $money;
        $order->time = time();
        $order->save();

        $data = array('imgurl' => $url,'oid'=>$no);
        echo json_encode($data);
        exit;
    }
    public function action_show_qr(){
        require_once Kohana::find_file("vendor",'unit/phpqrcode/phpqrcode');
        // echo $_GET['img'];
        $url = $_GET['img'];
        header('Content-type: image/jpg');
        QRcode::png($url);
        exit;
    }
    public function action_notify_qr($oid){
        //6079962
        require_once Kohana::find_file("vendor",'wuhanhui_wxpay/unit/phpqrcode/phpqrcode');
        require_once Kohana::find_file("vendor","wuhanhui_wxpay/lib/WxPay.Api");
        require_once Kohana::find_file("vendor","wuhanhui_wxpay/unit/WxPay.MicroPay");
        require_once Kohana::find_file("vendor","wuhanhui_wxpay/unit/log");
        $notify = new MicroPay();
        $succCode = 2;
        $res = $notify->query($oid,$succCode);
        // echo '<pre>';
        // var_dump($res);
        // exit;
        if($res['result_code'] == 'SUCCESS'){
            $flag = 1;
        }

        if($flag==1){
            $order = ORM::factory('qwt_hbyorder')->where('qrid','=',$oid)->find();
            $this->bid=$order->bid;
            $buy = ORM::factory('qwt_login')->where('id','=',$order->bid)->find();
            if($order->id){
                if($order->state==1)die('订单已存在！');
                $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyorders where bid=$this->bid and state = 1 ")->execute()->as_array();
                $order->state = 1;
                $buy->hby_money = number_format($money[0]['money']+$order->money,2);
                $order->left = $buy->hby_money;
                $buy->save();
                $order->save();
                $content = '支付成功';
            }else{
                $content = '支付异常，错误id为'.$oid;
            }
        }else{
            $content = '充值失败';
        }
        echo $content;
        exit;
    }
    // public function action_buy($money){
    //     $this->template = 'tpl/blank';
    //     self::before();
    //     require Kohana::find_file("vendor/kdt","KdtApiClient");

    //     $appId = 'c27bdd1e37cd8300fb';
    //     $appSecret = '3e7d8db9463b1e2fd92083418677c638';
    //     $client = new KdtApiClient($appId, $appSecret);

    //     $method = 'kdt.pay.qrcode.createQrCode';
    //     $params = [
    //         'qr_name' =>'红包雨充值-营销应用平台',

    //         'qr_price' => $money*100,
    //         //'qr_price' => 1,
    //         'qr_type' => 'QR_TYPE_DYNAMIC',
    //         // 'qr_source'=>$_POST['type'].'.'.$_POST['stream'],//类型和流量拼接  给了跟不给  获取不到  没有给的意义
    //     ];
    //     $test=$client->post($method, $params);

    //     $order = ORM::factory('qwt_hbyorder');
    //     $order->qrid = $test['response']['qr_id'];
    //     $order->tid = 'E'.$money.time();
    //     $order->bid = $this->bid;
    //     $order->money = $money;
    //     $order->time = time();
    //     $order->save();

    //     $data = array('imgurl' => $test['response']['qr_code'],'qrid' =>$test['response']['qr_id'],'url'=>$test['response']['qr_url']);
    //     echo json_encode($data);
    //     exit;
    // }
    // public function action_notify_qr($qr_id){
    //     //6079962
    //     require_once Kohana::find_file("vendor/kdt","KdtApiClient");

    //     $appId = 'c27bdd1e37cd8300fb';
    //     $appSecret = '3e7d8db9463b1e2fd92083418677c638';
    //     $client = new KdtApiClient($appId, $appSecret);
    //     $method1 = 'kdt.trades.qr.get';
    //     $params = [
    //         'status' =>'TRADE_RECEIVED'
    //     ];

    //     $resultarr=$client->post($method1,$params);
    //     $qrarr=$resultarr["response"]["qr_trades"];
    //     // echo '<pre>';
    //     // var_dump($qrarr);
    //     for($i=0;$qrarr[$i];$i++){
    //         if($qrarr[$i]['qr_id']==$qr_id){
    //             $flag = 1;
    //             // echo '付款成功';
    //         }
    //     }
    //     if($flag==1){
    //         $order = ORM::factory('qwt_hbyorder')->where('qrid','=',$qr_id)->find();
    //         $buy = ORM::factory('qwt_login')->where('id','=',$order->bid)->find();
    //         if($order->id){
    //             $money=DB::query(Database::SELECT,"SELECT sum(money) as money from qwt_hbyorders where bid=$this->bid and state = 1 ")->execute()->as_array();
    //             $order->state = 1;
    //             $buy->hby_money = number_format($money[0]['money']+$order->money,2);
    //             $order->left = $buy->hby_money;
    //             $buy->save();
    //             $order->save();
    //             $content = '支付成功';
    //         }else{
    //             $content = '支付异常，错误id为'.$qr_id;
    //         }
    //     }else{
    //         $content = '充值失败';
    //     }
    //     echo $content;
    //     exit;
    // }
    public function action_hby_cron(){
        set_time_limit(0);
        $hb_cron = ORM::factory('qwt_hbycron')->where('has_qr','=',0)->order_by('id','asc')->find();
        $bid = $hb_cron->bid;
        $time = date('ymd',time());
        $code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->limit(3000)->find_all();//5min 3000
        $count_code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$hb_cron->end_id)->where('lastupdate','=',$hb_cron->time)->count_all();
        if($count_code>0){//有口令才生成
            require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
            $zipname = DOCROOT."qwt/hby/qr_code/$bid/code.zip";
            umask(0002);
            @mkdir(dirname($zipname),0777,true);
            $zip = new ZipArchive();
            $zip->open($zipname, ZIPARCHIVE::CREATE);
            foreach ($code as $k => $v) {
                //aes加密
                $privateKey = "sjdksldkwospaisk";
                $iv = "wsldnsjwisqweskl";
                $data = $v->code;
                $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                $hb_code = urlencode(base64_encode($encrypted));

                $qrurl[$k] =  'http://yingyong.smfyun.com/smfyun/user_snsapi_base/'.$v->bid.'/hby/user_snsapi_base?hb_code='.$hb_code;
                $localfile = DOCROOT."qwt/hby/qr_code/$v->bid/".$time."_code/$v->code.png";
                umask(0002);
                @mkdir(dirname($localfile),0777,true);
                QRcode::png($qrurl[$k],$localfile,'L','6','2');
                $src_im = imagecreatefrompng($localfile);
                $im = imagecreatetruecolor(270, 300);
                $black = imagecolorallocate($im, 0, 0, 0);
                imagecopy($im,$src_im,0,0,0,0,270,300);
                $string = 'NO.'.$v->id;
                imagestring($im, 3, 20, 270, $string, $black);
                imagepng($im,$localfile);
                $zip->addFile($localfile, basename($localfile));
                $end_kl = $v->id;
            }
            $zip->close();
            $last = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('id','>',$end_kl)->where('lastupdate','=',$hb_cron->time)->find();//5min 3000
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
                $localfile = DOCROOT."qwt/hby/qr_code/$bid/".$time."_code/$v->code.png";
                @unlink($localfile);
            }
        }else{
            die('异常');
        }
        exit;
    }
    public function action_downzip(){
        $hb_cron = ORM::factory('qwt_hbycron')->where('bid','=',$this->bid)->where('state','=',1)->where('has_down','=',0)->order_by('id','desc')->find();
        if(!$hb_cron->id) die('未找到可下载的最新红包码');
        $zipname = DOCROOT."qwt/hby/qr_code/$bid/code.xls";
        umask(0002);
        @mkdir(dirname($zipname),0777,true);
        @file_put_contents($zipname,$hb_cron->code);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.$hb_cron->bid.".".$hb_cron->num.".".$hb_cron->id."."."xls"); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($zipname)); //告诉浏览器，文件大小
        @readfile($zipname);
        $hb_cron->has_down = 1;
        $hb_cron->save();
        $unlink($zipname);
        Request::instance()->redirect('/qwthbya/hbdownload');
        exit;
    }
    public function action_download_csv(){
        set_time_limit(0);
        $bid = $this->bid;
        $hb_cron = ORM::factory('qwt_hbycron')->where('bid','=',$bid)->order_by('id','DESC')->find();
        $code = ORM::factory('qwt_hbykl')->where('bid','=',$bid)->where('lastupdate','=',$hb_cron->time)->find_all();
        require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
        $zipname = DOCROOT."qwt/hby/qr_code/$bid/code.zip";
        umask(0002);
        @mkdir(dirname($zipname),0777,true);
        $zip = new ZipArchive();
        $zip->open($zipname, ZIPARCHIVE::CREATE);
        foreach ($code as $k => $v) {
            $qrurl[$k] =  'http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$bid.'/hby/user_snsapi_base?hb_code='.$v->code;
            $localfile = DOCROOT."qwt/hby/qr_code/$bid/".date('ymd',time())."_code/$v->code.png";
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
            $localfile = DOCROOT."qwt/hby/qr_code/$bid/".date('ymd',time())."_code/$v->code.png";
            @unlink($localfile);
        }
        // @unlink(DOCROOT."qwt/hby/qr_code/$bid/".date('ymd',time())."_code");
        exit;
    }
    public function action_generate(){//生成口令
        set_time_limit(0);
        require Kohana::find_file("vendor/code","hbyCommonHelper");
        $buynum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',14)->find()->hbnum;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('qwt_hbykl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
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
                Request::instance()->redirect('/qwthbya/home');
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
        $config = ORM::factory('qwt_hbycfg')->getCfg($bid);
        $order = ORM::factory('qwt_hbyweixin')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if($_GET['code']){
            $order = $order->where('kouling', '=', $_GET['code']);
        }
        if($_GET['from_lid']){
            $order = $order->where('from_lid', '=', $_GET['from_lid']);
        }
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('nickname', 'like', $s)->or_where('kouling', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/hby/pages');

        $result['orders'] = $order->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['countall'] = $countall;
        $this->template->title = '红包记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/qrcode')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_hbmct(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_hbycfg')->getCfg($bid);
        $order = ORM::factory('qwt_hbykl')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if($_GET['from_lid']){
            $order = $order->where('from_lid', '=', $_GET['from_lid']);
        }
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('id', 'like', $s)->or_where('code', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/hby/pages');

        $result['orders'] = $order->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['countall'] = $countall;
        $this->template->title = '红包码生成记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hby/hbmct')
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
