<?php

namespace Fomo\Commands\Scheduling;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'scheduling:status' , description: 'status scheduling server')]
class Status extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $table = new Table($output);
        $table
            ->setHeaderTitle('general information')
            ->setHeaders([
                '<fg=#FFCB8B;options=bold> Process Name </>' ,
                '<fg=#FFCB8B;options=bold> Process Status </>' ,
                '<fg=#FFCB8B;options=bold> Process PID </>'
            ])
            ->setRows([
                [
                    'scheduler' , (!is_null(getSchedulingProcessId()) && posix_kill(getSchedulingProcessId(), 0)) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getSchedulingProcessId()
                ]
            ]);
        $table->setVertical();
        $table->render();
        $output->writeln('');

        return self::SUCCESS;
    }
}