<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Yyb_Qrcode extends ORM {
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'order'  => array('model' => 'yyb_order', 'foreign_key' => 'oid'),
    );
    protected $__has_many= array(
        'item'  => array('model' => 'yyb_item', 'foreign_key' => 'qid'),
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    public function save() {
        return parent::save();
    }

}
