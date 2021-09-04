<?php

namespace Tower;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use RedisException;
use Tower\Crontab\Crontab;
use Tower\Scheduling\Kernel as SchedulingKernel;
use \App\Scheduling\Kernel as TasksKernel;

class SchedulingWorker
{
    public function onWorkerStart(): void
    {
        Elastic::setInstance();

        Mail::setInstance();

        $this->setRedis();

        $this->setDatabase();

        (new TasksKernel())->tasks();
        $tasks = SchedulingKernel::getInstance()->getTasks();

        foreach ($tasks as $class => $task){
            $schedule = new $class();
            if (method_exists($schedule , 'handle'))
                new Crontab($task , function () use($schedule) {
                    try {
                        call_user_func([$schedule , 'handle']);
                    }catch (Exception $e){
                        (new Log())->channel('scheduling')->error('message: ' . $e->getMessage() . ' file: ' . $e->getFile() . ' line: ' . $e->getLine());
                    }
                });
            else
                (new Log())->channel('scheduling')->warning("task $class not found");
        }
    }

    protected function setDatabase(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection(Loader::get('database')['mysql']);

        $capsule->setAsGlobal();
    }
    
    protected function setRedis(): void
    {
        try {
            Redis::setInstance();
        } catch (RedisException $e)
        {
            (new Log())->channel('scheduling')->alert($e->getMessage());
        }
    }
}