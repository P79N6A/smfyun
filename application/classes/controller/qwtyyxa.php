<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtyyxa extends Controller_Base {

    public $template = 'weixin/qwt/tpl/yyxatpl';
    public $pagesize = 20;
    public $access_token;
    public $tb_access_token;
    public $jd_access_token;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',16)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            // die('未购买相关产品或产品已过期');
            if(Request::instance()->action == 'information'){
                // echo "<script>alert('未购买相关产品或产品已过期')</script>";
                $hasover = 1;
                @View::bind_global('hasover', $hasover);
            }
        }

    }
    public function after() {
        if ($this->bid) {
            // $todo['hasbuy'] = ORM::factory('qwt_buy')->where('bid', '=', $this->bid)->where('expiretime', '>=', time())->find_all();
            $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->find_all();
            $this->template->todo = $todo;
        }
        @View::bind_global('bid', $this->bid);
        @View::bind_global('todo', $todo);

        parent::after();
    }
    public function action_index() {
        $this->action_login();
    }
    public function action_otag(){
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        $client = new YZTokenClient($this->access_token);

        $tag_name =  ORM::factory('qwt_yyxcfg')->where('bid','=',$this->bid)->where('key','=','tag_name')->find()->value;
        $sql = DB::query(Database::SELECT,"SELECT openid as OP FROM qwt_yyxqrcodes where (`bid`=$this->bid and `ticket`!= 'NULL') or (`bid`=$this->bid and `fopenid`!= 'NULL')");
        $openid = $sql->execute()->as_array();
        set_time_limit(0);
        for($p=0;$openid[$p];$p++){
            $method = 'youzan.users.weixin.follower.tags.add';
            $params = [
            'tags' =>$tag_name,
            'weixin_openid' =>$openid[$p]['OP'],
            ];
            $test=$client->post($method, $this->methodVersion, $params, $files);
        }
    }
    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid, 1);
        $this->access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        //文案配置
        if ($_POST['text']) {
            $cfg = ORM::factory('qwt_yyxcfg');
            foreach ($_POST['text'] as $k=>$v) {
                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok2'] += $ok;
            }
            //重新读取配置
            $config = ORM::factory('qwt_yyxcfg')->getCfg($bid, 1);
        }
        if($_POST['order']){
            $orders=$_POST['order'];
            $order=ORM::factory('qwt_yyxorder');
            $order->bid=$bid;
            $order->qid=0;
            $order->iid=0;
            $order->tid='order';
            $order->num=1;
            $order->title=$orders['name'];
            $order->buyer_nick='无名氏';
            $order->price=$orders['money'];
            $order->payment=$orders['money'];
            $order->receiver_state=$orders['area'];
            $order->created=strtotime($orders['time']);
            $order->update_time=strtotime($orders['time']);
            $order->pay_time=strtotime($orders['time']);
            $order->save();
        }
        $shops = ORM::factory('qwt_yyxshop')->where('bid', '=', $bid)->find_all();
        $buser = ORM::factory('qwt_login')->where('id', '=', $bid)->find();
        $result['access_token'] = $buser->yzaccess_token;
        // $result['tb_access_token'] = $buser->tb_access_token;
        $this->template->title = '首页';
        $this->template->name = $buser->name;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/home')
            ->bind('result', $result)
            ->bind('config', $config)
            ->bind('shops', $shops)
            ->bind('bid',$bid);
    }
    //有赞授权刷新脚本 七天一次
    public function action_oauthscript($bid=39){
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $url="https://open.youzan.com/oauth/token";
        if($shop->access_token&&$shop->id){
            $data=array(
                "client_id"=>"fdc4425fff26d518af",
                "client_secret"=>"75d7e0d4b2a2836c26e2edaf35faed9c",
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
        $type = 'callback';
        if($_GET['type']=='add'){
            $type = 'add'.$type;
        }
        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=fdc4425fff26d518af&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/yyxa/'.$type);
    }
    public function action_tb_oauth(){

        Request::instance()->redirect('https://oauth.tbsandbox.com/authorize?response_type=code&client_id=23075594&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/yyxa/tb_callback&state=1212&view=web');
    }
    public function action_tb_callback(){
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $url = 'https://oauth.taobao.com/token';
        $postfields= array('grant_type'=>'authorization_code',
        'client_id'=>'test',
        'client_secret'=>'test',
        'code'=>$code,
        "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/yyxa/tb_callback');
        $post_data = '';

        foreach($postfields as $key=>$value){
        $post_data .="$key=".urlencode($value)."&";}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);

        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post_data,0,-1));
        $output = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $httpStatusCode;
        curl_close($ch);
        var_dump($output);
        $result=json_decode($output);
        if(isset($result->access_token))
        {
            //var_dump($value);
            $usershop = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            $usershop->tb_access_token = $result->access_token;
            $usershop->tb_expires_in = time()+$result->expires_in;
            $usershop->tb_refresh_token = $result->refresh_token;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("yyxa/home")."';</script>";
        }
    }
    //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"fdc4425fff26d518af",
            "client_secret"=>"75d7e0d4b2a2836c26e2edaf35faed9c",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/yyxa/callback'
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

            $usershop = ORM::factory('qwt_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("yyxa/home")."';</script>";
        }
        //Request::instance()->redirect('yyxa/home');
    }
    public function action_addcallback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"fdc4425fff26d518af",
            "client_secret"=>"75d7e0d4b2a2836c26e2edaf35faed9c",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/yyxa/addcallback'
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

            $usershop = ORM::factory('qwt_yyxshop')->where('shopid','=',$sid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->name = $value['name'];
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->bid = $this->bid;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("yyxa/home")."';</script>";
        }
        //Request::instance()->redirect('yyxa/home');
    }
    //用户管理
    public function action_qrcodes($action='', $id=0) {
        $bid=$this->bid;
        $this->access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid);
        //修改用户
        $qrcode = ORM::factory('qwt_yyxqrcode')->where('bid', '=', $bid);
        $qrcode = $qrcode->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where('nick', 'like', $s); //->or_where('openid', 'like', $s);
        }
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/yyx/pages');
        $result['qrcodes'] = $qrcode->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '热销商品排行';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_items(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid);

        $item =ORM::factory('qwt_yyxorder')->where('bid', '=', $bid)->where('tid','=','order');
        $item = $item->reset(FALSE);
        $num =$item->count_all();

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/yyx/pages');
        $result['item'] = $item->order_by('lastupdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '大客户订单';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/item')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_his_rsync(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid);

        $this->template->title = '历史订单同步';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/hb_check')
            ->bind('config', $config);
    }
    public function action_pull(){
        $bid = $this->bid;

        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid,1);
        $buy = ORM::factory('qwt_buy')->where('iid','=',16)->where('bid','=',$bid)->find();
        if ($buy->dpm_order==0) {
            if ($config['start_time']) {
                //拉过了
                $status = 1;
                Request::instance()->redirect('qwtdpm/map');
                exit;
            }else{
                //没拉过
                $status = 0;
            }
        }else{
            if (time()<$config['start_time']) {
                //拉过了
                $status = 1;
                Request::instance()->redirect('qwtdpm/map');
                exit;
            }
            //正在拉
            $status = 2;
            $time = ((time()-$config['start_time'])/604800)*5+5;
            $time = number_format($time,0);
            if ($time<5) {
                $time = 5;
            }
        }
        if ($_POST['pull']) {
            $time2 = strtotime("-3 months");
            $ok=ORM::factory('qwt_yyxcfg')->setCfg($this->bid,'start_time',$time2);
            $buy2 = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',16)->find();
            $buy2->dpm_order = 1;
            $buy2->save();
            Request::instance()->redirect('qwtyyxa/home');
            exit;
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/pull')
            ->bind('status',$status)
            ->bind('time',$time);
    }
    public function action_item_add(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid);
        if($_POST['order']){
            $orders=$_POST['order'];
            $order=ORM::factory('qwt_yyxorder');
            $order->bid=$bid;
            $order->qid=0;
            $order->iid=0;
            $order->tid='order';
            $order->num=1;
            $order->title=$orders['name'];
            $order->buyer_nick='无名氏';
            $order->price=$orders['money'];
            $order->payment=$orders['money'];
            $order->receiver_state=$orders['pro'];
            $order->receiver_city=$orders['city'];
            $order->receiver_district=$orders['dis'];
            $order->created=strtotime($orders['time']);
            $order->update_time=strtotime($orders['time']);
            $order->pay_time=strtotime($orders['time']);
            if(!$orders['name']||!$orders['city']||!$orders['time']){
                $result['err3'] = '输入信息不全！';
            }else{
                $order->save();
                $result['ok3'] = 1;
            }
        }
        $this->template->title = '添加新订单';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/item_add')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_item_detele($id){
        $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_yyxorders` where `bid` = $this->bid and `id` = $id");
        $sql->execute();

        Request::instance()->redirect('yyxa/items');
    }
    public function action_stats_totle($action=''){
        $daytype='%Y-%m-%d';
        $length=10;
        $status=1;
        $this->config=ORM::factory('qwt_yyxcfg')->getCfg($this->bid,1);
        if($this->config['totle']!=date('Y-m-d',time())){
            if($this->config['totle']){
                $time_totle=strtotime($this->config['totle']);
            }else{
                $time_totle=0;
            }
            $daytype='%Y-%m-%d';
            $length=10;
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`pay_time`, '$daytype')as time FROM `qwt_yyxorders` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                $yzs=DB::query(Database::SELECT,"select sum(payment) as yz_sold from qwt_yyxorders where bid=$this->bid  and FROM_UNIXTIME(`pay_time`, '$daytype')='$time' and `tid` !='order'")->execute()->as_array();
                $newadd[$i]['yz_sold']=$yzs[0]['yz_sold'];
                //新增用户
                $lrs=DB::query(Database::SELECT,"select sum(payment) as lr_sold from qwt_yyxorders where bid=$this->bid and FROM_UNIXTIME(`pay_time`, '$daytype')='$time' and `tid` ='order'")->execute()->as_array();
                $newadd[$i]['lr_sold']=$lrs[0]['lr_sold'];
            }
            foreach ($newadd as $key => $value) {
                $totle=ORM::factory('qwt_yyxtotle')->where('bid','=',$this->bid)->where('time_quantum','=',$value['time'])->find();
                $totle->bid=$this->bid;
                $totle->time_quantum=$value['time'];
                $totle->timestamp=strtotime($value['time']);
                $totle->yz_sold=$value['yz_sold'];
                $totle->lr_sold=$value['lr_sold'];
                $totle->save();

            }
            $ok=ORM::factory('qwt_yyxcfg')->setCfg($this->bid,'totle',date('Y-m-d',time()));
            $this->config=ORM::factory('qwt_yyxcfg')->getCfg($this->bid,1);
        }else{
            $time_today=date('Y-m-d',time());
            // echo $this->bid."<br>";
            // echo $daytype."<br>";
            // echo $time_today."<br>";
            $yzs=DB::query(Database::SELECT,"SELECT sum(payment) as yz_sold from qwt_yyxorders where bid=$this->bid  and FROM_UNIXTIME(`pay_time`, '$daytype')='$time_today' and `tid` !='order'")->execute()->as_array();
            $yz_sold=$yzs[0]['yz_sold'];
            // echo $yz_sold."<br>";
            // exit;
                //新增用户
            $lrs=DB::query(Database::SELECT,"select sum(payment) as lr_sold from qwt_yyxorders where bid=$this->bid and FROM_UNIXTIME(`pay_time`, '$daytype')='$time_today' and `tid` ='order'")->execute()->as_array();
            $lr_sold=$lrs[0]['lr_sold'];
            if($yz_sold>0||$lr_sold>0){
                $totle=ORM::factory('qwt_yyxtotle')->where('bid','=',$this->bid)->where('time_quantum','=',date('Y-m-d',time()))->find();
                $totle->bid=$this->bid;
                $totle->time_quantum=date('Y-m-d',time());
                $totle->timestamp=strtotime(date('Y-m-d',time()));
                $totle->yz_sold=$yz_sold;
                $totle->lr_sold=$lr_sold;
                $totle->save();
            }
        }
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
               if(strtotime($begin)==strtotime($over)){
                 $newadd[0]['time']=$begin;
               }
               else{
                $newadd[0]['time']=$begin.'~'.$over;
               }

                // //新增用户
                // $alls=DB::query(Database::SELECT,"select sum(payment) as all_sold from qwt_yyxorders where bid=$this->bid  and FROM_UNIXTIME(`pay_time`, '$daytype')>='$begin' and FROM_UNIXTIME(`pay_time`, '$daytype')<='$over' ")->execute()->as_array();
                // $newadd[0]['all_sold']=$alls[0]['all_sold'];


                $yzs=DB::query(Database::SELECT,"select sum(payment) as yz_sold from qwt_yyxorders where bid=$this->bid  and FROM_UNIXTIME(`pay_time`, '$daytype')>='$begin' and FROM_UNIXTIME(`pay_time`, '$daytype')<='$over' and `tid`!='order' ")->execute()->as_array();
                $newadd[0]['yz_sold']=$yzs[0]['yz_sold'];

                $lrs=DB::query(Database::SELECT,"select sum(payment) as lr_sold from qwt_yyxorders where bid=$this->bid  and FROM_UNIXTIME(`pay_time`, '$daytype')>='$begin' and FROM_UNIXTIME(`pay_time`, '$daytype')<='$over' and `tid` ='order' ")->execute()->as_array();
                $newadd[0]['lr_sold']=$lrs[0]['lr_sold'];



            }
        }
        else{

            if($_GET['qid']==2||$action=='month'){
                $daytype='%Y-%m';
                $length=7;
                $status=2;
            }
            $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_yyxtotles` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();

            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qwt/admin/yyx/pages');

             $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`timestamp`, '$daytype')as time FROM `qwt_yyxtotles` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            $newadd=array();
            for($i=0;$days[$i];$i++){
                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                //新增用户
                $yzs=DB::query(Database::SELECT,"select sum(yz_sold) as yz_sold from qwt_yyxtotles where bid=$this->bid  and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['yz_sold']=$yzs[0]['yz_sold'];
                //新增用户
                $lrs=DB::query(Database::SELECT,"select sum(lr_sold) as lr_sold from qwt_yyxtotles where bid=$this->bid and FROM_UNIXTIME(`timestamp`, '$daytype')='$time' ")->execute()->as_array();
                $newadd[$i]['lr_sold']=$lrs[0]['lr_sold'];
            }
        }
        $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`update_time`, '%Y-%m-%d')as time FROM `qwt_yyxorders` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
        $num=count($duringdata);
        if(strtotime($duringdata[0]['time'])<strtotime($duringdata[$num-1]['time'])){
        $duringtime['begin']=$duringdata[0]['time'];
        echo $duringtime['begin']."pppp";
        $duringtime['over']=$duringdata[$num-1]['time'];
        }
        else{
        $duringtime['begin']=$duringdata[$num-1]['time'];
        $duringtime['over']=$duringdata[0]['time'];
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/stats_totle')
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }
    public function action_item_rank(){
        $bid=$this->bid;
        $this->access_token=ORM::factory('qwt_login')->where('id', '=', $bid)->find()->yzaccess_token;
        $config = ORM::factory('qwt_yyxcfg')->getCfg($bid);
        //修改用户
        $item = ORM::factory('qwt_yyxitem')->where('bid', '=', $bid);
        $item = $item->reset(FALSE);
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $item = $item->where('title', 'like', $s); //->or_where('openid', 'like', $s);
        }
        $result['countall'] = $countall = $item->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/yyx/pages');
        if($_POST['rank']){
            foreach ($_POST['rank'] as $k => $v) {
                // echo $k.'k<br>';
                // echo $v.'v<br>';
                $rank_item = ORM::factory('qwt_yyxitem')->where('bid','=',$bid)->where('id', '=', $k)->find();
                $rank_item->lv = $v;
                $rank_item->save();
            }
        }
        $result['items'] = $item->order_by('lv','DESC')->order_by('sold_num','DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '用户明细';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/yyx/item_rank')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_logout() {
        $_SESSION['yyxa'] = null;
        header('location:/yyxa/home');
        exit;
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_yyx$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
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
