<?php defined('SYSPATH') or die('No direct script access.');
Class qwt_qrcode {
    public static function selectorm($name,$bid){
        if($bid==2){
            $ormname=$name.'1';
        }elseif ($bid==6) {
            $ormname=$name;
        }
        return ORM::factory($ormname);
    }
}
