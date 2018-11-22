<?php
defined('SYSPATH') or die('No direct script access.');
require_once Kohana::find_file("vendor/kdt","KdtApiClient");
class Controller_User_Config_Qdb extends Controller_User_Home {

    public static $buy_id="";
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
        $view=View::factory("user/config/qdb");
        self::$buy_id=Request::instance()->param("id");
        $config=ORM::factory("config")->where("buy_id","=",self::$buy_id)->find();
        $yappid=$config->youzan_appid;
        $yappsecret=$config->youzan_appsecret;
        if($yappid!=null&&$yappsecret!=null)
        {
          $arr_goodid=ORM::factory("qdbcfg")->where('buyid',"=", self::$buy_id)->find_all()->as_array();
          foreach ($arr_goodid as $temp) {
           $this->gengxin($temp->goodid);
          }
        }
        $res=$this->getres(self::$buy_id);

        if(isset($_POST['keyword']))
        {
          $action_flag =$_POST["action_flag"];
          // $tt=Session::instance()->get("$action_flag");
          $tt=$_SESSION["$action_flag"];
          if ($action_flag!=""&&$tt==1)
          {
             $_SESSION["$action_flag"]=2;
             $view->set('con','签到设置保存成功!');
             unset ($_SESSION["$action_flag"]);
          }
        }
        $this->template->product_buy_id=self::$buy_id;
        $buy=ORM::factory("buy",array("buy_id"=>self::$buy_id));
        $overtime=date("Y-m-d",strtotime("$buy->expiretime"));
        $view->set("overtime",$overtime);
        $view->set("config",$res['config'])->set("id",self::$buy_id)->set('setgoods',$res['setgoods'])->set("soldeds",$res['soldeds'])->set("hexiao",$res['hexiao'])->set("scripts",array("Resource/js/rebuy.js"));
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
        $view=View::factory("user/config/qdb");

        $action_flag =$_POST["action_flag"];
        $tt=$_SESSION["$action_flag"];
        if ($action_flag!=""&&$tt==1)
        {
             $_SESSION["$action_flag"]=2;
             unset ($_SESSION["$action_flag"]);
            if($config->saved())
            {
                $view->set('con','有赞参数配置成功!');
            }
            else
            {
                $view->set('con','发生未知的错误请稍候再试！');
            }
        }

        $res=$this->getres(self::$buy_id);
        $buy=ORM::factory("buy",array("buy_id"=>self::$buy_id));
        $overtime=date("Y-m-d",strtotime("$buy->expiretime"));
        $view->set("overtime",$overtime);
        $view->set("config",$res['config'])->set("id",self::$buy_id)->set('setgoods',$res['setgoods'])->set("soldeds",$res['soldeds'])->set("hexiao",$res['hexiao'])->set("scripts",array("Resource/js/rebuy.js"));
        $this->template->content=$view;

    }

   public function action_goodidset()
    {

      $buyid=$_POST['buyid'];
      $goodid=$_POST['goodid'];

      $qdbcfg=ORM::factory("qdbcfg",array("buyid"=>$buyid,"goodid"=>$goodid));

      if($qdbcfg->loaded())//数据库中已存在
      {
        if($qdbcfg->isshow==1)//已显示 则直接退出
        {
        echo json_encode("1");
        exit();
        }
        else
        {
        $qdbcfg->isshow=1;
        $qdbcfg->configtime=date('y-m-d H:i:s',time());
        $qdbcfg->save();
        $confdata=DB::query(Database::SELECT,"select *from qdb_cfg where buyid=$buyid and isshow=1 order by configtime desc")->execute('alternate')->as_array();
        echo json_encode($confdata);
        exit();
        }
      }
      else{///***********************/
      $config=ORM::factory("config")->where("buy_id","=",$buyid)->find();
      $yappid=$config->youzan_appid;
      $yappsecret=$config->youzan_appsecret;
      $client = new KdtApiClient($yappid, $yappsecret);


      // $method = 'kdt.trades.sold.get';
      // $params = array(
      //      'page_size' =>100,
      //      'page_no' =>1 ,
      //      'use_has_next' => true,
      // );
      // $traderesults=$client->post($method,$params);

     $is_havedata=0;
     for($pg=1,$next=true;$next==true;$pg++){
       $method = 'kdt.trades.sold.get';
       $params = [
       'page_size' =>100,
       'page_no' =>$pg,
       'use_has_next'=>true,
      ];
      $traderesults = $client->post($method,$params);
      $next = $traderesults['response']['has_next'];


      $nowtime=date("Y-m-d H:i:s");


      for($i=0;$traderesults['response']['trades'][$i];$i++)/**for--flag start*/
      {
            $res=$traderesults['response']['trades'][$i];
                if($res['num_iid']=="$goodid")
                {
                   $is_havedata=1;
                   $qdbcfg->goodname=$res['title'];
                   $qdbcfg->goodid=$goodid;
                   $qdbcfg->buyid=$buyid;
                   $qdbcfg->price=$res['price'];
                   $qdbcfg->storename=$res['orders'][0]['seller_nick'];
                   $qdbcfg->configtime=date('y-m-d H:i:s',time());
                   //$qdbcfg->save();
                           // 存入数据到数据表qdb_trades
                       if($res['status']=="WAIT_BUYER_CONFIRM_GOODS"||$res['status']=="TRADE_BUYER_SIGNED")
                      {

                          $trade_tid=$res['tid'];//该笔交易唯一编号，更新时用作与数据库的对比插入；
                          $code=$res['tid'];
                          $method_code = 'kdt.trade.virtualcode.get';
                          $params_code = [
                              'code'=>$code,
                              ];
                          $coderes=$client->post($method_code,$params_code);

                              $qdbdate=ORM::factory("qdbtrade",array("trade_id"=>$trade_tid));
                              if(!$qdbdate->loaded())//该数据不存在，则插入
                                  {
                                  $ischange=true;
                                  $qdbdate->trade_id=$res['tid'];
                                  $qdbdate->goodid=$res['num_iid'];
                                  $qdbdate->goodname=$res['title'];
                                  $qdbdate->buynum=$res['num'];
                                  $qdbdate->mobile=$res['orders'][0]['buyer_messages'][0]['content'];
                                  $qdbdate->payname=$res['buyer_nick'];
                                  $qdbdate->buytime=$res['pay_time'];
                                  $qdbdate->buymoney=$res['price'];
                                  $qdbdate->prodbuy_id=$buyid;
                                  $qdbdate->isused=$coderes["response"]["status"];
                                  if ($coderes["response"]["status"]==2)//为2为已使用
                                  $qdbdate->usingtime= $coderes["response"]["use_time"];

                                  $qdbdate->save();
                                  //echo$qdbdate;
                                  }
                              else if($coderes["response"]["status"]==2)//如果数据存在，判断核销转态是否需要更新
                                  {
                                    $ischange=true;
                                     $qdbdate->isused=$coderes["response"]["status"];
                                     $qdbdate->usingtime= $coderes["response"]["use_time"];
                                     $qdbdate->save();
                                    //echo$qdbdate;
                                  }
                       }

                        $qdbcfg->lastupdatetime=$nowtime;//插入数据更新时间,刷新用;
                        $qdbcfg->configtime=date('y-m-d H:i:s',time());
                        $qdbcfg->save();

                }

      }/**for--flag end*/

    }

     if($is_havedata==0)//没有该goodid的数据
     {
      $confdata=null;

     }
     else//重新拉取该所有用户商品数据；
      $confdata=DB::query(Database::SELECT,"select *from qdb_cfg where buyid=$buyid and isshow=1 order by configtime desc")->execute('alternate')->as_array();

    echo json_encode($confdata);
    exit();
    }/////************/


 }



public function action_sousuo()
{
     $buyid=$_POST['buyid'];
    $mobile=$_POST['mobile'];
    $sql=DB::query(Database::SELECT,"select *from qdb_trades where mobile =$mobile and prodbuy_id=$buyid");
    $buydata=$sql->execute('alternate')->as_array();
    echo json_encode($buydata);
    exit();

}

public function action_hiddenid()
{
    $goodid=$_POST['goodid'];
    $config=ORM::factory("qdbcfg")->where("goodid","=",$goodid)->find();
    $config->isshow=0;
    $config->save();
    if($config->saved())
    {
      $res=1;
    }
    else
      $res=0;
    echo json_encode($res);
    exit();

}
public function getres($id)
{
        self::$buy_id=$id;
        $buyid=$id;
        $buy=ORM::factory("buy",array("buy_id"=>self::$buy_id));
        $product=ORM::factory("product",array("product_id"=>$buy->product_id));
        if($buy->is_config==0)
        {
            $config=null;
        }
        else
        {
            $config=ORM::factory("config",array("buy_id"=>self::$buy_id))->as_array();
           // $config["overtime"]=date("Y-m-d",strtotime("$buy->expiretime"));
            $config["overtime"]=date("Y-m-d",strtotime("$buy->buy_time+$product->time_over month"));
            if($config['youzan_appid']&&$config['youzan_appsecret'])
            {
                $goods=DB::query(Database::SELECT,"SELECT * from qdb_cfg where buyid =$buyid and isshow=1 order by configtime desc")->execute('alternate')->as_array();
                for($i=0;$goods[$i];$i++)
                {
                  $goodid=$goods[$i]['goodid'];
                    $setgoods[$i]=ORM::factory("qdbcfg")->where("buyid","=",self::$buy_id)->where("goodid","=",$goodid)->find();
                    $temp=$setgoods[$i]->as_array();
                   $solded[$i]['goodid']=$goodid;
                   $hexiao[$i]['goodid']=$goodid;
                   $solded[$i]['name']=$temp['goodname'];
                   $hexiao[$i]['name']=$temp['goodname'];
                  // $solded[$i]['tradedetail']=ORM::factory("qdbtrade")->where("goodid","=",$goodid[$i])->find_all()->as_array();
                    $sql=DB::query(Database::SELECT,"SELECT *FROM qdb_trades where goodid=$goodid order by buytime desc");
                     $solded[$i]['tradedetail']=$sql->execute('alternate')->as_array();
                    $sqlhx=DB::query(Database::SELECT,"SELECT *from qdb_trades where goodid=$goodid and isused =2 order by usingtime desc");
                    // $hexiao[$i]['hexiaodetail']=ORM::factory("qdbtrade")->where("goodid","=",$goodid[$i])->where("isused","=",2)->find_all()->as_array();
                    $solded[$i]['tradesnum']=ORM::factory("qdbtrade")->where("goodid","=",$goodid)->count_all();
                    $hexiao[$i]['hexiaodetail']=$sqlhx->execute('alternate')->as_array();
                    // $solded[$i]['soldednum']=ORM::factory("qdbtrade")->where("goodid","=",$goodid[$i])->count_all('buynum');
                    // $solded[$i]['usednum']=ORM::factory("qdbtrade")->where("goodid","=",$goodid[$i])->where("isused","=",2)->count_all();
                    $mysql_server_name="rds47z172hu2m8vci749private.mysql.rds.aliyuncs.com";
                    $mysql_username="smfyun";
                    $mysql_pwd="emg4h2q";
                    $mysql_database='smfyun';
                    $conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_pwd)or die("error connecting");
                    mysql_query("set names 'utf8'");
                    mysql_select_db($mysql_database,$conn) or die(mysql_error($conn));

                    $tempresult=mysql_fetch_row(mysql_query("select sum(buynum) from qdb_trades where goodid='$goodid'and isused=2"));
                    $solded[$i]['usednum']=0;
                    $hexiao[$i]['hexiaonum']=0;
                    if($tempresult[0])
                    {
                    $solded[$i]['usednum']=$tempresult[0];
                    $hexiao[$i]['hexiaonum']=$tempresult[0];
                    }
                     $tempresult=mysql_fetch_row(mysql_query("select sum(buynum) from qdb_trades where goodid='$goodid'"));
                    $solded[$i]['soldednum']=$tempresult[0];

                }



            }
            else
                {
                    $setgoods=null;
                    $solded=null;
                    $hexiao=null;
                }

        }
        $result=array('config'=>$config,'setgoods'=>$setgoods,"soldeds"=>$solded,"hexiao"=>$hexiao);
        return $result;
}



function action_flash($goodid=null)//更新数据
{
  if($_POST['goodid'])
  {
    $ischange=false;
    // $nowtime=date("Y-m-d H:i:s",strtotime("-60seconds"));
    $nowtime=date("Y-m-d H:i:s");
    // $trade=ORM::factory("qdbtrade")->where('trade_id',"=",$_POST['tradeid'])->find();
    // $goodid=$trade->goodid;
    $goodid=$_POST['goodid'];
    $data=ORM::factory("qdbcfg")->where("goodid","=",$goodid)->find();
    $start_updatatime=date("Y-m-d H:i:s",strtotime("$data->lastupdatetime-1hours"));
    $buy_id=$data->buyid;
    $data_app=ORM::factory("config")->where("buy_id","=",$buy_id)->find();
    $yappid=$data_app->youzan_appid;
    $yappsecret=$data_app->youzan_appsecret;
    // echo $start_updatatime;
    // echo $appid;

    $client = new KdtApiClient($yappid, $yappsecret);
    $method = 'kdt.trades.sold.get';
    $params = array(
             //'status' => "WAIT_SELLER_SEND_GOODS", //已付款?????
           'page_size' =>100,
           'page_no' =>1 ,
           'use_has_next' => true,
           'start_update' => $start_updatatime, //同步订单
                );
    $traderesults=$client->post($method,$params);

        for($i=0;$traderesults['response']['trades'][$i];$i++)
        {
              $res=$traderesults['response']['trades'][$i];
              if($res['num_iid']=="$goodid"&&($res['status']=="WAIT_BUYER_CONFIRM_GOODS"||$res['status']=="TRADE_BUYER_SIGNED"))
                {
                    $trade_tid=$res['tid'];//该笔交易唯一编号，更新时用作与数据库的对比插入；
                    $code=$res['tid'];
                    $method_code = 'kdt.trade.virtualcode.get';
                    $params_code = [
                        'code'=>$code,
                        ];
                    $coderes=$client->post($method_code,$params_code);

                        $qdbdate=ORM::factory("qdbtrade",array("trade_id"=>$trade_tid));
                        if(!$qdbdate->loaded())//该数据不存在，则插入
                            {
                            $ischange=true;
                            $qdbdate->trade_id=$res['tid'];
                            $qdbdate->goodid=$res['num_iid'];
                            $qdbdate->goodname=$res['title'];
                            $qdbdate->buynum=$res['num'];
                            $qdbdate->mobile=$res['orders'][0]['buyer_messages'][0]['content'];
                            $qdbdate->payname=$res['buyer_nick'];
                            $qdbdate->buytime=$res['pay_time'];
                            $qdbdate->buymoney=$res['price'];
                            $qdbdate->prodbuy_id=$buy_id;
                            $qdbdate->isused=$coderes["response"]["status"];
                            if ($coderes["response"]["status"]==2)//为2为已使用
                            $qdbdate->usingtime= $coderes["response"]["use_time"];

                            $qdbdate->save();
                            //echo$qdbdate;
                            }
                        else if($coderes["response"]["status"]==2)//如果数据存在，判断核销转态是否需要更新
                            {
                              $ischange=true;
                               $qdbdate->isused=$coderes["response"]["status"];
                               $qdbdate->usingtime= $coderes["response"]["use_time"];
                               $qdbdate->save();
                              //echo$qdbdate;
                            }
                 }

        }

        $data->lastupdatetime=$nowtime;
        $data->save();
        if($ischange)
        {
            $solded['goodid']=$goodid;
            $hexiao['goodid']=$goodid;

            $solded['name']=$data->goodname;
            $hexiao['name']=$data->goodname;

            $sql=DB::query(Database::SELECT,"SELECT *FROM qdb_trades where goodid=$goodid order by buytime desc");
            $sqlhx=DB::query(Database::SELECT,"SELECT *from qdb_trades where goodid=$goodid and isused =2 order by usingtime desc");

            $solded['tradedetail']=$sql->execute('alternate')->as_array();
            $hexiao['hexiaodetail']=$sqlhx->execute('alternate')->as_array();

            $solded['tradesnum']=ORM::factory("qdbtrade")->where("goodid","=",$goodid)->count_all();
            $mysql_server_name="rds47z172hu2m8vci749private.mysql.rds.aliyuncs.com";
            $mysql_username="smfyun";
            $mysql_pwd="emg4h2q";
            $mysql_database='smfyun';
            $conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_pwd)or die("error connecting");
            mysql_query("set names 'utf8'");
            mysql_select_db($mysql_database,$conn) or die(mysql_error($conn));
            $tempresult=mysql_fetch_row(mysql_query("select sum(buynum) from qdb_trades where goodid='$goodid'and isused=2"));
            $solded['usednum']=0;
            $hexiao['hexiaonum']=0;
            if($tempresult[0])
            {
            $solded['usednum']=$tempresult[0];
            $hexiao['hexiaonum']=$tempresult[0];
            }
            $tempresult=mysql_fetch_row(mysql_query("select sum(buynum) from qdb_trades where goodid='$goodid'"));
            $solded['soldednum']=$tempresult[0];
            if($_POST['divnum']<500)//div的id起始值为pagination-X小于500的为销售
            {
            echo json_encode($solded);
           }
           else
            echo json_encode($hexiao);
      }
      else {
       echo json_encode (array('nochange'=>1));
      }
      exit();

  }


}


function gengxin($goodid)
{
    $nowtime=date("Y-m-d H:i:s");
    $data=ORM::factory("qdbcfg")->where("goodid","=",$goodid)->find();
    $start_updatatime=date("Y-m-d H:i:s",strtotime("$data->lastupdatetime-1hours"));
    $buy_id=$data->buyid;
    $data_app=ORM::factory("config")->where("buy_id","=",$buy_id)->find();
    $yappid=$data_app->youzan_appid;
    $yappsecret=$data_app->youzan_appsecret;
    $client = new KdtApiClient($yappid, $yappsecret);
    $method = 'kdt.trades.sold.get';
    $params = array(
           'page_size' =>100,
           'page_no' =>1 ,
           'use_has_next' => true,
           'start_update' => $start_updatatime, //同步订单
                );
    $traderesults=$client->post($method,$params);

        for($i=0;$traderesults['response']['trades'][$i];$i++)
        {
              $res=$traderesults['response']['trades'][$i];
              if($res['num_iid']=="$goodid"&&($res['status']=="WAIT_BUYER_CONFIRM_GOODS"||$res['status']=="TRADE_BUYER_SIGNED"))
                {
                    $trade_tid=$res['tid'];//该笔交易唯一编号，更新时用作与数据库的对比插入；
                    $code=$res['tid'];
                    $method_code = 'kdt.trade.virtualcode.get';
                    $params_code = [
                        'code'=>$code,
                        ];
                    $coderes=$client->post($method_code,$params_code);

                        $qdbdate=ORM::factory("qdbtrade",array("trade_id"=>$trade_tid));
                        if(!$qdbdate->loaded())//该数据不存在，则插入
                            {
                            $ischange=true;
                            $qdbdate->trade_id=$res['tid'];
                            $qdbdate->goodid=$res['num_iid'];
                            $qdbdate->goodname=$res['title'];
                            $qdbdate->buynum=$res['num'];
                            $qdbdate->mobile=$res['orders'][0]['buyer_messages'][0]['content'];
                            $qdbdate->payname=$res['buyer_nick'];
                            $qdbdate->buytime=$res['pay_time'];
                            $qdbdate->buymoney=$res['price'];
                            $qdbdate->prodbuy_id=$buy_id;
                            $qdbdate->isused=$coderes["response"]["status"];
                            if ($coderes["response"]["status"]==2)//为2为已使用
                            $qdbdate->usingtime= $coderes["response"]["use_time"];

                            $qdbdate->save();
                            //echo$qdbdate;
                            }
                        else if($coderes["response"]["status"]==2)//如果数据存在，判断核销转态是否需要更新
                            {
                              $ischange=true;
                               $qdbdate->isused=$coderes["response"]["status"];
                               $qdbdate->usingtime= $coderes["response"]["use_time"];
                               $qdbdate->save();
                              //echo$qdbdate;
                            }
                 }

        }

        $data->lastupdatetime=$nowtime;
        $data->save();

}


}
?>
