<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Sjb_Tid extends ORM {

    // Relationships
    // protected $_has_many = array(
    //     'scores' => array('model' => 'wdy_score', 'foreign_key' => 'qid'),
    // );
    protected $_belongs_to = array(
        'user'  => array('model' => 'sjb_qrcode', 'foreign_key' => 'qid'),
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
