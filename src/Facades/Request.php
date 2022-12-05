<?php

namespace Fomo\Facades;

/**
 * @method static string|int|bool|array|float|null get(?string $name = null, string|int|bool|array|float|null $default = null)
 * @method static string|int|bool|array|float|null post(?string $name = null, string|int|bool|array|float|null $default = null)
 * @method static string|int|bool|array|float|null header(?string $name = null, string|int|bool|array|float|null $default = null)
 * @method static string|int|bool|array|float|null input(string $name, string|int|bool|array|float|null $default = null)
 * @method static array all()
 * @method static array only(array $keys)
 * @method static array except(array $keys)
 * @method static string url()
 * @method static string fullUrl()
 * @method static string method()
 * @method static string protocolVersion()
 * @method static string uri()
 * @method static string path()
 * @method static string queryString()
 * @method static string|null variable(string $variable)
 * @method static string remoteIp()
 * @method static int remotePort()
 * @method static string localhost()
 * @method static int localPort()
 * @method static string|null host(bool $withoutPort = false)
 * @method static string bearerToken()
 * @method static string|null ip()
 * @method static \Fomo\Request\Request getInstance()
 */
class Request extends Facade
{
    protected static function getMainClass(): string
    {
        return 'request';
    }
}