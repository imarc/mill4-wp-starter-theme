<?php

namespace App\Services;

class Router
{
    private static $instance = null;
    private $routes = [];
    private $container; // Service container

    private function __construct()
    {
        $this->container = Container::getInstance(); // Assuming you have a service container
    }

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public function get($path, $action)
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post($path, $action)
    {
        $this->routes['POST'][$path] = $action;
    }

    public function put($path, $action)
    {
        $this->routes['PUT'][$path] = $this->resolveAction($action);
    }

    public function delete($path, $action)
    {
        $this->routes['DELETE'][$path] = $this->resolveAction($action);
    }

    public function patch($path, $action)
    {
        $this->routes['PATCH'][$path] = $this->resolveAction($action);
    }

    public function handleRequest($request_method, $custom_route)
    {
        if (isset($this->routes[$request_method][$custom_route])) {
            $action = $this->routes[$request_method][$custom_route];

            // Resolve dependencies and call the action
            $resolvedAction = $this->resolveAction($action);
            call_user_func($resolvedAction);
            exit; // Prevent further processing
        }
    }

    private function resolveAction($action)
    {
        // If the action is a callable...
        if (is_callable($action)) {
            return $this->resolveCallable($action);
        }

        // If the action is a controller class name, let's hope
        // that it has an __invoke method
        if (class_exists($action)) {
            return $this->resolveClassMethod($action, '__invoke');
        }

        // If the action is a class method...
        if (preg_match('/(.*)@(\w+)$/', $action, $matches)) {
            $class = $matches[1];
            $method = $matches[2];

            return $this->resolveClassMethod($class, $method);
        }

        throw new \InvalidArgumentException("Action must be a callable or a valid controller class name.");
    }

    private function resolveClassMethod($class, $method)
    {
        $controller = new $class();

        // let's ensure the method exists
        if (! method_exists($controller, $method)) {
            throw new \InvalidArgumentException("Method $method does not exist in controller $class.");
        }

        return function () use ($controller, $method) {
            $reflection = new \ReflectionMethod($controller, $method);
            $parameters = $reflection->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $dependency = $this->container->get($parameter->getType()->getName());
                $dependencies[] = $dependency;
            }

            return $controller->$method(...$dependencies);
        };
    }

    private function resolveCallable($callable)
    {
        // If the callable is a closure, resolve its dependencies
        $reflection = new \ReflectionFunction($callable);
        $parameters = $reflection->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $this->container->get($parameter->getType()->getName());
            $dependencies[] = $dependency;
        }

        return function () use ($callable, $dependencies) {
            return $callable(...$dependencies);
        };
    }

    public function loadRoutes($callback)
    {
        if (is_callable($callback)) {
            $callback($this);
        }
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
