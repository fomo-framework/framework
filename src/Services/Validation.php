<?php

namespace Fomo\Services;

use Fomo\Validation\Validation as ValidationDriver;
use Swoole\Server;
use Fomo\Request\Request;

class Validation
{
    public function boot(Server $server = null, Request $request = null): void
    {
        ValidationDriver::setInstance();
    }
}