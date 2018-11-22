<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rwba extends Controller_Base {

    public $template = 'weixin/rwb/tpl/atpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "rwb";

        $_SESSION =& Session::instance()->as_array();

        parent::before();

        if (Request::instance()->action == 'tag') return;
        if (Request::instance()->action == 'kmpass') return;
        if (Request::instance()->action == 'oauthscript') return;
        if (Request::instance()->action == 'oauthscript2') return;
        if (Request::instance()->action == 'gift') return;
        $this->bid = $_SESSION['rwba']['bid'];
        $this->config = $_SESSION['rwba']['config'];
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/rwba/login');
            header('location:/rwba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('rwb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('rwb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            $todo['all'] = $todo['items'] + $todo['users'];
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }
        //微信卡劵
    public function action_kmpass($id,$iid){
        $this->template = 'tpl/blank';
        self::before();
        $km_text =ORM::factory('rwb_item')->where('id','=',$iid)->find()->km_text;
        $password1 = ORM::factory('rwb_km')->where('id','=',$id)->find()->password1;
        $password2 = ORM::factory('rwb_km')->where('id','=',$id)->find()->password2;
        $password3 = ORM::factory('rwb_km')->where('id','=',$id)->find()->password3;
        $km_text = str_replace("「%a」",$password1,$km_text);
        $password = $password1;
        if($password2){
            $km_text = str_replace("「%b」",$password2,$km_text);
            $password = $password.','.$password2;
            if($password3){
                $km_text = str_replace("「%c」",$password3,$km_text);
                $password = $password.','.$password3;
            }
        }
        // echo $km_text;
        $view = "weixin/rwb/kmi_text";
        $this->template->content = View::factory($view)
            ->bind('km_text', $km_text);
    }
    public function action_index() {
        $this->action_login();
    }
    public function action_gift(){
        $access_token='c6fe127c6e71345f93f1df8b0fc0e7f4';
        $openid='oDt2QjtTeio8l0dBl28SQGhcHSH4';
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $client = new YZTokenClient($access_token);
        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'weixin_openid'=>$openid,
            'fields'=>'user_id',
         ];
        $results = $client->post($method,'3.0.0', $params, $files);
        $user_id = $results['response']['user']['user_id'];
        $method = 'youzan.ump.present.give';
        $params = [
            'activity_id'=>214644,
            'fans_id'=>$user_id,
         ];
        $results = $client->post($method, '3.0.0', $params, $files);
        echo "<pre>";
        var_dump($results);
        echo "<pre>";
        exit();
    }
    public function action_otag(){
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        //$appId = ORM::factory('rwb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;

        //$appSecret = ORM::factory('rwb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;

        $client = new YZTokenClient($this->access_token);

        $tag_name =  ORM::factory('rwb_cfg')->where('bid','=',$this->bid)->where('key','=','tag_name')->find()->value;

        $sql = DB::query(Database::SELECT,"SELECT openid as OP FROM rwb_qrcodes where (`bid`=$this->bid and `ticket`!= 'NULL') or (`bid`=$this->bid and `fopenid`!= 'NULL')");
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
          }
    }

    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('rwb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."rwb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."rwb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."rwb/tmp/$bid/rootca.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        $result['rootca_file_exists'] = file_exists($rootca_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('rwb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }

             $Toname = ORM::factory('rwb_login')->where("id","=",$bid)->find()->user;
             //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dirname($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

             if ($_FILES['rootca']['error'] == 0) {
                @mkdir(dirname($rootca_file),0777,true);
                $ok = move_uploaded_file($_FILES['rootca']['tmp_name'], $rootca_file);
                 $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'rwb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'rwb_file_key', '', file_get_contents($key_file));
            if (file_exists($rootca_file)) $cfg->setCfg($bid, 'rwb_file_rootca', '', file_get_contents($rootca_file));

            //重新读取配置
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('rwb_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        }

        //文案配置
        if ($_POST['text']) {
            // echo "<pre>";
            // var_dump($_POST['text']);
            // echo "<pre>";
            // exit;
            $cfg = ORM::factory('rwb_cfg');
            $qrfile = DOCROOT."rwb/tmp/tpl.$bid.jpg";

            //图片合成
                /*之前的：
                $newfile = DOCROOT."rwb/tmp/news_score4.jpg";
                // @mkdir(dirname($newfile), 0777, true);

                header("Content-type: image/jpg");
                $im = @imagecreate(300, 200)
                    or die("Cannot Initialize new GD image stream");
                $background_color = imagecolorallocate($im, 72,102,150);
                $text_color = imagecolorallocate($im, 255, 255, 255);
                imagestring($im, 5, 0, 0, "jifenchaxun",$text_color);
                imagejpeg($im,$newfile);
                imagedestroy($im);*/
                /*function generateImg($type,$bid,$source, $text1, $font = 'rwb/dist/msyh.ttf') {
                switch ($type) {
                    case 1:
                        $newfile = DOCROOT.'rwb/imgtpl/'.$bid.'/score1.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    case 2:
                        $newfile = DOCROOT.'rwb/imgtpl/'.$bid.'/score2.jpg';
                        @mkdir(dirname($newfile),0777,true);
                        break;
                    case 3:
                        $newfile = DOCROOT.'rwb/imgtpl/'.$bid.'/score3.jpg';
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
                    $default_head_file = DOCROOT."rwb/tmp/head.$bid.jpg";
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

                        $tpl = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        }
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('rwb_cfg');
            // $count = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
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

            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        if ($_POST['tag']){
            $tag = $_POST['tag'];
            $cfg = ORM::factory('rwb_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }

            $this->action_otag();
            $result['fresh'] = 1;
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);

        }
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $tag = $_POST['rsync'];
                $cfg = ORM::factory('rwb_cfg');
                foreach ($tag as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        }

        if ($_POST['cancle']){
            if($this->access_token){
                $cancle = $_POST['cancle'];
                $cfg = ORM::factory('rwb_cfg');
                foreach ($cancle as $k => $v) {
                    $ok=$cfg->setCfg($bid,$k,$v);
                    $result['ok8']+=$ok;
                }
            }
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);

        }

        $result['tpl'] = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find()->id;
        $result['tplhead'] = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
        $result['expiretime'] = ORM::factory('rwb_login')->where('id', '=', $bid)->find()->expiretime;

        //sql 连接深圳服务器
        //$login_user = ORM::factory('rwb_login')->where('id', '=', $bid)->find()->user;
        $access_token = ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
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
        $this->template->content = View::factory('weixin/rwb/admin/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('oauth', $oauth)
            ->bind('bid',$bid);
    }

    public function action_zero() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/rwb/admin/zero')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_lab() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        // if ($_POST['tag']){
        //     $tag = $_POST['tag'];
        //     $cfg = ORM::factory('rwb_cfg');
        //     foreach ($tag as $k=>$v) {
        //         $ok = $cfg->setCfg($bid, $k, $v);
        //         $result['ok6'] += $ok;
        //     }
        //     $this->action_otag();
        //     $result['fresh'] = 1;
        //     $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        // }
        if ($_POST['tag']['tag_name']){//点击刷新
            $tag = $_POST['tag'];
            $cfg = ORM::factory('rwb_cfg');
            foreach ($tag as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok6'] += $ok;
            }
            $result['fresh'] = 1;
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
            // $next_qid=$config['next_qid'];
            // if(!$next_qid) $next_qid=0;
            $users = ORM::factory('rwb_qrcode')->where('bid','=',$bid)->find_all();
            foreach ($users as $k => $v) {
                $labuser[]="($v->bid,$v->id,'{$config['tag_name']}')";
            }
            $sql='INSERT IGNORE INTO rwb_labs (`bid`,`qid`,`lab_name`) VALUES '. join(',',$labuser);
            DB::query(Database::INSERT,$sql)->execute();
        }
        $result['islab'] = ORM::factory('rwb_lab')->where('bid','=',$bid)->where('status','=',1)->count_all();
        $result['alllab'] = ORM::factory('rwb_lab')->where('bid','=',$bid)->count_all();
        // $result['fresh'] = 1;
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/rwb/admin/lab')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_area() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        //2015.12.18选择可参与地区配置
        if ($_POST['area']){
            // print_r($_POST['area']);
            // exit;
            $area = $_POST['area'];
            $cfg = ORM::factory('rwb_cfg');
            // $count = ORM::factory('rwb_cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
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

            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
            //根据bid来存储 地理位置 一起存储
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/rwb/admin/area')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_rsync() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('rwb_cfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/rwb/admin/rsync')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_cancle() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;

        if ($_POST['cancle']){
            //if($this->access_token){
                $cancle = $_POST['cancle'];
                $cfg = ORM::factory('rwb_cfg');
                foreach ($cancle as $k => $v) {
                    $ok=$cfg->setCfg($bid,$k,$v);
                    $result['ok8']+=$ok;
                }
            //}
            $config = ORM::factory('rwb_cfg')->getCfg($bid, 1);

        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/rwb/admin/cancle')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    //有赞授权刷新脚本 七天一次
    public function action_oauthscript($bid=39){
        $shop = ORM::factory('rwb_login')->where('id','=',$bid)->find();
        $url="https://open.youzan.com/oauth/token";
        if($shop->access_token&&$shop->id){
            $data=array(
                "client_id"=>"bfedbd84029bdaf77f",
                "client_secret"=>"ff419e515885e8eb8afb7be243f14e8f",
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

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=bfedbd84029bdaf77f&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/rwba/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"bfedbd84029bdaf77f",
            "client_secret"=>"ff419e515885e8eb8afb7be243f14e8f",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/rwba/callback'
        );
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $result=json_decode($output);

        if(isset($result->access_token))
        {
            require Kohana::find_file("vendor","kdt/YZTokenClient");
            $oauth=new YZTokenClient($result->access_token);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['id'];
            $name = $value['name'];
            // //链接深圳服务器 更新token
            // $mysql_server_name="112.74.102.75";
            // $mysql_username="smfyun";
            // $mysql_pwd="emg4h2q";
            // $mysql_database='smfyun.com';
            // $conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_pwd)or die("error connecting");;
            // mysql_query("set names 'utf8'");
            // mysql_select_db($mysql_database,$conn) or die(mysql_error($conn));

            // $sql="select user_id from user where user_shopid=$sid";
            // $res_id=mysql_fetch_row(mysql_query($sql));
            // //var_dump($value);
            // if($res_id[0]==''){//如果没有就插入
            //     $sql="insert into user(user_name,other,user_shopid,access_token,expires_in,refresh_token)values('$name','1','$sid','$result->access_token','$result->expires_in','$result->refresh_token')";
            //     mysql_query($sql);
            // }else{//有就更新
            //     $sql="update user set access_token='$result->access_token',expires_in='$result->expires_in',refresh_token='$result->refresh_token' where user_shopid='$sid'";
            //     mysql_query($sql);
            // }
            $usershop = ORM::factory('rwb_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("rwba/home")."';</script>";
        }
        //Request::instance()->redirect('rwba/home');
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $this->access_token=ORM::factory('rwb_login')->where('id', '=', $bid)->find()->access_token;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
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
            $qrcode_edit = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])){
                 $qrcode_edit->lock = (int)$_POST['form']['lock'];
                 $qrcode_edit->save();
                }
            }
        }
        $qrcode = ORM::factory('rwb_qrcode')->where('bid', '=', $bid);
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
            $result['fuser'] = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM rwb_qrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
        ))->render('weixin/rwb/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/rwb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //兑换管理
    public function action_orders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
        $order = ORM::factory('rwb_order')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if ($_GET['qid']) {
            $result['qid'] = (int)$_GET['qid'];
            $result['qrcode'] = ORM::factory('rwb_qrcode', $result['qid']);
            $order = $order->where('qid', '=', $result['qid']);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            if($countuser>0){
                $user = ORM::factory('rwb_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
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
        if ($_GET['export']=='csv') {
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $title = array('id', '姓名', '任务名称', '奖品名称', '发送状态（1为成功0为失败）','原因');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $order = ORM::factory('rwb_order')->where('bid', '=', $bid)->find_all();
            foreach ($order as $o) {
                $array = array($o->id, $o->name, $o->task_name, $o->item_name, $o->state, $o->log);
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }
                fputcsv($fp, $array);
            }
            exit;
        }
        $countall = $order->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/rwb/admin/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/rwb/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //积分奖品管理
    public function action_items($action='', $id=0) {
        if ($action == 'add') return $this->action_items_add();
        if ($action == 'edit') return $this->action_items_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
        $item=ORM::factory('rwb_item')->where('bid', '=', $bid);
        $item = $item->reset(FALSE);
        $countall = $item->count_all();
        $iid = ORM::factory('rwb_item')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('rwb_order')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }
         //分页
        if ($_GET['export']=='csv') {
            $value1=$_GET['value1'];
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $order1 =ORM::factory('rwb_km')->where('bid','=',$this->bid)->where('starttime','=',$value1)->find();
            if($order1->password3){
                $title = array('字段1', '字段2', '字段3','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('rwb_km')->where('bid', '=', $this->bid)->where('starttime','=',$value1)->find_all();
                foreach ($order as $o) {
                    $array = array($o->password1, $o->password2, $o->password3, $o->live);
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                        foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                    }
                    fputcsv($fp, $array);
                }
            }elseif ($order1->password2) {
                $title = array('字段1', '字段2','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('rwb_km')->where('bid', '=', $this->bid)->where('starttime','=',$value1)->find_all();
                foreach ($order as $o) {
                    $array = array($o->password1, $o->password2,$o->live);
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                        foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                    }
                    fputcsv($fp, $array);
                }
            } else{
                $title = array('字段1','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('rwb_km')->where('bid', '=', $this->bid)->where('starttime','=',$value1)->find_all();
                foreach ($order as $o) {
                    $array = array($o->password1, $o->live);
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                        foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                    }
                    fputcsv($fp, $array);
                }
            }
            exit;
        }
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/rwb/admin/pages');
        $result['items'] = $item->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '奖品管理';
        $this->template->content = View::factory('weixin/rwb/admin/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('pages',$pages)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
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
        //拉取有赞优惠券优惠码
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];

        }
        // //赠品  present_id title
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        if ($_POST['hongbao']) {
            $item = ORM::factory('rwb_item');
            $item->bid= $bid;
            $item->key= 'hongbao';
            $item->value =$_POST['hongbao']['value'];
            $item->km_content= $_POST['hongbao']['km_content'];
            if (!$_POST['hongbao']['value']) $result['error']['hongbao'] = '请填写完整后再提交（请在基础设置-微信参数，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "rwb:items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('rwba/items');
            }
        }
        if ($_POST['coupon']) {
            $item = ORM::factory('rwb_item');
            $item->bid =$this->bid;
            $item->key= 'coupon';
            $item->value =$_POST['coupon']['value'];

            $item->km_content=$_POST['coupon']['km_content'];

            if (!$_POST['coupon']['value']) $result['error']['coupon'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "rwb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('rwba/items');
            }
        }
        if ($_POST['gift']) {

            $item = ORM::factory('rwb_item');
            $item->bid=$this->bid;
            $item->key= 'gift';
            $item->value =$_POST['gift']['value'];

            $item->km_content=$_POST['gift']['km_content'];


            if (!$_POST['gift']['value']) $result['error']['gift'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "rwb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('rwba/items');
            }
        }
        if ($_POST['yhq']) {

            $item = ORM::factory('rwb_item');
            $item->bid=$this->bid;
            $item->key= 'yzcoupon';
            $item->value =$_POST['yhq']['value'];

            $item->km_content=$_POST['yhq']['km_content'];
            if (!$_POST['yhq']['value']) $result['error']['yhq'] = '请填写完整后再提交';
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "rwb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('rwba/items');
            }
        }
        if ($_POST['yhm']){
            $text = $_POST['yhm']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $time=time();
            $text = $_POST['yhm']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $item = ORM::factory('rwb_item');
            $item->bid=$this->bid;
            $item->key= 'yhm';
            $item->value=$time;
            $item->km_text=$text;
            $item->km_content=$_POST['yhm']['km_content'];
            if (!$_POST['yhm']['km_content'] || !is_uploaded_file($_FILES['pic']['tmp_name'])) $result['error']['yhm'] = '请填写完整后再提交';
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error']['yhm'] ='不是Excel文件，重新上传';
                }else{
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    //echo $highestRow.'<br>';
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    //echo $highestColumm.'<br>';
                    // /** 循环读取每个单元格的数据 */
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    //echo '<pre>';
                    //var_dump($dataset);
                    //echo '</pre>';
                    //exit;
                    //$km =ORM::factory('kmi_km');
                    foreach ( $dataset as $data ) {
                        $km =ORM::factory('rwb_km');
                        $km->bid = $this->bid;
                        $km->starttime = $time;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
            }
            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "rwb:items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('rwba/items');
            }

        }
        if ($_POST['kmi']){

            $item = ORM::factory('rwb_item');
            $item->bid=$this->bid;
            $item->key= 'kmi';
            $item->value=$_POST['kmi']['value'].'&'.$_POST['kmi']['url'];

            $item->km_content=$_POST['kmi']['km_content'];

            if (!$_POST['kmi']['value']) $result['error']['rwb'] = '请填写完整后再提交';

            if (!$result['error']) {
                $item->save();
                $mem = Cache::instance('memcache');
                $key = "rwb:$items:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('rwba/items');
            }
        }
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->content = View::factory('weixin/rwb/admin/items_add')
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);

    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
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
        //拉取有赞优惠券优惠码
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];

        }
        // //赠品  present_id title
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        $item1 = ORM::factory('rwb_item')->where('bid','=',$bid)->where('id','=',$id)->find()->as_array();
        $this->template->title = '修改';
        if(isset($_POST['edit'])){
            $item = ORM::factory('rwb_item')->where('bid','=',$bid)->where('id','=',$id)->find();
            $edit = $_POST['edit'];
            $item->km_content=$edit['km_content'];
            if($edit['key']=='kmi'){
                $item->value=$edit['value'].'&'.$edit['url'];
            }elseif($edit['key']=='yhm'){
                $text=$edit['km_text'];
                $text = str_replace("<p>",'', $text);
                $text = str_replace("</p>",'',$text);
                $item->km_text=$text;
            }else{
                if($edit['key']=='coupon'){
                    $edit['value']=$edit['coupon'];
                }elseif($edit['key']=='yzcoupon'){
                    $edit['value']=$edit['yzcoupon'];
                }elseif($edit['key']=='gift'){
                    $edit['value']=$edit['gift'];
                }
                if(!$edit['value']){
                    $result['error']='奖品内容不完整，提交失败';
                }
                $item->value=$edit['value'];
            }
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error']['yhm'] ='不是Excel文件，重新上传';
                }else{
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    //echo $highestRow.'<br>';
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    //echo $highestColumm.'<br>';
                    // /** 循环读取每个单元格的数据 */
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    foreach ( $dataset as $data ) {
                        $km =ORM::factory('rwb_km');
                        $km->bid = $this->bid;
                        $km->starttime = $item->value;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
            }
            if (!$result['error']) {
                $item->save();
                 Request::instance()->redirect('rwba/items');
            }
           
        }
        $this->template->content = View::factory('weixin/rwb/admin/edit')
            ->bind('result',$result)
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('item', $item1);
    }
     public function action_items_delete($id){
        $value =ORM::factory('rwb_item')->where('bid','=',$this->bid)->where('id','=',$id)->find()->value;
        $type =ORM::factory('rwb_item')->where('bid','=',$this->bid)->where('id','=',$id)->find()->key;
        $sql = DB::query(Database::DELETE,"DELETE FROM `rwb_items` where `bid` = $this->bid and `id` = $id");
        $sql->execute();
        Request::instance()->redirect('rwba/items');
    }
      //积分奖品管理
    public function action_tasks($action='', $id=0) {
        if ($action == 'add') return $this->action_tasks_add();
        if ($action == 'edit') return $this->action_tasks_edit($id);
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);

        $result['countall'] = $countall = ORM::factory('rwb_task')->where('bid', '=', $bid)->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/rwb/admin/pages');

        $result['tasks'] = ORM::factory('rwb_task')->where('bid', '=', $bid)->order_by('endtime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '任务管理';
        $this->template->content = View::factory('weixin/rwb/admin/tasks')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }
    //奖品发送明细
    public function action_items_num($tid) {
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
        $items_num = ORM::factory('rwb_sku')->where('bid', '=', $bid)->where('tid', '=', $tid);
        $items_num = $items_num->reset(FALSE);
        $result['countall'] = $items_num->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $result['countall'],
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/rwb/admin/pages');
        $result['tid_name'] = ORM::factory('rwb_task')->where('bid', '=', $bid)->where('id', '=', $tid)->find()->name;
        $result['items_num'] = $items_num->order_by('id', 'ASC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = $result['tid_name'].'的奖品发送情况';
        $this->template->content = View::factory('weixin/rwb/admin/items_num')
            ->bind('bid',$bid)
            ->bind('result', $result)
            ->bind('pages', $pages)
            ->bind('config', $config);
    }

    public function action_tasks_add() {
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);

        if ($_POST['data']) {
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
            $task = ORM::factory('rwb_task');
            $task->values($_POST['data']);
            $task->bid = $bid;
            $past=ORM::factory('rwb_task')->where('bid','=',$bid)->where('endtime','>',time())->find();
            if($_POST['data']['begintime']>$_POST['data']['endtime']||$_POST['data']['endtime']<time()){
                $result['error'] = '时间设置不合理，请检查后再提交';
            }
            if(!$config['mgtpl']){
                $result['error'] = '请在【基础设置】->【微信参数】里面配置【下发奖品时的消息模板ID】';
            }
            if($past && $past->begintime!=$past->endtime ){
                $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
            }
            if (!$_POST['data']['name'] || !$_POST['data']['endtime']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $task->save();
                foreach ($_POST['goal'] as $k => $v) {
                    $sku = ORM::factory('rwb_sku');
                    $sku->bid = $bid;
                    $sku->lv = $k;
                    $sku->tid = $task->id;
                    $sku->num = $v;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k];
                    $sku->text = $_POST['text'][$k];
                    $sku->save();
                }

                Request::instance()->redirect('rwba/tasks');
            }
        }
        $items = ORM::factory('rwb_item')->where('bid','=',$bid)->find_all();
        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新任务';
        $this->template->content = View::factory('weixin/rwb/admin/tasks_add')
            ->bind('bid',$bid)
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_tasks_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('rwb_cfg')->getCfg($bid);
        $task = ORM::factory('rwb_task', $id);
        if (!$task || $task->bid != $bid) die('404 Not Found!');
        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            $begin=$task->begintime;
            $task->endtime=$begin;
            $task->save();
            Request::instance()->redirect('rwba/tasks');
        }
        if ($_POST['data']) {
            $_POST['data']['begintime']=strtotime($_POST['data']['begintime']);
            $_POST['data']['endtime']=strtotime($_POST['data']['endtime']);
            $task->values($_POST['data']);
            $task->bid = $bid;
            if($_POST['data']['begintime']>$_POST['data']['endtime']||$_POST['data']['endtime']<time()){
                $result['error'] = '时间设置不合理，请检查后再提交';
            }
            if(!$config['mgtpl']){
                $result['error'] = '请在【基础设置】->【微信参数】里面配置【下发奖品时的消息模板ID】';
            }
            if($past && $past->begintime!=$past->endtime ){
                $result['error'] = '还有有效任务正在进行，待有效任务过期或者终止有效任务再新建任务';
            }
            if (!$_POST['data']['name'] || !$_POST['data']['endtime']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $task->save();
                // echo "<pre>";
                // var_dump($_POST['goal']);
                // echo "</pre>";

                foreach ($_POST['goal'] as $k => $v) {
                    $sku = $skus = ORM::factory('rwb_sku')->where('bid', '=', $bid)->where('tid', '=', $id)->where('lv', '=', $k)->find();
                    $ordernum=ORM::factory('rwb_order')->where('bid','=',$bid)->where('kid','=',$sku->id)->where('state','=',1)->count_all();
                    $sku->bid = $bid;
                    $sku->tid = $task->id;
                    $sku->num = $v;
                    $sku->lv =$k;
                    $sku->iid = $_POST['prize'][$k];
                    $sku->stock = $_POST['stock'][$k]+$ordernum;
                    $sku->text = $_POST['text'][$k];
                    $sku->save();
                    $form = $k+1;
                    //echo 'form'.$form.'<br>';
                }
                $mysql = ORM::factory('rwb_sku')->where('bid', '=', $this->bid)->where('tid', '=', $id)->count_all();
                //echo 'mysql'.$mysql.'<br>';
                //exit;
                if($mysql>$form){
                    for ($i=0; $i <$mysql-$form ; $i++) {
                        $result = DB::query(Database::DELETE,"DELETE  from rwb_skus  where bid=$bid and tid =$id and lv= $form+$i")->execute();
                    }
                }
                Request::instance()->redirect('rwba/tasks');
            }
        }

        $_POST['data'] = $result['task'] = $task->as_array();
        $result['action'] = 'edit';
        $items = ORM::factory('rwb_item')->where('bid','=',$bid)->find_all();
        $skus = ORM::factory('rwb_sku')->where('bid','=',$bid)->where('tid','=',$id)->find_all();
        $result['title'] = $this->template->title = '修改任务';
        $this->template->content = View::factory('weixin/rwb/admin/tasks_add')
            ->bind('bid',$bid)
            ->bind('skus', $skus)
            ->bind('items', $items)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['rwba']['admin'] < 1) Request::instance()->redirect('rwba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('rwb_login');
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
        ))->render('weixin/rwb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/rwb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['rwba']['admin'] < 2) Request::instance()->redirect('rwba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('rwb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('rwb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('rwba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/rwb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['rwba']['admin'] < 2) Request::instance()->redirect('rwba/home');

        $bid = $this->bid;

        $login = ORM::factory('rwb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('rwb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('rwba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('rwb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('rwba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/rwb/admin/logins_add')
            ->bind('result', $result)
            ->bind('bid', $id)
            ->bind('config', $config);
    }
    public function action_empty_ticket($bid){
        $sql = DB::query(Database::UPDATE,"UPDATE rwb_qrcodes set ticket='' where bid =$bid");
        $result = $sql->execute();
        Request::instance()->redirect('rwba/logins/edit/'.$bid);
    }
    public function action_login() {
        $this->template = 'weixin/rwb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('rwb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

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
                //     $expiretime = strtotime(ORM::factory('rwb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find()->expiretime) ;
                // }
                //从smfyun拉取
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                }else{
                // if ($expiretime&&$expiretime<time()) {
                //     $this->template->error = '您的账号已到期';
                // } else {

                    $_SESSION['rwba']['bid'] = $biz->id;
                    $_SESSION['rwba']['user'] = $_POST['username'];
                    $_SESSION['rwba']['admin'] = $biz->admin; //超管
                    $_SESSION['rwba']['config'] = ORM::factory('rwb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['rwba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/rwba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['rwba'] = null;
        header('location:/rwba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "rwb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('rwb_score')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('rwb_qrcode')->table_name())
            ->set(array('score' => '0','yz_score' =>'0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            $this->config = ORM::factory('rwb_cfg')->getCfg($this->bid, 1);
            if($this->config['switch']==1){
                $this->access_token=ORM::factory('rwb_login')->where('id', '=', $this->bid)->find()->access_token;
                require_once Kohana::find_file("vendor/kdt","YZTokenClient");
                $client = new YZTokenClient($this->access_token);
                $userarr = ORM::factory('rwb_qrcode')->where('bid','=',$this->bid)->find_all();
                foreach ($userarr as $user) {
                    $weixin_openid=$user->openid;
                    $method ='youzan.shop.get';
                    $params =[
                    ];
                    $result =$client->post($method, $this->methodVersion, $params, $files);
                    $kdt_id = $result['response']['id'];
                    $method='youzan.users.weixin.follower.get';
                    $params=[
                        'weixin_openid'=>$weixin_openid,
                    ];
                    $results=$client->post($method, $this->methodVersion, $params, $files);
                    $user_id = $results['response']['user']['user_id'];
                    $user_id = $results['response']['user']['user_id'];
                    $method = 'youzan.crm.fans.points.get';
                    $params =[
                    'fans_id' => $user_id,
                    ];
                    $results=$client->post($method, $this->methodVersion, $params, $files);
                    $point = $results['response']['point'];
                    $method = 'youzan.crm.fans.points.payout.get';
                    $params =[
                    'fans_id' => $user_id,
                    'kdt_id' => $kdt_id,
                    'points' => $point,
                    ];
                    $a=$client->post($method, $this->methodVersion, $params, $files);

                }
            }
            Request::instance()->redirect('rwba/zero');
        }
    }
    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        $this->config=ORM::factory('rwb_cfg')->getCfg($this->bid,1);
        if($this->config['totle']!=date('Y-m-d',time())){
            if($this->config['totle']){
                $time_totle=strtotime($this->config['totle']);
            }else{
                $time_totle=0;
            }
            $daytype='%Y-%m-%d';
            $length=10;
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `rwb_qrcodes` where bid=$this->bid and  jointime >= $time_totle ORDER BY `time` DESC ")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from rwb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from rwb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from rwb_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' or FROM_UNIXTIME(`jointime`, '$daytype')='$time')")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `rwb_orders` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
            foreach ($newadd as $key => $value) {
                $totle=ORM::factory('rwb_totle')->where('bid','=',$this->bid)->where('time_quantum','=',$value['time'])->find();
                $totle->bid=$this->bid;
                $totle->fans_num=$value['fansnum'];
                $totle->time_quantum=$value['time'];
                $totle->timestamp=strtotime($value['time']);
                $totle->haibao_num=$value['tickets'];
                $totle->qr_num=$value['actnums'];
                $totle->order_num=$value['ordernums'];
                $totle->save();
                
            }
            $ok=ORM::factory('rwb_cfg')->setCfg($this->bid,'totle',date('Y-m-d',time()));
            $this->config=ORM::factory('rwb_cfg')->getCfg($this->bid,1);
        }else{
            $time_today=strtotime(date('Y-m-d',time()));
            $fnum=ORM::factory('rwb_qrcode')->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->count_all();
            $tnum=ORM::factory('rwb_qrcode')->where('bid','=',$this->bid)->where('jointime','>=',$time_today)->where('ticket','!=','')->count_all();
            $qnum=ORM::factory('rwb_qrcode')->where('bid','=',$this->bid)->and_where_open()->where('jointime','>=',$time_today)->or_where('lastupdate','>=',$time_today)->and_where_close()->count_all();
            $onum=ORM::factory('rwb_order')->where('bid','=',$this->bid)->where('lastupdate','>=',$time_today)->count_all();
            if($fnum>0||$tnum>0||$qnum>0||$onum>0){
                $totle=ORM::factory('rwb_totle')->where('bid','=',$this->bid)->where('time_quantum','=',date('Y-m-d',time()))->find();
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
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from rwb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from rwb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];
                //活动参与人数
                //$actnums=DB::query(Database::SELECT,"SELECT  count(distinct qid) as actnum FROM `rwb_scores` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                $actnums=DB::query(Database::SELECT,"select count(openid) as actnum from rwb_qrcodes where bid=$this->bid and (FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' or FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over')")->execute()->as_array();
                $newadd[0]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"select count(id) as ordernum FROM `rwb_orders` where bid =$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
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
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `rwb_totles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/rwb/admin/pages');
            $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `rwb_totles` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"SELECT sum(fans_num) as fansnum from rwb_totles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //产生海报数
                $ticket=DB::query(Database::SELECT,"SELECT sum(haibao_num) as tickets from rwb_totles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];
                //参加活动人数
                $actnums=DB::query(Database::SELECT,"SELECT sum(qr_num) as actnum from rwb_totles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['actnums']=$actnums[0]['actnum'];
                //奖品兑换数量
                $ordernums= DB::query(Database::SELECT,"SELECT sum(order_num) as ordernum FROM `rwb_totles` where bid =$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['ordernums']=$ordernums[0]['ordernum'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '%Y-%m-%d')as time FROM `rwb_totles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/rwb/admin/stats_totle')
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
}
