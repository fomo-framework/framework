<?php

namespace Tower;

use Faker\Factory;
use Faker\Generator;

class Forger
{
    public static function create(): Generator
    {
        $config = include configPath() . "server.php";

        return Factory::create($config['faker_locale']);
    }
}