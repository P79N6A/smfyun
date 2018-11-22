<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_bnk_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'bnk_score', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'bnk_trade', 'foreign_key' => 'qid'),
        'orders' => array('model' => 'bnk_order', 'foreign_key' => 'qid'),
    );
    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    public function save() {
        //if (!$this->ip) $this->ip = Request::$client_ip;
        return parent::save();
    }
}
