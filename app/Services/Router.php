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
        if (is_callable($action)) {
            // If it's a callable, resolve dependencies
            return $this->resolveCallable($action);
        }

        // Assuming action is a controller class name
        if (class_exists($action)) {
            // Create an instance of the controller
            $controller = new $action(); // Instantiate the controller
            return function () use ($controller) {
                // Resolve dependencies for __invoke method
                $reflection = new \ReflectionMethod($controller, '__invoke');
                $parameters = $reflection->getParameters();
                $dependencies = [];

                foreach ($parameters as $parameter) {
                    $dependency = $this->container->get($parameter->getType()->getName());
                    $dependencies[] = $dependency;
                }

                return $controller->__invoke(...$dependencies); // Call __invoke with dependencies
            };
        }

        throw new \InvalidArgumentException("Action must be a callable or a valid controller class name.");
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
