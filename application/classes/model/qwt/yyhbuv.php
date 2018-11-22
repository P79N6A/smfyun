<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Yyhbuv extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    // protected $_updated_column = array(
    //     'column'    => 'lastupdate',
    //     'format'    => TRUE,
    // );

    // protected $_belongs_to = array(
    //     'task'  => array('model' => 'Qwt_yyhbtask', 'foreign_key' => 'tid'),
    //     'user'  => array('model' => 'Qwt_yyhbqrcode', 'foreign_key' => 'qid'),
    //     'sku'   => array('model' => 'Qwt_yyhbsku', 'foreign_key' => 'kid'),
    //     'item'  => array('model' => 'Qwt_yyhbitem', 'foreign_key' => 'iid'),
    // );

}
