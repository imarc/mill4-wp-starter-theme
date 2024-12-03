<?php

namespace App\Services;

use League\Container\Container as BaseContainer;
use League\Container\ReflectionContainer;

class Container
{
    private static $instance;

    public function __construct()
    {
        self::getInstance();
    }

    public static function getInstance(): BaseContainer
    {
        if (self::$instance === null) {
            self::$instance = new BaseContainer();
            self::$instance->delegate(new ReflectionContainer());
        }

        return self::$instance;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([self::getInstance(), $method], $args);
    }
}
