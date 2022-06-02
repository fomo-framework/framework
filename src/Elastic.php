<?php

namespace Tower;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class Elastic
{
    protected static Client $instance;

    public static function setInstance()
    {
        $istance = (new ClientBuilder())->setHosts([Loader::get('elastic')['host'] . ':' . Loader::get('elastic')['port']]);

        if (Loader::get('elastic')['username'] != null && Loader::get('elastic')['password'] != null){
            $istance->setBasicAuthentication(Loader::get('elastic')['username'] , Loader::get('elastic')['password']);
        }

        self::$instance = $istance->build();
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
