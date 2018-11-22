<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Xdbscore extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    public function scoreIn($user,$num,$rid=0){
        $data['bid'] = $user->bid;
        $data['qid'] = $user->id;
        $data['num'] =$num;
        $data['rid'] = $rid;
        $this->values($data);
        //更新余额
        $user->score += $num;
        $user->save();
        $this->save();
        Kohana::$log->add("xdbscore$user->id",$user->score);
        return $this->id;
    }
    public function scoreOut($user, $num)
    {
        return $this->scoreIn($user, 0-$num);
    }
}
