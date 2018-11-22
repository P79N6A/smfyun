<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_dld_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'dld_score', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'dld_trade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'skus' => array('model' => 'dld_sku', 'foreign_key' => 'sid'),
       'suites' => array('model' => 'dld_suite', 'foreign_key' => 'group_id'),
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
