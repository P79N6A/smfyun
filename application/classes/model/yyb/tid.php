<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Yyb_Tid extends ORM {
    protected $_table_name = 'yyb_tids';
    protected $_primary_key = 'id';

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
