<?php

namespace Fomo\Servers;

use App\Exceptions\Handler;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Fomo\Facades\Route;
use Fomo\Facades\Request as RequestFacade;
use Swoole\Server;
use Fomo\Request\Request;

class Http
{
    protected Dispatcher $dispatcher;
    protected Request $request;
    protected array $cache = [];
    protected array $config = [];
    protected array $MPCache = [];

    public function onWorkerStart(Server $server, int $workerId): void
    {
        if ($workerId == resolve('config')->get('server.additional.worker_num') - 1){
            $this->saveWorkerIds($server);
        }

        $this->setDispatcher();
        $this->setRequest($server);
    }

    public function onReceive(Server $server, $fd, $from_id, $data): void
    {
        $this->request->setBC($data , $fd);
        $firstLine = strstr($data, "\r\n", true);

        if (isset($this->MPCache[$firstLine])) {
            $method = $this->MPCache[$firstLine][0];
            $path = $this->MPCache[$firstLine][1];
        }else{
            if (count($this->MPCache) >= 256) {
                unset($this->MPCache[key($this->MPCache)]);
            }

            $MP = explode(' ', $firstLine, 3);
            $path = strstr($MP[1], '?', true);
            $path = $path === false ? $MP[1] : $path;
            $method = $MP[0];
            $this->MPCache[$firstLine] = [$method , $path];
        }

        $cache = $this->cache[$method.$path] ?? null;

        if (!is_null($cache)){
            if ($cache[2] !== null) {
                foreach ($cache[2] as $middleware) {
                    $callback = $middleware->handle($this->request);
                    if ($callback !== true) {
                        $server->send($fd, $callback);
                        return;
                    }
                }
            }

            try {
                $server->send($fd, $cache[0]->{$cache[1]}($this->request , ...$cache[3]));
            } catch (\Throwable | \Exception $e) {
                $server->send($fd, (new Handler())->render($e, $this->request));
            }
            
            return;
        }

        $routeInfo = $this->dispatcher->dispatch($method , $path);
        if ($routeInfo[0] === 1) {
            if (count($this->cache) > 1024){
                $this->cache = [];
            }
            $this->cache[$method.$path] = [
                $routeInfo[1][0] ,
                $routeInfo[1][1] ,
                $routeInfo[1]['middleware'] ?? null ,
                $routeInfo[2]
            ];

            if (isset($routeInfo[1]['middleware'])) {
                foreach ($routeInfo[1]['middleware'] as $middleware) {
                    $callback = $middleware->handle($this->request);
                    if ($callback !== true) {
                        $server->send($fd, $callback);
                        return;
                    }
                }
            }

            try {
                $server->send($fd, $routeInfo[1][0]->{$routeInfo[1][1]}($this->request, ...$routeInfo[2]));
            } catch (\Throwable | \Exception $e) {
                $server->send($fd, (new Handler())->render($e, $this->request));
            }
        } elseif ($routeInfo[0] === 0){
            $server->send($fd , (new Handler())->notFoundHttpException($this->request));
        } else {
            $server->send($fd , (new Handler())->notAllowedHttpException($this->request));
        }
    }

    protected function saveWorkerIds(Server $server): void
    {
        $workerIds = [];
        for ($i = 0; $i < resolve('config')->get('server.additional.worker_num'); $i++){
            $workerIds[$i] = $server->getWorkerPid($i);
        }

        setMasterProcessId($server->getMasterPid());
        setManagerProcessId($server->getManagerPid());
        setWorkerProcessIds($workerIds);
    }

    protected function setDispatcher(): void
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            require_once basePath("router/router.php");
            foreach (Route::getRoutes() as $method => $callbacks){
                foreach ($callbacks as $callback){
                    $r->addRoute($method, $callback[0] , $callback[1]);
                }
            }
        });
    }

    protected function setRequest(Server $server): void
    {
        $this->request = RequestFacade::getInstance();

        RequestFacade::setServer($server);
        RequestFacade::setDispatcher($this->dispatcher);
    }
}
