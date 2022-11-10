<?php

namespace Fomo\Auth;

use stdClass;

class Auth
{
    protected static self $instance;

    public function __construct(
        protected ?stdClass $user = null
    ){}

    public static function getInstance(?stdClass $user = null): Auth
    {
        if (isset(self::$instance)){
            return self::$instance;
        }

        return self::$instance = new self($user);
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
        return !is_null($this->user);
    }
}