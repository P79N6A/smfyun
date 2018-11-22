<?php defined('SYSPATH') or die('No direct script access.');
class Controller_yzba extends Controller_Base {
    public $template = 'weixin/yzb/tpl/atpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public function before() {
        Database::$default = "wdy";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['yzba']['bid'];
        $this->config = $_SESSION['yzba']['config'];
        $this->access_token=ORM::factory('yzb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/yzba/login');
            header('location:/yzba/login?from='.Request::instance()->action);
            exit;
        }
    }
    public function after() {
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
        $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('yzb_login')->where('id', '=', $bid)->find()->access_token;
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('yzb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }
        $cert_file = DOCROOT."yzb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."yzb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."yzb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        $result['rootca_file_exists'] = file_exists($rootca_file);
        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('yzb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('yzb_login')->where("id","=",$bid)->find()->user;
             //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file));
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dir($key_file));
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

             if ($_FILES['rootca']['error'] == 0) {
                @mkdir(dir($rootca_file));
                $ok = move_uploaded_file($_FILES['rootca']['tmp_name'], $rootca_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'yzb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'yzb_file_key', '', file_get_contents($key_file));
            if (file_exists($rootca_file)) $cfg->setCfg($bid, 'yzb_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('yzb_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            // echo "<pre>";
            // var_dump($_POST['text']);
            // echo "<pre>";
            // exit;
            $cfg = ORM::factory('yzb_cfg');
            $qrfile = DOCROOT."yzb/tmp/tpl.$bid.jpg";

            //图片合成
                /*之前的：
                $newfile = DOCROOT."yzb/tmp/news_score4.jpg";
                // @mkdir(dirname($newfile), 0777, true);

                header("Content-type: image/jpg");
                $im = @imagecreate(300, 200)
                    or die("Cannot Initialize new GD image stream");
                $background_color = imagecolorallocate($im, 72,102,150);
                $text_color = imagecolorallocate($im, 255, 255, 255);
                imagestring($im, 5, 0, 0, "jifenchaxun",$text_color);
                imagejpeg($im,$newfile);
                imagedestroy($im);*/
                /*function generateImg($type,$bid,$source, $text1, $font = 'yzb/dist/msyh.ttf') {
                switch ($type) {
                    case 1:
                        $newfile = DOCROOT.'yzb/imgtpl/'.$bid.'/score1.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    case 2:
                        $newfile = DOCROOT.'yzb/imgtpl/'.$bid.'/score2.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    case 3:
                        $newfile = DOCROOT.'yzb/imgtpl/'.$bid.'/score3.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    default:
                        # code...
                        break;
                }


                $main = imagecreatefromjpeg ( $source );

                $width = imagesx ( $main );//图片的宽度
                $height = imagesy ( $main );

                $target = imagecreatetruecolor ( $width, $height );

                $white = imagecolorallocate ( $target, 255, 255, 255 );
                imagefill ( $target, 0, 0, $white );

                imagecopyresampled ( $target, $main, 0, 0, 0, 0, $width, $height, $width, $height );

                $fontSize = 68;//像素字体
                $fontColor = imagecolorallocate ( $target, 255, 255, 255 );//字的RGB颜色
                $fontBox = imagettfbbox($fontSize, 0, $font, $text1);//文字水平居中实质
                imagettftext ( $target, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), 230, $fontColor, $font, $text1 );

                // @mkdir ( './' . $date );
                imagejpeg ( $target, $newfile, 55 );

                imagedestroy ( $main );
                imagedestroy ( $target );
                return $newfile;
            }

            //http://my.oschina.net/cart/
            generateImg ( 1,$bid,'http://game.smfyun.com/pic_test/1.jpg', $config['scorename'].'明细');
            generateImg ( 2,$bid,'http://game.smfyun.com/pic_test/2.jpg', $config['scorename'].'兑换');
            generateImg ( 3,$bid,'http://game.smfyun.com/pic_test/3.jpg', $config['scorename'].'实时排行榜');
            //图片合成结束*/


            //海报有效期
            if ($_POST['text']['ticket_lifetime'] >= 30) $_POST['text']['ticket_lifetime'] = 30;

            //二维码海报
            if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*400) {
                    $result['err3'] = '海报模板文件不能超过 400K';
                } else {
                    $result['ok3']++;
                    $cfg->setCfg($bid, 'tpl', '', file_get_contents($_FILES['pic']['tmp_name']));
                    @unlink($qrfile);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                }
            }

            //默认头像
            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."yzb/tmp/head.$bid.jpg";
                    $cfg->setCfg($bid, 'tplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
                    @unlink($default_head_file);
                    move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }

            if (!$result['err3']) {
                foreach ($_POST['text'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;
                }

                //海报配置
                foreach ($_POST['px'] as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
                    $result['ok3'] += $ok;

                    //更新海报缓存 key
                    if ($result['ok3'] && file_exists($qrfile)) {
                        touch($qrfile);

                        $tpl = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('yzb_cfg');
            // $count = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $count = $area['count'];
            for ($i=1; $i<=$count ; $i++) {
                if (!$area['city'.$i]){
                $area['city'.$i]='';
                }
                if (!$area['dis'.$i]){
                $area['dis'.$i]='';
                }
            }
            // if (!$area['city']){
            //     $area['city']='';
            // }
            // if (!$area['dis']){
            //     $area['dis']='';
            // }
            foreach ($area as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok5'] += $ok;
            }

            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        if ($_POST['tag']){
            $tag = $_POST['tag'];
            $cfg = ORM::factory('yzb_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }

            $this->action_otag();
            $result['fresh'] = 1;
            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);

        }
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $tag = $_POST['rsync'];
                $cfg = ORM::factory('yzb_cfg');
                foreach ($tag as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);
        }

        if ($_POST['cancle']){
            if($this->access_token){
                $cancle = $_POST['cancle'];
                $cfg = ORM::factory('yzb_cfg');
                foreach ($cancle as $k => $v) {
                    $ok=$cfg->setCfg($bid,$k,$v);
                    $result['ok8']+=$ok;
                }
            }
            $config = ORM::factory('yzb_cfg')->getCfg($bid, 1);

        }

        $result['tpl'] = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('yzb_login')->where('id', '=', $bid)->find()->expiretime;

        //sql 连接深圳服务器
        //$login_user = ORM::factory('yzb_login')->where('id', '=', $bid)->find()->user;
        $access_token = ORM::factory('yzb_login')->where('id', '=', $bid)->find()->access_token;
        // $mysql_server_name="112.74.102.75";
        // $mysql_username="smfyun";
        // $mysql_pwd="emg4h2q";
        // $mysql_database='smfyun.com';
        // $conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_pwd)or die("error connecting");;
        // mysql_query("set names 'utf8'");
        // mysql_select_db($mysql_database,$conn) or die(mysql_error($conn));
        // //$login_user = 'wuhanhuishenghuo';
        // $sql="select access_token,expires_in,refresh_token,user_id from user where user_shopid='$shopid'";
        // $result=mysql_fetch_array(mysql_query($sql));
        if(!$access_token){
            $oauth=1;
        }
        // echo $result[0].'<br>';
        // echo $result[1].'<br>';
        // echo $result[2].'<br>';
        // echo $result['access_token'].'<br>';
        // echo $result['expires_in'].'<br>';
        // echo $result['refresh_token'];
        // exit;

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/yzb/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['yzba']['admin'] < 1) Request::instance()->redirect('yzba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('yzb_login');
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
        ))->render('weixin/yzb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/yzb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_logins_add() {
        if ($_SESSION['yzba']['admin'] < 2) Request::instance()->redirect('yzba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('yzb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('yzb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('yzba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/yzb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['yzba']['admin'] < 2) Request::instance()->redirect('yzba/home');
        $bid = $this->bid;

        $login = ORM::factory('yzb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('yzb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('yzba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('yzb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('yzba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/yzb/admin/logins_add')
            ->bind('result', $result)
            ->bind('bid', $id)
            ->bind('config', $config);
    }
    public function action_login() {
        $this->template = 'weixin/yzb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('yzb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('yzb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && strtotime($biz->expiretime) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['yzba']['bid'] = $biz->id;
                    $_SESSION['yzba']['user'] = $_POST['username'];
                    $_SESSION['yzba']['admin'] = $biz->admin; //超管
                    $_SESSION['yzba']['config'] = ORM::factory('yzb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['yzba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/yzba/'.$_GET['from']);
            exit;
        }
    }
    public function action_logout() {
        $_SESSION['yzba'] = null;
        header('location:/yzba/home');
        exit;
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "yzb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
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
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."yzb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."yzb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."yzb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'yzb_file_cert')->find();
        $file_key = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'yzb_file_key')->find();
        $file_rootca = ORM::factory('yzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'yzb_file_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        if (!file_exists(rootca_file)) {
            @mkdir(dirname($rootca_file));
            @file_put_contents($rootca_file, $file_rootca->pic);
        }

        // Kohana::$log->add("weixin_fxb:$bid:curl_post_ssl:cert_file", $cert_file);

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

        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $rootca_file);// CA根证书（用来验证的网站证书是否是CA颁布）

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);

        $data = curl_exec($ch);

        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo curl_error($ch);
            curl_close($ch);
            return false;
        }

    }
}
