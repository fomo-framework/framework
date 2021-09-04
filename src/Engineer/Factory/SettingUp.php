<?php

namespace Tower\Engineer\Factory;

use Database\Factory;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Tower\Console\Color;
use Tower\Elastic;
use Tower\Loader;
use Workerman\Worker;

class SettingUp
{
    public function run(array $arguments): void
    {
        Dotenv::createImmutable(basePath())->load();

        $app = include configPath() . "app.php";

        Loader::save([
            'app' => configPath() . "app.php" ,
            'database' => configPath() . "database.php" ,
            'elastic' => configPath() . "elastic.php" ,
            'mail' => configPath() . "mail.php" ,
            'redis' => configPath() . "redis.php" ,
            'server' => configPath() . "server.php" ,
            'errors' => languagePath() . 'validation/' . $app['locale'] . '/errors.php' ,
        ]);

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
        $capsule = new Capsule();

        $capsule->addConnection(Loader::get('database')['mysql']);

        $capsule->setAsGlobal();
    }
}