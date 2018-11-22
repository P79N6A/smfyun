<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言商户登录
class Model_whb_Login extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
