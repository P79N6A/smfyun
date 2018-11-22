<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_qwt_dkaTrade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'qwt_dkaqrcode', 'foreign_key' => 'qid'),
    );

}
