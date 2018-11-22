<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Config_Kmi extends Controller_User_Home {
	public function before()
    {
        parent::before();
    }
	public function action_index(){
		$url="http://cxb.smfyun.com/kmia/login";//登陆域名
		$intro="https://wap.koudaitong.com/v2/feature/gefup4ix";
		$view=View::factory("user/config/kmi");
		$id=Request::instance()->param("id");
		$buy=ORM::factory("buy",array("buy_id"=>$id));
        $product=ORM::factory("product",array("product_id"=>$buy->product_id));


		$from="插件平台";
        $update=strtotime(date('y-m-d h:i:s',time()));//更新时间
        $login=$update;//登录时间
        $logins=0;//登陆次数
        $admin=0;//是否是管理员
        $exprie=$buy->expiretime;//过期时间
        $name = Session::instance()->get("sname");
        $pass =  rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $kmi=ORM::factory("kmi");
        $result=$kmi->where("user",'=',$name)->find();
        $view->set("url",$url)->set("intro",$intro);
        $view->set("user",$name);

         //获取设备session
        $device=Session::instance()->get("agent");
        $view->set('device', $device);

        if($result->loaded())//表明用户存在
        {
            $view->set("user",$kmi->user)->set("pass",$kmi->pass);
        }
        else
        {
            $kmi->user=$name;
            $kmi->pass=$pass;
            $kmi->name=$from;
            $kmi->lastupdate=$update;
            $kmi->expiretime=$exprie;
            $kmi->admin=$admin;
            $kmi->logins=$logins;
            $kmi->lastlogin=$login;
            $kmi->save();
            if($kmi->saved())
            {
                $view->set("user",$kmi->user)->set("pass",$kmi->pass);
            }

        }
        $this->template->product_buy_id=$id;
        $view->set("time",$kmi->expiretime);
        $view->set("id",$id);
        $this->template->content=$view;
	}

}
