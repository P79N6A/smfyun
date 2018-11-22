<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Xcx_Product extends ORM {
    protected $_table_name = 'product';
    protected $_primary_key = 'product_id';
    protected $_has_many = array(
        'sku' => array('model' => 'xcx_sku', 'foreign_key' => 'product_id'),
    );
}
