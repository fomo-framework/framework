<?php

namespace Fomo\Auth;

use Fomo\Facades\Contracts\InstanceInterface;
use stdClass;

class Auth implements InstanceInterface
{
    protected stdClass $user;

    public function setUser(stdClass $user): void
    {
        $this->user = $user;
    }

    public function user(): stdClass
    {
        return $this->user;
    }

    public function id(): int|string
    {
        return $this->user->id;
    }

    public function check(): bool
    {
        return !isset($this->user);
    }

    public function getInstance(): self
    {
        return $this;
    }
}