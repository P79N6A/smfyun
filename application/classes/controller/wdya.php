<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Wdya extends Controller_Base {

    public $template = 'weixin/wdy/tpl/atpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion = '3.0.0';
    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        if (Request::instance()->action == 'oauthscript') return;
        if (Request::instance()->action == 'oauthscript2') return;
        if (Request::instance()->action == 'test1') return;
        $this->bid = $_SESSION['wdya']['bid'];
        $this->config = $_SESSION['wdya']['config'];
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/wdya/login');
            header('location:/wdya/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('wdy_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('wdy_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            $todo['items'] = ORM::factory('wdy_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            $todo['all'] = $todo['items'] + $todo['users'];
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }

    public function action_index() {
        $this->action_login();
    }
    public function action_test1(){
        $bid=1732;
        //$bid=5;
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        $config=ORM::factory('wdy_cfg')->getCfg($bid,1);
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            // echo '<pre>';
            // var_dump($result);
            // echo '</pre>';
            // exit();
            $total_num=$result['total_num'];
            echo $total_num."<br>";
            $num=floor($total_num/50)+1;
            echo $num."<br>";
            $last_num=0;
            $a=0;
            for ($i=0; $i < $num; $i++) {
                echo $last_num."<br>";
                $result=$we->getCardIdList($last_num,50);

                if($result['errmsg']=='ok'){
                    foreach ($result['card_id_list'] as $card_id) {
                        $wxcards[$a]['id']=$card_id;
                        $infos=$we->getCardInfo($card_id);
                        if($infos['errmsg']=='ok'){
                            if($infos['card']['card_type']=='DISCOUNT'){
                                $base_info=$infos['card']['discount']['base_info'];
                            }elseif($infos['card']['card_type']=='GENERAL_COUPON'){
                                $base_info=$infos['card']['general_coupon']['base_info'];
                            }elseif($infos['card']['card_type']=='CASH'){
                                $base_info=$infos['card']['cash']['base_info'];
                            }elseif($infos['card']['card_type']=='MEMBER_CARD'){
                                $base_info=$infos['card']['member_card']['base_info'];
                            }elseif($infos['card']['card_type']=='GIFT'){
                                $base_info=$infos['card']['gift']['base_info'];
                            }
                            $title=$base_info['title'];
                            $wxcards[$a]['title']=$title;
                        }
                        $a++;
                    }
                }
                $last_num+=50;

            }
            echo '<pre>';
            var_dump($wxcards);
            echo '</pre>';
        }
        exit;
    }
    public function action_otag(){
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        //$appId = ORM::factory('wdy_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;

        //$appSecret = ORM::factory('wdy_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;

        $client = new YZTokenClient($this->access_token);

        $tag_name =  ORM::factory('wdy_cfg')->where('bid','=',$this->bid)->where('key','=','tag_name')->find()->value;

        $sql = DB::query(Database::SELECT,"SELECT openid as OP FROM wdy_qrcodes where (`bid`=$this->bid and `ticket`!= 'NULL') or (`bid`=$this->bid and `fopenid`!= 'NULL')");
        $openid = $sql->execute()->as_array();
        set_time_limit(0);
        for($p=0;$openid[$p];$p++){
            //echo $openid[$p]['OP'].'<br>';
            $method = 'youzan.users.weixin.follower.tags.add';
            $params = [
            'tags' =>$tag_name,

            'weixin_openid' =>$openid[$p]['OP'],
            ];
            $test=$client->post($method, $this->methodVersion, $params, $files);
            // echo $appId.'<br>';
            // echo $appSecret.'<br>';
            // echo '<pre>';
            // var_dump($test);
            // echo '</pre>';
            // exit;
          }
    }

    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('wdy_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."wdy/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."wdy/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."wdy/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        $result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('wdy_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('wdy_login')->where("id","=",$bid)->find()->user;
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

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'wdy_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'wdy_file_key', '', file_get_contents($key_file));
            if (file_exists($rootca_file)) $cfg->setCfg($bid, 'wdy_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('wdy_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            // echo "<pre>";
            // var_dump($_POST['text']);
            // echo "<pre>";
            // exit;
            $cfg = ORM::factory('wdy_cfg');
            $qrfile = DOCROOT."wdy/tmp/tpl.$bid.jpg";

            //图片合成
                /*之前的：
                $newfile = DOCROOT."wdy/tmp/news_score4.jpg";
                // @mkdir(dirname($newfile), 0777, true);

                header("Content-type: image/jpg");
                $im = @imagecreate(300, 200)
                    or die("Cannot Initialize new GD image stream");
                $background_color = imagecolorallocate($im, 72,102,150);
                $text_color = imagecolorallocate($im, 255, 255, 255);
                imagestring($im, 5, 0, 0, "jifenchaxun",$text_color);
                imagejpeg($im,$newfile);
                imagedestroy($im);*/
                /*function generateImg($type,$bid,$source, $text1, $font = 'wdy/dist/msyh.ttf') {
                switch ($type) {
                    case 1:
                        $newfile = DOCROOT.'wdy/imgtpl/'.$bid.'/score1.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    case 2:
                        $newfile = DOCROOT.'wdy/imgtpl/'.$bid.'/score2.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    case 3:
                        $newfile = DOCROOT.'wdy/imgtpl/'.$bid.'/score3.jpg';
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
                    $default_head_file = DOCROOT."wdy/tmp/head.$bid.jpg";
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

                        $tpl = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('wdy_cfg');
            // $count = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
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

            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        if ($_POST['tag']){
            $tag = $_POST['tag'];
            $cfg = ORM::factory('wdy_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }

            $this->action_otag();
            $result['fresh'] = 1;
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);

        }
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $tag = $_POST['rsync'];
                $cfg = ORM::factory('wdy_cfg');
                foreach ($tag as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }

        if ($_POST['cancle']){
            if($this->access_token){
                $cancle = $_POST['cancle'];
                $cfg = ORM::factory('wdy_cfg');
                foreach ($cancle as $k => $v) {
                    $ok=$cfg->setCfg($bid,$k,$v);
                    $result['ok8']+=$ok;
                }
            }
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);

        }

        $result['tpl'] = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('wdy_login')->where('id', '=', $bid)->find()->expiretime;

        //sql 连接深圳服务器
        //$login_user = ORM::factory('wdy_login')->where('id', '=', $bid)->find()->user;
        $access_token = ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
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
        $this->template->content = View::factory('weixin/wdy/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }

    public function action_zero() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/zero')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_lab() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['tag']['tag_name']){//点击刷新
            $tag = $_POST['tag'];
            $cfg = ORM::factory('wdy_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }
            $result['fresh'] = 1;
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
            $next_qid=$config['next_qid'];
            if(!$next_qid) $next_qid=0;
            $users = ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('id','>',$next_qid)->order_by('id','ASC')->limit(2000)->find_all();
            foreach ($users as $k => $v) {
                $qid=$v->id;
                $lab = ORM::factory('wdy_lab')->where('bid','=',$bid)->where('qid','=',$qid)->find();
                if(!$lab->id){//不存在就插入
                    $lab->bid = $bid;
                    $lab->qid = $v->id;
                    $lab->lab_name = $config['tag_name'];
                    $lab->status = 0;
                    $lab->save();
                }
            }
            $ok=ORM::factory('wdy_cfg')->setCfg($bid,'next_qid',$qid);
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
            // $tag = $_POST['tag'];
            // $cfg = ORM::factory('wdy_cfg');
            // foreach ($tag as $k=>$v) {
            //     $ok = $cfg->setCfg($bid, $k, $v);
            //     $result['ok6'] += $ok;
            // }
            // $this->action_otag();
            $result['fresh'] = 1;
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }
        $result['islab'] = ORM::factory('wdy_lab')->where('bid','=',$bid)->where('status','=',1)->count_all();
        $result['alllab'] = ORM::factory('wdy_lab')->where('bid','=',$bid)->count_all();
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/lab')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_area() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        //2015.12.18选择可参与地区配置
        $txt = DOCROOT."{$config['txt']}";
        $result['txt'] = file_exists($txt);
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('wdy_cfg');
            // $count = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
            $count = $area['count'];
            for ($i=1; $i<=$count ; $i++) {
                if (!$area['city'.$i]){
                $area['city'.$i]='';
                }
                if (!$area['dis'.$i]){
                $area['dis'.$i]='';
                }
            }
            foreach ($area as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok5'] += $ok;
            }
            $txt = DOCROOT."{$_FILES['txt']['name']}";
            if ($_FILES['txt']['error'] == 0) {
                @mkdir(dirname($txt),0777,true);
                $ok = move_uploaded_file($_FILES['txt']['tmp_name'], $txt);
                $result['ok'] += $ok;
                $result['err1'] = '文件已更新！';
                $cfg->setCfg($bid, 'txt', $_FILES['txt']['name']);
            }
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/area')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_rsync() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('wdy_cfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/rsync')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_cancle() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;

        if ($_POST['cancle']){
            //if($this->access_token){
                $cancle = $_POST['cancle'];
                $cfg = ORM::factory('wdy_cfg');
                foreach ($cancle as $k => $v) {
                    $ok=$cfg->setCfg($bid,$k,$v);
                    $result['ok8']+=$ok;
                }
            //}
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);

        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/cancle')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_hb_check() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;

        if ($_POST['hb_check']){
            $hb_check = $_POST['hb_check'];
            $cfg = ORM::factory('wdy_cfg');
            foreach ($hb_check as $k => $v) {
                $ok=$cfg->setCfg($bid,$k,$v);
                $result['ok8']+=$ok;
            }
            $config = ORM::factory('wdy_cfg')->getCfg($bid, 1);
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wdy/admin/hb_check')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //有赞授权刷新脚本 七天一次
    public function action_oauthscript($bid=39){
        $shop = ORM::factory('wdy_login')->where('id','=',$bid)->find();
        $url="https://open.youzan.com/oauth/token";
        if($shop->access_token&&$shop->id){
            $data=array(
                "client_id"=>"41eeb7e302f34f799d",
                "client_secret"=>"6fd4c2a9c3ce4ab8f855e8a3a61d7a62",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            $shop->access_token = $result->access_token;
            $shop->expires_in = time()+$result->expires_in;
            $shop->refresh_token = $result->refresh_token;
            $shop->save();
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 成功';
            echo '</pre>';
            exit;
        }else{
            die('no id or no access_token!');
        }
    }
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=41eeb7e302f34f799d&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/wdya/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        require Kohana::find_file("vendor","kdt/YZOauthClient");
        $clientId = "41eeb7e302f34f799d";//请填入开发者后台的client_id
        $clientSecret = "6fd4c2a9c3ce4ab8f855e8a3a61d7a62";//请填入开发者后台的client_secret
        $redirectUrl = 'http://'.$_SERVER["HTTP_HOST"].'/wdya/callback';//请填入开发者后台所填写的回调地址，本示例中回调地址应指向本文件

        $token = new YZOauthClient( $clientId , $clientSecret );
        $keys = array();

        $type = 'code';//如要刷新access_token，这里的值为refresh_token
        $keys['code'] = $_GET['code'];
        //如要刷新access_token，这里为$keys['refresh_token']
        $keys['redirect_uri'] = $redirectUrl;

        $result=$token->getToken($type,$keys);
        if(isset($result['access_token']))
        {
            require Kohana::find_file("vendor","kdt/YZTokenClient");
            $oauth=new YZTokenClient($result['access_token']);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            $sid = $value['id'];
            $name = $value['name'];
            $usershop = ORM::factory('wdy_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result['access_token'];
            $usershop->expires_in = time()+$result['expires_in'];
            $usershop->refresh_token = $result['refresh_token'];
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("wdya/home")."';</script>";
        }
        //Request::instance()->redirect('wdya/home');
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $this->access_token=ORM::factory('wdy_login')->where('id', '=', $bid)->find()->access_token;
        $config = ORM::factory('wdy_cfg')->getCfg($bid);
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                if ($_POST['form']['score']) {
                    $qrcode_edit = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
                    $sid=ORM::factory('wdy_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                    if($config['switch']==1){
                        $this->rsync1($bid,$qrcode_edit->openid,$this->access_token,$_POST['form']['score'],$sid,'积分操作');
                    }
                }
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('wdy_qrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where('nickname', 'like', $s); //->or_where('openid', 'like', $s);
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM wdy_qrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
            $tempid=array();
              if($firstchild[0]['openid']==null)
              {
                $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
              }
              else
              {
                  for($i=0;$firstchild[$i];$i++)
                  {
                    $tempid[$i]=$firstchild[$i]['openid'];
                  }
              }
              //$qrcode = ORM::factory('fxb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid);
              $qrcode =$qrcode->where('fopenid', 'IN',$tempid);


        }

        //按状态搜索
        if ($_GET['lock']) {
            $result['lock'] = $_GET['lock'];
            $qrcode = $qrcode->where('lock', '=', $result['lock']);
        }

        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wdy/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/wdy/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid);

        //上传 CSV 批量发货
        if ($_FILES['csv'] && $_FILES['csv']['error'] == 0) {
            $i = 0;
            $fh = fopen($_FILES['csv']['tmp_name'], 'r');

            while ($data = fgetcsv($fh, 1024)) {
                $encode = mb_detect_encoding($data[15], array("ASCII",'UTF-8',"GB2312","GBK"));

                // print_r($data);
                if (count($data) < 17) continue;
                if (!is_numeric($data[0])) continue;

                //发货
                $oid = $data[0];

                if ($encode == 'EUC-CN') {
                    $shiptype = iconv('gbk', 'utf-8', $data[15]);
                    $shipcode = iconv('gbk', 'utf-8', $data[16]);
                } else {
                    $shiptype = $data[15];
                    $shipcode = $data[16];
                }

                $order = ORM::factory('wdy_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                if ($order->status == 0 || ($order->shipcode != $shipcode) || ($order->shiptype != $shiptype)) {
                    $order->status = 1;
                    $order->shiptype = $shiptype;
                    $order->shipcode = $shipcode;
                    $order->save();
                    $i++;
                }
            }

            fclose($fh);
            $result['ok'] = "共批量发$i个订单";
        }

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
            Kohana::$log->add("post",print_r($_POST['action'],true));
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('wdy_order')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';

                    $tempname=ORM::factory("wdy_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("wdy_item")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("wdy_qrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                    if($hbresult['result_code']=='FAIL'){
                        $order->memo = $hbresult['return_msg'];
                        $order->status = 0;
                        $order->save();
                    }
                }
                $order->save();
            }

            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        if ($action == 'ship' && $id) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $we = new Wechat($config);

            $order = ORM::factory('wdy_order')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();

                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['wdya']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['wdya']['shipcode'] = $_REQUEST['shipcode'];
                    $order->shiptype = $_REQUEST['shiptype'];
                    $order->shipcode = $_REQUEST['shipcode'];

                    $order->save();

                    //发微信消息给用户
                    $shipmsg = "%s，您的积分兑换奖品已发货。快递：{$_REQUEST['shiptype']}，单号：{$_REQUEST['shipcode']}，请注意查收";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $we->sendCustomMessage($msg);
                }
                if(($order->type)==3)
                {

                    $shipmsg = "%s，您的积分兑换奖品已经充值，请注意查收";
                    $msg['msgtype'] = 'text';
                    $msg['touser'] = $order->user->openid;
                    $msg['text']['content'] = sprintf($shipmsg, $order->name);
                    $we->sendCustomMessage($msg);
                }
                if($order->type==4){
                    Kohana::$log->add("type",print_r($order->type,true));
                    $order->shiptype = '无';
                    $order->shipcode = '无';

                    $tempname=ORM::factory("wdy_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("wdy_item")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("wdy_qrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                    Kohana::$log->add("wdy_hbresult:$bid",print_r($hbresult,true));
                    if($hbresult['result_code']=='FAIL'){
                        $order->memo = $hbresult['return_msg'];
                        $order->status = 0;
                        $order->save();
                    }
                }
                //Request::instance()->redirect('wdya/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('wdy_order')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            if($countuser>0){
                $user = ORM::factory('wdy_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
                $userarr = array();
                foreach ($user as $k => $v) {
                    $userarr[$k] = $v->id;
                }
                $order = $order->where('qid', 'IN', $userarr);
            }else{
                $order = $order->and_where_open();
                $s = '%'.trim($_GET['s'].'%');
                $order = $order->where('name', 'like', $s)->or_where('tel', 'like', $s)->or_where('address', 'like', $s);
                $order = $order->and_where_close();
            }
        }

        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('wdy_qrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        $active_type="total";
        //分类展示 1实物需发货的
         if ($_GET['type']=="object") {
            $order = $order->where('type', '=', null);
            $active_type="object";
        }
        //2虚拟话费和流量充值
         if ($_GET['type']=="fare") {
            $order = $order->where('type', '=', 3);
            $active_type="fare";
        }
        //3优惠码
        //  if ($_GET['type']=="code") {
        //     $order = $order->where('type', '=', 4);
        //     $active_type="code";
        // }
        //红包
         if ($_GET['type']=="hb") {
            $order = $order->where('type', '=', 4);
            $active_type="hb";
        }

        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
             $tempname="全部";
            $time1=microtime(true);
            switch ($_GET["tag"]) {
                case 'fare':
                    $orders=$order->order_by($result['sort'], 'DESC')->where('type','=',3)->limit(1000)->find_all();
                    $tempname="充值";
                    break;
                case'object':
                    $orders=$order->order_by($result['sort'], 'DESC')->where('type','=',null)->limit(1000)->find_all();
                    $tempname="实物";
                    break;
                case'code':
                    $orders=$order->order_by($result['sort'], 'DESC')->where('type','=',4)->limit(1000)->find_all();
                    $tempname="优惠码";
                    break;
                default:
                    $orders = $order->order_by($result['sort'], 'DESC')->limit(1000)->find_all();
                    break;
            }
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('id', '收货人', '收货电话', '收货城市', '收货地址', '备注', '兑换产品','金额','消耗积分', '订单时间', '是否有关注', '产品ID', 'OpenID', '是否锁定', '直接粉丝', '间接粉丝', '物流公司', '物流单号');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($orders as $o) {
                //$count2 = ORM::factory('wdy_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('wdy_qrcode')->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('wdy_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

                //地址处理
                list($prov, $city, $dist) = explode(' ', $o->city);
                $array = array($o->id, $o->name, $o->tel, "{$prov} {$city} {$dist}", $o->address, $o->memo, $o->item->name,$o->item->price, $o->score, date('Y-m-d H:i:s', $o->createdtime), $o->user->subscribe, $o->item->id, $o->user->openid, $o->user->lock, $count2, $count3);

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
        ))->render('weixin/wdy/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/wdy/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('activetype',$active_type);
    }

    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        if ($_POST['yz']=='2'){
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            //$appId = ORM::factory('wdy_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;
            //$appSecret = ORM::factory('wdy_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;
            if($this->access_token){
                $client = new YZTokenClient($this->access_token);
                $method = 'youzan.ump.presents.ongoing.all';
                $params = [
                ];
                $results = $client->post($method, $this->methodVersion, $params, $files);
                // for($i=0;$results['response']['presents'][$i];$i++){
                //     $res = $results['response']['presents'][$i];
                //     $present_id[$i]=$res['present_id'];
                //     $title1[$i]=$res['title'];
            }else{
                $results[0] = 'fail';
            }
            echo json_encode($results);
            exit;
        }
        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid);

        $result['items'] = ORM::factory('wdy_item')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all();
        $iid = ORM::factory('wdy_item')->where('bid', '=', $bid)->order_by('lastupdate', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('wdy_order')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }

        $this->template->title = '奖品管理';
        $this->template->content = View::factory('weixin/wdy/admin/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            $total_num=$result['total_num'];
            //echo $total_num."<br>";
            $num=floor($total_num/50)+1;
            //echo $num."<br>";
            $last_num=0;
            $a=0;
            for ($i=0; $i < $num; $i++) {
                //echo $last_num."<br>";
                $result=$we->getCardIdList($last_num,50);
                // echo '<pre>';
                // var_dump($result);
                // echo '</pre>';
                if($result['errmsg']=='ok'){
                    foreach ($result['card_id_list'] as $card_id) {
                        $wxcards[$a]['id']=$card_id;
                        $infos=$we->getCardInfo($card_id);
                        if($infos['errmsg']=='ok'){
                            if($infos['card']['card_type']=='DISCOUNT'){
                                $base_info=$infos['card']['discount']['base_info'];
                            }elseif($infos['card']['card_type']=='GENERAL_COUPON'){
                                $base_info=$infos['card']['general_coupon']['base_info'];
                            }elseif($infos['card']['card_type']=='CASH'){
                                $base_info=$infos['card']['cash']['base_info'];
                            }elseif($infos['card']['card_type']=='MEMBER_CARD'){
                                $base_info=$infos['card']['member_card']['base_info'];
                            }elseif($infos['card']['card_type']=='GIFT'){
                                $base_info=$infos['card']['gift']['base_info'];
                            }
                            $title=$base_info['title'];
                            $wxcards[$a]['title']=$title;
                        }
                        $a++;
                    }
                }
                $last_num+=50;

            }
            // echo '<pre>';
            // var_dump($wxcards);
            // echo '</pre>';
        }
        // var_dump($wxcards);
        // exit();
        //拉取有赞优惠券优惠码  title       group_id
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];

        }
        //赠品  present_id title
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信卡券列表';
                }
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==5){
                if(!$_POST['yzgift']){
                    $result['error'] = '未拉取到有赞赠品列表';
                }
                $_POST['data']['url']=$_POST['yzgift'];
            }elseif ($_POST['data']['type']==6) {
                if(!$_POST['yzcoupons']){
                    $result['error'] = '未拉取到有赞优惠券列表';
                }
                $_POST['data']['url']=$_POST['yzcoupons'];
            }
            $item = ORM::factory('wdy_item');
            $item->values($_POST['data']);

            $item->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['score'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';

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
                $key = "wdy:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('wdya/items');
            }
        }

        $result['action'] = 'add';
        $result['present_id']=$present_id;
        $result['title1']=$title1;
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->content = View::factory('weixin/wdy/admin/items_add')
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons',$yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('wdy_cfg')->getCfg($bid);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            $total_num=$result['total_num'];
            //echo $total_num."<br>";
            $num=floor($total_num/50)+1;
            //echo $num."<br>";
            $last_num=0;
            $a=0;
            for ($i=0; $i < $num; $i++) {
                //echo $last_num."<br>";
                $result=$we->getCardIdList($last_num,50);
                // echo '<pre>';
                // var_dump($result);
                // echo '</pre>';
                if($result['errmsg']=='ok'){
                    foreach ($result['card_id_list'] as $card_id) {
                        $wxcards[$a]['id']=$card_id;
                        $infos=$we->getCardInfo($card_id);
                        if($infos['errmsg']=='ok'){
                            if($infos['card']['card_type']=='DISCOUNT'){
                                $base_info=$infos['card']['discount']['base_info'];
                            }elseif($infos['card']['card_type']=='GENERAL_COUPON'){
                                $base_info=$infos['card']['general_coupon']['base_info'];
                            }elseif($infos['card']['card_type']=='CASH'){
                                $base_info=$infos['card']['cash']['base_info'];
                            }elseif($infos['card']['card_type']=='MEMBER_CARD'){
                                $base_info=$infos['card']['member_card']['base_info'];
                            }elseif($infos['card']['card_type']=='GIFT'){
                                $base_info=$infos['card']['gift']['base_info'];
                            }
                            $title=$base_info['title'];
                            $wxcards[$a]['title']=$title;
                        }
                        $a++;
                    }
                }
                $last_num+=50;

            }
            // echo '<pre>';
            // var_dump($wxcards);
            // echo '</pre>';
        }
        //拉取有赞优惠券优惠码  title       group_id
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];

        }
        //赠品  present_id title
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        $item = ORM::factory('wdy_item', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('wdy_order')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('wdya/items');
            }
        }

        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                if(!$_POST['wecoupons']){
                    $result['error'] = '未拉取到微信优惠券列表';
                }
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==5){
                if(!$_POST['yzgift']){
                    $result['error'] = '未拉取到有赞赠品列表';
                }
                $_POST['data']['url']=$_POST['yzgift'];
            }elseif ($_POST['data']['type']==6) {
                if(!$_POST['yzcoupons']){
                    $result['error'] = '未拉取到有赞优惠券列表';
                }
                $_POST['data']['url']=$_POST['yzcoupons'];
            }
            $item->values($_POST['data']);
            $item->bid = $bid;
            // var_dump($_POST);
            // exit();
            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交（请在基础设置-微信参数，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';

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
                $key = "wdy:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('wdya/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        $this->template->content = View::factory('weixin/wdy/admin/items_add')
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons',$yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['wdya']['admin'] < 1) Request::instance()->redirect('wdya/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('wdy_login');
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
        ))->render('weixin/wdy/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/wdy/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['wdya']['admin'] < 2) Request::instance()->redirect('wdya/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('wdy_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wdy_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('wdya/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/wdy/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['wdya']['admin'] < 2) Request::instance()->redirect('wdya/home');

        $bid = $this->bid;

        $login = ORM::factory('wdy_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('wdy_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('wdya/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wdy_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                if ($_POST['data']['copyright']) {
                    $ok = $cfg->setCfg($id, 'copyright', $_POST['data']['copyright']);
                }
                //appid 重置
                // if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                // }

                Request::instance()->redirect('wdya/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/wdy/admin/logins_add')
            ->bind('result', $result)
            ->bind('bid', $id)
            ->bind('config', $config);
    }
    public function action_empty_ticket($bid){
        $sql = DB::query(Database::UPDATE,"update wdy_qrcodes set ticket='' where bid =$bid");
        $result = $sql->execute();
        Request::instance()->redirect('wdya/logins/edit/'.$bid);
    }
    public function action_login() {
        $this->template = 'weixin/wdy/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('wdy_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('wdy_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['wdya']['bid'] = $biz->id;
                    $_SESSION['wdya']['user'] = $_POST['username'];
                    $_SESSION['wdya']['admin'] = $biz->admin; //超管
                    $_SESSION['wdya']['config'] = ORM::factory('wdy_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['wdya']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/wdya/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['wdya'] = null;
        header('location:/wdya/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "wdy_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        //ignore_user_abort(true);//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(0);
        if ($_GET['DELETE'] == 1) {
            $zero = ORM::factory('wdy_zero')->where('bid', '=', $this->bid)->find();
            $zero->bid = $this->bid;
            $zero->status = 0;
            $zero->lastupdate = time();
            $zero->save();
            exit;
            // $empty = ORM::factory('wdy_score')->where('bid', '=', $this->bid);
            // $empty->delete_all();
            // DB::update(ORM::factory('wdy_qrcode')->table_name())
            // ->set(array('score' => '0','yz_score' =>'0'))
            // ->where('bid', '=', $this->bid)
            // ->execute();
            // $this->config = ORM::factory('wdy_cfg')->getCfg($this->bid, 1);
            // if($this->config['switch']==1){
            //     $this->access_token=ORM::factory('wdy_login')->where('id', '=', $this->bid)->find()->access_token;
            //     require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            //     $client = new YZTokenClient($this->access_token);
            //     $num=ORM::factory('wdy_qrcode')->where('bid','=',$this->bid)->count_all();
            //     $yu = ceil($num/10000);
            //     for($i=0;$i<$yu;$i++){
            //         $userarr = ORM::factory('wdy_qrcode')->where('bid','=',$this->bid)->limit(10000)->find_all();
            //         foreach ($userarr as $user) {
            //             $weixin_openid=$user->openid;
            //             $method ='youzan.shop.basic.get';
            //             $params =[
            //             ];
            //             $result =$client->post($this->access_token,$method,$params);
            //             $kdt_id = $result['response']['sid'];
            //             $method='youzan.users.weixin.follower.get';
            //             $params=[
            //                 'weixin_openid'=>$weixin_openid,
            //             ];
            //             $results=$client->post($this->access_token,$method,$params);
            //             $user_id = $results['response']['user']['user_id'];
            //             $user_id = $results['response']['user']['user_id'];
            //             $method = 'youzan.crm.fans.points.get';
            //             $params =[
            //             'fans_id' => $user_id,
            //             ];
            //             $results=$client->post($this->access_token,$method,$params);
            //             if($point!=0){
            //                 $point = $results['response']['point'];
            //                 $method = 'youzan.crm.customer.points.decrease';
            //                 $params =[
            //                 'fans_id' => $user_id,
            //                 'kdt_id' => $kdt_id,
            //                 'points' => $point,
            //                 ];
            //                 $a=$client->post($this->access_token,$method,$params);
            //             }
            //         }
            //         // sleep($interval);// 等待5sg
            //     }

            // }
            // Request::instance()->redirect('wdya/zero');
        }
    }
    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        $this->config=ORM::factory('wdy_cfg')->getCfg($this->bid,1);
        if($this->config['totle']!=date('Y-m-d',time())){
            if($this->config['totle']){
                $time_totle=strtotime($this->config['totle']);
            }else{
                $time_totle=0;
            }
            $daytype='%Y-%m-%d';
            $length=10;
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `wdy_qrcodes` where bid=$this->bid and  jointime >= $time_totle ORDER BY `time` DESC ")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                $fans=DB::query(Database::SELECT,"SELECT count(openid) as fansnum from wdy_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from wdy_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from wdy_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' or FROM_UNIXTIME(`jointime`, '$daytype')='$time')")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `wdy_orders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
            foreach ($newadd as $key => $value) {
                $totle=ORM::factory('wdy_totle')->where('bid','=',$this->bid)->where('time_quantum','=',$value['time'])->find();
                $totle->bid=$this->bid;
                $totle->fans_num=$value['fansnum'];
                $totle->time_quantum=$value['time'];
                $totle->timestamp=strtotime($value['time']);
                $totle->haibao_num=$value['tickets'];
                $totle->qr_num=$value['actnums'];
                $totle->order_num=$value['ordernums'];
                $totle->save();

            }
            $ok=ORM::factory('wdy_cfg')->setCfg($this->bid,'totle',date('Y-m-d',time()));
            $this->config=ORM::factory('wdy_cfg')->getCfg($this->bid,1);
        }else{
            $time_today=strtotime(date('Y-m-d',time()));
            $fnum=ORM::factory('wdy_qrcode')->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->count_all();
            $tnum=ORM::factory('wdy_qrcode')->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->where('ticket','!=','')->count_all();
            $qnum=ORM::factory('wdy_qrcode')->where('bid','=',$this->bid)->and_where_open()->where('jointime','>=',$time_today)->or_where('lastupdate','>=',$time_today)->and_where_close()->count_all();
            $onum=ORM::factory('wdy_order')->where('bid','=',$this->bid)->where('createdtime','>=',$time_today)->count_all();
            if($fnum>0||$tnum>0||$qnum>0||$onum>0){
                $totle=ORM::factory('wdy_totle')->where('bid','=',$this->bid)->where('time_quantum','=',date('Y-m-d',time()))->find();
                $totle->bid=$this->bid;
                $totle->fans_num=$fnum;
                $totle->time_quantum=date('Y-m-d',time());
                $totle->timestamp=strtotime(date('Y-m-d',time()));
                $totle->haibao_num=$tnum;
                $totle->qr_num=$qnum;
                $totle->order_num=$onum;
                $totle->save();
            }
        }
        if($_GET['qid']==3||$action=='shaixuan')
        {
            $status=3;
            $newadd=array();
            if($_GET['data']['begin']!=null&&$_GET['data']['over']!=null)//搜索
            {
                $begin=$_GET['data']['begin'];
                $over=$_GET['data']['over'];
               if(strtotime($begin)>strtotime($over))
               {
                 $begin=$_GET['data']['over'];
                 $over=$_GET['data']['begin'];
               }
               // echo $begin.$over;
               if(strtotime($begin)==strtotime($over))
               {
                 $newadd[0]['time']=$begin;
               }
               else
               {
                $newadd[0]['time']=$begin.'~'.$over;
               }

                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from wdy_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from wdy_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];
                //活动参与人数
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `wdy_scores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from wdy_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' or FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over')")->execute()->as_array();
                $newadd[0]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `wdy_orders` where bid =$this->bid and FROM_UNIXTIME(`createdtime`, '$daytype')>='$begin' and FROM_UNIXTIME(`createdtime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['ordernums']=$ordernums[0]['ordernum'];

            }
        }
        else
        {

            if($_GET['qid']==2||$action=='month')//按月统计
            {
                $daytype='%Y-%m';
                $length=7;
                $status=2;
            }
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `wdy_totles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/wdy/admin/pages');
            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `wdy_totles` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"SELECT sum(fans_num) as fansnum from wdy_totles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $ticket=DB::query(Database::SELECT,"SELECT sum(haibao_num) as tickets from wdy_totles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"SELECT sum(qr_num) as actnum from wdy_totles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"SELECT sum(order_num) as ordernum FROM `wdy_totles` where bid =$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '%Y-%m-%d')as time FROM `wdy_totles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
        $num=count($duringdata);
        if(strtotime($duringdata[0]['time'])<strtotime($duringdata[$num-1]['time']))
        {
        $duringtime['begin']=$duringdata[0]['time'];
        echo $duringtime['begin']."pppp";
        $duringtime['over']=$duringdata[$num-1]['time'];
        }
        else
        {
        $duringtime['begin']=$duringdata[$num-1]['time'];
        $duringtime['over']=$duringdata[0]['time'];
        }
        $this->template->content = View::factory('weixin/wdy/admin/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
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
    //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
    private function hongbao($config, $openid, $we='', $bid=1, $money){
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $we = new Wechat($config);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $config['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        // $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        // $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写
        Kohana::$log->add("data",print_r( $data,true));
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);//支付安全验证函数（核心函数）
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;//hash数组
    }

    private function rsync1($bid,$openid,$access_token,$chscore,$sid,$reason){
        Kohana::$log->add("wdyscore1", print_r($reason, true));
        Kohana::$log->add("wdyscore3", print_r($chscore, true));
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($access_token){
            $client = new YZTokenClient($access_token);
        }else{
            die('请在后台一键授权给有赞');
        }
        $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        $result=$client->post($method, $this->methodVersion, $params, $files);
        Kohana::$log->add('wdyresult',print_r($result,true));
        $fans_id = $result['response']['user']['user_id'];
        if(!$fans_id){
            Kohana::$log->add("bid{$bid}openid{$openid}", print_r($result, true));
            return;
        }
        $method = 'youzan.crm.fans.points.get';
        $params =[
        'fans_id' => $fans_id,
        ];
        $methodVersion = '3.0.0';
        $results=$client->post($method, $methodVersion, $params, $files);
        $point = $results['response']['point'];
        if($qrcode->yz_score==0){
            $method = 'youzan.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            if($qrcode->score>0){
                $a=$client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add("wdyscore", print_r($a, true));
            }else{
                $qrcodescore0=1;
                Kohana::$log->add('wfbrsync1bidsocre0','1');
            }
            // $a=$client->post($method, $this->methodVersion, $params, $files);
            // Kohana::$log->add('result',print_r($a,true));
            if($a['response']['is_success']=='true'||$qrcodescore0==1){
                if($point!=0){
                   $qrcode->scores->scoreIn($qrcode,12,$point); 
                }
                //$qrcode->scores->scoreIn($qrcode,12,$point);
                $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $qrcode->yz_score=1;
                $qrcode->save();
            }else{
                $failscore=ORM::factory('wdy_failscore')->where('bid','=',$bid)->where('sid','=',$sid)->find();
                $failscore->bid=$bid;
                $failscore->sid=$sid;
                 $failscore->type=1;
                $failscore->qid=$qrcode->id;
                $failscore->log=$a['error_response']['code'].$a['error_response']['message'];
                $failscore->save();
                Kohana::$log->add('failscoreid33',print_r($failscore->id,true));
            }
            $qrcode=ORM::factory('wdy_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{
            // if($chscore>0){
            //     $method = 'youzan.crm.customer.points.increase';
            //     $params =[
            //     'fans_id' => $fans_id,
            //     'points' => $chscore,
            //     ];
            //     $a=$client->post($method, $this->methodVersion, $params, $files);
            // }elseif($chscore<0&&$point+$chscore>=0){
            //     $method = 'youzan.crm.customer.points.decrease';
            //     $params =[
            //     'fans_id' => $fans_id,
            //     'points' => -$chscore,
            //     ];
            //     $a=$client->post($method, $this->methodVersion, $params, $files);
            // }else{
            //     $a['response']['is_success']='false';
            //     $a['error_response']['message']='有赞积分不足';
            // }
            $method = 'youzan.crm.customer.points.sync';
            $params =[
            'reason' => $reason,
            'fans_id' => $fans_id,
            'points' => $qrcode->score,
            ];
            $methodVersion = '3.0.0';
            $a=$client->post($method, $methodVersion, $params, $files);
            Kohana::$log->add('result',print_r($a,true));
            if($a['response']['is_success']!='true'){
                $failscore=ORM::factory('wdy_failscore')->where('bid','=',$bid)->where('sid','=',$sid)->find();
                $failscore->bid=$bid;
                $failscore->sid=$sid;
                 $failscore->type=2;
                $failscore->qid=$qrcode->id;
                $failscore->log=$a['error_response']['code'].$a['error_response']['message'];
                $failscore->save();
                Kohana::$log->add('failscoreid3',print_r($failscore->id,true));
            }
        }
        // Kohana::$log->add('wdyaaa',print_r($a,true));
        // $method = 'youzan.crm.fans.points.get';
        // $params =[
        // 'fans_id' => $fans_id,
        // ];
        // $results=$client->post($method, $this->methodVersion, $params, $files);
        // Kohana::$log->add('wdy111',print_r($results,true));
        // $point = $results['response']['point'];
        // if($point&&$point!=$qrcode->score){
        //     $score_change=$point-$qrcode->score;
        //     $qrcode->scores->scoreIn($qrcode,5,$score_change);
        // }
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."wdy/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."wdy/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."wdy/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'wdy_file_cert')->find();
        $file_key = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'wdy_file_key')->find();
        $file_rootca = ORM::factory('wdy_cfg')->where('bid', '=', $bid)->where('key', '=', 'wdy_file_rootca')->find();

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
