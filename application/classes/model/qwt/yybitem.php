<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_qwt_yybItem extends ORM {
    protected $_table_name = 'qwt_yybitems';
	protected $_primary_key = 'id';

    protected $_belongs_to = array(
        'order'  => array('model' => 'qwt_yyborder', 'foreign_key' => 'oid'),
        'qrcode'  => array('model' => 'qwt_yybqrcode', 'foreign_key' => 'qid'),
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
