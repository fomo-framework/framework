<?php

namespace Tower;

class Redis
{
    protected static \Redis $_instance;

    public static function getInstance(): \Redis
    {
        return self::$_instance;
    }

    public static function setInstance(): void
    {
        $config = include configPath() . "redis.php";

        self::$_instance = new \Redis();

        self::$_instance->connect($config['host'] , $config['port']);
        self::$_instance->select($config['database']);

        if (! is_null($config['username']) && ! is_null($config['password']))
            self::$_instance->auth([$config['username'] , $config['password']]);
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return self::$_instance->$method(...$arguments);
    }
}