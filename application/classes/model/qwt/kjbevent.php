<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Kjbevent extends ORM {

    // Relationships
   protected $_belongs_to = array(
        // 'task'  => array('model' => 'Qwt_kjbtask', 'foreign_key' => 'tid'),
        'qrcode'  => array('model' => 'Qwt_kjbqrcode', 'foreign_key' => 'qid'),
        // 'sku'   => array('model' => 'Qwt_kjbsku', 'foreign_key' => 'kid'),
        'item'  => array('model' => 'Qwt_kjbitem', 'foreign_key' => 'iid'),
    );
   protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

// protected $_belongs_to = array(
//         'user'  => array('model' => 'Qwt_kjbqrcode', 'foreign_key' => 'qid'),
//         'item'  => array('model' => 'Qwt_kjbitem', 'foreign_key' => 'iid'),
//     );

}
