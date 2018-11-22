<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Qwt_hbykl extends ORM {

    protected $_created_column = array(
        'column'    => 'createtime',
        'format'    => TRUE,
    );
    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
    protected $_belongs_to = array(
        'logins'  => array('model' => 'qwt_hbylogin', 'foreign_key' => 'from_lid'),
    );
    public function genKouling($regen=0) {
        $code = (string)mt_rand(100000000, 999999999);
        $j = 0;
        $sum = 0;
        while ($j < 9) {
            $sum += $code{$j};
            $j++;
        }
        $sum = $sum%9;
        return $code.$sum;
    }
}
