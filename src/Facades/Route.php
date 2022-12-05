<?php

namespace Fomo\Facades;

use Closure;
use Fomo\Router\Router;

/**
 * @method static void post(string $route , array|Closure $callback)
 * @method static void get(string $route , array|Closure $callback)
 * @method static void patch(string $route , array|Closure $callback)
 * @method static void put(string $route , array|Closure $callback)
 * @method static void delete(string $route , array|Closure $callback)
 * @method static void head(string $route , array|Closure $callback)
 * @method static void any(string $route , array|Closure $callback)
 * @method static void group(array $parameters, Closure $callback)
 * @method static Router prefix(string $prefix)
 * @method static Router middleware(string|array $middlewares)
 * @method static Router withoutMiddleware(string|array $middlewares)
 */
class Route extends Facade
{
    protected static function getMainClass(): string
    {
        return 'route';
    }
}