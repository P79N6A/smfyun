<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_flb_Detail extends ORM {
    // Relationships
    protected $_belongs_to = array(
        'order'  => array('model' => 'flb_order', 'foreign_key' => 'oid'),
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
