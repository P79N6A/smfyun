<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_qwt_dldsmoney extends ORM {
   protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
   );
   protected $_updated_column = array(
       'column'    => 'lastupdate',
       'format'    => TRUE,
   );
   protected $_belongs_to = array(
       'suite' => array('model' => 'qwt_dldsuite', 'foreign_key' => 'sid')
    );
}
