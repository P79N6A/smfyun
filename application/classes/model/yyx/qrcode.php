<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Yyx_Qrcode extends ORM {

    // Relationships
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
