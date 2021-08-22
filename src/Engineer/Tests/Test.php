<?php

namespace Tower\Engineer\Tests;

class Test
{
    public function run()
    {
        echo shell_exec('./vendor/bin/phpunit --color=always');
    }
}