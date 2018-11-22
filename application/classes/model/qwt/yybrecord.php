<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_qwt_yybRecord extends ORM {
    protected $_belongs_to = array(
        'appointment'  => array('model' => 'qwt_appointment', 'foreign_key' => 'aid'),
        'qrcode'  => array('model' => 'qwt_yybqrcode', 'foreign_key' => 'qid'),
    );
    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );
}
