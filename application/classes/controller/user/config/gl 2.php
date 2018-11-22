<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Config_Gl extends Controller_User_Home {

    public static $file="";
    public static $tempname="";
    public $pagesize = 5;
    public $buy_id ;
    public $name ;
    public $access_token;
    public $methodVersion='3.0.0';
    public function before()
    {
        parent::before();
        require Kohana::find_file("vendor/code","CommonHelper");
        if (Request::instance()->action == 'see') return;
        $sid=Session::instance()->get('user')['sid'];
        $userid=ORM::factory('user')->where('user_shopid','=',$sid)->find()->user_id;
        $this->buy_id=Request::instance()->param("id");
        $this->name = Session::instance()->get("sname");
        $this->access_token = ORM::factory('config')->where('buy_id', '=', $this->buy_id)->find()->access_token;
        // if($this->buy_id!=138){
        //     die('盖楼插件更新中');
        // }
        //$buy=ORM::factory("buy",array("buy_id"=>self::$buy_id,'user_id'=>$this->userid));
        $buy=ORM::factory('buy')->where('buy_id','=',$this->buy_id)->where('user_id','=',$userid)->find();
        if(!$buy->loaded())
        {
            echo "此链接非法！！";
            exit();
        }
    }
    public function action_index(){
        $view=View::factory("user/config/gl/index");

        $url="gl.smfyun.com/api/weixin4/".$this->name;
        $buy=ORM::factory("buy",array("buy_id"=>$this->buy_id));
        $this->template->product_buy_id=$this->buy_id;
        $overtime=date("Y-m-d",strtotime("$buy->expiretime"));
        $is_config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find()->buy_id;
        if(!$is_config) {
            $config=ORM::factory("config");
            $config->buy_id=$this->buy_id;
            $config->save();
        }
        if(isset($_POST['youzan'])){
            $config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find();
            $youzan = $_POST['youzan'];
            $config->youzan_appid=$youzan["appid"];
            $config->youzan_appsecret=$youzan["appsecret"];
            $result=$config->save();
            if($result) $success='yz';
        }

        if(!$this->access_token){
            $oauth=1;
        }
        $config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find()->as_array();
        $view->set("config",$config)->set("url",$url)->set("scripts",array("Resource/js/rebuy.js"))->set("overtime",$overtime)->set("success",$success)->set("oauth",$oauth);
        $this->template->content=$view;
    }
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=869ce4fd8bc4840113&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/user/config/gl/callback/'.$this->buy_id);
    }
    //回调获取 商户信息
    public function action_callback($buy_id){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"869ce4fd8bc4840113",
            "client_secret"=>"75039c6f954050379ec60f5230057321",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/user/config/gl/callback/'.$buy_id
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
            require Kohana::find_file('vendor', 'oauth/YZTokenClient');
            $oauth=new YZTokenClient($result->access_token);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            $sid = $value['id'];
            $name = $value['name'];

            $gl = ORM::factory('config')->where('buy_id', '=', $buy_id)->find();
            $gl->access_token = $result->access_token;
            $gl->kind = 'gl';
            $gl->refresh_token = $result->refresh_token;
            $gl->save();
            echo "<script>alert('授权成功');location.href='".URL::site('user/config/gl/index/'.$buy_id)."';</script>";
        }
        //Request::instance()->redirect('wdya/home');
    }
    public function action_text(){
        $view=View::factory("user/config/gl/text");
        $this->template->product_buy_id=$this->buy_id;
        if(isset($_POST['text'])){
            $config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find();
            $text = $_POST['text'];
            $config->keyword=$text["keyword"];
            $config->fword=$text["fword"];
            $config->times=$text["times"];
            $result=$config->save();
            if($result) $success='text';
        }
        $config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find()->as_array();
        $view->set("config",$config)->set("success",$success);
        $this->template->content=$view;
    }
    public function action_wx(){
        $view=View::factory("user/config/gl/wx");
        $this->template->product_buy_id=$this->buy_id;
        $dir=Kohana::include_paths()[0].'vendor/weixin/glcert/'.$this->name;
        if(is_dir($dir)){
            $cert_name=$this->name.".zip";
        }
        else{
            $cert_name=null;
        }
        if(isset($_POST['wx'])||isset($_POST['filecert'])){
            $config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find();
            $wx = $_POST['wx'];
            $config->nick_name=$wx['nickname'];
            $config->appid=$wx['appid'];
            $config->appsecret=$wx['appsecret'];
            $config->partnerid=$wx['partnerid'];
            $config->partnerkey=$wx['partnerkey'];
            $result=$config->save();
            if($result) $success['ok']='wx';
            if($this->Upload($this->name)) $success['ok']='file';
        }
        $config=ORM::factory("config")->where('buy_id','=',$this->buy_id)->find()->as_array();
        $view->set("config",$config)->set("success",$success)->set("cert_name",$cert_name);
        $this->template->content=$view;
    }
    public function action_item(){
        $view=View::factory("user/config/gl/item");
        $this->template->product_buy_id=$this->buy_id;
        $config['buy_id']=$this->buy_id;
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $num = ORM::factory('glitem')->where('buyid','=',$this->buy_id)->count_all();
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('tpl/pages');

        $item = ORM::factory('glitem')->where('buyid','=',$this->buy_id)->limit($this->pagesize)->order_by('lastupdate','DESC')->offset($offset)->find_all()->as_array();
        $view->set("config",$config)->set("item",$item)->set("pages",$pages);
        $this->template->content=$view;
    }
    public function action_order(){
        $view=View::factory("user/config/gl/order");
        $this->template->product_buy_id=$this->buy_id;
        $config['buy_id']=$this->buy_id;
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $num = ORM::factory('glorder')->where('buyid','=',$this->buy_id)->count_all();
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('tpl/pages');

        $item = ORM::factory('glorder')->where('buyid','=',$this->buy_id)->limit($this->pagesize)->order_by('lastupdate','DESC')->offset($offset)->find_all()->as_array();
        $view->set("config",$config)->set("item",$item)->set("pages",$pages);
        $this->template->content=$view;
    }
    public function action_see($bid){
        $mem = Cache::instance('memcache');
        $lou_key = "weixin4:$bid:gl_count";
        $lou_count = (int)$mem->get($lou_key);
        echo $lou_count;
    }
    public function action_delete(){
        $view=View::factory("user/config/gl/delete");
        $this->template->product_buy_id=$this->buy_id;
        $config['buy_id']=$this->buy_id;
        if(isset($_POST['delete'])){
            $userid = ORM::factory('buy')->where('buy_id','=',$this->buy_id)->find()->user_id;
            $mem = Cache::instance('memcache');
            $bid = ORM::factory('user')->where('user_id','=',$userid)->find()->vip;
            $lou_key = "weixin4:$bid:gl_count";
            $result = $mem->set($lou_key, 0, 0);
            if($result){
               $success='delete';
            }
        }
        $mem = Cache::instance('memcache');
        $lou_key = "weixin4:$this->name:gl_count";
        $lou_count = (int)$mem->get($lou_key);
        $view->set("config",$config)->set("success",$success)->set("lou_count",$lou_count);
        $this->template->content=$view;
    }
    public function action_floor(){
        $view=View::factory("user/config/gl/floor");
        $this->template->product_buy_id=$this->buy_id;
        $config['buy_id']=$this->buy_id;
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        if(isset($_POST['delete'])){
            $result = ORM::factory('glfloor')->where('id','=',$_POST['delete'])->find();
            $result->delete();
            if($result) $success='delete';
        }
        if (isset($_POST['floor'])) {
            $floor=$_POST['floor'];
            if ($floor['type']==1) {
                $floors=ORM::factory('glfloor')->where('buyid','=',$this->buy_id)->where('floor','=',$floor['floor'])->find();//已经存在的执行覆盖
                $floors->buyid=$this->buy_id;
                $floors->floor=$floor['floor'];
                $floors->iid=$floor['iid'];
                $floors->lastupdate=time();
                $result=$floors->save();
                if($result) $success='floor';
            }
            if ($floor['type']==2) {
                $n=$floor['num'];
                $t=$floor['tail'];
                for ($i=1; $i <= $n; $i++) {
                    $floors=ORM::factory('glfloor')->where('buyid','=',$this->buy_id)->where('floor','=',$t)->find();//已经存在的不改变 进行顺延
                    if(!$floors->id){
                        $floors->buyid=$this->buy_id;
                        $floors->floor=$t;
                        $floors->iid=$floor['iid2'];
                        $floors->lastupdate=time();
                        $result=$floors->save();
                    }
                    $t=$t+10;
                }
                if($result) $success='floor';
            }
        }
        // if(isset($_POST['floor'])){
        //     $floor = $_POST['floor'];
        //     $floors = ORM::factory('glfloor');
        //     $floors->buyid=$this->buy_id;
        //     $floors->floor=$floor['floor'];
        //     $floors->iid=$floor['iid'];
        //     $floors->lastupdate=time();
        //     $result=$floors->save();
        //     if($result) $success='floor';
        // }
        $num = ORM::factory('glfloor')->where('buyid','=',$this->buy_id)->count_all();
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('tpl/pages');
        $floor = ORM::factory('glfloor')->where('buyid','=',$this->buy_id)->limit($this->pagesize)->order_by('floor','DESC')->offset($offset)->find_all()->as_array();
        $item = ORM::factory('glitem')->where('buyid','=',$this->buy_id)->order_by('lastupdate','DESC')->find_all()->as_array();
        $view->set("config",$config)
             ->set("floor",$floor)
             ->set("success",$success)
             ->set("item",$item)
             ->set("pages",$pages)
             ->set("scripts",array("Resource/js/rebuy.js"));
        $this->template->content=$view;
    }
    public function action_item_add(){
        $view=View::factory("user/config/gl/item_add");
        $this->template->product_buy_id=$this->buy_id;
        $config['buy_id']=$this->buy_id;

        if(!$this->access_token){//未授权
            require_once Kohana::find_file("vendor/oauth","YZSignClient");

            $appId = ORM::factory('config')->where('buy_id','=',$this->buy_id)->find()->youzan_appid;
            $appSecret = ORM::factory('config')->where('buy_id','=',$this->buy_id)->find()->youzan_appsecret;
            if(!$appId||!$appSecret){
                die('请在【绑定有赞】点击【一键授权有赞】');
                // echo '请填写完整有赞相关参数';
                // exit;
            }
            $client = new YZSignClient($appId, $appSecret);

            $method1 = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields' =>'title,value,stock,fetch_url,group_id'
            ];

            $coupon=$client->post($method1,$this->methodVersion,$params);

            $method1 = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields' =>'present_id,title'
            ];
            $gift=$client->post($method1,$this->methodVersion,$params);

        }else{
            require Kohana::find_file('vendor', 'oauth/YZTokenClient');
            $client = new YZTokenClient($this->access_token);

            $method1 = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields' =>'title,value,stock,fetch_url,group_id'
            ];
            $coupon = $client->post($method1,$this->methodVersion,$params);
            // var_dump($coupon);
            $method1 = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields' =>'present_id,title'
            ];
            $gift=$client->post($method1,$this->methodVersion,$params);
            // var_dump($coupon);
            // echo '2';
        }
        // var_dump($coupon);
        // var_dump($gift);

        if(isset($_POST['item'])){
            $item = $_POST['item'];
            $items = ORM::factory('glitem');

            if($item['type']==1){//优惠券
                if(!$this->access_token){//未授权
                    $method1 = 'youzan.ump.coupons.unfinished.search';
                    $params = [
                        'fields' =>'title,value,stock,fetch_url,group_id'
                    ];
                    $coupon=$client->post($method1,$this->methodVersion,$params);
                }else{
                    $method1 = 'youzan.ump.coupons.unfinished.search';
                    $params = [
                        'fields' =>'title,value,stock,fetch_url,group_id'
                    ];
                    $coupon=$client->post($method1,$this->methodVersion,$params);
                }

                foreach ($coupon['response']['coupons'] as $coupon) {
                    if($coupon['group_id']==$item['groupid']){
                        $items->buyid=$this->buy_id;
                        $items->name=$coupon['title'];
                        $items->stock=$coupon['stock'];
                       // $items->code=$coupon['fetch_url'];
                        $items->code=$item['groupid'];
                        $items->type=$item['type'];
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            if($item['type']==2){//红包
                $items->buyid=$this->buy_id;
                $items->name=$item['title'];
                $items->code=$item['code'];
                $items->type=$item['type'];
                $items->lastupdate=time();
                $items->word=$item['word'];
                $items->save();
            }
            if($item['type']==3){//赠品
                if(!$this->access_token){//未授权
                    $method1 = 'youzan.ump.presents.ongoing.all';
                    $params = [
                        'fields' =>'present_id,title'
                    ];
                    $gift=$client->post($method1,$this->methodVersion,$params);
                }else{
                    $method1 = 'youzan.ump.presents.ongoing.all';
                    $params = [
                        'fields' =>'present_id,title'
                    ];
                    $gift=$client->post($method1,$this->methodVersion,$params);
                }
                foreach ($gift['response']['presents'] as $gift) {
                    if($gift['present_id']==$item['presentid']){
                        $items->buyid=$this->buy_id;
                        $items->name=$gift['title'];

                        $items->code=$gift['present_id'];
                        $items->type=$item['type'];
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            Request::instance()->redirect('user/config/gl/item/'.$this->buy_id);
        }
        $view->set("config",$config)->set("coupon",$coupon)->set("gift",$gift);
        $this->template->content=$view;
    }
    public function action_item_edit($buyid='',$iid=''){
        $view=View::factory("user/config/gl/item_add");
        $this->template->product_buy_id=$this->buy_id;
        $config['buy_id']=$this->buy_id;

        if(!$this->access_token){//未授权
            require_once Kohana::find_file("vendor/oauth","YZSignClient");

            $appId = ORM::factory('config')->where('buy_id','=',$this->buy_id)->find()->youzan_appid;
            $appSecret = ORM::factory('config')->where('buy_id','=',$this->buy_id)->find()->youzan_appsecret;
            $client = new YZSignClient($appId, $appSecret);

            $method1 = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields' =>'title,value,stock,fetch_url,group_id'
            ];

            $coupon=$client->post($method1,$this->methodVersion,$params);

            $method1 = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields' =>'present_id,title'
            ];
            $gift=$client->post($method1,$this->methodVersion,$params);

        }else{
            require Kohana::find_file('vendor', 'oauth/YZTokenClient');
            $client = new YZTokenClient($this->access_token);

            $method1 = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields' =>'title,value,stock,fetch_url,group_id'
            ];
            $coupon = $client->post($method1,$this->methodVersion,$params);

            $method1 = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields' =>'present_id,title'
            ];
            $gift=$client->post($method1,$this->methodVersion,$params);
        }
        if(isset($_GET['delete'])){
            $result = ORM::factory('glitem')->where('buyid','=',$this->buy_id)->where('id','=',$_GET['delete'])->find();
            $result->delete();
            $result = ORM::factory('glfloor')->where('buyid','=',$this->buy_id)->where('iid','=',$_GET['delete']);
            $result->delete_all();
            Request::instance()->redirect('user/config/gl/item/'.$this->buy_id);
        }
        if(isset($_POST['item'])){
            $item = $_POST['item'];
            $items = ORM::factory('glitem')->where('id','=',$item['id'])->find();

            if($item['type']==1){//优惠券
                if(!$this->access_token){//未授权
                    $method1 = 'youzan.ump.coupons.unfinished.search';
                    $params = [
                        'fields' =>'title,value,stock,fetch_url,group_id'
                    ];
                    $coupon=$client->post($method1,$this->methodVersion,$params);
                }else{
                    $method1 = 'youzan.ump.coupons.unfinished.search';
                    $params = [
                        'fields' =>'title,value,stock,fetch_url,group_id'
                    ];
                    $coupon=$client->post($method1,$this->methodVersion,$params);
                }
                foreach ($coupon['response']['coupons'] as $coupon) {
                    if($coupon['group_id']==$item['groupid']){
                        $items->buyid=$this->buy_id;
                        $items->name=$coupon['title'];
                        $items->stock=$coupon['stock'];
                        $items->code=$coupon['group_id'];
                        $items->type=$item['type'];
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            if($item['type']==2){//红包
                $items->buyid=$this->buy_id;
                $items->name=$item['title'];
                $items->code=$item['code'];
                $items->type=$item['type'];
                $items->lastupdate=time();
                $items->word=$item['word'];
                $items->save();
            }
            if($item['type']==3){//赠品
                if(!$this->access_token){//未授权
                    $method1 = 'youzan.ump.presents.ongoing.all';
                    $params = [
                        'fields' =>'present_id,title'
                    ];
                    $gift=$client->post($method1,$this->methodVersion,$params);
                }else{
                    $method1 = 'youzan.ump.presents.ongoing.all';
                    $params = [
                        'fields' =>'present_id,title'
                    ];
                    $gift=$client->post($method1,$this->methodVersion,$params);
                }
                foreach ($gift['response']['presents'] as $gift) {
                    if($gift['present_id']==$item['presentid']){
                        $items->buyid=$this->buy_id;
                        $items->name=$gift['title'];

                        $items->code=$gift['present_id'];
                        $items->type=$item['type'];
                        $items->lastupdate=time();
                        $items->word=$item['word'];
                        $items->save();
                        break;
                    }
                }
            }
            Request::instance()->redirect('user/config/gl/item/'.$this->buy_id);
        }
        $item = ORM::factory('glitem')->where('buyid','=',$buyid)->where('id','=',$iid)->find()->as_array();
        $view->set("config",$config)->set("item",$item)->set("coupon",$coupon)->set("gift",$gift);
        $this->template->content=$view;
    }
    private function Upload($name){
        $dir=Kohana::include_paths()[0].'vendor/weixin/glcert/';
        $flag=true;
       //echo $_FILES['filecert']['error']."fileerror";
        // if($name=="shenmafuyun")
        //    {$name="shenmafuyug-chen";
        //     echo $name;

        //            }
        if($_FILES['filecert']['error']>0)
        {
           $flag=false;
        }
        if(is_uploaded_file($_FILES['filecert']['tmp_name']))
        {
            if(!is_dir($dir.$name)){
                $new=mkdir($dir.$name);
                //echo $name;
                @chmod($dir.$name, 0777);//权限设置为0777
             }
            if(move_uploaded_file($_FILES['filecert']['tmp_name'], $dir.$name.'/1.zip'))
            {
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
        //echo $flag;
        $this->chmodr($dir.$name, 0777);
        return $flag;
    }




   function chmodr($path, $filemode) {//更改文件夹下文件的权限
        if (!is_dir($path))
        return @chmod($path, $filemode);
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
        if($file != '.' && $file != '..') {
        $fullpath = $path.'/'.$file;
        if(is_link($fullpath))
        return FALSE;
        elseif(!is_dir($fullpath) && !@chmod($fullpath, $filemode))
        return FALSE;
        elseif(!$this->chmodr($fullpath, $filemode))
        return FALSE;
        }
        }
        closedir($dh);
        if(@chmod($path, $filemode))
        return TRUE;
        else
        return FALSE;
     }
}
?>
