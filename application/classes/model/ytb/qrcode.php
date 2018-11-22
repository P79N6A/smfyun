<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_ytb_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'ytb_score', 'foreign_key' => 'qid'),
        'shscores' => array('model' => 'ytb_shscore', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'ytb_trade', 'foreign_key' => 'qid'),
    );

    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );

    public function save() {
        //if (!$this->ip) $this->ip = Request::$client_ip;
        return parent::save();
    }
}
