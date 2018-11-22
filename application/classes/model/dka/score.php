<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言积分明细
class Model_Dka_Score extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    public function getTypeName($id)
    {
        $types = array('积分收入', '首次关注积分', '好友打卡积分', '间接打卡积分', '积分消费','打卡积分','连续打卡奖励','连续打卡积分','金蛋奖励','摇一摇奖励','积分抽奖消耗');
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
        if($type==5||$type==6||$type==7||$type==8||$type==9||$type==10){
            $data['date'] = date('y-m-d',time());
        }

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
