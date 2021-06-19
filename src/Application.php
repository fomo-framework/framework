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
    protected ?Dispatcher $dispatcher = null;

    public function onWorkerStart(): void
    {
        $router = new Router();
        $router->group([] , function ($router){
            include basePath() . "/routes/api.php";
        });

        $this->dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) use ($router) {
            foreach ($router->getRoutes() as $method => $callbacks)
                foreach ($callbacks as $callback)
                    $r->addRoute($method, $callback[0], $callback[1]);
        });

        Http::requestClass(Request::class);

        $this->setElastic();
        $this->setRedis();

        $this->setDatabase();
    }

    public function onMessage(TcpConnection $connection , Request $request): void
    {
        try {
            $dispatch = $this->dispatcher->dispatch($request->method() , $request->path());
            switch ($dispatch[0]) {
                case Dispatcher::NOT_FOUND:
                    try{
                        throw new NotFoundException();
                    }catch(NotFoundException $e){
                        $connection->send($e->handle());
                    }
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    try{
                        throw new MethodNotAllowedException($dispatch[1][0]);
                    }catch(MethodNotAllowedException $e){
                        $connection->send($e->handle());
                    }
                    break;
                case Dispatcher::FOUND:
                    Request::setInstance($request);
                    if (! empty($dispatch[1]['middleware'])){
                        foreach ($dispatch[1]['middleware'] as $middleware){
                            $middleware = new $middleware();
                            $callMiddleware = call_user_func([$middleware , 'handle']);
                            if ($callMiddleware !== true){
                                $connection->send($callMiddleware);
                                return;
                            }
                        }
                    }
                    $controller = new $dispatch[1][0];
                    $method = $dispatch[1][1];

                    $call = call_user_func_array([$controller , $method] , $dispatch[2] ?? []);
                    $connection->send($call);
                    break;
            }
        } catch (Throwable $e) {
                $this->logStore($e->getMessage() , $e->getFile() , $e->getLine());

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
            $page = \request($pageName);

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
            $this->logStore($e->getMessage() , $e->getFile() , $e->getLine());
        }
    }

    protected function setElastic(): void
    {
        try {
            Elastic::setInstance();
        } catch (ServerErrorResponseException $e)
        {
            $this->logStore($e->getMessage() , $e->getFile() , $e->getLine());
        }
    }

    protected function logStore(string $message , string $file , string $line): void
    {
        $log = fopen(storagePath() . "logs/tower.log", 'a');
        fwrite($log,'time : ' . Carbon::now() . ' | ' .
            'message : ' . $message . ' | ' .
            'file : ' . $file . ' | ' .
            'line : ' . $line .
            PHP_EOL .
            '<------------------------------------------------------------------------------>' .
            PHP_EOL
        );
        fclose($log);
    }
}
