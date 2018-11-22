<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Yty_Order extends ORM {
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
