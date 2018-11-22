<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Rwb_Sku extends ORM {

    // Relationships


    protected $_belongs_to = array(
        'task'  => array('model' => 'Rwb_task', 'foreign_key' => 'tid'),
        'item'  => array('model' => 'Rwb_item', 'foreign_key' => 'iid'),
    );
}
