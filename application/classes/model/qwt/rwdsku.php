<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Rwdsku extends ORM {

    // Relationships


    protected $_belongs_to = array(
        'task'  => array('model' => 'Qwt_rwdtask', 'foreign_key' => 'tid'),
        'item'  => array('model' => 'Qwt_rwditem', 'foreign_key' => 'iid'),
    );
}
