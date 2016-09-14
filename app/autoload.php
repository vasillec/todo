<?php

class AppAutoloader
{
    private static $loader;

    public static function register()
    {
        if (NULL == self::$loader)
            self::$loader = new self();
    }

    private function __construct()
    {
        spl_autoload_register([$this, 'controller']);
    }

    private function controller($className)
    {

        $fileName = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if(file_exists($fileName))
            include $fileName;
    }
}