<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_Fxb_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'fxb_score', 'foreign_key' => 'qid'),
        'shscores' => array('model' => 'fxb_shscore', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'fxb_trade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
        'sku'  => array('model' => 'fxb_sku', 'foreign_key' => 'sid'), //订单
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
