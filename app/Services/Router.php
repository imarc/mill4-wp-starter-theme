<?php

namespace App\Services;

class Router
{
    private static $instance = null;
    private $routes = [];

    private function __construct()
    {
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
        $this->routes['GET'][$path] = $this->resolveAction($action);
    }

    public function post($path, $action)
    {
        $this->routes['POST'][$path] = $this->resolveAction($action);
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

    private function resolveAction($action)
    {
        if (is_callable($action)) {
            return $action; // If it's already a callable, return it
        }

        // Assuming action is a controller class name
        if (class_exists($action)) {
            return new $action(); // Create an instance of the controller
        }

        throw new \InvalidArgumentException("Action must be a callable or a valid controller class name.");
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
