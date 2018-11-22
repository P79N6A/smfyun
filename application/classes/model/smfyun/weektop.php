<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_smfyun_weektop extends ORM {
    protected $_table_name = 'smfyun_weektops';
    protected $_primary_key = 'id';

    protected $_belongs_to = array(
        'shop'  => array('model' => 'smfyun_shop', 'foreign_key' => 'sid'),
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
