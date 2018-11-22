<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_wzba extends Controller_Base {

    public $template = 'weixin/wzb/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $we;
    public $appId = 'wxd0b3a6ff48335255';
    public $appSecret = 'c5c35a468cc1440da618aa3f598a53d9';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'zhibo';
    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['wzba']['bid'];
        $this->config = $_SESSION['wzba']['config'];
        $this->access_token=ORM::factory('wzb_login')->where('id', '=', $this->bid)->find()->yz_access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/wzba/login');
            header('location:/wzba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('wzb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('wzb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('wzb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();
            $todo['flag']=1;
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

    //系统配置
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=8b719902ac4d921bdb&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/wzba/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"8b719902ac4d921bdb",
            "client_secret"=>"33054b3b0a38988342879da6231d2d5a",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/wzba/callback'
        );
        require Kohana::find_file("vendor/youzan","YZOauthClient");
        $token = new YZOauthClient( '8b719902ac4d921bdb' , '95065db0a9dcef9bcf455d7a54af8615' );
        $keys = array();
        $type = 'code';//如要刷新access_token，这里的值为refresh_token
        $keys['code'] = $code;//如要刷新access_token，这里为$keys['refresh_token']
        $keys['redirect_uri'] = 'http://'.$_SERVER["HTTP_HOST"].'/wzba/callback';
        $result = $token->getToken( $type , $keys );

        if(isset($result['access_token']))
        {
            require Kohana::find_file("vendor/youzan","YZTokenClient");
            $client = new YZTokenClient($result['access_token']);

            $method = 'youzan.shop.get';//要调用的api名称
            $methodVersion = '3.0.0';//要调用的api版本号

            $params = [

            ];
            $value = $client->post($method, $methodVersion, $params, $files);
            // var_dump($value);
            // exit;
            if($value['response']){
                $sid = $value['response']['id'];
                $name = $value['response']['name'];
                $usershop = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
                $usershop->logo = $value['response']['logo'];
                $usershop->yz_access_token = $result['access_token'];
                $usershop->yz_expires_in = time()+$result['expires_in'];
                $usershop->yz_refresh_token = $result['refresh_token'];
                $usershop->shopid = $sid;
                $_SESSION['wzba']['sid'] = $sid;
                $usershop->name = $name;
                $usershop->url = 'rtmp://video-center.alivecdn.com/AppName/'.$this->bid.'?vhost=live.smfyun.com';
                // $usershop->url = 'rtmp://video-center.alivecdn.com/AppName/'.$sid.'?vhost='.$usershop->domain.'.smfyun.com';
                $usershop->save();
                echo "<script>alert('授权成功');location.href='".URL::site("wzba/home")."';</script>";
            }else if($value['error_response']){
                echo "<script>alert('获取店铺信息失败，code：".$value['error_response']['code']."msg：".$value['error_response']['msg']."');</script>";
            }
        }
        //Request::instance()->redirect('wzba/home');
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $user = ORM::factory('wzb_login')->where('id','=',$bid)->find();
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('wzb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."wzb/tmp/$bid/cert.pem";
        $key_file = DOCROOT."wzb/tmp/$bid/key.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);

        //微信授权
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$this->appId;
        $ctoken = $mem->get($cachename1);//获取token
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$ctoken;
        $post_data = array(
          'component_appid' =>$this->appId
        );
        $post_data = json_encode($post_data);
        $res = json_decode($this->request_post($url, $post_data),true);

        $pre_auth_code = $res['pre_auth_code'];
        $pre_auth_code = substr($pre_auth_code,14);//去掉前缀 preauthcode@@@
        //提交表单
        if ($_GET['wx']) {
            $cfg = ORM::factory('wzb_cfg');

            if ($_GET['auth_code']) {
                $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
                $post_data = array(
                  'component_appid' =>$this->appId,
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
                // $user = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
                //$user->access_token = $access_token;
                $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
                $user->appid = $appid;
                $user->expires_in = $expires_in;
                $user->auth_info = $auth_info;
                $user->save();
                $cachename1 ='wzb.access_token'.$this->bid;
                $mem->set($cachename1, $access_token, 5400);//有效期两小时
                $result['err1'] = '授权成功';
            }
            $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
            $actives = 'wx';
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('wzb_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        }
        //微信配置
        if ($_POST['cfg']) {
            $cfg = ORM::factory('wzb_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }
            //证书上传
            if ($_FILES['cert']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($cert_file),0777,true);
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                $result['ok2'] += $ok;
                $result['err2'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                umask(0002);
                @mkdir(dirname($key_file),0777,true);
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                $result['ok2'] += $ok;
                $result['err2'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'wzb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'wzb_file_key', '', file_get_contents($key_file));

            //重新读取配置
            $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        }
        //播放时间可选
        if ($_POST['time']) {
            $cfg = ORM::factory('wzb_cfg');

            foreach ($_POST['time'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
            }

            //重新读取配置
            $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        }
        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('wzb_cfg');
            $qrfile = DOCROOT."wzb/tmp/tpl.$bid.jpg";

            //默认头像
            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."wzb/tmp/head.$bid.jpg";
                    $cfg->setCfg($bid, 'wzbtplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
                    // @unlink($default_head_file);
                    // move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }
            if ($_FILES['pic3']['error'] == 0) {
                if ($_FILES['pic3']['size'] > 1024*100) {
                    $result['err3'] = '分享图标文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."wzb/tmp/share.$bid.jpg";
                    $cfg->setCfg($bid, 'wzbtplshare', '', file_get_contents($_FILES['pic3']['tmp_name']));
                    // @unlink($default_head_file);
                    // move_uploaded_file($_FILES['pic2']['tmp_name'], $default_head_file);
                }
            }
            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok3'] += $ok;
            }
            // if (!$result['err3']) {
            //     foreach ($_POST['text'] as $k=>$v) {
            //         $ok = $cfg->setCfg($bid, $k, $v);
            //         if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
            //         $result['ok3'] += $ok;
            //     }

            //     //海报配置
            //     foreach ($_POST['px'] as $k=>$v) {
            //         $ok = $cfg->setCfg($bid, $k, $v);
            //         if (!isset($v)) $ok = $cfg->delCfg($bid, $k);
            //         $result['ok3'] += $ok;

            //         //更新海报缓存 key
            //         if ($result['ok3'] && file_exists($qrfile)) {
            //             touch($qrfile);

            //             $tpl = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtpl')->find();
            //             if ($tpl) {
            //                 $tpl->lastupdate = time();
            //                 $tpl->save();
            //             }

            //             $tplhead = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtplhead')->find();
            //             if ($tplhead) {
            //                 $tplhead->lastupdate = time();
            //                 $tplhead->save();
            //             }
            //         }
            //     }
            // }

            //重新读取配置
            $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtpl')->find()->id;
        $result['tplhead'] = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtplhead')->find()->id;
        $result['tplshare'] = ORM::factory('wzb_cfg')->where('bid', '=', $bid)->where('key', '=', 'wzbtplshare')->find()->id;

        // exit;
        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/wzb/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('user',$user)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('user',$user)
            ->bind('actives',$actives)
            ->bind('config', $config);
    }
    public function action_marketing() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);

        if ($_POST['text']) {
            $cfg = ORM::factory('wzb_cfg');
            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok3'] += $ok;
            }
            //重新读取配置
            $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        }

        $access_token = ORM::factory('wzb_login')->where('id', '=', $bid)->find()->yz_access_token;
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $client = new YZTokenClient($access_token);
        $method = 'youzan.ump.coupons.unfinished.search';
        $methodVersion = '3.0.0';
        $params = [
            'fields' => 'title,group_id',
        ];
        $coupon = $client->post($method, $methodVersion, $params, $files);
        $this->template->title = '营销模块';
        $this->template->content = View::factory('weixin/wzb/admin/marketing')
            ->bind('result', $result)
            ->bind('coupon',$coupon)
            ->bind('config', $config);
    }
    public function action_lottery() {
        $bid = $this->bid;
        if($_POST['lottery']){
            $cfg = ORM::factory('wzb_cfg');
            foreach ($_POST['lottery'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }
            if($_POST['lottery']['switch']==1){
                if(!$_POST['data1']['other']||!$_POST['data1']['num']||!$_POST['data1']['other']||!$_POST['data1']['num']||!$_POST['data1']['other']||!$_POST['data1']['num']||!$_POST['data1']['other']||!$_POST['data1']['num']){
                    $result['err'] = '幸运抽奖转盘功能开启时，务必将奖品资料添加完整！';
                }else{
                    // for ($i=1; $i <=4 ; $i++) {
                    //     $data.$i = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',$i)->find();
                    //     $data.$i->bid = $bid;
                    //     $data.$i->item = $i;
                    //     $data.$i->type = $_POST['data'.$i]['type'];
                    //     $data.$i->other = $_POST['data'.$i]['other'];
                    //     $data.$i->num = $_POST['data'.$i]['num'];
                    //     $data.$i->save();
                    // }
                        $data1 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',1)->find();
                        $data1->bid = $bid;
                        $data1->item = 1;
                        $data1->type = $_POST['data1']['type'];
                        $data1->other = $_POST['data1']['other'];
                        $data1->num = $_POST['data1']['num'];
                        if ($_FILES['pic1']['error'] == 0) {
                            if ($_FILES['pic1']['size'] > 1024*100) {
                                $result['err'] = '奖品1图片不能超过 100K';
                            } else {
                                $data1->pic = file_get_contents($_FILES['pic1']['tmp_name']);
                            }
                        }
                        $data1->save();
                        $data2 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',2)->find();
                        $data2->bid = $bid;
                        $data2->item = 2;
                        $data2->type = $_POST['data2']['type'];
                        $data2->other = $_POST['data2']['other'];
                        $data2->num = $_POST['data2']['num'];
                        if ($_FILES['pic2']['error'] == 0) {
                            if ($_FILES['pic2']['size'] > 1024*100) {
                                $result['err'] = '奖品2图片不能超过 100K';
                            } else {
                                $data2->pic = file_get_contents($_FILES['pic2']['tmp_name']);
                            }
                        }
                        $data2->save();
                        $data3 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',3)->find();
                        $data3->bid = $bid;
                        $data3->item = 3;
                        $data3->type = $_POST['data3']['type'];
                        $data3->other = $_POST['data3']['other'];
                        $data3->num = $_POST['data3']['num'];
                        if ($_FILES['pic3']['error'] == 0) {
                            if ($_FILES['pic3']['size'] > 1024*100) {
                                $result['err'] = '奖品3图片不能超过 100K';
                            } else {
                                $data3->pic = file_get_contents($_FILES['pic3']['tmp_name']);
                            }
                        }
                        $data3->save();
                        $data4 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',4)->find();
                        $data4->bid = $bid;
                        $data4->item = 4;
                        $data4->type = $_POST['data4']['type'];
                        $data4->other = $_POST['data4']['other'];
                        $data4->num = $_POST['data4']['num'];
                        if ($_FILES['pic4']['error'] == 0) {
                            if ($_FILES['pic4']['size'] > 1024*100) {
                                $result['err'] = '奖品4图片不能超过 100K';
                            } else {
                                $data4->pic = file_get_contents($_FILES['pic4']['tmp_name']);
                            }
                        }
                        $data4->save();
                }
            }
        }
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $data1 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',1)->find();
        $data2 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',2)->find();
        $data3 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',3)->find();
        $data4 = ORM::factory('wzb_lottery')->where('bid','=',$bid)->where('item','=',4)->find();
        $access_token = ORM::factory('wzb_login')->where('id', '=', $bid)->find()->yz_access_token;
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $client = new YZTokenClient($access_token);
        $method = 'youzan.ump.coupons.unfinished.search';
        $methodVersion = '3.0.0';
        $params = [
            'fields' => 'title,group_id',
        ];
        $coupons = $client->post($method, $methodVersion, $params, $files);

        $method = 'youzan.ump.presents.ongoing.all';
        $methodVersion = '3.0.0';
        $params = [
            'fields' => 'title,present_id',
        ];
        $gifts = $client->post($method, $methodVersion, $params, $files);

        $this->template->title = '幸运抽奖转盘';
        $this->template->content = View::factory('weixin/wzb/admin/lottery')
                       ->bind('config',$config)
                       ->bind('coupons',$coupons)
                       ->bind('result',$result)
                       ->bind('gifts',$gifts)
                       ->bind('data1',$data1)
                       ->bind('data2',$data2)
                       ->bind('data3',$data3)
                       ->bind('data4',$data4);
    }
    public function action_lottery_history() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $sweep = ORM::factory('wzb_sweepstake')->where('bid','=',$bid)->where('state','=',1);
        $sweep = $sweep->reset(FALSE);
        $result['countall'] = $countall = $sweep->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');
        $result['sort'] = 'id';
        if ($result['sort']) $sweep = $sweep->order_by($result['sort'], 'DESC');
        $result['sweep'] = $sweep->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '幸运抽奖转盘';
        $this->template->content = View::factory('weixin/wzb/admin/lottery_history')
                    ->bind('pages', $pages)
                    ->bind('result', $result)
                    ->bind('config', $config);
    }
    public function action_download() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $this->template->title = '安卓端APK下载';
        $this->template->content = View::factory('weixin/wzb/admin/download');
    }
    public function action_download_ios() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $this->template->title = 'IOS端应用下载';
        $this->template->content = View::factory('weixin/wzb/admin/download_ios');
    }

    public function action_get_qrcode(){
        $sku_price['100GB'] = 60;
        $sku_price['500GB'] = 275;
        $sku_price['1TB'] = 542.72;
        $sku_price['5TB'] = 2611.2;
        $sku_price['10TB'] = 5017.6;
        $sku_price['50TB'] = 24064;
        $sku_price['100TB'] = 46080;
        if($_POST['data']){
            require Kohana::find_file("vendor/kdt","KdtApiClient");
            switch ($_POST['type']) {
                case 'month':
                    $product_name = '包月：神码云直播';
                    $price = 500;
                    break;
                case 'year':
                    $product_name = '包年：神码云直播';
                    $price = 720;
                    break;
                case 'stream':
                    $product_name = '神码云直播流量'.$_POST['stream'];
                    $price = $sku_price[$_POST['stream']];
                    break;
            }
            $appId = 'c27bdd1e37cd8300fb';
            $appSecret = '3e7d8db9463b1e2fd92083418677c638';
            $client = new KdtApiClient($appId, $appSecret);

            $method = 'kdt.pay.qrcode.createQrCode';
            $params = [
                'qr_name' =>$product_name,

                'qr_price' => $price*100,
                //'qr_price' => 1,
                'qr_type' => 'QR_TYPE_DYNAMIC',
                // 'qr_source'=>$_POST['type'].'.'.$_POST['stream'],//类型和流量拼接  给了跟不给  获取不到  没有给的意义
            ];
            $test=$client->post($method, $params);
            header('Content-type: image/jpg');
            // echo "<img src='".$test['response']['qr_code']."'>";
            // echo $test['response']['qr_id'];

            $data = array('imgurl' => "<img src='".$test['response']['qr_code']."'>",'imgid' =>$test['response']['qr_id'],'url'=>$test['response']['qr_url']);
            echo json_encode($data);
            exit;
        }
    }
    public function action_notify(){
        $sku_data['100GB'] = 100;
        $sku_data['500GB'] = 500;
        $sku_data['1TB'] = 1024;
        $sku_data['5TB'] = 5120;
        $sku_data['10TB'] = 10240;
        $sku_data['50TB'] = 51200;
        $sku_data['100TB'] = 102400;
        if($_POST['qrid']){
            require_once Kohana::find_file("vendor/kdt","KdtApiClient");

            $appId = 'c27bdd1e37cd8300fb';
            $appSecret = '3e7d8db9463b1e2fd92083418677c638';
            $client = new KdtApiClient($appId, $appSecret);

            $method1 = 'kdt.trades.qr.get';
            $params = [
                'status' =>'TRADE_RECEIVED'
            ];

            $resultarr=$client->post($method1,$params);
            $qrarr=$resultarr["response"]["qr_trades"];
            $flag=false;
            for($i=0;$qrarr[$i];$i++){
                if($qrarr[$i]['qr_id']==$_POST['qrid']){
                    $type = explode('.', $qrarr[$i]['qr_source'])[0];
                    $stream_data = $_POST['stream_data'];
                    $order = ORM::factory('wzb_order');
                    $order->bid = $this->bid;
                    $order->tid = 'E'.date('YmdHis');
                    $order->time = time();
                    $order->type = $_POST['type'];
                    $order->title = $qrarr[$i]['qr_name'];
                    $order->price = $qrarr[$i]['qr_price'];//元 为单位
                    $order->save();
                    switch ($_POST['type']) {
                        case 'month':
                            $shop = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
                            $shop->expiretime = date( "Y-m-d", strtotime( "$shop->expiretime +1 month" ) );
                            $flag = '成功续费一个月！';
                            break;
                        case 'year':
                            $shop = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
                            $shop->expiretime = date( "Y-m-d", strtotime( "$shop->expiretime +1 year" ) );
                            $flag = '成功续费有效期一年！';
                            break;
                        case 'stream':
                            $shop = ORM::factory('wzb_login')->where('id','=',$this->bid)->find();
                            $shop->stream_data = $shop->stream_data+$sku_data[$stream_data];
                            $flag = '充值'.$stream_data.'流量成功！';
                            break;
                    }
                    $shop->save();
                    echo $flag;
                }
            }
        }
        exit;
    }
    //会员中心-基本信息
    public function action_information() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $shop = ORM::factory('wzb_login')->where('id','=',$bid)->find();
        $this->template->title = '基本信息';
        $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM wzb_lives where bid=$bid ");
        $num = $sql->execute()->as_array();
        $use =  $num[0]['CT'];
        $all = ORM::factory('wzb_login')->where('id','=',$bid)->find()->stream_data;
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('wzb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }
        $this->template->content = View::factory('weixin/wzb/admin/information')
                                    ->bind('shop',$shop)
                                    ->bind('result',$result)
                                    ->bind('use',$use)
                                    ->bind('all',$all);
    }
    //会员中心-流量中心
    public function action_flowcenter() {
        $bid = $this->bid;
        $this->template->title = '流量中心';
        $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM wzb_lives where bid=$bid ");
        $num = $sql->execute()->as_array();
        $use =  $num[0]['CT'];
        $all = ORM::factory('wzb_login')->where('id','=',$bid)->find()->stream_data;
        $this->template->content = View::factory('weixin/wzb/admin/flowcenter')->bind('use',$use)->bind('all',$all);
    }
    public function action_flowcenter_history() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $this->template->title = '流量中心';
        $lives = ORM::factory('wzb_live')->where('bid','=',$bid);
        $lives = $lives->reset(FALSE);
        $result['countall'] = $countall = $lives->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');

        $result['lives'] = $lives->order_by('start_time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $all = ORM::factory('wzb_login')->where('id','=',$bid)->find()->stream_data;
        $this->template->content = View::factory('weixin/wzb/admin/flowcenter_history')
                                ->bind('all',$all)
                                ->bind('result',$result)
                                ->bind('pages',$pages);
    }
    //会员中心-购买记录
    public function action_buymentrecord() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        $this->template->title = '购买记录';
        $orders = ORM::factory('wzb_order')->where('bid','=',$bid);
        $orders = $orders->reset(FALSE);
        $result['countall'] = $countall = $orders->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');

        $result['orders'] = $orders->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/wzb/admin/buymentrecord')->bind('pages',$pages)->bind('result',$result);
    }
    //直播分析
    public function action_analyze($action='', $id=0) {
        $bid=$this->bid;

        $lives = ORM::factory('wzb_live')->where('bid','=',$bid);
        $lives = $lives->reset(FALSE);
        $result['countall'] = $countall = $lives->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');

        if ($result['sort']) $lives = $lives->order_by('start_time', $result['sort']);
        $result['lives'] = $lives->order_by('start_time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '直播分析';
        $this->template->content = View::factory('weixin/wzb/admin/analyze')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);

        $result['skus'] = ORM::factory('wzb_sku')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '返还管理';
        $this->template->content = View::factory('weixin/wzb/admin/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('wzb_sku');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('wzba/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/wzb/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);

        $sku = ORM::factory('wzb_sku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('wzba/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('wzba/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/wzb/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $this->access_token=ORM::factory('wzb_login')->where('id', '=', $bid)->find()->yz_access_token;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);
        $result['status'] = 0;
        $result['sort'] = 'jointime';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('wzb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) $qrcode_edit->lock = (int)$_POST['form']['lock'];
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('wzb_qrcode')->where('bid', '=', $bid);
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
        if ($_GET['start_time']) {
            $result['start_time'] = $_GET['start_time'];
            $result['end_time'] = $_GET['end_time'];
            if($result['end_time']==0){
                $qrcode = $qrcode->where('uvtime', '>', $result['start_time']);
            }else{
                $qrcode = $qrcode->where('uvtime', '>', $result['start_time'])->where('uvtime', '<', $_GET['end_time']);
            }

        }
        if ($_GET['fopenid']) {
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('wzb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
       if ($_GET['ffopenid']) {
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('wzb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();
            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM wzb_qrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
        ))->render('weixin/wzb/admin/pages');

        if ($result['sort']) $qrcode = $qrcode->order_by($result['sort'], 'DESC');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/wzb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['wzba']['admin'] < 1) Request::instance()->redirect('wzba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('wzb_login')->where('flag','=',0)->where('fadmin','=',$this->bid);
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

        $result['title'] = $this->template->title = '商户管理';
        $this->template->content = View::factory('weixin/wzb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('wzba/home');

        $bid = $this->bid;
        $biz=ORM::factory('wzb_login')->where('id','=',$bid)->find();
        if ($_POST['data']) {
            $login = ORM::factory('wzb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wzb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('wzba/logins');
            }
        }
        $admins=ORM::factory('wzb_login')->where('flag','=',1)->where('fadmin','=',$bid)->find_all();
        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/wzb/admin/logins_add')
            ->bind('biz',$biz)
            ->bind('admins',$admins)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('wzba/home');

        $bid = $this->bid;

        $login = ORM::factory('wzb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('wzb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('wzba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
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

                Request::instance()->redirect('wzba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/wzb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //管理员管理
    public function action_admins($action='', $id=0) {
        if ($_SESSION['wzba']['admin'] < 1) Request::instance()->redirect('wzba/home');
        if ($action == 'add') return $this->action_admins_add();
        if ($action == 'edit') return $this->action_admins_edit($id);
        $logins = ORM::factory('wzb_login')->where('flag','=',1)->where('fadmin','=',$this->bid);
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

        $result['title'] = $this->template->title = '管理员管理';
        $this->template->content = View::factory('weixin/wzb/admin/admins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_admins_add() {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->
        $bid = $this->bid;
        $biz=ORM::factory('wzb_login')->where('id','=',$bid)->find();
        if ($_POST['data']) {
            $login = ORM::factory('wzb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('wzb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('wzba/admins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加管理员';
        $this->template->content = View::factory('weixin/wzb/admin/admins_add')
            ->bind('biz',$biz)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_admins_edit($id) {
        if ($_SESSION['wzba']['admin'] < 2) Request::instance()->redirect('wzba/home');
        $bid = $this->bid;
        $login = ORM::factory('wzb_login', $id);
        if (!$login) die('404 Not Found!');
        $cfg = ORM::factory('wzb_cfg');
        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('wzba/admins');
        }
        if ($_POST['data']) {
            $login->values($_POST['data']);
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
                Request::instance()->redirect('wzba/admins');
            }
        }
        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改管理员';
        $this->template->content = View::factory('weixin/wzb/admin/admins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_login() {
        $this->template = 'weixin/wzb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('wzb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['wzba']['bid'] = $biz->id;
                    $_SESSION['wzba']['sid'] = $biz->shopid;
                    $_SESSION['wzba']['user'] = $_POST['username'];
                    $_SESSION['wzba']['admin'] = $biz->admin; //超管
                    $_SESSION['wzba']['config'] = ORM::factory('wzb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['wzba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/wzba/'.$_GET['from']);
            exit;
        }
    }
    public function action_admin() {
        $this->template = 'weixin/wzb/tpl/admin';
        $this->before();
        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);
        if ($_POST['username'] && $_POST['password']) {
            $biz1 = ORM::factory('wzb_admin')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();
            if ($biz->id) {
                //判断账号是否到期
                $_SESSION['wzba']['aid'] = $biz1->id;
                $_SESSION['wzba']['user'] = $_POST['username'];
                $_SESSION['wzba']['admin'] = $biz1->admin; //超管
                $biz->lastlogin = time();
                $biz->logins++;
                $biz->save();
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }
        if ($_SESSION['wzba']['aid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/wzba/'.$_GET['from']);
            exit;
        }
    }
    public function action_logout() {
        $_SESSION['wzba'] = null;
        header('location:/wzba/home');
        exit;
    }
    //产品图片
    public function action_dbimages($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "wzb_$type";

        $pic = ORM::factory($table, $id)->db_pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "wzb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

    public function action_history_trades($lid)
    {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }
        $live = ORM::factory('wzb_live')->where('id','=',$lid)->find();
        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }
        if($live->end_time){
            $trade = ORM::factory('wzb_trade')->where('bid', '=', $bid)->where('pay_time','>',date('Y-m-d H:i:s',$live->start_time))->where('pay_time','<',date('Y-m-d H:i:s',$live->end_time));
            if($_GET['now']==1){
                $trade = ORM::factory('wzb_trade')->where('bid', '=', $bid)->where('pay_time','>',date('Y-m-d H:i:s',$live->start_time));
            }
        }else{
            $trade = ORM::factory('wzb_trade')->where('bid', '=', $bid)->where('pay_time','>',date('Y-m-d H:i:s',$live->start_time));
        }

        $trade = $trade->reset(FALSE);

        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from wzb_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

            $trade =$trade->where('title', 'like', $s);

            if(count($openids)>0)
            $trade=$trade->or_where('openid', 'IN', $openids);

            $trade = $trade->and_where_close();
        }

        $result['countall'] = $countall = $trade->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/wzb/admin/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);
        $outmoney=ORM::factory('wzb_score')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from wzb_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

            if(count($qid)>0)
            $outmoney=$outmoney->where('qid', 'IN', $qid);
            else
            $outmoney=$outmoney->where('qid', "=",-100);
        }
        $result['countall'] = $countall = $outmoney->count_all();

        $result['sort'] = 'lastupdate';
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/wzb/admin/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }

    public function action_num()
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","KdtApiOauthClient");
            $tradeid=ORM::factory('wzb_trade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('wzb_order')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('wzb_cfg')->getCfg($tempbid);
                    $this->access_token = ORM::factory('wzb_login')->where('id','=',$tempbid)->find()->yz_access_token;
                    if (!$this->access_token) //die("$bid not found.\n");
                    continue;

                    $client = new KdtApiOauthClient();
                    $method = 'kdt.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($this->access_token,$method, $params);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('wzb_order')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
                        if(!$good->id)
                        {
                        $good->bid=$tempbid;
                        $good->tid=$k->tid;
                        $good->goodid=$result['response']['trade']['orders'][$j]['num_iid'];
                        $good->num=$result['response']['trade']['orders'][$j]['num'];
                        $good->price=$result['response']['trade']['orders'][$j]['payment'];
                        $good->title=$result['response']['trade']['orders'][$j]['title'];
                        $good->save();
                        }
                    }
              }

       }
       echo $i."////"."jj".$j;

    exit();
    }


    public function action_numtest($tid)
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","KdtApiOauthClient");
            echo $tid;
            $bid=ORM::factory('wzb_trade')->where('tid','=',$tid)->find()->bid;

            $this->access_token = ORM::factory('wzb_login')->where('id','=',$bid)->find()->yz_access_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('wzb_cfg')->getCfg($tempbid);

            if (!$this->access_token)  die("$bid not found.\n");


            $client = new KdtApiOauthClient();
            $method = 'kdt.trade.get';
            $params = array(
                'tid'=>$tid,
                //'fields' => 'tid,title,num_iid,orders,status,pay_time',
            );

             $result = $client->post($this->access_token,$method, $params);
             echo "<pre>";
             var_dump($result);



    exit();
    }

    public function action_stats_goods()
    {
        //$goods=ORM::factory('wzb_order')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `wzb_orders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT wzb_orders.* FROM `wzb_trades`,wzb_orders WHERE wzb_orders.tid=wzb_trades.tid and wzb_trades.status!='TRADE_CLOSED' and wzb_trades.status!='TRADE_CLOSED_BY_USER' and wzb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT wzb_orders.* FROM `wzb_trades`,wzb_orders WHERE wzb_orders.tid=wzb_trades.tid and wzb_trades.status!='TRADE_CLOSED' and wzb_trades.status!='TRADE_CLOSED_BY_USER' and wzb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT wzb_orders.* FROM `wzb_trades`,wzb_orders WHERE wzb_orders.tid=wzb_trades.tid and wzb_trades.status!='TRADE_CLOSED' and wzb_trades.status!='TRADE_CLOSED_BY_USER' and wzb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT wzb_orders.* FROM `wzb_trades`,wzb_orders WHERE wzb_orders.tid=wzb_trades.tid and wzb_trades.status!='TRADE_CLOSED' and wzb_trades.status!='TRADE_CLOSED_BY_USER' and wzb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        $this->template->content = View::factory('weixin/wzb/admin/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }
    public function action_other_setgood($action='',$id=0){
        if ($action == 'add') return $this->action_other_setgood_add();
        if ($action == 'edit') return $this->action_other_setgood_edit($id);
        $bid = $this->bid;
        $other_setgoods = ORM::factory('wzb_setgood')->where('bid','=',$bid)->where('type','=',2);
        $other_setgoods = $other_setgoods->reset(false);

        $result['countall'] = $countall = $other_setgoods->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');

        $result['items'] = $other_setgoods->order_by('priority', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '其他商品管理';
        $this->template->content = View::factory('weixin/wzb/admin/other_setgoods')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_other_setgood_add() {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $item = ORM::factory('wzb_setgood');
            $item->values($_POST['data']);
            $item->time=time();
            $item->type=2;
            $item->bid = $bid;

            if (!$_POST['data']['title'] || !$_POST['data']['price']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->db_pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "wzb:other_setgood:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('wzba/other_setgood');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加新商品';
        $this->template->content = View::factory('weixin/wzb/admin/other_setgoods_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_other_setgood_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid);

        $item = ORM::factory('wzb_setgood', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $item->delete();
            Request::instance()->redirect('wzba/other_setgood');
        }

        if ($_POST['data']) {
            $item->values($_POST['data']);
            $item->bid = $bid;
            $item->time=time();
            if (!$_POST['data']['title']) $result['error'] = '请填写完整后再提交';

            if ($_FILES['pic']['error'] == 0) {
                $tmpfile = $_FILES['pic']['tmp_name'];

                if ($_FILES['pic']['size'] > 1024*200) {
                    $result['error'] = '产品图片不符合规格，请检查！';
                } else {
                    $item->db_pic = file_get_contents($tmpfile);
                }
            }

            if (!$result['error']) {
                $item->save();

                $mem = Cache::instance('memcache');
                $key = "wzb:other_setgood:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('wzba/other_setgood');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改商品';
        $this->template->content = View::factory('weixin/wzb/admin/other_setgoods_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_setgoods1(){
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->access_token = ORM::factory('wzb_login')->where('id','=',$bid)->find()->yz_access_token;
        $tempconfig=ORM::factory('wzb_cfg')->getCfg($this->bid);
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $pg=1;
            $method = 'youzan.items.onsale.get';
            $params =[
                //'fields' =>'total_results',
            ];
            $total_result= $client->post($method, '3.0.0', $params, $files);
            // echo '<pre>';
            // var_dump($total_result);
            // echo '</pre>';
            $total =$total_result['response']['count'];
            $item_num=ORM::factory('wzb_setgood')->where('bid','=',$bid)->count_all();
            // echo $total."<br>";
            // echo $item_num."<br>";
            if($total!=$item_num||$_GET['refresh']==1){
                $a = ceil($total/100);
                for($k=0;$k<$a;$k++){
                    // echo $k."<br>";
                    $method = 'youzan.items.onsale.get';
                    $params = array(
                        'page_size'=>100,
                        'page_no'=>$k+1,
                        // 'q' => 'item_id','title',
                        );
                    $results = $client->post($method, '3.0.0', $params, $files);
                    // echo '<pre>';
                    // var_dump($results);
                    // echo '</pre>';
                    // echo "===============<br>";
                    for($i=0;$results['response']['items'][$i];$i++){
                        $res=$results['response']['items'][$i];
                        $method = 'youzan.item.get';
                        $params = array(
                             'item_id'=>$res['item_id'],
                        );
                        $result = $client->post($method, '3.0.0', $params, $files);
                        // echo '<pre>';
                        // var_dump($result);
                        // echo '</pre>';
                        // echo "===============<br>";
                        $item=$result['response']['item'];
                        $skus=$item['skus'];
                        $type=1;
                        $num_iid=$item['item_id'];
                        $name=str_replace("'", "", $item['title']);
                        $price=$item['price']/100;
                        $pic=$item['pic_url'];
                        $url=$item['detail_url'];
                        $num=$item['quantity'];
                        $num_num = ORM::factory('wzb_setgood')->where('goodid', '=', $num_iid)->count_all();
                        if($num_num==0 && $num_iid){
                            $sql = DB::query(Database::INSERT,"INSERT INTO `wzb_setgoods` (`bid`,`goodid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,1,$type,$num)");
                            $sql->execute();
                        }else{
                            $sql = DB::query(Database::UPDATE,"UPDATE `wzb_setgoods` SET `bid` = $bid ,`goodid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 1 , `type` =$type where `goodid` = $num_iid ");
                            $sql->execute();
                        }
                    }
                }
                $sql = DB::query(Database::DELETE,"DELETE FROM `wzb_setgoods` where `state` =0 and `bid` = $bid  and type =1");
                $sql->execute();
                $sql = DB::query(Database::UPDATE,"UPDATE `wzb_setgoods` SET `state` =0 where `bid` = $bid and type =1");
                $sql->execute();
            }
        }
        Request::instance()->redirect('wzba/setgoods');
    }
    public function action_setgoods(){
        $bid = $this->bid;
        $config = ORM::factory('wzb_cfg')->getCfg($bid, 1);
        //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
        require_once Kohana::find_file("vendor/youzan","YZTokenClient");
        $this->access_token = ORM::factory('wzb_login')->where('id','=',$bid)->find()->yz_access_token;
        $goods = ORM::factory('wzb_setgood')->where('bid','=',$bid)->where('type','=',1);
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('title', 'like', $s);
        }
        $tempconfig=ORM::factory('wzb_cfg')->getCfg($this->bid);
        if ($_POST['form']['num_iid']) {
            $goodid = $_POST['form']['num_iid'];
            $setgoods = ORM::factory('wzb_setgood')->where('bid', '=', $bid)->where('goodid','=',$goodid)->find();
                $setgoods->priority=$_POST['form']['priority'];
                $setgoods->status = $_POST['form']['status'];
                $setgoods->save();
        }
        $result['countall'] = $countall = $goods->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/wzb/admin/pages');
        $result['goods'] =$goods->order_by('status', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content=View::factory('weixin//wzb/admin/setgoods')
        ->bind('result',$result)
        ->bind('pages',$pages)
        ->bind('bid',$this->bid);

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
    private function sendsuccess($openid, $nickname,  $remark='恭喜您成功获得资格，赶紧点击菜单【生成海报】吧') {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $this->config['msg_success_tpl'];
        $tplmsg['url'] = $url;

        $tplmsg['data']['first']['value'] = '尊敬的用户，您提交的申请已经审核通过！';
        $tplmsg['data']['first']['color'] = '#FF0000';

        $tplmsg['data']['keyword1']['value'] = $nickname;
        $tplmsg['data']['keyword1']['color'] = '#FF0000';

        $tplmsg['data']['keyword2']['value'] = '已通过';
        $tplmsg['data']['keyword2']['color'] = '#06bf04';

        $tplmsg['data']['keyword3']['value'] = date('Y-m-d H:i:s');
        $tplmsg['data']['keyword3']['color'] = '#666666';

        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#666666';
        //Kohana::$log->add("weixin_wzb:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_wzb:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_wzb:tpl", print_r($result, true));
        return $result;
    }
}
