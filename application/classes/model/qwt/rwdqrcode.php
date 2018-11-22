<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Rwdqrcode extends ORM {

    // Relationships


    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );
     protected $_belongs_to = array(
        'qrcodes'  => array('model' => 'qwt_qrcode', 'foreign_key' => 'qid'),
    );
    public function save() {
        //if (!$this->ip) $this->ip = Request::$client_ip;
        return parent::save();
    }
}
