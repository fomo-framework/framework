<?php

namespace Fomo\Scheduling;

class Kernel
{
    public array $tasks = [];

    protected static self $instance;

    public static function getInstance(): self
    {
        if (isset(self::$instance)){
            return self::$instance;
        }

        return self::$instance = new self();
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}