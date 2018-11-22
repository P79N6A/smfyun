<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_qwt_ytyTrade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'qwt_ytyqrcode', 'foreign_key' => 'qid'),
    );

}
