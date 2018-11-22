<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝订单
class Model_qwt_dldTrade extends ORM {

    protected $_belongs_to = array(
        'qrcode'  => array('model' => 'qwt_dldqrcode', 'foreign_key' => 'qid'),
    );

}
