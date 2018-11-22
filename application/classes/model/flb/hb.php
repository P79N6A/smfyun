<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_flb_hb extends ORM {
    // Relationships
    protected $_belongs_to = array(
        'detail'  => array('model' => 'flb_detail', 'foreign_key' => 'did'),
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
     protected $_created_column = array(
    'column'    => 'lastupdate',
    'format'    => TRUE,
    );
}
