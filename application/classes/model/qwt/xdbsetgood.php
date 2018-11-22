<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_qwt_xdbSetgood extends ORM {
 protected $_created_column = array(
        'column'    => 'createtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
