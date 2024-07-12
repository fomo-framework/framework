<?php

namespace Fomo\Log;

/**
 * Class Log
 *
 * Strings methods
 * @method static Logger channel(string $name)
 * @method static void info(string $message , ?array $content = null)
 * @method static void alert(string $message , ?array $content = null)
 * @method static void critical(string $message , ?array $content = null)
 * @method static void debug(string $message , ?array $content = null)
 * @method static void emergency(string $message , ?array $content = null)
 * @method static void error(string $message , ?array $content = null)
 * @method static void log(string $message , ?array $content = null)
 * @method static void notice(string $message , ?array $content = null)
 * @method static void warning(string $message , ?array $content = null)
 */

class Log
{
    protected static Logger $instance;

    public static function setInstance(): void
    {
        if (!isset(self::$instance)){
            self::$instance = new Logger();
        }
    }

    public static function getInstance(): Logger
    {
        return self::$instance;
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return self::getInstance()->$method(...$arguments);
    }
}