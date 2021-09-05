<?php

namespace Tower;

use Workerman\Protocols\Http\Request as WorkerRequest;

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

    public function get($name = null, $default = null): string|int|bool|array|float|null
    {
        $get = $this->_data['get'];

        if (!isset($get)) {
            $this->parseGet();
        }

        if (null === $name) {
            return $get;
        }

        return $this->getData($get , $name , $default);
    }

    public function post($name = null, $default = null): string|int|bool|array|float|null
    {
        $post = $this->_data['post'];

        if (!isset($post)) {
            $this->parsePost();
        }

        if (null === $name) {
            return $post;
        }

        return $this->getData($post , $name , $default);
    }

    public function header($name = null, $default = null): string|int|bool|array|float|null
    {
        $headers = $this->_data['headers'];

        if (!isset($headers)) {
            $this->parseHeaders();
        }

        if (null === $name) {
            return $headers;
        }

        $name = strtolower($name);
        return $this->getData($headers , $name , $default);
    }

    public function cookie($name = null, $default = null): string|int|bool|array|float|null
    {
        $cookie = $this->_data['cookie'];

        if (!isset($cookie)) {
            parse_str(str_replace('; ', '&', $this->header('cookie')), $cookie);
        }

        if ($name === null) {
            return $cookie;
        }

        return $this->getData($cookie , $name , $default);
    }

    public function file($name = null): string|int|bool|array|float|null
    {
        $file = $this->_data['files'];

        if (!isset($file)) {
            $this->parsePost();
        }

        if (null === $name) {
            return $file;
        }

        return $this->getData($file , $name);
    }

    public function input(string $name, $default = null): string|int|bool|array|float|null
    {
        $post = $this->post($name , $default);
        if ($post) {
            return $post;
        }

        return $this->get($name , $default);
    }

    public function all(): array
    {
        return $this->post() + $this->get();
    }

    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $exists = $this->getData($this->all() , $key);
            if (!is_null($exists)) {
                $result[$key] = $exists;
            }
        }
        return $result;
    }

    public function except(array $keys): array
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    public function url(): string
    {
        if (env('APP_SSL' , false) == true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $this->host() . $this->path();
    }

    public function fullUrl(): string
    {
        if (env('APP_SSL' , false) == true) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $this->host() . $this->uri();
    }

    public function remoteAddress(): string
    {
        return self::$remoteAddress;
    }

    public function remoteIp(): string
    {
        $ip = explode(':' , self::$remoteAddress);

        return $ip[0];
    }

    public function remotePort(): int
    {
        $ip = explode(':' , self::$remoteAddress);

        return (int) $ip[1];
    }

    public function localAddress(): string
    {
        return self::$localAddress;
    }

    public function localIp(): string
    {
        $ip = explode(':' , self::$localAddress);

        return $ip[0];
    }

    public function localPort(): int
    {
        $ip = explode(':' , self::$localAddress);

        return (int) $ip[1];
    }

    public function ip(): ?string
    {
        return $this->header('client-ip', $this->header('x-forwarded-for',
            $this->header('x-real-ip', $this->header('x-client-ip',
                $this->header('via', $this->remoteIp())))));
    }

    protected function getData(array $array, string $key, ?string $default = null): string|int|bool|array|float|null
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }
}
