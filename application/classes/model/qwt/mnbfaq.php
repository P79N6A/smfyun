<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Qwt_mnbfaq extends ORM {

    protected $_created_column = array(
        'column'    => 'createtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'type'  => array('model' => 'qwt_mnbtype', 'foreign_key' => 'tid')
    );
}
