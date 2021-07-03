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

        $this->listen();
    }

    protected function listen(): void
    {
        Timer::add(1 ,  function (){
            $queue = Redis::getInstance()->lPop(env('APP_NAME' , 'tower') . 'Queue');
            if ($queue){
                $data = json_decode($queue);
                if (isset($this->jobs[$data->queue])){
                    try {
                        $class = new $this->jobs[$data->queue]();
                        if (method_exists($class ,'handle'))
                            call_user_func_array([$class , 'handle'] , [$data->data]);
                    }catch (Throwable $e)
                    {
                        $class = new $this->jobs[$data->queue]();
                        if ($data->attempts > 1){
                            (new QueueException('retry' , $data->queue , (array) $data->data , $e->getMessage() , $e->getFile() , $e->getLine()))->handle();
                            (new Queue())->store($data->queue , (array) $data->data , $data->attempts - 1);
                        }else {
                            if (method_exists($class ,'failed')){
                                call_user_func_array([$class , 'failed'] , [$data->data]);
                            }else{
                                (new QueueException('failed' , $data->queue , (array) $data->data , $e->getMessage() , $e->getFile() , $e->getLine()))->handle();
                            }
                        }
                    }
                }else{
                    $values = json_encode($data->data);
                    (new Log())->channel('queue')->alert("not found handler [queue: $data->queue] [data: $values]");
                }
            }
        });
    }
}