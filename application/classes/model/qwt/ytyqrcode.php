<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_qwt_ytyQrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        'scores' => array('model' => 'qwt_ytyscore', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'qwt_ytytrade', 'foreign_key' => 'qid'),
        'stocks' => array('model' => 'qwt_ytystock', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'agent' => array('model' => 'qwt_ytyagent', 'foreign_key' => 'aid'),
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
