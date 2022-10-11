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

        if (posix_kill(getMasterProcessId(), SIG_DFL)){
            posix_kill(getMasterProcessId(), SIGTERM);
        }

        if (posix_kill(getManagerProcessId(), SIG_DFL)){
            posix_kill(getManagerProcessId(), SIGTERM);
        }

        if (posix_kill(getWatcherProcessId(), SIG_DFL)){
            posix_kill(getWatcherProcessId(), SIGTERM);
        }

        foreach (getWorkerProcessIds() as $processId) {
            if (posix_kill($processId, SIG_DFL)){
                posix_kill($processId, SIGTERM);
            }
        }

        sleep(1);

        $io->success('stopping server...' , true);
        return self::SUCCESS;
    }
}