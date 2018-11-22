<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言奖品
class Model_Myb_Sku extends ORM {
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

