<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_qwt_dldScore extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'trade'  => array('model' => 'qwt_dldtrade', 'foreign_key' => 'tid'), //订单
        'qrcode'  => array('model' => 'qwt_dldqrcode', 'foreign_key' => 'qid'), //订单
    );

    public function getTypeName($id)
    {
        $types = array(0=>'团队奖励', 1=>'销售利润', 2=>'手动企业付款结算奖励',3=>'手动结算奖励',4=>'系统自动企业付款结算', 5=>'手动企业付款结算利润',6=>'手动结算利润');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id、相关订单流水号
    public function scoreIn($user, $type, $score, $rid=0, $tid=0,$bz='')
    {
        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['type'] = $type;
        $data['score'] = $score;
        $data['bz']=$bz;
        $data['rid'] = $rid;
        $data['tid'] = $tid;

        //结算时间：T+10
        $trade = ORM::factory('qwt_dldtrade', $tid);
        if ($trade->id) {
            $data['paydate'] = strtotime($trade->pay_time) + Date::DAY*7;
            $data['money'] = $trade->money;
        }
        $this->values($data);
        //更新余额
        $user->score += $score;
        $user->save();

        return $this->save();
    }

    public function scoreOut($user, $type=0, $score, $rid=0, $tid=0,$bz='')
    {
        if ($type == 0) $type = 4;
        return $this->scoreIn($user, $type, 0-$score, $rid, $tid,$bz);
    }

}
