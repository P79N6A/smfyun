<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_smfyun_score extends ORM {
    protected $_table_name = 'smfyun_score';
    protected $_primary_key = 'id';

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    public function getTypeName($id){
        $types = array(0=>'普通上榜加分', 1=>'连续两天上榜加分', 2=>'连续三天上榜加分');
        return $types[$id];
    }
    public function scoreIn($shop, $score,$type,$date){
        $this->sid = $shop->id;
        $this->type = $type;
        $this->score = $score;
        $this->date = $date;
        //更新余额
        $shop->score += $score;
        $shop->save();

        return $this->save();
    }
}
