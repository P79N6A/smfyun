<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_wzb_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'wzb_score', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'wzb_trade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'skus' => array('model' => 'wzb_sku', 'foreign_key' => 'sid'),
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
