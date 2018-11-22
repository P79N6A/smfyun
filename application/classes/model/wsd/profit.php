<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_wsd_Profit extends ORM {

    // Relationships
    protected $_belongs_to = array(
       'qrcode' => array('model' => 'wsd_qrcode', 'foreign_key' => 'qid'),
    );

    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );
    protected $_updateed_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
