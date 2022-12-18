<?php

namespace Fomo\Application;

use Fomo\Container\Container;
use Throwable;

class Application extends Container
{
    const VERSION = '2.3.0';

    public static function throwableErrToStderr(Throwable $throwable): void
    {
        fwrite(STDERR, json_encode([
                'type' => 'throwable',
                'class' => $throwable::class,
                'code' => $throwable->getCode(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'message' => $throwable->getMessage(),
                'trace' => array_slice($throwable->getTrace(), 0, 2),
            ])."\n");
    }

    public static function shutdownErrToStderr(Throwable $throwable): void
    {
        fwrite(STDERR, json_encode([
                'type' => 'shutdown',
                'class' => $throwable::class,
                'code' => $throwable->getCode(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'message' => $throwable->getMessage(),
                'trace' => array_slice($throwable->getTrace(), 0, 2),
            ])."\n");
    }

    public function basePath(string $path = null): string
    {
        return "$this->basePath/$path";
    }

    public function appPath(string $path = null): string
    {
        return $this->basePath("app/$path");
    }

    public function configPath(string $path = null): string
    {
        return $this->basePath("config/$path");
    }

    public function storagePath(string $path = null): string
    {
        return $this->basePath("storage/$path");
    }

    public function languagePath(string $path = null): string
    {
        return $this->basePath("language/$path");
    }

    public function databasePath(string $path = null): string
    {
        return $this->basePath("database/$path");
    }

    public function version(): string
    {
        return self::VERSION;
    }
}