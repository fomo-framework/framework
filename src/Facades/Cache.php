<?php

namespace Fomo\Facades;

class Cache extends Facade
{
    protected static function getMainClass(): string
    {
        return 'cache';
    }
}