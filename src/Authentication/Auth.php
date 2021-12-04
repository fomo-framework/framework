<?php

namespace Tower\Authentication;

use stdClass;

class Auth
{
    protected static self $instance;

    protected ?stdClass $user;

    public function __construct(?stdClass $user = null)
    {
        $this->user = $user;
    }

    public static function setInstance(stdClass $user): void
    {
        self::$instance = new self($user);
    }

    public static function getInstance(): Auth
    {
        if (!isset(self::$instance)){
            self::$instance = new self();
            return self::$instance;
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