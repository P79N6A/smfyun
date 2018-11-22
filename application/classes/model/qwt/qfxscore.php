<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_qwt_QfxScore extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'trade'  => array('model' => 'qwt_qfxtrade', 'foreign_key' => 'tid'), //订单
        'qrcode'  => array('model' => 'qwt_qfxqrcode', 'foreign_key' => 'qid'), //订单
    );

    public function getTypeName($id)
    {
        $types = array(0=>'收入', 1=>'购买奖励', 2=>'直接推广收益', 3=>'间接推广收益', 4=>'提现', 5=>'支出', 6=>'关注奖励', 7=>'扫码奖励', 8=>'间接扫码奖励');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id、相关订单流水号
    public function scoreIn($user, $type, $score, $rid=0, $tid=0)
    {
        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['type'] = $type;
        $data['score'] = $score;

        $data['rid'] = $rid;
        $data['tid'] = $tid;

        //结算时间：T+10
        $trade = ORM::factory('qwt_qfxtrade', $tid);
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

    public function scoreOut($user, $type=0, $score, $rid=0, $tid=0)
    {
        if ($type == 0) $type = 4;
        return $this->scoreIn($user, $type, 0-$score, $rid, $tid);
    }

}
