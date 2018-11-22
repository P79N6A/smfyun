<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_qwt_dkaQrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'qwt_dkascore', 'foreign_key' => 'qid'),
        'details' => array('model' => 'qwt_dkadetail', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'qwt_dkatrade', 'foreign_key' => 'qid'),
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
