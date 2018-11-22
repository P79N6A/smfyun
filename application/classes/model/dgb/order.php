<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_dgb_Order extends ORM {

    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'dgb_qrcode', 'foreign_key' => 'qid'),
        'item'  => array('model' => 'dgb_brand', 'foreign_key' => 'iid'),
    );

}
