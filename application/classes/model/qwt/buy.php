<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言积分明细
class Model_Qwt_Buy extends ORM {

    protected $_belongs_to = array(
        'item'  => array('model' => 'qwt_item', 'foreign_key' => 'iid'),
        'login'  => array('model' => 'qwt_login', 'foreign_key' => 'bid'),
    );
    protected $_created_column = array(
        'column'    => 'buy_time',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

}
