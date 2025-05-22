<?php

namespace App\Config;

abstract class SingletonInstance
{
    private static array $instances = [];

    public static function getInstance()
    {
        $calledClass = static::class;
        if (!isset(self::$instances[$calledClass])) {
            self::$instances[$calledClass] = new static();
        }
        return self::$instances[$calledClass];
    }

    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
}
