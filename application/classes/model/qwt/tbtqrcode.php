<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_qwt_tbtQrcode extends ORM {


    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );

    public function save() {
        //if (!$this->ip) $this->ip = Request::$client_ip;
        return parent::save();
    }
}
