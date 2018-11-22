<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_Qwt_ytyAgent extends ORM {


    // protected $_belongs_to = array(
    //    'user' => array('model' => 'qwt_ytyqrcode', 'foreign_key' => 'aid'),
    // );
    protected $_belongs_to = array(
       'skus' => array('model' => 'qwt_ytysku', 'foreign_key' => 'sid'),
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
