<?php

namespace Fomo\Commands\Build;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'build:job' , description: 'create a new job class')]
class Job extends Command
{
    protected function configure()
    {
        $this->addArgument('name' , InputArgument::REQUIRED , 'What is the name of the job?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (\class_exists('App\Jobs\\' . \str_replace('/', '\\', $input->getArgument('name')))){
            $io->error('job already exists!' , true);
            return Command::FAILURE;
        }

        $path = \explode('/' , $input->getArgument('name'));
        $className = \end($path);
        \array_pop($path);
        $classPath = \implode('/' , $path);
        $classNamespace = \implode('\\' , $path);

        if (!\is_dir(appPath('Jobs'))){
            \mkdir(appPath('Jobs'));
        }

        if (! \is_dir(appPath("Jobs/$classPath"))){
            \mkdir(appPath("Jobs/$classPath/") , 0777, true);
        }

        if (! \file_exists(appPath("Jobs/$classPath/$className.php"))){
            \touch(appPath("Jobs/$classPath/$className.php"));
        }

        \file_put_contents(
            appPath("Jobs/$classPath/$className.php") ,
            $classNamespace == ''
                ? "<?php\n\nnamespace App\Jobs;\n\nuse Fomo\Job\DispatchTrait;\n\nclass $className\n{\n\tuse DispatchTrait;\n\n\tpublic function __construct()\n\t{\n\t\t//\n\t}\n\n\tpublic function handle(): void\n\t{\n\t\t//\n\t}\n}"
                : "<?php\n\nnamespace App\Jobs\\$classNamespace;\n\nuse Fomo\Job\DispatchTrait;\n\nclass $className\n{\n\tuse DispatchTrait;\n\n\tpublic function __construct()\n\t{\n\t\t//\n\t}\n\n\tpublic function handle(): void\n\t{\n\t\t//\n\t}\n}"
        );

        $io->success('job created successfully' , true);
        return self::SUCCESS;
    }
}