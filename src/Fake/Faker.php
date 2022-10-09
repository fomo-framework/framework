<?php

namespace Fomo\Fake;

use Faker\Factory;
use Faker\Generator;

class Faker
{
    public static function create(): Generator
    {
        return Factory::create(config('app.faker_locale'));
    }
}