<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_flb_Qrcode extends ORM {

     protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );


}
