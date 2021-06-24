<?php

namespace Tower;

use Carbon\Carbon;
use Elasticsearch\Common\Exceptions\ServerErrorResponseException;
use FastRoute\Dispatcher;
use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\NotFoundException;
use App\Exceptions\OnMessageException;
use FastRoute\RouteCollector;
use Throwable;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;

class Application
{
    protected Dispatcher $dispatcher;
    protected Request $request;

    public function onWorkerStart(): void
    {
        $router = new Router();
        $router->group([] , function ($router){
            include basePath() . "/routes/api.php";
        });

        $this->dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) use ($router) {
            foreach ($router->getRoutes() as $method => $callback)
                $r->addRoute($method, $callback[0][0], $callback[0][1]);
        });

        Http::requestClass(Request::class);

        Elastic::setInstance();

        $this->setRedis();

        $this->setDatabase();
    }

    public function onMessage(TcpConnection $connection , Request $request): void
    {
        try {
            $dispatch = $this->dispatcher->dispatch($request->method() , $request->path());
            switch ($dispatch[0]) {
                case 0:
                    try{
                        throw new NotFoundException();
                    }catch(NotFoundException $e){
                        $connection->send($e->handle());
                    }
                    break;
                case 2:
                    try{
                        throw new MethodNotAllowedException($dispatch[1][0]);
                    }catch(MethodNotAllowedException $e){
                        $connection->send($e->handle());
                    }
                    break;
                case 1:
                    $this->request = $request;
                    if (! empty($dispatch[1]['middleware'])){
                        Request::setVariables($dispatch[2]);
                        foreach ($dispatch[1]['middleware'] as $middleware){
                            $call = call_user_func_array([new $middleware() , 'handle'] , [$request]);
                            if ($call !== true){
                                $connection->send($call);
                                return;
                            }
                        }
                    }

                    $class = new $dispatch[1][0]();
                    $variables = array_values($dispatch[2]);

                    $connection->send(call_user_func_array([$class , $dispatch[1][1]] , [$request , ...$variables]));
                    break;
            }
        } catch (Throwable $e) {
            Log::critical('message: ' . $e->getMessage() . ' file: ' . $e->getFile() . ' line: ' . $e->getLine());
            try{
                throw new OnMessageException($e);
            }catch(OnMessageException $e){
                $connection->send($e->handle());
            }
        }
    }

    protected function setDatabase(): void
    {
        $config = include configPath() . "database.php";

        $capsule = new Capsule();

        $capsule->addConnection($config['mysql']);

        $capsule->setAsGlobal();

        Paginator::currentPageResolver(function ($pageName = 'page')  {
            $page = $this->request->get($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });
    }

    protected function setRedis(): void
    {
        try {
            Redis::setInstance();
        } catch (\RedisException $e)
        {
            Log::alert($e->getMessage());
        }
    }
}
