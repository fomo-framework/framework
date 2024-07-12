<?php

namespace Fomo\Services;

use Fomo\Log\Log as BaseLog;
use Swoole\Server;
use Fomo\Request\Request;

class Log
{
    public function boot(Server $server = null, Request $request = null): void
    {
        BaseLog::setInstance();
    }
}