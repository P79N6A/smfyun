<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Sku extends ORM {
	protected $_belongs_to = array(
        'item'  => array('model' => 'qwt_item', 'foreign_key' => 'iid'),

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
