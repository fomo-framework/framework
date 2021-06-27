<?php

namespace Tower\Engineer;

use Tower\Console\Color;

class Controller
{
    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('App\Controllers\\' . $arguments[2])){
            echo Color::error('controller already exists!');
            return;
        }

        $code = "<?php \n\nnamespace App\Controllers;\n\nclass $namespace  extends Controller\n{\n\t//\n}";
        $build = fopen(appPath() . "Controllers/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('controller created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $checkExist = implode('\\' , $namespace);

        if (class_exists('App\Controllers\\' . $checkExist)){
            echo Color::error('controller already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        if (! is_dir(appPath() . "Controllers/" . $namespace))
            mkdir(appPath() . "Controllers/" . $namespace);

        $code = "<?php \n\nnamespace App\Controllers\\$namespace;\n\nuse App\Controllers\Controller;\n\nclass $className extends Controller\n{\n\t//\n}";

        $build = fopen(appPath() . "Controllers/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('controller created successfully');
    }

    public function build(array $arguments): void
    {
        $namespace = explode('/' , $arguments[2]);

        if (count($namespace) == 1){
            $this->sample($arguments , $namespace[0]);
            return;
        }

        $this->advanced($arguments , $namespace);
    }
}