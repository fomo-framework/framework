<?php

namespace Tower;

use Tower\Crontab\Crontab;
use Tower\Scheduling\Kernel as SchedulingKernel;
use \App\Tasks\Kernel as TasksKernel;

class SchedulingWorker
{
    public function workerRun()
    {
        (new TasksKernel())->tasks();
        $tasks = SchedulingKernel::getInstance()->getTasks();

        foreach ($tasks as $class => $task){
            $schedule = new $class();
            if (method_exists($schedule , 'handle'))
                new Crontab($task , function () use($schedule) {
                    call_user_func([$schedule , 'handle']);
                });
            else
                (new Log())->channel('scheduling')->error("task $class not found");
        }
    }
}