<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Wfborder extends ORM {

    protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'item'  => array('model' => 'qwt_wfbitem', 'foreign_key' => 'iid'),
        'user'  => array('model' => 'qwt_wfbqrcode', 'foreign_key' => 'qid'),
    );

}
