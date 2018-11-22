<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_User_Config_String extends Controller_User_Home {
    public function before()
    {
        parent::before();
        require Kohana::find_file("vendor/code","CommonHelper");
    }
    public function action_index(){

        $view=View::factory("user/config/string");
        $id=Request::instance()->param("id");
        $buy=ORM::factory("buy",array("buy_id"=>$id));
        $product=ORM::factory("product",array("product_id"=>$buy->product_id));
        //$timeover=date("Y-m-d",strtotime("$buy->buy_time+$product->time_over month"));

        //$exprie=date("Y-m-d H:i:s",strtotime("+ 1 month",$update));//过期时间
        $exprie=$buy->expiretime;//过期时间
         //获取设备session
        $device=Session::instance()->get("agent");
        //$view->set('device', $device);
        $this->template->product_buy_id=$id;
        $view->set("time",$buy->expiretime);
        $view->set("id",$id);
        $this->template->content=$view;


    }
    public function action_string(){
        if($_POST['num']!==""&&$_POST['num2']!==""&&$_POST["type"]!==""){
        $type=$_POST["type"];
        $num=$_POST['num'];
        $nums=$_POST['num2'];
        if(file_exists("result.csv"))
        {
            @unlink("result.csv");
        }
        $file="result.csv";
        $fh=fopen($file,"w+");
        // if($_POST['content']!==""){
        //     if(strlen($_POST['content']>$num)){
        //         echo "<script>history.back();alert('自定义内容过长');</script>";
        //         return;
        //     }
        // }
        if($type=="0"){
            for($i=0;$i<$nums;$i++){
                $str=$this->createRandom($num,0);
                fputcsv($fh, array($str));
            }
        }
        else if($type=="1")
        {
            if($_POST['content']!==""){
                for($i=0;$i<$nums;$i++){
                    if($num-strlen($_POST['content'])==0){
                        $str=$_POST['content'];
                    }
                    else
                    {
                        $str=$_POST['content'].$this->createRandom($num-strlen($_POST['content']),1);
                    }
                    fputcsv($fh, array($str));
                }
            }
            else
            {
                for($i=0;$i<$nums;$i++){
                    $str=$this->createRandom($num,1);
                    fputcsv($fh, array($str));
                }
            }
        }
        else
        {
            if($_POST['content']!==""){
                for($i=0;$i<$nums;$i++){
                    if($num-strlen($_POST['content'])==0){
                        $str=$_POST['content'];
                    }
                    else if($num-strlen($_POST['content'])==1){
                        $str=$_POST['content'].$this->createRandom(1,0);
                    }
                    else
                    {
                        $rand=rand(1,$num-strlen($_POST['content'])-1);
                        $str=$_POST['content'].$this->createRandom($rand,0).$this->createRandom($num-strlen($_POST['content'])-$rand,1);
                    }
                    fputcsv($fh, array($str));
                }
            }
            else
            {
                for($i=0;$i<$nums;$i++){
                    $rand=rand(1,$num-1);
                    $str=$this->createRandom($rand,0).$this->createRandom($num-$rand,1);
                    fputcsv($fh, array($str));
                }
            }
        }
        fclose($fh);
        $value=fopen($file,'r+');
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( 'Content-Disposition:attachment;filename="' . $file . '"' );
        header ( "Content-Transfer-Encoding: binary" );
        echo fread($value,filesize($file));
        fclose($value);
        @unlink($file);
        exit;
    }
    else
    {
        echo '<script>history.back()</script>';
        exit;
    }

    }
    public function createRandom($num,$type){
        $str="";
        for($i=0;$i<$num;$i++){
            if($type==0){
                $str.=rand(0,9);
            }
            else
            {
                $str.=chr(rand(97,122));
            }
        }
        return $str;
    }
}
