<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_Yty_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'yty_score', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'yty_trade', 'foreign_key' => 'qid'),
        'stocks' => array('model' => 'yty_stock', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'agent' => array('model' => 'yty_agent', 'foreign_key' => 'aid'),
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
