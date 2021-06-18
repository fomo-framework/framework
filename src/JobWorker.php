<?php

namespace Tower;

use App\Jobs\Kernel;
use Throwable;
use Tower\Exception\QueueException;
use Workerman\Lib\Timer;

class JobWorker extends Kernel
{
    public function workerRun()
    {
        Redis::setInstance();

        return $this->listen();
    }

    protected function listen()
    {
        Timer::add(0.00001 ,  function (){
            $queue = Redis::getInstance()->lPop('towerQueue');
            if ($queue){
                $data = json_decode($queue);
                try {
                    $class = new $this->jobs[$data->queue]();
                    if (method_exists($class ,'handle'))
                        call_user_func_array([$class , 'handle'] , [$data->data]);
                }catch (Throwable $e)
                {
                    if ($data->attempts > 1){
                        try {
                            throw new QueueException('retry' , $data->queue , (array) $data->data , $e->getMessage() , $e->getFile() , $e->getLine());
                        }catch (QueueException $e)
                        {
                            $e->handle();
                        }
                        Queue::store($data->queue , (array) $data->data , $data->attempts - 1);
                    }else {
                        try {
                            throw new QueueException('failed' , $data->queue , (array) $data->data , $e->getMessage() , $e->getFile() , $e->getLine());
                        }catch (QueueException $e)
                        {
                            $e->handle();
                        }
                    }
                }
            }
        });
    }
}