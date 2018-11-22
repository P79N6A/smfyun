<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_bnk_Trade extends ORM {
	protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'bnk_qrcode', 'foreign_key' => 'qid'),
        'order'  => array('model' => 'bnk_order', 'foreign_key' => 'oid'),
    );

}
