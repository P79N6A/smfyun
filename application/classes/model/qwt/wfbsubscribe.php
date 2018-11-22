<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Wfbsubscribe extends ORM {
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
