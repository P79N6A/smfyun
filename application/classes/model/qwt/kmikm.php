<?php defined('SYSPATH') or die('No direct access allowed.');


class Model_Qwt_kmiKm extends ORM {

    protected $_table_name = 'qwt_kmikms';
    protected $_primary_key = 'id';
    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );

}
