<?php

namespace Fomo\Services;

use Swoole\Server;
use Fomo\Request\Request;
use Fomo\Mail\Mail as MailDriver;

class Mail
{
    public function boot(Server $server = null, Request $request = null): void
    {
        MailDriver::setInstance();
    }
}