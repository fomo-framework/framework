<?php

namespace Fomo\Facades;

class Faker extends Facade
{
    protected static function getMainClass(): string
    {
        return 'faker';
    }
}