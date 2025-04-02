<?php

namespace App\Hooks;

use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;
use App\Jobs;
use App\Services\Container;

class JobHooks implements HooksInterface
{
    use RegistersHooks;

    public const JOBS = [
        Jobs\MyGreatJob::class
    ];

    public function __construct(
        private Container $container
    ) {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerJobs']);
    }

    public function registerJobs()
    {
        foreach (self::JOBS as $jobClass) {
            $job = $this->container->get($jobClass);
            $this->addAction($job->getName(), [$job, 'handle'], 10, 3);
            do_action('mill4_job_registered', $jobClass);
        }
    }
}
