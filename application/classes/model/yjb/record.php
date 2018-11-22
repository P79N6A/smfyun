<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言二维码库
class Model_Yjb_Record extends ORM {
	protected $_created_column = array(
        'column'    => 'jointime',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    // Relationships
    protected $_belongs_to = array(
            'party'  => array('model' => 'Yjb_party', 'foreign_key' => 'pid'),
            'qrcode'  => array('model' => 'Yjb_qrcode', 'foreign_key' => 'qid'),
            'score'  => array('model' => 'Yjb_score', 'foreign_key' => 'sid'),
        );
}

