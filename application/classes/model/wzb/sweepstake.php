<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝收益明细
class Model_wzb_sweepstake extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'wzb_qrcode', 'foreign_key' => 'qid'),
        'item'  => array('model' => 'wzb_lottery', 'foreign_key' => 'iid'),
    );
}
