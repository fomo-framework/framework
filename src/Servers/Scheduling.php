<?php

namespace Fomo\Servers;

use App\Scheduling\Kernel as TasksKernel;
use Carbon\Carbon;
use Swoole\Process;
use Fomo\Console\Style;
use Fomo\Scheduling\Crontab\Crontab;
use Fomo\Scheduling\Kernel as SchedulingKernel;

class Scheduling
{
    public function __construct(
        protected Style $io,
        protected bool $daemonize
    ){}

    public function start(): void
    {
        $process = new Process(function (Process $process) {
            setSchedulingProcessId($process->pid);
            $this->runServices();

            (new TasksKernel())->tasks();
            $tasks = SchedulingKernel::getInstance()->getTasks();

            foreach ($tasks as $class => $task) {
                new Crontab($task, function () use ($class) {
                    try {
                        (new $class)->handle();
                        if (!$this->daemonize){
                            $this->io->done("$class .............. " . Carbon::now());
                        }
                    } catch (\Exception) {
                        if (!$this->daemonize){
                            $this->io->failed("$class .............. " . Carbon::now());
                        }
                    }
                });

            }

        });

        $process->start();

        if (!$this->daemonize){
            $process->read();
        }
    }

    protected function runServices(): void
    {
        foreach (config('server.services') as $service){
            (new $service)->boot();
        }
    }
}