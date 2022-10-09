<?php

namespace Fomo\Services;

use Elastic\Elasticsearch\Exception\AuthenticationException;
use Swoole\Server;
use Fomo\Log\Log;
use Fomo\Request\Request;
use Fomo\Elasticsearch\Elasticsearch as ElasticsearchDriver;

class Elasticsearch
{
    public function boot(Server $server = null, Request $request = null): void
    {
        try {
            ElasticsearchDriver::setInstance();
        } catch (AuthenticationException $e) {
            Log::alert($e->getMessage() . " (elasticsearch)");
        }
    }
}