<?php

namespace App\Services;

use League\Container\Container as BaseContainer;
use ReflectionFunction;
use ReflectionMethod;

class Router
{
    private static ?Router $instance = null;
    private array $routes = [];
    private BaseContainer $container;

    private function __construct()
    {
        $this->container = Container::getInstance();
    }

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public function get($path, $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post($path, $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function put($path, $action): void
    {
        $this->routes['PUT'][$path] = $action;
    }

    public function delete($path, $action): void
    {
        $this->routes['DELETE'][$path] = $action;
    }

    public function patch($path, $action): void
    {
        $this->routes['PATCH'][$path] = $action;
    }

    public function handleRequest(string $method, string $route): void
    {
        if (isset($this->routes[$method][$route])) {
            $action = $this->routes[$method][$route];
            $resolvedAction = $this->resolveAction($action);
            call_user_func($resolvedAction);
            exit; // Prevent further processing
        }
    }

    private function resolveAction($action): callable
    {
        // If the action is a callable...
        if (is_callable($action)) {
            return $this->resolveCallable($action);
        }

        // If the action is a controller class name, let's hope
        // that it has an __invoke method!
        if (class_exists($action)) {
            return $this->resolveClassMethod($action, '__invoke');
        }

        // If the action is a class method...
        if (preg_match('/^(.*)@(\w+)$/', $action, $matches)) {
            $class = $matches[1];
            $method = $matches[2];

            return $this->resolveClassMethod($class, $method);
        }

        throw new \InvalidArgumentException("Action must be a callable or a valid controller class name.");
    }

    private function resolveClassMethod(string $class, string $method): callable
    {
        $controller = new $class();

        // let's ensure the method exists
        if (! method_exists($controller, $method)) {
            throw new \InvalidArgumentException("Method $method does not exist in controller $class.");
        }

        return function () use ($controller, $method) {
            $reflection = new \ReflectionMethod($controller, $method);
            return $controller->$method(...$this->getDependenciesForReflection($reflection));
        };
    }

    private function resolveCallable(callable $callable): callable
    {
        $reflection = new ReflectionFunction($callable);

        return $callable(...$this->getDependenciesForReflection($reflection));
    }

    private function getDependenciesForReflection(ReflectionMethod|ReflectionFunction $reflection): array
    {
        $parameters = $reflection->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $this->container->get($parameter->getType()->getName());
            $dependencies[] = $dependency;
        }

        return $dependencies;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
