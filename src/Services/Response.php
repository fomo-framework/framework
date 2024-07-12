<?php

namespace Fomo\Services;

use Swoole\Server;
use Fomo\Request\Request;
use Fomo\Response\Response as ResponseDriver;

class Response
{
    public function boot(Server $server = null, Request $request = null): void
    {
        ResponseDriver::setInstance();
    }
}