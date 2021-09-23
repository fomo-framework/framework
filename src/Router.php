<?php

namespace Tower;

use Closure;

class Router
{
    protected array $routes = [];

    protected string $currentGroupPrefix = '';

    protected array $currentGroupMiddleware = [];

    public function post(string $route , array $callback): void
    {
        $this->addRoute('POST' , $route , $callback);
    }

    public function get(string $route , array $callback): void
    {
        $this->addRoute('GET' , $route , $callback);
    }

    public function patch(string $route , array $callback): void
    {
        $this->addRoute('PATCH' , $route , $callback);
    }

    public function put(string $route , array $callback): void
    {
        $this->addRoute('PUT' , $route , $callback);
    }

    public function delete(string $route , array $callback): void
    {
        $this->addRoute('DELETE' , $route , $callback);
    }

    public function head(string $route , array $callback): void
    {
        $this->addRoute('HEAD' , $route , $callback);
    }

    public function any(string $route , array $callback): void
    {
        $this->addRoute('POST' , $route , $callback);
        $this->addRoute('GET' , $route , $callback);
        $this->addRoute('PATCH' , $route , $callback);
        $this->addRoute('PUT' , $route , $callback);
        $this->addRoute('DELETE' , $route , $callback);
        $this->addRoute('HEAD' , $route , $callback);
    }

    public function group(array $parameters, Closure $callback): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        if (isset($parameters['prefix'])){
            $groupLastChar = substr($parameters['prefix'] , 0 , 1);
            if (strcmp($groupLastChar , '/') === 0){
                $parameters['prefix'] = substr($parameters['prefix'] , 1);
            }

            $prefixLastChar = substr($this->currentGroupPrefix , -1);
            if (strcmp($prefixLastChar , '/') === 0)
                $this->currentGroupPrefix = $previousGroupPrefix . $parameters['prefix'];
            else
                $this->currentGroupPrefix = $previousGroupPrefix . '/' . $parameters['prefix'];
        }

        $previousGroupMiddleware = $this->currentGroupMiddleware;
        if (isset($parameters['middleware'])){
            $this->pushToCurrentGroupMiddleware($parameters['middleware']);
        }

        if (isset($parameters['withoutMiddleware'])){
            $this->unsetFromCurrentGroupMiddleware($parameters['withoutMiddleware']);
            unset($parameters['withoutMiddleware']);
        }

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupMiddleware = $previousGroupMiddleware;
    }

    protected function addRoute(string $method , string $route , array $callback): void
    {
        $firstChar = substr($route , 0 , 1);
        if (strcmp($firstChar , '/') === 0)
            $route = substr($route , 1);

        $prefixLastChar = substr($this->currentGroupPrefix , -1);
        if (strcmp($prefixLastChar , '/') === 0)
            $route = $this->currentGroupPrefix . $route;
        else
            $route = $this->currentGroupPrefix . '/' . $route;

        if ($this->currentGroupPrefix != ''){
            $routeLastChar = substr($route, -1);
            if (strcmp($routeLastChar , '/') === 0)
                $route = substr($route, 0 ,-1);
        }

        $previousGroupMiddleware = $this->currentGroupMiddleware;
        if (isset($callback['middleware'])) {
            $this->pushToCurrentGroupMiddleware($callback['middleware']);
        }

        if (isset($callback['withoutMiddleware'])){
            $this->unsetFromCurrentGroupMiddleware($callback['withoutMiddleware']);
            unset($callback['withoutMiddleware']);
        }

        $callback['middleware'] = $this->currentGroupMiddleware;
        $this->currentGroupMiddleware = $previousGroupMiddleware;

        $this->routes[$method][] = [$route , $callback];
    }

    protected function pushToCurrentGroupMiddleware(string|array $middleware): void
    {
        if (is_array($middleware)){
            array_push($this->currentGroupMiddleware , ...$middleware);
        }

        array_push($this->currentGroupMiddleware , $middleware);
    }

    protected function unsetFromCurrentGroupMiddleware(string|array $middleware): void
    {
        if (is_array($middleware)){
            $this->currentGroupMiddleware = array_values(array_diff($this->currentGroupMiddleware , $middleware));
        }

        $this->currentGroupMiddleware = array_values(array_diff($this->currentGroupMiddleware , [$middleware]));
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
