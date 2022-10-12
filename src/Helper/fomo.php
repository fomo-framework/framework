<?php

use Elastic\Elasticsearch\Client;
use Swoole\Process;
use Fomo\Auth\Auth;
use Fomo\Config\Config;
use Fomo\Elasticsearch\Elasticsearch;
use Fomo\Mail\Mail;
use Fomo\Response\Response;
use Fomo\ServerState\ServerState;
use Fomo\Redis\Redis;
use Fomo\Cache\Cache;

if (! function_exists('basePath')) {
    function basePath(string $path = null): string
    {
        return realpath(PROJECT_PATH) . "/$path";
    }
}

if (! function_exists('appPath')) {
    function appPath(string $path = null): string
    {
        return basePath("app/$path");
    }
}

if (! function_exists('configPath')) {
    function configPath(string $path = null): string
    {
        return basePath("config/$path");
    }
}

if (! function_exists('storagePath')) {
    function storagePath(string $path = null): string
    {
        return basePath("storage/$path");
    }
}

if (! function_exists('languagePath')) {
    function languagePath(string $path = null): string
    {
        return basePath("language/$path");
    }
}

if (! function_exists('databasePath')) {
    function databasePath(string $path = null): string
    {
        return basePath("database/$path");
    }
}

if (! function_exists('config')) {
    function config(string $key, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
    {
        return Config::getInstance()->get($key , $default);
    }
}

if (! function_exists('cpuCount')) {
    function cpuCount(): int
    {
        return swoole_cpu_num();
    }
}

if (! function_exists('setMasterProcessId')) {
    function setMasterProcessId(int $id): void
    {
        ServerState::getInstance()->setMasterProcessId($id);
    }
}

if (! function_exists('setQueueProcessId')) {
    function setQueueProcessId(int $id): void
    {
        ServerState::getInstance()->setQueueProcessId($id);
    }
}

if (! function_exists('setSchedulingProcessId')) {
    function setSchedulingProcessId(int $id): void
    {
        ServerState::getInstance()->setSchedulingProcessId($id);
    }
}

if (! function_exists('setManagerProcessId')) {
    function setManagerProcessId(int $id): void
    {
        ServerState::getInstance()->setManagerProcessId($id);
    }
}

if (! function_exists('setWatcherProcessId')) {
    function setWatcherProcessId(int $id): void
    {
        ServerState::getInstance()->setWatcherProcessId($id);
    }
}

if (! function_exists('setFactoryProcessId')) {
    function setFactoryProcessId(int $id): void
    {
        ServerState::getInstance()->setFactoryProcessId($id);
    }
}

if (! function_exists('setWorkerProcessIds')) {
    function setWorkerProcessIds(array $ids): void
    {
        ServerState::getInstance()->setWorkerProcessIds($ids);
    }
}

if (! function_exists('getMasterProcessId')) {
    function getMasterProcessId(): int|null
    {
        return ServerState::getInstance()->getMasterProcessId();
    }
}

if (! function_exists('getManagerProcessId')) {
    function getManagerProcessId(): int|null
    {
        return ServerState::getInstance()->getManagerProcessId();
    }
}

if (! function_exists('getWatcherProcessId')) {
    function getWatcherProcessId(): int|null
    {
        return ServerState::getInstance()->getWatcherProcessId();
    }
}

if (! function_exists('getFactoryProcessId')) {
    function getFactoryProcessId(): int|null
    {
        return ServerState::getInstance()->getFactoryProcessId();
    }
}

if (! function_exists('getQueueProcessId')) {
    function getQueueProcessId(): int|null
    {
        return ServerState::getInstance()->getQueueProcessId();
    }
}

if (! function_exists('getSchedulingProcessId')) {
    function getSchedulingProcessId(): int|null
    {
        return ServerState::getInstance()->getSchedulingProcessId();
    }
}

if (! function_exists('getWorkerProcessIds')) {
    function getWorkerProcessIds(): array
    {
        return ServerState::getInstance()->getWorkerProcessIds();
    }
}

if (! function_exists('httpServerIsRunning')) {
    function httpServerIsRunning(): bool
    {
        if (!is_null(getManagerProcessId()) && !is_null(getMasterProcessId())){
            return posix_kill(getManagerProcessId(), SIG_DFL) && posix_kill(getMasterProcessId(), SIG_DFL);
        }
        return false;
    }
}

if (! function_exists('queueServerIsRunning')) {
    function queueServerIsRunning(): bool
    {
        if (!is_null(getQueueProcessId())){
            return posix_kill(getQueueProcessId(), SIG_DFL);
        }
        return false;
    }
}

if (! function_exists('schedulingServerIsRunning')) {
    function schedulingServerIsRunning(): bool
    {
        if (!is_null(getSchedulingProcessId())){
            return posix_kill(getSchedulingProcessId(), SIG_DFL);
        }
        return false;
    }
}

if (! function_exists('response')) {
    function response(string $data = '', int $status = 200, array $headers = ['Connection' => 'keep-alive']): Response
    {
        return new Response($status , $headers , $data);
    }
}

if (! function_exists('auth')) {
    function auth(): Auth
    {
        return Auth::getInstance();
    }
}

if (! function_exists('elasticsearch')) {
    function elasticsearch(): Client
    {
        return Elasticsearch::getInstance();
    }
}

if (! function_exists('redis')) {
    function redis(): \Redis
    {
        return Redis::getInstance();
    }
}

if (! function_exists('mail')) {
    function mail(): Mail
    {
        return new Mail();
    }
}

if (! function_exists('cache')) {
    function cache(): Cache
    {
        return new Cache();
    }
}

if (!function_exists('env')) {
    function env($key, $default = null): string|bool|null
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
