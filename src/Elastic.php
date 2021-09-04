<?php

namespace Tower;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class Elastic
{
    protected static Client $instance;

    public static function setInstance()
    {
        self::$instance = (new ClientBuilder())
            ->setHosts(Loader::get('elastic'))
            ->build();
    }

    public static function getInstance(): Client
    {
        return self::$instance;
    }
    
    public static function __callStatic(string $method, array $arguments)
    {
        return self::$instance->$method(...$arguments);
    }
}
