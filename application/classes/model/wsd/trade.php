<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_wsd_Trade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'wsd_qrcode', 'foreign_key' => 'qid'),
    );

}
