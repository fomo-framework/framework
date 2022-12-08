<?php

namespace Fomo\Servers;

use App\Exceptions\Handler;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Fomo\Auth\Auth;
use Fomo\Cache\Cache;
use Fomo\Config\Config;
use Fomo\Facades\Route;
use Fomo\Facades\Setter;
use Fomo\Facades\Request as RequestFacade;
use Fomo\Language\Language;
use Fomo\Log\Logger;
use Fomo\Relationship\Relationship;
use Fomo\Response\Response;
use Fomo\ServerState\ServerState;
use Fomo\Validation\Validation;
use Swoole\Server;
use Fomo\Request\Request;
use Fomo\Router\Router;
use Fomo\Http\Http as HttpClient;
use Faker\Factory;
use Faker\Generator;

class Http
{
    protected Dispatcher $dispatcher;
    protected Request $request;
    protected Server $server;
    protected array $cache = [];
    protected array $config = [];
    protected array $MPCache = [];

    public function createServer(): self
    {
        $this->server = new Server(
            config('server.host') ,
            config('server.port') ,
            !is_null(config('server.ssl.ssl_cert_file')) && !is_null(config('server.ssl.ssl_key_file')) ? config('server.mode') | SWOOLE_SSL : config('server.mode') ,
            config('server.sockType')
        );

        $this->server->set(array_merge(config('server.additional') , ['enable_coroutine' => false]));

        $this->server->on('workerStart', [$this, 'onWorkerStart']);
        $this->server->on('receive', [$this, 'onReceive']);

        return $this;
    }

    public function start(bool $daemonize = false): void
    {
        if ($daemonize === true){
            $this->server->set([
                'daemonize' => 1
            ]);
        }
        $this->server->start();
    }

    public function onWorkerStart(Server $server, int $workerId): void
    {
        if ($workerId == config('server.additional.worker_num') - 1){
            $this->saveWorkerIds();
        }
        $this->setFacades();
        $this->setDispatcher();
        $this->setRequest($server);
    }

    public function onReceive(Server $server, $fd, $from_id, $data): void
    {
        $this->request->setBC($data , $fd);
        $firstLine = \strstr($data, "\r\n", true);

        if (isset($this->MPCache[$firstLine])) {
            $method = $this->MPCache[$firstLine][0];
            $path = $this->MPCache[$firstLine][1];
        }else{
            if (\count($this->MPCache) >= 256) {
                unset($this->MPCache[key($this->MPCache)]);
            }

            $MP = \explode(' ', $firstLine, 3);
            $path = \strstr($MP[1], '?', true);
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

            $server->send($fd, $cache[0]->{$cache[1]}($this->request , ...$cache[3]));
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

            $server->send($fd, $routeInfo[1][0]->{$routeInfo[1][1]}($this->request, ...$routeInfo[2]));
        } elseif ($routeInfo[0] === 0){
            $server->send($fd , (new Handler())->notFoundHttpException($this->request));
        } else {
            $server->send($fd , (new Handler())->notAllowedHttpException($this->request));
        }
    }

    protected function saveWorkerIds(): void
    {
        $workerIds = [];
        for ($i = 0; $i < config('server.additional.worker_num'); $i++){
            $workerIds[$i] = $this->server->getWorkerPid($i);
        }

        setMasterProcessId($this->server->getMasterPid());
        setManagerProcessId($this->server->getManagerPid());
        setWorkerProcessIds($workerIds);
    }

    protected function setFacades(): void
    {
        Setter::addClass('request', new Request);
        Setter::addClass('route', new Router);
        Setter::addClass('response', new Response);
        Setter::addClass('language', new Language);
        Setter::addClass('auth', new Auth);
        Setter::addClass('cache', new Cache);
        Setter::addClass('config', new Config);
        Setter::addClass('http', new HttpClient);
        Setter::addClass('log', new Logger);
        Setter::addClass('relationship', new Relationship);
        Setter::addClass('serverState', new ServerState);
        Setter::addClass('validation', new Validation);
        Setter::addClass('faker', Factory::create(config('app.faker_locale')));
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
