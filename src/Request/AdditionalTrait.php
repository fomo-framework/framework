<?php

namespace Fomo\Request;

trait AdditionalTrait
{
    public function host(bool $withoutPort = false): ?string
    {
        $host = $this->header('host');
        if ($host && $withoutPort && $pos = \strpos($host, ':')) {
            return \substr($host, 0, $pos);
        }
        return $host;
    }

    public function bearerToken(): string
    {
        $header = $this->header('Authorization', '');

        return mb_substr($header, 7, null, 'UTF-8');
    }

    public function ip(): ?string
    {
        return $this->header('client-ip', $this->header('x-forwarded-for',
            $this->header('x-real-ip', $this->header('x-client-ip',
                $this->header('via', $this->remoteIp())))));
    }

    public function input(string $name, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
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
        if (env('APP_SSL', false)) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $this->host() . $this->path();
    }

    public function fullUrl(): string
    {

        $protocol = !is_null(config('server.ssl.ssl_cert_file')) && !is_null(config('server.ssl.ssl_key_file')) ? 'https://' : 'http://';


        return $protocol . $this->host() . $this->uri();
    }

    protected function getData(array $array, string $key, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
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