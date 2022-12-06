<?php

namespace Fomo\Facades;

/**
 * @method static string|int|bool|array|float|null get(string $key, string|int|bool|array|float|null $default = null)
 */
class Config extends Facade
{
    protected static function getMainClass(): string
    {
        return 'config';
    }
}