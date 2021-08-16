<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf307298ec1a55838e0510e1d6802b122
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Mpdf\\QrCode\\' => 12,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Mpdf\\QrCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/mpdf/qrcode/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf307298ec1a55838e0510e1d6802b122::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf307298ec1a55838e0510e1d6802b122::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf307298ec1a55838e0510e1d6802b122::$classMap;

        }, null, ClassLoader::class);
    }
}