<?php

use Tower\Cache;
use Tower\Elastic;
use Tower\Queue;
use Tower\Request;
use Tower\Authentication\Auth;
use Tower\Response;
use Elasticsearch\Client;
use Tower\Redis;

if (! function_exists('basePath')) {
    function basePath(): string
    {
        return realpath('./');
    }
}
if (! function_exists('appPath')) {
    function appPath(): string
    {
        return basePath() . '/app/';
    }
}

if (! function_exists('configPath')) {
    function configPath(): string
    {
        return basePath() . '/config/';
    }
}

if (! function_exists('storagePath')) {
    function storagePath(): string
    {
        return basePath() . '/storage/';
    }
}

if (! function_exists('json')) {
    function json(array $data, int $status = 200): Response
    {
        return new Response(
            $status,
            ['Content-Type' => 'application/json']
            , json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE)
        );
    }
}

if (! function_exists('bearerToken')) {
    function bearerToken(): string
    {
        $header = Request::getInstance()->header('Authorization', '');

        return mb_substr($header, 7, null, 'UTF-8');
    }
}

if (! function_exists('auth')) {
    function auth(): Auth
    {
        return Auth::getInstance();
    }
}

if (! function_exists('elastic')) {
    function elastic(): Client
    {
        return Elastic::getInstance();
    }
}

if (! function_exists('redirect')) {
    function redirect(string $location, int $status = 302, array $headers = []): Response
    {
        $response = new Response($status, ['Location' => $location]);
        if (!empty($headers)) {
            $response->withHeaders($headers);
        }
        return $response;
    }
}

if (! function_exists('cpuCoreCount')) {
    function cpuCoreCount(): int
    {
        if (strtolower(PHP_OS) === 'darwin')
            $count = shell_exec('sysctl -n machdep.cpu.core_count');
        else
            $count = shell_exec('nproc');

        return (int)$count > 0 ? (int)$count : 4;
    }
}
if (! function_exists('redis')) {
    function redis(): \Redis
    {
        return Redis::getInstance();
    }
}

if (! function_exists('queue')) {
    function queue(string $queue , array $data , int $attempts = 5)
    {
        Queue::store($queue , $data , $attempts);
    }
}

if (! function_exists('cache')) {
    function cache(): Cache
    {
        return new Cache();
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}