<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_flb_Order extends ORM {
    // Relationships
    protected $_belongs_to = array(
        'user'  => array('model' => 'flb_qrcode', 'foreign_key' => 'qid'),
    );
    protected $_has_many = array(
        'details' => array('model' => 'flb_detail', 'foreign_key' => 'oid'),
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
