<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Rwdorder extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'task'  => array('model' => 'Qwt_rwdtask', 'foreign_key' => 'tid'),
        'user'  => array('model' => 'Qwt_rwdqrcode', 'foreign_key' => 'qid'),
        'sku'   => array('model' => 'Qwt_rwdsku', 'foreign_key' => 'kid'),
        'item'  => array('model' => 'Qwt_rwditem', 'foreign_key' => 'iid'),
    );

}
