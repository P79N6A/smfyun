<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Qwt_Hbyrule extends ORM {

    protected $_created_column = array(
        'column'    => 'time',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
