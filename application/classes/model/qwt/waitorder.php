<?php
defined("SYSPATH") OR  die('No direct script access.');

class Model_Qwt_waitorder extends ORM
{

 protected $_created_column = array(
        'column'    => 'createtime',
        'format'    => TRUE,
    );
 protected $_lastupdate_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
?>
