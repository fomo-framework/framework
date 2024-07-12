<?php

namespace Fomo\Services;

use Fomo\Auth\Auth as AuthDriver;
use Swoole\Server;
use Fomo\Request\Request;

class Auth
{
    public function boot(Server $server = null, Request $request = null): void
    {
        AuthDriver::setInstance();
    }
}