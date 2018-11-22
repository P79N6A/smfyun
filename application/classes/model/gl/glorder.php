<?php
defined("SYSPATH") OR  die('No direct script access.');

class Model_Glorder extends ORM
{
	protected $_table_name = 'glorders';
	protected $_db='default';

	protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
?>