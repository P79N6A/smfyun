<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Qwt_Hbbkl extends ORM {

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
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
