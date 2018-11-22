<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Kmi_Item extends ORM {
    protected $_table_name = 'kmi_items';
	protected $_primary_key = 'id';

protected $_belongs_to = array(
        'prize'  => array('model' => 'kmi_prize', 'foreign_key' => 'pid'),
    );
}
