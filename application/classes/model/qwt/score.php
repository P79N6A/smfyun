<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_qwt_Score extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'rebuy'  => array('model' => 'qwt_rebuy', 'foreign_key' => 'rid'), //订单
        'login'  => array('model' => 'qwt_login', 'foreign_key' => 'bid'), //订单
    );

    public function getTypeName($id)
    {
        $types = array(0=>'增加利润', 1=>'利润结算');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id、相关订单流水号
    public function scoreIn($user, $type, $score, $sid=0,$rid=0,$cbid=0,$bz=''){
        $data['bid'] = $user->id;
        $data['type'] = $type;
        $data['score'] = $score;
        $data['bz']=$bz;
        $data['rid'] = $rid;
        $data['sid'] = $sid;
        $data['cbid'] = $cbid;
        //结算时间：T+10
        $trade = ORM::factory('qwt_rebuy', $rid);
        if ($trade->id) {
            $data['paydate'] = $trade->rebuy_time + Date::DAY*7;
            $data['money'] = $trade->rebuy_price;
            $data['tid'] = $trade->tid;
        }
        $this->values($data);
        return $this->save();
    }
    public function scoreOut($user, $type=0, $score,$sid=0,$rid=0,$cbid=0,$bz=''){
        if ($type == 0) $type = 1;
        return $this->scoreIn($user, $type, 0-$score,$sid=0,$rid,$cbid,$bz);
    }

}
