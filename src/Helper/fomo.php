<?php

use Elastic\Elasticsearch\Client;
use Fomo\Application\Application;
use Fomo\Auth\Auth;
use Fomo\Facades\Auth as AuthFacade;
use Fomo\Cache\Cache;
use Fomo\Facades\Cache as CacheFacade;
use Fomo\Elasticsearch\Elasticsearch;
use Fomo\Facades\Config;
use Fomo\Request\Request;
use Fomo\Facades\Request as RequestFacade;
use Fomo\Facades\ServerState;
use Fomo\Mail\Mail;
use Fomo\Redis\Redis;
use Fomo\Response\Response;
use Fomo\Facades\Response as ResponseFacade;

if (! function_exists('app')) {
    function app(): Application
    {
        return Application::getInstance();
    }
}

if (! function_exists('resolve')) {
    function resolve(string $name, array $constructor = []): mixed
    {
        return Application::getInstance()->make($name, $constructor);
    }
}
if (! function_exists('basePath')) {
    function basePath(string $path = null): string
    {
        return Application::getInstance()->basePath($path);
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
        return Config::get($key , $default);
    }
}

if (! function_exists('cpuCount')) {
    function cpuCount(): int
    {
        if (class_exists(OpenSwoole\Util::class)) {
            return OpenSwoole\Util::getCPUNum();
        } else if (function_exists('swoole_cpu_num')) {
            return swoole_cpu_num();
        }
        return 1;
    }
}

if (! function_exists('setMasterProcessId')) {
    function setMasterProcessId(int $id): void
    {
        ServerState::setMasterProcessId($id);
    }
}

if (! function_exists('setQueueProcessId')) {
    function setQueueProcessId(int $id): void
    {
        ServerState::setQueueProcessId($id);
    }
}

if (! function_exists('setSchedulingProcessId')) {
    function setSchedulingProcessId(int $id): void
    {
        ServerState::setSchedulingProcessId($id);
    }
}

if (! function_exists('setManagerProcessId')) {
    function setManagerProcessId(int $id): void
    {
        ServerState::setManagerProcessId($id);
    }
}

if (! function_exists('setWatcherProcessId')) {
    function setWatcherProcessId(int $id): void
    {
        ServerState::setWatcherProcessId($id);
    }
}

if (! function_exists('setFactoryProcessId')) {
    function setFactoryProcessId(int $id): void
    {
        ServerState::setFactoryProcessId($id);
    }
}

if (! function_exists('setWorkerProcessIds')) {
    function setWorkerProcessIds(array $ids): void
    {
        ServerState::setWorkerProcessIds($ids);
    }
}

if (! function_exists('getMasterProcessId')) {
    function getMasterProcessId(): int|null
    {
        return ServerState::getMasterProcessId();
    }
}

if (! function_exists('getManagerProcessId')) {
    function getManagerProcessId(): int|null
    {
        return ServerState::getManagerProcessId();
    }
}

if (! function_exists('getWatcherProcessId')) {
    function getWatcherProcessId(): int|null
    {
        return ServerState::getWatcherProcessId();
    }
}

if (! function_exists('getFactoryProcessId')) {
    function getFactoryProcessId(): int|null
    {
        return ServerState::getFactoryProcessId();
    }
}

if (! function_exists('getQueueProcessId')) {
    function getQueueProcessId(): int|null
    {
        return ServerState::getQueueProcessId();
    }
}

if (! function_exists('getSchedulingProcessId')) {
    function getSchedulingProcessId(): int|null
    {
        return ServerState::getSchedulingProcessId();
    }
}

if (! function_exists('getWorkerProcessIds')) {
    function getWorkerProcessIds(): array
    {
        return ServerState::getWorkerProcessIds();
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

if (! function_exists('request')) {
    function request(): Request
    {
        return RequestFacade::getInstance();
    }
}

if (! function_exists('response')) {
    function response(): Response
    {
        return ResponseFacade::getInstance();
    }
}

if (! function_exists('auth')) {
    function auth(): Auth
    {
        return AuthFacade::getInstance();
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
        return CacheFacade::getInstance();
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
