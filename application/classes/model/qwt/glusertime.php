<?php
defined("SYSPATH") OR  die('No direct script access.');

class Model_Qwt_Glusertime extends ORM
{
	protected $_table_name = 'qwt_glusertimes';
	protected $_db='default';

	protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
?>