<?php

namespace Fomo\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Fomo\Facades\Contracts\InstanceInterface;

class Elasticsearch extends ClientBuilder implements InstanceInterface
{
    public function getInstance(): self
    {
        return $this;
    }
}