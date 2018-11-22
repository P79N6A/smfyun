<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言积分明细
class Model_ytb_Score extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'trade'  => array('model' => 'ytb_trade', 'foreign_key' => 'tid'), //订单
        'qrcode'  => array('model' => 'ytb_qrcode', 'foreign_key' => 'qid'), //订单
    );

    public function getTypeName($id)
    {
        $types = array('自购奖励', '直接推荐购买奖励', '间接推荐购买奖励','鱼苗修改','鱼苗消耗');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id
    public function scoreIn($user, $type, $score, $rid=0,$tid=0)
    {
        // if (Request::$client_ip == '180.150.190.133') return false;
        // if (Request::$client_ip == '180.150.190.132') return false;
        // if (Request::$client_ip == '120.132.64.179') return false;
        // if (Request::$client_ip == '120.132.64.178') return false;

        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['type'] = $type;
        $data['tid'] = $tid;
        $data['score'] = $score;
        $data['rid'] = $rid;

        $this->values($data);

        //更新余额
        $user->score += $score;
        $user->all_score += $score;
        $user->save();

        return $this->save();
    }

    public function scoreOut($user, $type=0, $score)
    {
        if ($type == 0) $type = 4;
        return $this->scoreIn($user, $type, 0-$score);
    }

}
