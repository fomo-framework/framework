<?php

namespace Fomo\Config;

class Config
{
    protected static ?self $instance = null;

    protected array $fileCache = [];
    protected array $keyCache = [];

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            return self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $key, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
    {
        $key = \explode('.' , $key);
        $configPath = $key[0];
        unset($key[0]);
        $key = \implode('.' , $key);

        $config = $this->fileCache[$configPath] ?? ($this->fileCache[$configPath] = require_once configPath("$configPath.php"));

        if ($key == ''){
            return $config;
        }

        return $this->keyCache["$configPath.$key"] ?? ($this->keyCache["$configPath.$key"] = $this->getData($config, $key, $default));
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