<?php

namespace Tower;

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

    public function group(array $parameters, \Closure $callback): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        if (isset($parameters['prefix']))
            $this->currentGroupPrefix = $previousGroupPrefix . $parameters['prefix'];

        $previousGroupMiddleware = $this->currentGroupMiddleware;
        if (isset($parameters['middleware']))
            array_push($this->currentGroupMiddleware , ...$parameters['middleware']);

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupMiddleware = $previousGroupMiddleware;
    }

    protected function addRoute(string $method , string $route , array $callback): void
    {
        $route = $this->currentGroupPrefix . $route;

        if (! empty($this->currentGroupMiddleware))
            $callback['middleware'] = $this->currentGroupMiddleware;
        
        $this->routes[$method][] = [$route , $callback];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

}
