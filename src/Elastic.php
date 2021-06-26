<?php

namespace Tower;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class Elastic
{
    protected static Client $_instance;

    public static function setInstance()
    {
        $config = include configPath() . "elastic.php";

        self::$_instance = (new ClientBuilder())
            ->setHosts($config)
            ->build();
    }

    public static function getInstance(): Client
    {
        return self::$_instance;
    }
    
    public static function __callStatic(string $method, array $arguments)
    {
        return self::$_instance->$method(...$arguments);
    }
}
