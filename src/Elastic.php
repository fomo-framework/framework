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

        self::$_instance = ClientBuilder::create()
            ->setHosts($config)
            ->build();
    }

    public static function getInstance(): Client
    {
        return self::$_instance;
    }
}