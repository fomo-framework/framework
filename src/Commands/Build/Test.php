<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:test' , description: 'create a new test class')]
class Test extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the test?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('Tests\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('test already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(basePath('tests'))){
            \mkdir(basePath('tests'));
        }

        if (! \is_dir(basePath("tests/$classPath"))){
            \mkdir(basePath("tests/$classPath/") , 0777, true);
        }

        if (! \file_exists(basePath("tests/$classPath/$className.php"))){
            \touch(basePath("tests/$classPath/$className.php"));
        }

        \file_put_contents(
            basePath("tests/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php \n\nnamespace Tests;\n\nclass $className extends TestCase\n{\n\tpublic function testExample()\n\t{\n\t\t"."\$"."this".'->assertTrue(true)'.";\n\t}\n}"
                : "<?php \n\nnamespace Tests\\$classNamespace;\n\nuse Tests\TestCase;\n\nclass $className extends TestCase\n{\n\tpublic function testExample()\n\t{\n\t\t"."\$"."this".'->assertTrue(true)'.";\n\t}\n}"
        );

        $io->success('test created successfully' , true);
        return self::SUCCESS;
    }
}