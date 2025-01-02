<?php

namespace App\Services;

use App\Providers\AssetServiceProvider;
use League\Container\Container as BaseContainer;
use League\Container\ReflectionContainer;

class Container
{
    private static $instance;

    public function __construct()
    {
    }

    public static function getInstance(): BaseContainer
    {
        if (self::$instance === null) {
            self::$instance = new BaseContainer();
            self::$instance->delegate(new ReflectionContainer());
            self::$instance->addServiceProvider(new AssetServiceProvider());
        }

        return self::$instance;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([self::getInstance(), $method], $args);
    }
}
