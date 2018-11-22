<?php defined('SYSPATH') or die('No direct script access.');

class Controller_yhba extends Controller_Base {

    public $template = 'weixin/yhb/tpl/atpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public function before() {
        Database::$default = "yhb";
        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        $this->bid = $_SESSION['yhba']['bid'];
        $this->config = $_SESSION['yhba']['config'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/yhba/login');
            header('location:/yhba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            // $todo['users'] = ORM::factory('yhb_qrcode')->where('bid', '=', $this->bid)->count_all();
            // $todo['tickets'] = ORM::factory('yhb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            // $todo['items'] = ORM::factory('yhb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            // $todo['all'] = $todo['items'] + $todo['users'];
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }

    public function action_index() {
        $this->action_login();
    }

    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('yhb_cfg')->getCfg($bid, 1);
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('yhb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }
        //红包
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        $lastupdate=ORM::factory('yhb_kl')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('yhb_login')->where('id','=',$bid)->find()->rebuy_time;//rebuy_time是时间戳
        if(empty($lastupdate)||$buytimenew>$lastupdate)
          $flag=1;
        else
        {
            $days=(time()-$lastupdate)/(24*60*60);
            if($days>=7)
            {
             $flag=1;
            }
        }
        $left=$flag;
        $user = ORM::factory('yhb_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yhb/admin/home')
            // ->bind('result', $result)
            // ->bind('config', $config)
            // ->bind('user', $user)
            // ->bind('oauth', $oauth)
            ->bind('left',$left)
            // ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }
        //系统配置
    public function action_password() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('yhb_cfg')->getCfg($bid, 1);
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('yhb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }
        // //红包
        // $flag=0;
        // //最后一次产生口令的时间,筛选时提出掉裂变口令;
        // $lastupdate=ORM::factory('yhb_kl')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        // //最新的续费时间；
        // $buytimenew=ORM::factory('yhb_login')->where('id','=',$bid)->find()->rebuy_time;//rebuy_time是时间戳
        // if(empty($lastupdate)||$buytimenew>$lastupdate)
        //   $flag=1;
        // else
        // {
        //     $days=(time()-$lastupdate)/(24*60*60);
        //     if($days>=7)
        //     {
        //      $flag=1;
        //     }
        // }
        $left=1;
        $user = ORM::factory('yhb_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yhb/admin/password')
            // ->bind('result', $result)
            // ->bind('config', $config)
            // ->bind('user', $user)
            // ->bind('oauth', $oauth)
            ->bind('left',$left)
            // ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
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
        $buycodenum=ORM::factory('yhb_login')->where('id','=',$this->bid)->find()->num;//购买总数
        $creatcodenum = ORM::factory('yhb_kl')->where('bid', '=', $bid)->count_all();//产生的口令总数
        $normalkoulinused=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('used','>',0)->count_all();//普通已使用的口令数
        $result['buynum']=$buycodenum;
        $result['total']=$creatcodenum;
        $result['normal']=$normalkoulinused;
        // var_dump($result);
        // exit;
        $this->template->title = '概况';
        $this->template->content = View::factory('weixin/yhb/admin/getdata')
            ->bind('result', $result)
            ->bind('config', $this->config);
    }

    public function action_generate(){//生成口令
        require Kohana::find_file("vendor/code","CommonHelper1");
        $buynum = ORM::factory('yhb_login')->where('id','=',$this->bid)->find()->num;
        Helper::GenerateCode($this->bid,$buynum);
             //直接退出
        exit();
        //最后一次产生口令的时间;筛选时提出掉裂变口令
        // $flag=0;
        // $lastupdate=ORM::factory('yhb_kl')->where('bid', '=', $this->bid)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        // //最新的续费时间；
        // $buytimenew=ORM::factory('yhb_login')->where('id','=',$this->bid)->order_by('rebuy_time','DESC')->find()->rebuy_time;

        // if(empty($lastupdate)||$buytimenew>$lastupdate)
        //   $flag=1;
        // else
        // {
        //     $days=(time()-$lastupdate)/(24*60*60);
        //     if($days>=7)
        //     {
        //      $flag=1;
        //     }
        //     else
        //         Request::instance()->redirect('/yhba/home');
        //    }

        // if($flag==1)
        // {
        //      Helper::GenerateCode($this->bid,$buynum);
        //      //直接退出
        //      exit();
        //      //echo "<script>history.go(-1);</script>";产生的csv会有参杂html代码
        // }
    }

    // //兑换管理
     public function action_qrcodes(){
        $bid = $this->bid;
        $config = ORM::factory('yhb_cfg')->getCfg($bid);
        $order = ORM::factory('yhb_youzan')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('nickname', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/yhb/admin/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '红包记录';
        $this->template->content = View::factory('weixin/yhb/admin/qrcode')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['yhba']['admin'] < 1) Request::instance()->redirect('yhba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('yhb_login');
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
        ))->render('weixin/yhb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/yhb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['yhba']['admin'] < 2) Request::instance()->redirect('yhba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('yhb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('yhb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->rebuy_time = time();//购买时间 即是账号建立时间
                $login->save();
                Request::instance()->redirect('yhba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/yhb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['yhba']['admin'] < 2) Request::instance()->redirect('yhba/home');

        $bid = $this->bid;

        $login = ORM::factory('yhb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('yhb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('yhba/items');
        }

        if ($_POST['data']) {
            if(strtotime($_POST['data']['expiretime'])>strtotime($login->expiretime)) $login->rebuy_time = time();//续费时间更新
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('yhb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('yhba/logins');
            }
        }

        //$cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();

        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/yhb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/yhb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('yhb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                // $username = $_POST['username'];
                // $sql = DB::query(Database::SELECT,"select user_id from user where user_name='$username'");
                // $user_id = $sql->execute();

                // $sql = DB::query(Database::SELECT,"select expiretime from buy where user_id=$user_id and product_id = 6 ");
                // $expiretime = $sql->execute();
                // if($expiretime){
                //     $expiretime = strtotime($expiretime);
                // }else{
                //     $expiretime = strtotime(ORM::factory('yhb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['yhba']['bid'] = $biz->id;
                    $_SESSION['yhba']['user'] = $_POST['username'];
                    $_SESSION['yhba']['admin'] = $biz->admin; //超管
                    $_SESSION['yhba']['config'] = ORM::factory('yhb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['yhba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/yhba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['yhba'] = null;
        header('location:/yhba/home');
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
