<?php

namespace Fomo\Redis;

use Fomo\Facades\Contracts\InstanceInterface;

class Redis extends \Redis implements InstanceInterface
{
    public function getInstance(): self
    {
        return $this;
    }
}