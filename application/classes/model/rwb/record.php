<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Rwb_Record extends ORM {

    // Relationships
    protected $_belongs_to = array(
            'task'  => array('model' => 'Rwb_task', 'foreign_key' => 'tid'),
            'user'  => array('model' => 'Rwb_qrcode', 'foreign_key' => 'qid'),
        );
}

