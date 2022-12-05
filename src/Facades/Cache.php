<?php

namespace Fomo\Facades;

/**
 * @method static mixed get(string $key , $default = null , int $expire = null)
 * @method static void put(string $key , $value , int $expire = null)
 * @method static mixed remember(string $key , int $expire , $value)
 * @method static mixed rememberForever(string $key , $value)
 * @method static mixed pull(string $key)
 * @method static void delete(string $key)
 * @method static void store(string $key , $value , int $expire = null)
 * @method static bool has(string $key)
 * @method static mixed getByKey(string $key)
 * @method static void setByKey(string $key , $value , int $expire = null)
 * @method static void deleteByKey(string $key)
 */
class Cache extends Facade
{
    protected static function getMainClass(): string
    {
        return 'cache';
    }
}