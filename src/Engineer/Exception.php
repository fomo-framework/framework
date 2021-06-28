<?php

namespace Tower\Engineer;

use Tower\Console\Color;

class Exception
{
    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('App\Exceptions\\' . $arguments[2])){
            echo Color::error('exception already exists!');
            return;
        }

        $code = "<?php\n\nnamespace App\Exceptions;\n\nclass $namespace extends \Exception\n{\n\t//\n}";
        $build = fopen(appPath() . "Exceptions/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('exception created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $check = implode('\\' , $namespace);
        if (class_exists('App\Exceptions\\' . $check)){
            echo Color::error('exception already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        $directory = str_replace('\\' , '/' , $namespace);
        if (! is_dir(appPath() . "Exceptions/$directory"))
            mkdir(appPath() . "Exceptions/$directory/" , 0777, true);

        $code = "<?php\n\nnamespace App\Exceptions\\$namespace;\n\nclass $className extends \Exception\n{\n\t//\n}";
        $build = fopen(appPath() . "Exceptions/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('exception created successfully');
    }

    public function build(array $arguments): void
    {
        if (! is_dir(appPath() . "Exceptions"))
            mkdir(appPath() . "Exceptions");

        $namespace = explode('/' , $arguments[2]);

        if (count($namespace) == 1){
            $this->sample($arguments , $namespace[0]);
            return;
        }

        $this->advanced($arguments , $namespace);
    }
}