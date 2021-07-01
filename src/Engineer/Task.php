<?php

namespace Tower\Engineer;

use Tower\Console\Color;

class Task
{
    protected function checkBaseDir(): void
    {
        if (! is_dir(appPath() . "Scheduling"))
            mkdir(appPath() . "Scheduling");

        if (! class_exists('App\Scheduling\Kernel')){
            $code = "<?php\n\nnamespace App\Scheduling;\n\nuse Tower\Scheduling\Scheduler;\n\nclass Kernel\n{\n\tpublic function " . 'tasks()' . "\n\t{\n//        (new Scheduler())->call(Task::class)->everyMinutes();\n\t}\n}";
            $build = fopen(appPath() . "Scheduling/Kernel.php", 'a');
            fwrite($build , $code);
            fclose($build);
        }

        if (! is_dir(appPath() . "Scheduling/Tasks"))
            mkdir(appPath() . "Scheduling/Tasks");
    }

    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('App\Scheduling\Tasks\\' . $arguments[2])){
            echo Color::error('task already exists!');
            return;
        }

        $code = "<?php\n\nnamespace App\Scheduling\Tasks;\n\nclass $namespace\n{\n\tpublic function handle(): void\n\t{\n\t\t//\n\t}\n}";
        $build = fopen(appPath() . "Scheduling/Tasks/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('task created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $check = implode('\\' , $namespace);
        if (class_exists('App\Scheduling\Tasks\\' . $check)){
            echo Color::error('task already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        $directory = str_replace('\\' , '/' , $namespace);
        if (! is_dir(appPath() . "Scheduling/Tasks/$directory"))
            mkdir(appPath() . "Scheduling/Tasks/$directory/" , 0777, true);

        $code = "<?php\n\nnamespace App\Scheduling\Tasks\\$namespace;\n\nclass $className\n{\n\tpublic function handle(): void\n\t{\n\t\t//\n\t}\n}";
        $build = fopen(appPath() . "Scheduling/Tasks/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('task created successfully');
    }


    public function build(array $arguments): void
    {
        $this->checkBaseDir();

        $namespace = explode('/' , $arguments[2]);

        if (count($namespace) == 1){
            $this->sample($arguments , $namespace[0]);
            return;
        }

        $this->advanced($arguments , $namespace);
    }
}
