<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言订单
class Model_qwt_yybOrder extends ORM {
    protected $_table_name = 'qwt_yyborders';
    protected $_primary_key = 'id';
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'appointment'  => array('model' => 'qwt_yybappointment', 'foreign_key' => 'aid'),
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    // protected $_has_many = array(
    //     'scores' => array('model' => 'qwt_yybscore', 'foreign_key' => 'qid'),
    // );
    // protected $_belongs_to = array(
    //     'item'  => array('model' => 'wdy_item', 'foreign_key' => 'iid'),
    //     'user'  => array('model' => 'wdy_qrcode', 'foreign_key' => 'qid'),
    // );

}
