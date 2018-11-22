<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Config_Hb extends Controller_User_Home {

    public static $file="";
    public static $tempname="";
    public $name;
    public $buy_id="";
    public $pagesize=20;

    public function before()
    {
        if (Request::instance()->action == 'cron') return;
        parent::before();
        require Kohana::find_file("vendor/code","CommonHelper");
        $sid=Session::instance()->get('user')['sid'];
        $userid=ORM::factory('user')->where('user_shopid','=',$sid)->find()->user_id;
        $this->buy_id=Request::instance()->param("id");

        $this->name = Session::instance()->get("sname");
        //echo $this->name;
        $buy=ORM::factory('buy')->where('buy_id','=',$this->buy_id)->where('user_id','=',$userid)->find();
        if(!$buy->loaded())
        {
            echo "此链接非法！！";
            exit();
        }

        $this->template->product_buy_id=$this->buy_id;
        View::bind_global('buy_id', $this->buy_id);

    }

    public function action_index(){
        $view=View::factory("user/config/hb/index");
        $url="hb.smfyun.com/api/weixin2/".$this->name;
        $buy=ORM::factory("buy",array("buy_id"=>$this->buy_id));

        if(isset($_POST['yz']))
        {   $yz=$_POST['yz'];
            $appid=$yz["youzanappid"];
            $appsecret=$yz["youzanappsecret"];
            $config=ORM::factory("config",array("buy_id"=>$this->buy_id));//A
            $buy=ORM::factory("buy",array("buy_id"=>$this->buy_id));
            $config->buy_id=$this->buy_id;//这句代码 当A中buyid不存在是可以根据这个创建新数据。
            $config->youzan_appid=$appid;
            $config->youzan_appsecret=$appsecret;
            $config->save();
            $buy->is_config=1;
            $buy->save();
            if($config->saved())
            {
                $this->savetpl($this->buy_id);
                $success['ok']='yz';
            }
        }
        $access_token = ORM::factory('config')->where('buy_id', '=', $this->buy_id)->find()->access_token;
        if(!$access_token){
            $oauth=1;
        }
        $config=ORM::factory("config",array("buy_id"=>$this->buy_id))->as_array();
        $view->set("config",$config)->set("url",$url)
                        ->set('success',$success)->set('oauth',$oauth);
        $this->template->content=$view;
    }

    public function action_oauth(){

        Request::instance()->redirect('https://open.koudaitong.com/oauth/authorize?client_id=40886d863442546449&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/user/config/hb/callback/'.$this->buy_id);
    }
    //回调获取 商户信息
    public function action_callback($buy_id){
        $url="https://open.koudaitong.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"40886d863442546449",
            "client_secret"=>"ac55d6b5934cdecbd66645fe76eca94a",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/user/config/hb/callback/'.$buy_id
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
            require Kohana::find_file('vendor', 'oauth/KdtApiOauthClient');
            $oauth=new KdtApiOauthClient();
            $value=$oauth->get($result->access_token,'kdt.shop.basic.get')["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['sid'];
            $name = $value['name'];

            $hb = ORM::factory('config')->where('buy_id', '=', $buy_id)->find();
            $hb->buy_id = $buy_id;
            $hb->access_token = $result->access_token;
            $hb->kind = 'hb';
            $hb->refresh_token = $result->refresh_token;
            $hb->save();
            echo "<script>alert('授权成功');location.href='".URL::site('user/config/hb/index/'.$buy_id)."';</script>";
        }
        //Request::instance()->redirect('wdya/home');
    }
    public function action_wechat()
    {
        $view=View::factory("user/config/hb/wechat");
        if(isset($_POST['wx'])||isset($_POST['filecert']))
        {   $wx=$_POST['wx'];
            $name=$wx['name'];
            $appid=$wx['appid'];
            $appsecret=$wx['appsecret'];
            $partnerid=$wx['partnerid'];
            $partnerkey=$wx['partnerkey'];
            Helper::check(array($appid,$appsecret,$partnerid,$partnerkey,$name));
            $config=ORM::factory("config",array("buy_id"=>$this->buy_id));
            $config->buy_id=$this->buy_id;
            $config->nick_name=$name;
            $config->appid=$appid;
            $config->appsecret=$appsecret;
            $config->partnerid=$partnerid;
            $config->partnerkey=$partnerkey;

            $config->save();
            if($config->saved())
            {

                $this->savetpl($this->buy_id);
                $success['ok']='wx';
                $temp=time();
                if($this->Upload($this->name))
                {
                    $success['ok']='file';
                }

            }
        }

        $dir=Kohana::include_paths()[0].'vendor/weixin/cert/'.$this->name;
        if(is_dir($dir))
        {
            $cert_name=$this->name.".zip";
        }
        else
        {
            $cert_name=null;
        }

        $config=ORM::factory("config",array("buy_id"=>$this->buy_id))->as_array();
        $view->set('config',$config)->set('success',$success)->set('cert_name',$cert_name);
        $this->template->content=$view;
    }

    public function action_custom()
    {
        $view=View::factory("user/config/hb/custom");
        if(isset($_POST['cus'])||isset($_POST['split']))
        {
            $cus=$_POST['cus'];
            $config=ORM::factory("config",array("buy_id"=>$this->buy_id));
            // $shopurl=$cus['shopurl'];
            // $saleurl=$cus['buyurl'];
            $money=$cus['money'];

            $moneyMin=$cus['moneyMin'];
            $success=$cus['success'];
            $success2=$cus['success2'];
            $payed=$cus['payed'];
            $got=$cus['got'];
            $hack=$cus['hack'];
            $rate=$cus['rate'];
            $success_msg=$cus['success_msg'];

            $splitdata=$_POST['split'];
            if($splitdata['status']==1&&$splitdata['count']>0)//开启裂变
                {
                    $splitcount=$splitdata['count'];
                    $splittimes=1;
                    $wxname=$splitdata['wxname'];
                }
            else
            {
                $splitcount=0;
                 $splittimes=0;
                 $wxname="";
            }

            Helper::check(array($money));
            $config->buy_id=$this->buy_id;
            // $config->shopurl=$shopurl;
            // $config->saleurl=$saleurl;
            $config->money=$money;

            $config->moneyMin=$moneyMin;
            $config->success=$success;
            $config->success2=$success2;
            $config->payed=$payed;
            $config->got=$got;
            $config->hack=$hack;
            $config->rate=$rate;
            $config->success_msg=$success_msg;

            $config->time=date('y-m-d h:i:s',time());
            $config->num=$splitcount;
            $config->other=$wxname;
            $config->save();
            if($config->saved())
            {
            $this->savetpl($this->buy_id);
            // $success['ok']='cus';
            $comi['ok']='cus';
           }
       }

        $config=ORM::factory("config",array("buy_id"=>$this->buy_id))->as_array();
        $view->set('config',$config);
        $view->set('comi',$comi);
        $this->template->content=$view;

    }

    public function  action_count()
    {
        $view=view::factory("user/config/hb/count");
        $flag=0;
        //最后一次产生口令的时间,筛选时提出掉裂变口令;
        $bid=$this->name;
        $lastupdate=ORM::factory('weixinkl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=strtotime(ORM::factory('rebuy')->where('rebuy_id','=',$this->buy_id)->order_by('rebuy_time','DESC')->find()->lastupdate);//rebuy_time是时间戳
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
        $config=orm::factory('config',array('buy_id'=>$this->buy_id))->find()->as_array();
        $view->set('config',$config)->set('left',$left);
        $this->template->content=$view;
    }

   public  function action_record()
    {
        $config=orm::factory('config',array('buy_id'=>$this->buy_id))->find()->as_array();
        //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款

        $countall=ORM::factory('weixinhb2')->where('bid','=',$this->name)->where('mch_billno','>',0)->count_all();

         //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('tpl/pages');
        $records=ORM::factory('weixinhb2')->where('bid','=',$this->name)->where('mch_billno','>',0)->limit($this->pagesize)->offset($offset)->find_all();
        foreach ($records as $temp) {
             $tempstatus=ORM::factory('Weixinhbstatu')->where('mch_billno','=',$temp->mch_billno)->find();
             if($tempstatus->status=="REFUND"||$tempstatus->status=="RECEIVED"||$tempstatus->status=="FAILED")
                continue;

             $result=$this->hongbaoresult($config,$temp->mch_billno);
            if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
                 {
                    $tempstatus->status=$result['status'];
                    $tempstatus->save();
                 }
                 else {

                    $return_msg=$result['return_msg'];
                    //var_dump($return_msg);
                    break;
                 }

       }

        $view=view::factory('user/config/hb/record')->set('records',$records)->set('page',$pages)->set('return_msg',$return_msg);
        $this->template->content=$view;
    }
    public function action_generate(){
        set_time_limit(0);
        $bid=$this->name;
        $buynum=ORM::factory('buy')->where('buy_id','=',$this->buy_id)->find()->other;

        //最后一次产生口令的时间;筛选时提出掉裂变口令
        $flag=0;
        $lastupdate=ORM::factory('weixinkl')->where('bid', '=', $bid)->where('split','=',0)->order_by('lastupdate', 'DESC')->find()->lastupdate;
        //最新的续费时间；
        $buytimenew=strtotime(ORM::factory('rebuy')->where('rebuy_id','=',$this->buy_id)->order_by('rebuy_time','DESC')->find()->rebuy_time);

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
                echo "<script>location.href='".URL::site('user/config/hb/count/'.$this->buy_id)."?koulin=3';</script>";
           }

            if($flag==1)
            {
             Helper::GenerateCode($bid,$buynum);
             //直接退出
             exit();
             //echo "<script>history.go(-1);</script>";产生的csv会有参杂html代码
            }
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
        // @unlink($file);
    }

    public function action_getdata(){
        $bid=$this->name;
        $result=array();
        $buycodenum=ORM::factory('buy')->where('buy_id','=',$this->buy_id)->find()->other;//购买总数
        $creatcodenum = ORM::factory('weixinkl')->where('bid', '=', $bid)->count_all();//产生的口令总数
        $normalkoulin=ORM::factory('weixinkl')->where('bid','=',$bid)->where('split','=',0)->count_all();//普通口令数
        $liebiankoulin=ORM::factory('weixinkl')->where('bid','=',$bid)->where('split','>',0)->count_all();//裂变口令数

        $normalkoulinused=ORM::factory('weixinkl')->where('bid','=',$bid)->where('split','=',0)->where('used','>',0)->count_all();//普通已使用的口令数
        $liebiankoulinused=ORM::factory('weixinkl')->where('bid','=',$bid)->where('split','>',0)->where('used','>',0)->count_all();//裂变已使用的口令数

        $usedcodenum=ORM::factory('weixinkl')->where('used', '>', 0)->where('bid', '=', $bid)->count_all();

        if($creatcodenum<=0){
            echo '0';
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
            echo json_encode($result);
        }
        exit;
    }




    private function Upload($name){
        $dir=Kohana::include_paths()[0].'vendor/weixin/cert/';
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
        $this->chmodr($dir.$name, 0775);
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

    public function savetpl($buyid)
    {
        $file_head=Kohana::include_paths()[0].'vendor/weixin/biz/';//配置文件所在的目录
        self::$file=$file_head.$this->name.'.php';//生成配置文件的文件名
        if(file_exists(self::$file))//存在则删除在保存最新数据
        {
            unlink(self::$file);
        }
            $config=ORM::factory("config")->where('buy_id','=',$buyid)->find();
            if(($config->num)>0)//分裂个数大于零
            {
                $splittimes=1;
                $splitcount=$config->num;
            }
            else
            {
                $splitcount=0;
                $splittimes=0;
            }
            $tpl=file_get_contents(Kohana::find_file('vendor', 'weixin/tpl'));
            $value=fopen(self::$file,'w+');
            $content=sprintf(
                $tpl,$config->appid,$config->appsecret,$config->partnerid,$config->partnerkey,
                $config->youzan_appid,$config->youzan_appsecret,$config->nick_name,$config->buy_id,$config->payed,
                $config->success2,$config->success,$config->got,$config->success_msg,$config->hack,$config->rate,$config->moneyMin,$config->money,10000,$splittimes,$splitcount,'%s','%s',$config->other,0,'%s');
            $flag=fwrite($value,$content);

            @chmod(self::$file, 0777);//权限设置为0777
            if($flag)
                return true;
            else
                return false;



    }


    private function hongbaoresult($config,$mch_billno)
    {

            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            require_once Kohana::find_file('vendor', 'weixin/inc');
            require Kohana::find_file('vendor', "weixin/biz/$this->name");

            $we = new Wechat($config);

        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //商户号
        $data["appid"] =$config['appid'];
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息



        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));

        $postXml = $we->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        //echo $this->name."********".$mch_billno;
        $resultXml = @curl_post_ssl($url, $postXml, 5, array(), $this->name);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }

    public function action_cron()
    {
         //SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取 REFUND:已退款
        set_time_limit(50);
        $senddates=ORM::factory('weixinhbstatu')->where_open()->or_where('status','=',null)->or_where('status','=','SENDING')->or_where('status','=','SENT')->where_close()->limit(100)->find_all();
        foreach ($senddates as $value)
         {
           $result=$this->hongbaocron($value->bid,$value->mch_billno);
           if($result['return_code']=="SUCCESS"&&$result['result_code']=="SUCCESS")
            {
                    $value->status=$result['status'];
                    $value->save();

            }
            echo $value->bid.'---mch_billno:'.$value->mch_billno."---".$result['return_msg']."</br>";
        }
        exit();


    }

     private function hongbaocron($name,$mch_billno)
    {

        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require Kohana::find_file('vendor', "weixin/biz/$name");
        $we = new Wechat($config);

        if (!$config['partnerid']) die("$bid not found.\n");

        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //商户号
       // echo "商户号为:".$config['partnerid']."----</br>";
        $data["appid"] =$config['appid'];
        $data['bill_type']='MCHT';//MCHT:通过商户订单号获取红包信息



        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));

        $postXml = $we->xml_encode($data);
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $resultXml = @curl_post_ssl($url, $postXml, 5, array(), $name);
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['status'] = (string)$response->status[0];
        $result['err_code'] = (string)$response->err_code[0];
        return $result;
    }

}
?>
