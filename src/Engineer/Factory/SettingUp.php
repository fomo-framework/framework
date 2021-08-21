<?php

namespace Tower\Engineer\Factory;

use Database\Factory;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Tower\Console\Color;
use Tower\Elastic;
use Workerman\Worker;

class SettingUp
{
    public function run(array $arguments): void
    {
        $dotenv = Dotenv::createImmutable(basePath());
        $dotenv->load();

        $worker = new Worker();

        $worker->name = 'factory';

        $worker->onWorkerStart = function (){
            Elastic::setInstance();

            $this->setDatabase();

            $time = microtime(true);

            (new Factory())->run();

            $time = microtime(true) - $time;

            echo Color::LIGHT_WHITE . '--------------------------------------------------------------------------------------' . PHP_EOL;
            echo Color::success("the operation done successfully ($time ms)");
            echo Color::LIGHT_WHITE . 'please press CTRL+C to exit' . PHP_EOL;
        };

        Worker::runAll();
    }

    protected function setDatabase(): void
    {
        $config = include configPath() . "database.php";

        $capsule = new Capsule();

        $capsule->addConnection($config['mysql']);

        $capsule->setAsGlobal();
    }
}