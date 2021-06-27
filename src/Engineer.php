<?php

namespace Tower;

use Tower\Console\Color;

class Engineer
{
    protected string $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    protected array $commands = [
        'build' => [
            'controller' ,
            'resource' ,
            'middleware' ,
            'job' ,
            'exception' ,
        ]
    ];

    protected array $description = [
        'build' => [
            'controller' => 'create a new controller class' ,
            'resource' => 'create a new resource class' ,
            'middleware' => 'create a new middleware class' ,
            'job' => 'create a new job class' ,
            'exception' => 'create a new exception class' ,
        ]
    ];

    public function run(array $arguments): void
    {
        if (count($arguments) == 1){
            $this->commands();
            return;
        }

        $operation = explode(':' , $arguments[1]);

        if (count($operation) == 1){
            $this->oneArgument($operation , $arguments);
            return;
        }

        if (count($operation) == 2){
            $this->multiArguments($operation , $arguments);
            return;
        }
    }

    protected function oneArgument(array $operation , array $arguments): void
    {
        if (! array_key_exists($operation[0] , $this->commands) || is_array($this->commands[$operation[0]])){
            echo Color::error("command not found!");
            return;
        }

        $class = 'Tower\\Engineer\\' . ucfirst($operation[0]);
        $method = 'run';
        $operation = new $class();
        $operation->$method($arguments);
    }
    protected function multiArguments(array $operation , array $arguments): void
    {
        if (! array_key_exists($operation[0] , $this->commands)){
            echo Color::error("command not found!");
            return;
        }

        if (! in_array($operation[1] , $this->commands[$operation[0]])){
            echo Color::error("command not found!");
            return;
        }

        $class = 'Tower\\Engineer\\' . ucfirst($operation[1]);
        $method = $operation[0];
        $operation = new $class();
        $operation->$method($arguments);
    }

    protected function commands(): void
    {
        echo Color::LIGHT_WHITE . 'tower framework ' . Color::LIGHT_BLUE . $this->version . Color::RESET . PHP_EOL . PHP_EOL;
        echo Color::GREEN . 'Hello
I am the engineer of your tower and I am ready to help you
What did he do to me?' . Color::RESET . PHP_EOL . PHP_EOL;

        echo Color::LIGHT_GRAY . 'What can I do to help?' . Color::RESET . PHP_EOL . PHP_EOL;
        foreach (array_keys($this->commands) as $command)
            echo Color::YELLOW . "$command" . Color::RESET . PHP_EOL;
        foreach ($this->commands['build'] as $command){
            $description = Color::LIGHT_WHITE . $this->description['build'][$command];
            echo Color::GREEN . " $command \t $description" . Color::RESET . PHP_EOL;
        }
    }
}