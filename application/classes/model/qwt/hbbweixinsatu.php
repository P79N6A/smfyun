<?php defined('SYSPATH') or die('No direct access allowed.');

//口令红包记录表
class Model_Qwt_Hbbweixinsatu extends ORM {
    //自动记录时间
    protected $_created_column = array(
        'column'    => 'createtime',
        'format'    => TRUE,
    );
}
