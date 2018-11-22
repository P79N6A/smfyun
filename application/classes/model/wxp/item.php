<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_wxp_item extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_update_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

}
