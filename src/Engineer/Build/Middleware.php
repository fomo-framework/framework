<?php

namespace Tower\Engineer\Build;

use Tower\Console\Color;

class Middleware
{
    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('App\Middlewares\\' . $arguments[2])){
            echo Color::error('middleware already exists!');
            return;
        }

        $code = "<?php\n\nnamespace App\Middlewares;\n\nuse Tower\Request;\nuse Tower\Response;\n\nclass $namespace\n{\n\tpublic function handle(Request " . '$request' . "): bool|Response\n\t{\n\t\treturn true;\n\t}\n}";
        $build = fopen(appPath() . "Middlewares/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('middleware created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $check = implode('\\' , $namespace);
        if (class_exists('App\Middlewares\\' . $check)){
            echo Color::error('middleware already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        $directory = str_replace('\\' , '/' , $namespace);
        if (! is_dir(appPath() . "Middlewares/$directory"))
            mkdir(appPath() . "Middlewares/$directory/" , 0777, true);

        $code = "<?php\n\nnamespace App\Middlewares\\$namespace;\n\nuse Tower\Request;\nuse Tower\Response;\n\nclass $className\n{\n\tpublic function handle(Request " . '$request' . "): bool|Response\n\t{\n\t\treturn true;\n\t}\n}";
        $build = fopen(appPath() . "Middlewares/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('middleware created successfully');
    }

    public function build(array $arguments): void
    {
        if (! is_dir(appPath() . "Middlewares"))
            mkdir(appPath() . "Middlewares");

        $namespace = explode('/' , $arguments[2]);

        if (count($namespace) == 1){
            $this->sample($arguments , $namespace[0]);
            return;
        }

        $this->advanced($arguments , $namespace);
    }
}