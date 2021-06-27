<?php

namespace Tower;

class Engineer
{
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

}