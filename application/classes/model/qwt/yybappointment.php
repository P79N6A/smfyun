<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_qwt_yybAppointment extends ORM {
	 protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
