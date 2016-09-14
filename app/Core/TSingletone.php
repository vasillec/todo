<?php

namespace app\Core;


trait TSingletone
{
    protected static $instance = null;

    protected function __construct()
    {
    }
    
    protected function __clone()
    {
    }

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}