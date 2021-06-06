<?php

namespace Tower;

use Predis\Client;

class Redis
{
    public static function __callStatic(string $method, array $arguments)
    {
        $config = include configPath() . "redis.php";

        return (new Client($config))->$method(...$arguments);
    }
}