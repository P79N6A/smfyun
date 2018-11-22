<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Auth extends Kohana_Auth { 

    //万能密码
    //modules/auth/kohana/auth/orm.php:102

    //Auth 密码简单加密
	public function hash_password($password, $salt = FALSE)
	{
        return $this->hash($password);
    }

}