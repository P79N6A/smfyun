<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Xcx_order extends ORM {
    protected $_table_name = 'xcxorder';
    protected $_primary_key = 'id';
    protected $_belongs_to = array(
        'ordersku' => array('model' => 'xcx_sku', 'foreign_key' => 'attach'),
    );
}
