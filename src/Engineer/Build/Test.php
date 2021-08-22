<?php

namespace Tower\Engineer\Build;

use Tower\Console\Color;

class Test
{
    protected function sample(array $arguments , string $namespace): void
    {
        if (class_exists('Tests\Units\\' . $arguments[2])){
            echo Color::error('test already exists!');
            return;
        }

        $code = "<?php \n\nnamespace Tests\Units;\n\nuse Tests\TestCase;\n\nclass $namespace extends TestCase\n{\n\tpublic function testTrueAssertsToTrue()\n\t{\n\t\t"."\$"."this".'->assertTrue(true)'.";\n\t}\n}";
        $build = fopen(basePath() . "/tests/Units/$arguments[2].php", 'a');

        fwrite($build , $code);

        fclose($build);
        echo Color::success('test created successfully!');
    }

    protected function advanced(array $arguments , array $namespace): void
    {
        $checkExist = implode('\\' , $namespace);

        if (class_exists('Tests\Units\\' . $checkExist)){
            echo Color::error('test already exists!');
            return;
        }

        $lastKey = array_key_last($namespace);
        $className = $namespace[$lastKey];
        array_pop($namespace);
        $namespace = implode('\\' , $namespace);

        $directory = str_replace('\\' , '/' , $namespace);
        if (! is_dir(basePath() . "/tests/Units/$directory"))
            mkdir(basePath() . "/tests/Units/$directory/" , 0777, true);

        $code = "<?php \n\nnamespace Tests\Units\\$namespace;\n\nuse Tests\TestCase;\n\nclass $className extends TestCase\n{\n\tpublic function testTrueAssertsToTrue()\n\t{\n\t\t"."\$"."this".'->assertTrue(true)'.";\n\t}\n}";

        $build = fopen(basePath() . "/tests/Units/$arguments[2].php", 'a');
        fwrite($build , $code);
        fclose($build);

        echo Color::success('tests created successfully');
    }

    public function build(array $arguments): void
    {
        if (! is_dir(basePath() . "/tests/Units"))
            mkdir(basePath() . "/tests/Units");

        $namespace = explode('/' , $arguments[2]);

        if (count($namespace) == 1){
            $this->sample($arguments , $namespace[0]);
            return;
        }

        $this->advanced($arguments , $namespace);
    }
}