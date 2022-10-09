<?php

namespace Fomo\Scheduling;

class Kernel
{
    public array $tasks = [];

    protected static ?self $instance = null;

    public static function getInstance(): self
    {
        if (is_null(self::$instance)){
            return self::$instance = new self();
        }

        return self::$instance;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}