<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_hfctrade extends ORM {

    // Relationships
    protected $_belongs_to = array(
        'qrcodes'  => array('model' => 'qwt_hfcqrcode', 'foreign_key' => 'qid'),
        'items'  => array('model' => 'qwt_hfcitem', 'foreign_key' => 'iid'),
    );
    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
