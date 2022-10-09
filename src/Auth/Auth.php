<?php

namespace Fomo\Auth;

use stdClass;

class Auth
{
    protected static ?self $instance = null;

    public function __construct(
        protected ?stdClass $user = null
    ){}

    public static function getInstance(?stdClass $user = null): Auth
    {
        if (is_null(self::$instance)){
            return self::$instance = new self($user);
        }

        return self::$instance;
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