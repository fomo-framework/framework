<?php

namespace Fomo\Servers;

use Carbon\Carbon;
use Swoole\Process;
use Symfony\Component\Console\Input\InputInterface;
use Fomo\Console\Style;
use Fomo\Redis\Redis;

class Queue
{
    public function __construct(
        protected Style $io,
        protected InputInterface $input,
        protected bool $daemonize
    ){}

    public function start(): void
    {
        $process = new Process(function (Process $process) {
            setQueueProcessId($process->pid);
            $this->runServices();

            while (true){
                $queue = Redis::getInstance()->lPop(env('APP_NAME' , 'tower') . 'Queue');
                if ($queue){
                    $data = json_decode($queue , true);
                    try {
                        (new $data['dispatcher'](...$data['parameters']))->handle();

                        if (!$this->daemonize){
                            $this->io->done("{$data['dispatcher']} .............. " . Carbon::now());
                        }
                    } catch (\Exception){
                        if (!$this->daemonize){
                            $this->io->failed("{$data['dispatcher']} .............. " . Carbon::now());
                        }

                        if (($this->input->getOption('retry') && !isset($data['retry'])) || (isset($data['retry']) && $data['retry'] > 0)){
                            Redis::getInstance()->rPush(env('APP_NAME' , 'tower') . 'Queue' , json_encode([
                                'dispatcher' => $data['dispatcher'] ,
                                'parameters' => [...$data['parameters']] ,
                                'retry' => (isset($data['retry'])) ? $data['retry'] - 1 : (int) $this->input->getOption('retry') - 1 ,
                            ]));
                            continue;
                        }

                        try {
                            (new $data['dispatcher'](...$data['parameters']))->failed();
                        } catch (\Exception){}
                    }
                }

                if (!$queue){
                    sleep(1);
                }
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