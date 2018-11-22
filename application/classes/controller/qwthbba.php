<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwthbba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/hbbatpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',1)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
        $config = ORM::factory('qwt_hbbcfg')->getCfg($bid, 1);
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
            $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            //$user->access_token = $access_token;
            $user->refresh_token = substr($refresh_token,15);//截取 refreshtoken
            $user->appid = $appid;
            $user->expires_in = $expires_in;
            $user->auth_info = $auth_info;
            $user->save();
            $cachename1 ='hbb.access_token'.$this->bid;
            $mem->set($cachename1, $access_token, 5400);//有效期两小时
        }
        //文案配置
        if ($_POST['cus']) {
            $cfg = ORM::factory('qwt_hbbcfg');

            foreach ($_POST['cus'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, $v);
                $result['ok2'] += $ok;
            }
            $config = ORM::factory('qwt_hbbcfg')->getCfg($bid, 1);
        }

        //红包
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        // $lastupdate=ORM::factory('qwt_hbbkl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',1)->find()->lastupdate;//rebuy_time是时间戳
        $hb_cron = ORM::factory('qwt_hbbcron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
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

        $result['cron'] = ORM::factory('qwt_hbbcron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
        $user = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hbb/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('user', $user)
            ->bind('oauth', $oauth)
            ->bind('left',$left)
            ->bind('pre_auth_code',$pre_auth_code)
            ->bind('bid',$bid);
    }
    public function action_download(){
        $file = DOCROOT.'../application/vendor/code/hongbao.zip';
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
        exit;
    }

    public function action_getdata(){
        $bid=$this->bid;
        $result=array();
        $buycodenum=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',1)->find()->hbnum;//购买总数
        $creatcodenum = ORM::factory('qwt_hbbkl')->where('bid', '=', $bid)->count_all();//产生的口令总数
        $normalkoulin=ORM::factory('qwt_hbbkl')->where('bid','=',$bid)->where('split','=',0)->count_all();//普通口令数
        $liebiankoulin=ORM::factory('qwt_hbbkl')->where('bid','=',$bid)->where('split','>',0)->count_all();//裂变口令数

        $normalkoulinused=ORM::factory('qwt_hbbkl')->where('bid','=',$bid)->where('split','=',0)->where('used','>',0)->count_all();//普通已使用的口令数
        $liebiankoulinused=ORM::factory('qwt_hbbkl')->where('bid','=',$bid)->where('split','>',0)->where('used','>',0)->count_all();//裂变已使用的口令数

        $usedcodenum=ORM::factory('qwt_hbbkl')->where('used', '>', 0)->where('bid', '=', $bid)->count_all();

        if($creatcodenum<=0){
            //echo '0';
        }
        else
        {

            //echo json_encode($result);
        }
        $result['used']['total']=$usedcodenum;
        $result['used']['liebian']=$liebiankoulinused;
        $result['used']['normal']=$normalkoulinused;
        $result['buynum']=$buycodenum;
        $result['creatnum']['total']=$creatcodenum;
        $result['creatnum']['liebian']=$liebiankoulin;
        $result['creatnum']['normal']=$normalkoulin;

        $this->template->title = '概况';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hbb/getdata')
            ->bind('result', $result)
            ->bind('config', $this->config);
    }
    public function action_download_csv(){
        set_time_limit(0);
        $bid = $this->bid;
        $hb_cron = ORM::factory('qwt_hbbcron')->where('bid','=',$bid)->order_by('id','DESC')->find();
        $csv = ORM::factory('qwt_hbbkl')->where('bid','=',$bid)->where('lastupdate','=',$hb_cron->time)->find_all();
        $file = "/tmp/$bid.$hb_cron->num.csv";
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
        $hb_cron->has_down = 1;
        $hb_cron->save();
        @unlink($file);
        exit;
    }
    public function action_pre_generate(){
        $buynum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',1)->find()->hbnum;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('qwt_hbbkl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',1)->find()->lastupdate;
        // $hb_cron = ORM::factory('qwt_hbbcron')->where('bid', '=', $this->bid)->where('state','=',0)->find();
        // if(empty($lastupdate)||$buytimenew>$lastupdate||!$hb_cron->id)
        $hb_cron = ORM::factory('qwt_hbbcron')->where('bid', '=', $this->bid)->order_by('id','desc')->find();
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
                Request::instance()->redirect('/qwthbba/home');
           }

            if($flag==1)
            {
              $hbbcron = ORM::factory('qwt_hbbcron');
              $hbbcron->bid = $this->bid;
              $hbbcron->time = time();
              $hbbcron->state = 0;
              $hbbcron->num = $buynum;
              $hbbcron->save();
             //直接退出
             Request::instance()->redirect('/qwthbba/home');
             //echo "<script>history.go(-1);</script>";产生的csv会有参杂html代码
            }
    }
    public function action_generate_cron(){
        $users = ORM::factory('qwt_hbbcron')->where('state','=',0)->find_all();
        require Kohana::find_file("vendor/code","QwtCommonHelper");
        foreach ($users as $k => $v) {
            $buynum = $v->num;
            Helper::GenerateCode($v->time,$v->bid,$buynum);
            $v->state = 1;
            $v->save();
        }
        exit;
    }
    public function action_generate(){//生成口令
        set_time_limit(0);
        require Kohana::find_file("vendor/code","QwtCommonHelper");
        $buynum = ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',1)->find()->hbnum;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('qwt_hbbkl')->where('bid', '=', $this->bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',1)->find()->lastupdate;

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
                Request::instance()->redirect('/qwthbba/home');
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
        $config = ORM::factory('qwt_hbbcfg')->getCfg($bid);
        $order = ORM::factory('qwt_hbbweixin')->where('bid', '=', $bid)->where('kouling', '>', 0);
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
        ))->render('weixin/qwt/admin/hbb/pages');

        $result['orders'] = $order->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '红包记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/hbb/qrcode')
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
