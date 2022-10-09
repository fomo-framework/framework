<?php

namespace Fomo\Scheduling;

class Kernel
{
    protected array $tasks = [];

    protected static ?self $instance = null;

    public static function getInstance(): self
    {
        if (is_null(self::$instance)){
            return self::$instance = new self();
        }

        return self::$instance;
    }

    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}