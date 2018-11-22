<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Config_Yy extends Controller_User_Home {

    public static $buy_id="";
    public static $file="";
    public function before()
    {

        parent::before();
        require Kohana::find_file("vendor/code","CommonHelper");

        $sid=Session::instance()->get('user')['sid'];
        $userid=ORM::factory('user')->where('user_shopid','=',$sid)->find()->user_id;
        self::$buy_id=Request::instance()->param("id");
        //$buy=ORM::factory("buy",array("buy_id"=>self::$buy_id,'user_id'=>$this->userid));
        $buy=ORM::factory('buy')->where('buy_id','=',self::$buy_id)->where('user_id','=',$userid)->find();
        if(!$buy->loaded())
        {
            echo "此链接非法！！";
            exit();
        }
    }
    public function action_index(){
        $view=View::factory("user/config/yy");
        self::$buy_id=Request::instance()->param("id");
        $name = Helper::UtfTo(Session::instance()->get("user")["name"]);
        if(strlen($name)>20)
            $name=substr($name,0,20);//截取20；
        $url="yy.smfyun.com/api/weixin3/".$name;
        $buy=ORM::factory("buy",array("buy_id"=>self::$buy_id));
        $product=ORM::factory("product",array("product_id"=>$buy->product_id));
        if($buy->is_config==0)
        {
            $config=null;
        }
        else
        {
            $config=ORM::factory("config",array("buy_id"=>self::$buy_id))->as_array();
            $config["token"]="smfyun1234";

        }
        $this->template->product_buy_id=self::$buy_id;
        $overtime=date("Y-m-d",strtotime("$buy->expiretime"));
        $view->set("overtime",$overtime);
        $view->set("config",$config)->set("id",self::$buy_id)->set("url",$url)->set("scripts",array("Resource/js/rebuy.js"));
        $this->template->content=$view;

    }

    public function action_youzan()
    {
        self::$buy_id=Request::instance()->param("id");
        $appid=$_POST["youzanappid"];
        $appsecret=$_POST["youzanappsecret"];
        $config=ORM::factory("config",array("buy_id"=>self::$buy_id));
        $buy=ORM::factory("buy",array("buy_id"=>self::$buy_id));
        $config->buy_id=self::$buy_id;
        $config->youzan_appid=$appid;
        $config->youzan_appsecret=$appsecret;
        $config->save();
        $buy->is_config=1;
        $buy->save();
        if($config->saved())
        {   $this->savetpl(self::$buy_id);
            echo "<script>location.href='".URL::site("user/config/yy/index/".self::$buy_id)."?yy=1'</script>";
        }
        else
        {
            echo "<script>location.href='".URL::site("user/config/yy/index/".self::$buy_id)."?yy=0'</script>";
        }

    }

    public function action_wechat()
    {
        self::$buy_id=Request::instance()->param("id");
        $name=$_POST['name'];
        $appid=$_POST['appid'];
        $appsecret=$_POST['appsecret'];
        Helper::check(array($appid,$appsecret,$name));
        $config=ORM::factory("config",array("buy_id"=>self::$buy_id));
        $config->buy_id=self::$buy_id;
        $config->nick_name=$name;
        $config->appid=$appid;
        $config->appsecret=$appsecret;
        $config->save();
        if($config->saved())
        {
           $this->savetpl(self::$buy_id);
           echo "<script>location.href='".URL::site("user/config/yy/index/".self::$buy_id)."?yy=0'</script>";
        }
        else
        {
            echo "<script>location.href='".URL::site("user/config/yy/index/".self::$buy_id)."?yy=0'</script>";
        }

    }

    public function action_customization()
    {
        self::$buy_id=Request::instance()->param("id");
        $config=ORM::factory("config",array("buy_id"=>self::$buy_id));
        $shopurl=$_POST["shopurl"];
        $keyword=$_POST['keyword'];
        $word=$_POST['word'];
        Helper::check(array($shopurl,$keyword,$word));
        $config->buy_id=self::$buy_id;
        $config->shopurl=$shopurl;
        $config->keyword=$keyword;
        $config->word=$word;
        $config->time=date('y-m-d h:i:s',time());
        $config->save();
        if($config->saved()){

            $file_head=Kohana::include_paths()[0].'vendor/weixin/yy/';//配置文件所在的目录
            $Toname=Helper::UtfTo(Session::instance()->get("user")["name"]);
            if(strlen($Toname)>20)
                $Toname=substr($Toname,0,20);

            self::$file=$file_head.$Toname.'.php';//生成配置文件的文件名
            $tpl=file_get_contents(Kohana::find_file('vendor', 'weixin/tpl_yy'));
            $value=fopen(self::$file,'w+');
            $content=sprintf(
                $tpl,$config->appid,$config->appsecret,$config->youzan_appid,$config->youzan_appsecret,self::$buy_id,'%s');

            $flag = fwrite($value,$content);

            $tpl = '$config["yuyin"]["'.$keyword.'"] = "'.$word.'";';
            if(!$word){
                $tpl = '$config["yuyin"]["'.$keyword.'"] = "嗯，小伙伴声音有点干哑，赶紧把安的蜜吃起来。点击链接'.$shopurl.'付邮费领取免费使用装。";';
            }
            fwrite($value,$tpl);
            if($flag)
            {
                echo "<script>location.href='".URL::site("user/config/yy/index/".self::$buy_id)."?yy=0'</script>";
            }
        }
        else
        {
            echo "<script>location.href='".URL::site("user/config/yy/index/".self::$buy_id)."?yy=0'</script>";
        }

    }

     public function savetpl($buyid)
    {
        $file_head=Kohana::include_paths()[0].'vendor/weixin/yy/';//配置文件所在的目录
        $Toname=Helper::UtfTo(Session::instance()->get("user")["name"]);
        if(strlen($Toname)>20)
            $Toname=substr($Toname,0,20);

        self::$file=$file_head.$Toname.'.php';//生成配置文件的文件名
        if(file_exists(self::$file))//存在则删除在保存最新数据
        {
            unlink(self::$file);
            $config=ORM::factory("config",array("buy_id"=>$buyid));
            $tpl=file_get_contents(Kohana::find_file('vendor', 'weixin/tpl_yy'));
            $value=fopen(self::$file,'w+');
            $content=sprintf(
                $tpl,$config->appid,$config->appsecret,$config->youzan_appid,$config->youzan_appsecret,self::$buy_id,'%s',$config->keyword,$config->shopurl,$config->word);
            $flag=fwrite($value,$content);
            if($flag)
                return true;
            else
                return false;
        }

    }
}
?>
