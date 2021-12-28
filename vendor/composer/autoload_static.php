<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb7fdbdd0a7cd6f0697775c0c52904cae
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitb7fdbdd0a7cd6f0697775c0c52904cae::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb7fdbdd0a7cd6f0697775c0c52904cae::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb7fdbdd0a7cd6f0697775c0c52904cae::$classMap;

        }, null, ClassLoader::class);
    }
}
