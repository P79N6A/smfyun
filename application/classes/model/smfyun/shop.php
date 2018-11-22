<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_smfyun_shop extends ORM {
    protected $_table_name = 'smfyun_shop';
    protected $_primary_key = 'id';

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}
