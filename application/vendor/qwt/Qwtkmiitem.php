<?php
class Qwtkmiitem{
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
        $this->config=ORM::factory('qwt_kmicfg')->getCfg($bid,1);
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
            ORM::factory('qwt_kmiitem')->where('bid', '=', $bid)->where('num_iid','=',$item_id)->delete_all();
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
            $num_iid=$item['item_id'];
            $name=$item['title'];
            $price=$item['price']/100;
            $pic=$item['pic_thumb_url'];
            $url=$item['detail_url'];
            $num=$item['quantity'];
            $sold_num=$item['sold_num'];
            $num_num = ORM::factory('qwt_kmiitem')->where('num_iid', '=', $num_iid)->count_all();
            if($num_num==0 && $num_iid){
                $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_kmiitems` (`bid`,`num_iid`,`name`,`price`, `pic`,`num`,`sold_num`,`state`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic',$num,$sold_num,0)");
                $sql->execute();
            }else{
                $sql = DB::query(Database::UPDATE,"UPDATE `qwt_kmiitems` SET `bid` = $bid ,`num_iid` = $num_iid,`name` ='$name',`price`=$price, `pic`='$pic',`num`=$num,`sold_num`=$sold_num ,`state` = 0 where `num_iid` = $num_iid");
                $sql->execute();
            }
        }
    }
}
