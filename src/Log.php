<?php

namespace Tower;

use Tower\Log\Logger;

/**
 * Class Log
 * @package Logger
 *
 * @method static Logger channel(string $name)
 * @method static void alert(string $message, array $content = null)
 * @method static void critical(string $message, array $content = null)
 * @method static void debug(string $message, array $content = null)
 * @method static void emergency(string $message, array $content = null)
 * @method static void error(string $message, array $content = null)
 * @method static void info(string $message, array $content = null)
 * @method static void log($level, string $message, array $content = null)
 * @method static void notice(string $message, array $content = null)
 * @method static void warning(string $message, array $content = null)
 */

class Log
{
    public static function __callStatic(string $method, array $arguments)
    {
        return (new Logger())->$method(...$arguments);
    }
}