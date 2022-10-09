<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:service' , description: 'create a new service class')]
class Service extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the service?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Services\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('service already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Services'))){
            \mkdir(appPath('Services'));
        }

        if (! \is_dir(appPath("Services/$classPath"))){
            \mkdir(appPath("Services/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Services/$classPath/$className.php"))){
            \touch(appPath("Services/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Services/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php\n\nnamespace App\Services;\n\nuse Swoole\Server;\nuse Tower\Request\Request;\n\nclass $className\n{\n\tpublic function boot(Server " . '$server = null' . ", Request " . '$request = null' . "): void\n\t{\n\t\t//\n\t}\n}"
                : "<?php\n\nnamespace App\Services\\$classNamespace;\n\nuse Swoole\Server;\nuse Tower\Request\Request;\n\nclass $className\n{\n\tpublic function boot(Server " . '$server = null' . ", Request " . '$request = null' . "): void\n\t{\n\t\t//\n\t}\n}"
        );

        $io->success('service created successfully' , true);
        return self::SUCCESS;
    }
}