<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Dlsku extends ORM {
	protected $_belongs_to = array(
        'sku'  => array('model' => 'qwt_sku', 'foreign_key' => 'sid'),
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
