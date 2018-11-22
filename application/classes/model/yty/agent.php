<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_Yty_Agent extends ORM {


    // protected $_belongs_to = array(
    //    'user' => array('model' => 'yty_qrcode', 'foreign_key' => 'aid'),
    // );
    protected $_belongs_to = array(
       'skus' => array('model' => 'yty_sku', 'foreign_key' => 'sid'),
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
