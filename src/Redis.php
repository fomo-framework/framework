<?php

namespace Tower;

class Redis
{
    protected static \Redis $instance;

    public static function getInstance(): \Redis
    {
        return self::$instance;
    }

    public static function setInstance(): void
    {
        $config = Loader::get('redis');

        self::$instance = new \Redis();

        self::$instance->connect($config['host'] , $config['port']);
        self::$instance->select($config['database']);

        if (! is_null($config['username']) && ! is_null($config['password']))
            self::$instance->auth([$config['username'] , $config['password']]);
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return self::$instance->$method(...$arguments);
    }
}