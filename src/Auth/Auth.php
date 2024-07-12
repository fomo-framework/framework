<?php

namespace Fomo\Auth;

use stdClass;

class Auth
{
    protected static self $instance;
    protected ?stdClass $user = null;
    
    public static function setInstance(): void
    {
        if (!isset(self::$instance)){
            self::$instance = new self();
        }
    }

    public static function getInstance(): Auth
    {
        return self::$instance;
    }
    
    public function setUser(stdClass $user): void
    {
        $this->user = $user;
    }

    public function getUser(): stdClass
    {
        return $this->user;
    }

    public function getId(): int|string
    {
        return $this->user->id;
    }

    public function checkExistUser(): bool
    {
        return !is_null($this->user);
    }
}