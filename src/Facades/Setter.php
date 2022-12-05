<?php

namespace Fomo\Facades;

class Setter extends Facade
{
    protected static function getMainClass(): string
    {
        return 'none';
    }

    public static function addClass(string $key, $value): void
    {
        self::$classes[$key] = $value;
    }
}