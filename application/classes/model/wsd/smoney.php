<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_wsd_smoney extends ORM {
   protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
   );
   protected $_updated_column = array(
       'column'    => 'lastupdate',
       'format'    => TRUE,
   );
   protected $_belongs_to = array(
       'suite' => array('model' => 'wsd_suite', 'foreign_key' => 'sid'),
    );
}
