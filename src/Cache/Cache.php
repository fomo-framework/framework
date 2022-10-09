<?php

namespace Fomo\Cache;

use Fomo\Redis\Redis;

class Cache
{
    public function get(string $key , $default = null , int $expire = null)
    {
        if (is_null($default)){
            return $this->getByKey($key);
        }

        if (is_callable($default)){
            $default = $default();
        }

        if (!$this->getByKey($key)){
            $this->setByKey($key , $default , $expire);
        }

        return $this->getByKey($key);
    }

    public function put(string $key , $value , int $expire = null): void
    {
        $this->setByKey($key , $value , $expire);
    }

    public function remember(string $key , int $expire , $value)
    {
        if ($this->has($key)){
            return $this->getByKey($key);
        }

        if (is_callable($value)){
            $value = $value();
        }

        $this->setByKey($key , $value , $expire);

        return $this->getByKey($key);
    }

    public function rememberForever(string $key , $value)
    {
        if ($this->has($key)){
            return $this->getByKey($key);
        }

        if (is_callable($value)){
            $value = $value();
        }

        $this->setByKey($key , $value);

        return $this->getByKey($key);
    }

    public function pull(string $key)
    {
        $value = $this->getByKey($key);

        $this->deleteByKey($key);

        return $value;
    }

    public function delete(string $key): void
    {
        $this->deleteByKey($key);
    }

    public function store(string $key , $value , int $expire = null): void
    {
        if (! $this->has($key)){
            if (is_callable($value)){
                $value = $value();
            }

            $this->setByKey($key , $value , $expire);
        }
    }

    public function has(string $key): bool
    {
        return (bool) Redis::getInstance()->get($key);
    }

    protected function getByKey(string $key)
    {
        return json_decode(Redis::getInstance()->get($key));
    }

    protected function setByKey(string $key , $value , int $expire = null): void
    {
        if (is_null($expire)){
            Redis::getInstance()->set($key , json_encode($value));
        } else{
            Redis::getInstance()->setex($key , $expire , json_encode($value));
        }
    }

    protected function deleteByKey(string $key): void
    {
        Redis::getInstance()->del($key);
    }
}