<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_wzb_Login extends ORM {

    protected $_created_column = array(
        'column'    => 'creatdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
