<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:resource' , description: 'create a new resource class')]
class Resource extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the resource?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Resources\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('resource already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Resources'))){
            \mkdir(appPath('Resources'));
        }

        if (! \is_dir(appPath("Resources/$classPath"))){
            \mkdir(appPath("Resources/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Resources/$classPath/$className.php"))){
            \touch(appPath("Resources/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Resources/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php\n\nnamespace App\Resources;\n\nuse Fomo\Resource\JsonResource;\n\nclass $className extends JsonResource\n{\n\tprotected function toArray(" . '$request' . ")\n\t{\n\t\treturn " . '$request' . ";\n\t}\n}"
                : "<?php\n\nnamespace App\Resources\\$classNamespace;\n\nuse Fomo\Resource\JsonResource;\n\nclass $className extends JsonResource\n{\n\tprotected function toArray(" . '$request' . ")\n\t{\n\t\treturn " . '$request' . ";\n\t}\n}"
        );

        $io->success('resource created successfully' , true);
        return self::SUCCESS;
    }
}