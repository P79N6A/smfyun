<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Yyb_Item extends ORM {
    protected $_table_name = 'yyb_items';
	protected $_primary_key = 'id';

    protected $_belongs_to = array(
        'order'  => array('model' => 'yyb_order', 'foreign_key' => 'oid'),
        'qrcode'  => array('model' => 'yyb_qrcode', 'foreign_key' => 'qid'),
    );
    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
