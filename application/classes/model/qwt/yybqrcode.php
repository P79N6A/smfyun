<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_qwt_yybQrcode extends ORM {
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'order'  => array('model' => 'qwt_yyborder', 'foreign_key' => 'oid'),
    );
    protected $__has_many= array(
        'item'  => array('model' => 'qwt_yybitem', 'foreign_key' => 'qid'),
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    public function save() {
        return parent::save();
    }

}
