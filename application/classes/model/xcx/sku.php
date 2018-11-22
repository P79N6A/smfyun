<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_xcx_Sku extends ORM {
    protected $_table_name = 'sku';
    protected $_primary_key = 'sku_id';
    protected $_belongs_to = array(
        'product'  => array('model' => 'xcx_product', 'foreign_key' => 'product_id'),
    );
}
