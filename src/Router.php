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
            if (is_array($parameters['middleware'])){
                array_push($this->currentGroupMiddleware , ...$parameters['middleware']);
            }

            if (is_string($parameters['middleware'])){
                array_push($this->currentGroupMiddleware , $parameters['middleware']);
            }
        }

        if (isset($parameters['withoutMiddleware'])){
            if (is_array($parameters['withoutMiddleware'])){
                $this->currentGroupMiddleware = array_values(array_diff($this->currentGroupMiddleware , $parameters['withoutMiddleware']));
                unset($parameters['withoutMiddleware']);
            } elseif (is_string($parameters['withoutMiddleware'])){
                $this->currentGroupMiddleware = array_values(array_diff($this->currentGroupMiddleware , [$parameters['withoutMiddleware']]));
                unset($parameters['withoutMiddleware']);
            }
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
            if (is_array($callback['middleware'])){
                array_push($this->currentGroupMiddleware , ...$callback['middleware']);
            }

            if (is_string($callback['middleware'])){
                array_push($this->currentGroupMiddleware , $callback['middleware']);
            }
        }

        if (isset($callback['withoutMiddleware'])){
            if (is_array($callback['withoutMiddleware'])){
                $this->currentGroupMiddleware = array_values(array_diff($this->currentGroupMiddleware , $callback['withoutMiddleware']));
                unset($callback['withoutMiddleware']);
            } elseif (is_string($callback['withoutMiddleware'])){
                $this->currentGroupMiddleware = array_values(array_diff($this->currentGroupMiddleware , [$callback['withoutMiddleware']]));
                unset($callback['withoutMiddleware']);
            }
        }

        $callback['middleware'] = $this->currentGroupMiddleware;
        $this->currentGroupMiddleware = $previousGroupMiddleware;

        $this->routes[$method][] = [$route , $callback];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

}
