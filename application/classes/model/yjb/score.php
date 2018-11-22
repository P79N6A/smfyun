<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言积分明细
class Model_Yjb_Score extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'party'  => array('model' => 'yjb_party', 'foreign_key' => 'pid'), //订单
        'qrcode'  => array('model' => 'yjb_qrcode', 'foreign_key' => 'qid'), //订单
    );
    public function getTypeName($id)
    {
        $types = array('活动入场费','活动打赏','匿名打赏','个人提现','手续费');
        return $types[$id];
    }
    //用户obj、类别、分数、推荐用户id
    public function scoreIn($user, $type, $score, $rid=0,$pid=0)
    {
        $qid=$user->id;
        $data['qid'] = $qid;
        $data['type'] = $type;
        $data['pid'] = $pid;
        $data['score'] = $score;
        $data['rid'] = $rid;
        if($score>0||$type==4){
            $data['paydate'] = time() + Date::DAY*7;
        }
        $this->values($data);
        //更新余额
        $user->score += $score;
        $user->save();
        $this->save();
        $score=ORM::factory('yjb_score')->where('id','=',$this->id)->find();
        $all_money=DB::query(Database::SELECT,"SELECT SUM(score) as allmoney from yjb_scores where qid = $qid")->execute()->as_array();
        $all_money=$all_money[0]['allmoney'];
        $score->allmoney=$all_money;
        $score->save();
        return $score->id;
    }

    public function scoreOut($user, $type=0, $score)
    {
        // if ($type == 0) $type = 4;
        return $this->scoreIn($user, $type, 0-$score);
    }

}
