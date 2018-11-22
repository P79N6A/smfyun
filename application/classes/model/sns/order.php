<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Sns_Order extends ORM {
  protected $_belongs_to = array(
        'qrcode' => array('model' => 'sns_qrcode', 'foreign_key' => 'qid'),
        'item' => array('model' => 'sns_item', 'foreign_key' => 'goodid'),
    );
}
