<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Qwt_Kmiitem extends ORM {
    protected $_table_name = 'Qwt_kmiitems';
	protected $_primary_key = 'id';

protected $_belongs_to = array(
        'prize'  => array('model' => 'qwt_kmiprize', 'foreign_key' => 'pid'),
    );
}
