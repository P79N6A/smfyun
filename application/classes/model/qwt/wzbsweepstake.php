<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_qwt_wzbsweepstake extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'qwt_wzbqrcode', 'foreign_key' => 'qid'),
        'item'  => array('model' => 'qwt_wzblottery', 'foreign_key' => 'iid'),
    );
}
