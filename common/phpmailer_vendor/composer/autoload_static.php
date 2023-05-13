<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7d04bb20f3940988083ce75585f2651b
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7d04bb20f3940988083ce75585f2651b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7d04bb20f3940988083ce75585f2651b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7d04bb20f3940988083ce75585f2651b::$classMap;

        }, null, ClassLoader::class);
    }
}
