<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_bnk_Order extends ORM {
	protected $_created_column = array(
        'column'    => 'createdtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
     protected $_belongs_to = array(
        'qrcode'  => array('model' => 'bnk_qrcode', 'foreign_key' => 'qid'),
    );

}

