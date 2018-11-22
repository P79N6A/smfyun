<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_bnk_Score extends ORM {

    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'trade'  => array('model' => 'bnk_trade', 'foreign_key' => 'tid'), //订单
        'order'  => array('model' => 'bnk_order', 'foreign_key' => 'oid'), //订单
        'qrcode'  => array('model' => 'bnk_qrcode', 'foreign_key' => 'qid'), //订单
    );

    public function getTypeName($id)
    {
        $types = array(0=>'不详', 1=>'账户充值', 2=>'发送红包', 3=>'领取红包', 4=>'红包退款', 5=>'账户提现');
        return $types[$id];
    }
    //用户obj、类别、分数、推荐用户id、相关订单流水号
    public function scoreIn($user, $type, $score, $oid=0, $tid=0,$flag=1)
    {
        $bid=$user->bid;
        $qid=$user->id;
        $data['bid'] = $bid;
        $data['qid'] = $qid;
        $data['type'] = $type;
        $data['score'] = $score;
        $data['oid'] = $oid;
        $data['tid'] = $tid;
        $data['flag'] = $flag;
        //结算时间：T+10
        //$money_sum=DB::query(Database::SELECT,"SELECT SUM(score) as money_sum from bnk_scores where bid =$bid and  qid = $qid")->excute()->as_array();
        $money_sum=DB::query(Database::SELECT,"SELECT SUM(score) as money_sum from bnk_scores where bid =$bid and  qid = $qid")->execute()->as_array();
        $money_sum=$money_sum[0]['money_sum']+$score;
        $data['money']=$money_sum;
        $this->values($data);
        //更新余额
        $user->score = $money_sum;
        //$user->score += $score;
        $user->save();
        return $this->save();
    }
    public function scoreOut($user, $type=0, $score, $oid=0, $tid=0,$flag=1){
        return $this->scoreIn($user, $type, 0-$score, $oid, $tid,$flag);
    }

}
