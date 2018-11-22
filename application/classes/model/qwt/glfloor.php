<?php

defined('SYSPATH') or die('No direct script access.');

class Model_Qwt_Glfloor extends ORM {
    protected $_table_name = 'qwt_glfloor';
    protected $_primary_key = 'id';
    protected $_db_group='default';
    protected $_belongs_to = array(
        'item'  => array('model' => 'qwt_glitem', 'foreign_key' => 'iid')
    );
}

?>
