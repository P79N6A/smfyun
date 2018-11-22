<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_dlda extends Controller_Base {

    public $template = 'weixin/dld/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $we;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'test') return;
        $this->bid = $_SESSION['dlda']['bid'];
        $this->config = $_SESSION['dlda']['config'];
        $this->access_token=ORM::factory('dld_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/dlda/login');
            header('location:/dlda/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('dld_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('dld_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

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
    // public function action_test(){
    //     require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
    //     require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
    //     $name='aaa';
    //     $objPHPExcel = new PHPExcel();
    //     /*以下是一些设置 ，什么作者  标题啊之类的*/
    //     $objPHPExcel->getProperties()->setCreator("转弯的阳光")
    //         ->setLastModifiedBy("转弯的阳光")
    //         ->setTitle("数据EXCEL导出")
    //         ->setSubject("数据EXCEL导出")
    //         ->setDescription("备份数据")
    //         ->setKeywords("excel")
    //         ->setCategory("result file");
    //      /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
    //     $num=1;
    //     $objPHPExcel->setActiveSheetIndex(0)
    //                  //Excel的第A列，uid是你查出数组的键值，下面以此类推
    //                 ->setCellValue('A'.$num, 'access_token')
    //                 ->setCellValue('B'.$num, 'shopname')
    //                 ->setCellValue('C'.$num, 'shopimg');
    //     foreach($logins as $k => $v){
    //         $num=$k+2;
    //         $objPHPExcel->setActiveSheetIndex(0)
    //                      //Excel的第A列，uid是你查出数组的键值，下面以此类推
    //                     ->setCellValue('A'.$num, $v->access_token)
    //                     ->setCellValue('B'.$num, $v->shopname)
    //                     ->setCellValue('C'.$num, $v->shopimg);
    //     }
    //     $objPHPExcel->getActiveSheet()->setTitle('User');
    //     $objPHPExcel->setActiveSheetIndex(0);
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="'.$name.'.xls"');
    //     header('Cache-Control: max-age=0');
    //     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //     $objWriter->save('php://output');
    //     exit;

    // }
    // public function action_test(){
    //     $str='aaaaa"aaa';
    //     // echo str_replace('"',' ','', $str).'<br>';
    //     echo str_replace('"','', $str).'<br>';
    //     // echo str_replace(' ','', $str).'<br>';
    //     exit();
    // }
    public function action_test(){
        $sql = DB::query(Database::UPDATE,"UPDATE `dld_goodskus` SET `state` = 3  where `item_id` = 232");
        $sql->execute();
        exit();

    }


    //系统配置
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=83f328eed03bcd7d49&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/dlda/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"83f328eed03bcd7d49",
            "client_secret"=>"a4eb0f7c054c11e815c074e6f8328663",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/dlda/callback'
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
            $sid = $value['id'];
            $name = $value['name'];
            $usershop = ORM::factory('dld_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->shopname = $value['name'];
            $usershop->shopimg = $value['logo'];
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("dlda/home")."';</script>";
        }
        //Request::instance()->redirect('dlda/home');
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('dld_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."dld/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dld/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        //店铺地址设置
        if ($_POST['shopurl']) {
            $cfg = ORM::factory('dld_cfg');
            $ok = $cfg->setCfg($bid, 'shopurl', $_POST['shopurl']);
            $result['ok5'] += $ok;
            //重新读取配置
            $config = ORM::factory('dld_cfg')->getCfg($bid, 1);
        }
        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('dld_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok'] += $ok;
            }

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

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'dld_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'dld_file_key', '', file_get_contents($key_file));

            //重新读取配置
            $config = ORM::factory('dld_cfg')->getCfg($bid, 1);
        }

        //菜单配置
        if ($_POST['menu']) {
            $cfg = ORM::factory('dld_cfg');

            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('dld_cfg')->getCfg($bid, 1);
        }
        //公告设置
        if($_POST['text']){
            $cfg = ORM::factory('dld_cfg');

            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok3'] += $ok;
            }

            //重新读取配置
            $config = ORM::factory('dld_cfg')->getCfg($bid, 1);
        }

        $access_token = ORM::factory('dld_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/dld/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('config', $config);
    }
    public function action_skus($action='', $id=0) {
        if ($action == 'add') return $this->action_skus_add();
        if ($action == 'edit') return $this->action_skus_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);
        $result['skus'] = ORM::factory('dld_sku')->where('bid', '=', $bid)->order_by('lv', 'ASC')->find_all();
        // if($_POST){
        //     echo "<pre>";
        //     var_dump($_POST);
        //     echo "</pre>";
        //     exit();
        // }
        if ($_POST['text']) {
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_self',$_POST['text']['self']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_direct',$_POST['text']['direct']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_group',$_POST['text']['group']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_dirctcus',$_POST['text']['dirctcus']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_customer',$_POST['text']['customer']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_dirctorder',$_POST['text']['dirctorder']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_selforder',$_POST['text']['selforder']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'text_order',$_POST['text']['order']);
        }
        if($_POST['quest']){
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'buy_money',$_POST['quest']['buy']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'buy_url',$_POST['quest']['buy_url']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'buytip',$_POST['quest']['buytip']);
        }
        if($_POST['calcu']){
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'money_type',$_POST['calcu']['type']);
        }
        if($_POST['ivcode']){
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'code',$_POST['ivcode']);
        }
        if($_POST['share']){
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'timeline',$_POST['share']['timeline']);
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'appmessage',$_POST['share']['appmessage']);
        }
        if($_POST['date']){
            $ok=ORM::factory('dld_cfg')->setCfg($bid,'date',$_POST['date']);
        }
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        if ($_POST['menu']) {
            ORM::factory('dld_sku')->where('bid', '=', $bid)->delete_all();
            $menu=$_POST['menu'];
            $level=($menu['count']);
            for ($i=1; $i <=$level ; $i++) {
                $j=$i*2-1;
                $k=$i*2;
                $skus= ORM::factory('dld_sku');
                $skus->bid = $this->bid;
                $skus->lv = $i;
                $skus->scale = $menu['value_c'.$i.'_dld'];
                $skus->money1 = $menu['key_c'.$j.'_dld'];
                $skus->money2 = $menu['key_c'.$k.'_dld'];
                $skus->save();
            }
            Request::instance()->redirect('dlda/skus');


        }

        $this->template->title = '代理设置';
        $this->template->content = View::factory('weixin/dld/admin/skus')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_add() {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('dld_sku');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('dlda/skus');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/dld/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_skus_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);

        $sku = ORM::factory('dld_sku', $id);
        if (!$sku || $sku->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sku->delete();
            Request::instance()->redirect('dlda/skus');
        }

        if ($_POST['data']) {
            $sku->values($_POST['data']);
            $sku->bid = $bid;

            if (!$_POST['data']['name'] || !$_POST['data']['lv'] || !$_POST['data']['money']|| !$_POST['data']['scale']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();
                Request::instance()->redirect('dlda/skus');
            }
        }

        $_POST['data'] = $result['sku'] = $sku->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/dld/admin/skus_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_group($action='', $id=0) {
        if ($action == 'add') return $this->action_group_add();
        if ($action == 'edit') return $this->action_group_edit($id);

        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);

        $result['group'] = ORM::factory('dld_suite')->where('bid', '=', $bid)->order_by('id', 'DESC')->find_all();

        $this->template->title = '分销商分组';
        $this->template->content = View::factory('weixin/dld/admin/group')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_group_add() {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);

        if ($_POST['data']) {

            $sku = ORM::factory('dld_suite');
            $sku->values($_POST['data']);

            $sku->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $sku->save();

                Request::instance()->redirect('dlda/group');
            }
        }

        $result['action'] = 'add';
        $result['title'] = $this->template->title = '添加';
        $this->template->content = View::factory('weixin/dld/admin/group_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_group_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);

        $group = ORM::factory('dld_suite', $id);
        if (!$group || $group->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            $sum = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('group_id','=',$id)->count_all();
            if($sum>0){
                die('该分组下有分销商不允许删除！');
            }
            $group->delete();
            Request::instance()->redirect('dlda/group');
        }

        if ($_POST['data']) {
            $group->values($_POST['data']);
            $group->bid = $bid;

            if (!$_POST['data']['name']) $result['error'] = '请填写完整后再提交';

            if (!$result['error']) {
                $group->save();
                Request::instance()->redirect('dlda/group');
            }
        }

        $_POST['data'] = $result['group'] = $group->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改';
        $this->template->content = View::factory('weixin/dld/admin/group_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户详细
    public function action_qrcodes_detail($id){
        $bid = $this->bid;
        $this->template->title = '用户详细';
        if($_GET['data']['begin']&&$_GET['data']['over']){
            $result['time'] = $_GET['data']['begin'].'——'.$_GET['data']['over'];
        }else{
            if($_GET['data']['time']=='today'||!$_GET['data']['time']){
                $result['time'] = date('Y-m-d 00:00:00',time()).'——'.'到现在';
            }
        }
        $result['begin'] = $_GET['data']['begin'];
        $result['over'] = $_GET['data']['over'];
        $this->template->content = View::factory('weixin/dld/admin/qrcodes_detail')
            ->bind('result', $result);
    }
    //审核管理
    public function action_qrcodes_m($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        $alls = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('lv','=',1)->find_all();
        //修改用户
        if ($_POST['form']['id']) {

            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];
                    $qrcode_edit->tel = $_POST['form']['tel'];
                    if($qrcode_edit->lv==3){//删除该代理商
                        $fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $qrcode_edit->fopenid)->find();
                        //找出直属下线
                        $childs = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('fopenid', '=', $qrcode_edit->openid)->find_all();
                        //修改他们的上级为上级的上级
                        foreach ($childs as $k => $v) {
                            $v->fopenid = $fuser->openid;
                            $v->save();
                        }
                        $qrcode_edit->openid = $qrcode_edit->openid.'_del_'.time();
                        $qrcode_edit->save();
                        //是否需要删除下线的top？不需要删除！
                    }else if($_POST['form']['fuser']=='zerofopenid'){
                        if($qrcode_edit->top==''){
                            $qrcode_edit->fopenid = '';
                            $qrcode_edit->save();
                        }else{
                            echo '此用户确实是有上级的，不是通过分享商品链接指定的上级！';
                            exit;
                        }
                    }else{
                        $qrcode_edit->group_id = (int)$_POST['form']['suite'];
                        $new_fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '=', $_POST['form']['fuser'])->find();
                        if(!$new_fuser->id){//不修改关系
                            $qrcode_edit->save();
                        }else{
                            $loop_qrcode = $qrcode_edit;
                            if(!strlen($qrcode_edit->bottom)>0){
                                $str = $qrcode_edit->id;
                            }else{
                                $str = $qrcode_edit->id.','.$qrcode_edit->bottom;;
                            }
                            $next_bottom_arr =  explode(',', $str);//4 6
                            while($loop_qrcode->fopenid){
                                // 原来的所有上级的bottom剔除 当前用户的id和之后的；
                                $loop_qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $loop_qrcode->fopenid)->find();
                                $fuser_str = $loop_qrcode->bottom;//4 5 6 7
                                $fuser_arr = explode(',', $fuser_str);
                                foreach ($next_bottom_arr as $k => $v) {
                                    foreach ($fuser_arr as $key => $value) {
                                        if($value==$v){
                                            array_splice($fuser_arr,$key,1);
                                        }
                                    }
                                }
                                $newstr = '';
                                foreach ($fuser_arr as $key => $value) {
                                    if($key == 0){
                                        $newstr = $value;
                                    }else{
                                        $newstr = $newstr.','.$value;
                                    }
                                }
                                $loop_qrcode->bottom = $newstr;
                                $loop_qrcode->save();
                            }
                            $qrcode_edit->fopenid = $new_fuser->openid;
                            $qrcode_edit->save();
                            $loop_qrcode = $qrcode_edit;
                            $qrcode_edit->top = '';
                            $new_qr_bottom = strlen($qrcode_edit->bottom)>0?','.$qrcode_edit->bottom:'';
                            while($loop_qrcode->fopenid){
                                // 新的上级都要加上bottom
                                $loop_qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $loop_qrcode->fopenid)->find();
                                if(!strlen($loop_qrcode->bottom) > 0) {//为空就直接加上
                                    $loop_qrcode->bottom = $qrcode_edit->id.$new_qr_bottom;
                                }else{
                                    $loop_qrcode->bottom = $loop_qrcode->bottom.','.$qrcode_edit->id.$new_qr_bottom;
                                }
                                $loop_qrcode->save();
                                if(!strlen($qrcode_edit->top) > 0){
                                    $qrcode_edit->top = $loop_qrcode->id;
                                }else{
                                    $qrcode_edit->top = $loop_qrcode->id.','.$qrcode_edit->top;
                                }
                                $qrcode_edit->save();
                            }

                            // for循环当前用户的bottom 循环加 上级用户的top和qid
                            $bottom_arr = explode(',', $qrcode_edit->bottom);
                            foreach ($bottom_arr as $k => $v) {
                                if($v){
                                    $now_user = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '=', $v)->find();
                                    $now_fuser = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $now_user->fopenid)->find();
                                    $now_user->top = $now_fuser->top.','.$now_fuser->id;
                                    $now_user->save();
                                }
                            }

                            // 组问题最后考虑了
                            // 未修改之前  当前修改用户存在的组
                            $now_user_g = ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$qrcode_edit->id)->order_by('id','DESC')->find();
                            $newstr = $now_user_g->bottom;
                            if(!strlen($newstr)>0){
                                $newstr = $now_user_g->id;
                            }else{
                                $newstr = $now_user_g->id.','.$now_user_g->bottom;
                            }
                            //循环当前用户存在组的bottom要加上自己  疯狂插入
                            $bottom_arr = explode(',', $newstr);
                            $new_add_arr = array();
                            foreach ($bottom_arr as $k => $v) {//先顺着轮只记录fqid
                                $old_group = ORM::factory('dld_group')->where('bid','=',$bid)->where('id','=',$v)->order_by('id','DESC')->find();
                                $new_add_g = ORM::factory('dld_group');
                                $new_add_g->bid=$bid;
                                $new_add_g->qid=$old_group->qid;
                                if($old_group->qid == $qrcode_edit->id){//如果是自己 就新增上级
                                    // $new_add_g->fqid=ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$new_fuser->id)->order_by('id','DESC')->find()->id;
                                    $new_add_g->fqid = $new_fuser->id;
                                }else{
                                    $new_add_g->fqid = $old_group->fqid;
                                }

                                $new_add_g->save();
                                $new_add_arr[$v] = $new_add_g->id;//4-8  6-9
                            }
                            // var_dump($new_add_arr);
                            // echo '<br>';
                            //找出最新的  新上级group
                            // echo 'newnew_fuser:::::::::'.$new_fuser->id;
                            $new_fuser_g = ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$new_fuser->id)->order_by('id','DESC')->find();
                            foreach ($new_add_arr as $k => $v) {//再轮一遍
                                // echo $k.'::'.$v.'<br>';
                                $new_add_g = ORM::factory('dld_group')->where('bid','=',$bid)->where('id','=',$v)->order_by('id','DESC')->find();
                                $old_g = ORM::factory('dld_group')->where('bid','=',$bid)->where('id','=',$k)->find();
                                $new_add_g->fgid = $new_add_arr[$old_g->fgid]?$new_add_arr[$old_g->fgid]:$new_fuser_g->id;
                                foreach ($new_add_arr as $key => $value) {//替换bottom
                                    // echo $old_g->bottom.'<br>';
                                    $old_g->bottom = str_replace($key, $new_add_arr[$key], $old_g->bottom);
                                }
                                $new_add_g->bottom = $old_g->bottom;
                                $new_add_g->save();
                            }
                            //找出当前用户最新的组
                            $now_new_user = ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$qrcode_edit->id)->order_by('id','DESC')->find();
                            // 给新的上级每个加bottom
                            $loop_group = $new_fuser_g;
                            $new_now_user_bottom = strlen($now_new_user->bottom)>0?','.$now_new_user->bottom:'';
                            while ($loop_group->id) {
                                if(!strlen($loop_group->bottom) > 0) {//为空就直接加上
                                    $loop_group->bottom = $now_new_user->id.$new_now_user_bottom;
                                }else{
                                    $loop_group->bottom = $loop_group->bottom.','.$now_new_user->id.$new_now_user_bottom;
                                }
                                $loop_group->save();
                                $loop_group = ORM::factory('dld_group')->where('bid','=',$bid)->where('id','=',$loop_group->fgid)->order_by('id','DESC')->find();
                            }
                            // exit;
                        }
                    }


                }
            }
        }

        $qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('lv','=',1);
        $qrcode = $qrcode->reset(FALSE);
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='代理列表';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '昵称')
                        ->setCellValue('B'.$num, '所辖团队成员')
                        ->setCellValue('C'.$num, '客户数')
                        ->setCellValue('D'.$num, '当日个人销量')
                        ->setCellValue('E'.$num, '当月个人销量')
                        ->setCellValue('F'.$num, '累计销量')
                        ->setCellValue('G'.$num, '当日团队销量')
                        ->setCellValue('H'.$num, '当月团队销量')
                        ->setCellValue('I'.$num, '累计团队销量')
                        ->setCellValue('J'.$num, '当月团队奖励')
                        ->setCellValue('K'.$num, '当月个人团队奖励')
                        ->setCellValue('L'.$num, '当日销售利润')
                        ->setCellValue('M'.$num, '当月销售利润')
                        ->setCellValue('N'.$num, '累计销售利润')
                        ->setCellValue('O'.$num, '上级代理');
            $qrcodes=$qrcode->find_all();
            foreach($qrcodes as $k => $v){
                $group1=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->order_by('lastupdate','DESC')->find();
                if($group1->bottom){
                $bottom='('.$group1->bottom.')';
                    //echo $bottom.'<br>';
                $group_ay=DB::query(Database::SELECT,"SELECT count(id) as group_num from dld_groups where bid=$v->bid and id in $bottom ")->execute()->as_array();
                    $group_num=$group_ay[0]['group_num'];
                }else{
                    $group_num=0;
                }
                  //echo $group_num.'<br>';所辖团队成员
                $qr_num=ORM::factory('dld_qrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','!=',1)->where('fopenid','!=','')->count_all();
                $groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                $month=date('Y-m',time());
                       //echo $month.'<br>';
                $daytype='%Y-%m-%d';
                $monthtype='%Y-%m';
                $day=date('Y-m-d',time());
                $month_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as month_pnum from dld_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                $month_pnum=$month_pnum[0]['month_pnum'];
                    //echo $month_pnum.'<br>';当月个人销量
                $day_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as day_pnum from dld_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                $day_pnum=$day_pnum[0]['day_pnum'];
                 //echo $day_pnum.'<br>';当天个人销量
                $all_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as all_pnum from dld_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' ")->execute()->as_array();
                $all_pnum=$all_pnum[0]['all_pnum'];
                $day_tnum=0;
                $month_tnum=0;
                $all_tnum=0;
                $month_tmoney=0;
                $month_pmoney=0;
                foreach ($groups as $group) {
                    if($group->bottom){
                        $bottom1='('.$group->id.','.$group->bottom.')';
                    }else{
                        $bottom1='('.$group->id.')';
                    }
                      //echo $bottom1.'<br>';
                    $day_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as day_tnum from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                    $day_tnum+=$day_tnum1[0]['day_tnum'];
                      //echo  $day_tnum.'<br>';当天团队销量
                    $month_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tnum from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_tnum+=$month_tnum1[0]['month_tnum'];
                      //echo  $month_tnum.'<br>';当月团队销量
                    $all_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as all_tnum from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 ")->execute()->as_array();
                    $all_tnum+=$all_tnum1[0]['all_tnum'];
                      //累计团队销量
                    $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_tmoney1=$month_tmoney1[0]['month_tmoney'];
                      //echo  $month_tmoney.'<br>';
                    $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                    if(!$sku->id){
                        $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                        if(!$fsku->id){
                            $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scale=0;
                        }
                    }else{
                        $scale=$sku->scale;
                    }
                    $month_tmoney+=$month_tmoney1*$scale/100;
                        // echo  $month_tmoney.'<br>';
                        // echo $group->id."<br>";
                    $child_groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                    $child_moneys=0;
                    foreach ($child_groups as $child_group) {
                        if($child_group->bottom){
                            $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                        }else{
                            $bottom2='('.$child_group->id.')';
                        }
                            //echo $bottom2."<br>";
                         $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                            //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                        $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>=',$month_ltmoney)->find();
                        if(!$sku->id){
                            $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>',$month_ltmoney)->find();
                            if(!$fsku->id){
                                $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                            }else{
                                $scale=0;
                            }
                        }else{
                            $scale=$sku->scale;
                        }
                        $child_money= $month_ltmoney*$scale/100;
                        $child_moneys+=$child_money;
                    }
                      //echo  $child_moneys.'<br>';
                    $month_pmoney+=$month_tmoney-$child_moneys;
                }
                //echo  $month_pmoney.'<br>';当月个人团队奖励
                $day_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as day_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$day' ")->execute()->as_array();
                $day_pxmoney=$day_pxmoney[0]['day_pxmoney'];
                //当天销售利润
                $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                //当月销售利润
                //echo  $month_pxmoney.'<br>';
                $all_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as all_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
                $all_pxmoney=$all_pxmoney[0]['all_pxmoney'];
                //累计销售利润
                $fname=ORM::factory('dld_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->nickname)
                            ->setCellValue('B'.$num, $group_num)
                            ->setCellValue('C'.$num, $qr_num)
                            ->setCellValue('D'.$num, $day_pnum?$day_pnum:0)
                            ->setCellValue('E'.$num, $month_pnum?$month_pnum:0)
                            ->setCellValue('F'.$num, $all_pnum?$all_pnum:0)
                            ->setCellValue('G'.$num, $day_tnum)
                            ->setCellValue('H'.$num, $month_tnum)
                            ->setCellValue('I'.$num, $all_tnum)
                            ->setCellValue('J'.$num, $month_tmoney)
                            ->setCellValue('K'.$num, $month_pmoney)
                            ->setCellValue('L'.$num, $day_pxmoney?$day_pxmoney:0)
                            ->setCellValue('M'.$num, $month_pxmoney?$month_pxmoney:0)
                            ->setCellValue('N'.$num, $all_pxmoney?$all_pxmoney:0)
                            ->setCellValue('O'.$num, $fname);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if($_GET['qid']){
            $openid=ORM::factory('dld_qrcode')->where('id','=',$_GET['qid'])->find()->openid;
            $qrcode=$qrcode ->where('fopenid','=',$openid);
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('tel', 'like', $s)->where_close(); //;
        }

        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['gid']) {
            $result['gid'] = (int)$_GET['gid'];
            $qrcode = $qrcode->where('group_id', '=', $result['gid']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }
        if ($_GET['type']) {
            $result['type'] = $_GET['type'];
            if($result['type']!='all'){
                $qrcode = $qrcode->where('lv', '=', $result['type']);
            }
        }
        if ($_GET['group']) {
            $result['group'] = $_GET['group'];
            if($result['group']!='all'){
                $qrcode = $qrcode->where('group_id', '=', $result['group']);
            }
        }
        $result['countall'] = $countall = $qrcode->count_all();
        if ($_GET['sort']=='fans_num'){
            $qrcodes = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',1)->or_where('lv','=',3)->where_close()->find_all();
            foreach ($qrcodes as $k => $v) {
                $num = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('fopenid','=',$v->openid)->where('subscribe','=',1)->count_all();
                if($v->fans_num!=$num){
                    $v->fans_num = $num;
                    $v->save();
                }
            }
        }
        //分组
        $suite= ORM::factory('dld_suite')->where('bid','=',$bid)->find_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $group = ORM::factory('dld_group')->where('bid','=',$bid)->find_all();
        $gnum = ORM::factory('dld_group')->where('bid','=',$bid)->count_all();
        $this->template->title = '代理列表';
        $this->template->content = View::factory('weixin/dld/admin/qrcodes_m')
            ->bind('pages', $pages)
            ->bind('group', $group)
            ->bind('gnum', $gnum)
            ->bind('result', $result)
            ->bind('alls', $alls)
            ->bind('suite',$suite)
            ->bind('config', $config);
    }
    //分销审核
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lv'])) {
                    $qrcode_edit->lv = (int)$_POST['form']['lv'];
                    $qrcode_edit->name = $_POST['form']['name'];
                    $qrcode_edit->tel = $_POST['form']['tel'];
                    $qrcode_edit->bz = $_POST['form']['bz'];
                    $qrcode_edit->group_id = $_POST['form']['groupid'];
                    $qrcode_edit->save();
                    if((int)$_POST['form']['lv']==1){
                        //给予编号
                        $front_user = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '<', $qrcode_edit->id)->order_by('id','desc')->find();
                        $qrcode_edit->fid = $front_user->fid + 1;
                        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                        $this->we = new Wechat($config);
                        if($config['msg_success_tpl']){
                            $this->sendsuccess($qrcode_edit->openid,$qrcode_edit->nickname);
                        }else{
                            $msg['touser'] = $qrcode_edit->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = "恭喜您的申请已经通过，成功获得资格，赶紧点击菜单【生成海报】吧";
                            $this->we->sendCustomMessage($msg);
                        }
                    }
                }
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where_open()->where('lv','=',2)->or_where('lv','=',4)->where_close();
        $qrcode = $qrcode->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('name', 'like', $s)->or_where('bz', 'like', $s)->or_where('shop', 'like', $s)->or_where('tel', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }
        if ($_GET['type']) {
            $result['type'] = $_GET['type'];
            if($result['type']!='all'){
                $qrcode = $qrcode->where('lv', '=', $result['type']);
            }
        }
        if ($_GET['id']) {
            $result['id'] = (int)$_GET['id'];
            $qrcode = $qrcode->where('id', '=', $result['id']);
        }

        if ($_GET['ticket']) {
            $result['ticket'] = $_GET['ticket'];
            $qrcode = $qrcode->where('ticket', '<>', "");
        }

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $group = ORM::factory('dld_group')->where('bid','=',$bid)->find_all();
        $gnum = ORM::factory('dld_group')->where('bid','=',$bid)->count_all();
        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/dld/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('group', $group)
            ->bind('gnum', $gnum)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['dlda']['admin'] < 1) Request::instance()->redirect('dlda/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('dld_login');
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
        ))->render('weixin/dld/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/dld/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['dlda']['admin'] < 2) Request::instance()->redirect('dlda/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('dld_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('dld_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('dlda/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/dld/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['dlda']['admin'] < 2) Request::instance()->redirect('dlda/home');

        $bid = $this->bid;

        $login = ORM::factory('dld_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('dld_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('dlda/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('dld_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('dlda/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/dld/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/dld/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('dld_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['dlda']['bid'] = $biz->id;
                    $_SESSION['dlda']['user'] = $_POST['username'];
                    $_SESSION['dlda']['admin'] = $biz->admin; //超管
                    $_SESSION['dlda']['config'] = ORM::factory('dld_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['dlda']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/dlda/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['dlda'] = null;
        header('location:/dlda/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "dld_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_export_data_users(){
        $bid = $this->bid;
        $daytype='%Y-%m-%d';
        $length=10;
        if($_POST['data']['begin']!=NULL&&$_POST['data']['over']!=NULL){
            $begin=$_POST['data']['begin'];
            $over=$_POST['data']['over'];
           if(strtotime($begin)>strtotime($over)){
             $begin=$_POST['data']['over'];
             $over=$_POST['data']['begin'];
           }
           if(strtotime($begin)==strtotime($over))
           {
             $temp='所有用户'.$begin;
           }
           else
           {
            $temp='所有用户'.$begin.'~'.$over;
           }
            $users = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('lv','=',1)->find_all();

            $filename = 'ORDERS.'.$temp.'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('用户id', '用户昵称', '用户姓名','所属分组', '新增粉丝数量', '有赞订单数量', '有赞商品交易数量', '有赞成交金额');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($users as $k => $v) {
                //新增粉丝数量
                $newadd[$k]['fansnum'] = 0;
                $newadd[$k]['tradesnum'] = 0;
                $newadd[$k]['goodsnum'] = 0;
                $newadd[$k]['payment'] = 0;
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from dld_qrcodes where bid=$this->bid and fopenid='$v->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[$k]['fansnum']=$fans[0]['fansnum'];
                //有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from dld_trades where bid=$this->bid and fopenid='$v->openid' and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[$k]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$k]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$k]['payment']=$tradesdata[0]['payment'];

                $array = array($v->id, $v->nickname, $v->name, $v->groups->name,$newadd[$k]['fansnum'], $newadd[$k]['tradesnum'], $newadd[$k]['goodsnum'],$newadd[$k]['payment']);

                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }

                fputcsv($fp, $array);
            }
        }
        exit;
    }
    public function action_export_data_groups(){
        $bid = $this->bid;
        $daytype='%Y-%m-%d';
        $length=10;
        if($_POST['data']['begin']!=NULL&&$_POST['data']['over']!=NULL){
            $begin=$_POST['data']['begin'];
            $over=$_POST['data']['over'];
           if(strtotime($begin)>strtotime($over)){
             $begin=$_POST['data']['over'];
             $over=$_POST['data']['begin'];
           }
           if(strtotime($begin)==strtotime($over))
           {
             $temp='所有分组'.$begin;
           }
           else
           {
            $temp='所有分组'.$begin.'~'.$over;
           }
            $groups = ORM::factory('dld_group')->where('bid','=',$bid)->find_all();

            $filename = 'ORDERS.'.$temp.'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');

            $title = array('分组id', '分组名称', '组成员数量', '新增粉丝数量', '有赞订单数量', '有赞商品交易数量', '有赞成交金额');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);

            foreach ($groups as $k => $v) {
                $group_users = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('group_id','=',$v->id)->find_all();
                $group_num = ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('group_id','=',$v->id)->count_all();
                $addcount[$k] = 0;
                $addtradesnum[$k] = 0;
                $addgoodnum[$k] = 0;
                $addpayment[$k] = 0;
                foreach ($group_users as $g => $u) {
                    //新增用户
                    $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from dld_qrcodes where bid=$this->bid and fopenid='$u->openid' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                    $addcount[$k] = $addcount[$k] + $fans[0]['fansnum'];
                    //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                    $tradesdata =DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from dld_trades where bid=$this->bid and fopenid='$u->openid' and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                    $addtradesnum[$k] = $addtradesnum[$k] + $tradesdata[0]['tradesnum'];
                    $addgoodnum[$k] = $addgoodnum[$k] + $tradesdata[0]['goodnum'];
                    $addpayment[$k] = $addpayment[$k] + $tradesdata[0]['payment'];
                }
                $array = array($v->id, $v->name, $group_num, $addcount[$k],$addtradesnum[$k], $addgoodnum[$k],$addpayment[$k]);

                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }
                fputcsv($fp, $array);
            }
        }
        exit;
    }
    public function diff_date($date1, $date2){
        if($date1>$date2){
            $startTime = strtotime($date1);
            $endTime = strtotime($date2);
        }else{
            $startTime = strtotime($date2);
            $endTime = strtotime($date1);
        }
            $diff = $startTime-$endTime;
            $day = $diff/86400;
            return intval($day);
    }
    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        if($_GET['qid']==3||$action=='shaixuan'){
            $status=3;
            $newadd=array();
            if($_GET['data']['begin']!=null&&$_GET['data']['over']!=null){
                $begin=$_GET['data']['begin'];
                $over=$_GET['data']['over'];
               if(strtotime($begin)>strtotime($over)){
                 $begin=$_GET['data']['over'];
                 $over=$_GET['data']['begin'];
               }
               // echo $begin.$over;
               if(strtotime($begin)==strtotime($over)){
                 $newadd[0]['time']=$begin;
               }
               else{
                $newadd[0]['time']=$begin.'~'.$over;
               }
                //新增代理数
                $fans=DB::query(Database::SELECT,"SELECT count(id) as dlnum from dld_qrcodes where bid=$this->bid and lv=1 and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['dlnum']=$fans[0]['dlnum'];
                //新增客户数
                $ticket=DB::query(Database::SELECT,"SELECT count(id) as khnum from dld_qrcodes where bid=$this->bid and lv !=1 and fopenid !='' and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['khnum']=$ticket[0]['khnum'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from dld_trades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[0]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from dld_scores where bid=$this->bid and score > 0 and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();

                $newadd[0]['commision']=$commision[0]['paymoney'];
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
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `dld_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from dld_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/dld/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `dld_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from dld_trades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"SELECT count(id) as dlnum from dld_qrcodes where bid=$this->bid and lv =1 and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['dlnum']=$fans[0]['dlnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"SELECT count(id) as khnum from dld_qrcodes where bid=$this->bid and lv !=1 and fopenid !='' and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['khnum']=$ticket[0]['khnum'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from dld_trades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$i]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from dld_scores where bid=$this->bid and score>0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
               // var_dump($commision);
                $newadd[$i]['commision']=$commision[0]['paymoney'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `dld_qrcodes` where bid=$this->bid UNION select left(pay_time,10) from dld_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/dld/admin/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }


    public function action_history_trades()
    {

        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $trade = ORM::factory('dld_trade')->where('bid', '=', $bid);
        $trade = $trade->reset(FALSE);
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='订单记录';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '订单名称')
                        ->setCellValue('B'.$num, '付款时间')
                        ->setCellValue('C'.$num, '金额')
                        ->setCellValue('D'.$num, '客户昵称')
                        ->setCellValue('E'.$num, '所属代理')
                        ->setCellValue('F'.$num, '需扣除的销售利润')
                        ->setCellValue('G'.$num, '订单状态');
            $orders=$trade->order_by('int_time','DESC')->limit(400)->find_all();
            foreach($orders as $k => $v){
                $fuser=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$v->fopenid)->find();
                switch ($v->status) {
                    case 'WAIT_SELLER_SEND_GOODS':
                        $status='已付款';
                        break;
                    case 'WAIT_BUYER_CONFIRM_GOODS':
                        $status='已发货';
                        break;
                    case 'TRADE_BUYER_SIGNED':
                        $status='已签收';
                        break;
                    case 'TRADE_CLOSED':
                        $status='已退款';
                        break;
                    default:
                        $status='不详';
                        break;
                }
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->title)
                            ->setCellValue('B'.$num, $v->pay_time)
                            ->setCellValue('C'.$num, $v->payment)
                            ->setCellValue('D'.$num, $v->qrcode->nickname)
                            ->setCellValue('E'.$num, $fuser->nickname)
                            ->setCellValue('F'.$num, $v->money1)
                            ->setCellValue('G'.$num, $status);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if($_GET['qid']||$_GET['flag']){
            $openid=ORM::factory('dld_qrcode')->where('id','=',$_GET['qid'])->find()->openid;
            $group=ORM::factory('dld_group')->where('bid','=',$bid)->where('qid','=',$_GET['qid'])->find();
            $child_group=array();
            foreach ($groups as $group) {
                $bottom=$group->bottom;
                if($bottom){
                    $child_group1=explode(",",$bottom);
                    Array_push($child_group1,$group->id);
                }else{
                    $child_group1=array();
                    Array_push($child_group1,$group->id);
                }
                $child_group=array_merge($child_group,$child_group1);
            }
            $month=date('Y-m',time()).'%';
            // $month=(string)$month
            $day=date('Y-m-d',time()).'%';
            if($_GET['flag']=='dayp'){
                // echo $openid.'<br>';
                // echo $day."<br>";
                // exit();
                $trade=$trade->where('fopenid','=',$openid)->where('pay_time','like',$day);
            }elseif($_GET['flag']=='monthp'){
                $trade=$trade->where('fopenid','=',$openid)->where('pay_time','like',$month);
            }elseif($_GET['flag']=='allp'){
                $trade=$trade->where('fopenid','=',$openid);
            }elseif($_GET['flag']=='dayt'){
                $trade=$trade->where('gid','IN',$child_group)->where('pay_time','like',$day);
            }elseif($_GET['flag']=='montht'){
                $trade=$trade->where('gid','IN',$child_group)->where('pay_time','like',$month);
            }elseif($_GET['flag']=='allt'){
                $trade=$trade->where('gid','IN',$child_group);
            }elseif($_GET['flag']=='cnum'){
                $trade=$trade->where('openid','=',$openid);
            }
        }
        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from dld_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/dld/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/dld/admin/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);
        $outmoney=ORM::factory('dld_score')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from dld_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/dld/admin/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/dld/admin/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }

    public function action_num()
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            $tradeid=ORM::factory('dld_trade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('dld_order')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('dld_cfg')->getCfg($tempbid);
                    $this->access_token = ORM::factory('dld_login')->where('id','=',$tempbid)->find()->access_token;
                    if (!$this->access_token) //die("$bid not found.\n");
                    continue;

                    $client = new YZTokenClient($this->access_token);
                    $method = 'youzan.trade.get';
                    $params = array(
                        'tid'=>$k->tid,
                        'fields' => 'tid,title,num_iid,orders,status,pay_time',
                    );

                     $result = $client->post($method, $this->methodVersion, $params, $files);
                    for($j=0;$result['response']['trade']['orders'][$j];$j++)
                    {
                        $good=ORM::factory('dld_order')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
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
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            echo $tid;
            $bid=ORM::factory('dld_trade')->where('tid','=',$tid)->find()->bid;

            $this->access_token = ORM::factory('dld_login')->where('id','=',$bid)->find()->access_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('dld_cfg')->getCfg($tempbid);

            if (!$this->access_token)  die("$bid not found.\n");


            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.trade.get';
            $params = array(
                'tid'=>$tid,
                //'fields' => 'tid,title,num_iid,orders,status,pay_time',
            );

             $result = $client->post($method, $this->methodVersion, $params, $files);
             echo "<pre>";
             var_dump($result);



    exit();
    }

    public function action_stats_goods()
    {
        //$goods=ORM::factory('dld_order')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `dld_orders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT dld_orders.* FROM `dld_trades`,dld_orders WHERE dld_orders.tid=dld_trades.tid and dld_trades.status!='TRADE_CLOSED' and dld_trades.status!='TRADE_CLOSED_BY_USER' and dld_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT dld_orders.* FROM `dld_trades`,dld_orders WHERE dld_orders.tid=dld_trades.tid and dld_trades.status!='TRADE_CLOSED' and dld_trades.status!='TRADE_CLOSED_BY_USER' and dld_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT dld_orders.* FROM `dld_trades`,dld_orders WHERE dld_orders.tid=dld_trades.tid and dld_trades.status!='TRADE_CLOSED' and dld_trades.status!='TRADE_CLOSED_BY_USER' and dld_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT dld_orders.* FROM `dld_trades`,dld_orders WHERE dld_orders.tid=dld_trades.tid and dld_trades.status!='TRADE_CLOSED' and dld_trades.status!='TRADE_CLOSED_BY_USER' and dld_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        $this->template->content = View::factory('weixin/dld/admin/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }
    public function action_setgood1s(){
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid, 1);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $this->access_token = ORM::factory('dld_login')->where('id','=',$bid)->find()->access_token;
        $tempconfig=ORM::factory('dld_cfg')->getCfg($this->bid);
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
            if(isset($total_result['response']['count'])){
                $item_num=ORM::factory('dld_setgood')->where('bid','=',$bid)->count_all();
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
                            $type=0;
                            if($skus){
                                // echo "aaa<br>";
                                $type=1;
                                foreach ($skus as $sku) {
                                    $properties_name_json=$sku['properties_name_json'];
                                    $msgs=json_decode( $properties_name_json,true);
                                    $skutitle='';
                                    foreach ($msgs as $msg) {
                                        if($skutitle){
                                            $skutitle=$skutitle.'/'.$msg['k'].':'.$msg['v'];
                                        }else{
                                            $skutitle=$msg['k'].':'.$msg['v'];
                                        }

                                    }
                                    $price=$sku['price']/100;
                                    $title=$skutitle;
                                    $sku_id=$sku['sku_id'];
                                    $item_id=$sku['item_id'];
                                    $num=$sku['quantity'];
                                    // echo $sku_id."<br>";
                                    $sku_num = ORM::factory('dld_goodsku')->where('sku_id', '=', $sku_id)->count_all();
                                    // echo $sku_num.'<br>';
                                    if($sku_num==0 && $sku_id){
                                        // echo "上面<br>";
                                        $sql = DB::query(Database::INSERT,"INSERT INTO `dld_goodskus` (`bid`,`item_id`,`title`,`sku_id`, `price`,`status`,`state`,`num`) VALUES ($bid,$item_id,'$title' ,$sku_id,$price,0,1,$num)");
                                        $sql->execute();
                                    }else{
                                        // echo "下面<br>";
                                        $sql = DB::query(Database::UPDATE,"UPDATE `dld_goodskus` SET `bid` = $bid ,`item_id` = $item_id,`title` ='$title',`sku_id`=$sku_id, `price`=$price,`state` = 1 , `num`= $num where `sku_id` = $sku_id ");
                                        $sql->execute();
                                    }
                                }
                            }
                            $num_iid=$item['item_id'];
                            $name=$item['title'];
                            $price=$item['price']/100;
                            $pic=$item['pic_url'];
                            $url=$item['detail_url'];
                            $num=$item['quantity'];
                            $num_num = ORM::factory('dld_setgood')->where('num_iid', '=', $num_iid)->count_all();
                            if($num_num==0 && $num_iid){
                                $sql = DB::query(Database::INSERT,"INSERT INTO `dld_setgoods` (`bid`,`num_iid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,1,$type,$num)");
                                $sql->execute();
                            }else{
                                $sql = DB::query(Database::UPDATE,"UPDATE `dld_setgoods` SET `bid` = $bid ,`num_iid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 1 , `type` =$type where `num_iid` = $num_iid ");
                                $sql->execute();
                            }
                        }
                    }
                    $sql = DB::query(Database::DELETE,"DELETE FROM `dld_goodskus` where `state` =0 and `bid` = $bid ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `dld_goodskus` SET `state` =0 where `bid` = $bid");
                    $sql->execute();
                    $sql = DB::query(Database::DELETE,"DELETE FROM `dld_setgoods` where `state` =0 and `bid` = $bid ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `dld_setgoods` SET `state` =0 where `bid` = $bid");
                    $sql->execute();
                }
            }
        }
        Request::instance()->redirect('dlda/setgoods');
    }
    public function action_setgoods(){
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid, 1);
        $goods = ORM::factory('dld_setgood')->where('bid','=',$bid);
        $goods = $goods->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods = $goods->where('title', 'like', $s);
        }
        if ($_POST['form']['num_iid']) {
            $goodid = $_POST['form']['num_iid'];
            // echo '<pre>';
            // var_dump($_POST['form']);
            if($_POST['form']['smoney']){
                foreach ($_POST['form']['smoney'] as $k => $v) {
                    // echo $k.'<br>';// sid
                    // echo $v['money'].'<br>'; //money
                    // echo $v['skuid'].'<br>'; //skuid
                    // echo $v['suiteid'].'<br>'; //suiteid
                    // echo $_POST['form']['num_iid'].'<br>'; //num_iid
                    // exit;
                    $sku = ORM::factory('dld_goodsku')->where('id','=',$v['skuid'])->find();
                    if($sku->sku_id){

                    }else{
                        $sku->sku_id = 0;
                    }
                    $smoney = ORM::factory('dld_smoney')->where('bid','=',$bid)->where('sid','=',$v['suiteid'])->where('sku_id','=',$sku->sku_id)->where('item_id','=',$_POST['form']['num_iid'])->find();
                    $smoney->bid = $bid;
                    $smoney->sid = $v['suiteid'];
                    $smoney->sku_id = $sku->sku_id;
                    $smoney->item_id = $_POST['form']['num_iid'];
                    $smoney->money = $v['money'];
                    $smoney->save();
                }
            }
            if($_POST['form']['type']==1){
                $sku=$_POST['form']['money'];
                foreach ($sku as $k => $v) {
                    //echo $k."<br>";
                   $setsku=ORM::factory('dld_goodsku')->where('id','=',$k)->find();
                   $setsku->money=$v;
                   $setsku->save();
                }
            }
            $good = ORM::factory('dld_setgood')->where('bid', '=', $bid)->where('num_iid','=',$goodid)->find();
            if(isset($_POST['form']['status'])){
                $good->status=$_POST['form']['status'];
            }
            if($_POST['form']['type']!=1){
                $good->money=$_POST['form']['money'];
            }
            $good->save();
        }
        $result['countall'] = $countall = $goods->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');

        $result['goods'] =$goods->order_by('status', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $result['suite'] = ORM::factory('dld_suite')->where('bid','=',$bid)->find_all();
      //   //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
      //   require_once Kohana::find_file("vendor/kdt","YZTokenClient");
      //   $this->access_token = ORM::factory('dld_login')->where('id','=',$bid)->find()->access_token;
      //   $tempconfig=ORM::factory('dld_cfg')->getCfg($this->bid);
      //   if($this->access_token)
      //   {
      //       $page = max($_GET['page'], 1);

      //       $client = new YZTokenClient($this->access_token);
      //       $method = 'kdt.items.onsale.get';
      //       $params = array(
      //            'page_size'=>20,
      //            'page_no'=>$page,
      //           'fields' => 'num_iid,title,price,pic_url,num,sold_num,detail_url',
      //       );


      //           //修改佣金


      //        $result = $client->post($method, '1.0.0', $params, $files);
      //         $pages = Pagination::factory(array(
      //           'total_items'   =>$result['response']['total_results'],
      //           'items_per_page'=> $this->pagesize,
      //       ))->render('weixin/dld/admin/pages');
      // }
      // else
      //   $result['response']=array();

    $this->template->content=View::factory('weixin//dld/admin/setgoods')
    ->bind('result',$result)
    ->bind('pages',$pages)
    ->bind('bid',$this->bid);

     }
    public function action_customers(){
        $bid = $this->bid;
        $customers=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('lv','!=',1)->where('fopenid','!=','');
        $customers =  $customers->reset(FALSE);
         //分页
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='客户明细';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '微信昵称')
                        ->setCellValue('B'.$num, '累计订单数')
                        ->setCellValue('C'.$num, '累计订单金额')
                        ->setCellValue('D'.$num, '所属代理');
            $customer1s=$customers->find_all();
            foreach($customer1s as $k => $v){
                $allnum=ORM::factory('dld_trade')->where('bid','=',$bid)->where('deletedd','=',0)->where('openid','=',$v->openid)->count_all();
                $allmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as allmoney from dld_trades where bid=$bid and deletedd = 0 and `openid` = '$v->openid' ")->execute()->as_array();
                    $allmoney=$allmoney[0]['allmoney'];
                $fname=ORM::factory('dld_qrcode')->where('bid','=',$bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->nickname)
                            ->setCellValue('B'.$num, $allnum)
                            ->setCellValue('C'.$num, $allmoney)
                            ->setCellValue('D'.$num, $fname);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $customers = $customers->where_open()->where('nickname', 'like', $s)->or_where('receiver_mobile', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }
        if($_GET['qid']){
            $openid=ORM::factory('dld_qrcode')->where('id','=',$_GET['qid'])->find()->openid;
            $customers=$customers->where('fopenid','=', $openid);
        }
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));


        $countall=$result['countall']= $customers->count_all();
        $result['customers']= $customers->limit($this->pagesize)->offset($offset)->find_all();
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');
        $this->template->content=View::factory('weixin//dld/admin/customers')
            ->bind('pages',$pages)
            ->bind('result',$result)
            ->bind('bid',$this->bid);
    }
    public function action_calculate(){
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        $month = date("Y-m",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        //$month=date('Y-m',time());
        if ($_GET['data']['begin']) {
            $month= $_GET['data']['begin'];
        }else{
            $_GET['data']['begin']=$month;
        }
        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $time=$_POST['form']['time'];
            $money=$_POST['form']['money'];
            $qrcode_edit = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if($_POST['form']['type']==1){
                $type=2;
                $result = $this->sendMoney($qrcode_edit,$money*100,$time);
                if($result['result_code']=='FAIL'){
                    echo '付款失败：'.$result['err_code'];
                    exit;
                }else{
                    ORM::factory('dld_score')->scoreOut($qrcode_edit, $type, $money,'','',$time);
                }
            }elseif($_POST['form']['type']==2){
                $type=3;
                if ($money){
                    ORM::factory('dld_score')->scoreOut($qrcode_edit, $type, $money,'','',$time);
                }
            }
            $qrcode_edit->save();
        }
        $qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('lv','=',1);
        $qrcode = $qrcode->reset(FALSE);
         if ($_GET['export']=='xls') {
            $qrcode->order_by('id', 'DESC');
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='奖励对账单';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '微信昵称')
                        ->setCellValue('B'.$num, '姓名')
                        ->setCellValue('C'.$num, '电话')
                        ->setCellValue('D'.$num, '支付宝账号')
                        ->setCellValue('E'.$num, '当月团队奖励')
                        ->setCellValue('F'.$num, '当月团队可结算奖励')
                        ->setCellValue('G'.$num, '当月团队待结算奖励')
                        ->setCellValue('H'.$num, '当月个人总团队奖励')
                        ->setCellValue('I'.$num, '当月个人可结算团队奖励')
                        ->setCellValue('J'.$num, '当月个人待结算团队奖励')
                        // ->setCellValue('K'.$num, '当月总销售利润')
                        // ->setCellValue('L'.$num, '当月可结算销售利润')
                        // ->setCellValue('M'.$num, '当月待结算销售利润')
                        // ->setCellValue('K'.$num, '当月总收益')
                        // ->setCellValue('L'.$num, '当月可结算收益')
                        ->setCellValue('K'.$num, '账期')
                        ->setCellValue('L'.$num, '是否结算')
                        ->setCellValue('M'.$num, '上级代理');
            $qrcode1s=$qrcode->find_all();
            foreach($qrcode1s as $k => $v){
                $groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                $nawtime=time();
                $monthtype='%Y-%m';
                $score=ORM::factory('dld_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$month)->find();
                if($score->id){
                  $flag=1;
                }else{
                  $flag=0;
                }
                $month_tmoney=0;
                $month_pmoney=0;
                $monthjs_tmoney=0;
                $monthjs_pmoney=0;
                foreach ($groups as $group) {
                    if($group->bottom){
                    $bottom1='('.$group->id.','.$group->bottom.')';
                    }else{
                        $bottom1='('.$group->id.')';
                    }
                    $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney1 from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_tmoney1=$month_tmoney1[0]['month_tmoney1'];
                    $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from dld_trades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                    //echo  $month_tmoney.'<br>';
                    $skujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                    if(!$skujs->id){
                        $fskujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                        if(!$fskujs->id){
                           $scalejs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                        }else{
                            $scalejs=0;
                        }
                    }else{
                        $scalejs=$skujs->scale;
                    }
                    $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                      if(!$sku->id){
                          $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                          if(!$fsku->id){
                             $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                          }else{
                              $scale=0;
                          }
                      }else{
                          $scale=$sku->scale;
                      }
                    $month_tmoney+=$month_tmoney1*$scale/100;

                    $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;
                      // echo  $month_tmoney.'<br>';
                      // echo $group->id."<br>";
                    $child_groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                    $child_moneys=0;
                    $childjs_moneys=0;
                    foreach ($child_groups as $child_group) {
                          if($child_group->bottom){
                               $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                            }else{
                                  $bottom2='('.$child_group->id.')';
                            }

                          //echo $bottom2."<br>";
                          $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                          $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from dld_trades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                          $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                          $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                          //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                          $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>=',$month_ltmoney)->find();
                           $skujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>=',$monthjs_ltmoney)->find();
                          if(!$skujs->id){
                              $fskujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                              if(!$fskujs->id){
                                 $scalejs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                              }else{
                                  $scalejs=0;
                              }
                          }else{
                              $scalejs=$skujs->scale;
                          }
                          if(!$sku->id){
                              $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_ltmoney)->find();
                              if(!$fsku->id){
                                 $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                              }else{
                                  $scale=0;
                              }
                          }else{
                              $scale=$sku->scale;
                          }
                          $child_money= $month_ltmoney*$scale/100;
                          $child_moneys+=$child_money;
                          $childjs_money= $monthjs_ltmoney*$scalejs/100;
                          $childjs_moneys+=$childjs_money;
                    }
                    //echo  $child_moneys.'<br>';
                    $month_pmoney+=$month_tmoney-$child_moneys;
                    $monthjs_pmoney+=$monthjs_tmoney-$childjs_moneys;
                    //echo  $month_pmoney.'<br>';当月个人团队奖励
                }
                // $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                // $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                // $monthjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthjs_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                // $monthjs_pxmoney=$monthjs_pxmoney[0]['monthjs_pxmoney'];
                // $monthdjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthdjs_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and paydate >= $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                // $monthdjs_pxmoney=$monthdjs_pxmoney[0]['monthdjs_pxmoney'];
                // //当月销售利润
                // //echo  $month_pxmoney.'<br>';
                // $all_money=$month_pmoney+$month_pxmoney;
                // $alljs_money=$monthjs_pmoney+$monthjs_pxmoney;
                //累计销售利润
                $fname=ORM::factory('dld_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                $score=ORM::factory('dld_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$_GET['data']['begin'])->find();
                if($score->id){
                  $flag='已结算';
                }else{
                  $flag='未结算';
                }
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->nickname)
                            ->setCellValue('B'.$num, $v->name)
                            ->setCellValue('C'.$num, $v->tel)
                            ->setCellValue('D'.$num, $v->alipay_name)
                            ->setCellValue('E'.$num, number_format($month_tmoney,2))
                            ->setCellValue('F'.$num, number_format($monthjs_tmoney,2))
                            ->setCellValue('G'.$num, number_format($month_tmoney-$monthjs_tmoney,2))
                            ->setCellValue('H'.$num, number_format($month_pmoney,2))
                            ->setCellValue('I'.$num, number_format($monthjs_pmoney,2))
                            ->setCellValue('J'.$num, number_format($month_pmoney-$monthjs_pmoney,2))
                            // ->setCellValue('K'.$num, $month_pxmoney)
                            // ->setCellValue('L'.$num, $monthjs_pxmoney)
                            // ->setCellValue('M'.$num, $monthdjs_pxmoney)
                            // ->setCellValue('K'.$num, number_format($month_pmoney,2))
                            // ->setCellValue('L'.$num, number_format($monthjs_pmoney,2))
                            ->setCellValue('K'.$num, $_GET['data']['begin'])
                            ->setCellValue('L'.$num, $flag)
                            ->setCellValue('M'.$num, $fname);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('alipay_name', 'like', $s)->or_where('tel', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }
        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');

        $result['qrcodes'] = $qrcode->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content=View::factory('weixin//dld/admin/calculate')
            ->bind('pages',$pages)
            ->bind('month',$month)
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid',$this->bid);

    }
    public function action_profit(){
        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid,1);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];
        $month=date('Y-m',time());
        if ($_GET['data']['begin']) {
            $month= $_GET['data']['begin'];
        }else{
            $_GET['data']['begin']=date('Y-m',time());
        }
        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $time=$_POST['form']['time'];
            $money=$_POST['form']['money'];
            $qrcode_edit = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if($_POST['form']['type']==1){
                $type=5;
                $result = $this->sendMoney1($qrcode_edit,$money*100,$time);
                if($result['result_code']=='FAIL'){
                    echo '付款失败：'.$result['err_code'];
                    exit;
                }else{
                    ORM::factory('dld_score')->scoreOut($qrcode_edit, $type, $money,'','','');
                }
            }elseif($_POST['form']['type']==2){
                $type=6;
                if ($money){
                    ORM::factory('dld_score')->scoreOut($qrcode_edit, $type, $money,'','','');
                }
            }
            $qrcode_edit->save();
        }
        $qrcode = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where('lv','=',1);
        $qrcode = $qrcode->reset(FALSE);
         if ($_GET['export']=='xls') {
            $qrcode->order_by('id', 'DESC');
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='利润对账单';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '微信昵称')
                        ->setCellValue('B'.$num, '姓名')
                        ->setCellValue('C'.$num, '电话')
                        ->setCellValue('D'.$num, '支付宝账号')
                        // ->setCellValue('E'.$num, '当月团队奖励')
                        // ->setCellValue('F'.$num, '当月团队可结算奖励')
                        // ->setCellValue('G'.$num, '当月团队待结算奖励')
                        // ->setCellValue('H'.$num, '当月个人总团队奖励')
                        // ->setCellValue('I'.$num, '当月个人可结算团队奖励')
                        // ->setCellValue('J'.$num, '当月个人待结算团队奖励')
                        ->setCellValue('E'.$num, '累计销售利润')
                        ->setCellValue('F'.$num, '目前可结算销售利润')
                        ->setCellValue('G'.$num, '待结算销售利润')
                        ->setCellValue('H'.$num, '已结算销售利润')
                        // ->setCellValue('N'.$num, '当月总收益')
                        // ->setCellValue('O'.$num, '当月可结算收益')
                        // ->setCellValue('H'.$num, '账期')
                        // ->setCellValue('I'.$num, '是否结算')
                        ->setCellValue('I'.$num, '上级代理');
            $qrcode1s=$qrcode->find_all();
            foreach($qrcode1s as $k => $v){
                $groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                $nawtime=time();
                $monthtype='%Y-%m';
                $score=ORM::factory('dld_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$month)->find();
                if($score->id){
                  $flag=1;
                }else{
                  $flag=0;
                }
                // $month_tmoney=0;
                // $month_pmoney=0;
                // $monthjs_tmoney=0;
                // $monthjs_pmoney=0;
                // foreach ($groups as $group) {
                //     if($group->bottom){
                //     $bottom1='('.$group->id.','.$group->bottom.')';
                //     }else{
                //         $bottom1='('.$group->id.')';
                //     }
                //     $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney1 from dld_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                //     $month_tmoney1=$month_tmoney1[0]['month_tmoney1'];
                //     $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from dld_trades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                //     $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                //     //echo  $month_tmoney.'<br>';
                //     $skujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                //     if(!$skujs->id){
                //         $fskujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                //         if(!$fskujs->id){
                //            $scalejs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                //         }else{
                //             $scalejs=0;
                //         }
                //     }else{
                //         $scalejs=$skujs->scale;
                //     }
                //     $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                //       if(!$sku->id){
                //           $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                //           if(!$fsku->id){
                //              $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                //           }else{
                //               $scale=0;
                //           }
                //       }else{
                //           $scale=$sku->scale;
                //       }
                //     $month_tmoney+=$month_tmoney1*$scale/100;

                //     $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;
                //       // echo  $month_tmoney.'<br>';
                //       // echo $group->id."<br>";
                //     // $child_groups=ORM::factory('dld_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                //     // $child_moneys=0;
                //     // $childjs_moneys=0;
                //     // foreach ($child_groups as $child_group) {
                //     //       if($child_group->bottom){
                //     //            $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                //     //         }else{
                //     //               $bottom2='('.$child_group->id.')';
                //     //         }

                //     //       //echo $bottom2."<br>";
                //     //       $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from dld_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                //     //       $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from dld_trades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                //     //       $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                //     //       $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                //     //       //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                //     //       $sku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>=',$month_ltmoney)->find();
                //     //        $skujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>=',$monthjs_ltmoney)->find();
                //     //       if(!$skujs->id){
                //     //           $fskujs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                //     //           if(!$fskujs->id){
                //     //              $scalejs=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                //     //           }else{
                //     //               $scalejs=0;
                //     //           }
                //     //       }else{
                //     //           $scalejs=$skujs->scale;
                //     //       }
                //     //       if(!$sku->id){
                //     //           $fsku=ORM::factory('dld_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_ltmoney)->find();
                //     //           if(!$fsku->id){
                //     //              $scale=ORM::factory('dld_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                //     //           }else{
                //     //               $scale=0;
                //     //           }
                //     //       }else{
                //     //           $scale=$sku->scale;
                //     //       }
                //     //       $child_money= $month_ltmoney*$scale/100;
                //     //       $child_moneys+=$child_money;
                //     //       $childjs_money= $monthjs_ltmoney*$scalejs/100;
                //     //       $childjs_moneys+=$childjs_money;
                //     // }
                //     //echo  $child_moneys.'<br>';
                //     $month_pmoney+=$month_tmoney-$child_moneys;
                //     $monthjs_pmoney+=$monthjs_tmoney-$childjs_moneys;
                //     //echo  $month_pmoney.'<br>';当月个人团队奖励
                // }
                $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
                $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                $monthjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthjs_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0 ")->execute()->as_array();
                $monthjs_pxmoney=$monthjs_pxmoney[0]['monthjs_pxmoney'];
                $monthdjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthdjs_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and paydate >= $nawtime and score > 0 ")->execute()->as_array();
                $monthdjs_pxmoney=$monthdjs_pxmoney[0]['monthdjs_pxmoney'];
                $monthyjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthyjs_pxmoney from dld_scores where bid=$v->bid and qid = $v->id and type IN (5,6) ")->execute()->as_array();
                $monthyjs_pxmoney=$monthyjs_pxmoney[0]['monthyjs_pxmoney'];
                //当月销售利润
                //echo  $month_pxmoney.'<br>';
                $all_money=$month_pmoney+$month_pxmoney;
                $alljs_money=$monthjs_pmoney+$monthjs_pxmoney;
                //累计销售利润
                $fname=ORM::factory('dld_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                $score=ORM::factory('dld_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$_GET['data']['begin'])->find();
                if($score->id){
                  $flag='已结算';
                }else{
                  $flag='未结算';
                }
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->nickname)
                            ->setCellValue('B'.$num, $v->name)
                            ->setCellValue('C'.$num, $v->tel)
                            ->setCellValue('D'.$num, $v->alipay_name)
                            // ->setCellValue('E'.$num, $month_tmoney)
                            // ->setCellValue('F'.$num, $monthjs_tmoney)
                            // ->setCellValue('G'.$num, $month_tmoney-$monthjs_tmoney)
                            // ->setCellValue('H'.$num, $month_pmoney)
                            // ->setCellValue('I'.$num, $monthjs_pmoney)
                            // ->setCellValue('J'.$num, $month_pmoney-$monthjs_pmoney)
                            ->setCellValue('E'.$num, number_format($month_pxmoney,2))
                            ->setCellValue('F'.$num, number_format($monthjs_pxmoney,2))
                            ->setCellValue('G'.$num, number_format($monthdjs_pxmoney,2))
                            ->setCellValue('H'.$num, number_format(-$monthyjs_pxmoney,2))
                            // ->setCellValue('N'.$num, $all_money)
                            // ->setCellValue('O'.$num, $alljs_money)
                            // ->setCellValue('H'.$num, $_GET['data']['begin'])
                            // ->setCellValue('I'.$num, $flag)
                            ->setCellValue('I'.$num, $fname);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('alipay_name', 'like', $s)->or_where('tel', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }
        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');

        $result['qrcodes'] = $qrcode->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content=View::factory('weixin//dld/admin/profit')
            ->bind('pages',$pages)
            ->bind('month',$month)
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid',$this->bid);

    }
    public function action_history_scores(){

        $bid = $this->bid;
        $config = ORM::factory('dld_cfg')->getCfg($bid);
        $type=array();
        $type[0]=2;
        $type[1]=3;
        $type[2]=5;
        $type[3]=6;
        $scores = ORM::factory('dld_score')->where('bid', '=', $bid)->where('type','IN',$type);
        if($_GET['qid']){
           $scores=$scores->where('qid','=',$_GET['qid']);
           if($_GET['flag']=='jl'){
                $scores=$scores->where('type','IN', array(2,3));
           }elseif ($_GET['flag']=='lr') {
                $scores=$scores->where('type','IN', array(5,6));
           }
        }
        if($_GET['s']['type']){

            if($_GET['s']['text']){
                $s = '%'.trim($_GET['s']['text'].'%');
                $user = ORM::factory('dld_qrcode')->where('bid', '=', $bid)->where_open()->where('nickname', 'like', $s)->or_where('alipay_name', 'like', $s)->or_where('tel', 'like', $s)->where_close()->find_all();
                $user_arr[0] = 0;//qid
                foreach ($user as $k => $v) {
                    $k++;
                    $user_arr[$k] = $v->id;//qid
                }
                $scores=$scores->where('qid','IN',$user_arr);
            }
            if($_GET['s']['type']==1){
                $scores=$scores->where('type','IN', array(2,3));
            }
            if($_GET['s']['type']==2){
                $scores=$scores->where('type','IN', array(5,6));
            }
        }

        $scores = $scores->reset(FALSE);
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='结算记录';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '昵称')
                        ->setCellValue('B'.$num, '金额')
                        ->setCellValue('C'.$num, '账单时间')
                        ->setCellValue('D'.$num, '结算时间')
                        ->setCellValue('E'.$num, '结算类型');
            $score1s=$scores->order_by('lastupdate','DESC')->limit(400)->find_all();
            foreach($score1s as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->qrcode->nickname)
                            ->setCellValue('B'.$num, -$v->score)
                            ->setCellValue('C'.$num, $v->bz)
                            ->setCellValue('D'.$num, date('Y-m-d H:i:s',$v->lastupdate))
                            ->setCellValue('E'.$num, $v->getTypeName($v->type));
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        $result['countall'] = $countall = $scores->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/dld/admin/pages');

        $result['scores'] = $scores->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/dld/admin/history_scores')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

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
        //Kohana::$log->add("weixin_dld:$bid:tplmsg", print_r($openid, true));
         //Kohana::$log->add("weixin_dld:$bid:tplmsg", print_r($tplmsg, true));
        $result = $this->we->sendTemplateMessage($tplmsg);
        Kohana::$log->add("weixin_dld:tpl", print_r($result, true));
        return $result;
    }
    private function sendMoney1($userobj, $money,$time) {
        $config = $this->config;
        $openid = $userobj->openid;

        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $this->we = $we = new Wechat($config);
        }

        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["mch_appid"] = $config['appid'];
        $data["mchid"] = $config['partnerid']; //商户号
        $data["nonce_str"] = $this->we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.'的利润结算';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->we->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_dld:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_dld:hongbaoresult', print_r($result, true));
        return $result;
    }
    private function sendMoney($userobj, $money,$time) {
        $config = $this->config;
        $openid = $userobj->openid;

        if (!$this->we) {
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $this->we = $we = new Wechat($config);
        }

        $mch_billno = $config['partnerid'] . date('YmdHis').rand(1000, 9999); //订单号

        $data["mch_appid"] = $config['appid'];
        $data["mchid"] = $config['partnerid']; //商户号
        $data["nonce_str"] = $this->we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号

        $data["openid"] = $openid;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名

        $data["amount"] = $money;
        $data["desc"] = $userobj->nickname.'的'.$time.'月奖励';

        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址

        $data["sign"] = strtoupper(md5($this->we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));
        $postXml = $this->we->xml_encode($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        // Kohana::$log->add('weixin_dld:hongbaopost', print_r($data, true));

        $resultXml = $this->curl_post_ssl($url, $postXml, 10);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        // Kohana::$log->add('weixin_dld:hongbaoresult', print_r($result, true));
        return $result;
    }

    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."dld/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."dld/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('dld_cfg')->where('bid', '=', $bid)->where('key', '=', 'dld_file_cert')->find();
        $file_key = ORM::factory('dld_cfg')->where('bid', '=', $bid)->where('key', '=', 'dld_file_key')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        // Kohana::$log->add("weixin_dld:$bid:curl_post_ssl:cert_file", $cert_file);

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
