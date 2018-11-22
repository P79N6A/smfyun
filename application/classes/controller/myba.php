<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_Myba extends Controller_Base {

    public $template = 'weixin/myb/tpl/fatpl';
    public $pagesize = 20;
    public $access_token;
    public $config;
    public $bid;
    public $appId = 'wx5e00a61d465c412e';
    public $appSecret = '8c8f2b05fa512f3b5b561641eada14f9';
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "wdy";

        $_SESSION =& Session::instance()->as_array();
        parent::before();
        if (Request::instance()->action == 'test') return;
        $this->bid = $_SESSION['myba']['bid'];
        $this->config = $_SESSION['myba']['config'];
        $this->access_token=ORM::factory('myb_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/myba/login');
            header('location:/myba/login?from='.Request::instance()->action);
            exit;
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['users'] = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->count_all();
            $todo['tickets'] = ORM::factory('myb_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['items'] = ORM::factory('myb_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            $todo['all'] = $todo['items'] + $todo['users'];
            $this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }
    public function action_test(){
        set_time_limit(0);
        $start_created = "2017-08-26 13:20:00";
        $end_created = "2017-08-31 21:40:00";
        $this->bid=$bid=1;
        $this->access_token=ORM::factory('myb_login')->where('id', '=', $this->bid)->find()->access_token;
        require_once Kohana::find_file("vendor","kdt/YZTokenClient");
        if($this->access_token){
             $client = new YZTokenClient($this->access_token);
        }else{
             echo "没有access_token";
        }
        for($pg=1,$next=true;$next==true;$pg++){
            $method = 'youzan.trades.sold.get';
            $params = [
             'page_size' =>100,
             'page_no' =>$pg ,
             'use_has_next'=>true,
             // 'status'=>'WAIT_SELLER_SEND_GOODS',
             'start_created'=>$start_created,
             'end_created'=>$end_created,
            ];
            $results = $client->post($method, '3.0.0', $params, $files);
            $next = $results['response']['has_next'];
            for($i=0;$results['response']['trades'][$i];$i++){
                $res=$results['response']['trades'][$i];
                // echo "<pre>";
                // var_dump($res['fans_info']);
                // echo "</pre>";
                echo 'pay_time:'.$res['pay_time']."<br>";
                echo 'status:'.$res['status']."<br>";
                echo 'title:'.$res['title']."<br>";
                echo 'fans_nickname:'.$res['fans_info']['fans_nickname']."<br>";
                echo 'fans_weixin_openid:'.$res['fans_info']['fans_weixin_openid']."<br>";
                echo 'fans_id:'.$res['fans_info']['fans_id']."<br>";
                echo 'buyer_id:'.$res['fans_info']['buyer_id']."<br>";
                echo 'fans_type:'.$res['fans_info']["fans_type"]."<br>";
                echo 'tid:'.$res['tid']."<br>";
                $method = 'youzan.users.weixin.follower.get';
                $params = [
                    'fans_id'=>$res['fans_info']["fans_id"],
                ];
                $result = $client->post($method, '3.0.0', $params, $files);
                echo "<pre>";
                var_dump($result);
                echo "</pre>";
                echo "---------------------------------<br>";
             }
        }
        exit();
    }
    public function action_index() {
        $this->action_login();
    }
     public function action_calculate(){
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid,1);
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
            $qrcode_edit = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if($_POST['form']['type']==1){
                $type=9;
                $result = $this->sendMoney($qrcode_edit,$money*100,$time);
                if($result['result_code']=='FAIL'){
                    echo '付款失败：'.$result['err_code'];
                    exit;
                }else{
                    ORM::factory('myb_score')->scoreOut($qrcode_edit, $type, $money,'','',$time);
                }
            }elseif($_POST['form']['type']==2){
                $type=10;
                if ($money){
                    ORM::factory('myb_score')->scoreOut($qrcode_edit, $type, $money,'','',$time);
                }
            }
            $qrcode_edit->save();
        }
        $qrcode = ORM::factory('myb_qrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);
         if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='对账单';
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
                        ->setCellValue('E'.$num, '当月收益')
                        ->setCellValue('F'.$num, '当月可结算收益')
                        ->setCellValue('G'.$num, '当月待结算收益')
                        ->setCellValue('H'.$num, '累计收益')
                        ->setCellValue('I'.$num, '累计可结算收益')
                        ->setCellValue('J'.$num, '账期')
                        ->setCellValue('K'.$num, '是否结算')
                        ->setCellValue('L'.$num, '上线');
            $qrcode1s=$qrcode->find_all();
            foreach($qrcode1s as $k => $v){
                $nawtime=time();
                $monthtype='%Y-%m';
                $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                $monthjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthjs_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                $monthjs_pxmoney=$monthjs_pxmoney[0]['monthjs_pxmoney'];
                $monthdjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthdjs_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and paydate >= $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                $monthdjs_pxmoney=$monthdjs_pxmoney[0]['monthdjs_pxmoney'];
                //累计销售利润
                $monthlj_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthlj_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
                $monthlj_pxmoney=$monthlj_pxmoney[0]['monthlj_pxmoney'];
                $monthdljjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthljjs_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0  ")->execute()->as_array();
                $monthljjs_pxmoney=$monthljjs_pxmoney[0]['monthljjs_pxmoney'];
                $fname=ORM::factory('myb_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->find()->nickname;
                $score=ORM::factory('myb_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$_GET['data']['begin'])->find();
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
                            ->setCellValue('E'.$num, $month_pxmoney)
                            ->setCellValue('F'.$num, $monthjs_pxmoney)
                            ->setCellValue('G'.$num, $monthdjs_pxmoney)
                            ->setCellValue('H'.$num, $monthlj_pxmoney)
                            ->setCellValue('I'.$num, $monthljjs_pxmoney)
                            ->setCellValue('J'.$num, $_GET['data']['begin'])
                            ->setCellValue('K'.$num, $flag)
                            ->setCellValue('L'.$num, $fname);
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
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->where_close(); //->or_where('openid', 'like', $s);
        }
        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/myb/admin/pages');

        $result['qrcodes'] = $qrcode->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content=View::factory('weixin//myb/admin/calculate')
            ->bind('month',$month)
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid',$this->bid);

    }
    public function action_history_scores(){

        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);
        $type=array();
        $type[0]=9;
        $type[1]=10;
        $scores = ORM::factory('myb_score')->where('bid', '=', $bid)->where('type','IN',$type);
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
        if($_GET['qid']){
             $scores=$scores->where('qid','=',$_GET['qid']);
        }
        $result['countall'] = $countall = $scores->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/myb/admin/pages');

        $result['scores'] = $scores->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/myb/admin/history_scores')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    //系统配置
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=01d4e8941f3a8c7322&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/myba/callback');
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"01d4e8941f3a8c7322",
            "client_secret"=>"165b7748358bc9e71fb7035ee93a5758",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/myba/callback'
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
            $usershop = ORM::factory('myb_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("myba/home")."';</script>";
        }
        //Request::instance()->redirect('myba/home');
    }
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('myb_login', $bid);
            $old_password = $biz->pass;

            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }

        $cert_file = DOCROOT."myb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."myb/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('myb_cfg');

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

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'myb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'myb_file_key', '', file_get_contents($key_file));

            //重新读取配置
            $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        }

         //菜单配置
        if ($_POST['menu']) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $buser = ORM::factory('myb_login')->where('id','=',$bid)->find();
            $cfg = ORM::factory('myb_cfg');

            if(!$_POST['menu']['key_score']&&!$_POST['menu']['key_item']&&!$_POST['menu']['key_top']&&!$_POST['menu']['key_qrcode']){//如果菜单为空
                // $_POST['menu']['key_score']='积分查询';
                // $_POST['menu']['key_item']='积分兑换';
                // $_POST['menu']['key_top']='积分排行';
                // $_POST['menu']['key_qrcode']='生成海报';
            }
            if(!$_POST['menu']['key_score']){//积分查询 必须保留
                $_POST['menu']['key_score']='积分查询';
            }
            foreach ($_POST['menu'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }
            //配置菜单更新
            // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            //$we = new Wechat($config);
            if(!$bid) Kohana::$log->add('mybbid:', 'menu');//写入日志，可以删除
            $we = new wechat($config);
            // $data['button'][0]['name']=$_POST['menu']['key_menu'];
            $i=0;
            // if($_POST['menu']['key_qrcode']){
            //     $data['button'][0]['sub_button'][0]['type']='click';
            //     $data['button'][0]['sub_button'][0]['name']=$_POST['menu']['key_qrcode'];
            //     $data['button'][0]['sub_button'][0]['key']='qrcode';
            // }
            // if($_POST['menu']['key_score']){
            //     $i++;
            //     $data['button'][0]['sub_button'][$i]['type']='click';
            //     $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_score'];
            //     $data['button'][0]['sub_button'][$i]['key']='score';
            // }
            // if($_POST['menu']['key_item']){
            //     $i++;
            //     $data['button'][0]['sub_button'][$i]['type']='click';
            //     $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_item'];
            //     $data['button'][0]['sub_button'][$i]['key']='item';
            // }
            // if($_POST['menu']['key_top']){
            //     $i++;
            //     $data['button'][0]['sub_button'][$i]['type']='click';
            //     $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_top'];
            //     $data['button'][0]['sub_button'][$i]['key']='top';
            // }
            if($_POST['menu']['key_b0']){
                if($_POST['menu']['value_b1']){
                    for ($i=1; $i <=5 ; $i++) {
                        if($_POST['menu']['key_b'.$i]){
                            $m=$i;
                        }
                    }
                    $data['button'][0]['name']=$_POST['menu']['key_b0'];
                    for ($i=0 ; $i<$m; $i++){
                        $y=$i+1;
                        if(substr($_POST['menu']['value_b'.$y], 0,4)=='http'){
                            $data['button'][0]['sub_button'][$i]['type']='view';
                            $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_b'.$y];
                            $data['button'][0]['sub_button'][$i]['url']=$_POST['menu']['value_b'.$y];
                        }else{
                            $data['button'][0]['sub_button'][$i]['type']='click';
                            $data['button'][0]['sub_button'][$i]['name']=$_POST['menu']['key_b'.$y];
                            $data['button'][0]['sub_button'][$i]['key']='key_b'.$y;
                        }
                    }
                }else{
                    if(substr($_POST['menu']['value_b0'], 0,4)=='http'){
                        $data['button'][0]['type']='view';
                        $data['button'][0]['name']=$_POST['menu']['key_b0'];
                        $data['button'][0]['url']=$_POST['menu']['value_b0'];
                    }else{
                        $data['button'][0]['type']='click';
                        $data['button'][0]['name']=$_POST['menu']['key_b0'];
                        $data['button'][0]['key']='key_b0';
                    }
                }
            }
            if($_POST['menu']['key_c0']){
                if($_POST['menu']['value_c1']){
                    for ($i=1; $i <=5 ; $i++) {
                        if($_POST['menu']['key_c'.$i]){
                            $m=$i;
                        }
                    }
                    $data['button'][1]['name']=$_POST['menu']['key_c0'];
                    for ($i=0 ; $i<$m; $i++){
                        $y=$i+1;
                        if(substr($_POST['menu']['value_c'.$y], 0,4)=='http'){
                            $data['button'][1]['sub_button'][$i]['type']='view';
                            $data['button'][1]['sub_button'][$i]['name']=$_POST['menu']['key_c'.$y];
                            $data['button'][1]['sub_button'][$i]['url']=$_POST['menu']['value_c'.$y];
                        }else{
                            $data['button'][1]['sub_button'][$i]['type']='click';
                            $data['button'][1]['sub_button'][$i]['name']=$_POST['menu']['key_c'.$y];
                            $data['button'][1]['sub_button'][$i]['key']='key_c'.$y;
                        }
                    }
                }else{
                     if(substr($_POST['menu']['value_c0'], 0,4)=='http'){
                        $data['button'][1]['type']='view';
                        $data['button'][1]['name']=$_POST['menu']['key_c0'];
                        $data['button'][1]['url']=$_POST['menu']['value_c0'];
                    }else{
                        $data['button'][1]['type']='click';
                        $data['button'][1]['name']=$_POST['menu']['key_c0'];
                        $data['button'][1]['key']='key_c0';
                    }
                }
            }
            echo "<pre>";
            var_dump($data);
            echo "</pre>";
            $menu = $we->createMenu($data);
            var_dump($menu);
            exit();
            if($menu==true) {
                $result['menu']=1;
            }else{
                $result['menu']=0;
            }
            //重新读取配置
            $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        }
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('myb_cfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        }
        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('myb_cfg');
            $qrfile = DOCROOT."myb/tmp/$bid/tpl.{$config['appsecret']}.jpg";

            //二维码海报
            if ($_FILES['pic']['error'] == 0) {
                if ($_FILES['pic']['size'] > 1024*400) {
                    $result['err3'] = '海报模板文件不能超过 400K';
                } else {
                    $result['ok3']++;
                    $cfg->setCfg($bid, 'mybtpl', '', file_get_contents($_FILES['pic']['tmp_name']));
                    @unlink($qrfile);
                    @mkdir(dirname($qrfile),0777,true);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                }
            }

            //默认头像
            if ($_FILES['pic2']['error'] == 0) {
                if ($_FILES['pic2']['size'] > 1024*100) {
                    $result['err3'] = '默认头像文件不能超过 100K';
                } else {
                    $result['ok3']++;
                    $default_head_file = DOCROOT."myb/tmp/$bid/head.{$config['appsecret']}.jpg";
                    $cfg->setCfg($bid, 'mybtplhead', '', file_get_contents($_FILES['pic2']['tmp_name']));
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

                        $tpl = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'mybtpl')->find();
                        if ($tpl) {
                            $tpl->lastupdate = time();
                            $tpl->save();
                        }

                        $tplhead = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'mybtplhead')->find();
                        if ($tplhead) {
                            $tplhead->lastupdate = time();
                            $tplhead->save();
                        }
                    }
                }
            }

            //重新读取配置
            $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        }

        $result['tpl'] = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'mybtpl')->find()->id;
        $result['tplhead'] = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'mybtplhead')->find()->id;
        $access_token = ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/myb/admin/home')
            ->bind('result', $result)
            ->bind('oauth',$oauth)
            ->bind('config', $config);
    }

    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);
        $access_token=$this->access_token;
        $result['status'] = 0;
        $result['sort'] = 'id';
        if ($_GET['sort']) $result['sort'] = $_GET['sort'];

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
            if ($qrcode_edit->id) {
                if (isset($_POST['form']['lock'])) {
                    $qrcode_edit->lock = (int)$_POST['form']['lock'];
                    $qrcode_edit->save();
                }
                if ($_POST['form']['score'])
                    ORM::factory('myb_score')->scoreIn($qrcode_edit, 0, $_POST['form']['score']);
                if ($_POST['form']['shscore']){
                    $qrcode_edit = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('id', '=', $id)->find();
                    ORM::factory('myb_shscore')->scoreIn($qrcode_edit, 5, $_POST['form']['shscore']);
                    if($config['switch']==1){
                        $this->rsync($bid,$qrcode_edit->openid,$access_token, $_POST['form']['shscore']);
                    }
                }
                $qrcode_edit->save();
            }
        }

        $qrcode = ORM::factory('myb_qrcode')->where('bid', '=', $bid);
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

        if ($_GET['fopenid']) {//下线
            $result['fopenid'] = trim($_GET['fopenid']);
            $result['fuser'] = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fopenid'])->find();
            $qrcode = $qrcode->where('fopenid', '=', $result['fopenid']);
        }


        if ($_GET['ffopenid']) {//二级
            $result['ffopenid'] = trim($_GET['ffopenid']);
            $result['ffuser'] = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['ffopenid'])->find();

            $ffopenid=trim($_GET['ffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$ffopenid'")->execute()->as_array();
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
              //$qrcode = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid);
              $qrcode =$qrcode->where('fopenid', 'IN',$tempid);


        }
        if ($_GET['fffopenid']) {//三级
            $result['fffopenid'] = trim($_GET['fffopenid']);
            $result['fffuser'] = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $result['fffopenid'])->find();

            $fffopenid=trim($_GET['fffopenid']);
           // echo $result['ffqrcodeid']."-----";

            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM myb_qrcodes WHERE fopenid='$fffopenid'")->execute()->as_array();
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
              //var_dump($tempid);
              //$qrcode = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid);

              $tempdata = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid)->find_all();
                  $i=0;
                  $resid=array();
                  foreach ($tempdata as $res) {
                    $resid[$i]=$res->openid;
                    $i++;
                  }
                  if($resid[0]==null) $resid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
               $qrcode =$qrcode->where('fopenid', 'IN',$resid);

        }

        $result['countall'] = $countall = $qrcode->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/myb/admin/pages');

        $result['qrcodes'] = $qrcode->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '用户明细';
        $this->template->content = View::factory('weixin/myb/admin/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //兑换管理
    public function action_shorders($action='', $id=0) {
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);

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

                $order = ORM::factory('myb_shorder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
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
        }
        //一键批量订单发货
        if ($action == 'oneship' && $id){
            $shiptype = '请联系商家';
            $shipcode = '请联系商家';
            for ($i=0; $i < count($id); $i++) {
                $oid=$id[$i];
                $order = ORM::factory('myb_shorder')->where('bid', '=', $bid)->where('id', '=', $oid)->find();
                $order->status = 1;
                $order->shiptype = $shiptype;
                $order->shipcode = $shipcode;
                if($order->type==4){
                    $order->shiptype = '无';
                    $order->shipcode = '无';

                    $tempname=ORM::factory("myb_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("myb_item")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("myb_qrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                }
                $order->save();
            }

            $result['ok'] = "共批量处理 $i 个订单。";
        }
        //订单发货
        if ($action == 'ship' && $id) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $we = new Wechat($config);

            $order = ORM::factory('myb_shorder')->where('bid', '=', $bid)->where('id', '=', $id)->find();

            // print_r($_REQUEST);
            // print_r($order->as_array());exit;

            if ($order->status == 0) {
                $order->status = 1;
                $order->save();

                //有单号的情况
                if ($_REQUEST['shiptype'] && $_REQUEST['shipcode']) {
                    $_SESSION['myba']['shiptype'] = $_REQUEST['shiptype'];
                    $_SESSION['myba']['shipcode'] = $_REQUEST['shipcode'];
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
                    $order->shiptype = '无';
                    $order->shipcode = '无';

                    $tempname=ORM::factory("myb_login")->where("id","=",$bid)->find()->user;
                    $tempmoney=ORM::factory("myb_item")->where("id","=",$order->iid)->find()->price;
                    $openid = ORM::factory("myb_qrcode")->where("id","=",$order->qid)->find()->openid;
                    $tempmoney=$tempmoney*100;
                    $hbresult = $this->hongbao($config, $openid, '', $tempname, $tempmoney);
                }

                //Request::instance()->redirect('myba/orders?p='.$_GET['page']);
            }
        }

        $result['status'] = 0;
        $result['sort'] = 'id';
        // $result['sort'] = 'lastupdate';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $order = ORM::factory('myb_shorder')->where('bid', '=', $bid)->where('status', '=', $result['status']);
        $order = $order->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $countuser = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->count_all();
            if($countuser>0){
                $user = ORM::factory('myb_qrcode')->where('bid', '=', $bid)->where('nickname','=',$_GET['s'])->find_all();
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
            $result['qrcode'] = ORM::factory('myb_qrcode', $result['qid']);
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
        if ($_GET['type']=="hb") {
            $order = $order->where('type', '=', 4);
            $active_type="hb";
        }

        $countall = $order->count_all();

        //下载
        if ($_GET['export']=='csv') {
             $tempname="全部";
            switch ($_GET["tag"]) {
                case 'fare':
                    $orders=$order->where('type','=',3)->limit(1000)->find_all();
                    $tempname="充值";
                    break;
                case'object':
                    $orders=$order->where('type','=',null)->limit(1000)->find_all();
                    $tempname="实物";
                    break;
                case'code':
                    $orders=$order->where('type','=',4)->limit(1000)->find_all();
                    $tempname="优惠码";
                    break;
                default:
                    $orders = $order->find_all();
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
                //$count2 = ORM::factory('myb_score')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 2)->count_all();

                $count2 = ORM::factory('myb_qrcode')->where('bid', '=', $o->bid)->where('fopenid', '=', $o->user->openid)->count_all();
                $count3 = ORM::factory('myb_shscore')->where('bid', '=', $o->bid)->where('qid', '=', $o->qid)->where('type', '=', 3)->count_all();

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
        ))->render('weixin/myb/admin/pages');

        $result['orders'] = $order->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $this->template->title = '兑换记录';
        $this->template->content = View::factory('weixin/myb/admin/shorders')
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
            //$appId = ORM::factory('myb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appid')->find()->value;
            //$appSecret = ORM::factory('myb_cfg')->where('bid','=',$this->bid)->where('key','=','yz_appsecert')->find()->value;
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
        $config = ORM::factory('myb_cfg')->getCfg($bid);

        $result['items'] = ORM::factory('myb_item')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all();
        $iid = ORM::factory('myb_item')->where('bid', '=', $bid)->order_by('pri', 'DESC')->find_all()->as_array();
        //var_dump($iid);
        $convert = array();
        foreach ($iid as $key => $value) {
           //echo $value->id;
           $convert[$key] = ORM::factory('myb_shorder')->where('bid', '=', $bid)->where('iid','=',$value->id)->count_all();
           //echo $convert[$key].'<br>';
        }

        $this->template->title = '奖品管理';
        $this->template->content = View::factory('weixin/myb/admin/items')
            ->bind('result', $result)
            ->bind('convert',$convert)
            ->bind('config', $config);
    }

    public function action_items_add() {
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            if($result['errmsg']=='ok'){
                $i=0;
                foreach ($result['card_id_list'] as $card_id) {
                    $wxcards[$i]['id']=$card_id;
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
                        }
                        $title=$base_info['title'];
                        $wxcards[$i]['title']=$title;
                    }
                    $i++;
                }

            }
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
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==5){
                $_POST['data']['url']=$_POST['yzgift'];
            }
            $item = ORM::factory('myb_item');
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
                $key = "myb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('myba/items');
            }
        }

        $result['action'] = 'add';
        $result['present_id']=$present_id;
        $result['title1']=$title1;
        $result['title'] = $this->template->title = '添加新奖品';
        $this->template->content = View::factory('weixin/myb/admin/items_add')
            ->bind('wxcards', $wxcards)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_items_edit($id) {
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);
          require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            if($result['errmsg']=='ok'){
                $i=0;
                foreach ($result['card_id_list'] as $card_id) {
                    $wxcards[$i]['id']=$card_id;
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
                        }
                        $title=$base_info['title'];
                        $wxcards[$i]['title']=$title;
                    }
                    $i++;
                }

            }
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
        $item = ORM::factory('myb_item', $id);
        if (!$item || $item->bid != $bid) die('404 Not Found!');

        if ($_GET['DELETE'] == 1) {
            //有兑换记录的产品不能删除
            if (ORM::factory('myb_order')->where('iid', '=', $id)->count_all() == 0) {
                $item->delete();
                Request::instance()->redirect('myba/items');
            }
        }

        if ($_POST['data']) {
            if($_POST['data']['type']==1){
                $_POST['data']['url']=$_POST['wecoupons'];
            }elseif($_POST['data']['type']==5){
                $_POST['data']['url']=$_POST['yzgift'];
            }
            $item->values($_POST['data']);
            $item->bid = $bid;

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
                $key = "myb:items:{$this->bid}";
                $mem->delete($key);

                Request::instance()->redirect('myba/items');
            }
        }

        $_POST['data'] = $result['item'] = $item->as_array();
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改奖品';
        $this->template->content = View::factory('weixin/myb/admin/items_add')
             ->bind('wxcards', $wxcards)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['myba']['admin'] < 1) Request::instance()->redirect('myba/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('myb_login');
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
        ))->render('weixin/myb/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/myb/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_add() {
        if ($_SESSION['myba']['admin'] < 2) Request::instance()->redirect('myba/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('myb_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('myb_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('myba/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/myb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['myba']['admin'] < 2) Request::instance()->redirect('myba/home');

        $bid = $this->bid;

        $login = ORM::factory('myb_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('myb_cfg');

        if ($_GET['DELETE'] == 1) {
            //$login->delete();
            Request::instance()->redirect('myba/items');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('myb_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
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

                Request::instance()->redirect('myba/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];
        $_POST['data']['copyright'] = $cfgs['copyright'];
        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/myb/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/myb/tpl/login';
        $this->before();

        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('myb_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['myba']['bid'] = $biz->id;
                    $_SESSION['myba']['user'] = $_POST['username'];
                    $_SESSION['myba']['admin'] = $biz->admin; //超管
                    $_SESSION['myba']['config'] = ORM::factory('myb_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '宝塔镇河妖';
            }
        }

        if ($_SESSION['myba']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/myba/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['myba'] = null;
        header('location:/myba/home');
        exit;
    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "myb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }

     public function action_stats_totle($action='')
    {
         $daytype='%Y-%m-%d';
         $length=10;
         $status=1;
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
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from myb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' ")->execute()->as_array();
                $newadd[0]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from myb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')>='$begin' and FROM_UNIXTIME(`jointime`, '$daytype')<='$over' and ticket !=''")->execute()->as_array();
                $newadd[0]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from myb_trades where bid=$this->bid and left(pay_time,$length) >='$begin' and left(pay_time,$length) <='$over'")->execute()->as_array();
                $newadd[0]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[0]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[0]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from myb_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME('lastupdate','$daytype')<='$over' and tid !=0")->execute()->as_array();

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
            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `myb_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from myb_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/myb/admin/pages');

            $days=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `myb_qrcodes` where bid=$this->bid UNION select left(pay_time,$length) from myb_trades where bid=$this->bid ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from myb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time'")->execute()->as_array();
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];

                //产生海报数
                $ticket=DB::query(Database::SELECT,"select count(ticket) as tickets from myb_qrcodes where bid=$this->bid and FROM_UNIXTIME(`jointime`, '$daytype')='$time' and ticket !=''")->execute()->as_array();
                $newadd[$i]['tickets']=$ticket[0]['tickets'];

                //有赞订单数，有赞订单数、有赞商品交易数量、有赞成交金额
                $tradesdata=DB::query(Database::SELECT,"SELECT COUNT(id) AS tradesnum,SUM(NUM) as goodnum,SUM(payment) as payment from myb_trades where bid=$this->bid and left(pay_time,$length) LIKE '$time'")->execute()->as_array();
                $newadd[$i]['tradesnum']=$tradesdata[0]['tradesnum'];
                $newadd[$i]['goodsnum']=$tradesdata[0]['goodnum'];
                $newadd[$i]['payment']=$tradesdata[0]['payment'];

                //所有佣金 已结算的佣金、待结算的佣金
                $commision=DB::query(Database::SELECT,"SELECT SUM(score) AS paymoney from myb_scores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' and tid !=0")->execute()->as_array();
               // var_dump($commision);
                $newadd[$i]['commision']=$commision[0]['paymoney'];
            }
        }


        $duringdata=DB::query(Database::SELECT,"SELECT FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `myb_qrcodes` where bid=$this->bid UNION select left(pay_time,10) from myb_trades where bid=$this->bid ORDER BY `time` DESC ")->execute()->as_array();
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
        $this->template->content = View::factory('weixin/myb/admin/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }


    public function action_history_trades()
    {

        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);

        if ($_POST['action']) {
            $action = $_POST['action'];
            $id = $_POST['id'];
        }

        $result['status'] = 0;
        $result['sort'] = 'id';

        if ($action == 'done') {
            $result['status'] = 1;
        }

        $trade = ORM::factory('myb_trade')->where('bid', '=', $bid);
        $trade = $trade->reset(FALSE);


        if ($_GET['s']) {
            $trade = $trade->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $openids=DB::query(Database::SELECT,"select openid from myb_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/myb/admin/pages');

        $result['trades'] = $trade->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/myb/admin/history_trades')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }


    public function action_history_withdrawals()
    {
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid);
        $outmoney=ORM::factory('myb_score')->where('bid',"=",$bid)->where('score','<',0);
        $outmoney = $outmoney->reset(FALSE);

        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qid=DB::query(Database::SELECT,"select id from myb_qrcodes where nickname like '$s'  and bid=$this->bid")->execute()->as_array();

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
        ))->render('weixin/myb/admin/pages');

        $result['withdrawals'] = $outmoney->order_by($result['sort'], 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->content = View::factory('weixin/myb/admin/history_withdrawals')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
        }

    public function action_num()
    {

            //require_once Kohana::find_file('vendor', 'kdt/KdtApiClient');
            require_once Kohana::find_file("vendor/kdt","YZTokenClient");
            $tradeid=ORM::factory('myb_trade')->order_by('id','ASC')->find_all();
            $i=$j=1;
            foreach ($tradeid as $k)
             {  $i++;
                $goodd=ORM::factory('myb_order')->where('tid',"=",$k->tid)->find();
                if(!$goodd->id)
                {
                    $j++;
                    $tempbid=$k->bid;
                    $tempconfig = ORM::factory('myb_cfg')->getCfg($tempbid);
                    $this->access_token = ORM::factory('myb_login')->where('id','=',$tempbid)->find()->access_token;
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
                        $good=ORM::factory('myb_order')->where('goodid',"=",$result['response']['trade']['orders'][$j]['num_iid'])->where('tid',"=",$k->tid)->find();
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
            $bid=ORM::factory('myb_trade')->where('tid','=',$tid)->find()->bid;

            $this->access_token = ORM::factory('myb_login')->where('id','=',$bid)->find()->access_token;
            $tempbid=$bid;
            $tempconfig = ORM::factory('myb_cfg')->getCfg($tempbid);

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
        //$goods=ORM::factory('myb_order')->where('bid','=',$this->bid)->find_all();
        $or = 'id';
        if ($_GET['sort']) $or = $_GET['sort'];


        //$goods=DB::query(database::SELECT,"SELECT DISTINCT goodid,title, sum(num) AS tonum,count(id) as totle,sum(price) as toprice  FROM `myb_orders` WHERE bid=$this->bid group BY goodid order by $or DESC")->execute()->as_array();
        $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT myb_orders.* FROM `myb_trades`,myb_orders WHERE myb_orders.tid=myb_trades.tid and myb_trades.status!='TRADE_CLOSED' and myb_trades.status!='TRADE_CLOSED_BY_USER' and myb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT myb_orders.* FROM `myb_trades`,myb_orders WHERE myb_orders.tid=myb_trades.tid and myb_trades.status!='TRADE_CLOSED' and myb_trades.status!='TRADE_CLOSED_BY_USER' and myb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc ")->execute()->as_array();

         }

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => count($goods),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/myb/admin/pages');


        if ($_GET['s']) {
            $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT myb_orders.* FROM `myb_trades`,myb_orders WHERE myb_orders.tid=myb_trades.tid and myb_trades.status!='TRADE_CLOSED' and myb_trades.status!='TRADE_CLOSED_BY_USER' and myb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid and temp.title like '$s' GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }
         else
         {
             $goods=DB::query(database::SELECT,"select DISTINCT (temp.goodid) as goodid,temp.title,sum(temp.price)as toprice,sum(temp.num)as tonum,count(temp.id)as totle from (SELECT myb_orders.* FROM `myb_trades`,myb_orders WHERE myb_orders.tid=myb_trades.tid and myb_trades.status!='TRADE_CLOSED' and myb_trades.status!='TRADE_CLOSED_BY_USER' and myb_trades.status!='NO_REFUND') as temp where temp.bid=$this->bid GROUP by temp.goodid ORDER by $or desc limit $this->pagesize offset $offset")->execute()->as_array();
         }

        $this->template->content = View::factory('weixin/myb/admin/stats_goods')
        ->bind('goods',$goods)
        ->bind('pages', $pages)
        ->bind('result',$result)
        ->bind('or',$or);

    }
    public function action_hb_check() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        //$this->access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;

        if ($_POST['hb_check']){
            $hb_check = $_POST['hb_check'];
            $cfg = ORM::factory('myb_cfg');
            foreach ($hb_check as $k => $v) {
                $ok=$cfg->setCfg($bid,$k,$v);
                $result['ok8']+=$ok;
            }
            $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        }

        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/myb/admin/hb_check')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_setgoods()
    {
        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        //require_once kohana::find_file('vendor',"kdt/KdtApiClient");
        require_once Kohana::find_file("vendor","kdt/YZTokenClient");
        $this->access_token = ORM::factory('myb_login')->where('id','=',$bid)->find()->access_token;
        $tempconfig=ORM::factory('myb_cfg')->getCfg($this->bid);
        if($this->access_token)
        {
            $page = max($_GET['page'], 1);

            $client = new YZTokenClient($this->access_token);
            $method = 'kdt.items.onsale.get';
            $params = array(
                 'page_size'=>20,
                 'page_no'=>$page,
                'fields' => 'num_iid,title,price,pic_url,num,sold_num,detail_url',
            );


                //修改佣金
            if ($_POST['form']['num_iid']) {
                // echo '<pre>';
                // var_dump($_POST['form']);
                // echo '</pre>';
                // exit;
                $goodid = $_POST['form']['num_iid'];
                $setgoods = ORM::factory('myb_setgood')->where('bid', '=', $bid)->where('goodid','=',$goodid)->find();

                    $setgoods->money0=$_POST['form']['money0'];
                    $setgoods->money1=$_POST['form']['money1'];
                    $setgoods->money2=$_POST['form']['money2'];
                    if($tempconfig['kaiguan_needpay']==1){
                        $setgoods->money3=$_POST['form']['money3'];
                    }
                    $setgoods->goodid=$_POST['form']['num_iid'];
                    $setgoods->bid=$bid;
                    if(isset($_POST['form']['status'])){
                        $setgoods->status=$_POST['form']['status'];
                    }
                    $setgoods->title=$_POST['form']['title'];
                    $setgoods->pic=$_POST['form']['pic'];
                    $setgoods->price=$_POST['form']['price'];
                    $setgoods->url=$_POST['form']['url'];
                    $setgoods->save();


            }
             $result = $client->post($method, '1.0.0', $params, $files);

              $pages = Pagination::factory(array(
                'total_items'   =>$result['response']['total_results'],
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/myb/admin/pages');
      }
      else
        $result['response']=array();

    $this->template->content=View::factory('weixin//myb/admin/setgoods')
    ->bind('result',$result['response'])
    ->bind('pages',$pages)
    ->bind('bid',$this->bid);

     }
     public function action_rsync() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
        if ($_POST['rsync']['switch']==1){
            if($this->access_token){
                $rsync = $_POST['rsync'];
                $cfg = ORM::factory('myb_cfg');
                foreach ($rsync as $k=>$v) {
                    $ok = $cfg->setCfg($bid, $k, $v);
                    $result['ok7'] += $ok;
                }
            }else{
                $result['error7']=7;
            }
            $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/myb/admin/rsync')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    public function action_empty() {
        if ($_GET['DELETE'] == 1) {
            $empty = ORM::factory('myb_shscore')->where('bid', '=', $this->bid);
            $empty->delete_all();
            DB::update(ORM::factory('myb_qrcode')->table_name())
            ->set(array('shscore' => '0','yz_score' =>'0'))
            ->where('bid', '=', $this->bid)
            ->execute();
            $this->config = ORM::factory('myb_cfg')->getCfg($this->bid, 1);
            if($this->config['switch']==1){
                $this->access_token=ORM::factory('myb_login')->where('id', '=', $this->bid)->find()->access_token;
                require_once Kohana::find_file("vendor/kdt","YZTokenClient");
                $client = new YZTokenClient($this->access_token);
                $userarr = ORM::factory('myb_qrcode')->where('bid','=',$this->bid)->find_all();
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
                    $method = 'youzan.crm.customer.points.decrease';
                    $params =[
                    'fans_id' => $user_id,
                    'points' => $point,
                    ];
                    $a=$client->post($method, $this->methodVersion, $params, $files);

                }
            }
            Request::instance()->redirect('myba/zero');
        }
    }
     public function action_zero() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $bid = $this->bid;
        $config = ORM::factory('myb_cfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('myb_login')->where('id', '=', $bid)->find()->access_token;
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/myb/admin/zero')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('bid',$bid);
    }
    private function rsync($bid,$openid,$access_token,$chscore){
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($access_token){
            $client = new YZTokenClient($access_token);
        }else{
            die('请在后台一键授权给有赞');
        }
        $qrcode=ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        $method = 'youzan.users.weixin.follower.get';
        $params =[
        'weixin_openid'=>$openid,
        ];
        $result=$client->post($method, $this->methodVersion, $params, $files);
        $fans_id = $result['response']['user']['user_id'];
        if($qrcode->yz_score==0){
            $method = 'youzan.crm.customer.points.increase';
            $params =[
            'fans_id' => $fans_id,
            'points' => $qrcode->shscore,
            ];
            $a=$client->post($method, $this->methodVersion, $params, $files);
            $qrcode->yz_score=1;
            $qrcode->save();
            $qrcode=ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        }else{
            if($chscore>=0){
                $method = 'youzan.crm.customer.points.increase';
                $params =[
                'fans_id' => $fans_id,
                'points' => $chscore,
                ];
                $a=$client->post($method, $this->methodVersion, $params, $files);
            }else{
                $method = 'youzan.crm.customer.points.decrease';
                $params =[
                'fans_id' => $fans_id,
                'points' => -$chscore,
                ];
                $a=$client->post($method, $this->methodVersion, $params, $files);
            }
        }
        $method = 'youzan.crm.fans.points.get';
        $params =[
        'fans_id' => $fans_id,
        ];
        $results=$client->post($method, $this->methodVersion, $params, $files);
        $point = $results['response']['point'];
        if($point&&$point!=$qrcode->shscore){
            $score_change=$point-$qrcode->shscore;
            $qrcode->scores->scoreIn($qrcode,6,$score_change);
        }
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


    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."myb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."myb/tmp/$bid/key.{$config['appsecret']}.pem";
        $rootca_file=DOCROOT."myb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'myb_file_cert')->find();
        $file_key = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'myb_file_key')->find();
        $file_rootca = ORM::factory('myb_cfg')->where('bid', '=', $bid)->where('key', '=', 'myb_file_rootca')->find();

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

        // Kohana::$log->add("weixin_myb:$bid:curl_post_ssl:cert_file", $cert_file);

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
