<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:middleware' , description: 'create a new middleware class')]
class Middleware extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the middleware?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Middlewares\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('middleware already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Middlewares'))){
            \mkdir(appPath('Middlewares'));
        }

        if (! \is_dir(appPath("Middlewares/$classPath"))){
            \mkdir(appPath("Middlewares/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Middlewares/$classPath/$className.php"))){
            \touch(appPath("Middlewares/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Middlewares/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php\n\nnamespace App\Middlewares;\n\nuse Fomo\Request\Request;\n\nclass $className\n{\n\tpublic function handle(Request " . '$request' . "): bool|string\n\t{\n\t\treturn true;\n\t}\n}"
                : "<?php\n\nnamespace App\Middlewares\\$classNamespace;\n\nuse Fomo\Request\Request;\n\nclass $className\n{\n\tpublic function handle(Request " . '$request' . "): bool|string\n\t{\n\t\treturn true;\n\t}\n}"
        );

        $io->success('middleware created successfully' , true);
        return self::SUCCESS;
    }
}