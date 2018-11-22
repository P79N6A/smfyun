<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Home extends Controller_Template {

    public $template="tpl/template";
    public static $products=array();
	public function action_index(){
        $home=View::factory("user/home")->set("user",Session::instance()->get("user"));
        $this->template->content=$home;
	}
    public function before()
    {
        parent::before();
        if(Session::instance()->get("user")==null)
        {
            echo "<script>location.href='".URL::site("index/index")."'</script>";
            return;
        }
        $user_id=ORM::factory("user",array("user_shopid"=>Session::instance()->get("user")["sid"]))->user_id;
        //echo $user_id;
        $buy=ORM::factory("buy")->where("user_id","=",$user_id)->find_all()->as_array();
       //print_r($buy);
        foreach($buy as $value)
        {
            $product=ORM::factory("product",array("product_id"=>$value->product_id))->as_array();
            $product["buy_id"]=$value->buy_id;
            array_push(self::$products,$product);
        }
        $this->template->products=self::$products;
        $this->template->action='home';
        $this->template->styles=array();
        $this->template->scripts=array();
        $this->template->title="";
        $this->template->content="";

    }
}
?>
