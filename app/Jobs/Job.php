<?php

namespace App\Jobs;

use App\Services\Container;
use App\Services\JobDispatcher;

abstract class Job
{
    public static function dispatch(...$args): JobDispatcher
    {
        $container = Container::getInstance();
        $dispatcher = $container->get(JobDispatcher::class);

        return $dispatcher->args($args)
            ->dispatch(static::class);
    }

    public function getName(): string
    {
        return $this->jobName ?? $this->generateName();
    }

    private function generateName(): string
    {
        $name = str_replace('App\\Jobs\\', '', static::class);

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }
}
