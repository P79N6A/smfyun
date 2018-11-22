<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_Dka_detail extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'trade'  => array('model' => 'dka_trade', 'foreign_key' => 'tid'), //订单
        'qrcode'  => array('model' => 'dka_qrcode', 'foreign_key' => 'qid'), //订单
    );

    public function getTypeName($id)
    {
        $types = array(0=>'收入', 1=>'购买奖励', 2=>'直接推广收益', 3=>'间接推广收益', 4=>'提现', 5=>'支出', 6=>'关注奖励', 7=>'扫码奖励', 8=>'间接扫码奖励');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id、相关订单流水号
    public function cashIn($user, $type, $cash, $rid=0, $tid=0)
    {
        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['type'] = $type;
        $data['cash'] = $cash;

        $data['rid'] = $rid;
        $data['tid'] = $tid;

        //结算时间：T+10
        $trade = ORM::factory('dka_trade', $tid);
        if ($trade->id) {
            $data['paydate'] = strtotime($trade->pay_time) + Date::DAY*7;
            $data['money'] = $trade->money;
        }

        $this->values($data);

        //更新余额
        $user->cash += $cash;
        $user->save();

        return $this->save();
    }

    public function cashOut($user, $type=0, $cash, $rid=0, $tid=0)
    {
        if ($type == 0) $type = 4;
        return $this->cashIn($user, $type, 0-$cash, $rid, $tid);
    }

}