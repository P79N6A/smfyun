<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_ytb_Trade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'ytb_qrcode', 'foreign_key' => 'qid'),
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
