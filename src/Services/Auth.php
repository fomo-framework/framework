<?php

namespace Fomo\Services;

use Fomo\Auth\Auth as BaseAuth;
use Swoole\Server;
use Fomo\Request\Request;

class Auth
{
    public function boot(Server $server = null, Request $request = null): void
    {
        BaseAuth::setInstance();
    }
}