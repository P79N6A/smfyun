<?php defined('SYSPATH') or die('No direct script access.');

class Controller_wxpa extends Controller_Base {

    public $template = 'weixin/wxp/tpl/atpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $appid = 'wx47384b8b7a68241e';

    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        $this->bid = $_SESSION['wxpa']['bid'];
        $this->config = $_SESSION['wxpa']['config'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/wxpa/login');
            header('location:/wxpa/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            // $todo['users'] = ORM::factory('wxp_qrcode')->where('bid', '=', $this->bid)->count_all();
            // $todo['tickets'] = ORM::factory('wxp_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            // $todo['items'] = ORM::factory('wxp_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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
        $config = ORM::factory('wxp_cfg')->getCfg($bid, 1);
        //微信授权
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$this->appid;
        $ctoken = $mem->get($cachename1);//获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$ctoken;
        $post_data = array(
          'component_appid' =>$this->appid
        );
        $post_data = json_encode($post_data);
        $res = json_decode($this->request_post($url, $post_data),true);
        // var_dump($res);
        $pre_auth_code = $res['pre_auth_code'];
        $pre_auth_code = substr($pre_auth_code,14);//去掉前缀 preauthcode@@@

        if ($_GET['auth_code']) {
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
            $post_data = array(
              'component_appid' =>$this->appid,
              'authorization_code' =>$_GET['auth_code']
            );
            $post_data = json_encode($post_data);
            $res = json_decode($this->request_post($url, $post_data),true);
            $appid = $res['authorization_info']['authorizer_appid'];
            $access_token = $res['authorization_info']['authorizer_access_token'];
            $refresh_token = $res['authorization_info']['authorizer_refresh_token'];
            $expires_in = time()+7200;
            for($i=0;$res['authorization_info']['func_info'][$i];$i++){
                $auth_info = $auth_info.','.$res['authorization_info']['func_info'][$i]['funcscope_category']['id'];
            }
            // echo $auth_info.'<br>';
            // var_dump($res);
            $user = ORM::factory('wxp_login')->where('id','=',$this->bid)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='wxp.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('wxp_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."wxp/tmp/$bid/cert.pem";
        $key_file = DOCROOT."wxp/tmp/$bid/key.pem";
        $rootca_file=DOCROOT."wxp/tmp/$bid/rootca.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        $result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('wxp_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('wxp_login')->where("id","=",$bid)->find()->user;
             //证书上传
            if ($_FILES['cert']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            // if ($_FILES['rootca']['error'] == 0) {
            //     @mkdir(dir($rootca_file));
            //     $ok = move_uploaded_file($_FILES['rootca']['tmp_name'], $rootca_file);
            //      $result['ok'] += $ok;
            //     $result['err1'] = '证书文件已更新！';
            // }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'wxp_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'wxp_file_key', '', file_get_contents($key_file));
            // if (file_exists($rootca_file)) $cfg->setCfg($bid, 'wxp_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('wxp_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['cus']) {
            $cfg = ORM::factory('wxp_cfg');

            foreach ($_POST['cus'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }
            $config = ORM::factory('wxp_cfg')->getCfg($bid, 1);
        }

        //红包
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        $lastupdate=ORM::factory('wxp_kl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('wxp_login')->where('id','=',$bid)->find()->rebuy_time;//rebuy_time是时间戳
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


        $user = ORM::factory('wxp_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wxp/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('oauth', $oauth)
            ->bind('left',$left)
            ->bind('pre_auth_code',$pre_auth_code)
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
        $buycodenum=ORM::factory('wxp_login')->where('id','=',$this->bid)->find()->num;//购买总数
        $creatcodenum = ORM::factory('wxp_kl')->where('bid', '=', $bid)->count_all();//产生的口令总数
        $normalkoulin=ORM::factory('wxp_kl')->where('bid','=',$bid)->where('split','=',0)->count_all();//普通口令数
        $liebiankoulin=ORM::factory('wxp_kl')->where('bid','=',$bid)->where('split','>',0)->count_all();//裂变口令数

        $normalkoulinused=ORM::factory('wxp_kl')->where('bid','=',$bid)->where('split','=',0)->where('used','>',0)->count_all();//普通已使用的口令数
        $liebiankoulinused=ORM::factory('wxp_kl')->where('bid','=',$bid)->where('split','>',0)->where('used','>',0)->count_all();//裂变已使用的口令数

        $usedcodenum=ORM::factory('wxp_kl')->where('used', '>', 0)->where('bid', '=', $bid)->count_all();

        if($creatcodenum<=0){
            //echo '0';
        }
        else
        {
            $result['used']['total']=$usedcodenum;
            $result['used']['liebian']=$liebiankoulinused;
            $result['used']['normal']=$normalkoulinused;
            $result['buynum']=$buycodenum;
            $result['creatnum']['total']=$creatcodenum;
            $result['creatnum']['liebian']=$liebiankoulin;
            $result['creatnum']['normal']=$normalkoulin;
            //echo json_encode($result);
        }
        $this->template->title = '概况';
        $this->template->content = View::factory('weixin/wxp/admin/getdata')
            ->bind('result', $result)
            ->bind('config', $this->config);
    }

    public function action_generate(){//生成口令
        set_time_limit(0);
        require Kohana::find_file("vendor/code","CommonHelper");
        $buynum = ORM::factory('wxp_login')->where('id','=',$this->bid)->find()->num;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('wxp_kl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('wxp_login')->where('id','=',$this->bid)->order_by('rebuy_time','DESC')->find()->rebuy_time;

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
                Request::instance()->redirect('/wxpa/home');
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
        $config = ORM::factory('wxp_cfg')->getCfg($bid);
        $order = ORM::factory('wxp_weixin')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
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
        ))->render('weixin/wxp/admin/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '红包记录';
        $this->template->content = View::factory('weixin/wxp/admin/qrcode')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_downcsv($iid=0){
        set_time_limit(0);
        $bid = $this->bid;
        if($iid>0){
            $item = ORM::factory('wxp_item')->where('id','=',$iid)->find();
            $csv = ORM::factory('wxp_kl')->where('iid','=',$iid)->find_all();
            $file = "/tmp/$bid.$item->name.$item->num.csv";
        }else{//不存在就默认下载全部的
            $csv = ORM::factory('wxp_kl')->where('bid','=',$bid)->find_all();
            $file = "/tmp/$bid.all.csv";
        }
        $fh = fopen($file, 'w+');
        foreach ($csv as $k => $v) {
            # code...
            fputcsv($fh, array($v->code));
        }
        fclose($fh);

        $value=fopen($file,'r+');
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( 'Content-Disposition:attachment;filename="' . basename($file) . '"' );
        header ( "Content-Transfer-Encoding: binary" );

        echo fread($value,filesize($file));
        fclose($value);

        @unlink($file);
        if($item->id){
            $item->hasdown = 1;
            $item->save();
        }
        exit;
    }
    public function action_items($action='',$id=0){
        $bid = $this->bid;
        if ($action == 'add') return $this->action_items_add();
        $buynum = ORM::factory('wxp_login')->where('id','=',$bid)->find()->num;
        $all = ORM::factory('wxp_kl')->where('bid','=',$bid)->count_all();
        $result['items'] = ORM::factory('wxp_item')->where('bid','=',$bid)->find_all();
        $result['title'] = $this->template->title = '红包发送规则管理';
        $this->template->content = View::factory('weixin/wxp/admin/items')
            ->bind('buynum',$buynum)
            ->bind('all',$all)
            ->bind('result', $result);
    }
    public function action_items_edit($iid){
        $bid = $this->bid;
        if($_POST['data']){
            if($_POST['data']['name']&&$_POST['data']['money']){
                $item = ORM::factory('wxp_item')->where('id','=',$_POST['data']['id'])->find();
                $item->name = $_POST['data']['name'];
                $item->rate = $_POST['data']['rate'];
                $item->moneymin = $_POST['data']['moneymin'];
                $item->money = $_POST['data']['money'];
                $item->save();
                Request::instance()->redirect('wxpa/items');
            }else{
                $result['error'] = '参数务必填写完整！';
            }
        }
        $result['item'] = ORM::factory('wxp_item')->where('id','=',$iid)->find();
        $result['title'] = $this->template->title = '修改红包发送规则';
        $this->template->content = View::factory('weixin/wxp/admin/items_edit')
            ->bind('result', $result);
    }
    public function action_items_add(){
        $bid = $this->bid;
        $all = ORM::factory('wxp_kl')->where('bid','=',$bid)->count_all();
        $buynum = ORM::factory('wxp_login')->where('id','=',$bid)->find()->num;
        if($all>=$buynum){
            Request::instance()->redirect('wxpa/items');
        }
        if($_POST['data']){
            if($all+$_POST['data']['num']>$buynum){
                Request::instance()->redirect('wxpa/items');
            }
            if($_POST['data']['name']&&$_POST['data']['num']&&$_POST['data']['money']){
                $item = ORM::factory('wxp_item');
                $item->bid = $bid;
                $item->name = $_POST['data']['name'];
                $item->rate = $_POST['data']['rate'];
                $item->num = $_POST['data']['num'];
                $item->moneymin = $_POST['data']['moneymin'];
                $item->money = $_POST['data']['money'];
                $item->save();
                //生成口令
                require Kohana::find_file("vendor/code","wxphbb");
                Helper::GenerateCode(time(),$bid,$_POST['data']['num'],$item->id);
                Request::instance()->redirect('wxpa/items');
            }else{
                $result['error'] = '参数务必填写完整！';
            }
        }
        $result['title'] = $this->template->title = '添加新的红包发送规则';
        $this->template->content = View::factory('weixin/wxp/admin/items_add')
            ->bind('result', $result);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['wxpa']['admin'] < 1) Request::instance()->redirect('wxpa/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('wxp_login');
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
        ))->render('weixin/wxp/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/wxp/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['wxpa']['admin'] < 2) Request::instance()->redirect('wxpa/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('wxp_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wxp_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->rebuy_time = time();//购买时间 即是账号建立时间
                $login->save();
                Request::instance()->redirect('wxpa/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/wxp/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['wxpa']['admin'] < 2) Request::instance()->redirect('wxpa/home');

        $bid = $this->bid;

        $login = ORM::factory('wxp_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('wxp_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('wxpa/items');
        }

        if ($_POST['data']) {
            if(strtotime($_POST['data']['expiretime'])>strtotime($login->expiretime)) $login->rebuy_time = time();//续费时间更新
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wxp_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('wxpa/logins');
            }
        }

        //$cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();

        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/wxp/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/wxp/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('wxp_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('wxp_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['wxpa']['bid'] = $biz->id;
                    $_SESSION['wxpa']['user'] = $_POST['username'];
                    $_SESSION['wxpa']['admin'] = $biz->admin; //超管
                    $_SESSION['wxpa']['config'] = ORM::factory('wxp_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['wxpa']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/wxpa/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['wxpa'] = null;
        header('location:/wxpa/home');
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
