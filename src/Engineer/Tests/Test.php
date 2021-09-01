<?php

namespace Tower\Engineer\Tests;

class Test
{
    public function run(array $arguments): void
    {
        unset($arguments[0]);
        unset($arguments[1]);
        if (!empty($arguments)){
            $arguments = implode(' ' , $arguments);
            $exec = "./vendor/bin/phpunit $arguments --color=always";
        }else{
            $exec = "./vendor/bin/phpunit --color=always";
        }

        echo shell_exec($exec);
    }
}