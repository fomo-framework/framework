<?php

namespace Tower;

use \Workerman\Protocols\Http\Request as WorkerRequest;

class Request extends WorkerRequest
{
    protected static array $variables;

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
        return '//' . $this->host() . $this->path();
    }

    public function fullUrl(): string
    {
        return '//' . $this->host() . $this->uri();
    }
}
