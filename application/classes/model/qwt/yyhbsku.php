<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Yyhbsku extends ORM {

    // Relationships


    protected $_belongs_to = array(
        'task'  => array('model' => 'Qwt_yyhbtask', 'foreign_key' => 'tid'),
        'item'  => array('model' => 'Qwt_yyhbitem', 'foreign_key' => 'iid'),
    );
}
