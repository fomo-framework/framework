<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:controller' , description: 'create a new controller class')]
class Controller extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the controller?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Controllers\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('controller already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Controllers'))){
            \mkdir(appPath('Controllers'));
        }

        if (! \is_dir(appPath("Controllers/$classPath"))){
            \mkdir(appPath("Controllers/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Controllers/$classPath/$className.php"))){
            \touch(appPath("Controllers/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Controllers/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php \n\nnamespace App\Controllers;\n\nclass $className extends Controller\n{\n\t//\n}"
                : "<?php \n\nnamespace App\Controllers\\$classNamespace;\n\nuse App\Controllers\Controller;\n\nclass $className extends Controller\n{\n\t//\n}"
        );

        $io->success('controller created successfully' , true);
        return self::SUCCESS;
    }
}