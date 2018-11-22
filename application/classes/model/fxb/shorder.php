<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Fxb_Shorder extends ORM {

    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'item'  => array('model' => 'fxb_item', 'foreign_key' => 'iid'),
        'user'  => array('model' => 'fxb_qrcode', 'foreign_key' => 'qid'),
    );

}
