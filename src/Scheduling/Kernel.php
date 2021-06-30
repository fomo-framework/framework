<?php

namespace Tower\Scheduling;

class Kernel
{
    public array $tasks = [];

    protected static ?self $_instance = null;

    public static function getInstance(): self
    {
        if (is_null(self::$_instance))
            self::$_instance = new self();

        return self::$_instance;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }
}