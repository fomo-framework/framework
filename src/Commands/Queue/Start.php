<?php

namespace Fomo\Commands\Queue;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;
use Fomo\Servers\Queue;

#[AsCommand(name: 'queue:start' , description: 'start queue server')]
class Start extends Command
{
    protected function configure()
    {
        $this->addOption('daemonize', 'd', InputOption::VALUE_NONE, 'The program works in the background');
        $this->addOption('retry', 'r', InputOption::VALUE_REQUIRED, 'retry queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (queueServerIsRunning()){
            $io->error('queue server is running' , true);
            return self::FAILURE;
        }

        $io->success('queue server running…');

        if (! $input->getOption('daemonize')){
            $io->info('Press Ctrl+C to stop the server' , true);
        }

        (new Queue($io , $input , (bool) $input->getOption('daemonize')))->start();

        return self::SUCCESS;
    }
}