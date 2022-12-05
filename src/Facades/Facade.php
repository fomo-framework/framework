<?php

namespace Fomo\Facades;

abstract class Facade
{
    protected static array $classes = [];

    abstract protected static function getMainClass(): string;

    public static function __callStatic(string $method, array $args)
    {
        return self::$classes[static::getMainClass()]->$method(...$args);
    }
}