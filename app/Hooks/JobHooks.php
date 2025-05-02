<?php

namespace App\Hooks;

use App\Attributes\RegistersJob;
use App\Hooks\Concerns\DiscoversClasses;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;
use App\Services\Container;

class JobHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

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
        $classes = $this->discoverClassesForAttribute(RegistersJob::class, 'Jobs');

        foreach ($classes as $jobClass) {
            $job = $this->container->get($jobClass);
            $this->addAction($job->getName(), [$job, 'handle'], 10, 3);
            do_action('mill4_job_registered', $jobClass);
        }
    }
}
