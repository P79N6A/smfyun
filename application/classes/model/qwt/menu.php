<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_menu extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

}
