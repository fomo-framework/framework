<?php

namespace Fomo\Commands\Factory;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use App\Database\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;

#[AsCommand(name: 'factory:start' , description: 'start factory server')]
class Start extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        $time = microtime(true);
        (new Process(function (Process $process){
            setFactoryProcessId($process->pid);
            (new Factory())->run();
        }))->start();
        $time = microtime(true) - $time;

        $io->success("The operation done successfully ($time ms)");
        return self::SUCCESS;
    }
}