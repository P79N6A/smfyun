<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_Qwt_Kjborder extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_belongs_to = array(
        // 'task'  => array('model' => 'Qwt_kjbtask', 'foreign_key' => 'tid'),
        'user'  => array('model' => 'Qwt_kjbqrcode', 'foreign_key' => 'qid'),
        // 'sku'   => array('model' => 'Qwt_kjbsku', 'foreign_key' => 'kid'),
        'item'  => array('model' => 'Qwt_kjbitem', 'foreign_key' => 'iid'),
    );

}
