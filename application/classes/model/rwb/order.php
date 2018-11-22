<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Rwb_Order extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'task'  => array('model' => 'Rwb_task', 'foreign_key' => 'tid'),
        'user'  => array('model' => 'Rwb_qrcode', 'foreign_key' => 'qid'),
        'sku'   => array('model' => 'Rwb_sku', 'foreign_key' => 'kid'),
        'item'  => array('model' => 'Rwb_item', 'foreign_key' => 'iid'),
    );

}
