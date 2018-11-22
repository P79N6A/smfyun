<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_qwt_QfxTrade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'qwt_qfxqrcode', 'foreign_key' => 'qid'),
    );

}
