<?php

namespace Fomo\Commands\Tests;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'tests:run' , description: 'run tests')]
class Run extends Command
{
    protected function configure()
    {
        $this->addArgument('test' , InputArgument::OPTIONAL , 'If you want to run a specific test, please enter the test path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('test')){
            $testPath = $input->getArgument('test');
            $exec = "./vendor/bin/phpunit $testPath --color=always";
        }else{
            $testPaths = basePath('tests');
            $exec = "./vendor/bin/phpunit $testPaths --color=always";
        }

        echo shell_exec($exec);
        return self::SUCCESS;
    }
}