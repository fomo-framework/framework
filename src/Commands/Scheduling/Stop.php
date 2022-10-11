<?php

namespace Fomo\Commands\Scheduling;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'scheduling:stop' , description: 'stop scheduling server')]
class Stop extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (! schedulingServerIsRunning()){
            $io->error('server is not running...' , true);
            return self::FAILURE;
        }

        if (posix_kill(getSchedulingProcessId(), 0)){
            posix_kill(getSchedulingProcessId(), SIGKILL);
        }

        sleep(1);

        $io->success('stopping server...' , true);
        return self::SUCCESS;
    }
}