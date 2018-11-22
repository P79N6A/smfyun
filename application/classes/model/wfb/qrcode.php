<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Wfb_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'wfb_score', 'foreign_key' => 'qid'),
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
