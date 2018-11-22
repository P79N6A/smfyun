<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Qwt_Hbylogin extends ORM {

    protected $_created_column = array(
        'column'    => 'time',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'logins'  => array('model' => 'qwt_hbyqrcode', 'foreign_key' => 'wx_bind'),
        'rules'  => array('model' => 'qwt_hbyrule', 'foreign_key' => 'rid'),
    );
}
