<?php

namespace Tower\Engineer;

use Tower\Console\Color;

class Resource
{
    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('App\Resources\\' . $arguments[2])){
            echo Color::error('resource already exists!');
            return;
        }

        $code = "<?php\n\nnamespace App\Resources;\n\nuse Tower\JsonResource;\n\nclass $namespace extends JsonResource\n{\n\tprotected function toArray(" . '$request' . ")\n\t{\n\t\treturn " . '$request' . ";\n\t}\n}";
        $build = fopen(appPath() . "Resources/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('resource created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $checkExist = implode('\\' , $namespace);
        if (class_exists('App\Resources\\' . $checkExist)){
            echo Color::error('resource already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        $directory = str_replace('\\' , '/' , $namespace);
        if (! is_dir(appPath() . "Resources/$directory"))
            mkdir(appPath() . "Resources/$directory/" , 0777, true);

        $code = "<?php\n\nnamespace App\Resources\\$namespace;\n\nuse Tower\JsonResource;\n\nclass $className extends JsonResource\n{\n\tprotected function toArray(" . '$request' . ")\n\t{\n\t\treturn " . '$request' . ";\n\t}\n}";
        $build = fopen(appPath() . "Resources/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('resource created successfully');
    }

    public function build(array $arguments): void
    {
        if (! is_dir(appPath() . "Resources"))
            mkdir(appPath() . "Resources");

        $namespace = explode('/' , $arguments[2]);

        if (count($namespace) == 1){
            $this->sample($arguments , $namespace[0]);
            return;
        }

        $this->advanced($arguments , $namespace);
    }
}