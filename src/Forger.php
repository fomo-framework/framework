<?php

namespace Tower;

use Faker\Factory;
use Faker\Generator;

class Forger
{
    public static function create(): Generator
    {
        $config = include configPath() . "app.php";

        return Factory::create($config['faker_locale']);
    }
}