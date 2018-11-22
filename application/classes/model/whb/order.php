<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Whb_Order extends ORM {

    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'user'  => array('model' => 'whb_qrcode', 'foreign_key' => 'qid'),
    );

}
