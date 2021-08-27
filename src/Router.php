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
        if (isset($parameters['middleware']) && is_array($parameters['middleware']))
            array_push($this->currentGroupMiddleware , ...$parameters['middleware']);

        if (isset($parameters['middleware']) && is_string($parameters['middleware']))
            array_push($this->currentGroupMiddleware , $parameters['middleware']);

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

        $routeLastChar = substr($route, -1);
        if (strcmp($routeLastChar , '/') === 0)
            $route = substr($route, 0 ,-1);

        if (isset($callback['middleware']) && is_array($callback['middleware']))
            array_push($this->currentGroupMiddleware , ...$callback['middleware']);

        if (isset($callback['middleware']) && is_string($callback['middleware']))
            array_push($this->currentGroupMiddleware , $callback['middleware']);

        $callback['middleware'] = $this->currentGroupMiddleware;

        $this->routes[$method][] = [$route , $callback];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

}
