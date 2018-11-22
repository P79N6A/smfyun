<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_qd_Order extends ORM {
    // Relationships
    protected $_belongs_to = array(
        'user'  => array('model' => 'qd_qrcode', 'foreign_key' => 'qid'),
        'item'  => array('model' => 'qd_item', 'foreign_key' => 'iid'),
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
