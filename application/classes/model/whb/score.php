<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言积分明细
class Model_Whb_Score extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    public function getTypeName($id)
    {
        $types = array('积分收入', '首次关注积分', '直接推广积分', '间接推广积分', '积分消费','有赞积分收入','有赞积分消耗');
        return $types[$id];
    }

    //用户obj、类别、分数、推荐用户id
    public function scoreIn($user, $type, $score, $rid=0)
    {
        // if (Request::$client_ip == '180.150.190.133') return false;
        // if (Request::$client_ip == '180.150.190.132') return false;
        // if (Request::$client_ip == '120.132.64.179') return false;
        // if (Request::$client_ip == '120.132.64.178') return false;

        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['type'] = $type;
        $data['score'] = $score;
        $data['rid'] = $rid;

        $this->values($data);

        //更新余额
        $user->score += $score;
        $user->save();

        return $this->save();
    }

    public function scoreOut($user, $type=0, $score)
    {
        if ($type == 0) $type = 4;
        return $this->scoreIn($user, $type, 0-$score);
    }

}
