<?php

namespace Fomo\Services;

use Fomo\Language\Language as LanguageDriver;
use Swoole\Server;
use Fomo\Request\Request;

class Language
{
    public function boot(Server $server = null, Request $request = null): void
    {
        LanguageDriver::setInstance();
    }
}