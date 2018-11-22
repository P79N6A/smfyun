<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Record extends Controller_User_Home {
    public static $products=array();
    //public static $onlyproduct=array();
    public $pagesize = 5;
    public function before()
    {
        parent::before();
        require Kohana::find_file("vendor/code","CommonHelper");
    }
    public function action_index(){
        $user_id=ORM::factory("user",array("user_shopid"=>Session::instance()->get("user")["sid"]))->user_id;
       $sql= DB::query(Database::SELECT,"select rebuy_id,rebuy_price,rebuy_time,rebuy_orderid from rebuy where user_id=$user_id union select buy_id,buy_price,buy_time,order_id from buy where user_id=$user_id");
       $num=count($sql->execute()->as_array());
       $page = max($_GET['page'], 1);
       $offset = ($this->pagesize * ($page - 1));
       $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('tpl/pages');
       $allbuy_data=DB::query(Database::SELECT,"select rebuy_id,product_id,rebuy_price,rebuy_time,rebuy_orderid from rebuy where user_id=$user_id union select buy_id,product_id,buy_price,buy_time,order_id from buy where user_id=$user_id order by rebuy_time desc limit $this->pagesize offset $offset")->execute()->as_array();
        foreach($allbuy_data as $value)
        {
            $product=ORM::factory("product",array("product_id"=>$value["product_id"]))->as_array();
            $product["buy_id"]=$value["rebuy_id"];
            $product["order_id"]=$value["rebuy_orderid"];
            $product["buy_price"]=$value["rebuy_price"];
            $product["buy_time"]=$value["rebuy_time"];
            $temp=ORM::factory('rebuy')->where('rebuy_id','=',$value['rebuy_id'])->where('user_id','=',$user_id)->where('rebuy_orderid','=',$value['rebuy_orderid'])->find();
            if($temp->id)
            {
                $product['time_over']="";
                $product['time_over']=$temp->rebuy_timeover;
            }else{
               // $product["time_over"]=$value[""];
                // echo $product['time_over'];
                $sku_time=ORM::factory('sku')->where('product_id','=',$value["product_id"])->where('sku_price','=',floatval($product["buy_price"]))->find()->other;
                if(isset($sku_time)&&$sku_time<20)
                $product["time_over"]=$sku_time;


            }
            //exit;
            array_push(self::$products,$product);
        }


        $view=View::factory("user/record");
        $this->template->action='record';
        $view->set("products",self::$products);
        $view->set('pages', $pages);
        $view->set('type', 1);
        $this->template->content=$view;
    }


    public function action_rebuy(){
        $user_id=ORM::factory("user",array("user_shopid"=>Session::instance()->get("user")["sid"]))->user_id;
        $items=ORM::factory("buy")->where("user_id","=",$user_id);
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages_items = Pagination::factory(array(
            'total_items'   => $items->count_all(),
            'items_per_page'=> $this->pagesize,
        ))->render('tpl/pages');
         $onlybuy_data=DB::query(Database::SELECT,"select * from product,buy where buy.user_id=$user_id and buy.product_id=product.product_id order by buy.buy_time desc limit $this->pagesize offset $offset")->execute()->as_array();

        //获取设备session
        $device=Session::instance()->get("agent");
        $this->template->action='record';
        $view=View::factory("user/record");
        $view->set("onlybuydata",$onlybuy_data);
        $view->set('pages', $pages_items);
        $view->set('type', 0);
        $view->set('device', $device);
        $this->template->content=$view;
    }
    public function action_xufeiing(){
        if($_POST['stage']==-1)
        {
            $pid=ORM::factory("buy")->where("buy_id","=",$_POST['rebuyid'])->find()->product_id;
            $skudata=DB::query(database::SELECT,"select sku.sku_id,sku.sku_name,sku.sku_pro,sku.sku_price,sku.old_price,sku.product_id,product.product_name,product.category,product.count FROM sku,product where sku.product_id=$pid and sku.product_id=product.product_id")->execute()->as_array();
            echo json_encode($skudata);

        }
        exit();

    }

     public function action_update(){
        $product=ORM::factory("product")->find_all()->as_array();
        foreach ($product as $key) {
            $product_id=$key->product_id;
            //$selddata=DB::query(Database::SELECT,"select rebuy_id,product_id,rebuy_price,rebuy_time,rebuy_orderid from rebuy where user_id=$user_id union select buy_id,product_id,buy_price,buy_time,order_id from buy where user_id=$user_id order by rebuy_time limit $this->pagesize offset $offset")->execute()->as_array();
            $buy=ORM::factory("buy")->where("product_id","=",$product_id)->find_all();
            $buynum=count($buy);
            echo $buynum."////";
            $rebuy=ORM::factory("rebuy")->where("product_id","=",$product_id)->find_all();
            $rebuynum=count($rebuy);
             $pro=ORM::factory("product",array('product_id'=>$product_id));

            $pro->count=$buynum+$rebuynum;
            $pro->save();
            echo $rebuynum."///</br>";
            //exit();

        }
        // $=ORM::factory("buy")->where("product_id","=",self::$product_id)->find_all();
        // $num=count($co);

    }

}
