<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Yjb_Party extends ORM {

    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'user'  => array('model' => 'Yjb_qrcode', 'foreign_key' => 'qid'),
    );
}
