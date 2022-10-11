<?php

namespace Fomo\Commands\Queue;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'queue:status' , description: 'status server')]
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
                    'queue' , (!is_null(getQueueProcessId()) && posix_kill(getQueueProcessId(), SIG_DFL)) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getQueueProcessId()
                ]
            ]);
        $table->setVertical();
        $table->render();
        $output->writeln('');

        return self::SUCCESS;
    }
}