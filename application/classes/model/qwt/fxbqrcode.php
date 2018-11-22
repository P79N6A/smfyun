<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_Qwt_Fxbqrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'qwt_fxbscore', 'foreign_key' => 'qid'),
        'shscores' => array('model' => 'qwt_fxbshscore', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'qwt_fxbtrade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
        'sku'  => array('model' => 'qwt_fxbsku', 'foreign_key' => 'sid'), //订单
    );
    protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );

    public function save() {
        //if (!$this->ip) $this->ip = Request::$client_ip;
        return parent::save();
    }
}
