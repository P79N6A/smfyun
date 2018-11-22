<?php
class Qwtdlditem{
    public $methodVersion='3.0.0';
    public $bid;
    public $msg;
    public $yzaccess_token;
    public $config;
    public $status;
    var $wx;
    var $client;
    var $smfy;
    public function __construct($bid,$msg,$status) {
        Kohana::$log->add("bid", print_r($bid, true));
        Kohana::$log->add("msg", print_r($msg, true));
        require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $bid;
        $this->msg = $msg;
        $this->status=$status;
        $this->smfy=new SmfyQwt();
        $this->yzaccess_token=ORM::factory('qwt_login')->where('id', '=', $this->bid)->find()->yzaccess_token;
        if(!$this->yzaccess_token) throw new Exception('请授权有赞');
        $this->config=ORM::factory('qwt_dldcfg')->getCfg($bid,1);
        $this->client = new YZTokenClient($this->yzaccess_token);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
           $this->wx = new Wxoauth($this->bid,$options); 
        }
    }
    public function itempush(){
        $bid=$this->bid;
        $config=$this->config;
        $posttid=urldecode($this->msg);
        $jsona=json_decode($posttid,true);
        $status=$this->status;
        $data=json_decode($jsona['data'],true);
        $item_id=$data['item_id'];
        if ($status=='ITEM_DELETE'||$status=='ITEM_SALE_DOWN') {
            //商品删除和下架
            ORM::factory('qwt_dldsetgood')->where('bid', '=', $bid)->where('num_iid','=',$item_id)->delete_all();
            ORM::factory('qwt_dldgoodsku')->where('bid', '=', $bid)->where('item_id','=',$item_id)->delete_all();
        }elseif($status=='ITEM_SALE_UP'||$status=='SOLD_OUT_PART'||$status=='SOLD_OUT_ALL'||$status=='SOLD_OUT_REVERT'||$status=='ITEM_CREATE'||$status=='ITEM_UPDATE'){
            //部分售罄，全部售罄，售罄恢复
            $method = 'youzan.item.get';
            $params = array(
                 'item_id'=>$item_id,
            );
            $result = $this->client->post($method, '3.0.0', $params, $files);
            Kohana::$log->add('result', print_r($result, true));
            $item=$result['response']['item'];
            Kohana::$log->add('item', print_r($item, true));
            $skus=$item['skus'];
            $type=0;
            $sql = DB::query(Database::UPDATE,"UPDATE `qwt_dldgoodskus` SET `state` = 3  where `item_id` = $item_id");
            $sql->execute();
            if($skus){
                $type=1;
                foreach ($skus as $sku) {
                    $properties_name_json=$sku['properties_name_json'];
                    $msgs=json_decode( $properties_name_json,true);
                    $skutitle='';
                    foreach ($msgs as $msg) {
                        if($skutitle){
                            $skutitle=$skutitle.'/'.$msg['k'].':'.$msg['v'];
                        }else{
                            $skutitle=$msg['k'].':'.$msg['v'];
                        }
                    }
                    $price=$sku['price']/100;
                    $title=$skutitle;
                    $sku_id=$sku['sku_id'];
                    $item_id=$sku['item_id'];
                    $num=$sku['quantity'];
                    // echo $sku_id."<br>";
                    $sku_num = ORM::factory('qwt_dldgoodsku')->where('sku_id', '=', $sku_id)->count_all();
                    // echo $sku_num.'<br>';
                    if($sku_num==0 && $sku_id){
                        // echo "上面<br>";
                        $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_dldgoodskus` (`bid`,`item_id`,`title`,`sku_id`, `price`,`status`,`state`,`num`) VALUES ($bid,$item_id,'$title' ,$sku_id,$price,0,0,$num)");
                        $sql->execute();
                    }else{
                        // echo "下面<br>";
                        $sql = DB::query(Database::UPDATE,"UPDATE `qwt_dldgoodskus` SET `bid` = $bid ,`item_id` = $item_id,`title` ='$title',`sku_id`=$sku_id, `price`=$price,`state` = 0 , `num`= $num where `sku_id` = $sku_id ");
                        $sql->execute();
                    }
                }
            }
            $num_iid=$item['item_id'];
            $name=$item['title'];
            $price=$item['price']/100;
            $pic=$item['pic_thumb_url'];
            $url=$item['detail_url'];
            $num=$item['quantity'];
            $num_num = ORM::factory('qwt_dldsetgood')->where('num_iid', '=', $num_iid)->count_all();
            if($num_num==0 && $num_iid){
                $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_dldsetgoods` (`bid`,`num_iid`,`title`,`price`, `pic`,`url`,`status`,`state`,`type`,`num`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic','$url',0,0,$type,$num)");
                $sql->execute();
            }else{
                $sql = DB::query(Database::UPDATE,"UPDATE `qwt_dldsetgoods` SET `bid` = $bid ,`num_iid` = $num_iid,`title` ='$name',`price`=$price, `pic`='$pic',`url`='$url' ,`num` = $num ,`state` = 0 , `type` =$type where `num_iid` = $num_iid ");
                $sql->execute();
            }
            $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_dldgoodskus` where `state` =3 and `bid` = $bid ");
                    $sql->execute();
                    $sql = DB::query(Database::UPDATE,"UPDATE `qwt_dldgoodskus` SET `state` =0 where `bid` = $bid");
            $sql->execute();
        }
    }
}
