<?php

namespace Tower\Engineer;

use Tower\Console\Color;

class Job
{
    protected function checkBaseDir(): void
    {
        if (! is_dir(appPath() . "Jobs"))
            mkdir(appPath() . "Jobs");

        if (! class_exists('App\Jobs\Kernel')){
            $code = "<?php\n\nnamespace App\Jobs;\n\nclass Kernel\n{\n\tprotected array ".'$jobs'." = [\n//\t\t'queueName' => Job::class\n\t];\n}";
            $build = fopen(appPath() . "Jobs/Kernel.php", 'a');
            fwrite($build , $code);
            fclose($build);
        }

        if (! is_dir(appPath() . "Jobs/Dispatching"))
            mkdir(appPath() . "Jobs/Dispatching");
    }

    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('App\Jobs\Dispatching\\' . $arguments[2])){
            echo Color::error('job already exists!');
            return;
        }

        $code = "<?php\n\nnamespace App\Jobs\Dispatching;\n\nuse stdClass;\n\nclass $namespace\n{\n\tpublic function handle(stdClass " . '$data' . "): void\n\t{\n\t\t//\n\t}\n}";
        $build = fopen(appPath() . "Jobs/Dispatching/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('job created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $check = implode('\\' , $namespace);
        if (class_exists('App\Jobs\Dispatching\\' . $check)){
            echo Color::error('job already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        if (! is_dir(appPath() . "Jobs/Dispatching/" . $namespace))
            mkdir(appPath() . "Jobs/Dispatching/" . $namespace);

        $code = "<?php\n\nnamespace App\Jobs\Dispatching\\$namespace;\n\nuse stdClass;\n\nclass $className\n{\n\tpublic function handle(stdClass " . '$data' . "): void\n\t{\n\t\t//\n\t}\n}";
        $build = fopen(appPath() . "Jobs/Dispatching/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('job created successfully');
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