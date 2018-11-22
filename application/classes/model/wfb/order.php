<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Wfb_Order extends ORM {

    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'item'  => array('model' => 'wfb_item', 'foreign_key' => 'iid'),
        'user'  => array('model' => 'wfb_qrcode', 'foreign_key' => 'qid'),
    );

}
