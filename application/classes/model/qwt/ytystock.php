<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_qwt_ytyStock extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'fqrcode'  => array('model' => 'qwt_ytyqrcode', 'foreign_key' => 'fqid'), //订单
        'qrcode'  => array('model' => 'qwt_ytyqrcode', 'foreign_key' => 'qid'), //订单
    );
        public function getTypeName($id)
    {
        $types = array(0=>'经销商代理金', 1=>'申请进货额', 2=>'出货扣除进货额', 3=>'订单扣除进货额', 4=>'下级升级奖励金额额', 5=>'前下级进货奖励进货额',6=>'订单退款退回进货额');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id、相关订单流水号
    public function stockIn($user, $type, $money, $fqid=0, $flag=0)
    {
        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['type'] = $type;
        $data['money'] = $money;
        $data['fqid'] = $fqid;
        $data['flag'] = $flag;
        //更新余额
        if($flag==1){
            $agent=$user->agent;
            $agent->stock += $money;
            $agent->save();
            $agent=$user->agent;
            $data['money_all'] =  $agent->stock;
        }
        $this->values($data);
        return $this->save();
    }
    public function stockOut($user, $type=0, $money, $fqid=0, $flag=0)
    {
        if ($type == 0) $type = 4;
        return $this->stockIn($user, $type, 0-$money, $fqid, $flag);
    }
}
