<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Rwbrecord extends ORM {

    // Relationships


protected $_belongs_to = array(
        'task'  => array('model' => 'Qwt_rwbtask', 'foreign_key' => 'tid'),
        'user'  => array('model' => 'Qwt_rwbqrcode', 'foreign_key' => 'qid'),
    );
}
