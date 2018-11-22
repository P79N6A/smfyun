<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Wdy_lab extends ORM {

    // Relationships
    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'wdy_qrcode', 'foreign_key' => 'qid')
    );
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
