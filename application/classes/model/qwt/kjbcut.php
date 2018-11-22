<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_Kjbcut extends ORM {

    // Relationships

   protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

protected $_belongs_to = array(
        'qrcode'  => array('model' => 'Qwt_kjbqrcode', 'foreign_key' => 'qid'),
        'event'  => array('model' => 'Qwt_kjbevent', 'foreign_key' => 'eid'),
    );
}
