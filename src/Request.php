<?php

namespace Tower;

use \Workerman\Protocols\Http\Request as WorkerRequest;

class Request extends WorkerRequest
{
    protected static array $variables;
    protected static string $localAddress;
    protected static string $remoteAddress;

    public static function setLocalAddress(string $localAddress): void
    {
        self::$localAddress = $localAddress;
    }

    public static function setRemoteAddress(string $remoteAddress): void
    {
        self::$remoteAddress = $remoteAddress;
    }

    public static function setVariables(array $variables): void
    {
        self::$variables = $variables;
    }

    public function variable(string $variable): string|null
    {
        return self::$variables[$variable] ?? null;
    }

    public function bearerToken(): string
    {
        $header = $this->header('Authorization', '');

        return mb_substr($header, 7, null, 'UTF-8');
    }

    public function input(string $name, $default = null): mixed
    {
        $post = $this->post();
        if (isset($post[$name])) {
            return $post[$name];
        }
        $get = $this->get();
        return $get[$name] ?? $default;
    }

    public function all(): mixed
    {
        return $this->post() + $this->get();
    }

    public function only(array $keys): array
    {
        $all = $this->all();
        $result = [];
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        return $result;
    }

    public function except(array $keys): mixed
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    public function url(): string
    {
        if (env("APP_SSL", false) == true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $this->host() . $this->path();
    }

    public function fullUrl(): string
    {
        if (env("APP_SSL", false) == true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $this->host() . $this->uri();
    }

    public function remoteIp(): string
    {
        $ip = explode(self::$remoteAddress , ':');

        return $ip[0];
    }

    public function remotePort(): string
    {
        $ip = explode(self::$remoteAddress , ':');

        return $ip[1];
    }

    public function localIp(): string
    {
        $ip = explode(self::$localAddress , ':');

        return $ip[0];
    }

    public function localPort(): string
    {
        $ip = explode(self::$localAddress , ':');

        return $ip[1];
    }

    public function realIp(): ?string
    {
        return $this->header('client-ip', $this->header('x-forwarded-for',
            $this->header('x-real-ip', $this->header('x-client-ip',
                $this->header('via', $this->remoteIp())))));
    }
}
