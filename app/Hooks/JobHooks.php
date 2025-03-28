<?php

namespace App\Hooks;

use App\Hooks\Contracts\HooksInterface;
use App\Jobs;
use App\Jobs\MyGreatJob;
use App\Services\Container;

class JobHooks implements HooksInterface
{
    public const JOBS = [
        Jobs\MyGreatJob::class
    ];

    public function __construct(
        private Container $container
    ) {
    }

    public function initialize(): void
    {
        add_action('init', [$this, 'registerJobs']);
    }

    public function registerJobs()
    {
        foreach (self::JOBS as $jobClass) {
            $job = $this->container->get($jobClass);
            add_action($job->getName(), [$job, 'handle'], 10, 3);
            do_action('mill4_job_registered', $jobClass);
        }

        MyGreatJob::dispatch('bar')
            ->at('2025-03-29 12:00:00')
            ->execute();
    }
}
