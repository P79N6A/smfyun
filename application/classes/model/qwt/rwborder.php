<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Rwborder extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        'task'  => array('model' => 'Qwt_rwbtask', 'foreign_key' => 'tid'),
        'user'  => array('model' => 'Qwt_rwbqrcode', 'foreign_key' => 'qid'),
        'sku'   => array('model' => 'Qwt_rwbsku', 'foreign_key' => 'kid'),
        'item'  => array('model' => 'Qwt_rwbitem', 'foreign_key' => 'iid'),
    );

}
