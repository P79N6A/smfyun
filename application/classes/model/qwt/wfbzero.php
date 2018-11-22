<?php defined('SYSPATH') or die('No direct access allowed.');

//积分清零
class Model_Qwt_wfbzero extends ORM {

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'zeros' => array('model' => 'qwt_wfbqrcode', 'foreign_key' => 'qid'),
    );
}
