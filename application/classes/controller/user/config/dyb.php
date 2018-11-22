<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Config_Dyb extends Controller_User_Home {
 public function before()
    {
        parent::before();
        require Kohana::find_file("vendor/code","CommonHelper");
    }
 public function action_index(){
  $url="http://dyb.smfyun.com/dyba/login";//登陆域名
  $intro="";
  $view=View::factory("user/config/dyb");
  $id=Request::instance()->param("id");
  $buy=ORM::factory("buy",array("buy_id"=>$id));
        $product=ORM::factory("product",array("product_id"=>$buy->product_id));
        //$timeover=date("Y-m-d",strtotime("$buy->buy_time+$product->time_over month"));
  $from="插件平台";
        $update=strtotime(date('y-m-d h:i:s',time()));//更新时间
        $login=$update;//登录时间
        $logins=0;//登陆次数
        $admin=0;//是否是管理员
        //$exprie=date("Y-m-d H:i:s",strtotime("+ 1 month",$update));//过期时间
        $exprie=$buy->expiretime;//过期时间
        // $name=Helper::UtfTo(Session::instance()->get('user')["name"]);
        // if(!$name){
        //     $name = Session::instance()->get('user')["name"];
        // }
        // //$name='qiaozhigaoerfuchuang';

        // if(strlen($name)>20)
        //     $name=substr($name,0,20);//数据库字段为20 超出部分会丢失，无法和session匹配；
        $name = Session::instance()->get("sname");
        $pass =  rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        $dyb=ORM::factory("dyb");
        $result=$dyb->where("user",'=',$name)->find();
        $view->set("url",$url)->set("intro",$intro);
        $view->set("user",$name);

         //获取设备session
        $device=Session::instance()->get("agent");
        $view->set('device', $device);

        if($result->loaded())//表明用户存在
        {
            $view->set("user",$dyb->user)->set("pass",$dyb->pass);
        }
        else
        {
            $dyb->user=$name;
            $dyb->pass=$pass;
            $dyb->name=$from;
            $dyb->lastupdate=$update;
            $dyb->expiretime=$exprie;
            $dyb->admin=$admin;
            $dyb->logins=$logins;
            $dyb->lastlogin=$login;
            $dyb->save();
            if($dyb->saved())
            {
                $view->set("user",$dyb->user)->set("pass",$dyb->pass);
            }

        }
        $this->template->product_buy_id=$id;
        $view->set("time",$dyb->expiretime);
        $view->set("id",$id);
        $this->template->content=$view;
 }

}
