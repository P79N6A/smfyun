<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Yyx_Location extends ORM {
    // var $_table_name = 'test';

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
