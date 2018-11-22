<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Qwt_Hbycron extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

}
