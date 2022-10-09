<?php

namespace Fomo\Commands\Server;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'server:stop' , description: 'stop http server')]
class Stop extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        if (! httpServerIsRunning()){
            $io->error('server is not running...' , true);
            return self::FAILURE;
        }

        if (Process::kill(getMasterProcessId(), 0)){
            Process::kill(getMasterProcessId(), SIGKILL);
        }

        if (Process::kill(getManagerProcessId(), 0)){
            Process::kill(getManagerProcessId(), SIGKILL);
        }

        if (Process::kill(getWatcherProcessId(), 0)){
            Process::kill(getWatcherProcessId(), SIGKILL);
        }

        foreach (getWorkerProcessIds() as $processId) {
            if (Process::kill($processId, 0)){
                Process::kill($processId, SIGKILL);
            }
        }

        sleep(1);

        $io->success('stopping server...' , true);
        return self::SUCCESS;
    }
}