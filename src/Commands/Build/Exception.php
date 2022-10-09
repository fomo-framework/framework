<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:exception' , description: 'create a new exception class')]
class Exception extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the exception?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Exceptions\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('exception already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Exceptions'))){
            \mkdir(appPath('Exceptions'));
        }

        if (! \is_dir(appPath("Exceptions/$classPath"))){
            \mkdir(appPath("Exceptions/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Exceptions/$classPath/$className.php"))){
            \touch(appPath("Exceptions/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Exceptions/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php\n\nnamespace App\Exceptions;\n\nclass $className extends \\Exception\n{\n\t//\n}"
                : "<?php\n\nnamespace App\Exceptions\\$classNamespace;\n\nclass $className extends \\Exception\n{\n\t//\n}"
        );

        $io->success('exception created successfully' , true);
        return self::SUCCESS;
    }
}