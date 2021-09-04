<?php

namespace Tower;

use Faker\Factory;
use Faker\Generator;

class Forger
{
    public static function create(): Generator
    {
        return Factory::create(Loader::get('app')['faker_locale']);
    }
}