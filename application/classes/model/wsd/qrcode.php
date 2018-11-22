<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_wsd_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'wsd_score', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'wsd_trade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'skus' => array('model' => 'wsd_sku', 'foreign_key' => 'sid'),
       'suites' => array('model' => 'wsd_suite', 'foreign_key' => 'group_id'),
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
