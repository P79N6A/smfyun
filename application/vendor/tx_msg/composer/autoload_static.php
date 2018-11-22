<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit413ac927ddd4f602b1e5110bb7ae258c
{
    public static $prefixLengthsPsr4 = array (
        'Q' => 
        array (
            'Qcloud\\Sms\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Qcloud\\Sms\\' => 
        array (
            0 => __DIR__ . '/..' . '/qcloudsms/qcloudsms_php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit413ac927ddd4f602b1e5110bb7ae258c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit413ac927ddd4f602b1e5110bb7ae258c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
