<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_qwt_wzbQrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        // 'scores' => array('model' => 'qwt_wzbscore', 'foreign_key' => 'qid'),
        'trades' => array('model' => 'qwt_wzbtrade', 'foreign_key' => 'qid'),
    );
    protected $_belongs_to = array(
       'skus' => array('model' => 'qwt_wzbsku', 'foreign_key' => 'sid'),
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
