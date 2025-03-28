<?php

namespace App\Services;

use App\Jobs\Job;
use App\Services\Container;

class JobDispatcher
{
    private array $args = [];
    private int $timestamp;
    private string $jobName;
    private Job $job;

    public function __construct(
        private Container $container
    ) {
    }

    public function dispatch(string $jobClass): static
    {
        $this->job = $this->container->get($jobClass);
        $this->jobName = $this->job->getName();

        return $this;
    }

    public function now(): static
    {
        $this->timestamp = time();

        return $this;
    }

    public function at(string|int $time): static
    {
        $this->timestamp = is_string($time) ? strtotime($time) : $time;

        return $this;
    }

    public function execute(bool $useQueue = true): void
    {
        if ($useQueue) {
            wp_schedule_single_event($this->timestamp, $this->jobName, $this->args);
        } else {
            do_action($this->jobName, ...$this->args);
        }
    }

    public function args(array $args): static
    {
        $this->args = $args;

        return $this;
    }
}
