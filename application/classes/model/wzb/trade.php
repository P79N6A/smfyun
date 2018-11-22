<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_wzb_Trade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'wzb_qrcode', 'foreign_key' => 'qid'),
    );

}
