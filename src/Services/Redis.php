<?php

namespace Fomo\Services;

use Swoole\Server;
use Fomo\Log\Log;
use Fomo\Redis\Redis as RedisDriver;
use Fomo\Request\Request;

class Redis
{
    public function boot(Server $server = null, Request $request = null): void
    {
        try {
            RedisDriver::setInstance();
        } catch (\Exception $e) {
            Log::alert($e->getMessage() . " (redis)");
        }
    }
}