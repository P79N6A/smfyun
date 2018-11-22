<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_Dka_Trade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'dka_qrcode', 'foreign_key' => 'qid'),
    );

}
