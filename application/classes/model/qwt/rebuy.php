<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Rebuy extends ORM {

    // Relationships
    protected $_belongs_to = array(
        'buy'  => array('model' => 'qwt_buy', 'foreign_key' => 'buy_id'),
        'pro'  => array('model' => 'qwt_sku', 'foreign_key' => 'sku_id'),

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
