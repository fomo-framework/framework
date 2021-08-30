<?php

namespace Tower;

use App\Jobs\Kernel;
use Illuminate\Database\Capsule\Manager as Capsule;
use Exception;
use RedisException;
use Tower\Exception\QueueException;
use Workerman\Lib\Timer;

class JobWorker extends Kernel
{
    public function onWorkerStart(): void
    {
        Elastic::setInstance();

        Mail::setInstance();

        $this->setRedis();

        $this->setDatabase();

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
                    }catch (Exception $e)
                    {
                        $class = new $this->jobs[$data->queue]();
                        if ($data->attempts > 1){
                            (new QueueException('retry' , $data->queue , (array) $data->data , $e->getMessage() , $e->getFile() , $e->getLine()))->handle();
                            (new Queue())->store($data->queue , (array) $data->data , $data->attempts - 1);
                        }else {
                            if (method_exists($class ,'failed')){
                                try {
                                    call_user_func_array([$class , 'failed'] , [$data->data]);
                                }catch (Exception $exception){
                                    (new QueueException('failed' , $data->queue , (array) $data->data , $exception->getMessage() , $exception->getFile() , $exception->getLine()))->handle();
                                }
                            }else{
                                $values = json_encode($data->data);
                                (new Log())->channel('queue')->alert("not found failed handler [queue: $data->queue] [data: $values]");
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

    protected function setDatabase(): void
    {
        $config = include configPath() . "database.php";

        $capsule = new Capsule();

        $capsule->addConnection($config['mysql']);

        $capsule->setAsGlobal();
    }

    protected function setRedis(): void
    {
        try {
            Redis::setInstance();
        } catch (RedisException $e)
        {
            (new Log())->alert($e->getMessage());
        }
    }
}