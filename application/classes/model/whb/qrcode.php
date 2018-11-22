<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_whb_Qrcode extends ORM {

    // Relationships
    // protected $_has_many = array(
    //     'scores' => array('model' => 'whb_score', 'foreign_key' => 'qid'),
    // );

    protected $_belongs_to = array(
        'qr'  => array('model' => 'whb_qr', 'foreign_key' => 'from_qr'),
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
