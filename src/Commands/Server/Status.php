<?php

namespace Fomo\Commands\Server;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'server:status' , description: 'status http server')]
class Status extends Command
{
    protected function configure(): void
    {
        $this->addOption('full', 'f', InputOption::VALUE_NONE, 'Display full information about the status of processes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('full')){
            $this->fullInformation($output);
        }else {
            $this->generalInformation($output);
        }

        return self::SUCCESS;
    }

    protected function fullInformation(OutputInterface $output): void
    {
        $rows = [
            [
                'manager' , !is_null(getManagerProcessId()) && Process::kill(getManagerProcessId(), 0) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getManagerProcessId()
            ] ,
            [
                'master' , !is_null(getMasterProcessId()) && Process::kill(getMasterProcessId(), 0) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getMasterProcessId()
            ] ,
            [
                'watcher' , !is_null(getWatcherProcessId()) && Process::kill(getWatcherProcessId(), 0) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getWatcherProcessId()
            ] ,
            [
                'factory' , !is_null(getFactoryProcessId()) && Process::kill(getFactoryProcessId(), 0) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getWatcherProcessId()
            ] ,
            [
                'queue' , (!is_null(getQueueProcessId()) && Process::kill(getQueueProcessId(), 0)) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getQueueProcessId()
            ]
        ];

        foreach (getWorkerProcessIds() as $index => $workerId){
            $index++;
            if (Process::kill($workerId, 0)){
                $rows[] = [
                    "process $index" ,
                    '<fg=#C3E88D;options=bold> ACTIVE </>' ,
                    $workerId
                ];
                continue;
            }
            $rows[] = [
                "worker $index" ,
                '<fg=#FF5572;options=bold> DEACTIVE </>' ,
                $workerId
            ];
        }

        $output->writeln('');
        $table = new Table($output);
        $table
            ->setHeaderTitle('full information')
            ->setHeaders([
                '<fg=#FFCB8B;options=bold> Process Name </>' ,
                '<fg=#FFCB8B;options=bold> Process Status </>' ,
                '<fg=#FFCB8B;options=bold> Process PID </>'
            ])
            ->setRows($rows);
        $table->setVertical();
        $table->render();
        $output->writeln('');
    }

    protected function generalInformation(OutputInterface $output): void
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
                    'server' , (!is_null(getManagerProcessId()) && Process::kill(getManagerProcessId(), 0)) && (!is_null(getMasterProcessId()) && Process::kill(getMasterProcessId(), 0)) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getManagerProcessId()
                ] ,
                [
                    'watcher' , !is_null(getWatcherProcessId()) && Process::kill(getWatcherProcessId(), 0) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getWatcherProcessId()
                ] ,
                [
                    'factory' , !is_null(getFactoryProcessId()) && Process::kill(getFactoryProcessId(), 0) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getFactoryProcessId()
                ] ,
                [
                    'queue' , (!is_null(getQueueProcessId()) && Process::kill(getQueueProcessId(), 0)) ? '<fg=#C3E88D;options=bold> ACTIVE </>' : '<fg=#FF5572;options=bold> DEACTIVE </>' , getQueueProcessId()
                ]
            ]);
        $table->setVertical();
        $table->render();
        $output->writeln('');
    }
}