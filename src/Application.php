<?php

namespace Tower;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;

class Application
{
    protected ?Dispatcher $dispatcher = null;

    protected function database(): void
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
        Elastic::setInstance();

        $this->database();
    }

    public function onMessage(TcpConnection $connection , Request $request): void
    {
        try {
            $dispatch = $this->dispatcher->dispatch($request->method() , $request->path());
            switch ($dispatch[0]) {
                case Dispatcher::NOT_FOUND:
                    $connection->send(json([
                        'message' => 'not found'
                    ] , Response::HTTP_NOT_FOUND));
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    $connection->send(json([
                        "message" => "this is route not supported {$dispatch[1][0]} method"
                    ] , Response::HTTP_METHOD_NOT_ALLOWED));
                    break;
                case Dispatcher::FOUND:
                    if (! empty($dispatch[1]['middleware'])){
                        foreach ($dispatch[1]['middleware'] as $middleware){
                            $call = new $middleware();
                            $callMiddleware = $call->handle();
                            if ($callMiddleware !== true){
                                $connection->send($callMiddleware);
                                return;
                            }
                        }
                    }
                    Request::setInstance($request);
                    $controller = new $dispatch[1][0];
                    $method = $dispatch[1][1];

                    $call = call_user_func_array([$controller , $method] , $dispatch[2] ?? []);
                    $connection->send($call);
                    break;
            }
        } catch (\Throwable $e) {
            $log = fopen(storagePath() . "logs/tower.log", 'a');
            fwrite($log, (string)$e);
            fclose($log);
            $connection->send(json([
                'message' => "The operation failed"
            ] , Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}