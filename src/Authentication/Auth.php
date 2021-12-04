<?php

namespace Tower\Authentication;

use stdClass;

class Auth
{
    protected static Auth $instance;

    protected stdClass $user;

    public function __construct(stdClass $user)
    {
        $this->user = $user;
    }

    public static function setInstance(stdClass $user): void
    {
        self::$instance = new self($user);
    }

    public static function getInstance(): Auth
    {
        return self::$instance;
    }

    public function user(): stdClass
    {
        return $this->user;
    }

    public function id(): int
    {
        return (int) $this->user->id;
    }

    public function check(): bool
    {
        return !empty($this->user);
    }
}