<?php defined('SYSPATH') or die('No direct access allowed.');

//分销宝二维码库
class Model_sns_Qrcode extends ORM {

    // Relationships
    protected $_has_many = array(
        // 'groups' => array('model' => 'sns_group', 'foreign_key' => 'qid'),

    );
    protected $_belongs_to = array(
        'groups' => array('model' => 'sns_group', 'foreign_key' => 'flag'),
    );

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    public function save() {
        //if (!$this->ip) $this->ip = Request::$client_ip;
        return parent::save();
    }
}
