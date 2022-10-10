<?php

namespace Fomo\Commands\Scheduling;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;
use Fomo\Servers\Scheduling;

#[AsCommand(name: 'scheduling:start' , description: 'start scheduling server')]
class Start extends Command
{
    protected function configure()
    {
        $this->addOption('daemonize', 'd', InputOption::VALUE_NONE, 'The program works in the background');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (schedulingServerIsRunning()){
            $io->error('scheduling server is running' , true);
            return self::FAILURE;
        }

        if (!class_exists('App\Scheduling\Kernel')){
            $io->error('There are no tasks to perform' , true);
            return self::FAILURE;
        }

        $io->success('scheduling server running…');

        if (! $input->getOption('daemonize')){
            $io->info('Press Ctrl+C to stop the server' , true);
        }

        (new Scheduling($io , (bool) $input->getOption('daemonize')))->start();

        return self::SUCCESS;
    }
}