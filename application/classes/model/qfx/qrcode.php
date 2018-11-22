<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_Qfx_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'qfx_score', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'qfx_trade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'skus' => array('model' => 'qfx_sku', 'foreign_key' => 'sid'),
       'groups' => array('model' => 'qfx_group', 'foreign_key' => 'group_id'),
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
