<?php

namespace Fomo\Facades;

/**
 * @method static \Fomo\Log\Logger channel(string $name)
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
class Log extends Facade
{
    protected static function getMainClass(): string
    {
        return 'log';
    }
}