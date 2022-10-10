<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:task' , description: 'create a new task class')]
class Task extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the task?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Scheduling\Tasks\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('task already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Scheduling'))){
            \mkdir(appPath('Scheduling'));
        }

        if (! \class_exists('App\Scheduling\Kernel')){
            \touch(appPath('Scheduling/Kernel.php'));

            \file_put_contents(
                appPath('Scheduling/Kernel.php') ,
                "<?php\n\nnamespace App\Scheduling;\n\nuse Fomo\Scheduling\Scheduler;\n\nclass Kernel\n{\n\tpublic function " . 'tasks(): void' . "\n\t{\n//        (new Scheduler())->call(Task::class)->everyMinutes();\n\t}\n}"
            );
        }

        if (! \is_dir(appPath('Scheduling/Tasks'))){
            \mkdir(appPath('Scheduling/Tasks'));
        }

        if (! \is_dir(appPath("Scheduling/Tasks/$classPath"))){
            \mkdir(appPath("Scheduling/Tasks/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Scheduling/Tasks/$classPath/$className.php"))){
            \touch(appPath("Scheduling/Tasks/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Scheduling/Tasks/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php\n\nnamespace App\Scheduling\Tasks;\n\nclass $className\n{\n\tpublic function handle(): void\n\t{\n\t\t//\n\t}\n}"
                : "<?php\n\nnamespace App\Scheduling\Tasks\\$classNamespace;\n\nclass $className\n{\n\tpublic function handle(): void\n\t{\n\t\t//\n\t}\n}"
        );

        $io->success('task created successfully' , true);
        return self::SUCCESS;
    }
}