<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_ywmqrcode extends ORM {

    // Relationships
    // protected $_has_many = array(
    //     'scores' => array('model' => 'qwt_wfbscore', 'foreign_key' => 'qid'),
    // );

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
