<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Xdborder extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'task'  => array('model' => 'Qwt_xdbtask', 'foreign_key' => 'tid'),
        'user'  => array('model' => 'Qwt_xdbqrcode', 'foreign_key' => 'qid'),
        'sku'   => array('model' => 'Qwt_xdbsku', 'foreign_key' => 'kid'),
        'item'  => array('model' => 'Qwt_xdbitem', 'foreign_key' => 'iid'),
    );

}
