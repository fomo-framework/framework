<?php

namespace Tower\Scheduling;

class Kernel
{
    public array $tasks = [];

    protected static ?self $instance;

    public static function getInstance(): self
    {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}