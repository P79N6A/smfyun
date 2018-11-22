<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_mnbbind extends ORM {

    protected $_created_column = array(
        'column'    => 'createtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    // protected $_has_many = array(
    //     'qrcode'  => array('model' => 'qwt_mnbqrcode', 'foreign_key' => 'lv')
    // );
}
